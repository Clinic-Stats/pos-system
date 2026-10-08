<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیستی فرۆشتنەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Almarai', sans-serif;
        }

        .font-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body class="bg-slate-900 text-slate-100 h-screen flex flex-col overflow-hidden p-3 gap-3">

    <!-- سەرپەڕە -->
    <div class="shrink-0 flex flex-wrap justify-between items-center bg-slate-800 p-3 rounded-xl border border-slate-700 gap-2 text-xs">
        <h1 class="text-sm font-bold flex items-center gap-2 text-white">
            <i class="fa-solid fa-receipt text-emerald-400"></i>
            لیستی هەموو وەسڵەکانی فرۆشتن
        </h1>
        <div class="flex flex-wrap items-center gap-1.5 font-bold">
            <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg transition">
                <i class="fa-solid fa-chart-pie"></i> ڕاپۆرتەکان
            </a>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition shadow">
                <i class="fa-solid fa-cash-register"></i> POS
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="shrink-0 bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-2 rounded-lg text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="shrink-0 bg-rose-600/20 border border-rose-500 text-rose-400 p-2 rounded-lg text-xs font-bold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    <!-- فلتەرەکان -->
    <div class="shrink-0 bg-slate-800 p-3 rounded-xl border border-slate-700">
        <form method="GET" action="{{ route('sales.list') }}" class="grid grid-cols-1 md:grid-cols-5 gap-2 text-xs">
            <div>
                <label class="block text-[10px] text-slate-400 font-bold mb-1">لە بەرواری:</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white text-[11px] focus:outline-none">
            </div>
            <div>
                <label class="block text-[10px] text-slate-400 font-bold mb-1">بۆ بەرواری:</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white text-[11px] focus:outline-none">
            </div>
            <div>
                <label class="block text-[10px] text-slate-400 font-bold mb-1">جۆری پارەدان:</label>
                <select name="payment_type" class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white text-[11px] focus:outline-none">
                    <option value="">هەموو</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>نەقد</option>
                    <option value="debt" {{ request('payment_type') == 'debt' ? 'selected' : '' }}>قەرز</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] text-slate-400 font-bold mb-1">گەڕان (ژمارە وەسڵ / کڕیار):</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="INV-xxx یان ناوی کڕیار..." class="w-full p-2 rounded-lg bg-slate-700 border border-slate-600 text-white text-[11px] focus:outline-none">
            </div>
            <div class="flex items-end gap-1.5">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg text-[11px] transition">
                    <i class="fa-solid fa-filter"></i> فلتەرکردن
                </button>
                <a href="{{ route('sales.list') }}" class="bg-slate-600 hover:bg-slate-500 text-white font-bold py-2 px-3 rounded-lg text-[11px] transition">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- خشتەی فرۆشتنەکان -->
    <div class="flex-1 bg-slate-800 p-3 rounded-xl border border-slate-700 flex flex-col overflow-hidden">

        <div class="shrink-0 flex justify-between items-center border-b border-slate-700 pb-2 mb-2">
            <span class="text-xs font-bold text-slate-300">
                کۆی گشتی: <span class="font-num text-emerald-400">{{ $sales->total() }}</span> وەسڵ
            </span>
        </div>

        <div class="flex-1 overflow-auto custom-scrollbar rounded-lg border border-slate-700/80">
            <table class="w-full text-xs text-right text-slate-300">
                <thead class="bg-slate-700/50 text-[11px] text-slate-400 sticky top-0 z-10">
                    <tr>
                        <th class="p-2.5">#</th>
                        <th class="p-2.5">ژمارەی وەسڵ</th>
                        <th class="p-2.5">کڕیار</th>
                        <th class="p-2.5">کۆی پسوولە</th>
                        <th class="p-2.5">کۆی گشتی</th>
                        <th class="p-2.5">بڕی دراو</th>
                        <th class="p-2.5">قازانج</th>
                        <th class="p-2.5">دراو</th>
                        <th class="p-2.5">جۆر</th>
                        <th class="p-2.5">بەروار</th>
                        <th class="p-2.5 text-center">کردارەکان</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($sales as $index => $sale)
                    @php
                        $disc = (float) ($sale->discount ?? 0);
                        $subtotal = $sale->total_amount + $disc;
                        $fmt = fn($v) => $sale->currency == 'USD' ? '$' . number_format((float)$v, 2) : number_format((float)$v) . ' IQD';

                        // ── کۆکردنەوەی بڕ بەپێی یەکە (کگ / کارتۆن / ...) ──
                        $unitTotals = [];
                        foreach ($sale->details as $d) {
                            $uName = $d->unit->name ?? 'دانە';
                            $unitTotals[$uName] = ($unitTotals[$uName] ?? 0) + (float) $d->quantity;
                        }
                        $unitText = collect($unitTotals)
                            ->map(fn($qty, $name) => rtrim(rtrim(number_format($qty, 2), '0'), '.') . ' ' . $name)
                            ->implode(' + ');

                        $typesCount = $sale->details->count();
                    @endphp
                    <tr class="hover:bg-slate-700/30 transition {{ $sale->remaining_amount > 0 ? 'bg-amber-950/20' : '' }}">
                        <td class="p-2.5 font-num text-slate-500">{{ $sales->firstItem() + $index }}</td>
                        <td class="p-2.5 font-mono text-blue-400 text-[11px]">{{ $sale->invoice_no }}</td>
                        <td class="p-2.5 text-[11px]">{{ $sale->customer->name ?? 'کڕیاری نەقد' }}</td>

                     {{-- کۆی پسوولە: نرخی سەرەتا + جۆر و بڕ --}}
<td class="p-2.5 font-mono text-slate-300 text-[11px]">
    <span class="block font-bold text-slate-200" dir="ltr">{{ $fmt($subtotal) }}</span>
    <span class="block text-[9px] text-slate-400 mt-0.5 font-sans whitespace-nowrap">
        <i class="fa-solid fa-cubes"></i>
        {{ $typesCount }} جۆر
        @if($unitText)
        <span class="text-slate-500 mx-0.5">·</span>
        <span class="text-amber-300">{{ $unitText }}</span>
        @endif
    </span>
</td>

                        {{-- کۆی گشتی (دوای داشکاندن) --}}
<td class="p-2.5 font-mono font-bold text-emerald-400 text-[11px]">
    <span class="block" dir="ltr">{{ $fmt($sale->total_amount) }}</span>
    @if($disc > 0)
    <span class="block text-[9px] text-amber-400 mt-0.5 font-sans whitespace-nowrap">
        داشکاندن: -{{ $fmt($disc) }}
    </span>
    @endif
</td>

                        {{-- بڕی دراو --}}
                        <td class="p-2.5 font-mono text-[11px]" dir="ltr">
                            <span class="text-cyan-400 font-bold">{{ $fmt($sale->paid_amount) }}</span>
                            @if($sale->remaining_amount > 0)
                            <span class="block text-[9px] text-rose-400 mt-0.5">ماوە: {{ $fmt($sale->remaining_amount) }}</span>
                            @endif
                        </td>

                        <td class="p-2.5 font-mono font-bold text-blue-400 text-[11px]" dir="ltr">
                            {{ $fmt($sale->total_profit) }}
                        </td>
                        <td class="p-2.5 text-[10px] font-bold text-slate-400">
                            {{ $sale->currency ?? 'IQD' }}
                        </td>
                        <td class="p-2.5">
                            @if($sale->payment_type == 'cash')
                            <span class="bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded text-[10px] font-bold">نەقد</span>
                            @else
                            <span class="bg-amber-500/20 text-amber-400 px-1.5 py-0.5 rounded text-[10px] font-bold">قەرز</span>
                            @endif
                        </td>
                        <td class="p-2.5 text-[10px] text-slate-400 font-mono">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2.5">
                            <div class="flex items-center justify-center gap-1">
                                <!-- چاپی A4 -->
                                <a href="{{ route('sales.print', $sale->id) }}?type=a4" target="_blank" title="چاپی A4"
                                    class="bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-white px-2 py-1 rounded text-[10px] font-bold transition">
                                    <i class="fa-solid fa-file-lines"></i> A4
                                </a>

                                <!-- چاپی بچووک -->
                                <a href="{{ route('sales.print', $sale->id) }}?type=small" target="_blank" title="چاپی بچووک"
                                    class="bg-blue-500/20 hover:bg-blue-500 text-blue-400 hover:text-white px-2 py-1 rounded text-[10px] font-bold transition">
                                    <i class="fa-solid fa-receipt"></i> بچووک
                                </a>

                                <!-- دەستکاری (لەناو POS) -->
                                <a href="{{ route('sales.edit', $sale->id) }}" title="دەستکاریکردن"
                                    class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-2 py-1 rounded text-[10px] font-bold transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <!-- سڕینەوە -->
                                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم وەسڵە؟ کاڵاکان دەگەڕێنەوە کۆگا.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="سڕینەوە"
                                        class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2 py-1 rounded text-[10px] font-bold transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-8 text-center text-slate-500 text-xs">
                            <i class="fa-solid fa-inbox text-2xl block mb-2"></i>
                            هیچ وەسڵێکی فرۆشتن نەدۆزرایەوە
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- پەیجینەیشن -->
        <div class="shrink-0 pt-2 mt-2 border-t border-slate-700">
            {{ $sales->appends(request()->query())->links() }}
        </div>

    </div>

</body>

</html>