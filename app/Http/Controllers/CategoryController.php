<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'تکایە ناوی کاتیگۆری بنووسە.',
            'name.unique' => 'ئەم کاتیگۆرییە پێشتر تۆمار کراوە.',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'کاتیگۆری بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ], [
            'name.required' => 'تکایە ناوی کاتیگۆری بنووسە.',
            'name.unique' => 'ئەم کاتیگۆرییە پێشتر تۆمار کراوە.',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'کاتیگۆری بە سەرکەوتوویی نوێکرایەوە');
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // پشکنین: ئەگەر کاڵا لەم کاتیگۆرییەدا هەبێت، ناتوانرێت بسڕدرێتەوە
            $productCount = Product::where('category_id', $id)->count();
            if ($productCount > 0) {
                return redirect()->back()->with('error', "نەتوانرا کاتیگۆرییەکە بسڕدرێتەوە! ئەم کاتیگۆرییە {$productCount} کاڵای تێدایە.");
            }

            $category->delete();

            return redirect()->route('categories.index')->with('success', 'کاتیگۆری بە سەرکەوتوویی سڕایەوە');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'کێشەیەک ڕوویدا: ' . $e->getMessage());
        }
    }
}