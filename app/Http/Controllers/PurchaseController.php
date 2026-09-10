<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'details.product', 'details.unit']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
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

        return view('purchases.index', compact('purchases', 'products', 'categories', 'units', 'suppliers', 'customers'));
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

    public function create()
    {
        $products = Product::where('is_active', 1)->get();
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();

        return view('purchases.create', compact('products', 'categories', 'units', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id'    => 'required|exists:units,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.buy_price'  => 'required|numeric|min:0',
        ], [
            'supplier_id.required'        => 'تکایە شوێنی کڕین (کۆمپانیا) دیاری بکە.',
            'items.required'              => 'وەسڵ بەتاڵە! تکایە لانی کەم کاڵایەک زیاد بکە.',
            'items.min'                   => 'وەسڵ بەتاڵە! تکایە لانی کەم کاڵایەک زیاد بکە.',
            'items.*.product_id.required' => 'تکایە جۆری کاڵاکە دیاری بکە.',
            'items.*.quantity.min'        => 'بڕی کاڵا دەبێت لە 0 زیاتر بێت.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $totalAmount = 0;

                // ١. ژماردنی کۆی گشتی و پشکنینی بڕ و نرخ
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $costPerUnit = (float)$item['buy_price'] * $factor;
                    $lineTotal   = (float)$item['quantity'] * $costPerUnit;
                    $totalAmount += $lineTotal;
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی یان بە نرخی 0 IQD!');
                }

                $paid = ($request->payment_type === 'cash') ? $totalAmount : ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;
                $invCode = 'PUR-' . strtoupper(uniqid());

                $pDate = $request->filled('created_at') 
                    ? Carbon::parse($request->created_at)->format('Y-m-d') 
                    : now()->format('Y-m-d');

                $cDateTime = $request->filled('created_at') 
                    ? Carbon::parse($request->created_at)->setTime(date('H'), date('i'), date('s')) 
                    : now();

                $purchaseData = [
                    'purchase_no'      => $invCode,
                    'purchase_date'    => $pDate,
                    'supplier_id'      => $request->supplier_id,
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'user_id'          => auth()->id() ?? 1,
                    'created_at'       => $cDateTime,
                ];

                if (Schema::hasColumn('purchases', 'invoice_no')) {
                    $purchaseData['invoice_no'] = $invCode;
                }

                $purchase = Purchase::create($purchaseData);

                // ٢. زیادکردن بۆ کۆگا و حیسابکردنی تێکڕای تێچوو
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $costPerUnit = (float)$item['buy_price'] * $factor;
                    $lineTotal   = (float)$item['quantity'] * $costPerUnit;
                    $addedKg     = (float)$item['quantity'] * $factor;

                    PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    $currentStock    = (float) ($product->stock_kg ?? $product->stock ?? 0);
                    $currentCost     = (float) ($product->base_buy_price ?? $product->buy_price ?? 0);
                    $newBoughtKg     = (float) $addedKg;
                    $newPricePerKg   = (float) $item['buy_price'];

                    $totalCombinedKg = $currentStock + $newBoughtKg;

                    if ($totalCombinedKg > 0 && $currentStock > 0 && $currentCost > 0) {
                        $averageCostPerKg = (($currentStock * $currentCost) + ($newBoughtKg * $newPricePerKg)) / $totalCombinedKg;
                    } else {
                        $averageCostPerKg = $newPricePerKg;
                    }

                    if (Schema::hasColumn('products', 'stock_kg')) {
                        $product->increment('stock_kg', $addedKg);
                    } else {
                        $product->increment('stock', $addedKg);
                    }

                    $updateFields = [];
                    if (Schema::hasColumn('products', 'base_buy_price')) {
                        $updateFields['base_buy_price'] = round($averageCostPerKg, 2);
                    }
                    if (Schema::hasColumn('products', 'buy_price')) {
                        $updateFields['buy_price'] = round($averageCostPerKg, 2);
                    }

                    if (!empty($updateFields)) {
                        $product->update($updateFields);
                    }
                }
            });

            return redirect()->route('purchases.index')->with('success', 'وەسڵی کڕین بە سەرکەوتوویی تۆمار کرا و تێکڕای تێچووی کۆگا نوێکرایەوە');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['details.product', 'details.unit', 'supplier'])->findOrFail($id);
        $products = Product::where('is_active', 1)->get();
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();

        return view('purchases.edit', compact('purchase', 'products', 'categories', 'units', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id'    => 'required|exists:units,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.buy_price'  => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $purchase = Purchase::with('details.product', 'details.unit')->findOrFail($id);

                // گەڕاندنەوەی بڕەکانی پێشوو لە کۆگا
                foreach ($purchase->details as $oldDetail) {
                    $factor = $this->getFactorAndWeight($oldDetail->product, $oldDetail->unit);
                    $oldKg = $oldDetail->quantity * $factor;
                    if (Schema::hasColumn('products', 'stock_kg')) {
                        Product::where('id', $oldDetail->product_id)->decrement('stock_kg', $oldKg);
                    } else {
                        Product::where('id', $oldDetail->product_id)->decrement('stock', $oldKg);
                    }
                }

                $purchase->details()->delete();

                $totalAmount = 0;
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $costPerUnit = (float)$item['buy_price'] * $factor;
                    $lineTotal   = (float)$item['quantity'] * $costPerUnit;
                    $totalAmount += $lineTotal;
                }

                if ($totalAmount <= 0) {
                    throw new \Exception('وەسڵ ناتوانرێت خەزن بکرێت بە بەتاڵی!');
                }

                $paid = ($request->payment_type === 'cash') ? $totalAmount : ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;

                $pDate = $request->filled('created_at') 
                    ? Carbon::parse($request->created_at)->format('Y-m-d') 
                    : ($request->filled('purchase_date') ? Carbon::parse($request->purchase_date)->format('Y-m-d') : now()->format('Y-m-d'));

                $cDateTime = $request->filled('created_at') 
                    ? Carbon::parse($request->created_at)->setTime(date('H'), date('i'), date('s')) 
                    : ($request->filled('purchase_date') ? Carbon::parse($request->purchase_date)->setTime(date('H'), date('i'), date('s')) : now());

                $updateData = [
                    'supplier_id'      => $request->supplier_id,
                    'total_amount'     => $totalAmount,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'purchase_date'    => $pDate,
                    'created_at'       => $cDateTime,
                    'updated_at'       => now(),
                ];

                $purchase->update($updateData);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $costPerUnit = (float)$item['buy_price'] * $factor;
                    $lineTotal   = (float)$item['quantity'] * $costPerUnit;
                    $addedKg     = (float)$item['quantity'] * $factor;

                    PurchaseDetail::create([
                        'purchase_id'    => $purchase->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_buy_price' => $costPerUnit,
                        'subtotal'       => $lineTotal,
                    ]);

                    if (Schema::hasColumn('products', 'stock_kg')) {
                        $product->increment('stock_kg', $addedKg);
                    } else {
                        $product->increment('stock', $addedKg);
                    }

                    if (Schema::hasColumn('products', 'base_buy_price')) {
                        $product->update(['base_buy_price' => $item['buy_price']]);
                    } elseif (Schema::hasColumn('products', 'buy_price')) {
                        $product->update(['buy_price' => $item['buy_price']]);
                    }
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

                foreach ($purchase->details as $detail) {
                    $factor = $this->getFactorAndWeight($detail->product, $detail->unit);
                    $deductedKg = $detail->quantity * $factor;
                    if (Schema::hasColumn('products', 'stock_kg')) {
                        Product::where('id', $detail->product_id)->decrement('stock_kg', $deductedKg);
                    } else {
                        Product::where('id', $detail->product_id)->decrement('stock', $deductedKg);
                    }
                }

                $purchase->details()->delete();
                $purchase->delete();
            });

            return redirect()->route('purchases.index')->with('success', 'وەسڵی کڕین سڕایەوە و کاڵاکان لە کۆگا کەمکرانەوە');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'کێشەیەک ڕوویدا: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product', 'details.unit'])->findOrFail($id);
        return view('purchases.print', compact('purchase'));
    }
}