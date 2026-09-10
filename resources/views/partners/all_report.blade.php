<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕاپۆرتی گشتی هاوبەشەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        @media print {
            @page { size: A4 portrait; margin: 10mm 15mm; }
            body { margin: 0; background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-6" onload="window.print()">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 text-xs">
        
        <div class="no-print flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <a href="{{ route('partners.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl font-bold transition">گەڕانەوە</a>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">چاپکردنەوە</button>
        </div>

        <div class="flex justify-between items-center pb-6 border-b-2 border-slate-800 gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">{{ $setting->shop_name ?? 'فرۆشگا / کۆمپانیا' }}</h1>
                <p class="text-slate-600 text-xs">{{ $setting->shop_address ?? '' }}</p>
                <p class="text-slate-700 font-mono text-xs font-bold">{{ $setting->shop_phone ?? '' }}</p>
            </div>
            <div class="text-left space-y-1">
                <div class="inline-block bg-slate-900 text-white px-3 py-1 rounded-lg font-bold text-sm">ڕاپۆرتی سەرمایەی هاوبەشەکان</div>
                <div class="font-mono text-slate-600">بەروار: {{ date('Y-m-d') }}</div>
            </div>
        </div>

        <table class="w-full text-right border border-slate-300 rounded-xl overflow-hidden my-6">
            <thead class="bg-slate-900 text-white text-[11px]">
                <tr>
                    <th class="p-2.5 text-center w-10">#</th>
                    <th class="p-2.5">ناوی هاوبەش</th>
                    <th class="p-2.5 text-center">ڕێژەی پشک</th>
                    <th class="p-2.5 text-center">سەرمایەی سەرەتایی</th>
                    <th class="p-2.5 text-center">کۆی دانراو (+)</th>
                    <th class="p-2.5 text-center">کۆی ڕاکێشراو (-)</th>
                    <th class="p-2.5 text-left">باڵانسی ئێستا</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @php
                    $totalCap = 0;
                    $totalDep = 0;
                    $totalWith = 0;
                    $totalBal = 0;
                @endphp
                @foreach($partners as $index => $p)
                @php
                    $pCap = $p->initial_capital ?? ($p->capital_amount ?? ($p->capital ?? 0));
                    $pShare = $p->share ?? ($p->share_percentage ?? ($p->share_percent ?? 0));
                    $dep = $p->transactions ? $p->transactions->where('type', 'deposit')->sum('amount') : 0;
                    $with = $p->transactions ? $p->transactions->where('type', 'withdraw')->sum('amount') : 0;
                    $bal = $pCap + $dep - $with;

                    $totalCap += $pCap;
                    $totalDep += $dep;
                    $totalWith += $with;
                    $totalBal += $bal;
                @endphp
                <tr>
                    <td class="p-2.5 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                    <td class="p-2.5 font-bold text-slate-900">{{ $p->name }}</td>
                    <td class="p-2.5 text-center font-mono font-bold text-cyan-700">{{ (float) $pShare }}%</td>
                    <td class="p-2.5 text-center font-mono" dir="ltr">{{ number_format($pCap) }}</td>
                    <td class="p-2.5 text-center font-mono text-emerald-700" dir="ltr">{{ number_format($dep) }}</td>
                    <td class="p-2.5 text-center font-mono text-rose-700" dir="ltr">{{ number_format($with) }}</td>
                    <td class="p-2.5 text-left font-mono font-black text-slate-900" dir="ltr">{{ number_format($bal) }} د.ع</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-400">
                <tr>
                    <td colspan="3" class="p-2.5 text-center font-black">کۆی گشتی سەرمایە:</td>
                    <td class="p-2.5 text-center font-mono" dir="ltr">{{ number_format($totalCap) }}</td>
                    <td class="p-2.5 text-center font-mono text-emerald-700" dir="ltr">{{ number_format($totalDep) }}</td>
                    <td class="p-2.5 text-center font-mono text-rose-700" dir="ltr">{{ number_format($totalWith) }}</td>
                    <td class="p-2.5 text-left font-mono font-black text-emerald-800 text-sm" dir="ltr">{{ number_format($totalBal) }} د.ع</td>
                </tr>
            </tfoot>
        </table>

        <div class="pt-8 border-t border-slate-300 text-center text-slate-600 mt-8">
            <span class="block mb-8 font-bold">واژووی کارگێڕی گشتی و پشکنەر</span>
            <span>...................................</span>
        </div>

    </div>

</body>
</html>