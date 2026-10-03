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
.inp{width:100%;padding:.55rem .7rem;border-radius:.7rem;background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.1);color:#fff;font-size:12px}
.inp:focus,button:focus-visible,a:focus-visible{outline:2px solid #2dd4bf;outline-offset:1px}
.seg{display:flex;gap:.25rem;background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.1);border-radius:.8rem;padding:.2rem}
.seg button{flex:1;padding:.4rem;border-radius:.6rem;font-size:11px;font-weight:800;color:#94a3b8}
.seg button.on{background:#14b8a6;color:#04201d}
.sc::-webkit-scrollbar{width:5px;height:5px}.sc::-webkit-scrollbar-thumb{background:#334155;border-radius:9px}
</style>
</head>
@php
    $exchangeRate = $setting->exchange_rate ?? 1500;
    $outCount = $products->where('stock_kg', '<=', 0)->count();
    $lowCount = $products->filter(fn($p) => $p->stock_kg > 0 && $p->stock_kg <= ($p->alert_quantity ?? 5))->count();
@endphp
<body class="text-slate-100 min-h-screen lg:h-screen p-3 flex flex-col gap-3 lg:overflow-hidden">

<header class="glass rounded-2xl p-3 flex flex-wrap justify-between items-center gap-2 text-xs shrink-0">
  <h1 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-boxes-stacked text-teal-400"></i> بەڕێوەبردنی کاڵاکان و کۆگا</h1>
  <div class="flex flex-wrap items-center gap-1.5 font-bold">
    <a href="{{ route('categories.index') }}" class="glass hover:bg-white/10 px-3 py-1.5 rounded-lg">کاتیگۆری</a>
    <a href="{{ route('units.index') }}" class="glass hover:bg-white/10 px-3 py-1.5 rounded-lg">یەکەکان</a>
    <a href="{{ route('reports.index') }}" class="glass hover:bg-white/10 px-3 py-1.5 rounded-lg">ڕاپۆرتەکان</a>
    <a href="{{ route('export.products') }}" class="bg-emerald-600/80 hover:bg-emerald-600 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-file-excel"></i> هەناردە</a>
    <button type="button" onclick="toggle('importProductModal')" class="bg-teal-600/80 hover:bg-teal-600 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-file-import"></i> هاوردە</button>
    <a href="{{ route('pos.index') }}" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-3 py-1.5 rounded-lg font-extrabold">POS</a>
  </div>
</header>

<div class="shrink-0 space-y-2 empty:hidden">
  @if(session('success'))<div class="bg-emerald-500/15 border border-emerald-500/50 text-emerald-300 p-2 rounded-lg text-xs font-bold"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold">{{ session('error') }}</div>@endif
  @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold space-y-1">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>@endif
</div>

<section class="shrink-0 grid grid-cols-3 gap-2 text-xs">
  <button type="button" onclick="filterByState('out')" class="glass rounded-xl p-2.5 text-right border-rose-500/30"><span class="text-[10px] text-rose-300 font-bold block">نەماو</span><span class="num text-lg font-extrabold text-rose-400">{{ $outCount }}</span></button>
  <button type="button" onclick="filterByState('low')" class="glass rounded-xl p-2.5 text-right border-amber-500/30"><span class="text-[10px] text-amber-300 font-bold block">کەمبووەوە</span><span class="num text-lg font-extrabold text-amber-400">{{ $lowCount }}</span></button>
  <button type="button" onclick="filterByState('all')" class="glass rounded-xl p-2.5 text-right border-emerald-500/30"><span class="text-[10px] text-emerald-300 font-bold block">کۆی کاڵاکان</span><span class="num text-lg font-extrabold text-emerald-400">{{ $products->count() }}</span></button>
</section>

<main class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-3 gap-3">

  <!-- فۆڕمی کاڵای نوێ -->
  <section class="glass rounded-2xl p-3 flex flex-col min-h-0">
    <h2 class="text-sm font-extrabold flex items-center gap-2 pb-2 mb-2 border-b border-white/10"><i class="fa-solid fa-square-plus text-teal-400"></i> زیادکردنی کاڵای نوێ</h2>
    <form action="{{ route('products.store') }}" method="POST" id="productForm" class="flex-1 overflow-y-auto sc pr-1 space-y-2.5 text-xs">
      @csrf
      <div><label class="block text-slate-300 text-[11px] font-bold mb-1">ناوی کاڵا:</label><input type="text" name="name" id="f_name" required autofocus class="inp"></div>
      <div><label class="block text-slate-300 text-[11px] font-bold mb-1">کۆد یان بارکۆد:</label><input type="text" name="code" id="f_code" required class="inp num"></div>
      <div>
        <div class="flex justify-between mb-1"><label class="text-slate-300 text-[11px] font-bold">کاتیگۆری:</label><button type="button" onclick="toggle('quickCategoryModal')" class="text-[10px] text-teal-400 font-bold"><i class="fa-solid fa-plus-circle"></i> نوێ</button></div>
        <select name="category_id" id="f_category" required class="inp">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
      </div>

      <!-- جۆری فرۆشتن -->
      <div>
        <label class="block text-slate-300 text-[11px] font-bold mb-1">چۆن دەیفرۆشیت؟</label>
        <div class="seg" id="typeSeg">
          <button type="button" data-type="weight" class="on">کیلۆ / تەن</button>
          <button type="button" data-type="carton">کارتۆن (دانە)</button>
        </div>
        <input type="hidden" name="sell_type" id="f_type" value="weight">
        <p id="typeHint" class="text-[10px] text-slate-400 mt-1.5 leading-5"></p>
      </div>
      <div id="cartonBox" class="hidden">
        <label class="block text-amber-300 text-[11px] font-bold mb-1">کێشی هەر کارتۆنێک بە کیلۆ:</label>
        <input type="number" step="any" min="0.01" name="kg_per_carton" id="f_carton" placeholder="نموونە: 10" class="inp num">
        <p class="text-[10px] text-slate-500 mt-1">تەنها بۆ زانینی کێشی گشتی وەسڵە، نرخ و کۆگا بە کارتۆنە.</p>
      </div>

      <div class="flex items-center justify-between bg-white/5 p-1.5 rounded-lg border border-white/10">
        <span class="text-[10px] font-bold text-slate-300">دراوی نرخ:</span>
        <div class="flex gap-1">
          <button type="button" id="cur-usd" onclick="setCur('USD')" class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-teal-500 text-slate-900">دۆلار</button>
          <button type="button" id="cur-iqd" onclick="setCur('IQD')" class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-700 text-slate-300">دینار</button>
        </div>
      </div>
      <input type="hidden" name="currency" id="f_currency" value="USD">
      <input type="hidden" name="exchange_rate" value="{{ $exchangeRate }}">

      <div class="grid grid-cols-2 gap-2">
        <div><label class="block text-slate-300 text-[11px] font-bold mb-1">نرخی کڕین (<span class="curLbl">$</span>) <span class="perLbl text-slate-500"></span></label><input type="number" step="any" min="0" name="base_buy_price" id="f_buy" required class="inp num"></div>
        <div><label class="block text-slate-300 text-[11px] font-bold mb-1">نرخی فرۆشتن (<span class="curLbl">$</span>) <span class="perLbl text-slate-500"></span></label><input type="number" step="any" min="0" name="base_sale_price" id="f_sale" required class="inp num"></div>
      </div>
      <div><label class="block text-slate-300 text-[11px] font-bold mb-1">بڕی سەرەتایی (<span class="stockLbl">کیلۆ</span>):</label><input type="number" step="any" min="0" name="stock_kg" id="f_stock" value="0" required class="inp num"></div>
      <label class="flex items-center justify-between bg-white/5 p-2 rounded-lg border border-white/10 cursor-pointer text-[11px]"><span class="font-bold text-slate-300">چالاک بێت لە POS</span><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4"></label>
      <button type="submit" class="w-full bg-teal-500 hover:bg-teal-400 text-slate-900 font-extrabold py-2.5 rounded-xl text-xs"><i class="fa-solid fa-check"></i> تۆمارکردنی کاڵا</button>
    </form>
  </section>

  <!-- خشتە -->
  <section class="lg:col-span-2 glass rounded-2xl p-3 flex flex-col min-h-0 h-[70vh] lg:h-auto">
    <div class="flex flex-wrap justify-between items-center gap-2 pb-2 mb-2 border-b border-white/10">
      <h2 class="text-sm font-extrabold flex items-center gap-2"><i class="fa-solid fa-warehouse text-teal-400"></i> لیستی مەخزەن</h2>
      <span id="countBadge" class="num text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded">{{ $products->count() }}</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-2 text-xs">
      <input type="text" id="q" onkeyup="filterTable()" placeholder="گەڕان..." class="inp">
      <select id="fCat" onchange="filterTable()" class="inp"><option value="all">هەموو کاتیگۆرییەکان</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
      <select id="fState" onchange="filterTable()" class="inp">
        <option value="all">هەموو ئاستەکان</option><option value="low">کەمبووەکان</option><option value="out">نەماوەکان</option>
        <option value="carton">کارتۆنی</option><option value="weight">کیلۆ / تەن</option><option value="inactive">ناچالاک</option>
      </select>
    </div>
    <div class="flex-1 overflow-auto sc rounded-lg border border-white/10">
      <table class="w-full text-xs text-right text-slate-300">
        <thead class="bg-slate-900 text-[11px] text-slate-400 sticky top-0 z-10"><tr>
          <th class="p-2">کۆد / ناو</th><th class="p-2">جۆر</th><th class="p-2">نرخی کڕین</th><th class="p-2">نرخی فرۆشتن</th><th class="p-2">کۆگا</th><th class="p-2 text-center">دۆخ</th><th class="p-2 text-center">کردار</th>
        </tr></thead>
        <tbody class="divide-y divide-white/5" id="tbody">
        @forelse($products as $p)
          @php
            $stock = (float) $p->stock_kg; $isOut = $stock <= 0; $isLow = !$isOut && $stock <= ($p->alert_quantity ?? 5);
            $isC = ($p->sell_type ?? 'weight') === 'carton'; $per = $isC ? 'کارتۆن' : 'کگ';
          @endphp
          <tr class="prow hover:bg-white/5 {{ $isOut ? 'bg-rose-500/5' : ($isLow ? 'bg-amber-500/5' : '') }} {{ !$p->is_active ? 'opacity-60' : '' }}"
              data-name="{{ mb_strtolower($p->name) }}" data-code="{{ mb_strtolower($p->code) }}" data-cat="{{ $p->category_id }}"
              data-stock="{{ $stock }}" data-low="{{ $p->alert_quantity ?? 5 }}" data-active="{{ $p->is_active ? 1 : 0 }}" data-type="{{ $isC ? 'carton' : 'weight' }}">
            <td class="p-2"><div class="font-bold text-white text-[11px]">{{ $p->name }}
              @if($isOut)<span class="text-[8px] bg-rose-600 px-1 rounded">نەماوە</span>@elseif($isLow)<span class="text-[8px] bg-amber-600 px-1 rounded">کەمە</span>@endif</div>
              <span class="num text-[10px] text-teal-400">{{ $p->code }}</span></td>
            <td class="p-2 text-[11px]">
              @if($isC)<span class="px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-300 font-bold">کارتۆن</span><div class="num text-[9px] text-slate-500">{{ (float) $p->kg_per_carton }} کگ</div>
              @else<span class="px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-300 font-bold">کیلۆ / تەن</span>@endif
              <div class="text-[9px] text-slate-500">{{ $p->category->name ?? '-' }}</div></td>
            <td class="p-2"><div class="num font-bold text-sky-300 text-[11px]">${{ number_format($p->base_buy_price, 2) }} <span class="text-[9px] text-slate-500">/{{ $per }}</span></div><div class="num text-[9px] text-slate-500">≈ {{ number_format($p->base_buy_price * $exchangeRate) }} IQD</div></td>
            <td class="p-2"><div class="num font-bold text-emerald-400 text-[11px]">${{ number_format($p->base_sale_price, 2) }} <span class="text-[9px] text-slate-500">/{{ $per }}</span></div><div class="num text-[9px] text-slate-500">≈ {{ number_format($p->base_sale_price * $exchangeRate) }} IQD</div></td>
            <td class="p-2 num font-bold text-[11px] {{ $isOut ? 'text-rose-400' : ($isLow ? 'text-amber-400' : 'text-slate-100') }}">{{ rtrim(rtrim(number_format($stock, 2), '0'), '.') ?: '0' }} <span class="text-[9px] text-slate-500">{{ $per }}</span></td>
            <td class="p-2 text-center"><form action="{{ route('products.toggle', $p->id) }}" method="POST">@csrf @method('PATCH')<button class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-600 text-slate-400' }}">{{ $p->is_active ? 'چالاک' : 'ناچالاک' }}</button></form></td>
            <td class="p-2 text-center"><div class="flex items-center justify-center gap-1">
              <button type="button" onclick='openEdit(@json($p))' class="bg-amber-500/15 hover:bg-amber-500 text-amber-400 hover:text-slate-900 px-2 py-0.5 rounded text-[10px] font-bold"><i class="fa-solid fa-pen-to-square"></i> دەستکاری</button>
              @if($stock > 0)<span class="text-[10px] bg-slate-700/50 text-slate-500 px-1.5 py-0.5 rounded" title="کۆگای تێدایە"><i class="fa-solid fa-lock text-[9px]"></i></span>
              @else<form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">@csrf @method('DELETE')<button class="bg-rose-500/15 hover:bg-rose-500 text-rose-400 hover:text-white px-2 py-0.5 rounded text-[10px] font-bold"><i class="fa-solid fa-trash"></i></button></form>@endif
            </div></td>
          </tr>
        @empty
          <tr><td colspan="7" class="p-4 text-center text-slate-500">هیچ کاڵایەک تۆمار نەکراوە</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- مۆداڵی دەستکاری -->
<div id="editProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
  <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs max-h-[95vh] overflow-y-auto sc">
    <div class="flex justify-between items-center border-b border-white/10 pb-2"><h3 class="text-sm font-extrabold"><i class="fa-solid fa-box-open text-amber-400"></i> دەستکاریکردنی کاڵا</h3><button type="button" onclick="toggle('editProductModal')" class="text-slate-400 hover:text-white text-lg">&times;</button></div>
    <form id="editProductForm" method="POST" class="space-y-2.5">
      @csrf @method('PUT')
      <div><label class="block font-bold text-slate-300 mb-1 text-[11px]">ناوی کاڵا:</label><input type="text" name="name" id="e_name" required class="inp"></div>
      <div><label class="block font-bold text-slate-300 mb-1 text-[11px]">کۆد:</label><input type="text" name="code" id="e_code" required class="inp num"></div>
      <div><label class="block font-bold text-slate-300 mb-1 text-[11px]">کاتیگۆری:</label><select name="category_id" id="e_category" required class="inp">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
      <div>
        <label class="block font-bold text-slate-300 mb-1 text-[11px]">جۆری فرۆشتن:</label>
        <select name="sell_type" id="e_type" onchange="editTypeChanged()" class="inp"><option value="weight">کیلۆ / تەن</option><option value="carton">کارتۆن (دانە)</option></select>
        <p id="e_lock" class="hidden text-[10px] text-amber-300 mt-1">کۆگای ئەم کاڵایە سفر نییە، بۆیە جۆرەکەی ناگۆڕدرێت.</p>
      </div>
      <div id="e_cartonBox" class="hidden"><label class="block font-bold text-amber-300 mb-1 text-[11px]">کێشی هەر کارتۆنێک بە کیلۆ:</label><input type="number" step="any" min="0.01" name="kg_per_carton" id="e_carton" class="inp num"></div>
      <p class="text-[10px] text-emerald-300 bg-emerald-500/10 p-1.5 rounded-lg border border-emerald-500/20">نرخەکان بە دۆلار پیشان دەدرێن و پاشەکەوت دەکرێن</p>
      <input type="hidden" name="currency" value="USD"><input type="hidden" name="exchange_rate" value="{{ $exchangeRate }}">
      <div class="grid grid-cols-2 gap-2">
        <div><label class="block font-bold text-slate-300 mb-1 text-[11px]">نرخی کڕین ($) <span class="e_per text-slate-500"></span></label><input type="number" step="any" min="0" name="base_buy_price" id="e_buy" required class="inp num"></div>
        <div><label class="block font-bold text-slate-300 mb-1 text-[11px]">نرخی فرۆشتن ($) <span class="e_per text-slate-500"></span></label><input type="number" step="any" min="0" name="base_sale_price" id="e_sale" required class="inp num"></div>
      </div>
      <label class="flex items-center gap-2 bg-white/5 p-2 rounded-lg cursor-pointer"><input type="checkbox" name="is_active" id="e_active" value="1" class="w-4 h-4"><span class="font-bold text-[11px]">چالاک بێت لە POS</span></label>
      <div class="flex justify-end gap-2 pt-2 border-t border-white/10"><button type="button" onclick="toggle('editProductModal')" class="bg-slate-700 hover:bg-slate-600 font-bold px-3 py-1.5 rounded-lg">داخستن</button><button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-extrabold px-4 py-1.5 rounded-lg">نوێکردنەوە</button></div>
    </form>
  </div>
</div>

<!-- هاوردە -->
<div id="importProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
  <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs">
    <div class="flex justify-between items-center border-b border-white/10 pb-2"><h3 class="text-sm font-extrabold"><i class="fa-solid fa-file-csv text-teal-400"></i> هاوردەکردنی کاڵاکان</h3><button type="button" onclick="toggle('importProductModal')" class="text-slate-400 text-lg">&times;</button></div>
    <div class="num bg-black/30 p-1.5 rounded text-[10px] text-amber-300 text-left">code, name, base_buy_price, base_sale_price, stock_kg</div>
    <form action="{{ route('products.importCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-3">@csrf
      <input type="file" name="csv_file" accept=".csv,.txt" required class="inp !text-[10px]">
      <select name="category_id" required class="inp">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
      <div class="flex justify-end gap-2 pt-2 border-t border-white/10"><button type="button" onclick="toggle('importProductModal')" class="bg-slate-700 font-bold px-3 py-1.5 rounded-lg">داخستن</button><button type="submit" class="bg-teal-500 text-slate-900 font-extrabold px-4 py-1.5 rounded-lg">دەستپێکردن</button></div>
    </form>
  </div>
</div>

<!-- کاتیگۆری خێرا -->
<div id="quickCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
  <div class="bg-slate-900 border border-white/10 p-4 rounded-2xl w-full max-w-sm space-y-3 text-xs">
    <h3 class="text-sm font-extrabold"><i class="fa-solid fa-tags text-teal-400"></i> کاتیگۆری نوێ</h3>
    <form action="{{ route('categories.store') }}" method="POST" id="quickCategoryForm" class="space-y-2.5">@csrf
      <input type="text" name="name" id="quickName" required placeholder="ناوی کاتیگۆری" class="inp">
      <div class="flex justify-end gap-2"><button type="button" onclick="toggle('quickCategoryModal')" class="px-3 py-1.5 bg-slate-700 rounded-lg">پاشگەزبوونەوە</button><button type="submit" class="px-3 py-1.5 bg-teal-500 text-slate-900 font-extrabold rounded-lg">تۆمارکردن</button></div>
    </form>
  </div>
</div>

<script>
const $ = id => document.getElementById(id);
const toggle = id => $(id).classList.toggle('hidden');
const KEY = 'pos_product_form_v2';

/* جۆری فرۆشتن لە فۆڕمی نوێ */
function setType(t) {
  $('f_type').value = t;
  document.querySelectorAll('#typeSeg button').forEach(b => b.classList.toggle('on', b.dataset.type === t));
  const c = t === 'carton';
  $('cartonBox').classList.toggle('hidden', !c); $('f_carton').required = c;
  document.querySelectorAll('.perLbl').forEach(e => e.textContent = c ? '/ کارتۆن' : '/ کگ');
  document.querySelector('.stockLbl').textContent = c ? 'ژمارەی کارتۆن' : 'کیلۆ';
  $('typeHint').textContent = c
    ? 'نرخی کڕین و فرۆشتن بۆ هەر کارتۆنێکە، و کۆگا بە کارتۆن کەم دەبێتەوە. کێشی کارتۆن تەنها بۆ کێشی گشتی وەسڵە.'
    : 'نرخ بۆ هەر کیلۆیەکە. لە POS دەتوانیت بە کیلۆ یان تەن بیفرۆشیت، و کۆگا بە کیلۆ کەم دەبێتەوە.';
}
document.querySelectorAll('#typeSeg button').forEach(b => b.addEventListener('click', () => setType(b.dataset.type)));
setType('weight');

/* دراو */
function setCur(c) {
  $('f_currency').value = c;
  $('cur-usd').className = 'px-2.5 py-0.5 rounded text-[10px] font-bold ' + (c === 'USD' ? 'bg-teal-500 text-slate-900' : 'bg-slate-700 text-slate-300');
  $('cur-iqd').className = 'px-2.5 py-0.5 rounded text-[10px] font-bold ' + (c === 'IQD' ? 'bg-teal-500 text-slate-900' : 'bg-slate-700 text-slate-300');
  document.querySelectorAll('.curLbl').forEach(e => e.textContent = c === 'USD' ? '$' : 'IQD');
}

/* دەستکاری */
function openEdit(p) {
  $('editProductForm').action = '/products/' + p.id;
  $('e_name').value = p.name || ''; $('e_code').value = p.code || ''; $('e_category').value = p.category_id || '';
  $('e_type').value = p.sell_type === 'carton' ? 'carton' : 'weight';
  $('e_type').disabled = false; $('e_lock').classList.add('hidden');
  if (parseFloat(p.stock_kg) > 0) { $('e_lock').classList.remove('hidden'); lockType = $('e_type').value; } else lockType = null;
  $('e_carton').value = parseFloat(p.kg_per_carton) > 0 ? parseFloat(p.kg_per_carton) : '';
  $('e_buy').value = parseFloat(p.base_buy_price || 0).toFixed(2);
  $('e_sale').value = parseFloat(p.base_sale_price || 0).toFixed(2);
  $('e_active').checked = p.is_active == 1;
  editTypeChanged(); toggle('editProductModal');
}
let lockType = null;
function editTypeChanged() {
  if (lockType) $('e_type').value = lockType;   // کۆگا سفر نییە: جۆر ناگۆڕدرێت
  const c = $('e_type').value === 'carton';
  $('e_cartonBox').classList.toggle('hidden', !c); $('e_carton').required = c;
  document.querySelectorAll('.e_per').forEach(e => e.textContent = c ? '/ کارتۆن' : '/ کگ');
}

/* فلتەر */
function filterTable() {
  const q = $('q').value.toLowerCase().trim(), cat = $('fCat').value, st = $('fState').value; let n = 0;
  document.querySelectorAll('.prow').forEach(r => {
    const stock = parseFloat(r.dataset.stock) || 0, low = parseFloat(r.dataset.low) || 5;
    const okS = !q || r.dataset.name.includes(q) || r.dataset.code.includes(q);
    const okC = cat === 'all' || r.dataset.cat == cat;
    const okT = st === 'all' || (st === 'out' && stock <= 0) || (st === 'low' && stock > 0 && stock <= low)
      || (st === 'carton' && r.dataset.type === 'carton') || (st === 'weight' && r.dataset.type === 'weight') || (st === 'inactive' && r.dataset.active === '0');
    const show = okS && okC && okT; r.style.display = show ? '' : 'none'; if (show) n++;
  });
  $('countBadge').textContent = n;
}
function filterByState(s) { $('fState').value = s; filterTable(); }

/* پاراستنی فۆڕم کاتێک کاتیگۆری خێرا زیاد دەکەیت */
$('quickCategoryForm').addEventListener('submit', () => {
  sessionStorage.setItem(KEY, JSON.stringify({ name: $('f_name').value, code: $('f_code').value, type: $('f_type').value, carton: $('f_carton').value,
    buy: $('f_buy').value, sale: $('f_sale').value, stock: $('f_stock').value, cur: $('f_currency').value }));
});
document.addEventListener('DOMContentLoaded', () => {
  const s = sessionStorage.getItem(KEY); if (!s) return;
  try {
    const d = JSON.parse(s);
    $('f_name').value = d.name || ''; $('f_code').value = d.code || ''; $('f_carton').value = d.carton || '';
    $('f_buy').value = d.buy || ''; $('f_sale').value = d.sale || ''; $('f_stock').value = d.stock || 0;
    setType(d.type || 'weight'); setCur(d.cur || 'USD');
    $('f_category').selectedIndex = $('f_category').options.length - 1;
  } catch (e) {}
  sessionStorage.removeItem(KEY);
});
</script>
</body>
</html>