<!DOCTYPE html>
<html lang="ckb" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاریکردنی وەسڵی کڕین - {{ $purchase->purchase_no ?? $purchase->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
    </style>
    @php
    $curCurrency = old('currency', $purchase->currency ?? 'IQD');
    $curRate = old('exchange_rate', $purchase->exchange_rate ?? ($setting->exchange_rate ?? 1500));
    $curPayType = old('payment_type', $purchase->payment_type ?? 'cash');
    $curPaid = old('paid_amount', $purchase->paid_amount ?? 0);
    $curNo = old('purchase_no', $purchase->purchase_no ?? $purchase->invoice_no);
    @endphp
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                دەستکاریکردنی وەسڵی (<span class="font-mono text-amber-400">{{ $purchase->purchase_no ?? $purchase->invoice_no }}</span>)
            </h1>
            <a href="{{ route('purchases.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                گەڕانەوە
            </a>
        </div>

        @if(session('error'))
        <div class="bg-rose-500/20 border border-rose-500 text-rose-300 p-4 rounded-xl text-xs font-bold">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3.5 rounded-xl text-xs font-bold space-y-1">
            @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
        </div>
        @endif

        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form" class="space-y-6" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- زانیاری وەسڵ -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-300 mb-1">ژمارەی پسوولەی کڕین:</label>
                    <input type="text" name="purchase_no" value="{{ $curNo }}" required maxlength="100" dir="ltr" style="text-align:right;" class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">شوێنی کڕین (دابینکەر):</label>
                    <select name="supplier_id" required class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white text-xs">
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">بەرواری وەسڵ:</label>
                    <input type="date" name="created_at" value="{{ old('created_at', $purchase->created_at ? $purchase->created_at->format('Y-m-d') : ($purchase->purchase_date ?? date('Y-m-d'))) }}" required class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-xl text-white font-mono text-xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">دراوی وەسڵ:</label>
                    <div class="flex items-center gap-1.5 bg-slate-700 p-1 rounded-xl">
                        <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-colors">دۆلار</button>
                        <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="flex-1 py-1.5 rounded-lg text-xs font-bold transition-colors">دینار</button>
                    </div>
                    <input type="hidden" name="currency" id="currency_input" value="{{ $curCurrency }}">
                </div>

                <div id="exchangeRateBox" class="md:col-span-3 hidden">
                    <label class="block text-xs font-bold text-slate-300 mb-1">نرخی ئاڵوگۆڕی دۆلار (١ دۆلار = چ دینار):</label>
                    <input type="number" step="any" min="1" name="exchange_rate" id="exchange_rate_input" value="{{ $curRate }}" class="w-full p-2.5 rounded-xl border border-amber-600 bg-slate-700 text-white text-sm font-mono">
                </div>
            </div>

            <!-- خشتەی کاڵاکان -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-bold text-white">کاڵاکانی ناو وەسڵ</h2>
                    <button type="button" onclick="addRow()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i> زیادکردنی کاڵا
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-700/50 text-slate-400">
                            <tr>
                                <th class="p-3">کاڵا</th>
                                <th class="p-3">یەکە</th>
                                <th class="p-3">بڕ</th>
                                <th class="p-3">نرخی ١ کیلۆ (<span id="priceLabel">{{ $curCurrency === 'USD' ? '$' : 'IQD' }}</span>)</th>
                                <th class="p-3">کۆی پارە</th>
                                <th class="p-3 text-center">لابردن</th>
                            </tr>
                        </thead>
                        <tbody id="items-table" class="divide-y divide-slate-700">
                            @foreach($purchase->details as $index => $detail)
                            @php
                            $uName = mb_strtolower(trim($detail->unit->name ?? ''));
                            if (str_contains($uName, 'کارتۆن') || str_contains($uName, 'carton')) {
                            $unitFactor = (float) (($detail->product->kg_per_carton ?? 1) ?: 1);
                            } elseif (str_contains($uName, 'تەن') || str_contains($uName, 'ton')) {
                            $unitFactor = 1000;
                            } else {
                            $unitFactor = (float) (($detail->unit->factor_to_base ?? 1) ?: 1);
                            }
                            $basePrice = $detail->unit_buy_price / ($unitFactor ?: 1);
                            @endphp
                            <tr class="item-row">
                                <td class="p-2">
                                    <select name="items[{{ $index }}][product_id]" required onchange="calculateTotal()" class="prod-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <select name="items[{{ $index }}][unit_id]" required onchange="calculateTotal()" class="unit-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                                        @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $detail->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="any" min="0.01" name="items[{{ $index }}][quantity]" value="{{ (float) $detail->quantity }}" required oninput="calculateTotal()" class="qty-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-24">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="any" min="0" name="items[{{ $index }}][buy_price]" value="{{ round($basePrice, 4) }}" required oninput="calculateTotal()" class="price-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-32">
                                </td>
                                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr"></td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="removeRow(this)" class="text-rose-400 hover:text-rose-300">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-700 font-bold">
                    <span class="text-slate-300">کۆی گشتیی وەسڵ:</span>
                    <span id="grand-total" class="text-emerald-400 font-mono text-lg" dir="ltr"></span>
                </div>
            </div>

            <!-- پارەدان -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">جۆری پارەدان:</label>
                    <select name="payment_type" id="payment_type" onchange="togglePaid()" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm">
                        <option value="cash" {{ $curPayType === 'cash' ? 'selected' : '' }}>نەقد</option>
                        <option value="debt" {{ $curPayType === 'debt' ? 'selected' : '' }}>قەرز</option>
                    </select>
                </div>
                <div id="paidAmountBox" class="hidden">
                    <label class="block text-xs font-bold text-slate-300 mb-1">بڕی پارەی دراو:</label>
                    <input type="number" step="any" min="0" name="paid_amount" id="paid_amount" value="{{ $curPaid }}" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm font-mono">
                </div>
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3.5 rounded-2xl transition">
                خەزنکردنی دەستکارییەکان و ڕێکخستنەوەی کۆگا
            </button>
        </form>

    </div>

    <script>
        const products = @json($products);
        const units = @json($units);
        let rowIndex = document.querySelectorAll('.item-row').length;
        let currentCurrency = document.getElementById('currency_input').value || 'IQD';

        function setCurrency(cur) {
            currentCurrency = cur;
            document.getElementById('currency_input').value = cur;
            const usd = document.getElementById('btn-cur-usd');
            const iqd = document.getElementById('btn-cur-iqd');
            const on = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors';
            const off = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors';
            usd.className = cur === 'USD' ? on : off;
            iqd.className = cur === 'IQD' ? on : off;
            document.getElementById('exchangeRateBox').classList.toggle('hidden', cur === 'USD');
            document.getElementById('priceLabel').innerText = cur === 'USD' ? '$' : 'IQD';
            calculateTotal();
        }

        function togglePaid() {
            const isDebt = document.getElementById('payment_type').value === 'debt';
            document.getElementById('paidAmountBox').classList.toggle('hidden', !isDebt);
        }

        function fmtMoney(v) {
            if (currentCurrency === 'USD') {
                return '$' + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return Math.round(v).toLocaleString() + ' IQD';
        }

        function getUnitFactor(unitId, productId) {
            const unit = units.find(u => u.id == unitId);
            const product = products.find(p => p.id == productId);
            if (!unit) return 1;
            const uName = (unit.name || '').toLowerCase();
            if (uName.includes('تەن') || uName.includes('ton')) return 1000;
            if ((uName.includes('کارتۆن') || uName.includes('carton')) && product && product.kg_per_carton) {
                return parseFloat(product.kg_per_carton) || 1;
            }
            return parseFloat(unit.factor_to_base) || 1;
        }

        function addRow() {
            const table = document.getElementById('items-table');
            const row = document.createElement('tr');
            row.className = 'item-row';

            // ✅ زیادکردنی هەڵبژاردەی بەتاڵ و لابردنی required بۆ ڕیزە نوێیەکان
            let prodOpts = '<option value="">— کاڵا هەڵبژێرە —</option>' + products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
            let unitOpts = units.map(u => `<option value="${u.id}">${u.name}</option>`).join('');

            row.innerHTML = `
                <td class="p-2">
                    <select name="items[${rowIndex}][product_id]" onchange="calculateTotal()" class="prod-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                        ${prodOpts}
                    </select>
                </td>
                <td class="p-2">
                    <select name="items[${rowIndex}][unit_id]" onchange="calculateTotal()" class="unit-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                        ${unitOpts}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" step="any" min="0.01" name="items[${rowIndex}][quantity]" value="1" oninput="calculateTotal()" class="qty-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-24">
                </td>
                <td class="p-2">
                    <input type="number" step="any" min="0" name="items[${rowIndex}][buy_price]" value="0" oninput="calculateTotal()" class="price-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-32">
                </td>
                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr"></td>
                <td class="p-2 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-rose-400 hover:text-rose-300">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            table.appendChild(row);
            rowIndex++;
            calculateTotal();
        }

        function removeRow(btn) {
            if (document.querySelectorAll('.item-row').length > 1) {
                btn.closest('tr').remove();
                calculateTotal();
            } else {
                alert('ناتوانی هەموو کاڵاکان بسڕیتەوە');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const prodSelect = row.querySelector('.prod-select');
                
                // ✅ ئەگەر کاڵا هەڵنەبژێردرابێت، هەژماری بۆ ناکەین
                if (!prodSelect.value) {
                    row.querySelector('.row-total').innerText = fmtMoney(0);
                    return;
                }

                const prodId = prodSelect.value;
                const unitId = row.querySelector('.unit-select').value;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;

                const lineTotal = qty * (price * getUnitFactor(unitId, prodId));
                grandTotal += lineTotal;
                row.querySelector('.row-total').innerText = fmtMoney(lineTotal);
            });
            document.getElementById('grand-total').innerText = fmtMoney(grandTotal);
        }

        // ✅ پشکنین پێش ناردنی فۆرمەکە بۆ ڕاگرتنی ڕیزە بەتاڵەکان
        document.getElementById('purchase-form').addEventListener('submit', function(e) {
            let validRows = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const prodSelect = row.querySelector('.prod-select');
                if (!prodSelect.value) {
                    // ئەگەر کاڵا هەڵنەبژێردرابێت، خانەکانی ئەم ڕیزە ناچالاک بکە بۆ ئەوەی نەنێردرێن
                    row.querySelectorAll('input, select').forEach(el => el.disabled = true);
                } else {
                    // دڵنیابەرەوە کە ڕیزە پڕکراوەکان ناچالاک نەکراون
                    row.querySelectorAll('input, select').forEach(el => el.disabled = false);
                    validRows++;
                }
            });

            if (validRows === 0) {
                e.preventDefault();
                alert('تکایە لانیکەم یەک کاڵا هەڵبژێرە و پڕی بکەرەوە بۆ ئەوەی وەسڵەکە تۆمار بکرێت!');
            }
        });

        setCurrency(currentCurrency);
        togglePaid();
    </script>

</body>

</html>