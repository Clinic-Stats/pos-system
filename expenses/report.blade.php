<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کەشفی حیسابی خەرجییەکان</title>
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
        
        <!-- دوگمەی کۆنتڕۆڵ -->
        <div class="no-print flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl font-bold transition">
                گەڕانەوە بۆ خەرجییەکان
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition">
                دووبارە چاپکردن
            </button>
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
                <div class="inline-block bg-slate-900 text-white px-3 py-1 rounded-lg font-bold text-sm">کەشفی گشتیی خەرجییەکان</div>
                <div class="font-mono text-slate-600">بەرواری چاپ: {{ date('Y-m-d') }}</div>
                @if($fromDate && $toDate)
                    <div class="text-[11px] font-mono text-slate-500">ماوە: {{ $fromDate }} تا {{ $toDate }}</div>
                @endif
            </div>
        </div>

        <!-- خشتەی خەرجییەکان بۆ چاپ -->
        <table class="w-full text-right border border-slate-300 rounded-xl overflow-hidden my-6">
            <thead class="bg-slate-900 text-white text-[11px]">
                <tr>
                    <th class="p-2.5 text-center w-10">#</th>
                    <th class="p-2.5">ناونیشانی خەرجی</th>
                    <th class="p-2.5 text-center">پۆلێن</th>
                    <th class="p-2.5 text-center">کێ پارەکەی داوە</th>
                    <th class="p-2.5 text-center">بەروار</th>
                    <th class="p-2.5 text-center">بڕی پارە (د.ع)</th>
                    <th class="p-2.5">تێبینی</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($expenses as $index => $exp)
                <tr>
                    <td class="p-2.5 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                    <td class="p-2.5 font-bold text-slate-900">{{ $exp->title }}</td>
                    <td class="p-2.5 text-center">{{ $exp->category }}</td>
                    <td class="p-2.5 text-center font-bold text-slate-700">{{ $exp->user->name ?? '-' }}</td>
                    <td class="p-2.5 text-center font-mono">{{ $exp->date->format('Y-m-d') }}</td>
                    <td class="p-2.5 text-center font-mono font-bold text-rose-700" dir="ltr">{{ number_format($exp->amount) }}</td>
                    <td class="p-2.5 text-slate-600">{{ $exp->note ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-slate-500">هیچ خەرجییەک لەم ماوەیەدا نەدۆزرایەوە</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-400">
                <tr>
                    <td colspan="5" class="p-2.5 text-center font-black">کۆی گشتی خەرجییەکان:</td>
                    <td class="p-2.5 text-center font-mono font-black text-rose-800 text-sm" dir="ltr">
                        {{ number_format($totalExpenses) }} د.ع
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- واژوو بۆ چاپ -->
        <div class="pt-8 border-t border-slate-300 grid grid-cols-2 gap-6 text-center text-slate-600 mt-8">
            <div>
                <span class="block mb-10 font-bold">واژووی ژمێریاری / ئامادەکار</span>
                <span>...................................</span>
            </div>
            <div>
                <span class="block mb-10 font-bold">پەسەندکردنی بەڕێوەبەر</span>
                <span>...................................</span>
            </div>
        </div>

    </div>

</body>
</html>