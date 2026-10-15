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
        body { font-family: 'Noto Sans Arabic', sans-serif; overflow-x: hidden !important; }
        html { overflow-x: hidden !important; }
        .pcard { transition: border-color .15s, background .15s, transform .08s; }
        .pcard:hover { background: rgba(51, 65, 85, .8); border-color: #10b981; }
        .pcard:active { transform: scale(.97); }
        .flash { animation: flash .6s ease; }
        @keyframes flash { 0% { background: rgba(16, 185, 129, .35); } 100% { background: transparent; } }
        .sc::-webkit-scrollbar { width: 5px; height: 5px; }
        .sc::-webkit-scrollbar-thumb { background: #334155; border-radius: 9px; }
        /* بێ زووم لە ئایفۆن */
        @media (max-width: 767px) { #cartList select, #cartList input { font-size: 16px; } }
    </style>
    @php
    $curCurrency = old('currency', $purchase->currency ?? 'IQD');
    $curRate = old('exchange_rate', $purchase->exchange_rate ?? ($setting->exchange_rate ?? 1500));
    $curPayType = old('payment_type', $purchase->payment_type ?? 'cash');
    $curPaid = old('paid_amount', $purchase->paid_amount ?? 0);
    $curNo = old('purchase_no', $purchase->purchase_no ?? $purchase->invoice_no);
    $curDate = old('created_at', $purchase->created_at ? $purchase->created_at->format('Y-m-d') : ($purchase->purchase_date ?? date('Y-m-d')));

    // کاڵاکانی ناو وەسڵەکە (نرخی ١ کیلۆ / ١ کارتۆن بە دراوی وەسڵەکە)
    $initItems = $purchase->details->map(function ($d) {
        $uName = mb_strtolower(trim($d->unit->name ?? ''));
        if (str_contains($uName, 'کارتۆن') || str_contains($uName, 'carton')) {
            $f = (float) (($d->product->kg_per_carton ?? 1) ?: 1);
        } elseif (str_contains($uName, 'تەن') || str_contains($uName, 'ton')) {
            $f = 1000;
        } else {
            $f = (float) (($d->unit->factor_to_base ?? 1) ?: 1);
        }
        return [
            'product_id' => $d->product_id,
            'unit_id'    => $d->unit_id,
            'quantity'   => (float) $d->quantity,
            'buy_price'  => round($d->unit_buy_price / ($f ?: 1), 4),
        ];
    })->values();
    @endphp
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen p-2 sm:p-4">

    <div class="max-w-7xl mx-auto space-y-3">

        <div class="flex flex-wrap justify-between items-center gap-2 bg-slate-800 p-3 rounded-2xl border border-slate-700">
            <h1 class="text-sm sm:text-lg font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                دەستکاریکردنی وەسڵی (<span class="font-mono text-amber-400">{{ $purchase->purchase_no ?? $purchase->invoice_no }}</span>)
            </h1>
            <a href="{{ route('purchases.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 sm:px-4 py-2 rounded-xl transition">
                <i class="fa-solid fa-arrow-right"></i> گەڕانەوە
            </a>
        </div>

        @if(session('error'))
        <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-400"></i> {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3 rounded-xl text-xs font-bold space-y-1">
            @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
        </div>
        @endif

        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchaseForm" onsubmit="return validatePurchaseForm(event)" class="space-y-3" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- زانیاری وەسڵ --}}
            <div class="bg-slate-800 p-3 rounded-2xl border border-slate-700 grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="col-span-2 lg:col-span-1">
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">ژمارەی پسوولەی کڕین <span class="text-rose-400">*</span></label>
                    <input type="text" name="purchase_no" id="purchase_no" value="{{ $curNo }}" required maxlength="100"
                        class="w-full p-2.5 rounded-xl border {{ $errors->has('purchase_no') ? 'border-rose-500' : 'border-slate-600' }} bg-slate-700 text-white text-base sm:text-sm font-mono focus:outline-none focus:border-blue-500" dir="ltr" style="text-align:right;">
                </div>

                <div class="col-span-2 lg:col-span-1">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-[11px] font-bold text-slate-300">شوێنی کڕین (دابینکەر):</label>
                        
                    </div>
                    <select name="supplier_id" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-sm focus:outline-none focus:border-blue-500">
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ old('supplier_id', $purchase->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">بەرواری وەسڵ:</label>
                    <input type="date" name="created_at" value="{{ $curDate }}" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-sm font-mono focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">دراوی وەسڵ:</label>
                    <div class="flex items-center gap-1.5 bg-slate-700 p-1 rounded-xl">
                        <button type="button" onclick="setCurrency('USD')" id="btn-cur-usd" class="flex-1 py-2 sm:py-1.5 rounded-lg text-xs font-bold transition-colors">دۆلار</button>
                        <button type="button" onclick="setCurrency('IQD')" id="btn-cur-iqd" class="flex-1 py-2 sm:py-1.5 rounded-lg text-xs font-bold transition-colors">دینار</button>
                    </div>
                    <input type="hidden" name="currency" id="currency_input" value="{{ $curCurrency }}">
                </div>

                <div id="exchangeRateBox" class="col-span-2 lg:col-span-4 hidden">
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">نرخی ئاڵوگۆڕی دۆلار (١ دۆلار = چ دینار):</label>
                    <input type="number" step="any" min="1" name="exchange_rate" id="exchange_rate_input" value="{{ $curRate }}" class="w-full p-2.5 rounded-xl border border-amber-600 bg-slate-700 text-white text-base sm:text-sm font-mono focus:outline-none focus:border-amber-500">
                    <p class="text-[10px] text-amber-400 mt-1">ئەم نرخە بۆ گۆڕینی نرخی کڕین بۆ دۆلار بەکار دەهێنرێت پێش پاشەکەوتکردن</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-3 items-start">

                {{-- کاڵاکان: کلیک بکە بۆ زیادکردن --}}
                <section class="lg:col-span-3 bg-slate-800 p-3 rounded-2xl border border-slate-700 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-boxes-stacked text-emerald-400"></i> کاڵاکان <span class="text-[10px] font-normal text-slate-400">(کلیک بکە بۆ زیادکردن)</span></h2>
                        <span id="pCount" class="font-mono text-[10px] bg-slate-700 px-2 py-0.5 rounded">{{ $products->count() }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="pSearch" oninput="filterProducts()" onkeydown="searchKey(event)" placeholder="گەڕان بە ناو یان کۆد..." class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-xs focus:outline-none focus:border-emerald-500">
                        <select id="pCat" onchange="filterProducts()" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-xs focus:outline-none">
                            <option value="all">هەموو کاتیگۆرییەکان</option>
                            @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>

                    <div class="max-h-[55vh] lg:max-h-[68vh] overflow-y-auto sc pl-1">
                        <div id="pGrid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2">
                            @foreach($products as $p)
                            @php
                                $isC = ($p->sell_type ?? 'weight') === 'carton';
                                $stk = (float) ($p->stock_kg ?? $p->stock ?? 0);
                                $per = $isC ? 'کارتۆن' : 'کگ';
                            @endphp
                            <button type="button" onclick="addProduct({{ $p->id }})"
                                class="pcard relative text-right bg-slate-900/60 border border-slate-700 rounded-xl p-2 flex flex-col gap-1 min-h-[84px]"
                                data-pid="{{ $p->id }}" data-name="{{ mb_strtolower($p->name) }}" data-code="{{ mb_strtolower($p->code ?? '') }}" data-cat="{{ $p->category_id }}">
                                <span class="cart-badge hidden absolute top-1.5 left-1.5 bg-emerald-500 text-slate-900 font-mono font-extrabold text-[10px] px-1.5 rounded-full"></span>
                                <span class="font-bold text-white text-xs leading-4 pl-6">{{ $p->name }}</span>
                                <span class="font-mono text-[10px] text-emerald-400" dir="ltr" style="text-align:right">{{ $p->code }}</span>
                                <span class="mt-auto flex items-center justify-between gap-1 text-[10px]">
                                    <span class="px-1.5 rounded {{ $isC ? 'bg-amber-500/15 text-amber-300' : 'bg-sky-500/15 text-sky-300' }} font-bold">{{ $isC ? 'کارتۆن' : 'کیلۆ' }}</span>
                                    <span class="font-mono {{ $stk <= 0 ? 'text-rose-400' : 'text-slate-300' }}" dir="ltr">{{ rtrim(rtrim(number_format($stk, 2), '0'), '.') ?: '0' }} {{ $per }}</span>
                                </span>
                            </button>
                            @endforeach
                        </div>
                        <div id="pEmpty" class="hidden p-6 text-center text-xs text-slate-500">هیچ کاڵایەک نەدۆزرایەوە</div>
                    </div>
                </section>

                {{-- وەسڵ --}}
                <section class="lg:col-span-2 bg-slate-800 p-3 rounded-2xl border border-slate-700 space-y-3 lg:sticky lg:top-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-white flex items-center gap-2"><i class="fa-solid fa-receipt text-blue-400"></i> کاڵاکانی ئەم وەسڵە <span id="cartCount" class="font-mono text-[10px] bg-slate-700 px-2 py-0.5 rounded">0</span></h2>
                    </div>

                    <div id="emptyHint" class="border border-dashed border-slate-600 rounded-xl p-6 text-center text-xs text-slate-500">
                        <i class="fa-solid fa-hand-pointer text-lg mb-2 block"></i> کلیک لە کاڵایەک بکە بۆ ئەوەی بچێتە ناو وەسڵەکە
                    </div>
                    <div id="cartList" class="space-y-2 max-h-[40vh] lg:max-h-[38vh] overflow-y-auto sc pl-1"></div>

                    <div class="pt-2 border-t border-slate-700 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-300">کۆی گشتی وەسڵ:</span>
                        <span id="grandTotal" class="text-emerald-400 font-mono font-black text-lg" dir="ltr">$0.00</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">جۆری پارەدان:</label>
                            <select name="payment_type" id="payment_type" onchange="togglePaid()" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-xs focus:outline-none focus:border-blue-500">
                                <option value="cash" {{ $curPayType === 'cash' ? 'selected' : '' }}>نەقد</option>
                                <option value="debt" {{ $curPayType === 'debt' ? 'selected' : '' }}>قەرز</option>
                            </select>
                        </div>
                        <div id="paidAmountBox" class="hidden">
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">بڕی پارەی دراو:</label>
                            <input type="number" step="any" min="0" name="paid_amount" id="paid_amount" value="{{ $curPaid }}" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-base sm:text-xs font-mono focus:outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-2xl transition text-sm shadow-lg shadow-amber-900/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check"></i> خەزنکردنی دەستکارییەکان و ڕێکخستنەوەی کۆگا
                    </button>
                </section>
            </div>
        </form>
    </div>

    <script>
        const products = @json($products);
        const units = @json($units);
        const initialItems = @json(old('items') ?? $initItems);
        let rowCount = 0;
        let currentCurrency = document.getElementById('currency_input').value || 'IQD';

        const $ = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const rate = () => parseFloat($('exchange_rate_input').value) || 1500;
        const isCarton = p => (p.sell_type || 'weight') === 'carton';
        const perText = p => isCarton(p) ? 'کارتۆن' : 'کیلۆ';

        /* ---------- دراو ---------- */
        function setCurrency(cur) {
            currentCurrency = cur;
            $('currency_input').value = cur;
            const on = 'flex-1 py-2 sm:py-1.5 rounded-lg text-xs font-bold bg-emerald-500 text-white transition-colors';
            const off = 'flex-1 py-2 sm:py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 transition-colors';
            $('btn-cur-usd').className = cur === 'USD' ? on : off;
            $('btn-cur-iqd').className = cur === 'IQD' ? on : off;
            $('exchangeRateBox').classList.toggle('hidden', cur === 'USD');
            document.querySelectorAll('.cur-lbl').forEach(e => e.textContent = cur === 'USD' ? '$' : 'IQD');
            refreshAutoPrices();
            calcTotal();
        }

        // نرخی خۆکار (لە نرخی ئێستای کاڵا) بەپێی دراو دەگۆڕدرێت؛ نرخی بە دەست نووسراو ناگۆڕدرێت
        function refreshAutoPrices() {
            document.querySelectorAll('.purchase-item-row').forEach(row => {
                if (row.dataset.auto !== '1') return;
                const usd = parseFloat(row.dataset.baseUsd) || 0;
                const v = currentCurrency === 'USD' ? usd : usd * rate();
                row.querySelector('.price-input').value = parseFloat(v.toFixed(currentCurrency === 'USD' ? 4 : 2));
            });
        }

        function togglePaid() {
            $('paidAmountBox').classList.toggle('hidden', $('payment_type').value !== 'debt');
        }

        /* ---------- یەکە ---------- */
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

        function defaultUnitId(p) {
            const find = re => units.find(u => re.test((u.name || '').toLowerCase()));
            const u = isCarton(p) ? find(/کارتۆن|carton/) : find(/کیلۆ|کگ|kg/);
            return (u || units[0] || {}).id;
        }

        /* ---------- زیادکردنی کاڵا بە کلیک ---------- */
        function addProduct(pid, opt = {}) {
            const p = products.find(x => x.id == pid);
            if (!p) return;

            if (!opt.force) {
                const exist = document.querySelector(`.purchase-item-row[data-pid="${pid}"]`);
                if (exist) {   // هەمان کاڵا: تەنها بڕەکەی یەک زیاد دەکات
                    const q = exist.querySelector('.qty-input');
                    q.value = (parseFloat(q.value) || 0) + 1;
                    exist.classList.remove('flash'); void exist.offsetWidth; exist.classList.add('flash');
                    calcTotal();
                    return;
                }
            }

            const rowId = rowCount++;
            const baseUsd = parseFloat(p.base_buy_price ?? p.buy_price ?? 0) || 0;
            const unitId = opt.unit_id || defaultUnitId(p);
            const unitOptions = units.map(u => `<option value="${u.id}" ${u.id == unitId ? 'selected' : ''}>${esc(u.name)}</option>`).join('');
            const stock = parseFloat(p.stock_kg ?? p.stock ?? 0) || 0;

            const row = document.createElement('div');
            row.id = `row-${rowId}`;
            row.className = 'purchase-item-row bg-slate-900/60 border border-slate-700 rounded-xl p-2.5 space-y-2';
            row.dataset.pid = p.id;
            row.dataset.baseUsd = baseUsd;
            row.dataset.auto = opt.price === undefined ? '1' : '0';

            row.innerHTML = `
                <input type="hidden" class="prod-select" name="items[${rowId}][product_id]" value="${p.id}">
                <div class="flex justify-between items-start gap-2">
                    <div class="min-w-0">
                        <div class="font-bold text-white text-xs leading-4">${esc(p.name)}</div>
                        <div class="font-mono text-[10px] text-slate-500" dir="ltr" style="text-align:right">${esc(p.code)} · ${stock} ${isCarton(p) ? 'کارتۆن' : 'کگ'}</div>
                    </div>
                    <button type="button" onclick="removeRow(${rowId})" class="text-rose-400 hover:text-rose-300 text-sm px-1 shrink-0" title="لابردن"><i class="fa-solid fa-trash"></i></button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div><label class="block text-[10px] text-slate-400 mb-0.5">یەکە</label>
                        <select name="items[${rowId}][unit_id]" onchange="calcTotal()" class="unit-select w-full p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white text-xs">${unitOptions}</select></div>
                    <div><label class="block text-[10px] text-slate-400 mb-0.5">بڕ</label>
                        <input type="number" step="any" min="0.01" name="items[${rowId}][quantity]" value="${opt.qty ?? 1}" oninput="calcTotal()" class="qty-input w-full p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono text-xs"></div>
                    <div><label class="block text-[10px] text-slate-400 mb-0.5">نرخی ١ ${perText(p)} (<span class="cur-lbl">${currentCurrency === 'USD' ? '$' : 'IQD'}</span>)</label>
                        <input type="number" step="any" min="0" name="items[${rowId}][buy_price]" value="0" oninput="this.closest('.purchase-item-row').dataset.auto='0'; calcTotal()" class="price-input w-full p-1.5 bg-slate-700 border border-slate-600 rounded-lg text-white font-mono text-xs"></div>
                </div>
                <div class="flex justify-between items-center bg-slate-950/50 rounded-lg px-2.5 py-1.5">
                    <span class="text-[10px] text-slate-400">کۆی پارە</span>
                    <span class="row-total font-mono font-bold text-emerald-400 text-sm" dir="ltr">0</span>
                </div>`;

            if (opt.append) $('cartList').appendChild(row); else $('cartList').prepend(row);
            if (opt.price !== undefined) row.querySelector('.price-input').value = opt.price;
            else refreshAutoPrices();
            row.classList.add('flash');
            calcTotal();
        }

        function removeRow(id) {
            const row = $(`row-${id}`);
            if (row) { row.remove(); calcTotal(); }
        }

        /* ---------- کۆ ---------- */
        function fmtMoney(v) {
            if (currentCurrency === 'USD') return '$' + v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            return Math.round(v).toLocaleString() + ' IQD';
        }

        function calcTotal() {
            let total = 0;
            const rows = document.querySelectorAll('.purchase-item-row');
            const qtyByPid = {};
            rows.forEach(row => {
                const pid = row.querySelector('.prod-select').value;
                const unitId = row.querySelector('.unit-select').value;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const sub = qty * (price * getUnitFactor(unitId, pid));
                row.querySelector('.row-total').innerText = fmtMoney(sub);
                total += sub;
                qtyByPid[pid] = (qtyByPid[pid] || 0) + qty;
            });
            $('grandTotal').innerText = fmtMoney(total);
            $('cartCount').innerText = rows.length;
            $('emptyHint').classList.toggle('hidden', rows.length > 0);

            document.querySelectorAll('#pGrid .pcard').forEach(c => {
                const b = c.querySelector('.cart-badge'), q = qtyByPid[c.dataset.pid];
                b.classList.toggle('hidden', !q);
                if (q) b.textContent = parseFloat(q.toFixed(2));
                c.classList.toggle('border-emerald-500', !!q);
            });
            return total;
        }
        $('exchange_rate_input').addEventListener('input', () => { refreshAutoPrices(); calcTotal(); });

        /* ---------- گەڕان ---------- */
        function filterProducts() {
            const q = $('pSearch').value.toLowerCase().trim(), cat = $('pCat').value; let n = 0;
            document.querySelectorAll('#pGrid .pcard').forEach(c => {
                const ok = (!q || c.dataset.name.includes(q) || c.dataset.code.includes(q)) && (cat === 'all' || c.dataset.cat == cat);
                c.style.display = ok ? '' : 'none'; if (ok) n++;
            });
            $('pCount').textContent = n;
            $('pEmpty').classList.toggle('hidden', n > 0);
        }
        // Enter: یەکەم کاڵای دەرکەوتوو زیاد دەکات (بۆ بارکۆد سکانەر)
        function searchKey(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const first = [...document.querySelectorAll('#pGrid .pcard')].find(c => c.style.display !== 'none');
            if (first) { addProduct(first.dataset.pid); $('pSearch').select(); }
        }

        /* ---------- پشکنین ---------- */
        function validatePurchaseForm(e) {
            if (!$('purchase_no').value.trim()) {
                alert('تکایە ژمارەی پسوولەی کڕین بنووسە.'); $('purchase_no').focus(); e.preventDefault(); return false;
            }
            const rows = document.querySelectorAll('.purchase-item-row');
            if (rows.length === 0) {
                alert('تکایە لانیکەم یەک کاڵا هەڵبژێرە بۆ ئەوەی وەسڵەکە تۆمار بکرێت!'); e.preventDefault(); return false;
            }
            let hasError = false;
            rows.forEach(row => {
                const qi = row.querySelector('.qty-input'), pi = row.querySelector('.price-input');
                qi.style.border = ''; pi.style.border = '';
                if ((parseFloat(qi.value) || 0) <= 0) { qi.style.border = '2px solid red'; hasError = true; }
                if ((parseFloat(pi.value) || 0) <= 0) { pi.style.border = '2px solid red'; hasError = true; }
            });
            if (hasError) {
                alert('تکایە نرخی کڕین و بڕی کاڵاکان بە دروستی پڕبکەرەوە! (خانە سوورەکان)'); e.preventDefault(); return false;
            }
            return true;
        }

        /* ---------- دەستپێک ---------- */
        setCurrency(currentCurrency);
        togglePaid();
        // کاڵاکانی ناو وەسڵەکە (یان ئەوانەی پێش هەڵەکە نووسرابوون)
        Object.values(initialItems || {}).forEach(it => {
            if (it && it.product_id) addProduct(it.product_id, { force: true, append: true, qty: it.quantity, price: it.buy_price, unit_id: it.unit_id });
        });
        calcTotal();
    </script>
</body>

</html>