<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrCreate(['id' => 1]);
        return view('settings.receipt', compact('setting'));
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
        ]);

        $data['show_barcode'] = $request->has('show_barcode');

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

        return redirect()->back()->with('success', 'ڕێکخستنەکانی پسوولە و لۆگۆ پاشەکەوت کران');
    }
}