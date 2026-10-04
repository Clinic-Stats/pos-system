<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\StockLoss;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockLossController extends Controller
{
    private function stockCol(): string
    {
        return Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';
    }

    public function index(Request $request)
    {
        $query = StockLoss::with(['product', 'customer', 'user', 'saleReturn']);

        if ($request->filled('from_date')) $query->whereDate('loss_date', '>=', $request->from_date);
        if ($request->filled('to_date'))   $query->whereDate('loss_date', '<=', $request->to_date);
        if ($request->filled('reason'))    $query->where('reason', $request->reason);
        if ($request->filled('source'))    $query->where('source', $request->source);
        if ($request->filled('product_id')) $query->where('product_id', $request->product_id);

        $totalUsd = (float) (clone $query)->sum('total_cost_usd');
        $byReason = (clone $query)->selectRaw('reason, SUM(total_cost_usd) as total, COUNT(*) as cnt')->groupBy('reason')->get()->keyBy('reason');

        $losses   = $query->latest('loss_date')->latest('id')->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();
        $rate = (float) (Setting::first()->exchange_rate ?? 1500);

        return view('losses.index', compact('losses', 'products', 'totalUsd', 'byReason', 'rate'));
    }

    /** دەرکردنی کاڵا لە کۆگا بەهۆی بەسەرچوون / تێکچوون / ونبوون */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.001',
            'reason'     => 'required|in:expired,damaged,lost,other',
            'note'       => 'nullable|string|max:255',
            'loss_date'  => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $product = Product::lockForUpdate()->findOrFail($data['product_id']);
                $col = $this->stockCol();
                $stock = (float) $product->{$col};

                if ((float) $data['quantity'] > $stock + 0.0005) {
                    throw new \Exception('بڕەکە لە کۆگای ئێستا زیاترە (کۆگا: ' . rtrim(rtrim(number_format($stock, 3), '0'), '.') . ')');
                }

                $unitCost = (float) ($product->base_buy_price ?? 0);
                $qty = (float) $data['quantity'];

                $product->decrement($col, $qty);

                StockLoss::create([
                    'product_id'     => $product->id,
                    'quantity'       => $qty,
                    'unit_cost_usd'  => $unitCost,
                    'total_cost_usd' => round($qty * $unitCost, 4),
                    'reason'         => $data['reason'],
                    'note'           => $data['note'] ?? null,
                    'source'         => 'manual',
                    'user_id'        => auth()->id(),
                    'loss_date'      => Carbon::parse($data['loss_date'])->setTime((int) date('H'), (int) date('i')),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'کاڵاکە لە کۆگا دەرکرا و وەک زیان تۆمارکرا');
    }

    /** هەڵوەشاندنەوەی دەرکردنی دەستی: کاڵاکە دەگەڕێتەوە کۆگا */
    public function destroy($id)
    {
        $loss = StockLoss::findOrFail($id);

        if ($loss->source !== 'manual') {
            return redirect()->back()->with('error', 'ئەم زیانە لە وەسڵی گەڕاوەوە هاتووە. بۆ گۆڕینی، وەسڵی گەڕاوەکە دەستکاری بکە.');
        }

        DB::transaction(function () use ($loss) {
            Product::where('id', $loss->product_id)->increment($this->stockCol(), (float) $loss->quantity);
            $loss->delete();
        });

        return redirect()->back()->with('success', 'زیانەکە هەڵوەشێنرایەوە و کاڵاکە گەڕایەوە کۆگا');
    }
}