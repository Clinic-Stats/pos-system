<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی هاوبەشەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- سەرپەڕە -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-handshake text-emerald-400"></i>
                بەڕێوەبردنی هاوبەشەکان و سەرمایە
            </h1>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('partners.allReport') }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition flex items-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-print"></i> ڕاپۆرتی گشتی هەموو هاوبەشەکان (A4)
                </a>
                <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition">ڕاپۆرتەکان</a>
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">POS</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی زیادکردنی هاوبەشی نوێ -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-sm font-bold text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus text-emerald-400"></i> تۆمارکردنی هاوبەشی نوێ
                </h2>
                <form action="{{ route('partners.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ناوی هاوبەش:</label>
                        <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-bold mb-1">ڕێژەی بەشداربوون (%):</label>
                            <input type="number" step="any" min="0" max="100" name="share_percent" placeholder="0" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-bold mb-1">سەرمایەی سەرەتایی:</label>
                            <input type="number" step="any" min="0" name="capital" placeholder="0" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ژمارەی مۆبایل:</label>
                        <input type="text" name="phone" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">تێبینی:</label>
                        <input type="text" name="note" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition text-xs flex items-center justify-center gap-1.5 shadow-md">
                        <i class="fa-solid fa-plus"></i> تۆمارکردن
                    </button>
                </form>
            </div>

            <!-- خشتەی هاوبەشەکان -->
            <div class="lg:col-span-2 bg-slate-800 p-5 rounded-2xl border border-slate-700 overflow-x-auto space-y-4">
                <h2 class="text-sm font-bold text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-list-check text-blue-400"></i> لیستی هاوبەشەکان و جووڵەی سەرمایە
                </h2>
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-slate-700/50 text-[11px] text-slate-400">
                        <tr>
                            <th class="p-3">ناو</th>
                            <th class="p-3 text-center">ڕێژەی پشک</th>
                            <th class="p-3">سەرمایەی ئێستا</th>
                            <th class="p-3">ڕاکێشان / دانان بە بەروار</th>
                            <th class="p-3 text-center">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($partners as $partner)
                        @php
                            $initialCap = $partner->initial_capital ?? ($partner->capital_amount ?? ($partner->capital ?? 0));
                            $deposits = $partner->transactions ? $partner->transactions->where('type', 'deposit')->sum('amount') : 0;
                            $withdraws = $partner->transactions ? $partner->transactions->where('type', 'withdraw')->sum('amount') : 0;
                            $currentBalance = $initialCap + $deposits - $withdraws;
                            $shareVal = $partner->share ?? ($partner->share_percentage ?? ($partner->share_percent ?? 0));
                        @endphp
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-3">
                                <a href="{{ route('partners.show', $partner->id) }}" class="font-bold text-white hover:text-blue-400 transition flex items-center gap-1">
                                    {{ $partner->name }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500"></i>
                                </a>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $partner->phone ?? '-' }}</div>
                            </td>
                            <td class="p-3 text-center font-mono font-bold text-cyan-400">{{ (float) $shareVal }}%</td>
                            <td class="p-3 font-mono font-bold text-emerald-400" dir="ltr">
                                {{ number_format($currentBalance) }} IQD
                            </td>
                            <td class="p-3">
                                <form action="{{ route('partners.transaction', $partner->id) }}" method="POST" class="flex flex-wrap items-center gap-1">
                                    @csrf
                                    <select name="type" class="p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white text-[11px]">
                                        <option value="deposit">+ دانان</option>
                                        <option value="withdraw">- ڕاکێشان</option>
                                    </select>
                                    <input type="number" step="any" min="1" name="amount" placeholder="بڕ" required class="w-20 p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono text-[11px]">
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono text-[11px]">
                                    <input type="text" name="note" placeholder="تێبینی" class="w-20 p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white text-[11px]">
                                    <button type="submit" title="تۆمارکردنی مامەڵە" class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1.5 rounded-lg text-[11px] transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- کەشفی حیساب و چاپی A4 -->
                                    <a href="{{ route('partners.show', $partner->id) }}" title="کەشفی حیساب و چاپی A4" class="bg-indigo-600/40 text-indigo-300 border border-indigo-500/40 hover:bg-indigo-600 hover:text-white px-2 py-1.5 rounded-lg text-xs transition font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-file-invoice"></i> کەشف
                                    </a>

                                    <!-- دوگمەی دەستکاری -->
                                    <button type="button" 
                                            onclick="openEditModal({{ json_encode($partner) }})" 
                                            title="دەستکاریکردن" 
                                            class="bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1.5 rounded-lg text-xs transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- دوگمەی سڕینەوە -->
                                    <form action="{{ route('partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم هاوبەشە؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="سڕینەوە" class="bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1.5 rounded-lg text-xs transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500">هیچ هاوبەشێک تۆمار نەکراوە</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- مۆداڵی دەستکاریکردنی هاوبەش -->
    <div id="editModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-5 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی زانیاری هاوبەش
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-lg p-1">&times;</button>
            </div>

            <form id="editForm" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-slate-300 font-bold mb-1">ناوی هاوبەش:</label>
                    <input type="text" name="name" id="edit_name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ڕێژەی پشک (%):</label>
                        <input type="number" step="any" min="0" max="100" name="share_percent" id="edit_share_percent" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">سەرمایەی سەرەتایی:</label>
                        <input type="number" step="any" min="0" name="capital" id="edit_capital" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">ژمارەی مۆبایل:</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">تێبینی:</label>
                    <input type="text" name="note" id="edit_note" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                </div>

                <div class="flex gap-2 pt-2 border-t border-slate-700">
                    <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl transition">
                        نوێکردنەوە
                    </button>
                    <button type="button" onclick="closeEditModal()" class="px-5 bg-slate-700 hover:bg-slate-600 text-slate-300 font-bold rounded-xl transition">
                        داخستن
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(partner) {
            document.getElementById('editForm').action = '/partners/' + partner.id;
            document.getElementById('edit_name').value = partner.name || '';
            
            const share = partner.share_percentage !== undefined ? partner.share_percentage : (partner.share_percent || 0);
            const cap = partner.capital_amount !== undefined ? partner.capital_amount : (partner.capital || 0);

            document.getElementById('edit_share_percent').value = parseFloat(share) || 0;
            document.getElementById('edit_capital').value = parseFloat(cap) || 0;
            document.getElementById('edit_phone').value = partner.phone || '';
            document.getElementById('edit_note').value = partner.note || '';

            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>