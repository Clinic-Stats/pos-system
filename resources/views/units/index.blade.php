<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی یەکەکان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-emerald-400"></i> بەڕێوەبردنی یەکەکان
            </h1>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl">گەڕانەوە بۆ POS</a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3 rounded-xl text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 h-fit space-y-3">
                <h2 class="text-sm font-bold text-white">زیادکردنی یەکەی نوێ</h2>
                <form action="{{ route('units.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">ناوی یەکە (بۆ نموونە: کیلۆ، فەردە، کارتۆن):</label>
                        <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">کێش بە کیلۆ (Factor to Base):</label>
                        <input type="number" step="any" name="factor_to_base" placeholder="بۆ نموونە: بۆ فەردە بنووسە 50، بۆ کیلۆ 1" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-sm font-mono">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl text-sm transition">تۆمارکردن</button>
                </form>
            </div>

            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 space-y-3">
                <h2 class="text-sm font-bold text-white">لیستی یەکەکان</h2>
                <div class="space-y-2">
                    @forelse($units as $u)
                    <div class="flex justify-between items-center p-2.5 bg-slate-700/50 rounded-xl border border-slate-600">
                        <div>
                            <span class="text-sm text-white font-bold">{{ $u->name }}</span>
                            <span class="text-xs text-emerald-400 block font-mono">بەرامبەرە بە: {{ $u->factor_to_base }} کیلۆ</span>
                        </div>
                        <form action="{{ route('units.destroy', $u->id) }}" method="POST" onsubmit="return confirm('دڵنیایت؟')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-rose-400 hover:underline">سڕینەوە</button>
                        </form>
                    </div>
                    @empty
                    <p class="text-xs text-slate-500 text-center py-4">هیچ یەکەیەک تۆمار نەکراوە</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>