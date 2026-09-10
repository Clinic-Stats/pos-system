<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کەشفی حیسابی {{ $customer->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-card { border: 1px solid #ddd !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-4 md:p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-slate-200 print-card space-y-6">

        {{-- دوگمەی چاپ و گەڕانەوە --}}
        <div class="flex justify-between items-center pb-4 border-b border-slate-200 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-print"></i> چاپکردن / دابەزاندن بە PDF
            </button>
            <span class="text-xs text-slate-500">بەرواری دەرچوون: {{ now()->setTimezone('Asia/Baghdad')->format('Y-m-d h:i A') }}</span>
        </div>

        {{-- سەردێڕ و زانیاری کڕیار --}}
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-black text-slate-800">کەشفی حیسابی کڕیار</h1>
                <p class="text-sm font-bold text-slate-600 mt-1">{{ $customer->name }}</p>
                <div class="text-xs text-slate-500 space-y-0.5 mt-2">
                    <div>مۆبایل: <span class="font-mono text-slate-700" dir="ltr">{{ $customer->phone ?? 'نادیار' }}</span></div>
                    <div>ناونیشان: <span class="text-slate-700">{{ $customer->address ?? 'نادیار' }}</span></div>
                </div>
            </div>
            <div class="text-left bg-slate-50 p-4 rounded-2xl border border-slate-200 min-w-[200px]">
                <div class="text-xs text-slate-500 font-bold mb-1">کۆی قەرزی ماوە لەسەری:</div>
                <div class="text-2xl font-black font-mono {{ $remainingDebt > 0 ? 'text-rose-600' : 'text-emerald-600' }}" dir="ltr">
                    {{ number_format($remainingDebt) }} IQD
                </div>
            </div>
        </div>

        {{-- پوختەی گشتی حیساب --}}
        <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl text-center border border-slate-100">
            <div>
                <div class="text-xs text-slate-500">کۆی کڕینەکان</div>
                <div class="text-sm font-bold font-mono text-slate-800 mt-1" dir="ltr">{{ number_format($totalPurchases) }} د.ع</div>
            </div>
            <div>
                <div class="text-xs text-slate-500">کۆی پارەی دراو / داشکاو</div>
                <div class="text-sm font-bold font-mono text-emerald-600 mt-1" dir="ltr">{{ number_format($totalPaid) }} د.ع</div>
            </div>
            <div>
                <div class="text-xs text-slate-500">ماوەی قەرز</div>
                <div class="text-sm font-bold font-mono {{ $remainingDebt > 0 ? 'text-rose-600' : 'text-slate-800' }}" dir="ltr">{{ number_format($remainingDebt) }} د.ع</div>
            </div>
        </div>

        {{-- خشتەی جووڵەکان (وەسڵەکان، پارەدانەوەکان، و گەڕاوەکان) --}}
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-right border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 border-b border-slate-200">
                        <th class="p-3">بەروار</th>
                        <th class="p-3">ژمارەی بەڵگە / وەسڵ</th>
                        <th class="p-3">ڕوونکردنەوە</th>
                        <th class="p-3">تۆمارکار (کارمەند)</th>
                        <th class="p-3 text-center">کڕین (قەرز)</th>
                        <th class="p-3 text-center">پارەدانەوە / داشکاندن</th>
                        <th class="p-3 text-left">ڕەسید (ماوە)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ledger as $row)
                    <tr class="{{ $row['type'] === 'payment' ? 'bg-emerald-50/40' : ($row['type'] === 'return' ? 'bg-amber-50/60' : '') }}">
                        <td class="p-3 font-mono text-slate-500">{{ $row['date'] }}</td>
                        <td class="p-3 font-mono font-bold text-slate-700">{{ $row['reference'] }}</td>
                        <td class="p-3">
                            <div class="font-bold {{ $row['type'] === 'return' ? 'text-amber-800' : ($row['type'] === 'payment' ? 'text-emerald-800' : 'text-slate-800') }}">
                                {{ $row['description'] }}
                            </div>
                            @if(!empty($row['details']) && count($row['details']) > 0)
                                <div class="text-[10px] text-slate-500 mt-1">
                                    @foreach($row['details'] as $item)
                                        <span>• {{ $item->product->name ?? '' }} ({{ $item->quantity }} {{ $item->unit->name ?? '' }}{{ isset($item->condition_type) ? ' - ' . ($item->condition_type === 'normal' ? 'ئاسایی' : ($item->condition_type === 'expired' ? 'بەسەرچوو' : 'تێکچوو')) : '' }})</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="p-3 font-bold text-blue-600">
                            {{ $row['user_name'] ?? ($row['user']->name ?? 'سیستەم') }}
                        </td>
                        <td class="p-3 font-mono text-center font-bold text-slate-800" dir="ltr">
                            {{ $row['debit'] > 0 ? number_format($row['debit']) : '-' }}
                        </td>
                        <td class="p-3 font-mono text-center font-bold text-emerald-600" dir="ltr">
                            {{ $row['credit'] > 0 ? number_format($row['credit']) : '-' }}
                        </td>
                        <td class="p-3 font-mono text-left font-bold {{ $row['balance'] > 0 ? 'text-rose-600' : 'text-slate-900' }}" dir="ltr">
                            {{ number_format($row['balance']) }} IQD
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-400">هیچ مامەڵەیەک تۆمار نەکراوە</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- مێژووی وەرگرتنەوەی قەرزەکان و دەستکاریکردنیان --}}
        <div class="mt-8 pt-6 border-t border-slate-200 no-print space-y-3">
            <h3 class="text-sm font-bold text-slate-800">مێژووی پارەدانەوەکان و دەستکاریکردن</h3>
            <table class="w-full text-xs text-right border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-600">
                    <tr>
                        <th class="p-2.5">بەروار</th>
                        <th class="p-2.5">وەرگر (کارمەند)</th>
                        <th class="p-2.5">بڕی دراو</th>
                        <th class="p-2.5">تێبینی</th>
                        <th class="p-2.5 text-center">کردار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($customer->payments->sortBy('payment_date') as $payment)
                    <tr>
                        <form action="{{ route('customer_payments.update', $payment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <td class="p-2">
                                <input type="date" name="payment_date" value="{{ $payment->payment_date ?? $payment->created_at->format('Y-m-d') }}" class="p-1 border border-slate-300 rounded font-mono text-xs">
                            </td>
                            <td class="p-2 font-bold text-blue-600">
                                {{ $payment->user->name ?? 'سیستەم' }}
                            </td>
                            <td class="p-2">
                                <input type="number" step="any" name="amount" value="{{ $payment->amount }}" class="p-1 border border-slate-300 rounded font-mono text-xs w-28">
                            </td>
                            <td class="p-2">
                                <input type="text" name="note" value="{{ $payment->note }}" placeholder="تێبینی" class="p-1 border border-slate-300 rounded text-xs w-full">
                            </td>
                            <td class="p-2 text-center flex items-center justify-center gap-2">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-2 py-1 rounded text-[11px] font-bold">
                                    گۆڕین
                                </button>
                        </form>
                                <form action="{{ route('customer_payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم پارەدانەوەیە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-2 py-1 rounded text-[11px] font-bold">
                                        سڕینەوە
                                    </button>
                                </form>
                            </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-slate-400">هیچ پارەدانەوەیەک نییە</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-6 border-t border-slate-200 flex justify-between items-center text-xs text-slate-400">
            <div>ئەم پسوولەیە بە سیستەمی ئەلیکترۆنی دەرچووە</div>
            <div class="font-bold text-slate-700">واژووی کڕیار: .......................</div>
        </div>

    </div>

</body>
</html>