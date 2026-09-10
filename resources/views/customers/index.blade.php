<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی کڕیاران و قەرز</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-users text-blue-400"></i>
                بەڕێوەبردنی کڕیاران و وەرگرتنەوەی قەرز
            </h1>
            <div class="flex gap-2">
                <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition">ڕاپۆرتەکان</a>
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">POS</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3.5 rounded-xl text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی تۆمارکردنی کڕیاری نوێ -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white">تۆمارکردنی کڕیاری نوێ</h2>
                <form action="{{ route('customers.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ناوی سیانی:</label>
                        <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ژمارەی مۆبایل:</label>
                        <input type="text" name="phone" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ناونیشان:</label>
                        <input type="text" name="address" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-xl transition text-sm">
                        تۆمارکردنی کڕیار
                    </button>
                </form>
            </div>

            <!-- لیستی کڕیاران و حسابات -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 overflow-x-auto space-y-4">
                <h2 class="text-base font-bold text-white">لیستی کڕیاران و حسابات</h2>
                <table class="w-full text-sm text-right text-slate-300">
                    <thead class="bg-slate-700/50 text-xs text-slate-400">
                        <tr>
                            <th class="p-3">ناو</th>
                            <th class="p-3">مۆبایل</th>
                            <th class="p-3">کۆی کڕینەکان</th>
                            <th class="p-3">قەرزی ماوە</th>
                            <th class="p-3">وەرگرتنەوەی قەرز</th>
                            <th class="p-3 text-center">ڕاپۆرت / کردار</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($customers as $cust)
                        @php
                            $totalBuy = $cust->sales->sum('total_amount');
                            $totalPaid = $cust->sales->sum('paid_amount') + ($cust->payments ? $cust->payments->sum('amount') : 0);
                            $debt = $totalBuy - $totalPaid;
                        @endphp
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-3 font-bold text-white">{{ $cust->name }}</td>
                            <td class="p-3 font-mono text-cyan-400 text-xs">{{ $cust->phone ?? '-' }}</td>
                            <td class="p-3 font-mono font-bold" dir="ltr">{{ number_format($totalBuy) }} د.ع</td>
                            <td class="p-3 font-mono font-black {{ $debt > 0 ? 'text-amber-400' : 'text-emerald-400' }}" dir="ltr">
                                {{ number_format($debt) }} د.ع
                            </td>
                            <td class="p-3">
                                <!-- فۆڕمی وەرگرتنەوەی قەرز -->
                                <form action="{{ route('customers.payment', $cust->id) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    <input type="number" step="any" min="1" name="amount" placeholder="بڕی پارە" required class="w-24 p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-xs font-mono text-white">
                                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="p-1 bg-slate-700 border border-slate-600 rounded-lg text-xs text-slate-300 font-mono">
                                    <button type="submit" title="وەرگرتنی پارە" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-lg text-xs transition">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- دوگمەی کەشف -->
                                    <a href="{{ route('customers.statement', $cust->id) }}" target="_blank" class="bg-blue-600/20 text-blue-400 border border-blue-500/40 hover:bg-blue-600 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-file-lines"></i> کەشف
                                    </a>

                                    <!-- دوگمەی دەستکاری -->
                                    <button type="button" 
                                            onclick="openEditCustomerModal({{ $cust->id }}, '{{ addslashes($cust->name) }}', '{{ $cust->phone ?? '' }}', '{{ addslashes($cust->address ?? '') }}')" 
                                            class="bg-amber-600/20 text-amber-400 border border-amber-500/40 hover:bg-amber-600 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </button>

                                    <!-- دوگمەی سڕینەوە -->
                                    <form action="{{ route('customers.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوە؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-400 text-xs p-1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-6 text-center text-slate-500">هیچ کڕیارێک تۆمار نەکراوە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- مۆداڵی دەستکاریکردنی کڕیار (Popup Modal) -->
    <div id="editCustomerModal" class="hidden fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-5 space-y-4 shadow-2xl text-right">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-amber-400"></i> دەستکاریکردنی کڕیار
                </h3>
                <button type="button" onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editCustomerForm" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-slate-400 mb-1 font-bold">ناوی سیانی:</label>
                    <input type="text" name="name" id="edit_name" required class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white text-xs focus:outline-none focus:border-amber-400">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1 font-bold">ژمارەی مۆبایل:</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white text-xs font-mono focus:outline-none focus:border-amber-400">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1 font-bold">ناونیشان:</label>
                    <input type="text" name="address" id="edit_address" class="w-full p-2.5 rounded-xl bg-slate-700 border border-slate-600 text-white text-xs focus:outline-none focus:border-amber-400">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditCustomerModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold transition">پاشگەزبوونەوە</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> پاشەکەوتکردنی گۆڕانکاری
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditCustomerModal(id, name, phone, address) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_phone').value = (phone && phone !== '-') ? phone : '';
            document.getElementById('edit_address').value = (address && address !== '-') ? address : '';
            document.getElementById('editCustomerForm').action = '/customers/' + id;
            document.getElementById('editCustomerModal').classList.remove('hidden');
        }

        function closeEditCustomerModal() {
            document.getElementById('editCustomerModal').classList.add('hidden');
        }
    </script>
</body>
</html>