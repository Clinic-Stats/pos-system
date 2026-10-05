<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
    $setting = $setting ?? \App\Models\Setting::first();

    $invoiceNo = $purchase->purchase_no ?? $purchase->invoice_no ?? '-';

    $isUsd = ($purchase->currency ?? 'IQD') === 'USD';
    $fmt = fn($v) => $isUsd ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' IQD';

    $totalKg = 0;
    foreach ($purchase->details as $item) {
    $uName = mb_strtolower(trim($item->unit->name ?? ''));
    if (str_contains($uName, 'کارتۆن') || str_contains($uName, 'carton')) {
    $factor = ($item->product->kg_per_carton ?? 1) ?: 1;
    } elseif (str_contains($uName, 'تەن') || str_contains($uName, 'ton')) {
    $factor = 1000;
    } else {
    $factor = ($item->unit->factor_to_base ?? 1) ?: 1;
    }
    $totalKg += $item->quantity * $factor;
    }
    @endphp
    <title>وەسڵی کڕین A4 - {{ $invoiceNo }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Noto Sans Arabic', 'Tahoma', sans-serif; font-size: 13px; color: #111; margin: 0; padding: 20px; background: #f1f5f9; }
        .invoice-box { max-width: 800px; margin: auto; background: #fff; border: 1px solid #ddd; padding: 28px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: center; gap: 16px; border-bottom: 2px solid #222; padding-bottom: 14px; margin-bottom: 18px; }
        .header h1 { margin: 0 0 4px 0; font-size: 22px; font-weight: 800; }
        .header .sub { margin: 2px 0; color: #555; font-size: 12px; }
        .doc-title { display: inline-block; background: #111; color: #fff; padding: 4px 12px; border-radius: 6px; font-weight: bold; font-size: 14px; margin-bottom: 6px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; }
        .badge-cash { background: #d1fae5; color: #065f46; }
        .badge-debt { background: #fef3c7; color: #92400e; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px 10px; text-align: center; }
        .table th { background-color: #111; color: #fff; font-weight: bold; }
        .table tbody tr:nth-child(even) { background: #f9fafb; }
        .bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: start; }
        .weight-box { border: 2px dashed #444; padding: 12px; text-align: center; font-weight: bold; font-size: 14px; background-color: #f9fafb; border-radius: 8px; }
        .totals { border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; padding: 8px 12px; }
        .totals .row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals .grand { border-top: 1px solid #cbd5e1; margin-top: 4px; padding-top: 8px; font-size: 15px; font-weight: 800; }
        .totals .debt { color: #be123c; font-weight: bold; }
        .signs { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; text-align: center; color: #475569; margin-top: 38px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .signs b { display: block; margin-bottom: 30px; }
        .no-print { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px; }
        .btn { background: #2563eb; color: #fff; border: none; padding: 8px 18px; font-size: 13px; font-weight: bold; border-radius: 8px; cursor: pointer; text-decoration: none; font-family: inherit; }
        .btn-pdf { background: #059669; }
        .btn-back { background: #475569; }
        @media print {
            @page { size: A4 portrait; margin: 10mm 15mm; }
            body { background: #fff; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .invoice-box { border: none; padding: 0; max-width: none; }
        }
    </style>
</head>

<body>

   <div class="no-print">
    <button onclick="closeOrRedirect('{{ route('purchases.index') }}')" class="btn btn-back">گەڕانەوە بۆ لیستی کڕینەکان</button>
    <button class="btn" onclick="window.print()">چاپکردن (A4)</button>
    <button class="btn btn-pdf" onclick="window.print()">پاشەکەوتکردن وەک PDF</button>
</div>

    <div class="invoice-box">

        <!-- هێدەر -->
        <div class="header">
            <div style="flex:1;">
                <h1>{{ $setting->shop_name ?? 'کۆمپانیای بازرگانی' }}</h1>
                @if(!empty($setting->shop_address))
                <p class="sub">{{ $setting->shop_address }}</p>
                @endif
                @if(!empty($setting->shop_phone))
                <p class="sub" dir="ltr" style="text-align:right;">{{ $setting->shop_phone }}</p>
                @endif
            </div>

            @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
            <div style="width:120px;text-align:center;">
                <img src="{{ asset($setting->shop_logo) }}" style="max-height:70px;max-width:100%;object-fit:contain;">
            </div>
            @endif

            <div style="flex:1;text-align:left;" dir="ltr">
                <div class="doc-title">وەسڵی کڕین (A4)</div>
                <div><b>No:</b> {{ $invoiceNo }}</div>
                <div><b>Date:</b> {{ $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i') : $purchase->purchase_date }}</div>
            </div>
        </div>

        <!-- زانیاری دابینکەر -->
        <div class="meta-grid">
            <div><b>کۆمپانیا / دابینکەر:</b> {{ $purchase->supplier->name ?? 'گشتی' }}</div>
            <div><b>تەلەفۆن:</b> <span dir="ltr">{{ $purchase->supplier->phone ?? '-' }}</span></div>
            <div>
                <b>جۆری پارەدان:</b>
                <span class="badge {{ ($purchase->payment_type ?? 'cash') === 'cash' ? 'badge-cash' : 'badge-debt' }}">
                    {{ ($purchase->payment_type ?? 'cash') === 'cash' ? 'نەقد' : 'قەرز' }}
                </span>
            </div>
            <div><b>دراو:</b> {{ $isUsd ? 'USD ($)' : 'IQD (دینار)' }}</div>
        </div>

        <!-- خشتەی کاڵاکان -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>ناوی کاڵا</th>
                    <th>یەکە</th>
                    <th>بڕ</th>
                    <th>نرخی کڕین</th>
                    <th>کۆی نرخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align:right;font-weight:bold;">{{ $item->product->name ?? 'کاڵا' }}</td>
                    <td>{{ $item->unit->name ?? 'کگ' }}</td>
                    <td>{{ (float) $item->quantity }}</td>
                    <td dir="ltr">{{ $fmt($item->unit_buy_price) }}</td>
                    <td dir="ltr" style="font-weight:bold;">{{ $fmt($item->subtotal) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- کێش و کۆی پارەکان -->
        <div class="bottom">
            <div class="weight-box">
                کۆی کێشی گشتی بارەکە:
                <div style="margin-top:4px;">
                    {{ number_format($totalKg, 2) }} کیلۆگرام
                    @if($totalKg >= 1000)
                    <span style="color:#4b5563;font-weight:normal;">({{ number_format($totalKg / 1000, 3) }} تەن)</span>
                    @endif
                </div>
            </div>

            <div class="totals">
                <div class="row grand">
                    <span>کۆی گشتی کڕین:</span>
                    <span dir="ltr">{{ $fmt($purchase->total_amount) }}</span>
                </div>
                @if(($purchase->payment_type ?? 'cash') === 'debt')
                <div class="row">
                    <span>پارەی دراو:</span>
                    <span dir="ltr">{{ $fmt($purchase->paid_amount) }}</span>
                </div>
                <div class="row debt">
                    <span>ماوە (قەرز):</span>
                    <span dir="ltr">{{ $fmt($purchase->remaining_amount) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- واژوو -->
        <div class="signs">
            <div><b>واژووی وەرگر / کۆگا</b>......................</div>
            <div><b>واژووی دابینکەر</b>......................</div>
        </div>

    </div>

    <!-- کۆدی جاڤاسکریپت بۆ داخستنی تابەکە دوای چاپکردن -->
   <script>
    function closeOrRedirect(url) {
        // هەوڵدان بۆ داخستنی تابەکە
        window.close();
        
        // ئەگەر وێبگەڕەکە ڕێگەی نەدا تابەکە دابخرێت، دوای ١٠٠ میلی چرکە دەگەڕێتەوە بۆ لیستەکە
        setTimeout(function() {
            window.location.href = url;
        }, 100);
    }

    // کاتێک چاپەکە تەواو بوو، تابەکە دەخرێتەوە
    window.onafterprint = function() {
        window.close();
    };
</script>

</body>

</html>