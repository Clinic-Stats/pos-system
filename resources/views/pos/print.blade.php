<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>پسوولەی فرۆشتن - {{ $sale->invoice_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    @php
    $setting = $setting ?? \App\Models\Setting::first();
    $paperWidth = ($setting && in_array($setting->receipt_width, ['58mm', '80mm'])) ? $setting->receipt_width : '80mm';
    $is58 = $paperWidth === '58mm';
    $bodyWidth = $is58 ? '56mm' : '78mm';
    $fs = $is58 ? '10px' : '12px';
    $fsTd = $is58 ? '10px' : '11px';
    $pad = $is58 ? '4px' : '10px';

    $isUsd = ($sale->currency ?? 'IQD') === 'USD';
    $fmt = fn($v) => $isUsd ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' د.ع';
    $fmtShort = fn($v) => $isUsd ? '$' . number_format((float) $v, 2) : number_format((float) $v);

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
    <style>
        @page {
            size: {{ $paperWidth }} auto;
            margin: 0;
        }

        body {
            font-family: 'Tahoma', 'Noto Sans Arabic', sans-serif;
            width: {{ $bodyWidth }};
            margin: 0 auto;
            padding: {{ $pad }};
            font-size: {{ $fs }};
            color: #000;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-b { border-bottom: 1px dashed #000; }
        .my-2 { margin: 6px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            padding: 4px 0;
            font-size: {{ $fsTd }};
        }

        .barcode {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 34px;
            line-height: 1;
        }

        .toolbar {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
        }

        .btn {
            flex: 1;
            display: block;
            text-align: center;
            padding: 8px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
            font-size: 11px;
        }

        .btn-print { background: #2563eb; }
        .btn-a4 { background: #0891b2; }
        .btn-close { background: #dc2626; }

        @media print {
            .toolbar { display: none; }
        }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body>

    <div class="toolbar">
        <button onclick="window.print()" class="btn btn-print">چاپکردن</button>
        <a href="{{ route('sales.print', $sale->id) }}?type=a4" class="btn btn-a4">A4</a>
        <button onclick="window.close()" class="btn btn-close">داخستن</button>
    </div>

    <!-- سەردێڕ و لۆگۆ -->
    <div class="text-center">
        @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
        <img src="{{ asset($setting->shop_logo) }}" style="max-height: 65px; max-width: 80%; margin: 0 auto 6px auto; display: block; object-fit: contain;">
        @endif
        <h2 style="margin: 0; font-size: 15px; font-weight: bold;">{{ $setting->shop_name ?? 'کۆمپانیای ساموا' }}</h2>
        @if(!empty($setting->shop_phone))
        <div style="font-size: 11px; margin-top: 2px;">تەلەفۆن: {{ $setting->shop_phone }}</div>
        @endif
        @if(!empty($setting->shop_address))
        <div style="font-size: 10px; color: #333;">{{ $setting->shop_address }}</div>
        @endif
        <div style="font-size: 11px; margin-top: 3px; font-weight: bold;">وەسڵی فرۆشتن</div>
    </div>

    <div class="border-b my-2"></div>

    <div>
        <div><strong>وەسڵ:</strong> {{ $sale->invoice_no }}</div>
        <div><strong>بەروار:</strong> {{ $sale->created_at->format('n/j/y, g:i A') }}</div>
        <div><strong>کڕیار:</strong> {{ $sale->customer->name ?? ($sale->guest_name ?: 'کڕیاری گشتی') }}</div>
        @if(!$sale->customer && $sale->guest_phone)
        <div><strong>مۆبایل:</strong> <span dir="ltr">{{ $sale->guest_phone }}</span></div>
        @endif
        @if(!$sale->customer && $sale->guest_address)
        <div><strong>ناونیشان:</strong> {{ $sale->guest_address }}</div>
        @endif
        <div><strong>جۆری پارەدان:</strong> {{ $sale->payment_type == 'cash' ? 'نەقد' : 'قەرز' }}</div>
        <div><b>کاشیر:</b> {{ $sale->user->name ?? (auth()->user()->name ?? 'کارمەند') }}</div>
    </div>

    <div class="border-b my-2"></div>

    <table>
        <thead>
            <tr class="border-b">
                <th style="text-align: right;">کاڵا</th>
                <th class="text-center">بڕ</th>
                <th class="text-left">کۆ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td class="text-center">{{ (float) $item->quantity }} {{ $item->unit->name ?? '' }}</td>
                <td class="text-left font-bold" dir="ltr">{{ $fmtShort($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-b my-2"></div>

    <table style="font-size: 12px;">
        @if(($sale->discount ?? 0) > 0)
        <tr>
            <td>کۆی کاڵاکان:</td>
            <td class="text-left" dir="ltr">{{ $fmt($sale->total_amount + $sale->discount) }}</td>
        </tr>
        <tr>
            <td>داشکاندن:</td>
            <td class="text-left" dir="ltr">{{ $fmt($sale->discount) }}</td>
        </tr>
        @endif
        <tr class="font-bold">
            <td>کۆی گشتی:</td>
            <td class="text-left" dir="ltr">{{ $fmt($sale->total_amount) }}</td>
        </tr>
        @if($sale->payment_type === 'debt')
        <tr>
            <td>بڕی دراو:</td>
            <td class="text-left" dir="ltr">{{ $fmt($sale->paid_amount) }}</td>
        </tr>
        <tr class="font-bold">
            <td>ماوە (قەرز):</td>
            <td class="text-left" dir="ltr">{{ $fmt($sale->remaining_amount) }}</td>
        </tr>
        @endif
        @if($showDebt)
        @php
            $fmtDebt = function($arr) {
                $parts = [];
                if ($arr['USD'] > 0) $parts[] = '$' . number_format($arr['USD'], 2);
                if ($arr['IQD'] > 0) $parts[] = number_format($arr['IQD']) . ' د.ع';
                return empty($parts) ? '0' : implode(' + ', $parts);
            };
        @endphp

        <tr>
            <td>قەرزی پێش ئەم وەسڵە:</td>
            <td class="text-left" dir="ltr">{{ $fmtDebt($debtBefore) }}</td>
        </tr>
        <tr class="font-bold">
            <td>کۆی قەرز دوای ئەم وەسڵە:</td>
            <td class="text-left" dir="ltr">{{ $fmtDebt($debtAfter) }}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-left" style="font-size:10px; color:#555;" dir="ltr">
                کۆی گشتی قەرز بە دۆلار ≈ {{ '$' . number_format($debtAfterUsd, 2) }}
            </td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 10px; padding: 6px; border: 1.5px dashed #000; text-align: center; font-weight: bold; font-size: 12px;">
        کۆی کێشی گشتیی بار:
        <span>{{ number_format($totalWeightKg, 2) }} کگم</span>
        @if($totalWeightKg >= 1000)
        ({{ number_format($totalWeightKg / 1000, 2) }} تەن)
        @endif
    </div>

    @if(!empty($mapUrl))
    <div class="text-center" style="margin-top: 8px;">
        <div id="qrBox" style="width: {{ $is58 ? '80px' : '100px' }}; height: {{ $is58 ? '80px' : '100px' }}; margin: 0 auto; background: #fff;"></div>
        <div style="font-size: 10px; font-weight: bold; margin-top: 3px;">شوێنی گەیاندن (QR)</div>
    </div>
    @endif

    <div class="border-b my-2"></div>

    <div class="text-center" style="font-size: 10px; margin-top: 6px;">
        <p style="margin: 0 0 4px 0;">{{ $setting->invoice_footer ?? 'سوپاس بۆ مامەڵەکەتان' }}</p>

        @if($setting && $setting->show_barcode)
        <div style="margin-top: 4px;">
            <span class="barcode">*{{ $sale->invoice_no }}*</span>
        </div>
        @endif
    </div>

    @if(!empty($mapUrl))
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script>
        (function () {
            var qr = qrcode(0, 'M'); qr.addData(@json($mapUrl)); qr.make();
            var el = document.getElementById('qrBox'); if (!el) return;
            el.innerHTML = qr.createImgTag(4, 0);
            var im = el.querySelector('img'); im.style.width = '100%'; im.style.height = '100%'; im.style.imageRendering = 'pixelated';
        })();
    </script>
    @endif

    <script>
        window.addEventListener('load', function() {
            window.print();
        });
    </script>
</body>

</html>