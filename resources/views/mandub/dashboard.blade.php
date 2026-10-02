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
        body { font-family: 'Noto Sans Arabic', sans-serif; background: radial-gradient(1200px 500px at 85% -10%, #14352f 0%, transparent 60%), #0b1220; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
        .num { font-variant-numeric: tabular-nums; direction: ltr; unicode-bidi: isolate; }
        /* دراو: both / usd / iqd */
        [data-view="usd"] .iqd { display: none !important; }
        [data-view="iqd"] .usd { display: none !important; }
        [data-view="iqd"] .eq  { display: none !important; }
        .seg button[aria-pressed="true"] { background: #10b981; color: #052e24; }
        .seg button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible { outline: 2px solid #34d399; outline-offset: 2px; }
        @media (prefers-reduced-motion: no-preference) { .count { animation: rise .5s ease-out both; } @keyframes rise { from { opacity: 0; transform: translateY(6px); } } }
    </style>
</head>
@php
    $usd = fn($v) => '$' . number_format($v, 2);
    $iqd = fn($v) => number_format($v) . ' IQD';

    // کارتەکان: [ناونیشان، دراوەکان، ئایکۆن، ڕەنگ، ژێرنووس]
    $cards = [
        ['فرۆشتنی ماوەکە', $totalSales, 'fa-bag-shopping', 'text-sky-400', $salesCount . ' وەسڵی فرۆشراو'],
        ['وەرگرتنەوەی قەرز', $collectedDebt, 'fa-hand-holding-dollar', 'text-emerald-400', count($paymentsList) . ' پارەدان'],
        ['گەڕاوەکان', $returns, 'fa-rotate-left', 'text-rose-400', $returnsCount . ' وەسڵی گەڕاوە'],
    ];
@endphp
<body class="text-slate-100 min-h-screen p-4 md:p-8">
<div class="max-w-7xl mx-auto space-y-6" id="root" data-view="both">

    <!-- هێدەر -->
    <header class="glass rounded-3xl p-4 md:p-5 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-lg"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <h1 class="text-base font-extrabold">داشبۆردی چالاکییەکان و تەسلیمات</h1>
                <p class="text-xs text-slate-400">نرخی ئاڵوگۆڕ: <span class="num">$1 = {{ number_format($rate) }} IQD</span></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="seg glass rounded-xl p-1 flex text-xs font-bold" role="group" aria-label="دراو">
                <button type="button" data-set="both" aria-pressed="true" class="px-3 py-1.5 rounded-lg transition">هەردووکیان</button>
                <button type="button" data-set="usd" aria-pressed="false" class="px-3 py-1.5 rounded-lg transition">دۆلار $</button>
                <button type="button" data-set="iqd" aria-pressed="false" class="px-3 py-1.5 rounded-lg transition">دینار</button>
            </div>
            <a href="{{ route('pos.index') }}" class="glass hover:bg-white/10 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-right"></i> POS
            </a>
        </div>
    </header>

    <!-- فلتەر -->
    <form method="GET" action="{{ route('mandub.dashboard') }}" class="glass rounded-3xl p-4 flex flex-wrap items-end gap-4 text-xs">
        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-64">
            <label class="block font-bold text-slate-300 mb-1">مەندووب</label>
            <select name="user_id" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                @foreach($mandubs as $m)
                    <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->email }})</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="w-[46%] sm:w-44">
            <label class="block font-bold text-slate-300 mb-1">لە بەرواری</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
        </div>
        <div class="w-[46%] sm:w-44">
            <label class="block font-bold text-slate-300 mb-1">تا بەرواری</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
        </div>
        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-emerald-950 font-bold px-6 py-2.5 rounded-xl transition">فلتەرکردن</button>
    </form>

    <!-- کاشی ماوە (کارتی سەرەکی) + کارتەکانی تر -->
    <section class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <div class="lg:col-span-1 rounded-3xl p-6 bg-gradient-to-br from-emerald-500 to-teal-700 text-emerald-950 shadow-xl shadow-emerald-900/30 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold"><span>کاشی ماوە لە دەستیدا</span><i class="fa-solid fa-wallet"></i></div>
            <p class="usd num count text-4xl font-black">{{ $usd($netCashInHand['USD']) }}</p>
            <p class="iqd num count text-lg font-extrabold opacity-80">{{ $iqd($netCashInHand['IQD']) }}</p>
            <p class="eq num text-[11px] font-bold opacity-70 border-t border-emerald-950/20 pt-2">کۆی هەموو بە دۆلار: {{ $usd($toUsd($netCashInHand)) }}</p>
        </div>

        @foreach($cards as [$title, $val, $icon, $color, $sub])
        <div class="glass rounded-3xl p-5 space-y-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400"><span>{{ $title }}</span><i class="fa-solid {{ $icon }} {{ $color }}"></i></div>
            <p class="usd num count text-2xl font-black {{ $color }}">{{ $usd($val['USD']) }}</p>
            <p class="iqd num count text-sm font-bold text-slate-300">{{ $iqd($val['IQD']) }}</p>
            <p class="eq num text-[11px] text-slate-500">≈ {{ $usd($toUsd($val)) }}</p>
            <p class="text-[11px] text-slate-400 border-t border-white/5 pt-2">{{ $sub }}</p>
        </div>
        @endforeach
    </section>

    <!-- وەرگرتنی کاش + مێژوو -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass rounded-3xl p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i> وەرگرتنی کاش لە مەندووب</h3>

            @if(session('success'))
                <div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-2.5 rounded-xl text-xs font-bold">{{ session('success') }}</div>
            @endif

            <form action="{{ route('handovers.store') }}" method="POST" class="space-y-4 text-xs" id="handoverForm">
                @csrf
                <input type="hidden" name="mandub_id" value="{{ $targetUser->id }}">

                <div>
                    <label class="block font-bold text-slate-300 mb-1">دراو</label>
                    <select name="currency" id="hCur" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                        <option value="USD" data-max="{{ max(0, $netCashInHand['USD']) }}">دۆلار (USD)</option>
                        <option value="IQD" data-max="{{ max(0, $netCashInHand['IQD']) }}">دینار (IQD)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">بڕی پارەی وەرگیراو</label>
                    <input type="number" step="any" name="amount" id="hAmount" placeholder="0" required class="num w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                    <p class="flex justify-between text-[11px] text-slate-400 mt-1">
                        <span>ماوە لە دەستیدا:</span>
                        <b id="hLeft" class="num text-emerald-400"></b>
                    </p>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">تێبینی</label>
                    <input type="text" name="note" placeholder="نموونە: تەسلیماتی ڕۆژانە" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 disabled:bg-slate-700 disabled:text-slate-400 disabled:cursor-not-allowed text-emerald-950 font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-1.5" id="hBtn">
                    <i class="fa-solid fa-circle-check"></i> تۆمارکردن و بڕینی پسوولە
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 glass rounded-3xl p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-receipt text-sky-400"></i> مێژووی وەسڵەکانی تەسلیمات</h3>
                <span class="text-xs text-slate-400">کۆی تەسلیمکراو:
                    <b class="usd num text-emerald-400">{{ $usd($allHandedOver['USD']) }}</b>
                    <b class="iqd num text-emerald-400 mr-2">{{ $iqd($allHandedOver['IQD']) }}</b>
                </span>
            </div>
            <div class="overflow-auto max-h-80">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="text-slate-400 sticky top-0 bg-[#0f1a2e]">
                        <tr>
                            <th class="p-2.5">وەسڵ</th><th class="p-2.5">بەروار</th><th class="p-2.5">مەندووب</th>
                            <th class="p-2.5">وەرگر</th><th class="p-2.5">بڕ</th><th class="p-2.5 text-center">چاپ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($handovers as $h)
                        @php $isUsd = strtoupper($h->currency ?? 'IQD') === 'USD'; @endphp
                        <tr class="hover:bg-white/5">
                            <td class="p-2.5 num font-bold text-sky-400">{{ $h->receipt_no }}</td>
                            <td class="p-2.5 num text-slate-400">{{ $h->handover_date->format('Y-m-d h:i A') }}</td>
                            <td class="p-2.5 text-amber-400 font-bold">{{ $h->mandub->name ?? 'مەندووب' }}</td>
                            <td class="p-2.5 font-bold">{{ $h->receiver->name ?? 'سندوق' }}</td>
                            <td class="p-2.5 num font-bold {{ $isUsd ? 'usd text-emerald-400' : 'iqd text-slate-200' }}">
                                {{ $isUsd ? $usd($h->amount) : $iqd($h->amount) }}
                            </td>
                            <td class="p-2.5 text-center">
                                <a href="{{ route('handovers.print', $h->id) }}" target="_blank" class="bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 px-2.5 py-1 rounded-lg font-bold inline-flex items-center gap-1 transition"><i class="fa-solid fa-print"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-500">هیچ وەسڵێک تۆمار نەکراوە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    const root = document.getElementById('root');
    const saved = localStorage.getItem('mandubView');
    function setView(v) {
        root.dataset.view = v;
        document.querySelectorAll('[data-set]').forEach(b => b.setAttribute('aria-pressed', b.dataset.set === v));
        try { localStorage.setItem('mandubView', v); } catch (e) {}
    }
    document.querySelectorAll('[data-set]').forEach(b => b.addEventListener('click', () => setView(b.dataset.set)));
    if (saved) setView(saved);

    // فۆڕمی تەسلیمات: سنووری بڕ بەپێی دراو
    const cur = document.getElementById('hCur'), amt = document.getElementById('hAmount'),
          left = document.getElementById('hLeft'), btn = document.getElementById('hBtn');
    function syncHandover() {
        const max = parseFloat(cur.selectedOptions[0].dataset.max) || 0;
        const isUsd = cur.value === 'USD';
        amt.max = max; amt.step = isUsd ? '0.01' : '1';
        left.textContent = isUsd ? '$' + max.toLocaleString('en', {minimumFractionDigits: 2}) : max.toLocaleString('en') + ' IQD';
        btn.disabled = max <= 0;
    }
    cur.addEventListener('change', syncHandover); syncHandover();
</script>
</body>
</html>