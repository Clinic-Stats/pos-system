<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'factor_to_base' => 'required|numeric|min:0.001',
        ]);

        Unit::create($request->only('name', 'factor_to_base'));
        return redirect()->back()->with('success', 'یەکە بە سەرکەوتوویی زیادکرا');
    }

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'factor_to_base' => 'required|numeric|min:0.0001',
    ]);

    $unit = \App\Models\Unit::findOrFail($id);
    
    $unit->update([
        'name' => $request->name,
        'factor_to_base' => $request->factor_to_base,
    ]);

    return redirect()->route('units.index')->with('success', 'یەکەکە بە سەرکەوتوویی نوێکرایەوە');
}

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'یەکە سڕایەوە');
    }
}