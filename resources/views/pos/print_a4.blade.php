<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پسوولەی فرۆشتن A4 - {{ $sale->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&family=Libre+Barcode+39&display=swap" rel="stylesheet">
    @php
    $setting = $setting ?? \App\Models\Setting::first();
    $L = \App\Support\InvoiceLabels::forSetting($setting);   // نووسینەکان لە ڕێکخستنەوە
    $isUsd = ($sale->currency ?? 'IQD') === 'USD';
    $fmt = fn($v) => $isUsd ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' د.ع';
    // ژمارەکانی خشتەکە: ئێستا نیشانەی $ یان د.ع یان لەگەڵە
    $fmtShort = $fmt;

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

    $totalQty = (float) $sale->details->sum('quantity');
    $itemsTotal = (float) $sale->details->sum('line_total');
    $custName = $sale->customer->name ?? ($sale->guest_name ?: 'کڕیاری گشتی');
    $custPhone = $sale->customer->phone ?? $sale->guest_phone;
    $custAddress = $sale->customer->address ?? $sale->guest_address;
    $isDebtSale = $sale->payment_type === 'debt';

    $fmtDebt = function ($arr) {
        $parts = [];
        if ($arr['USD'] > 0) $parts[] = '$' . number_format($arr['USD'], 2);
        if ($arr['IQD'] > 0) $parts[] = number_format($arr['IQD']) . ' د.ع';
        return empty($parts) ? '0' : implode(' + ', $parts);
    };
    @endphp
    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
        }

        .barcode {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 38px;
            line-height: 1;
        }

        :root {
            --inv-blue: #4f81bd;
            --inv-bar: #b8cce4;
            --inv-dark: #366092;
            --inv-title: #243b9b;
        }

        .inv-title { color: var(--inv-title); }

        .inv-dash { border-top: 2px dashed #111; }

        .inv-table { width: 100%; border-collapse: collapse; font-weight: 800; }
        .inv-table th { background: var(--inv-blue); color: #fff; border: 1px solid #fff; padding: 7px 4px; font-size: 12px; font-weight: 800; }
        .inv-table td { border: 1px solid #111; padding: 6px 6px; text-align: center; font-size: 12.5px; }
        .inv-table td.name { font-size: 13px; }
        .inv-table td.frac { color: #2e9e4f; }
        .inv-table tfoot td { border: 0; padding: 8px 6px; font-size: 14px; }

        .inv-sum { width: 100%; border-collapse: separate; border-spacing: 0 6px; font-weight: 800; }
        .inv-sum .lbl { background: var(--inv-bar); text-align: center; padding: 10px 8px; font-size: 14px; }
        .inv-sum .val { width: 32%; background: #fff; border: 1px solid #111; text-align: center; padding: 8px 6px; font-size: 16px; direction: ltr; }
        .inv-sum .val.dark { background: var(--inv-dark); color: #fff; border-color: var(--inv-dark); }
        .inv-sum .val.big { font-size: 17px; }

        /* وەشانی بچووک: دوو جووت لەسەر یەک ڕیز */
        .inv-sum2 { border-spacing: 8px 4px; width: calc(100% + 16px); margin-right: -8px; margin-left: -8px; }
        .inv-sum2 .lbl { width: 22%; padding: 6px 4px; font-size: 11.5px; line-height: 1.35; }
        .inv-sum2 .val { width: 28%; padding: 5px 4px; font-size: 13px; white-space: normal; }
        .inv-sum2 .val.big { font-size: 14px; }

        .num { direction: ltr; unicode-bidi: isolate; white-space: nowrap; }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
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
                max-width: none !important;
            }

            .no-print {
                display: none !important;
            }

            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
        }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen p-6">

    <div class="sheet max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 text-xs">

        <!-- دوگمەکانی چاپ و داخستن -->
        <div class="no-print flex justify-end items-center mb-6 pb-4 border-b border-slate-200">
            <div class="flex gap-2">
                <a href="{{ route('sales.print', $sale->id) }}?type=small" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl font-bold transition">وەسڵی بچووک</a>
                <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">چاپکردن</button>
                <button onclick="window.close()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition">داخستن</button>
            </div>
        </div>

        <!-- ═══════ سەردێڕ ═══════ -->
        <div class="relative text-center pb-4 min-h-[88px] flex flex-col items-center justify-center">
            @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
            <div class="absolute left-0 top-0 w-24 h-24 flex items-center justify-center">
                <img src="{{ asset($setting->shop_logo) }}" class="max-h-24 max-w-full object-contain">
            </div>
            @endif

            <h1 class="inv-title text-[30px] leading-tight font-black px-24">{{ $setting->shop_name ?? 'کۆمپانیای بازرگانی' }}</h1>
            @if(!empty($setting->shop_address))
            <p class="text-slate-800 text-[13px] font-bold mt-1 px-24">{{ $L['shop_address_label'] }} : {{ $setting->shop_address }}</p>
            @endif
            @if(!empty($setting->shop_phone))
            <p class="text-slate-900 text-[13px] font-bold mt-0.5" dir="ltr">{{ $setting->shop_phone }} :{{ $L['shop_phone_label'] }}</p>
            @endif
        </div>

        <div class="inv-dash"></div>

        <!-- ═══════ ناونیشانی پسووڵە و ناوی کڕیار ═══════ -->
        <div class="text-center py-3">
            <div class="text-[26px] font-black text-slate-900">پسووڵەی فرۆشتن</div>
        </div>

        <!-- ═══════ زانیاری وەسڵ ═══════ -->
        <div class="grid grid-cols-3 gap-2 text-[14px] font-extrabold items-center pb-2">
            <div class="text-right">{{ $L['invoice_no'] }}&nbsp;&nbsp;<span class="text-[22px] font-black text-red-600 font-mono">{{ $sale->invoice_no }}</span></div>
            <div class="text-center text-[22px] font-black text-slate-900 leading-tight">کڕیار : {{ $custName }}</div>
            <div class="text-left">{{ $L['cashier'] }} : {{ $sale->user->name ?? 'ئەدمین' }}</div>
        </div>
        <div class="grid grid-cols-3 gap-2 text-[13px] font-extrabold items-center pb-3">
            <div class="text-right">{{ $L['invoice_type'] }} : <span class="{{ $isDebtSale ? 'text-rose-700' : 'text-emerald-700' }}">{{ $isDebtSale ? $L['type_debt'] : $L['type_cash'] }}</span></div>
            <div class="text-center">@if($custPhone){{ $L['customer_phone'] }} : <span class="font-mono" dir="ltr">{{ $custPhone }}</span>@endif</div>
            <div class="text-left font-mono" dir="ltr">{{ $sale->created_at->format('j/n/y, g:i A') }}</div>
        </div>
        @if($custAddress)
        <div class="text-[12.5px] font-bold text-slate-700 pb-3">{{ $L['customer_address'] }} : {{ $custAddress }}</div>
        @endif

        <!-- ═══════ خشتەی کاڵاکان ═══════ -->
        <table class="inv-table">
            <thead>
                <tr>
                    <th style="width:4%">{{ $L['col_no'] }}</th>
                    <th style="width:11%">{{ $L['col_code'] }}</th>
                    <th>{{ $L['col_item'] }}</th>
                    <th style="width:11%">{{ $L['col_unit'] }}</th>
                    <th style="width:10%">{{ $L['col_qty'] }}</th>
                    <th style="width:14%">{{ $L['col_price'] }}</th>
                    <th style="width:15%">{{ $L['col_total'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->details as $index => $item)
                @php
                    $qty = (float) $item->quantity;
                    $price = $item->unit_price ?? ($item->line_total / ($item->quantity ?: 1));
                @endphp
                <tr>
                    <td class="font-mono">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $item->product->code ?? '-' }}</td>
                    <td class="name">{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->unit->name ?? 'دانە' }}</td>
                    <td class="font-mono {{ fmod($qty, 1) != 0 ? 'frac' : '' }}">{{ $qty }}</td>
                    <td class="font-mono num">{{ $fmtShort($price) }}</td>
                    <td class="font-mono num">{{ $fmtShort($item->line_total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right"><div style="display:flex;justify-content:space-between;align-items:center"><span>{{ $L['total_row'] }}</span><span class="font-mono">{{ number_format($totalWeightKg, 2) }} کگم@if($totalWeightKg >= 1000) <span style="font-size:12px">({{ number_format($totalWeightKg / 1000, 2) }} تەن)</span>@endif</span><span style="visibility:hidden">{{ $L['total_row'] }}</span></div></td>
                    <td class="font-mono">{{ $totalQty }}</td>
                    <td></td>
                    <td class="font-mono num">{{ $fmtShort($itemsTotal) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- ═══════ داشکاندن، کۆ، پارەی دراو ═══════ -->
        <table class="inv-sum inv-sum2 mt-1">
            <tr>
                <td class="lbl">{{ $L['discount'] }}</td>
                <td class="val">{{ $fmt($sale->discount ?? 0) }}</td>
                <td class="lbl">{{ $L['after_discount'] }}</td>
                <td class="val big">{{ $fmt($sale->total_amount) }}</td>
            </tr>
            @if($isDebtSale)
            <tr>
                <td class="lbl">{{ $L['paid'] }}</td>
                <td class="val">{{ $fmt($sale->paid_amount) }}</td>
                <td class="lbl" style="color:#b91c1c">{{ $L['remaining'] }}</td>
                <td class="val big" style="color:#b91c1c">{{ $fmt($sale->remaining_amount) }}</td>
            </tr>
            @endif
        </table>

        @if($showDebt)
        <div class="inv-dash my-2"></div>
        <table class="inv-sum">
            <tr>
                <td class="lbl">{{ $L['debt_before'] }}</td>
                <td class="val dark big">{{ $fmtDebt($debtBefore) }}</td>
            </tr>
            <tr>
                <td class="lbl">{{ $L['debt_after'] }}</td>
                <td class="val big" style="color:#b91c1c">{{ $fmtDebt($debtAfter) }}</td>
            </tr>
        </table>
        <div class="text-[11px] font-bold text-slate-600 text-left" dir="ltr">{{ $L['debt_usd_note'] }} ≈ {{ '$' . number_format($debtAfterUsd, 2) }}</div>
        @endif

        <!-- ═══════ QR ی شوێنی گەیاندن ═══════ -->
        @if(!empty($mapUrl))
        <div class="mt-4 pt-3 border-t border-slate-300 flex items-center gap-3">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($mapUrl) }}" alt="QR" class="w-24 h-24 shrink-0 bg-white object-contain">
            <div class="text-[12px] text-slate-800 font-extrabold leading-snug">
                {{ $L['delivery'] }}
                <div class="font-normal text-[11px] text-slate-500 mt-0.5">{{ $L['qr_hint'] }}</div>
            </div>
        </div>
        @endif

        <!-- ═══════ واژوو و بارکۆد ═══════ -->
        <div class="mt-6 pt-4 border-t border-slate-300 grid grid-cols-3 gap-4 text-center text-slate-700">
            <div>
                <span class="block mb-8 font-bold">{{ $L['sign_customer'] }}</span>
                <span>......................</span>
            </div>
            <div class="flex flex-col items-center justify-center">
                @if($setting && $setting->show_barcode)
                <span class="barcode text-slate-800">*{{ $sale->invoice_no }}*</span>
                @endif
                <p class="text-[11px] mt-1 font-bold">{{ $setting->invoice_footer ?? 'سوپاس بۆ سەردانەکەتان' }}</p>
            </div>
            <div>
                <span class="block mb-8 font-bold">{{ $L['sign_accountant'] }}</span>
                <span>......................</span>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 600);
        });
    </script>
</body>

</html>