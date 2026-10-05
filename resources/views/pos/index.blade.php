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
/* ==================== ڕووناک (Light Mode) - دیزاینی نوێ ==================== */
:root{
  --bg:#f3f5fa;
  --bg-grad-1:#eef2fb;
  --bg-grad-2:#f7f4ff;
  --sf:rgba(255,255,255,0.85);
  --sf-solid:#ffffff;
  --sf2:#f6f8fd;
  --bd:rgba(226,232,240,0.8);
  --bd-solid:#e5e9f2;
  --tx:#0d1424;
  --mu:#6b7a99;
  --ac:#0d9488;
  --ac-2:#14b8a6;
  --ac-grad:linear-gradient(135deg,#0d9488 0%,#14b8a6 100%);
  --acs:#d5f5ef;
  --acs-2:#e6fbf7;
  --wa:#d97706;
  --was:#fef3c7;
  --ro:#e11d48;
  --ros:#ffe4e6;
  --sh-sm:0 1px 2px rgba(13,20,36,.04), 0 1px 3px rgba(13,20,36,.05);
  --sh-md:0 4px 16px -4px rgba(13,20,36,.08), 0 2px 6px -2px rgba(13,20,36,.05);
  --sh-lg:0 12px 32px -8px rgba(13,20,36,.12), 0 4px 12px -4px rgba(13,20,36,.06);
  --sh-glow:0 8px 24px -8px rgba(13,148,136,.35);
  --ring:rgba(13,148,136,.15);
}

/* ==================== تاریک (Dark Mode) ==================== */
.dark{
  --bg:#080e1a;
  --bg-grad-1:#080e1a;
  --bg-grad-2:#0a1424;
  --sf:rgba(16,24,40,0.85);
  --sf-solid:#101828;
  --sf2:#16213a;
  --bd:rgba(32,46,74,0.9);
  --bd-solid:#202e4a;
  --tx:#e6edf7;
  --mu:#8da0bd;
  --ac:#2dd4bf;
  --ac-2:#5eead4;
  --ac-grad:linear-gradient(135deg,#14b8a6 0%,#2dd4bf 100%);
  --acs:#0c3a3a;
  --acs-2:#0f4444;
  --wa:#fbbf24;
  --was:#3a2a08;
  --ro:#fb7185;
  --ros:#3d1420;
  --sh-sm:0 1px 2px rgba(0,0,0,.3);
  --sh-md:0 4px 16px -4px rgba(0,0,0,.5);
  --sh-lg:0 12px 32px -8px rgba(0,0,0,.6);
  --sh-glow:0 8px 24px -8px rgba(45,212,191,.35);
  --ring:rgba(45,212,191,.2);
}

*{transition:background-color .2s ease, border-color .2s ease, color .15s ease, box-shadow .2s ease}

body{
  font-family:'Almarai',sans-serif;
  background:var(--bg);
  background-image:
    radial-gradient(at 0% 0%, var(--bg-grad-1) 0px, transparent 50%),
    radial-gradient(at 100% 0%, var(--bg-grad-2) 0px, transparent 50%),
    radial-gradient(at 50% 100%, var(--bg-grad-1) 0px, transparent 50%);
  background-attachment:fixed;
  color:var(--tx);
  min-height:100vh;
}

.num{font-family:'Plus Jakarta Sans',sans-serif;direction:ltr;unicode-bidi:isolate}

/* کارتەکان - Glassmorphism */
.sf{
  background:var(--sf);
  backdrop-filter:blur(20px) saturate(180%);
  -webkit-backdrop-filter:blur(20px) saturate(180%);
  border:1px solid var(--bd);
  border-radius:1.25rem;
  box-shadow:var(--sh-md);
}

.inp{
  background:var(--sf2);
  border:1.5px solid var(--bd-solid);
  border-radius:.75rem;
  padding:.55rem .8rem;
  font-size:11px;
  font-weight:700;
  color:var(--tx);
  width:100%;
  transition:all .2s ease;
}
.inp:hover{border-color:var(--ac-2)}
.inp:focus{
  outline:none;
  border-color:var(--ac);
  box-shadow:0 0 0 4px var(--ring);
  background:var(--sf-solid);
}

button:focus-visible,a:focus-visible{outline:2px solid var(--ac);outline-offset:2px;border-radius:.5rem}

/* چیپ */
.chip{
  background:var(--sf2);
  border:1.5px solid var(--bd-solid);
  color:var(--mu);
  border-radius:999px;
  padding:.45rem 1rem;
  font-size:11px;
  font-weight:800;
  white-space:nowrap;
  transition:all .18s ease;
  cursor:pointer;
}
.chip:hover{
  color:var(--tx);
  border-color:var(--ac-2);
  transform:translateY(-1px);
  box-shadow:var(--sh-sm);
}
.chip.on{
  background:var(--ac-grad);
  border-color:transparent;
  color:#fff;
  box-shadow:var(--sh-glow);
}

/* ناڤ */
.nav{
  display:flex;
  align-items:center;
  gap:.45rem;
  padding:.5rem .85rem;
  border-radius:.75rem;
  font-size:11px;
  font-weight:800;
  color:var(--mu);
  transition:all .15s ease;
  white-space:nowrap;
  cursor:pointer;
  text-decoration:none;
}
.nav:hover{
  background:var(--acs-2);
  color:var(--ac);
  transform:translateX(-2px);
}

/* کارتی کاڵا */
.pc{
  background:var(--sf-solid);
  border:1.5px solid var(--bd-solid);
  border-radius:1.1rem;
  padding:.75rem;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  gap:.55rem;
  position:relative;
  transition:all .22s cubic-bezier(.2,.8,.2,1);
  box-shadow:var(--sh-sm);
  overflow:hidden;
}
.pc::before{
  content:'';
  position:absolute;
  inset:0;
  border-radius:inherit;
  padding:1.5px;
  background:var(--ac-grad);
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;
  mask-composite:exclude;
  opacity:0;
  transition:opacity .22s ease;
  pointer-events:none;
}
.pc:hover{
  transform:translateY(-3px);
  box-shadow:var(--sh-lg);
  border-color:transparent;
}
.pc:hover::before{opacity:1}
.pc.out{opacity:.55;filter:grayscale(.3)}
.pc.low{border-color:var(--wa);background:linear-gradient(180deg,var(--sf-solid) 70%,var(--was) 200%)}

/* دوگمەی چوارگۆشە */
.sq{
  width:2.1rem;
  height:2.1rem;
  border-radius:.7rem;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  transition:all .15s ease;
  cursor:pointer;
  border:none;
}
.sq:active{transform:scale(.9)}
.sq.add{
  background:var(--ac-grad);
  color:#fff;
  box-shadow:var(--sh-glow);
}
.sq.add:hover{filter:brightness(1.08);transform:translateY(-1px)}
.sq.sub{
  background:var(--sf2);
  color:var(--mu);
  border:1.5px solid var(--bd-solid);
}
.sq.sub:hover{
  color:var(--ro);
  border-color:var(--ro);
  background:var(--ros);
}

/* سێگمێنت */
.seg{
  display:flex;
  background:var(--sf2);
  border:1.5px solid var(--bd-solid);
  border-radius:.85rem;
  padding:.25rem;
  gap:.25rem;
}
.seg>*{
  flex:1;
  text-align:center;
  padding:.5rem .6rem;
  border-radius:.65rem;
  font-size:11px;
  font-weight:800;
  color:var(--mu);
  cursor:pointer;
  transition:all .18s ease;
}
.seg>*:hover{color:var(--tx)}
.seg>.on,.seg>label:has(input:checked){
  background:var(--ac-grad);
  color:#fff;
  box-shadow:var(--sh-glow);
}
.seg>label.debt:has(input:checked){
  background:linear-gradient(135deg,#d97706,#f59e0b);
  color:#fff;
  box-shadow:0 8px 24px -8px rgba(217,119,6,.35);
}
.seg input{display:none}

/* باج */
.badge{
  position:absolute;
  top:-.5rem;
  left:-.5rem;
  background:var(--ac-grad);
  color:#fff;
  border-radius:999px;
  padding:.15rem .6rem;
  font-size:12px;
  font-weight:800;
  border:2px solid var(--sf-solid);
  display:none;
  box-shadow:var(--sh-glow);
  z-index:10;
}

/* سکرۆڵبار */
.scroll::-webkit-scrollbar{width:8px;height:8px}
.scroll::-webkit-scrollbar-track{background:transparent}
.scroll::-webkit-scrollbar-thumb{
  background:var(--bd-solid);
  border-radius:9px;
  border:2px solid transparent;
  background-clip:content-box;
}
.scroll::-webkit-scrollbar-thumb:hover{background:var(--ac);background-clip:content-box}

/* ئەنیمەیشن */
.rowin{animation:rin .3s cubic-bezier(.2,.8,.2,1)}
@keyframes rin{from{opacity:0;transform:translateY(8px)}}

input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none}
input[type=number]{-moz-appearance:textfield}

@media (prefers-reduced-motion:reduce){
  *{transition:none!important;animation:none!important}
}
</style>
</head>
<body class="min-h-screen lg:h-screen p-2 flex flex-col gap-2 lg:overflow-hidden select-none">

<!-- هێدەر -->
<header class="sf px-3 py-2.5 flex flex-wrap items-center justify-between gap-2 shrink-0 relative z-40">
  <div class="flex items-center gap-2.5">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background:var(--ac-grad);color:#fff;box-shadow:var(--sh-glow)"><i class="fa-solid fa-cash-register"></i></div>
    <div><h1 class="font-extrabold text-sm leading-none">POS</h1><p class="text-[10px] mt-1 flex items-center gap-1" style="color:var(--mu)"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>فرۆشتن</p></div>
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
      <div id="rateEdit" class="hidden sf absolute left-0 top-full mt-2 p-3 w-56 shadow-2xl z-50 space-y-2">
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
        <h2 class="font-extrabold text-sm flex items-center gap-2"><i class="fa-solid fa-cart-shopping" style="color:var(--ac)"></i> سەبەتە <span id="cartCount" class="num text-[10px] px-2 py-0.5 rounded-md" style="background:var(--acs);color:var(--ac)">0</span></h2>
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
      <div class="rounded-2xl px-4 py-3 flex justify-between items-end" style="background:var(--ac-grad);box-shadow:var(--sh-glow)">
        <span class="font-extrabold text-sm" style="color:#fff">کۆی گشتی</span>
        <div class="text-left leading-tight"><div id="grandTotalText" class="num font-extrabold text-2xl" style="color:#fff">$0.00</div><div id="grandAltText" class="num text-[10px] font-bold" style="color:rgba(255,255,255,.85)"></div></div>
      </div>
      <button type="button" onclick="submitSale()" id="btnSubmitSale" class="w-full py-3.5 rounded-2xl font-extrabold text-[13px] flex items-center justify-center gap-2 transition active:scale-[.98]" style="background:var(--ac-grad);color:#fff;box-shadow:var(--sh-glow)"><i class="fa-solid fa-paper-plane"></i> پسوولەکردن</button>
    </div>
  </aside>
</main>

<!-- مۆداڵی سەرکەوتن -->
<div id="successModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-3 z-[999]">
  <div class="sf w-full max-w-lg p-5 flex flex-col max-h-[90vh] shadow-2xl rowin">
    <div class="flex items-center justify-between pb-3 border-b" style="border-color:var(--bd)">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg" style="background:var(--ac-grad);color:#fff;box-shadow:var(--sh-glow)"><i class="fa-solid fa-check"></i></div>
        <div><h3 class="font-extrabold">وەسڵ تۆمارکرا</h3><p class="text-[10px]" style="color:var(--mu)">چاپ بکە، یان وەسڵی نوێ دەست پێبکە</p></div>
      </div>
      <button type="button" onclick="startNewSale()" class="sq sub" title="داخستن و وەسڵی نوێ"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div id="modalItemsList" class="grow overflow-y-auto scroll py-3 space-y-2"></div>
    <div class="pt-3 border-t space-y-3" style="border-color:var(--bd)">
      <div class="flex justify-between items-center rounded-2xl px-4 py-3" style="background:var(--acs)"><span class="font-extrabold text-sm" style="color:var(--ac)">کۆی گشتی</span><span id="modalGrandTotal" class="num font-extrabold text-xl" style="color:var(--ac)">$0.00</span></div>
      <div class="grid grid-cols-2 gap-2 text-xs font-extrabold">
        <a href="#" id="printA4Btn" target="_blank" onclick="afterPrint()" class="py-3 rounded-xl text-center" style="background:var(--ac-grad);color:#fff;box-shadow:var(--sh-glow)"><i class="fa-solid fa-file-lines"></i> چاپی A4</a>
        <a href="#" id="printSmallBtn" target="_blank" onclick="afterPrint()" class="py-3 rounded-xl text-center" style="background:var(--sf2);border:1.5px solid var(--bd-solid)"><i class="fa-solid fa-receipt"></i> چاپی بچووک</a>
        <button type="button" onclick="closeKeepEditing()" class="py-3 rounded-xl" style="background:var(--sf2);border:1.5px solid var(--bd-solid)"><i class="fa-solid fa-pen-to-square"></i> دەستکاری ئەم وەسڵە</button>
        <button type="button" onclick="startNewSale()" class="py-3 rounded-xl" style="background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff"><i class="fa-solid fa-plus"></i> وەسڵی نوێ</button>
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
  if (p.sell_type === 'carton') return 1;
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
    d.className = 'rowin rounded-xl p-2.5 border';
    d.style.cssText = 'background:var(--sf2);border-color:var(--bd-solid)';
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
let isSubmitting = false;
let savedSaleId = null;
let lastUse = {};
let lastDiscount = 0;

function adjustStock(pid, delta) {
  const p = allProducts.find(x => x.id == pid); if (!p) return;
  const key = p.stock_kg !== undefined ? 'stock_kg' : 'stock';
  p[key] = (parseFloat(p[key]) || 0) + delta;
  const el = $('stock-' + pid); if (el) el.textContent = +(+p[key]).toFixed(2);
}
function applySaleUse(items) {
  Object.keys(lastUse).forEach(pid => adjustStock(pid, lastUse[pid]));
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

  const targetId = editSale ? editSale.id : savedSaleId;
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
    row.className = 'flex items-center gap-2 p-2.5 rounded-xl border text-xs';
    row.style.cssText = 'background:var(--sf2);border-color:var(--bd-solid)';
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

function closeKeepEditing() { $('successModal').classList.add('hidden'); }
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
function afterPrint() { if (!editSale) setTimeout(startNewSale, 600); }

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