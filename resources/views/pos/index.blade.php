<!DOCTYPE html>
<html lang="ckb" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سیستەمی فرۆشتن - POS Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca'
                        }
                    }
                }
            }
        }
    </script>
    <!-- فۆنتی مۆدێرنی کوردی/عەرەبی بەرزترین ئاست -->
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; }
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* سکرۆڵباری مۆدێرن */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #1e293b; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* جووڵە و کارلێکی تایبەت */
        .btn-press {
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-press:active {
            transform: scale(0.94);
        }
        .card-hover {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
        }
        .card-hover:active {
            transform: scale(0.96);
        }
    </style>
</head>
<body class="bg-slate-100/90 dark:bg-[#070b14] text-slate-800 dark:text-slate-100 min-h-screen p-2.5 overflow-hidden select-none transition-colors duration-300">

    <!-- سەرپەڕە -->
    <header class="flex items-center justify-between bg-white/90 dark:bg-[#0f172a]/90 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 mb-2.5 gap-2 shadow-sm">
        <div class="flex items-center gap-2.5 shrink-0">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 text-white flex items-center justify-center text-sm shadow-md shadow-indigo-500/20">
                <i class="fa-solid fa-bolt text-xs"></i>
            </div>
            <h1 class="text-sm font-extrabold tracking-wide text-slate-900 dark:text-white flex items-center gap-1.5">
                POS <span class="text-brand-600 dark:text-brand-400 text-xs font-bold">پڕۆ</span>
            </h1>
        </div>

        <!-- دوگمەکانی ڕێدۆزی بە جووڵەی مۆدێرن -->
        <div class="flex items-center gap-1.5 text-[11px] font-bold overflow-x-auto no-scrollbar py-0.5">
            <a href="{{ route('purchases.create') }}" class="btn-press bg-blue-50 dark:bg-blue-600/10 hover:bg-blue-600 text-blue-600 dark:text-blue-400 hover:text-white border border-blue-200 dark:border-blue-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">کڕینی نوێ</a>
            <a href="{{ route('suppliers.index') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap">شوێنی کڕین</a>
            <a href="{{ route('products.index') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap">کۆگا</a>
            <a href="{{ route('categories.index') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap">کاتیگۆری</a>
            <a href="{{ route('units.index') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap">یەکەکان</a>
            <a href="{{ route('partners.index') }}" class="btn-press bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-600 text-emerald-600 dark:text-emerald-400 hover:text-white border border-emerald-200 dark:border-emerald-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">هاوبەشەکان</a>
            <a href="{{ route('customers.index') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap">کڕیاران</a>
            <a href="{{ route('returns.index') }}" class="btn-press bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-600 text-amber-600 dark:text-amber-400 hover:text-white border border-amber-200 dark:border-amber-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">گەڕاوەکان</a>
            <a href="{{ route('mandub.dashboard') }}" class="btn-press bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-600 text-indigo-600 dark:text-indigo-400 hover:text-white border border-indigo-200 dark:border-indigo-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">چالاکییەکانم</a>
            <a href="{{ route('users.index') }}" class="btn-press bg-purple-50 dark:bg-purple-500/10 hover:bg-purple-600 text-purple-600 dark:text-purple-400 hover:text-white border border-purple-200 dark:border-purple-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">کارمەندان</a>
            <a href="{{ route('reports.index') }}" class="btn-press bg-teal-50 dark:bg-teal-500/10 hover:bg-teal-600 text-teal-600 dark:text-teal-400 hover:text-white border border-teal-200 dark:border-teal-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap">ڕاپۆرتەکان</a>
            <a href="{{ route('settings.receipt') }}" class="btn-press bg-slate-100 dark:bg-slate-800/70 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/60 whitespace-nowrap flex items-center gap-1">
                <i class="fa-solid fa-receipt text-[10px]"></i> پسوولە
            </a>
            <a href="{{ route('expenses.index') }}" class="btn-press bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 text-rose-600 dark:text-rose-400 hover:text-white border border-rose-200 dark:border-rose-500/30 px-3 py-1.5 rounded-xl whitespace-nowrap flex items-center gap-1">
                <i class="fa-solid fa-wallet text-[10px]"></i> خەرجی
            </a>

            <!-- دوگمەی گۆڕینی دۆخ -->
            <button type="button" onclick="toggleTheme()" id="themeToggleBtn" class="btn-press bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-amber-500 p-2 rounded-xl border border-slate-200 dark:border-slate-700/60 flex items-center justify-center shadow-sm" title="گۆڕینی دۆخ">
                <i id="themeIcon" class="fa-solid fa-moon text-xs"></i>
            </button>

            <!-- دەرچوون -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-press bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-xl whitespace-nowrap flex items-center gap-1 shadow-sm">
                    <i class="fa-solid fa-power-off text-[10px]"></i> دەرچوون
                </button>
            </form>
        </div>

        <!-- کارمەند -->
        <div class="flex items-center gap-1.5 shrink-0 bg-slate-100 dark:bg-slate-800/80 px-3 py-1.5 rounded-xl text-xs border border-slate-200 dark:border-slate-700/60">
            <i class="fa-solid fa-circle-user text-brand-600 dark:text-brand-400"></i>
            <span class="font-extrabold text-slate-800 dark:text-white text-[11px]">{{ auth()->user()->name ?? 'کاشیر' }}</span>
        </div>
    </header>

    <!-- پەنجەرەی سەرەکی -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2.5 h-[calc(100vh-68px)]">

        <!-- بەشی کاڵاکان (لای ڕاست) -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0f172a] p-3.5 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 flex flex-col h-full overflow-hidden shadow-sm">
            
            <div class="shrink-0 space-y-2.5 pb-2.5 border-b border-slate-100 dark:border-slate-800/80">
                <div class="flex justify-between items-center gap-2">
                    <h2 class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-2 uppercase tracking-wide">
                        <i class="fa-solid fa-boxes-stacked text-brand-600 dark:text-brand-400"></i> کاڵاکانی کۆگا
                    </h2>
                    <div class="w-64 relative">
                        <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان بەپێی ناو یان بارکۆد..." 
                               class="w-full pl-8 pr-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-white text-xs focus:outline-none focus:border-brand-500 transition shadow-inner">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 no-scrollbar">
                    <button type="button" onclick="filterCategory('all')" id="cat-btn-all"
                            class="cat-filter-btn btn-press bg-brand-600 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap shadow-sm shadow-brand-500/30">
                        هەمووی (گشتی)
                    </button>
                    @foreach($categories as $cat)
                    <button type="button" onclick="filterCategory('{{ $cat->id }}')" id="cat-btn-{{ $cat->id }}"
                            class="cat-filter-btn btn-press bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700/60">
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- کارتی کاڵاکان بە جووڵەی سەرنجڕاکێش -->
            <div class="grow overflow-y-auto pt-2.5 pr-1 custom-scrollbar grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 content-start" id="productsGrid">
                @foreach($products as $p)
                @php
                    $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
                    $alertVal = (float) ($p->alert_quantity ?? 5);
                    $isOut = $stockVal <= 0;
                    $isLow = !$isOut && $stockVal <= $alertVal;
                @endphp
                <div class="product-card card-hover group bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-850 border {{ $isOut ? 'border-rose-300 dark:border-rose-900/50 bg-rose-50/40 dark:bg-rose-950/20 opacity-60' : ($isLow ? 'border-amber-300 dark:border-amber-900/50 bg-amber-50/40 dark:bg-amber-950/20' : 'border-slate-200/90 dark:border-slate-800 hover:border-brand-500/70') }} rounded-2xl p-3 cursor-pointer flex flex-col justify-between space-y-2 select-none shadow-sm hover:shadow-md h-fit"
                     data-id="{{ $p->id }}"
                     data-name="{{ $p->name }}"
                     data-code="{{ $p->code }}"
                     data-category="{{ $p->category_id }}"
                     onclick="addToCart({{ json_encode($p) }})">
                    
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-num font-bold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-500/10 px-2 py-0.5 rounded-lg border border-brand-200 dark:border-brand-500/20">{{ $p->code }}</span>
                        @if($isOut)
                            <span class="text-[9px] font-extrabold text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 px-2 py-0.5 rounded-md">نەماوە</span>
                        @elseif($isLow)
                            <span class="text-[9px] font-extrabold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 px-2 py-0.5 rounded-md">کەم ماوە</span>
                        @else
                            <span class="text-[11px] text-slate-400 font-bold">{{ $p->category->name ?? '' }}</span>
                        @endif
                    </div>

                    <div class="text-center py-1">
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs line-clamp-1 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $p->name }}</h3>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">
                            کۆگا: 
                            <b class="font-num {{ $isOut ? 'text-rose-600 dark:text-rose-500' : ($isLow ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                                {{ $stockVal }} کگ
                            </b>
                        </span>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-950/80 rounded-xl py-1.5 px-2 text-center border border-slate-200/80 dark:border-slate-800 shadow-inner">
                        <span class="text-xs font-black font-num text-emerald-600 dark:text-emerald-400" dir="ltr">{{ number_format($p->base_sale_price) }} IQD</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- بەشی سەبەتە (لای چەپ) -->
        <div class="bg-white dark:bg-[#0f172a] p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col h-full overflow-hidden shadow-sm">
            
            <div class="shrink-0 space-y-2 pb-2.5 border-b border-slate-100 dark:border-slate-800/80">
                <div class="flex justify-between items-center">
                    <h2 class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <i class="fa-solid fa-cart-shopping text-emerald-600 dark:text-emerald-400"></i> سەبەتەی فرۆشتن
                    </h2>
                    <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" 
                            class="btn-press bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-600 text-rose-600 dark:text-rose-400 hover:text-white border border-rose-200 dark:border-rose-500/20 px-2.5 py-1 rounded-xl text-[10px] font-bold flex items-center gap-1">
                        <i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span>
                    </button>
                </div>

                <div class="space-y-1.5 text-xs">
                    <input type="datetime-local" id="saleCreatedAt" value="{{ date('Y-m-d\TH:i') }}" class="w-full p-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-white font-num text-[11px] focus:outline-none">

                    <select id="customerId" class="w-full p-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 text-slate-800 dark:text-white text-xs focus:outline-none font-bold">
                        <option value="">کڕیاری گشتی (نەقد)</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ number_format($c->balance ?? 0) }} IQD)</option>
                        @endforeach
                    </select>

                    <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-900/60 p-1 rounded-xl border border-slate-200 dark:border-slate-800">
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-bold text-xs transition border border-transparent has-[:checked]:bg-white dark:has-[:checked]:bg-slate-800 has-[:checked]:text-emerald-600 dark:has-[:checked]:text-emerald-400 has-[:checked]:shadow-sm">
                            <input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()" class="hidden">
                            <span>نەقد</span>
                        </label>
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-bold text-xs transition border border-transparent has-[:checked]:bg-white dark:has-[:checked]:bg-slate-800 has-[:checked]:text-amber-600 dark:has-[:checked]:text-amber-400 has-[:checked]:shadow-sm">
                            <input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()" class="hidden">
                            <span>قەرز</span>
                        </label>
                    </div>

                    <div id="paidAmountBox" class="hidden">
                        <input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0" step="any" class="w-full p-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-num text-xs focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- کاڵاکانی ناو سەبەتە بە سکرۆڵ -->
            <div id="cartItemsContainer" class="grow overflow-y-auto py-2 pr-1 space-y-2 custom-scrollbar">
                <!-- بە جاڤاسکریپت پڕ دەبێتەوە -->
            </div>

            <!-- ژێرەوەی وەسڵ و تەواوکردنی فرۆشتن -->
            <div class="shrink-0 space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400 font-bold">کۆی کاڵاکان:</span>
                    <span id="subTotalText" class="font-num font-bold text-slate-800 dark:text-slate-200" dir="ltr">0 IQD</span>
                </div>
                <div class="flex justify-between items-center gap-1.5">
                    <span class="text-slate-500 dark:text-slate-400 font-bold text-[11px]">داشکاندنی پسوولە:</span>
                    <div class="flex items-center gap-1">
                        <input type="number" step="any" min="0" id="cartDiscount" value="0" oninput="renderCart(false)" 
                               class="w-24 p-1 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-amber-600 dark:text-amber-400 font-num text-left text-xs focus:outline-none focus:border-amber-400">
                        <span class="text-[9px] text-slate-400 font-bold">IQD</span>
                    </div>
                </div>
                <div class="flex justify-between items-center pt-1.5 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-900 dark:text-white font-black text-xs">کۆی گشتی ماوە:</span>
                    <span id="grandTotalText" class="text-emerald-600 dark:text-emerald-400 font-num font-black text-lg" dir="ltr">0 IQD</span>
                </div>

                <button type="button" onclick="submitSale()" id="btnSubmitSale" 
                        class="btn-press w-full bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:brightness-110 text-white font-black py-3 rounded-xl text-xs shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-circle text-sm"></i> تەواوکردنی فرۆشتن و پسوولە
                </button>
            </div>

        </div>

    </div>

    <!-- مۆداڵی سەرکەوتنی فرۆشتن -->
    <div id="successModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl w-full max-w-sm p-6 text-center space-y-4 shadow-2xl">
            <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner border border-emerald-200 dark:border-emerald-500/30">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">فرۆشتن بە سەرکەوتوویی ئەنجامدرا</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">وەسڵەکە لە داتابەیس تۆمارکرا و کۆگا نوێکرایەوە.</p>
            </div>
            <div class="flex justify-center gap-2 pt-1">
                <button type="button" onclick="closeSuccessModal()" class="btn-press px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold">داخستن</button>
                <a href="#" id="printInvoiceBtn" target="_blank" class="btn-press px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-print"></i> چاپی پسوولە
                </a>
            </div>
        </div>
    </div>

    <!-- بۆکسی پەیامی مۆدێرنی Toast -->
    <div id="toastContainer" class="fixed top-4 left-4 z-50 space-y-2 pointer-events-none"></div>

    <script>
        const units = @json($units);
        let cart = [];
        let clearCartTimer = null;
        let isConfirmingClear = false;

        // پاراستن و گۆڕینی دۆخی شاشە
        function initTheme() {
            const savedTheme = localStorage.getItem('pos_theme') || 'dark';
            applyTheme(savedTheme);
        }

        function applyTheme(theme) {
            const icon = document.getElementById('themeIcon');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (icon) icon.className = 'fa-solid fa-moon text-xs text-amber-400';
            } else {
                document.documentElement.classList.remove('dark');
                if (icon) icon.className = 'fa-solid fa-sun text-xs text-amber-500';
            }
            localStorage.setItem('pos_theme', theme);
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            applyTheme(isDark ? 'light' : 'dark');
        }

        initTheme();

        // پەیامی Toast بە فۆنت و دیزاینی شیک
        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const icon = type === 'error' ? 'fa-circle-xmark text-rose-500' : 'fa-triangle-exclamation text-amber-500';
            
            toast.className = 'pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-white text-xs font-bold shadow-2xl border border-slate-200 dark:border-slate-700/80 transition-all duration-300 transform -translate-x-full opacity-0';
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('-translate-x-full', 'opacity-0');
            }, 50);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ١. سڕینەوەی پسوولە
        function handleClearCartTwoClicks() {
            if (cart.length === 0) return;

            const btn = document.getElementById('btnClearCart');
            const label = document.getElementById('clearCartLabel');

            if (!isConfirmingClear) {
                isConfirmingClear = true;
                btn.classList.remove('bg-rose-50', 'dark:bg-rose-500/10', 'text-rose-600', 'dark:text-rose-400');
                btn.classList.add('bg-rose-600', 'text-white', 'animate-pulse');
                label.innerText = 'دڵنیایت؟ کلیک بکەرەوە!';

                clearCartTimer = setTimeout(() => {
                    resetClearCartButton();
                }, 4000);
            } else {
                clearTimeout(clearCartTimer);
                cart = [];
                document.getElementById('cartDiscount').value = 0;
                renderCart(false);
                resetClearCartButton();
            }
        }

        function resetClearCartButton() {
            isConfirmingClear = false;
            const btn = document.getElementById('btnClearCart');
            const label = document.getElementById('clearCartLabel');
            btn.classList.remove('bg-rose-600', 'text-white', 'animate-pulse');
            btn.classList.add('bg-rose-50', 'dark:bg-rose-500/10', 'text-rose-600', 'dark:text-rose-400');
            label.innerText = 'سڕینەوە';
        }

        // ٢. فلتەری کاتیگۆری
        function filterCategory(catId) {
            document.querySelectorAll('.cat-filter-btn').forEach(btn => {
                btn.classList.remove('bg-brand-600', 'text-white', 'shadow-brand-500/30');
                btn.classList.add('bg-slate-100', 'dark:bg-slate-800/80', 'text-slate-600', 'dark:text-slate-300');
            });

            const activeBtn = document.getElementById('cat-btn-' + catId);
            if (activeBtn) {
                activeBtn.classList.add('bg-brand-600', 'text-white', 'shadow-brand-500/30');
                activeBtn.classList.remove('bg-slate-100', 'dark:bg-slate-800/80', 'text-slate-600', 'dark:text-slate-300');
            }

            document.querySelectorAll('.product-card').forEach(card => {
                const cardCat = card.getAttribute('data-category');
                card.style.display = (catId === 'all' || cardCat == catId) ? 'flex' : 'none';
            });
        }

        // ٣. گەڕان لە کاڵاکان
        function searchProducts() {
            const query = document.getElementById('searchBox').value.toLowerCase().trim();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                const code = card.getAttribute('data-code').toLowerCase();
                card.style.display = (name.includes(query) || code.includes(query)) ? 'flex' : 'none';
            });
        }

        // ٤. پشکنینی بڕ و فاکتەری یەکە
        function getUnitFactor(product, unit) {
            const unitName = (unit?.name || '').toLowerCase().trim();
            if (unitName.includes('کارتۆن') || unitName.includes('carton')) {
                return parseFloat(product.kg_per_carton) || 1.0;
            }
            if (unitName.includes('تەن') || unitName.includes('ton')) {
                return 1000.0;
            }
            return parseFloat(unit?.factor_to_base) || 1.0;
        }

        // زیادکردن بۆ سەبەتە
        function addToCart(p) {
            const stockAvailable = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));

            if (stockAvailable <= 0) {
                showToast(`کاڵای (${p.name}) لە کۆگا نەماوە و ناتوانرێت بفرۆشرێت!`, 'error');
                return;
            }

            const initialUnit = units[0] || { id: 1, name: 'دانە', factor_to_base: 1 };
            const factor = getUnitFactor(p, initialUnit);
            const maxAllowedQty = factor > 0 ? (stockAvailable / factor) : stockAvailable;

            let existingIdx = cart.findIndex(i => i.id === p.id);
            let targetIdx;

            if (existingIdx !== -1) {
                const currentUnit = units.find(u => u.id == cart[existingIdx].unit_id) || initialUnit;
                const curFactor = getUnitFactor(p, currentUnit);
                const curMax = curFactor > 0 ? (stockAvailable / curFactor) : stockAvailable;

                if (cart[existingIdx].qty + 1 > curMax) {
                    showToast(`تەنها (${curMax.toFixed(2).replace(/\.00$/, '')} ${currentUnit.name}) لە کۆگا ماوە!`);
                    cart[existingIdx].qty = curMax;
                    renderCart(true, existingIdx);
                    return;
                }
                cart[existingIdx].qty += 1;
                targetIdx = existingIdx;
            } else {
                cart.push({
                    id: p.id,
                    name: p.name,
                    code: p.code,
                    price: parseFloat(p.base_sale_price) || 0,
                    stock_kg: stockAvailable,
                    kg_per_carton: parseFloat(p.kg_per_carton) || 1,
                    qty: 1,
                    unit_id: initialUnit.id,
                    factor: factor
                });
                targetIdx = cart.length - 1;
            }

            renderCart(true, targetIdx);
        }

        function updateItemPrice(index, val) {
            cart[index].price = parseFloat(val) || 0;
            renderCart(false);
        }

        function updateItemUnit(index, unitId) {
            const item = cart[index];
            const u = units.find(unit => unit.id == unitId);
            item.unit_id = unitId;
            item.factor = getUnitFactor(item, u);

            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;
            if (item.qty > maxQty) {
                showToast(`بڕی دیاریکراو گۆڕدرا بۆ ئەوپەڕی ماوە لە کۆگا: (${maxQty.toFixed(2).replace(/\.00$/, '')} ${u.name})`);
                item.qty = maxQty;
            }

            renderCart(false);
        }

        function updateQty(index, delta) {
            const item = cart[index];
            const u = units.find(unit => unit.id == item.unit_id);
            const unitName = u?.name || 'دانە';
            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;

            const newQty = item.qty + delta;

            if (newQty > maxQty) {
                showToast(`تەنها (${maxQty.toFixed(2).replace(/\.00$/, '')} ${unitName}) لە کۆگا ماوە!`);
                item.qty = maxQty;
            } else if (newQty <= 0) {
                cart.splice(index, 1);
            } else {
                item.qty = newQty;
            }

            renderCart(false);
        }

        function setQtyDirect(index, val) {
            const item = cart[index];
            const u = units.find(unit => unit.id == item.unit_id);
            const unitName = u?.name || 'دانە';
            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;

            let num = parseFloat(val);
            if (isNaN(num) || num <= 0) num = 1;

            if (num > maxQty) {
                showToast(`بڕی داواکراو لە مەخزەن زیاترە! تەنها (${maxQty.toFixed(2).replace(/\.00$/, '')} ${unitName}) لە کۆگا ماوە.`);
                item.qty = maxQty;
            } else {
                item.qty = num;
            }

            renderCart(false);
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart(false);
        }

        // ٥. نەخشاندنی سەبەتە بە جووڵە و فۆنتی نایاب
        function renderCart(shouldFocus = false, focusIdx = -1) {
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';
            let subtotal = 0;

            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center py-8 text-slate-400 dark:text-slate-500 text-xs font-bold">سەبەتە بەتاڵە</div>';
                document.getElementById('subTotalText').innerText = '0 IQD';
                document.getElementById('grandTotalText').innerText = '0 IQD';
                resetClearCartButton();
                return;
            }

            cart.forEach((item, idx) => {
                const lineTotal = item.qty * (item.price * item.factor);
                subtotal += lineTotal;

                const curUnit = units.find(u => u.id == item.unit_id);
                const maxStockOfUnit = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;

                let unitOpts = units.map(u => `<option value="${u.id}" ${item.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('');

                const div = document.createElement('div');
                div.id = `cart-row-${idx}`;
                div.className = 'bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-2.5 space-y-1.5 text-xs shadow-sm transition';
                div.innerHTML = `
                    <div class="flex justify-between items-center pb-1 border-b border-slate-200/80 dark:border-slate-800/80">
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-slate-900 dark:text-white text-xs">${item.name}</span>
                            <span class="text-[10px] text-slate-400 font-num">ماوە: <b class="text-emerald-600 dark:text-emerald-400">${maxStockOfUnit.toFixed(2).replace(/\.00$/, '')} ${curUnit?.name ?? ''}</b></span>
                        </div>
                        <button type="button" onclick="removeItem(${idx})" class="btn-press text-rose-400 hover:text-rose-600 p-0.5">
                            <i class="fa-solid fa-trash text-[11px]"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5 items-center">
                        <div>
                            <label class="block text-[9px] text-slate-400 mb-0.5 font-bold">یەکە:</label>
                            <select onchange="updateItemUnit(${idx}, this.value)" class="w-full p-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-800 dark:text-white text-[11px] focus:outline-none font-bold">
                                ${unitOpts}
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9px] text-slate-400 mb-0.5 font-bold">نرخی فرۆشتن:</label>
                            <input type="number" step="any" min="0" value="${item.price}" 
                                   id="price-input-${idx}"
                                   onchange="updateItemPrice(${idx}, this.value)" 
                                   class="w-full p-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-center text-emerald-600 dark:text-emerald-400 font-num text-[11px] font-bold focus:border-brand-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[9px] text-slate-400 mb-0.5 text-center font-bold">دانە / بڕ:</label>
                            <div class="flex items-center bg-white dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 p-0.5">
                                <button type="button" onclick="updateQty(${idx}, -1)" class="btn-press w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 font-bold">-</button>
                                <input type="number" step="any" min="0.01" max="${maxStockOfUnit}" value="${item.qty}" 
                                       id="qty-input-${idx}"
                                       onchange="setQtyDirect(${idx}, this.value)" 
                                       class="w-full text-center bg-transparent font-num text-amber-600 dark:text-amber-400 text-[11px] p-0 focus:outline-none font-bold">
                                <button type="button" onclick="updateQty(${idx}, 1)" class="btn-press w-6 h-6 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 font-bold">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-1 border-t border-slate-200/80 dark:border-slate-800/80">
                        <span class="text-[10px] text-slate-400 font-bold">کۆی ئەم کاڵایە:</span>
                        <span class="font-num font-extrabold text-emerald-600 dark:text-emerald-400 text-[11px]" dir="ltr">${Math.round(lineTotal).toLocaleString()} IQD</span>
                    </div>
                `;
                container.appendChild(div);
            });

            const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
            const grandTotal = Math.max(0, subtotal - discount);

            document.getElementById('subTotalText').innerText = Math.round(subtotal).toLocaleString() + ' IQD';
            document.getElementById('grandTotalText').innerText = Math.round(grandTotal).toLocaleString() + ' IQD';

            if (shouldFocus && focusIdx !== -1) {
                const targetRow = document.getElementById(`cart-row-${focusIdx}`);
                const qtyInput = document.getElementById(`qty-input-${focusIdx}`);
                if (targetRow) targetRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (qtyInput) setTimeout(() => { qtyInput.focus(); qtyInput.select(); }, 80);
            }
        }

        function togglePaymentType() {
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const box = document.getElementById('paidAmountBox');
            box.classList.toggle('hidden', !isDebt);
        }

        // ٦. تەواوکردنی فرۆشتن
        function submitSale() {
            if (cart.length === 0) {
                showToast('تکایە سەرەتا کاڵا بخەرە ناو سەبەتەی فرۆشتن', 'warning');
                return;
            }

            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const customerId = document.getElementById('customerId').value;
            if (isDebt && !customerId) {
                showToast('بۆ فرۆشتنی قەرز، پێویستە کڕیارێک دیاری بکەیت', 'warning');
                return;
            }

            const btn = document.getElementById('btnSubmitSale');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوانبە...';

            const payload = {
                customer_id: customerId,
                payment_type: isDebt ? 'debt' : 'cash',
                paid_amount: isDebt ? parseFloat(document.getElementById('paidAmount').value) || 0 : null,
                discount: parseFloat(document.getElementById('cartDiscount').value) || 0,
                created_at: document.getElementById('saleCreatedAt').value,
                items: cart.map(i => ({
                    product_id: i.id,
                    unit_id: i.unit_id,
                    quantity: i.qty,
                    base_price: i.price
                }))
            };

            fetch('/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check-circle text-sm"></i> تەواوکردنی فرۆشتن و پسوولە';

                if (data.success) {
                    cart = [];
                    document.getElementById('cartDiscount').value = 0;
                    renderCart(false);

                    document.getElementById('printInvoiceBtn').href = '/sales/print/' + data.sale_id;
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    showToast('هەڵە: ' + (data.error || 'فرۆشتن تەواو نەبوو'), 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check-circle text-sm"></i> تەواوکردنی فرۆشتن و پسوولە';
                showToast('کێشەیەک ڕوویدا لە کاتی پەیوەندی بە سێرڤەر: ' + err.message, 'error');
            });
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            location.reload();
        }
    </script>
</body>
</html>