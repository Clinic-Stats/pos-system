<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SaleReturn::with(['customer', 'user', 'details.product', 'details.unit']);

        if ($request->filled('search')) {
            $query->where('return_no', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $returns = $query->latest('created_at')->paginate(15)->withQueryString();
        $customers = Customer::all();

        return view('returns.index', compact('returns', 'customers'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('is_active', 1)->get();
        $units = Unit::all();

        return view('returns.create', compact('customers', 'products', 'units'));
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

    public function store(Request $request)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'refund_type' => 'required|in:cash,deduct_debt',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = auth()->user() ?? User::first();
                $totalAmount = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $unitPrice = $item['unit_price'] * $factor;
                    $lineTotal = $item['quantity'] * $unitPrice;
                    $totalAmount += $lineTotal;
                }

                $saleReturn = SaleReturn::create([
                    'return_no'    => 'RET-' . strtoupper(uniqid()),
                    'customer_id'  => $request->customer_id ?: null,
                    'user_id'      => $user ? $user->id : null,
                    'total_amount' => $totalAmount,
                    'refund_type'  => $request->refund_type,
                    'notes'        => $request->notes,
                    'created_at'   => $request->filled('created_at') ? Carbon::parse($request->created_at)->setTime(date('H'), date('i'), date('s')) : now(),
                ]);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $unitPrice = $item['unit_price'] * $factor;
                    $lineTotal = $item['quantity'] * $unitPrice;

                    SaleReturnDetail::create([
                        'sale_return_id' => $saleReturn->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_price'     => $unitPrice,
                        'subtotal'       => $lineTotal,
                        'condition_type' => $item['condition_type'] ?? 'normal',
                    ]);

                    // ئەگەر کاڵاکە ساغ بوو، بگەڕێتەوە سەر کۆگا
                    if (($item['condition_type'] ?? 'normal') === 'normal') {
                        $addedKg = $item['quantity'] * $factor;
                        if (Schema::hasColumn('products', 'stock_kg')) {
                            $product->increment('stock_kg', $addedKg);
                        } else {
                            $product->increment('stock', $addedKg);
                        }
                    }
                }

                // ئەگەر کڕیار دیاریکرابوو و شێوازەکە داشکاندن لە قەرز بوو، لە قەرزەکەی کەم دەکاتەوە
                if ($request->customer_id && $request->refund_type === 'deduct_debt') {
                    Customer::where('id', $request->customer_id)->decrement('balance', $totalAmount);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'وەسڵی گەڕانەوە بە سەرکەوتوویی تۆمار کرا و کۆگا و حسابات نوێکرانەوە'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $return = SaleReturn::with(['details.product', 'details.unit', 'customer'])->findOrFail($id);
        $customers = Customer::all();
        $products = Product::where('is_active', 1)->get();
        $units = Unit::all();

        return view('returns.edit', compact('return', 'customers', 'products', 'units'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'refund_type' => 'required|in:cash,deduct_debt',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $saleReturn = SaleReturn::with('details.product', 'details.unit')->findOrFail($id);

                // گەڕاندنەوەی قەرزی پێشووی کڕیار ئەگەر قەرز بووبێت
                if ($saleReturn->customer_id && $saleReturn->refund_type === 'deduct_debt') {
                    Customer::where('id', $saleReturn->customer_id)->increment('balance', $saleReturn->total_amount);
                }

                // کەمکردنەوەی ئەو کاڵایانەی پێشتر خرابوونەوە کۆگا
                foreach ($saleReturn->details as $oldDetail) {
                    if ($oldDetail->condition_type === 'normal') {
                        $factor = $this->getFactorAndWeight($oldDetail->product, $oldDetail->unit);
                        $deductedKg = $oldDetail->quantity * $factor;
                        if (Schema::hasColumn('products', 'stock_kg')) {
                            Product::where('id', $oldDetail->product_id)->decrement('stock_kg', $deductedKg);
                        } else {
                            Product::where('id', $oldDetail->product_id)->decrement('stock', $deductedKg);
                        }
                    }
                }

                $saleReturn->details()->delete();

                $totalAmount = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getFactorAndWeight($product, $unit);

                    $unitPrice = $item['unit_price'] * $factor;
                    $lineTotal = $item['quantity'] * $unitPrice;
                    $totalAmount += $lineTotal;

                    SaleReturnDetail::create([
                        'sale_return_id' => $saleReturn->id,
                        'product_id'     => $product->id,
                        'unit_id'        => $unit->id,
                        'quantity'       => $item['quantity'],
                        'unit_price'     => $unitPrice,
                        'subtotal'       => $lineTotal,
                        'condition_type' => $item['condition_type'] ?? 'normal',
                    ]);

                    if (($item['condition_type'] ?? 'normal') === 'normal') {
                        $addedKg = $item['quantity'] * $factor;
                        if (Schema::hasColumn('products', 'stock_kg')) {
                            $product->increment('stock_kg', $addedKg);
                        } else {
                            $product->increment('stock', $addedKg);
                        }
                    }
                }

                $saleReturn->customer_id  = $request->customer_id ?: null;
                $saleReturn->refund_type  = $request->refund_type;
                $saleReturn->total_amount = $totalAmount;
                $saleReturn->notes        = $request->notes;

                if ($request->filled('created_at')) {
                    $saleReturn->created_at = Carbon::parse($request->created_at)->setTime(date('H'), date('i'), date('s'));
                }

                $saleReturn->save();

                // دابەزاندنی قەرزی نوێ
                if ($request->customer_id && $request->refund_type === 'deduct_debt') {
                    Customer::where('id', $request->customer_id)->decrement('balance', $totalAmount);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'وەسڵی گەڕانەوە بە سەرکەوتوویی نوێکرایەوە'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function print($id)
    {
        $return = SaleReturn::with(['customer', 'details.product', 'details.unit'])->findOrFail($id);
        return view('returns.print', compact('return'));
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $saleReturn = SaleReturn::with('details.product', 'details.unit')->findOrFail($id);

                // ئەگەر لە قەرز کەم کرابووەوە، قەرزەکەی دەچێتەوە سەر
                if ($saleReturn->customer_id && $saleReturn->refund_type === 'deduct_debt') {
                    Customer::where('id', $saleReturn->customer_id)->increment('balance', $saleReturn->total_amount);
                }

                // دەرهێنانەوەی کاڵا لە عەمبار
                foreach ($saleReturn->details as $detail) {
                    if ($detail->condition_type === 'normal') {
                        $factor = $this->getFactorAndWeight($detail->product, $detail->unit);
                        $deductedKg = $detail->quantity * $factor;
                        if (Schema::hasColumn('products', 'stock_kg')) {
                            Product::where('id', $detail->product_id)->decrement('stock_kg', $deductedKg);
                        } else {
                            Product::where('id', $detail->product_id)->decrement('stock', $deductedKg);
                        }
                    }
                }

                $saleReturn->details()->delete();
                $saleReturn->delete();
            });

            return redirect()->route('returns.index')->with('success', 'وەسڵی گەڕانەوە سڕایەوە و باڵانس و کۆگا گەڕانەوە باری پێشوو');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'کێشەیەک ڕوویدا: ' . $e->getMessage());
        }
    }
}