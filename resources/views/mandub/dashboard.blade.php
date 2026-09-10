<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی چالاکییەکانی مەندووب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Noto Sans Arabic', sans-serif; } 
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 md:p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- هێدەری سەرەوە -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="text-base font-black text-white">داشبۆردی چالاکییەکان و تەسلیمات</h1>
                    <p class="text-xs text-slate-400">چاودێری فرۆشتن، کاشی دەست و تەسلیمکردنی پارە</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ POS
                </a>
            </div>
        </div>

        <!-- فلتەری مەندووب و بەروار -->
        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
            <form method="GET" action="{{ route('mandub.dashboard') }}" class="flex flex-wrap items-end gap-4 text-xs">
                @if(auth()->user()->isAdmin())
                <div class="w-64">
                    <label class="block font-bold text-slate-300 mb-1">هەڵبژاردنی مەندووب:</label>
                    <select name="user_id" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($mandubs as $m)
                            <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} ({{ $m->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="w-44">
                    <label class="block font-bold text-slate-300 mb-1">لە بەرواری:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div class="w-44">
                    <label class="block font-bold text-slate-300 mb-1">تا بەرواری:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl transition shadow">
                        فلتەرکردن
                    </button>
                </div>
            </form>
        </div>

        <!-- کارتی سەرەکی: کاشی دەستی مەندووب -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="bg-gradient-to-br from-amber-500/20 to-amber-600/10 p-5 rounded-2xl border border-amber-500/30 space-y-1">
                <span class="text-xs text-amber-400 font-bold block">کاشی ماوە لە دەستیدا</span>
                <p class="text-2xl font-black font-mono text-amber-400" dir="ltr">{{ number_format($netCashInHand) }} IQD</p>
                <span class="text-[11px] text-slate-400 block">تەسلیمی سندوق نەکراوە</span>
            </div>

            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-1">
                <span class="text-xs text-slate-400 font-bold block">کۆی فرۆشتنی ماوەکە</span>
                <p class="text-2xl font-black font-mono text-blue-400" dir="ltr">{{ number_format($totalSalesAmount) }} IQD</p>
                <span class="text-[11px] text-slate-400 block">{{ $salesCount }} وەسڵی فرۆشراو</span>
            </div>

            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-1">
                <span class="text-xs text-slate-400 font-bold block">وەرگرتنەوەی قەرز (ماوەکە)</span>
                <p class="text-2xl font-black font-mono text-emerald-400" dir="ltr">{{ number_format($collectedDebt) }} IQD</p>
                <span class="text-[11px] text-slate-400 block">{{ count($paymentsList) }} پارەدان</span>
            </div>

            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-1">
                <span class="text-xs text-slate-400 font-bold block">گەڕاوەکان (ماوەکە)</span>
                <p class="text-2xl font-black font-mono text-rose-400" dir="ltr">{{ number_format($returnsAmount) }} IQD</p>
                <span class="text-[11px] text-slate-400 block">{{ $returnsCount }} وەسڵی گەڕاوە</span>
            </div>

        </div>

        <!-- بەشی وەرگرتنی کاش لە مەندووب و مێژووی وەسڵەکان -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- فۆڕمی وەرگرتنی پارە لە مەندووب -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i> وەرگرتنی کاش لە مەندووب
                    </h3>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold">سندوق</span>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-2.5 rounded-xl text-xs font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('handovers.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="mandub_id" value="{{ $targetUser->id }}">

                    <div>
                        <label class="block font-bold text-slate-300 mb-1">بڕی پارەی وەرگیراو (IQD):</label>
                        <input type="number" step="any" name="amount" max="{{ max(0, $netCashInHand) }}" placeholder="0" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:outline-none focus:border-emerald-500">
                        <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                            <span>کاشی ماوە لە دەستیدا:</span>
                            <span class="font-mono font-bold text-amber-400" dir="ltr">{{ number_format($netCashInHand) }} IQD</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-300 mb-1">تێبینی:</label>
                        <input type="text" name="note" placeholder="نموونە: تەسلیماتی دەستی ڕۆژانە" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <button type="submit" {{ $netCashInHand <= 0 ? 'disabled' : '' }} class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-700 disabled:cursor-not-allowed text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow">
                        <i class="fa-solid fa-circle-check"></i> تۆمارکردن و بڕینی پسوولە
                    </button>
                </form>
            </div>

            <!-- خشتەی مێژووی پسوولەکانی تەسلیمات -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-blue-400"></i> مێژووی وەسڵەکانی تەسلیماتی کاش
                    </h3>
                    <span class="text-xs text-slate-400 font-mono">کۆی تەسلیمکراو: <b class="text-emerald-400 font-bold">{{ number_format($allHandedOver) }} IQD</b></span>
                </div>

                <div class="overflow-x-auto max-h-72 overflow-y-auto pr-1">
                    <table class="w-full text-xs text-right text-slate-300">
                      <thead class="bg-slate-900/60 text-slate-400 sticky top-0">
    <tr>
        <th class="p-2.5">ژمارەی وەسڵ</th>
        <th class="p-2.5">بەروار و کات</th>
        <th class="p-2.5">تەسلیمکار (مەندووب)</th>
        <th class="p-2.5">وەرگر (سندوق)</th>
        <th class="p-2.5">بڕی پارە</th>
        <th class="p-2.5 text-center">کردار</th>
    </tr>
</thead>
                       <tbody class="divide-y divide-slate-700/60">
    @forelse($handovers as $h)
    <tr class="hover:bg-slate-700/30">
        <td class="p-2.5 font-mono font-bold text-blue-400">{{ $h->receipt_no }}</td>
        <td class="p-2.5 font-mono text-slate-400">{{ $h->handover_date->format('Y-m-d h:i A') }}</td>
        <td class="p-2.5 text-amber-400 font-bold">
            <i class="fa-solid fa-user-tag text-[10px] ml-1"></i>{{ $h->mandub->name ?? 'مەندووب' }}
        </td>
        <td class="p-2.5 text-white font-bold">
            <i class="fa-solid fa-cash-register text-[10px] ml-1 text-emerald-400"></i>{{ $h->receiver->name ?? 'سندوق' }}
        </td>
        <td class="p-2.5 font-mono font-bold text-emerald-400" dir="ltr">{{ number_format($h->amount) }} IQD</td>
        <td class="p-2.5 text-center">
            <a href="{{ route('handovers.print', $h->id) }}" target="_blank" class="bg-blue-600/80 hover:bg-blue-600 text-white px-2.5 py-1 rounded-lg text-[11px] font-bold inline-flex items-center gap-1 transition">
                <i class="fa-solid fa-print"></i> چاپ
            </a>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="p-4 text-center text-slate-500">هیچ وەسڵێکی تەسلیمات تۆمار نەکراوە</td>
    </tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</body>
</html>