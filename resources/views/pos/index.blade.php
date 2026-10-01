<!DOCTYPE html>
<html lang="ckb" dir="rtl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Pro - سیستەمی فرۆشتنی مۆدێرن</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eef2ff', 100: '#e0e7ff', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 900: '#312e81' },
                        accent: { 400: '#34d399', 500: '#10b981', 600: '#059669' }
                    },
                    fontFamily: { sans: ['Almarai', 'sans-serif'], num: ['Plus Jakarta Sans', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'scale-in': 'scaleIn 0.2s ease-out',
                        'shimmer': 'shimmer 2s linear infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } },
                        scaleIn: { '0%': { transform: 'scale(0.9)', opacity: 0 }, '100%': { transform: 'scale(1)', opacity: 1 } },
                        shimmer: { '0%': { backgroundPosition: '-1000px 0' }, '100%': { backgroundPosition: '1000px 0' } }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; }
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* ============================================ */
        /* ڕەنگی پشتەوە - مۆدێرن */
        /* ============================================ */
        body { 
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            background-attachment: fixed;
        }
        .dark body { 
            background: #0a0e1a;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.15), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.1), transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(139, 92, 246, 0.08), transparent 60%);
        }
        
        /* ============================================ */
        /* Glassmorphism Cards */
        /* ============================================ */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.08);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(99, 102, 241, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }
        
        /* ============================================ */
        /* Custom Scrollbar */
        /* ============================================ */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #6366f1, #8b5cf6); 
            border-radius: 10px; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(180deg, #4f46e5, #7c3aed); 
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* ============================================ */
        /* Buttons */
        /* ============================================ */
        .btn-press { transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1); }
        .btn-press:active { transform: scale(0.96); }
        
        /* ============================================ */
        /* Nav Buttons - Modern */
        /* ============================================ */
        .nav-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 11px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            color: #475569;
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            white-space: nowrap;
        }
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.3);
            color: #4f46e5;
        }
        .dark .nav-btn {
            background: rgba(30, 41, 59, 0.8);
            color: #cbd5e1;
            border-color: rgba(71, 85, 105, 0.5);
        }
        .dark .nav-btn:hover {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.4);
            color: #a5b4fc;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        
        /* ============================================ */
        /* Product Cards - Modern */
        /* ============================================ */
        .product-card {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .product-card:hover::before {
            left: 100%;
        }
        .product-card:hover {
            transform: translateY(-4px) scale(1.02);
        }
        
        /* ============================================ */
        /* Gradient Buttons */
        /* ============================================ */
        .gradient-btn-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .gradient-btn-emerald:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
            transform: translateY(-2px);
        }
        
        .gradient-btn-brand {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        .gradient-btn-brand:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.6);
            transform: translateY(-2px);
        }
        
        /* ============================================ */
        /* ویجێتی ئاڵوگۆڕ - ڕەنگ دەگۆڕێت */
        /* ============================================ */
        @keyframes colorShift {
            0%   { background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 0 25px rgba(245, 158, 11, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
            25%  { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 0 25px rgba(16, 185, 129, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
            50%  { background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 0 25px rgba(99, 102, 241, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
            75%  { background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 0 25px rgba(139, 92, 246, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
            100% { background: linear-gradient(135deg, #f59e0b, #ea580c); box-shadow: 0 0 25px rgba(245, 158, 11, 0.6), 0 8px 25px rgba(0,0,0,0.3); }
        }
        @keyframes iconSpin {
            0%, 100% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(15deg) scale(1.15); }
        }
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }
        #exchangeRateWidget > div:first-child {
            animation: colorShift 10s ease-in-out infinite;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        #exchangeRateWidget:hover > div:first-child {
            transform: scale(1.1);
            box-shadow: 0 0 45px rgba(255, 255, 255, 0.5), 0 15px 40px rgba(0,0,0,0.5);
        }
        #exchangeRateWidget #rateIcon {
            animation: iconSpin 4s ease-in-out infinite, iconFloat 2s ease-in-out infinite;
        }
        #exchangeRateWidget #newExchangeRate {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        #exchangeRateWidget #newExchangeRate:focus {
            background: #ffffff;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.15), 0 0 0 3px rgba(255, 255, 255, 0.4);
        }
        
        /* ============================================ */
        /* Floating background shapes */
        /* ============================================ */
        .bg-shape {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: floatShape 20s ease-in-out infinite;
        }
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        /* ============================================ */
        /* Cart Item Animation */
        /* ============================================ */
        @keyframes slideInRight {
            0% { transform: translateX(-20px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        .cart-item-enter { animation: slideInRight 0.3s ease-out; }
        
        /* ============================================ */
        /* Glow effects */
        /* ============================================ */
        .glow-emerald {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5), 0 0 40px rgba(16, 185, 129, 0.2);
        }
        .glow-brand {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5), 0 0 40px rgba(99, 102, 241, 0.2);
        }
        
        /* ============================================ */
        /* Stat cards hover */
        /* ============================================ */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        @media (max-width: 768px) {
            #exchangeRateWidget {
                top: 70px !important;
                right: 8px !important;
                min-width: 150px;
            }
        }
    </style>
</head>
<body class="text-slate-800 dark:text-slate-100 h-screen p-1 md:p-2 overflow-hidden select-none transition-colors duration-500 flex flex-col">

    <!-- بەشەکانی پشتەوەی جوڵاو -->
    <div class="bg-shape" style="width:400px;height:400px;background:#6366f1;top:-100px;right:-100px;"></div>
    <div class="bg-shape" style="width:300px;height:300px;background:#10b981;bottom:-100px;left:-100px;animation-delay:-10s;"></div>
    <div class="bg-shape" style="width:250px;height:250px;background:#f59e0b;top:50%;left:50%;animation-delay:-5s;"></div>

    <!-- ============================================ -->
    <!-- هێدەر - مۆدێرن -->
    <!-- ============================================ -->
    <header class="glass-panel px-3 py-2 rounded-2xl mb-2 flex flex-col md:flex-row items-center justify-between shadow-lg z-[100] shrink-0 gap-2 relative">
        
        <!-- لۆگۆ و ناو -->
        <div class="flex items-center justify-between w-full md:w-auto gap-3">
            <div class="flex items-center gap-2.5 shrink-0">
                <div class="relative">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-500 to-pink-500 text-white flex items-center justify-center text-lg shadow-lg shadow-indigo-500/40 rotate-3 hover:rotate-0 transition-transform duration-300">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full animate-ping"></div>
                </div>
                <div>
                    <h1 class="text-sm md:text-base font-black tracking-tight text-slate-900 dark:text-white leading-none">
                        POS <span class="bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">PRO</span>
                    </h1>
                    <p class="text-[9px] text-slate-500 dark:text-slate-400 font-bold mt-0.5">سیستەمی فرۆشتنی مۆدێرن</p>
                </div>
            </div>

            <!-- مۆبایل: دوگمەکان -->
            <div class="flex md:hidden items-center gap-1.5 shrink-0">
                <button type="button" onclick="toggleTheme()" class="btn-press w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-md">
                    <i id="themeIconMobile" class="fa-solid fa-moon text-xs"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="w-8 h-8 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- دوگمەکانی ناڤیگەیشن -->
        <div class="flex flex-wrap items-center justify-center gap-1.5 relative z-[105] w-full md:w-auto">
            <a href="{{ route('purchases.create') }}" class="nav-btn"><i class="fa-solid fa-box-open text-amber-500"></i> کڕین</a>
            <a href="{{ route('products.index') }}" class="nav-btn"><i class="fa-solid fa-boxes-stacked text-blue-500"></i> کۆگا</a>
            <a href="{{ route('customers.index') }}" class="nav-btn"><i class="fa-solid fa-users text-emerald-500"></i> کڕیار</a>
            <a href="{{ route('reports.index') }}" class="nav-btn"><i class="fa-solid fa-chart-pie text-purple-500"></i> ڕاپۆرت</a>
            <div class="relative inline-block">
                <button type="button" onclick="event.stopPropagation(); document.getElementById('moreDropdown').classList.toggle('hidden')" class="nav-btn">
                    <i class="fa-solid fa-ellipsis text-slate-500"></i> زیاتر
                    <i class="fa-solid fa-chevron-down text-[8px]"></i>
                </button>
                <div id="moreDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 z-[9999] max-h-96 overflow-y-auto custom-scrollbar text-xs p-1.5 animate-slide-up">
                    <a href="{{ route('sales.list') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-emerald-600 dark:text-emerald-400 font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"><i class="fa-solid fa-receipt w-5 text-center"></i> فرۆشتنەکان</a>
                    <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-tags w-5 text-center text-indigo-500"></i> کاتیگۆری</a>
                    <a href="{{ route('units.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-scale-balanced w-5 text-center text-orange-500"></i> یەکەکان</a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-truck-field w-5 text-center text-cyan-500"></i> دابینکەران</a>
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-money-bill-trend-up w-5 text-center text-rose-500"></i> خەرجییەکان</a>
                    <a href="{{ route('partners.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-handshake w-5 text-center text-yellow-500"></i> هاوبەشەکان</a>
                    <a href="{{ route('returns.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-rotate-left w-5 text-center text-red-500"></i> گەڕاوەکان</a>
                    <a href="{{ route('mandub.dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-motorcycle w-5 text-center text-teal-500"></i> مەندووب</a>
                    <a href="{{ route('settings.receipt') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-gear w-5 text-center text-slate-500"></i> ڕێکخستنی وەسڵ</a>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors"><i class="fa-solid fa-user-shield w-5 text-center text-indigo-600"></i> کارمەندان</a>
                </div>
            </div>
        </div>

        <!-- بەشی بەکارهێنەر - دێسکتۆپ -->
        <div class="hidden md:flex items-center gap-2 shrink-0">
            <button type="button" onclick="toggleTheme()" class="btn-press w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30 hover:shadow-xl hover:shadow-amber-500/50 transition-all">
                <i id="themeIcon" class="fa-solid fa-moon text-sm"></i>
            </button>
            
            <div class="flex items-center gap-2 bg-white/60 dark:bg-slate-800/60 backdrop-blur pl-3 pr-1.5 py-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 shadow-sm">
                <div class="relative">
                    <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-user text-[10px]"></i>
                    </div>
                    <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-800"></div>
                </div>
                <span class="font-extrabold text-[11px] text-slate-700 dark:text-slate-200">{{ auth()->user()->name ?? 'کاشیر' }}</span>
                <div class="h-4 w-px bg-slate-300 dark:bg-slate-600"></div>
                <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="w-7 h-7 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white transition-all flex items-center justify-center" title="دەرچوون">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- ============================================ -->
    <!-- بەشی سەرەکی -->
    <!-- ============================================ -->
    <div class="flex flex-col lg:grid lg:grid-cols-12 gap-2 h-full overflow-hidden relative z-0">
        
        <!-- ============================================ -->
        <!-- لیستی کاڵاکان -->
        <!-- ============================================ -->
        <div class="lg:col-span-9 glass-panel rounded-2xl p-2.5 flex flex-col h-[55vh] lg:h-full overflow-hidden relative">
            
            <!-- گەڕان و کاتیگۆری -->
            <div class="shrink-0 space-y-2 pb-2.5 border-b border-slate-200/60 dark:border-slate-700/40">
                <div class="flex justify-between items-center gap-2">
                    
                    <!-- گەڕان -->
                    <div class="w-full md:w-80 relative group">
                        <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان بۆ کاڵا..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white/70 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-800 dark:text-white text-[11px] transition-all shadow-sm font-bold">
                        <div class="absolute left-3 top-2.5 w-5 h-5 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                            <i class="fa-solid fa-magnifying-glass text-white text-[9px]"></i>
                        </div>
                        <kbd class="absolute right-3 top-3 text-[9px] font-bold text-slate-400 hidden md:block">Ctrl+K</kbd>
                    </div>
                    
                    <!-- کاتیگۆرییەکان -->
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar w-full md:w-auto">
                        <button type="button" onclick="filterCategory('all')" id="cat-btn-all" 
                                class="cat-filter-btn btn-press bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-2 rounded-xl text-[10px] font-black whitespace-nowrap shadow-lg shadow-indigo-500/30">
                            <i class="fa-solid fa-star text-[9px]"></i> هەمووی
                        </button>
                        <?php foreach($categories as $cat): ?>
                        <button type="button" onclick="filterCategory('<?php echo $cat->id; ?>')" id="cat-btn-<?php echo $cat->id; ?>" 
                                class="cat-filter-btn btn-press bg-white/70 dark:bg-slate-800/70 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-xl text-[10px] font-bold whitespace-nowrap border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition-all">
                            <?php echo $cat->name; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- گرێدی کاڵاکان -->
            <div class="grow overflow-y-auto pt-2.5 pr-0.5 custom-scrollbar grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2 content-start" id="productsGrid">
                <?php foreach($products as $p): ?>
                <?php
                    $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
                    $alertVal = (float) ($p->alert_quantity ?? 5);
                    $isOut = $stockVal <= 0;
                    $isLow = !$isOut && $stockVal <= $alertVal;
                ?>
                <div class="product-card group relative bg-gradient-to-br from-white to-slate-50 dark:from-slate-800/90 dark:to-slate-900/90 border <?php echo $isOut ? 'border-rose-300/60 dark:border-rose-500/40' : ($isLow ? 'border-amber-300/60 dark:border-amber-500/40' : 'border-slate-200/80 dark:border-slate-700/60'); ?> rounded-2xl p-2 flex flex-col justify-between shadow-md hover:shadow-2xl transition-all duration-300 <?php echo $isOut ? 'opacity-60' : ''; ?>"
                     data-category="<?php echo $p->category_id; ?>" 
                     data-name="<?php echo htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8'); ?>" 
                     data-code="<?php echo htmlspecialchars($p->code, ENT_QUOTES, 'UTF-8'); ?>"
                     data-price-usd="<?php echo $p->base_sale_price; ?>">
                    
                    <!-- باجی بڕ -->
                    <div id="qty-badge-<?php echo $p->id; ?>" class="qty-badge hidden absolute -top-1.5 -left-1.5 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white font-num font-black text-[10px] px-2 py-1 rounded-full shadow-lg shadow-emerald-500/50 border-2 border-white dark:border-slate-900 z-20 transition-all duration-300 transform scale-0 flex items-center gap-0.5 pointer-events-none">
                        <i class="fa-solid fa-check text-[7px]"></i> <span class="badge-val">0</span>
                    </div>

                    <div class="cursor-pointer" onclick="addToCart(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>)">
                        
                        <!-- باجی کۆد و دۆخ -->
                        <div class="flex justify-between items-start mb-1.5 relative z-10">
                            <span class="text-[8px] font-num font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-0.5 rounded-lg border border-indigo-200/50 dark:border-indigo-700/50"><?php echo $p->code; ?></span>
                            <?php if($isOut): ?>
                                <span class="text-[8px] font-black text-white bg-gradient-to-r from-rose-500 to-red-600 px-1.5 py-0.5 rounded-lg shadow-sm">نەماوە</span>
                            <?php elseif($isLow): ?>
                                <span class="text-[8px] font-black text-white bg-gradient-to-r from-amber-500 to-orange-600 px-1.5 py-0.5 rounded-lg shadow-sm">کەمە</span>
                            <?php endif; ?>
                        </div>

                        <!-- ناو و بڕ -->
                        <div class="text-center my-1.5 relative z-10">
                            <h3 class="font-black text-slate-800 dark:text-white text-[10px] md:text-[11px] line-clamp-2 leading-tight"><?php echo $p->name; ?></h3>
                            <div class="flex items-center justify-center gap-1 mt-1">
                                <i class="fa-solid fa-cube text-[8px] <?php echo $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-500'); ?>"></i>
                                <p class="text-[9px] text-slate-500 dark:text-slate-400 font-bold">
                                    <span class="font-num <?php echo $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-500'); ?>"><?php echo $stockVal; ?></span> کگ
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- دوگمەکانی + و - -->
                    <div class="mt-1.5 pt-1.5 border-t border-slate-200/70 dark:border-slate-700/50 flex items-center justify-between gap-1 relative z-10">
                        <button type="button" onclick="quickIncrease(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>, event)" 
                                class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl text-sm font-black flex items-center justify-center shadow-md shadow-indigo-500/30 hover:shadow-lg transition-all active:scale-95">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                        
                        <div class="flex flex-col items-center">
                            <span id="price-anim-<?php echo $p->id; ?>" class="text-[11px] font-black font-num text-emerald-600 dark:text-emerald-400 transition-all duration-200 inline-block" dir="ltr">$<?php echo number_format($p->base_sale_price, 2); ?></span>
                        </div>
                        
                        <button type="button" onclick="quickDecrease(<?php echo $p->id; ?>, event)" 
                                class="w-8 h-8 bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-slate-600 dark:text-slate-300 hover:text-rose-600 rounded-xl text-sm font-black flex items-center justify-center transition-all active:scale-95 border border-slate-200 dark:border-slate-700">
                            <i class="fa-solid fa-minus text-[10px]"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- سەبەتە -->
        <!-- ============================================ -->
        <div class="lg:col-span-3 glass-panel rounded-2xl p-2.5 flex flex-col h-[40vh] lg:h-full overflow-hidden relative shadow-xl">
            
            <!-- سەرەتای سەبەتە -->
            <div class="shrink-0 pb-2.5 border-b border-slate-200/60 dark:border-slate-700/40">
                <div class="flex justify-between items-center mb-2">
                    <h2 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="relative">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <i id="cartIconAnim" class="fa-solid fa-cart-shopping text-white text-xs"></i>
                            </div>
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full animate-ping opacity-75 hidden" id="cartPing"></span>
                        </div>
                        سەبەتە
                    </h2>
                    <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()" 
                            class="btn-press bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 px-2.5 py-1.5 rounded-xl text-[9px] font-bold border border-rose-200 dark:border-rose-800/50 hover:bg-rose-100 transition-colors">
                        <i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span>
                    </button>
                </div>

                <div class="bg-white/60 dark:bg-slate-900/60 p-2 rounded-2xl border border-slate-200/80 dark:border-slate-700/60 space-y-2 backdrop-blur">
                    
                    <!-- دراو و ئاڵوگۆڕ -->
                    <div class="flex items-center justify-between gap-1 bg-gradient-to-r from-slate-100 to-slate-50 dark:from-slate-800/60 dark:to-slate-900/60 p-1.5 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                        <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i class="fa-solid fa-coins text-amber-500"></i> دراو:
                        </span>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 transition-all">
                                <i class="fa-solid fa-dollar-sign text-[9px]"></i>
                            </button>
                            <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all">
                                د.ع
                            </button>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="number" id="exchangeRate" value="<?php echo $setting->exchange_rate ?? 1500; ?>" onchange="renderCart(false)" 
                                   class="w-14 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-1.5 py-1 text-[10px] font-num font-black text-center focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                    </div>

                    <!-- بەروار و کڕیار -->
                    <div class="flex gap-1.5">
                        <div class="relative flex-1">
                            <i class="fa-regular fa-calendar absolute left-2.5 top-2 text-indigo-500 text-[10px]"></i>
                            <input type="datetime-local" id="saleCreatedAt" value="<?php echo date('Y-m-d\TH:i'); ?>" 
                                   class="w-full pl-7 pr-2 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-num font-bold focus:outline-none focus:border-indigo-500">
                        </div>
                        <div class="relative flex-1">
                            <i class="fa-solid fa-user absolute left-2.5 top-2 text-emerald-500 text-[10px]"></i>
                            <select id="customerId" class="w-full pl-7 pr-2 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-bold focus:outline-none focus:border-indigo-500">
                                <option value="">کڕیاری نەقد</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- جۆری پارەدان -->
                    <div class="flex p-0.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-xl relative">
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-black text-[10px] z-10 has-[:checked]:text-white transition-colors">
                            <input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()" class="hidden peer">
                            <span><i class="fa-solid fa-money-bill-wave text-[9px]"></i> نەقد</span>
                        </label>
                        <label class="flex-1 text-center py-1.5 rounded-lg cursor-pointer font-black text-[10px] z-10 has-[:checked]:text-white text-slate-600 dark:text-slate-400 transition-colors">
                            <input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()" class="hidden peer">
                            <span><i class="fa-solid fa-clock text-[9px]"></i> قەرز</span>
                        </label>
                        <div class="absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg shadow-md transition-all duration-300" id="paymentSelector"></div>
                    </div>

                    <!-- بڕی پارەی دراو -->
                    <div id="paidAmountBox" class="hidden">
                        <input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0" 
                               class="w-full p-2 rounded-xl bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-700 text-[11px] font-num font-bold focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                    </div>
                </div>
            </div>

            <!-- کاڵاکانی سەبەتە -->
            <div id="cartItemsContainer" class="grow overflow-y-auto py-2 pr-0.5 space-y-2 custom-scrollbar"></div>

            <!-- کۆی گشتی -->
            <div class="shrink-0 pt-2 mt-1 border-t border-slate-200/60 dark:border-slate-700/40">
                <div class="space-y-1.5 mb-2 px-1 text-[10px]">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-calculator text-indigo-500 text-[9px]"></i> کۆی کاڵا:
                        </span>
                        <span id="subTotalText" class="font-num font-black text-slate-700 dark:text-slate-200" dir="ltr">0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-percent text-amber-500 text-[9px]"></i> داشکاندن:
                        </span>
                        <input type="number" min="0" id="cartDiscount" value="0" oninput="renderCart(false)" 
                               class="w-16 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-amber-600 dark:text-amber-400 font-num font-black text-left text-[10px] focus:outline-none border border-slate-200 dark:border-slate-700">
                    </div>
                    <div class="flex justify-between items-end pt-1 pb-1">
                        <span class="font-black text-[12px] text-slate-800 dark:text-white">کۆی گشتی:</span>
                        <span id="grandTotalText" class="text-emerald-500 dark:text-emerald-400 font-num font-black text-xl" dir="ltr">0</span>
                    </div>
                </div>
                
                <button type="button" onclick="submitSale()" id="btnSubmitSale" 
                        class="btn-press w-full gradient-btn-emerald text-white font-black py-3 rounded-2xl text-[12px] flex items-center justify-center gap-2 transition-all duration-300 active:scale-95">
                    <i class="fa-solid fa-paper-plane"></i> پسوولەکردن
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- مۆداڵی سەرکەوتن -->
    <!-- ============================================ -->
    <div id="successModal" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-2 md:p-4 z-[999]">
        <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-5 text-right shadow-2xl flex flex-col max-h-[90vh] animate-scale-in border border-slate-200 dark:border-slate-700">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-emerald-500/30">
                        <i class="fa-solid fa-check text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black dark:text-white">وەسڵ بە سەرکەوتوویی تۆمارکرا!</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">دەتوانیت کاڵاکان دەستکاری بکەیت پێش چاپکردن</p>
                    </div>
                </div>
                <button type="button" onclick="returnToSameSale()" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center hover:bg-rose-100 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="grow overflow-y-auto py-3 custom-scrollbar space-y-2" id="modalItemsList"></div>
            
            <div class="shrink-0 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex justify-between items-center px-3 py-2 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <span class="text-xs font-black text-slate-700 dark:text-slate-200">کۆی گشتی پارە:</span>
                    <span id="modalGrandTotal" class="text-emerald-500 text-lg font-num font-black" dir="ltr">0 IQD</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="#" id="printInvoiceBtn" target="_blank" 
                       class="btn-press py-3 gradient-btn-emerald text-white rounded-xl text-xs font-black flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-solid fa-print"></i> پرینت
                    </a>
                    <button type="button" onclick="returnToSameSale()" 
                            class="btn-press py-3 gradient-btn-brand text-white rounded-xl text-xs font-black active:scale-95">
                        <i class="fa-solid fa-rotate-right"></i> گەڕانەوە
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-3 left-1/2 transform -translate-x-1/2 z-[999] space-y-2 pointer-events-none flex flex-col items-center"></div>

    <!-- ============================================ -->
    <!-- ویجێتی نرخی ئاڵوگۆڕ -->
    <!-- ============================================ -->
    <div id="exchangeRateWidget" class="fixed top-3 right-32 z-[9000]">
        <div class="rounded-2xl shadow-2xl p-2.5 min-w-[200px] border-2 border-white/30 backdrop-blur-sm transition-all duration-300">
            <div id="rateDisplay" onclick="toggleRateEdit()" class="flex items-center justify-between gap-2 cursor-pointer">
                <div class="flex items-center gap-2">
                    <div id="rateIcon" class="w-9 h-9 bg-white/25 rounded-xl flex items-center justify-center border border-white/30 shadow-inner">
                        <i class="fa-solid fa-dollar-sign text-white text-sm"></i>
                    </div>
                    <div class="text-white">
                        <div class="text-[9px] font-black opacity-90 flex items-center gap-1">
                            <i class="fa-solid fa-arrow-right-arrow-left text-[7px]"></i> نرخی ئاڵوگۆڕ
                        </div>
                        <div class="text-sm font-black font-mono" dir="ltr" id="currentRateDisplay">
                            1$ = {{ number_format($setting->exchange_rate ?? 1500) }}
                        </div>
                    </div>
                </div>
                <i class="fa-solid fa-pen-to-square text-white/80 text-xs"></i>
            </div>

            <div id="rateEdit" class="hidden">
                <label class="block text-[10px] font-black text-white mb-1.5 flex items-center gap-1">
                    <i class="fa-solid fa-edit"></i> نرخی نوێ (١$ = چ دینار)
                </label>
                <div class="flex items-center gap-1">
                    <input type="number" id="newExchangeRate" value="{{ $setting->exchange_rate ?? 1500 }}" min="1" step="any"
                           class="w-full text-amber-900 font-black font-mono text-sm p-1.5 rounded-xl text-center focus:outline-none">
                    <button type="button" onclick="saveExchangeRate()" class="bg-emerald-500 hover:bg-emerald-600 text-white p-2 rounded-xl transition-colors shadow-md">
                        <i class="fa-solid fa-check text-xs"></i>
                    </button>
                    <button type="button" onclick="toggleRateEdit()" class="bg-slate-800/70 hover:bg-slate-900 text-white p-2 rounded-xl transition-colors shadow-md">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>
                <p class="text-[9px] text-white/90 mt-1.5 text-center">
                    <i class="fa-solid fa-info-circle"></i> هەموو سیستەمەکە نوێ دەبێتەوە
                </p>
            </div>
        </div>

        <div id="rateSaving" class="hidden absolute inset-0 bg-black/50 rounded-2xl flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-white text-xl"></i>
        </div>
    </div>

    <script>
        const units = <?php echo json_encode($units); ?>;
        let cart = [];
        let clearCartTimer = null;
        let isConfirmingClear = false;
        let lastSaleItems = [];
        let activeSaleId = null;
        let currentCurrency = 'USD'; 
        let currentExchangeRate = <?php echo $setting->exchange_rate ?? 1500; ?>;

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('moreDropdown');
            const moreBtn = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(event.target) && !moreBtn.contains(event.target)) dropdown.classList.add('hidden');
        });

        // کیبۆرد شۆرتکەت: Ctrl+K بۆ گەڕان
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchBox').focus();
            }
        });

        function initTheme() { applyTheme(localStorage.getItem('pos_theme') || 'dark'); }
        function applyTheme(theme) {
            const icons = [document.getElementById('themeIcon'), document.getElementById('themeIconMobile')];
            if (theme === 'dark') { 
                document.documentElement.classList.add('dark'); 
                icons.forEach(i => { if(i) i.className = 'fa-solid fa-moon text-sm'; }); 
            } else { 
                document.documentElement.classList.remove('dark'); 
                icons.forEach(i => { if(i) i.className = 'fa-solid fa-sun text-sm'; }); 
            }
            localStorage.setItem('pos_theme', theme);
        }
        function toggleTheme() { applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark'); }
        initTheme();

        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            let bgClass = 'bg-gradient-to-r from-slate-800 to-slate-900 text-white';
            let icon = '<i class="fa-solid fa-circle-info text-blue-400"></i>';
            
            if (type === 'error') {
                bgClass = 'bg-gradient-to-r from-rose-500 to-red-600 text-white';
                icon = '<i class="fa-solid fa-circle-exclamation"></i>';
            } else if (type === 'success') {
                bgClass = 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white';
                icon = '<i class="fa-solid fa-circle-check"></i>';
            }
            
            toast.className = `pointer-events-auto flex items-center gap-2.5 px-4 py-2.5 rounded-2xl ${bgClass} text-[11px] font-black shadow-2xl transition-all duration-300 transform -translate-y-10 opacity-0`;
            toast.innerHTML = `${icon}<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);
            setTimeout(() => { 
                toast.classList.add('opacity-0', '-translate-y-10'); 
                setTimeout(() => toast.remove(), 300); 
            }, 3000);
        }

        function setCurrency(currency) {
            currentCurrency = currency;
            const btnIqd = document.getElementById('btn-cur-iqd');
            const btnUsd = document.getElementById('btn-cur-usd');
            
            if (currency === 'USD') {
                btnUsd.className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 transition-all';
                btnIqd.className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all';
            } else {
                btnIqd.className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 transition-all';
                btnUsd.className = 'px-2.5 py-1 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all';
            }
            renderCart(false);
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
            btn.classList.remove('bg-rose-500', 'text-white');
            document.getElementById('clearCartLabel').innerText = 'سڕینەوە';
        }

        function filterCategory(catId) {
            document.querySelectorAll('.cat-filter-btn').forEach(btn => { 
                btn.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-500', 'text-white', 'shadow-lg', 'shadow-indigo-500/30'); 
                btn.classList.add('bg-white/70', 'dark:bg-slate-800/70', 'text-slate-600', 'dark:text-slate-300'); 
            });
            const activeBtn = document.getElementById('cat-btn-' + catId);
            if (activeBtn) { 
                activeBtn.classList.add('bg-gradient-to-r', 'from-indigo-500', 'to-purple-500', 'text-white', 'shadow-lg', 'shadow-indigo-500/30'); 
                activeBtn.classList.remove('bg-white/70', 'dark:bg-slate-800/70', 'text-slate-600', 'dark:text-slate-300'); 
            }
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

        function animateFly(startX, startY, endX, endY, text, colorClass) {
            const flyEl = document.createElement('div');
            flyEl.className = `fixed z-[9999] flex items-center justify-center w-7 h-7 rounded-full text-white text-[11px] font-black shadow-xl transition-all ease-in-out ${colorClass}`;
            flyEl.style.transitionDuration = '0.6s'; 
            flyEl.innerText = text;
            flyEl.style.left = startX + 'px'; 
            flyEl.style.top = startY + 'px';
            flyEl.style.opacity = '1'; 
            flyEl.style.transform = 'scale(1)'; 
            flyEl.style.pointerEvents = 'none';
            document.body.appendChild(flyEl); 
            void flyEl.offsetWidth;
            flyEl.style.left = endX + 'px'; 
            flyEl.style.top = endY + 'px';
            flyEl.style.opacity = '0.5'; 
            flyEl.style.transform = 'scale(0.5)';
            setTimeout(() => { flyEl.remove(); }, 600);
        }

        function addToCart(p) {
            const stock = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));
            if (stock <= 0) { showToast('نەماوە!', 'error'); return; }

            const defaultUnitId = getDefaultUnitId();
            const initialUnit = units.find(u => u.id == defaultUnitId) || units[0] || { id: 1, name: 'کیلۆ', factor_to_base: 1 };
            const factor = getUnitFactor(p, initialUnit);
            const basePriceUsd = parseFloat(p.base_sale_price) || 0;
            
            let idx = cart.findIndex(i => i.id === p.id);
            if (idx !== -1) {
                const u = units.find(u => u.id == cart[idx].unit_id) || initialUnit;
                const cFactor = getUnitFactor(p, u);
                const max = cFactor > 0 ? (stock / cFactor) : stock;
                if (cart[idx].qty + 1 > max) { showToast('تەواو بوو!'); cart[idx].qty = max; } else { cart[idx].qty++; }
            } else {
                cart.push({ id: p.id, name: p.name, code: p.code, price_usd: basePriceUsd, stock_kg: stock, kg_per_carton: parseFloat(p.kg_per_carton)||1, qty: 1, unit_id: initialUnit.id, factor: factor });
            }
            renderCart(false);
        }

        function quickIncrease(p, event) { 
            event.stopPropagation(); 
            addToCart(p);
            const btnRect = event.currentTarget.getBoundingClientRect();
            const cartIcon = document.getElementById('cartIconAnim');
            if(cartIcon) {
                const cartRect = cartIcon.getBoundingClientRect();
                animateFly(btnRect.left + (btnRect.width / 2), btnRect.top + (btnRect.height / 2), cartRect.left + (cartRect.width / 2), cartRect.top + (cartRect.height / 2), '+1', 'bg-gradient-to-br from-indigo-500 to-purple-600');
            }
            let el = document.getElementById('price-anim-' + p.id);
            if(el) { 
                el.classList.add('scale-125', 'text-indigo-500'); 
                setTimeout(() => el.classList.remove('scale-125', 'text-indigo-500'), 200); 
            }
        }

        function quickDecrease(productId, event) {
            event.stopPropagation();
            let idx = cart.findIndex(i => i.id === productId);
            if (idx !== -1) {
                if (cart[idx].qty > 1) { cart[idx].qty--; } else { cart.splice(idx, 1); }
                renderCart(false);
                const btnRect = event.currentTarget.getBoundingClientRect();
                const cartIcon = document.getElementById('cartIconAnim');
                if(cartIcon) {
                    const cartRect = cartIcon.getBoundingClientRect();
                    animateFly(cartRect.left + (cartRect.width / 2), cartRect.top + (cartRect.height / 2), btnRect.left + (btnRect.width / 2), btnRect.top + (btnRect.height / 2), '-1', 'bg-gradient-to-br from-rose-500 to-red-600');
                }
                let el = document.getElementById('price-anim-' + productId);
                if(el) { 
                    el.classList.add('scale-75', 'text-rose-500'); 
                    setTimeout(() => el.classList.remove('scale-75', 'text-rose-500'), 200); 
                }
            }
        }

        function updateItemPrice(index, val) { cart[index].price_usd = parseFloat(val) || 0; renderCart(false); }
        function updateItemUnit(index, unitId) {
            const i = cart[index]; 
            const u = units.find(x => x.id == unitId);
            i.unit_id = unitId; 
            i.factor = getUnitFactor(i, u);
            const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            if (i.qty > max) i.qty = max;
            renderCart(false);
        }
        function updateQty(index, delta) {
            const i = cart[index]; 
            const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            const n = i.qty + delta;
            if (n > max) i.qty = max; 
            else if (n <= 0) cart.splice(index, 1); 
            else i.qty = n;
            renderCart(false);
        }
        function setQtyDirect(index, val) {
            const i = cart[index]; 
            const max = i.factor > 0 ? (i.stock_kg / i.factor) : i.stock_kg;
            let num = parseFloat(val); 
            if (isNaN(num) || num <= 0) num = 1;
            i.qty = num > max ? max : num;
            renderCart(false);
        }
        function removeItem(index) { cart.splice(index, 1); renderCart(false); }

        function updateProductBadges() {
            document.querySelectorAll('.qty-badge').forEach(badge => { 
                badge.classList.add('hidden', 'scale-0'); 
                badge.classList.remove('scale-100'); 
            });
            cart.forEach(item => {
                let badge = document.getElementById('qty-badge-' + item.id);
                if (badge) {
                    let valSpan = badge.querySelector('.badge-val');
                    if(valSpan) { 
                        valSpan.innerText = item.qty % 1 === 0 ? item.qty : parseFloat(item.qty).toFixed(2); 
                    }
                    badge.classList.remove('hidden', 'scale-0'); 
                    badge.classList.add('scale-100');
                }
            });
        }

        function renderCart(shouldFocus = false) {
            const container = document.getElementById('cartItemsContainer'); 
            container.innerHTML = ''; 
            let subtotal = 0;
            currentExchangeRate = parseFloat(document.getElementById('exchangeRate').value) || 1500;
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="h-32 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-[10px] font-bold">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center mb-2">
                            <i class="fa-solid fa-cart-arrow-down text-2xl text-slate-400 dark:text-slate-600"></i>
                        </div>
                        سەبەتە بەتاڵە
                        <span class="text-[9px] mt-1 text-slate-400">کلیک لە کاڵا بکە بۆ زیادکردن</span>
                    </div>`;
                document.getElementById('subTotalText').innerText = currentCurrency === 'USD' ? '$0.00' : '0';
                document.getElementById('grandTotalText').innerText = currentCurrency === 'USD' ? '$0.00' : '0';
                resetClearCartButton(); 
                updateProductBadges(); 
                return;
            }

            cart.forEach((item, idx) => {
                const displayPrice = currentCurrency === 'USD' ? item.price_usd : item.price_usd * currentExchangeRate;
                const lineTotal = item.qty * (displayPrice * item.factor);
                subtotal += lineTotal;
                const opts = units.map(u => `<option value="${u.id}" ${item.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('');

                const div = document.createElement('div'); 
                div.id = `cart-row-${idx}`;
                div.className = 'cart-item-enter bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 border border-slate-200/80 dark:border-slate-700/60 rounded-2xl p-2 shadow-sm hover:shadow-md transition-all';
                div.innerHTML = `
                    <div class="flex justify-between items-start mb-1.5">
                        <div class="pr-0.5 flex items-center gap-1.5">
                            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[9px] font-black">
                                ${idx + 1}
                            </div>
                            <h4 class="font-black text-[10px] leading-tight text-slate-800 dark:text-white">${item.name}</h4>
                        </div>
                        <button type="button" onclick="removeItem(${idx})" class="w-6 h-6 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors">
                            <i class="fa-solid fa-xmark text-[9px]"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-12 gap-1 bg-slate-50/80 dark:bg-slate-900/60 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                        <div class="col-span-4">
                            <select onchange="updateItemUnit(${idx}, this.value)" class="w-full bg-transparent text-[9px] font-black focus:outline-none appearance-none cursor-pointer text-indigo-600 dark:text-indigo-400">
                                ${opts}
                            </select>
                        </div>
                        <div class="col-span-4 border-r border-slate-200 dark:border-slate-700">
                            <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? item.price_usd.toFixed(2) : item.price_usd}" 
                                   onchange="updateItemPrice(${idx}, this.value)" 
                                   class="w-full bg-transparent text-center text-[10px] font-black font-num focus:outline-none text-emerald-600 dark:text-emerald-400">
                        </div>
                        <div class="col-span-4 flex items-center justify-between px-0.5 border-r border-slate-200 dark:border-slate-700">
                            <button type="button" onclick="updateQty(${idx}, 1)" class="w-5 h-5 rounded-md bg-indigo-500 text-white font-black text-[10px] flex items-center justify-center active:scale-90 transition-transform">+</button>
                            <input type="number" step="any" min="0.01" value="${item.qty}" 
                                   onchange="setQtyDirect(${idx}, this.value)" 
                                   class="w-7 text-center bg-transparent font-num text-indigo-600 dark:text-indigo-400 text-[11px] font-black focus:outline-none p-0">
                            <button type="button" onclick="updateQty(${idx}, -1)" class="w-5 h-5 rounded-md bg-slate-300 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-black text-[10px] flex items-center justify-center active:scale-90 transition-transform">-</button>
                        </div>
                    </div>`;
                container.appendChild(div);
            });

            const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
            const finalTotal = Math.max(0, subtotal - discount);
            
            if (currentCurrency === 'USD') {
                document.getElementById('subTotalText').innerText = '$' + subtotal.toFixed(2);
                document.getElementById('grandTotalText').innerText = '$' + finalTotal.toFixed(2);
            } else {
                document.getElementById('subTotalText').innerText = Math.round(subtotal).toLocaleString();
                document.getElementById('grandTotalText').innerText = Math.round(finalTotal).toLocaleString();
            }
            updateProductBadges();
        }

        function togglePaymentType() {
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const selector = document.getElementById('paymentSelector'); 
            const box = document.getElementById('paidAmountBox');
            if (isDebt) { 
                selector.style.transform = 'translateX(-100%)'; 
                selector.className = 'absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg shadow-md transition-all duration-300'; 
                box.classList.remove('hidden'); 
            } else { 
                selector.style.transform = 'translateX(0)'; 
                selector.className = 'absolute top-0.5 bottom-0.5 w-[calc(50%-2px)] bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg shadow-md transition-all duration-300'; 
                box.classList.add('hidden'); 
            }
        }

        function submitSale() {
            if (cart.length === 0) { showToast('کاڵا نییە!', 'error'); return; }
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const customerId = document.getElementById('customerId').value;
            if (isDebt && !customerId) { showToast('کڕیار دیاری بکە بۆ قەرز', 'error'); return; }

            const btn = document.getElementById('btnSubmitSale'); 
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوان بە...';
            lastSaleItems = JSON.parse(JSON.stringify(cart));

            fetch('/sales', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>', 'Accept': 'application/json' },
                body: JSON.stringify({
                    customer_id: customerId, payment_type: isDebt ? 'debt' : 'cash',
                    paid_amount: isDebt ? parseFloat(document.getElementById('paidAmount').value) || 0 : null,
                    discount: parseFloat(document.getElementById('cartDiscount').value) || 0,
                    created_at: document.getElementById('saleCreatedAt').value,
                    currency: currentCurrency,
                    exchange_rate: parseFloat(document.getElementById('exchangeRate').value) || 1500,
                    items: cart.map(i => ({ product_id: i.id, unit_id: i.unit_id, quantity: i.qty, base_price: i.price_usd }))
                })
            }).then(res => res.json()).then(data => {
                btn.disabled = false; 
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> پسوولەکردن';
                if (data.success) {
                    activeSaleId = data.sale_id;
                    document.getElementById('printInvoiceBtn').href = '/sales/print/'.concat(data.sale_id);
                    renderModalItems();
                    document.getElementById('successModal').classList.remove('hidden');
                    cart = []; 
                    document.getElementById('cartDiscount').value = 0; 
                    renderCart(false);
                } else { showToast(data.error || 'هەڵە', 'error'); }
            }).catch(err => {
                btn.disabled = false; 
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> پسوولەکردن';
                showToast('کێشەیەک ڕوویدا', 'error');
            });
        }

        function renderModalItems() {
            const listContainer = document.getElementById('modalItemsList');
            listContainer.innerHTML = '';
            let total = 0;

            lastSaleItems.forEach((item, index) => {
                const displayPrice = currentCurrency === 'USD' ? item.price_usd : item.price_usd * currentExchangeRate;
                const lineTotal = item.qty * (displayPrice * item.factor);
                total += lineTotal;
                const u = units.find(x => x.id == item.unit_id);

                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-2 bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs';
                row.innerHTML = `
                    <div class="w-1/3 font-black truncate text-slate-800 dark:text-white">${item.name}</div>
                    <div class="w-1/4 flex items-center gap-1">
                        <input type="number" step="any" min="0.01" value="${item.qty}" onchange="updateModalQty(${index}, this.value)" 
                               class="w-12 bg-white dark:bg-slate-900 text-center font-num font-bold border border-slate-300 dark:border-slate-600 rounded-lg p-1 text-xs">
                        <span class="text-[10px] text-slate-400 font-bold">${u ? u.name : ''}</span>
                    </div>
                    <div class="w-1/4">
                        <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? item.price_usd.toFixed(2) : item.price_usd}" 
                               onchange="updateModalPrice(${index}, this.value)" 
                               class="w-20 bg-white dark:bg-slate-900 text-center font-num border border-slate-300 dark:border-slate-600 rounded-lg p-1 text-xs text-emerald-500 font-black">
                    </div>
                    <div class="w-1/6 text-left font-num font-black text-emerald-600 dark:text-emerald-400" dir="ltr">${currentCurrency === 'USD' ? '$' + lineTotal.toFixed(2) : Math.round(lineTotal).toLocaleString()}</div>
                `;
                listContainer.appendChild(row);
            });

            document.getElementById('modalGrandTotal').innerText = currentCurrency === 'USD' ? '$' + total.toFixed(2) : Math.round(total).toLocaleString().concat(' IQD');
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
            lastSaleItems[index].price_usd = currentCurrency === 'USD' ? p : p / currentExchangeRate;
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

        // ============================================
        // ویجێتی نرخی ئاڵوگۆڕ
        // ============================================
        function toggleRateEdit() {
            const display = document.getElementById('rateDisplay');
            const edit = document.getElementById('rateEdit');
            
            if (edit.classList.contains('hidden')) {
                display.classList.add('hidden');
                edit.classList.remove('hidden');
                setTimeout(() => {
                    const inp = document.getElementById('newExchangeRate');
                    inp.focus();
                    inp.select();
                }, 100);
            } else {
                display.classList.remove('hidden');
                edit.classList.add('hidden');
            }
        }
        
        function saveExchangeRate() {
            const newRate = parseFloat(document.getElementById('newExchangeRate').value);
            
            if (!newRate || newRate < 1) {
                showToast('تکایە نرخێکی دروست بنووسە', 'error');
                return;
            }
            
            document.getElementById('rateSaving').classList.remove('hidden');
            
            fetch('/update-exchange-rate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ exchange_rate: newRate })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('rateSaving').classList.add('hidden');
                
                if (data.success) {
                    document.getElementById('currentRateDisplay').innerText = `1$ = ${newRate.toLocaleString()}`;
                    currentExchangeRate = newRate;
                    
                    const rateInput = document.getElementById('exchangeRate');
                    if (rateInput) rateInput.value = newRate;
                    
                    renderCart(false);
                    toggleRateEdit();
                    showToast('نرخی ئاڵوگۆڕ نوێکرایەوە بۆ ' + newRate.toLocaleString(), 'success');
                } else {
                    showToast(data.message || 'هەڵەیەک ڕوویدا', 'error');
                }
            })
            .catch(err => {
                document.getElementById('rateSaving').classList.add('hidden');
                showToast('کێشەیەک ڕوویدا', 'error');
            });
        }
        
        document.getElementById('newExchangeRate').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveExchangeRate();
            } else if (e.key === 'Escape') {
                toggleRateEdit();
            }
        });
    </script>
</body>
</html>