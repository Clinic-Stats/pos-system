<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SaleController extends Controller
{
  public function index()
    {
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';
        $alertCol = Schema::hasColumn('products', 'alert_quantity') ? 'alert_quantity' : null;

        // تەنها پەیوەندی category بهێڵەرەوە
        $products = Product::with('category')
            ->where('is_active', 1)
            ->get();

        $lowStockProducts = collect();
        $outOfStockProducts = $products->where($stockCol, '<=', 0);

        if ($alertCol) {
            $lowStockProducts = $products->filter(function ($item) use ($stockCol, $alertCol) {
                return $item->{$stockCol} > 0 && $item->{$stockCol} <= ($item->{$alertCol} ?: 5);
            });
        }

        $categories = Category::all();
        $units = Unit::all();
        $customers = Customer::all();

        return view('pos.index', compact(
            'products', 
            'categories', 
            'units', 
            'customers', 
            'lowStockProducts', 
            'outOfStockProducts'
        ));
    }
    public function store(Request $request)
    {
        $request->validate([
            'items'        => 'required|array|min:1',
            'payment_type' => 'required|in:cash,debt',
            'paid_amount'  => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                // پشکنینی سەرەتایی مەخزەن پێش هیچ کردارێک
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getUnitFactor($product, $unit);

                    $requestedQty = (float) $item['quantity'];
                    $neededStock = $requestedQty * $factor;
                    $availableStock = (float) $product->{$stockCol};

                    if ($availableStock <= 0) {
                        throw new \Exception("کاڵای ({$product->name}) لە کۆگا نەماوە و ناتوانرێت بفرۆشرێت!");
                    }

                    if ($neededStock > $availableStock) {
                        // حیسابکردنی ئەوەی چەند دانە/کیلۆ لەسەر بنەمای یەکە هەڵبژێردراوەکە ماوە
                        $maxPossible = $factor > 0 ? floor(($availableStock / $factor) * 100) / 100 : 0;
                        throw new \Exception("بڕی داواکراو بۆ ({$product->name}) لە مەخزەن نییە! تەنها ({$maxPossible} {$unit->name}) بەردەستە.");
                    }
                }

                $user = auth()->user() ?? User::first();
                if (!$user) {
                    $user = User::create([
                        'name'     => 'ئەدمین',
                        'email'    => 'admin@pos.com',
                        'password' => bcrypt('12345678'),
                    ]);
                }

                $subtotal = 0;
                $totalCost = 0;

                // حیسابکردنی تێچوو و کۆی فرۆشتن
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);

                    $factor = $this->getUnitFactor($product, $unit);
                    $itemPrice = (float) ($item['base_price'] ?? $product->base_sale_price);

                    $lineTotal = $item['quantity'] * ($itemPrice * $factor);
                    $lineCost  = $item['quantity'] * ($product->base_buy_price * $factor);

                    $subtotal += $lineTotal;
                    $totalCost += $lineCost;
                }

                $discount = (float) ($request->discount ?? 0);
                $totalAmount = max(0, $subtotal - $discount);
                $totalProfit = $totalAmount - $totalCost;

                $paid = ($request->payment_type === 'cash') ? $totalAmount : ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;

                $saleData = [
                    'invoice_no'       => 'INV-' . strtoupper(uniqid()),
                    'customer_id'      => $request->customer_id ?: null,
                    'user_id'          => auth()->id() ?? ($user ? $user->id : null),
                    'total_amount'     => $totalAmount,
                    'total_cost'       => $totalCost,
                    'total_profit'     => $totalProfit,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'created_at'       => $request->filled('created_at') ? Carbon::parse($request->created_at) : now(),
                ];

                if (Schema::hasColumn('sales', 'discount')) {
                    $saleData['discount'] = $discount;
                }

                $sale = Sale::create($saleData);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);

                    $factor = $this->getUnitFactor($product, $unit);
                    $itemPrice = (float) ($item['base_price'] ?? $product->base_sale_price);

                    $unitPrice  = $itemPrice * $factor;
                    $unitCost   = $product->base_buy_price * $factor;
                    $lineTotal  = $item['quantity'] * $unitPrice;
                    $lineCost   = $item['quantity'] * $unitCost;
                    $lineProfit = $lineTotal - $lineCost;

                    SaleDetail::create([
                        'sale_id'     => $sale->id,
                        'product_id'  => $product->id,
                        'unit_id'     => $unit->id,
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $unitPrice,
                        'unit_cost'   => $unitCost,
                        'subtotal'    => $lineTotal,
                        'line_total'  => $lineTotal,
                        'line_profit' => $lineProfit,
                    ]);

                    $deductedKg = $item['quantity'] * $factor;
                    $product->decrement($stockCol, $deductedKg);
                }

                return $sale;
            });

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'فرۆشتن بە سەرکەوتوویی تەواو بوو'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $sale = Sale::with('details.product', 'details.unit', 'customer')->findOrFail($id);
        $products = Product::with('category')->where('is_active', 1)->get();
        $categories = Category::all();
        $units = Unit::all();
        $customers = Customer::all();

        return view('pos.edit', compact('sale', 'products', 'categories', 'units', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'items'        => 'required|array|min:1',
            'payment_type' => 'required|in:cash,debt',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $sale = Sale::with('details')->findOrFail($id);
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                // ١. گەڕاندنەوەی بڕەکانی پێشوو بۆ کۆگا تا باڵانسەکەی ئێستا دروست بێت
                foreach ($sale->details as $oldDetail) {
                    $product = Product::find($oldDetail->product_id);
                    $unit = Unit::find($oldDetail->unit_id);
                    $factor = ($product && $unit) ? $this->getUnitFactor($product, $unit) : 1;
                    if ($product) {
                        $product->increment($stockCol, $oldDetail->quantity * $factor);
                    }
                }

                // ٢. پشکنینی کاڵاکانی فۆڕمە نوێیەکە ئایا بەشی دەکات
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getUnitFactor($product, $unit);

                    $requestedQty = (float) $item['quantity'];
                    $neededStock = $requestedQty * $factor;
                    $availableStock = (float) $product->fresh()->{$stockCol};

                    if ($availableStock <= 0) {
                        throw new \Exception("کاڵای ({$product->name}) لە کۆگا نەماوە!");
                    }

                    if ($neededStock > $availableStock) {
                        $maxPossible = $factor > 0 ? floor(($availableStock / $factor) * 100) / 100 : 0;
                        throw new \Exception("بڕی داواکراو بۆ ({$product->name}) لە کۆگا نییە! تەنها ({$maxPossible} {$unit->name}) ماوە.");
                    }
                }

                $sale->details()->delete();

                $subtotal = 0;
                $totalCost = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);

                    $factor = $this->getUnitFactor($product, $unit);
                    $itemPrice = (float) ($item['base_price'] ?? $product->base_sale_price);

                    $unitPrice = $itemPrice * $factor;
                    $unitCost  = $product->base_buy_price * $factor;
                    $lineTotal = $item['quantity'] * $unitPrice;
                    $lineCost  = $item['quantity'] * $unitCost;
                    $lineProfit = $lineTotal - $lineCost;

                    $subtotal += $lineTotal;
                    $totalCost += $lineCost;

                    SaleDetail::create([
                        'sale_id'     => $sale->id,
                        'product_id'  => $product->id,
                        'unit_id'     => $unit->id,
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $unitPrice,
                        'unit_cost'   => $unitCost,
                        'subtotal'    => $lineTotal,
                        'line_total'  => $lineTotal,
                        'line_profit' => $lineProfit,
                    ]);

                    $deductedKg = $item['quantity'] * $factor;
                    $product->decrement($stockCol, $deductedKg);
                }

                $discount = (float) ($request->discount ?? 0);
                $totalAmount = max(0, $subtotal - $discount);
                $totalProfit = $totalAmount - $totalCost;

                $paid = ($request->payment_type === 'cash') ? $totalAmount : ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;

                $updateData = [
                    'customer_id'      => $request->customer_id ?: null,
                    'total_amount'     => $totalAmount,
                    'total_cost'       => $totalCost,
                    'total_profit'     => $totalProfit,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                ];

                if (Schema::hasColumn('sales', 'discount')) {
                    $updateData['discount'] = $discount;
                }

                if ($request->filled('created_at')) {
                    $updateData['created_at'] = Carbon::parse($request->created_at);
                }

                $sale->update($updateData);
            });

            return response()->json(['success' => true, 'message' => 'وەسڵەکە بە سەرکەوتوویی نوێکرایەوە']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $sale = Sale::with('details')->findOrFail($id);
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                foreach ($sale->details as $detail) {
                    $product = Product::find($detail->product_id);
                    $unit = Unit::find($detail->unit_id);
                    $factor = ($product && $unit) ? $this->getUnitFactor($product, $unit) : 1;
                    
                    if ($product) {
                        $product->increment($stockCol, $detail->quantity * $factor);
                    }
                }

                $sale->details()->delete();
                $sale->delete();
            });

            return redirect()->route('reports.index')->with('success', 'وەسڵی فرۆشتن سڕایەوە و کاڵاکان گەڕانەوە کۆگا');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'هەڵەیەک ڕوویدا: ' . $e->getMessage());
        }
    }

    public function print(Request $request, $id)
    {
        $sale = Sale::with(['details.product', 'details.unit', 'customer', 'user'])->findOrFail($id);
        $setting = Setting::first();

        if ($request->get('type') === 'a4' || ($setting && $setting->receipt_width === 'a4')) {
            return view('pos.print_a4', compact('sale'));
        }

        return view('pos.print', compact('sale'));
    }

    private function getUnitFactor($product, $unit)
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
}