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
/* ===== ڕووناک (ڕەنگە خۆڵەمێشییەکانی خۆت پارێزراون) ===== */
:root{
  --bg:#cfcfcf; --sf:#ececec; --sf2:#e3e3e3; --bd:#c0c0c0;
  --tx:#0b1b33; --mu:#5a5a5a; --card:#ffffff;
  --ac:#0f766e; --ac2:#0e7490; --acs:#d8f3ee; --on:#ffffff;
  --wa:#b45309; --was:#fff0cc; --ro:#dc2626; --ros:#ffe2e2;
  --shadow:0 2px 4px rgba(0,0,0,.06),0 10px 28px -14px rgba(0,0,0,.25);
  --shadow-sm:0 1px 3px rgba(0,0,0,.08),0 4px 10px -6px rgba(0,0,0,.15);
  --page:radial-gradient(900px 420px at 88% -8%,rgba(14,116,144,.08),transparent 60%),radial-gradient(700px 380px at 5% 105%,rgba(15,118,110,.07),transparent 60%),linear-gradient(180deg,#d5d5d5,#bfbfbf);
}
/* ===== تاریک (ڕەنگەکانی تاریک گەڕێندرانەوە) ===== */
.dark{
  --bg:#131c2e; --sf:#1a2640; --sf2:#213050; --bd:#2f4066; --card:#1f2d4a;
  --tx:#e8eefb; --mu:#9fb0cc;
  --ac:#2dd4bf; --ac2:#38bdf8; --acs:#0f3f43; --on:#03201d;
  --wa:#fbbf24; --was:#3a2a0b; --ro:#fb7185; --ros:#3f1523;
  --shadow:0 0 0 1px rgba(255,255,255,.05) inset,0 14px 34px -14px rgba(0,0,0,.6);
  --shadow-sm:0 6px 14px -8px rgba(0,0,0,.55);
  --page:radial-gradient(900px 480px at 88% -10%,rgba(56,189,248,.12),transparent 60%),radial-gradient(760px 420px at 0% 110%,rgba(45,212,191,.10),transparent 60%),#131c2e;
}
body{font-family:'Almarai',sans-serif;background:var(--page);background-attachment:fixed;color:var(--tx)}
.num{font-family:'Plus Jakarta Sans',sans-serif;direction:ltr;unicode-bidi:isolate}

/* پانێڵەکان */
.sf{background:var(--sf);border:1px solid var(--bd);border-radius:1.25rem;box-shadow:var(--shadow)}
.panel-head{margin:-.75rem -.75rem .75rem;padding:.8rem .75rem;background:var(--sf2);border-bottom:1px solid var(--bd);border-radius:1.25rem 1.25rem 0 0}
.panel-foot{margin:0 -.75rem -.75rem;padding:.8rem .75rem;background:var(--sf2);border-top:1px solid var(--bd);border-radius:0 0 1.25rem 1.25rem}
.app-header{padding:.6rem .85rem}
.brand-tile{background:linear-gradient(135deg,var(--ac),var(--ac2))!important;color:var(--on)!important;box-shadow:0 8px 18px -8px var(--ac)}

/* خانەکان */
.inp{background:var(--card);border:1px solid var(--bd);border-radius:.8rem;padding:.5rem .7rem;font-size:11px;font-weight:700;color:var(--tx);width:100%;box-shadow:inset 0 1px 2px rgba(15,35,70,.04);transition:border-color .15s,box-shadow .15s}
.inp:hover{border-color:color-mix(in srgb,var(--ac) 45%,var(--bd))}
.inp:focus,button:focus-visible,a:focus-visible{outline:none;border-color:var(--ac);box-shadow:0 0 0 3px color-mix(in srgb,var(--ac) 22%,transparent)}

/* دوگمە و چیپ */
.chip{background:var(--card);border:1px solid var(--bd);color:var(--mu);border-radius:999px;padding:.4rem .95rem;font-size:11px;font-weight:800;white-space:nowrap;transition:.15s;box-shadow:var(--shadow-sm)}
.chip:hover{color:var(--tx);border-color:var(--ac)}
.chip.on{background:linear-gradient(135deg,var(--ac),var(--ac2));border-color:transparent;color:var(--on);box-shadow:0 8px 16px -8px var(--ac)}
.sq{width:2.1rem;height:2.1rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-size:11px;transition:.12s}
.sq:active{transform:scale(.9)}
.sq.add{background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);box-shadow:0 6px 12px -6px var(--ac)}
.sq.add:hover{filter:brightness(1.08)}
.sq.sub{background:var(--card);color:var(--mu);border:1px solid var(--bd)}
.sq.sub:hover{color:var(--ro);border-color:var(--ro)}

/* مینیو */
.nav-group{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.2rem;background:var(--sf2);border:1px solid var(--bd);border-radius:1rem;padding:.22rem}
.nav{display:flex;align-items:center;gap:.4rem;padding:.45rem .8rem;border-radius:.8rem;font-size:11px;font-weight:800;color:var(--mu);transition:.15s;white-space:nowrap}
.nav:hover{background:var(--card);color:var(--tx);box-shadow:var(--shadow-sm)}
.nav i{color:var(--ac)}
.nav.hl{background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);box-shadow:0 6px 14px -8px var(--ac)}
.nav.hl i{color:var(--on)}
.nav.hl:hover{background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);filter:brightness(1.08)}
#moreDropdown .nav{width:100%}

/* کاڵاکان */
.pc{background:var(--card);border:1px solid var(--bd);border-radius:1.05rem;padding:.7rem;display:flex;flex-direction:column;justify-content:space-between;gap:.55rem;position:relative;overflow:visible;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease,background .18s ease;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 12px -6px rgba(0,0,0,.12);will-change:transform}
.pc::before{content:'';position:absolute;top:0;left:14px;right:14px;height:3px;border-radius:0 0 6px 6px;background:linear-gradient(90deg,var(--ac),var(--ac2));opacity:0;transition:opacity .18s}
.pc h3{transition:color .18s}
.pc.low{border-color:color-mix(in srgb,var(--wa) 55%,var(--bd))}
.pc.low::before{background:var(--wa);opacity:1}
.pc.out{opacity:.62;background:color-mix(in srgb,var(--ros) 45%,var(--card))}
.pc.out::before{background:var(--ro);opacity:1}
/* هۆڤەر: تەنها لەسەر ئامێرێک کە ماوس هەیە (لە مۆبایل نامێنێتەوە) */
@media (hover:hover){
  .pc:hover{
    border-color:var(--ac);
    background:linear-gradient(180deg,color-mix(in srgb,var(--ac) 11%,var(--card)),var(--card) 75%);
    transform:translateY(-5px) scale(1.015);
    box-shadow:0 0 0 2px color-mix(in srgb,var(--ac) 38%,transparent),0 20px 32px -14px color-mix(in srgb,var(--ac) 60%,transparent),0 8px 16px -8px rgba(0,0,0,.22);
  }
  .pc:hover::before{opacity:1}
  .pc:hover h3{color:var(--ac)}
  .pc:hover .sq.add{transform:scale(1.1);box-shadow:0 10px 18px -6px var(--ac)}
  .pc.out:hover{transform:none;box-shadow:var(--shadow-sm);border-color:var(--ro)}
}
.pc:active{transform:translateY(-1px) scale(.995)}

/* دوگمەی نەقد/قەرز و دراو */
.seg{display:flex;background:var(--card);border:1px solid var(--bd);border-radius:.9rem;padding:.2rem;gap:.2rem;box-shadow:inset 0 1px 2px rgba(15,35,70,.05)}
.seg>*{flex:1;text-align:center;padding:.42rem .5rem;border-radius:.7rem;font-size:11px;font-weight:800;color:var(--mu);cursor:pointer;transition:.15s}
.seg>*:hover{color:var(--tx)}
.seg>.on,.seg>label:has(input:checked){background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);box-shadow:0 6px 12px -7px var(--ac)}
.seg>label.debt:has(input:checked){background:var(--wa);color:var(--on)}
.seg input{display:none}
.badge{position:absolute;top:-.55rem;left:-.55rem;background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);border-radius:999px;padding:.12rem .6rem;font-size:12px;font-weight:800;border:2px solid var(--card);display:none;box-shadow:var(--shadow-sm);z-index:2}

/* سەبەتە */
.rowin{animation:rin .25s ease-out;box-shadow:var(--shadow-sm);background:var(--card)!important;border-color:var(--bd)!important}
@keyframes rin{from{opacity:0;transform:translateY(6px)}}
#cartCount{display:inline-block}
.total-card{background:linear-gradient(135deg,var(--acs),color-mix(in srgb,var(--acs) 55%,var(--sf)))!important;border:1px solid color-mix(in srgb,var(--ac) 30%,transparent)}
#btnSubmitSale{background:linear-gradient(135deg,var(--ac),var(--ac2))!important;color:var(--on)!important;box-shadow:0 14px 24px -12px var(--ac)}
#btnSubmitSale:hover{filter:brightness(1.07)}
#btnSubmitSale:disabled{opacity:.7}

/* جووڵەی فڕین: + بۆ سەبەتە، - لە سەبەتەوە */
.fly-chip{position:fixed;z-index:2000;pointer-events:none;min-width:30px;height:30px;padding:0 9px;border-radius:999px;display:flex;align-items:center;justify-content:center;font:800 12px 'Plus Jakarta Sans',sans-serif;color:var(--on);background:linear-gradient(135deg,var(--ac),var(--ac2));box-shadow:0 10px 20px -6px var(--ac);will-change:transform,opacity}
.fly-chip.sub{background:linear-gradient(135deg,#fb7185,#e11d48);color:#fff;box-shadow:0 10px 20px -6px #e11d48}
.bump{animation:bump .38s ease}
@keyframes bump{0%{transform:scale(1)}40%{transform:scale(1.3)}100%{transform:scale(1)}}

.scroll::-webkit-scrollbar{width:7px;height:7px}.scroll::-webkit-scrollbar-thumb{background:color-mix(in srgb,var(--mu) 40%,transparent);border-radius:9px}
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none}input[type=number]{-moz-appearance:textfield}

/* ===== مۆبایل: سەبەتە دەبێتە پەنجەرەی خوارەوە ===== */
.cart-fab,.cart-backdrop{display:none}
@media (max-width:1023px){
  main{padding-bottom:5.2rem}
  .pc-section{height:auto!important}
  .pc-section #productsGrid{overflow:visible;max-height:none}
  .pc-section .panel-head{position:sticky;top:0;z-index:20;box-shadow:var(--shadow-sm)}
  .cart-panel{position:fixed;left:0;right:0;bottom:0;z-index:70;height:90vh!important;border-radius:1.4rem 1.4rem 0 0;transform:translateY(110%);transition:transform .28s ease;box-shadow:0 -20px 50px -20px rgba(0,0,0,.5)}
  .cart-panel.open{transform:translateY(0)}
  .cart-backdrop{display:block;position:fixed;inset:0;z-index:65;background:rgba(5,10,25,.55);backdrop-filter:blur(2px);opacity:0;pointer-events:none;transition:opacity .25s}
  .cart-backdrop.show{opacity:1;pointer-events:auto}
  .cart-fab{display:flex;position:fixed;left:.75rem;right:.75rem;bottom:.75rem;z-index:60;align-items:center;justify-content:space-between;gap:.75rem;padding:.85rem 1.05rem;border-radius:1.1rem;background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);font-weight:800;font-size:13px;box-shadow:0 16px 30px -12px var(--ac)}
  body.cart-open .cart-fab{display:none}
}
/* سەبەتەی گەورەتر */
.cart-panel{overflow-y:auto}
#cartItemsContainer{min-height:9.5rem}
.cart-panel .panel-head .inp{font-size:12.5px;padding:.55rem .75rem}
.cart-panel .seg>*{font-size:12.5px;padding:.5rem .6rem}
.cart-panel #grandTotalText{font-size:1.9rem}

/* ===== دوگمە و بەرواری بچووک، کڕیاری گەورە ===== */
.cart-panel .seg.sm{padding:.12rem;border-radius:.7rem}
.cart-panel .seg.sm>*{font-size:10.5px;padding:.28rem .5rem;border-radius:.55rem;white-space:nowrap}
.cart-panel .panel-head .inp.sm{font-size:10.5px;padding:.34rem .45rem;border-radius:.65rem}
.cart-panel .panel-head .inp.cust-big{font-size:15px;font-weight:800;padding:.85rem 1rem;border-radius:1rem}

/* ===== مۆبایل: لیستی بچووکی کاڵاکانی سەبەتە لەسەر دوگمەی خوارەوە ===== */
.mini-cart{display:none}
@media (max-width:1023px){
  .mini-cart.has-items{display:block;position:fixed;left:.75rem;right:.75rem;bottom:4.7rem;z-index:59;max-height:30vh;overflow-y:auto;padding:.4rem .7rem .3rem}
  body.mini-on main{padding-bottom:calc(6.2rem + 30vh)}
  body.cart-open .mini-cart{display:none!important}
}
.flash{animation:flash 1.1s ease-out}
@keyframes flash{0%{box-shadow:0 0 0 3px var(--ac),0 14px 26px -8px var(--ac)}100%{box-shadow:var(--shadow-sm)}}
@media (prefers-reduced-motion:reduce){.rowin,.bump,.flash{animation:none}.pc,.chip,.nav,.cart-panel{transition:none}}
</style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
<body class="min-h-screen lg:h-screen p-2 flex flex-col gap-2 lg:overflow-hidden select-none">

<!-- هێدەر -->
<header class="sf app-header flex flex-wrap items-center justify-between gap-2 shrink-0 relative z-40">
  <div class="flex items-center gap-2.5">
    <div class="brand-tile w-10 h-10 rounded-xl flex items-center justify-center text-lg"><i class="fa-solid fa-cash-register"></i></div>
    <div><h1 class="font-extrabold text-sm leading-none">POS</h1><p class="text-[10px] mt-1 flex items-center gap-1" style="color:var(--mu)"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>فرۆشتن</p></div>
  </div>

  <nav class="nav-group">
    <a href="{{ route('purchases.create') }}" class="nav hl"><i class="fa-solid fa-box-open"></i> کڕین</a>
    <a href="{{ route('purchases.index') }}" class="nav"><i class="fa-solid fa-file-invoice"></i> وەسڵەکانی کڕین</a>
    <a href="{{ route('sales.list') }}" class="nav"><i class="fa-solid fa-receipt"></i> فرۆشتنەکان</a>
    <a href="{{ route('products.index') }}" class="nav"><i class="fa-solid fa-boxes-stacked"></i> کۆگا</a>
    <a href="{{ route('customers.index') }}" class="nav"><i class="fa-solid fa-users"></i> کڕیار</a>
    <a href="{{ route('reports.index') }}" class="nav"><i class="fa-solid fa-chart-pie"></i> ڕاپۆرت</a>
    <div class="relative">
      <button type="button" class="nav" onclick="event.stopPropagation();document.getElementById('moreDropdown').classList.toggle('hidden')"><i class="fa-solid fa-ellipsis"></i> زیاتر <i class="fa-solid fa-chevron-down text-[8px]"></i></button>
      <div id="moreDropdown" class="hidden sf absolute right-0 top-full mt-2 w-56 p-1.5 text-xs shadow-2xl scroll max-h-96 overflow-y-auto z-50">
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

<main class="flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_470px] xl:grid-cols-[minmax(0,1fr)_580px] gap-2">

  <!-- کاڵاکان -->
  <section class="sf pc-section p-3 flex flex-col min-h-0 h-[70vh] lg:h-auto">
    <div class="shrink-0 space-y-2.5 panel-head">
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

    <div id="productsGrid" class="grow overflow-y-auto scroll pt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-3 content-start">
      @foreach($products as $p)
        @php
          $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
          $alertVal = (float) ($p->alert_quantity ?? 5);
          $isOut = $stockVal <= 0; $isLow = !$isOut && $stockVal <= $alertVal;
          $isCarton = ($p->sell_type ?? 'weight') === 'carton';
        @endphp
        <div class="pc {{ $isOut ? 'out' : ($isLow ? 'low' : '') }}" data-category="{{ $p->category_id }}" data-name="{{ $p->name }}" data-code="{{ $p->code }}" data-price-usd="{{ $p->base_sale_price }}">
          <div id="qty-badge-{{ $p->id }}" class="badge num"><i class="fa-solid fa-check text-[10px]"></i> <span class="badge-val">0</span></div>
          <div class="cursor-pointer space-y-2" onclick="addToCart({{ $p->id }}, event)">
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
  <aside class="sf cart-panel p-3 flex flex-col min-h-0 h-[85vh] lg:h-auto">
    <div class="shrink-0 space-y-2.5 panel-head">
      <div class="flex items-center justify-between">
        <h2 class="font-extrabold text-base flex items-center gap-2 flex-wrap"><i class="fa-solid fa-cart-shopping" style="color:var(--ac)"></i> سەبەتە
          <span class="inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--acs);color:var(--ac)" title="ژمارەی کاڵا"><i class="fa-solid fa-cubes text-[10px]"></i><span id="cartCount" class="num">0</span> دانە</span>
          <span id="cartTypesBox" class="hidden inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--was);color:var(--wa)" title="ژمارە بەپێی جۆر"><i class="fa-solid fa-layer-group text-[10px]"></i><span id="cartTypes" class="num"></span></span>
          <span class="inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--sf);color:var(--mu);border:1px solid var(--bd)" title="کێشی گشتی"><i class="fa-solid fa-weight-hanging text-[10px]"></i><span id="cartWeight" class="num">0 کگ</span></span>
        </h2>
        <div class="flex items-center gap-1.5"><button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" class="chip !py-1.5 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span></button><button type="button" class="sq sub lg:hidden" onclick="closeCart()" title="داخستن"><i class="fa-solid fa-chevron-down"></i></button></div>
      </div>

      <div id="editBanner" class="hidden rounded-xl px-3 py-2 text-[10px] font-extrabold flex items-center justify-between" style="background:var(--was);color:var(--wa)">
        <span><i class="fa-solid fa-pen-to-square"></i> دەستکاریکردنی وەسڵ <span id="editInvoiceNo" class="num"></span></span>
        <a href="{{ route('pos.index') }}" class="underline">پسوولەی نوێ</a>
      </div>

      <!-- دراو + نرخ + بەروار (بچووک) -->
      <div class="flex items-center gap-1.5 flex-wrap">
        <div class="seg sm">
          <button type="button" id="btn-cur-usd" onclick="setCurrency('USD')" class="on">$ دۆلار</button>
          <button type="button" id="btn-cur-iqd" onclick="setCurrency('IQD')">دینار</button>
        </div>
        <input type="number" id="exchangeRate" value="{{ $setting->exchange_rate ?? 1500 }}" onchange="renderCart()" class="inp sm num !w-16 text-center" title="نرخی ئاڵوگۆڕی ئەم وەسڵە">
        <input type="datetime-local" id="saleCreatedAt" value="{{ date('Y-m-d\TH:i') }}" class="inp sm num flex-1 !w-auto min-w-[9.5rem]">
      </div>

      <!-- کڕیار (گەورە) -->
      <select id="customerId" class="inp cust-big">
        <option value="">کڕیاری نەقد</option>
        @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
      </select>

      <div class="seg sm">
        <label><input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()"><i class="fa-solid fa-money-bill-wave"></i> نەقد</label>
        <label class="debt"><input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()"><i class="fa-solid fa-clock"></i> قەرز</label>
      </div>
      <div id="paidAmountBox" class="hidden"><input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0" class="inp num"></div>
    </div>

    <div id="cartItemsContainer" class="grow overflow-y-auto scroll py-2.5 space-y-2"></div>

    <div class="shrink-0 space-y-2 panel-foot">
      <div class="grid grid-cols-2 gap-3 items-center text-[12px] font-bold">
        <div class="flex justify-between items-center"><span style="color:var(--mu)">کۆی کاڵا</span><span id="subTotalText" class="num">$0.00</span></div>
        <div class="flex items-center justify-end gap-2"><span style="color:var(--mu)">داشکاندن</span><input type="number" min="0" id="cartDiscount" value="0" oninput="renderCart()" class="inp num !w-24 !py-1.5 text-left"></div>
      </div>
      <div class="total-card rounded-2xl px-3.5 py-3 flex justify-between items-end">
        <span class="font-extrabold text-sm" style="color:var(--ac)">کۆی گشتی</span>
        <div class="text-left leading-tight"><div id="grandTotalText" class="num font-extrabold text-2xl" style="color:var(--ac)">$0.00</div><div id="grandAltText" class="num text-[10px] font-bold" style="color:var(--mu)"></div></div>
      </div>
      <button type="button" onclick="submitSale()" id="btnSubmitSale" class="w-full py-4 rounded-2xl font-extrabold text-[15px] flex items-center justify-center gap-2 transition active:scale-[.98]" style="background:var(--ac);color:var(--bg)"><i class="fa-solid fa-paper-plane"></i> پسوولەکردن</button>
    </div>
  </aside>
</main>

<!-- مۆبایل: دوگمەی سەبەتە و پەردە -->
<div id="cartBackdrop" class="cart-backdrop" onclick="closeCart()"></div>
<div id="miniCart" class="mini-cart sf scroll">
  <div class="flex items-center justify-between pb-1 mb-1 border-b sticky top-0" style="border-color:var(--bd);background:var(--sf)">
    <span class="text-[11px] font-extrabold" style="color:var(--mu)"><i class="fa-solid fa-basket-shopping"></i> کاڵاکانی سەبەتە</span>
    <button type="button" class="text-[10px] font-extrabold px-2 py-1 rounded-lg" style="color:var(--ac)" onclick="openCart()">کردنەوە <i class="fa-solid fa-chevron-up"></i></button>
  </div>
  <div id="miniCartItems"></div>
</div>
<button type="button" id="cartFab" class="cart-fab" onclick="openCart()"><span><i class="fa-solid fa-cart-shopping"></i> سەبەتە · <b id="fabCount" class="num">0</b> دانە · <b id="fabWeight" class="num">0</b> کگ</span><span id="fabTotal" class="num">$0.00</span></button>

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
let lastAddedId = null;   // دوا کاڵایەی زیادکرا یان کەمکرایەوە، لە سەبەتە دەدرەوشێتەوە
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
function addToCart(id, e) {
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
  lastAddedId = p.id; renderCart();
  if (e && e.currentTarget) fly(e.currentTarget, cartTarget(), '+1', 'add');   // دانە بەرەو سەبەتە دەفڕێت
  return true;
}
function quickIncrease(id, e) { e.stopPropagation(); addToCart(id, e); }
function quickDecrease(id, e) {
  e.stopPropagation();
  const i = cart.findIndex(x => x.id === id); if (i === -1) return;
  const btn = e.currentTarget;
  if (cart[i].qty > 1) { cart[i].qty--; lastAddedId = id; }   // فۆکەس بچێتە سەر هەمان کاڵا لە سەبەتە
  else cart.splice(i, 1);                                    // ئەگەر لابرا، شتێک نەماوە فۆکەسی بکرێت
  renderCart();
  fly(cartTarget(), btn, '−1', 'sub');   // دانە لە سەبەتەوە دەگەڕێتەوە بۆ کاڵاکە
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
  let subtotal = 0, weight = 0, qtySum = 0, addedEl = null;
  const typeTotals = {};   // کۆی بڕ بەپێی جۆر (کارتۆن / کیلۆ / ...)
  let mini = '';
  if (!cart.length) {
    box.innerHTML = `<div class="h-full min-h-[8rem] flex flex-col items-center justify-center text-[11px] font-bold gap-2" style="color:var(--mu)"><i class="fa-solid fa-cart-arrow-down text-3xl opacity-50"></i>سەبەتە بەتاڵە<span class="text-[10px] font-normal">کلیک لە کاڵا بکە بۆ زیادکردن</span></div>`;
  }
  cart.forEach((it, idx) => {
    const price = toDisp(it.price_usd), line = it.qty * price * it.factor;
    subtotal += line; qtySum += it.qty;
    weight += it.carton ? it.qty * (it.kg_per_carton || 1) : it.qty * it.factor;
    const unitObj = units.find(u => u.id == it.unit_id);
    const typeName = it.carton ? 'کارتۆن' : (unitObj ? unitObj.name : 'دانە');
    typeTotals[typeName] = (typeTotals[typeName] || 0) + it.qty;
    mini += `<div class="flex items-center gap-2 py-1.5 border-b" style="border-color:var(--bd)">
      <button type="button" class="sq sub !w-7 !h-7 !rounded-lg" onclick="updateQty(${idx},-1)"><i class="fa-solid fa-minus text-[10px]"></i></button>
      <span class="num font-extrabold text-[12px] w-9 text-center">${+it.qty.toFixed(2)}</span>
      <button type="button" class="sq add !w-7 !h-7 !rounded-lg" onclick="updateQty(${idx},1)"><i class="fa-solid fa-plus text-[10px]"></i></button>
      <div class="flex-1 min-w-0"><div class="font-extrabold text-[12px] truncate">${it.name}</div><div class="text-[10px]" style="color:var(--mu)">${typeName}</div></div>
      <div class="num font-extrabold text-[12px]" style="color:var(--ac)">${money(line)}</div>
    </div>`;
    const opts = units.map(u => `<option value="${u.id}" ${it.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('');
    const d = document.createElement('div');
    d.className = 'rowin rounded-xl p-3 border'; d.style.cssText = 'background:var(--sf2);border-color:var(--bd)';
    d.innerHTML = `
      <div class="flex justify-between items-center mb-2">
        <h4 class="font-extrabold text-[14px] truncate">${it.name}</h4>
        <button type="button" onclick="removeItem(${idx})" class="text-[14px] px-1" style="color:var(--ro)"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="grid grid-cols-12 gap-1.5 items-center">
        ${it.carton ? `<div class="inp col-span-4 !px-1.5 !py-2 !text-[13px] text-center">کارتۆن</div>` : `<select onchange="updateItemUnit(${idx}, this.value)" class="inp col-span-4 !px-1.5 !py-2 !text-[13px]">${opts}</select>`}
        <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? price.toFixed(2) : Math.round(price)}" onchange="updateItemPrice(${idx}, this.value)" class="inp num col-span-4 text-center !py-2 !text-[14px]" style="color:var(--ac)">
        <div class="col-span-4 flex items-center justify-between">
          <button type="button" class="sq add !w-8 !h-8 !rounded-lg !text-sm" onclick="updateQty(${idx}, 1)">+</button>
          <input type="number" step="any" min="0.01" value="${+it.qty.toFixed(3)}" onchange="setQtyDirect(${idx}, this.value)" class="num w-12 text-center bg-transparent font-extrabold text-[15px] focus:outline-none">
          <button type="button" class="sq sub !w-8 !h-8 !rounded-lg !text-sm" onclick="updateQty(${idx}, -1)">-</button>
        </div>
      </div>
      <div class="num text-left text-[14px] font-extrabold mt-2" style="color:var(--ac)">${money(line)}</div>`;
    if (it.id === lastAddedId) { d.classList.add('flash'); addedEl = d; }
    box.appendChild(d);
  });
  if (addedEl) addedEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  lastAddedId = null;
  // نیشاندانی ژمارە بەپێی جۆر: نموونە «٣ کارتۆن + ٢ کیلۆ»
  const typeText = Object.keys(typeTotals).map(k => (+typeTotals[k].toFixed(2)) + ' ' + k).join(' + ');
  $('cartCount').innerText = +qtySum.toFixed(2);
  $('cartTypes').innerText = typeText;
  $('cartTypesBox').classList.toggle('hidden', !typeText);
  const discount = parseFloat($('cartDiscount').value) || 0;
  const total = Math.max(0, subtotal - discount);
  const other = currentCurrency === 'USD' ? 'IQD' : 'USD';
  const alt = currentCurrency === 'USD' ? total * rate : total / rate;
  $('subTotalText').innerText = money(subtotal);
  $('cartWeight').innerText = (+weight.toFixed(2)).toLocaleString() + ' کگ';
  $('grandTotalText').innerText = money(total);
  $('grandAltText').innerText = total > 0 ? '≈ ' + money(alt, other) : '';
  if (!cart.length) resetClear();
  $('miniCartItems').innerHTML = mini;
  $('miniCart').classList.toggle('has-items', cart.length > 0);
  document.body.classList.toggle('mini-on', cart.length > 0);
  updateBadges(); updateCardPrices(); updateFab(total, qtySum, weight);
}

function togglePaymentType() {
  const debt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
  $('paidAmountBox').classList.toggle('hidden', !debt);
}

/* ===== جووڵەی فڕین و سەبەتەی مۆبایل ===== */
function centerOf(el) { const r = el.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top + r.height / 2 }; }
function cartTarget() { return (window.innerWidth < 1024 && $('cartFab')) ? $('cartFab') : $('cartCount'); }
function bump(el) { if (!el) return; el.classList.remove('bump'); void el.offsetWidth; el.classList.add('bump'); }
function fly(fromEl, toEl, text, kind) {
  if (!fromEl || !toEl || !fromEl.isConnected || !toEl.isConnected) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { bump(toEl); return; }
  const a = centerOf(fromEl), b = centerOf(toEl);
  const dx = b.x - a.x, dy = b.y - a.y, lift = Math.min(90, Math.abs(dx) * 0.25 + 40);
  const el = document.createElement('div');
  el.className = 'fly-chip ' + kind; el.textContent = text;
  el.style.left = a.x + 'px'; el.style.top = a.y + 'px';
  document.body.appendChild(el);
  const anim = el.animate([
    { transform: 'translate(-50%,-50%) scale(1)', opacity: 1 },
    { transform: `translate(calc(-50% + ${dx * 0.5}px), calc(-50% + ${dy * 0.5 - lift}px)) scale(1.15)`, opacity: 1, offset: 0.5 },
    { transform: `translate(calc(-50% + ${dx}px), calc(-50% + ${dy}px)) scale(.45)`, opacity: 0.2 }
  ], { duration: 650, easing: 'cubic-bezier(.45,.05,.35,1)' });
  anim.onfinish = () => { el.remove(); bump(toEl); };
}
function openCart() { document.querySelector('.cart-panel').classList.add('open'); $('cartBackdrop').classList.add('show'); document.body.classList.add('cart-open'); }
function closeCart() { document.querySelector('.cart-panel').classList.remove('open'); $('cartBackdrop').classList.remove('show'); document.body.classList.remove('cart-open'); }
function updateFab(total, qty, weight) { $('fabCount').textContent = +(+qty).toFixed(2); $('fabWeight').textContent = +(+weight).toFixed(2); $('fabTotal').textContent = money(total); }

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
  $('successModal').classList.add('hidden'); closeCart();
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