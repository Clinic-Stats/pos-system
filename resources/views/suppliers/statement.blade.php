<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کەشفی حساب - {{ $supplier->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Noto Sans Arabic', Tahoma, sans-serif; font-size: 12px; color: #111; margin: 0; padding: 20px; background: #f1f5f9; }
        .page { max-width: 840px; margin: auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 26px; }
        .bar { max-width: 840px; margin: 0 auto 14px; display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
        .btn { background: #2563eb; color: #fff; border: 0; padding: 8px 16px; font: inherit; font-weight: 700; border-radius: 8px; cursor: pointer; text-decoration: none; }
        .btn.gray { background: #475569; } .btn.green { background: #059669; }
        .card { max-width: 840px; margin: 0 auto 14px; background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 14px 18px; }
        .card h3 { margin: 0 0 10px; font-size: 13px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; align-items: end; }
        label { display: block; font-weight: 700; margin-bottom: 4px; font-size: 11px; color: #334155; }
        input, select { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
        .err { background: #fee2e2; color: #991b1b; padding: 8px 12px; border-radius: 8px; margin: 0 auto 12px; max-width: 840px; font-weight: 700; }
        .ok { background: #d1fae5; color: #065f46; padding: 8px 12px; border-radius: 8px; margin: 0 auto 12px; max-width: 840px; font-weight: 700; }
        .head { display: flex; justify-content: space-between; align-items: center; gap: 14px; border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 14px; }
        .head h1 { margin: 0 0 3px; font-size: 20px; font-weight: 800; }
        .muted { color: #555; font-size: 11px; margin: 2px 0; }
        .tag { display: inline-block; background: #111; color: #fff; padding: 3px 11px; border-radius: 6px; font-weight: 700; font-size: 13px; margin-bottom: 5px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 10px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 14px; }
        h2 { font-size: 13px; margin: 16px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; }
        th { background: #111; color: #fff; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .l { text-align: right; } .n { direction: ltr; unicode-bidi: isolate; }
        .open td, .close td { background: #f1f5f9; font-weight: 800; }
        .close td { background: #ecfdf5; border-top: 2px solid #059669; }
        .owe { color: #b45309; font-weight: 800; } .clean { color: #047857; font-weight: 800; }
        .x { color: #be123c; background: none; border: 0; cursor: pointer; font-size: 11px; }
        .signs { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; text-align: center; color: #475569; margin-top: 34px; padding-top: 14px; border-top: 1px solid #e2e8f0; }
        .signs b { display: block; margin-bottom: 26px; }
        @media print {
            @page { size: A4 portrait; margin: 10mm 14mm; }
            body { background: #fff; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .noprint, .x { display: none !important; }
            .page { border: 0; padding: 0; max-width: none; }
        }
    </style>
    @include('partials.system-head')
</head>
@php
    $fmt = fn($v, $c) => ($v < 0 ? '-' : '') . ($c === 'USD' ? '$' . number_format(abs($v), 2) : number_format(abs($v)) . ' IQD');
    $names = ['USD' => 'دۆلار ($)', 'IQD' => 'دینار (IQD)'];
@endphp
<body>
<div class="bar noprint">
    <a href="{{ route('suppliers.index') }}" class="btn gray">گەڕانەوە بۆ دابینکەران</a>
    <button class="btn green" onclick="window.print()">چاپکردن / PDF</button>
</div>

@if(session('success'))<div class="ok noprint">{{ session('success') }}</div>@endif
@if($errors->any())<div class="err noprint">{{ $errors->first() }}</div>@endif

<!-- فلتەر و پارەدان -->
<div class="card noprint">
    <h3>ماوەی کەشف</h3>
    <form method="GET" class="grid">
        <div><label>لە بەرواری</label><input type="date" name="from_date" value="{{ request('from_date') }}"></div>
        <div><label>تا بەرواری</label><input type="date" name="to_date" value="{{ request('to_date') }}"></div>
        <div><button class="btn" style="width:100%">فلتەرکردن</button></div>
    </form>
</div>

<div class="card noprint">
    <h3>پارەدان بە {{ $supplier->name }}</h3>
    <form action="{{ route('suppliers.payments.store', $supplier->id) }}" method="POST" class="grid">
        @csrf
        <div><label>دراو</label>
            <select name="currency">
                <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>دۆلار (ماوە: {{ $fmt(max(0, $balances['USD']), 'USD') }})</option>
                <option value="IQD" {{ old('currency') === 'IQD' ? 'selected' : '' }}>دینار (ماوە: {{ $fmt(max(0, $balances['IQD']), 'IQD') }})</option>
            </select></div>
        <div><label>بڕی پارە</label><input type="number" step="any" min="0.01" name="amount" value="{{ old('amount') }}" required></div>
        <div><label>بەروار</label><input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required></div>
        <div><label>تێبینی</label><input type="text" name="note" value="{{ old('note') }}" placeholder="نموونە: بە نەقد"></div>
        <div><button class="btn green" style="width:100%">تۆمارکردنی پارەدان</button></div>
    </form>
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
            <div class="tag">کەشفی حسابی دابینکەر</div>
            <div class="muted">Printed: {{ date('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div class="meta">
        <div><b>دابینکەر:</b> {{ $supplier->name }}</div>
        <div><b>مۆبایل:</b> <span class="n">{{ $supplier->phone ?? '-' }}</span></div>
        <div><b>ناونیشان:</b> {{ $supplier->address ?? '-' }}</div>
        <div><b>ماوە:</b> <span class="n">{{ $from ? $from->format('Y-m-d') : 'سەرەتا' }} → {{ $to ? $to->format('Y-m-d') : 'ئێستا' }}</span></div>
    </div>

    <h2>پوختە (قەرزی ئێمە بە دابینکەر)</h2>
    <table>
        <thead><tr><th class="l">دراو</th><th>کۆی کڕین</th><th>کۆی دراو</th><th>ماوە (ئەم ماوەیە)</th><th>ماوەی ئێستا (هەموو مێژوو)</th></tr></thead>
        <tbody>
        @foreach(['USD', 'IQD'] as $c)
            @php $L = $ledgers[$c]; $cur = $balances[$c]; @endphp
            <tr>
                <td class="l"><b>{{ $names[$c] }}</b></td>
                <td class="n">{{ $fmt($L['debit'], $c) }}</td>
                <td class="n">{{ $fmt($L['credit'], $c) }}</td>
                <td class="n">{{ $fmt($L['closing'], $c) }}</td>
                <td class="n {{ $cur > 0.004 ? 'owe' : 'clean' }}">{{ $cur > 0.004 ? $fmt($cur, $c) : 'پاکە' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @foreach(['USD', 'IQD'] as $c)
        @php $L = $ledgers[$c]; @endphp
        @if(count($L['rows']) || abs($L['opening']) > 0.004)
        <h2>جووڵەکانی {{ $names[$c] }}</h2>
        <table>
            <thead><tr><th>بەروار</th><th>جۆر</th><th>ژمارەی وەسڵ</th><th>تێبینی</th><th>کڕین</th><th>دراو</th><th>ماوە</th><th class="noprint"></th></tr></thead>
            <tbody>
                @if($from)
                <tr class="open"><td colspan="6" class="l">ماوەی پێشوو (سەرەتای ماوە)</td><td class="n">{{ $fmt($L['opening'], $c) }}</td><td class="noprint"></td></tr>
                @endif
                @foreach($L['rows'] as $r)
                <tr>
                    <td class="n">{{ $r['date']->format('Y-m-d') }}</td>
                    <td>{{ $r['type'] === 'purchase' ? 'وەسڵی کڕین' : 'پارەدان' }}</td>
                    <td class="n">
                        @if($r['purchase_id'])<a href="{{ route('purchases.print', $r['purchase_id']) }}" target="_blank" style="color:#2563eb;text-decoration:none">{{ $r['ref'] }}</a>@else {{ $r['ref'] }} @endif
                    </td>
                    <td class="l">{{ $r['note'] }}</td>
                    <td class="n">{{ $r['debit'] > 0 ? $fmt($r['debit'], $c) : '-' }}</td>
                    <td class="n">{{ $r['credit'] > 0 ? $fmt($r['credit'], $c) : '-' }}</td>
                    <td class="n"><b>{{ $fmt($r['balance'], $c) }}</b></td>
                    <td class="noprint">
                        @if($r['payment_id'])
                        <form action="{{ route('suppliers.payments.destroy', $r['payment_id']) }}" method="POST" onsubmit="return confirm('ئەم پارەدانە دەسڕدرێتەوە؟')">@csrf @method('DELETE')<button class="x">✕</button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="close"><td colspan="4" class="l">کۆی ماوەکە / ماوەی کۆتایی</td><td class="n">{{ $fmt($L['debit'], $c) }}</td><td class="n">{{ $fmt($L['credit'], $c) }}</td><td class="n">{{ $fmt($L['closing'], $c) }}</td><td class="noprint"></td></tr>
            </tbody>
        </table>
        @endif
    @endforeach

    @if(!count($ledgers['USD']['rows']) && !count($ledgers['IQD']['rows']))
        <p style="text-align:center;color:#64748b;padding:16px">هیچ جووڵەیەک لەم ماوەیەدا نییە</p>
    @endif

    <div class="signs">
        <div><b>واژووی ژمێریار</b>......................</div>
        <div><b>واژووی دابینکەر</b>......................</div>
    </div>
</div>
</body>
</html>