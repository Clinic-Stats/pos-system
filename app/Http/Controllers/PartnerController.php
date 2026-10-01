<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::with(['transactions' => function ($q) {
            $q->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        }])->latest()->get();

        $setting = Setting::first();
        $exchangeRate = $setting->exchange_rate ?? 1500;

        return view('partners.index', compact('partners', 'setting', 'exchangeRate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'share_percent' => 'nullable|numeric|min:0|max:100',
            'capital'       => 'nullable|numeric|min:0',
            'phone'         => 'nullable|string|max:50',
            'note'          => 'nullable|string|max:255',
            'currency'      => 'nullable|in:USD,IQD',
            'exchange_rate' => 'nullable|numeric|min:1',
        ]);

        $shareVal = (float) ($request->share_percent ?? 0);
        $capitalVal = (float) ($request->capital ?? 0);
        $currency = $request->input('currency', 'USD');
        $exchangeRate = (float) $request->input('exchange_rate', 1500);

        // گۆڕینی سەرمایە بۆ دۆلار ئەگەر بە دینار بوو
        if ($currency === 'IQD' && $exchangeRate > 0) {
            $capitalVal = $capitalVal / $exchangeRate;
        }

        $partnerData = [
            'name'  => $request->name,
            'phone' => $request->phone,
        ];

        if (Schema::hasColumn('partners', 'note')) {
            $partnerData['note'] = $request->note;
        }

        if (Schema::hasColumn('partners', 'share_percentage')) {
            $partnerData['share_percentage'] = $shareVal;
        } elseif (Schema::hasColumn('partners', 'share_percent')) {
            $partnerData['share_percent'] = $shareVal;
        }

        if (Schema::hasColumn('partners', 'capital_amount')) {
            $partnerData['capital_amount'] = round($capitalVal, 2);
        } elseif (Schema::hasColumn('partners', 'capital')) {
            $partnerData['capital'] = round($capitalVal, 2);
        }

        if (Schema::hasColumn('partners', 'currency')) {
            $partnerData['currency'] = $currency;
        }

        Partner::create($partnerData);

        return redirect()->back()->with('success', 'هاوبەش بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'share_percent' => 'nullable|numeric|min:0|max:100',
            'capital'       => 'nullable|numeric|min:0',
            'phone'         => 'nullable|string|max:50',
            'note'          => 'nullable|string|max:255',
            'currency'      => 'nullable|in:USD,IQD',
            'exchange_rate' => 'nullable|numeric|min:1',
        ]);

        $shareVal = (float) ($request->share_percent ?? 0);
        $capitalVal = (float) ($request->capital ?? 0);
        $currency = $request->input('currency', 'USD');
        $exchangeRate = (float) $request->input('exchange_rate', 1500);

        if ($currency === 'IQD' && $exchangeRate > 0) {
            $capitalVal = $capitalVal / $exchangeRate;
        }

        $updateData = [
            'name'  => $request->name,
            'phone' => $request->phone,
        ];

        if (Schema::hasColumn('partners', 'note')) {
            $updateData['note'] = $request->note;
        }

        if (Schema::hasColumn('partners', 'share_percentage')) {
            $updateData['share_percentage'] = $shareVal;
        } elseif (Schema::hasColumn('partners', 'share_percent')) {
            $updateData['share_percent'] = $shareVal;
        }

        if (Schema::hasColumn('partners', 'capital_amount')) {
            $updateData['capital_amount'] = round($capitalVal, 2);
        } elseif (Schema::hasColumn('partners', 'capital')) {
            $updateData['capital'] = round($capitalVal, 2);
        }

        if (Schema::hasColumn('partners', 'currency')) {
            $updateData['currency'] = $currency;
        }

        $partner->update($updateData);

        return redirect()->back()->with('success', 'زانیارییەکانی هاوبەش نوێکرانەوە');
    }

    public function addTransaction(Request $request, $id)
    {
        $request->validate([
            'type'          => 'required|in:deposit,withdraw',
            'amount'        => 'required|numeric|min:1',
            'date'          => 'nullable|date',
            'note'          => 'nullable|string|max:255',
            'currency'      => 'nullable|in:USD,IQD',
            'exchange_rate' => 'nullable|numeric|min:1',
        ]);

        $amountVal = (float) $request->amount;
        $currency = $request->input('currency', 'USD');
        $exchangeRate = (float) $request->input('exchange_rate', 1500);

        // گۆڕینی بڕ بۆ دۆلار ئەگەر بە دینار بوو
        if ($currency === 'IQD' && $exchangeRate > 0) {
            $amountVal = $amountVal / $exchangeRate;
        }

        $trxData = [
            'partner_id' => $id,
            'type'       => $request->type,
            'amount'     => round($amountVal, 2),
            'note'       => $request->note,
            'created_at' => $request->filled('date') ? Carbon::parse($request->date) : now(),
        ];

        if (Schema::hasColumn('partner_transactions', 'date')) {
            $trxData['date'] = $request->date ?? date('Y-m-d');
        }

        if (Schema::hasColumn('partner_transactions', 'currency')) {
            $trxData['currency'] = $currency;
        }

        if (Schema::hasColumn('partner_transactions', 'exchange_rate')) {
            $trxData['exchange_rate'] = $exchangeRate;
        }

        PartnerTransaction::create($trxData);

        return redirect()->back()->with('success', 'مامەڵەکە بە سەرکەوتوویی تۆمارکرا');
    }

    public function destroyTransaction($id)
    {
        PartnerTransaction::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'مامەڵەکە سڕایەوە');
    }

    public function show($id)
    {
        $partner = Partner::with(['transactions' => function ($q) {
            $q->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($id);

        $setting = Setting::first();
        $exchangeRate = $setting->exchange_rate ?? 1500;

        return view('partners.show', compact('partner', 'setting', 'exchangeRate'));
    }

    public function allReport()
    {
        $partners = Partner::with(['transactions'])->get();
        $setting = Setting::first();
        $exchangeRate = $setting->exchange_rate ?? 1500;

        return view('partners.all_report', compact('partners', 'setting', 'exchangeRate'));
    }

    public function destroy($id)
    {
        Partner::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'هاوبەشەکە سڕایەوە');
    }
}