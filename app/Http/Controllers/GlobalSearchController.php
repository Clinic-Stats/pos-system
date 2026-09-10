<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CashHandover;
use App\Models\SaleReturn;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('query', ''));

        if (empty($q)) {
            return redirect()->back();
        }

        // ١. گەڕان لە فرۆشتن (ژمارەی وەسڵ و تێبینی)
        $sales = Sale::with(['customer', 'user'])
            ->where('invoice_no', 'LIKE', "%{$q}%")
            ->orWhere('note', 'LIKE', "%{$q}%")
            ->latest()
            ->take(15)
            ->get();

        // ٢. گەڕان لە کڕیاران (ناو، مۆبایل، ناونیشان)
        $customers = Customer::where('name', 'LIKE', "%{$q}%")
            ->orWhere('phone', 'LIKE', "%{$q}%")
            ->orWhere('address', 'LIKE', "%{$q}%")
            ->latest()
            ->take(10)
            ->get();

        // ٣. گەڕان لە کاڵاکان (ناو، بارکۆد)
        $products = Product::where('name', 'LIKE', "%{$q}%")
            ->orWhere('barcode', 'LIKE', "%{$q}%")
            ->latest()
            ->take(10)
            ->get();

        // ٤. گەڕان لە تەسلیماتی کاش (ژمارەی وەسڵ و تێبینی)
        $handovers = CashHandover::with(['mandub', 'receiver'])
            ->where('receipt_no', 'LIKE', "%{$q}%")
            ->orWhere('note', 'LIKE', "%{$q}%")
            ->latest()
            ->take(10)
            ->get();

        // ٥. گەڕان لە گەڕاوەکان (ژمارەی وەسڵ)
        $returns = SaleReturn::with('customer')
            ->where('invoice_no', 'LIKE', "%{$q}%")
            ->orWhere('note', 'LIKE', "%{$q}%")
            ->latest()
            ->take(10)
            ->get();

        return view('search.results', compact('q', 'sales', 'customers', 'products', 'handovers', 'returns'));
    }
}