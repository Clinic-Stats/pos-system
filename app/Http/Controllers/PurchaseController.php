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

    private function getFactorAndWeight($product, $unit)
    {
        $unitName = mb_strtolower(trim($unit->name));

        if (str_contains($unitName, 'کارتۆن') || str_contains($unitName, 'carton')) {
            return (float) ($product->kg_per_carton ?: 1);
        }

        if (str_contains($unitName, 'تەن') || str_contains($unitName, 'ton')) {
            return 1000.0;
        }

        return (float) ($unit->factor_to_base ?: 1);
    }

    /**
     * زیادکردنی کۆگا + نوێکردنەوەی تێکڕای نرخی کڕین (بە دۆلار بۆ ١ کیلۆ)
     * هەمان لۆژیک بۆ store و update بەکاردێت.
     */
    private function addStockAndAverageCost(Product $product, float $addedKg, float $priceUsdPerKg): void
    {
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

        $product->refresh();
        $currentStock   = max(0, (float) ($product->{$stockCol} ?? 0));
        $currentCostUsd = (float) ($product->base_buy_price ?? $product->buy_price ?? 0);
        $totalCombinedKg = $currentStock + $addedKg;

        if ($totalCombinedKg > 0 && $currentStock > 0 && $currentCostUsd > 0) {
            $averageCostUsd = (($currentStock * $currentCostUsd) + ($addedKg * $priceUsdPerKg)) / $totalCombinedKg;
        } else {
            $averageCostUsd = $priceUsdPerKg;
        }

        $product->increment($stockCol, $addedKg);

        $updateFields = [];
        if (Schema::hasColumn('products', 'base_buy_price')) {
            $updateFields['base_buy_price'] = round($averageCostUsd, 4);
        }
        if (Schema::hasColumn('products', 'buy_price')) {
            $updateFields['buy_price'] = round($averageCostUsd, 4);
        }
        if (!empty($updateFields)) {
            $product->update($updateFields);
        }
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->get();
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
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $totalAmount += (float) $item['quantity'] * ((float) $item['buy_price'] * $factor);
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی یان بە نرخی 0!');
                }

                $paid = ($request->payment_type === 'debt') ? (float) ($request->paid_amount ?? 0) : $totalAmount;
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
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $priceInCurrency = (float) $item['buy_price'];
                    $costPerUnit = $priceInCurrency * $factor;
                    $lineTotal   = (float) $item['quantity'] * $costPerUnit;
                    $addedKg     = (float) $item['quantity'] * $factor;
                    $priceUsdPerKg = $currency === 'IQD' ? $priceInCurrency / $exchangeRate : $priceInCurrency;

                    PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    $this->addStockAndAverageCost($product, $addedKg, $priceUsdPerKg);
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
                $currency = $request->currency;
                $exchangeRate = (float) $request->exchange_rate;
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                // گەڕاندنەوەی (کەمکردنەوەی) بڕە کۆنەکان لە کۆگا
                foreach ($purchase->details as $oldDetail) {
                    if (!$oldDetail->product || !$oldDetail->unit) {
                        continue;
                    }
                    $factor = $this->getFactorAndWeight($oldDetail->product, $oldDetail->unit);
                    Product::where('id', $oldDetail->product_id)->decrement($stockCol, $oldDetail->quantity * $factor);
                }

                $purchase->details()->delete();

                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $totalAmount += (float) $item['quantity'] * ((float) $item['buy_price'] * $factor);
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی!');
                }

                $paid = ($request->payment_type === 'debt') ? (float) ($request->paid_amount ?? 0) : $totalAmount;
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
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $priceInCurrency = (float) $item['buy_price'];
                    $costPerUnit = $priceInCurrency * $factor;
                    $lineTotal   = (float) $item['quantity'] * $costPerUnit;
                    $addedKg     = (float) $item['quantity'] * $factor;
                    $priceUsdPerKg = $currency === 'IQD' ? $priceInCurrency / $exchangeRate : $priceInCurrency;

                    PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    // هەمان تێکڕای کێشراو وەک store (پێشتر لێرە نرخەکە بە نرخی کۆتایی دەنووسرایەوە)
                    $this->addStockAndAverageCost($product, $addedKg, $priceUsdPerKg);
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
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                foreach ($purchase->details as $detail) {
                    if (!$detail->product || !$detail->unit) {
                        continue;
                    }
                    $factor = $this->getFactorAndWeight($detail->product, $detail->unit);
                    Product::where('id', $detail->product_id)->decrement($stockCol, $detail->quantity * $factor);
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
