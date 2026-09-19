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
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' },
                        surface: { dark: '#0b1120', card: 'rgba(15, 23, 42, 0.7)', border: 'rgba(51, 65, 85, 0.5)' }
                    },
                    fontFamily: {
                        sans: ['Almarai', 'sans-serif'],
                        num: ['Plus Jakarta Sans', 'sans-serif']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.36)',
                        'glow': '0 0 20px rgba(59, 130, 246, 0.5)',
                        'glow-emerald': '0 0 20px rgba(16, 185, 129, 0.4)'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; background-color: #f1f5f9; }
        .dark body { background-color: #0b1120; background-image: radial-gradient(circle at top right, rgba(30,58,138,0.15), transparent 40%), radial-gradient(circle at bottom left, rgba(16,185,129,0.05), transparent 40%); }
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .btn-press { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-press:active { transform: scale(0.95); }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(51, 65, 85, 0.5);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="text-slate-800 dark:text-slate-100 min-h-screen p-3 overflow-hidden select-none transition-colors duration-500">

    <header class="glass-panel px-4 py-3 rounded-2xl mb-4 flex items-center justify-between shadow-sm z-[100] relative">
        <div class="flex items-center gap-4 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-blue-400 text-white flex items-center justify-center text-lg shadow-glow">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold tracking-wide text-slate-900 dark:text-white flex items-center gap-1.5 leading-tight">
                    POS <span class="text-brand-600 dark:text-brand-400 font-black">PRO</span>
                </h1>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold">سیستەمی پێشکەوتووی فرۆشتن</p>
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-2 text-xs font-bold relative z-50">
            <a href="{{ route('purchases.create') }}" class="btn-press px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-box-open ml-1"></i> کڕینی نوێ</a>
            <a href="{{ route('products.index') }}" class="btn-press px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-boxes-stacked ml-1"></i> کۆگا</a>
            <a href="{{ route('customers.index') }}" class="btn-press px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-users ml-1"></i> کڕیاران</a>
            <a href="{{ route('reports.index') }}" class="btn-press px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-chart-pie ml-1"></i> ڕاپۆرت</a>
            
            <div class="relative inline-block">
                <button type="button" onclick="event.stopPropagation(); document.getElementById('moreDropdown').classList.toggle('hidden')" class="btn-press px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1">
                    زیاتر <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </button>
                <div id="moreDropdown" class="hidden absolute left-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 z-[9999] overflow-hidden">
                    <a href="{{ route('categories.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-tags w-5"></i> کاتیگۆری</a>
                    <a href="{{ route('partners.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-handshake w-5"></i> هاوبەشەکان</a>
                    <a href="{{ route('returns.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-rotate-left w-5"></i> گەڕاوەکان</a>
                    <a href="{{ route('users.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50"><i class="fa-solid fa-user-shield w-5"></i> کارمەندان</a>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 shrink-0 relative z-50">
            <button type="button" onclick="toggleTheme()" id="themeToggleBtn" class="btn-press w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-amber-500 flex items-center justify-center shadow-sm border border-slate-200 dark:border-slate-700 transition">
                <i id="themeIcon" class="fa-solid fa-moon"></i>
            </button>
            
            <div class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800/80 pl-4 pr-1.5 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                <div class="w-7 h-7 rounded-full bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 flex items-center justify-center">
                    <i class="fa-solid fa-user text-[11px]"></i>
                </div>
                <span class="font-extrabold text-[11px]">{{ auth()->user()->name ?? 'کاشیر' }}</span>
                <div class="h-4 w-px bg-slate-300 dark:bg-slate-600 mx-1"></div>
                <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="text-rose-500 hover:text-rose-600 text-sm p-1 transition" title="دەرچوون">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 h-[calc(100vh-88px)] relative z-0">
        <div class="lg:col-span-3 glass-panel rounded-3xl p-4 flex flex-col h-full overflow-hidden relative z-0">
            
            <div class="shrink-0 space-y-4 pb-4 border-b border-slate-200 dark:border-slate-700/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <h2 class="text-sm font-black flex items-center gap-2 text-slate-800 dark:text-white">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center"><i class="fa-solid fa-boxes-stacked"></i></span>
                        کاڵاکانی کۆگا
                    </h2>
                    
                    <div class="w-full md:w-96 relative group">
                        <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان بەپێی ناو یان بارکۆد..." 
                               class="w-full pl-10 pr-4 py-3 rounded-2xl bg-white dark:bg-[#070b14] border-2 border-transparent focus:border-brand-500/50 focus:bg-white dark:focus:bg-slate-900 text-slate-800 dark:text-white text-sm transition-all duration-300 shadow-sm focus:shadow-glow">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        <div class="absolute right-3 top-2.5 px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-400 text-[10px] font-bold border border-slate-200 dark:border-slate-700">F2</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                    <button type="button" onclick="filterCategory('all')" id="cat-btn-all"
                            class="cat-filter-btn btn-press bg-brand-600 text-white px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap shadow-md shadow-brand-500/30">
                        هەمووی (گشتی)
                    </button>
                    @foreach($categories as $cat)
                    <button type="button" onclick="filterCategory('{{ $cat->id }}')" id="cat-btn-{{ $cat->id }}"
                            class="cat-filter-btn btn-press bg-white dark:bg-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700 transition-colors">
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="grow overflow-y-auto pt-4 pr-1 custom-scrollbar grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 content-start" id="productsGrid">
                @foreach($products as $p)
                @php
                    $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
                    $alertVal = (float) ($p->alert_quantity ?? 5);
                    $isOut = $stockVal <= 0;
                    $isLow = !$isOut && $stockVal <= $alertVal;
                @endphp
                
                <div class="product-card group relative bg-white dark:bg-[#0f172a] hover:bg-brand-50 dark:hover:bg-slate-800/80 border {{ $isOut ? 'border-rose-300/50 dark:border-rose-900/30 bg-rose-50/20 dark:bg-rose-950/10 opacity-70' : ($isLow ? 'border-amber-300/50 dark:border-amber-900/30' : 'border-slate-200 dark:border-slate-700/50') }} rounded-2xl p-3.5 cursor-pointer flex flex-col justify-between select-none transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-500/10 hover:border-brand-400/50"
                     data-id="{{ $p->id }}"
                     data-name="{{ $p->name }}"
                     data-code="{{ $p->code }}"
                     data-category="{{ $p->category_id }}"
                     onclick="addToCart({{ json_encode($p) }})">
                    
                    <div class="flex justify-between items-start mb-3 relative z-10">
                        <span class="text-[10px] font-num font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700 group-hover:text-brand-600 dark:group-hover:text-brand-400 group-hover:border-brand-200 transition-colors">{{ $p->code }}</span>
                        
                        @if($isOut)
                            <span class="flex items-center gap-1 text-[10px] font-extrabold text-rose-600 dark:text-rose-400 bg-rose-100 dark:bg-rose-500/10 px-2 py-1 rounded-md"><i class="fa-solid fa-ban text-[8px]"></i> نەماوە</span>
                        @elseif($isLow)
                            <span class="flex items-center gap-1 text-[10px] font-extrabold text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 px-2 py-1 rounded-md"><i class="fa-solid fa-triangle-exclamation text-[8px]"></i> کەم ماوە</span>
                        @endif
                    </div>

                    <div class="text-center my-2 relative z-10">
                        <h3 class="font-extrabold text-slate-800 dark:text-white text-sm line-clamp-2 leading-tight group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">{{ $p->name }}</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 font-medium">
                            کۆگا: <span class="font-num font-bold {{ $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-500') }}">{{ $stockVal }} کگ</span>
                        </p>
                    </div>

                    <div class="mt-3 relative z-10">
                        <div class="w-full bg-slate-50 dark:bg-slate-900 group-hover:bg-gradient-to-r group-hover:from-emerald-50 group-hover:to-teal-50 dark:group-hover:from-emerald-900/20 dark:group-hover:to-teal-900/20 rounded-xl py-2 px-2 text-center border border-slate-200 dark:border-slate-800 group-hover:border-emerald-200 dark:group-hover:border-emerald-800/50 transition-colors">
                            <span class="text-sm font-black font-num text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400" dir="ltr">{{ number_format($p->base_sale_price) }} IQD</span>
                        </div>
                    </div>
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/0 to-brand-500/0 group-hover:from-brand-500/5 group-hover:to-purple-500/5 rounded-2xl transition-all duration-500 pointer-events-none"></div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="glass-panel rounded-3xl p-4 flex flex-col h-full overflow-hidden relative z-10 shadow-lg">
            
            <div class="shrink-0 pb-3 border-b border-slate-200 dark:border-slate-700/50">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="relative">
                            <i class="fa-solid fa-cart-shopping text-emerald-500 text-lg"></i>
                            <span class="absolute -top-1 -right-2 w-3 h-3 bg-rose-500 rounded-full border border-white dark:border-slate-900 animate-ping opacity-75 hidden" id="cartPing"></span>
                        </span>
                        سەبەتە
                    </h2>
                    <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" 
                            class="btn-press bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span>
                    </button>
                </div>

                <div class="bg-white/50 dark:bg-[#070b14]/50 p-3 rounded-2xl border border-slate-200 dark:border-slate-700/50 space-y-2.5">
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <input type="datetime-local" id="saleCreatedAt" value="{{ date('Y-m-d\TH:i') }}" class="w-full pl-8 pr-3 py-2 rounded-xl bg-transparent border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-num text-[11px] focus:outline-none focus:border-brand-500">
                    </div>
                    
                    <div class="relative">
                        <i class="fa-solid fa-user-tag absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <select id="customerId" class="w-full pl-8 pr-3 py-2 rounded-xl bg-transparent border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold focus:outline-none focus:border-brand-500 appearance-none">
                            <option value="">کڕیاری گشتی (نەقد)</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ number_format($c->balance ?? 0) }} IQD)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex p-1 bg-slate-200/50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 relative">
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-bold text-xs transition-all duration-300 z-10 has-[:checked]:text-white">
                            <input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()" class="hidden peer">
                            <span>نەقد</span>
                        </label>
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-bold text-xs transition-all duration-300 z-10 has-[:checked]:text-white text-slate-600 dark:text-slate-400">
                            <input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()" class="hidden peer">
                            <span>قەرز</span>
                        </label>
                        <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] bg-emerald-500 rounded-lg shadow-sm transition-all duration-300 ease-out transform translate-x-0" id="paymentSelector"></div>
                    </div>

                    <div id="paidAmountBox" class="hidden overflow-hidden transition-all duration-300">
                        <input type="number" id="paidAmount" placeholder="بڕی پارەی دراو (ئارەزوومەندانە)" value="0" min="0" step="any" class="w-full p-2.5 rounded-xl bg-white dark:bg-[#070b14] border border-amber-200 dark:border-amber-900/50 text-slate-900 dark:text-white font-num text-xs focus:outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div id="cartItemsContainer" class="grow overflow-y-auto py-3 pr-1 space-y-2.5 custom-scrollbar">
                <!-- بە جاڤاسکریپت پڕ دەبێتەوە -->
            </div>

            <div class="shrink-0 pt-3 mt-1 border-t border-slate-200 dark:border-slate-700/50 bg-white/30 dark:bg-slate-900/30 rounded-2xl px-2 pb-1">
                
                <div class="space-y-2 mb-4 px-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">کۆی کاڵاکان:</span>
                        <span id="subTotalText" class="font-num font-bold text-slate-700 dark:text-slate-300" dir="ltr">0 IQD</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">داشکاندن:</span>
                        <div class="flex items-center gap-1.5 bg-white dark:bg-[#070b14] px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 group-focus-within:border-amber-400 transition-colors">
                            <input type="number" step="any" min="0" id="cartDiscount" value="0" oninput="renderCart(false)" 
                                   class="w-20 bg-transparent text-amber-600 dark:text-amber-400 font-num text-left text-xs font-bold focus:outline-none">
                            <span class="text-[10px] text-slate-400 font-extrabold">IQD</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-end pt-2">
                        <span class="text-slate-800 dark:text-white font-black text-sm">کۆی گشتی:</span>
                        <span id="grandTotalText" class="text-emerald-500 dark:text-emerald-400 font-num font-black text-2xl drop-shadow-sm" dir="ltr">0 IQD</span>
                    </div>
                </div>

                <button type="button" onclick="submitSale()" id="btnSubmitSale" 
                        class="btn-press w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-black py-4 rounded-2xl text-sm shadow-glow-emerald flex items-center justify-center gap-2 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-out"></div>
                    <i class="fa-solid fa-paper-plane text-lg"></i>
                    <span>تەواوکردنی فرۆشتن و چاپی پسوولە</span>
                </button>
            </div>

        </div>

    </div>

    <div id="successModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 z-50 transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2rem] w-full max-w-sm p-8 text-center shadow-2xl transform scale-100 transition-transform">
            <div class="relative w-20 h-20 mx-auto mb-6">
                <div class="absolute inset-0 bg-emerald-100 dark:bg-emerald-900/30 rounded-full animate-ping"></div>
                <div class="relative w-full h-full bg-gradient-to-br from-emerald-400 to-emerald-600 text-white rounded-full flex items-center justify-center text-4xl shadow-lg">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white mb-2">سەرکەوتوو بوو!</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mb-8">وەسڵەکە بە سەرکەوتوویی تۆمارکرا و کۆگا نوێکرایەوە.</p>
            
            <div class="flex flex-col gap-3">
                <a href="#" id="printInvoiceBtn" target="_blank" class="btn-press w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-md">
                    <i class="fa-solid fa-print"></i> بینین و چاپی پسوولە
                </a>
                <button type="button" onclick="closeSuccessModal()" class="btn-press w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors">
                    فرۆشتنی نوێ (داخستن)
                </button>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-[100] space-y-2 pointer-events-none flex flex-col items-center"></div>

    <script>
        const units = @json($units);
        let cart = [];
        let clearCartTimer = null;
        let isConfirmingClear = false;

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('moreDropdown');
            const moreBtn = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(event.target) && !moreBtn.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function initTheme() {
            const savedTheme = localStorage.getItem('pos_theme') || 'dark';
            applyTheme(savedTheme);
        }

        function applyTheme(theme) {
            const icon = document.getElementById('themeIcon');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (icon) icon.className = 'fa-solid fa-moon text-amber-400';
            } else {
                document.documentElement.classList.remove('dark');
                if (icon) icon.className = 'fa-solid fa-sun text-amber-500';
            }
            localStorage.setItem('pos_theme', theme);
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            applyTheme(isDark ? 'light' : 'dark');
        }

        initTheme();

        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            let bgClass = type === 'error' ? 'bg-rose-500' : 'bg-slate-800 dark:bg-white';
            let textClass = type === 'error' ? 'text-white' : 'text-white dark:text-slate-900';
            let icon = type === 'error' ? 'fa-circle-exclamation' : 'fa-bell';
            
            toast.className = `pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-full ${bgClass} ${textClass} text-xs font-bold shadow-2xl transition-all duration-300 transform -translate-y-10 opacity-0`;
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-10');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function handleClearCartTwoClicks() {
            if (cart.length === 0) return;
            const btn = document.getElementById('btnClearCart');
            const label = document.getElementById('clearCartLabel');

            if (!isConfirmingClear) {
                isConfirmingClear = true;
                btn.classList.remove('bg-rose-50', 'dark:bg-rose-500/10', 'text-rose-600');
                btn.classList.add('bg-rose-500', 'text-white', 'shadow-md', 'shadow-rose-500/30');
                label.innerText = 'دڵنیایت؟';
                clearCartTimer = setTimeout(resetClearCartButton, 3000);
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
            btn.classList.remove('bg-rose-500', 'text-white', 'shadow-md', 'shadow-rose-500/30');
            btn.classList.add('bg-rose-50', 'dark:bg-rose-500/10', 'text-rose-600');
            label.innerText = 'سڕینەوە';
        }

        function filterCategory(catId) {
            document.querySelectorAll('.cat-filter-btn').forEach(btn => {
                btn.classList.remove('bg-brand-600', 'text-white', 'shadow-md', 'shadow-brand-500/30');
                btn.classList.add('bg-white', 'dark:bg-slate-800/80', 'text-slate-600', 'dark:text-slate-300');
            });
            const activeBtn = document.getElementById('cat-btn-' + catId);
            if (activeBtn) {
                activeBtn.classList.add('bg-brand-600', 'text-white', 'shadow-md', 'shadow-brand-500/30');
                activeBtn.classList.remove('bg-white', 'dark:bg-slate-800/80', 'text-slate-600', 'dark:text-slate-300');
            }
            document.querySelectorAll('.product-card').forEach(card => {
                const cardCat = card.getAttribute('data-category');
                card.style.display = (catId === 'all' || cardCat == catId) ? 'flex' : 'none';
            });
        }

        function searchProducts() {
            const query = document.getElementById('searchBox').value.toLowerCase().trim();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                const code = card.getAttribute('data-code').toLowerCase();
                card.style.display = (name.includes(query) || code.includes(query)) ? 'flex' : 'none';
            });
        }

        function getUnitFactor(product, unit) {
            const unitName = (unit?.name || '').toLowerCase().trim();
            if (unitName.includes('کارتۆن') || unitName.includes('carton')) return parseFloat(product.kg_per_carton) || 1.0;
            if (unitName.includes('تەن') || unitName.includes('ton')) return 1000.0;
            return parseFloat(unit?.factor_to_base) || 1.0;
        }

        function addToCart(p) {
            const stockAvailable = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));
            if (stockAvailable <= 0) {
                showToast(`کاڵای (${p.name}) لە کۆگا نەماوە!`, 'error');
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
                    showToast(`تەنها (${curMax.toFixed(2).replace(/\.00$/, '')} ${currentUnit.name}) ماوە!`);
                    cart[existingIdx].qty = curMax;
                    renderCart(true, existingIdx);
                    return;
                }
                cart[existingIdx].qty += 1;
                targetIdx = existingIdx;
            } else {
                cart.push({
                    id: p.id, name: p.name, code: p.code,
                    price: parseFloat(p.base_sale_price) || 0,
                    stock_kg: stockAvailable, kg_per_carton: parseFloat(p.kg_per_carton) || 1,
                    qty: 1, unit_id: initialUnit.id, factor: factor
                });
                targetIdx = cart.length - 1;
            }

            const ping = document.getElementById('cartPing');
            if(ping) {
                ping.classList.remove('hidden');
                setTimeout(() => ping.classList.add('hidden'), 500);
            }

            renderCart(true, targetIdx);
        }

        function updateItemPrice(index, val) { cart[index].price = parseFloat(val) || 0; renderCart(false); }
        
        function updateItemUnit(index, unitId) {
            const item = cart[index];
            const u = units.find(unit => unit.id == unitId);
            item.unit_id = unitId;
            item.factor = getUnitFactor(item, u);
            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;
            if (item.qty > maxQty) {
                showToast(`بڕەکە گۆڕدرا بۆ ئەوپەڕی: (${maxQty.toFixed(2).replace(/\.00$/, '')} ${u.name})`);
                item.qty = maxQty;
            }
            renderCart(false);
        }

        function updateQty(index, delta) {
            const item = cart[index];
            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;
            const newQty = item.qty + delta;

            if (newQty > maxQty) {
                item.qty = maxQty;
                showToast(`گەیشتیتە ئەوپەڕی بڕی بەردەست`);
            } else if (newQty <= 0) {
                cart.splice(index, 1);
            } else {
                item.qty = newQty;
            }
            renderCart(false);
        }

        function setQtyDirect(index, val) {
            const item = cart[index];
            const maxQty = item.factor > 0 ? (item.stock_kg / item.factor) : item.stock_kg;
            let num = parseFloat(val);
            if (isNaN(num) || num <= 0) num = 1;

            if (num > maxQty) {
                showToast(`تەنها (${maxQty.toFixed(2).replace(/\.00$/, '')}) ماوە لە کۆگا`);
                item.qty = maxQty;
            } else {
                item.qty = num;
            }
            renderCart(false);
        }

        function removeItem(index) { cart.splice(index, 1); renderCart(false); }

        function renderCart(shouldFocus = false, focusIdx = -1) {
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';
            let subtotal = 0;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="h-40 flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 space-y-3">
                        <i class="fa-solid fa-cart-arrow-down text-4xl"></i>
                        <p class="text-xs font-bold">هیچ کاڵایەک لە سەبەتەدا نییە</p>
                    </div>`;
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
                div.className = 'bg-white dark:bg-[#070b14] border border-slate-100 dark:border-slate-800 rounded-2xl p-3 shadow-sm hover:border-brand-300 dark:hover:border-brand-700 transition-colors group relative overflow-hidden';
                
                div.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div class="pr-1">
                            <h4 class="font-extrabold text-slate-800 dark:text-white text-xs leading-tight">${item.name}</h4>
                            <p class="text-[10px] text-slate-400 font-num mt-1 font-bold">ب. بەردەست: <span class="text-emerald-500">${maxStockOfUnit.toFixed(2).replace(/\.00$/, '')} ${curUnit?.name ?? ''}</span></p>
                        </div>
                        <button type="button" onclick="removeItem(${idx})" class="text-rose-300 hover:text-rose-500 bg-rose-50 dark:bg-rose-500/10 w-6 h-6 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-12 gap-2 items-center bg-slate-50 dark:bg-slate-900/50 p-1.5 rounded-xl border border-slate-100 dark:border-slate-800/80">
                        <div class="col-span-4">
                            <select onchange="updateItemUnit(${idx}, this.value)" class="w-full bg-transparent text-slate-700 dark:text-slate-300 text-[10px] font-bold focus:outline-none appearance-none cursor-pointer">
                                ${unitOpts}
                            </select>
                        </div>
                        
                        <div class="col-span-4 border-r border-slate-200 dark:border-slate-700">
                            <input type="number" step="any" min="0" value="${item.price}" 
                                   onchange="updateItemPrice(${idx}, this.value)" 
                                   class="w-full bg-transparent text-center text-slate-700 dark:text-slate-300 font-num text-[11px] font-bold focus:outline-none">
                        </div>

                        <div class="col-span-4 flex items-center justify-between px-1 border-r border-slate-200 dark:border-slate-700">
                            <button type="button" onclick="updateQty(${idx}, -1)" class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-brand-500 font-bold">-</button>
                            <input type="number" step="any" min="0.01" value="${item.qty}" 
                                   id="qty-input-${idx}" onchange="setQtyDirect(${idx}, this.value)" 
                                   class="w-8 text-center bg-transparent font-num text-brand-600 dark:text-brand-400 text-[11px] font-black focus:outline-none p-0">
                            <button type="button" onclick="updateQty(${idx}, 1)" class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-brand-500 font-bold">+</button>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-brand-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
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
                if (targetRow) targetRow.scrollIntoView({ behavior: 'smooth', block: 'end' });
                if (qtyInput) setTimeout(() => { qtyInput.focus(); qtyInput.select(); }, 100);
            }
        }

        function togglePaymentType() {
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const selector = document.getElementById('paymentSelector');
            const box = document.getElementById('paidAmountBox');
            
            if (isDebt) {
                selector.style.transform = 'translateX(-100%)';
                selector.className = 'absolute top-1 bottom-1 w-[calc(50%-4px)] bg-amber-500 rounded-lg shadow-sm transition-all duration-300 ease-out';
                box.classList.remove('hidden');
            } else {
                selector.style.transform = 'translateX(0)';
                selector.className = 'absolute top-1 bottom-1 w-[calc(50%-4px)] bg-emerald-500 rounded-lg shadow-sm transition-all duration-300 ease-out';
                box.classList.add('hidden');
            }
        }

        function submitSale() {
            if (cart.length === 0) { showToast('تکایە سەرەتا کاڵا دیاری بکە', 'error'); return; }

            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const customerId = document.getElementById('customerId').value;
            if (isDebt && !customerId) { showToast('بۆ قەرز، دەبێت کڕیار دیاری بکەیت', 'error'); return; }

            const btn = document.getElementById('btnSubmitSale');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-lg"></i> <span>تکایە چاوەڕوانبە...</span>';

            const payload = {
                customer_id: customerId, payment_type: isDebt ? 'debt' : 'cash',
                paid_amount: isDebt ? parseFloat(document.getElementById('paidAmount').value) || 0 : null,
                discount: parseFloat(document.getElementById('cartDiscount').value) || 0,
                created_at: document.getElementById('saleCreatedAt').value,
                items: cart.map(i => ({ product_id: i.id, unit_id: i.unit_id, quantity: i.qty, base_price: i.price }))
            };

            fetch('/sales', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane text-lg"></i> <span>تەواوکردنی فرۆشتن و چاپی پسوولە</span>';

                if (data.success) {
                    cart = []; document.getElementById('cartDiscount').value = 0; renderCart(false);
                    document.getElementById('printInvoiceBtn').href = '/sales/print/' + data.sale_id;
                    document.getElementById('successModal').classList.remove('hidden');
                } else {
                    showToast('هەڵە: ' + (data.error || 'فرۆشتن تەواو نەبوو'), 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane text-lg"></i> <span>تەواوکردنی فرۆشتن و چاپی پسوولە</span>';
                showToast('کێشەیەک ڕوویدا: ' + err.message, 'error');
            });
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            location.reload();
        }
    </script>
</body>
</html>