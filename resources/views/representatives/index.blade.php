<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی مەندووبەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- هێدەر -->
        <div class="flex flex-wrap justify-between items-center bg-[#1e293b]/90 p-4 rounded-2xl border border-slate-700/70 gap-4">
            <h1 class="text-base font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-user-tie text-blue-400 text-lg"></i>
                بەڕێوەبردنی مەندووبەکان (نوێنەرانی فرۆشتن)
            </h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                    POS
                </a>
                <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> مەندووبی نوێ
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-950/40 border border-emerald-500/50 text-emerald-400 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- خشتەی مەندووبەکان -->
        <div class="bg-[#1e293b]/90 rounded-2xl border border-slate-700/70 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-300">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5 text-right">ناوی مەندووب</th>
                            <th class="p-3.5">مۆبایل</th>
                            <th class="p-3.5">ناوچەی کارکردن</th>
                            <th class="p-3.5">ڕێژەی کۆمسیۆن</th>
                            <th class="p-3.5">ژمارەی فرۆشتنەکان</th>
                            <th class="p-3.5">کۆی بڕی فرۆشراو</th>
                            <th class="p-3.5">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($representatives as $index => $rep)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-3.5 font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-3.5 text-right font-bold text-white">{{ $rep->name }}</td>
                            <td class="p-3.5 font-mono text-slate-300" dir="ltr">{{ $rep->phone ?? '-' }}</td>
                            <td class="p-3.5 text-slate-300">{{ $rep->area ?? 'دیاری نەکراوە' }}</td>
                            <td class="p-3.5 font-mono font-bold text-amber-400">٪{{ $rep->commission_rate }}</td>
                            <td class="p-3.5 font-mono text-blue-400 font-bold">{{ $rep->sales_count }} وەسڵ</td>
                            <td class="p-3.5 font-mono font-bold text-emerald-400" dir="ltr">
                                {{ number_format($rep->sales_sum_total_amount ?? 0) }} IQD
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('representatives.show', $rep->id) }}" class="bg-blue-600/80 hover:bg-blue-600 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-chart-line"></i> ڕاپۆرت
                                    </a>
                                    <button onclick="editRep({{ json_encode($rep) }})" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </button>
                                    <form action="{{ route('representatives.destroy', $rep->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم مەندووبە؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-600/90 hover:bg-rose-600 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                                            <i class="fa-solid fa-trash"></i> سڕینەوە
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 font-medium">هیچ مەندووبێک تۆمار نەکراوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- مۆداڵی زیادکردن -->
    <div id="add-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white">زیادکردنی مەندووبی نوێ</h3>
                <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form action="{{ route('representatives.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوی مەندووب:</label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">مۆبایل:</label>
                    <input type="text" name="phone" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوچەی کارکردن:</label>
                    <input type="text" name="area" placeholder="نموونە: هەڵەبجە، خورماڵ..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ڕێژەی کۆمسیۆن (٪ لەسەدا):</label>
                    <input type="number" step="0.1" name="commission_rate" value="0" min="0" max="100" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-xl">پاشگەزبوونەوە</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl">تۆمارکردن</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مۆداڵی دەستکاری -->
    <div id="edit-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white">دەستکاریکردنی زانیاریی مەندووب</h3>
                <button onclick="document.getElementById('edit-modal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form id="edit-form" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوی مەندووب:</label>
                    <input type="text" name="name" id="edit-name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">مۆبایل:</label>
                    <input type="text" name="phone" id="edit-phone" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوچەی کارکردن:</label>
                    <input type="text" name="area" id="edit-area" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block font-bold text-slate-300 mb-1">ڕێژەی کۆمسیۆن (٪ لەسەدا):</label>
                    <input type="number" step="0.1" name="commission_rate" id="edit-rate" min="0" max="100" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-700">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-xl">پاشگەزبوونەوە</button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl">نوێکردنەوە</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editRep(rep) {
            document.getElementById('edit-name').value = rep.name;
            document.getElementById('edit-phone').value = rep.phone || '';
            document.getElementById('edit-area').value = rep.area || '';
            document.getElementById('edit-rate').value = rep.commission_rate || 0;
            document.getElementById('edit-form').action = `/representatives/${rep.id}`;
            document.getElementById('edit-modal').classList.remove('hidden');
        }
    </script>
</body>
</html>