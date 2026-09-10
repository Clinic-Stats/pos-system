<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ئەنجامی گەڕان بۆ: {{ $q }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 md:p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- سەرپەڕە و فۆڕمی گەڕان -->
        <div class="flex flex-col md:flex-row justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700 gap-4">
            <div>
                <h1 class="text-base font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-blue-400"></i>
                    ئەنجامەکانی گەڕان لە داتابەیس بۆ: <span class="text-amber-400 font-mono">"{{ $q }}"</span>
                </h1>
            </div>

            <!-- خانەی گەڕانی خێرا -->
            <form action="{{ route('global.search') }}" method="GET" class="flex items-center gap-2 w-full md:w-96">
                <input type="text" name="query" value="{{ $q }}" placeholder="گەڕان بە ناو، ژمارەی وەسڵ، کۆد..." required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">گەڕان</button>
            </form>

            <a href="{{ route('pos.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">گەڕانەوە بۆ POS</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- وەسڵەکانی فرۆشتن -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i> وەسڵەکانی فرۆشتن ({{ $sales->count() }})
                </h2>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-900/60 text-slate-400 sticky top-0">
                            <tr>
                                <th class="p-2">وەسڵ</th>
                                <th class="p-2">کڕیار</th>
                                <th class="p-2">بڕ</th>
                                <th class="p-2">مەندووب</th>
                                <th class="p-2 text-center">چاپ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($sales as $s)
                            <tr>
                                <td class="p-2 font-mono text-blue-400">{{ $s->invoice_no }}</td>
                                <td class="p-2 text-white font-bold">{{ $s->customer->name ?? 'کڕیاری گشتی' }}</td>
                                <td class="p-2 font-mono text-emerald-400" dir="ltr">{{ number_format($s->total_amount) }} IQD</td>
                                <td class="p-2 text-slate-400">{{ $s->user->name ?? '—' }}</td>
                                <td class="p-2 text-center">
                                    <a href="{{ route('sales.print', $s->id) }}" target="_blank" class="text-blue-400 hover:text-blue-300"><i class="fa-solid fa-print"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-3 text-center text-slate-500">هیچ وەسڵێک نەدۆزرایەوە</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- وەسڵەکانی تەسلیماتی کاش -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i> تەسلیماتی کاش ({{ $handovers->count() }})
                </h2>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-900/60 text-slate-400 sticky top-0">
                            <tr>
                                <th class="p-2">ژمارەی وەسڵ</th>
                                <th class="p-2">مەندووب</th>
                                <th class="p-2">وەرگر</th>
                                <th class="p-2">بڕ</th>
                                <th class="p-2 text-center">چاپ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($handovers as $h)
                            <tr>
                                <td class="p-2 font-mono text-amber-400">{{ $h->receipt_no }}</td>
                                <td class="p-2 text-white font-bold">{{ $h->mandub->name ?? '—' }}</td>
                                <td class="p-2 text-slate-400">{{ $h->receiver->name ?? '—' }}</td>
                                <td class="p-2 font-mono text-emerald-400" dir="ltr">{{ number_format($h->amount) }} IQD</td>
                                <td class="p-2 text-center">
                                    <a href="{{ route('handovers.print', $h->id) }}" target="_blank" class="text-blue-400 hover:text-blue-300"><i class="fa-solid fa-print"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-3 text-center text-slate-500">هیچ تەسلیماتێک نەدۆزرایەوە</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- کڕیاران -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <i class="fa-solid fa-users text-cyan-400"></i> کڕیاران ({{ $customers->count() }})
                </h2>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-900/60 text-slate-400 sticky top-0">
                            <tr>
                                <th class="p-2">ناو</th>
                                <th class="p-2">مۆبایل</th>
                                <th class="p-2">قەرز</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($customers as $c)
                            <tr>
                                <td class="p-2 font-bold text-white">{{ $c->name }}</td>
                                <td class="p-2 font-mono text-cyan-400">{{ $c->phone ?? '—' }}</td>
                                <td class="p-2 font-mono text-rose-400" dir="ltr">{{ number_format($c->total_debt ?? 0) }} IQD</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-3 text-center text-slate-500">هیچ کڕیارێک نەدۆزرایەوە</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- کاڵاکان و کۆگا -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-2">
                    <i class="fa-solid fa-boxes-stacked text-purple-400"></i> کاڵاکان ({{ $products->count() }})
                </h2>
                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-900/60 text-slate-400 sticky top-0">
                            <tr>
                                <th class="p-2">ناوی کاڵا</th>
                                <th class="p-2">بارکۆد</th>
                                <th class="p-2">مەخزەن</th>
                                <th class="p-2">نرخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($products as $p)
                            <tr>
                                <td class="p-2 font-bold text-white">{{ $p->name }}</td>
                                <td class="p-2 font-mono text-slate-400">{{ $p->barcode ?? '—' }}</td>
                                <td class="p-2 font-mono text-amber-400">{{ $p->stock }}</td>
                                <td class="p-2 font-mono text-emerald-400" dir="ltr">{{ number_format($p->sale_price) }} IQD</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-3 text-center text-slate-500">هیچ کاڵایەک نەدۆزرایەوە</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</body>
</html>