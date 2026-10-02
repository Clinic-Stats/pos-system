<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /** کۆی خەرجی بە جیا بۆ هەر دراوێک */
    private function totals($query): array
    {
        $rows = (clone $query)
            ->selectRaw("UPPER(COALESCE(currency,'IQD')) as cur, SUM(amount) as total")
            ->groupBy('cur')->pluck('total', 'cur');

        return ['USD' => (float) ($rows['USD'] ?? 0), 'IQD' => (float) ($rows['IQD'] ?? 0)];
    }

    private function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,IQD',
            'date'     => 'required|date',
            'category' => 'nullable|string|max:100',
            'user_id'  => 'nullable|exists:users,id',
            'note'     => 'nullable|string|max:255',
        ];
    }

    private function amountFor(Request $request): float
    {
        return $request->currency === 'USD' ? round($request->amount, 2) : round($request->amount);
    }

    public function index(Request $request)
    {
        $users = User::all();
        $query = Expense::with('user');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        } elseif ($request->filled('period')) {
            if ($request->period === 'today') {
                $query->whereDate('date', Carbon::today());
            } elseif ($request->period === 'yesterday') {
                $query->whereDate('date', Carbon::yesterday());
            } elseif ($request->period === 'month') {
                $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
            }
        }

        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('category')) $query->where('category', $request->category);

        $totals = $this->totals($query);
        $rate = (float) (Setting::first()->exchange_rate ?? 1500);
        $expenses = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        return view('expenses.index', compact('expenses', 'users', 'totals', 'rate'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        Expense::create([
            'title'    => $request->title,
            'amount'   => $this->amountFor($request),
            'currency' => $request->currency,
            'date'     => $request->date,
            'category' => $request->category ?? 'گشتی',
            'user_id'  => $request->user_id ?? auth()->id(),
            'note'     => $request->note,
        ]);

        return redirect()->back()->with('success', 'خەرجی بە سەرکەوتوویی تۆمارکرا');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $request->validate($this->rules());

        $expense->update([
            'title'    => $request->title,
            'amount'   => $this->amountFor($request),
            'currency' => $request->currency,
            'date'     => $request->date,
            'category' => $request->category ?? 'گشتی',
            'user_id'  => $request->user_id ?? $expense->user_id,
            'note'     => $request->note,
        ]);

        return redirect()->back()->with('success', 'زانیارییەکانی خەرجی بە سەرکەوتوویی نوێکرایەوە');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'خەرجی سڕایەوە');
    }

    public function report(Request $request)
    {
        $query = Expense::with('user');

        $fromDate = $request->filled('from_date') ? $request->from_date : null;
        $toDate   = $request->filled('to_date') ? $request->to_date : null;

        if ($fromDate && $toDate) $query->whereBetween('date', [$fromDate, $toDate]);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('category')) $query->where('category', $request->category);

        $expenses = $query->orderBy('date', 'asc')->get();
        $totals = $this->totals($query);
        $setting = Setting::first();

        return view('expenses.report', compact('expenses', 'totals', 'setting', 'fromDate', 'toDate'));
    }
}