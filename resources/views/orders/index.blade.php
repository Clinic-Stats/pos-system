<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داواکارییەکانی کڕیار</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
    @php
        $fmt = fn($v, $c) => $c === 'USD' ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' IQD';
        $tabs = ['pending' => 'چاوەڕێ', 'accepted' => 'قبوڵکراو', 'rejected' => 'ڕەتکراو', 'all' => 'هەمووی'];
        $pendingNow = (int) ($counts['pending'] ?? 0);
    @endphp
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-3 bg-slate-800 p-4 rounded-2xl border border-slate-700">
        <h1 class="text-xl font-bold flex items-center gap-2 text-white">
            <i class="fa-solid fa-bell-concierge text-cyan-400"></i> داواکارییەکانی کڕیار
        </h1>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="copyLink(this)" data-link="{{ route('order.create') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition">
                <i class="fa-solid fa-link"></i> <span>کۆپیکردنی لینکی داواکاری</span>
            </button>
            <a href="{{ route('order.create') }}" target="_blank" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition"><i class="fa-solid fa-up-right-from-square"></i> کردنەوە</a>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">POS</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-sm font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ئاگادارکردنەوەی داواکاری نوێ --}}
    <div id="newBanner" class="hidden bg-amber-500/20 border border-amber-400 text-amber-300 p-3.5 rounded-xl text-sm font-extrabold flex items-center justify-between gap-2">
        <span><i class="fa-solid fa-bell"></i> داواکاری نوێ هاتووە!</span>
        <a href="{{ route('orders.index') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1.5 rounded-lg text-xs">نوێکردنەوە</a>
    </div>

    {{-- تابەکان --}}
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as $key => $label)
            @php $n = $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0); @endphp
            <a href="{{ route('orders.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-xl text-xs font-extrabold border transition {{ $status === $key ? 'bg-cyan-600 border-cyan-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700' }}">
                {{ $label }} <span class="font-mono mr-1 opacity-80">{{ $n }}</span>
            </a>
        @endforeach
    </div>

    {{-- لیستی داواکارییەکان --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($orders as $o)
            @php
                $debt = $o->customer ? $o->customer->currencySummary() : null;
                $stClass = ['pending' => 'bg-amber-500/20 text-amber-300', 'accepted' => 'bg-emerald-500/20 text-emerald-300', 'rejected' => 'bg-rose-500/20 text-rose-300'][$o->status] ?? '';
                $stLabel = ['pending' => 'چاوەڕێ', 'accepted' => 'قبوڵکراو', 'rejected' => 'ڕەتکراو'][$o->status] ?? $o->status;
            @endphp
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-black text-sm text-white">{{ $o->order_no }}</span>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black {{ $stClass }}">{{ $stLabel }}</span>
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono" dir="ltr">{{ $o->created_at->format('n/j/y, g:i A') }}</span>
                </div>

                {{-- کڕیار --}}
                <div class="bg-slate-900/60 rounded-xl p-3 space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-extrabold text-white">{{ $o->name }}</span>
                        @if($o->customer)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-cyan-500/20 text-cyan-300"><i class="fa-solid fa-user-check"></i> کڕیاری تۆمارکراو</span>
                        @else
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-600/50 text-slate-300">کڕیاری ئاسایی</span>
                        @endif
                        @if($o->claims_regular && !$o->customer)
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-rose-500/20 text-rose-300">وتی هەمیشەییم، بەڵام ژمارەکە نەدۆزرایەوە</span>
                        @endif
                    </div>
                    <div class="text-xs text-cyan-300 font-mono" dir="ltr" style="text-align:right;"><i class="fa-solid fa-phone"></i> {{ $o->phone }}</div>
                    @if($o->address)<div class="text-xs text-slate-400"><i class="fa-solid fa-location-dot"></i> {{ $o->address }}</div>@endif
                    @if($o->latitude && $o->longitude)
                        <a href="https://www.google.com/maps?q={{ $o->latitude }},{{ $o->longitude }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1.5 bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600 hover:text-white px-2.5 py-1 rounded-lg text-[11px] font-extrabold transition">
                            <i class="fa-solid fa-map-location-dot"></i> کردنەوەی شوێن لەسەر نەخشە
                        </a>
                    @endif
                    @if($debt)
                        <div class="text-[11px] text-slate-400 flex flex-wrap gap-x-4 gap-y-0.5 pt-1 border-t border-slate-700">
                            <span>قەرزی ئێستا:</span>
                            @foreach(['USD', 'IQD'] as $cur)
                                <span class="font-mono font-bold {{ $debt[$cur]['debt'] > 0 ? 'text-amber-300' : 'text-emerald-400' }}" dir="ltr">{{ $fmt($debt[$cur]['debt'], $cur) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- کاڵاکان --}}
                <div class="space-y-1">
                    @foreach($o->items as $it)
                        <div class="flex items-center justify-between text-xs bg-slate-900/40 rounded-lg px-3 py-2">
                            <span class="font-bold text-slate-100">{{ $it->product->name ?? '— (کاڵا سڕاوەتەوە)' }}</span>
                            <span class="font-mono font-black text-cyan-300">{{ rtrim(rtrim(number_format((float) $it->quantity, 3), '0'), '.') }} <span class="font-sans font-bold text-slate-400">{{ $it->unit->name ?? '' }}</span></span>
                        </div>
                    @endforeach
                </div>

                @if($o->note)
                    <div class="text-xs bg-amber-500/10 border border-amber-500/30 text-amber-200 rounded-lg px-3 py-2"><i class="fa-solid fa-note-sticky"></i> {{ $o->note }}</div>
                @endif

                {{-- کردارەکان --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if($o->status === 'pending')
                        <a href="{{ route('pos.order', $o->id) }}" class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs py-2.5 rounded-xl transition">
                            <i class="fa-solid fa-check"></i> قبوڵکردن و کردنی بە وەسڵ
                        </a>
                        <form action="{{ route('orders.reject', $o->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە ڕەتکردنەوەی ئەم داواکارییە؟')">
                            @csrf
                            <button type="submit" class="bg-rose-600/20 text-rose-300 border border-rose-500/40 hover:bg-rose-600 hover:text-white px-3 py-2.5 rounded-xl text-xs font-bold transition"><i class="fa-solid fa-xmark"></i> ڕەتکردنەوە</button>
                        </form>
                    @elseif($o->status === 'accepted' && $o->sale_id)
                        <a href="{{ route('sales.print', $o->sale_id) }}?type=small" target="_blank" class="bg-blue-600/20 text-blue-300 border border-blue-500/40 hover:bg-blue-600 hover:text-white px-3 py-2 rounded-xl text-xs font-bold transition"><i class="fa-solid fa-receipt"></i> وەسڵەکە</a>
                    @endif
                    <form action="{{ route('orders.destroy', $o->id) }}" method="POST" onsubmit="return confirm('سڕینەوەی ئەم داواکارییە؟')" class="mr-auto">
                        @csrf @method('DELETE')
                        <button type="submit" title="سڕینەوە" class="text-slate-500 hover:text-rose-400 px-2 py-2 text-xs"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl p-10 text-center text-slate-500 text-sm font-bold">
                <i class="fa-solid fa-inbox text-3xl mb-2 block opacity-50"></i> هیچ داواکارییەک نییە
            </div>
        @endforelse
    </div>
</div>

<script>
    let knownPending = {{ $pendingNow }};
    function copyLink(btn) {
        const link = btn.dataset.link, span = btn.querySelector('span');
        const done = () => { const t = span.textContent; span.textContent = 'کۆپیکرا ✓'; setTimeout(() => span.textContent = t, 1800); };
        if (navigator.clipboard) navigator.clipboard.writeText(link).then(done, () => prompt('لینکەکە کۆپی بکە:', link));
        else prompt('لینکەکە کۆپی بکە:', link);
    }
    function beep() {
        try {
            const c = new (window.AudioContext || window.webkitAudioContext)(), o = c.createOscillator(), g = c.createGain();
            o.connect(g); g.connect(c.destination); o.frequency.value = 880; g.gain.value = 0.08;
            o.start(); setTimeout(() => { o.stop(); c.close(); }, 250);
        } catch (e) {}
    }
    // هەر ١٥ چرکە پشکنینی داواکاری نوێ
    setInterval(() => {
        fetch('{{ route('orders.count') }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                if (d.count > knownPending) { document.getElementById('newBanner').classList.remove('hidden'); beep(); }
                knownPending = d.count;
            }).catch(() => {});
    }, 15000);
</script>
</body>
</html>