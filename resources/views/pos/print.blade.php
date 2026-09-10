<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پسوولەی فرۆشتن - {{ $sale->invoice_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
    @php
        $setting = \App\Models\Setting::first();
        $paperWidth = $setting ? $setting->receipt_width : '80mm';
        $bodyWidth = ($paperWidth === '58mm') ? '56mm' : '78mm';
    @endphp
    <style>
        @page { size: {{ $paperWidth }} auto; margin: 0; }
        body {
            font-family: 'Tahoma', 'Noto Sans Arabic', sans-serif;
            width: {{ $bodyWidth }};
            margin: 0 auto;
            padding: {{ ($paperWidth === '58mm') ? '4px' : '10px' }};
            font-size: {{ ($paperWidth === '58mm') ? '10px' : '12px' }};
            color: #000;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-b { border-bottom: 1px dashed #000; }
        .my-2 { margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { padding: 4px 0; font-size: {{ ($paperWidth === '58mm') ? '10px' : '11px' }}; }
        .barcode { font-family: 'Libre Barcode 39', cursive; font-size: 34px; line-height: 1; }
        .btn-print {
            display: block;
            width: 100%;
            padding: 8px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
        }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">چاپکردنی پسوولە</button>

    <!-- بەشی سەردێڕ و لۆگۆ بە شێوەی داینامیک لە Settings -->
   <!-- بەشی سەردێڕ و لۆگۆ -->
    <div class="text-center">
        @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
            <img src="{{ asset($setting->shop_logo) }}" style="max-height: 65px; max-width: 80%; margin: 0 auto 6px auto; display: block; object-contain: contain;">
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
        <div><strong>بەروار:</strong> {{ $sale->created_at->format('Y-m-d H:i') }}</div>
        <div><strong>کڕیار:</strong> {{ $sale->customer->name ?? 'کڕیاری گشتی' }}</div>
        <div><strong>جۆری پارەدان:</strong> {{ $sale->payment_type == 'cash' ? 'نەقد' : 'قەرز' }}</div>
        <div><b>نوێنەری فرۆشتن / کاشیر:</b> <span style="font-weight: bold;">{{ $sale->user->name ?? (auth()->user()->name ?? 'کارمەند') }}</span></div>
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
                <td class="text-left font-bold" dir="ltr">{{ number_format($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-b my-2"></div>

    <table style="font-size: 12px;">
        @if(isset($sale->discount) && $sale->discount > 0)
        <tr>
            <td>کۆی کاڵاکان:</td>
            <td class="text-left" dir="ltr">{{ number_format($sale->total_amount + $sale->discount) }} د.ع</td>
        </tr>
        <tr>
            <td>داشکاندن:</td>
            <td class="text-left" dir="ltr">{{ number_format($sale->discount) }} د.ع</td>
        </tr>
        @endif
        <tr class="font-bold">
            <td>کۆی گشتی:</td>
            <td class="text-left" dir="ltr">{{ number_format($sale->total_amount) }} د.ع</td>
        </tr>
        @if($sale->payment_type === 'debt')
        <tr>
            <td>بڕی دراو:</td>
            <td class="text-left" dir="ltr">{{ number_format($sale->paid_amount) }} د.ع</td>
        </tr>
        <tr class="font-bold">
            <td>ماوە (قەرز):</td>
            <td class="text-left" dir="ltr">{{ number_format($sale->remaining_amount) }} د.ع</td>
        </tr>
        @endif
    </table>

    @php
        $totalWeightKg = 0;
        foreach($sale->details as $item) {
            $unitName = strtolower(trim($item->unit->name ?? ''));
            if (str_contains($unitName, 'کارتۆن') || str_contains($unitName, 'carton')) {
                $totalWeightKg += $item->quantity * ($item->product->kg_per_carton ?: 1);
            } elseif (str_contains($unitName, 'تەن') || str_contains($unitName, 'ton')) {
                $totalWeightKg += $item->quantity * 1000;
            } else {
                $totalWeightKg += $item->quantity * ($item->unit->factor_to_base ?: 1);
            }
        }
    @endphp

    <div style="margin-top: 10px; padding: 6px; border: 1.5px dashed #000; text-align: center; font-weight: bold; font-size: 12px;">
        کۆی کێشی گشتیی بار: 
        <span>{{ number_format($totalWeightKg, 2) }} کگم</span>
        @if($totalWeightKg >= 1000)
            ({{ number_format($totalWeightKg / 1000, 2) }} تەن)
        @endif
    </div>

    <div class="border-b my-2"></div>

    <!-- فووتەر و ڕێنمایی دیاریکراو لە Settings -->
    <div class="text-center" style="font-size: 10px; margin-top: 6px;">
        <p style="margin: 0 0 4px 0;">{{ $setting->invoice_footer ?? 'سوپاس بۆ مامەڵەکەتان' }}</p>

        @if($setting && $setting->show_barcode)
            <div style="margin-top: 4px;">
                <span class="barcode">*{{ $sale->invoice_no }}*</span>
            </div>
        @endif
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>