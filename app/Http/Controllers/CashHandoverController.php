<?php

namespace App\Http\Controllers;

use App\Models\CashHandover;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashHandoverController extends Controller
{
    // تۆمارکردنی تەسلیماتی پارەکە
    public function store(Request $request)
    {
        $request->validate([
            'mandub_id' => 'required|exists:users,id',
            'amount'    => 'required|numeric|min:1',
            'note'      => 'nullable|string|max:255',
        ]);

        $receiptNo = 'REC-' . strtoupper(substr(uniqid(), -8));

        CashHandover::create([
            'receipt_no'    => $receiptNo,
            'mandub_id'     => $request->mandub_id,
            'received_by'   => auth()->id(), // ئەدمینی وەرگر
            'amount' => round($request->amount),
            'note'          => $request->note,
            'handover_date' => $request->filled('handover_date') ? Carbon::parse($request->handover_date) : now(),
        ]);

        return redirect()->back()->with('success', "پارەکە بە سەرکەوتوویی وەرگیرا. ژمارەی پسوولە: {$receiptNo}");
    }

    // لاپەڕەی چاپی پسوولەی تەسلیمات
    public function printReceipt($id)
    {
        $handover = CashHandover::with(['mandub', 'receiver'])->findOrFail($id);
        return view('mandub.print_handover', compact('handover'));
    }
}