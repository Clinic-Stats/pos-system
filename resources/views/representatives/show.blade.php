<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ڕاپۆرتی فرۆشتنی مەندووب - {{ $rep->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-[#1e293b]/90 p-4 rounded-2xl border border-slate-700/70">
            <div>
                <h1 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-400"></i>
                    کەشفی فرۆشتن و کۆمسیۆنی: {{ $rep->name }}
                </h1>
                <div class="text-xs text-slate-400 mt-1">مۆبایل: {{ $rep->phone ?? '-' }} | ناوچە: {{ $rep->area ?? '-' }}</div>
            </div>
            <a href="{{ route('representatives.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                گەڕانەوە
            </a>
        </div>

        <!-- کارتەکانی ئامار -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-[#1e293b]/90 p-5 rounded-2xl border border-slate-700/70 text-center">
                <span class="text-xs text-slate-400 block mb-1">کۆی فرۆشراو بەم مەندووبە</span>
                <span class="text-xl font-bold font-mono text-emerald-400" dir="ltr">{{ number_format($totalSales) }} IQD</span>
            </div>
            <div class="bg-[#1e293b]/90 p-5 rounded-2xl border border-slate-700/70 text-center">
                <span class="text-xs text-slate-400 block mb-1">ڕێژەی کۆمسیۆن</span>
                <span class="text-xl font-bold font-mono text-amber-400">٪{{ $rep->commission_rate }}</span>
            </div>
            <div class="bg-[#1e293b]/90 p-5 rounded-2xl border border-slate-700/70 text-center">
                <span class="text-xs text-slate-400 block mb-1">کۆی شایستەی کۆمسیۆن (پاداشت)</span>
                <span class="text-xl font-bold font-mono text-blue-400" dir="ltr">{{ number_format($commissionAmount) }} IQD</span>
            </div>
        </div>

        <!-- لیستی وەسڵە فرۆشراوەکان -->
        <div class="bg-[#1e293b]/90 rounded-2xl border border-slate-700/70 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-300">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">ژمارەی وەسڵ</th>
                            <th class="p-3.5">بەروار</th>
                            <th class="p-3.5">کڕیار</th>
                            <th class="p-3.5">جۆری فرۆشتن</th>
                            <th class="p-3.5">کۆی پارەی وەسڵ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($rep->sales as $index => $sale)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-3.5 font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-3.5 font-mono font-bold text-blue-400">{{ $sale->invoice_no }}</td>
                            <td class="p-3.5 font-mono text-slate-300">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3.5 font-bold text-white">{{ $sale->customer->name ?? 'کڕیاری گشتی' }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-1 rounded text-[11px] font-bold {{ $sale->payment_type == 'debt' ? 'bg-amber-950/60 text-amber-400 border border-amber-800' : 'bg-emerald-950/60 text-emerald-400 border border-emerald-800' }}">
                                    {{ $sale->payment_type == 'debt' ? 'قەرز' : 'نەقد' }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-emerald-400" dir="ltr">
                                {{ number_format($sale->total_amount) }} IQD
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">هیچ فرۆشتنێک لە ڕێگەی ئەم مەندووبەوە تۆمار نەکراوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>