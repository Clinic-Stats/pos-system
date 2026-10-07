<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تۆماری چالاکییەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background: radial-gradient(900px 400px at 90% -10%, #1e2a4a 0%, transparent 60%), #0a111d; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); }
        .num { font-variant-numeric: tabular-nums; direction: ltr; unicode-bidi: isolate; }
        .inp { width: 100%; padding: .55rem .7rem; border-radius: .7rem; background: rgba(15,23,42,.7); border: 1px solid rgba(255,255,255,.1); color: #fff; font-size: 12px; }
        .inp:focus, button:focus-visible, a:focus-visible, summary:focus-visible { outline: 2px solid #818cf8; outline-offset: 1px; }
        summary { cursor: pointer; list-style: none; } summary::-webkit-details-marker { display: none; }
        details[open] .chev { transform: rotate(180deg); }
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
@php
    $actions = \App\Models\ActivityLog::ACTIONS;
    $types = \App\Models\ActivityLog::TYPES;
    $fields = \App\Models\ActivityLog::FIELDS;
    $aStyle = ['created' => 'bg-emerald-500/15 text-emerald-300', 'updated' => 'bg-amber-500/15 text-amber-300', 'deleted' => 'bg-rose-500/15 text-rose-300'];
    $aIcon = ['created' => 'fa-plus', 'updated' => 'fa-pen', 'deleted' => 'fa-trash'];
    $tIcon = ['sale' => 'fa-bag-shopping', 'purchase' => 'fa-cart-flatbed', 'return' => 'fa-rotate-left', 'customer_payment' => 'fa-hand-holding-dollar',
              'handover' => 'fa-right-left', 'expense' => 'fa-wallet', 'supplier_payment' => 'fa-truck-field', 'loss' => 'fa-triangle-exclamation'];
    $money = function ($v, $c) { if ($v === null) return '-'; return strtoupper($c ?? '') === 'USD' ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' IQD'; };
    $val = function ($k, $v) {
        if (is_array($v)) return json_encode($v, JSON_UNESCAPED_UNICODE);
        if ($v === null || $v === '') return '—';
        if ($k === 'payment_type') return $v === 'debt' ? 'قەرز' : ($v === 'cash' ? 'نەقد' : $v);
        if ($k === 'refund_type') return $v === 'deduct_debt' ? 'داشکاندن لە قەرز' : ($v === 'cash' ? 'نەقد' : $v);
        if ($k === 'reason') return \App\Models\StockLoss::REASONS[$v] ?? $v;
        return $v;
    };
    $itemsTable = function ($items) { return $items; };
@endphp
<body class="text-slate-100 min-h-screen p-4 md:p-6">
<div class="max-w-7xl mx-auto space-y-5">

    <header class="glass rounded-2xl p-4 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h1 class="text-base font-extrabold flex items-center gap-2"><i class="fa-solid fa-user-shield text-indigo-300"></i> تۆماری چالاکییەکان</h1>
            <p class="text-[11px] text-slate-400 mt-1">کێ چی کردووە: وەسڵ، پارە، دەستکاری و سڕینەوە. تەنها ئەدمین دەیبینێت</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold">
            <a href="{{ route('reports.index') }}" class="glass hover:bg-white/10 px-3 py-2 rounded-xl"><i class="fa-solid fa-chart-pie"></i> ڕاپۆرت</a>
            <a href="{{ route('pos.index') }}" class="bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 rounded-xl font-extrabold">POS</a>
        </div>
    </header>

    <section class="grid grid-cols-3 gap-3">
        @foreach($actions as $k => $label)
        <div class="glass rounded-2xl p-4">
            <p class="text-[11px] font-bold text-slate-400"><span class="inline-block w-6 h-6 text-center leading-6 rounded-lg {{ $aStyle[$k] }} ml-1"><i class="fa-solid {{ $aIcon[$k] }} text-[10px]"></i></span> {{ $label }}</p>
            <p class="num text-2xl font-black mt-1">{{ (int) ($counts[$k] ?? 0) }}</p>
        </div>
        @endforeach
    </section>

    <form method="GET" class="glass rounded-2xl p-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2 text-xs">
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="inp num" title="لە بەرواری">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="inp num" title="تا بەرواری">
        <select name="user_id" class="inp"><option value="">هەموو کارمەندان</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select>
        <select name="action" class="inp"><option value="">هەموو کردارەکان</option>@foreach($actions as $k => $l)<option value="{{ $k }}" {{ request('action') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select>
        <select name="type" class="inp"><option value="">هەموو جۆرەکان</option>@foreach($types as $k => $l)<option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="گەڕان: ژمارە، کڕیار..." class="inp">
        <button class="bg-indigo-500 hover:bg-indigo-400 text-white font-bold rounded-xl py-2"><i class="fa-solid fa-filter"></i> فلتەر</button>
    </form>

    <section class="space-y-2">
    @forelse($logs as $log)
        @php
            $d = $log->details ?? [];
            $old = $d['old'] ?? []; $new = $d['new'] ?? [];
            $before = $d['items_before'] ?? null; $after = $d['items_after'] ?? null;
            $hasMore = !empty($old) || !empty($new) || !empty($before) || !empty($after);
        @endphp
        <details class="glass rounded-2xl overflow-hidden">
            <summary class="p-3.5 flex flex-wrap items-center gap-3">
                <span class="w-9 h-9 rounded-xl flex items-center justify-center {{ $aStyle[$log->action] }}"><i class="fa-solid {{ $aIcon[$log->action] }} text-xs"></i></span>
                <div class="min-w-[170px] flex-1">
                    <p class="text-sm font-extrabold"><i class="fa-solid {{ $tIcon[$log->type] ?? 'fa-circle' }} text-slate-400 text-xs ml-1"></i>{{ $log->label }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $aStyle[$log->action] }}">{{ $actions[$log->action] ?? $log->action }}</span>
                        @if($log->party) · {{ $log->party }} @endif
                    </p>
                </div>
                <div class="text-[11px] text-slate-300"><i class="fa-solid fa-user text-slate-500"></i> <b>{{ $log->user_name ?? 'نادیار' }}</b></div>
                <div class="num text-sm font-extrabold min-w-[110px] text-left">{{ $money($log->amount, $log->currency) }}</div>
                <div class="num text-[11px] text-slate-400 min-w-[120px] text-left">{{ $log->created_at->format('Y-m-d H:i') }}</div>
                @if($hasMore)<i class="fa-solid fa-chevron-down chev text-slate-500 text-xs transition"></i>@endif
            </summary>

            @if($hasMore)
            <div class="border-t border-white/10 p-4 space-y-4 text-xs bg-black/20">
                @if($log->action === 'updated' && !empty($old))
                <div>
                    <p class="font-bold text-amber-300 mb-1.5">گۆڕانکارییەکان</p>
                    <table class="w-full text-right"><thead class="text-slate-400 text-[11px]"><tr><th class="p-1.5">خانە</th><th class="p-1.5">پێش</th><th class="p-1.5">دوای</th></tr></thead>
                    <tbody class="divide-y divide-white/5">
                    @foreach($new as $k => $v)
                        <tr><td class="p-1.5 text-slate-300">{{ $fields[$k] ?? $k }}</td><td class="p-1.5 num text-rose-300">{{ $val($k, $old[$k] ?? null) }}</td><td class="p-1.5 num text-emerald-300">{{ $val($k, $v) }}</td></tr>
                    @endforeach
                    </tbody></table>
                </div>
                @endif

                @if($log->action === 'deleted' && !empty($old))
                <div>
                    <p class="font-bold text-rose-300 mb-1.5">زانیاری سڕاوە</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1">
                    @foreach($old as $k => $v)
                        @if(isset($fields[$k]))<div class="flex justify-between border-b border-white/5 py-1"><span class="text-slate-400">{{ $fields[$k] }}</span><span class="num">{{ $val($k, $v) }}</span></div>@endif
                    @endforeach
                    </div>
                </div>
                @endif

                @foreach(['items_before' => ['کاڵاکان (پێش)', $before, 'text-rose-300'], 'items_after' => ['کاڵاکان (دوای)', $after, 'text-emerald-300']] as $key => [$title, $items, $color])
                    @if(!empty($items) && !($log->action === 'created' && $key === 'items_before'))
                    <div>
                        <p class="font-bold {{ $color }} mb-1.5">{{ $title }}</p>
                        <table class="w-full text-right"><thead class="text-slate-400 text-[11px]"><tr><th class="p-1.5">کاڵا</th><th class="p-1.5">یەکە</th><th class="p-1.5">بڕ</th><th class="p-1.5">نرخ</th><th class="p-1.5">کۆ</th></tr></thead>
                        <tbody class="divide-y divide-white/5">
                        @foreach($items as $it)
                            <tr><td class="p-1.5 font-bold">{{ $it['product'] }}@if(!empty($it['cond']) && $it['cond'] !== 'normal') <span class="text-[10px] text-amber-300">({{ $it['cond'] === 'expired' ? 'بەسەرچوو' : 'تێکچوو' }})</span>@endif</td><td class="p-1.5">{{ $it['unit'] }}</td><td class="p-1.5 num">{{ $it['qty'] }}</td><td class="p-1.5 num">{{ number_format($it['price'], 2) }}</td><td class="p-1.5 num">{{ number_format($it['total'], 2) }}</td></tr>
                        @endforeach
                        </tbody></table>
                    </div>
                    @endif
                @endforeach
                @if($log->ip)<p class="text-[10px] text-slate-500">IP: <span class="num">{{ $log->ip }}</span></p>@endif
            </div>
            @endif
        </details>
    @empty
        <div class="glass rounded-2xl p-10 text-center text-slate-500"><i class="fa-solid fa-inbox text-3xl block mb-2"></i> هیچ چالاکییەک تۆمار نەکراوە</div>
    @endforelse
    </section>

    @if($logs->hasPages())<div>{{ $logs->links() }}</div>@endif
</div>
</body>
</html>