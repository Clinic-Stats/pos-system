<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کەشفی حساب - {{ $targetUser->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Noto Sans Arabic', Tahoma, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 20px; background: #f1f5f9; }
        .page { max-width: 820px; margin: auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 26px; }
        .head { display: flex; justify-content: space-between; align-items: center; gap: 14px; border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 14px; }
        .head h1 { margin: 0 0 3px; font-size: 20px; font-weight: 800; }
        .muted { color: #555; font-size: 11px; margin: 2px 0; }
        .tag { display: inline-block; background: #111; color: #fff; padding: 3px 11px; border-radius: 6px; font-weight: 700; font-size: 13px; margin-bottom: 5px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 14px; }
        h2 { font-size: 13px; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; }
        th { background: #111; color: #fff; font-weight: 700; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .l { text-align: right; } .n { direction: ltr; unicode-bidi: isolate; }
        .sum td.k { text-align: right; font-weight: 700; background: #f1f5f9; }
        .sum tr.total td { background: #ecfdf5; font-weight: 800; font-size: 13px; border-top: 2px solid #059669; }
        .neg { color: #be123c; }
        .signs { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; text-align: center; color: #475569; margin-top: 34px; padding-top: 14px; border-top: 1px solid #e2e8f0; }
        .signs b { display: block; margin-bottom: 26px; }
        .bar { display: flex; gap: 10px; justify-content: center; margin-bottom: 16px; }
        .btn { background: #2563eb; color: #fff; border: 0; padding: 8px 18px; font: inherit; font-weight: 700; border-radius: 8px; cursor: pointer; text-decoration: none; }
        .btn.back { background: #475569; }
        @media print {
            @page { size: A4 portrait; margin: 10mm 14mm; }
            body { background: #fff; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bar { display: none !important; }
            .page { border: 0; padding: 0; max-width: none; }
        }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
@php
    $u = fn($v) => ($v < 0 ? '-' : '') . '$' . number_format(abs($v), 2);
    $i = fn($v) => ($v < 0 ? '-' : '') . number_format(abs($v)) . ' IQD';
    $own = fn($x, $v) => strtoupper($x->currency ?? 'IQD') === 'USD' ? $u($v) : $i($v);
    $cell = fn($a) => '<td class="n">' . $u($a['USD']) . '</td><td class="n">' . $i($a['IQD']) . '</td>';
    $periodNet = ['USD' => $collectedSalesPeriod['USD'] + $collectedDebt['USD'] - $refundsPeriod['USD'], 'IQD' => $collectedSalesPeriod['IQD'] + $collectedDebt['IQD'] - $refundsPeriod['IQD']];
@endphp
<body>
<div class="bar">
    <button type="button" class="btn back" onclick="closeTab()">داخستن</button>
    <button class="btn" onclick="window.print()">چاپکردن / PDF</button>
</div>

<div class="page">
    <div class="head">
        <div>
            <h1>{{ $setting->shop_name ?? 'کۆمپانیا' }}</h1>
            @if(!empty($setting->shop_phone))<p class="muted n" style="text-align:right">{{ $setting->shop_phone }}</p>@endif
        </div>
        @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
            <img src="{{ asset($setting->shop_logo) }}" style="max-height:64px;max-width:110px;object-fit:contain">
        @endif
        <div style="text-align:left" dir="ltr">
            <div class="tag">کەشفی حساب</div>
            <div class="muted">Printed: {{ date('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div class="meta">
        <div><b>کارمەند:</b> {{ $targetUser->name }}</div>
        <div><b>ماوە:</b> <span class="n">{{ $startDate }} → {{ $endDate }}</span></div>
    </div>

    <h2>پوختەی حساب</h2>
    <table class="sum">
        <thead><tr><th class="l">بابەت</th><th>دۆلار</th><th>دینار</th></tr></thead>
        <tbody>
            <tr><td class="k">وەرگیراو لە فرۆشتن (نەقد + بەشی دراوی قەرز)</td>{!! $cell($collectedSalesPeriod) !!}</tr>
            <tr><td class="k">وەرگرتنەوەی قەرزی کڕیاران</td>{!! $cell($collectedDebt) !!}</tr>
            <tr class="neg"><td class="k">گەڕاوەی نەقد (کەمدەکرێتەوە)</td>{!! $cell($refundsPeriod) !!}</tr>
            <tr><td class="k">کۆی وەرگیراوی ئەم ماوەیە</td>{!! $cell($periodNet) !!}</tr>
            <tr><td class="k">تەسلیمکراو بە سندوق / ئەدمین (ئەم ماوەیە)</td>{!! $cell($handedPeriod) !!}</tr>
            <tr class="total"><td class="k">ماوە لە دەستیدا (هەموو مێژوو)</td>{!! $cell($netCashInHand) !!}</tr>
        </tbody>
    </table>

    <h2>وەسڵەکانی فرۆشتن ({{ $salesList->count() }})</h2>
    <table>
        <thead><tr><th>وەسڵ</th><th>بەروار</th><th>کڕیار</th><th>جۆر</th><th>کۆ</th><th>ماوە (قەرز)</th></tr></thead>
        <tbody>
        @forelse($salesList as $s)
            <tr>
                <td class="n">{{ $s->invoice_no }}</td>
                <td class="n">{{ $s->created_at->format('Y-m-d H:i') }}</td>
                <td class="l">{{ $s->customer->name ?? 'کڕیاری گشتی' }}</td>
                <td>{{ $s->payment_type === 'cash' ? 'نەقد' : 'قەرز' }}</td>
                <td class="n">{{ $own($s, $s->total_amount) }}</td>
                <td class="n">{{ $own($s, $s->remaining_amount) }}</td>
            </tr>
        @empty
            <tr><td colspan="6">هیچ وەسڵێک نییە</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>وەرگرتنەوەی قەرز ({{ $paymentsList->count() }})</h2>
    <table>
        <thead><tr><th>بەروار</th><th>کڕیار</th><th>بڕ</th><th>تێبینی</th></tr></thead>
        <tbody>
        @forelse($paymentsList as $p)
            <tr>
                <td class="n">{{ \Illuminate\Support\Carbon::parse($p->payment_date)->format('Y-m-d') }}</td>
                <td class="l">{{ $p->customer->name ?? '-' }}</td>
                <td class="n">{{ $own($p, $p->amount) }}</td>
                <td class="l">{{ $p->note ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">هیچ پارەدانێک نییە</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>تەسلیمات ({{ $handoversPeriod->count() }})</h2>
    <table>
        <thead><tr><th>ژمارەی پسوولە</th><th>بەروار</th><th>وەرگر</th><th>بڕ</th><th>تێبینی</th></tr></thead>
        <tbody>
        @forelse($handoversPeriod as $h)
            <tr>
                <td class="n">{{ $h->receipt_no }}</td>
                <td class="n">{{ $h->handover_date->format('Y-m-d H:i') }}</td>
                <td class="l">{{ $h->receiver->name ?? 'سندوق' }}</td>
                <td class="n">{{ $own($h, $h->amount) }}</td>
                <td class="l">{{ $h->note ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5">هیچ تەسلیماتێک نییە</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="signs">
        <div><b>واژووی کارمەند</b>......................</div>
        <div><b>واژووی وەرگر / ئەدمین</b>......................</div>
    </div>
</div>
<script>
    // تابە نوێیەکە دادەخات؛ ئەگەر براوزەر ڕێگە نەدا، دەگەڕێتەوە بۆ داشبۆرد
    function closeTab() {
        window.close();
        setTimeout(function () { location.href = @json(route('mandub.dashboard')); }, 300);
    }
</script>
</body>
</html>