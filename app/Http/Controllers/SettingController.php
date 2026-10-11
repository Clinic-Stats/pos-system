<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\InvoiceLabels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $defaults = InvoiceLabels::defaults();
        $labels   = InvoiceLabels::forSetting($setting);
        $groups   = InvoiceLabels::groups();

        return view('settings.receipt', compact('setting', 'defaults', 'labels', 'groups'));
    }

    public function updateExchangeRate(Request $request)
    {
        $request->validate([
            'exchange_rate' => 'required|numeric|min:1',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create(['exchange_rate' => $request->exchange_rate]);
        } else {
            $setting->update(['exchange_rate' => $request->exchange_rate]);
        }

        return response()->json([
            'success' => true,
            'exchange_rate' => $setting->exchange_rate,
            'message' => 'نرخی ئاڵوگۆڕ نوێکرایەوە'
        ]);
    }

    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $data = $request->validate([
            'shop_name'      => 'required|string|max:255',
            'shop_phone'     => 'nullable|string|max:100',
            'shop_address'   => 'nullable|string|max:255',
            'invoice_footer' => 'nullable|string',
            'receipt_width'  => 'required|string|in:80mm,58mm,a4',
            'show_barcode'   => 'nullable|boolean',
            'shop_logo'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'labels'         => 'nullable|array',
            'labels.*'       => 'nullable|string|max:150',
        ]);

        $data['show_barcode'] = $request->has('show_barcode');
        unset($data['labels']);   // نووسینەکان بە جیا پاشەکەوت دەکرێن

        // بارکردنی لۆگۆ ڕاستەوخۆ بۆ ناو public/uploads/logos
        if ($request->hasFile('shop_logo')) {
            $file = $request->file('shop_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads/logos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            if ($setting->shop_logo && file_exists(public_path($setting->shop_logo))) {
                @unlink(public_path($setting->shop_logo));
            }

            $file->move($destinationPath, $filename);
            $data['shop_logo'] = 'uploads/logos/' . $filename;
        }

        $setting->update($data);

        // نووسینەکانی پسوولە: تەنها ئەوانەی لە بنەڕەتی جیاوازن پاشەکەوت دەکرێن
        if (Schema::hasColumn('settings', 'invoice_labels')) {
            $defaults = InvoiceLabels::defaults();
            $clean = [];
            foreach ((array) $request->input('labels', []) as $key => $value) {
                if (!array_key_exists($key, $defaults)) continue;
                $value = trim((string) $value);
                if ($value !== '' && $value !== $defaults[$key]) {
                    $clean[$key] = $value;
                }
            }
            $setting->invoice_labels = $clean ? json_encode($clean, JSON_UNESCAPED_UNICODE) : null;
            $setting->save();
        }

        return redirect()->back()->with('success', 'ڕێکخستنەکانی پسوولە و لۆگۆ پاشەکەوت کران');
    }
}