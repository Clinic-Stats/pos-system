<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی سەرەکیی فرۆشتن و دارایی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Almarai', sans-serif; background: radial-gradient(900px 400px at 90% -5%, #dbeafe 0%, transparent 60%), #f4f6fb; }
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; direction: ltr; unicode-bidi: isolate; }
        .card { background: #fff; border-radius: 1.25rem; border: 1px solid #e6ebf3; box-shadow: 0 6px 20px -8px rgba(15,23,42,.08); }
        .btn-press { transition: transform .15s; } .btn-press:active { transform: scale(.96); }
        [data-view="usd"] .iqd { display: none !important; }
        [data-view="iqd"] .usd, [data-view="iqd"] .eq { display: none !important; }
        .seg button[aria-pressed="true"] { background: #2563eb; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.35); }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
    </style>
</head>
@php
    $u = fn($v) => ($v < 0 ? '-' : '') . '$' . number_format(abs($v), 2);
    $i = fn($v) => ($v < 0 ? '-' : '') . number_format(abs($v)) . ' IQD';
    $sale = fn($s, $v) => strtoupper($s->currency ?? 'IQD') === 'USD' ? $u($v) : $i($v);

    // [ناونیشان، دراوەکان، ئایکۆن، ڕەنگی ئایکۆن/ژمارە، ژێرنووس]
    $row1 = [
        ['کۆی فرۆشراو', $totalSalesAll, 'fa-bag-shopping', 'text-blue-600', 'نەقد ≈ ' . $u($toUsd($totalSalesCash)) . ' · قەرز ≈ ' . $u($toUsd($totalSalesDebt))],
        ['کۆی تێچووی فرۆشراو', $totalCostAll, 'fa-boxes-stacked', 'text-slate-600', 'تێچووی کڕینی کاڵاکان'],
        ['قازانجی کاڵا', $totalGrossProfit, 'fa-chart-line', 'text-emerald-600', 'فرۆشراو − تێچوو − قازانجی گەڕاوەکان'],
        ['کۆی مەسروفات', $totalExpenses, 'fa-wallet', 'text-rose-600', 'خەرجیی ئەم ماوەیە'],
        ['پوختەی قازانج (صافی)', $realNetProfit, 'fa-scale-balanced', $toUsd($realNetProfit) >= 0 ? 'text-emerald-600' : 'text-rose-600', 'قازانج - مەسروفات - تەلەف'],
    ];
    $row2 = [
        ['کاڵای ماوە (بێ قازانج)', $stockCost, 'fa-warehouse', 'text-slate-700', 'سەرمایەی ناو کۆگا بە نرخی کڕین'],
        ['کاڵای ماوە (بە قازانج)', $stockValue, 'fa-tags', 'text-indigo-600', 'بەهای فرۆشتنی مەخزەن'],
        ['قازانجی چاوەڕوانکراو', $stockProfit, 'fa-sack-dollar', 'text-emerald-600', 'ئەگەر هەموو مەخزەن بفرۆشرێت'],
        ['قەرزی سەر کڕیاران', $totalCustomerDebts, 'fa-hand-holding-dollar', 'text-amber-600', 'باڵانسی ماوە لای کڕیاران'],
        ['زیانی بەسەرچوو / تەلەف', $totalLosses, 'fa-triangle-exclamation', 'text-rose-600', 'بەهای کڕینی کاڵا لەناوچووەکان'],
    ];
@endphp
<body class="text-slate-800 min-h-screen p-3 md:p-6">
<div class="max-w-[1440px] mx-auto space-y-4" id="root" data-view="both">

    <header class="card p-3.5 md:px-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('pos.index') }}" class="btn-press bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 shadow-md shadow-blue-500/25">
            <i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ POS
        </a>
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
            <a href="{{ route('products.index') }}" class="btn-press bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl">کۆگا</a>
            <a href="{{ route('customers.index') }}" class="btn-press bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl">کڕیاران</a>
            <a href="{{ route('expenses.index') }}" class="btn-press bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl">خەرجی</a>
            <a href="{{ route('backup.database') }}" class="btn-press bg-indigo-50 text-indigo-600 border border-indigo-200 px-3 py-1.5 rounded-xl"><i class="fa-solid fa-database text-[10px]"></i> باکئەپ</a>
        </div>
        <div class="flex items-center gap-3">
            <div class="seg bg-slate-100 p-0.5 rounded-xl flex text-[11px] font-bold" role="group" aria-label="دراو">
                <button type="button" data-set="both" aria-pressed="true" class="px-3 py-1.5 rounded-lg">هەردووکیان</button>
                <button type="button" data-set="usd" aria-pressed="false" class="px-3 py-1.5 rounded-lg">دۆلار $</button>
                <button type="button" data-set="iqd" aria-pressed="false" class="px-3 py-1.5 rounded-lg">دینار</button>
            </div>
            <span class="font-num text-[11px] text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">$1 = {{ number_format($rate) }} IQD</span>
        </div>
    </header>

    <div class="card p-2.5 md:px-4 flex flex-wrap items-center justify-between gap-2.5 text-xs">
        <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2">
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="p-1.5 bg-slate-50 border border-slate-200 rounded-lg font-num text-[11px]">
            <span class="text-slate-400 font-bold">بۆ</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="p-1.5 bg-slate-50 border border-slate-200 rounded-lg font-num text-[11px]">
            <button type="submit" class="btn-press bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold"><i class="fa-solid fa-filter text-[10px]"></i> فلتەر</button>
        </form>
        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl font-bold text-[11px]">
            @foreach(['today' => 'ئەمڕۆ', 'yesterday' => 'دوێنێ', 'week' => 'ئەم هەفتەیە', 'month' => 'ئەم مانگە'] as $k => $label)
                <a href="{{ route('reports.index', ['period' => $k]) }}" class="px-2.5 py-1 rounded-lg transition {{ request('period') === $k ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ route('reports.index') }}" class="px-2.5 py-1 rounded-lg transition {{ !request()->has('period') && !request()->has('from_date') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600' }}">هەمووی</a>
        </div>
    </div>

    <!-- ڕیزی یەکەم -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-4 rounded-[1.25rem] shadow-xl shadow-slate-900/20 flex flex-col justify-between">
            <div class="flex justify-between items-center text-[11px] font-bold"><span>کاشی بەردەست (ناو دەخڵ)</span><i class="fa-solid fa-coins text-emerald-400"></i></div>
            <div class="mt-3">
                <p class="usd font-num text-2xl font-extrabold text-emerald-400">{{ $u($cashInHand['USD']) }}</p>
                <p class="iqd font-num text-sm font-bold text-emerald-200/80">{{ $i($cashInHand['IQD']) }}</p>
                <p class="eq font-num text-[10px] text-slate-400 mt-1">کۆی هەموو ≈ {{ $u($toUsd($cashInHand)) }}</p>
            </div>
            <div class="mt-3 pt-2 border-t border-white/10 space-y-1 text-[10px] text-slate-300">
                <div class="flex justify-between"><span class="font-num text-emerald-400 font-bold">+{{ $u($toUsd($totalSalesCash)) }}</span><span>فرۆشتنی نەقد</span></div>
                <div class="flex justify-between"><span class="font-num text-cyan-400 font-bold">+{{ $u($toUsd($totalDebtCollected)) }}</span><span>وەرگرتنەوەی قەرز</span></div>
                @if($toUsd($debtPaidAtSale) > 0)
                <div class="flex justify-between"><span class="font-num text-teal-300 font-bold">+{{ $u($toUsd($debtPaidAtSale)) }}</span><span>دراوی وەسڵی قەرز</span></div>
                @endif
                @if($toUsd($totalCashReturns) > 0)
                <div class="flex justify-between"><span class="font-num text-rose-400 font-bold">-{{ $u($toUsd($totalCashReturns)) }}</span><span>گەڕاوەی نەقد</span></div>
                @endif
            </div>
        </div>

        @foreach($row1 as [$t, $v, $ic, $col, $sub])
        <div class="card p-3.5 flex flex-col justify-between">
            <div class="flex justify-between items-center text-slate-500 text-[11px] font-bold"><span>{{ $t }}</span><i class="fa-solid {{ $ic }} {{ $col }}"></i></div>
            <div class="mt-3">
                <p class="usd font-num text-xl font-extrabold {{ $col }}">{{ $u($v['USD']) }}</p>
                <p class="iqd font-num text-xs font-bold text-slate-500">{{ $i($v['IQD']) }}</p>
                <p class="eq font-num text-[10px] text-slate-400">کۆی هەموو ≈ {{ $u($toUsd($v)) }}</p>
            </div>
            <p class="text-[10px] text-slate-400 pt-2 mt-2 border-t border-slate-100 text-right">{{ $sub }}</p>
        </div>
        @endforeach
    </section>

    <!-- ڕیزی دووەم -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($row2 as [$t, $v, $ic, $col, $sub])
        <div class="card p-4 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-500 block">{{ $t }}</span>
                <p class="usd font-num text-lg font-extrabold mt-1 {{ $col }}">{{ $u($v['USD']) }}</p>
                <p class="iqd font-num text-xs font-bold text-slate-500">{{ $i($v['IQD']) }}</p>
                <span class="text-[10px] text-slate-400">{{ $sub }}</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-slate-50 {{ $col }} flex items-center justify-center"><i class="fa-solid {{ $ic }}"></i></div>
        </div>
        @endforeach
    </section>

    <!-- چارتەکان -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h3 class="text-xs font-extrabold flex items-center gap-1.5 pb-2 border-b border-slate-100"><i class="fa-solid fa-crown text-amber-500"></i> ٥ پڕفرۆشترین کاڵاکان <span class="text-[10px] text-slate-400 font-bold mr-auto">بەپێی بڕ</span></h3>
            <div class="h-56 mt-3"><canvas id="topProductsChart"></canvas></div>
        </div>
        <div class="card p-4">
            <h3 class="text-xs font-extrabold flex items-center gap-1.5 pb-2 border-b border-slate-100"><i class="fa-solid fa-user-check text-blue-600"></i> ٥ زۆرترین کڕیاران <span id="custUnit" class="text-[10px] text-slate-400 font-bold mr-auto">بە دۆلار ($)</span></h3>
            <div class="h-56 mt-3"><canvas id="topCustomersChart"></canvas></div>
        </div>
        <div class="lg:col-span-2 card p-4">
            <h3 class="text-xs font-extrabold flex items-center gap-1.5"><i class="fa-solid fa-chart-column text-blue-600"></i> ئەنجامی دارایی <span id="finUnit" class="text-[10px] text-slate-400 font-bold mr-auto">بە دۆلار ($)</span></h3>
            <div class="h-52 mt-3"><canvas id="financialBarChart"></canvas></div>
        </div>
        <div class="card p-4 flex flex-col">
            <h3 class="text-xs font-extrabold flex items-center gap-1.5"><i class="fa-solid fa-chart-pie text-indigo-600"></i> نەقد بەرامبەر قەرز</h3>
            <div class="h-44 my-auto"><canvas id="paymentDonutChart"></canvas></div>
        </div>
    </section>

    <!-- خشتەی وەسڵەکان -->
    <section class="card overflow-hidden">
        <div class="p-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50/60">
            <span class="text-[11px] font-bold text-slate-500">کۆی تۆمارەکان: <b class="font-num text-slate-800">{{ $paginatedSales->total() }}</b></span>
            <h3 class="text-xs font-extrabold flex items-center gap-1.5"><i class="fa-solid fa-receipt text-blue-600"></i> دوایین وەسڵەکانی فرۆشتن</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-center">
                <thead class="bg-slate-100/70 text-slate-500 text-[10px] font-bold border-b border-slate-200">
                    <tr><th class="p-2.5">وەسڵ</th><th class="p-2.5">بەروار</th><th class="p-2.5">کڕیار</th><th class="p-2.5">جۆر</th><th class="p-2.5">دراو</th><th class="p-2.5">کۆی وەسڵ</th><th class="p-2.5">تێچوو</th><th class="p-2.5">قازانج</th><th class="p-2.5">کردار</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($paginatedSales as $s)
                    @php $isU = strtoupper($s->currency ?? 'IQD') === 'USD'; @endphp
                    <tr class="hover:bg-slate-50/80">
                        <td class="p-2.5 font-num font-bold text-blue-600">{{ $s->invoice_no }}</td>
                        <td class="p-2.5 font-num text-slate-500">{{ $s->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2.5 font-bold">{{ $s->customer->name ?? 'کڕیاری گشتی' }}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $s->payment_type === 'cash' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">{{ $s->payment_type === 'cash' ? 'نەقد' : 'قەرز' }}</span></td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $isU ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">{{ $isU ? 'USD' : 'IQD' }}</span></td>
                        <td class="p-2.5 font-num font-bold">{{ $sale($s, $s->total_amount) }}</td>
                        <td class="p-2.5 font-num text-slate-400">{{ $sale($s, $s->total_cost) }}</td>
                        <td class="p-2.5 font-num font-bold {{ $s->total_profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ ($s->total_profit >= 0 ? '+' : '') . $sale($s, $s->total_profit) }}</td>
                        <td class="p-2.5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('sales.print', $s->id) }}" target="_blank" title="پسوولە" class="btn-press bg-slate-100 hover:bg-slate-200 text-slate-600 p-1.5 rounded-lg"><i class="fa-solid fa-print"></i></a>
                                <a href="{{ route('sales.print', $s->id) }}?type=a4" target="_blank" class="btn-press bg-blue-50 text-blue-600 px-2 py-1 rounded-lg text-[10px] font-bold border border-blue-200">A4</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-6 text-slate-400 font-bold">هیچ وەسڵێک نەدۆزرایەوە</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($paginatedSales->hasPages())<div class="p-2.5 border-t border-slate-100">{{ $paginatedSales->links() }}</div>@endif
    </section>
</div>

<script>
    const RATE = {{ (float) $rate }};
    const charts = [];
    const fontNum = { family: 'Plus Jakarta Sans' };
    const mk = (id, type, labels, base, colors, opts = {}, scalable = true) => {
        const c = new Chart(document.getElementById(id), {
            type, data: { labels, datasets: [{ data: [...base], backgroundColor: colors, borderRadius: type === 'bar' ? 6 : 0, borderWidth: 0, barPercentage: .55 }] },
            options: Object.assign({ responsive: true, maintainAspectRatio: false, plugins: { legend: { display: type === 'doughnut', position: 'bottom', labels: { font: { family: 'Almarai', size: 10 } } } } }, opts)
        });
        charts.push({ c, base, scalable });
    };
    const axis = (horizontal) => ({ scales: { [horizontal ? 'x' : 'y']: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: fontNum } }, [horizontal ? 'y' : 'x']: { grid: { display: false }, ticks: { font: { family: 'Almarai', size: 10, weight: 'bold' } } } } });

    const pl = {!! json_encode($topProducts->pluck('name')) !!}, pv = {!! json_encode($topProducts->pluck('total_qty')->map(fn($v) => (float) $v)) !!};
    mk('topProductsChart', 'bar', pl.length ? pl : ['نییە'], pv.length ? pv : [0], '#f59e0b', Object.assign({ indexAxis: 'y' }, axis(true)), false);

    const cl = {!! json_encode($topCustomers->pluck('name')) !!}, cv = {!! json_encode($topCustomers->pluck('total_spent')->map(fn($v) => round((float) $v, 2))) !!};
    mk('topCustomersChart', 'bar', cl.length ? cl : ['نییە'], cv.length ? cv : [0], '#4f46e5', axis(false));

    mk('financialBarChart', 'bar', ['کۆی فرۆشراو', 'تێچوو', 'قازانجی فرۆشتن', 'مەسروفات', 'پوختەی قازانج'],
       [{{ round($toUsd($totalSalesAll), 2) }}, {{ round($toUsd($totalCostAll), 2) }}, {{ round($toUsd($totalGrossProfit), 2) }}, {{ round($toUsd($totalExpenses), 2) }}, {{ round($toUsd($realNetProfit), 2) }}],
       ['#2563eb', '#64748b', '#059669', '#e11d48', '#0d9488'], axis(false));

    mk('paymentDonutChart', 'doughnut', ['فرۆشتنی نەقد', 'فرۆشتنی قەرز'],
       [{{ round($toUsd($totalSalesCash), 2) }}, {{ round($toUsd($totalSalesDebt), 2) }}], ['#059669', '#d97706'], { cutout: '68%', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { family: 'Almarai', size: 10 } } } } });

    const root = document.getElementById('root');
    function setView(v) {
        root.dataset.view = v;
        document.querySelectorAll('[data-set]').forEach(b => b.setAttribute('aria-pressed', b.dataset.set === v));
        const k = v === 'iqd' ? RATE : 1;
        charts.forEach(o => { if (o.scalable) { o.c.data.datasets[0].data = o.base.map(x => x * k); o.c.update(); } });
        const unit = v === 'iqd' ? 'بە دینار (IQD)' : 'بە دۆلار ($)';
        document.getElementById('custUnit').textContent = unit;
        document.getElementById('finUnit').textContent = unit;
        try { localStorage.setItem('reportView', v); } catch (e) {}
    }
    document.querySelectorAll('[data-set]').forEach(b => b.addEventListener('click', () => setView(b.dataset.set)));
    try { const s = localStorage.getItem('reportView'); if (s) setView(s); } catch (e) {}
</script>
</body>
</html>