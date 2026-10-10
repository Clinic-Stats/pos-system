<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>داواکاری کاڵا - {{ $setting->shop_name ?? '' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
:root{
  --bg:#cfcfcf; --sf:#ececec; --sf2:#e3e3e3; --bd:#c0c0c0;
  --tx:#0b1b33; --mu:#5a5a5a; --card:#ffffff;
  --ac:#0f766e; --ac2:#0e7490; --acs:#d8f3ee; --on:#ffffff;
  --wa:#b45309; --was:#fff0cc; --ro:#dc2626; --ros:#ffe2e2;
  --shadow:0 2px 4px rgba(0,0,0,.06),0 10px 28px -14px rgba(0,0,0,.25);
  --shadow-sm:0 1px 3px rgba(0,0,0,.08),0 4px 10px -6px rgba(0,0,0,.15);
}
body{font-family:'Almarai',sans-serif;color:var(--tx);background:radial-gradient(900px 420px at 88% -8%,rgba(14,116,144,.08),transparent 60%),radial-gradient(700px 380px at 5% 105%,rgba(15,118,110,.07),transparent 60%),linear-gradient(180deg,#d5d5d5,#bfbfbf);background-attachment:fixed}
.num{font-family:'Plus Jakarta Sans',sans-serif;direction:ltr;unicode-bidi:isolate}
.sf{background:var(--sf);border:1px solid var(--bd);border-radius:1.25rem;box-shadow:var(--shadow)}
.panel-head{margin:-.75rem -.75rem .75rem;padding:.8rem .75rem;background:var(--sf2);border-bottom:1px solid var(--bd);border-radius:1.25rem 1.25rem 0 0}
.panel-foot{margin:0 -.75rem -.75rem;padding:.8rem .75rem;background:var(--sf2);border-top:1px solid var(--bd);border-radius:0 0 1.25rem 1.25rem}
.brand-tile{background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);box-shadow:0 8px 18px -8px var(--ac)}
.inp{background:var(--card);border:1px solid var(--bd);border-radius:.8rem;padding:.5rem .7rem;font-size:12px;font-weight:700;color:var(--tx);width:100%;box-shadow:inset 0 1px 2px rgba(15,35,70,.04);transition:border-color .15s,box-shadow .15s}
.inp.big{font-size:14px;padding:.8rem .9rem;border-radius:.9rem}
.inp:focus,button:focus-visible,a:focus-visible{outline:none;border-color:var(--ac);box-shadow:0 0 0 3px color-mix(in srgb,var(--ac) 22%,transparent)}
.chip{background:var(--card);border:1px solid var(--bd);color:var(--mu);border-radius:999px;padding:.4rem .95rem;font-size:11px;font-weight:800;white-space:nowrap;transition:.15s;box-shadow:var(--shadow-sm)}
.chip:hover{color:var(--tx);border-color:var(--ac)}
.chip.on{background:linear-gradient(135deg,var(--ac),var(--ac2));border-color:transparent;color:var(--on)}
.sq{width:2.1rem;height:2.1rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-size:11px;transition:.12s;flex-shrink:0}
.sq:active{transform:scale(.9)}
.sq.add{background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);box-shadow:0 6px 12px -6px var(--ac)}
.sq.sub{background:var(--card);color:var(--mu);border:1px solid var(--bd)}
.sq.sub:hover{color:var(--ro);border-color:var(--ro)}
.btn{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:1rem;border-radius:1rem;font-weight:800;font-size:15px;transition:.15s}
.btn:active{transform:scale(.98)}
.btn-main{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff;box-shadow:0 14px 24px -12px var(--ac)}
.btn-main:disabled{opacity:.6}
.btn-soft{background:var(--card);border:1px solid var(--bd);color:var(--tx)}

/* کاڵاکان */
.pc{background:var(--card);border:1px solid var(--bd);border-radius:1.05rem;padding:.7rem;display:flex;flex-direction:column;justify-content:space-between;gap:.55rem;position:relative;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 12px -6px rgba(0,0,0,.12)}
.pc::before{content:'';position:absolute;top:0;left:14px;right:14px;height:3px;border-radius:0 0 6px 6px;background:linear-gradient(90deg,var(--ac),var(--ac2));opacity:0;transition:opacity .18s}
.pc.low{border-color:color-mix(in srgb,var(--wa) 55%,var(--bd))}.pc.low::before{background:var(--wa);opacity:1}
.pc.out{opacity:.62;background:color-mix(in srgb,var(--ros) 45%,var(--card))}.pc.out::before{background:var(--ro);opacity:1}
@media (hover:hover){.pc:not(.out):hover{border-color:var(--ac);transform:translateY(-4px);box-shadow:0 0 0 2px color-mix(in srgb,var(--ac) 38%,transparent),0 18px 30px -14px color-mix(in srgb,var(--ac) 60%,transparent)}.pc:not(.out):hover::before{opacity:1}}
.badge{position:absolute;top:-.55rem;left:-.55rem;background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);border-radius:999px;padding:.12rem .6rem;font-size:12px;font-weight:800;border:2px solid var(--card);display:none;box-shadow:var(--shadow-sm);z-index:2}
.rowin{animation:rin .25s ease-out;box-shadow:var(--shadow-sm)}
@keyframes rin{from{opacity:0;transform:translateY(6px)}}
.total-card{background:linear-gradient(135deg,var(--acs),color-mix(in srgb,var(--acs) 55%,var(--sf)));border:1px solid color-mix(in srgb,var(--ac) 30%,transparent)}
.scroll::-webkit-scrollbar{width:7px;height:7px}.scroll::-webkit-scrollbar-thumb{background:color-mix(in srgb,var(--mu) 40%,transparent);border-radius:9px}
input[type=number]::-webkit-inner-spin-button{-webkit-appearance:none}input[type=number]{-moz-appearance:textfield}
.hp{position:absolute;left:-9999px;top:-9999px;height:0;width:0;opacity:0}

/* مۆبایل: سەبەتە دەبێتە پەنجەرەی خوارەوە */
.cart-fab,.cart-backdrop{display:none}
@media (max-width:1023px){
  .shop-main{padding-bottom:5.2rem}
  .pc-section #productsGrid{overflow:visible;max-height:none}
  .pc-section .panel-head{position:sticky;top:0;z-index:20;box-shadow:var(--shadow-sm)}
  .cart-panel{position:fixed;left:0;right:0;bottom:0;z-index:70;height:90vh!important;border-radius:1.4rem 1.4rem 0 0;transform:translateY(110%);transition:transform .28s ease;box-shadow:0 -20px 50px -20px rgba(0,0,0,.5)}
  .cart-panel.open{transform:translateY(0)}
  .cart-backdrop{display:block;position:fixed;inset:0;z-index:65;background:rgba(5,10,25,.55);backdrop-filter:blur(2px);opacity:0;pointer-events:none;transition:opacity .25s}
  .cart-backdrop.show{opacity:1;pointer-events:auto}
  .cart-fab{display:none;position:fixed;left:.75rem;right:.75rem;bottom:.75rem;z-index:60;align-items:center;justify-content:space-between;gap:.75rem;padding:.85rem 1.05rem;border-radius:1.1rem;background:linear-gradient(135deg,var(--ac),var(--ac2));color:var(--on);font-weight:800;font-size:13px;box-shadow:0 16px 30px -12px var(--ac)}
  body.in-shop .cart-fab{display:flex}
  body.cart-open .cart-fab{display:none!important}
}
.cart-panel{overflow-y:auto}
#cartItems{min-height:9.5rem}
</style>
{{-- فۆنتی NRT و ڕێکخستنی گشتی سیستەم (هەمان ئەوەی لاپەڕەکانی تر) --}}
@include('partials.system-head')
</head>
<body class="min-h-screen">

<div class="p-2 flex flex-col gap-2 max-w-[1500px] mx-auto">

  <!-- هێدەر -->
  <header class="sf px-3.5 py-2.5 flex items-center justify-between gap-2">
    <div class="flex items-center gap-2.5">
      <div class="brand-tile w-10 h-10 rounded-xl flex items-center justify-center text-lg"><i class="fa-solid fa-basket-shopping"></i></div>
      <div>
        <h1 class="font-extrabold text-sm leading-tight">{{ $setting->shop_name ?? 'داواکاری کاڵا' }}</h1>
        <p class="text-[10px] mt-0.5" style="color:var(--mu)">داواکاری کاڵا</p>
      </div>
    </div>
    <div id="whoChip" class="hidden items-center gap-2 text-left min-w-0">
      <div class="min-w-0 text-right">
        <div id="whoName" class="font-extrabold text-[12px] truncate"></div>
        <div id="whoPhone" class="num text-[10px]" style="color:var(--mu)"></div>
      </div>
      <button type="button" class="chip" onclick="show('screen-ask')"><i class="fa-solid fa-pen"></i> گۆڕین</button>
    </div>
  </header>

  <input type="text" id="website" class="hp" tabindex="-1" autocomplete="off">

  <!-- ١) پرسیار -->
  <section id="screen-ask" class="screen sf p-5 space-y-4 max-w-xl w-full mx-auto">
    <h2 class="font-extrabold text-lg text-center leading-snug">ئایا کڕیاری هەمیشەیی و قەرزاری ئەم شوێنەیت؟</h2>
    <p class="text-center text-xs" style="color:var(--mu)">ئەگەر پێشتر لای ئێمە حیسابت هەیە «بەڵێ» هەڵبژێرە</p>
    <button type="button" class="btn btn-main" onclick="choose('existing')"><i class="fa-solid fa-user-check"></i> بەڵێ، کڕیاری هەمیشەییم</button>
    <button type="button" class="btn btn-soft" onclick="choose('new')"><i class="fa-solid fa-user-plus"></i> نەخێر، کڕیاری ئاسایم</button>
  </section>

  <!-- ٢ا) هەمیشەیی -->
  <section id="screen-existing" class="screen sf p-5 space-y-4 max-w-xl w-full mx-auto hidden">
    <h2 class="font-extrabold text-lg text-center">ژمارەی مۆبایلەکەت بنووسە</h2>
    <input type="tel" id="exPhone" class="inp big num text-center" placeholder="0750 000 0000" inputmode="tel" autocomplete="tel">
    <div id="exError" class="hidden rounded-xl px-3 py-2.5 text-xs font-extrabold" style="background:var(--ros);color:var(--ro)"></div>
    <div id="exFound" class="hidden rounded-2xl px-4 py-4 text-center space-y-1" style="background:var(--acs)">
      <div class="text-[11px] font-bold" style="color:var(--mu)">بەخێربێیت</div>
      <div id="exName" class="font-extrabold text-xl" style="color:var(--ac)"></div>
    </div>
    <button type="button" id="exCheckBtn" class="btn btn-main" onclick="checkPhone()"><i class="fa-solid fa-magnifying-glass"></i> پشکنین</button>
    <button type="button" id="exGoBtn" class="btn btn-main hidden" onclick="startShop()"><i class="fa-solid fa-arrow-left"></i> بەردەوامبە بۆ هەڵبژاردنی کاڵا</button>
    <button type="button" class="btn btn-soft" onclick="show('screen-ask')"><i class="fa-solid fa-arrow-right"></i> گەڕانەوە</button>
  </section>

  <!-- ٢ب) ئاسایی -->
  <section id="screen-new" class="screen sf p-5 space-y-3 max-w-xl w-full mx-auto hidden">
    <h2 class="font-extrabold text-lg text-center">زانیارییەکانت بنووسە</h2>
    <div><label class="text-xs font-bold block mb-1">ناو <span style="color:var(--ro)">*</span></label><input type="text" id="nwName" class="inp big" autocomplete="name"></div>
    <div><label class="text-xs font-bold block mb-1">ژمارەی مۆبایل <span style="color:var(--ro)">*</span></label><input type="tel" id="nwPhone" class="inp big num text-right" placeholder="0750 000 0000" inputmode="tel" autocomplete="tel"></div>
    <div><label class="text-xs font-bold block mb-1">ناونیشان <span style="color:var(--ro)">*</span></label><input type="text" id="nwAddress" class="inp big" placeholder="شار / گەڕەک / شەقام" autocomplete="street-address"></div>
    <div id="nwError" class="hidden rounded-xl px-3 py-2.5 text-xs font-extrabold" style="background:var(--ros);color:var(--ro)"></div>
    <button type="button" class="btn btn-main" onclick="goNew()"><i class="fa-solid fa-arrow-left"></i> بەردەوامبە بۆ هەڵبژاردنی کاڵا</button>
    <button type="button" class="btn btn-soft" onclick="show('screen-ask')"><i class="fa-solid fa-arrow-right"></i> گەڕانەوە</button>
  </section>

  <!-- ٣) فرۆشتن: کاڵاکان + سەبەتە (وەک POS) -->
  <main id="screen-shop" class="screen shop-main hidden grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_430px] gap-2 lg:h-[calc(100vh-5.2rem)]">

    <section class="sf pc-section p-3 flex flex-col min-h-0">
      <div class="shrink-0 space-y-2.5 panel-head">
        <div class="relative">
          <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-xs" style="color:var(--mu)"></i>
          <input type="text" id="searchBox" oninput="filterProducts()" placeholder="گەڕان بە ناو یان کۆد..." autocomplete="off" spellcheck="false" class="inp !py-2.5 !pr-9">
        </div>
        <div id="cats" class="flex gap-1.5 overflow-x-auto scroll pb-1"></div>
      </div>
      <div id="productsGrid" class="grow overflow-y-auto scroll pt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-3 content-start"></div>
    </section>

    <aside class="sf cart-panel p-3 flex flex-col min-h-0">
      <div class="shrink-0 space-y-2.5 panel-head">
        <div class="flex items-center justify-between">
          <h2 class="font-extrabold text-base flex items-center gap-2 flex-wrap"><i class="fa-solid fa-cart-shopping" style="color:var(--ac)"></i> سەبەتە
            <span class="inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--acs);color:var(--ac)"><i class="fa-solid fa-cubes text-[10px]"></i><span id="cartCount" class="num">0</span> دانە</span>
            <span id="cartTypesBox" class="hidden inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--was);color:var(--wa)"><i class="fa-solid fa-layer-group text-[10px]"></i><span id="cartTypes" class="num"></span></span>
            <span class="inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--sf);color:var(--mu);border:1px solid var(--bd)"><i class="fa-solid fa-list text-[10px]"></i><span id="cartKinds" class="num">0</span> جۆر</span>
          </h2>
          <div class="flex items-center gap-1.5">
            <button type="button" onclick="clearCartTwice()" class="chip !py-1.5 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> <span id="clearLabel">سڕینەوە</span></button>
            <button type="button" class="sq sub lg:hidden" onclick="closeCart()"><i class="fa-solid fa-chevron-down"></i></button>
          </div>
        </div>
      </div>

      <div id="cartItems" class="grow overflow-y-auto scroll py-2.5 space-y-2"></div>

      <div class="shrink-0 space-y-2 panel-foot">
        <div class="flex justify-between items-center text-[12px] font-bold"><span style="color:var(--mu)">کۆی نرخی کاڵاکان</span><span id="subTotal" class="num">$0.00</span></div>
        <div class="total-card rounded-2xl px-3.5 py-3 flex justify-between items-end">
          <span class="font-extrabold text-sm" style="color:var(--ac)">کۆی گشتی</span>
          <div class="text-left leading-tight">
            <div id="grandTotal" class="num font-extrabold text-2xl" style="color:var(--ac)">$0.00</div>
            <div id="grandAlt" class="num text-[10px] font-bold" style="color:var(--mu)"></div>
          </div>
        </div>
        <button type="button" onclick="goReview()" class="btn btn-main !py-4"><i class="fa-solid fa-paper-plane"></i> پسوولەکردن</button>
      </div>
    </aside>
  </main>

  <div id="cartBackdrop" class="cart-backdrop" onclick="closeCart()"></div>
  <button type="button" id="cartFab" class="cart-fab" onclick="openCart()"><span><i class="fa-solid fa-cart-shopping"></i> سەبەتە · <b id="fabCount" class="num">0</b> دانە</span><span id="fabTotal" class="num">$0.00</span></button>

  <!-- ٤) پێداچوونەوە و ناردن -->
  <section id="screen-review" class="screen sf p-4 space-y-3 max-w-2xl w-full mx-auto hidden">
    <div class="flex items-center justify-between gap-2">
      <h2 class="font-extrabold text-lg"><i class="fa-solid fa-clipboard-check" style="color:var(--ac)"></i> پێداچوونەوە بە داواکارییەکەت</h2>
      <span class="inline-flex items-center gap-1 text-[12px] font-extrabold px-2 py-0.5 rounded-lg" style="background:var(--acs);color:var(--ac)"><span id="rvCount" class="num">0</span> دانە · <span id="rvKinds" class="num">0</span> جۆر</span>
    </div>
    <p class="text-xs" style="color:var(--mu)">دەتوانیت لێرەش بڕ و یەکە بگۆڕیت یان کاڵا لابەریت، پاشان بینێرە.</p>
    <div id="reviewItems" class="space-y-2"></div>
    <div class="total-card rounded-2xl px-4 py-3 flex justify-between items-end">
      <span class="font-extrabold text-sm" style="color:var(--ac)">کۆی گشتی</span>
      <div class="text-left leading-tight">
        <div id="rvTotal" class="num font-extrabold text-2xl" style="color:var(--ac)">$0.00</div>
        <div id="rvAlt" class="num text-[10px] font-bold" style="color:var(--mu)"></div>
      </div>
    </div>
    <div>
      <label class="text-xs font-bold block mb-1">تێبینی (ئارەزوومەندانە)</label>
      <textarea id="note" rows="2" maxlength="500" class="inp" placeholder="نموونە: کاتی گەیاندن..."></textarea>
    </div>
    <div id="sendError" class="hidden rounded-xl px-3 py-2.5 text-xs font-extrabold" style="background:var(--ros);color:var(--ro)"></div>
    <button type="button" id="sendBtn" onclick="sendOrder()" class="btn btn-main"><i class="fa-solid fa-paper-plane"></i> ناردنی داواکاری</button>
    <button type="button" class="btn btn-soft" onclick="show('screen-shop')"><i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ کاڵاکان</button>
  </section>

  <!-- ٥) تەواو -->
  <section id="screen-done" class="screen sf p-6 space-y-4 text-center max-w-xl w-full mx-auto hidden">
    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-3xl" style="background:var(--acs);color:var(--ac)"><i class="fa-solid fa-check"></i></div>
    <h2 class="font-extrabold text-xl">داواکارییەکەت نێردرا</h2>
    <p class="text-sm" style="color:var(--mu)">ژمارەی داواکاری:</p>
    <div id="doneNo" class="num font-extrabold text-2xl" style="color:var(--ac)"></div>
    <p class="text-xs" style="color:var(--mu)">دوای پشکنین پەیوەندیت پێوە دەکەین.</p>
    <button type="button" class="btn btn-soft" onclick="location.reload()"><i class="fa-solid fa-plus"></i> داواکاری نوێ</button>
  </section>
</div>

<div id="toastContainer" class="fixed top-3 left-1/2 -translate-x-1/2 z-[1000] space-y-2 pointer-events-none flex flex-col items-center"></div>

<script>
const products   = @json($products);
const categories = @json($categories);
const units     = @json($units);
const RATE      = @json((float) ($setting->exchange_rate ?? 1500)) || 1500;
const $ = id => document.getElementById(id);
const csrf = document.querySelector('meta[name=csrf-token]').content;
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
const usd = v => '$' + (+v).toFixed(2);
const iqd = v => Math.round(v * RATE).toLocaleString() + ' IQD';
const fmtQty = n => +(+n).toFixed(3);

const state = { mode: null, name: '', phone: '', address: '' };
let cart = [];            // { id, name, code, price, stock, kgc, carton, qty, unit_id, factor }
let cat = 'all', clearTimer = null, confirmingClear = false;

/* ───── یەکە (هەمان لۆجیکی POS) ───── */
const cartonUnit = () => units.find(u => /کارتۆن|carton/i.test(u.name || '')) || defaultUnit();
const defaultUnit = () => units.find(u => /کیلۆ|kg/i.test(u.name || '')) || units[0] || { id: null, name: 'کیلۆ', factor_to_base: 1 };
function unitFactor(p, u) {
  if (p.sell_type === 'carton') return 1;
  const n = (u?.name || '').toLowerCase();
  if (n.includes('کارتۆن') || n.includes('carton')) return parseFloat(p.kg_per_carton) || 1;
  if (n.includes('تەن') || n.includes('ton')) return 1000;
  return parseFloat(u?.factor_to_base) || 1;
}
const maxQty = i => i.factor > 0 ? i.stock / i.factor : i.stock;
const pOf = id => products.find(x => x.id == id);

function toast(msg, type = 'warning') {
  const c = { error: ['var(--ros)', 'var(--ro)'], success: ['var(--acs)', 'var(--ac)'], warning: ['var(--was)', 'var(--wa)'] }[type];
  const t = document.createElement('div');
  t.className = 'pointer-events-auto px-4 py-2.5 rounded-xl text-[11px] font-extrabold shadow-xl border transition-all duration-300 -translate-y-6 opacity-0';
  t.style.cssText = `background:${c[0]};color:${c[1]};border-color:${c[1]}`; t.textContent = msg;
  $('toastContainer').appendChild(t);
  setTimeout(() => t.classList.remove('-translate-y-6', 'opacity-0'), 10);
  setTimeout(() => { t.classList.add('opacity-0'); setTimeout(() => t.remove(), 300); }, 2600);
}

/* ───── شاشەکان ───── */
function show(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.add('hidden'));
  $(id).classList.remove('hidden');
  document.body.classList.toggle('in-shop', id === 'screen-shop');
  document.body.classList.remove('cart-open'); $('cartBackdrop').classList.remove('show'); document.querySelector('.cart-panel').classList.remove('open');
  if (id === 'screen-review') renderReview();
  window.scrollTo(0, 0);
}
function choose(m) {
  state.mode = m;
  if (m === 'existing') { $('exError').classList.add('hidden'); $('exFound').classList.add('hidden'); $('exGoBtn').classList.add('hidden'); $('exCheckBtn').classList.remove('hidden'); show('screen-existing'); setTimeout(() => $('exPhone').focus(), 50); }
  else { show('screen-new'); setTimeout(() => $('nwName').focus(), 50); }
}
async function post(url, body) {
  const r = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(body) });
  let d = {}; try { d = await r.json(); } catch (e) {}
  return { ok: r.ok, status: r.status, d };
}
function errText(res) {
  if (res.status === 429) return 'زۆر هەوڵت دا، تکایە کەمێک چاوەڕێ بکە و دووبارە هەوڵ بدەرەوە.';
  if (res.d && res.d.errors) return Object.values(res.d.errors)[0][0];
  return (res.d && (res.d.error || res.d.message)) || 'کێشەیەک ڕوویدا، تکایە دووبارە هەوڵ بدەرەوە.';
}

/* کڕیاری هەمیشەیی */
async function checkPhone() {
  const phone = $('exPhone').value.trim(), err = $('exError');
  err.classList.add('hidden'); $('exFound').classList.add('hidden'); $('exGoBtn').classList.add('hidden');
  if (phone.replace(/\D/g, '').length < 9) { err.textContent = 'تکایە ژمارەیەکی دروست بنووسە.'; err.classList.remove('hidden'); return; }
  const btn = $('exCheckBtn'); btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوان بە...';
  const res = await post('{{ route('order.lookup') }}', { phone });
  btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> پشکنین';
  if (!res.ok) { err.textContent = errText(res); err.classList.remove('hidden'); return; }
  if (res.d.found) {
    state.name = res.d.name; state.phone = phone; state.address = '';
    $('exName').textContent = res.d.name; $('exFound').classList.remove('hidden'); $('exGoBtn').classList.remove('hidden'); btn.classList.add('hidden');
  } else {
    err.innerHTML = 'ئەم ژمارەیە لە کڕیارانی هەمیشەیی تۆمار نەکراوە. دەتوانیت وەک کڕیاری ئاسایی بەردەوام بیت: <button type="button" class="underline font-extrabold" onclick="choose(\'new\')">کڕیاری ئاسایم</button>';
    err.classList.remove('hidden');
  }
}
$('exPhone').addEventListener('input', () => { $('exFound').classList.add('hidden'); $('exGoBtn').classList.add('hidden'); $('exCheckBtn').classList.remove('hidden'); });
$('exPhone').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); checkPhone(); } });

/* کڕیاری ئاسایی */
function goNew() {
  const name = $('nwName').value.trim(), phone = $('nwPhone').value.trim(), address = $('nwAddress').value.trim(), err = $('nwError');
  err.classList.add('hidden');
  if (!name || !address || phone.replace(/\D/g, '').length < 9) { err.textContent = 'تکایە ناو، ژمارەی مۆبایلی دروست و ناونیشان بنووسە.'; err.classList.remove('hidden'); return; }
  state.name = name; state.phone = phone; state.address = address; startShop();
}

function startShop() {
  $('whoName').textContent = state.name; $('whoPhone').textContent = state.phone; $('whoChip').classList.remove('hidden'); $('whoChip').classList.add('flex');
  if (!$('productsGrid').children.length) { buildCats(); buildGrid(); }
  renderAll(); show('screen-shop');
}

/* ───── کاڵاکان (وەک POS) ───── */
function buildCats() {
  const used = new Set(products.map(p => String(p.category_id)));
  let h = `<button type="button" class="chip cat-btn on" data-c="all" onclick="setCat('all')">هەمووی</button>`;
  categories.filter(c => used.has(String(c.id))).forEach(c => { h += `<button type="button" class="chip cat-btn" data-c="${c.id}" onclick="setCat('${c.id}')">${esc(c.name)}</button>`; });
  $('cats').innerHTML = h;
}
function setCat(c) { cat = String(c); document.querySelectorAll('.cat-btn').forEach(b => b.classList.toggle('on', b.dataset.c === cat)); filterProducts(); }
function buildGrid() {
  $('productsGrid').innerHTML = products.map(p => {
    const out = p.stock <= 0, low = !out && p.stock <= p.alert, isC = p.sell_type === 'carton';
    return `<div class="pc ${out ? 'out' : (low ? 'low' : '')}" data-id="${p.id}" data-category="${p.category_id}" data-name="${esc(p.name).toLowerCase()}" data-code="${esc(p.code).toLowerCase()}">
      <div id="badge-${p.id}" class="badge num"><i class="fa-solid fa-check text-[10px]"></i> <span class="bv">0</span></div>
      <div class="${out ? '' : 'cursor-pointer'} space-y-2" ${out ? '' : `onclick="addToCart(${p.id})"`}>
        <div class="flex items-center justify-between gap-1">
          <span class="num text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--sf2);color:var(--mu)">${esc(p.code)}</span>
          ${out ? '<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--ros);color:var(--ro)">نەماوە</span>' : (low ? '<span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md" style="background:var(--was);color:var(--wa)">کەمە</span>' : '')}
        </div>
        <h3 class="font-extrabold text-[12px] leading-snug line-clamp-2 min-h-[2.4em]">${esc(p.name)}</h3>
        <p class="text-[10px] font-bold flex items-center gap-1" style="color:${out ? 'var(--ro)' : (low ? 'var(--wa)' : 'var(--mu)')}"><i class="fa-solid fa-cube text-[9px]"></i><span class="num" id="stock-${p.id}">${fmtQty(p.stock)}</span> ${isC ? 'کارتۆن' : 'کگ'}</p>
      </div>
      <div class="flex items-center justify-between gap-1 pt-2 border-t" style="border-color:var(--bd)">
        <button type="button" class="sq add" ${out ? 'disabled style="opacity:.4"' : ''} onclick="addToCart(${p.id})"><i class="fa-solid fa-plus"></i></button>
        <div class="text-center leading-tight">
          <div class="num font-extrabold text-[13px]" style="color:var(--ac)">${usd(p.price)}</div>
          <div class="num text-[9px] font-bold" style="color:var(--mu)">${iqd(p.price)}</div>
          ${isC ? '<div class="text-[9px] font-extrabold" style="color:var(--wa)">هەر کارتۆنێک</div>' : ''}
        </div>
        <button type="button" class="sq sub" onclick="quickDecrease(${p.id})"><i class="fa-solid fa-minus"></i></button>
      </div></div>`;
  }).join('');
}
function filterProducts() {
  const q = $('searchBox').value.toLowerCase().trim();
  document.querySelectorAll('#productsGrid .pc').forEach(c => {
    const okCat = cat === 'all' || c.dataset.category === cat;
    const okQ = !q || c.dataset.name.includes(q) || c.dataset.code.includes(q);
    c.style.display = okCat && okQ ? 'flex' : 'none';
  });
}

/* ───── سەبەتە ───── */
function addToCart(id) {
  const p = pOf(id); if (!p) return;
  if (p.stock <= 0) { toast('نەماوە!', 'error'); return; }
  const i = cart.findIndex(x => x.id === p.id);
  if (i !== -1) {
    if (cart[i].qty + 1 > maxQty(cart[i])) { toast('بڕی کۆگا تەواو بوو'); return; }
    cart[i].qty++;
  } else {
    const isC = p.sell_type === 'carton', u = isC ? cartonUnit() : defaultUnit();
    cart.push({ id: p.id, name: p.name, code: p.code, price: p.price, stock: p.stock, kgc: p.kg_per_carton || 1, carton: isC, qty: 1, unit_id: u.id, factor: unitFactor(p, u) });
  }
  renderAll();
}
function quickDecrease(id) {
  const i = cart.findIndex(x => x.id === id); if (i === -1) return;
  if (cart[i].qty > 1) cart[i].qty--; else cart.splice(i, 1);
  renderAll();
}
function updateQty(i, d) {
  const it = cart[i], n = it.qty + d;
  if (n > maxQty(it)) { it.qty = Math.floor(maxQty(it) * 100) / 100; toast('بڕی کۆگا تەواو بوو'); }
  else if (n <= 0) cart.splice(i, 1); else it.qty = n;
  renderAll();
}
function setQtyDirect(i, v) {
  let n = parseFloat(v); if (isNaN(n) || n <= 0) n = 1;
  const mx = maxQty(cart[i]); if (n > mx) { n = Math.floor(mx * 100) / 100; toast('بڕی کۆگا تەواو بوو'); }
  cart[i].qty = n; renderAll();
}
function updateItemUnit(i, uid) {
  const it = cart[i], p = pOf(it.id);
  it.unit_id = parseInt(uid); it.factor = unitFactor(p, units.find(u => u.id == uid));
  it.qty = Math.min(it.qty, Math.floor(maxQty(it) * 100) / 100) || 1; renderAll();
}
function removeItem(i) { cart.splice(i, 1); renderAll(); }
function clearCartTwice() {
  if (!cart.length) return;
  if (!confirmingClear) { confirmingClear = true; $('clearLabel').textContent = 'دڵنیایت؟'; clearTimer = setTimeout(resetClear, 3000); }
  else { clearTimeout(clearTimer); cart = []; renderAll(); resetClear(); }
}
function resetClear() { confirmingClear = false; $('clearLabel').textContent = 'سڕینەوە'; }

function rowHtml(it, idx) {
  const unitPrice = it.price * it.factor, line = it.qty * unitPrice;
  const opts = units.map(u => `<option value="${u.id}" ${it.unit_id == u.id ? 'selected' : ''}>${esc(u.name)}</option>`).join('');
  return `<div class="rowin rounded-xl p-3 border" style="background:var(--card);border-color:var(--bd)">
    <div class="flex justify-between items-center mb-2">
      <h4 class="font-extrabold text-[14px] truncate">${esc(it.name)}</h4>
      <button type="button" onclick="removeItem(${idx})" class="text-[14px] px-1" style="color:var(--ro)"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="grid grid-cols-12 gap-1.5 items-center">
      ${it.carton ? `<div class="inp col-span-4 !px-1.5 !py-2 text-center">کارتۆن</div>` : `<select onchange="updateItemUnit(${idx}, this.value)" class="inp col-span-4 !px-1.5 !py-2">${opts}</select>`}
      <div class="col-span-3 text-center leading-tight"><div class="num font-extrabold text-[13px]" style="color:var(--ac)">${usd(unitPrice)}</div><div class="text-[9px]" style="color:var(--mu)">نرخی یەکە</div></div>
      <div class="col-span-5 flex items-center justify-between">
        <button type="button" class="sq add !w-8 !h-8 !rounded-lg" onclick="updateQty(${idx}, 1)">+</button>
        <input type="number" step="any" min="0.01" value="${fmtQty(it.qty)}" onchange="setQtyDirect(${idx}, this.value)" class="num w-12 text-center bg-transparent font-extrabold text-[15px] focus:outline-none">
        <button type="button" class="sq sub !w-8 !h-8 !rounded-lg" onclick="updateQty(${idx}, -1)">-</button>
      </div>
    </div>
    <div class="mt-2 flex justify-between items-end"><span class="text-[10px]" style="color:var(--mu)">کۆی ئەم کاڵایە</span><div class="text-left leading-tight"><div class="num font-extrabold text-[14px]" style="color:var(--ac)">${usd(line)}</div><div class="num text-[9px]" style="color:var(--mu)">${iqd(line)}</div></div></div>
  </div>`;
}
function totals() {
  let sum = 0, qty = 0; const types = {};
  cart.forEach(it => {
    sum += it.qty * it.price * it.factor; qty += it.qty;
    const u = units.find(x => x.id == it.unit_id), n = it.carton ? 'کارتۆن' : (u ? u.name : 'دانە');
    types[n] = (types[n] || 0) + it.qty;
  });
  return { sum, qty, kinds: cart.length, typeText: Object.keys(types).map(k => fmtQty(types[k]) + ' ' + k).join(' + ') };
}
function renderAll() {
  const t = totals();
  $('cartItems').innerHTML = cart.length ? cart.map(rowHtml).join('')
    : `<div class="min-h-[8rem] h-full flex flex-col items-center justify-center text-[11px] font-bold gap-2" style="color:var(--mu)"><i class="fa-solid fa-cart-arrow-down text-3xl opacity-50"></i>سەبەتە بەتاڵە<span class="text-[10px] font-normal">کلیک لە کاڵا بکە بۆ زیادکردن</span></div>`;
  $('cartCount').textContent = fmtQty(t.qty); $('cartKinds').textContent = t.kinds;
  $('cartTypes').textContent = t.typeText; $('cartTypesBox').classList.toggle('hidden', !t.typeText);
  $('subTotal').textContent = usd(t.sum); $('grandTotal').textContent = usd(t.sum);
  $('grandAlt').textContent = t.sum > 0 ? '≈ ' + iqd(t.sum) : '';
  $('fabCount').textContent = fmtQty(t.qty); $('fabTotal').textContent = usd(t.sum);
  if (!cart.length) resetClear();

  // ماوەی کۆگا (بەپێی ئەوەی لە سەبەتەدایە) و بادجەکان
  const reserved = {}; cart.forEach(i => { reserved[i.id] = (reserved[i.id] || 0) + i.qty * i.factor; });
  products.forEach(p => { const el = $('stock-' + p.id); if (el) el.textContent = fmtQty(Math.max(0, p.stock - (reserved[p.id] || 0))); });
  document.querySelectorAll('#productsGrid .badge').forEach(b => b.style.display = 'none');
  cart.forEach(i => { const b = $('badge-' + i.id); if (b) { b.querySelector('.bv').textContent = fmtQty(i.qty); b.style.display = 'inline-flex'; } });
  if (!$('screen-review').classList.contains('hidden')) renderReview();
}
function openCart() { document.querySelector('.cart-panel').classList.add('open'); $('cartBackdrop').classList.add('show'); document.body.classList.add('cart-open'); }
function closeCart() { document.querySelector('.cart-panel').classList.remove('open'); $('cartBackdrop').classList.remove('show'); document.body.classList.remove('cart-open'); }

/* ───── پسوولەکردن: پێداچوونەوە ───── */
function goReview() {
  if (!cart.length) { toast('تکایە لانیکەم یەک کاڵا هەڵبژێرە.', 'error'); return; }
  show('screen-review');
}
function renderReview() {
  const t = totals();
  $('reviewItems').innerHTML = cart.length ? cart.map(rowHtml).join('') : '<div class="text-center text-sm font-bold py-6" style="color:var(--mu)">سەبەتە بەتاڵە</div>';
  $('rvCount').textContent = fmtQty(t.qty); $('rvKinds').textContent = t.kinds;
  $('rvTotal').textContent = usd(t.sum); $('rvAlt').textContent = t.sum > 0 ? '≈ ' + iqd(t.sum) : '';
}

/* ───── ناردن ───── */
async function sendOrder() {
  const err = $('sendError'); err.classList.add('hidden');
  if (!cart.length) { err.textContent = 'تکایە لانیکەم یەک کاڵا هەڵبژێرە.'; err.classList.remove('hidden'); return; }
  const btn = $('sendBtn'); btn.disabled = true; const old = btn.innerHTML; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوان بە...';
  const res = await post('{{ route('order.store') }}', {
    mode: state.mode, name: state.name, phone: state.phone, address: state.address,
    note: $('note').value.trim(), website: $('website').value,
    items: cart.map(i => ({ product_id: i.id, unit_id: i.unit_id, quantity: i.qty }))
  });
  btn.disabled = false; btn.innerHTML = old;
  if (res.ok && res.d.success) { $('doneNo').textContent = res.d.order_no; show('screen-done'); $('whoChip').classList.add('hidden'); $('whoChip').classList.remove('flex'); }
  else { err.textContent = errText(res); err.classList.remove('hidden'); err.scrollIntoView({ block: 'center' }); }
}
</script>
</body>
</html>