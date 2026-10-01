<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی دابینکەران</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @php 
        $isEmbedded = request()->has('embedded'); 
    @endphp
    
    <style> 
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        html, body { overflow-x: hidden !important; max-width: 100% !important; }
        
        /* سکرۆڵباری تەنک و جوان */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { 
            background: #475569; 
            border-radius: 10px; 
        }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        
        /* کاتێک لە iframe دایە */
        @if($isEmbedded)
            body {
                padding: 10px !important;
                background: #0f172a;
                overflow-x: hidden !important;
            }
            .max-w-5xl {
                max-width: 100% !important;
            }
            /* سەرپەڕەی زۆر بچووکتر */
            .page-header {
                padding: 8px 12px !important;
                margin-bottom: 12px !important;
            }
        @endif
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- سەرپەڕە -->
        <div class="page-header flex flex-wrap justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700 gap-3">
            <h1 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-truck-field text-emerald-400"></i>
                بەڕێوەبردنی دابینکەران
            </h1>
            
            {{-- دوگمەکانی دەرەکی - تەنها کاتێک لە iframe نەبێت دەردەکەون --}}
            @if(!$isEmbedded)
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('reports.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-chart-pie"></i> ڕاپۆرتەکان
                </a>
                <a href="{{ route('purchases.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-cart-flatbed"></i> وەسڵی کڕین
                </a>
                <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                    <i class="fa-solid fa-cash-register"></i> POS
                </a>
            </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-600/20 border border-rose-500 text-rose-400 p-3 rounded-xl text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی زیادکردنی دابینکەری نوێ -->
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-700 pb-3">
                    <i class="fa-solid fa-square-plus text-emerald-400"></i>
                    زیادکردنی دابینکەری نوێ
                </h2>

                <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-3 text-xs" autocomplete="off">
                    @csrf

                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ناوی دابینکەر / کۆمپانیا:</label>
                        <input type="text" name="name" required autofocus autocomplete="off"
                               placeholder="بۆ نموونە: Active Halabja"
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ژمارەی مۆبایل:</label>
                        <input type="text" name="phone" autocomplete="off"
                               placeholder="07XX XXX XXXX"
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono focus:outline-none focus:border-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-bold mb-1">ناونیشان:</label>
                        <input type="text" name="address" autocomplete="off"
                               placeholder="شار، گەڕەک..."
                               class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-bold mb-1">تێبینی:</label>
                        <textarea name="note" rows="2" autocomplete="off"
                                  placeholder="تێبینی زیاتر..."
                                  class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white focus:outline-none focus:border-emerald-500 text-sm resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/30">
                        <i class="fa-solid fa-check"></i> تۆمارکردنی دابینکەر
                    </button>
                </form>
            </div>

            <!-- خشتەی دابینکەران -->
            <div class="lg:col-span-2 bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                    <h2 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-blue-400"></i>
                        لیستی دابینکەران
                    </h2>
                    <span class="text-[10px] font-mono font-bold bg-slate-700 px-2.5 py-1 rounded-lg text-slate-300">
                        کۆی گشتی: {{ $suppliers->count() ?? 0 }}
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-700/60">
                    <table class="w-full text-xs text-right text-slate-300">
                        <thead class="bg-slate-700/50 text-[11px] text-slate-400">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">ناو</th>
                                <th class="p-3">ژمارەی مۆبایل</th>
                                <th class="p-3">ناونیشان</th>
                                <th class="p-3 text-center">کردارەکان</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($suppliers ?? [] as $index => $supplier)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="p-3 font-mono text-slate-500">{{ $index + 1 }}</td>
                                <td class="p-3 font-bold text-white">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-[10px] font-black">
                                            {{ mb_substr($supplier->name, 0, 1) }}
                                        </div>
                                        {{ $supplier->name }}
                                    </div>
                                </td>
                                <td class="p-3 font-mono text-slate-400" dir="ltr">{{ $supplier->phone ?? '-' }}</td>
                                <td class="p-3 text-slate-400">{{ $supplier->address ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick="openEditSupplierModal({{ json_encode($supplier) }})" 
                                                title="دەستکاریکردن"
                                                class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" 
                                              onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم دابینکەرە؟')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="سڕینەوە"
                                                    class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 text-xs">
                                    <i class="fa-solid fa-inbox text-2xl block mb-2"></i>
                                    هیچ دابینکەرێک تۆمار نەکراوە
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- مۆداڵی دەستکاریکردنی دابینکەر -->
    <div id="editSupplierModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-5 space-y-4 shadow-2xl text-xs">
            
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی دابینکەر
                </h3>
                <button onclick="closeEditSupplierModal()" class="text-slate-400 hover:text-white text-base font-bold">&times;</button>
            </div>

            <form id="editSupplierForm" method="POST" class="space-y-3" autocomplete="off">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">ناوی دابینکەر:</label>
                    <input type="text" name="name" id="edit_supplier_name" required autocomplete="off"
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">ژمارەی مۆبایل:</label>
                    <input type="text" name="phone" id="edit_supplier_phone" autocomplete="off"
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">ناونیشان:</label>
                    <input type="text" name="address" id="edit_supplier_address" autocomplete="off"
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1 text-[11px]">تێبینی:</label>
                    <textarea name="note" id="edit_supplier_note" rows="2" autocomplete="off"
                              class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:border-amber-500 focus:outline-none resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditSupplierModal()" class="bg-slate-700 hover:bg-slate-600 text-white font-bold px-4 py-2 rounded-xl text-[11px]">
                        داخستن
                    </button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2 rounded-xl text-[11px] shadow">
                        <i class="fa-solid fa-check"></i> نوێکردنەوە
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditSupplierModal(supplier) {
            document.getElementById('editSupplierForm').action = '/suppliers/' + supplier.id;
            document.getElementById('edit_supplier_name').value = supplier.name || '';
            document.getElementById('edit_supplier_phone').value = supplier.phone || '';
            document.getElementById('edit_supplier_address').value = supplier.address || '';
            document.getElementById('edit_supplier_note').value = supplier.note || '';
            document.getElementById('editSupplierModal').classList.remove('hidden');
        }

        function closeEditSupplierModal() {
            document.getElementById('editSupplierModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditSupplierModal();
            }
        });
    </script>
</body>
</html>