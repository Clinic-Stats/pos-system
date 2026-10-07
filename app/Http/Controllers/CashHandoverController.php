<?php

namespace App\Http\Controllers;

use App\Models\CashHandover;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashHandoverController extends Controller
{
    public function store(Request $request)
    {
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

    /**
     * دەستکاریکردنی وەسڵی تەسلیمات (بڕ، دراو، کەسی تەسلیمکار، بەروار، تێبینی).
     * سڕینەوە بوونی نییە: ئەگەر بڕەکە بکرێت بە سفر، وەسڵەکە هەر دەمێنێتەوە.
     * هەر دەستکارییەک لە «تۆماری چالاکییەکان» دەنووسرێت (پێش و دوای).
     */
    public function update(Request $request, $id)
    {
        abort_unless(auth()->user() && auth()->user()->canReceiveCash(), 403, 'ئەم کارە بۆ تۆ ڕێگەپێدراو نییە');

        $handover = CashHandover::findOrFail($id);

        $data = $request->validate([
            'mandub_id'     => 'required|exists:users,id',
            'amount'        => 'required|numeric|min:0',
            'currency'      => 'required|in:USD,IQD',
            'note'          => 'nullable|string|max:255',
            'handover_date' => 'nullable|date',
        ]);

        $handover->update([
            'mandub_id'     => $data['mandub_id'],
            'amount'        => $data['currency'] === 'USD' ? round($data['amount'], 2) : round($data['amount']),
            'currency'      => $data['currency'],
            'note'          => $data['note'] ?? null,
            'handover_date' => !empty($data['handover_date']) ? Carbon::parse($data['handover_date']) : $handover->handover_date,
        ]);

        return redirect()->back()->with('success', 'وەسڵی تەسلیمات ' . $handover->receipt_no . ' نوێکرایەوە');
    }

    public function printReceipt($id)
    {
        $handover = CashHandover::with(['mandub', 'receiver'])->findOrFail($id);
        return view('mandub.print_handover', compact('handover'));
    }
}