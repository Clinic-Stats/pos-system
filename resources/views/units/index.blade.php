<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی یەکەکان</title>
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
                <i class="fa-solid fa-scale-balanced text-blue-400"></i>
                بەڕێوەبردنی یەکەکان
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

            <!-- لیستی یەکەکان -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-3">
                    <i class="fa-solid fa-list text-blue-400"></i> لیستی یەکەکان
                </h2>

                <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($units as $unit)
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700 flex justify-between items-center hover:border-slate-600 transition">
                        <div>
                            <h3 class="font-bold text-white text-sm">{{ $unit->name }}</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                بەرامبەر بە: 
                                <span class="font-num text-emerald-400 font-bold">{{ $unit->factor_to_base ?? 1 }}</span> 
                                کیلۆ
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <!-- دوگمەی دەستکاری -->
                            <button type="button" 
                                    onclick="openEditUnitModal({{ json_encode($unit) }})" 
                                    class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                            </button>

                            <!-- دوگمەی سڕینەوە -->
                            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم یەکەیە؟')" class="inline">
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
                        هیچ یەکەیەک تۆمار نەکراوە
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- فۆڕمی زیادکردنی یەکەی نوێ -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-3">
                    <i class="fa-solid fa-square-plus text-emerald-400"></i> زیادکردنی یەکەی نوێ
                </h2>

                <form action="{{ route('units.store') }}" method="POST" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1.5">ناوی یەکە (نموونە: کیلۆ، کارتۆن):</label>
                        <input type="text" name="name" required autofocus
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1.5">کێشی بە کیلۆ (Factor to Base):</label>
                        <input type="number" step="any" min="0.0001" name="factor_to_base" required
                               placeholder="نموونە: بۆ قەرەدە بنووسە 50، بۆ کیلۆ 1"
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-num focus:border-blue-500 focus:outline-none text-sm">
                        <p class="text-[10px] text-slate-500 mt-1">بۆ نموونە: ١ کیلۆ = ١، ١ کارتۆن = ٥٠، ١ تەن = ١٠٠٠</p>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg">
                        <i class="fa-solid fa-check"></i> تۆمارکردن
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- مۆداڵی دەستکاریکردنی یەکە -->
    <div id="editUnitModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl text-xs">
            
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی یەکە
                </h3>
                <button onclick="closeEditUnitModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>

            <form id="editUnitForm" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-300 mb-1.5 text-[11px]">ناوی یەکە:</label>
                    <input type="text" name="name" id="edit_unit_name" required 
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white text-sm focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1.5 text-[11px]">کێشی بە کیلۆ (Factor to Base):</label>
                    <input type="number" step="any" min="0.0001" name="factor_to_base" id="edit_unit_factor" required 
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-num text-sm focus:border-amber-500 focus:outline-none">
                    <p class="text-[10px] text-slate-500 mt-1">بۆ نموونە: ١ کیلۆ = ١، ١ کارتۆن = ٥٠</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditUnitModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-xl text-[11px]">
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
        function openEditUnitModal(unit) {
            document.getElementById('edit_unit_name').value = unit.name || '';
            document.getElementById('edit_unit_factor').value = unit.factor_to_base || 1;
            
            // دیاریکردنی ڕاوتەکە بۆ نوێکردنەوە
            document.getElementById('editUnitForm').action = '/units/' + unit.id;
            
            document.getElementById('editUnitModal').classList.remove('hidden');
        }

        // داخستنی مۆداڵی دەستکاریکردن
        function closeEditUnitModal() {
            document.getElementById('editUnitModal').classList.add('hidden');
        }

        // داخستن بە ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditUnitModal();
            }
        });
    </script>
</body>
</html>