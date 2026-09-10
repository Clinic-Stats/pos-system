<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use Illuminate\Http\Request;

class RepresentativeController extends Controller
{
    public function index()
    {
        $representatives = Representative::withCount('sales')
            ->withSum('sales', 'total_amount')
            ->latest()
            ->get();

        return view('representatives.index', compact('representatives'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'area'            => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        Representative::create([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'area'            => $request->area,
            'commission_rate' => $request->commission_rate ?? 0,
            'is_active'       => 1,
        ]);

        return redirect()->back()->with('success', 'مەندووب بە سەرکەوتوویی زیاد کرا');
    }

    public function update(Request $request, $id)
    {
        $rep = Representative::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:50',
            'area'            => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $rep->update([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'area'            => $request->area,
            'commission_rate' => $request->commission_rate ?? 0,
        ]);

        return redirect()->back()->with('success', 'زانیاریی مەندووب بە سەرکەوتوویی نوێکرایەوە');
    }

    public function destroy($id)
    {
        $rep = Representative::findOrFail($id);
        $rep->delete();

        return redirect()->back()->with('success', 'مەندووب سڕایەوە');
    }

    // پەڕەی ڕاپۆرت و وردەکاری فرۆشتنی مەندووب
    public function show($id)
    {
        $rep = Representative::with(['sales.customer'])->findOrFail($id);
        $totalSales = (float) $rep->sales->sum('total_amount');
        $commissionAmount = ($totalSales * (float) $rep->commission_rate) / 100;

        return view('representatives.show', compact('rep', 'totalSales', 'commissionAmount'));
    }
}