<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>بەڕێوەبردنی کاڵاکان و کۆگا</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; } 
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- سەرپەڕە -->
        <div class="flex flex-wrap justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700 gap-3">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-boxes-stacked text-amber-500"></i>
                بەڕێوەبردنی کاڵاکان و کۆگا
            </h1>
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <a href="{{ route('categories.index', [], false) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-2 rounded-xl transition">کاتیگۆری</a>
                <a href="{{ route('reports.index', [], false) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-2 rounded-xl transition">ڕاپۆرتەکان</a>
                <a href="{{ route('pos.index', [], false) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition shadow">POS</a>
                
                <!-- هەناردەکردن بۆ ئێکسیڵ -->
                <a href="{{ route('export.products', [], false) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-xl transition flex items-center gap-1.5 shadow">
                    <i class="fa-solid fa-file-excel"></i> هەناردە
                </a>

                <!-- دوگمەی نوێ: هاوردەکردنی ئێکسیڵ / CSV -->
                <button type="button" onclick="openImportModal()" class="bg-teal-600 hover:bg-teal-700 text-white px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shadow">
                    <i class="fa-solid fa-file-import"></i> هاوردەکردنی ئێکسیڵ
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3.5 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif

        @php
            $outOfStockCount = $products->where('stock_kg', '<=', 0)->count();
            $lowStockCount = $products->filter(function($p) {
                return $p->stock_kg > 0 && $p->stock_kg <= ($p->alert_quantity ?? 5);
            })->count();
            $availableCount = $products->where('stock_kg', '>', 5)->count();
        @endphp

        <!-- کارتەکانی هۆشداری و بارودۆخی کۆگا -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-800 p-4 rounded-2xl border border-rose-500/40 bg-rose-950/20 flex justify-between items-center cursor-pointer hover:border-rose-400 transition" onclick="filterByStockState('out')">
                <div>
                    <span class="text-xs text-rose-300 font-bold block">کاڵای نەماو (سفر)</span>
                    <span class="text-2xl font-black font-num text-rose-400">{{ $outOfStockCount }} کاڵا</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>

            <div class="bg-slate-800 p-4 rounded-2xl border border-amber-500/40 bg-amber-950/20 flex justify-between items-center cursor-pointer hover:border-amber-400 transition" onclick="filterByStockState('low')">
                <div>
                    <span class="text-xs text-amber-300 font-bold block">کاڵای کەمبووەوە (داواکردنەوە)</span>
                    <span class="text-2xl font-black font-num text-amber-400">{{ $lowStockCount }} کاڵا</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>

            <div class="bg-slate-800 p-4 rounded-2xl border border-emerald-500/40 bg-emerald-950/20 flex justify-between items-center cursor-pointer hover:border-emerald-400 transition" onclick="filterByStockState('all')">
                <div>
                    <span class="text-xs text-emerald-300 font-bold block">کۆی گشتی بەردەست</span>
                    <span class="text-2xl font-black font-num text-emerald-400">{{ $products->count() }} کاڵا</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی زیادکردنی کاڵا -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-square-plus text-blue-400"></i> زیادکردنی کاڵای نوێ
                </h2>
                <form action="https://pos-system-neon-iota.vercel.app/products" method="POST" id="productForm" class="space-y-3.5 text-sm">
                    @csrf
                    
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ناوی کاڵا:</label>
                        <input type="text" name="name" id="field_name" required autofocus
                               class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">کۆد یان بارکۆد:</label>
                        <input type="text" name="code" id="field_code" required
                               class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-slate-300 text-xs font-bold">کاتیگۆری:</label>
                            <button type="button" onclick="openQuickCategoryModal()" class="text-[11px] text-blue-400 hover:text-blue-300 flex items-center gap-1 font-bold">
                                <i class="fa-solid fa-plus-circle"></i> نوێ
                            </button>
                        </div>
                        <select name="category_id" id="field_category" required
                                class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-slate-300 text-xs font-bold mb-1">نرخی کڕین (١ کگ):</label>
                            <input type="number" step="any" min="0" name="base_buy_price" id="field_buy_price" required
                                   class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-300 text-xs font-bold mb-1">نرخی فرۆشتن (١ کگ):</label>
                            <input type="number" step="any" min="0" name="base_sale_price" id="field_sale_price" required
                                   class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">بڕی سەرەتایی بە کیلۆ (Stock):</label>
                        <input type="number" step="any" min="0" name="stock_kg" id="field_stock" value="0" required
                               class="enter-nav w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none">
                    </div>

                    <div class="pt-1 flex items-center justify-between bg-slate-700/40 p-2.5 rounded-xl border border-slate-600">
                        <span class="text-xs font-bold text-slate-300">دۆخی کاڵا:</span>
                        <label class="flex items-center gap-2 cursor-pointer text-xs">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-emerald-500 focus:ring-0">
                            <span class="text-emerald-400 font-bold">چالاک (لە POS دەردەکەوێت)</span>
                        </label>
                    </div>

                    <button type="submit" id="btnSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg">
                        <i class="fa-solid fa-check"></i> تۆمارکردنی کاڵا
                    </button>
                </form>
            </div>

            <!-- خشتەی کاڵاکان لە عەمبار -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 flex flex-col">
                
                <div class="flex flex-wrap justify-between items-center gap-3 border-b border-slate-700 pb-3">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-warehouse text-blue-400"></i> لیستی مەخزەنی کاڵاکان
                    </h2>
                    <span id="productCountBadge" class="text-xs font-mono font-bold bg-slate-700 px-2.5 py-1 rounded-lg text-slate-300">
                        کۆی کاڵاکان: {{ $products->count() }}
                    </span>
                </div>

                <!-- بەشی گەڕان و فلتەر -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/60 p-3 rounded-xl border border-slate-700/80">
                    <div class="relative sm:col-span-1">
                        <input type="text" id="stockSearchInput" onkeyup="filterStockTable()" placeholder="گەڕان بەپێی ناو یان کۆد..."
                               class="w-full p-2 pl-8 rounded-lg bg-slate-800 border border-slate-600 text-white text-xs focus:outline-none focus:border-blue-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-3 text-slate-400 text-xs"></i>
                    </div>

                    <div>
                        <select id="stockCategoryFilter" onchange="filterStockTable()" class="w-full p-2 rounded-lg bg-slate-800 border border-slate-600 text-white text-xs focus:outline-none focus:border-blue-500">
                            <option value="all">هەموو کاتیگۆرییەکان</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select id="stockStatusFilter" onchange="filterStockTable()" class="w-full p-2 rounded-lg bg-slate-800 border border-slate-600 text-white text-xs focus:outline-none focus:border-blue-500">
                            <option value="all">هەموو ئاستەکانی کۆگا</option>
                            <option value="low">تەنها کەمبووەکان (≤ 5 کگ)</option>
                            <option value="out">تەنها نەماوەکان (0 کگ)</option>
                            <option value="active">تەنها چالاکەکان</option>
                            <option value="inactive">تەنها ناچالاکەکان</option>
                        </select>
                    </div>
                </div>

                <!-- خشتەی کاڵاکان -->
                <div class="overflow-x-auto rounded-xl border border-slate-700/80">
                    <table class="w-full text-sm text-right text-slate-300">
                        <thead class="bg-slate-700/50 text-xs text-slate-400">
                            <tr>
                                <th class="p-3">کۆد / ناو</th>
                                <th class="p-3">کاتیگۆری</th>
                                <th class="p-3">نرخی فرۆشتن</th>
                                <th class="p-3">مەخزەن (کیلۆ)</th>
                                <th class="p-3 text-center">دۆخ</th>
                                <th class="p-3 text-center">کردار</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700" id="stockTableBody">
                            @forelse($products as $p)
                            @php
                                $stock = (float) $p->stock_kg;
                                $isOut = $stock <= 0;
                                $isLow = !$isOut && $stock <= ($p->alert_quantity ?? 5);
                            @endphp
                            <tr class="product-row hover:bg-slate-700/30 transition {{ $isOut ? 'bg-rose-950/20' : ($isLow ? 'bg-amber-950/20' : '') }} {{ !$p->is_active ? 'opacity-60' : '' }}"
                                data-name="{{ mb_strtolower($p->name) }}"
                                data-code="{{ mb_strtolower($p->code) }}"
                                data-category="{{ $p->category_id }}"
                                data-stock="{{ $stock }}"
                                data-active="{{ $p->is_active ? '1' : '0' }}">
                                
                                <td class="p-3">
                                    <div class="font-bold text-white flex items-center gap-1.5">
                                        {{ $p->name }}
                                        @if($isOut)
                                            <span class="text-[9px] font-black bg-rose-600 text-white px-1.5 py-0.2 rounded">نەماوە</span>
                                        @elseif($isLow)
                                            <span class="text-[9px] font-black bg-amber-600 text-white px-1.5 py-0.2 rounded">کەم ماوە</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] font-mono text-blue-400">{{ $p->code }}</span>
                                </td>
                                <td class="p-3 text-xs text-slate-400">{{ $p->category->name ?? '-' }}</td>
                                <td class="p-3 font-mono font-bold text-emerald-400 text-xs" dir="ltr">{{ number_format($p->base_sale_price) }} IQD</td>
                                <td class="p-3 font-mono font-bold text-sm {{ $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-400' : 'text-slate-200') }}">
                                    {{ $p->stock_kg }} کگ
                                </td>
                                <td class="p-3 text-center">
                                    <form action="{{ route('products.toggle', $p->id, false) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="کلیک بکە بۆ گۆڕینی دۆخ"
                                                class="px-2 py-0.5 rounded text-[11px] font-bold transition {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' : 'bg-slate-600 text-slate-400 hover:bg-slate-500' }}">
                                            {{ $p->is_active ? 'چالاک' : 'ناچالاک' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick="openEditProductModal({{ json_encode($p) }})" 
                                                class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                            <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                        </button>

                                        @if($p->stock_kg > 0)
                                            <span class="text-xs bg-slate-700/50 text-slate-500 px-2 py-1 rounded cursor-not-allowed border border-slate-700" title="لەبەر ئەوەی ستۆکی تێدایە ناتوانیت بیسڕیتەوە">
                                                <i class="fa-solid fa-lock text-[10px] ml-1"></i> سڕینەوە قفڵە
                                            </span>
                                        @else
                                            <form action="{{ route('products.destroy', $p->id, false) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم کاڵایە؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                                    <i class="fa-solid fa-trash"></i> سڕینەوە
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow"><td colspan="6" class="p-6 text-center text-slate-500">هیچ کاڵایەک تۆمار نەکراوە</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>

    <!-- مۆداڵی نوێ: هاوردەکردنی ئێکسیڵ / CSV -->
    <div id="importProductModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl text-xs">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-csv text-teal-400"></i> هاوردەکردنی کاڵاکان لە ئێکسیڵەوە
                </h3>
                <button onclick="closeImportModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>

            <div class="bg-slate-900/80 p-3 rounded-xl border border-slate-700 space-y-2 text-slate-300">
                <span class="font-bold text-teal-400 block text-xs">ڕێنمایی ستوونەکانی ئێکسیڵ:</span>
                <p class="text-[11px] leading-relaxed">
                    فایلەکەت وەک <b>CSV UTF-8</b> سەیڤ بکە بەم ڕیزبەندییە:
                </p>
                <div class="bg-slate-950 p-2 rounded-lg font-mono text-[10px] text-amber-300 text-left" dir="ltr">
                    code, name, base_buy_price, base_sale_price, stock_kg
                </div>
            </div>

            <form action="{{ route('products.importCsv', [], false) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-1.5">فایلی ئێکسیڵ (CSV) دیاریبکە:</label>
                    <input type="file" name="csv_file" accept=".csv, .txt" required 
                           class="w-full text-xs text-slate-400 file:ml-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer bg-slate-900 p-1.5 rounded-xl border border-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1.5">کاتیگۆری بۆ کاڵا هاوردەکراوەکان:</label>
                    <select name="category_id" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" onclick="closeImportModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-xl">داخستن</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-5 py-2 rounded-xl shadow">دەستپێکردنی هاوردە</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مۆداڵی دەستکاریکردنی کاڵا -->
    <div id="editProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl text-xs">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-box-open text-amber-400"></i> دەستکاریکردنی کاڵا
                </h3>
                <button onclick="closeEditProductModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>

            <form id="editProductForm" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوی کاڵا:</label>
                    <input type="text" name="name" id="edit_name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">کۆد یان بارکۆد:</label>
                    <input type="text" name="code" id="edit_code" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">کاتیگۆری:</label>
                    <select name="category_id" id="edit_category_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">نرخی کڕین (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_buy_price" id="edit_buy_price" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1">نرخی فرۆشتن (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_sale_price" id="edit_sale_price" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 bg-slate-900 p-2.5 rounded-xl cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-blue-600">
                        <span class="text-slate-300 font-bold">چالاک بێت لە شاشەی POS</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditProductModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-xl">داخستن</button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2 rounded-xl">نوێکردنەوە</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مۆداڵی زیادکردنی خێرای کاتیگۆری -->
    <div id="quickCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 p-5 rounded-2xl w-full max-w-sm space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-tags text-blue-400"></i> زیادکردنی کاتیگۆری نوێ
            </h3>
            <form action="{{ route('categories.store', [], false) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-slate-300 mb-1">ناوی کاتیگۆری:</label>
                    <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-xs">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeQuickCategoryModal()" class="px-3 py-1.5 bg-slate-700 text-slate-300 rounded-xl text-xs">پاشگەزبوونەوە</button>
                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs">تۆمارکردن</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // مۆداڵی هاوردەکردنی CSV
        function openImportModal() {
            document.getElementById('importProductModal').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('importProductModal').classList.add('hidden');
        }

        // ١. جووڵاندنی فۆڕم لە خانەیەک بۆ خانەی دواتر بە Enter
        const inputs = Array.from(document.querySelectorAll('.enter-nav'));
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                        if (typeof inputs[index + 1].select === 'function') {
                            inputs[index + 1].select();
                        }
                    } else {
                        document.getElementById('productForm').submit();
                    }
                }
            });
        });

        // ٢. فلتەری هەمەلایەنە لە خشتەی کۆگا
        function filterStockTable() {
            const searchText = document.getElementById('stockSearchInput').value.toLowerCase().trim();
            const selectedCat = document.getElementById('stockCategoryFilter').value;
            const selectedStatus = document.getElementById('stockStatusFilter').value;

            const rows = document.querySelectorAll('.product-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const code = row.getAttribute('data-code');
                const cat = row.getAttribute('data-category');
                const stock = parseFloat(row.getAttribute('data-stock')) || 0;
                const active = row.getAttribute('data-active');

                const matchesSearch = !searchText || name.includes(searchText) || code.includes(searchText);
                const matchesCat = (selectedCat === 'all' || cat == selectedCat);

                let matchesStatus = true;
                if (selectedStatus === 'out') {
                    matchesStatus = (stock <= 0);
                } else if (selectedStatus === 'low') {
                    matchesStatus = (stock > 0 && stock <= 5);
                } else if (selectedStatus === 'active') {
                    matchesStatus = (active === '1');
                } else if (selectedStatus === 'inactive') {
                    matchesStatus = (active === '0');
                }

                if (matchesSearch && matchesCat && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const badge = document.getElementById('productCountBadge');
            if (badge) {
                badge.innerText = `کاڵای دیاریکراو: ${visibleCount}`;
            }
        }

        function filterByStockState(state) {
            const statusSelect = document.getElementById('stockStatusFilter');
            if (statusSelect) {
                statusSelect.value = state;
                filterStockTable();
            }
        }

        // مۆداڵی کاتیگۆری
        function openQuickCategoryModal() {
            document.getElementById('quickCategoryModal').classList.remove('hidden');
        }
        function closeQuickCategoryModal() {
            document.getElementById('quickCategoryModal').classList.add('hidden');
        }

        // مۆداڵی دەستکاری
        function openEditProductModal(item) {
            document.getElementById('edit_name').value = item.name || '';
            document.getElementById('edit_code').value = item.code || '';
            document.getElementById('edit_category_id').value = item.category_id || '';
            document.getElementById('edit_buy_price').value = item.base_buy_price || 0;
            document.getElementById('edit_sale_price').value = item.base_sale_price || 0;
            document.getElementById('edit_is_active').checked = (item.is_active == 1);

            document.getElementById('editProductForm').action = '/products/' + item.id;
            document.getElementById('editProductModal').classList.remove('hidden');
        }

        function closeEditProductModal() {
            document.getElementById('editProductModal').classList.add('hidden');
        }
    </script>
</body>
</html>