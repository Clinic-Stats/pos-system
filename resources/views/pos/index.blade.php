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

        .custom-scrollbar::-webkit-scrollbar { width: 3px; height: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .btn-press { transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-press:active { transform: scale(0.95); }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }

        /* ستايلی تایبەت بە Liquid Button بۆ دوگمەکانی سەرەوە */
        .liquid-nav-btn {
            position: relative;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 9999px;
            box-shadow: 
                inset 0 4px 6px rgba(255, 255, 255, 0.9),  /* بریقەی سەرەوە */
                inset 0 -4px 6px rgba(0, 0, 0, 0.05),      /* سێبەری خوارەوەی ناوەوە */
                0 4px 10px rgba(0, 0, 0, 0.05);           /* سێبەری دەرەوە */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #475569; /* text-slate-600 */
        }
        .dark .liquid-nav-btn {
            background: rgba(30, 41, 59, 0.6); /* slate-800/60 */
            box-shadow: 
                inset 0 3px 5px rgba(255, 255, 255, 0.15), 
                inset 0 -4px 6px rgba(0, 0, 0, 0.4), 
                0 4px 10px rgba(0, 0, 0, 0.3);
            color: #cbd5e1; /* text-slate-300 */
        }
        .liquid-nav-btn:hover, .liquid-nav-btn:active {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
            box-shadow: 
                inset 0 4px 6px rgba(255, 255, 255, 0.4), 
                inset 0 -4px 6px rgba(0, 0, 0, 0.2), 
                0 6px 15px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }
        .dark .liquid-nav-btn:hover, .dark .liquid-nav-btn:active {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 
                inset 0 3px 5px rgba(255, 255, 255, 0.2), 
                inset 0 -4px 6px rgba(0, 0, 0, 0.4), 
                0 6px 15px rgba(59, 130, 246, 0.3);
        }
        .liquid-nav-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="text-slate-800 dark:text-slate-100 h-screen p-1 md:p-2 overflow-hidden select-none transition-colors duration-500 flex flex-col">

    <header class="glass-panel px-2 py-1.5 md:py-2 rounded-xl mb-2 flex items-center justify-between shadow-sm z-[100] shrink-0">
        
        <div class="flex items-center justify-between w-full lg:w-auto">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-blue-400 text-white flex items-center justify-center text-xs shadow-glow">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <h1 class="text-xs md:text-sm font-extrabold tracking-wide text-slate-900 dark:text-white flex items-center gap-1 leading-none">
                        POS <span class="text-brand-600 dark:text-brand-400 font-black">PRO</span>
                    </h1>
                </div>
            </div>

            <div class="flex lg:hidden items-center gap-1.5 shrink-0 relative z-50">
                <button type="button" onclick="toggleTheme()" class="btn-press w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-amber-500 flex items-center justify-center border border-slate-200 dark:border-slate-700">
                    <i id="themeIconMobile" class="fa-solid fa-moon text-[9px]"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="w-7 h-7 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center border border-rose-200 dark:border-rose-800">
                        <i class="fa-solid fa-power-off text-[9px]"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- بەکارهێنانی ستايلی Liquid Button بۆ مێنیوی سەرەوە -->
        <div class="flex items-center gap-2 text-[10px] md:text-xs font-bold overflow-x-auto no-scrollbar relative z-50 w-full lg:w-auto px-1 lg:px-0 py-1">
            <a href="{{ route('purchases.create') }}" class="liquid-nav-btn px-4 py-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <i class="fa-solid fa-box-open"></i> کڕین
            </a>
            <a href="{{ route('products.index') }}" class="liquid-nav-btn px-4 py-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <i class="fa-solid fa-boxes-stacked"></i> کۆگا
            </a>
            <a href="{{ route('customers.index') }}" class="liquid-nav-btn px-4 py-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <i class="fa-solid fa-users"></i> کڕیار
            </a>
            <a href="{{ route('reports.index') }}" class="liquid-nav-btn px-4 py-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <i class="fa-solid fa-chart-pie"></i> ڕاپۆرت
            </a>
            
            <div class="relative inline-block">
                <button type="button" onclick="event.stopPropagation(); document.getElementById('moreDropdown').classList.toggle('hidden')" class="liquid-nav-btn px-4 py-1.5 flex items-center gap-1.5 whitespace-nowrap">
                    زیاتر <i class="fa-solid fa-chevron-down text-[8px] mt-0.5"></i>
                </button>
                <div id="moreDropdown" class="hidden absolute left-0 top-full mt-2 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 z-[9999] overflow-hidden text-xs">
                    <a href="{{ route('categories.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-tags w-5 text-center"></i> کاتیگۆری</a>
                    <a href="{{ route('partners.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-handshake w-5 text-center"></i> هاوبەشەکان</a>
                    <a href="{{ route('returns.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700/50"><i class="fa-solid fa-rotate-left w-5 text-center"></i> گەڕاوەکان</a>
                    <a href="{{ route('users.index') }}" class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50"><i class="fa-solid fa-user-shield w-5 text-center"></i> کارمەندان</a>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-2 shrink-0 relative z-50">
            <button type="button" onclick="toggleTheme()" id="themeToggleBtn" class="btn-press w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-amber-500 flex items-center justify-center border border-slate-200 dark:border-slate-700">
                <i id="themeIcon" class="fa-solid fa-moon text-[10px]"></i>
            </button>
            
            <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/80 pl-3 pr-1 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                <div class="w-5 h-5 rounded-full bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 flex items-center justify-center">
                    <i class="fa-solid fa-user text-[9px]"></i>
                </div>
                <span class="font-extrabold text-[10px]">{{ auth()->user()->name ?? 'کاشیر' }}</span>
                <div class="h-3 w-px bg-slate-300 dark:bg-slate-600 mx-0.5"></div>
                <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="text-rose-500 hover:text-rose-600 text-xs p-1" title="دەرچوون">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex flex-col lg:grid lg:grid-cols-12 gap-2 h-full overflow-hidden relative z-0">
        
        <!-- بەشی کاڵاکان -->
        <div class="lg:col-span-9 glass-panel rounded-xl p-2 flex flex-col h-[55vh] lg:h-full overflow-hidden relative z-0">
            
            <div class="shrink-0 space-y-2 pb-2 border-b border-slate-200 dark:border-slate-700/50">
                <div class="flex justify-between items-center gap-2">
                    <div class="w-full md:w-72 relative group">
                        <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان..." 
                               class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-white dark:bg-[#070b14] border border-slate-200 dark:border-slate-700 focus:border-brand-500/50 text-slate-800 dark:text-white text-[11px] transition-all shadow-sm">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2 text-slate-400 text-[10px]"></i>
                    </div>
                    
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar w-full md:w-auto">
                        <button type="button" onclick="filterCategory('all')" id="cat-btn-all"
                                class="cat-filter-btn btn-press bg-brand-600 text-white px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap shadow-sm">
                            هەمووی
                        </button>
                        
                        <?php foreach($categories as $cat): ?>
                        <button type="button" onclick="filterCategory('<?php echo $cat->id; ?>')" id="cat-btn-<?php echo $cat->id; ?>"
                                class="cat-filter-btn btn-press bg-white dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 px-3 py-1 rounded-full text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700">
                            <?php echo $cat->name; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- کاڵاکان -->
            <div class="grow overflow-y-auto pt-2 pr-0.5 custom-scrollbar grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2 content-start" id="productsGrid">
                
                <?php foreach($products as $p): ?>
                <?php
                    $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
                    $alertVal = (float) ($p->alert_quantity ?? 5);
                    $isOut = $stockVal <= 0;
                    $isLow = !$isOut && $stockVal <= $alertVal;
                ?>
                
                <div class="product-card group relative bg-white dark:bg-[#0f172a] border <?php echo $isOut ? 'border-rose-300/50 bg-rose-50/20 opacity-70' : ($isLow ? 'border-amber-300/50' : 'border-slate-200 dark:border-slate-700/50'); ?> rounded-xl p-1.5 flex flex-col justify-between select-none shadow-sm hover:shadow-md transition-all">
                    
                    <div class="cursor-pointer" onclick="addToCart(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>)">
                        <div class="flex justify-between items-start mb-1 relative z-10">
                            <span class="text-[8px] font-num font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700"><?php echo $p->code; ?></span>
                            <?php if($isOut): ?>
                                <span class="text-[8px] font-extrabold text-rose-600 bg-rose-100 px-1 py-0.5 rounded">نەماوە</span>
                            <?php elseif($isLow): ?>
                                <span class="text-[8px] font-extrabold text-amber-600 bg-amber-100 px-1 py-0.5 rounded">کەمە</span>
                            <?php endif; ?>
                        </div>

                        <div class="text-center my-1 relative z-10">
                            <h3 class="font-bold text-slate-800 dark:text-white text-[10px] md:text-[11px] line-clamp-2 leading-tight"><?php echo $p->name; ?></h3>
                            <p class="text-[9px] text-slate-500 dark:text-slate-400 mt-0.5 font-bold">
                                ماوە: <span class="font-num <?php echo $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-500'); ?>"><?php echo $stockVal; ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-1 pt-1 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
                        <button type="button" onclick="quickIncrease(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>)" class="w-8 h-8 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-xs font-black flex items-center justify-center transition-colors shadow-sm">+</button>
                        
                        <!-- ئایدی بۆ ئەنیمەیشنی نرخەکە دانراوە -->
                        <span id="price-anim-<?php echo $p->id; ?>" class="text-[10px] font-black font-num text-emerald-500 transition-all duration-200 inline-block" dir="ltr"><?php echo number_format($p->base_sale_price); ?></span>
                        
                        <button type="button" onclick="quickDecrease(<?php echo $p->id; ?>)" class="w-8 h-8 bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 text-slate-700 dark:text-slate-200 hover:text-rose-600 rounded-lg text-xs font-black flex items-center justify-center transition-colors shadow-sm">-</button>
                    </div>

                </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- بەشی سەبەتە -->
        <div class="lg:col-span-3 glass-panel rounded-xl p-2 flex flex-col h-[40vh] lg:h-full overflow-hidden relative shadow-md">
            
            <div class="shrink-0 pb-2 border-b border-slate-200 dark:border-slate-700/50">
                <div class="flex justify-between items-center mb-1.5">
                    <h2 class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                        <span class="relative">
                            <i class="fa-solid fa-cart-shopping text-emerald-500"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-rose-500 rounded-full animate-ping opacity-75 hidden" id="cartPing"></span>
                        </span>
                        سەبەتە
                    </h2>
                    <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" class="btn-press bg-rose-50 text-rose-600 px-2 py-1 rounded-lg text-[9px] font-bold">
                        <i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span>
                    </button>
                </div>

                <div class="bg-white/50 dark:bg-[#070b14]/50 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700/50 space-y-1.5">
                    <div class="flex gap-1.5">
                        <div class="relative flex-1">
                            <i class="fa-regular fa-calendar absolute left-2 top-1.5 text-slate-400 text-[10px]"></i>
                            <input type="datetime-local" id="saleCreatedAt" value="<?php echo date('Y-m-d\TH:i'); ?>" class="w-full pl-6 pr-2 py-1 rounded-lg bg-white border border-slate-200 dark:bg-slate-800 text-[10px] font-num focus:outline-none">
                        </div>
                        <div class="relative flex-1">
                            <select id="customerId" class="w-full px-2 py-1 rounded-lg bg-white border border-slate-200 dark:bg-slate-800 text-[10px] font-bold focus:outline-none">
                                <option value="">کڕیاری نەقد</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex p-0.5 bg-slate-200/50 dark:bg-slate-800/80 rounded-lg relative">
                        <label class="flex-1 text-center py-1 rounded cursor-pointer font-bold text-[10px] z-10 has-[:checked]:text-white">
                            <input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()" class="hidden peer">
                            <span>نەقد</span>
                        </label>
                        <label class="flex-1 text-center py-1 rounded cursor-pointer font-bold text-[10px] z-10 has-[:checked]:text-white text-slate-600">
                            <input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()" class="hidden peer">
                            <span>قەرز</span>
                        </label>
                        <div class="absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-emerald-500 rounded shadow-sm transition-all duration-300" id="paymentSelector"></div>
                    </div>

                    <div id="paidAmountBox" class="hidden">
                        <input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0" class="w-full p-1.5 rounded-lg bg-white border border-amber-200 text-[10px] font-num focus:outline-none">
                    </div>
                </div>
            </div>

            <div id="cartItemsContainer" class="grow overflow-y-auto py-1.5 pr-0.5 space-y-1.5 custom-scrollbar"></div>

            <div class="shrink-0 pt-1.5 mt-1 border-t border-slate-200 dark:border-slate-700/50">
                <div class="space-y-1 mb-2 px-1 text-[10px]">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-bold">کۆی کاڵا:</span>
                        <span id="subTotalText" class="font-num font-bold" dir="ltr">0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-bold">داشکاندن:</span>
                        <input type="number" min="0" id="cartDiscount" value="0" oninput="renderCart(false)" class="w-14 bg-slate-100 dark:bg-slate-800 px-1 py-0.5 rounded text-amber-600 font-num text-left text-[10px] focus:outline-none">
                    </div>
                    <div class="flex justify-between items-end pt-0.5">
                        <span class="font-black text-[11px]">کۆی گشتی:</span>
                        <span id="grandTotalText" class="text-emerald-500 font-num font-black text-lg" dir="ltr">0</span>
                    </div>
                </div>

                <button type="button" onclick="submitSale()" id="btnSubmitSale" class="btn-press w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black py-2 rounded-xl text-[11px] shadow-glow-emerald flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> پسوولە
                </button>
            </div>
        </div>

    </div>

    <!-- مۆداڵی سەرکەوتنی فرۆشتن و گەڕانەوە بۆ هەمان وەسڵی فرۆشتن -->
    <div id="successModal" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-2 md:p-4 z-[999]">
        <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-5 text-right shadow-2xl flex flex-col max-h-[90vh]">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-lg"><i class="fa-solid fa-receipt"></i></div>
                    <div>
                        <h3 class="text-sm font-black dark:text-white">وەسڵی فرۆشتن</h3>
                        <p class="text-[10px] text-slate-400">دەتوانیت کاڵاکان لە خوارەوە کەم و زیاد بکەیت پێش چاپکردن</p>
                    </div>
                </div>
                <button type="button" onclick="returnToSameSale()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center hover:bg-rose-100 hover:text-rose-500"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="grow overflow-y-auto py-3 custom-scrollbar space-y-2" id="modalItemsList"></div>

            <div class="shrink-0 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex justify-between items-center text-xs font-black">
                    <span>کۆی گشتی پارە:</span>
                    <span id="modalGrandTotal" class="text-emerald-500 text-base font-num" dir="ltr">0 IQD</span>
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <a href="#" id="printInvoiceBtn" target="_blank" class="btn-press py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-md">
                        <i class="fa-solid fa-print"></i> پرینتی کۆتایی
                    </a>
                    <button type="button" onclick="returnToSameSale()" class="btn-press py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-md">
                        گەڕانەوە بۆ هەمان وەسڵی فرۆشتن
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div id="toastContainer" class="fixed top-2 left-1/2 transform -translate-x-1/2 z-[999] space-y-2 pointer-events-none flex flex-col items-center"></div>

    <script>
        const units = <?php echo json_encode($units); ?>;
        let cart = [];
        let clearCartTimer = null;
        let isConfirmingClear = false;
        let lastSaleItems = [];
        let activeSaleId = null;

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('moreDropdown');
            const moreBtn = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(event.target) && !moreBtn.contains(event.target)) dropdown.classList.add('hidden');
        });

        function initTheme() { applyTheme(localStorage.getItem('pos_theme') || 'dark'); }
        function applyTheme(theme) {
            const icons = [document.getElementById('themeIcon'), document.getElementById('themeIconMobile')];
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                icons.forEach(i => { if(i) i.className = 'fa-solid fa-moon text-amber-400 text-[10px]'; });
            } else {
                document.documentElement.classList.remove('dark');
                icons.forEach(i => { if(i) i.className = 'fa-solid fa-sun text-amber-500 text-[10px]'; });
            }
            localStorage.setItem('pos_theme', theme);
        }
        function toggleTheme() { applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark'); }
        initTheme();

        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            let bgClass = type === 'error' ? 'bg-rose-500' : 'bg-slate-800 dark:bg-white';
            let textClass = type === 'error' ? 'text-white' : 'text-white dark:text-slate-900';
            toast.className = `pointer-events-auto flex items-center gap-2 px-3 py-1.5 rounded-full ${bgClass} ${textClass} text-[10px] font-bold shadow-xl transition-all duration-300 transform -translate-y-10 opacity-0`;
            toast.innerHTML = `<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);
            setTimeout(() => { toast.classList.add('opacity-0', '-translate-y-10'); setTimeout(() => toast.remove(), 300); }, 2500);
        }

        function handleClearCartTwoClicks() {
            if (cart.length === 0) return;
            const btn = document.getElementById('btnClearCart');
            if (!isConfirmingClear) {
                isConfirmingClear = true;
                btn.classList.add('bg-rose-500', 'text-white');
                document.getElementById('clearCartLabel').innerText = 'دڵنیایت؟';
                clearCartTimer = setTimeout(resetClearCartButton, 3000);
            } else {
                clearTimeout(clearCartTimer); cart = []; document.getElementById('cartDiscount').value = 0;
                renderCart(false); resetClearCartButton();
            }
        }

        function resetClearCartButton() {
            isConfirmingClear = false;
            const btn = document.getElementById('btnClearCart');
            btn.classList.remove('bg-rose-500', 'text-white');
            document.getElementById('clearCartLabel').innerText = 'سڕینەوە';
        }

        function filterCategory(catId) {
            document.querySelectorAll('.cat-filter-btn').forEach(btn => {
                btn.classList.remove('bg-brand-600', 'text-white'); btn.classList.add('bg-white', 'text-slate-600');
            });
            const activeBtn = document.getElementById('cat-btn-' + catId);
            if (activeBtn) { activeBtn.classList.add('bg-brand-600', 'text-white'); activeBtn.classList.remove('bg-white', 'text-slate-600'); }
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = (catId === 'all' || card.getAttribute('data-category') == catId) ? 'flex' : 'none';
            });
        }

        function searchProducts() {
            const query = document.getElementById('searchBox').value.toLowerCase().trim();
            document.querySelectorAll('.product-card').forEach(card => {
                const match = card.getAttribute('data-name').toLowerCase().includes(query) || card.getAttribute('data-code').toLowerCase().includes(query);
                card.style.display = match ? 'flex' : 'none';
            });
        }

        function getDefaultUnitId() {
            const kgUnit = units.find(u => (u.name || '').toLowerCase().includes('کیلۆ') || (u.name || '').toLowerCase().includes('kg'));
            return kgUnit ? kgUnit.id : (units[0] ? units[0].id : 1);
        }

        function getUnitFactor(product, unit) {
            const n = (unit?.name || '').toLowerCase();
            if (n.includes('کارتۆن') || n.includes('carton')) return parseFloat(product.kg_per_carton) || 1;
            if (n.includes('تەن') || n.includes('ton')) return 1000;
            return parseFloat(unit?.factor_to_base) || 1;
        }

        function addToCart(p) {
            const stock = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));
            if (stock <= 0) { showToast('نەماوە!', 'error'); return; }

            const defaultUnitId = getDefaultUnitId();
            const initialUnit = units.find(u => u.id == defaultUnitId) || units[0] || { id: 1, name: 'کیلۆ', factor_to_base: 1 };
            const factor = getUnitFactor(p, initialUnit);
            
            let idx = cart.findIndex(i => i.id === p.id);
            if (idx !== -1) {
                const u = units.find(u => u.id == cart[idx].unit_id) || initialUnit;
                const cFactor = getUnitFactor(p, u);
                const max = cFactor > 0 ? (stock / cFactor) : stock;
                if (cart[idx].qty + 1 > max) { showToast('تەواو بوو!'); cart[idx].qty = max; } else { cart[idx].qty++; }
            } else {
                cart.push({ id: p.id, name: p.name, code: p.code, price: parseFloat(p.base_sale_price)||0, stock_kg: stock, kg_per_carton: parseFloat(p.kg_per_carton)||1, qty: 1, unit_id: initialUnit.id, factor: factor });
            }
            
            const ping = document.getElementById('cartPing');
            if(ping) { ping.classList.remove('hidden'); setTimeout(() => ping.classList.add('hidden'), 300); }
            renderCart(false);
        }

        // زیادکردن لەگەڵ ئەنیمەیشنی گەورەبوون
        function quickIncrease(p) { 
            addToCart(p);
            let el = document.getElementById('price-anim-' + p.id);
            if(el) {
                el.classList.add('scale-125', 'text-brand-500');
                setTimeout(() => el.classList.remove('scale-125', 'text-brand-500'), 150);
            }
        }

        // کەمکردنەوە لەگەڵ ئەنیمەیشنی بچووکبوون
        function quickDecrease(productId) {
            let idx = cart.findIndex(i => i.id === productId);
            if (idx !== -1) {
                if (cart[idx].qty > 1) { cart[idx].qty--; } else { cart.splice(idx, 1); }
                renderCart(false);
                
                let el = document.getElementById('price-anim-' + productId);
                if(el) {
                    el.classList.add('scale-75', 'text-rose-500');
                    setTimeout(() => el.classList.remove('scale-75', 'text-rose-500'), 150);
                }
            }
        }

        function updateItemPrice(index, val) { cart[index].price = parseFloat(val) || 0; renderCart(false); }
        function updateItemUnit(index, unitId) {
            const i = cart[index]; const u = units.find(x => x.id == unitId);
            i.unit_id = unitId; i.factor = getUnitFactor(i, u);
            const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            if (i.qty > max) i.qty = max;
            renderCart(false);
        }
        function updateQty(index, delta) {
            const i = cart[index]; const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            const n = i.qty + delta;
            if (n > max) i.qty = max; else if (n <= 0) cart.splice(index, 1); else i.qty = n;
            renderCart(false);
        }
        function setQtyDirect(index, val) {
            const i = cart[index]; const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            let num = parseFloat(val); if (isNaN(num) || num <= 0) num = 1;
            i.qty = num > max ? max : num;
            renderCart(false);
        }
        function removeItem(index) { cart.splice(index, 1); renderCart(false); }

        function renderCart(shouldFocus = false) {
            const container = document.getElementById('cartItemsContainer'); container.innerHTML = ''; let subtotal = 0;
            if (cart.length === 0) {
                container.innerHTML = `<div class="h-20 flex flex-col items-center justify-center text-slate-400 text-[10px] font-bold"><i class="fa-solid fa-cart-arrow-down text-xl mb-1"></i>بەتاڵە</div>`;
                document.getElementById('subTotalText').innerText = '0'; document.getElementById('grandTotalText').innerText = '0';
                resetClearCartButton(); return;
            }

            cart.forEach((item, idx) => {
                subtotal += item.qty * (item.price * item.factor);
                const opts = units.map(u => `<option value="${u.id}" ${item.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('');

                const div = document.createElement('div'); div.id = `cart-row-${idx}`;
                div.className = 'bg-white dark:bg-[#070b14] border border-slate-100 dark:border-slate-800 rounded-xl p-1.5 shadow-sm';
                div.innerHTML = `
                    <div class="flex justify-between items-start mb-1">
                        <div class="pr-0.5"><h4 class="font-extrabold text-[10px] leading-tight">${item.name}</h4></div>
                        <button type="button" onclick="removeItem(${idx})" class="text-rose-400 w-4 h-4 flex items-center justify-center"><i class="fa-solid fa-xmark text-[9px]"></i></button>
                    </div>
                    <div class="grid grid-cols-12 gap-1 bg-slate-50 dark:bg-slate-900/50 p-1 rounded-lg border border-slate-100 dark:border-slate-800/80">
                        <div class="col-span-4"><select onchange="updateItemUnit(${idx}, this.value)" class="w-full bg-transparent text-[9px] font-bold focus:outline-none appearance-none cursor-pointer">${opts}</select></div>
                        <div class="col-span-4 border-r border-slate-200 dark:border-slate-700"><input type="number" step="any" min="0" value="${item.price}" onchange="updateItemPrice(${idx}, this.value)" class="w-full bg-transparent text-center text-[10px] font-bold font-num focus:outline-none"></div>
                        <div class="col-span-4 flex items-center justify-between px-0.5 border-r border-slate-200 dark:border-slate-700">
                            <button type="button" onclick="updateQty(${idx}, 1)" class="w-4 h-4 text-brand-500 font-bold text-[10px]">+</button>
                            <input type="number" step="any" min="0.01" value="${item.qty}" onchange="setQtyDirect(${idx}, this.value)" class="w-6 text-center bg-transparent font-num text-brand-600 text-[10px] font-black focus:outline-none p-0">
                            <button type="button" onclick="updateQty(${idx}, -1)" class="w-4 h-4 text-slate-400 font-bold text-[10px]">-</button>
                        </div>
                    </div>`;
                container.appendChild(div);
            });

            const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
            document.getElementById('subTotalText').innerText = Math.round(subtotal).toLocaleString();
            document.getElementById('grandTotalText').innerText = Math.round(Math.max(0, subtotal - discount)).toLocaleString();
        }

        function togglePaymentType() {
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const selector = document.getElementById('paymentSelector'); const box = document.getElementById('paidAmountBox');
            if (isDebt) { selector.style.transform = 'translateX(-100%)'; selector.className = 'absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-amber-500 rounded shadow-sm transition-all duration-300'; box.classList.remove('hidden'); } 
            else { selector.style.transform = 'translateX(0)'; selector.className = 'absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-emerald-500 rounded shadow-sm transition-all duration-300'; box.classList.add('hidden'); }
        }

        function submitSale() {
            if (cart.length === 0) { showToast('کاڵا نییە!', 'error'); return; }
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const customerId = document.getElementById('customerId').value;
            if (isDebt && !customerId) { showToast('کڕیار دیاری بکە بۆ قەرز', 'error'); return; }

            const btn = document.getElementById('btnSubmitSale'); btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            lastSaleItems = JSON.parse(JSON.stringify(cart));

            fetch('/sales', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>', 'Accept': 'application/json' },
                body: JSON.stringify({
                    customer_id: customerId, payment_type: isDebt ? 'debt' : 'cash',
                    paid_amount: isDebt ? parseFloat(document.getElementById('paidAmount').value) || 0 : null,
                    discount: parseFloat(document.getElementById('cartDiscount').value) || 0,
                    created_at: document.getElementById('saleCreatedAt').value,
                    items: cart.map(i => ({ product_id: i.id, unit_id: i.unit_id, quantity: i.qty, base_price: i.price }))
                })
            }).then(res => res.json()).then(data => {
                btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> پسوولە';
                if (data.success) {
                    activeSaleId = data.sale_id;
                    document.getElementById('printInvoiceBtn').href = '/sales/print/'.concat(data.sale_id);
                    renderModalItems();
                    document.getElementById('successModal').classList.remove('hidden');
                    cart = []; document.getElementById('cartDiscount').value = 0; renderCart(false);
                } else { showToast(data.error || 'هەڵە', 'error'); }
            }).catch(err => {
                btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> پسوولە';
                showToast('کێشەیەک ڕوویدا', 'error');
            });
        }

        function renderModalItems() {
            const listContainer = document.getElementById('modalItemsList');
            listContainer.innerHTML = '';
            let total = 0;

            lastSaleItems.forEach((item, index) => {
                const lineTotal = item.qty * (item.price * item.factor);
                total += lineTotal;
                const u = units.find(x => x.id == item.unit_id);

                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-2 bg-slate-50 dark:bg-slate-800/50 p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs';
                row.innerHTML = `
                    <div class="w-1/3 font-bold truncate">${item.name}</div>
                    <div class="w-1/4 flex items-center gap-1">
                        <input type="number" step="any" min="0.01" value="${item.qty}" onchange="updateModalQty(${index}, this.value)" class="w-12 bg-white dark:bg-slate-900 text-center font-num border border-slate-300 dark:border-slate-600 rounded p-1 text-xs">
                        <span class="text-[10px] text-slate-400">${u ? u.name : ''}</span>
                    </div>
                    <div class="w-1/4">
                        <input type="number" step="any" min="0" value="${item.price}" onchange="updateModalPrice(${index}, this.value)" class="w-20 bg-white dark:bg-slate-900 text-center font-num border border-slate-300 dark:border-slate-600 rounded p-1 text-xs text-emerald-500 font-bold">
                    </div>
                    <div class="w-1/6 text-left font-num font-black" dir="ltr">${Math.round(lineTotal).toLocaleString()}</div>
                `;
                listContainer.appendChild(row);
            });

            document.getElementById('modalGrandTotal').innerText = Math.round(total).toLocaleString().concat(' IQD');
        }

        function updateModalQty(index, val) {
            let q = parseFloat(val);
            if (isNaN(q) || q <= 0) q = 1;
            lastSaleItems[index].qty = q;
            renderModalItems();
        }

        function updateModalPrice(index, val) {
            let p = parseFloat(val);
            if (isNaN(p) || p < 0) p = 0;
            lastSaleItems[index].price = p;
            renderModalItems();
        }

        function returnToSameSale() {
            if (lastSaleItems && lastSaleItems.length > 0) {
                cart = JSON.parse(JSON.stringify(lastSaleItems));
                renderCart(false);
                lastSaleItems = [];
            }
            document.getElementById('successModal').classList.add('hidden');
        }
    </script>
</body>
</html>