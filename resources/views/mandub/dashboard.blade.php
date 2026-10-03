<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پارەی وەرگیراو و تەسلیمات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background: radial-gradient(1200px 500px at 85% -10%, #14352f 0%, transparent 60%), #0b1220; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
        .num { font-variant-numeric: tabular-nums; direction: ltr; unicode-bidi: isolate; }
        [data-view="usd"] .iqd { display: none !important; }
        [data-view="iqd"] .usd, [data-view="iqd"] .eq { display: none !important; }
        .seg button[aria-pressed="true"] { background: #10b981; color: #052e24; }
        a:focus-visible, input:focus-visible, select:focus-visible, button:focus-visible { outline: 2px solid #34d399; outline-offset: 2px; }
    </style>
</head>
@php
    $usd = fn($v) => ($v < 0 ? '-' : '') . '$' . number_format(abs($v), 2);
    $iqd = fn($v) => ($v < 0 ? '-' : '') . number_format(abs($v)) . ' IQD';
    $inCur = fn($s, $v) => strtoupper($s->currency ?? 'IQD') === 'USD' ? $usd($v) : $iqd($v);

    // [ناونیشان، دراوەکان، ئایکۆن، ڕەنگ، ژێرنووس]
    $cards = [
        ['وەرگیراو لە کڕیاران (ماوەکە)', $collectedPeriod, 'fa-hand-holding-dollar', 'text-emerald-400', 'نەقد + بەشی دراوی قەرز + وەرگرتنەوەی قەرز'],
        ['تەسلیمکراو (ماوەکە)', $handedPeriod, 'fa-right-left', 'text-sky-400', 'ئەوەی دراوە بە سندوق / ئەدمین'],
        ['کۆی فرۆشتن (ماوەکە)', $totalSales, 'fa-bag-shopping', 'text-indigo-300', $salesCount . ' وەسڵی فرۆشراو'],
        ['قەرزی نوێ بە کڕیاران', $newDebt, 'fa-clock', 'text-amber-400', 'ئەوەی لەم وەسڵانە نەدراوە'],
        ['وەرگرتنەوەی قەرز (ماوەکە)', $collectedDebt, 'fa-money-bill-transfer', 'text-teal-300', $paymentsCount . ' پارەدان'],
        ['گەڕاوەکان (ماوەکە)', $returns, 'fa-rotate-left', 'text-rose-400', $returnsCount . ' وەسڵی گەڕاوە'],
    ];
@endphp
<body class="text-slate-100 min-h-screen p-4 md:p-8">
<div class="max-w-7xl mx-auto space-y-6" id="root" data-view="both">

    <header class="glass rounded-3xl p-4 md:p-5 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-lg"><i class="fa-solid fa-scale-balanced"></i></div>
            <div>
                <h1 class="text-base font-extrabold">پارەی وەرگیراو و تەسلیمات</h1>
                <p class="text-xs text-slate-400">چەند پارە وەرگیراوە، چەند تەسلیمکراوە، و چەند ماوە لای کارمەند</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="seg glass rounded-xl p-1 flex text-xs font-bold" role="group" aria-label="دراو">
                <button type="button" data-set="both" aria-pressed="true" class="px-3 py-1.5 rounded-lg transition">هەردووکیان</button>
                <button type="button" data-set="usd" aria-pressed="false" class="px-3 py-1.5 rounded-lg transition">دۆلار $</button>
                <button type="button" data-set="iqd" aria-pressed="false" class="px-3 py-1.5 rounded-lg transition">دینار</button>
            </div>
            <a href="{{ route('pos.index') }}" class="glass hover:bg-white/10 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5"><i class="fa-solid fa-arrow-right"></i> POS</a>
        </div>
    </header>

    <form method="GET" action="{{ route('mandub.dashboard') }}" class="glass rounded-3xl p-4 flex flex-wrap items-end gap-4 text-xs">
        @if($canReceive)
        <div class="w-full sm:w-64">
            <label class="block font-bold text-slate-300 mb-1">کارمەند</label>
            <select name="user_id" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                @foreach($mandubs as $m)
                    <option value="{{ $m->id }}" {{ $targetUser->id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div class="text-sm font-bold"><i class="fa-solid fa-user text-emerald-400"></i> {{ $targetUser->name }}</div>
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
        <a href="{{ route('mandub.dashboard', ['user_id' => $targetUser->id, 'start_date' => $startDate, 'end_date' => $endDate, 'print' => 1]) }}" target="_blank" class="glass hover:bg-white/10 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-1.5"><i class="fa-solid fa-print"></i> کەشفی حساب (A4)</a>
    </form>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- ماوە لە دەستیدا -->
        <div class="lg:row-span-2 rounded-3xl p-6 bg-gradient-to-br from-emerald-500 to-teal-700 text-emerald-950 shadow-xl shadow-emerald-900/30 flex flex-col justify-between gap-3">
            <div>
                <div class="flex items-center justify-between text-xs font-bold"><span>ماوە لە دەستی {{ $targetUser->name }}</span><i class="fa-solid fa-wallet"></i></div>
                <p class="usd num text-4xl font-black mt-3">{{ $usd($netCashInHand['USD']) }}</p>
                <p class="iqd num text-xl font-extrabold opacity-80 mt-1">{{ $iqd($netCashInHand['IQD']) }}</p>
                <p class="eq text-[11px] font-bold opacity-70 mt-2">کۆی هەموو بە دۆلار: <span class="num">{{ $usd($toUsd($netCashInHand)) }}</span></p>
            </div>
            <div class="text-[11px] font-bold border-t border-emerald-950/20 pt-3 space-y-1.5">
                <div class="flex justify-between"><span>کۆی هەموو وەرگیراو</span><span class="num">{{ $usd($toUsd($allCollected)) }}</span></div>
                <div class="flex justify-between"><span>گەڕاوەی نەقد</span><span class="num">-{{ $usd($toUsd($allRefunds)) }}</span></div>
                <div class="flex justify-between"><span>کۆی هەموو تەسلیمکراو</span><span class="num">-{{ $usd($toUsd($allHandedOver)) }}</span></div>
            </div>
        </div>

        @foreach($cards as [$title, $val, $icon, $color, $sub])
        <div class="glass rounded-3xl p-5 space-y-1.5">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400"><span>{{ $title }}</span><i class="fa-solid {{ $icon }} {{ $color }}"></i></div>
            <p class="usd num text-2xl font-black {{ $color }}">{{ $usd($val['USD']) }}</p>
            <p class="iqd num text-sm font-bold text-slate-300">{{ $iqd($val['IQD']) }}</p>
            <p class="eq text-[11px] text-slate-500">≈ <span class="num">{{ $usd($toUsd($val)) }}</span></p>
            <p class="text-[11px] text-slate-400 border-t border-white/5 pt-2">{{ $sub }}</p>
        </div>
        @endforeach
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if($canReceive)
        <div class="glass rounded-3xl p-6 space-y-4 h-fit">
            <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-hand-holding-dollar text-emerald-400"></i> وەرگرتنی پارە لە {{ $targetUser->name }}</h3>

            @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-2.5 rounded-xl text-xs font-bold">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2.5 rounded-xl text-xs font-bold">{{ $errors->first() }}</div>@endif

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
                    <p class="flex justify-between text-[11px] text-slate-400 mt-1"><span>ماوە لە دەستیدا:</span><b id="hLeft" class="num text-emerald-400"></b></p>
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">تێبینی</label>
                    <input type="text" name="note" placeholder="نموونە: تەسلیماتی ڕۆژانە" class="w-full p-2.5 bg-slate-900/70 border border-white/10 rounded-xl">
                </div>
                <button type="submit" id="hBtn" class="w-full bg-emerald-500 hover:bg-emerald-400 disabled:bg-slate-700 disabled:text-slate-400 disabled:cursor-not-allowed text-emerald-950 font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i> تۆمارکردن و بڕینی پسوولە
                </button>
            </form>
        </div>
        @endif

        <div class="{{ $canReceive ? 'lg:col-span-2' : 'lg:col-span-3' }} glass rounded-3xl p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-receipt text-sky-400"></i> مێژووی وەسڵەکانی تەسلیمات</h3>
                <span class="text-xs text-slate-400">کۆی تەسلیمکراو:
                    <b class="usd num text-emerald-400">{{ $usd($allHandedOver['USD']) }}</b>
                    <b class="iqd num text-emerald-400 mr-2">{{ $iqd($allHandedOver['IQD']) }}</b>
                </span>
            </div>
            <div class="overflow-auto max-h-80">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="text-slate-400 sticky top-0 bg-[#0f1a2e]"><tr>
                        <th class="p-2.5">وەسڵ</th><th class="p-2.5">بەروار</th><th class="p-2.5">تەسلیمکار</th><th class="p-2.5">وەرگر</th><th class="p-2.5">بڕ</th><th class="p-2.5 text-center">چاپ</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/5">
                    @forelse($handovers as $h)
                        @php $hU = strtoupper($h->currency ?? 'IQD') === 'USD'; @endphp
                        <tr class="hover:bg-white/5">
                            <td class="p-2.5 num font-bold text-sky-400">{{ $h->receipt_no }}</td>
                            <td class="p-2.5 num text-slate-400">{{ $h->handover_date->format('Y-m-d h:i A') }}</td>
                            <td class="p-2.5 text-amber-400 font-bold">{{ $h->mandub->name ?? '-' }}</td>
                            <td class="p-2.5 font-bold">{{ $h->receiver->name ?? 'سندوق' }}</td>
                            <td class="p-2.5 num font-bold {{ $hU ? 'usd text-emerald-400' : 'iqd text-slate-200' }}">{{ $hU ? $usd($h->amount) : $iqd($h->amount) }}</td>
                            <td class="p-2.5 text-center"><a href="{{ route('handovers.print', $h->id) }}" target="_blank" class="bg-sky-500/20 hover:bg-sky-500/40 text-sky-300 px-2.5 py-1 rounded-lg font-bold inline-flex items-center gap-1 transition"><i class="fa-solid fa-print"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-500">هیچ وەسڵێکی تەسلیمات تۆمار نەکراوە</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- وەسڵەکانی ماوەکە -->
    <section class="glass rounded-3xl p-6 space-y-4">
        <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-file-invoice text-indigo-300"></i> وەسڵەکانی فرۆشتن لەم ماوەیەدا <span class="num text-xs text-slate-400">({{ $salesCount }})</span></h3>
        <div class="overflow-auto max-h-96">
            <table class="w-full text-xs text-right text-slate-300">
                <thead class="text-slate-400 sticky top-0 bg-[#0f1a2e]"><tr>
                    <th class="p-2.5">وەسڵ</th><th class="p-2.5">بەروار</th><th class="p-2.5">کڕیار</th><th class="p-2.5">جۆر</th>
                    <th class="p-2.5">کۆ</th><th class="p-2.5">دراو</th><th class="p-2.5">ماوە (قەرز)</th>
                </tr></thead>
                <tbody class="divide-y divide-white/5">
                @forelse($salesList as $s)
                    <tr class="hover:bg-white/5">
                        <td class="p-2.5 num font-bold text-sky-400">{{ $s->invoice_no }}</td>
                        <td class="p-2.5 num text-slate-400">{{ $s->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2.5 font-bold">{{ $s->customer->name ?? 'کڕیاری گشتی' }}</td>
                        <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $s->payment_type === 'cash' ? 'bg-emerald-500/15 text-emerald-300' : 'bg-amber-500/15 text-amber-300' }}">{{ $s->payment_type === 'cash' ? 'نەقد' : 'قەرز' }}</span></td>
                        <td class="p-2.5 num font-bold">{{ $inCur($s, $s->total_amount) }}</td>
                        <td class="p-2.5 num text-slate-400">{{ strtoupper($s->currency ?? 'IQD') === 'USD' ? 'USD' : 'IQD' }}</td>
                        <td class="p-2.5 num {{ $s->remaining_amount > 0 ? 'text-amber-400 font-bold' : 'text-slate-500' }}">{{ $inCur($s, $s->remaining_amount) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">هیچ وەسڵێک لەم ماوەیەدا نییە</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    const root = document.getElementById('root');
    function setView(v) {
        root.dataset.view = v;
        document.querySelectorAll('[data-set]').forEach(b => b.setAttribute('aria-pressed', b.dataset.set === v));
        try { localStorage.setItem('mandubView', v); } catch (e) {}
    }
    document.querySelectorAll('[data-set]').forEach(b => b.addEventListener('click', () => setView(b.dataset.set)));
    try { const s = localStorage.getItem('mandubView'); if (s) setView(s); } catch (e) {}

    const cur = document.getElementById('hCur');
    if (cur) {
        const amt = document.getElementById('hAmount'), left = document.getElementById('hLeft'), btn = document.getElementById('hBtn');
        const sync = () => {
            const max = parseFloat(cur.selectedOptions[0].dataset.max) || 0, isUsd = cur.value === 'USD';
            amt.max = max; amt.step = isUsd ? '0.01' : '1';
            left.textContent = isUsd ? '$' + max.toLocaleString('en', { minimumFractionDigits: 2 }) : max.toLocaleString('en') + ' IQD';
            btn.disabled = max <= 0;
        };
        cur.addEventListener('change', sync); sync();
    }
</script>
</body>
</html>