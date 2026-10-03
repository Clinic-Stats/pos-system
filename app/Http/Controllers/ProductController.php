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

    private function rules(?int $id = null): array
    {
        return [
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|unique:products,code' . ($id ? ',' . $id : ''),
            'category_id'     => 'required|exists:categories,id',
            'sell_type'       => 'required|in:weight,carton',
            'kg_per_carton'   => 'required_if:sell_type,carton|nullable|numeric|min:0.01',
            'base_buy_price'  => 'required|numeric|min:0',
            'base_sale_price' => 'required|numeric|min:0',
            'stock_kg'        => 'nullable|numeric|min:0',
        ];
    }

    /** نرخەکان بۆ دۆلار دەگۆڕدرێن ئەگەر بە دینار نووسرابن */
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
        $request->validate($this->rules());
        [$buy, $sale] = $this->prices($request);
        $isCarton = $request->sell_type === 'carton';

        Product::create([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'sell_type'       => $request->sell_type,
            'kg_per_carton'   => $isCarton ? $request->kg_per_carton : 1,
            'base_buy_price'  => $buy,
            'base_sale_price' => $sale,
            'stock_kg'        => $request->stock_kg ?? 0,   // بۆ کاڵای کارتۆنی = ژمارەی کارتۆن
            'is_active'       => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('products.index')->with('success', 'کاڵا بە سەرکەوتوویی زیادکرا');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate($this->rules($product->id));
        [$buy, $sale] = $this->prices($request);

        // جۆری فرۆشتن تەنها کاتێک دەگۆڕدرێت کە کۆگا سفر بێت، بۆ ئەوەی ژمارەی کۆگا تێک نەچێت
        $type = ((float) $product->stock_kg > 0) ? ($product->sell_type ?? 'weight') : $request->sell_type;

        $product->update([
            'name'            => $request->name,
            'code'            => $request->code,
            'category_id'     => $request->category_id,
            'sell_type'       => $type,
            'kg_per_carton'   => $type === 'carton' ? $request->kg_per_carton : 1,
            'base_buy_price'  => $buy,
            'base_sale_price' => $sale,
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
        $product = Product::findOrFail($id);
        $product->increment('stock_kg', $request->added_stock);
        $unit = ($product->sell_type ?? 'weight') === 'carton' ? 'کارتۆن' : 'کیلۆ';

        return redirect()->back()->with('success', "بڕی {$request->added_stock} {$unit} بۆ مەخزەن زیادکرا");
    }

    public function destroy($id)
    {
        try {
            Product::findOrFail($id)->delete();
            return redirect()->route('products.index')->with('success', 'کاڵاکە بە سەرکەوتوویی سڕایەوە.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'نەتوانرا کاڵاکە بسڕدرێتەوە! ئەم کاڵایە پێشتر لە پسوولەی فرۆشتن یان کڕیندا بەکارهاتووە و پاراستنی بۆ کراوە.');
            }
            return redirect()->back()->with('error', 'هەڵەیەک ڕوویدا لە کاتی سڕینەوەی کاڵاکەدا.');
        }
    }

    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|mimes:csv,txt']);

        $file = fopen($request->file('csv_file'), 'r');
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 4) {
                Product::updateOrCreate(
                    ['code' => $row[0]],
                    [
                        'name'            => $row[1],
                        'base_buy_price'  => (float) $row[2],
                        'base_sale_price' => (float) $row[3],
                        'stock_kg'        => isset($row[4]) ? (float) $row[4] : 0,
                        'category_id'     => $request->category_id ?? 1,
                    ]
                );
            }
        }
        fclose($file);

        return back()->with('success', 'هەموو کاڵاکان بە سەرکەوتوویی هاوردە کران!');
    }
}