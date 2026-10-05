<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'details.product', 'details.unit']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                if (Schema::hasColumn('purchases', 'purchase_no')) {
                    $q->orWhere('purchase_no', 'like', '%' . $request->search . '%');
                }
                if (Schema::hasColumn('purchases', 'invoice_no')) {
                    $q->orWhere('invoice_no', 'like', '%' . $request->search . '%');
                }
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $purchases = $query->latest('created_at')->paginate(15)->withQueryString();
        $products = Product::where('is_active', 1)->get();
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();
        $setting = Setting::first();

        return view('purchases.index', compact('purchases', 'products', 'categories', 'units', 'suppliers', 'customers', 'setting'));
    }

    /** کاڵای کارتۆنی: نرخ و کۆگا بە کارتۆنە، بۆیە فۆڕمەکان کێشی کارتۆن وەک فاکتەر نابینن */
    private function cartonSafe($products)
    {
        return $products->map(function ($p) {
            if (($p->sell_type ?? 'weight') === 'carton') {
                $p->kg_per_carton = 1;
            }
            return $p;
        });
    }

    /** کاڵای کارتۆنی هەمیشە بە یەکەی «کارتۆن» تۆمار دەکرێت (بۆ پسوولە و کێش) */
    private function resolveUnit($product, $unit)
    {
        if (($product->sell_type ?? 'weight') !== 'carton') {
            return $unit;
        }
        return Unit::where('name', 'like', '%کارتۆن%')->orWhere('name', 'like', '%carton%')->first() ?: $unit;
    }

    private function getFactorAndWeight($product, $unit)
    {
        // کاڵای کارتۆنی: نرخ و کۆگا بە کارتۆن، فاکتەر هەمیشە ١
        if (($product->sell_type ?? 'weight') === 'carton') {
            return 1.0;
        }

        $unitName = mb_strtolower(trim($unit->name));

        if (str_contains($unitName, 'کارتۆن') || str_contains($unitName, 'carton')) {
            return (float) ($product->kg_per_carton ?: 1);
        }

        if (str_contains($unitName, 'تەن') || str_contains($unitName, 'ton')) {
            return 1000.0;
        }

        return (float) ($unit->factor_to_base ?: 1);
    }

    /** تێچووی تێکڕای ڕاستەقینە (٦ ژمارە): لە دوایین کڕین، مەگەر نرخەکە بە دەست گۆڕدرابێت */
    private function exactCost(Product $product): float
    {
        $base = (float) ($product->base_buy_price ?? $product->buy_price ?? 0);
        if (!Schema::hasColumn('purchase_details', 'cost_after')) {
            return $base;
        }
        $last = DB::table('purchase_details')->where('product_id', $product->id)->whereNotNull('cost_after')->orderByDesc('id')->first();
        if ($last && abs((float) $last->cost_after - $base) < 0.006) {
            return (float) $last->cost_after;
        }
        return $base;
    }

    /**
     * زیادکردنی کاڵا بۆ کۆگا و ژماردنی تێچووی تێکڕا.
     * وێنەیەک (پێش/دوای) دەگەڕێنێتەوە کە لەگەڵ وردەکاری کڕینەکە دەپارێزرێت.
     */
    private function addStockAndAverageCost(Product $product, float $addedKg, float $priceUsdPerKg): array
    {
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

        $product->refresh();
        $currentStock = max(0, (float) ($product->{$stockCol} ?? 0));
        $currentCost  = $this->exactCost($product);
        $total        = $currentStock + $addedKg;

        if ($total > 0 && $currentStock > 0 && $currentCost > 0) {
            $avg = (($currentStock * $currentCost) + ($addedKg * $priceUsdPerKg)) / $total;
        } else {
            $avg = $priceUsdPerKg;
        }

        $product->increment($stockCol, $addedKg);

        $fields = [];
        if (Schema::hasColumn('products', 'base_buy_price')) $fields['base_buy_price'] = round($avg, 6);
        if (Schema::hasColumn('products', 'buy_price'))      $fields['buy_price'] = round($avg, 6);
        if ($fields) {
            $product->update($fields);
        }

        return [
            'stock_before'   => $currentStock,
            'cost_before'    => $currentCost,
            'cost_after'     => $avg,
            'added_units'    => $addedKg,
            'price_usd_unit' => $priceUsdPerKg,
        ];
    }

    private function saveSnapshot($detail, array $snap): void
    {
        if (Schema::hasColumn('purchase_details', 'cost_after')) {
            DB::table('purchase_details')->where('id', $detail->id)->update($snap);
        }
    }

    /**
     * لابردنی کڕینێک (دەستکاری / سڕینەوە) و گەڕاندنەوەی تێچووی تێکڕا بۆ ئەوەی پێش ئەو کڕینە بوو.
     * - ئەگەر دوایین کڕین بێت: تێچوو ڕێک دەگەڕێتەوە بۆ وێنەی پێشووی (cost_before)
     * - ئەگەر کڕینی دواتر هەبێت: ئەوانیش بە ڕیز دووبارە دەژمێردرێنەوە بەبێ ئەم کڕینە
     */
    private function removeStockAndAverageCost($detail, $purchase, string $stockCol): void
    {
        $product = $detail->product;
        if (!$product || !$detail->unit) {
            return;
        }

        $factor  = $this->getFactorAndWeight($product, $detail->unit);
        $removed = (float) $detail->quantity * $factor;                        // بە یەکەی کۆگا
        $rate    = (float) ($purchase->exchange_rate ?? 0) ?: 1;
        $priceUsd = ((float) $detail->unit_buy_price / ($factor ?: 1)) / (strtoupper($purchase->currency ?? 'USD') === 'IQD' ? $rate : 1);

        $hasCols = Schema::hasColumn('purchase_details', 'cost_after');
        $row = $hasCols ? DB::table('purchase_details')->where('id', $detail->id)->first() : null;
        $hasSnap = $row && $row->cost_before !== null && $row->stock_before !== null && $row->cost_after !== null;

        $fresh = Product::find($product->id);
        $newCost = null;

        if ($hasSnap) {
            // ١) تێچووی پێش ئەم کڕینە، ٢) دووبارە ژماردنی کڕینەکانی دواتر بەبێ ئەم کڕینە
            $A = (float) $row->cost_before;
            $later = DB::table('purchase_details')->where('product_id', $product->id)->where('id', '>', $detail->id)
                ->whereNotNull('cost_after')->orderBy('id')->get();

            foreach ($later as $l) {
                $S = (float) $l->stock_before - $removed;
                $q = (float) $l->added_units;
                $p = (float) $l->price_usd_unit;
                $costBefore = $A;
                $A = ($S > 0 && $A > 0) ? (($S * $A) + ($q * $p)) / ($S + $q) : $p;

                DB::table('purchase_details')->where('id', $l->id)->update([
                    'stock_before' => max(0, $S), 'cost_before' => $costBefore, 'cost_after' => $A,
                ]);
            }
            $newCost = $A;
        } else {
            // وەسڵی کۆن (بێ وێنە): ژماردنی بەهای ماتماتیکی
            $stock = (float) $fresh->{$stockCol};
            $cost  = $this->exactCost($fresh);
            $remaining = $stock - $removed;
            if ($remaining > 0 && $cost > 0) {
                $value = ($stock * $cost) - ($removed * $priceUsd);
                if ($value > 0) {
                    $newCost = $value / $remaining;
                }
            }
        }

        if ($newCost !== null && $newCost > 0) {
            if (Schema::hasColumn('products', 'base_buy_price')) $fresh->base_buy_price = round($newCost, 6);
            if (Schema::hasColumn('products', 'buy_price'))      $fresh->buy_price = round($newCost, 6);
            $fresh->save();
        }

        Product::where('id', $product->id)->decrement($stockCol, $removed);
    }

    public function create()
    {
        $products = $this->cartonSafe(Product::where('is_active', 1)->get());
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        $setting = Setting::first();

        return view('purchases.create', compact('products', 'categories', 'units', 'suppliers', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_no'        => ['required', 'string', 'max:100', Rule::unique('purchases', 'purchase_no')],
            'supplier_id'        => 'required|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id'    => 'required|exists:units,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.buy_price'  => 'required|numeric|min:0',
            'currency'           => 'required|in:USD,IQD',
            'exchange_rate'      => 'required|numeric|min:1',
            'payment_type'       => 'nullable|in:cash,debt',
            'paid_amount'        => 'nullable|numeric|min:0',
        ], [
            'purchase_no.required'        => 'تکایە ژمارەی پسوولەی کڕین بنووسە.',
            'purchase_no.unique'          => 'ئەم ژمارەی پسوولەیە پێشتر بەکارهاتووە.',
            'supplier_id.required'        => 'تکایە شوێنی کڕین (کۆمپانیا) دیاری بکە.',
            'items.required'              => 'وەسڵ بەتاڵە! تکایە لانی کەم کاڵایەک زیاد بکە.',
            'items.min'                   => 'وەسڵ بەتاڵە! تکایە لانی کەم کاڵایەک زیاد بکە.',
            'items.*.product_id.required' => 'تکایە جۆری کاڵاکە دیاری بکە.',
            'items.*.quantity.min'        => 'بڕی کاڵا دەبێت لە 0 زیاتر بێت.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $currency = $request->currency;
                $exchangeRate = (float) $request->exchange_rate;

                $totalAmount = 0;

                // ١. ژماردنی کۆی گشتی بەپێی دراوی هەڵبژێردراو
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = $this->resolveUnit($product, Unit::findOrFail($item['unit_id']));
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $totalAmount += (float) $item['quantity'] * ((float) $item['buy_price'] * $factor);
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی یان بە نرخی 0!');
                }

                $paid = ($request->payment_type === 'debt') ? min((float) ($request->paid_amount ?? 0), $totalAmount) : $totalAmount;
                $remaining = $totalAmount - $paid;
                $invCode = trim($request->purchase_no);

                $pDate = $request->filled('created_at')
                    ? Carbon::parse($request->created_at)->format('Y-m-d')
                    : now()->format('Y-m-d');

                $cDateTime = $request->filled('created_at')
                    ? Carbon::parse($request->created_at)->setTime((int) date('H'), (int) date('i'), (int) date('s'))
                    : now();

                $purchaseData = [
                    'purchase_no'      => $invCode,
                    'purchase_date'    => $pDate,
                    'supplier_id'      => $request->supplier_id,
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'currency'         => $currency,
                    'exchange_rate'    => $exchangeRate,
                    'user_id'          => auth()->id() ?? 1,
                    'created_at'       => $cDateTime,
                ];

                if (Schema::hasColumn('purchases', 'invoice_no')) {
                    $purchaseData['invoice_no'] = $invCode;
                }

                $purchase = Purchase::create($purchaseData);

                // ٢. تۆمارکردنی کاڵاکان + کۆگا + تێکڕای تێچوو
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = $this->resolveUnit($product, Unit::findOrFail($item['unit_id']));
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $priceInCurrency = (float) $item['buy_price'];
                    $costPerUnit = $priceInCurrency * $factor;
                    $lineTotal   = (float) $item['quantity'] * $costPerUnit;
                    $addedKg     = (float) $item['quantity'] * $factor;
                    $priceUsdPerKg = $currency === 'IQD' ? $priceInCurrency / $exchangeRate : $priceInCurrency;

                    $newDetail = PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    $snap = $this->addStockAndAverageCost($product, $addedKg, $priceUsdPerKg);
                    $this->saveSnapshot($newDetail, $snap);
                }
            });

            return redirect()->route('purchases.index')->with('success', 'وەسڵی کڕین بە سەرکەوتوویی تۆمار کرا');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['details.product', 'details.unit', 'supplier'])->findOrFail($id);
        $products = Product::where('is_active', 1)
            ->orWhereIn('id', $purchase->details->pluck('product_id'))
            ->get();
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        $setting = Setting::first();

        $products = $this->cartonSafe($products);
        foreach ($purchase->details as $d) {
            if ($d->product && ($d->product->sell_type ?? 'weight') === 'carton') {
                $d->product->kg_per_carton = 1;
            }
        }

        return view('purchases.edit', compact('purchase', 'products', 'categories', 'units', 'suppliers', 'setting'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'purchase_no'        => ['required', 'string', 'max:100', Rule::unique('purchases', 'purchase_no')->ignore($id)],
            'supplier_id'        => 'required|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id'    => 'required|exists:units,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.buy_price'  => 'required|numeric|min:0',
            'currency'           => 'required|in:USD,IQD',
            'exchange_rate'      => 'required|numeric|min:1',
            'payment_type'       => 'nullable|in:cash,debt',
            'paid_amount'        => 'nullable|numeric|min:0',
        ], [
            'purchase_no.required' => 'تکایە ژمارەی پسوولەی کڕین بنووسە.',
            'purchase_no.unique'   => 'ئەم ژمارەی پسوولەیە پێشتر بەکارهاتووە.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $purchase = Purchase::with('details.product', 'details.unit')->findOrFail($id);
                \App\Models\ActivityLog::stash($purchase);
                $currency = $request->currency;
                $exchangeRate = (float) $request->exchange_rate;
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                // گەڕاندنەوەی (کەمکردنەوەی) بڕە کۆنەکان لە کۆگا
                foreach ($purchase->details as $oldDetail) {
                    if (!$oldDetail->product || !$oldDetail->unit) {
                        continue;
                    }
                    $this->removeStockAndAverageCost($oldDetail, $purchase, $stockCol);
                }

                $purchase->details()->delete();

                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = $this->resolveUnit($product, Unit::findOrFail($item['unit_id']));
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $totalAmount += (float) $item['quantity'] * ((float) $item['buy_price'] * $factor);
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی!');
                }

                $paid = ($request->payment_type === 'debt') ? min((float) ($request->paid_amount ?? 0), $totalAmount) : $totalAmount;
                $remaining = $totalAmount - $paid;

                $pDate = $request->filled('created_at')
                    ? Carbon::parse($request->created_at)->format('Y-m-d')
                    : ($purchase->purchase_date ?? now()->format('Y-m-d'));

                // کاتژمێری وەسڵە کۆنەکە دەپارێزین، تەنها بەروارەکە دەگۆڕێت
                $oldTime = $purchase->created_at ?: now();
                $cDateTime = $request->filled('created_at')
                    ? Carbon::parse($request->created_at)->setTime($oldTime->hour, $oldTime->minute, $oldTime->second)
                    : $oldTime;

                $newNo = trim($request->purchase_no);

                $updateData = [
                    'purchase_no'      => $newNo,
                    'supplier_id'      => $request->supplier_id,
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'currency'         => $currency,
                    'exchange_rate'    => $exchangeRate,
                    'purchase_date'    => $pDate,
                    'created_at'       => $cDateTime,
                    'updated_at'       => now(),
                ];

                if (Schema::hasColumn('purchases', 'invoice_no')) {
                    $updateData['invoice_no'] = $newNo;
                }

                $purchase->update($updateData);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = $this->resolveUnit($product, Unit::findOrFail($item['unit_id']));
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $priceInCurrency = (float) $item['buy_price'];
                    $costPerUnit = $priceInCurrency * $factor;
                    $lineTotal   = (float) $item['quantity'] * $costPerUnit;
                    $addedKg     = (float) $item['quantity'] * $factor;
                    $priceUsdPerKg = $currency === 'IQD' ? $priceInCurrency / $exchangeRate : $priceInCurrency;

                    $newDetail = PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    // هەمان تێکڕای کێشراو وەک store (پێشتر لێرە نرخەکە بە نرخی کۆتایی دەنووسرایەوە)
                    $snap = $this->addStockAndAverageCost($product, $addedKg, $priceUsdPerKg);
                    $this->saveSnapshot($newDetail, $snap);
                }
            });

            return redirect()->route('purchases.index')->with('success', 'وەسڵی کڕین بە سەرکەوتوویی نوێکرایەوە');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchase = Purchase::with('details.product', 'details.unit')->findOrFail($id);
                \App\Models\ActivityLog::stash($purchase);
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                foreach ($purchase->details as $detail) {
                    if (!$detail->product || !$detail->unit) {
                        continue;
                    }
                    $this->removeStockAndAverageCost($detail, $purchase, $stockCol);
                }

                $purchase->details()->delete();
                $purchase->delete();
            });

            return redirect()->route('purchases.index')->with('success', 'وەسڵی کڕین سڕایەوە');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'کێشەیەک ڕوویدا: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product', 'details.unit'])->findOrFail($id);
        $setting = Setting::first();
        return view('purchases.print', compact('purchase', 'setting'));
    }
}