<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیستی وەسڵەکانی کڕین</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Noto Sans Arabic', sans-serif;
        }
    </style>
    @php
    // نیشاندانی بڕ بەپێی دراوی هەر وەسڵێک
    $fmt = fn($v, $cur) => ($cur ?? 'IQD') === 'USD'
    ? '$' . number_format((float) $v, 2)
    : number_format((float) $v) . ' IQD';
    @endphp
    @include('partials.system-head')
</head>

<body class="bg-[#0f172a] text-slate-100 min-h-screen p-2 sm:p-4">

    <div class="max-w-7xl mx-auto space-y-3 sm:space-y-4">

        <!-- سەرپەڕە -->
        <header class="flex flex-wrap items-center justify-between gap-2 bg-slate-800 px-3 sm:px-4 py-2.5 rounded-xl border border-slate-700">
            <h1 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-cart-flatbed text-blue-400"></i>
                وەسڵەکانی کڕین
            </h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-lg transition">
                    POS
                </a>
                <a href="{{ route('purchases.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> کڕینی نوێ
                </a>
            </div>
        </header>

        @if(session('success'))
        <div class="bg-emerald-950/40 border border-emerald-500/50 text-emerald-400 p-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-rose-950/40 border border-rose-500/50 text-rose-400 p-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- فلتەرکردن -->
        <form action="{{ route('purchases.index') }}" method="GET" class="bg-slate-800/90 p-3 rounded-xl border border-slate-700/70 grid grid-cols-2 md:flex md:flex-wrap md:items-center gap-2 md:gap-3">
            <div class="col-span-2 md:flex-1 md:min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="گەڕان بە ژمارەی وەسڵی کڕین..." class="w-full p-2.5 md:p-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs font-mono">
            </div>

            <div class="col-span-2 md:w-48">
                <select name="supplier_id" class="w-full p-2.5 md:p-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs">
                    <option value="">هەموو شوێنەکانی کڕین</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="flex flex-col md:flex-row md:items-center gap-1 md:gap-1.5 text-xs text-slate-400 min-w-0">
                <span>لە:</span>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full min-w-0 p-2 md:p-1.5 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs font-mono">
            </label>
            <label class="flex flex-col md:flex-row md:items-center gap-1 md:gap-1.5 text-xs text-slate-400 min-w-0">
                <span>بۆ:</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full min-w-0 p-2 md:p-1.5 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs font-mono">
            </label>

            <button type="submit" class="col-span-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 md:py-2 rounded-lg transition flex items-center justify-center gap-1">
                <i class="fa-solid fa-filter"></i> فلتەر
            </button>
        </form>

        <!-- مۆبایل: کارت -->
        <div class="md:hidden space-y-2.5">
            @forelse($purchases as $p)
            <div class="bg-slate-800/90 rounded-xl border border-slate-700/70 p-3 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="font-mono font-bold text-blue-400 text-sm" dir="ltr">{{ $p->purchase_no ?? $p->invoice_no }}</div>
                        <div class="font-bold text-white text-sm mt-0.5 truncate">{{ $p->supplier->name ?? 'دیارینەکراو' }}</div>
                    </div>
                    <div class="font-mono text-[11px] text-slate-400 shrink-0" dir="ltr">{{ $p->created_at ? $p->created_at->format('Y-m-d H:i') : $p->purchase_date }}</div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-slate-900/60 rounded-lg p-2">
                        <div class="text-[10px] text-slate-400 mb-1">کۆی گشتی</div>
                        <div class="font-mono font-bold text-emerald-400 text-[12px]" dir="ltr">{{ $fmt($p->total_amount, $p->currency) }}</div>
                    </div>
                    <div class="bg-slate-900/60 rounded-lg p-2">
                        <div class="text-[10px] text-slate-400 mb-1">پارەی دراو</div>
                        <div class="font-mono text-slate-200 text-[12px]" dir="ltr">{{ $fmt($p->paid_amount, $p->currency) }}</div>
                    </div>
                    <div class="bg-slate-900/60 rounded-lg p-2">
                        <div class="text-[10px] text-slate-400 mb-1">ماوە (قەرز)</div>
                        <div class="font-mono font-bold text-[12px] {{ $p->remaining_amount > 0 ? 'text-rose-400' : 'text-slate-400' }}" dir="ltr">{{ $fmt($p->remaining_amount, $p->currency) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('purchases.print', $p->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-[11px] font-bold text-center">
                        <i class="fa-solid fa-file-lines"></i> چاپ
                    </a>
                    <a href="{{ route('purchases.edit', $p->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg text-[11px] font-bold text-center">
                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                    </a>
                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوە؟ بڕی کاڵاکان لە کۆگا کەم دەکرێتەوە.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-lg text-[11px] font-bold">
                            <i class="fa-solid fa-trash"></i> سڕینەوە
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-slate-800/90 rounded-xl border border-slate-700/70 p-6 text-center text-slate-500 font-medium text-xs">هیچ وەسڵێکی کڕین تۆمار نەکراوە</div>
            @endforelse
        </div>

        <!-- کۆمپیوتەر: خشتە -->
        <div class="hidden md:block bg-slate-800/90 rounded-xl border border-slate-700/70 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-300">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase text-[11px] border-b border-slate-700">
                        <tr>
                            <th class="p-3">ژمارەی وەسڵ</th>
                            <th class="p-3">بەروار</th>
                            <th class="p-3">شوێنی کڕین (کۆمپانیا)</th>
                            <th class="p-3">کۆی گشتی</th>
                            <th class="p-3">پارەی دراو</th>
                            <th class="p-3">ماوە (قەرز)</th>
                            <th class="p-3">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($purchases as $p)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-3 font-mono font-bold text-blue-400">{{ $p->purchase_no ?? $p->invoice_no }}</td>
                            <td class="p-3 font-mono text-slate-300">{{ $p->created_at ? $p->created_at->format('Y-m-d H:i') : $p->purchase_date }}</td>
                            <td class="p-3 font-bold text-white">{{ $p->supplier->name ?? 'دیارینەکراو' }}</td>
                            <td class="p-3 font-mono font-bold text-emerald-400" dir="ltr">{{ $fmt($p->total_amount, $p->currency) }}</td>
                            <td class="p-3 font-mono text-slate-300" dir="ltr">{{ $fmt($p->paid_amount, $p->currency) }}</td>
                            <td class="p-3 font-mono font-bold {{ $p->remaining_amount > 0 ? 'text-rose-400' : 'text-slate-400' }}" dir="ltr">
                                {{ $fmt($p->remaining_amount, $p->currency) }}
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('purchases.print', $p->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-file-lines"></i> چاپی A4
                                    </a>
                                    <a href="{{ route('purchases.edit', $p->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </a>
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوە؟ بڕی کاڵاکان لە کۆگا کەم دەکرێتەوە.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded-md text-[11px] font-bold">
                                            <i class="fa-solid fa-trash"></i> سڕینەوە
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-medium">هیچ وەسڵێکی کڕین تۆمار نەکراوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($purchases->hasPages())
        <div class="bg-slate-800/90 rounded-xl border border-slate-700/70 p-3">
            {{ $purchases->links() }}
        </div>
        @endif

    </div>

</body>

</html>