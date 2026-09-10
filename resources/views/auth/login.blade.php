<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە بۆ سیستەم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-[#0b1329] text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-[#131e3a] border border-slate-700/80 rounded-3xl p-8 shadow-2xl space-y-6">
        
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600/20 text-blue-400 text-2xl mb-2 border border-blue-500/30">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h1 class="text-xl font-black text-white">چوونەژوورەوە بۆ سیستەم</h1>
            <p class="text-xs text-slate-400">تکایە ئیمەیڵ و وشەی نهێنی داخڵ بکە</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-950/40 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-bold text-slate-300 mb-1.5">ئیمەیڵ:</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="example@pos.com" class="w-full p-3 bg-[#0b1329] border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1.5">وشەی نهێنی (Password):</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••" class="w-full p-3 bg-[#0b1329] border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-400 text-xs">
                    <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-700 text-blue-600">
                    لەبیرم مەبە
                </label>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-lg shadow-blue-600/30">
                چوونەژوورەوە <i class="fa-solid fa-arrow-left mr-1"></i>
            </button>
        </form>

    </div>

</body>
</html>