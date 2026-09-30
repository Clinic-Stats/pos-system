<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبۆردی سەرەکیی فرۆشتن و دارایی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; }
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }
        .soft-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px -2px rgba(15, 23, 42, 0.03);
        }
        .btn-press {
            transition: all 0.15s ease;
        }
        .btn-press:active {
            transform: scale(0.96);
        }
    </style>
</head>
<body class="bg-[#f1f5f9] text-slate-800 min-h-screen p-3 md:p-5 select-none">

    <div class="max-w-[1440px] mx-auto space-y-4">

        <!-- سەرپەڕە -->
        <header class="bg-white p-3.5 md:px-5 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('pos.index') }}" class="btn-press bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm shadow-blue-500/20">
                <i class="fa-solid fa-arrow-right text-xs"></i>
                <span>گەڕانەوە بۆ POS</span>
            </a>

            <div class="flex items-center gap-1.5 text-xs font-bold">
                <a href="{{ route('products.index') }}" class="btn-press bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">کۆگا</a>
                <a href="{{ route('customers.index') }}" class="btn-press bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl transition">کڕیاران</a>
                <a href="{{ route('expenses.index') }}" class="btn-press bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-3 py-1.5 rounded-xl transition">خەرجی</a>
                <a href="{{ route('backup.database') }}" class="btn-press bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                    <i class="fa-solid fa-database text-[10px]"></i> باکئەپ
                </a>
            </div>

            <div class="flex items-center gap-2">
                <div class="text-left">
                    <h1 class="text-xs font-extrabold text-slate-900">داشبۆردی فرۆشتن و دارایی</h1>
                    <span class="text-[10px] text-slate-400 block">ئاماری گشتی کۆگا و قازانج</span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
            </div>
        </header>

        <!-- فلتەری کات -->
        <div class="bg-white p-2.5 md:px-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-2.5 text-xs">
            <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 font-num text-[11px] focus:outline-none">
                <span class="text-slate-400 font-bold">بۆ</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="p-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 font-num text-[11px] focus:outline-none">
                <button type="submit" class="btn-press bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold flex items-center gap-1 shadow-sm">
                    <i class="fa-solid fa-filter text-[10px]"></i> فلتەر
                </button>
            </form>

            <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl font-bold text-[11px]">
                <a href="{{ route('reports.index', ['period' => 'today']) }}" class="px-2.5 py-1 rounded-lg {{ request('period') === 'today' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} transition">ئەمڕۆ</a>
                <a href="{{ route('reports.index', ['period' => 'yesterday']) }}" class="px-2.5 py-1 rounded-lg {{ request('period') === 'yesterday' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} transition">دوێنێ</a>
                <a href="{{ route('reports.index', ['period' => 'week']) }}" class="px-2.5 py-1 rounded-lg {{ request('period') === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} transition">ئەم هەفتەیە</a>
                <a href="{{ route('reports.index', ['period' => 'month']) }}" class="px-2.5 py-1 rounded-lg {{ request('period') === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} transition">ئەم مانگە</a>
                <a href="{{ route('reports.index') }}" class="px-2.5 py-1 rounded-lg {{ !request()->has('period') && !request()->has('from_date') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} transition">هەمووی</a>
            </div>
        </div>

        <!-- کارتەکانی بەشی ١: دارایی و فرۆشتنی ماوەکە -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            
            <!-- ١. کاشی بەردەست (بە وردەکاریی سەرچاوەکان) -->
            <div class="bg-[#0f172a] text-white p-3.5 rounded-2xl shadow-sm flex flex-col justify-between border border-slate-800">
                <div>
                    <div class="flex justify-between items-center text-slate-400 text-[11px]">
                        <span class="font-bold text-white">کاشی بەردەست (ناو دەخڵ)</span>
                        <i class="fa-solid fa-coins text-emerald-400 text-xs"></i>
                    </div>
                    <div class="mt-2 text-left">
                        <span class="text-xl font-black font-num text-emerald-400" dir="ltr">{{ number_format($cashInHand ?? 0) }}</span>
                        <span class="text-[9px] text-slate-400 font-bold block">IQD</span>
                    </div>
                </div>

                <div class="mt-2 pt-2 border-t border-slate-800 space-y-1 text-[10px]">
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="font-num text-emerald-400 font-bold" dir="ltr">+{{ number_format($totalSalesCash ?? 0) }}</span>
                        <span class="text-slate-400">فرۆشتنی نەقد:</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="font-num text-cyan-400 font-bold" dir="ltr">+{{ number_format($totalDebtCollected ?? 0) }}</span>
                        <span class="text-slate-400">وەرگرتنەوەی قەرز:</span>
                    </div>
                    @if(($totalCashReturns ?? 0) > 0)
                    <div class="flex justify-between items-center text-slate-300">
                        <span class="font-num text-rose-400 font-bold" dir="ltr">-{{ number_format($totalCashReturns ?? 0) }}</span>
                        <span class="text-slate-400">گەڕاوەی نەقد:</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- ٢. کۆی فرۆشراو -->
            <div class="soft-card p-3 flex flex-col justify-between">
                <div class="flex justify-between items-center text-slate-500 text-[11px]">
                    <span class="font-bold">کۆی فرۆشراو</span>
                    <i class="fa-solid fa-bag-shopping text-blue-600 text-xs"></i>
                </div>
                <div class="mt-2 text-left">
                    <span class="text-base font-black font-num text-slate-800" dir="ltr">{{ number_format($totalSalesAll ?? 0) }}</span>
                    <span class="text-[9px] text-slate-400 block font-bold">IQD</span>
                </div>
                <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 flex justify-between mt-1">
                    <span class="text-amber-600 font-bold">قەرز: {{ number_format($totalSalesDebt ?? 0) }}</span>
                    <span class="text-emerald-600 font-bold">نەقد: {{ number_format($totalSalesCash ?? 0) }}</span>
                </div>
            </div>

            <!-- ٣. کۆی تێچووی فرۆشراو -->
            <div class="soft-card p-3 flex flex-col justify-between">
                <div class="flex justify-between items-center text-slate-500 text-[11px]">
                    <span class="font-bold">کۆی تێچووی فرۆشراو</span>
                    <i class="fa-solid fa-boxes-stacked text-slate-500 text-xs"></i>
                </div>
                <div class="mt-2 text-left">
                    <span class="text-base font-black font-num text-slate-600" dir="ltr">{{ number_format($totalCostAll ?? 0) }}</span>
                    <span class="text-[9px] text-slate-400 block font-bold">IQD</span>
                </div>
                <p class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 text-right mt-1">تێچووی کڕینی کاڵاکان</p>
            </div>

            <!-- ٤. کۆی قازانجی کاڵا -->
            <div class="soft-card p-3 flex flex-col justify-between">
                <div class="flex justify-between items-center text-slate-500 text-[11px]">
                    <span class="font-bold">قازانجی کاڵا</span>
                    <i class="fa-solid fa-chart-line text-emerald-600 text-xs"></i>
                </div>
                <div class="mt-2 text-left">
                    <span class="text-base font-black font-num text-emerald-600" dir="ltr">+{{ number_format($totalGrossProfit ?? 0) }}</span>
                    <span class="text-[9px] text-slate-400 block font-bold">IQD</span>
                </div>
                <p class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 text-right mt-1">فرۆشراو کەمکردنەوەی تێچوو</p>
            </div>

            <!-- ٥. کۆی مەسروفات -->
            <div class="soft-card p-3 flex flex-col justify-between">
                <div class="flex justify-between items-center text-slate-500 text-[11px]">
                    <span class="font-bold">کۆی مەسروفات</span>
                    <i class="fa-solid fa-wallet text-rose-600 text-xs"></i>
                </div>
                <div class="mt-2 text-left">
                    <span class="text-base font-black font-num text-rose-600" dir="ltr">-{{ number_format($totalExpenses ?? 0) }}</span>
                    <span class="text-[9px] text-slate-400 block font-bold">IQD</span>
                </div>
                <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 flex justify-between items-center mt-1">
                    <a href="{{ route('expenses.index') }}" class="text-rose-500 hover:underline font-bold">بینین</a>
                    <span>خەرجیی ئەم ماوەیە</span>
                </div>
            </div>

            <!-- ٦. پوختەی قازانج (صافی) -->
            <div class="soft-card p-3 border {{ ($realNetProfit ?? 0) >= 0 ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} flex flex-col justify-between">
                <div class="flex justify-between items-center text-[11px] {{ ($realNetProfit ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    <span class="font-bold">پوختەی قازانج (صافی)</span>
                    <i class="fa-solid fa-scale-balanced text-xs"></i>
                </div>
                <div class="mt-2 text-left">
                    <span class="text-base font-black font-num {{ ($realNetProfit ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                        {{ (($realNetProfit ?? 0) >= 0 ? '+' : '') . number_format($realNetProfit ?? 0) }}
                    </span>
                    <span class="text-[9px] text-slate-400 block font-bold">IQD</span>
                </div>
                <p class="text-[10px] text-slate-400 pt-1 border-t border-slate-100 text-right mt-1">قازانج - مەسروفات</p>
            </div>

        </div>

        <!-- کارتەکانی بەشی ٢: سەرمایە و کاڵای ماوە لە مەخزەن -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            
            <!-- کاڵای ماوە بە تێچووی کڕین (بێ قازانج) -->
            <div class="soft-card p-3.5 bg-gradient-to-br from-white to-slate-50 flex items-center justify-between border-slate-200">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 block">کاڵای ماوە (بێ قازانج - تێچوو)</span>
                    <div class="text-base font-black font-num text-slate-800 mt-1" dir="ltr">{{ number_format($stockCostWithoutProfit ?? 0) }} IQD</div>
                    <span class="text-[10px] text-slate-400">سەرمایەی ڕاستەقینەی ناو کۆگا</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
            </div>

            <!-- کاڵای ماوە بە نرخی فرۆشتن (بە قازانج) -->
            <div class="soft-card p-3.5 bg-gradient-to-br from-white to-indigo-50/40 flex items-center justify-between border-indigo-100">
                <div>
                    <span class="text-[11px] font-bold text-indigo-700 block">کاڵای ماوە (بە قازانج - فرۆشتن)</span>
                    <div class="text-base font-black font-num text-indigo-600 mt-1" dir="ltr">{{ number_format($stockValueWithProfit ?? 0) }} IQD</div>
                    <span class="text-[10px] text-slate-400">بەهای فرۆشتنی تەواوی مەخزەن</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>

            <!-- قازانجی پێشبینیکراوی مەخزەن -->
            <div class="soft-card p-3.5 bg-gradient-to-br from-white to-emerald-50/40 flex items-center justify-between border-emerald-100">
                <div>
                    <span class="text-[11px] font-bold text-emerald-700 block">قازانجی چاوەڕوانکراوی مەخزەن</span>
                    <div class="text-base font-black font-num text-emerald-600 mt-1" dir="ltr">+{{ number_format($expectedStockProfit ?? 0) }} IQD</div>
                    <span class="text-[10px] text-slate-400">قازانج لە کاتی فرۆشتنی هەمووی</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>

            <!-- قەرزی سەر کڕیاران -->
            <div class="soft-card p-3.5 bg-gradient-to-br from-white to-amber-50/40 flex items-center justify-between border-amber-100">
                <div>
                    <span class="text-[11px] font-bold text-amber-700 block">کۆی قەرزی سەر کڕیاران</span>
                    <div class="text-base font-black font-num text-amber-600 mt-1" dir="ltr">{{ number_format($totalCustomerDebts ?? 0) }} IQD</div>
                    <span class="text-[10px] text-slate-400">باڵانسی ماوە لای کڕیارەکان</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>

        </div>

        <!-- بەشی چارتەکان: پڕفرۆشترین ٥ کاڵا و ٥ کڕیاری سەرەکی -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <div class="soft-card p-4 space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400">بەپێی کۆی بڕی فرۆشراو</span>
                    <h3 class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-crown text-amber-500"></i> ٥ پڕفرۆشترین کاڵاکان
                    </h3>
                </div>
                <div class="h-56 w-full">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            <div class="soft-card p-4 space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400">بەپێی کۆی کڕین (IQD)</span>
                    <h3 class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-check text-blue-600"></i> ٥ زۆرترین کڕیارەکان
                    </h3>
                </div>
                <div class="h-56 w-full">
                    <canvas id="topCustomersChart"></canvas>
                </div>
            </div>

        </div>

        <!-- بەشی دوو چارتی دارایی و نەقد/قەرز -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <div class="lg:col-span-2 soft-card p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-slate-400">بەراوردی داهات و خەرجی</span>
                    <h3 class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-column text-blue-600"></i> ئەنجامی دارایی
                    </h3>
                </div>
                <div class="h-52 w-full">
                    <canvas id="financialBarChart"></canvas>
                </div>
            </div>

            <div class="soft-card p-4 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-slate-400">نەقد بەرامبەر قەرز</span>
                    <h3 class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-pie text-indigo-600"></i> شێوازی فرۆشتن
                    </h3>
                </div>
                <div class="h-40 relative flex items-center justify-center my-auto">
                    <canvas id="paymentDonutChart"></canvas>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-center font-bold text-slate-500">
                    دابەشبوونی فرۆشتن لەم ماوەیەدا
                </div>
            </div>

        </div>

        <!-- خشتەی دوایین وەسڵەکان -->
        <div class="soft-card overflow-hidden">
            <div class="p-3.5 border-b border-slate-100 flex justify-between items-center bg-slate-50/60">
                <span class="text-[11px] font-bold text-slate-500">کۆی تۆمارەکان: <b class="font-num text-slate-800">{{ $paginatedSales->total() }}</b></span>
                <h3 class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-blue-600"></i> دوایین وەسڵەکانی فرۆشتن
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-700">
                    <thead class="bg-slate-100/70 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-2.5">وەسڵ</th>
                            <th class="p-2.5">بەروار</th>
                            <th class="p-2.5">کڕیار</th>
                            <th class="p-2.5">جۆر</th>
                            <th class="p-2.5">کۆی وەسڵ</th>
                            <th class="p-2.5">تێچوو</th>
                            <th class="p-2.5">قازانج</th>
                            <th class="p-2.5">کردار</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($paginatedSales as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-2.5 font-num font-bold text-blue-600">{{ $s->invoice_no }}</td>
                            <td class="p-2.5 font-num text-slate-500">{{ $s->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-2.5 font-bold text-slate-800">{{ $s->customer->name ?? 'کڕیاری گشتی' }}</td>
                            <td class="p-2.5">
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $s->payment_type === 'cash' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                    {{ $s->payment_type === 'cash' ? 'نەقد' : 'قەرز' }}
                                </span>
                            </td>
                            <td class="p-2.5 font-num font-bold text-slate-800" dir="ltr">{{ number_format($s->total_amount) }} IQD</td>
                            <td class="p-2.5 font-num text-slate-400" dir="ltr">{{ number_format($s->total_cost) }} IQD</td>
                            <td class="p-2.5 font-num font-bold {{ $s->total_profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                                {{ ($s->total_profit >= 0 ? '+' : '') . number_format($s->total_profit) }} IQD
                            </td>
                            <td class="p-2.5">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('sales.print', $s->id) }}" target="_blank" title="پسوولە" class="btn-press bg-slate-100 hover:bg-slate-200 text-slate-600 p-1.5 rounded-lg text-xs">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('sales.print', $s->id) }}?type=a4" target="_blank" title="A4" class="btn-press bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white px-2 py-1 rounded-lg text-[10px] font-bold border border-blue-200">
                                        A4
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400 font-bold">هیچ وەسڵێک نەدۆزرایەوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($paginatedSales->hasPages())
            <div class="p-2.5 border-t border-slate-100 bg-slate-50/50">
                {{ $paginatedSales->links() }}
            </div>
            @endif
        </div>

    </div>

    <!-- چارتەکانی Chart.js -->
    <script>
        const topProdLabels = {!! json_encode($topProducts->pluck('name') ?? []) !!};
        const topProdData = {!! json_encode($topProducts->pluck('total_qty') ?? []) !!};

        new Chart(document.getElementById('topProductsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: topProdLabels.length ? topProdLabels : ['نییە'],
                datasets: [{
                    label: 'بڕی فرۆشراو',
                    data: topProdData.length ? topProdData : [0],
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans' } } },
                    y: { grid: { display: false }, ticks: { font: { family: 'Almarai', size: 10, weight: 'bold' } } }
                }
            }
        });

        const topCustLabels = {!! json_encode($topCustomers->pluck('name') ?? []) !!};
        const topCustData = {!! json_encode($topCustomers->pluck('total_spent') ?? []) !!};

        new Chart(document.getElementById('topCustomersChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: topCustLabels.length ? topCustLabels : ['نییە'],
                datasets: [{
                    label: 'کۆی کڕین',
                    data: topCustData.length ? topCustData : [0],
                    backgroundColor: '#4f46e5',
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans' } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Almarai', size: 10, weight: 'bold' } } }
                }
            }
        });

        new Chart(document.getElementById('financialBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['کۆی فرۆشراو', 'تێچووی کاڵا', 'قازانجی فرۆشتن', 'مەسروفات', 'پوختەی قازانج'],
                datasets: [{
                    data: [
                        {{ (float)($totalSalesAll ?? 0) }},
                        {{ (float)($totalCostAll ?? 0) }},
                        {{ (float)($totalGrossProfit ?? 0) }},
                        {{ (float)($totalExpenses ?? 0) }},
                        {{ (float)($realNetProfit ?? 0) }}
                    ],
                    backgroundColor: ['#2563eb', '#64748b', '#059669', '#e11d48', '#0d9488'],
                    borderRadius: 6,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans' } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Almarai', size: 10 } } }
                }
            }
        });

        new Chart(document.getElementById('paymentDonutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['فرۆشتنی نەقد', 'فرۆشتنی قەرز'],
                datasets: [{
                    data: [
                        {{ (float)($totalSalesCash ?? 0) }},
                        {{ (float)($totalSalesDebt ?? 0) }}
                    ],
                    backgroundColor: ['#059669', '#d97706'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Almarai', size: 9 } } }
                }
            }
        });
    </script>
</body>
</html>