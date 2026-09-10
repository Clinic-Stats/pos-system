<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\SaleReturn;
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
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        CustomerPayment::create([
            'customer_id'  => $id,
            'user_id'      => auth()->id(),
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date,
            'note'         => $request->note,
        ]);

        return redirect()->back()->with('success', 'پارەی قەرز بە سەرکەوتوویی وەرگیرا');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        $payment = CustomerPayment::findOrFail($id);
        $payment->update([
            'amount'       => $request->amount,
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
            'payments',
            'returns.details.product',
            'returns.details.unit'
        ])->findOrFail($id);

        $ledger = collect();

        // ١. زیادکردنی وەسڵەکانی فرۆشتن
        foreach ($customer->sales as $sale) {
            $ledger->push([
                'type'        => 'invoice',
                'date'        => $sale->created_at->format('Y-m-d H:i'),
                'raw_date'    => $sale->created_at,
                'reference'   => $sale->invoice_no,
                'description' => 'وەسڵی فرۆشتن (' . ($sale->payment_type === 'debt' ? 'قەرز' : 'نەقد') . ')',
                'debit'       => (float) $sale->total_amount,
                'credit'      => (float) $sale->paid_amount,
                'details'     => $sale->details,
            ]);
        }

        // ٢. زیادکردنی وەرگرتنەوەی قەرزەکان
        foreach ($customer->payments as $pay) {
            $rawDate = $pay->payment_date ? Carbon::parse($pay->payment_date) : $pay->created_at;
            $ledger->push([
                'type'        => 'payment',
                'date'        => $rawDate->format('Y-m-d'),
                'raw_date'    => $rawDate,
                'reference'   => 'PAY-' . str_pad($pay->id, 5, '0', STR_PAD_LEFT),
                'description' => 'وەرگرتنەوەی قەرز' . ($pay->note ? " ({$pay->note})" : ''),
                'debit'       => 0,
                'credit'      => (float) $pay->amount,
                'details'     => null,
            ]);
        }

        // ٣. زیادکردنی وەسڵەکانی گەڕانەوەی فرۆشتن (Sale Returns)
        if ($customer->relationLoaded('returns') || method_exists($customer, 'returns')) {
            foreach ($customer->returns as $return) {
                $rawDate = $return->created_at ?? now();
                $creditAmount = ($return->refund_type === 'deduct_debt') ? (float) $return->total_amount : 0;

                $ledger->push([
                    'type'        => 'return',
                    'date'        => $rawDate->format('Y-m-d H:i'),
                    'raw_date'    => $rawDate,
                    'reference'   => $return->return_no,
                    'description' => 'گەڕانەوەی کاڵا (' . ($return->refund_type === 'deduct_debt' ? 'داشکاندن لە قەرز' : 'دانەوە بە نەقد') . ')',
                    'debit'       => 0,
                    'credit'      => $creditAmount,
                    'details'     => $return->details,
                ]);
            }
        }

        // ڕیزبەندی بەپێی بەروار لە کۆنەوە بۆ نوێ (Oldest to Newest)
        $ledger = $ledger->sortBy(function ($row) {
            return Carbon::parse($row['raw_date'])->timestamp;
        })->values();

        // هەژمارکردنی باڵانسی ماوە هەنگاو بە هەنگاو
        $balance = 0;
        $ledger = $ledger->map(function ($row) use (&$balance) {
            $balance += ($row['debit'] - $row['credit']);
            $row['balance'] = $balance;
            return $row;
        });

        $totalPurchases = (float) $customer->sales->sum('total_amount');
        
        // کۆی پارەی دراو لەگەڵ بڕی کاڵا گەڕاوەکان کە لە قەرز داشکێنراون
        $totalReturnsDeducted = (float) $customer->returns->where('refund_type', 'deduct_debt')->sum('total_amount');
        $totalPaid = (float) $customer->sales->sum('paid_amount') + (float) $customer->payments->sum('amount') + $totalReturnsDeducted;
        $remainingDebt = $totalPurchases - $totalPaid;

        return view('customers.statement', compact('customer', 'ledger', 'totalPurchases', 'totalPaid', 'remainingDebt'));
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'کڕیار سڕایەوە');
    }
}