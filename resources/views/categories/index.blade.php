<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی کاتیگۆرییەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Almarai', sans-serif; } 
        .font-num { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- سەرپەڕە -->
        <div class="flex flex-wrap justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700 gap-3">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-tags text-blue-400"></i>
                بەڕێوەبردنی کاتیگۆرییەکان
            </h1>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition shadow text-xs font-bold">
                <i class="fa-solid fa-arrow-right"></i> گەڕانەوە بۆ POS
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- لیستی کاتیگۆرییەکان -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-3">
                    <i class="fa-solid fa-list text-blue-400"></i> لیستی کاتیگۆرییەکان
                </h2>

                <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($categories as $category)
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700 flex justify-between items-center hover:border-slate-600 transition">
                        <div class="flex-1">
                            <h3 class="font-bold text-white text-sm">{{ $category->name }}</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                <i class="fa-solid fa-box text-blue-400"></i>
                                {{ $category->products_count ?? 0 }} کاڵا لەم کاتیگۆرییەدا
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <!-- دوگمەی دەستکاری -->
                            <button type="button" 
                                    onclick="openEditCategoryModal({{ json_encode($category) }})" 
                                    class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                            </button>

                            <!-- دوگمەی سڕینەوە -->
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم کاتیگۆرییە؟')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i> سڕینەوە
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-500 text-xs">
                        <i class="fa-solid fa-inbox text-2xl block mb-2"></i>
                        هیچ کاتیگۆرییەک تۆمار نەکراوە
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- فۆڕمی زیادکردنی کاتیگۆری نوێ -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-3">
                    <i class="fa-solid fa-square-plus text-emerald-400"></i> زیادکردنی کاتیگۆری نوێ
                </h2>

                <form action="{{ route('categories.store') }}" method="POST" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1.5">ناوی کاتیگۆری:</label>
                        <input type="text" name="name" required autofocus
                               placeholder="نموونە: مەواد، خواردن، خواردنەوە..."
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg">
                        <i class="fa-solid fa-check"></i> تۆمارکردن
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- مۆداڵی دەستکاریکردنی کاتیگۆری -->
    <div id="editCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl text-xs">
            
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی کاتیگۆری
                </h3>
                <button onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>

            <form id="editCategoryForm" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-300 mb-1.5 text-[11px]">ناوی کاتیگۆری:</label>
                    <input type="text" name="name" id="edit_category_name" required 
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:border-amber-500 focus:outline-none">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditCategoryModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-xl text-[11px]">
                        داخستن
                    </button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2 rounded-xl text-[11px] shadow">
                        <i class="fa-solid fa-check"></i> نوێکردنەوە
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // کردنەوەی مۆداڵی دەستکاریکردن
        function openEditCategoryModal(category) {
            document.getElementById('edit_category_name').value = category.name || '';
            
            // دیاریکردنی ڕاوتەکە بۆ نوێکردنەوە
            document.getElementById('editCategoryForm').action = '/categories/' + category.id;
            
            document.getElementById('editCategoryModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('edit_category_name').focus(), 100);
        }

        // داخستنی مۆداڵی دەستکاریکردن
        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }

        // داخستن بە ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditCategoryModal();
            }
        });
    </script>
</body>
</html>