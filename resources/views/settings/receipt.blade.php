<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕێکخستنی دیزاینی پسوولە</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        .a4p-table { width: 100%; border-collapse: collapse; font-weight: 800; }
        .a4p-table th { background: #4f81bd; color: #fff; border: 1px solid #fff; padding: 4px 2px; font-size: 9px; }
        .a4p-table td { border: 1px solid #111; padding: 3px 3px; text-align: center; font-size: 9.5px; }
        .a4p-sum { width: 100%; border-collapse: separate; border-spacing: 0 3px; font-weight: 800; }
        .a4p-sum .lbl { background: #b8cce4; text-align: center; padding: 5px 4px; font-size: 10px; }
        .a4p-sum .val { width: 32%; background: #fff; border: 1px solid #111; text-align: center; padding: 4px 3px; font-size: 11px; direction: ltr; }
        .a4p-sum .val.dark { background: #366092; color: #fff; border-color: #366092; }
        .a4p-sum2 { border-spacing: 4px 3px; width: calc(100% + 8px); margin: 0 -4px; }
        .a4p-sum2 .lbl { width: 22%; font-size: 9px; padding: 4px 2px; line-height: 1.3; }
        .a4p-sum2 .val { width: 28%; font-size: 10px; }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-emerald-400"></i>
                دیزاین و ڕێکخستنی سەردێڕی پسوولەی فرۆشتن
            </h1>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                گەڕانەوە بۆ POS
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-600/20 border border-red-500 text-red-400 p-3.5 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <form action="{{ route('settings.receipt.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-6 bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-slate-300 font-bold mb-1">ناوی فرۆشگا / کۆمپانیا:</label>
                    <input type="text" name="shop_name" id="in_name" value="{{ $setting->shop_name }}" oninput="updatePreview()" required class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ژمارەی تەلەفۆن / مۆبایل:</label>
                        <input type="text" name="shop_phone" id="in_phone" value="{{ $setting->shop_phone }}" oninput="updatePreview()" class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ناونیشان:</label>
                        <input type="text" name="shop_address" id="in_address" value="{{ $setting->shop_address }}" oninput="updatePreview()" class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">وێنە / لۆگۆی پسوولە:</label>
                    <input type="file" name="shop_logo" id="in_logo" accept="image/*" onchange="previewLogo(event)" class="w-full p-2 rounded-xl bg-slate-700 border border-slate-600 text-slate-300 file:bg-slate-800 file:border-0 file:text-white file:px-3 file:py-1 file:rounded-lg file:text-xs">
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">تێبینی و ڕێنمایی خوارەوەی پسوولە (Footer):</label>
                    <textarea name="invoice_footer" id="in_footer" rows="2" oninput="updatePreview()" class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">{{ $setting->invoice_footer }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-700">
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">قەبارەی کاغەزی پرینتەر:</label>
                        <select name="receipt_width" id="in_width" onchange="changeReceiptWidth(this.value)" class="w-full p-2 rounded-xl bg-slate-700 border border-slate-600 text-white">
                            <option value="80mm" {{ $setting->receipt_width == '80mm' ? 'selected' : '' }}>80mm (پێوانەی گشتی POS)</option>
                            <option value="58mm" {{ $setting->receipt_width == '58mm' ? 'selected' : '' }}>58mm (پێوانەی بچووک)</option>
                            <option value="a4" {{ $setting->receipt_width == 'a4' ? 'selected' : '' }}>A4 (پسوولەی گەورەی فەرمی)</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 font-bold">
                            <input type="checkbox" name="show_barcode" value="1" {{ $setting->show_barcode ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                            <span>بارکۆدی پسوولە پیشان بدرێت</span>
                        </label>
                    </div>
                </div>

                <!-- نووسینەکانی پسوولە -->
                <details class="pt-2 border-t border-slate-700 group" {{ $errors->has('labels.*') ? 'open' : '' }}>
                    <summary class="cursor-pointer flex items-center justify-between text-slate-200 font-bold text-sm py-2 select-none">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-emerald-400"></i>
                            نووسینەکانی پسوولە (A4 و بچووک)
                        </span>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition group-open:rotate-180"></i>
                    </summary>

                    <p class="text-slate-400 text-[11px] mb-3 leading-relaxed">
                        هەر نووسینێک بگۆڕیت لە پسوولەی A4 و چاپی بچووک دا دەگۆڕێت. ئەگەر بەتاڵی بهێڵیتەوە، دەگەڕێتەوە بۆ بنەڕەتی.
                    </p>

                    @foreach($groups as $groupName => $keys)
                        <div class="mb-4 bg-slate-900/40 border border-slate-700 rounded-xl p-3">
                            <h3 class="text-emerald-400 font-bold text-xs mb-2">{{ $groupName }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($keys as $key)
                                    <div>
                                        <label for="label_{{ $key }}" class="block text-slate-400 text-[10px] mb-1">
                                            بنەڕەتی: {{ $defaults[$key] }}
                                        </label>
                                        <input type="text"
                                               id="label_{{ $key }}"
                                               name="labels[{{ $key }}]"
                                               value="{{ old('labels.' . $key, $labels[$key]) }}"
                                               placeholder="{{ $defaults[$key] }}"
                                               maxlength="150"
                                               class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white focus:outline-none focus:border-blue-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="button" onclick="resetLabels()" class="text-[11px] text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left"></i> گەڕاندنەوەی هەموو نووسینەکان بۆ بنەڕەتی
                    </button>
                </details>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> پاشەکەوتکردن و پەسەندکردنی هێدەری پسوولە
                </button>
            </form>

            <!-- بینینی ڕاستەوخۆ (Live Preview) -->
            <div class="lg:col-span-6 flex flex-col items-center lg:sticky lg:top-6">
                <div class="text-xs font-bold text-slate-400 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-eye"></i> پێشبینینی کاتی چاپ
                </div>

                {{-- ═══════ پێشبینینی A4 (شین) ═══════ --}}
                <div id="a4Preview" class="{{ $setting->receipt_width == 'a4' ? '' : 'hidden' }} bg-white text-black rounded-xl shadow-2xl border border-slate-300 p-4 w-full" style="font-size:10px;">

                    <div class="relative text-center pb-2 flex flex-col items-center justify-center" style="min-height:56px;">
                        <div class="pv-logo-container absolute left-0 top-0 w-14 h-14 flex items-center justify-center {{ !empty($setting->shop_logo) ? '' : 'hidden' }}">
                            <img class="pv-logo max-h-14 max-w-full object-contain" src="{{ !empty($setting->shop_logo) ? asset($setting->shop_logo) : '' }}">
                        </div>
                        <h1 data-bind="name" style="color:#243b9b;font-size:19px;font-weight:900;line-height:1.2;padding:0 60px;">{{ $setting->shop_name }}</h1>
                        <p style="font-size:9px;font-weight:700;margin-top:2px;"><span data-pv="shop_address_label">{{ $labels['shop_address_label'] }}</span> : <span data-bind="address">{{ $setting->shop_address }}</span></p>
                        <p style="font-size:9px;font-weight:700;" dir="ltr"><span data-bind="phone">{{ $setting->shop_phone }}</span> :<span data-pv="shop_phone_label">{{ $labels['shop_phone_label'] }}</span></p>
                    </div>

                    <div style="border-top:2px dashed #111;"></div>

                    <div class="text-center" style="padding:6px 0;font-size:16px;font-weight:900;">پسووڵەی فرۆشتن</div>

                    <div class="grid grid-cols-3 gap-1 items-center" style="font-size:9px;font-weight:800;padding-bottom:3px;">
                        <div class="text-right"><span data-pv="invoice_no">{{ $labels['invoice_no'] }}</span> <span style="color:#dc2626;font-size:13px;font-weight:900;">1</span></div>
                        <div class="text-center" style="font-size:13px;font-weight:900;line-height:1.2;">کڕیار : حاجی کارزان</div>
                        <div class="text-left"><span data-pv="cashier">{{ $labels['cashier'] }}</span> : ئارام</div>
                    </div>
                    <div class="grid grid-cols-3 gap-1 items-center" style="font-size:9px;font-weight:800;padding-bottom:5px;">
                        <div class="text-right"><span data-pv="invoice_type">{{ $labels['invoice_type'] }}</span> : <span style="color:#be123c;" data-pv="type_debt">{{ $labels['type_debt'] }}</span></div>
                        <div class="text-center"><span data-pv="customer_phone">{{ $labels['customer_phone'] }}</span> : <span dir="ltr">0770 000 0000</span></div>
                        <div class="text-left" dir="ltr">10/11/26, 12:34 AM</div>
                    </div>
                    <div style="font-size:9px;font-weight:700;padding-bottom:5px;"><span data-pv="customer_address">{{ $labels['customer_address'] }}</span> : سلێمانی</div>

                    <table class="a4p-table">
                        <thead>
                            <tr>
                                <th style="width:5%" data-pv="col_no">{{ $labels['col_no'] }}</th>
                                <th style="width:11%" data-pv="col_code">{{ $labels['col_code'] }}</th>
                                <th data-pv="col_item">{{ $labels['col_item'] }}</th>
                                <th style="width:11%" data-pv="col_unit">{{ $labels['col_unit'] }}</th>
                                <th style="width:10%" data-pv="col_qty">{{ $labels['col_qty'] }}</th>
                                <th style="width:15%" data-pv="col_price">{{ $labels['col_price'] }}</th>
                                <th style="width:15%" data-pv="col_total">{{ $labels['col_total'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>01</td><td>سنگی مریشک 2 کگم</td><td>تەن</td><td>4</td><td dir="ltr">$3,000.00</td><td dir="ltr">$12,000.00</td></tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="border:0;text-align:right;font-size:10px;"><div style="display:flex;justify-content:space-between;align-items:center"><span data-pv="total_row">{{ $labels['total_row'] }}</span><span>4,000.00 کگم (4.00 تەن)</span><span data-pv="total_row" style="visibility:hidden">{{ $labels['total_row'] }}</span></div></td>
                                <td style="border:0;font-size:10px;">4</td>
                                <td style="border:0;"></td>
                                <td style="border:0;font-size:10px;" dir="ltr">$12,000.00</td>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="a4p-sum a4p-sum2">
                        <tr><td class="lbl" data-pv="discount">{{ $labels['discount'] }}</td><td class="val">$0.00</td><td class="lbl" data-pv="after_discount">{{ $labels['after_discount'] }}</td><td class="val">$12,000.00</td></tr>
                        <tr><td class="lbl" data-pv="paid">{{ $labels['paid'] }}</td><td class="val">$0.00</td><td class="lbl" style="color:#b91c1c" data-pv="remaining">{{ $labels['remaining'] }}</td><td class="val" style="color:#b91c1c">$12,000.00</td></tr>
                    </table>

                    <div style="border-top:2px dashed #111;margin:4px 0;"></div>

                    <table class="a4p-sum">
                        <tr><td class="lbl" data-pv="debt_before">{{ $labels['debt_before'] }}</td><td class="val dark">0</td></tr>
                        <tr><td class="lbl" data-pv="debt_after">{{ $labels['debt_after'] }}</td><td class="val" style="color:#b91c1c">$12,000.00</td></tr>
                    </table>
                    <div class="text-left" dir="ltr" style="font-size:8px;font-weight:700;color:#475569;"><span data-pv="debt_usd_note">{{ $labels['debt_usd_note'] }}</span> ≈ $12,000.00</div>

                    <div class="flex items-center gap-2" style="margin-top:8px;padding-top:6px;border-top:1px solid #cbd5e1;">
                        <div class="shrink-0 flex items-center justify-center bg-slate-200 text-slate-500" style="width:44px;height:44px;font-size:9px;font-weight:800;">QR</div>
                        <div style="font-size:9px;font-weight:800;line-height:1.3;">
                            <span data-pv="delivery">{{ $labels['delivery'] }}</span>
                            <div style="font-weight:400;font-size:8px;color:#64748b;" data-pv="qr_hint">{{ $labels['qr_hint'] }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center" style="margin-top:12px;padding-top:6px;border-top:1px solid #cbd5e1;font-size:9px;color:#334155;">
                        <div><span class="block font-bold" style="margin-bottom:14px;" data-pv="sign_customer">{{ $labels['sign_customer'] }}</span>......................</div>
                        <div class="flex flex-col items-center justify-center"><p data-bind="footer" style="font-size:8px;font-weight:700;">{{ $setting->invoice_footer }}</p></div>
                        <div><span class="block font-bold" style="margin-bottom:14px;" data-pv="sign_accountant">{{ $labels['sign_accountant'] }}</span>......................</div>
                    </div>
                </div>

                {{-- ═══════ پێشبینینی 80mm / 58mm ═══════ --}}
                <div id="receiptPreviewBox" class="{{ $setting->receipt_width == 'a4' ? 'hidden' : '' }} bg-white text-black p-4 rounded-xl shadow-2xl font-mono text-[12px] space-y-3 border border-slate-300 transition-all {{ $setting->receipt_width == '58mm' ? 'w-[58mm] text-[10px]' : 'w-[80mm]' }}">
                    <div class="text-center space-y-1 border-b border-dashed border-black pb-3">
                        <div class="pv-logo-container {{ !empty($setting->shop_logo) ? '' : 'hidden' }}">
                            <img class="pv-logo max-h-16 mx-auto object-contain mb-1" src="{{ !empty($setting->shop_logo) ? asset($setting->shop_logo) : '' }}">
                        </div>
                        <h2 data-bind="name" class="font-black text-sm text-black">{{ $setting->shop_name }}</h2>
                        <p class="text-[11px] text-zinc-700"><span data-pv="shop_phone_label">{{ $labels['shop_phone_label'] }}</span>: <span data-bind="phone">{{ $setting->shop_phone }}</span></p>
                        <p data-bind="address" class="text-[10px] text-zinc-600">{{ $setting->shop_address }}</p>
                    </div>

                    <div class="text-[10px] space-y-0.5 border-b border-dashed border-black pb-2 text-zinc-800">
                        <div class="flex justify-between"><span><span data-pv="invoice_no">{{ $labels['invoice_no'] }}</span>: #INV-001</span><span>2026-09-09</span></div>
                        <div class="flex justify-between"><span>کڕیار: گشتی</span><span><span data-pv="cashier">{{ $labels['cashier'] }}</span>: ئارام</span></div>
                    </div>

                    <table class="w-full text-right text-[11px] border-b border-dashed border-black pb-2">
                        <thead>
                            <tr class="border-b border-black text-[10px]">
                                <th class="py-1" data-pv="col_item">{{ $labels['col_item'] }}</th>
                                <th class="text-center py-1" data-pv="col_qty">{{ $labels['col_qty'] }}</th>
                                <th class="text-left py-1" data-pv="col_total">{{ $labels['col_total'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="py-1">برنجی کوردی</td><td class="text-center">2</td><td class="text-left font-bold" dir="ltr">8,000</td></tr>
                            <tr><td class="py-1">ڕۆنی زەیتوون</td><td class="text-center">1</td><td class="text-left font-bold" dir="ltr">6,000</td></tr>
                        </tbody>
                    </table>

                    <div class="space-y-1 font-bold text-xs pt-1">
                        <div class="flex justify-between"><span>کۆی گشتی:</span><span dir="ltr">14,000 IQD</span></div>
                        <div class="flex justify-between text-[10px] text-zinc-600"><span>شێوازی پارەدان:</span><span data-pv="type_cash">{{ $labels['type_cash'] }}</span></div>
                    </div>

                    <div class="text-center border border-dashed border-black p-1 text-[10px] font-bold">
                        <span data-pv="weight_row">{{ $labels['weight_row'] }}</span>: 40.00 کگم
                    </div>

                    <div class="text-center border-t border-dashed border-black pt-2 space-y-1">
                        <p data-bind="footer" class="text-[10px] text-zinc-700 leading-tight">{{ $setting->invoice_footer }}</p>
                        <div class="text-[9px] text-zinc-500 pt-1">سیستەمی POS</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        const labelDefaults = @json($defaults);

        function bind(name, value) {
            document.querySelectorAll('[data-bind="' + name + '"]').forEach(function (el) {
                el.innerText = value;
            });
        }

        function updatePreview() {
            bind('name', document.getElementById('in_name').value || 'ناوی فرۆشگا');
            bind('phone', document.getElementById('in_phone').value || '');
            bind('address', document.getElementById('in_address').value || '');
            bind('footer', document.getElementById('in_footer').value || '');
        }

        // نوێکردنەوەی نووسینەکانی پێشبینین کاتێک نووسینەکان دەگۆڕدرێن
        document.querySelectorAll('input[name^="labels["]').forEach(function (input) {
            input.addEventListener('input', function () {
                const key = input.name.match(/^labels\[(.+)\]$/)[1];
                const value = input.value.trim() || labelDefaults[key];
                document.querySelectorAll('[data-pv="' + key + '"]').forEach(function (el) {
                    el.innerText = value;
                });
            });
        });

        function resetLabels() {
            if (!confirm('هەموو نووسینەکان دەگەڕێنەوە بۆ بنەڕەتی. دڵنیایت؟')) return;
            document.querySelectorAll('input[name^="labels["]').forEach(function (input) {
                const key = input.name.match(/^labels\[(.+)\]$/)[1];
                input.value = labelDefaults[key];
                input.dispatchEvent(new Event('input'));
            });
        }

        function changeReceiptWidth(width) {
            const box = document.getElementById('receiptPreviewBox');
            const a4 = document.getElementById('a4Preview');
            box.classList.remove('w-[58mm]', 'w-[80mm]', 'text-[10px]');

            if (width === 'a4') {
                a4.classList.remove('hidden');
                box.classList.add('hidden');
                return;
            }

            a4.classList.add('hidden');
            box.classList.remove('hidden');
            if (width === '58mm') {
                box.classList.add('w-[58mm]', 'text-[10px]');
            } else {
                box.classList.add('w-[80mm]');
            }
        }

        function previewLogo(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.querySelectorAll('.pv-logo').forEach(function (img) { img.src = reader.result; });
                document.querySelectorAll('.pv-logo-container').forEach(function (c) { c.classList.remove('hidden'); });
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>