<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->latest()->get();
        $setting = Setting::first();
        return view('products.index', compact('products', 'categories', 'setting'));
    }

    /** نرخەکان دەگۆڕێت بۆ دۆلار ئەگەر بە دینار نووسرابوون */
    private function prices(Request $request): array
    {
        $rate = (float) $request->input('exchange_rate', 1500);
        $buy  = (float) $request->base_buy_price;
        $sale = (float) $request->base_sale_price;

        if ($request->input('currency', 'USD') === 'IQD' && $rate > 0) {
            $buy /= $rate;
            $sale /= $rate;
        }
        return [round($buy, 4), round($sale, 4)];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|unique:products,code',
            'category_id'     => 'required|exists:categories,id',
            'base_buy_price'  => 'required|numeric|min:0',
            'base_sale_price' => 'required|numeric|min:0',
            'kg_per_carton'   => 'required|numeric|min:0.01',
            'stock_kg'        => 'nullable|numeric|min:0',
        ]);

        [$buy, $sale] = $this->prices($request);

        Product::create([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'base_buy_price'  => $buy,
            'base_sale_price' => $sale,
            'kg_per_carton'   => $request->kg_per_carton,
            'stock_kg'        => $request->stock_kg ?? 0,
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('products.index')->with('success', 'کاڵا بە سەرکەوتوویی زیادکرا');
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
            'kg_per_carton'   => 'required|numeric|min:0.01',
        ]);

        [$buy, $sale] = $this->prices($request);

        $product->update([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'base_buy_price'  => $buy,
            'base_sale_price' => $sale,
            'kg_per_carton'   => $request->kg_per_carton,
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'زانیارییەکانی کاڵا بە سەرکەوتوویی نوێکرانەوە');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        $status = $product->is_active ? 'چالاک کرا' : 'ناچالاک کرا';
        return redirect()->back()->with('success', "دۆخی کاڵاکە گۆڕدرا بۆ: {$status}");
    }

    public function addStock(Request $request, $id)
    {
        $request->validate(['added_stock' => 'required|numeric|min:0.1']);

        Product::findOrFail($id)->increment('stock_kg', $request->added_stock);

        return redirect()->back()->with('success', "بڕی {$request->added_stock} کیلۆ بۆ مەخزەن زیادکرا");
    }

    public function destroy($id)
    {
        try {
            Product::findOrFail($id)->delete();
            return redirect()->route('products.index')->with('success', 'کاڵاکە بە سەرکەوتوویی سڕایەوە.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->back()->with('error', 'نەتوانرا کاڵاکە بسڕدرێتەوە! ئەم کاڵایە پێشتر لە پسوولەی فرۆشتن یان کڕیندا بەکارهاتووە.');
            }
            return redirect()->back()->with('error', 'هەڵەیەک ڕوویدا لە کاتی سڕینەوەی کاڵاکەدا.');
        }
    }

    /**
     * CSV: code, name, base_buy_price, base_sale_price, stock_kg, kg_per_carton
     * ستوونی ٥ و ٦ ئارەزوومەندانەن. نرخەکان دەبێت بە دۆلار بن.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file'    => 'required|mimes:csv,txt',
            'category_id' => 'required|exists:categories,id',
        ]);

        $file = fopen($request->file('csv_file'), 'r');
        fgetcsv($file); // دێڕی ناونیشان

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 4) {
                $data = [
                    'name'            => $row[1],
                    'base_buy_price'  => (float) $row[2],
                    'base_sale_price' => (float) $row[3],
                    'stock_kg'        => isset($row[4]) ? (float) $row[4] : 0,
                    'category_id'     => $request->category_id,
                ];
                if (isset($row[5]) && (float) $row[5] > 0) {
                    $data['kg_per_carton'] = (float) $row[5];
                }
                Product::updateOrCreate(['code' => $row[0]], $data);
            }
        }
        fclose($file);

        return back()->with('success', 'هەموو کاڵاکان بە سەرکەوتوویی هاوردە کران!');
    }
}