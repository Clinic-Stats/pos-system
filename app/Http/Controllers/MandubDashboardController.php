<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\CustomerPayment;
use App\Models\User;
use App\Models\CashHandover;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MandubDashboardController extends Controller
{
    /** کۆکردنەوە بە دراو: ['USD' => x, 'IQD' => y] (ئەگەر ستوونی currency نەبوو = IQD) */
    private function sumByCurrency($query, string $column = 'total_amount'): array
    {
        $out = ['USD' => 0.0, 'IQD' => 0.0];

        if (!Schema::hasColumn($query->getModel()->getTable(), 'currency')) {
            $out['IQD'] = (float) (clone $query)->sum($column);
            return $out;
        }

        $rows = (clone $query)
            ->selectRaw("UPPER(COALESCE(currency,'IQD')) as cur, SUM($column) as total")
            ->groupBy('cur')->pluck('total', 'cur');

        foreach ($rows as $cur => $total) {
            $out[$cur === 'USD' ? 'USD' : 'IQD'] += (float) $total;
        }
        return $out;
    }

    private function add(array ...$parts): array
    {
        $out = ['USD' => 0.0, 'IQD' => 0.0];
        foreach ($parts as $p) { $out['USD'] += $p['USD']; $out['IQD'] += $p['IQD']; }
        return $out;
    }

    private function subtract(array $a, array $b): array
    {
        return ['USD' => $a['USD'] - $b['USD'], 'IQD' => $a['IQD'] - $b['IQD']];
    }

    /** پارەی ڕاستەقینە وەرگیراو لە کڕیاران: فرۆشتنی نەقد + بەشی دراوی فرۆشتنی قەرز */
    private function collectedFromSales($salesQuery): array
    {
        $cash = $this->sumByCurrency((clone $salesQuery)->where('payment_type', 'cash'), 'total_amount');
        $debtPaid = $this->sumByCurrency((clone $salesQuery)->where('payment_type', 'debt'), 'paid_amount');
        return $this->add($cash, $debtPaid);
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $canReceive = $currentUser->canReceiveCash();   // ئەدمین / کاشیر / خاوەن دەسەڵاتی receive_cash

        if ($canReceive) {
            // هەموو کارمەندان لە لیستەکەدا دەردەکەون (ئەوانەی چالاکییان هەیە لە پێشەوە)
            $activeIds = Sale::query()->distinct()->pluck('user_id')
                ->merge(CustomerPayment::query()->distinct()->pluck('user_id'))
                ->merge(CashHandover::query()->distinct()->pluck('mandub_id'))
                ->filter()->unique()->values();

            $mandubs = User::orderBy('name')->get()
                ->sortByDesc(fn($u) => $activeIds->contains($u->id))->values();
            $selectedUserId = (int) $request->get('user_id', $currentUser->id);
            $targetUser = User::find($selectedUserId) ?? $currentUser;
            $selectedUserId = $targetUser->id;
        } else {
            $mandubs = collect([$currentUser]);
            $selectedUserId = $currentUser->id;
            $targetUser = $currentUser;
        }

        $rate = (float) (Setting::first()->exchange_rate ?? 1500);
        if ($rate <= 0) $rate = 1500;
        $toUsd = fn(array $a) => $a['USD'] + ($a['IQD'] / $rate);

        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate   = $request->get('end_date', Carbon::today()->toDateString());
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime   = Carbon::parse($endDate)->endOfDay();

        // ---------- ماوەی هەڵبژێردراو ----------
        $salesQuery = Sale::where('user_id', $selectedUserId)->whereBetween('created_at', [$startDateTime, $endDateTime]);
        $totalSales = $this->sumByCurrency($salesQuery);
        $salesCount = (clone $salesQuery)->count();
        $salesList  = (clone $salesQuery)->with('customer')->latest()->get();
        $newDebt    = $this->sumByCurrency((clone $salesQuery)->where('payment_type', 'debt'), 'remaining_amount');

        $paymentsQuery = CustomerPayment::where('user_id', $selectedUserId)->whereBetween('payment_date', [$startDate, $endDate]);
        $collectedDebt = $this->sumByCurrency($paymentsQuery, 'amount');
        $paymentsCount = (clone $paymentsQuery)->count();

        $returnsQuery = SaleReturn::where('user_id', $selectedUserId)->whereBetween('created_at', [$startDateTime, $endDateTime]);
        $returns      = $this->sumByCurrency($returnsQuery);
        $returnsCount = (clone $returnsQuery)->count();

        $collectedPeriod = $this->add($this->collectedFromSales($salesQuery), $collectedDebt);

        $collectedSalesPeriod = $this->collectedFromSales($salesQuery);
        $refundsPeriod = $this->sumByCurrency((clone $returnsQuery)->where('refund_type', 'cash'), 'total_amount');
        $paymentsList  = (clone $paymentsQuery)->with('customer')->latest('payment_date')->get();
        $handoversPeriod = CashHandover::with('receiver')->where('mandub_id', $selectedUserId)
            ->whereBetween('handover_date', [$startDateTime, $endDateTime])->latest('handover_date')->get();

        $handedPeriod = $this->sumByCurrency(
            CashHandover::where('mandub_id', $selectedUserId)->whereBetween('handover_date', [$startDateTime, $endDateTime]), 'amount');

        // ---------- هەموو مێژوو: ئەوەی لە دەستیدا ماوە ----------
        $allSales   = $this->collectedFromSales(Sale::where('user_id', $selectedUserId));
        $allPays    = $this->sumByCurrency(CustomerPayment::where('user_id', $selectedUserId), 'amount');
        $allRefunds = $this->sumByCurrency(SaleReturn::where('user_id', $selectedUserId)->where('refund_type', 'cash'), 'total_amount');
        $allHanded  = $this->sumByCurrency(CashHandover::where('mandub_id', $selectedUserId), 'amount');

        $allCollected  = $this->add($allSales, $allPays);
        $netCashInHand = $this->subtract($this->subtract($allCollected, $allRefunds), $allHanded);
        $allHandedOver = $allHanded;

        $handovers = CashHandover::with(['receiver', 'mandub'])
            ->where('mandub_id', $selectedUserId)->latest('handover_date')->get();

        // کەشفی حسابی چاپکراو (A4): هەمان ئادرێس بە ?print=1
        if ($request->boolean('print')) {
            $setting = Setting::first();
            return view('mandub.statement', compact(
                'targetUser', 'startDate', 'endDate', 'setting', 'salesList', 'paymentsList', 'handoversPeriod',
                'collectedSalesPeriod', 'collectedDebt', 'refundsPeriod', 'handedPeriod', 'netCashInHand'
            ));
        }

        return view('mandub.dashboard', compact(
            'mandubs', 'targetUser', 'canReceive', 'startDate', 'endDate', 'rate', 'toUsd',
            'totalSales', 'salesCount', 'salesList', 'newDebt',
            'collectedDebt', 'paymentsCount', 'collectedPeriod', 'handedPeriod',
            'returns', 'returnsCount',
            'allCollected', 'allRefunds', 'netCashInHand', 'allHandedOver', 'handovers'
        ));
    }
}