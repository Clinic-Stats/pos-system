<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی کاڵاکان و کۆگا</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; } 
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 h-screen flex flex-col overflow-hidden p-3 gap-3">

    <!-- سەرپەڕە -->
    <div class="shrink-0 flex flex-wrap justify-between items-center bg-slate-800 p-3 rounded-xl border border-slate-700 gap-2 text-xs">
        <h1 class="text-sm font-bold flex items-center gap-2 text-white">
            <i class="fa-solid fa-boxes-stacked text-amber-500"></i>
            بەڕێوەبردنی کاڵاکان و کۆگا
        </h1>
        <div class="flex flex-wrap items-center gap-1.5 font-bold">
            <a href="{{ route('categories.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-2.5 py-1.5 rounded-lg transition">کاتیگۆری</a>
            <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-2.5 py-1.5 rounded-lg transition">ڕاپۆرتەکان</a>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition shadow">POS</a>
            <a href="{{ route('export.products') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shadow"><i class="fa-solid fa-file-excel"></i> هەناردە</a>
            <button type="button" onclick="openImportModal()" class="bg-teal-600 hover:bg-teal-700 text-white px-2.5 py-1.5 rounded-lg transition flex items-center gap-1 shadow"><i class="fa-solid fa-file-import"></i> هاوردەکردن</button>
        </div>
    </div>

    <!-- نامەکانی سیستەم -->
    <div class="shrink-0 space-y-2">
        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-2 rounded-lg text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-2 rounded-lg text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-2 rounded-lg text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif
    </div>

    @php
        $outOfStockCount = $products->where('stock_kg', '<=', 0)->count();
        $lowStockCount = $products->filter(function($p) { return $p->stock_kg > 0 && $p->stock_kg <= ($p->alert_quantity ?? 5); })->count();
    @endphp

    <!-- کارتەکانی هۆشداری -->
    <div class="shrink-0 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
        <div class="bg-slate-800 p-2.5 rounded-xl border border-rose-500/40 bg-rose-950/20 flex justify-between items-center cursor-pointer hover:border-rose-400 transition" onclick="filterByStockState('out')">
            <div>
                <span class="text-[10px] text-rose-300 font-bold block">کاڵای نەماو (سفر)</span>
                <span class="text-lg font-black font-num text-rose-400">{{ $outOfStockCount }} کاڵا</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center"><i class="fa-solid fa-circle-xmark"></i></div>
        </div>
        <div class="bg-slate-800 p-2.5 rounded-xl border border-amber-500/40 bg-amber-950/20 flex justify-between items-center cursor-pointer hover:border-amber-400 transition" onclick="filterByStockState('low')">
            <div>
                <span class="text-[10px] text-amber-300 font-bold block">کاڵای کەمبووەوە</span>
                <span class="text-lg font-black font-num text-amber-400">{{ $lowStockCount }} کاڵا</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="bg-slate-800 p-2.5 rounded-xl border border-emerald-500/40 bg-emerald-950/20 flex justify-between items-center cursor-pointer hover:border-emerald-400 transition" onclick="filterByStockState('all')">
            <div>
                <span class="text-[10px] text-emerald-300 font-bold block">کۆی گشتی بەردەست</span>
                <span class="text-lg font-black font-num text-emerald-400">{{ $products->count() }} کاڵا</span>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center"><i class="fa-solid fa-boxes-packing"></i></div>
        </div>
    </div>

    <!-- بەشی سەرەکی -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-3 overflow-hidden min-h-0">
        
        <!-- فۆڕمی زیادکردنی کاڵا -->
        <div class="bg-slate-800 p-3 rounded-xl border border-slate-700 flex flex-col h-full overflow-hidden">
            <h2 class="shrink-0 text-sm font-bold text-white flex items-center gap-2 mb-2 border-b border-slate-700 pb-2">
                <i class="fa-solid fa-square-plus text-blue-400"></i> زیادکردنی کاڵای نوێ
            </h2>
            
            <form action="{{ route('products.store') }}" method="POST" id="productForm" class="flex-1 overflow-y-auto custom-scrollbar pr-1 space-y-2.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 text-[11px] font-bold mb-1">ناوی کاڵا:</label>
                    <input type="text" name="name" id="field_name" required autofocus class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none text-xs">
                </div>
                <div>
                    <label class="block text-slate-300 text-[11px] font-bold mb-1">کۆد یان بارکۆد:</label>
                    <input type="text" name="code" id="field_code" required class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none text-xs">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-slate-300 text-[11px] font-bold">کاتیگۆری:</label>
                        <button type="button" onclick="openQuickCategoryModal()" class="text-[10px] text-blue-400 hover:text-blue-300 flex items-center gap-1 font-bold"><i class="fa-solid fa-plus-circle"></i> نوێ</button>
                    </div>
                    <select name="category_id" id="field_category" required class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none text-xs">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-300 text-[11px] font-bold mb-1">نرخی کڕین (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_buy_price" id="field_buy_price" required class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-[11px] font-bold mb-1">نرخی فرۆشتن (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_sale_price" id="field_sale_price" required class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none text-xs">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-300 text-[11px] font-bold mb-1">بڕی سەرەتایی بە کیلۆ:</label>
                    <input type="number" step="any" min="0" name="stock_kg" id="field_stock" value="0" required class="enter-nav w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white font-mono focus:border-blue-500 focus:outline-none text-xs">
                </div>
                <div class="pt-1 flex items-center justify-between bg-slate-700/40 p-2 rounded-lg border border-slate-600">
                    <span class="text-[11px] font-bold text-slate-300">دۆخی کاڵا:</span>
                    <label class="flex items-center gap-1.5 cursor-pointer text-[11px]">
                        <input type="checkbox" name="is_active" id="field_is_active" value="1" checked class="w-3.5 h-3.5 rounded text-emerald-500 focus:ring-0">
                        <span class="text-emerald-400 font-bold">چالاک بێت</span>
                    </label>
                </div>
                <button type="submit" id="btnSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition text-xs flex items-center justify-center gap-2 shadow-lg mt-2">
                    <i class="fa-solid fa-check"></i> تۆمارکردنی کاڵا
                </button>
            </form>
        </div>

        <!-- خشتەی کاڵاکان -->
        <div class="lg:col-span-2 bg-slate-800 p-3 rounded-xl border border-slate-700 flex flex-col h-full overflow-hidden">
            
            <div class="shrink-0 flex flex-wrap justify-between items-center gap-2 border-b border-slate-700 pb-2 mb-2">
                <h2 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-warehouse text-blue-400"></i> لیستی مەخزەنی کاڵاکان
                </h2>
                <span id="productCountBadge" class="text-[10px] font-mono font-bold bg-slate-700 px-2 py-0.5 rounded text-slate-300">
                    کۆی کاڵاکان: {{ $products->count() }}
                </span>
            </div>

            <div class="shrink-0 grid grid-cols-1 sm:grid-cols-3 gap-2 bg-slate-900/60 p-2 rounded-lg border border-slate-700/80 mb-2 text-xs">
                <div class="relative">
                    <input type="text" id="stockSearchInput" onkeyup="filterStockTable()" placeholder="گەڕان..." class="w-full p-1.5 pl-7 rounded-lg bg-slate-800 border border-slate-600 text-white text-[11px] focus:outline-none focus:border-blue-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-2 top-2.5 text-slate-400 text-[10px]"></i>
                </div>
                <div>
                    <select id="stockCategoryFilter" onchange="filterStockTable()" class="w-full p-1.5 rounded-lg bg-slate-800 border border-slate-600 text-white text-[11px] focus:outline-none focus:border-blue-500">
                        <option value="all">هەموو کاتیگۆرییەکان</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="stockStatusFilter" onchange="filterStockTable()" class="w-full p-1.5 rounded-lg bg-slate-800 border border-slate-600 text-white text-[11px] focus:outline-none focus:border-blue-500">
                        <option value="all">هەموو ئاستەکانی کۆگا</option>
                        <option value="low">کەمبووەکان (≤ 5)</option>
                        <option value="out">نەماوەکان (0)</option>
                        <option value="active">چالاکەکان</option>
                        <option value="inactive">ناچالاکەکان</option>
                    </select>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar rounded-lg border border-slate-700/80 relative">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-slate-800 text-[11px] text-slate-400 sticky top-0 z-10 shadow-sm border-b border-slate-700">
                        <tr>
                            <th class="p-2">کۆد / ناو</th>
                            <th class="p-2">کاتیگۆری</th>
                            <th class="p-2">نرخی فرۆشتن</th>
                            <th class="p-2">مەخزەن (کگ)</th>
                            <th class="p-2 text-center">دۆخ</th>
                            <th class="p-2 text-center">کردار</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60" id="stockTableBody">
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
                            
                            <td class="p-2">
                                <div class="font-bold text-white flex items-center gap-1.5 text-[11px]">
                                    {{ $p->name }}
                                    @if($isOut) <span class="text-[8px] font-black bg-rose-600 text-white px-1 rounded">نەماوە</span>
                                    @elseif($isLow) <span class="text-[8px] font-black bg-amber-600 text-white px-1 rounded">کەمە</span> @endif
                                </div>
                                <span class="text-[10px] font-mono text-blue-400">{{ $p->code }}</span>
                            </td>
                            <td class="p-2 text-[11px] text-slate-400">{{ $p->category->name ?? '-' }}</td>
                            <td class="p-2 font-mono font-bold text-emerald-400 text-[11px]" dir="ltr">{{ number_format($p->base_sale_price) }}</td>
                            <td class="p-2 font-mono font-bold text-[11px] {{ $isOut ? 'text-rose-500' : ($isLow ? 'text-amber-400' : 'text-slate-200') }}">
                                {{ $p->stock_kg }}
                            </td>
                            <td class="p-2 text-center">
                                <form action="{{ route('products.toggle', $p->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-1.5 py-0.5 rounded text-[10px] font-bold transition {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30' : 'bg-slate-600 text-slate-400 hover:bg-slate-500' }}">
                                        {{ $p->is_active ? 'چالاک' : 'ناچالاک' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-2 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button type="button" onclick="openEditProductModal({{ json_encode($p) }})" class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-2 py-0.5 rounded text-[10px] font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </button>
                                    @if($p->stock_kg > 0)
                                        <span class="text-[10px] bg-slate-700/50 text-slate-500 px-1.5 py-0.5 rounded cursor-not-allowed border border-slate-700" title="ستۆکی تێدایە"><i class="fa-solid fa-lock text-[9px]"></i></span>
                                    @else
                                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوە؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2 py-0.5 rounded text-[10px] font-bold transition flex items-center gap-1">
                                                <i class="fa-solid fa-trash"></i> سڕینەوە
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow"><td colspan="6" class="p-4 text-center text-slate-500 text-xs">هیچ کاڵایەک تۆمار نەکراوە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- مۆداڵی هاوردەکردنی CSV -->
    <div id="importProductModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs">
            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                <h3 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-file-csv text-teal-400"></i> هاوردەکردنی کاڵاکان</h3>
                <button onclick="closeImportModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>
            <div class="bg-slate-900/80 p-2.5 rounded-lg border border-slate-700 space-y-1 text-slate-300 text-[11px]">
                <span class="font-bold text-teal-400 block">ڕێنمایی ستوونەکانی CSV:</span>
                <div class="bg-slate-950 p-1.5 rounded font-mono text-[10px] text-amber-300 text-left" dir="ltr">code, name, base_buy_price, base_sale_price, stock_kg</div>
            </div>
            <form action="{{ route('products.importCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">فایلی CSV:</label>
                    <input type="file" name="csv_file" accept=".csv, .txt" required class="w-full text-[10px] text-slate-400 file:ml-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer bg-slate-900 p-1 rounded-lg border border-slate-700">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">کاتیگۆری:</label>
                    <select name="category_id" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs">
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" onclick="closeImportModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-3 py-1.5 rounded-lg text-[11px]">داخستن</button>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-4 py-1.5 rounded-lg text-[11px] shadow">دەستپێکردن</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مۆداڵی دەستکاریکردنی کاڵا -->
    <div id="editProductModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-xl w-full max-w-md p-4 space-y-3 shadow-2xl text-xs">
            <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                <h3 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-box-open text-amber-400"></i> دەستکاریکردنی کاڵا</h3>
                <button onclick="closeEditProductModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>
            <form id="editProductForm" method="POST" class="space-y-2.5">
                @csrf @method('PUT')
                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">ناوی کاڵا:</label>
                    <input type="text" name="name" id="edit_name" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">کۆد یان بارکۆد:</label>
                    <input type="text" name="code" id="edit_code" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">کاتیگۆری:</label>
                    <select name="category_id" id="edit_category_id" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white text-xs">
                        @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-slate-300 mb-1 text-[11px]">نرخی کڕین (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_buy_price" id="edit_buy_price" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-300 mb-1 text-[11px]">نرخی فرۆشتن (١ کگ):</label>
                        <input type="number" step="any" min="0" name="base_sale_price" id="edit_sale_price" required class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs">
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 bg-slate-900 p-2 rounded-lg cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-blue-600 w-3.5 h-3.5">
                        <span class="text-slate-300 font-bold text-[11px]">چالاک بێت لە شاشەی POS</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" onclick="closeEditProductModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-3 py-1.5 rounded-lg text-[11px]">داخستن</button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-1.5 rounded-lg text-[11px]">نوێکردنەوە</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مۆداڵی زیادکردنی خێرای کاتیگۆری -->
    <div id="quickCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 p-4 rounded-xl w-full max-w-sm space-y-3 text-xs">
            <h3 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-tags text-blue-400"></i> زیادکردنی کاتیگۆری نوێ</h3>
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-2.5" id="quickCategoryForm">
                @csrf
                <div>
                    <label class="block text-[11px] text-slate-300 mb-1">ناوی کاتیگۆری:</label>
                    <input type="text" name="name" id="quick_category_name" required class="w-full p-2 rounded-lg border border-slate-600 bg-slate-700 text-white text-xs">
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="closeQuickCategoryModal()" class="px-3 py-1.5 bg-slate-700 text-slate-300 rounded-lg text-[11px]">پاشگەزبوونەوە</button>
                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[11px]">تۆمارکردن</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // =====================================================
        // 🔥 چارەسەری سەرەکی: پاشەکەوت و گەڕاندنەوەی داتای فۆرم
        // =====================================================

        const FORM_DATA_KEY = 'pos_product_form_data_v1';

        // پاشەکەوتکردنی داتای فۆرم پێش هەر گۆڕانکاری
        function saveProductFormData() {
            const formData = {
                name: document.getElementById('field_name').value,
                code: document.getElementById('field_code').value,
                category_id: document.getElementById('field_category').value,
                base_buy_price: document.getElementById('field_buy_price').value,
                base_sale_price: document.getElementById('field_sale_price').value,
                stock_kg: document.getElementById('field_stock').value,
                is_active: document.getElementById('field_is_active').checked
            };
            sessionStorage.setItem(FORM_DATA_KEY, JSON.stringify(formData));
        }

        // گەڕاندنەوەی داتای فۆرم دوای نوێبوونەوەی لاپەڕە
        document.addEventListener('DOMContentLoaded', function() {
            const saved = sessionStorage.getItem(FORM_DATA_KEY);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    
                    // گەڕاندنەوەی نرخەکان
                    if (data.name) document.getElementById('field_name').value = data.name;
                    if (data.code) document.getElementById('field_code').value = data.code;
                    if (data.base_buy_price) document.getElementById('field_buy_price').value = data.base_buy_price;
                    if (data.base_sale_price) document.getElementById('field_sale_price').value = data.base_sale_price;
                    if (data.stock_kg) document.getElementById('field_stock').value = data.stock_kg;
                    
                    // دۆخی چالاک
                    if (data.is_active !== undefined) {
                        document.getElementById('field_is_active').checked = data.is_active;
                    }
                    
                    // دیاریکردنی کاتیگۆری
                    const catSelect = document.getElementById('field_category');
                    if (catSelect.options.length > 0) {
                        // چونکە کاتیگۆری نوێ زیادکراوە، دوایین هەڵبژاردن هەڵدەبژێرین
                        catSelect.selectedIndex = catSelect.options.length - 1;
                    }
                    
                    // پاککردنەوەی داتای پاشەکەوتکراو
                    sessionStorage.removeItem(FORM_DATA_KEY);
                    
                    // فۆکس لەسەر ناوی کاڵا دانەین چونکە بەکارهێنەر لەوانەیە نامەوێت
                } catch(e) {
                    console.error('Error restoring form data:', e);
                    sessionStorage.removeItem(FORM_DATA_KEY);
                }
            }
        });

        // =====================================================
        // مۆداڵەکان و فەنکشنەکانی تر
        // =====================================================

        function openImportModal() { document.getElementById('importProductModal').classList.remove('hidden'); }
        function closeImportModal() { document.getElementById('importProductModal').classList.add('hidden'); }

        // 🔥 کردنەوەی مۆداڵی کاتیگۆری لەگەڵ پاشەکەوتکردنی داتا
        function openQuickCategoryModal() {
            saveProductFormData();
            document.getElementById('quickCategoryModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('quick_category_name').focus(), 100);
        }
        
        function closeQuickCategoryModal() { 
            document.getElementById('quickCategoryModal').classList.add('hidden'); 
            sessionStorage.removeItem(FORM_DATA_KEY);
        }

        // پاشەکەوتکردنی داتا لە کاتی ناردنی فۆرمی کاتیگۆری
        document.getElementById('quickCategoryForm').addEventListener('submit', function() {
            saveProductFormData();
        });

        // جووڵاندنی فۆڕم بە Enter
        const inputs = Array.from(document.querySelectorAll('.enter-nav'));
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                        if (typeof inputs[index + 1].select === 'function') inputs[index + 1].select();
                    } else {
                        document.getElementById('productForm').submit();
                    }
                }
            });
        });

        // فلتەری خشتە
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
                
                if (selectedStatus === 'out') matchesStatus = (stock <= 0);
                else if (selectedStatus === 'low') matchesStatus = (stock > 0 && stock <= 5);
                else if (selectedStatus === 'active') matchesStatus = (active === '1');
                else if (selectedStatus === 'inactive') matchesStatus = (active === '0');

                if (matchesSearch && matchesCat && matchesStatus) {
                    row.style.display = ''; visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const badge = document.getElementById('productCountBadge');
            if (badge) badge.innerText = `کاڵای دیاریکراو: ${visibleCount}`;
        }

        function filterByStockState(state) {
            const statusSelect = document.getElementById('stockStatusFilter');
            if (statusSelect) { statusSelect.value = state; filterStockTable(); }
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

        function closeEditProductModal() { document.getElementById('editProductModal').classList.add('hidden'); }
    </script>
</body>
</html>