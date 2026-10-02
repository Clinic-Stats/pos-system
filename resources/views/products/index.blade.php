<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بەڕێوەبردنی کاڵاکان و کۆگا</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body{font-family:'Almarai',sans-serif;background:radial-gradient(900px 400px at 90% -10%,#123a3a 0%,transparent 60%),#0a111d}
.num{font-family:'Plus Jakarta Sans',sans-serif;direction:ltr;unicode-bidi:isolate}
.glass{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)}
.inp{width:100%;padding:.5rem .65rem;border-radius:.7rem;background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.1);color:#fff;font-size:12px}
.inp:focus,button:focus-visible,a:focus-visible{outline:2px solid #2dd4bf;outline-offset:1px}
.lbl{display:block;color:#cbd5e1;font-size:11px;font-weight:700;margin-bottom:.25rem}
.sc::-webkit-scrollbar{width:5px;height:5px}.sc::-webkit-scrollbar-thumb{background:#334155;border-radius:9px}
.cur.on{background:#14b8a6;color:#042f2e}
</style>
</head>
@php
    $outCount = $products->where('stock_kg', '<=', 0)->count();
    $lowCount = $products->filter(fn($p) => $p->stock_kg > 0 && $p->stock_kg <= ($p->alert_quantity ?? 5))->count();
    $rate = $setting->exchange_rate ?? 1500;
    $curBox = function ($p) { return '
        <div class="flex items-center justify-between bg-white/5 p-1.5 rounded-lg border border-white/10">
            <span class="text-[10px] font-bold text-slate-300">دراوی نرخ:</span>
            <div class="flex gap-1">
                <button type="button" id="'.$p.'-usd" onclick="setCur(\''.$p.'\',\'USD\')" class="cur on px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">دۆلار</button>
                <button type="button" id="'.$p.'-iqd" onclick="setCur(\''.$p.'\',\'IQD\')" class="cur px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">دینار</button>
            </div>
        </div>'; };
@endphp
<body class="text-slate-100 min-h-screen lg:h-screen flex flex-col p-3 gap-3 lg:overflow-hidden">

<header class="shrink-0 glass rounded-2xl p-3 flex flex-wrap justify-between items-center gap-2 text-xs">
    <h1 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-boxes-stacked text-teal-400"></i> بەڕێوەبردنی کاڵاکان و کۆگا</h1>
    <div class="flex flex-wrap items-center gap-1.5 font-bold">
        <a href="{{ route('categories.index') }}" class="bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">کاتیگۆری</a>
        <a href="{{ route('units.index') }}" class="bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">یەکەکان</a>
        <a href="{{ route('reports.index') }}" class="bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">ڕاپۆرتەکان</a>
        <a href="{{ route('export.products') }}" class="bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 px-3 py-1.5 rounded-lg transition"><i class="fa-solid fa-file-excel"></i> هەناردە</a>
        <button type="button" onclick="toggleModal('importProductModal')" class="bg-teal-500/20 text-teal-300 hover:bg-teal-500/30 px-3 py-1.5 rounded-lg transition"><i class="fa-solid fa-file-import"></i> هاوردەکردن</button>
        <a href="{{ route('pos.index') }}" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-4 py-1.5 rounded-lg transition font-extrabold">POS</a>
    </div>
</header>

<div class="shrink-0 space-y-2 empty:hidden">
    @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-2 rounded-lg text-xs font-bold"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>@endif
    @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold space-y-1">@foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach</div>@endif
</div>

<section class="shrink-0 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
    @foreach([['out','کاڵای نەماو (سفر)',$outCount,'rose','fa-circle-xmark'],['low','کاڵای کەمبووەوە',$lowCount,'amber','fa-triangle-exclamation'],['all','کۆی گشتی کاڵاکان',$products->count(),'emerald','fa-boxes-packing']] as [$st,$t,$n,$c,$ic])
    <button type="button" onclick="filterByStockState('{{ $st }}')" class="glass rounded-xl p-3 flex justify-between items-center text-right hover:border-{{ $c }}-400/60 transition">
        <div><span class="text-[10px] text-{{ $c }}-300 font-bold block">{{ $t }}</span><span class="num text-lg font-extrabold text-{{ $c }}-400">{{ $n }}</span></div>
        <div class="w-9 h-9 rounded-lg bg-{{ $c }}-500/15 text-{{ $c }}-400 flex items-center justify-center"><i class="fa-solid {{ $ic }}"></i></div>
    </button>
    @endforeach
</section>

<main class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-3 min-h-0 lg:overflow-hidden">

    <!-- فۆڕمی کاڵای نوێ -->
    <section class="glass rounded-2xl p-3 flex flex-col lg:h-full lg:overflow-hidden">
        <h2 class="shrink-0 text-sm font-extrabold flex items-center gap-2 mb-2 border-b border-white/10 pb-2"><i class="fa-solid fa-square-plus text-teal-400"></i> زیادکردنی کاڵای نوێ</h2>
        <form action="{{ route('products.store') }}" method="POST" id="productForm" class="flex-1 overflow-y-auto sc pr-1 space-y-2.5">
            @csrf
            <div><label class="lbl">ناوی کاڵا:</label><input type="text" name="name" id="field_name" required autofocus class="enter-nav inp"></div>
            <div><label class="lbl">کۆد یان بارکۆد:</label><input type="text" name="code" id="field_code" required class="enter-nav inp num"></div>
            <div>
                <div class="flex justify-between items-center mb-1"><label class="lbl !mb-0">کاتیگۆری:</label><button type="button" onclick="openQuickCategory()" class="text-[10px] text-teal-400 font-bold"><i class="fa-solid fa-plus-circle"></i> نوێ</button></div>
                <select name="category_id" id="field_category" required class="enter-nav inp">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
            </div>

            {!! $curBox('f') !!}
            <input type="hidden" name="currency" id="f_currency" value="USD">
            <input type="hidden" name="exchange_rate" value="{{ $rate }}">

            <div class="grid grid-cols-2 gap-2">
                <div><label class="lbl">نرخی کڕین (<span id="f-lb">$</span>) بۆ ١ کیلۆ:</label><input type="number" step="any" min="0" name="base_buy_price" id="field_buy_price" required class="enter-nav inp num"></div>
                <div><label class="lbl">نرخی فرۆشتن (<span id="f-ls">$</span>) بۆ ١ کیلۆ:</label><input type="number" step="any" min="0" name="base_sale_price" id="field_sale_price" required class="enter-nav inp num"></div>
            </div>
            <p id="f-hint" class="text-[10px] text-emerald-300 bg-emerald-500/10 p-2 rounded-lg border border-emerald-500/20">نرخەکان بە دۆلار پاشەکەوت دەکرێن</p>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="lbl"><i class="fa-solid fa-box text-amber-400"></i> کێشی ١ کارتۆن (کگ):</label>
                    <input type="number" step="any" min="0.01" name="kg_per_carton" id="field_carton" value="1" required class="enter-nav inp num">
                </div>
                <div><label class="lbl">بڕی سەرەتایی (کگ):</label><input type="number" step="any" min="0" name="stock_kg" id="field_stock" value="0" required class="enter-nav inp num"></div>
            </div>
            <p class="text-[10px] text-amber-300 bg-amber-500/10 p-2 rounded-lg border border-amber-500/20">نموونە: ئەگەر کارتۆنی ئەم کاڵایە ١٠ کیلۆیە، 10 بنووسە. بۆ فرۆشتن بە کارتۆن پێویستە.</p>

            <label class="flex items-center justify-between bg-white/5 p-2 rounded-lg border border-white/10 cursor-pointer text-[11px]">
                <span class="font-bold text-slate-300">دۆخی کاڵا:</span>
                <span class="flex items-center gap-1.5"><input type="checkbox" name="is_active" id="field_is_active" value="1" checked class="w-3.5 h-3.5"><span class="text-emerald-400 font-bold">چالاک بێت</span></span>
            </label>
            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-400 text-slate-900 font-extrabold py-2.5 rounded-xl transition text-xs"><i class="fa-solid fa-check"></i> تۆمارکردنی کاڵا</button>
        </form>
    </section>

    <!-- خشتەی کاڵاکان -->
    <section class="lg:col-span-2 glass rounded-2xl p-3 flex flex-col lg:h-full lg:overflow-hidden h-[75vh]">
        <div class="shrink-0 flex flex-wrap justify-between items-center gap-2 border-b border-white/10 pb-2 mb-2">
            <h2 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-warehouse text-teal-400"></i> لیستی مەخزەنی کاڵاکان</h2>
            <span id="productCountBadge" class="num text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded text-slate-300">کۆی کاڵاکان: {{ $products->count() }}</span>
        </div>

        <div class="shrink-0 grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2">
            <input type="text" id="stockSearchInput" onkeyup="filterStockTable()" placeholder="گەڕان بە ناو یان کۆد..." class="inp">
            <select id="stockCategoryFilter" onchange="filterStockTable()" class="inp"><option value="all">هەموو کاتیگۆرییەکان</option>@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select>
            <select id="stockStatusFilter" onchange="filterStockTable()" class="inp">
                <option value="all">هەموو ئاستەکانی کۆگا</option><option value="low">کەمبووەکان (≤ 5)</option><option value="out">نەماوەکان (0)</option><option value="active">چالاکەکان</option><option value="inactive">ناچالاکەکان</option>
            </select>
        </div>

        <div class="flex-1 overflow-auto sc rounded-xl border border-white/10">
            <table class="w-full text-xs text-right text-slate-300">
                <thead class="bg-[#0f1a2e] text-[11px] text-slate-400 sticky top-0 z-10">
                    <tr><th class="p-2">کۆد / ناو</th><th class="p-2">کاتیگۆری</th><th class="p-2">نرخی کڕین</th><th class="p-2">نرخی فرۆشتن</th><th class="p-2">کارتۆن</th><th class="p-2">مەخزەن (کگ)</th><th class="p-2 text-center">دۆخ</th><th class="p-2 text-center">کردار</th></tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                @forelse($products as $p)
                    @php $stock = (float) $p->stock_kg; $isOut = $stock <= 0; $isLow = !$isOut && $stock <= ($p->alert_quantity ?? 5); $carton = (float) ($p->kg_per_carton ?? 1); @endphp
                    <tr class="product-row hover:bg-white/5 transition {{ $isOut ? 'bg-rose-500/5' : ($isLow ? 'bg-amber-500/5' : '') }} {{ !$p->is_active ? 'opacity-60' : '' }}"
                        data-name="{{ mb_strtolower($p->name) }}" data-code="{{ mb_strtolower($p->code) }}" data-category="{{ $p->category_id }}" data-stock="{{ $stock }}" data-active="{{ $p->is_active ? '1' : '0' }}">
                        <td class="p-2">
                            <div class="font-bold text-white text-[11px] flex items-center gap-1.5">{{ $p->name }}
                                @if($isOut)<span class="text-[8px] font-extrabold bg-rose-600 text-white px-1 rounded">نەماوە</span>@elseif($isLow)<span class="text-[8px] font-extrabold bg-amber-600 text-white px-1 rounded">کەمە</span>@endif
                            </div>
                            <span class="num text-[10px] text-teal-400">{{ $p->code }}</span>
                        </td>
                        <td class="p-2 text-[11px] text-slate-400">{{ $p->category->name ?? '-' }}</td>
                        <td class="p-2"><div class="num font-bold text-sky-300 text-[11px]">${{ number_format($p->base_buy_price, 2) }}</div><div class="num text-[9px] text-slate-500">≈ {{ number_format($p->base_buy_price * $rate) }} IQD</div></td>
                        <td class="p-2"><div class="num font-bold text-emerald-400 text-[11px]">${{ number_format($p->base_sale_price, 2) }}</div><div class="num text-[9px] text-slate-500">≈ {{ number_format($p->base_sale_price * $rate) }} IQD</div></td>
                        <td class="p-2">
                            <span class="num text-[11px] font-bold {{ $carton <= 1 ? 'text-amber-400' : 'text-slate-200' }}">{{ rtrim(rtrim(number_format($carton, 2), '0'), '.') }} کگ</span>
                            @if($carton <= 1)<div class="text-[9px] text-amber-400">دیاری نەکراوە</div>@endif
                        </td>
                        <td class="p-2 num font-bold text-[11px] {{ $isOut ? 'text-rose-400' : ($isLow ? 'text-amber-400' : 'text-slate-200') }}">{{ $p->stock_kg }}</td>
                        <td class="p-2 text-center">
                            <form action="{{ route('products.toggle', $p->id) }}" method="POST">@csrf @method('PATCH')
                                <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold transition {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-600 text-slate-400' }}">{{ $p->is_active ? 'چالاک' : 'ناچالاک' }}</button>
                            </form>
                        </td>
                        <td class="p-2 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick='openEditProductModal(@json($p))' class="bg-amber-500/15 hover:bg-amber-500 text-amber-400 hover:text-slate-900 px-2 py-1 rounded text-[10px] font-bold transition"><i class="fa-solid fa-pen-to-square"></i> دەستکاری</button>
                                @if($p->stock_kg > 0)
                                    <span class="text-[10px] bg-white/5 text-slate-500 px-1.5 py-1 rounded border border-white/10" title="ستۆکی تێدایە"><i class="fa-solid fa-lock text-[9px]"></i></span>
                                @else
                                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">@csrf @method('DELETE')
                                        <button type="submit" class="bg-rose-500/15 hover:bg-rose-500 text-rose-400 hover:text-white px-2 py-1 rounded text-[10px] font-bold transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-slate-500">هیچ کاڵایەک تۆمار نەکراوە</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- هاوردەکردن -->
<div id="importProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs">
        <div class="flex justify-between items-center border-b border-white/10 pb-2"><h3 class="text-sm font-extrabold"><i class="fa-solid fa-file-csv text-teal-400"></i> هاوردەکردنی کاڵاکان</h3><button type="button" onclick="toggleModal('importProductModal')" class="text-slate-400 hover:text-white text-lg">&times;</button></div>
        <div class="bg-black/30 p-2.5 rounded-lg space-y-1 text-[11px] text-slate-300">
            <span class="font-bold text-teal-400 block">ستوونەکانی CSV (نرخەکان بە دۆلار):</span>
            <div class="num bg-black/40 p-1.5 rounded text-[10px] text-amber-300 text-left">code, name, base_buy_price, base_sale_price, stock_kg, kg_per_carton</div>
            <span class="text-slate-500">دوو ستوونی کۆتایی ئارەزوومەندانەن.</span>
        </div>
        <form action="{{ route('products.importCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-3">@csrf
            <input type="file" name="csv_file" accept=".csv,.txt" required class="inp !p-1.5 text-[10px]">
            <div><label class="lbl">کاتیگۆری:</label><select name="category_id" required class="inp">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
            <div class="flex justify-end gap-2 pt-2 border-t border-white/10"><button type="button" onclick="toggleModal('importProductModal')" class="bg-slate-700 font-bold px-3 py-1.5 rounded-lg">داخستن</button><button type="submit" class="bg-teal-500 text-slate-900 font-extrabold px-4 py-1.5 rounded-lg">دەستپێکردن</button></div>
        </form>
    </div>
</div>

<!-- دەستکاری -->
<div id="editProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs max-h-[95vh] overflow-y-auto sc">
        <div class="flex justify-between items-center border-b border-white/10 pb-2"><h3 class="text-sm font-extrabold"><i class="fa-solid fa-box-open text-amber-400"></i> دەستکاریکردنی کاڵا</h3><button type="button" onclick="toggleModal('editProductModal')" class="text-slate-400 hover:text-white text-lg">&times;</button></div>
        <form id="editProductForm" method="POST" class="space-y-2.5">@csrf @method('PUT')
            <div><label class="lbl">ناوی کاڵا:</label><input type="text" name="name" id="edit_name" required class="inp"></div>
            <div><label class="lbl">کۆد یان بارکۆد:</label><input type="text" name="code" id="edit_code" required class="inp num"></div>
            <div><label class="lbl">کاتیگۆری:</label><select name="category_id" id="edit_category_id" required class="inp">@foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach</select></div>
            {!! $curBox('e') !!}
            <input type="hidden" name="currency" id="e_currency" value="USD">
            <input type="hidden" name="exchange_rate" value="{{ $rate }}">
            <div class="grid grid-cols-2 gap-2">
                <div><label class="lbl">نرخی کڕین (<span id="e-lb">$</span>):</label><input type="number" step="any" min="0" name="base_buy_price" id="edit_buy_price" required class="inp num"></div>
                <div><label class="lbl">نرخی فرۆشتن (<span id="e-ls">$</span>):</label><input type="number" step="any" min="0" name="base_sale_price" id="edit_sale_price" required class="inp num"></div>
            </div>
            <p id="e-hint" class="text-[10px] text-emerald-300 bg-emerald-500/10 p-2 rounded-lg border border-emerald-500/20">نرخەکان بە دۆلار پاشەکەوت دەکرێن</p>
            <div><label class="lbl"><i class="fa-solid fa-box text-amber-400"></i> کێشی ١ کارتۆن (کگ):</label><input type="number" step="any" min="0.01" name="kg_per_carton" id="edit_carton" required class="inp num"></div>
            <label class="flex items-center gap-2 bg-white/5 p-2 rounded-lg cursor-pointer"><input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-3.5 h-3.5"><span class="text-slate-300 font-bold text-[11px]">چالاک بێت لە شاشەی POS</span></label>
            <div class="flex justify-end gap-2 pt-2 border-t border-white/10"><button type="button" onclick="toggleModal('editProductModal')" class="bg-slate-700 font-bold px-3 py-1.5 rounded-lg">داخستن</button><button type="submit" class="bg-amber-500 text-slate-900 font-extrabold px-4 py-1.5 rounded-lg">نوێکردنەوە</button></div>
        </form>
    </div>
</div>

<!-- کاتیگۆری خێرا -->
<div id="quickCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-slate-900 border border-white/10 p-4 rounded-2xl w-full max-w-sm space-y-3 text-xs">
        <h3 class="text-sm font-extrabold"><i class="fa-solid fa-tags text-teal-400"></i> زیادکردنی کاتیگۆری نوێ</h3>
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-2.5" id="quickCategoryForm">@csrf
            <div><label class="lbl">ناوی کاتیگۆری:</label><input type="text" name="name" id="quick_category_name" required class="inp"></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="closeQuickCategory()" class="px-3 py-1.5 bg-slate-700 rounded-lg">پاشگەزبوونەوە</button><button type="submit" class="px-3 py-1.5 bg-emerald-500 text-slate-900 font-extrabold rounded-lg">تۆمارکردن</button></div>
        </form>
    </div>
</div>

<script>
const KEY = 'pos_product_form_data_v2';
const $ = id => document.getElementById(id);
const toggleModal = id => $(id).classList.toggle('hidden');

/* دراوی نرخ بۆ هەردوو فۆڕم: f = زیادکردن، e = دەستکاری */
function setCur(p, cur) {
    $(p + '_currency').value = cur;
    $(p + '-usd').classList.toggle('on', cur === 'USD');
    $(p + '-iqd').classList.toggle('on', cur === 'IQD');
    $(p + '-lb').innerText = $(p + '-ls').innerText = cur === 'USD' ? '$' : 'IQD';
    const h = $(p + '-hint');
    h.innerText = cur === 'USD' ? 'نرخەکان بە دۆلار پاشەکەوت دەکرێن' : 'نرخەکان بە دینار دەنووسیت و بە نرخی ئاڵوگۆڕ بۆ دۆلار دەگۆڕدرێن';
    h.className = 'text-[10px] p-2 rounded-lg border ' + (cur === 'USD' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20' : 'text-amber-300 bg-amber-500/10 border-amber-500/20');
}

/* کاتیگۆری خێرا: داتای فۆڕم دەپارێزرێت */
function saveForm() {
    sessionStorage.setItem(KEY, JSON.stringify({
        name: $('field_name').value, code: $('field_code').value, buy: $('field_buy_price').value, sale: $('field_sale_price').value,
        stock: $('field_stock').value, carton: $('field_carton').value, active: $('field_is_active').checked, cur: $('f_currency').value
    }));
}
function openQuickCategory() { saveForm(); $('quickCategoryModal').classList.remove('hidden'); setTimeout(() => $('quick_category_name').focus(), 100); }
function closeQuickCategory() { $('quickCategoryModal').classList.add('hidden'); sessionStorage.removeItem(KEY); }
$('quickCategoryForm').addEventListener('submit', saveForm);
document.addEventListener('DOMContentLoaded', () => {
    const s = sessionStorage.getItem(KEY); if (!s) return;
    try {
        const d = JSON.parse(s);
        $('field_name').value = d.name || ''; $('field_code').value = d.code || '';
        $('field_buy_price').value = d.buy || ''; $('field_sale_price').value = d.sale || '';
        $('field_stock').value = d.stock || 0; $('field_carton').value = d.carton || 1;
        $('field_is_active').checked = d.active !== false;
        if (d.cur) setCur('f', d.cur);
        const c = $('field_category'); if (c.options.length) c.selectedIndex = c.options.length - 1;
    } catch (e) {}
    sessionStorage.removeItem(KEY);
});

/* Enter = خانەی دواتر */
const navs = Array.from(document.querySelectorAll('.enter-nav'));
navs.forEach((el, i) => el.addEventListener('keydown', e => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    if (i < navs.length - 1) { navs[i + 1].focus(); navs[i + 1].select?.(); } else $('productForm').submit();
}));

/* فلتەری خشتە */
function filterStockTable() {
    const q = $('stockSearchInput').value.toLowerCase().trim(), cat = $('stockCategoryFilter').value, st = $('stockStatusFilter').value;
    let n = 0;
    document.querySelectorAll('.product-row').forEach(r => {
        const stock = parseFloat(r.dataset.stock) || 0, act = r.dataset.active;
        const ok = (!q || r.dataset.name.includes(q) || r.dataset.code.includes(q)) && (cat === 'all' || r.dataset.category == cat) &&
            (st === 'all' || (st === 'out' && stock <= 0) || (st === 'low' && stock > 0 && stock <= 5) || (st === 'active' && act === '1') || (st === 'inactive' && act === '0'));
        r.style.display = ok ? '' : 'none'; if (ok) n++;
    });
    $('productCountBadge').innerText = 'کاڵای دیاریکراو: ' + n;
}
function filterByStockState(s) { $('stockStatusFilter').value = s; filterStockTable(); }

/* دەستکاری */
function openEditProductModal(p) {
    $('edit_name').value = p.name || ''; $('edit_code').value = p.code || ''; $('edit_category_id').value = p.category_id || '';
    $('edit_buy_price').value = parseFloat(p.base_buy_price || 0).toFixed(2);
    $('edit_sale_price').value = parseFloat(p.base_sale_price || 0).toFixed(2);
    $('edit_carton').value = parseFloat(p.kg_per_carton || 1);
    $('edit_is_active').checked = p.is_active == 1;
    $('editProductForm').action = '/products/' + p.id;
    setCur('e', 'USD'); $('editProductModal').classList.remove('hidden');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') ['editProductModal', 'importProductModal', 'quickCategoryModal'].forEach(id => $(id).classList.add('hidden')); });
</script>
</body>
</html>