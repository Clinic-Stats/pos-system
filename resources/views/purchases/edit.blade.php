<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاریکردنی وەسڵی کڕین - {{ $purchase->purchase_no ?? $purchase->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
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

        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- زانیاری وەسڵ -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                <th class="p-3">نرخی یەکە (د.ع)</th>
                                <th class="p-3">کۆی پارە</th>
                                <th class="p-3 text-center">لابردن</th>
                            </tr>
                        </thead>
                        <tbody id="items-table" class="divide-y divide-slate-700">
                            @foreach($purchase->details as $index => $detail)
                            @php
                                $unitFactor = 1;
                                $uName = mb_strtolower(trim($detail->unit->name ?? ''));
                                if (str_contains($uName, 'تەن') || str_contains($uName, 'ton')) {
                                    $unitFactor = 1000;
                                } elseif ((str_contains($uName, 'کارتۆن') || str_contains($uName, 'carton')) && $detail->product && $detail->product->kg_per_carton) {
                                    $unitFactor = (float)$detail->product->kg_per_carton;
                                } else {
                                    $unitFactor = (float)($detail->unit->factor_to_base ?? 1);
                                }
                                $basePrice = $detail->product->base_buy_price ?? ($detail->unit_buy_price / ($unitFactor ?: 1));
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
                                    <input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $detail->quantity }}" required oninput="calculateTotal()" class="qty-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-24">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="any" name="items[{{ $index }}][buy_price]" value="{{ round($basePrice, 2) }}" required oninput="calculateTotal()" class="price-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-32">
                                </td>
                                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr">
                                    {{ number_format($detail->subtotal) }} IQD
                                </td>
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
                    <span id="grand-total" class="text-emerald-400 font-mono text-lg" dir="ltr">{{ number_format($purchase->total_amount) }} IQD</span>
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
        let rowIndex = {{ count($purchase->details) }};

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

            let prodOpts = products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
            let unitOpts = units.map(u => `<option value="${u.id}">${u.name}</option>`).join('');

            row.innerHTML = `
                <td class="p-2">
                    <select name="items[${rowIndex}][product_id]" required onchange="calculateTotal()" class="prod-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                        ${prodOpts}
                    </select>
                </td>
                <td class="p-2">
                    <select name="items[${rowIndex}][unit_id]" required onchange="calculateTotal()" class="unit-select p-2 bg-slate-700 border border-slate-600 rounded-lg text-white w-full">
                        ${unitOpts}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" step="0.01" name="items[${rowIndex}][quantity]" value="1" required oninput="calculateTotal()" class="qty-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-24">
                </td>
                <td class="p-2">
                    <input type="number" step="any" min="0" name="items[${rowIndex}][buy_price]" value="0" required oninput="calculateTotal()" class="price-input p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono w-32">
                </td>
                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr">0 IQD</td>
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
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('tr').remove();
                calculateTotal();
            } else {
                alert('ناتوانی هەموو کاڵاکان بسڕیتەوە');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const prodId = row.querySelector('.prod-select').value;
                const unitId = row.querySelector('.unit-select').value;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;

                const factor = getUnitFactor(unitId, prodId);
                const lineTotal = qty * (price * factor);

                grandTotal += lineTotal;
                row.querySelector('.row-total').innerText = Math.round(lineTotal).toLocaleString() + ' IQD';
            });
            document.getElementById('grand-total').innerText = Math.round(grandTotal).toLocaleString() + ' IQD';
        }
    </script>

</body>
</html>