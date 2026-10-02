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
    /**
     * کۆکردنەوە بە دراو: ['USD' => x, 'IQD' => y]
     * ئەگەر خشتەکە ستوونی currency نەبوو، هەموو بە IQD دادەنرێت.
     */
    private function sumByCurrency($query, string $column = 'total_amount'): array
    {
        $out = ['USD' => 0.0, 'IQD' => 0.0];
        $table = $query->getModel()->getTable();

        if (!Schema::hasColumn($table, 'currency')) {
            $out['IQD'] = (float) (clone $query)->sum($column);
            return $out;
        }

        $rows = (clone $query)
            ->selectRaw("UPPER(COALESCE(currency,'IQD')) as cur, SUM($column) as total")
            ->groupBy('cur')
            ->pluck('total', 'cur');

        foreach ($rows as $cur => $total) {
            $out[$cur === 'USD' ? 'USD' : 'IQD'] += (float) $total;
        }
        return $out;
    }

    private function subtract(array $a, array $b): array
    {
        return ['USD' => $a['USD'] - $b['USD'], 'IQD' => $a['IQD'] - $b['IQD']];
    }

    private function add(array $a, array $b): array
    {
        return ['USD' => $a['USD'] + $b['USD'], 'IQD' => $a['IQD'] + $b['IQD']];
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdmin()) {
            $mandubs = User::whereIn('role', ['mandub', 'sales', 'user'])->get();
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

        // نرخی ئاڵوگۆڕ: چەند دینار = ١ دۆلار
        $rate = (float) (Setting::first()->exchange_rate ?? 1500);
        if ($rate <= 0) $rate = 1500;

        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate   = $request->get('end_date', Carbon::today()->toDateString());
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime   = Carbon::parse($endDate)->endOfDay();

        // فرۆشتن
        $salesQuery = Sale::where('user_id', $selectedUserId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        $totalSales = $this->sumByCurrency($salesQuery);
        $salesCount = (clone $salesQuery)->count();
        $salesList  = (clone $salesQuery)->with('customer')->latest()->get();

        // وەرگرتنەوەی قەرز
        $paymentsQuery = CustomerPayment::where('user_id', $selectedUserId)
            ->whereBetween('payment_date', [$startDate, $endDate]);
        $collectedDebt = $this->sumByCurrency($paymentsQuery, 'amount');
        $paymentsList  = (clone $paymentsQuery)->with('customer')->latest('payment_date')->get();

        // گەڕاوەکان
        $returnsQuery = SaleReturn::where('user_id', $selectedUserId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);
        $returns      = $this->sumByCurrency($returnsQuery);
        $returnsCount = (clone $returnsQuery)->count();
        $returnsList  = (clone $returnsQuery)->with('customer')->latest()->get();

        // کاشی ماوە لە دەستی مەندووب (هەموو مێژوو) بە جیا بۆ هەر دراوێک
        $allCashInSales    = $this->sumByCurrency(Sale::where('user_id', $selectedUserId)->where('payment_type', 'cash'));
        $allCashInPayments = $this->sumByCurrency(CustomerPayment::where('user_id', $selectedUserId), 'amount');
        $allHandedOver     = $this->sumByCurrency(CashHandover::where('mandub_id', $selectedUserId), 'amount');
        $netCashInHand     = $this->subtract($this->add($allCashInSales, $allCashInPayments), $allHandedOver);

        $handovers = CashHandover::with('receiver')
            ->where('mandub_id', $selectedUserId)
            ->latest('handover_date')
            ->get();

        // کۆی هەموو بە دۆلار (دینار / نرخی ئاڵوگۆڕ)
        $toUsd = fn(array $a) => $a['USD'] + ($a['IQD'] / $rate);

        return view('mandub.dashboard', compact(
            'mandubs', 'targetUser', 'startDate', 'endDate', 'rate', 'toUsd',
            'totalSales', 'salesCount', 'salesList',
            'collectedDebt', 'paymentsList',
            'returns', 'returnsCount', 'returnsList',
            'netCashInHand', 'allHandedOver', 'handovers'
        ));
    }
}