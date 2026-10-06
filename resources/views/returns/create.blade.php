<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تۆمارکردنی گەڕانەوەی فرۆشتن</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
    @include('partials.system-head')
</head>
<body class="bg-[#0f172a] text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex justify-between items-center bg-[#1e293b]/90 p-4 rounded-2xl border border-slate-700/70">
            <h1 class="text-base font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-rotate-left text-amber-400 text-lg"></i>
                تۆمارکردنی وەسڵی گەڕانەوەی فرۆشتن
            </h1>
            <a href="{{ route('returns.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                گەڕانەوە بۆ لیست
            </a>
        </div>

        <div class="bg-[#1e293b]/90 p-5 rounded-2xl border border-slate-700/70 grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-300 mb-1">کڕیار:</label>
                <select id="customer-select" class="w-full p-2.5 bg-[#0f172a] border border-slate-700 rounded-xl text-white">
                    <option value="">کڕیاری گشتی (نەقد)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">بەرواری گەڕانەوە:</label>
                <input type="datetime-local" id="return-date" value="{{ date('Y-m-d\TH:i') }}" class="w-full p-2.5 bg-[#0f172a] border border-slate-700 rounded-xl text-white font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">شێوازی چارەسەری پارە:</label>
                <select id="refund-type" class="w-full p-2.5 bg-[#0f172a] border border-slate-700 rounded-xl text-white font-bold text-amber-400">
                    <option value="deduct_debt">داشکاندن لە قەرزی کڕیار</option>
                    <option value="cash">دانەوەی پارەی نەقد بە کڕیار</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">دراوی وەسڵ:</label>
                <div class="flex items-center gap-1.5 bg-[#0f172a] p-1 rounded-xl border border-slate-700">
                    <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors">دۆلار</button>
                    <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors">دینار</button>
                </div>
            </div>

            <div id="rate-box" class="md:col-span-4 hidden">
                <label class="block font-bold text-slate-300 mb-1">نرخی ئاڵوگۆڕی دۆلار (١ دۆلار = چ دینار):</label>
                <input type="number" step="any" min="1" id="exchange-rate" value="{{ $setting->exchange_rate ?? 1500 }}" class="w-full p-2.5 bg-[#0f172a] border border-amber-600 rounded-xl text-white font-mono">
                <p class="text-[10px] text-amber-400 mt-1">ئەم نرخە تەنها بۆ گۆڕینی نرخی پێشنیارکراوی کاڵا بۆ دینار بەکار دێت.</p>
            </div>
        </div>

        <div class="bg-[#1e293b]/90 p-5 rounded-2xl border border-slate-700/70 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-sm font-bold text-white">لیستی کاڵا گەڕاوەکان</h2>
                <button type="button" onclick="addRow()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition flex items-center gap-1">
                    <i class="fa-solid fa-plus"></i> زیادکردنی دێڕ
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-center text-slate-300">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3 text-right">کاڵا</th>
                            <th class="p-3">یەکە</th>
                            <th class="p-3">بڕ</th>
                            <th class="p-3">نرخی ١ کیلۆ (<span id="price-label">$</span>)</th>
                            <th class="p-3">بارودۆخی کاڵا (جۆری گەڕانەوە)</th>
                            <th class="p-3">کۆی نرخ</th>
                            <th class="p-3">سڕینەوە</th>
                        </tr>
                    </thead>
                    <tbody id="items-table" class="divide-y divide-slate-700/50">
                        <tr id="empty-row">
                            <td colspan="7" class="p-8 text-slate-500">
                                <i class="fa-solid fa-box-open text-2xl block mb-2"></i>
                                هیچ کاڵایەک زیاد نەکراوە. دوگمەی «زیادکردنی دێڕ» بکە.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-[#0f172a]/60 rounded-xl border border-slate-700/50 flex flex-wrap justify-between items-center font-bold">
                <div class="text-slate-400 text-xs flex items-center gap-4">
                    <span>کۆی کێش: <b id="total-weight" class="text-amber-400 font-mono">0 کگ</b></span>
                    <span class="text-[11px] text-slate-500">(کاڵای بەسەرچوو ناخرێتەوە عەمبار)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-300 text-sm">کۆی گشتی وەسڵ:</span>
                    <span id="grand-total" class="text-emerald-400 font-mono text-lg" dir="ltr">$0.00</span>
                </div>
            </div>
        </div>

        <button onclick="submitReturn()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl transition">
            تەواوکردن و تۆمارکردنی وەسڵی گەڕانەوە
        </button>

    </div>
    <script>
        const products = @json($products);
        const units = @json($units);
        let currentCurrency = 'USD';

        function getRate() {
            return parseFloat(document.getElementById('exchange-rate').value) || 1500;
        }

        function fmt(v) {
            if (currentCurrency === 'USD') {
                return '$' + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return Math.round(v).toLocaleString() + ' IQD';
        }

        function suggestedPrice(usd) {
            const v = currentCurrency === 'USD' ? usd : usd * getRate();
            return parseFloat(v.toFixed(currentCurrency === 'USD' ? 4 : 2));
        }

        function setCurrency(cur) {
            if (cur === currentCurrency) return;
            const rate = getRate();

            document.querySelectorAll('.item-row .price-input').forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                const converted = cur === 'IQD' ? val * rate : val / rate;
                inp.value = parseFloat(converted.toFixed(cur === 'USD' ? 4 : 2));
            });

            currentCurrency = cur;
            const on = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors';
            const off = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors';
            document.getElementById('btn-cur-usd').className = cur === 'USD' ? on : off;
            document.getElementById('btn-cur-iqd').className = cur === 'IQD' ? on : off;
            document.getElementById('rate-box').classList.toggle('hidden', cur === 'USD');
            document.getElementById('price-label').innerText = cur === 'USD' ? '$' : 'IQD';
            calculate();
        }

        function addRow() {
            const table = document.getElementById('items-table');
            const row = document.createElement('tr');
            row.className = 'item-row hover:bg-slate-800/40 transition';

            // بە ڕیزی بەتاڵ دەست پێدەکات
            let prodOpts = '<option value="">— کاڵا هەڵبژێرە —</option>' + products.map(p => `<option value="${p.id}" data-price="${p.base_sale_price}" data-carton="${p.kg_per_carton}">${p.name}</option>`).join('');
            let unitOpts = units.map(u => `<option value="${u.id}" data-name="${u.name}" data-factor="${u.factor_to_base}">${u.name}</option>`).join('');

            row.innerHTML = `
                <td class="p-2 text-right">
                    <select class="prod-select p-2 bg-slate-800 border border-slate-700 rounded-lg text-white w-full" onchange="onProductChange(this)">
                        ${prodOpts}
                    </select>
                </td>
                <td class="p-2">
                    <select class="unit-select p-2 bg-slate-800 border border-slate-700 rounded-lg text-white w-full" onchange="calculate()">
                        ${unitOpts}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" step="any" value="1" min="0.01" class="qty-input p-2 bg-slate-800 border border-slate-700 rounded-lg text-white font-mono w-24 text-center" oninput="calculate()">
                </td>
                <td class="p-2">
                    <input type="number" step="any" value="0" class="price-input p-2 bg-slate-800 border border-slate-700 rounded-lg text-white font-mono w-32 text-center" oninput="calculate()">
                </td>
                <td class="p-2">
                    <select class="condition-select p-2 bg-slate-800 border border-slate-700 rounded-lg text-xs">
                        <option value="normal" class="text-emerald-400 font-bold">ئاسایی (دەگەڕێتەوە کۆگا)</option>
                        <option value="expired" class="text-rose-400 font-bold">بەسەرچوو (ناگەڕێتەوە کۆگا)</option>
                        <option value="damaged" class="text-amber-400 font-bold">تێکچوو / شکاو (ناگەڕێتەوە)</option>
                    </select>
                </td>
                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr">$0.00</td>
                <td class="p-2">
                    <button type="button" onclick="this.closest('tr').remove(); calculate();" class="text-rose-400 hover:text-rose-300">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            table.appendChild(row);
            calculate();
        }

        function onProductChange(select) {
            const row = select.closest('tr');
            const selected = select.options[select.selectedIndex];
            const usd = parseFloat(selected.getAttribute('data-price')) || 0;
            row.querySelector('.price-input').value = select.value ? suggestedPrice(usd) : 0;
            calculate();
        }

        function calculate() {
            let grandTotal = 0;
            let totalKg = 0;
            const rows = document.querySelectorAll('.item-row');
            document.getElementById('empty-row').classList.toggle('hidden', rows.length > 0);

            rows.forEach(row => {
                const prodSelect = row.querySelector('.prod-select');
                
                // ئەگەر کاڵا هەڵنەبژێردرابێت، هەژماری بۆ ناکەین و دەینێرینەوە 0
                if (!prodSelect.value) {
                    row.querySelector('.row-total').innerText = fmt(0);
                    return; 
                }

                const selectedProd = prodSelect.options[prodSelect.selectedIndex];
                const kgPerCarton = parseFloat(selectedProd.getAttribute('data-carton')) || 1;

                const unitSelect = row.querySelector('.unit-select');
                const unitName = (unitSelect.options[unitSelect.selectedIndex]?.getAttribute('data-name') || '').toLowerCase();
                const unitFactor = parseFloat(unitSelect.options[unitSelect.selectedIndex]?.getAttribute('data-factor')) || 1;

                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;

                let factor = unitFactor;
                if (unitName.includes('کارتۆن') || unitName.includes('carton')) {
                    factor = kgPerCarton;
                } else if (unitName.includes('تەن') || unitName.includes('ton')) {
                    factor = 1000;
                }

                const lineTotal = qty * price * factor;
                grandTotal += lineTotal;
                totalKg += (qty * factor);

                row.querySelector('.row-total').innerText = fmt(lineTotal);
            });

            document.getElementById('grand-total').innerText = fmt(grandTotal);
            document.getElementById('total-weight').innerText = totalKg.toLocaleString() + ' کگ';
        }

        function submitReturn() {
            const rows = document.querySelectorAll('.item-row');
            let items = [];

            // تەنها ئەو ڕیزانە کۆبکەرەوە کە کاڵایان هەڵبژێردراوە
            rows.forEach(row => {
                const prodSelect = row.querySelector('.prod-select');
                
                if (prodSelect.value) { // تەنها ئەگەر کاڵا هەڵبژێردرابێت
                    items.push({
                        product_id: prodSelect.value,
                        unit_id: row.querySelector('.unit-select').value,
                        quantity: row.querySelector('.qty-input').value,
                        unit_price: row.querySelector('.price-input').value,
                        condition_type: row.querySelector('.condition-select').value
                    });
                }
            });

            // ئەگەر هەموو ڕیزەکان بەتاڵ بوون، ئاگاداری بکەرەوە
            if (items.length === 0) {
                alert('تکایە لانیکەم یەک کاڵا هەڵبژێرە و پڕی بکەرەوە بۆ ئەوەی وەسڵەکە تۆمار بکرێت!');
                return;
            }

            fetch(@json(route('returns.store')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customer_id: document.getElementById('customer-select').value || null,
                    refund_type: document.getElementById('refund-type').value,
                    created_at: document.getElementById('return-date').value,
                    currency: currentCurrency,
                    items: items // تەنها ئەو ڕیزانەی کە کاڵایان تێدایە دەنێردرێن
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = @json(route('returns.index'));
                } else {
                    alert('هەڵە: ' + (data.error || ''));
                }
            })
            .catch(err => alert('کێشەیەک لە سێرڤەر ڕوویدا'));
        }

        calculate();
    </script>

</body>
</html>