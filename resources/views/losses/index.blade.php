<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیانی کاڵا (بەسەرچوو / تەلەف)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background: radial-gradient(900px 400px at 90% -10%, #3b1522 0%, transparent 60%), #0a111d; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); }
        .num { font-variant-numeric: tabular-nums; direction: ltr; unicode-bidi: isolate; }
        .inp { width: 100%; padding: .6rem .75rem; border-radius: .75rem; background: rgba(15,23,42,.7); border: 1px solid rgba(255,255,255,.1); color: #fff; font-size: 12px; }
        .inp:focus, button:focus-visible, a:focus-visible { outline: 2px solid #fb7185; outline-offset: 1px; }
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
@php
    $usd = fn($v) => '$' . number_format($v, 2);
    $iqd = fn($v) => number_format($v) . ' IQD';
    $reasons = \App\Models\StockLoss::REASONS;
    $rColor = ['expired' => 'bg-amber-500/15 text-amber-300', 'damaged' => 'bg-rose-500/15 text-rose-300', 'lost' => 'bg-violet-500/15 text-violet-300', 'other' => 'bg-slate-500/20 text-slate-300'];
    $qty = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.') ?: '0';
@endphp
<body class="text-slate-100 min-h-screen p-4 md:p-6">
<div class="max-w-7xl mx-auto space-y-5">

    <header class="glass rounded-2xl p-4 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-base font-extrabold flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation text-rose-400"></i> زیانی کاڵا (بەسەرچوو / تەلەف)</h1>
            <p class="text-[11px] text-slate-400 mt-1">کاڵای لەناوچوو لە کۆگا دەردەکرێت و وەک زیان لە حیساباتدا دەژمێردرێت</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold">
            <a href="{{ route('products.index') }}" class="glass hover:bg-white/10 px-3 py-2 rounded-xl"><i class="fa-solid fa-boxes-stacked"></i> کۆگا</a>
            <a href="{{ route('reports.index') }}" class="glass hover:bg-white/10 px-3 py-2 rounded-xl"><i class="fa-solid fa-chart-pie"></i> ڕاپۆرت</a>
            <a href="{{ route('pos.index') }}" class="bg-rose-500 hover:bg-rose-400 text-white px-4 py-2 rounded-xl font-extrabold">POS</a>
        </div>
    </header>

    @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-3 rounded-xl text-xs font-bold"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold">{{ $errors->first() }}</div>@endif

    <!-- پوختە -->
    <section class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="glass rounded-2xl p-4 col-span-2 lg:col-span-1 border-rose-500/30">
            <p class="text-[11px] font-bold text-rose-300">کۆی زیان (بەپێی فلتەر)</p>
            <p class="num text-2xl font-black text-rose-400 mt-1">{{ $usd($totalUsd) }}</p>
            <p class="num text-[11px] text-slate-400">≈ {{ $iqd($totalUsd * $rate) }}</p>
        </div>
        @foreach($reasons as $key => $label)
        <div class="glass rounded-2xl p-4">
            <p class="text-[11px] font-bold text-slate-400">{{ $label }}</p>
            <p class="num text-lg font-extrabold mt-1">{{ $usd((float) ($byReason[$key]->total ?? 0)) }}</p>
            <p class="text-[11px] text-slate-500">{{ (int) ($byReason[$key]->cnt ?? 0) }} تۆمار</p>
        </div>
        @endforeach
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- فۆڕمی دەرکردن -->
        <section class="glass rounded-2xl p-5 space-y-3 h-fit">
            <h2 class="text-sm font-extrabold flex items-center gap-2 border-b border-white/10 pb-3"><i class="fa-solid fa-box-archive text-rose-400"></i> دەرکردنی کاڵا لە کۆگا</h2>
            <form action="{{ route('losses.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-bold mb-1">کاڵا:</label>
                    <select name="product_id" id="lossProduct" required class="inp" onchange="syncProduct()">
                        <option value="">— کاڵا هەڵبژێرە —</option>
                        @foreach($products as $p)
                            @php $isC = ($p->sell_type ?? 'weight') === 'carton'; @endphp
                            <option value="{{ $p->id }}" data-stock="{{ (float) ($p->stock_kg ?? $p->stock ?? 0) }}" data-unit="{{ $isC ? 'کارتۆن' : 'کگ' }}" data-cost="{{ (float) $p->base_buy_price }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <p id="stockHint" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="block text-slate-300 font-bold mb-1">بڕ (<span id="unitLbl">کگ / کارتۆن</span>):</label><input type="number" step="any" min="0.001" name="quantity" id="lossQty" value="{{ old('quantity') }}" required class="inp num" oninput="syncProduct()"></div>
                    <div><label class="block text-slate-300 font-bold mb-1">بەروار:</label><input type="date" name="loss_date" value="{{ old('loss_date', date('Y-m-d')) }}" required class="inp num"></div>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">هۆکاری دەرکردن:</label>
                    <select name="reason" required class="inp">
                        @foreach($reasons as $key => $label)<option value="{{ $key }}" {{ old('reason') === $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-slate-300 font-bold mb-1">تێبینی:</label><input type="text" name="note" value="{{ old('note') }}" placeholder="نموونە: بەرواری بەسەرچوونی تەواو بوو" class="inp"></div>
                <p id="costHint" class="text-[11px] text-rose-300 bg-rose-500/10 border border-rose-500/20 rounded-lg p-2 hidden"></p>
                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-400 text-white font-extrabold py-2.5 rounded-xl"><i class="fa-solid fa-check"></i> دەرکردن و تۆمارکردنی زیان</button>
            </form>
        </section>

        <!-- لیست -->
        <section class="lg:col-span-2 glass rounded-2xl p-5 space-y-4">
            <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-2 text-xs">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="inp num" title="لە بەرواری">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="inp num" title="تا بەرواری">
                <select name="reason" class="inp"><option value="">هەموو هۆکارەکان</option>@foreach($reasons as $k => $l)<option value="{{ $k }}" {{ request('reason') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select>
                <select name="source" class="inp"><option value="">هەموو سەرچاوەکان</option><option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>دەرکردنی دەستی</option><option value="sale_return" {{ request('source') === 'sale_return' ? 'selected' : '' }}>گەڕاوەی کڕیار</option></select>
                <button class="bg-rose-500 hover:bg-rose-400 text-white font-bold rounded-xl py-2"><i class="fa-solid fa-filter"></i> فلتەر</button>
            </form>

            <div class="overflow-auto rounded-xl border border-white/10">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-white/5 text-[11px] text-slate-400"><tr>
                        <th class="p-2.5">بەروار</th><th class="p-2.5">کاڵا</th><th class="p-2.5">بڕ</th><th class="p-2.5">هۆکار</th><th class="p-2.5">سەرچاوە</th><th class="p-2.5">زیان</th><th class="p-2.5"></th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/5">
                    @forelse($losses as $l)
                        @php $isC = ($l->product->sell_type ?? 'weight') === 'carton'; @endphp
                        <tr class="hover:bg-white/5">
                            <td class="p-2.5 num text-slate-400">{{ $l->loss_date->format('Y-m-d') }}</td>
                            <td class="p-2.5 font-bold text-white">{{ $l->product->name ?? '-' }}@if($l->note)<div class="text-[10px] text-slate-500 font-normal">{{ $l->note }}</div>@endif</td>
                            <td class="p-2.5 num">{{ $qty($l->quantity) }} <span class="text-[10px] text-slate-500">{{ $isC ? 'کارتۆن' : 'کگ' }}</span></td>
                            <td class="p-2.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rColor[$l->reason] ?? $rColor['other'] }}">{{ $reasons[$l->reason] ?? $l->reason }}</span></td>
                            <td class="p-2.5 text-[11px]">
                                @if($l->source === 'sale_return')
                                    <span class="text-sky-300 font-bold"><i class="fa-solid fa-rotate-left"></i> گەڕاوەی کڕیار</span>
                                    <div class="text-[10px] text-slate-500">{{ $l->customer->name ?? 'کڕیاری گشتی' }} @if($l->saleReturn) · <span class="num">{{ $l->saleReturn->return_no }}</span>@endif</div>
                                @else
                                    <span class="text-slate-400">دەرکردنی دەستی</span>
                                    <div class="text-[10px] text-slate-500">{{ $l->user->name ?? '' }}</div>
                                @endif
                            </td>
                            <td class="p-2.5 num font-bold text-rose-400">{{ $usd((float) $l->total_cost_usd) }}</td>
                            <td class="p-2.5">
                                @if($l->source === 'manual')
                                <form action="{{ route('losses.destroy', $l->id) }}" method="POST" onsubmit="return confirm('هەڵدەوەشێنرێتەوە و کاڵاکە دەگەڕێتەوە کۆگا؟')">@csrf @method('DELETE')<button class="text-[10px] bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg font-bold" title="هەڵوەشاندنەوە"><i class="fa-solid fa-rotate-left"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-8 text-center text-slate-500"><i class="fa-solid fa-inbox text-2xl block mb-2"></i> هیچ زیانێک تۆمار نەکراوە</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($losses->hasPages())<div>{{ $losses->links() }}</div>@endif
        </section>
    </div>
</div>

<script>
    const RATE = {{ (float) $rate }};
    function syncProduct() {
        const sel = document.getElementById('lossProduct'), o = sel.selectedOptions[0];
        const hint = document.getElementById('stockHint'), cost = document.getElementById('costHint'), qtyEl = document.getElementById('lossQty');
        if (!o || !o.value) { hint.textContent = ''; cost.classList.add('hidden'); return; }
        const stock = parseFloat(o.dataset.stock) || 0, unit = o.dataset.unit, unitCost = parseFloat(o.dataset.cost) || 0;
        document.getElementById('unitLbl').textContent = unit;
        hint.textContent = 'کۆگای ئێستا: ' + stock.toLocaleString() + ' ' + unit;
        qtyEl.max = stock;
        const q = parseFloat(qtyEl.value) || 0;
        if (q > 0) {
            const total = q * unitCost;
            cost.textContent = 'زیانی ئەم دەرکردنە: $' + total.toFixed(2) + '  (≈ ' + Math.round(total * RATE).toLocaleString() + ' IQD)';
            cost.classList.remove('hidden');
        } else cost.classList.add('hidden');
    }
    syncProduct();
</script>
</body>
</html>