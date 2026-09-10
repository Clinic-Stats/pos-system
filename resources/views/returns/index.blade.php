<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیستی وەسڵەکانی گەڕانەوەی فرۆشتن</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-5">

        <!-- سەرپەڕە -->
        <div class="flex flex-wrap justify-between items-center bg-[#1e293b]/90 p-4 rounded-2xl border border-slate-700/70 gap-4">
            <h1 class="text-base font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-rotate-left text-amber-400 text-lg"></i>
                وەسڵەکانی گەڕانەوەی فرۆشتن (Sale Returns)
            </h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    POS
                </a>
                <a href="{{ route('returns.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> وەسڵی نوێی گەڕانەوە
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-950/40 border border-emerald-500/50 text-emerald-400 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- فلتەرکردن -->
        <form action="{{ route('returns.index') }}" method="GET" class="bg-[#1e293b]/80 p-3.5 rounded-2xl border border-slate-700/60 flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="گەڕان بە ژمارەی وەسڵی گەڕانەوە..." class="w-full p-2.5 bg-[#0f172a] border border-slate-700 rounded-xl text-white text-xs placeholder-slate-500 font-mono">
            </div>

            <div class="w-48">
                <select name="customer_id" class="w-full p-2.5 bg-[#0f172a] border border-slate-700 rounded-xl text-white text-xs">
                    <option value="">هەموو کڕیارەکان</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                <span>لە:</span>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="p-2 bg-[#0f172a] border border-slate-700 rounded-xl text-white text-xs font-mono">
                <span>بۆ:</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="p-2 bg-[#0f172a] border border-slate-700 rounded-xl text-white text-xs font-mono">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-filter"></i> فلتەرکردن
            </button>
        </form>

        <!-- خشتە -->
        <div class="bg-[#1e293b]/90 rounded-2xl border border-slate-700/70 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-300">
                    <thead class="bg-slate-800/80 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-700">
                        <tr>
                            <th class="p-3.5">ژمارەی وەسڵ</th>
                            <th class="p-3.5">بەرواری گەڕانەوە</th>
                            <th class="p-3.5">ناوی کڕیار</th>
                            <th class="p-3.5">شێوازی حیسابکردن</th>
                            <th class="p-3.5">وردەکاریی کاڵاکان</th>
                            <th class="p-3.5">کۆی پارەی گەڕاوە</th>
                            <th class="p-3.5">تۆمارکار (مەندووب)</th>
                            <th class="p-3.5">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($returns as $ret)
                        <tr class="hover:bg-slate-800/40 transition">
                            <!-- ١. ژمارەی وەسڵ -->
                            <td class="p-3.5 font-mono font-bold text-amber-400">{{ $ret->return_no }}</td>

                            <!-- ٢. بەروار -->
                            <td class="p-3.5 font-mono text-slate-300">{{ $ret->created_at ? $ret->created_at->format('Y-m-d H:i') : '---' }}</td>

                            <!-- ٣. ناوی کڕیار -->
                            <td class="p-3.5 font-bold text-white">{{ $ret->customer->name ?? 'کڕیاری گشتی' }}</td>

                            <!-- ٤. شێوازی حیسابکردن -->
                            <td class="p-3.5">
                                @if($ret->refund_type == 'deduct_debt')
                                    <span class="bg-blue-950/60 text-blue-400 border border-blue-800 px-2.5 py-1 rounded-md font-bold text-[11px]">داشکاندن لە قەرز</span>
                                @else
                                    <span class="bg-emerald-950/60 text-emerald-400 border border-emerald-800 px-2.5 py-1 rounded-md font-bold text-[11px]">دانەوە بە نەقد</span>
                                @endif
                            </td>

                            <!-- ٥. وردەکاری کاڵاکان -->
                            <td class="p-3.5">
                                <span class="bg-slate-800 border border-slate-700 px-2.5 py-1 rounded-md text-slate-300">
                                    {{ $ret->details->count() }} کاڵا
                                </span>
                            </td>

                            <!-- ٦. کۆی پارە -->
                            <td class="p-3.5 font-mono font-bold text-rose-400 text-sm" dir="ltr">
                                -{{ number_format($ret->total_amount) }} IQD
                            </td>

                            <!-- ٧. تۆمارکار -->
                            <td class="p-3.5 font-bold text-slate-300">
                                <span class="inline-flex items-center gap-1.5 bg-slate-800/70 px-2.5 py-1 rounded-lg border border-slate-700/60">
                                    <i class="fa-solid fa-user text-[11px] text-amber-400"></i>
                                    {{ $ret->user->name ?? 'بەڕێوەبەر' }}
                                </span>
                            </td>

                            <!-- ٨. کردارەکان -->
                            <td class="p-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- پرینت -->
                                    <a href="{{ route('returns.print', $ret->id) }}" target="_blank" class="bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-print"></i> پرینت
                                    </a>
                                    <!-- دەستکاری -->
                                    <a href="{{ route('returns.edit', $ret->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </a>
                                    <!-- سڕینەوە -->
                                    <form action="{{ route('returns.destroy', $ret->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم وەسڵە؟ کاڵاکان و باڵانس دەگەڕێنەوە باری پێشوو.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                            <i class="fa-solid fa-trash"></i> سڕینەوە
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 font-medium">هیچ وەسڵێکی گەڕانەوە تۆمار نەکراوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($returns->hasPages())
                <div class="p-4 border-t border-slate-700/60">
                    {{ $returns->links() }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>