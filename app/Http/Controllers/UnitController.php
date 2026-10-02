<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /** جۆری یەکە: carton (کێشی هەر کاڵایەک) / ton (١٠٠٠ کگ) / normal */
    private function kind(string $name): string
    {
        $n = mb_strtolower(trim($name));
        if (str_contains($n, 'کارتۆن') || str_contains($n, 'carton')) return 'carton';
        if (str_contains($n, 'تەن') || str_contains($n, 'ton')) return 'ton';
        return 'normal';
    }

    /** کارتۆن و تەن کێشەکەیان خۆکارە، بۆیە ژمارەکە ڕێک دەکرێت */
    private function factorFor(Request $request): float
    {
        return match ($this->kind($request->name)) {
            'carton' => 1,
            'ton'    => 1000,
            default  => (float) $request->factor_to_base,
        };
    }

    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $kind = $this->kind((string) $request->name);

        $request->validate([
            'name'           => 'required|string|max:255|unique:units,name',
            'factor_to_base' => ($kind === 'normal' ? 'required' : 'nullable') . '|numeric|min:0.0001',
        ]);

        Unit::create(['name' => $request->name, 'factor_to_base' => $this->factorFor($request)]);
        return redirect()->back()->with('success', 'یەکە بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $kind = $this->kind((string) $request->name);

        $request->validate([
            'name'           => 'required|string|max:255|unique:units,name,' . $id,
            'factor_to_base' => ($kind === 'normal' ? 'required' : 'nullable') . '|numeric|min:0.0001',
        ]);

        Unit::findOrFail($id)->update(['name' => $request->name, 'factor_to_base' => $this->factorFor($request)]);
        return redirect()->route('units.index')->with('success', 'یەکەکە بە سەرکەوتوویی نوێکرایەوە');
    }

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'یەکە سڕایەوە');
    }
}