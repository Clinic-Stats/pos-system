<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['sales', 'payments', 'returns'])->latest()->get();
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($request->only('name', 'phone', 'address'));
        return redirect()->back()->with('success', 'کڕیار بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only('name', 'phone', 'address'));

        return redirect()->back()->with('success', 'زانیارییەکانی کڕیار بە سەرکەوتوویی نوێکرایەوە');
    }

    public function addPayment(Request $request, $id)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'currency'     => 'required|in:USD,IQD',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        Customer::findOrFail($id);

        CustomerPayment::create([
            'customer_id'  => $id,
            'user_id'      => auth()->id(),
            'amount'       => $request->amount,
            'currency'     => $request->currency,
            'payment_date' => $request->payment_date,
            'note'         => $request->note,
        ]);

        return redirect()->back()->with('success', 'پارەی قەرز بە سەرکەوتوویی وەرگیرا');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'currency'     => 'required|in:USD,IQD',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        $payment = CustomerPayment::findOrFail($id);
        $payment->update([
            'amount'       => $request->amount,
            'currency'     => $request->currency,
            'payment_date' => $request->payment_date,
            'note'         => $request->note,
        ]);

        return redirect()->back()->with('success', 'وەرگرتنەوەی قەرز دەستکاری کرا');
    }

    public function destroyPayment($id)
    {
        CustomerPayment::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'پارەدانەوەکە سڕایەوە');
    }

    public function statement($id)
    {
        $customer = Customer::with([
            'sales.details.product',
            'sales.details.unit',
            'sales.user',
            'payments.user',
            'returns.details.product',
            'returns.details.unit',
            'returns.user',
        ])->findOrFail($id);

        $ledger = collect();

        // ١. وەسڵەکانی فرۆشتن
        foreach ($customer->sales as $sale) {
            $ledger->push([
                'type'        => 'invoice',
                'currency'    => Customer::normalizeCurrency($sale->currency),
                'date'        => $sale->created_at->format('Y-m-d H:i'),
                'raw_date'    => $sale->created_at,
                'reference'   => $sale->invoice_no,
                'description' => 'وەسڵی فرۆشتن (' . ($sale->payment_type === 'debt' ? 'قەرز' : 'نەقد') . ')',
                'debit'       => (float) $sale->total_amount,
                'credit'      => (float) $sale->paid_amount,
                'details'     => $sale->details,
                'user_name'   => $sale->user->name ?? 'سیستەم',
            ]);
        }

        // ٢. وەرگرتنەوەی قەرزەکان
        foreach ($customer->payments as $pay) {
            $rawDate = $pay->payment_date ? Carbon::parse($pay->payment_date) : $pay->created_at;
            $ledger->push([
                'type'        => 'payment',
                'currency'    => Customer::normalizeCurrency($pay->currency),
                'date'        => $rawDate->format('Y-m-d'),
                'raw_date'    => $rawDate,
                'reference'   => 'PAY-' . str_pad($pay->id, 5, '0', STR_PAD_LEFT),
                'description' => 'وەرگرتنەوەی قەرز' . ($pay->note ? " ({$pay->note})" : ''),
                'debit'       => 0,
                'credit'      => (float) $pay->amount,
                'details'     => null,
                'user_name'   => $pay->user->name ?? 'سیستەم',
            ]);
        }

        // ٣. گەڕانەوەی کاڵا
        foreach ($customer->returns as $return) {
            $rawDate = $return->created_at ?? now();
            $creditAmount = ($return->refund_type === 'deduct_debt') ? (float) $return->total_amount : 0;

            $ledger->push([
                'type'        => 'return',
                'currency'    => Customer::normalizeCurrency($return->currency),
                'date'        => $rawDate->format('Y-m-d H:i'),
                'raw_date'    => $rawDate,
                'reference'   => $return->return_no,
                'description' => 'گەڕانەوەی کاڵا (' . ($return->refund_type === 'deduct_debt' ? 'داشکاندن لە قەرز' : 'دانەوە بە نەقد') . ')',
                'debit'       => 0,
                'credit'      => $creditAmount,
                'details'     => $return->details,
                'user_name'   => $return->user->name ?? 'سیستەم',
            ]);
        }

        // ڕیزبەندی لە کۆنەوە بۆ نوێ
        $ledger = $ledger->sortBy(fn($row) => Carbon::parse($row['raw_date'])->timestamp)->values();

        // ڕەسیدی ماوە بە جیا بۆ هەر دراوێک
        $running = ['USD' => 0.0, 'IQD' => 0.0];
        $ledger = $ledger->map(function ($row) use (&$running) {
            $c = $row['currency'];
            $running[$c] += ($row['debit'] - $row['credit']);
            $row['balance'] = $running[$c];
            return $row;
        });

        $summary = $customer->currencySummary();

        return view('customers.statement', compact('customer', 'ledger', 'summary'));
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'کڕیار سڕایەوە');
    }
}