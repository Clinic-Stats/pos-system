<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // فلتەری کات
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        if ($request->filled('period')) {
            if ($request->period === 'today') {
                $fromDate = Carbon::today()->startOfDay();
                $toDate   = Carbon::today()->endOfDay();
            } elseif ($request->period === 'yesterday') {
                $fromDate = Carbon::yesterday()->startOfDay();
                $toDate   = Carbon::yesterday()->endOfDay();
            } elseif ($request->period === 'week') {
                $fromDate = Carbon::now()->startOfWeek();
                $toDate   = Carbon::now()->endOfWeek();
            } elseif ($request->period === 'month') {
                $fromDate = Carbon::now()->startOfMonth();
                $toDate   = Carbon::now()->endOfMonth();
            }
        }

        // ١. فلتەرکردنی فرۆشتنەکان
        $salesQuery = Sale::with(['customer', 'user', 'details.product', 'details.unit']);
        if ($fromDate && $toDate) {
            $salesQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $sales = $salesQuery->latest('created_at')->get();

        // فرۆشتنی نەقد و قەرز
        $totalSalesCash = $sales->where('payment_type', 'cash')->sum('total_amount');
        $totalSalesDebt = $sales->where('payment_type', 'debt')->sum('total_amount');
        $totalSalesAll  = $totalSalesCash + $totalSalesDebt;

        // کۆی تێچوو و قازانجی کاڵا فرۆشراوەکان
        $totalCostAll   = $sales->sum('total_cost');
        $totalGrossProfit = $sales->sum('total_profit');

        // ٢. وەرگرتنەوەی قەرز لە کڕیاران لەم ماوەیەدا (Cash Inflow)
        $paymentsQuery = CustomerPayment::query();
        if ($fromDate && $toDate) {
            $paymentsQuery->whereBetween('payment_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
        }
        $totalDebtCollected = $paymentsQuery->sum('amount');

        // ٣. گەڕاوەی فرۆشتن بە نەقد (Cash Outflow)
        $returnsQuery = SaleReturn::query();
        if ($fromDate && $toDate) {
            $returnsQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }
        $totalCashReturns = $returnsQuery->where('refund_type', 'cash')->sum('total_amount');
        $totalAllReturns  = $returnsQuery->sum('total_amount');

        // ٤. کۆی خەرجییەکان لەم ماوەیەدا
        $totalExpenses = 0;
        if (class_exists(Expense::class) && Schema::hasTable('expenses')) {
            $expenseQuery = Expense::query();
            if ($fromDate && $toDate) {
                $expenseQuery->whereBetween('date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')]);
            }
            $totalExpenses = $expenseQuery->sum('amount');
        }

        // ٥. حسابی کۆتایی ڕۆژ: کاشی بەردەست (مەسروفاتی لێ دەرناکرێت)
        $cashInHand = ($totalSalesCash + $totalDebtCollected) - $totalCashReturns;

        // قازانجی پوختەی کارگێڕی (قازانجی فرۆشتن - خەرجییەکان)
        $realNetProfit = $totalGrossProfit - $totalExpenses;

        // ٦. کۆی گشتی قەرزی ماوە لای هەموو کڕیاران
        $customers = Customer::with(['sales', 'payments', 'returns'])->get();
        $totalCustomerDebts = 0;
        foreach ($customers as $c) {
            $cBuy = $c->sales->sum('total_amount');
            $cReturnsDeducted = $c->returns->where('refund_type', 'deduct_debt')->sum('total_amount');
            $cPaid = $c->sales->sum('paid_amount') + $c->payments->sum('amount') + $cReturnsDeducted;
            $totalCustomerDebts += max(0, $cBuy - $cPaid);
        }

        // ٧. هەژمارکردنی کاڵای ماوە لە مەخزەن (بە قازانج و بێ قازانج)
        $products = Product::all();
        $stockCostWithoutProfit = 0; // کۆی سەرمایەی مەخزەن بە تێچووی کڕین
        $stockValueWithProfit = 0;    // کۆی بەهای مەخزەن بە نرخی فرۆشتن
        $totalStockKg = 0;

        foreach ($products as $prod) {
            $qty = (float)($prod->stock_kg ?? $prod->stock ?? 0);
            if ($qty > 0) {
                $totalStockKg += $qty;
                $stockCostWithoutProfit += ($qty * (float)$prod->base_buy_price);
                $stockValueWithProfit += ($qty * (float)$prod->base_sale_price);
            }
        }
        $expectedStockProfit = max(0, $stockValueWithProfit - $stockCostWithoutProfit);

        // ٨. پێنج پڕفرۆشترین کاڵا
        $topProductsQuery = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('products.name', DB::raw('SUM(sale_details.quantity) as total_qty'));

        if ($fromDate && $toDate) {
            $topProductsQuery->whereBetween('sales.created_at', [$fromDate, $toDate]);
        }

        $topProducts = $topProductsQuery->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ٩. پێنج زۆرترین کڕیار
        $topCustomersQuery = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('SUM(sales.total_amount) as total_spent'));

        if ($fromDate && $toDate) {
            $topCustomersQuery->whereBetween('sales.created_at', [$fromDate, $toDate]);
        }

        $topCustomers = $topCustomersQuery->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // پەیجینەیشن بۆ خشتەی وەسڵەکان
        $paginatedSales = $salesQuery->paginate(15)->withQueryString();

        return view('reports.index', compact(
            'paginatedSales',
            'totalSalesAll',
            'totalSalesCash',
            'totalSalesDebt',
            'totalCostAll',
            'totalGrossProfit',
            'realNetProfit',
            'totalDebtCollected',
            'totalCashReturns',
            'totalExpenses',
            'cashInHand',
            'totalCustomerDebts',
            'stockCostWithoutProfit',
            'stockValueWithProfit',
            'expectedStockProfit',
            'totalStockKg',
            'topProducts',
            'topCustomers'
        ));
    }
}