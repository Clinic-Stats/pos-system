<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وەسڵی کڕین - {{ $purchase->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Tahoma', 'Arial', sans-serif;
            font-size: 13px;
            color: #111;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 25px;
            border-radius: 8px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 20px; }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: center;
        }
        .table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .weight-box {
            border: 2px dashed #444;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            background-color: #f9fafb;
            margin-top: 15px;
        }
        .totals-table {
            width: 50%;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
        }
        .no-print {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .btn {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-pdf { background: #059669; }
        @media print {
            .no-print { display: none !important; }
            .invoice-box { border: none; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">
            ڕاستەوخۆ چاپکردن
        </button>
        <button class="btn btn-pdf" onclick="window.print()">
            پاشەکەوتکردن وەک PDF (Save as PDF)
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>وەسڵی کڕینی کاڵا</h1>
                <p style="margin: 4px 0 0 0; color: #555;">ژمارەی پسوولە: <b>{{ $purchase->invoice_no }}</b></p>
            </div>
            <div style="text-align: left;" dir="ltr">
                <div><b>Date:</b> {{ $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i') : '' }}</div>
            </div>
        </div>

        <div class="meta-grid">
            <div><b>کۆمپانیا / دابینکەر:</b> {{ $purchase->supplier->name ?? 'گشتی' }}</div>
            <div><b>تەلەفۆن:</b> {{ $purchase->supplier->phone ?? '-' }}</div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ناوی کاڵا</th>
                    <th>یەکە</th>
                    <th>بڕ</th>
                    <th>نرخی کڕین</th>
                    <th>کۆی نرخ</th>
                </tr>
            </thead>
            <tbody>
                @php $totalKg = 0; @endphp
                @foreach($purchase->details as $index => $item)
                @php
                    $uName = mb_strtolower(trim($item->unit->name ?? ''));
                    if (str_contains($uName, 'کارتۆن') || str_contains($uName, 'carton')) {
                        $factor = $item->product->kg_per_carton ?: 1;
                    } elseif (str_contains($uName, 'تەن') || str_contains($uName, 'ton')) {
                        $factor = 1000;
                    } else {
                        $factor = $item->unit->factor_to_base ?: 1;
                    }
                    $totalKg += ($item->quantity * $factor);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $item->product->name ?? 'کاڵا' }}</td>
                    <td>{{ $item->unit->name ?? 'کگ' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td dir="ltr">{{ number_format($item->unit_buy_price) }} IQD</td>
                    <td dir="ltr" style="font-weight: bold;">{{ number_format($item->subtotal) }} IQD</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- کۆی کێشی گشتیی بار -->
        <div class="weight-box">
            کۆی کێشی گشتی بارەکە: <span>{{ number_format($totalKg, 2) }} کیلۆگرام</span>
            @if($totalKg >= 1000)
                <span style="color: #4b5563;"> ({{ number_format($totalKg / 1000, 3) }} تەن)</span>
            @endif
        </div>

        <table class="totals-table">
            <tr>
                <td><b>کۆی گشتی کڕین:</b></td>
                <td dir="ltr" style="text-align: left; font-size: 15px; font-weight: bold;">{{ number_format($purchase->total_amount) }} IQD</td>
            </tr>
        </table>
    </div>

</body>
</html>