<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پسوولەی فرۆشتن A4 - {{ $sale->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&family=Libre+Barcode+39&display=swap" rel="stylesheet">
    @php
    $setting = \App\Models\Setting::first();

    // نیشاندانی بڕ بەپێی دراوی وەسڵ (USD یان IQD)
    $isUsd = ($sale->currency ?? 'IQD') === 'USD';
    $fmt = fn($v) => $isUsd ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' د.ع';

    // کێشی گشتی
    $totalWeightKg = 0;
    foreach ($sale->details as $item) {
    $unitName = strtolower(trim($item->unit->name ?? ''));
    if (str_contains($unitName, 'کارتۆن') || str_contains($unitName, 'carton')) {
    $totalWeightKg += $item->quantity * ($item->product->kg_per_carton ?? 1 ?: 1);
    } elseif (str_contains($unitName, 'تەن') || str_contains($unitName, 'ton')) {
    $totalWeightKg += $item->quantity * 1000;
    } else {
    $totalWeightKg += $item->quantity * ($item->unit->factor_to_base ?? 1 ?: 1);
    }
    }
    @endphp

    {{-- قەرزی کڕیار پێش و دوای ئەم وەسڵە --}}
    @include('partials.sale-debt')

    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
        }

        .barcode {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 38px;
            line-height: 1;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 15mm;
            }

            body {
                margin: 0;
                padding: 0 !important;
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen p-6">

    <div class="sheet max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 text-xs">

        <!-- دوگمەکانی چاپ و داخستن -->
        <div class="no-print flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <a href="{{ route('sales.list') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl font-bold transition">گەڕانەوە بۆ لیستی فرۆشتنەکان</a>
            <div class="flex gap-2">
                <a href="{{ route('sales.print', $sale->id) }}?type=small" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl font-bold transition">وەسڵی بچووک</a>
                <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">چاپکردن</button>
                <button onclick="window.close()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition">داخستن</button>
            </div>
        </div>

        <!-- هێدەر -->
        <div class="flex justify-between items-center pb-6 border-b-2 border-slate-800 gap-4">
            <div class="space-y-1.5 flex-1">
                <h1 class="text-2xl font-black text-slate-900">{{ $setting->shop_name ?? 'کۆمپانیای بازرگانی' }}</h1>
                @if(!empty($setting->shop_address))
                <p class="text-slate-600 text-xs font-medium">{{ $setting->shop_address }}</p>
                @endif
                @if(!empty($setting->shop_phone))
                <p class="text-slate-700 font-mono text-xs font-bold">تەلەفۆن: {{ $setting->shop_phone }}</p>
                @endif
            </div>

            @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
            <div class="w-32 h-20 flex items-center justify-center">
                <img src="{{ asset($setting->shop_logo) }}" class="max-h-20 max-w-full object-contain">
            </div>
            @endif

            <div class="text-left space-y-1 flex-1">
                <div class="font-mono text-xs font-bold text-slate-800">وەسڵ: {{ $sale->invoice_no }}</div>
                <div class="font-mono text-xs text-slate-600">بەروار: {{ $sale->created_at->format('n/j/y, g:i A') }}</div>
            </div>
        </div>

        <!-- زانیاری کڕیار -->
        <div class="grid grid-cols-2 gap-4 my-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="space-y-1">
                <span class="block text-slate-500 text-[11px] font-bold">زانیاری کڕیار:</span>
                <span class="text-sm font-bold text-slate-900">{{ $sale->customer->name ?? 'کڕیاری گشتی' }}</span>
                @if($sale->customer && $sale->customer->phone)
                <span class="block font-mono text-slate-600">{{ $sale->customer->phone }}</span>
                @endif
            </div>
            <div class="space-y-1 text-left">
                <span class="block text-slate-500 text-[11px] font-bold">شێوازی مامەڵە:</span>
                <span class="inline-block px-2.5 py-0.5 rounded-md font-bold {{ $sale->payment_type === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $sale->payment_type === 'cash' ? 'نەقد' : 'قەرز' }}
                </span>
                <span class="block text-[11px] text-slate-600">کاشیر / مەندووب: <b>{{ $sale->user->name ?? 'ئەدمین' }}</b></span>
            </div>
        </div>

        <!-- خشتەی کاڵاکان -->
        <table class="w-full text-right border border-slate-300 rounded-xl overflow-hidden mb-6">
            <thead class="bg-slate-900 text-white text-[11px]">
                <tr>
                    <th class="p-2.5 text-center w-12">#</th>
                    <th class="p-2.5">ناوی کاڵا</th>
                    <th class="p-2.5 text-center">یەکە</th>
                    <th class="p-2.5 text-center">بڕ</th>
                    <th class="p-2.5 text-center">نرخی یەکە</th>
                    <th class="p-2.5 text-left">کۆی گشتی</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($sale->details as $index => $item)
                <tr>
                    <td class="p-2.5 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                    <td class="p-2.5 font-bold text-slate-900">{{ $item->product->name ?? '-' }}</td>
                    <td class="p-2.5 text-center text-slate-600">{{ $item->unit->name ?? 'دانە' }}</td>
                    <td class="p-2.5 text-center font-mono font-bold">{{ (float) $item->quantity }}</td>
                    <td class="p-2.5 text-center font-mono" dir="ltr">{{ $fmt($item->unit_price ?? ($item->line_total / ($item->quantity ?: 1))) }}</td>
                    <td class="p-2.5 text-left font-mono font-bold text-slate-900" dir="ltr">{{ $fmt($item->line_total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- کێش و کۆی پارەکان -->
        <div class="grid grid-cols-2 gap-6 items-start mb-6">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <div class="font-bold text-slate-700">کۆی کێشی بار:</div>
                <div class="text-base font-black text-slate-900 font-mono">
                    {{ number_format($totalWeightKg, 2) }} کگم
                    @if($totalWeightKg >= 1000)
                    <span class="text-xs text-slate-600 font-normal">({{ number_format($totalWeightKg / 1000, 2) }} تەن)</span>
                    @endif
                </div>
            </div>

            <div class="space-y-1.5 p-4 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                @if(($sale->discount ?? 0) > 0)
                <div class="flex justify-between text-slate-600">
                    <span>کۆی کاڵاکان:</span>
                    <span class="font-mono" dir="ltr">{{ $fmt($sale->total_amount + $sale->discount) }}</span>
                </div>
                <div class="flex justify-between text-amber-600">
                    <span>داشکاندن:</span>
                    <span class="font-mono" dir="ltr">- {{ $fmt($sale->discount) }}</span>
                </div>
                @endif

                <div class="flex justify-between text-base font-black text-slate-900 pt-1 border-t border-slate-300">
                    <span>کۆی گشتی:</span>
                    <span class="font-mono text-emerald-700" dir="ltr">{{ $fmt($sale->total_amount) }}</span>
                </div>

                @if($sale->payment_type === 'debt')
                <div class="flex justify-between text-slate-600 pt-1 border-t border-slate-200 text-[11px]">
                    <span>بڕی پارەی دراو:</span>
                    <span class="font-mono" dir="ltr">{{ $fmt($sale->paid_amount) }}</span>
                </div>
                <div class="flex justify-between text-rose-700 font-black text-[11px]">
                    <span>ماوە (قەرز):</span>
                    <span class="font-mono" dir="ltr">{{ $fmt($sale->remaining_amount) }}</span>
                </div>
                @endif

                @if($showDebt)
                <div class="flex justify-between text-slate-600 pt-1 border-t border-slate-200 text-[11px]">
                    <span>قەرزی پێش ئەم پسوولەیە:</span>
                    <span class="font-mono" dir="ltr">{{ $fmt($debtBefore) }}</span>
                </div>
                <div class="flex justify-between text-rose-700 font-black text-[12px]">
                    <span>کۆی گشتی قەرز دوای ئەم پسوولەیە:</span>
                    <span class="font-mono" dir="ltr">{{ $fmt($debtAfter) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- واژوو -->
        <div class="pt-6 border-t border-slate-200 grid grid-cols-3 gap-4 text-center text-slate-600">
            <div>
                <span class="block mb-8 font-bold">واژووی کڕیار</span>
                <span>......................</span>
            </div>
            <div class="flex flex-col items-center justify-center">
                @if($setting && $setting->show_barcode)
                <span class="barcode text-slate-800">*{{ $sale->invoice_no }}*</span>
                @endif
                <p class="text-[10px] mt-1">{{ $setting->invoice_footer ?? 'سوپاس بۆ سەردانەکەتان' }}</p>
            </div>
            <div>
                <span class="block mb-8 font-bold">واژووی ژمێریار / کۆگا</span>
                <span>......................</span>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>