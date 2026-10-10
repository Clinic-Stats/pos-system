<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داواکارییەکانی کڕیار</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }

        /* داواکاری نوێ: دێتە ناوەوە، دەدرەوشێتەوە و نیشانەی «نوێ» هەیە */
        .order-new { animation: orderIn .75s cubic-bezier(.2, 1.3, .3, 1) both, orderGlow 2.6s ease-out 2; border-color: #f59e0b !important; }
        .order-new .new-tag { display: inline-block; animation: tagBounce 1.1s ease-in-out infinite; }
        @keyframes orderIn { 0% { opacity: 0; transform: translateY(-28px) scale(.92); } 60% { opacity: 1; transform: translateY(4px) scale(1.02); } 100% { transform: translateY(0) scale(1); } }
        @keyframes orderGlow { 0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, .65); } 70% { box-shadow: 0 0 0 16px rgba(245, 158, 11, 0); } 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); } }
        @keyframes tagBounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }

        /* ئاگادارکردنەوەی سەرەوە */
        .live-toast { animation: toastIn .55s cubic-bezier(.2, 1.4, .3, 1) both; }
        .live-toast i.bell { display: inline-block; transform-origin: 50% 0; animation: bellShake 1s ease-in-out 3; }
        @keyframes toastIn { from { opacity: 0; transform: translate(-50%, -30px) scale(.9); } to { opacity: 1; transform: translate(-50%, 0) scale(1); } }
        @keyframes bellShake { 0%, 100% { transform: rotate(0); } 10% { transform: rotate(22deg); } 20% { transform: rotate(-20deg); } 30% { transform: rotate(16deg); } 40% { transform: rotate(-14deg); } 50% { transform: rotate(8deg); } 60% { transform: rotate(-6deg); } 70% { transform: rotate(0); } }

        .tab-count.bump { animation: cnt .6s ease; }
        @keyframes cnt { 0% { transform: scale(1); } 40% { transform: scale(1.6); color: #fbbf24; } 100% { transform: scale(1); } }
        .live-dot { animation: liveDot 1.6s ease-in-out infinite; }
        @keyframes liveDot { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
        @media (prefers-reduced-motion: reduce) { .order-new, .order-new .new-tag, .live-toast, .live-toast i.bell, .tab-count.bump, .live-dot { animation: none !important; } }
    </style>
    @php
        $tabs = ['pending' => 'چاوەڕێ', 'accepted' => 'قبوڵکراو', 'rejected' => 'ڕەتکراو', 'all' => 'هەمووی'];
    @endphp
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-3 bg-slate-800 p-4 rounded-2xl border border-slate-700">
        <h1 class="text-xl font-bold flex items-center gap-2 text-white">
            <i class="fa-solid fa-bell-concierge text-cyan-400"></i> داواکارییەکانی کڕیار
            <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 rounded-full px-2 py-0.5" title="بەخۆکار نوێ دەبێتەوە">
                <span class="live-dot w-1.5 h-1.5 rounded-full bg-emerald-400"></span> ڕاستەوخۆ
            </span>
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

    {{-- تابەکان --}}
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as $key => $label)
            @php $n = $key === 'all' ? $counts->sum() : ($counts[$key] ?? 0); @endphp
            <a href="{{ route('orders.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-xl text-xs font-extrabold border transition {{ $status === $key ? 'bg-cyan-600 border-cyan-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700' }}">
                {{ $label }} <span class="tab-count font-mono mr-1 opacity-80 inline-block" data-count="{{ $key }}">{{ $n }}</span>
            </a>
        @endforeach
    </div>

    {{-- لیستی داواکارییەکان --}}
    <div id="ordersGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($orders as $o)
            @include('orders.order_card', ['o' => $o])
        @empty
            <div id="emptyState" class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-2xl p-10 text-center text-slate-500 text-sm font-bold">
                <i class="fa-solid fa-inbox text-3xl mb-2 block opacity-50"></i> هیچ داواکارییەک نییە
            </div>
        @endforelse
    </div>
</div>

<div id="toastBox" class="fixed top-4 left-1/2 -translate-x-1/2 z-[1000] pointer-events-none"></div>

<script>
    const STATUS = @json($status);
    const FEED_URL = @json(route('orders.feed'));
    let lastId = {{ (int) $lastId }};
    const grid = document.getElementById('ordersGrid');

    function copyLink(btn) {
        const link = btn.dataset.link, span = btn.querySelector('span');
        const done = () => { const t = span.textContent; span.textContent = 'کۆپیکرا ✓'; setTimeout(() => span.textContent = t, 1800); };
        if (navigator.clipboard) navigator.clipboard.writeText(link).then(done, () => prompt('لینکەکە کۆپی بکە:', link));
        else prompt('لینکەکە کۆپی بکە:', link);
    }

    function beep() {
        try {
            const c = new (window.AudioContext || window.webkitAudioContext)();
            [880, 1175].forEach((f, i) => {
                const o = c.createOscillator(), g = c.createGain();
                o.type = 'sine'; o.frequency.value = f; o.connect(g); g.connect(c.destination);
                const t = c.currentTime + i * 0.16;
                g.gain.setValueAtTime(0.0001, t); g.gain.exponentialRampToValueAtTime(0.12, t + 0.02); g.gain.exponentialRampToValueAtTime(0.0001, t + 0.22);
                o.start(t); o.stop(t + 0.25);
            });
            setTimeout(() => c.close(), 700);
        } catch (e) {}
    }

    function notify(n) {
        const box = document.getElementById('toastBox');
        const el = document.createElement('div');
        el.className = 'live-toast pointer-events-auto flex items-center gap-2.5 bg-gradient-to-r from-amber-500 to-rose-500 text-white font-extrabold text-sm px-5 py-3 rounded-2xl shadow-2xl';
        el.style.cssText = 'position:fixed;top:1rem;left:50%;transform:translateX(-50%)';
        el.innerHTML = '<i class="bell fa-solid fa-bell text-lg"></i><span>' + (n > 1 ? n + ' داواکاری نوێ هاتن!' : 'داواکاری نوێ هات!') + '</span>';
        box.appendChild(el);
        setTimeout(() => { el.style.transition = 'opacity .4s, transform .4s'; el.style.opacity = '0'; el.style.transform = 'translate(-50%,-20px)'; setTimeout(() => el.remove(), 450); }, 4500);
        beep();
    }

    function setCounts(c) {
        const set = (k, v) => {
            const el = document.querySelector('[data-count="' + k + '"]'); if (!el) return;
            if (el.textContent.trim() !== String(v)) { el.textContent = v; el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); }
        };
        set('pending', c.pending); set('accepted', c.accepted); set('rejected', c.rejected); set('all', c.pending + c.accepted + c.rejected);
        document.title = (c.pending > 0 ? '(' + c.pending + ') ' : '') + 'داواکارییەکانی کڕیار';
    }

    async function poll() {
        if (document.hidden) return;
        try {
            const r = await fetch(FEED_URL + '?after=' + lastId, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return;
            const d = await r.json();
            setCounts(d.counts);
            if (d.last_id > lastId) lastId = d.last_id;
            if (d.new_count > 0) {
                if (STATUS === 'pending' || STATUS === 'all') {
                    const tmp = document.createElement('div'); tmp.innerHTML = d.html;
                    const empty = document.getElementById('emptyState'); if (empty) empty.remove();
                    [...tmp.children].forEach(card => {
                        card.classList.add('order-new');
                        card.querySelector('.new-tag')?.classList.remove('hidden');
                        grid.prepend(card);
                        setTimeout(() => { card.classList.remove('order-new'); card.querySelector('.new-tag')?.classList.add('hidden'); }, 12000);
                    });
                }
                notify(d.new_count);
            }
        } catch (e) { /* هێڵی ئینتەرنێت: جارێکی تر هەوڵ دەدرێتەوە */ }
    }
    setInterval(poll, 4000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
</script>
</body>
</html>