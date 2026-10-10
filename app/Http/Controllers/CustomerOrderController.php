<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerOrderController extends Controller
{
    // ═════════════════ بەشی گشتی (کڕیار، بێ لۆگین) ═════════════════

    public function create()
    {
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';

        // هەموو کاڵا چالاکەکان: ناو، نرخ، ماوەی کۆگا (وەک POS)
        $products = Product::where('is_active', 1)
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'code'          => $p->code,
                'category_id'   => $p->category_id,
                'sell_type'     => $p->sell_type ?? 'weight',
                'price'         => (float) $p->base_sale_price,
                'stock'         => (float) ($p->{$stockCol} ?? 0),
                'alert'         => (float) ($p->alert_quantity ?? 5),
                'kg_per_carton' => (float) ($p->kg_per_carton ?: 1),
            ])->values();

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $units      = Unit::all(['id', 'name', 'factor_to_base']);
        $setting    = Setting::first();

        return view('orders.public', compact('products', 'categories', 'units', 'setting'));
    }

    /** پشکنینی ژمارەی مۆبایل: ئەگەر کڕیاری تۆمارکراو بوو ناوەکەی دەگەڕێنێتەوە */
    public function lookup(Request $request)
    {
        $request->validate(['phone' => 'required|string|max:50']);

        $customer = $this->findCustomer($request->phone);

        return $customer
            ? response()->json(['found' => true, 'name' => $customer->name])
            : response()->json(['found' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mode'                => 'required|in:new,existing',
            'name'                => 'nullable|string|max:255',
            'phone'               => 'required|string|max:50',
            'address'             => 'nullable|string|max:255',
            'note'                => 'nullable|string|max:500',
            'website'             => 'nullable|string',                 // honeypot بۆ بۆتەکان
            'items'               => 'required|array|min:1|max:80',
            'items.*.product_id'  => 'required|integer|exists:products,id',
            'items.*.unit_id'     => 'nullable|integer|exists:units,id',
            'items.*.quantity'    => 'required|numeric|min:0.01|max:100000',
        ]);

        // بۆت: وەک سەرکەوتوو وەڵام دەدرێتەوە بەڵام هیچ تۆمار ناکرێت
        if (!empty($data['website'])) {
            return response()->json(['success' => true, 'order_no' => 'ORD-OK']);
        }

        $customer = $this->findCustomer($data['phone']);

        if ($data['mode'] === 'existing' && !$customer) {
            return response()->json(['success' => false, 'error' => 'ئەم ژمارەیە لە کڕیارانی هەمیشەیی تۆمار نەکراوە.'], 422);
        }

        if ($data['mode'] === 'new' && (blank($data['name'] ?? null) || blank($data['address'] ?? null))) {
            return response()->json(['success' => false, 'error' => 'تکایە ناو و ناونیشان بنووسە.'], 422);
        }

        // یەک کاڵا + یەک یەکە = یەک هێڵ
        $merged = [];
        foreach ($data['items'] as $it) {
            $key = $it['product_id'] . '-' . ($it['unit_id'] ?? 0);
            if (!isset($merged[$key])) {
                $merged[$key] = ['product_id' => $it['product_id'], 'unit_id' => $it['unit_id'] ?? null, 'quantity' => 0];
            }
            $merged[$key]['quantity'] += (float) $it['quantity'];
        }

        // پشکنینی کۆگا: بڕی داواکراو نابێت لە ماوەی کۆگا زیاتر بێت
        $stockCol = Schema::hasColumn('products', 'stock_kg') ? 'stock_kg' : 'stock';
        $used = [];
        foreach ($merged as $m) {
            $product = Product::find($m['product_id']);
            $unit    = $m['unit_id'] ? Unit::find($m['unit_id']) : null;
            $used[$product->id] = ($used[$product->id] ?? 0) + $m['quantity'] * $this->unitFactor($product, $unit);
        }
        foreach ($used as $pid => $need) {
            $product = Product::find($pid);
            if ((float) $product->{$stockCol} <= 0 || $need > (float) $product->{$stockCol} + 0.0001) {
                return response()->json(['success' => false, 'error' => "بڕی داواکراو بۆ ({$product->name}) لە کۆگا نییە، تکایە کەمی بکەرەوە."], 422);
            }
        }

        $order = DB::transaction(function () use ($data, $customer, $merged) {
            $order = CustomerOrder::create([
                'order_no'       => 'ORD-' . strtoupper(substr(uniqid(), -6)),
                'customer_id'    => $customer?->id,
                'claims_regular' => $data['mode'] === 'existing',
                'name'           => $customer ? $customer->name : trim($data['name']),
                'phone'          => trim($data['phone']),
                'address'        => $customer ? ($customer->address ?: ($data['address'] ?? null)) : trim($data['address']),
                'note'           => $data['note'] ?? null,
                'status'         => 'pending',
            ]);
            $order->items()->createMany(array_values($merged));
            return $order;
        });

        return response()->json(['success' => true, 'order_no' => $order->order_no]);
    }

    // ═════════════════ بەشی داشبۆرد (تەنها کارمەند) ═════════════════

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = CustomerOrder::with(['items.product', 'items.unit', 'customer.sales', 'customer.payments', 'customer.returns'])
            ->latest();

        if (in_array($status, ['pending', 'accepted', 'rejected'])) {
            $query->where('status', $status);
        }

        $orders = $query->limit(200)->get();
        $counts = CustomerOrder::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('orders.index', compact('orders', 'counts', 'status'));
    }

    public function pendingCount()
    {
        return response()->json(['count' => CustomerOrder::where('status', 'pending')->count()]);
    }

    public function reject($id)
    {
        $order = CustomerOrder::findOrFail($id);
        if ($order->status === 'pending') {
            $order->update(['status' => 'rejected', 'handled_by' => auth()->id(), 'handled_at' => now()]);
        }
        return redirect()->back()->with('success', 'داواکارییەکە ڕەتکرایەوە');
    }

    public function destroy($id)
    {
        CustomerOrder::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'داواکارییەکە سڕایەوە');
    }

    // ═════════════════ یاریدەدەر ═════════════════

    private function unitFactor($product, $unit): float
    {
        if (($product->sell_type ?? 'weight') === 'carton') return 1.0;
        $n = mb_strtolower(trim($unit->name ?? ''));
        if (str_contains($n, 'کارتۆن') || str_contains($n, 'carton')) return (float) ($product->kg_per_carton ?: 1);
        if (str_contains($n, 'تەن') || str_contains($n, 'ton')) return 1000.0;
        return (float) (($unit->factor_to_base ?? 1) ?: 1);
    }

    /** ژمارەی مۆبایل: ژمارەی کوردی/عەرەبی دەگۆڕێت و تەنها ١٠ ژمارەی کۆتایی بەراورد دەکات (٠٧٥٠.. = +٩٦٤٧٥٠..) */
    private function tail(?string $phone): string
    {
        $phone = strtr((string) $phone, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ]);
        return substr(preg_replace('/\D+/', '', $phone), -10);
    }

    private function findCustomer(?string $phone): ?Customer
    {
        $t = $this->tail($phone);
        if (strlen($t) < 9) return null;

        return Customer::whereNotNull('phone')->get()->first(fn($c) => $this->tail($c->phone) === $t);
    }
}