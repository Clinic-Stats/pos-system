<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|unique:products,code',
            'category_id'     => 'required|exists:categories,id',
            'base_buy_price'  => 'required|numeric|min:0',
            'base_sale_price' => 'required|numeric|min:0',
            'stock_kg'        => 'nullable|numeric|min:0',
        ]);

        Product::create([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'base_buy_price'  => $request->base_buy_price,
            'base_sale_price' => $request->base_sale_price,
            'stock_kg'        => $request->stock_kg ?? 0,
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->to(secure_url('/products'))->with('success', 'کاڵا بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|unique:products,code,' . $product->id,
            'category_id'     => 'required|exists:categories,id',
            'base_buy_price'  => 'required|numeric|min:0',
            'base_sale_price' => 'required|numeric|min:0',
        ]);

        $product->update([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'base_buy_price'  => $request->base_buy_price,
            'base_sale_price' => $request->base_sale_price,
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->to(secure_url('/products'))->with('success', 'زانیارییەکانی کاڵا بە سەرکەوتوویی نوێکرانەوە');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        $status = $product->is_active ? 'چالاک کرا' : 'ناچالاک کرا';
        return redirect()->to(secure_url('/products'))->with('success', "دۆخی کاڵاکە گۆڕدرا بۆ: {$status}");
    }

    public function addStock(Request $request, $id)
    {
        $request->validate([
            'added_stock' => 'required|numeric|min:0.1',
        ]);

        $product = Product::findOrFail($id);
        $product->increment('stock_kg', $request->added_stock);

        return redirect()->to(secure_url('/products'))->with('success', "بڕی {$request->added_stock} کیلۆ بۆ مەخزەن زیادکرا");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->stock_kg > 0) {
            return redirect()->to(secure_url('/products'))->with('error', 'ناتوانیت ئەم کاڵایە بسڕیتەوە چونکە بڕی مەخزەنەکەی لە سفر زیاترە (' . $product->stock_kg . ' کگ ماوە). پێویستە سفر بێت یان ناچالاکی بکەیت.');
        }

        $product->delete();
        return redirect()->to(secure_url('/products'))->with('success', 'کاڵاکە بە سەرکەوتوویی سڕایەوە');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file'), 'r');
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 4) {
                \App\Models\Product::updateOrCreate(
                    ['code' => $row[0]],
                    [
                        'name'            => $row[1],
                        'base_buy_price'  => (float) $row[2],
                        'base_sale_price' => (float) $row[3],
                        'stock_kg'        => isset($row[4]) ? (float) $row[4] : 0,
                        'category_id'     => 1,
                    ]
                );
            }
        }
        fclose($file);

        return redirect()->to(secure_url('/products'))->with('success', 'هەموو کاڵاکان بە سەرکەوتوویی هاوردە کران!');
    }
}