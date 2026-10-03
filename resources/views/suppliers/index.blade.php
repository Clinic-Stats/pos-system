<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی دابینکەران</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @php $isEmbedded = request()->has('embedded'); @endphp
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; background: radial-gradient(900px 400px at 90% -10%, #123a3a 0%, transparent 60%), #0a111d; }
        html, body { overflow-x: hidden !important; max-width: 100% !important; }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); }
        .num { font-variant-numeric: tabular-nums; direction: ltr; unicode-bidi: isolate; }
        .inp { width: 100%; padding: .6rem .75rem; border-radius: .75rem; background: rgba(15,23,42,.7); border: 1px solid rgba(255,255,255,.1); color: #fff; font-size: 13px; }
        .inp:focus, button:focus-visible, a:focus-visible { outline: 2px solid #2dd4bf; outline-offset: 1px; }
        ::-webkit-scrollbar { width: 6px; height: 6px; } ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        @if($isEmbedded) body { padding: 10px !important; } @endif
    </style>
</head>
@php
    $usd = fn($v) => '$' . number_format($v, 2);
    $iqd = fn($v) => number_format($v) . ' IQD';
@endphp
<body class="text-slate-100 min-h-screen p-6">
<div class="max-w-6xl mx-auto space-y-5">

    <header class="glass rounded-2xl p-4 flex flex-wrap justify-between items-center gap-3">
        <h1 class="text-base font-extrabold flex items-center gap-2"><i class="fa-solid fa-truck-field text-teal-400"></i> بەڕێوەبردنی دابینکەران</h1>
        @if(!$isEmbedded)
        <div class="flex items-center gap-2 flex-wrap text-xs font-bold">
            <a href="{{ route('reports.index') }}" class="glass hover:bg-white/10 px-3 py-2 rounded-xl"><i class="fa-solid fa-chart-pie"></i> ڕاپۆرتەکان</a>
            <a href="{{ route('purchases.create') }}" class="bg-teal-600/80 hover:bg-teal-600 px-3 py-2 rounded-xl"><i class="fa-solid fa-cart-flatbed"></i> وەسڵی کڕین</a>
            <a href="{{ route('pos.index') }}" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-4 py-2 rounded-xl font-extrabold"><i class="fa-solid fa-cash-register"></i> POS</a>
        </div>
        @endif
    </header>

    @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-3 rounded-xl text-xs font-bold"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold space-y-1">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- فۆڕمی نوێ -->
        <section class="glass rounded-2xl p-5 space-y-4 h-fit">
            <h2 class="text-sm font-extrabold flex items-center gap-2 border-b border-white/10 pb-3"><i class="fa-solid fa-square-plus text-teal-400"></i> زیادکردنی دابینکەری نوێ</h2>
            <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-3 text-xs" autocomplete="off">
                @csrf
                <div><label class="block text-slate-300 font-bold mb-1">ناوی دابینکەر / کۆمپانیا:</label><input type="text" name="name" required autofocus placeholder="بۆ نموونە: Active Halabja" class="inp"></div>
                <div><label class="block text-slate-300 font-bold mb-1">ژمارەی مۆبایل:</label><input type="text" name="phone" placeholder="07XX XXX XXXX" class="inp num"></div>
                <div><label class="block text-slate-300 font-bold mb-1">ناونیشان:</label><input type="text" name="address" placeholder="شار، گەڕەک..." class="inp"></div>
                <div><label class="block text-slate-300 font-bold mb-1">تێبینی:</label><textarea name="note" rows="2" placeholder="تێبینی زیاتر..." class="inp resize-none"></textarea></div>
                <button type="submit" class="w-full bg-teal-500 hover:bg-teal-400 text-slate-900 font-extrabold py-3 rounded-xl text-sm"><i class="fa-solid fa-check"></i> تۆمارکردنی دابینکەر</button>
            </form>
        </section>

        <!-- لیست -->
        <section class="lg:col-span-2 glass rounded-2xl p-5 space-y-4">
            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                <h2 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-list-check text-sky-400"></i> لیستی دابینکەران</h2>
                <span class="num text-[10px] font-bold bg-white/10 px-2.5 py-1 rounded-lg">{{ $suppliers->count() }}</span>
            </div>
            <div class="overflow-x-auto rounded-xl border border-white/10">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-white/5 text-[11px] text-slate-400"><tr>
                        <th class="p-3">#</th><th class="p-3">ناو</th><th class="p-3">مۆبایل</th><th class="p-3">قەرزی ئێمە</th><th class="p-3 text-center">کردارەکان</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/5">
                    @forelse($suppliers as $i => $s)
                        <tr class="hover:bg-white/5">
                            <td class="p-3 num text-slate-500">{{ $i + 1 }}</td>
                            <td class="p-3 font-bold text-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-teal-400 to-emerald-600 flex items-center justify-center text-slate-900 text-[10px] font-black">{{ mb_substr($s->name, 0, 1) }}</div>
                                    <div>{{ $s->name }}<div class="text-[10px] text-slate-500 font-normal">{{ $s->address ?? '' }}</div></div>
                                </div>
                            </td>
                            <td class="p-3 num text-slate-400">{{ $s->phone ?? '-' }}</td>
                            <td class="p-3">
                                @if($s->balances['USD'] > 0.004 || $s->balances['IQD'] > 0.5)
                                    @if($s->balances['USD'] > 0.004)<div class="num font-bold text-amber-400">{{ $usd($s->balances['USD']) }}</div>@endif
                                    @if($s->balances['IQD'] > 0.5)<div class="num font-bold text-amber-400">{{ $iqd($s->balances['IQD']) }}</div>@endif
                                @else
                                    <span class="text-emerald-400 font-bold text-[11px]"><i class="fa-solid fa-check"></i> پاکە</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if(!$isEmbedded)
                                    <a href="{{ route('suppliers.statement', $s->id) }}" title="کەشفی حساب" class="bg-teal-500/20 hover:bg-teal-500 text-teal-300 hover:text-slate-900 px-2.5 py-1.5 rounded-lg text-[10px] font-extrabold transition"><i class="fa-solid fa-file-invoice"></i> کەشفی حساب</a>
                                    @endif
                                    <button type="button" onclick='openEditSupplierModal(@json($s))' title="دەستکاری" class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-slate-900 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم دابینکەرە؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="سڕینەوە" class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-500"><i class="fa-solid fa-inbox text-2xl block mb-2"></i> هیچ دابینکەرێک تۆمار نەکراوە</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<!-- مۆداڵی دەستکاری -->
<div id="editSupplierModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-5 space-y-4 shadow-2xl text-xs">
        <div class="flex justify-between items-center border-b border-white/10 pb-3">
            <h3 class="text-sm font-extrabold"><i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی دابینکەر</h3>
            <button type="button" onclick="closeEditSupplierModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>
        <form id="editSupplierForm" method="POST" class="space-y-3" autocomplete="off">
            @csrf @method('PUT')
            <div><label class="block font-bold text-slate-300 mb-1">ناوی دابینکەر:</label><input type="text" name="name" id="e_name" required class="inp"></div>
            <div><label class="block font-bold text-slate-300 mb-1">ژمارەی مۆبایل:</label><input type="text" name="phone" id="e_phone" class="inp num"></div>
            <div><label class="block font-bold text-slate-300 mb-1">ناونیشان:</label><input type="text" name="address" id="e_address" class="inp"></div>
            <div><label class="block font-bold text-slate-300 mb-1">تێبینی:</label><textarea name="note" id="e_note" rows="2" class="inp resize-none"></textarea></div>
            <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
                <button type="button" onclick="closeEditSupplierModal()" class="bg-slate-700 hover:bg-slate-600 font-bold px-4 py-2 rounded-xl">داخستن</button>
                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold px-5 py-2 rounded-xl"><i class="fa-solid fa-check"></i> نوێکردنەوە</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditSupplierModal(s) {
        document.getElementById('editSupplierForm').action = '/suppliers/' + s.id;
        document.getElementById('e_name').value = s.name || '';
        document.getElementById('e_phone').value = s.phone || '';
        document.getElementById('e_address').value = s.address || '';
        document.getElementById('e_note').value = s.note || '';
        document.getElementById('editSupplierModal').classList.remove('hidden');
    }
    function closeEditSupplierModal() { document.getElementById('editSupplierModal').classList.add('hidden'); }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditSupplierModal(); });
</script>
</body>
</html>