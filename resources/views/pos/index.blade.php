<!DOCTYPE html>
<html lang="ckb" dir="rtl" class="">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS - فرۆشتن</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{--bg:#eef2f7;--sf:#fff;--sf2:#f5f7fb;--bd:#e2e8f0;--tx:#0f172a;--mu:#64748b;--ac:#0f766e;--acs:#d5f5ef;--wa:#b45309;--was:#fef3c7;--ro:#e11d48;--ros:#ffe4e6}
.dark{--bg:#080e1a;--sf:#101828;--sf2:#16213a;--bd:#202e4a;--tx:#e6edf7;--mu:#8da0bd;--ac:#2dd4bf;--acs:#0c3a3a;--wa:#fbbf24;--was:#3a2a08;--ro:#fb7185;--ros:#3d1420}
body{font-family:'Almarai',sans-serif;background:var(--bg);color:var(--tx)}
.num{font-family:'Plus Jakarta Sans',sans-serif;direction:ltr;unicode-bidi:isolate}
.sf{background:var(--sf);border:1px solid var(--bd);border-radius:1.1rem}
.inp{background:var(--sf2);border:1px solid var(--bd);border-radius:.7rem;padding:.5rem .7rem;font-size:11px;font-weight:700;color:var(--tx);width:100%}
.inp:focus,button:focus-visible,a:focus-visible{outline:2px solid var(--ac);outline-offset:1px}
.chip{background:var(--sf2);border:1px solid var(--bd);color:var(--mu);border-radius:999px;padding:.4rem .9rem;font-size:11px;font-weight:800;white-space:nowrap;transition:.15s}
.chip:hover{color:var(--tx)} .chip.on{background:var(--ac);border-color:var(--ac);color:var(--bg)}
.nav{display:flex;align-items:center;gap:.4rem;padding:.45rem .8rem;border-radius:.75rem;font-size:11px;font-weight:800;color:var(--mu);transition:.15s;white-space:nowrap}
.nav:hover{background:var(--sf2);color:var(--tx)}
.pc{background:var(--sf);border:1px solid var(--bd);border-radius:1rem;padding:.65rem;display:flex;flex-direction:column;justify-content:space-between;gap:.5rem;position:relative;transition:.15s}
.pc:hover{border-color:var(--ac);transform:translateY(-2px);box-shadow:0 10px 24px -14px rgba(15,118,110,.55)}
.pc.out{opacity:.55}.pc.low{border-color:var(--wa)}
.sq{width:2.1rem;height:2.1rem;border-radius:.7rem;display:flex;align-items:center;justify-content:center;font-size:11px;transition:.12s}
.sq:active{transform:scale(.9)}
.sq.add{background:var(--ac);color:var(--bg)} .sq.sub{background:var(--sf2);color:var(--mu);border:1px solid var(--bd)}
.seg{display:flex;background:var(--sf2);border:1px solid var(--bd);border-radius:.8rem;padding:.2rem;gap:.2rem}
.seg>*{flex:1;text-align:center;padding:.4rem .5rem;border-radius:.6rem;font-size:11px;font-weight:800;color:var(--mu);cursor:pointer}
.seg>.on,.seg>label:has(input:checked){background:var(--ac);color:var(--bg)}
.seg>label.debt:has(input:checked){background:var(--wa);color:var(--bg)}
.seg input{display:none}
.badge{position:absolute;top:-.5rem;left:-.5rem;background:var(--ac);color:var(--bg);border-radius:999px;padding:.1rem .55rem;font-size:12px;font-weight:800;border:2px solid var(--bg);display:none}
.scroll::-webkit-scrollbar{width:6px;height:6px}.scroll::-webkit-scrollbar-thumb{background:var(--bd);border-radius:9px}
.rowin{animation:rin .25s ease-out}@keyframes rin{from{opacity:0;transform:translateY(6px)}}
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none}input[type=number]{-moz-appearance:textfield}
@media (prefers-reduced-motion:reduce){.rowin{animation:none}.pc{transition:none}}
</style>
</head>
<body class="min-h-screen lg:h-screen p-2 flex flex-col gap-2 lg:overflow-hidden select-none">

<!-- هێدەر -->
<header class="sf px-3 py-2 flex flex-wrap items-center justify-between gap-2 shrink-0 relative z-40">
  <div class="flex items-center gap-2.5">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background:var(--ac);color:var(--bg)"><i class="fa-solid fa-cash-register"></i></div>
    <div><h1 class="font-extrabold text-sm leading-none">POS</h1><p class="text-[10px] mt-1 flex items-center gap-1" style="color:var(--mu)"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>فرۆشتن</p></div>
  </div>

  <nav class="flex flex-wrap items-center justify-center gap-1">
    <a href="{{ route('purchases.create') }}" class="nav"><i class="fa-solid fa-box-open"></i> کڕین</a>
    <a href="{{ route('products.index') }}" class="nav"><i class="fa-solid fa-boxes-stacked"></i> کۆگا</a>
    <a href="{{ route('customers.index') }}" class="nav"><i class="fa-solid fa-users"></i> کڕیار</a>
    <a href="{{ route('reports.index') }}" class="nav"><i class="fa-solid fa-chart-pie"></i> ڕاپۆرت</a>
    <div class="relative">
      <button type="button" class="nav" onclick="event.stopPropagation();document.getElementById('moreDropdown').classList.toggle('hidden')"><i class="fa-solid fa-ellipsis"></i> زیاتر <i class="fa-solid fa-chevron-down text-[8px]"></i></button>
      <div id="moreDropdown" class="hidden sf absolute right-0 top-full mt-2 w-56 p-1.5 text-xs shadow-2xl scroll max-h-96 overflow-y-auto z-50">
        <a href="{{ route('sales.list') }}" class="nav"><i class="fa-solid fa-receipt w-4"></i> فرۆشتنەکان</a>
        <a href="{{ route('returns.index') }}" class="nav"><i class="fa-solid fa-rotate-left w-4"></i> گەڕاوەکان</a>
        <a href="{{ route('losses.index') }}" class="nav"><i class="fa-solid fa-triangle-exclamation w-4"></i> زیانی کاڵا</a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('audit.index') }}" class="nav"><i class="fa-solid fa-user-shield w-4"></i> تۆماری چالاکییەکان</a>
        @endif
        <a href="{{ route('expenses.index') }}" class="nav"><i class="fa-solid fa-money-bill-trend-up w-4"></i> خەرجییەکان</a>
        <a href="{{ route('mandub.dashboard') }}" class="nav"><i class="fa-solid fa-motorcycle w-4"></i> مەندووب</a>
        <a href="{{ route('categories.index') }}" class="nav"><i class="fa-solid fa-tags w-4"></i> کاتیگۆری</a>
        <a href="{{ route('units.index') }}" class="nav"><i class="fa-solid fa-scale-balanced w-4"></i> یەکەکان</a>
        <a href="{{ route('suppliers.index') }}" class="nav"><i class="fa-solid fa-truck-field w-4"></i> دابینکەران</a>
        <a href="{{ route('partners.index') }}" class="nav"><i class="fa-solid fa-handshake w-4"></i> هاوبەشەکان</a>
        <a href="{{ route('settings.receipt') }}" class="nav"><i class="fa-solid fa-gear w-4"></i> ڕێکخستنی وەسڵ</a>
        <a href="{{ route('users.index') }}" class="nav"><i class="fa-solid fa-user-shield w-4"></i> کارمەندان</a>
      </div>
    </div>
  </nav>

  <div class="flex items-center gap-2">
    <!-- نرخی ئاڵوگۆڕ -->
    <div class="relative">
      <button type="button" id="rateDisplay" onclick="toggleRateEdit()" class="chip flex items-center gap-1.5" title="گۆڕینی نرخی ئاڵوگۆڕ">
        <i class="fa-solid fa-arrow-right-arrow-left"></i><span id="currentRateDisplay" class="num">1$ = {{ number_format($setting->exchange_rate ?? 1500) }}</span>
      </button>
      <div id="rateEdit" class="hidden sf absolute left-0 top-full mt-2 p-2.5 w-52 shadow-2xl z-50 space-y-2">
        <label class="text-[10px] font-bold block" style="color:var(--mu)">نرخی نوێ (١$ = چەند دینار)</label>
        <div class="flex gap-1.5">
          <input type="number" id="newExchangeRate" min="1" step="any" value="{{ $setting->exchange_rate ?? 1500 }}" class="inp num">
          <button type="button" id="rateSaveBtn" onclick="saveExchangeRate()" class="sq add"><i class="fa-solid fa-check"></i></button>
        </div>
      </div>
    </div>
    <button type="button" onclick="toggleTheme()" class="sq sub" title="تەم"><i id="themeIcon" class="fa-solid fa-moon"></i></button>
    <div class="flex items-center gap-2 pr-2 border-r" style="border-color:var(--bd)">
      <span class="text-[11px] font-extrabold">{{ auth()->user()->name ?? 'کاشیر' }}</span>
      <form action="{{ route('logout') }}" method="POST" class="m-0">@csrf<button type="submit" class="sq" style="background:var(--ros);color:var(--ro)" title="دەرچوون"><i class="fa-solid fa-power-off"></i></button></form>
    </div>
  </div>
</header>

<main class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-[1fr_390px] gap-2">

  <!-- کاڵاکان -->
  <section class="sf p-3 flex flex-col min-h-0 h-[70vh] lg:h-auto">
    <div class="shrink-0 space-y-2.5 pb-3 border-b" style="border-color:var(--bd)">
      <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-xs" style="color:var(--mu)"></i>
        <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان بە ناو یان کۆد...  (Ctrl+K)" autocomplete="off" spellcheck="false" class="inp !py-2.5 !pr-9 !text-xs">
      </div>
      <div class="flex gap-1.5 overflow-x-auto scroll pb-1">
        <button type="button" onclick="filterCategory('all')" id="cat-btn-all" class="chip cat-filter-btn on">هەمووی</button>
        @foreach($categories as $cat)
          <button type="button" onclick="filterCategory('{{ $cat->id }}')" id="cat-btn-{{ $cat->id }}" class="chip cat-filter-btn">{{ $cat->name }}</button>
        @endforeach
      </div>
    </div>

    <div id="productsGrid" class="grow overflow-y-auto scroll pt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5 content-start">
      @foreach($products as $p)
        @php
          $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
          $alertVal = (float) ($p->alert_quantity ?? 5);
          $isOut = $stockVal <= 0; $isLow = !$isOut && $stockVal <= $alertVal;
          $isCarton = ($p->sell_type ?? 'weight') === 'carton';
        @endphp
        <div class="pc {{ $isOut ? 'out' : ($isLow ? 'low' : '') }}" data-category="{{ $p->category_id }}" data-name="{{ $p->name }}" data-code="{{ $p->code }}" data-price-usd="{{ $p->base_sale_price }}">
          <div id="qty-badge-{{ $p->id }}" class="badge num"><i class="fa-solid fa-check text-[10px]"></i> <span class="badge-val">0</span></div>
          <div class="cursor-pointer space-y-2" onclick="addToCart({{ $p->id }})">
            <div class="flex items-center justify-between gap-1">
              <span class="num text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--sf2);color:var(--mu)">{{ $p->code }}</span>
              @if($isOut)<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--ros);color:var(--ro)">نەماوە</span>
              @elseif($isLow)<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--was);color:var(--wa)">کەمە</span>@endif
            </div>
            <h3 class="font-extrabold text-[12px] leading-snug line-clamp-2 min-h-[2.4em]">{{ $p->name }}</h3>
            <p class="text-[10px] font-bold flex items-center gap-1" style="color:{{ $isOut ? 'var(--ro)' : ($isLow ? 'var(--wa)' : 'var(--mu)') }}"><i class="fa-solid fa-cube text-[9px]"></i><span class="num" id="stock-{{ $p->id }}">{{ rtrim(rtrim(number_format($stockVal, 2), '0'), '.') }}</span> {{ $isCarton ? 'کارتۆن' : 'کگ' }}</p>
          </div>
          <div class="flex items-center justify-between gap-1 pt-2 border-t" style="border-color:var(--bd)">
            <button type="button" class="sq add" onclick="quickIncrease({{ $p->id }}, event)"><i class="fa-solid fa-plus"></i></button>
            <div class="text-center leading-tight">
              <div class="num font-extrabold text-[13px]" style="color:var(--ac)">${{ number_format($p->base_sale_price, 2) }}</div>
              <div class="num text-[9px] font-bold p-iqd" style="color:var(--mu)"></div>
              @if($isCarton)<div class="text-[9px] font-extrabold" style="color:var(--wa)">هەر کارتۆنێک</div>@endif
            </div>
            <button type="button" class="sq sub" onclick="quickDecrease({{ $p->id }}, event)"><i class="fa-solid fa-minus"></i></button>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <!-- سەبەتە -->
  <aside class="sf p-3 flex flex-col min-h-0 h-[85vh] lg:h-auto">
    <div class="shrink-0 space-y-2.5 pb-3 border-b" style="border-color:var(--bd)">
      <div class="flex items-center justify-between">
        <h2 class="font-extrabold text-sm flex items-center gap-2"><i class="fa-solid fa-cart-shopping" style="color:var(--ac)"></i> سەبەتە <span id="cartCount" class="num text-[10px] px-1.5 rounded-md" style="background:var(--acs);color:var(--ac)">0</span></h2>
        <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" class="chip !py-1.5 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span></button>
      </div>

      <div id="editBanner" class="hidden rounded-xl px-3 py-2 text-[10px] font-extrabold flex items-center justify-between" style="background:var(--was);color:var(--wa)">
        <span><i class="fa-solid fa-pen-to-square"></i> دەستکاریکردنی وەسڵ <span id="editInvoiceNo" class="num"></span></span>
        <a href="{{ route('pos.index') }}" class="underline">پسوولەی نوێ</a>
      </div>

      <div class="flex items-center gap-2">
        <div class="seg flex-1">
          <button type="button" id="btn-cur-usd" onclick="setCurrency('USD')" class="on">$ دۆلار</button>
          <button type="button" id="btn-cur-iqd" onclick="setCurrency('IQD')">دینار</button>
        </div>
        <input type="number" id="exchangeRate" value="{{ $setting->exchange_rate ?? 1500 }}" onchange="renderCart()" class="inp num !w-20 text-center" title="نرخی ئاڵوگۆڕی ئەم وەسڵە">
      </div>

      <div class="grid grid-cols-2 gap-2">
        <input type="datetime-local" id="saleCreatedAt" value="{{ date('Y-m-d\TH:i') }}" class="inp num">
        <select id="customerId" class="inp">
          <option value="">کڕیاری نەقد</option>
          @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
        </select>
      </div>

      <div class="seg">
        <label><input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()"><i class="fa-solid fa-money-bill-wave"></i> نەقد</label>
        <label class="debt"><input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()"><i class="fa-solid fa-clock"></i> قەرز</label>
      </div>
      <div id="paidAmountBox" class="hidden"><input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0" class="inp num"></div>
    </div>

    <div id="cartItemsContainer" class="grow overflow-y-auto scroll py-2.5 space-y-2"></div>

    <div class="shrink-0 pt-3 border-t space-y-2" style="border-color:var(--bd)">
      <div class="flex justify-between items-center text-[11px] font-bold"><span style="color:var(--mu)">کۆی کاڵا</span><span id="subTotalText" class="num">$0.00</span></div>
      <div class="flex justify-between items-center text-[11px] font-bold"><span style="color:var(--mu)">کێشی گشتی</span><span id="cartWeight" class="num">0 کگ</span></div>
      <div class="flex justify-between items-center text-[11px] font-bold"><span style="color:var(--mu)">داشکاندن</span><input type="number" min="0" id="cartDiscount" value="0" oninput="renderCart()" class="inp num !w-24 !py-1 text-left"></div>
      <div class="rounded-2xl px-3.5 py-3 flex justify-between items-end" style="background:var(--acs)">
        <span class="font-extrabold text-sm" style="color:var(--ac)">کۆی گشتی</span>
        <div class="text-left leading-tight"><div id="grandTotalText" class="num font-extrabold text-2xl" style="color:var(--ac)">$0.00</div><div id="grandAltText" class="num text-[10px] font-bold" style="color:var(--mu)"></div></div>
      </div>
      <button type="button" onclick="submitSale()" id="btnSubmitSale" class="w-full py-3.5 rounded-2xl font-extrabold text-[13px] flex items-center justify-center gap-2 transition active:scale-[.98]" style="background:var(--ac);color:var(--bg)"><i class="fa-solid fa-paper-plane"></i> پسوولەکردن</button>
    </div>
  </aside>
</main>

<!-- مۆداڵی سەرکەوتن -->
<div id="successModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-3 z-[999]">
  <div class="sf w-full max-w-lg p-5 flex flex-col max-h-[90vh] shadow-2xl rowin">
    <div class="flex items-center justify-between pb-3 border-b" style="border-color:var(--bd)">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg" style="background:var(--acs);color:var(--ac)"><i class="fa-solid fa-check"></i></div>
        <div><h3 class="font-extrabold">وەسڵ تۆمارکرا</h3><p class="text-[10px]" style="color:var(--mu)">چاپ بکە، یان وەسڵی نوێ دەست پێبکە</p></div>
      </div>
      <button type="button" onclick="startNewSale()" class="sq sub" title="داخستن و وەسڵی نوێ"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div id="modalItemsList" class="grow overflow-y-auto scroll py-3 space-y-2"></div>
    <div class="pt-3 border-t space-y-3" style="border-color:var(--bd)">
      <div class="flex justify-between items-center rounded-2xl px-4 py-3" style="background:var(--acs)"><span class="font-extrabold text-sm">کۆی گشتی</span><span id="modalGrandTotal" class="num font-extrabold text-xl" style="color:var(--ac)">$0.00</span></div>
      <div class="grid grid-cols-2 gap-2 text-xs font-extrabold">
        <a href="#" id="printA4Btn" target="_blank" onclick="afterPrint()" class="py-3 rounded-xl text-center" style="background:var(--ac);color:var(--bg)"><i class="fa-solid fa-file-lines"></i> چاپی A4</a>
        <a href="#" id="printSmallBtn" target="_blank" onclick="afterPrint()" class="py-3 rounded-xl text-center" style="background:var(--sf2);border:1px solid var(--bd)"><i class="fa-solid fa-receipt"></i> چاپی بچووک</a>
        <button type="button" onclick="closeKeepEditing()" class="py-3 rounded-xl" style="background:var(--sf2);border:1px solid var(--bd)"><i class="fa-solid fa-pen-to-square"></i> دەستکاری ئەم وەسڵە</button>
        <button type="button" onclick="startNewSale()" class="py-3 rounded-xl" style="background:var(--wa);color:var(--bg)"><i class="fa-solid fa-plus"></i> وەسڵی نوێ</button>
      </div>
    </div>
  </div>
</div>

<div id="toastContainer" class="fixed top-3 left-1/2 -translate-x-1/2 z-[1000] space-y-2 pointer-events-none flex flex-col items-center"></div>

<script>
const units = @json($units);
const allProducts = @json($products);
const editSale = @json($editSale ?? null);
let cart = [], lastSaleItems = [], activeSaleId = null;
let currentCurrency = 'USD';
let rate = parseFloat(document.getElementById('exchangeRate').value) || 1500;
let clearTimer = null, confirmingClear = false;
const $ = id => document.getElementById(id);
const getRate = () => parseFloat($('exchangeRate').value) || 1500;
const money = (v, cur = currentCurrency) => cur === 'USD' ? '$' + v.toFixed(2) : Math.round(v).toLocaleString() + ' IQD';
const toDisp = usd => currentCurrency === 'USD' ? usd : usd * rate;
const maxQty = i => i.factor > 0 ? i.stock_kg / i.factor : i.stock_kg;

/* تەم و مینیوی زیاتر */
function applyTheme(t) { document.documentElement.classList.toggle('dark', t === 'dark'); $('themeIcon').className = t === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun'; try { localStorage.setItem('pos_theme', t); } catch (e) {} }
function toggleTheme() { applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark'); }
applyTheme((() => { try { return localStorage.getItem('pos_theme') || 'light'; } catch (e) { return 'light'; } })());
document.addEventListener('click', e => {
  const dd = $('moreDropdown'); if (!dd.contains(e.target)) dd.classList.add('hidden');
  const re = $('rateEdit'); if (!re.classList.contains('hidden') && !re.contains(e.target) && !$('rateDisplay').contains(e.target)) re.classList.add('hidden');
});
document.addEventListener('keydown', e => { if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); $('searchBox').focus(); } });

function showToast(msg, type = 'warning') {
  const c = { error: ['var(--ros)', 'var(--ro)', 'fa-circle-exclamation'], success: ['var(--acs)', 'var(--ac)', 'fa-circle-check'], warning: ['var(--was)', 'var(--wa)', 'fa-circle-info'] }[type] || [];
  const t = document.createElement('div');
  t.className = 'pointer-events-auto flex items-center gap-2 px-4 py-2.5 rounded-xl text-[11px] font-extrabold shadow-xl border transition-all duration-300 -translate-y-6 opacity-0';
  t.style.cssText = `background:${c[0]};color:${c[1]};border-color:${c[1]}`;
  t.innerHTML = `<i class="fa-solid ${c[2]}"></i><span>${msg}</span>`;
  $('toastContainer').appendChild(t);
  setTimeout(() => t.classList.remove('-translate-y-6', 'opacity-0'), 10);
  setTimeout(() => { t.classList.add('opacity-0'); setTimeout(() => t.remove(), 300); }, 2800);
}

/* دراو */
function paintCurrency() {
  $('btn-cur-usd').classList.toggle('on', currentCurrency === 'USD');
  $('btn-cur-iqd').classList.toggle('on', currentCurrency === 'IQD');
}
function setCurrency(cur) {
  if (cur !== currentCurrency) {
    // داشکاندن و پارەی دراو دەگۆڕین بۆ دراوە نوێیەکە
    const r = getRate();
    ['cartDiscount', 'paidAmount'].forEach(id => {
      const v = parseFloat($(id).value) || 0;
      $(id).value = cur === 'IQD' ? Math.round(v * r) : +(v / r).toFixed(2);
    });
  }
  currentCurrency = cur; paintCurrency(); renderCart();
}
function updateCardPrices() {
  document.querySelectorAll('.pc').forEach(c => {
    const el = c.querySelector('.p-iqd');
    if (el) el.textContent = Math.round((parseFloat(c.dataset.priceUsd) || 0) * rate).toLocaleString() + ' IQD';
  });
}

/* گەڕان و فلتەر */
function filterCategory(id) {
  document.querySelectorAll('.cat-filter-btn').forEach(b => b.classList.remove('on'));
  $('cat-btn-' + id)?.classList.add('on');
  document.querySelectorAll('.pc').forEach(c => c.style.display = (id === 'all' || c.dataset.category == id) ? 'flex' : 'none');
}
function searchProducts() {
  const q = $('searchBox').value.toLowerCase().trim();
  document.querySelectorAll('.pc').forEach(c => c.style.display = (c.dataset.name.toLowerCase().includes(q) || c.dataset.code.toLowerCase().includes(q)) ? 'flex' : 'none');
}

/* یەکە و سەبەتە */
function cartonUnit() {
  return units.find(u => /کارتۆن|carton/i.test(u.name || '')) || defaultUnit();
}
function defaultUnit() {
  return units.find(u => /کیلۆ|kg/i.test(u.name || '')) || units[0] || { id: 1, name: 'کیلۆ', factor_to_base: 1 };
}
function unitFactor(p, u) {
  if (p.sell_type === 'carton') return 1;   // کارتۆنی: نرخ و کۆگا هەموو بە کارتۆن
  const n = (u?.name || '').toLowerCase();
  if (n.includes('کارتۆن') || n.includes('carton')) return parseFloat(p.kg_per_carton) || 1;
  if (n.includes('تەن') || n.includes('ton')) return 1000;
  return parseFloat(u?.factor_to_base) || 1;
}
function addToCart(id) {
  const p = allProducts.find(x => x.id == id); if (!p) return false;
  const stock = parseFloat(p.stock_kg ?? p.stock ?? 0);
  const idx = cart.findIndex(i => i.id === p.id);
  if (stock <= 0 && idx === -1) { showToast('نەماوە!', 'error'); return false; }
  if (idx !== -1) {
    if (cart[idx].qty + 1 > maxQty(cart[idx])) { showToast('بڕی کۆگا تەواو بوو'); return false; }
    cart[idx].qty++;
  } else {
    const isC = p.sell_type === 'carton';
    const u = isC ? cartonUnit() : defaultUnit();
    cart.push({ carton: isC, id: p.id, name: p.name, code: p.code, price_usd: parseFloat(p.base_sale_price) || 0, stock_kg: stock,
      kg_per_carton: parseFloat(p.kg_per_carton) || 1, qty: 1, unit_id: u.id, factor: unitFactor(p, u) });
  }
  renderCart(); return true;
}
function quickIncrease(id, e) { e.stopPropagation(); addToCart(id); }
function quickDecrease(id, e) {
  e.stopPropagation();
  const i = cart.findIndex(x => x.id === id); if (i === -1) return;
  if (cart[i].qty > 1) cart[i].qty--; else cart.splice(i, 1);
  renderCart();
}
function updateItemPrice(i, v) { const p = parseFloat(v) || 0; cart[i].price_usd = currentCurrency === 'USD' ? p : p / rate; renderCart(); }
function updateItemUnit(i, uid) {
  const it = cart[i], p = allProducts.find(x => x.id == it.id) || it;
  it.unit_id = uid; it.factor = unitFactor(p, units.find(u => u.id == uid));
  it.qty = Math.min(it.qty, maxQty(it)); renderCart();
}
function updateQty(i, d) {
  const it = cart[i], n = it.qty + d;
  if (n > maxQty(it)) { it.qty = maxQty(it); showToast('بڕی کۆگا تەواو بوو'); } else if (n <= 0) cart.splice(i, 1); else it.qty = n;
  renderCart();
}
function setQtyDirect(i, v) { let n = parseFloat(v); if (isNaN(n) || n <= 0) n = 1; cart[i].qty = Math.min(n, maxQty(cart[i])); renderCart(); }
function removeItem(i) { cart.splice(i, 1); renderCart(); }
function handleClearCartTwoClicks() {
  if (!cart.length) return;
  if (!confirmingClear) { confirmingClear = true; $('clearCartLabel').innerText = 'دڵنیایت؟'; clearTimer = setTimeout(resetClear, 3000); }
  else { clearTimeout(clearTimer); cart = []; $('cartDiscount').value = 0; renderCart(); resetClear(); }
}
function resetClear() { confirmingClear = false; $('clearCartLabel').innerText = 'سڕینەوە'; }

function updateBadges() {
  document.querySelectorAll('.badge').forEach(b => b.style.display = 'none');
  cart.forEach(i => { const b = $('qty-badge-' + i.id); if (b) { b.querySelector('.badge-val').innerText = +i.qty.toFixed(2); b.style.display = 'inline-flex'; } });
}
function renderCart() {
  rate = getRate();
  const box = $('cartItemsContainer'); box.innerHTML = '';
  let subtotal = 0, weight = 0;
  $('cartCount').innerText = cart.length;
  if (!cart.length) {
    box.innerHTML = `<div class="h-full min-h-[8rem] flex flex-col items-center justify-center text-[11px] font-bold gap-2" style="color:var(--mu)"><i class="fa-solid fa-cart-arrow-down text-3xl opacity-50"></i>سەبەتە بەتاڵە<span class="text-[10px] font-normal">کلیک لە کاڵا بکە بۆ زیادکردن</span></div>`;
  }
  cart.forEach((it, idx) => {
    const price = toDisp(it.price_usd), line = it.qty * price * it.factor;
    subtotal += line;
    weight += it.carton ? it.qty * (it.kg_per_carton || 1) : it.qty * it.factor;
    const opts = units.map(u => `<option value="${u.id}" ${it.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('');
    const d = document.createElement('div');
    d.className = 'rowin rounded-xl p-2.5 border'; d.style.cssText = 'background:var(--sf2);border-color:var(--bd)';
    d.innerHTML = `
      <div class="flex justify-between items-center mb-2">
        <h4 class="font-extrabold text-[11px] truncate">${it.name}</h4>
        <button type="button" onclick="removeItem(${idx})" class="text-[11px]" style="color:var(--ro)"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="grid grid-cols-12 gap-1.5 items-center">
        ${it.carton ? `<div class="inp col-span-4 !px-1.5 text-center">کارتۆن</div>` : `<select onchange="updateItemUnit(${idx}, this.value)" class="inp col-span-4 !px-1.5">${opts}</select>`}
        <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? price.toFixed(2) : Math.round(price)}" onchange="updateItemPrice(${idx}, this.value)" class="inp num col-span-4 text-center" style="color:var(--ac)">
        <div class="col-span-4 flex items-center justify-between">
          <button type="button" class="sq add !w-6 !h-6 !rounded-md" onclick="updateQty(${idx}, 1)">+</button>
          <input type="number" step="any" min="0.01" value="${+it.qty.toFixed(3)}" onchange="setQtyDirect(${idx}, this.value)" class="num w-9 text-center bg-transparent font-extrabold text-[11px] focus:outline-none">
          <button type="button" class="sq sub !w-6 !h-6 !rounded-md" onclick="updateQty(${idx}, -1)">-</button>
        </div>
      </div>
      <div class="num text-left text-[11px] font-extrabold mt-1.5" style="color:var(--ac)">${money(line)}</div>`;
    box.appendChild(d);
  });
  const discount = parseFloat($('cartDiscount').value) || 0;
  const total = Math.max(0, subtotal - discount);
  const other = currentCurrency === 'USD' ? 'IQD' : 'USD';
  const alt = currentCurrency === 'USD' ? total * rate : total / rate;
  $('subTotalText').innerText = money(subtotal);
  $('cartWeight').innerText = (+weight.toFixed(2)).toLocaleString() + ' کگ';
  $('grandTotalText').innerText = money(total);
  $('grandAltText').innerText = total > 0 ? '≈ ' + money(alt, other) : '';
  if (!cart.length) resetClear();
  updateBadges(); updateCardPrices();
}

function togglePaymentType() {
  const debt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
  $('paidAmountBox').classList.toggle('hidden', !debt);
}

/* تۆمارکردنی فرۆشتن */
let isSubmitting = false;   // ڕێگری لە دووجار کلیک
let savedSaleId = null;     // دوای یەکەم تۆمارکردن، پاشەکەوتکردنی دواتر هەمان وەسڵ نوێ دەکاتەوە (دووبارە نابێتەوە)
let lastUse = {};           // ئەو بڕەی ئەم وەسڵە لە کۆگا بردوویەتی
let lastDiscount = 0;

function adjustStock(pid, delta) {
  const p = allProducts.find(x => x.id == pid); if (!p) return;
  const key = p.stock_kg !== undefined ? 'stock_kg' : 'stock';
  p[key] = (parseFloat(p[key]) || 0) + delta;
  const el = $('stock-' + pid); if (el) el.textContent = +(+p[key]).toFixed(2);
}
function applySaleUse(items) {
  Object.keys(lastUse).forEach(pid => adjustStock(pid, lastUse[pid]));   // بڕە کۆنەکە دەگەڕێتەوە
  lastUse = {};
  items.forEach(i => { lastUse[i.id] = (lastUse[i.id] || 0) + i.qty * i.factor; });
  Object.keys(lastUse).forEach(pid => adjustStock(pid, -lastUse[pid]));
}
const labelNew = '<i class="fa-solid fa-paper-plane"></i> پسوولەکردن';
const labelUpdate = '<i class="fa-solid fa-floppy-disk"></i> نوێکردنەوەی پسوولە';

function submitSale() {
  if (isSubmitting) return;
  if (!cart.length) { showToast('کاڵا نییە!', 'error'); return; }
  const debt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
  const customerId = $('customerId').value;
  if (debt && !customerId) { showToast('کڕیار دیاری بکە بۆ قەرز', 'error'); return; }

  const targetId = editSale ? editSale.id : savedSaleId;   // ئەگەر وەسڵەکە پێشتر تۆمارکراوە، نوێ دەکرێتەوە نەک دووبارە
  const isEdit = !!targetId;
  const btn = $('btnSubmitSale');
  isSubmitting = true; btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوان بە...';
  const sentItems = JSON.parse(JSON.stringify(cart));
  const sentDiscount = parseFloat($('cartDiscount').value) || 0;

  fetch(isEdit ? '/sales/' + targetId : '/sales', {
    method: isEdit ? 'PUT' : 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify({
      customer_id: customerId, payment_type: debt ? 'debt' : 'cash',
      paid_amount: debt ? parseFloat($('paidAmount').value) || 0 : null,
      discount: sentDiscount,
      created_at: $('saleCreatedAt').value, currency: currentCurrency, exchange_rate: getRate(),
      items: cart.map(i => ({ product_id: i.id, unit_id: i.unit_id, quantity: i.qty, base_price: i.price_usd }))
    })
  }).then(r => r.json()).then(data => {
    isSubmitting = false; btn.disabled = false;
    if (data.success) {
      activeSaleId = data.sale_id || targetId;
      if (!editSale) savedSaleId = activeSaleId;
      lastSaleItems = sentItems; lastDiscount = sentDiscount;
      applySaleUse(sentItems);
      btn.innerHTML = labelUpdate;
      $('printA4Btn').href = '/sales/print/' + activeSaleId + '?type=a4';
      $('printSmallBtn').href = '/sales/print/' + activeSaleId + '?type=small';
      renderModalItems(); $('successModal').classList.remove('hidden');
    } else { btn.innerHTML = (editSale || savedSaleId) ? labelUpdate : labelNew; showToast(data.error || data.message || 'هەڵە', 'error'); }
  }).catch(() => { isSubmitting = false; btn.disabled = false; btn.innerHTML = (editSale || savedSaleId) ? labelUpdate : labelNew; showToast('کێشەیەک ڕوویدا', 'error'); });
}

function renderModalItems() {
  const list = $('modalItemsList'); list.innerHTML = ''; let total = 0;
  lastSaleItems.forEach(it => {
    const price = toDisp(it.price_usd), line = it.qty * price * it.factor; total += line;
    const u = units.find(x => x.id == it.unit_id);
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 p-2.5 rounded-xl border text-xs'; row.style.cssText = 'background:var(--sf2);border-color:var(--bd)';
    row.innerHTML = `
      <div class="flex-1 font-extrabold truncate">${it.name}</div>
      <div class="num">${+it.qty.toFixed(3)} <span class="text-[10px]" style="color:var(--mu)">${it.carton ? 'کارتۆن' : (u ? u.name : '')}</span></div>
      <div class="num w-20 text-center" style="color:var(--mu)">${currentCurrency === 'USD' ? price.toFixed(2) : Math.round(price)}</div>
      <div class="num font-extrabold w-24 text-left" style="color:var(--ac)">${money(line)}</div>`;
    list.appendChild(row);
  });
  if (lastDiscount > 0) {
    const d = document.createElement('div');
    d.className = 'flex justify-between text-xs px-2.5'; d.style.color = 'var(--mu)';
    d.innerHTML = `<span>داشکاندن</span><span class="num">-${money(lastDiscount)}</span>`; list.appendChild(d);
  }
  $('modalGrandTotal').innerText = money(Math.max(0, total - lastDiscount));
}

/* دوای تۆمارکردن: دوگمەکانی مۆداڵ */
function closeKeepEditing() { $('successModal').classList.add('hidden'); }   // سەبەتە دەمێنێتەوە، پاشەکەوتی داهاتوو هەمان وەسڵ نوێ دەکاتەوە
function startNewSale() {
  $('successModal').classList.add('hidden');
  if (editSale) { window.location.href = '{{ route('pos.index') }}'; return; }
  cart = []; savedSaleId = null; lastUse = {}; lastSaleItems = []; lastDiscount = 0;
  $('cartDiscount').value = 0; $('paidAmount').value = 0; $('customerId').value = '';
  document.querySelector('input[name="paymentType"][value="cash"]').checked = true; togglePaymentType();
  $('saleCreatedAt').value = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 16);
  $('btnSubmitSale').innerHTML = labelNew;
  renderCart();
}
function afterPrint() { if (!editSale) setTimeout(startNewSale, 600); }   // دوای کردنەوەی چاپ، POS ئامادەی وەسڵی نوێ دەبێت

/* نرخی ئاڵوگۆڕ */
function toggleRateEdit() {
  const e = $('rateEdit'); e.classList.toggle('hidden');
  if (!e.classList.contains('hidden')) setTimeout(() => { $('newExchangeRate').focus(); $('newExchangeRate').select(); }, 50);
}
function saveExchangeRate() {
  const n = parseFloat($('newExchangeRate').value);
  if (!n || n < 1) { showToast('تکایە نرخێکی دروست بنووسە', 'error'); return; }
  const b = $('rateSaveBtn'); b.disabled = true;
  fetch('/update-exchange-rate', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify({ exchange_rate: n })
  }).then(r => r.json()).then(d => {
    b.disabled = false;
    if (d.success) {
      $('currentRateDisplay').innerText = '1$ = ' + n.toLocaleString();
      $('exchangeRate').value = n; renderCart(); $('rateEdit').classList.add('hidden');
      showToast('نرخی ئاڵوگۆڕ نوێکرایەوە', 'success');
    } else showToast(d.message || 'هەڵەیەک ڕوویدا', 'error');
  }).catch(() => { b.disabled = false; showToast('کێشەیەک ڕوویدا', 'error'); });
}
$('newExchangeRate').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); saveExchangeRate(); } else if (e.key === 'Escape') $('rateEdit').classList.add('hidden'); });

/* دەستکاریکردنی وەسڵی پێشوو */
function loadEditSale() {
  if (!editSale) return;
  $('exchangeRate').value = editSale.exchange_rate; rate = parseFloat(editSale.exchange_rate) || rate;
  currentCurrency = editSale.currency || 'USD';
  $('customerId').value = editSale.customer_id || '';
  $('saleCreatedAt').value = editSale.created_at;
  $('cartDiscount').value = editSale.discount || 0;
  const radio = document.querySelector('input[name="paymentType"][value="' + editSale.payment_type + '"]');
  if (radio) radio.checked = true;
  $('paidAmount').value = editSale.paid_amount || 0; togglePaymentType();
  cart = editSale.items.map(it => {
    const p = allProducts.find(x => x.id == it.product_id); if (!p) return null;
    const unit = units.find(u => u.id == it.unit_id) || units[0], factor = unitFactor(p, unit);
    const now = parseFloat(p.stock_kg ?? p.stock ?? 0);
    return { carton: p.sell_type === 'carton', id: p.id, name: p.name, code: p.code, price_usd: parseFloat(it.price_usd) || 0, stock_kg: now + it.quantity * factor,
      kg_per_carton: parseFloat(p.kg_per_carton) || 1, qty: parseFloat(it.quantity), unit_id: unit.id, factor };
  }).filter(Boolean);
  lastUse = {}; cart.forEach(i => { lastUse[i.id] = (lastUse[i.id] || 0) + i.qty * i.factor; });
  paintCurrency();
  $('editBanner').classList.remove('hidden'); $('editInvoiceNo').innerText = editSale.invoice_no;
  $('btnSubmitSale').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> نوێکردنەوەی پسوولە';
  renderCart();
}
renderCart(); loadEditSale();
</script>
</body>
</html>