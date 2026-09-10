<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وەسڵی گەڕانەوە - {{ $return->return_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Tahoma', 'Arial', sans-serif; font-size: 13px; color: #111; margin: 0; padding: 20px; background: #fff; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #ddd; padding: 25px; border-radius: 8px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #222; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; color: #b45309; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px 10px; text-align: center; }
        .table th { background-color: #f3f4f6; font-weight: bold; }
        .totals-table { width: 50%; margin-right: auto; border-collapse: collapse; margin-top: 10px; }
        .totals-table td { padding: 6px 10px; border-bottom: 1px solid #eee; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; }
        .badge-normal { background: #dcfce7; color: #15803d; }
        .badge-expired { background: #fee2e2; color: #b91c1c; }
        .badge-damaged { background: #fef3c7; color: #b45309; }
        .no-print { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
        .btn { background: #2563eb; color: #fff; border: none; padding: 8px 16px; font-size: 13px; font-weight: bold; border-radius: 6px; cursor: pointer; }
        @media print {
            .no-print { display: none !important; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">ڕاستەوخۆ چاپکردن</button>
        <button class="btn" style="background: #059669;" onclick="window.print()">پاشەکەوتکردن وەک PDF</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>وەسڵی گەڕانەوەی کاڵا</h1>
                <p style="margin: 4px 0 0 0; color: #555;">ژمارەی وەسڵ: <b>{{ $return->return_no }}</b></p>
            </div>
            <div style="text-align: left;" dir="ltr">
                <div><b>Date:</b> {{ $return->created_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <div class="meta-grid">
      
     <div class="meta-grid">
       <div><b>کڕیار:</b> {{ $return->customer->name ?? 'کڕیاری گشتی (نەقد)' }}</div>
       <div><b>شێوازی چارەسەری پارە:</b> {{ $return->refund_type == 'deduct_debt' ? 'داشکاندن لە قەرز' : 'دانەوە بە نەقد' }}</div>
       <div><b>تۆمارکراوە لەلایەن:</b> <span style="font-weight: bold; color: #1d4ed8;">{{ $return->user->name ?? (auth()->user()->name ?? 'نادیار') }}</span></div>
      </div>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ناوی کاڵا</th>
                    <th>یەکە</th>
                    <th>بڕ</th>
                    <th>نرخ</th>
                    <th>جۆری حاڵەت</th>
                    <th>کۆی نرخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $item->product->name ?? 'کاڵا' }}</td>
                    <td>{{ $item->unit->name ?? 'کگ' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td dir="ltr">{{ number_format($item->unit_price) }} IQD</td>
                    <td>
                        @if($item->condition_type == 'normal')
                            <span class="badge badge-normal">ئاسایی (گەڕاوە بۆ کۆگا)</span>
                        @elseif($item->condition_type == 'expired')
                            <span class="badge badge-expired">بەسەرچوو</span>
                        @else
                            <span class="badge badge-damaged">تێکچوو / زیانلێکەوتوو</span>
                        @endif
                    </td>
                    <td dir="ltr" style="font-weight: bold;">{{ number_format($item->subtotal) }} IQD</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td><b>کۆی گشتی پارەی گەڕاوە:</b></td>
                <td dir="ltr" style="text-align: left; font-size: 15px; font-weight: bold; color: #b91c1c;">
                    {{ number_format($return->total_amount) }} IQD
                </td>
            </tr>
        </table>
    </div>

</body>
</html>