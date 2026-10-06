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
    @php
        $fmt = fn($v, $c) => $c === 'USD'
            ? '$' . number_format((float) $v, 2)
            : number_format((float) $v) . ' IQD';

        // یەک جار حیساب دەکرێت بۆ هەموو کڕیارەکان (بۆ کارتەکان و خشتەکە)
        $summaries = [];
        $totalDebt = ['USD' => 0, 'IQD' => 0];
        $debtorsCount = 0;
        foreach ($customers as $c) {
            $s = $c->currencySummary();
            $summaries[$c->id] = $s;
            $hasDebt = false;
            foreach (['USD', 'IQD'] as $cur) {
                if ($s[$cur]['debt'] > 0) {
                    $totalDebt[$cur] += $s[$cur]['debt'];
                    $hasDebt = true;
                }
            }
            if ($hasDebt) $debtorsCount++;
        }
    @endphp
    @include('partials.system-head')
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

        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3.5 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif

        {{-- کارتەکانی پوختە --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4">
                <div class="text-[11px] text-slate-400 font-bold mb-1"><i class="fa-solid fa-users text-blue-400"></i> کۆی کڕیاران</div>
                <div class="text-2xl font-black font-mono">{{ $customers->count() }}</div>
            </div>
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4">
                <div class="text-[11px] text-slate-400 font-bold mb-1"><i class="fa-solid fa-user-clock text-amber-400"></i> قەرزارەکان</div>
                <div class="text-2xl font-black font-mono text-amber-400">{{ $debtorsCount }}</div>
            </div>
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4">
                <div class="text-[11px] text-slate-400 font-bold mb-1"><i class="fa-solid fa-dollar-sign text-emerald-400"></i> کۆی قەرزی دۆلار</div>
                <div class="text-xl font-black font-mono {{ $totalDebt['USD'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}" dir="ltr">{{ $fmt($totalDebt['USD'], 'USD') }}</div>
            </div>
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4">
                <div class="text-[11px] text-slate-400 font-bold mb-1"><i class="fa-solid fa-coins text-amber-400"></i> کۆی قەرزی دینار</div>
                <div class="text-xl font-black font-mono {{ $totalDebt['IQD'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}" dir="ltr">{{ $fmt($totalDebt['IQD'], 'IQD') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی تۆمارکردنی کڕیاری نوێ -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white">تۆمارکردنی کڕیاری نوێ</h2>
                <form action="{{ route('customers.store') }}" method="POST" class="space-y-3 text-sm" autocomplete="off">
                    @csrf
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ناوی سیانی: <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ژمارەی مۆبایل:</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-blue-500" dir="ltr" style="text-align:right;">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs font-bold mb-1">ناونیشان:</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-xl transition text-sm">
                        <i class="fa-solid fa-user-plus"></i> تۆمارکردنی کڕیار
                    </button>
                </form>
            </div>

            <!-- لیستی کڕیاران و حسابات -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-base font-bold text-white">لیستی کڕیاران و حسابات</h2>
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex items-center gap-1.5 text-xs font-bold text-amber-400 cursor-pointer select-none">
                            <input type="checkbox" id="debtorsOnly" onchange="filterCustomers()" class="accent-amber-500 w-4 h-4">
                            تەنها قەرزارەکان
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute right-3 top-2.5 text-slate-400 text-xs"></i>
                            <input type="text" id="customerSearch" oninput="filterCustomers()" placeholder="گەڕان بە ناو، مۆبایل، ناونیشان..."
                                   autocomplete="off"
                                   class="w-60 pr-8 pl-3 py-2 rounded-xl border border-slate-600 bg-slate-700 text-white text-xs focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
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
                    <tbody class="divide-y divide-slate-700" id="customersBody">
                        @forelse($customers as $cust)
                        @php
                            $sum = $summaries[$cust->id];
                            $isDebtor = $sum['USD']['debt'] > 0 || $sum['IQD']['debt'] > 0;
                            // دراوی بنەڕەتی فۆڕمی پارەدان: ئەو دراوەی قەرزی هەیە
                            $defaultCur = ($sum['USD']['debt'] > 0 && $sum['IQD']['debt'] <= 0) ? 'USD' : 'IQD';
                        @endphp
                        <tr class="customer-row hover:bg-slate-700/30 transition"
                            data-search="{{ mb_strtolower($cust->name . ' ' . ($cust->phone ?? '') . ' ' . ($cust->address ?? '')) }}"
                            data-debtor="{{ $isDebtor ? 1 : 0 }}">
                            <td class="p-3">
                                <div class="font-bold text-white flex items-center gap-1.5">
                                    {{ $cust->name }}
                                    @if($isDebtor)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 text-[9px] font-black">قەرزار</span>
                                    @endif
                                </div>
                                @if(!empty($cust->address))
                                    <div class="text-[10px] text-slate-500 mt-0.5"><i class="fa-solid fa-location-dot"></i> {{ $cust->address }}</div>
                                @endif
                            </td>
                            <td class="p-3 font-mono text-cyan-400 text-xs">{{ $cust->phone ?? '-' }}</td>

                            <td class="p-3 font-mono font-bold text-xs space-y-1" dir="ltr">
                                <div class="{{ $sum['USD']['purchases'] > 0 ? 'text-slate-100' : 'text-slate-600' }}">{{ $fmt($sum['USD']['purchases'], 'USD') }}</div>
                                <div class="{{ $sum['IQD']['purchases'] > 0 ? 'text-slate-100' : 'text-slate-600' }}">{{ $fmt($sum['IQD']['purchases'], 'IQD') }}</div>
                            </td>

                            <td class="p-3 font-mono font-black text-xs space-y-1" dir="ltr">
                                @foreach(['USD', 'IQD'] as $cur)
                                    <div class="{{ $sum[$cur]['debt'] > 0 ? 'text-amber-400' : ($sum[$cur]['debt'] < 0 ? 'text-sky-400' : 'text-emerald-400') }}">
                                        {{ $fmt($sum[$cur]['debt'], $cur) }}
                                    </div>
                                @endforeach
                            </td>

                            <td class="p-3">
                                <form action="{{ route('customers.payment', $cust->id) }}" method="POST" class="flex flex-wrap items-center gap-1.5" autocomplete="off">
                                    @csrf
                                    <input type="number" step="any" min="0.01" name="amount" placeholder="بڕی پارە" required class="w-24 p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-xs font-mono text-white">
                                    <select name="currency" class="p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-xs font-bold text-white">
                                        <option value="USD" {{ $defaultCur === 'USD' ? 'selected' : '' }}>$ دۆلار</option>
                                        <option value="IQD" {{ $defaultCur === 'IQD' ? 'selected' : '' }}>د.ع دینار</option>
                                    </select>
                                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="p-1 bg-slate-700 border border-slate-600 rounded-lg text-xs text-slate-300 font-mono">
                                    <input type="text" name="note" placeholder="تێبینی (ئارەزوومەندانە)" class="w-40 p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-xs text-white">
                                    <button type="submit" title="وەرگرتنی پارە" class="bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-lg text-xs transition">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </button>
                                </form>
                            </td>

                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('customers.statement', $cust->id) }}" target="_blank" class="bg-blue-600/20 text-blue-400 border border-blue-500/40 hover:bg-blue-600 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-file-lines"></i> کەشف
                                    </a>

                                    <button type="button"
                                            data-id="{{ $cust->id }}"
                                            data-name="{{ $cust->name }}"
                                            data-phone="{{ $cust->phone ?? '' }}"
                                            data-address="{{ $cust->address ?? '' }}"
                                            onclick="openEditCustomerModal(this)"
                                            class="bg-amber-600/20 text-amber-400 border border-amber-500/40 hover:bg-amber-600 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square"></i> دەستکاری
                                    </button>

                                    <form action="{{ route('customers.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ({{ addslashes($cust->name) }})؟')">
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
                        <tr id="noResultRow" class="hidden">
                            <td colspan="6" class="p-6 text-center text-slate-500">هیچ کڕیارێک نەدۆزرایەوە</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>

        </div>

    </div>

    <!-- مۆداڵی دەستکاریکردنی کڕیار -->
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

            <form id="editCustomerForm" method="POST" class="space-y-3 text-xs" autocomplete="off">
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
        function openEditCustomerModal(btn) {
            const phone = btn.dataset.phone;
            const address = btn.dataset.address;
            document.getElementById('edit_name').value = btn.dataset.name || '';
            document.getElementById('edit_phone').value = (phone && phone !== '-') ? phone : '';
            document.getElementById('edit_address').value = (address && address !== '-') ? address : '';
            document.getElementById('editCustomerForm').action = '/customers/' + btn.dataset.id;
            document.getElementById('editCustomerModal').classList.remove('hidden');
        }

        function closeEditCustomerModal() {
            document.getElementById('editCustomerModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEditCustomerModal();
        });

        // گەڕان + فلتەری قەرزارەکان
        function filterCustomers() {
            const q = document.getElementById('customerSearch').value.toLowerCase().trim();
            const debtorsOnly = document.getElementById('debtorsOnly').checked;
            let visible = 0;

            document.querySelectorAll('.customer-row').forEach(row => {
                const matchText = !q || row.dataset.search.includes(q);
                const matchDebt = !debtorsOnly || row.dataset.debtor === '1';
                const show = matchText && matchDebt;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const noRes = document.getElementById('noResultRow');
            if (noRes) noRes.classList.toggle('hidden', visible > 0 || document.querySelectorAll('.customer-row').length === 0);
        }
    </script>
</body>
</html>