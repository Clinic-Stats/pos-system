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
.pcard{transition:border-color .15s,background .15s}
.pcard:hover{background:rgba(255,255,255,.07)}
</style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
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
    <a href="{{ route('losses.index') }}" class="bg-rose-500/20 hover:bg-rose-500/40 text-rose-300 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-triangle-exclamation"></i> زیانی کاڵا</a>
    <a href="{{ route('units.index') }}" class="glass hover:bg-white/10 px-3 py-1.5 rounded-lg">یەکەکان</a>
    <a href="{{ route('reports.index') }}" class="glass hover:bg-white/10 px-3 py-1.5 rounded-lg">ڕاپۆرتەکان</a>
    <a href="{{ route('export.products') }}" class="bg-emerald-600/80 hover:bg-emerald-600 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-file-excel"></i> هەناردە</a>
    <button type="button" onclick="toggle('importProductModal')" class="bg-teal-600/80 hover:bg-teal-600 px-3 py-1.5 rounded-lg"><i class="fa-solid fa-file-import"></i> هاوردە</button>
    <a href="{{ route('pos.index') }}" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-3 py-1.5 rounded-lg font-extrabold">POS</a>
  </div>
</header>

@if(session('success'))<div id="toast" class="fixed top-3 left-1/2 -translate-x-1/2 z-[70] bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-2xl transition-opacity duration-500 pointer-events-none"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
@if(session('error') || $errors->any())
<div class="shrink-0 space-y-2">
  @if(session('error'))<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold">{{ session('error') }}</div>@endif
  @if($errors->any())<div class="bg-rose-500/15 border border-rose-500/50 text-rose-300 p-2 rounded-lg text-xs font-bold space-y-1">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>@endif
</div>
@endif



<!-- لیستی مەخزەن: پانی تەواو -->
<main class="flex-1 min-h-0">
  <section class="glass rounded-2xl p-3 flex flex-col min-h-0 h-[80vh] lg:h-full">
    <div class="flex flex-wrap items-center gap-2 pb-2 mb-2 border-b border-white/10 text-xs">
      <h2 class="text-sm font-extrabold flex items-center gap-2 ml-1"><i class="fa-solid fa-warehouse text-teal-400"></i> لیستی مەخزەن <span id="countBadge" class="num text-[10px] font-bold bg-white/10 px-2 py-0.5 rounded">{{ $products->count() }}</span></h2>
      <button type="button" onclick="filterByState('out')" class="rounded-lg border border-rose-500/40 bg-rose-500/10 px-2.5 py-1.5 font-bold text-rose-300">نەماو <span class="num font-extrabold text-rose-400">{{ $outCount }}</span></button>
      <button type="button" onclick="filterByState('low')" class="rounded-lg border border-amber-500/40 bg-amber-500/10 px-2.5 py-1.5 font-bold text-amber-300">کەمبووەوە <span class="num font-extrabold text-amber-400">{{ $lowCount }}</span></button>
      <button type="button" onclick="filterByState('all')" class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1.5 font-bold text-emerald-300">کۆی کاڵاکان <span class="num font-extrabold text-emerald-400">{{ $products->count() }}</span></button>
      <input type="text" id="q" onkeyup="filterTable()" placeholder="گەڕان..." class="inp !w-auto flex-1 min-w-[120px]">
      <select id="fCat" onchange="filterTable()" class="inp !w-auto min-w-[130px]"><option value="all">هەموو کاتیگۆرییەکان</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
      <select id="fSort" onchange="sortCards()" class="inp !w-auto min-w-[130px]">
        <option value="new">ڕیزکردن: نوێترین</option>
        <option value="name_asc">بەپێی ناوی کاڵا (أ - ی)</option>
        <option value="name_desc">بەپێی ناوی کاڵا (ی - أ)</option>
        <option value="sale_asc">نرخی فرۆشتن: کەم بۆ زۆر</option>
        <option value="sale_desc">نرخی فرۆشتن: زۆر بۆ کەم</option>
        <option value="buy_asc">نرخی کڕین: کەم بۆ زۆر</option>
        <option value="buy_desc">نرخی کڕین: زۆر بۆ کەم</option>
        <option value="stock_asc">کۆگا: کەم بۆ زۆر</option>
        <option value="stock_desc">کۆگا: زۆر بۆ کەم</option>
        <option value="type">بەپێی جۆر</option>
      </select>
      <button type="button" onclick="toggle('addProductModal')" class="bg-teal-500 hover:bg-teal-400 text-slate-900 font-extrabold px-4 py-2 rounded-xl"><i class="fa-solid fa-plus"></i> زیادکردنی کاڵا</button>
    </div>
    <input type="hidden" id="fState" value="all">

    <div class="flex-1 overflow-y-auto sc pr-1">
      <div id="grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2">
      @forelse($products as $p)
        @php
          $stock = (float) $p->stock_kg; $isOut = $stock <= 0; $isLow = !$isOut && $stock <= ($p->alert_quantity ?? 5);
          $isC = ($p->sell_type ?? 'weight') === 'carton'; $per = $isC ? 'کارتۆن' : 'کگ';
        @endphp
        <div class="prow pcard glass rounded-xl p-2 flex flex-col gap-1.5 text-[11px] {{ $isOut ? 'border-rose-500/40' : ($isLow ? 'border-amber-500/40' : '') }} {{ !$p->is_active ? 'opacity-60' : '' }}"
             data-idx="{{ $loop->index }}" data-name="{{ mb_strtolower($p->name) }}" data-code="{{ mb_strtolower($p->code) }}" data-cat="{{ $p->category_id }}"
             data-stock="{{ $stock }}" data-low="{{ $p->alert_quantity ?? 5 }}" data-active="{{ $p->is_active ? 1 : 0 }}" data-type="{{ $isC ? 'carton' : 'weight' }}"
             data-buy="{{ (float) $p->base_buy_price }}" data-sale="{{ (float) $p->base_sale_price }}">

          <div class="flex justify-between items-start gap-1">
            <div class="min-w-0">
              <div class="font-extrabold text-white text-xs leading-4 break-words">{{ $p->name }}
                @if($isOut)<span class="text-[8px] bg-rose-600 px-1 py-0.5 rounded align-middle">نەماوە</span>@elseif($isLow)<span class="text-[8px] bg-amber-600 px-1 py-0.5 rounded align-middle">کەمە</span>@endif
              </div>
              <div class="text-[9px] text-slate-500"><span class="num text-teal-400">{{ $p->code }}</span> · {{ $p->category->name ?? '-' }}</div>
            </div>
            <form action="{{ route('products.toggle', $p->id) }}" method="POST" class="shrink-0">@csrf @method('PATCH')
              <button class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-600 text-slate-400' }}">{{ $p->is_active ? 'چالاک' : 'ناچالاک' }}</button>
            </form>
          </div>

          <div>
            @if($isC)<span class="px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-300 font-bold text-[9px]">کارتۆن</span><span class="num text-[9px] text-slate-500 mx-1">{{ (float) $p->kg_per_carton }} کگ</span>
            @else<span class="px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-300 font-bold text-[9px]">کیلۆ / تەن</span>@endif
          </div>

          <div class="bg-black/20 rounded-lg p-1.5 space-y-1">
            <div class="flex justify-between items-baseline gap-1"><span class="text-[9px] text-slate-500">کڕین</span>
              <span class="num font-bold text-sky-300">${{ number_format($p->base_buy_price, 2) }}<span class="text-[8px] text-slate-500">/{{ $per }}</span></span></div>
            <div class="flex justify-between items-baseline gap-1"><span class="text-[9px] text-slate-500">فرۆشتن</span>
              <span class="num font-bold text-emerald-400">${{ number_format($p->base_sale_price, 2) }}<span class="text-[8px] text-slate-500">/{{ $per }}</span></span></div>
            <div class="flex justify-between items-baseline gap-1"><span class="text-[9px] text-slate-500">کۆگا</span>
              <span class="num font-extrabold {{ $isOut ? 'text-rose-400' : ($isLow ? 'text-amber-400' : 'text-slate-100') }}">{{ rtrim(rtrim(number_format($stock, 2), '0'), '.') ?: '0' }} <span class="text-[8px] text-slate-500 font-bold">{{ $per }}</span></span></div>
          </div>

          <div class="flex items-center justify-between">
            @if($stock > 0)
              <span class="text-[10px] text-slate-500" title="کۆگای تێدایە"><i class="fa-solid fa-lock"></i></span>
            @else
              <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">@csrf @method('DELETE')
                <button class="text-rose-400 hover:text-rose-300 px-1" title="سڕینەوە"><i class="fa-regular fa-trash-can"></i></button></form>
            @endif
            <button type="button" onclick='openEdit(@json($p))' class="bg-amber-500/15 hover:bg-amber-500 text-amber-400 hover:text-slate-900 px-2 py-0.5 rounded-md text-[10px] font-bold"><i class="fa-solid fa-pen-to-square"></i> دەستکاری</button>
          </div>
        </div>
      @empty
        <div class="col-span-full p-6 text-center text-slate-500">هیچ کاڵایەک تۆمار نەکراوە</div>
      @endforelse
      </div>
      <div id="noResult" class="hidden p-6 text-center text-slate-500 text-xs">هیچ کاڵایەک نەدۆزرایەوە</div>
    </div>
  </section>
</main>

<!-- مۆداڵی زیادکردنی کاڵا (گەورە) -->
<div id="addProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
  <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-3xl p-5 shadow-2xl text-xs max-h-[95vh] overflow-y-auto sc">
    <div class="flex justify-between items-center border-b border-white/10 pb-3 mb-4">
      <h3 class="text-base font-extrabold flex items-center gap-2"><i class="fa-solid fa-square-plus text-teal-400"></i> زیادکردنی کاڵای نوێ</h3>
      <button type="button" onclick="toggle('addProductModal')" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
    </div>
    <form action="{{ route('products.store') }}" method="POST" id="productForm" class="space-y-4 text-xs">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div><label class="block text-slate-300 text-xs font-bold mb-1">ناوی کاڵا:</label><input type="text" name="name" id="f_name" required class="inp !text-sm !py-2.5" value="{{ old('name') }}"></div>
        <div><label class="block text-slate-300 text-xs font-bold mb-1">کۆد یان بارکۆد:</label><input type="text" name="code" id="f_code" required class="inp num !text-sm !py-2.5" value="{{ old('code') }}"></div>
      </div>

      <div>
        <div class="flex justify-between mb-1"><label class="text-slate-300 text-xs font-bold">کاتیگۆری:</label><button type="button" onclick="toggle('quickCategoryModal')" class="text-[11px] text-teal-400 font-bold"><i class="fa-solid fa-plus-circle"></i> نوێ</button></div>
        <select name="category_id" id="f_category" required class="inp !text-sm !py-2.5">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
      </div>

      <div>
        <label class="block text-slate-300 text-xs font-bold mb-1">چۆن دەیفرۆشیت؟</label>
        <div class="seg" id="typeSeg">
          <button type="button" data-type="weight" class="on">کیلۆ / تەن</button>
          <button type="button" data-type="carton">کارتۆن (دانە)</button>
        </div>
        <input type="hidden" name="sell_type" id="f_type" value="weight">
        <p id="typeHint" class="text-[11px] text-slate-400 mt-1.5 leading-5"></p>
      </div>
      <div id="cartonBox" class="hidden">
        <label class="block text-amber-300 text-xs font-bold mb-1">کێشی هەر کارتۆنێک بە کیلۆ:</label>
        <input type="number" step="any" min="0.01" name="kg_per_carton" id="f_carton" placeholder="نموونە: 10" class="inp num !text-sm !py-2.5">
        <p class="text-[10px] text-slate-500 mt-1">تەنها بۆ زانینی کێشی گشتی وەسڵە، نرخ و کۆگا بە کارتۆنە.</p>
      </div>

      <div class="flex items-center justify-between bg-white/5 p-2 rounded-lg border border-white/10">
        <span class="text-[11px] font-bold text-slate-300">دراوی نرخ:</span>
        <div class="flex gap-1">
          <button type="button" id="cur-usd" onclick="setCur('USD')" class="px-3 py-1 rounded text-[11px] font-bold bg-teal-500 text-slate-900">دۆلار</button>
          <button type="button" id="cur-iqd" onclick="setCur('IQD')" class="px-3 py-1 rounded text-[11px] font-bold bg-slate-700 text-slate-300">دینار</button>
        </div>
      </div>
      <input type="hidden" name="currency" id="f_currency" value="USD">
      <input type="hidden" name="exchange_rate" value="{{ $exchangeRate }}">

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div><label class="block text-slate-300 text-xs font-bold mb-1">نرخی کڕین (<span class="curLbl">$</span>) <span class="perLbl text-slate-500"></span></label><input type="number" step="any" min="0" name="base_buy_price" id="f_buy" required class="inp num !text-sm !py-2.5"></div>
        <div><label class="block text-slate-300 text-xs font-bold mb-1">نرخی فرۆشتن (<span class="curLbl">$</span>) <span class="perLbl text-slate-500"></span></label><input type="number" step="any" min="0" name="base_sale_price" id="f_sale" required class="inp num !text-sm !py-2.5"></div>
        <div><label class="block text-slate-300 text-xs font-bold mb-1">بڕی سەرەتایی (<span class="stockLbl">کیلۆ</span>):</label><input type="number" step="any" min="0" name="stock_kg" id="f_stock" value="0" required class="inp num !text-sm !py-2.5"></div>
      </div>

      <label class="flex items-center justify-between bg-white/5 p-2.5 rounded-lg border border-white/10 cursor-pointer text-xs"><span class="font-bold text-slate-300">چالاک بێت لە POS</span><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4"></label>

      <div class="flex justify-end gap-2 pt-3 border-t border-white/10">
        <button type="button" onclick="toggle('addProductModal')" class="bg-slate-700 hover:bg-slate-600 font-bold px-4 py-2.5 rounded-xl">داخستن</button>
        <button type="submit" class="bg-teal-500 hover:bg-teal-400 text-slate-900 font-extrabold px-8 py-2.5 rounded-xl"><i class="fa-solid fa-check"></i> تۆمارکردنی کاڵا</button>
      </div>
    </form>
  </div>
</div>

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

<!-- کاتیگۆری خێرا (دوا شوێن بۆ ئەوەی لەسەر مۆداڵی زیادکردن بێت) -->
<div id="quickCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-[60]">
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
  $('cur-usd').className = 'px-3 py-1 rounded text-[11px] font-bold ' + (c === 'USD' ? 'bg-teal-500 text-slate-900' : 'bg-slate-700 text-slate-300');
  $('cur-iqd').className = 'px-3 py-1 rounded text-[11px] font-bold ' + (c === 'IQD' ? 'bg-teal-500 text-slate-900' : 'bg-slate-700 text-slate-300');
  document.querySelectorAll('.curLbl').forEach(e => e.textContent = c === 'USD' ? '$' : 'IQD');
}

/* دەستکاری */
let lockType = null;
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
function editTypeChanged() {
  if (lockType) $('e_type').value = lockType;
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
    const okT = st === 'all' || (st === 'out' && stock <= 0) || (st === 'low' && stock > 0 && stock <= low);
    const show = okS && okC && okT; r.style.display = show ? '' : 'none'; if (show) n++;
  });
  $('countBadge').textContent = n;
  $('noResult').classList.toggle('hidden', n > 0 || !document.querySelector('.prow'));
}
function filterByState(s) { $('fState').value = s; filterTable(); }

/* ڕیزکردن */
function sortCards() {
  const grid = $('grid'), v = $('fSort').value;
  const rows = [...grid.querySelectorAll('.prow')];
  const num = (r, k) => parseFloat(r.dataset[k]) || 0;
  const cmp = {
    new: (a, b) => num(a, 'idx') - num(b, 'idx'),
    name_asc: (a, b) => a.dataset.name.localeCompare(b.dataset.name, 'ckb'),
    name_desc: (a, b) => b.dataset.name.localeCompare(a.dataset.name, 'ckb'),
    sale_asc: (a, b) => num(a, 'sale') - num(b, 'sale'),
    sale_desc: (a, b) => num(b, 'sale') - num(a, 'sale'),
    buy_asc: (a, b) => num(a, 'buy') - num(b, 'buy'),
    buy_desc: (a, b) => num(b, 'buy') - num(a, 'buy'),
    stock_asc: (a, b) => num(a, 'stock') - num(b, 'stock'),
    stock_desc: (a, b) => num(b, 'stock') - num(a, 'stock'),
    type: (a, b) => a.dataset.type.localeCompare(b.dataset.type) || a.dataset.name.localeCompare(b.dataset.name, 'ckb'),
  }[v];
  rows.sort(cmp).forEach(r => grid.appendChild(r));
}

/* پاراستنی فۆڕم کاتێک کاتیگۆری خێرا زیاد دەکەیت */
$('quickCategoryForm').addEventListener('submit', () => {
  sessionStorage.setItem(KEY, JSON.stringify({ name: $('f_name').value, code: $('f_code').value, type: $('f_type').value, carton: $('f_carton').value,
    buy: $('f_buy').value, sale: $('f_sale').value, stock: $('f_stock').value, cur: $('f_currency').value }));
});
document.addEventListener('DOMContentLoaded', () => {
  const t = $('toast'); if (t) { setTimeout(() => t.style.opacity = 0, 2200); setTimeout(() => t.remove(), 2800); }
  @if($errors->any() && old('code') !== null)
  $('addProductModal').classList.remove('hidden');
  @endif
  const s = sessionStorage.getItem(KEY); if (!s) return;
  try {
    const d = JSON.parse(s);
    $('f_name').value = d.name || ''; $('f_code').value = d.code || ''; $('f_carton').value = d.carton || '';
    $('f_buy').value = d.buy || ''; $('f_sale').value = d.sale || ''; $('f_stock').value = d.stock || 0;
    setType(d.type || 'weight'); setCur(d.cur || 'USD');
    $('f_category').selectedIndex = $('f_category').options.length - 1;
    $('addProductModal').classList.remove('hidden');   // فۆڕمەکە دووبارە دەکرێتەوە
  } catch (e) {}
  sessionStorage.removeItem(KEY);
});
</script>
</body>
</html>