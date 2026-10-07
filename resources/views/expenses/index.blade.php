<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی خەرجییەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
    @include('partials.system-head')
    @include('partials.mobile-tables')
</head>
@php
    $fmtMoney = fn($v, $cur) => ($cur ?? 'IQD') === 'USD' ? '$' . number_format((float) $v, 2) : number_format((float) $v) . ' IQD';
    $cats = ['گشتی' => 'گشتی', 'کرێ' => 'کرێ', 'مووچە' => 'مووچە', 'گواستنەوە' => 'گواستنەوە', 'نانخواردن' => 'خواردن', 'کارەبا و ئاو' => 'کارەبا و ئاو', 'نۆژەنکردنەوە' => 'نۆژەنکردنەوە'];
    $inp = 'w-full p-2.5 rounded-xl border border-slate-600 bg-slate-900 text-white focus:outline-none focus:border-rose-500';
@endphp
<body class="bg-[#0b1329] text-slate-100 min-h-screen p-5">
<div class="max-w-7xl mx-auto space-y-5">

    <header class="flex flex-wrap items-center justify-between gap-3 bg-slate-800/90 p-4 rounded-2xl border border-slate-700/80 shadow-lg">
        <h1 class="text-base font-black text-white flex items-center gap-2">
            <i class="fa-solid fa-wallet text-rose-400"></i> بەڕێوەبردنی خەرجییەکان و تێچووی ڕۆژانە
        </h1>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('expenses.report', request()->all()) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-print"></i> کەشفی حیسابی خەرجییەکان (A4)
            </a>
            <a href="{{ route('reports.index') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition">ڕاپۆرتەکان</a>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">POS</a>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-600/20 border border-rose-500 text-rose-300 p-3.5 rounded-xl text-xs font-bold">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        <!-- فۆڕمی خەرجی نوێ -->
        <div class="lg:col-span-4 bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 space-y-4 h-fit">
            <h2 class="text-xs font-bold text-white flex items-center gap-1.5"><i class="fa-solid fa-circle-plus text-rose-400"></i> تۆمارکردنی خەرجی نوێ</h2>

            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-bold mb-1">ناونیشانی خەرجی:</label>
                    <input type="text" name="title" placeholder="وەک: کرێی دوکان، مووچە، بەنزین..." required class="{{ $inp }}">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-slate-300 font-bold mb-1">بڕی پارە:</label>
                        <input type="number" step="any" min="0.01" name="amount" required placeholder="0" class="{{ $inp }} font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">دراو:</label>
                        <select name="currency" class="{{ $inp }} font-bold">
                            <option value="USD">$</option>
                            <option value="IQD">IQD</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">بەروار:</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="{{ $inp }} font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">پۆلێن / جۆر:</label>
                        <select name="category" class="{{ $inp }}">
                            @foreach($cats as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">کێ پارەکەی داوە:</label>
                        <select name="user_id" class="{{ $inp }}">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">تێبینی:</label>
                    <input type="text" name="note" placeholder="ڕوونکردنەوەی زیاتر..." class="{{ $inp }}">
                </div>

                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-xl transition text-xs flex items-center justify-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-check"></i> تۆمارکردنی خەرجی
                </button>
            </form>
        </div>

        <!-- خشتەی خەرجییەکان -->
        <div class="lg:col-span-8 bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-700/80 pb-3">
                <h2 class="text-xs font-bold text-white flex items-center gap-1.5"><i class="fa-solid fa-list text-blue-400"></i> لیستی خەرجییەکان</h2>
                <div class="text-xs font-mono font-bold text-rose-400 flex items-center gap-2">
                    <span>کۆی خەرجی:</span>
                    <span dir="ltr">${{ number_format($totals['USD'], 2) }}</span>
                    <span class="text-slate-500">·</span>
                    <span dir="ltr">{{ number_format($totals['IQD']) }} IQD</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-slate-900/60 text-[11px] text-slate-400">
                        <tr>
                            <th class="p-3">ناونیشان</th>
                            <th class="p-3 text-center">پۆلێن</th>
                            <th class="p-3 text-center">کێ داویەتی</th>
                            <th class="p-3 text-center">بەروار</th>
                            <th class="p-3 text-center">بڕی خەرجی</th>
                            <th class="p-3">تێبینی</th>
                            <th class="p-3 text-center">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($expenses as $exp)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-3 font-bold text-white">{{ $exp->title }}</td>
                            <td class="p-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] bg-slate-700 text-slate-300 border border-slate-600">{{ $exp->category }}</span></td>
                            <td class="p-3 text-center"><span class="font-bold text-cyan-400">{{ $exp->user->name ?? 'نادیار' }}</span></td>
                            <td class="p-3 text-center font-mono text-slate-400">{{ $exp->date->format('Y-m-d') }}</td>
                            <td class="p-3 text-center font-mono font-bold text-rose-400" dir="ltr">{{ $fmtMoney($exp->amount, $exp->currency) }}</td>
                            <td class="p-3 text-slate-400 text-[11px]">{{ $exp->note ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openEditExpenseModal({{ json_encode($exp) }})" title="دەستکاریکردن" class="bg-amber-600 hover:bg-amber-700 text-white px-2 py-1.5 rounded-lg text-xs transition"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم خەرجییە؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="سڕینەوە" class="bg-rose-600 hover:bg-rose-700 text-white px-2 py-1.5 rounded-lg text-xs transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="p-6 text-center text-slate-500">هیچ خەرجییەک تۆمار نەکراوە</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="pt-2 border-t border-slate-700">{{ $expenses->links() }}</div>
            @endif
        </div>
    </div>
</div>

<!-- مۆداڵی دەستکاریکردن -->
<div id="editExpenseModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-5 space-y-4 shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-700 pb-3">
            <h3 class="font-bold text-white text-sm flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-amber-400"></i> دەستکاریکردنی خەرجی</h3>
            <button type="button" onclick="closeEditExpenseModal()" class="text-slate-400 hover:text-white text-lg p-1">&times;</button>
        </div>

        <form id="editExpenseForm" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-300 font-bold mb-1">ناونیشانی خەرجی:</label>
                <input type="text" name="title" id="edit_title" required class="{{ $inp }}">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-slate-300 font-bold mb-1">بڕی پارە:</label>
                    <input type="number" step="any" min="0.01" name="amount" id="edit_amount" required class="{{ $inp }} font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">دراو:</label>
                    <select name="currency" id="edit_currency" class="{{ $inp }} font-bold">
                        <option value="USD">$</option>
                        <option value="IQD">IQD</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-300 font-bold mb-1">بەروار:</label>
                <input type="date" name="date" id="edit_date" required class="{{ $inp }} font-mono">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-300 font-bold mb-1">پۆلێن / جۆر:</label>
                    <select name="category" id="edit_category" class="{{ $inp }}">
                        @foreach($cats as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-bold mb-1">کێ پارەکەی داوە:</label>
                    <select name="user_id" id="edit_user_id" class="{{ $inp }}">
                        @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-slate-300 font-bold mb-1">تێبینی:</label>
                <input type="text" name="note" id="edit_note" class="{{ $inp }}">
            </div>
            <div class="flex gap-2 pt-2 border-t border-slate-700">
                <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-bold py-2.5 rounded-xl transition">نوێکردنەوە</button>
                <button type="button" onclick="closeEditExpenseModal()" class="px-5 bg-slate-700 hover:bg-slate-600 text-slate-300 font-bold rounded-xl transition">داخستن</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditExpenseModal(exp) {
        document.getElementById('editExpenseForm').action = '/expenses/' + exp.id;
        document.getElementById('edit_title').value = exp.title || '';
        document.getElementById('edit_amount').value = exp.amount || '';
        document.getElementById('edit_currency').value = exp.currency || 'IQD';
        if (exp.date) document.getElementById('edit_date').value = exp.date.split('T')[0];
        document.getElementById('edit_category').value = exp.category || 'گشتی';
        if (exp.user_id) document.getElementById('edit_user_id').value = exp.user_id;
        document.getElementById('edit_note').value = exp.note || '';
        document.getElementById('editExpenseModal').classList.remove('hidden');
    }
    function closeEditExpenseModal() {
        document.getElementById('editExpenseModal').classList.add('hidden');
    }
</script>
</body>
</html>