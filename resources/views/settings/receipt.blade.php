<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕێکخستنی دیزاینی پسوولە</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <form action="{{ route('settings.receipt.update') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-7 bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4 text-xs">
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

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> پاشەکەوتکردن و پەسەندکردنی هێدەری پسوولە
                </button>
            </form>

            <!-- بینینی ڕاستەوخۆ (Live Preview) -->
            <div class="lg:col-span-5 flex flex-col items-center">
                <div class="text-xs font-bold text-slate-400 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-eye"></i> پێشبینینی کاتی چاپ
                </div>

                <div id="receiptPreviewBox" class="bg-white text-black p-4 rounded-xl shadow-2xl font-mono text-[12px] space-y-3 border border-slate-300 transition-all {{ $setting->receipt_width == '58mm' ? 'w-[58mm] text-[10px]' : ($setting->receipt_width == 'a4' ? 'w-full max-w-sm text-xs' : 'w-[80mm]') }}">
                    <div class="text-center space-y-1 border-b border-dashed border-black pb-3">
                        <div id="pv_logo_container" class="{{ !empty($setting->shop_logo) ? '' : 'hidden' }}">
                            <img id="pv_logo" src="{{ !empty($setting->shop_logo) ? asset($setting->shop_logo) : '' }}" class="max-h-16 mx-auto object-contain mb-1">
                        </div>
                        <h2 id="pv_name" class="font-black text-sm text-black">{{ $setting->shop_name }}</h2>
                        <p id="pv_phone" class="text-[11px] text-zinc-700">{{ $setting->shop_phone }}</p>
                        <p id="pv_address" class="text-[10px] text-zinc-600">{{ $setting->shop_address }}</p>
                    </div>

                    <div class="text-[10px] space-y-0.5 border-b border-dashed border-black pb-2 text-zinc-800">
                        <div class="flex justify-between"><span>وەسڵ: #INV-001</span><span>2026-09-09</span></div>
                        <div class="flex justify-between"><span>کڕیار: گشتی</span><span>کاسیە: ئارام</span></div>
                    </div>

                    <table class="w-full text-right text-[11px] border-b border-dashed border-black pb-2">
                        <thead>
                            <tr class="border-b border-black text-[10px]">
                                <th class="py-1">کاڵا</th>
                                <th class="text-center py-1">دانە</th>
                                <th class="text-left py-1">کۆ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="py-1">برنجی کوردی</td><td class="text-center">2</td><td class="text-left font-bold" dir="ltr">8,000</td></tr>
                            <tr><td class="py-1">ڕۆنی زەیتوون</td><td class="text-center">1</td><td class="text-left font-bold" dir="ltr">6,000</td></tr>
                        </tbody>
                    </table>

                    <div class="space-y-1 font-bold text-xs pt-1">
                        <div class="flex justify-between"><span>کۆی گشتی:</span><span dir="ltr">14,000 IQD</span></div>
                        <div class="flex justify-between text-[10px] text-zinc-600"><span>شێوازی پارەدان:</span><span>نەقد</span></div>
                    </div>

                    <div class="text-center border-t border-dashed border-black pt-2 space-y-1">
                        <p id="pv_footer" class="text-[10px] text-zinc-700 leading-tight">{{ $setting->invoice_footer }}</p>
                        <div class="text-[9px] text-zinc-500 pt-1">سیستەمی POS</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        function updatePreview() {
            document.getElementById('pv_name').innerText = document.getElementById('in_name').value || 'ناوی فرۆشگا';
            document.getElementById('pv_phone').innerText = document.getElementById('in_phone').value || '';
            document.getElementById('pv_address').innerText = document.getElementById('in_address').value || '';
            document.getElementById('pv_footer').innerText = document.getElementById('in_footer').value || '';
        }

        function changeReceiptWidth(width) {
            const box = document.getElementById('receiptPreviewBox');
            box.classList.remove('w-[58mm]', 'w-[80mm]', 'w-full', 'max-w-sm', 'text-[10px]');
            
            if (width === '58mm') {
                box.classList.add('w-[58mm]', 'text-[10px]');
            } else if (width === 'a4') {
                box.classList.add('w-full', 'max-w-sm');
            } else {
                box.classList.add('w-[80mm]');
            }
        }

        function previewLogo(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const img = document.getElementById('pv_logo');
                const container = document.getElementById('pv_logo_container');
                img.src = reader.result;
                container.classList.remove('hidden');
            }
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>