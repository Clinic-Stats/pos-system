<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
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

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $totalExpenses = (clone $query)->sum('amount');
        $expenses = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        return view('expenses.index', compact('expenses', 'users', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:1',
            'date'     => 'required|date',
            'category' => 'nullable|string|max:100',
            'user_id'  => 'nullable|exists:users,id',
            'note'     => 'nullable|string|max:255',
        ]);

        Expense::create([
            'title'    => $request->title,
            'amount'   => $request->amount,
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

        $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:1',
            'date'     => 'required|date',
            'category' => 'nullable|string|max:100',
            'user_id'  => 'nullable|exists:users,id',
            'note'     => 'nullable|string|max:255',
        ]);

        $expense->update([
            'title'    => $request->title,
            'amount'   => $request->amount,
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

        if ($fromDate && $toDate) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('date', 'asc')->get();
        $totalExpenses = $expenses->sum('amount');
        $setting = Setting::first();

        return view('expenses.report', compact('expenses', 'totalExpenses', 'setting', 'fromDate', 'toDate'));
    }
}