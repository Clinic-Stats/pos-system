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
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81'
                        },
                        neon: {
                            purple: '#a855f7',
                            pink: '#ec4899',
                            cyan: '#06b6d4',
                            emerald: '#10b981'
                        }
                    },
                    fontFamily: {
                        sans: ['Almarai', 'sans-serif'],
                        num: ['Plus Jakarta Sans', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'scale-in': 'scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'shimmer': 'shimmer 2.5s linear infinite',
                        'gradient-x': 'gradientX 8s ease infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow-pulse': 'glowPulse 3s ease-in-out infinite',
                        'spin-slow': 'spin 8s linear infinite',
                        'bounce-slow': 'bounce 3s infinite',
                        'wiggle': 'wiggle 1s ease-in-out infinite'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: 0
                            },
                            '100%': {
                                opacity: 1
                            }
                        },
                        slideUp: {
                            '0%': {
                                transform: 'translateY(30px)',
                                opacity: 0
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: 1
                            }
                        },
                        scaleIn: {
                            '0%': {
                                transform: 'scale(0.85)',
                                opacity: 0
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: 1
                            }
                        },
                        shimmer: {
                            '0%': {
                                backgroundPosition: '-1000px 0'
                            },
                            '100%': {
                                backgroundPosition: '1000px 0'
                            }
                        },
                        gradientX: {
                            '0%, 100%': {
                                backgroundPosition: '0% 50%'
                            },
                            '50%': {
                                backgroundPosition: '100% 50%'
                            }
                        },
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-15px)'
                            }
                        },
                        glowPulse: {
                            '0%, 100%': {
                                boxShadow: '0 0 20px rgba(168, 85, 247, 0.5), 0 0 40px rgba(168, 85, 247, 0.2)'
                            },
                            '50%': {
                                boxShadow: '0 0 40px rgba(168, 85, 247, 0.8), 0 0 80px rgba(168, 85, 247, 0.4)'
                            }
                        },
                        wiggle: {
                            '0%, 100%': {
                                transform: 'rotate(-3deg)'
                            },
                            '50%': {
                                transform: 'rotate(3deg)'
                            }
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Almarai', sans-serif;
        }

        .font-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: linear-gradient(-45deg, #f8fafc, #e0e7ff, #fce7f3, #cffafe, #f8fafc);
            background-size: 400% 400%;
            animation: gradientX 15s ease infinite;
        }

        .dark body {
            background: #050810;
            background-image:
                radial-gradient(ellipse at 20% 10%, rgba(168, 85, 247, 0.25), transparent 50%),
                radial-gradient(ellipse at 80% 90%, rgba(6, 182, 212, 0.2), transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(236, 72, 153, 0.15), transparent 60%),
                radial-gradient(ellipse at 90% 20%, rgba(16, 185, 129, 0.15), transparent 50%);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(30px) saturate(200%);
            -webkit-backdrop-filter: blur(30px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .dark .glass-panel {
            background: rgba(10, 15, 30, 0.6);
            border: 1px solid rgba(168, 85, 247, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 60px rgba(168, 85, 247, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #a855f7, #ec4899);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #9333ea, #db2777);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .btn-press {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-press:active {
            transform: scale(0.94);
        }

        .nav-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 11px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: rgba(255, 255, 255, 0.9);
            color: #475569;
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .nav-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, transparent 0%, rgba(168, 85, 247, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .nav-btn:hover::after {
            opacity: 1;
        }

        .nav-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 24px rgba(168, 85, 247, 0.2);
            border-color: rgba(168, 85, 247, 0.4);
            color: #9333ea;
        }

        .dark .nav-btn {
            background: rgba(20, 25, 45, 0.8);
            color: #cbd5e1;
            border-color: rgba(168, 85, 247, 0.15);
        }

        .dark .nav-btn:hover {
            background: rgba(168, 85, 247, 0.12);
            border-color: rgba(168, 85, 247, 0.5);
            color: #d8b4fe;
            box-shadow: 0 12px 24px rgba(168, 85, 247, 0.4), 0 0 40px rgba(168, 85, 247, 0.2);
        }

        .product-card {
            position: relative;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
            isolation: isolate;
        }

        .product-card:hover {
            transform: translateY(-6px) scale(1.03);
        }

        .dark .product-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(168, 85, 247, 0.3), 0 0 60px rgba(236, 72, 153, 0.15);
        }

        .gradient-btn-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            background-size: 200% 200%;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            animation: gradientX 4s ease infinite;
        }

        .gradient-btn-emerald:hover {
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .gradient-btn-brand {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            background-size: 200% 200%;
            box-shadow: 0 8px 20px rgba(168, 85, 247, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            animation: gradientX 4s ease infinite;
        }

        .gradient-btn-brand:hover {
            box-shadow: 0 12px 30px rgba(168, 85, 247, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @keyframes colorShift {
            0% {
                background: linear-gradient(135deg, #f59e0b, #ea580c, #dc2626);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.6), 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            25% {
                background: linear-gradient(135deg, #10b981, #059669, #047857);
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.6), 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            50% {
                background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
                box-shadow: 0 0 20px rgba(168, 85, 247, 0.6), 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            75% {
                background: linear-gradient(135deg, #06b6d4, #0ea5e9, #3b82f6);
                box-shadow: 0 0 20px rgba(6, 182, 212, 0.6), 0 8px 20px rgba(0, 0, 0, 0.3);
            }

            100% {
                background: linear-gradient(135deg, #f59e0b, #ea580c, #dc2626);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.6), 0 8px 20px rgba(0, 0, 0, 0.3);
            }
        }

        @keyframes iconSpin {

            0%,
            100% {
                transform: rotate(0deg) scale(1);
            }

            50% {
                transform: rotate(15deg) scale(1.15);
            }
        }

        @keyframes iconFloat {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        #exchangeRateWidget>div:first-child {
            animation: colorShift 10s ease-in-out infinite;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        #exchangeRateWidget:hover>div:first-child {
            transform: scale(1.08);
        }

        #exchangeRateWidget #rateIcon {
            animation: iconSpin 4s ease-in-out infinite, iconFloat 2s ease-in-out infinite;
        }

        #exchangeRateWidget #newExchangeRate {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .bg-shape {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(-30px) scale(0.95);
                opacity: 0;
            }

            100% {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        .cart-item-enter {
            animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .cat-pill {
            position: relative;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        .cat-pill::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: -1;
        }

        .cat-pill:hover::before {
            opacity: 1;
        }

        .cat-pill:hover {
            transform: translateY(-2px) scale(1.05);
            color: white;
            border-color: transparent;
            box-shadow: 0 8px 20px rgba(168, 85, 247, 0.4);
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        @media (max-width: 768px) {
            #exchangeRateWidget {
                top: 70px !important;
                right: 50% !important;
                transform: translateX(50%) !important;
                min-width: 160px;
            }
        }
    </style>
</head>

<body class="text-slate-800 dark:text-slate-100 h-screen p-1 md:p-2 overflow-hidden select-none transition-colors duration-500 flex flex-col">

    <div class="bg-shape" style="width:500px;height:500px;background:radial-gradient(circle, #a855f7, transparent);top:-150px;right:-100px;"></div>
    <div class="bg-shape" style="width:400px;height:400px;background:radial-gradient(circle, #06b6d4, transparent);bottom:-150px;left:-100px;animation-delay:-7s;"></div>
    <div class="bg-shape" style="width:350px;height:350px;background:radial-gradient(circle, #ec4899, transparent);top:40%;left:40%;animation-delay:-12s;"></div>
    <div class="bg-shape" style="width:300px;height:300px;background:radial-gradient(circle, #10b981, transparent);top:20%;left:20%;animation-delay:-4s;"></div>

    <header class="glass-panel px-3 py-2.5 rounded-3xl mb-2 flex flex-col md:flex-row items-center justify-between shadow-2xl z-[100] shrink-0 gap-2 relative">

        <div class="flex items-center justify-between w-full md:w-auto gap-3">
            <div class="flex items-center gap-3 shrink-0">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 via-pink-500 to-cyan-500 rounded-2xl blur opacity-60 group-hover:opacity-100 transition duration-500 animate-glow-pulse"></div>
                    <div class="relative w-11 h-11 md:w-12 md:h-12 rounded-2xl bg-gradient-to-tr from-purple-600 via-pink-500 to-cyan-500 text-white flex items-center justify-center text-xl shadow-xl rotate-3 group-hover:rotate-0 transition-all duration-500">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-base md:text-lg font-black tracking-tight leading-none">
                        <span class="bg-gradient-to-r from-purple-600 via-pink-500 to-cyan-500 bg-clip-text text-transparent">POS</span>
                        <span class="text-slate-900 dark:text-white"> PRO</span>
                    </h1>
                    <p class="text-[9px] text-slate-500 dark:text-slate-400 font-black mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        سیستەمی مۆدێرن
                    </p>
                </div>
            </div>

            <div class="flex md:hidden items-center gap-1.5 shrink-0">
                <button type="button" onclick="toggleTheme()" class="btn-press w-9 h-9 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg">
                    <i id="themeIconMobile" class="fa-solid fa-moon text-sm"></i>
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="w-9 h-9 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-1.5 relative z-[105] w-full md:w-auto">
            <a href="{{ route('purchases.create') }}" class="nav-btn"><i class="fa-solid fa-box-open text-amber-500"></i> کڕین</a>
            <a href="{{ route('products.index') }}" class="nav-btn"><i class="fa-solid fa-boxes-stacked text-cyan-500"></i> کۆگا</a>
            <a href="{{ route('customers.index') }}" class="nav-btn"><i class="fa-solid fa-users text-emerald-500"></i> کڕیار</a>
            <a href="{{ route('reports.index') }}" class="nav-btn"><i class="fa-solid fa-chart-pie text-pink-500"></i> ڕاپۆرت</a>
            <div class="relative inline-block">
                <button type="button" onclick="event.stopPropagation(); document.getElementById('moreDropdown').classList.toggle('hidden')" class="nav-btn">
                    <i class="fa-solid fa-ellipsis text-purple-500"></i> زیاتر
                    <i class="fa-solid fa-chevron-down text-[8px]"></i>
                </button>
                <div id="moreDropdown" class="hidden absolute right-0 top-full mt-2 w-60 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-purple-500/20 z-[9999] max-h-96 overflow-y-auto custom-scrollbar text-xs p-2 animate-slide-up">
                    <a href="{{ route('sales.list') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-emerald-600 dark:text-emerald-400 font-black hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 dark:hover:from-emerald-900/20 dark:hover:to-teal-900/20 transition-all"><i class="fa-solid fa-receipt w-5 text-center"></i> فرۆشتنەکان</a>
                    <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-purple-900/20 dark:hover:to-pink-900/20 transition-all"><i class="fa-solid fa-tags w-5 text-center text-purple-500"></i> کاتیگۆری</a>
                    <a href="{{ route('units.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 dark:hover:from-orange-900/20 dark:hover:to-amber-900/20 transition-all"><i class="fa-solid fa-scale-balanced w-5 text-center text-orange-500"></i> یەکەکان</a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 dark:hover:from-cyan-900/20 dark:hover:to-blue-900/20 transition-all"><i class="fa-solid fa-truck-field w-5 text-center text-cyan-500"></i> دابینکەران</a>
                    <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-rose-50 hover:to-red-50 dark:hover:from-rose-900/20 dark:hover:to-red-900/20 transition-all"><i class="fa-solid fa-money-bill-trend-up w-5 text-center text-rose-500"></i> خەرجییەکان</a>
                    <a href="{{ route('partners.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-yellow-50 hover:to-amber-50 dark:hover:from-yellow-900/20 dark:hover:to-amber-900/20 transition-all"><i class="fa-solid fa-handshake w-5 text-center text-yellow-500"></i> هاوبەشەکان</a>
                    <a href="{{ route('returns.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 dark:hover:from-red-900/20 dark:hover:to-rose-900/20 transition-all"><i class="fa-solid fa-rotate-left w-5 text-center text-red-500"></i> گەڕاوەکان</a>
                    <a href="{{ route('mandub.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-teal-50 hover:to-emerald-50 dark:hover:from-teal-900/20 dark:hover:to-emerald-900/20 transition-all"><i class="fa-solid fa-motorcycle w-5 text-center text-teal-500"></i> مەندووب</a>
                    <a href="{{ route('settings.receipt') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-slate-50 hover:to-gray-50 dark:hover:from-slate-700/50 dark:hover:to-gray-700/50 transition-all"><i class="fa-solid fa-gear w-5 text-center text-slate-500"></i> ڕێکخستنی وەسڵ</a>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all"><i class="fa-solid fa-user-shield w-5 text-center text-indigo-600"></i> کارمەندان</a>
                </div>
            </div>
        </div>

        <div class="hidden md:flex items-center gap-2 shrink-0">
            <button type="button" onclick="toggleTheme()" class="btn-press relative w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 via-orange-500 to-red-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/40 hover:shadow-xl hover:shadow-amber-500/60 transition-all">
                <i id="themeIcon" class="fa-solid fa-moon text-sm"></i>
            </button>

            <div class="flex items-center gap-2.5 bg-white/70 dark:bg-slate-900/70 backdrop-blur px-3 py-1.5 rounded-2xl border border-slate-200/80 dark:border-purple-500/20 shadow-sm">
                <div class="relative">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl blur opacity-60"></div>
                    <div class="relative w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-user text-[11px]"></i>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-[11px] text-slate-800 dark:text-slate-100 leading-tight">{{ auth()->user()->name ?? 'کاشیر' }}</span>
                    <span class="text-[8px] text-emerald-500 font-black flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        ئۆنلاین
                    </span>
                </div>
                <div class="h-5 w-px bg-slate-300 dark:bg-slate-700"></div>
                <form action="{{ route('logout') }}" method="POST" class="inline m-0">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="w-8 h-8 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white transition-all flex items-center justify-center" title="دەرچوون">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex flex-col lg:grid lg:grid-cols-12 gap-2 h-full overflow-hidden relative z-0">

        <div class="lg:col-span-9 glass-panel rounded-3xl p-3 flex flex-col h-[55vh] lg:h-full overflow-hidden relative">

            <div class="shrink-0 space-y-2.5 pb-2.5 border-b border-slate-200/60 dark:border-purple-500/10">
                <div class="flex justify-between items-center gap-2.5">

                    <div class="w-full md:w-96 relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-500 via-pink-500 to-cyan-500 rounded-2xl blur opacity-30 group-focus-within:opacity-70 transition duration-300"></div>
                        <div class="relative">
                            <input type="text" id="searchBox" onkeyup="searchProducts()" placeholder="گەڕان بۆ کاڵا..."
                                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                class="w-full pl-11 pr-16 py-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 focus:border-purple-500 text-slate-800 dark:text-white text-xs transition-all shadow-sm font-bold">
                            <div class="absolute left-2 top-2 w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-magnifying-glass text-white text-xs"></i>
                            </div>
                            <kbd class="absolute right-3 top-3 text-[9px] font-black text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 hidden md:block">Ctrl+K</kbd>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar w-full md:w-auto">
                        <button type="button" onclick="filterCategory('all')" id="cat-btn-all"
                            class="cat-filter-btn cat-pill btn-press bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 text-white px-4 py-2.5 rounded-2xl text-[10px] font-black whitespace-nowrap shadow-lg shadow-purple-500/40">
                            <i class="fa-solid fa-star text-[9px]"></i> هەمووی
                        </button>
                        <?php foreach ($categories as $cat): ?>
                            <button type="button" onclick="filterCategory('<?php echo $cat->id; ?>')" id="cat-btn-<?php echo $cat->id; ?>"
                                class="cat-filter-btn cat-pill btn-press bg-white/80 dark:bg-slate-900/80 text-slate-600 dark:text-slate-300 px-4 py-2.5 rounded-2xl text-[10px] font-black whitespace-nowrap border border-slate-200 dark:border-slate-700">
                                <?php echo $cat->name; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="grow overflow-y-auto pt-3 pr-0.5 custom-scrollbar grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2.5 content-start" id="productsGrid">
                <?php foreach ($products as $p): ?>
                    <?php
                    $stockVal = (float) ($p->stock_kg ?? $p->stock ?? 0);
                    $alertVal = (float) ($p->alert_quantity ?? 5);
                    $isOut = $stockVal <= 0;
                    $isLow = !$isOut && $stockVal <= $alertVal;
                    ?>
                    <div class="product-card group relative bg-gradient-to-br from-white via-slate-50 to-white dark:from-slate-900 dark:via-slate-900 dark:to-slate-950 border <?php echo $isOut ? 'border-rose-300/60 dark:border-rose-500/40' : ($isLow ? 'border-amber-300/60 dark:border-amber-500/40' : 'border-slate-200/80 dark:border-purple-500/20'); ?> rounded-2xl p-2.5 flex flex-col justify-between shadow-lg hover:shadow-2xl transition-all duration-300 <?php echo $isOut ? 'opacity-60' : ''; ?>"
                        data-category="<?php echo $p->category_id; ?>"
                        data-name="<?php echo htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8'); ?>"
                        data-code="<?php echo htmlspecialchars($p->code, ENT_QUOTES, 'UTF-8'); ?>"
                        data-price-usd="<?php echo $p->base_sale_price; ?>">

                        <div id="qty-badge-<?php echo $p->id; ?>" class="qty-badge hidden absolute -top-3 -left-3 bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 text-white font-num font-black text-[16px] px-3.5 py-1.5 rounded-full shadow-xl shadow-emerald-500/60 border-2 border-white dark:border-slate-900 z-20 transition-all duration-300 transform scale-0 flex items-center gap-1.5 pointer-events-none">
                            <i class="fa-solid fa-check text-[12px]"></i> <span class="badge-val">0</span>
                        </div>

                        <div class="cursor-pointer" onclick="addToCart(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>)">

                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <span class="text-[8px] font-num font-black text-purple-600 dark:text-purple-300 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/40 px-2 py-1 rounded-lg border border-purple-200/50 dark:border-purple-700/50 shadow-sm"><?php echo $p->code; ?></span>
                                <?php if ($isOut): ?>
                                    <span class="text-[8px] font-black text-white bg-gradient-to-r from-rose-500 to-red-600 px-2 py-1 rounded-lg shadow-md flex items-center gap-1">
                                        <i class="fa-solid fa-xmark text-[7px]"></i> نەماوە
                                    </span>
                                <?php elseif ($isLow): ?>
                                    <span class="text-[8px] font-black text-white bg-gradient-to-r from-amber-500 to-orange-600 px-2 py-1 rounded-lg shadow-md flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[7px]"></i> کەمە
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="text-center my-2 relative z-10">
                                <h3 class="font-black text-slate-800 dark:text-white text-[11px] leading-tight mb-1.5 line-clamp-2"><?php echo $p->name; ?></h3>
                                <div class="inline-flex items-center justify-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100/70 dark:bg-slate-800/70 border border-slate-200/50 dark:border-slate-700/50">
                                    <i class="fa-solid fa-cube text-[8px] <?php echo $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-500'); ?>"></i>
                                    <span class="text-[9px] font-num font-black <?php echo $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400'); ?>"><?php echo $stockVal; ?></span>
                                    <span class="text-[8px] text-slate-500 dark:text-slate-400 font-bold">کگ</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 pt-2 border-t border-slate-200/70 dark:border-slate-700/40 flex items-center justify-between gap-1.5 relative z-10">
                            <button type="button" onclick="quickIncrease(<?php echo htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8'); ?>, event)"
                                class="w-9 h-9 bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 hover:from-purple-600 hover:via-purple-700 hover:to-pink-700 text-white rounded-xl text-sm font-black flex items-center justify-center shadow-lg shadow-purple-500/40 hover:shadow-xl hover:shadow-purple-500/60 transition-all active:scale-90">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                            </button>

                            <div class="flex flex-col items-center">
                                <span id="price-anim-<?php echo $p->id; ?>" class="text-[12px] font-black font-num bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent transition-all duration-200 inline-block" dir="ltr">$<?php echo number_format($p->base_sale_price, 2); ?></span>
                            </div>

                            <button type="button" onclick="quickDecrease(<?php echo $p->id; ?>, event)"
                                class="w-9 h-9 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 hover:from-rose-100 hover:to-rose-200 dark:hover:from-rose-900/40 dark:hover:to-rose-900/40 text-slate-600 dark:text-slate-300 hover:text-rose-600 rounded-xl text-sm font-black flex items-center justify-center transition-all active:scale-90 border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-minus text-[11px]"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="lg:col-span-3 glass-panel rounded-3xl p-3 flex flex-col h-[40vh] lg:h-full overflow-hidden relative shadow-2xl">

            <div class="shrink-0 pb-3 border-b border-slate-200/60 dark:border-purple-500/10">
                <div class="flex justify-between items-center mb-2.5">
                    <h2 class="text-sm font-black text-slate-800 dark:text-white flex items-center gap-2">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-60"></div>
                            <div class="relative w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-400 via-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/40">
                                <i id="cartIconAnim" class="fa-solid fa-cart-shopping text-white text-sm"></i>
                            </div>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full animate-ping opacity-75 hidden" id="cartPing"></span>
                        </div>
                        <div class="flex flex-col">
                            <span>سەبەتە</span>
                            <span class="text-[8px] text-slate-500 dark:text-slate-400 font-bold">کاڵاکانی هەڵبژێردراو</span>
                        </div>
                    </h2>
                    <button type="button" id="btnClearCart" onclick="handleClearCartTwoClicks()"
                        class="btn-press bg-gradient-to-r from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 text-rose-600 dark:text-rose-400 px-3 py-2 rounded-xl text-[9px] font-black border border-rose-200 dark:border-rose-800/50 hover:from-rose-100 hover:to-red-100 transition-all">
                        <i class="fa-solid fa-trash-can"></i> <span id="clearCartLabel">سڕینەوە</span>
                    </button>
                </div>

                <div class="bg-white/70 dark:bg-slate-900/60 p-2.5 rounded-2xl border border-slate-200/80 dark:border-purple-500/20 space-y-2 backdrop-blur">

                    <div class="flex items-center justify-between gap-1.5 bg-gradient-to-r from-slate-100 to-slate-50 dark:from-slate-800/60 dark:to-slate-900/60 p-2 rounded-xl border border-slate-200/50 dark:border-slate-700/50">
                        <span class="text-[10px] font-black text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i class="fa-solid fa-coins text-amber-500"></i> دراو:
                        </span>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/40 transition-all">
                                <i class="fa-solid fa-dollar-sign text-[10px]"></i>
                            </button>
                            <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all">
                                د.ع
                            </button>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="number" id="exchangeRate" value="<?php echo $setting->exchange_rate ?? 1500; ?>" onchange="renderCart(false)"
                                autocomplete="off"
                                class="w-16 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg px-2 py-1.5 text-[10px] font-num font-black text-center focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
                        </div>
                    </div>

                    <div class="flex gap-1.5">
                        <div class="relative flex-1">
                            <i class="fa-regular fa-calendar absolute left-3 top-2.5 text-purple-500 text-[10px]"></i>
                            <input type="datetime-local" id="saleCreatedAt" value="<?php echo date('Y-m-d\TH:i'); ?>"
                                autocomplete="off"
                                class="w-full pl-8 pr-2 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] font-num font-bold focus:outline-none focus:border-purple-500">
                        </div>
                        <div class="relative flex-1">
                            <i class="fa-solid fa-user absolute left-3 top-2.5 text-emerald-500 text-[10px]"></i>
                            <select id="customerId" class="w-full pl-8 pr-2 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] font-bold focus:outline-none focus:border-purple-500">
                                <option value="">کڕیاری نەقد</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo $c->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex p-1 bg-slate-200/70 dark:bg-slate-800/80 rounded-xl relative">
                        <label class="flex-1 text-center py-2 rounded-lg cursor-pointer font-black text-[10px] z-10 has-[:checked]:text-white transition-colors">
                            <input type="radio" name="paymentType" value="cash" checked onchange="togglePaymentType()" class="hidden peer">
                            <span><i class="fa-solid fa-money-bill-wave text-[10px]"></i> نەقد</span>
                        </label>
                        <label class="flex-1 text-center py-2 rounded-lg cursor-pointer font-black text-[10px] z-10 has-[:checked]:text-white text-slate-600 dark:text-slate-400 transition-colors">
                            <input type="radio" name="paymentType" value="debt" onchange="togglePaymentType()" class="hidden peer">
                            <span><i class="fa-solid fa-clock text-[10px]"></i> قەرز</span>
                        </label>
                        <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg transition-all duration-300" id="paymentSelector"></div>
                    </div>

                    <div id="paidAmountBox" class="hidden">
                        <input type="number" id="paidAmount" placeholder="بڕی پارەی دراو" value="0" min="0"
                            autocomplete="off"
                            class="w-full p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 text-[11px] font-num font-black focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                    </div>
                </div>
            </div>
            <div id="editBanner" class="hidden shrink-0 mt-2 px-3 py-2 rounded-xl bg-amber-500/15 border border-amber-500/40 text-amber-600 dark:text-amber-300 text-[10px] font-black flex items-center justify-between">
                <span><i class="fa-solid fa-pen-to-square"></i> دەستکاریکردنی وەسڵ <span id="editInvoiceNo" dir="ltr"></span></span>
                <a href="{{ route('pos.index') }}" class="underline">پسوولەی نوێ</a>
            </div>
            <div id="cartItemsContainer" class="grow overflow-y-auto py-2.5 pr-0.5 space-y-2 custom-scrollbar"></div>

            <div class="shrink-0 pt-2.5 mt-1 border-t border-slate-200/60 dark:border-purple-500/10">
                <div class="space-y-1.5 mb-2.5 px-1 text-[10px]">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-purple-500 text-[10px]"></i> کۆی کاڵا:
                        </span>
                        <span id="subTotalText" class="font-num font-black text-slate-700 dark:text-slate-200" dir="ltr">0</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-percent text-amber-500 text-[10px]"></i> داشکاندن:
                        </span>
                        <input type="number" min="0" id="cartDiscount" value="0" oninput="renderCart(false)"
                            autocomplete="off"
                            class="w-20 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg text-amber-600 dark:text-amber-400 font-num font-black text-left text-[10px] focus:outline-none border border-slate-200 dark:border-slate-700 focus:border-purple-500">
                    </div>
                    <div class="flex justify-between items-end pt-1.5 pb-1 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/10 dark:to-teal-900/10 px-2.5 py-2 rounded-xl border border-emerald-200/50 dark:border-emerald-800/30">
                        <span class="font-black text-[12px] text-slate-800 dark:text-white">کۆی گشتی:</span>
                        <span id="grandTotalText" class="bg-gradient-to-r from-emerald-500 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent font-num font-black text-xl" dir="ltr">0</span>
                    </div>
                </div>

                <button type="button" onclick="submitSale()" id="btnSubmitSale"
                    class="btn-press w-full gradient-btn-emerald text-white font-black py-3.5 rounded-2xl text-[13px] flex items-center justify-center gap-2 transition-all duration-300 active:scale-95 relative overflow-hidden group">
                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                    <i class="fa-solid fa-paper-plane"></i> پسوولەکردن
                </button>
            </div>
        </div>
    </div>

    <div id="successModal" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-xl flex items-center justify-center p-2 md:p-4 z-[999]">
        <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-6 text-right shadow-2xl flex flex-col max-h-[90vh] animate-scale-in border-2 border-purple-500/30 dark:border-purple-500/40 relative overflow-hidden">

            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-60 animate-pulse"></div>
                        <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-400 via-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg">
                            <i class="fa-solid fa-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-black dark:text-white bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent">وەسڵ تۆمارکرا!</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5 font-bold">دەتوانیت کاڵاکان دەستکاری بکەیت پێش چاپکردن</p>
                    </div>
                </div>
                <button type="button" onclick="returnToSameSale()" class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-500 flex items-center justify-center hover:bg-rose-100 hover:text-rose-500 hover:rotate-90 transition-all duration-300">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="grow overflow-y-auto py-4 custom-scrollbar space-y-2" id="modalItemsList"></div>

            <div class="shrink-0 pt-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex justify-between items-center px-4 py-3 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 dark:from-emerald-900/20 dark:via-teal-900/20 dark:to-cyan-900/20 rounded-2xl border-2 border-emerald-200 dark:border-emerald-800/50">
                    <span class="text-sm font-black text-slate-700 dark:text-slate-200">کۆی گشتی پارە:</span>
                    <span id="modalGrandTotal" class="text-emerald-500 text-xl font-num font-black" dir="ltr">0 IQD</span>
                </div>
                <div class="grid grid-cols-3 gap-2.5">
                    <a href="#" id="printA4Btn" target="_blank"
                        class="btn-press py-3.5 gradient-btn-emerald text-white rounded-2xl text-xs font-black flex items-center justify-center gap-2 active:scale-95 shadow-lg">
                        <i class="fa-solid fa-file-lines"></i> چاپی A4
                    </a>
                    <a href="#" id="printSmallBtn" target="_blank"
                        class="btn-press py-3.5 bg-gradient-to-br from-cyan-500 to-blue-600 text-white rounded-2xl text-xs font-black flex items-center justify-center gap-2 active:scale-95 shadow-lg">
                        <i class="fa-solid fa-receipt"></i> چاپی بچووک
                    </a>
                    <button type="button" onclick="returnToSameSale()"
                        class="btn-press py-3.5 gradient-btn-brand text-white rounded-2xl text-xs font-black active:scale-95 shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i> گەڕانەوە
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="fixed top-3 left-1/2 transform -translate-x-1/2 z-[999] space-y-2 pointer-events-none flex flex-col items-center"></div>

    <div id="exchangeRateWidget" class="fixed top-3 right-56 z-[9000]">
        <div class="rounded-2xl shadow-2xl p-2 min-w-[180px] border-2 border-white/30 backdrop-blur-sm transition-all duration-300">
            <div id="rateDisplay" onclick="toggleRateEdit()" class="flex items-center justify-between gap-2 cursor-pointer">
                <div class="flex items-center gap-2">
                    <div id="rateIcon" class="w-8 h-8 bg-white/25 rounded-xl flex items-center justify-center border border-white/30 shadow-inner backdrop-blur">
                        <i class="fa-solid fa-dollar-sign text-white text-sm"></i>
                    </div>
                    <div class="text-white">
                        <div class="text-[8px] font-black opacity-90 flex items-center gap-1">
                            <i class="fa-solid fa-arrow-right-arrow-left text-[7px]"></i> نرخی ئاڵوگۆڕ
                        </div>
                        <div class="text-sm font-black font-mono" dir="ltr" id="currentRateDisplay">
                            1$ = {{ number_format($setting->exchange_rate ?? 1500) }}
                        </div>
                    </div>
                </div>
                <i class="fa-solid fa-pen-to-square text-white/80 text-[10px]"></i>
            </div>

            <div id="rateEdit" class="hidden">
                <label class="block text-[9px] font-black text-white mb-1 flex items-center gap-1">
                    <i class="fa-solid fa-edit"></i> نرخی نوێ (١$ = چ دینار)
                </label>
                <div class="flex items-center gap-1">
                    <input type="number" id="newExchangeRate" value="{{ $setting->exchange_rate ?? 1500 }}" min="1" step="any"
                        autocomplete="off"
                        class="w-full text-amber-900 font-black font-mono text-xs p-1.5 rounded-lg text-center focus:outline-none">
                    <button type="button" onclick="saveExchangeRate()" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded-lg transition-colors shadow-md">
                        <i class="fa-solid fa-check text-[10px]"></i>
                    </button>
                    <button type="button" onclick="toggleRateEdit()" class="bg-slate-800/70 hover:bg-slate-900 text-white p-1.5 rounded-lg transition-colors shadow-md">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>
                <p class="text-[8px] text-white/90 mt-1 text-center">
                    <i class="fa-solid fa-info-circle"></i> هەموو سیستەمەکە نوێ دەبێتەوە
                </p>
            </div>
        </div>

        <div id="rateSaving" class="hidden absolute inset-0 bg-black/50 rounded-2xl flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-white text-lg"></i>
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
        const allProducts = <?php echo json_encode($products); ?>;
        const editSale = <?php echo isset($editSale) ? json_encode($editSale) : 'null'; ?>;
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('moreDropdown');
            const moreBtn = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(event.target) && !moreBtn.contains(event.target)) dropdown.classList.add('hidden');
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('searchBox').focus();
            }
        });

        function initTheme() {
            applyTheme(localStorage.getItem('pos_theme') || 'dark');
        }

        function applyTheme(theme) {
            const icons = [document.getElementById('themeIcon'), document.getElementById('themeIconMobile')];
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                icons.forEach(i => {
                    if (i) i.className = 'fa-solid fa-moon text-sm';
                });
            } else {
                document.documentElement.classList.remove('dark');
                icons.forEach(i => {
                    if (i) i.className = 'fa-solid fa-sun text-sm';
                });
            }
            localStorage.setItem('pos_theme', theme);
        }

        function toggleTheme() {
            applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
        }
        initTheme();

        function showToast(message, type = 'warning') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            let bgClass = 'bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 text-white';
            let icon = '<i class="fa-solid fa-circle-info"></i>';

            if (type === 'error') {
                bgClass = 'bg-gradient-to-r from-rose-500 to-red-600 text-white';
                icon = '<i class="fa-solid fa-circle-exclamation"></i>';
            } else if (type === 'success') {
                bgClass = 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white';
                icon = '<i class="fa-solid fa-circle-check"></i>';
            }

            toast.className = `pointer-events-auto flex items-center gap-2.5 px-5 py-3 rounded-2xl ${bgClass} text-[11px] font-black shadow-2xl transition-all duration-500 transform -translate-y-10 opacity-0 backdrop-blur`;
            toast.innerHTML = `${icon}<span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.classList.remove('-translate-y-10', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-10');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        function setCurrency(currency) {
            currentCurrency = currency;
            const btnIqd = document.getElementById('btn-cur-iqd');
            const btnUsd = document.getElementById('btn-cur-usd');

            if (currency === 'USD') {
                btnUsd.className = 'px-3 py-1.5 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/40 transition-all';
                btnIqd.className = 'px-3 py-1.5 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all';
            } else {
                btnIqd.className = 'px-3 py-1.5 rounded-lg text-[10px] font-black bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/40 transition-all';
                btnUsd.className = 'px-3 py-1.5 rounded-lg text-[10px] font-black bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all';
            }
            renderCart(false);
        }

        function handleClearCartTwoClicks() {
            if (cart.length === 0) return;
            const btn = document.getElementById('btnClearCart');
            if (!isConfirmingClear) {
                isConfirmingClear = true;
                btn.classList.add('bg-rose-500', 'text-white', 'animate-wiggle');
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
            btn.classList.remove('bg-rose-500', 'text-white', 'animate-wiggle');
            document.getElementById('clearCartLabel').innerText = 'سڕینەوە';
        }

        function filterCategory(catId) {
            document.querySelectorAll('.cat-filter-btn').forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-purple-500', 'via-pink-500', 'to-purple-500', 'text-white', 'shadow-lg', 'shadow-purple-500/40');
                btn.classList.add('bg-white/80', 'dark:bg-slate-900/80', 'text-slate-600', 'dark:text-slate-300');
            });
            const activeBtn = document.getElementById('cat-btn-' + catId);
            if (activeBtn) {
                activeBtn.classList.add('bg-gradient-to-r', 'from-purple-500', 'via-pink-500', 'to-purple-500', 'text-white', 'shadow-lg', 'shadow-purple-500/40');
                activeBtn.classList.remove('bg-white/80', 'dark:bg-slate-900/80', 'text-slate-600', 'dark:text-slate-300');
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
            flyEl.className = `fixed z-[9999] flex items-center justify-center w-8 h-8 rounded-full text-white text-[12px] font-black shadow-2xl ${colorClass}`;

            flyEl.style.transition = 'left 0.7s cubic-bezier(0.4, 0, 0.2, 1), top 0.7s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.7s ease, transform 0.7s ease';
            flyEl.innerText = text;
            flyEl.style.left = startX + 'px';
            flyEl.style.top = startY + 'px';
            flyEl.style.opacity = '1';
            flyEl.style.transform = 'scale(1)';
            flyEl.style.pointerEvents = 'none';
            flyEl.style.willChange = 'left, top, opacity, transform';

            document.body.appendChild(flyEl);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    flyEl.style.left = endX + 'px';
                    flyEl.style.top = endY + 'px';
                    flyEl.style.opacity = '0.2';
                    flyEl.style.transform = 'scale(0.3)';
                });
            });

            setTimeout(() => {
                flyEl.remove();
            }, 800);
        }

        function addToCart(p) {
            const stock = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));
            const alreadyInCart = cart.some(i => i.id === p.id);

            if (stock <= 0 && !alreadyInCart) {
                showToast('نەماوە!', 'error');
                return false;
            }

            const defaultUnitId = getDefaultUnitId();
            const initialUnit = units.find(u => u.id == defaultUnitId) || units[0] || {
                id: 1,
                name: 'کیلۆ',
                factor_to_base: 1
            };
            const factor = getUnitFactor(p, initialUnit);
            const basePriceUsd = parseFloat(p.base_sale_price) || 0;

            let idx = cart.findIndex(i => i.id === p.id);
            if (idx !== -1) {
                const u = units.find(u => u.id == cart[idx].unit_id) || initialUnit;
                const cFactor = getUnitFactor(p, u);
                const availKg = cart[idx].stock_kg; // کۆگا + ئەوەی لەم وەسڵەدا فرۆشراوە
                const max = cFactor > 0 ? (availKg / cFactor) : availKg;

                if (cart[idx].qty >= max) {
                    showToast('تەواو بوو!');
                    return false;
                }

                if (cart[idx].qty + 1 > max) {
                    showToast('تەواو بوو!');
                    cart[idx].qty = max;
                    renderCart(false);
                    return false;
                } else {
                    cart[idx].qty++;
                }
            } else {
                cart.push({
                    id: p.id,
                    name: p.name,
                    code: p.code,
                    price_usd: basePriceUsd,
                    stock_kg: stock,
                    kg_per_carton: parseFloat(p.kg_per_carton) || 1,
                    qty: 1,
                    unit_id: initialUnit.id,
                    factor: factor
                });
            }
            renderCart(false);
            return true;
        }

        function quickIncrease(p, event) {
            event.stopPropagation();

            const success = addToCart(p);

            if (success) {
                const btnRect = event.currentTarget.getBoundingClientRect();
                const cartIcon = document.getElementById('cartIconAnim');
                if (cartIcon) {
                    const cartRect = cartIcon.getBoundingClientRect();
                    animateFly(
                        btnRect.left + (btnRect.width / 2),
                        btnRect.top + (btnRect.height / 2),
                        cartRect.left + (cartRect.width / 2),
                        cartRect.top + (cartRect.height / 2),
                        '+1',
                        'bg-gradient-to-br from-purple-500 to-pink-600'
                    );
                }
                let el = document.getElementById('price-anim-' + p.id);
                if (el) {
                    el.classList.add('scale-125');
                    setTimeout(() => el.classList.remove('scale-125'), 200);
                }
            }
        }

        function quickDecrease(productId, event) {
            event.stopPropagation();
            let idx = cart.findIndex(i => i.id === productId);
            if (idx !== -1) {
                if (cart[idx].qty > 1) {
                    cart[idx].qty--;
                } else {
                    cart.splice(idx, 1);
                }
                renderCart(false);

                const btnRect = event.currentTarget.getBoundingClientRect();
                const cartIcon = document.getElementById('cartIconAnim');
                if (cartIcon) {
                    const cartRect = cartIcon.getBoundingClientRect();
                    animateFly(
                        cartRect.left + (cartRect.width / 2),
                        cartRect.top + (cartRect.height / 2),
                        btnRect.left + (btnRect.width / 2),
                        btnRect.top + (btnRect.height / 2),
                        '-1',
                        'bg-gradient-to-br from-rose-500 to-red-600'
                    );
                }
                let el = document.getElementById('price-anim-' + productId);
                if (el) {
                    el.classList.add('scale-75');
                    setTimeout(() => el.classList.remove('scale-75'), 200);
                }
            }
        }

        function updateItemPrice(index, val) {
            cart[index].price_usd = parseFloat(val) || 0;
            renderCart(false);
        }

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

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart(false);
        }

        function updateProductBadges() {
            document.querySelectorAll('.qty-badge').forEach(badge => {
                badge.classList.add('hidden', 'scale-0');
                badge.classList.remove('scale-100');
            });
            cart.forEach(item => {
                let badge = document.getElementById('qty-badge-' + item.id);
                if (badge) {
                    let valSpan = badge.querySelector('.badge-val');
                    if (valSpan) {
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
                    <div class="h-40 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-[10px] font-bold">
                        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-purple-100 via-pink-100 to-cyan-100 dark:from-purple-900/20 dark:via-pink-900/20 dark:to-cyan-900/20 flex items-center justify-center mb-3 border border-purple-200/50 dark:border-purple-700/30">
                            <i class="fa-solid fa-cart-arrow-down text-3xl text-purple-400 dark:text-purple-600"></i>
                        </div>
                        سەبەتە بەتاڵە
                        <span class="text-[9px] mt-1.5 text-slate-400 flex items-center gap-1">
                            <i class="fa-solid fa-hand-pointer text-[8px]"></i>
                            کلیک لە کاڵا بکە بۆ زیادکردن
                        </span>
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
                div.className = 'cart-item-enter bg-gradient-to-br from-white via-purple-50/30 to-pink-50/30 dark:from-slate-900 dark:via-purple-950/20 dark:to-pink-950/20 border border-purple-200/60 dark:border-purple-500/30 rounded-2xl p-2.5 shadow-md hover:shadow-lg transition-all';
                div.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div class="pr-0.5 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-purple-500 via-pink-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black shadow-md">
                                ${idx + 1}
                            </div>
                            <h4 class="font-black text-[10px] leading-tight text-slate-800 dark:text-white">${item.name}</h4>
                        </div>
                        <button type="button" onclick="removeItem(${idx})" class="w-7 h-7 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white hover:rotate-90 transition-all duration-300">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-12 gap-1 bg-white/60 dark:bg-slate-900/60 p-1.5 rounded-xl border border-purple-200/40 dark:border-purple-500/20">
                        <div class="col-span-4">
                            <select onchange="updateItemUnit(${idx}, this.value)" class="w-full bg-transparent text-[9px] font-black focus:outline-none appearance-none cursor-pointer text-purple-600 dark:text-purple-400">
                                ${opts}
                            </select>
                        </div>
                        <div class="col-span-4 border-r border-purple-200/40 dark:border-purple-500/20">
                            <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? item.price_usd.toFixed(2) : item.price_usd}" 
                                   onchange="updateItemPrice(${idx}, this.value)" 
                                   autocomplete="off"
                                   class="w-full bg-transparent text-center text-[10px] font-black font-num focus:outline-none text-emerald-600 dark:text-emerald-400">
                        </div>
                        <div class="col-span-4 flex items-center justify-between px-0.5 border-r border-purple-200/40 dark:border-purple-500/20">
                            <button type="button" onclick="updateQty(${idx}, 1)" class="w-5 h-5 rounded-md bg-gradient-to-br from-purple-500 to-pink-600 text-white font-black text-[10px] flex items-center justify-center active:scale-90 transition-transform shadow-sm">+</button>
                            <input type="number" step="any" min="0.01" value="${item.qty}" 
                                   onchange="setQtyDirect(${idx}, this.value)" 
                                   autocomplete="off"
                                   class="w-7 text-center bg-transparent font-num text-purple-600 dark:text-purple-400 text-[11px] font-black focus:outline-none p-0">
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
                selector.className = 'absolute top-1 bottom-1 w-[calc(50%-4px)] bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg shadow-lg transition-all duration-300';
                box.classList.remove('hidden');
            } else {
                selector.style.transform = 'translateX(0)';
                selector.className = 'absolute top-1 bottom-1 w-[calc(50%-4px)] bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg transition-all duration-300';
                box.classList.add('hidden');
            }
        }

        function submitSale() {
            if (cart.length === 0) {
                showToast('کاڵا نییە!', 'error');
                return;
            }
            const isDebt = document.querySelector('input[name="paymentType"]:checked').value === 'debt';
            const customerId = document.getElementById('customerId').value;
            if (isDebt && !customerId) {
                showToast('کڕیار دیاری بکە بۆ قەرز', 'error');
                return;
            }

            const isEdit = !!editSale;
            const idleLabel = isEdit ?
                '<i class="fa-solid fa-floppy-disk"></i> نوێکردنەوەی پسوولە' :
                '<i class="fa-solid fa-paper-plane"></i> پسوولەکردن';

            const btn = document.getElementById('btnSubmitSale');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> چاوەڕوان بە...';
            lastSaleItems = JSON.parse(JSON.stringify(cart));

            fetch(isEdit ? '/sales/' + editSale.id : '/sales', {
                method: isEdit ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    payment_type: isDebt ? 'debt' : 'cash',
                    paid_amount: isDebt ? parseFloat(document.getElementById('paidAmount').value) || 0 : null,
                    discount: parseFloat(document.getElementById('cartDiscount').value) || 0,
                    created_at: document.getElementById('saleCreatedAt').value,
                    currency: currentCurrency,
                    exchange_rate: parseFloat(document.getElementById('exchangeRate').value) || 1500,
                    items: cart.map(i => ({
                        product_id: i.id,
                        unit_id: i.unit_id,
                        quantity: i.qty,
                        base_price: i.price_usd
                    }))
                })
            }).then(res => res.json()).then(data => {
                btn.disabled = false;
                btn.innerHTML = idleLabel;
                if (data.success) {
                    const saleId = data.sale_id || (editSale ? editSale.id : null);
                    activeSaleId = saleId;
                    document.getElementById('printA4Btn').href = '/sales/print/' + saleId + '?type=a4';
                    document.getElementById('printSmallBtn').href = '/sales/print/' + saleId + '?type=small';
                    renderModalItems();
                    document.getElementById('successModal').classList.remove('hidden');

                    // لە دۆخی دەستکاری، لەسەر هەمان وەسڵ دەمێنینەوە (سەبەتە نابێتە بەتاڵ)
                    if (!isEdit) {
                        cart = [];
                        document.getElementById('cartDiscount').value = 0;
                        renderCart(false);
                    }
                } else {
                    showToast(data.error || data.message || 'هەڵە', 'error');
                }
            }).catch(err => {
                btn.disabled = false;
                btn.innerHTML = idleLabel;
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
                row.className = 'flex items-center justify-between gap-2 bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-purple-900/10 dark:to-pink-900/10 p-3 rounded-xl border border-purple-200/50 dark:border-purple-500/20 text-xs';
                row.innerHTML = `
                    <div class="w-1/3 font-black truncate text-slate-800 dark:text-white">${item.name}</div>
                    <div class="w-1/4 flex items-center gap-1">
                        <input type="number" step="any" min="0.01" value="${item.qty}" onchange="updateModalQty(${index}, this.value)" 
                               autocomplete="off"
                               class="w-12 bg-white dark:bg-slate-900 text-center font-num font-bold border border-slate-300 dark:border-slate-600 rounded-lg p-1 text-xs">
                        <span class="text-[10px] text-slate-400 font-bold">${u ? u.name : ''}</span>
                    </div>
                    <div class="w-1/4">
                        <input type="number" step="any" min="0" value="${currentCurrency === 'USD' ? item.price_usd.toFixed(2) : item.price_usd}" 
                               onchange="updateModalPrice(${index}, this.value)" 
                               autocomplete="off"
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
            // لە دۆخی دەستکاری سەبەتە هەر ماوە؛ لە فرۆشتنی نوێ دەگەڕێتەوە
            if (!editSale && lastSaleItems && lastSaleItems.length > 0) {
                cart = JSON.parse(JSON.stringify(lastSaleItems));
                renderCart(false);
                lastSaleItems = [];
            }
            document.getElementById('successModal').classList.add('hidden');
        }

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
                    body: JSON.stringify({
                        exchange_rate: newRate
                    })
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

        function loadEditSale() {
            if (!editSale) return;

            // پێشتر نرخی ئاڵوگۆڕ و دراو، چونکە renderCart ئەوانە دەخوێنێتەوە
            document.getElementById('exchangeRate').value = editSale.exchange_rate;
            currentExchangeRate = editSale.exchange_rate;
            currentCurrency = editSale.currency || 'USD';

            document.getElementById('customerId').value = editSale.customer_id || '';
            document.getElementById('saleCreatedAt').value = editSale.created_at;
            document.getElementById('cartDiscount').value = editSale.discount || 0;

            const radio = document.querySelector('input[name="paymentType"][value="' + editSale.payment_type + '"]');
            if (radio) radio.checked = true;
            document.getElementById('paidAmount').value = editSale.paid_amount || 0;
            togglePaymentType();

            cart = editSale.items.map(it => {
                const p = allProducts.find(x => x.id == it.product_id);
                if (!p) return null;
                const unit = units.find(u => u.id == it.unit_id) || units[0];
                const factor = getUnitFactor(p, unit);
                const stockNow = parseFloat(p.stock_kg !== undefined ? p.stock_kg : (p.stock || 0));
                return {
                    id: p.id,
                    name: p.name,
                    code: p.code,
                    price_usd: parseFloat(it.price_usd) || 0,
                    stock_kg: stockNow + it.quantity * factor, // کۆگای ئێستا + بڕی ئەم وەسڵە (دەگەڕێتەوە)
                    kg_per_carton: parseFloat(p.kg_per_carton) || 1,
                    qty: parseFloat(it.quantity),
                    unit_id: unit.id,
                    factor: factor
                };
            }).filter(Boolean);

            // دوگمەکانی دراو
            setCurrency(currentCurrency);

            document.getElementById('editBanner').classList.remove('hidden');
            document.getElementById('editInvoiceNo').innerText = editSale.invoice_no;
            document.getElementById('btnSubmitSale').innerHTML =
                '<i class="fa-solid fa-floppy-disk"></i> نوێکردنەوەی پسوولە';
            renderCart(false);
        }
        loadEditSale();
    </script>
</body>

</html>