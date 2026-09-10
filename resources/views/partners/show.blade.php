<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کەشفی حیسابی هاوبەش - {{ $partner->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        @media print {
            @page { size: A4 portrait; margin: 10mm 15mm; }
            body { margin: 0; background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-6">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 text-xs">
        
        <!-- کۆنتڕۆڵی وێب -->
        <div class="no-print flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <a href="{{ route('partners.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ لیستی هاوبەشەکان
            </a>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow">
                    <i class="fa-solid fa-print"></i> چاپی کەشف (A4)
                </button>
            </div>
        </div>

        <!-- هێدەری فەرمی A4 -->
        <div class="flex justify-between items-center pb-6 border-b-2 border-slate-800 gap-4">
            <div class="space-y-1">
                <h1 class="text-2xl font-black text-slate-900">{{ $setting->shop_name ?? 'فرۆشگا / کۆمپانیا' }}</h1>
                <p class="text-slate-600 text-xs">{{ $setting->shop_address ?? '' }}</p>
                <p class="text-slate-700 font-mono text-xs font-bold">{{ $setting->shop_phone ?? '' }}</p>
            </div>

            @if(!empty($setting->shop_logo) && file_exists(public_path($setting->shop_logo)))
                <img src="{{ asset($setting->shop_logo) }}" class="max-h-20 max-w-[120px] object-contain">
            @endif

            <div class="text-left space-y-1">
                <div class="inline-block bg-slate-900 text-white px-3 py-1 rounded-lg font-bold text-sm">کەشفی حیسابی هاوبەش</div>
                <div class="font-mono text-slate-600">بەروار: {{ date('Y-m-d') }}</div>
            </div>
        </div>

        @php
            $initialCap = $partner->initial_capital ?? ($partner->capital_amount ?? ($partner->capital ?? 0));
            $shareVal   = $partner->share ?? ($partner->share_percentage ?? ($partner->share_percent ?? 0));
            $depositsTotal = $partner->transactions ? $partner->transactions->where('type', 'deposit')->sum('amount') : 0;
            $withdrawsTotal = $partner->transactions ? $partner->transactions->where('type', 'withdraw')->sum('amount') : 0;
            $curBal = $initialCap + $depositsTotal - $withdrawsTotal;
        @endphp

        <!-- کارتی کورتەی هاوبەش -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 my-6 p-4 bg-slate-50 border border-slate-200 rounded-xl text-center">
            <div>
                <span class="block text-slate-500 text-[11px] font-bold">ناوی هاوبەش:</span>
                <span class="text-sm font-black text-slate-900">{{ $partner->name }}</span>
            </div>
            <div>
                <span class="block text-slate-500 text-[11px] font-bold">ڕێژەی پشک:</span>
                <span class="text-sm font-black font-mono text-cyan-700">{{ (float) $shareVal }}%</span>
            </div>
            <div>
                <span class="block text-slate-500 text-[11px] font-bold">سەرمایەی سەرەتایی:</span>
                <span class="text-sm font-black font-mono text-slate-800" dir="ltr">{{ number_format($initialCap) }} د.ع</span>
            </div>
            <div>
                <span class="block text-slate-500 text-[11px] font-bold">باڵانسی ئێستا:</span>
                <span class="text-sm font-black font-mono text-emerald-700" dir="ltr">{{ number_format($curBal) }} د.ع</span>
            </div>
        </div>

        <!-- خشتەی جووڵەی پارە بە بەروار و تێبینی -->
        <table class="w-full text-right border border-slate-300 rounded-xl overflow-hidden mb-6">
            <thead class="bg-slate-900 text-white text-[11px]">
                <tr>
                    <th class="p-2.5 text-center w-12">#</th>
                    <th class="p-2.5">بەروار</th>
                    <th class="p-2.5 text-center">جۆری جووڵە</th>
                    <th class="p-2.5 text-center">بڕی دانان (+)</th>
                    <th class="p-2.5 text-center">بڕی ڕاکێشان (-)</th>
                    <th class="p-2.5 text-center">باڵانسی خولاو</th>
                    <th class="p-2.5">تێبینی / هۆکار</th>
                    <th class="p-2.5 text-center no-print">کردار</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <!-- دێڕی یەکەم: سەرمایەی سەرەتایی -->
                <tr class="bg-slate-50/80 font-bold">
                    <td class="p-2.5 text-center font-mono">1</td>
                    <td class="p-2.5 font-mono">{{ $partner->created_at ? $partner->created_at->format('Y-m-d') : '-' }}</td>
                    <td class="p-2.5 text-center text-slate-700">سەرمایەی سەرەتایی</td>
                    <td class="p-2.5 text-center font-mono text-emerald-700" dir="ltr">{{ number_format($initialCap) }}</td>
                    <td class="p-2.5 text-center font-mono text-slate-400">-</td>
                    <td class="p-2.5 text-center font-mono text-slate-900 font-black" dir="ltr">{{ number_format($initialCap) }}</td>
                    <td class="p-2.5 text-slate-500">دەستپێکی پشک</td>
                    <td class="p-2.5 text-center no-print">-</td>
                </tr>

                @php
                    $runningBalance = $initialCap;
                    $rowNum = 2;
                @endphp

                @foreach($partner->transactions as $trx)
                    @php
                        if ($trx->type === 'deposit') {
                            $runningBalance += $trx->amount;
                        } else {
                            $runningBalance -= $trx->amount;
                        }
                        $trxDate = $trx->date ?? ($trx->created_at ? $trx->created_at->format('Y-m-d') : '-');
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 text-center font-mono text-slate-500">{{ $rowNum++ }}</td>
                        <td class="p-2.5 font-mono font-bold">{{ $trxDate }}</td>
                        <td class="p-2.5 text-center">
                            <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $trx->type === 'deposit' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $trx->type === 'deposit' ? 'دانان' : 'ڕاکێشان' }}
                            </span>
                        </td>
                        <td class="p-2.5 text-center font-mono font-bold text-emerald-700" dir="ltr">
                            {{ $trx->type === 'deposit' ? number_format($trx->amount) : '-' }}
                        </td>
                        <td class="p-2.5 text-center font-mono font-bold text-rose-700" dir="ltr">
                            {{ $trx->type === 'withdraw' ? number_format($trx->amount) : '-' }}
                        </td>
                        <td class="p-2.5 text-center font-mono font-black text-slate-900" dir="ltr">
                            {{ number_format($runningBalance) }}
                        </td>
                        <td class="p-2.5 text-slate-600">{{ $trx->note ?? '-' }}</td>
                        <td class="p-2.5 text-center no-print">
                            <form action="{{ route('partners.transaction.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم جووڵەیە؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs p-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- واژوو بۆ چاپ -->
        <div class="pt-8 border-t border-slate-300 grid grid-cols-2 gap-6 text-center text-slate-600 mt-6">
            <div>
                <span class="block mb-10 font-bold">واژووی بەڕێوەبەر / ژمێریار</span>
                <span>...................................</span>
            </div>
            <div>
                <span class="block mb-10 font-bold">واژووی هاوبەش ({{ $partner->name }})</span>
                <span>...................................</span>
            </div>
        </div>

    </div>

</body>
</html>