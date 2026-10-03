<?php

namespace App\Http\Controllers;

use App\Models\CashHandover;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashHandoverController extends Controller
{
    public function store(Request $request)
    {
        // تەنها ئەدمین، کاشیر، یان کەسێکی خاوەن دەسەڵات دەتوانێت پارە وەربگرێت
        abort_unless(auth()->user() && auth()->user()->canReceiveCash(), 403, 'ئەم کارە بۆ تۆ ڕێگەپێدراو نییە');

        $request->validate([
            'mandub_id' => 'required|exists:users,id',
            'amount'    => 'required|numeric|min:0.01',
            'currency'  => 'required|in:USD,IQD',
            'note'      => 'nullable|string|max:255',
        ]);

        $receiptNo = 'REC-' . strtoupper(substr(uniqid(), -8));
        $isUsd = $request->currency === 'USD';

        CashHandover::create([
            'receipt_no'    => $receiptNo,
            'mandub_id'     => $request->mandub_id,
            'received_by'   => auth()->id(),
            'amount'        => $isUsd ? round($request->amount, 2) : round($request->amount),
            'currency'      => $request->currency,
            'note'          => $request->note,
            'handover_date' => $request->filled('handover_date') ? Carbon::parse($request->handover_date) : now(),
        ]);

        return redirect()->back()->with('success', "پارەکە بە سەرکەوتوویی وەرگیرا. ژمارەی پسوولە: {$receiptNo}");
    }

    public function printReceipt($id)
    {
        $handover = CashHandover::with(['mandub', 'receiver'])->findOrFail($id);
        return view('mandub.print_handover', compact('handover'));
    }
}