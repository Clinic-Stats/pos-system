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
        return $this->posView();
    }

    /**
     * ڤیوی POS - هەم بۆ فرۆشتنی نوێ هەم بۆ دەستکاری (بە $extraProductIds کاڵای ناچالاکی ناو وەسڵە کۆنەکەش دێت)
     */
    private function posView(array $extraProductIds = [])
    {
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';
        $alertCol = Schema::hasColumn('products', 'alert_quantity') ? 'alert_quantity' : null;

        $products = Product::with('category')
            ->where('is_active', 1)
            ->when(!empty($extraProductIds), fn($q) => $q->orWhereIn('id', $extraProductIds))
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
        $setting = Setting::first();

        return view('pos.index', compact(
            'products',
            'categories',
            'units',
            'customers',
            'lowStockProducts',
            'outOfStockProducts',
            'setting'
        ));
    }

    /**
     * لیستی هەموو فرۆشتنەکان
     */
    public function listSales(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'details.unit']);

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', trim($request->payment_type));
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->latest()->paginate(20);
        $customers = Customer::all();

        return view('sales.list', compact('sales', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'         => 'required|array|min:1',
            'payment_type'  => 'required|in:cash,debt',
            'paid_amount'   => 'nullable|numeric|min:0',
            'discount'      => 'nullable|numeric|min:0',
            'currency'      => 'required|in:USD,IQD',
            'exchange_rate' => 'required|numeric|min:1',
            'guest_name'    => 'nullable|string|max:255',
            'guest_phone'   => 'nullable|string|max:50',
            'guest_address' => 'nullable|string|max:255',
            'order_id'      => 'nullable|integer',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getUnitFactor($product, $unit);

                    $neededStock = (float) $item['quantity'] * $factor;
                    $availableStock = (float) $product->{$stockCol};

                    if ($availableStock <= 0) {
                        throw new \Exception("کاڵای ({$product->name}) لە کۆگا نەماوە و ناتوانرێت بفرۆشرێت!");
                    }

                    if ($neededStock > $availableStock) {
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

                $currency = $request->currency;
                $exchangeRate = (float) $request->exchange_rate;

                [$lines, $subtotal, $totalCost] = $this->buildLines($request->items, $currency, $exchangeRate);

                $discount = (float) ($request->discount ?? 0);
                $totalAmount = max(0, $subtotal - $discount);
                $totalProfit = $totalAmount - $totalCost;

                $paid = ($request->payment_type === 'cash') ? $totalAmount : (float) ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;

                $saleData = [
                    'invoice_no'       => $this->nextInvoiceNo(),   // ژمارەی ڕیزبەند: 1, 2, 3 ... (بێ بەتاڵی)
                    'customer_id'      => $request->customer_id ?: null,
                    'user_id'          => auth()->id() ?? $user->id,
                    'total_amount'     => $totalAmount,
                    'total_cost'       => $totalCost,
                    'total_profit'     => $totalProfit,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'currency'         => $currency,
                    'exchange_rate'    => $exchangeRate,
                    'created_at'       => $request->filled('created_at') ? Carbon::parse($request->created_at) : now(),
                ];

                if (Schema::hasColumn('sales', 'discount')) {
                    $saleData['discount'] = $discount;
                }

                // کڕیاری ئاسایی (نەتۆمارکراو): ناو / مۆبایل / ناونیشان لەسەر وەسڵەکە
                if (!$request->customer_id && Schema::hasColumn('sales', 'guest_name')) {
                    $saleData['guest_name']    = $request->guest_name ?: null;
                    $saleData['guest_phone']   = $request->guest_phone ?: null;
                    $saleData['guest_address'] = $request->guest_address ?: null;
                }

                $sale = Sale::create($saleData);
                $this->saveLines($sale, $lines, $stockCol);

                // ئەگەر لە داواکاری کڕیارەوە هاتووە: بیکە بە «قبوڵکراو» و بیبەستەرەوە بە وەسڵەکە
                if ($request->filled('order_id')) {
                    \App\Models\CustomerOrder::where('id', $request->order_id)
                        ->whereIn('status', ['pending', 'rejected'])
                        ->update([
                            'status'     => 'accepted',
                            'sale_id'    => $sale->id,
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                        ]);
                }

                return $sale;
            });

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'فرۆشتن بە سەرکەوتوویی تەواو بوو',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * قبوڵکردنی داواکاری کڕیار: POS دەکرێتەوە و کاڵاکانی داواکارییەکە دەچنە ناو سەبەتە
     * (کڕیاری تۆمارکراو = قەرز، کڕیاری ئاسایی = نەقد؛ دوای پسوولەکردن داواکارییەکە دەبێتە «قبوڵکراو»)
     */
    public function fromOrder($id)
    {
        $order = \App\Models\CustomerOrder::with('items.product', 'items.unit')->findOrFail($id);

        if (!in_array($order->status, ['pending', 'rejected'])) {
            return redirect()->route('orders.index', ['status' => $order->status])
                ->with('success', 'ئەم داواکارییە پێشتر مامەڵەی لەسەر کراوە');
        }

        $fromOrder = [
            'id'          => $order->id,
            'order_no'    => $order->order_no,
            'customer_id' => $order->customer_id,
            'name'        => $order->name,
            'phone'       => $order->phone,
            'address'     => $order->address,
            'note'        => $order->note,
            'latitude'    => $order->latitude,
            'longitude'   => $order->longitude,
            'items'       => $order->items->map(fn($i) => [
                'product_id' => $i->product_id,
                'unit_id'    => $i->unit_id,
                'quantity'   => (float) $i->quantity,
                'name'       => $i->product->name ?? '',
            ])->values(),
        ];

        return $this->posView($order->items->pluck('product_id')->all())
            ->with('fromOrder', $fromOrder);
    }

    /**
     * دەستکاری: هەمان لاپەڕەی POS دەکرێتەوە و وەسڵەکە دەچێتە ناو سەبەتە
     */
    public function edit($id)
    {
        $sale = Sale::with('details')->findOrFail($id);
        $currency = $sale->currency ?: 'USD';
        $rate = (float) ($sale->exchange_rate ?: 1500);

        $items = $sale->details->map(function ($d) use ($currency, $rate) {
            $product = Product::find($d->product_id);
            $unit = Unit::find($d->unit_id);
            $factor = ($product && $unit) ? $this->getUnitFactor($product, $unit) : 1;

            // unit_price = نرخی یەکە بە دراوی وەسڵ؛ دەیگەڕێنینەوە بۆ نرخی ١ کیلۆ بە دۆلار
            $perBase = $factor > 0 ? ((float) $d->unit_price / $factor) : (float) $d->unit_price;
            $priceUsd = $currency === 'IQD' ? $perBase / $rate : $perBase;

            return [
                'product_id' => $d->product_id,
                'unit_id'    => $d->unit_id,
                'quantity'   => (float) $d->quantity,
                'price_usd'  => round($priceUsd, 6),
            ];
        })->values();

        $editSale = [
            'id'            => $sale->id,
            'invoice_no'    => $sale->invoice_no,
            'customer_id'   => $sale->customer_id,
            'guest_name'    => $sale->guest_name ?? null,
            'guest_phone'   => $sale->guest_phone ?? null,
            'guest_address' => $sale->guest_address ?? null,
            'payment_type'  => $sale->payment_type,
            'paid_amount'   => (float) $sale->paid_amount,
            'discount'      => (float) ($sale->discount ?? 0),
            'created_at'    => $sale->created_at->format('Y-m-d\TH:i'),
            'currency'      => $currency,
            'exchange_rate' => $rate,
            'items'         => $items,
        ];

        return $this->posView($sale->details->pluck('product_id')->all())
            ->with('editSale', $editSale);
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
                \App\Models\ActivityLog::stash($sale);
                $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

                // 1) گەڕاندنەوەی کۆگای فرۆشتنە کۆنەکە
                foreach ($sale->details as $oldDetail) {
                    $product = Product::find($oldDetail->product_id);
                    $unit = Unit::find($oldDetail->unit_id);
                    $factor = ($product && $unit) ? $this->getUnitFactor($product, $unit) : 1;
                    if ($product) {
                        $product->increment($stockCol, $oldDetail->quantity * $factor);
                    }
                }

                // 2) پشکنینی کۆگا
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $unit = Unit::findOrFail($item['unit_id']);
                    $factor = $this->getUnitFactor($product, $unit);

                    $neededStock = (float) $item['quantity'] * $factor;
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

                $currency = $request->currency ?? ($sale->currency ?? 'USD');
                $exchangeRate = (float) ($request->exchange_rate ?? $sale->exchange_rate ?? 1500);

                [$lines, $subtotal, $totalCost] = $this->buildLines($request->items, $currency, $exchangeRate);
                $this->saveLines($sale, $lines, $stockCol);

                $discount = (float) ($request->discount ?? 0);
                $totalAmount = max(0, $subtotal - $discount);
                $totalProfit = $totalAmount - $totalCost;

                $paid = ($request->payment_type === 'cash') ? $totalAmount : (float) ($request->paid_amount ?? 0);
                $remaining = $totalAmount - $paid;

                $updateData = [
                    'customer_id'      => $request->customer_id ?: null,
                    'total_amount'     => $totalAmount,
                    'total_cost'       => $totalCost,
                    'total_profit'     => $totalProfit,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_type'     => $request->payment_type ?? 'cash',
                    'currency'         => $currency,
                    'exchange_rate'    => $exchangeRate,
                ];

                if (Schema::hasColumn('sales', 'discount')) {
                    $updateData['discount'] = $discount;
                }

                if ($request->filled('created_at')) {
                    $updateData['created_at'] = Carbon::parse($request->created_at);
                }

                if (Schema::hasColumn('sales', 'guest_name')) {
                    $hasCustomer = !empty($request->customer_id);
                    $updateData['guest_name']    = $hasCustomer ? null : ($request->guest_name ?: null);
                    $updateData['guest_phone']   = $hasCustomer ? null : ($request->guest_phone ?: null);
                    $updateData['guest_address'] = $hasCustomer ? null : ($request->guest_address ?: null);
                }

                $sale->update($updateData);
            });

            return response()->json([
                'success' => true,
                'sale_id' => (int) $id,
                'message' => 'وەسڵەکە بە سەرکەوتوویی نوێکرایەوە',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $sale = Sale::with('details')->findOrFail($id);
                \App\Models\ActivityLog::stash($sale);
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

            return redirect()->route('sales.list')->with('success', 'وەسڵی فرۆشتن سڕایەوە و کاڵاکان گەڕانەوە کۆگا');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'هەڵەیەک ڕوویدا: ' . $e->getMessage());
        }
    }

    /**
     * چاپ: ?type=a4 -> A4 | ?type=small -> بچووک | بێ type -> بەپێی ڕێکخستنی وەسڵ
     */
    public function print(Request $request, $id)
    {
        $sale = Sale::with(['details.product', 'details.unit', 'customer', 'user'])->findOrFail($id);
        $setting = Setting::first();
        $type = $request->get('type');

        $isUsd = ($sale->currency ?? 'IQD') === 'USD';
        $rate  = (float) ($sale->exchange_rate ?: 1500);
        if ($rate <= 0) $rate = 1500;

        // ═══════ قەرزی کڕیار بە جیا بۆ هەر دراوێک (هەمان لۆجیکی کەشفی حیساب) ═══════
        $debtBefore = ['USD' => 0.0, 'IQD' => 0.0];
        $debtAfter  = ['USD' => 0.0, 'IQD' => 0.0];
        $showDebt   = false;

        if ($sale->customer_id && $sale->customer) {
            $norm    = fn($c) => Customer::normalizeCurrency($c);
            $saleAt  = $sale->created_at;
            $saleDay = $saleAt->toDateString();

            // ١. وەسڵە فرۆشتنەکانی پێش ئەم وەسڵە (کڕین − پارەی دراو لە کاتی وەسڵەکە)
            $prevSales = Sale::where('customer_id', $sale->customer_id)
                ->where('id', '!=', $sale->id)
                ->get()
                ->filter(fn($s) => $s->created_at->lt($saleAt)
                    || ($s->created_at->eq($saleAt) && $s->id < $sale->id));

            foreach ($prevSales as $s) {
                $debtBefore[$norm($s->currency)] += (float) $s->total_amount - (float) $s->paid_amount;
            }

            // ٢. وەرگرتنەوەی قەرزەکان (تا ڕۆژی ئەم وەسڵە، چونکە payment_date کاتی نییە)
            foreach ($sale->customer->payments as $pay) {
                $d = $pay->payment_date ? Carbon::parse($pay->payment_date) : $pay->created_at;
                if ($d->toDateString() <= $saleDay) {
                    $debtBefore[$norm($pay->currency)] -= (float) $pay->amount;
                }
            }

            // ٣. گەڕاوەکانی کاڵا کە لە قەرز دادەشکێن
            foreach ($sale->customer->returns as $ret) {
                if ($ret->refund_type === 'deduct_debt' && $ret->created_at && $ret->created_at->lt($saleAt)) {
                    $debtBefore[$norm($ret->currency)] -= (float) $ret->total_amount;
                }
            }

            // قەرزی دوای ئەم وەسڵە = پێشوو + ماوەی ئەم وەسڵە
            $debtAfter = $debtBefore;
            $thisRemain = (float) $sale->total_amount - (float) $sale->paid_amount;
            $debtAfter[$norm($sale->currency)] += $thisRemain;

            foreach (['USD', 'IQD'] as $c) {
                $debtBefore[$c] = round($debtBefore[$c], 2);
                $debtAfter[$c]  = round($debtAfter[$c], 2);
            }

            $showDebt = $sale->payment_type === 'debt'
                || abs($debtBefore['USD']) > 0.004
                || abs($debtBefore['IQD']) > 0.004;
        }

        // کۆی گشتی قەرز بە دۆلار (دینار دەگۆڕدرێت بۆ دۆلار)
        $debtBeforeUsd = $debtBefore['USD'] + ($debtBefore['IQD'] / $rate);
        $debtAfterUsd  = $debtAfter['USD']  + ($debtAfter['IQD']  / $rate);

        // شوێنی کڕیار (QR لەسەر وەسڵ): ئەگەر ئەم وەسڵە لە داواکاری کڕیارەوە هاتبێت و شوێنی ناردبێت
        $mapUrl = null;
        $orderLoc = \App\Models\CustomerOrder::where('sale_id', $sale->id)
            ->whereNotNull('latitude')->whereNotNull('longitude')->first();
        if ($orderLoc) {
            $mapUrl = 'https://www.google.com/maps?q=' . $orderLoc->latitude . ',' . $orderLoc->longitude;
        }

        $shared = compact(
            'sale', 'setting', 'showDebt',
            'debtBefore', 'debtAfter',
            'debtBeforeUsd', 'debtAfterUsd',
            'isUsd', 'rate', 'mapUrl'
        );

        if ($type === 'a4') {
            return view('pos.print_a4', $shared);
        }
        if ($type === 'small') {
            return view('pos.print', $shared);
        }
        if ($setting && $setting->receipt_width === 'a4') {
            return view('pos.print_a4', $shared);
        }
        return view('pos.print', $shared);
    }

    // ---------------------------------------------------------------
    // یاریدەدەرەکان (کۆدی دووبارەی store و update یەکخراوە)
    // ---------------------------------------------------------------

    /**
     * هەژمارکردنی هەموو هێڵەکانی وەسڵ
     * @return array [lines, subtotal, totalCost]
     */
    private function buildLines(array $items, string $currency, float $exchangeRate): array
    {
        $lines = [];
        $subtotal = 0;
        $totalCost = 0;

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unit = Unit::findOrFail($item['unit_id']);
            $factor = $this->getUnitFactor($product, $unit);

            $itemPriceUsd = (float) ($item['base_price'] ?? $product->base_sale_price);
            $itemCostUsd = (float) $product->base_buy_price;

            $unitPrice = $currency === 'IQD' ? $itemPriceUsd * $exchangeRate : $itemPriceUsd;
            $unitCost  = $currency === 'IQD' ? $itemCostUsd * $exchangeRate : $itemCostUsd;

            $qty = (float) $item['quantity'];
            $lineTotal = $qty * $unitPrice * $factor;
            $lineCost  = $qty * $unitCost * $factor;

            $subtotal += $lineTotal;
            $totalCost += $lineCost;

            $lines[] = [
                'product'     => $product,
                'unit'        => $unit,
                'factor'      => $factor,
                'quantity'    => $qty,
                'unit_price'  => $unitPrice * $factor,
                'unit_cost'   => $unitCost * $factor,
                'line_total'  => $lineTotal,
                'line_profit' => $lineTotal - $lineCost,
            ];
        }

        return [$lines, $subtotal, $totalCost];
    }

    private function saveLines(Sale $sale, array $lines, string $stockCol): void
    {
        foreach ($lines as $l) {
            SaleDetail::create([
                'sale_id'     => $sale->id,
                'product_id'  => $l['product']->id,
                'unit_id'     => $l['unit']->id,
                'quantity'    => $l['quantity'],
                'unit_price'  => $l['unit_price'],
                'unit_cost'   => $l['unit_cost'],
                'subtotal'    => $l['line_total'],
                'line_total'  => $l['line_total'],
                'line_profit' => $l['line_profit'],
            ]);

            $l['product']->decrement($stockCol, $l['quantity'] * $l['factor']);
        }
    }

    /**
     * ژمارەی وەسڵی داهاتوو: 1, 2, 3 ...
     * دەبێت لە ناو DB::transaction ی فرۆشتنەکەدا بانگ بکرێت. ڕیزی ژمارەدەرەکە قفڵ دەکرێت تا کۆتایی
     * transaction، بۆیە لە یەک کاتدا چەند کەس وەسڵ بکەن ژمارەکان تێکەڵ نابن، و ئەگەر
     * فرۆشتنەکە شکست بهێنێت ژمارەکە دەگەڕێتەوە (هیچ ژمارەیەک نادزرێت).
     */
    private function nextInvoiceNo(): string
    {
        $row = DB::table('invoice_counters')->where('name', 'sales')->lockForUpdate()->first();

        if (!$row) {   // یەکەم جار (ئەگەر migration ڕیزەکەی دروست نەکردبێت)
            DB::table('invoice_counters')->insertOrIgnore(['name' => 'sales', 'last_number' => 0, 'created_at' => now(), 'updated_at' => now()]);
            $row = DB::table('invoice_counters')->where('name', 'sales')->lockForUpdate()->first();
        }

        $next = (int) $row->last_number + 1;

        // ئەگەر ئەم ژمارەیە پێشتر بەکارهاتبێت (نموونە وەسڵی کۆن)، بپەڕێنە بۆ یەکەم ژمارەی بەتاڵ
        while (Sale::where('invoice_no', (string) $next)->exists()) {
            $next++;
        }

        DB::table('invoice_counters')->where('name', 'sales')->update(['last_number' => $next, 'updated_at' => now()]);

        return (string) $next;
    }

    private function getUnitFactor($product, $unit)
    {
        $unitName = mb_strtolower(trim($unit->name));

        // کاڵای کارتۆنی: بە کارتۆن دەفرۆشرێت و کۆگا بە کارتۆن دەژمێردرێت
        if (($product->sell_type ?? 'weight') === 'carton') {
            return 1.0;
        }

        if (str_contains($unitName, 'کارتۆن') || str_contains($unitName, 'carton')) {
            return (float) ($product->kg_per_carton ?: 1);
        }

        if (str_contains($unitName, 'تەن') || str_contains($unitName, 'ton')) {
            return 1000.0;
        }

        return (float) ($unit->factor_to_base ?: 1);
    }
}