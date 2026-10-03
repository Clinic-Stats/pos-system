<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private function cur($c): string
    {
        return strtoupper($c ?? 'IQD') === 'USD' ? 'USD' : 'IQD';
    }

    /**
     * هەموو جووڵەکانی حیسابی دابینکەر بە جیا بۆ هەر دراوێک.
     * کڕین = debit (ئێمە قەرزارین)، پارەی دراو = credit
     */
    private function events(int $supplierId): array
    {
        $events = ['USD' => [], 'IQD' => []];

        foreach (Purchase::where('supplier_id', $supplierId)->get() as $p) {
            $total = (float) $p->total_amount;
            // کڕینی نەقد = تەواو دراوە (بۆ وەسڵە کۆنەکانیش)
            $credit = $p->payment_type === 'debt' ? min((float) $p->paid_amount, $total) : $total;
            $events[$this->cur($p->currency)][] = [
                'date' => $p->created_at ?? Carbon::parse($p->purchase_date),
                'order' => 0, 'type' => 'purchase',
                'ref' => $p->purchase_no ?? $p->invoice_no ?? '-',
                'note' => $p->payment_type === 'debt' ? 'کڕینی قەرز' : 'کڕینی نەقد',
                'debit' => $total, 'credit' => $credit,
                'purchase_id' => $p->id, 'payment_id' => null,
            ];
        }

        foreach (SupplierPayment::where('supplier_id', $supplierId)->get() as $pay) {
            $events[$this->cur($pay->currency)][] = [
                'date' => Carbon::parse($pay->payment_date)->setTime(12, 0),
                'order' => 1, 'type' => 'payment',
                'ref' => 'پارەدان', 'note' => $pay->note ?: '-',
                'debit' => 0.0, 'credit' => (float) $pay->amount,
                'purchase_id' => null, 'payment_id' => $pay->id,
            ];
        }

        foreach ($events as $c => $list) {
            usort($list, fn($a, $b) => [$a['date']->timestamp, $a['order']] <=> [$b['date']->timestamp, $b['order']]);
            $events[$c] = $list;
        }
        return $events;
    }

    /** قەرزی ماوە (ئەوەی ئێمە قەرزارین بە دابینکەر) بە هەر دراوێک */
    private function balances(int $supplierId): array
    {
        $out = ['USD' => 0.0, 'IQD' => 0.0];
        foreach ($this->events($supplierId) as $c => $list) {
            foreach ($list as $e) { $out[$c] += $e['debit'] - $e['credit']; }
        }
        return $out;
    }

    public function index()
    {
        $suppliers = Supplier::with('purchases')->latest()->get();
        foreach ($suppliers as $s) {
            $s->balances = $this->balances($s->id);
        }
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'note'    => 'nullable|string|max:1000',
        ]);

        Supplier::create($request->only('name', 'phone', 'address', 'note'));

        return redirect()->back()->with('success', 'شوێنی کڕین بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'note'    => 'nullable|string|max:1000',
        ]);

        Supplier::findOrFail($id)->update($request->only('name', 'phone', 'address', 'note'));

        return redirect()->back()->with('success', 'زانیارییەکانی دابینکەر نوێکرایەوە');
    }

    public function destroy($id)
    {
        $supplier = Supplier::withCount('purchases')->findOrFail($id);

        if ($supplier->purchases_count > 0) {
            return redirect()->back()->with('error', 'ئەم دابینکەرە ' . $supplier->purchases_count . ' وەسڵی کڕینی هەیە و ناسڕدرێتەوە.');
        }

        $supplier->delete();
        return redirect()->back()->with('success', 'شوێنی کڕین سڕایەوە');
    }

    /** کەشفی حسابی دابینکەر */
    public function statement(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $to   = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        $ledgers = [];
        foreach ($this->events($supplier->id) as $c => $list) {
            $opening = 0.0; $rows = []; $debit = 0.0; $credit = 0.0;

            foreach ($list as $e) {
                if ($from && $e['date']->lt($from)) { $opening += $e['debit'] - $e['credit']; continue; }
                if ($to && $e['date']->gt($to)) { continue; }
                $rows[] = $e;
                $debit += $e['debit']; $credit += $e['credit'];
            }

            $running = $opening;
            foreach ($rows as &$r) { $running += $r['debit'] - $r['credit']; $r['balance'] = $running; }
            unset($r);

            $ledgers[$c] = ['opening' => $opening, 'rows' => $rows, 'debit' => $debit, 'credit' => $credit, 'closing' => $running];
        }

        $balances = $this->balances($supplier->id);
        $setting = Setting::first();

        return view('suppliers.statement', compact('supplier', 'ledgers', 'balances', 'setting', 'from', 'to'));
    }

    /** پارەدان بە دابینکەر (کەمکردنەوەی قەرز) */
    public function storePayment(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $data = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'currency'     => 'required|in:USD,IQD',
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:255',
        ]);

        $balance = $this->balances($supplier->id)[$data['currency']];
        if ((float) $data['amount'] > $balance + 0.005) {
            return redirect()->back()->withInput()->withErrors(['amount' => 'بڕەکە لە قەرزی ماوە زیاترە (ماوە: ' . number_format(max(0, $balance), 2) . ' ' . $data['currency'] . ')']);
        }

        SupplierPayment::create([
            'supplier_id'  => $supplier->id,
            'amount'       => $data['currency'] === 'USD' ? round($data['amount'], 2) : round($data['amount']),
            'currency'     => $data['currency'],
            'payment_date' => $data['payment_date'],
            'note'         => $data['note'] ?? null,
            'user_id'      => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'پارەدانەکە تۆمارکرا');
    }

    public function destroyPayment($id)
    {
        SupplierPayment::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'پارەدانەکە سڕایەوە');
    }
}