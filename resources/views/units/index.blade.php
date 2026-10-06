<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی یەکەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Almarai', sans-serif; background: radial-gradient(900px 400px at 90% -10%, #123a3a 0%, transparent 60%), #0a111d; }
        .num { font-family: 'Plus Jakarta Sans', sans-serif; direction: ltr; unicode-bidi: isolate; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); }
        input:focus, button:focus-visible { outline: 2px solid #2dd4bf; outline-offset: 1px; }
    </style>
    @include('partials.system-head')
</head>
@php
    $kindOf = function ($name) {
        $n = mb_strtolower(trim($name));
        if (str_contains($n, 'کارتۆن') || str_contains($n, 'carton')) return 'carton';
        if (str_contains($n, 'تەن') || str_contains($n, 'ton')) return 'ton';
        return 'normal';
    };
    $inp = 'w-full p-2.5 rounded-xl bg-slate-900/70 border border-white/10 text-white text-sm';
@endphp
<body class="text-slate-100 min-h-screen p-4 md:p-6">
<div class="max-w-6xl mx-auto space-y-5">

    <header class="glass rounded-2xl p-4 flex flex-wrap justify-between items-center gap-3">
        <h1 class="text-lg font-extrabold flex items-center gap-2"><i class="fa-solid fa-scale-balanced text-teal-400"></i> بەڕێوەبردنی یەکەکان</h1>
        <a href="{{ route('pos.index') }}" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-4 py-2 rounded-xl transition text-xs font-extrabold"><i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ POS</a>
    </header>

    @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-3 rounded-xl text-sm font-bold"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-sm font-bold">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold space-y-1">@foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach</div>@endif

    <!-- ڕوونکردنەوە -->
    <section class="glass rounded-2xl p-4 text-xs leading-7 text-slate-300">
        <b class="text-white block mb-1"><i class="fa-solid fa-circle-info text-teal-400"></i> چۆن کار دەکات؟</b>
        <div><b class="text-emerald-400">کیلۆ و گۆنی و هەر یەکەیەکی ئاسایی:</b> ژمارەی کێشی بە کیلۆ دەنووسیت (کیلۆ = 1، گۆنی ٥٠ کگ = 50).</div>
        <div><b class="text-amber-400">کارتۆن:</b> ناوەکەی پێویستە وشەی «کارتۆن» تێدا بێت. کێشەکەی لە ناو <u>هەر کاڵایەک</u> دیاری دەکرێت (٥، ١٠، ١٢ کگ...) لە لاپەڕەی کاڵاکان، بۆیە لێرە ژمارە ناپرسێت.</div>
        <div><b class="text-sky-400">تەن:</b> ناوەکەی پێویستە وشەی «تەن» تێدا بێت و خۆکارانە ١٠٠٠ کگ دەبێت.</div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- لیست -->
        <section class="glass rounded-2xl p-5 space-y-3">
            <h2 class="font-extrabold flex items-center gap-2 border-b border-white/10 pb-3"><i class="fa-solid fa-list text-teal-400"></i> لیستی یەکەکان</h2>
            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                @forelse($units as $unit)
                @php $k = $kindOf($unit->name); @endphp
                <div class="bg-slate-900/50 p-3 rounded-xl border border-white/5 flex justify-between items-center gap-2 hover:border-teal-500/40 transition">
                    <div>
                        <h3 class="font-bold text-sm">{{ $unit->name }}</h3>
                        <p class="text-[11px] mt-0.5 text-slate-400">
                            @if($k === 'carton') <span class="text-amber-400 font-bold">کێشەکەی لە ناو هەر کاڵایەکدایە</span>
                            @elseif($k === 'ton') <span class="text-sky-400 font-bold num">1000</span> کیلۆ (خۆکار)
                            @else بەرامبەر بە: <span class="num text-emerald-400 font-bold">{{ $unit->factor_to_base ?? 1 }}</span> کیلۆ @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="openEditUnitModal({{ json_encode($unit) }})" class="bg-amber-500/15 hover:bg-amber-500 text-amber-400 hover:text-slate-900 px-3 py-1.5 rounded-lg text-xs font-bold transition"><i class="fa-solid fa-pen-to-square"></i> دەستکاری</button>
                        <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم یەکەیە؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-rose-500/15 hover:bg-rose-500 text-rose-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-500 text-xs"><i class="fa-solid fa-inbox text-2xl block mb-2"></i> هیچ یەکەیەک تۆمار نەکراوە</div>
                @endforelse
            </div>
        </section>

        <!-- زیادکردن -->
        <section class="glass rounded-2xl p-5 space-y-4 h-fit">
            <h2 class="font-extrabold flex items-center gap-2 border-b border-white/10 pb-3"><i class="fa-solid fa-square-plus text-emerald-400"></i> زیادکردنی یەکەی نوێ</h2>
            <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-slate-300 text-xs font-bold mb-1.5">ناوی یەکە (کیلۆ، کارتۆن، تەن، گۆنی...):</label>
                    <input type="text" name="name" required autofocus oninput="syncFactor(this, 'add_factor_box', 'add_factor', 'add_hint')" class="{{ $inp }}">
                </div>
                <div id="add_factor_box">
                    <label class="block text-slate-300 text-xs font-bold mb-1.5">کێشی بە کیلۆ:</label>
                    <input type="number" step="any" min="0.0001" name="factor_to_base" id="add_factor" placeholder="نموونە: کیلۆ 1، گۆنی 50" class="{{ $inp }} num">
                </div>
                <p id="add_hint" class="text-[11px] text-amber-300 hidden"></p>
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-extrabold py-3 rounded-xl transition text-sm"><i class="fa-solid fa-check"></i> تۆمارکردن</button>
            </form>
        </section>
    </div>
</div>

<!-- مۆداڵی دەستکاری -->
<div id="editUnitModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl text-xs">
        <div class="flex justify-between items-center border-b border-white/10 pb-3">
            <h3 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی یەکە</h3>
            <button type="button" onclick="closeEditUnitModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>
        <form id="editUnitForm" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block font-bold text-slate-300 mb-1.5">ناوی یەکە:</label>
                <input type="text" name="name" id="edit_unit_name" required oninput="syncFactor(this, 'edit_factor_box', 'edit_unit_factor', 'edit_hint')" class="{{ $inp }}">
            </div>
            <div id="edit_factor_box">
                <label class="block font-bold text-slate-300 mb-1.5">کێشی بە کیلۆ:</label>
                <input type="number" step="any" min="0.0001" name="factor_to_base" id="edit_unit_factor" class="{{ $inp }} num">
            </div>
            <p id="edit_hint" class="text-[11px] text-amber-300 hidden"></p>
            <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                <button type="button" onclick="closeEditUnitModal()" class="bg-slate-700 hover:bg-slate-600 font-bold px-4 py-2 rounded-xl">داخستن</button>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold px-5 py-2 rounded-xl"><i class="fa-solid fa-check"></i> نوێکردنەوە</button>
            </div>
        </form>
    </div>
</div>

<script>
    // کارتۆن و تەن: خانەی کێش دەشاردرێتەوە چونکە خۆکارە
    function syncFactor(nameInput, boxId, factorId, hintId) {
        const n = nameInput.value.toLowerCase(), box = document.getElementById(boxId), f = document.getElementById(factorId), hint = document.getElementById(hintId);
        const carton = n.includes('کارتۆن') || n.includes('carton');
        const ton = !carton && (n.includes('تەن') || n.includes('ton'));
        box.classList.toggle('hidden', carton || ton);
        f.required = !(carton || ton);
        hint.classList.toggle('hidden', !(carton || ton));
        hint.textContent = carton ? 'کێشی کارتۆن لە ناو هەر کاڵایەک دیاری دەکرێت (لە لاپەڕەی کاڵاکان).' : ton ? 'تەن خۆکارانە ١٠٠٠ کیلۆ دەبێت.' : '';
    }
    function openEditUnitModal(unit) {
        const n = document.getElementById('edit_unit_name');
        n.value = unit.name || '';
        document.getElementById('edit_unit_factor').value = unit.factor_to_base || 1;
        document.getElementById('editUnitForm').action = '/units/' + unit.id;
        syncFactor(n, 'edit_factor_box', 'edit_unit_factor', 'edit_hint');
        document.getElementById('editUnitModal').classList.remove('hidden');
    }
    function closeEditUnitModal() { document.getElementById('editUnitModal').classList.add('hidden'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditUnitModal(); });
</script>
</body>
</html>