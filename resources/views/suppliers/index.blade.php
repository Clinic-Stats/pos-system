<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شوێنەکانی کڕین (دابینکەران)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-building text-amber-400"></i>
                شوێنەکانی کڕین (کۆمپانیا و دابینکەران)
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('purchases.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition">وەسڵی کڕین</a>
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition">POS</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3 rounded-xl text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3 rounded-xl text-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی زیادکردن -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white">زیادکردنی شوێنی کڕین</h2>
                <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-slate-300 mb-1">ناوی کۆمپانیا یان کەس:</label>
                        <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 mb-1">ژمارەی مۆبایل:</label>
                        <input type="text" name="phone" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 mb-1">ناونیشان:</label>
                        <input type="text" name="address" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl transition">
                        تۆمارکردن
                    </button>
                </form>
            </div>

            <!-- خشتەی دابینکەران -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 overflow-x-auto space-y-4">
                <h2 class="text-base font-bold text-white">لیستی شوێنەکان</h2>
                <table class="w-full text-sm text-right text-slate-300">
                    <thead class="bg-slate-700/50 text-xs text-slate-400">
                        <tr>
                            <th class="p-3">ناو</th>
                            <th class="p-3">مۆبایل</th>
                            <th class="p-3">ناونیشان</th>
                            <th class="p-3">ژمارەی کڕینەکان</th>
                            <th class="p-3 text-center">دەستکاری</th>
                            <th class="p-3 text-center">سڕینەوە</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($suppliers as$sup)
                        <tr class="hover:bg-slate-700/30">
                            <td class="p-3 font-bold text-white">{{ $sup->name }}</td>
                            <td class="p-3 font-mono text-cyan-400">{{ $sup->phone ?? '-' }}</td>
                            <td class="p-3">{{ $sup->address ?? '-' }}</td>
                            <td class="p-3 font-mono font-bold text-emerald-400">{{ $sup->purchases->count() }} وەسڵ</td>
                            
                            <!-- دوگمەی دەستکاری -->
                            <td class="p-3 text-center">
                                <button type="button" onclick="openEditModal({{ $sup->id }}, '{{ addslashes($sup->name) }}', '{{ addslashes($sup->phone ?? '') }}', '{{ addslashes($sup->address ?? '') }}')" class="bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">
                                    <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                </button>
                            </td>

                            <!-- دوگمەی سڕینەوە -->
                            <td class="p-3 text-center">
                                <form action="{{ route('suppliers.destroy', $sup->id) }}" method="POST" onsubmit="return confirm('دڵنیایت؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">
                                        <i class="fa-solid fa-trash"></i> سڕینەوە
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-500">هیچ شوێنێک تۆمار نەکراوە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- مۆداڵی دەستکاریکردن -->
    <div id="editModal" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 transition-opacity">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="flex justify-between items-center p-4 border-b border-slate-700 bg-slate-800/50">
                <h3 class="text-white font-bold text-lg"><i class="fa-solid fa-pen-to-square text-amber-500 ml-2"></i> دەستکاریکردنی زانیاری</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-slate-300 text-sm mb-1.5 font-bold">ناوی کۆمپانیا یان کەس:</label>
                    <input type="text" id="edit_name" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-slate-300 text-sm mb-1.5 font-bold">ژمارەی مۆبایل:</label>
                    <input type="text" id="edit_phone" name="phone" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500 transition-colors">
                </div>
                <div>
                    <label class="block text-slate-300 text-sm mb-1.5 font-bold">ناونیشان:</label>
                    <input type="text" id="edit_address" name="address" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500 transition-colors">
                </div>
                
                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl transition-colors shadow-lg shadow-blue-500/30">
                        نوێکردنەوە
                    </button>
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-bold py-2.5 rounded-xl transition-colors">
                        پاشگەزبوونەوە
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, phone, address) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_address').value = address;
            
            // دانانی ڕووتی دروست بۆ فۆڕمەکە
            document.getElementById('editForm').action = `/suppliers/${id}`;
            
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>