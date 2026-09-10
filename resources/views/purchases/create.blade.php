<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وەسڵی نوێی کڕین</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-cart-flatbed text-emerald-400"></i>
                تۆمارکردنی وەسڵی نوێی کڕین (فرە-کاڵا)
            </h1>
            <a href="{{ route('purchases.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                گەڕانەوە بۆ لیستی وەسڵەکان
            </a>
        </div>

        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3.5 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif

        <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm" onsubmit="return validatePurchaseForm(event)" class="space-y-6">
            @csrf

            {{-- بەشی سەرەوەی وەسڵ: شوێن و بەروار --}}
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">شوێنی کڕین (کۆمپانیا/دابینکەر):</label>
                    <select name="supplier_id" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm focus:outline-none focus:border-blue-500">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">بەرواری وەسڵ:</label>
                    <input type="date" name="created_at" value="{{ date('Y-m-d') }}" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm font-mono focus:outline-none focus:border-blue-500">
                </div>
            </div>

            {{-- خشتەی کاڵاکان لە ناو وەسڵەکەدا --}}
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-sm font-bold text-white">لیستی کاڵاکانی ئەم وەسڵە</h2>
                    <button type="button" onclick="addRow()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-xl transition flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i> زیادکردنی کاڵا
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-right text-slate-300" id="itemsTable">
                        <thead class="bg-slate-700/50 text-slate-400">
                            <tr>
                                <th class="p-2.5 w-44">کاڵا</th>
                                <th class="p-2.5 w-32">یەکە</th>
                                <th class="p-2.5 w-24">بڕ</th>
                                <th class="p-2.5 w-32">نرخی یەکە (د.ع)</th>
                                <th class="p-2.5 w-36">کۆی پارە</th>
                                <th class="p-2.5 text-center w-12">لابردن</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700" id="tableBody">
                            <!-- لێرە دێڕەکان بە JS زیاد دەبن -->
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-700 flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-300">کۆی گشتی وەسڵ:</span>
                    <span id="grandTotal" class="text-emerald-400 font-mono font-black text-xl" dir="ltr">0 IQD</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl transition text-base shadow-lg shadow-emerald-900/30 flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> تەواوکردن و تۆمارکردنی وەسڵی کڕین بۆ ناو کۆگا
            </button>
        </form>

    </div>

    <script>
        const products = @json($products);
        const units = @json($units);
        let rowCount = 0;

        function getUnitFactor(unitId, productId) {
            const unit = units.find(u => u.id == unitId);
            const product = products.find(p => p.id == productId);
            if (!unit) return 1;

            const uName = (unit.name || '').toLowerCase();
            if (uName.includes('تەن') || uName.includes('ton')) {
                return 1000;
            }
            if ((uName.includes('کارتۆن') || uName.includes('carton')) && product && product.kg_per_carton) {
                return parseFloat(product.kg_per_carton) || 1;
            }
            return parseFloat(unit.factor_to_base) || 1;
        }

        function addRow() {
            const tbody = document.getElementById('tableBody');
            const rowId = rowCount++;

            let prodOptions = products.map(p => `<option value="${p.id}">${p.name} (کۆگا: ${p.stock_kg ?? p.stock ?? 0} کگ)</option>`).join('');
            let unitOptions = units.map(u => `<option value="${u.id}">${u.name}</option>`).join('');

            const tr = document.createElement('tr');
            tr.id = `row-${rowId}`;
            tr.className = 'purchase-item-row';
            tr.innerHTML = `
                <td class="p-2">
                    <select name="items[${rowId}][product_id]" onchange="calcTotal()" required class="w-full p-2 bg-slate-700 border border-slate-600 rounded-lg text-white prod-select">
                        ${prodOptions}
                    </select>
                </td>
                <td class="p-2">
                    <select name="items[${rowId}][unit_id]" onchange="calcTotal()" required class="w-full p-2 bg-slate-700 border border-slate-600 rounded-lg text-white unit-select">
                        ${unitOptions}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" step="any" min="0.01" name="items[${rowId}][quantity]" value="1" oninput="calcTotal()" required class="w-full p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono qty-input">
                </td>
                <td class="p-2">
                    <input type="number" step="any" min="0" name="items[${rowId}][buy_price]" value="0" oninput="calcTotal()" required class="w-full p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono price-input">
                </td>
                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr">0 IQD</td>
                <td class="p-2 text-center">
                    <button type="button" onclick="removeRow(${rowId})" class="text-rose-400 hover:text-rose-300 text-sm">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            calcTotal();
        }

        function removeRow(id) {
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.remove();
                calcTotal();
            }
        }

        function calcTotal() {
            let total = 0;
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                const prodId = row.querySelector('.prod-select')?.value;
                const unitId = row.querySelector('.unit-select')?.value;
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                const pricePerBase = parseFloat(row.querySelector('.price-input')?.value) || 0;

                const factor = getUnitFactor(unitId, prodId);
                const sub = qty * (pricePerBase * factor);

                if (row.querySelector('.row-total')) {
                    row.querySelector('.row-total').innerText = Math.round(sub).toLocaleString() + ' IQD';
                }
                total += sub;
            });
            document.getElementById('grandTotal').innerText = Math.round(total).toLocaleString() + ' IQD';
            return total;
        }

        // پشکنینی وەسڵ پێش خەزنکردن
        function validatePurchaseForm(e) {
            const rows = document.querySelectorAll('#tableBody tr');
            if (rows.length === 0) {
                alert('وەسڵ ناتوانرێت بەتاڵ بێت! تکایە لانی کەم کاڵایەک زیاد بکە.');
                e.preventDefault();
                return false;
            }

            let hasZeroPrice = false;
            rows.forEach(row => {
                const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                if (price <= 0 || qty <= 0) {
                    hasZeroPrice = true;
                }
            });

            if (hasZeroPrice) {
                alert('تکایە نرخی کڕین و بڕی هەموو کاڵاکان بە دروستی پڕبکەرەوە (نابێت 0 بن).');
                e.preventDefault();
                return false;
            }

            const currentTotal = calcTotal();
            if (currentTotal <= 0) {
                alert('کۆی گشتی وەسڵ ناتوانێت 0 بێت.');
                e.preventDefault();
                return false;
            }

            return true;
        }

        // خستنەگەڕی دێڕی یەکەم لە سەرەتادا
        addRow();
    </script>
</body>
</html>