<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\CustomerPayment;
use App\Models\User;
use App\Models\CashHandover;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MandubDashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // ١. هێنانی هەموو ئەو بەکارهێنەرانەی مەندووب، فرۆشیار، یان کارمەندن بۆ ناو لیستەکە
        if ($currentUser->isAdmin()) {
            $mandubs = User::whereIn('role', ['mandub', 'sales', 'user'])->get();
            
            // ئەگەر بە هیچ کام لەو ڕۆڵانە کەس نەبوو، هەموو بەکارهێنەرەکان دەهێنێت
            if ($mandubs->isEmpty()) {
                $mandubs = User::all();
            }

            $selectedUserId = $request->get('user_id', $mandubs->first()->id ?? $currentUser->id);
            $targetUser = User::find($selectedUserId) ?? $currentUser;
        } else {
            $mandubs = collect([$currentUser]);
            $selectedUserId = $currentUser->id;
            $targetUser = $currentUser;
        }

        // ٢. فلتەری بەروار
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate   = $request->get('end_date', Carbon::today()->toDateString());

        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime   = Carbon::parse($endDate)->endOfDay();

        // ٣. فرۆشتنەکان لەم ماوەیەدا
        $salesQuery = Sale::where('user_id', $selectedUserId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        $totalSalesAmount = (clone $salesQuery)->sum('total_amount');
        $cashSalesAmount  = (clone $salesQuery)->where('payment_type', 'cash')->sum('total_amount');
        $debtSalesAmount  = (clone $salesQuery)->where('payment_type', 'debt')->sum('total_amount');
        $salesCount       = (clone $salesQuery)->count();
        $salesList        = (clone $salesQuery)->with('customer')->latest()->get();

        // ٤. وەرگرتنەوەی قەرز لەم ماوەیەدا
        $paymentsQuery = CustomerPayment::where('user_id', $selectedUserId)
            ->whereBetween('payment_date', [$startDate, $endDate]);

        $collectedDebt = (clone $paymentsQuery)->sum('amount');
        $paymentsList  = (clone $paymentsQuery)->with('customer')->latest('payment_date')->get();

        // ٥. گەڕاوەکان لەم ماوەیەدا
        $returnsQuery = SaleReturn::where('user_id', $selectedUserId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        $returnsAmount = (clone $returnsQuery)->sum('total_amount');
        $returnsCount  = (clone $returnsQuery)->count();
        $returnsList   = (clone $returnsQuery)->with('customer')->latest()->get();

        // ٦. ژمێریاری کاشی گشتی دەستی مەندووب (تەواوی مێژووی نەقد بێ بەستنەوە بە بەروار)
        $allCashInSales    = Sale::where('user_id', $selectedUserId)->where('payment_type', 'cash')->sum('total_amount');
        $allCashInPayments = CustomerPayment::where('user_id', $selectedUserId)->sum('amount');
        $allCashIn         = $allCashInSales + $allCashInPayments;

        // کۆی ئەو کاشەی تا ئێستا تەسلیمی ئەدمین کراوە
        $allHandedOver = CashHandover::where('mandub_id', $selectedUserId)->sum('amount');

        // ڕەسیدی ڕاستەقینەی نەقد لە دەستی مەندووبدا (تەسلیمنەکراو)
        $netCashInHand = $allCashIn - $allHandedOver;

        // لیستی پسوولەکانی تەسلیماتی ئەم مەندووبە
        $handovers = CashHandover::with('receiver')
            ->where('mandub_id', $selectedUserId)
            ->latest('handover_date')
            ->get();

        return view('mandub.dashboard', compact(
            'mandubs',
            'targetUser',
            'startDate',
            'endDate',
            'totalSalesAmount',
            'cashSalesAmount',
            'debtSalesAmount',
            'salesCount',
            'salesList',
            'collectedDebt',
            'paymentsList',
            'returnsAmount',
            'returnsCount',
            'returnsList',
            'netCashInHand',
            'allHandedOver',
            'handovers'
        ));
    }
}