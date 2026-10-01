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
    @php
        $fmt = fn($v, $c) => $c === 'USD'
            ? '$' . number_format((float) $v, 2)
            : number_format((float) $v) . ' IQD';
    @endphp
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen p-4 md:p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl border border-slate-200 print-card space-y-6">

        {{-- دوگمەی چاپ --}}
        <div class="flex justify-between items-center pb-4 border-b border-slate-200 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-2 shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-print"></i> چاپکردن / دابەزاندن بە PDF
            </button>
            <span class="text-xs text-slate-500">بەرواری دەرچوون: {{ now()->setTimezone('Asia/Baghdad')->format('Y-m-d h:i A') }}</span>
        </div>

        {{-- سەردێڕ و زانیاری کڕیار --}}
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800">کەشفی حیسابی کڕیار</h1>
                <p class="text-sm font-bold text-slate-600 mt-1">{{ $customer->name }}</p>
                <div class="text-xs text-slate-500 space-y-0.5 mt-2">
                    <div>مۆبایل: <span class="font-mono text-slate-700" dir="ltr">{{ $customer->phone ?? 'نادیار' }}</span></div>
                    <div>ناونیشان: <span class="text-slate-700">{{ $customer->address ?? 'نادیار' }}</span></div>
                </div>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 min-w-[220px] space-y-2">
                <div class="text-xs text-slate-500 font-bold">کۆی قەرزی ماوە لەسەری:</div>
                @foreach(['USD', 'IQD'] as $cur)
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-[11px] font-bold text-slate-500">{{ $cur === 'USD' ? 'دۆلار' : 'دینار' }}</span>
                        <span class="text-lg font-black font-mono {{ $summary[$cur]['debt'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}" dir="ltr">
                            {{ $fmt($summary[$cur]['debt'], $cur) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- پوختەی حیساب بۆ هەر دراوێک --}}
        <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden">
            <table class="w-full text-xs text-center">
                <thead class="bg-slate-100 text-slate-500">
                    <tr>
                        <th class="p-2.5">دراو</th>
                        <th class="p-2.5">کۆی کڕینەکان</th>
                        <th class="p-2.5">کۆی پارەی دراو / داشکاو</th>
                        <th class="p-2.5">ماوەی قەرز</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono font-bold">
                    @foreach(['USD' => 'دۆلار ($)', 'IQD' => 'دینار (IQD)'] as $cur => $label)
                    <tr>
                        <td class="p-2.5 font-sans text-slate-600">{{ $label }}</td>
                        <td class="p-2.5 text-slate-800" dir="ltr">{{ $fmt($summary[$cur]['purchases'], $cur) }}</td>
                        <td class="p-2.5 text-emerald-600" dir="ltr">{{ $fmt($summary[$cur]['paid'], $cur) }}</td>
                        <td class="p-2.5 {{ $summary[$cur]['debt'] > 0 ? 'text-rose-600' : 'text-slate-800' }}" dir="ltr">{{ $fmt($summary[$cur]['debt'], $cur) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- خشتەی جووڵەکان --}}
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
                    @php $cur = $row['currency']; @endphp
                    <tr class="{{ $row['type'] === 'payment' ? 'bg-emerald-50/40' : ($row['type'] === 'return' ? 'bg-amber-50/60' : '') }}">
                        <td class="p-3 font-mono text-slate-500">{{ $row['date'] }}</td>
                        <td class="p-3 font-mono font-bold text-slate-700">{{ $row['reference'] }}</td>
                        <td class="p-3">
                            <div class="font-bold {{ $row['type'] === 'return' ? 'text-amber-800' : ($row['type'] === 'payment' ? 'text-emerald-800' : 'text-slate-800') }}">
                                {{ $row['description'] }}
                                <span class="mr-1 px-1.5 py-0.5 rounded text-[10px] font-black {{ $cur === 'USD' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $cur === 'USD' ? '$' : 'IQD' }}</span>
                            </div>
                            @if(!empty($row['details']) && count($row['details']) > 0)
                                <div class="text-[10px] text-slate-500 mt-1">
                                    @foreach($row['details'] as $item)
                                        <span>• {{ $item->product->name ?? '' }} ({{ $item->quantity }} {{ $item->unit->name ?? '' }}{{ isset($item->condition_type) ? ' - ' . ($item->condition_type === 'normal' ? 'ئاسایی' : ($item->condition_type === 'expired' ? 'بەسەرچوو' : 'تێکچوو')) : '' }})</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="p-3 font-bold text-blue-600">{{ $row['user_name'] ?? 'سیستەم' }}</td>
                        <td class="p-3 font-mono text-center font-bold text-slate-800" dir="ltr">
                            {{ $row['debit'] > 0 ? $fmt($row['debit'], $cur) : '-' }}
                        </td>
                        <td class="p-3 font-mono text-center font-bold text-emerald-600" dir="ltr">
                            {{ $row['credit'] > 0 ? $fmt($row['credit'], $cur) : '-' }}
                        </td>
                        <td class="p-3 font-mono text-left font-bold {{ $row['balance'] > 0 ? 'text-rose-600' : 'text-slate-900' }}" dir="ltr">
                            {{ $fmt($row['balance'], $cur) }}
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
                        <th class="p-2.5">دراو</th>
                        <th class="p-2.5">تێبینی</th>
                        <th class="p-2.5 text-center">کردار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($customer->payments->sortBy('payment_date') as $payment)
                    @php
                        $fid = 'pf-' . $payment->id;
                        $pc = \App\Models\Customer::normalizeCurrency($payment->currency);
                    @endphp
                    <tr>
                        <td class="p-2">
                            <input form="{{ $fid }}" type="date" name="payment_date" value="{{ $payment->payment_date ?? $payment->created_at->format('Y-m-d') }}" class="p-1 border border-slate-300 rounded font-mono text-xs">
                        </td>
                        <td class="p-2 font-bold text-blue-600">{{ $payment->user->name ?? 'سیستەم' }}</td>
                        <td class="p-2">
                            <input form="{{ $fid }}" type="number" step="any" min="0.01" name="amount" value="{{ $payment->amount }}" class="p-1 border border-slate-300 rounded font-mono text-xs w-28">
                        </td>
                        <td class="p-2">
                            <select form="{{ $fid }}" name="currency" class="p-1 border border-slate-300 rounded text-xs font-bold">
                                <option value="USD" {{ $pc === 'USD' ? 'selected' : '' }}>$ دۆلار</option>
                                <option value="IQD" {{ $pc === 'IQD' ? 'selected' : '' }}>د.ع دینار</option>
                            </select>
                        </td>
                        <td class="p-2">
                            <input form="{{ $fid }}" type="text" name="note" value="{{ $payment->note }}" placeholder="تێبینی" class="p-1 border border-slate-300 rounded text-xs w-full">
                        </td>
                        <td class="p-2">
                            <div class="flex items-center justify-center gap-2">
                                <form id="{{ $fid }}" action="{{ route('customer_payments.update', $payment->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-2 py-1 rounded text-[11px] font-bold">گۆڕین</button>
                                </form>
                                <form action="{{ route('customer_payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم پارەدانەوەیە؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-2 py-1 rounded text-[11px] font-bold">سڕینەوە</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-400">هیچ پارەدانەوەیەک نییە</td>
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