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

            {{-- بەشی سەرەوەی وەسڵ: شوێن و بەروار و دراو --}}
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- شوێنی کڕین + دوگمەی بچووکی دابینکەران -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-slate-300">شوێنی کڕین (کۆمپانیا/دابینکەر):</label>
                        <button type="button" onclick="openSuppliersModal()" 
                                class="text-[10px] text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-bold transition-colors">
                            <i class="fa-solid fa-plus-circle"></i> بەڕێوەبردن
                        </button>
                    </div>
                    <select name="supplier_id" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm focus:outline-none focus:border-blue-500">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- بەرواری وەسڵ -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">بەرواری وەسڵ:</label>
                    <input type="date" name="created_at" value="{{ date('Y-m-d') }}" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm font-mono focus:outline-none focus:border-blue-500">
                </div>

                <!-- دراوی وەسڵ -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">دراوی وەسڵ:</label>
                    <div class="flex items-center gap-1.5 bg-slate-700 p-1 rounded-xl">
                        <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors">دۆلار</button>
                        <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors">دینار</button>
                    </div>
                    <input type="hidden" name="currency" id="currency_input" value="USD">
                </div>

                <!-- نرخی ئاڵوگۆڕ -->
                <div id="exchangeRateBox" class="md:col-span-3 hidden">
                    <label class="block text-xs font-bold text-slate-300 mb-1">نرخی ئاڵوگۆڕی دۆلار (١ دۆلار = چ دینار):</label>
                    <input type="number" step="any" min="1" name="exchange_rate" id="exchange_rate_input" value="{{ $setting->exchange_rate ?? 1500 }}" class="w-full p-2.5 rounded-xl border border-amber-600 bg-slate-700 text-white text-sm font-mono focus:outline-none focus:border-amber-500">
                    <p class="text-[10px] text-amber-400 mt-1">ئەم نرخە بۆ گۆڕینی نرخی کڕین بۆ دۆلار بەکار دەهێنرێت پێش پاشەکەوتکردن</p>
                </div>
            </div>

            {{-- خشتەی کاڵاکان --}}
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
                                <th class="p-2.5 w-36">نرخی یەکە (<span id="priceLabel">$</span>)</th>
                                <th class="p-2.5 w-36">کۆی پارە</th>
                                <th class="p-2.5 text-center w-12">لابردن</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700" id="tableBody">
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-700 flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-300">کۆی گشتی وەسڵ:</span>
                    <span id="grandTotal" class="text-emerald-400 font-mono font-black text-xl" dir="ltr">$0.00</span>
                </div>
            </div>

            {{-- پارەدان --}}
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">جۆری پارەدان:</label>
                    <select name="payment_type" id="payment_type" onchange="togglePaid()" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm focus:outline-none focus:border-blue-500">
                        <option value="cash">نەقد</option>
                        <option value="debt">قەرز</option>
                    </select>
                </div>
                <div id="paidAmountBox" class="hidden">
                    <label class="block text-xs font-bold text-slate-300 mb-1">بڕی پارەی دراو:</label>
                    <input type="number" step="any" min="0" name="paid_amount" id="paid_amount" value="0" autocomplete="off" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm font-mono focus:outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl transition text-base shadow-lg shadow-emerald-900/30 flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> تەواوکردن و تۆمارکردنی وەسڵی کڕین بۆ ناو کۆگا
            </button>
        </form>

    </div>

    <!-- ============================================ -->
    <!-- مۆداڵی بەڕێوەبردنی دابینکەران -->
    <!-- ============================================ -->
    <div id="suppliersModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-2 md:p-4 z-[100]">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-6xl h-[92vh] overflow-hidden flex flex-col shadow-2xl">
            
            <!-- سەرپەڕەی مۆداڵ -->
            <div class="flex justify-between items-center p-3 border-b border-slate-700 shrink-0 bg-slate-900/50">
                <h3 class="text-sm font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-truck-field text-emerald-400"></i>
                    بەڕێوەبردنی دابینکەران
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshSuppliersIframe()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-rotate"></i> نوێکردنەوە
                    </button>
                    <button type="button" onclick="closeSuppliersModal()" 
                            class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            
            <!-- شاشەی بارکردن -->
            <div id="suppliersLoading" class="flex-1 flex items-center justify-center bg-slate-900">
                <div class="text-center">
                    <i class="fa-solid fa-spinner fa-spin text-emerald-400 text-3xl mb-3"></i>
                    <p class="text-slate-400 text-xs font-bold">چاوەڕوان بە...</p>
                </div>
            </div>
            
            <!-- iframe ی دابینکەران -->
            <iframe id="suppliersIframe" 
                    src="" 
                    onload="hideSuppliersLoading()"
                    class="flex-1 w-full bg-white hidden"
                    frameborder="0"></iframe>
        </div>
    </div>

    <script>
        const products = @json($products);
        const units = @json($units);
        let rowCount = 0;
        let currentCurrency = 'USD';
        let currentRate = {{ $setting->exchange_rate ?? 1500 }};

        function setCurrency(currency) {
            currentCurrency = currency;
            document.getElementById('currency_input').value = currency;
            
            const btnIqd = document.getElementById('btn-cur-iqd');
            const btnUsd = document.getElementById('btn-cur-usd');
            const exchangeBox = document.getElementById('exchangeRateBox');
            const priceLabel = document.getElementById('priceLabel');

            if (currency === 'USD') {
                btnUsd.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors';
                btnIqd.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors';
                exchangeBox.classList.add('hidden');
                priceLabel.innerText = '$';
            } else {
                btnIqd.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors';
                btnUsd.className = 'flex-1 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors';
                exchangeBox.classList.remove('hidden');
                priceLabel.innerText = 'IQD';
            }
            calcTotal();
        }

        function togglePaid() {
            const type = document.getElementById('payment_type').value;
            const box = document.getElementById('paidAmountBox');
            if (type === 'debt') {
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
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
                    <input type="number" step="any" min="0" name="items[${rowId}][buy_price]" value="0" oninput="calcTotal()" required autocomplete="off" class="w-full p-2 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono price-input">
                </td>
                <td class="p-2 font-mono font-bold text-emerald-400 row-total" dir="ltr">$0.00</td>
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
            currentRate = parseFloat(document.getElementById('exchange_rate_input').value) || 1500;
            
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                const prodId = row.querySelector('.prod-select')?.value;
                const unitId = row.querySelector('.unit-select')?.value;
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                const pricePerBase = parseFloat(row.querySelector('.price-input')?.value) || 0;

                const factor = getUnitFactor(unitId, prodId);
                const sub = qty * (pricePerBase * factor);

                const symbol = currentCurrency === 'USD' ? '$' : '';
                const suffix = currentCurrency === 'IQD' ? ' IQD' : '';

                if (row.querySelector('.row-total')) {
                    row.querySelector('.row-total').innerText = symbol + sub.toLocaleString(undefined, {minimumFractionDigits: currentCurrency === 'USD' ? 2 : 0, maximumFractionDigits: 2}) + suffix;
                }
                total += sub;
            });
            
            const symbol = currentCurrency === 'USD' ? '$' : '';
            const suffix = currentCurrency === 'IQD' ? ' IQD' : '';
            document.getElementById('grandTotal').innerText = symbol + total.toLocaleString(undefined, {minimumFractionDigits: currentCurrency === 'USD' ? 2 : 0, maximumFractionDigits: 2}) + suffix;
            return total;
        }

        document.getElementById('exchange_rate_input').addEventListener('input', calcTotal);

        function validatePurchaseForm(e) {
            const rows = document.querySelectorAll('#tableBody tr');
            if (rows.length === 0) {
                alert('وەسڵ ناتوانرێت بەتاڵ بێت!');
                e.preventDefault();
                return false;
            }

            let hasZero = false;
            rows.forEach(row => {
                const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
                if (price <= 0 || qty <= 0) hasZero = true;
            });

            if (hasZero) {
                alert('تکایە نرخی کڕین و بڕی هەموو کاڵاکان بە دروستی پڕبکەرەوە.');
                e.preventDefault();
                return false;
            }

            if (calcTotal() <= 0) {
                alert('کۆی گشتی وەسڵ ناتوانێت 0 بێت.');
                e.preventDefault();
                return false;
            }

            return true;
        }

        addRow();

        // ============================================
        // مۆداڵی دابینکەران
        // ============================================
        function openSuppliersModal() {
            const modal = document.getElementById('suppliersModal');
            const iframe = document.getElementById('suppliersIframe');
            const loading = document.getElementById('suppliersLoading');
            
            // پیشاندانی مۆداڵ
            modal.classList.remove('hidden');
            
            // پیشاندانی شاشەی بارکردن
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            
            // بارکردنی لاپەڕەی دابینکەران لە iframe
            iframe.src = '{{ route("suppliers.index") }}';
        }

        function closeSuppliersModal() {
            const modal = document.getElementById('suppliersModal');
            const iframe = document.getElementById('suppliersIframe');
            
            // داخستنی مۆداڵ
            modal.classList.add('hidden');
            
            // پاککردنەوەی iframe
            iframe.src = '';
            
            // نوێکردنەوەی dropdown ی دابینکەران
            refreshParentSuppliersDropdown();
        }

        function refreshSuppliersIframe() {
            const iframe = document.getElementById('suppliersIframe');
            const loading = document.getElementById('suppliersLoading');
            
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            
            // دووبارە بارکردنەوە
            iframe.src = iframe.src;
        }

        function hideSuppliersLoading() {
            const loading = document.getElementById('suppliersLoading');
            const iframe = document.getElementById('suppliersIframe');
            
            loading.classList.add('hidden');
            iframe.classList.remove('hidden');
        }

        // نوێکردنەوەی dropdown ی دابینکەران لە فۆرمی کڕین
        function refreshParentSuppliersDropdown() {
            // وەرگرتنی لیستی دابینکەران لە iframe
            try {
                const iframe = document.getElementById('suppliersIframe');
                
                // دواتر لە database بخوازە
                fetch('/api/suppliers-list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(suppliers => {
                    const select = document.querySelector('select[name="supplier_id"]');
                    if (!select || !suppliers.length) return;
                    
                    const currentValue = select.value;
                    
                    select.innerHTML = suppliers.map(s => 
                        `<option value="${s.id}">${s.name}</option>`
                    ).join('');
                    
                    // هەوڵدان بۆ گەڕاندنەوەی هەڵبژاردنی پێشوو
                    if (currentValue) {
                        const exists = Array.from(select.options).some(o => o.value === currentValue);
                        if (exists) select.value = currentValue;
                    }
                })
                .catch(err => {
                    // ئەگەر API نەبوو، تەنها لاپەڕەکە نوێ بکەرەوە
                    console.log('Could not refresh suppliers:', err);
                });
            } catch(e) {
                console.error(e);
            }
        }

        // داخستن بە ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('suppliersModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeSuppliersModal();
                }
            }
        });
    </script>
</body>
</html>