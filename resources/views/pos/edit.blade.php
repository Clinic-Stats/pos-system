<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دەستکاریکردنی وەسڵ - {{ $sale->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

    <header class="bg-slate-800 border-b border-slate-700 px-6 py-3 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-pen-to-square text-2xl text-amber-400"></i>
            <h1 class="text-lg font-bold text-white">دەستکاریکردنی وەسڵی ({{ $sale->invoice_no }})</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition">
                گەڕانەوە
            </a>
            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوە؟ کاڵاکان دەگەڕێنەوە سەر کۆگا')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition flex items-center gap-1">
                    <i class="fa-solid fa-trash"></i> سڕینەوەی وەسڵ
                </button>
            </form>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden p-4 gap-4">
        
        <div class="flex-1 flex flex-col bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden p-4">
            <h2 class="text-sm font-bold text-slate-300 mb-3">زیادکردنی کاڵا بۆ ناو وەسڵ</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 overflow-y-auto pr-1">
                @foreach($products as $product)
                <div onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->base_sale_price }})"
                     class="bg-slate-700/60 hover:bg-slate-700 p-3 rounded-xl border border-slate-600 cursor-pointer transition flex flex-col justify-between select-none">
                    <div>
                        <span class="text-[10px] font-mono text-blue-400 bg-blue-950/60 px-2 py-0.5 rounded">{{ $product->code }}</span>
                        <h3 class="font-bold text-white text-sm mt-1">{{ $product->name }}</h3>
                        <span class="text-xs text-slate-400">{{ $product->category->name ?? '' }}</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-600 flex justify-between items-center">
                        <span class="text-xs text-slate-300">نرخ:</span>
                        <span class="font-mono font-bold text-emerald-400 text-xs" dir="ltr">{{ number_format($product->base_sale_price) }} IQD</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="w-96 bg-slate-800 rounded-2xl border border-slate-700 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-slate-700">
                <h2 class="font-bold text-white text-base">ناوەڕۆکی وەسڵ</h2>
            </div>

            <div class="p-3 bg-slate-700/40 border-b border-slate-700 space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">بەروار و کاتی وەسڵ:</label>
                    <input type="datetime-local" id="sale-date" value="{{ $sale->created_at ? $sale->created_at->format('Y-m-d\TH:i') : date('Y-m-d\TH:i') }}" class="w-full p-2 border border-slate-600 rounded-xl text-xs bg-slate-800 text-white font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">کڕیار:</label>
                    <select id="customer-select" class="w-full p-2 border border-slate-600 rounded-xl text-xs bg-slate-800 text-white">
                        <option value="">کڕیاری گشتی (نەقد)</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ $sale->customer_id == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4 text-xs font-bold text-slate-200">
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="pay_type" value="cash" {{ $sale->payment_type == 'cash' ? 'checked' : '' }} onchange="toggleDebtInput(false)">
                        <span>نەقد</span>
                    </label>
                    <label class="flex items-center gap-1 cursor-pointer">
                        <input type="radio" name="pay_type" value="debt" {{ $sale->payment_type == 'debt' ? 'checked' : '' }} onchange="toggleDebtInput(true)">
                        <span class="text-amber-400">قەرز</span>
                    </label>
                </div>

                <div id="debt-field" class="{{ $sale->payment_type == 'debt' ? '' : 'hidden' }}">
                    <label class="block text-xs font-bold text-amber-400 mb-1">بڕی پارەی دراو:</label>
                    <input type="number" id="paid-amount" value="{{ $sale->paid_amount }}" placeholder="0" class="w-full p-2 border border-slate-600 rounded-xl font-mono bg-slate-800 text-white text-xs">
                </div>
            </div>

            <div id="cart-list" class="flex-1 overflow-y-auto p-3 space-y-2"></div>

            <div class="p-4 bg-slate-900/60 border-t border-slate-700 space-y-3">
                <div class="flex justify-between items-center text-sm font-bold">
                    <span class="text-slate-300">کۆی گشتی:</span>
                    <span id="grand-total" class="text-emerald-400 font-mono text-lg" dir="ltr">0 IQD</span>
                </div>
                <button onclick="updateInvoice()" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-xl transition">
                    خەزنکردنی گۆڕانکارییەکان
                </button>
            </div>
        </div>

    </div>

    <script>
        const availableUnits = @json($units);
        
        let cart = [
            @foreach($sale->details as $d)
            {
                product_id: {{ $d->product_id }},
                name: '{{ $d->product->name ?? "کاڵا" }}',
                base_price: {{ $d->product->base_sale_price ?? 0 }},
                quantity: {{ $d->quantity }},
                unit_id: {{ $d->unit_id }},
                factor: {{ $d->unit->factor_to_base ?? 1 }}
            },
            @endforeach
        ];

        function addToCart(productId, name, basePrice) {
            const existing = cart.find(i => i.product_id === productId);
            if (existing) {
                existing.quantity += 1;
            } else {
                const defaultUnit = availableUnits.find(u => u.factor_to_base == 1) || availableUnits[0];
                cart.push({
                    product_id: productId,
                    name: name,
                    base_price: basePrice,
                    quantity: 1,
                    unit_id: defaultUnit.id,
                    factor: defaultUnit.factor_to_base
                });
            }
            renderCart();
        }

        function updateQty(index, delta) {
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function updateUnit(index, unitId) {
            const unit = availableUnits.find(u => u.id == unitId);
            if (unit) {
                cart[index].unit_id = unit.id;
                cart[index].factor = unit.factor_to_base;
                renderCart();
            }
        }

        function toggleDebtInput(isDebt) {
            const field = document.getElementById('debt-field');
            const paidInput = document.getElementById('paid-amount');
            if (isDebt) {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
                paidInput.value = '';
            }
        }

        function renderCart() {
            const list = document.getElementById('cart-list');
            list.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.base_price * item.factor * item.quantity;
                total += itemTotal;

                let unitOptions = availableUnits.map(u => 
                    `<option value="${u.id}" ${u.id == item.unit_id ? 'selected' : ''}>${u.name}</option>`
                ).join('');

                list.innerHTML += `
                    <div class="bg-slate-700/40 p-2.5 rounded-xl border border-slate-600 text-xs space-y-2">
                        <div class="flex justify-between items-center font-bold text-white">
                            <span>${item.name}</span>
                            <span class="font-mono text-emerald-400" dir="ltr">${itemTotal.toLocaleString()} IQD</span>
                        </div>
                        <div class="flex justify-between items-center gap-2">
                            <div class="flex items-center gap-1 bg-slate-800 rounded-lg p-1 border border-slate-600">
                                <button onclick="updateQty(${index}, -1)" class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 font-bold">-</button>
                                <span class="px-2 font-mono font-bold">${item.quantity}</span>
                                <button onclick="updateQty(${index}, 1)" class="w-6 h-6 rounded bg-slate-700 hover:bg-slate-600 font-bold">+</button>
                            </div>
                            <select onchange="updateUnit(${index}, this.value)" class="p-1 rounded-lg bg-slate-800 border border-slate-600 text-xs text-white">
                                ${unitOptions}
                            </select>
                            <button onclick="cart.splice(${index}, 1); renderCart();" class="text-rose-400 hover:text-rose-300 ml-auto">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('grand-total').innerText = total.toLocaleString() + ' IQD';
        }

        function updateInvoice() {
            if (cart.length === 0) {
                alert('ناتوانی وەسڵ بە بەتاڵی دابنێیت');
                return;
            }

            const customerSelect = document.getElementById('customer-select');
            const customerId = (customerSelect && customerSelect.value !== "") ? customerSelect.value : null;
            const payType = document.querySelector('input[name="pay_type"]:checked').value;
            const paidAmt = document.getElementById('paid-amount').value || 0;
            const saleDate = document.getElementById('sale-date').value;

            fetch('/sales/{{ $sale->id }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    items: cart,
                    customer_id: customerId ? parseInt(customerId) : null,
                    payment_type: payType,
                    paid_amount: Number(paidAmt),
                    created_at: saleDate
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('وەسڵەکە بە سەرکەوتوویی نوێکرایەوە');
                    window.location.href = "{{ route('reports.index') }}";
                } else {
                    alert('هەڵە: ' + (data.error || ''));
                }
            })
            .catch(err => alert('هەڵەیەک لە سێرڤەر ڕوویدا'));
        }

        renderCart();
    </script>
</body>
</html>