<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Setting;
use App\Models\StockLoss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function empty(): array { return ['USD' => 0.0, 'IQD' => 0.0]; }

    /** کۆکردنەوەی کۆڵێکشن بە دراو */
    private function byCur($collection, string $col): array
    {
        $out = $this->empty();
        foreach ($collection as $row) {
            $cur = strtoupper($row->currency ?? 'IQD') === 'USD' ? 'USD' : 'IQD';
            $out[$cur] += (float) $row->{$col};
        }
        return $out;
    }

    /** کۆکردنەوەی کوێری بە دراو (ئەگەر ستوونی currency نەبوو = IQD) */
    private function queryByCur($query, string $col = 'amount'): array
    {
        $out = $this->empty();
        $table = $query->getModel()->getTable();
        if (!Schema::hasColumn($table, 'currency')) {
            $out['IQD'] = (float) (clone $query)->sum($col);
            return $out;
        }
        $rows = (clone $query)
            ->selectRaw("UPPER(COALESCE(currency,'IQD')) as cur, SUM($col) as total")
            ->groupBy('cur')->pluck('total', 'cur');
        foreach ($rows as $cur => $t) {
            $out[$cur === 'USD' ? 'USD' : 'IQD'] += (float) $t;
        }
        return $out;
    }

    private function add(array $a, array $b): array { return ['USD' => $a['USD'] + $b['USD'], 'IQD' => $a['IQD'] + $b['IQD']]; }
    private function sub(array $a, array $b): array { return ['USD' => $a['USD'] - $b['USD'], 'IQD' => $a['IQD'] - $b['IQD']]; }

    public function index(Request $request)
    {
        $rate = (float) (Setting::first()->exchange_rate ?? 1500);
        if ($rate <= 0) $rate = 1500;
        $toUsd = fn(array $a) => $a['USD'] + ($a['IQD'] / $rate);

        // فلتەری کات
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        switch ($request->period) {
            case 'today':     $fromDate = Carbon::today()->startOfDay(); $toDate = Carbon::today()->endOfDay(); break;
            case 'yesterday': $fromDate = Carbon::yesterday()->startOfDay(); $toDate = Carbon::yesterday()->endOfDay(); break;
            case 'week':      $fromDate = Carbon::now()->startOfWeek(); $toDate = Carbon::now()->endOfWeek(); break;
            case 'month':     $fromDate = Carbon::now()->startOfMonth(); $toDate = Carbon::now()->endOfMonth(); break;
        }
        $hasRange = $fromDate && $toDate;

        // فرۆشتن
        $salesQuery = Sale::with(['customer', 'user', 'details.product', 'details.unit']);
        if ($hasRange) $salesQuery->whereBetween('created_at', [$fromDate, $toDate]);
        $sales = (clone $salesQuery)->latest('created_at')->get();

        $cashSales = $sales->where('payment_type', 'cash');
        $debtSales = $sales->where('payment_type', 'debt');

        $totalSalesCash = $this->byCur($cashSales, 'total_amount');
        $totalSalesDebt = $this->byCur($debtSales, 'total_amount');
        $totalSalesAll  = $this->add($totalSalesCash, $totalSalesDebt);
        $totalCostAll   = $this->byCur($sales, 'total_cost');
        $totalGrossProfit = $this->byCur($sales, 'total_profit');
        $debtPaidAtSale = $this->byCur($debtSales, 'paid_amount');   // بەشی دراو لە کاتی فرۆشتنی قەرز

        // وەرگرتنەوەی قەرز
        $paymentsQuery = CustomerPayment::query();
        if ($hasRange) $paymentsQuery->whereBetween('payment_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
        $totalDebtCollected = $this->queryByCur($paymentsQuery, 'amount');

        // گەڕاوەکان
        $returnsQuery = SaleReturn::query();
        if ($hasRange) $returnsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        $totalCashReturns = $this->queryByCur((clone $returnsQuery)->where('refund_type', 'cash'), 'total_amount');

        // خەرجی
        $totalExpenses = $this->empty();
        if (Schema::hasTable('expenses')) {
            $expenseQuery = Expense::query();
            if ($hasRange) $expenseQuery->whereBetween('date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            $totalExpenses = $this->queryByCur($expenseQuery, 'amount');
        }

        // زیانی کاڵای بەسەرچوو / تەلەف (بەهای کڕین، بە دۆلار)
        $lossUsd = 0.0;
        if (Schema::hasTable('stock_losses')) {
            $lossQuery = StockLoss::query();
            if ($hasRange) $lossQuery->whereBetween('loss_date', [$fromDate, $toDate]);
            $lossUsd = (float) $lossQuery->sum('total_cost_usd');
        }
        $totalLosses = ['USD' => $lossUsd, 'IQD' => $lossUsd * $rate];

        // کاشی بەردەست و قازانجی سافی (هەر دراوێک بە جیا)
        $cashInHand    = $this->sub($this->add($this->add($totalSalesCash, $debtPaidAtSale), $totalDebtCollected), $totalCashReturns);
        $realNetProfit = $this->sub($this->sub($totalGrossProfit, $totalExpenses), ['USD' => $lossUsd, 'IQD' => 0.0]);

        // قەرزی کڕیاران بە هەر دراوێک
        $totalCustomerDebts = $this->empty();
        foreach (Customer::with(['sales', 'payments', 'returns'])->get() as $c) {
            foreach (['USD', 'IQD'] as $cur) {
                $inCur = fn($col) => $col->filter(fn($r) => (strtoupper($r->currency ?? 'IQD') === 'USD' ? 'USD' : 'IQD') === $cur);
                $buy  = $inCur($c->sales)->sum('total_amount');
                $paid = $inCur($c->sales)->sum('paid_amount')
                      + $inCur($c->payments)->sum('amount')
                      + $inCur($c->returns->where('refund_type', 'deduct_debt'))->sum('total_amount');
                $totalCustomerDebts[$cur] += max(0, $buy - $paid);
            }
        }

        // مەخزەن: نرخی کاڵا لە داتابەیس بە دۆلارە
        $stockCostUsd = 0; $stockValueUsd = 0; $totalStockKg = 0;
        foreach (Product::all() as $p) {
            $qty = (float) ($p->stock_kg ?? $p->stock ?? 0);
            if ($qty > 0) {
                $totalStockKg += $qty;
                $stockCostUsd  += $qty * (float) $p->base_buy_price;
                $stockValueUsd += $qty * (float) $p->base_sale_price;
            }
        }
        $stockCost   = ['USD' => $stockCostUsd,  'IQD' => $stockCostUsd * $rate];
        $stockValue  = ['USD' => $stockValueUsd, 'IQD' => $stockValueUsd * $rate];
        $stockProfit = ['USD' => max(0, $stockValueUsd - $stockCostUsd), 'IQD' => max(0, $stockValueUsd - $stockCostUsd) * $rate];

        // پڕفرۆشترین کاڵاکان
        $topProductsQuery = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_qty'));
        if ($hasRange) $topProductsQuery->whereBetween('sales.created_at', [$fromDate, $toDate]);
        $topProducts = $topProductsQuery->groupBy('products.id', 'products.name')->orderByDesc('total_qty')->limit(5)->get();

        // زۆرترین کڕیاران: هەموو دەگۆڕدرێت بۆ دۆلار
        $topCustomersQuery = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->selectRaw("customers.name, SUM(CASE WHEN UPPER(COALESCE(sales.currency,'IQD')) = 'USD' THEN sales.total_amount ELSE sales.total_amount / ? END) as total_spent", [$rate]);
        if ($hasRange) $topCustomersQuery->whereBetween('sales.created_at', [$fromDate, $toDate]);
        $topCustomers = $topCustomersQuery->groupBy('customers.id', 'customers.name')->orderByDesc('total_spent')->limit(5)->get();

        $paginatedSales = $salesQuery->latest('created_at')->paginate(15)->withQueryString();

        return view('reports.index', compact(
            'rate', 'toUsd', 'paginatedSales',
            'totalSalesAll', 'totalSalesCash', 'totalSalesDebt', 'totalCostAll', 'totalGrossProfit',
            'realNetProfit', 'debtPaidAtSale', 'totalDebtCollected', 'totalCashReturns', 'totalExpenses', 'cashInHand',
            'totalCustomerDebts', 'totalLosses', 'stockCost', 'stockValue', 'stockProfit', 'totalStockKg',
            'topProducts', 'topCustomers'
        ));
    }
}