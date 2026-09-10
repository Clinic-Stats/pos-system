<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پسوولەی وەرگرتنی کاش - {{ $handover->receipt_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Arabic', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 md:p-8 flex justify-center items-center">

    <div class="print-card w-full max-w-lg bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
        
        <!-- بەشی دوگمەکان بۆ چاپ و داخستن -->
        <div class="flex justify-between items-center pb-4 border-b border-slate-200 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow transition">
                <i class="fa-solid fa-print"></i> چاپکردنی پسوولە
            </button>
            <button onclick="window.close()" class="text-slate-500 hover:text-slate-700 text-xs font-bold">
                داخستن
            </button>
        </div>

        <!-- ناونیشانی پسوولە -->
        <div class="text-center space-y-1">
            <h1 class="text-xl font-black text-slate-800">پسوولەی وەرگرتنی پارەی نەقد (سندوق)</h1>
            <p class="text-xs font-mono font-bold text-blue-600" dir="ltr">{{ $handover->receipt_no }}</p>
        </div>

        <!-- زانیارییەکانی تەسلیمات -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-xs text-slate-700">
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
                <span class="text-slate-500">بەروار و کاتی تەسلیمات:</span>
                <span class="font-mono font-bold text-slate-900">{{ $handover->handover_date->format('Y-m-d h:i A') }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
                <span class="text-slate-500">مەندووبی تەسلیمکار:</span>
                <span class="font-bold text-slate-900">{{ $handover->mandub->name }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
                <span class="text-slate-500">وەرگیراوە لەلایەن (سندوق):</span>
                <span class="font-bold text-slate-900">{{ $handover->receiver->name ?? 'بەڕێوەبەر' }}</span>
            </div>
            @if($handover->note)
            <div class="flex justify-between items-center">
                <span class="text-slate-500">تێبینی:</span>
                <span class="font-medium text-slate-800">{{ $handover->note }}</span>
            </div>
            @endif
        </div>

        <!-- بڕی پارەی وەرگیراو -->
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-center">
            <span class="text-xs text-emerald-800 block mb-1 font-bold">بڕی پارەی وەرگیراو:</span>
            <span class="text-2xl font-black font-mono text-emerald-600" dir="ltr">
                {{ number_format($handover->amount) }} IQD
            </span>
        </div>

        <!-- واژووی تەسلیمکار و وەرگر -->
        <div class="pt-8 flex justify-between items-center text-xs text-slate-600">
            <div class="text-center">
                <p class="font-bold mb-8">واژووی تەسلیمکار (مەندووب)</p>
                <p>...................................</p>
            </div>
            <div class="text-center">
                <p class="font-bold mb-8">واژووی وەرگر (بەڕێوەبەر)</p>
                <p>...................................</p>
            </div>
        </div>

    </div>

</body>
</html>