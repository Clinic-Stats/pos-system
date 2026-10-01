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
            <p class="text-xs text-slate-400">تکایە ناو و وشەی نهێنی داخڵ بکە</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-950/40 border border-rose-500/50 text-rose-300 p-3 rounded-xl text-xs font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs" autocomplete="off">
            @csrf
            
            <div>
                <label class="block font-bold text-slate-300 mb-1.5">ناوی بەکارهێنەر:</label>
                <div class="relative">
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           placeholder="ناوی کارمەند" 
                           autocomplete="off" 
                           autocorrect="off" 
                           autocapitalize="off" 
                           spellcheck="false"
                           class="w-full p-3 bg-[#0b1329] border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1.5">وشەی نهێنی (Password):</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="passwordField"
                           required 
                           placeholder="••••••••" 
                           autocomplete="new-password"
                           autocorrect="off" 
                           autocapitalize="off" 
                           spellcheck="false"
                           class="w-full p-3 pl-11 bg-[#0b1329] border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    
                    <!-- دوگمەی پیشاندانی پاسۆرد -->
                    <button type="button" 
                            onclick="togglePasswordVisibility()" 
                            id="togglePasswordBtn"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-blue-400 transition-colors p-1"
                            title="پیشاندانی پاسۆرد">
                        <i id="eyeIcon" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-lg shadow-blue-600/30">
                چوونەژوورەوە <i class="fa-solid fa-arrow-left mr-1"></i>
            </button>
        </form>

    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');
            const toggleBtn = document.getElementById('togglePasswordBtn');
            
            if (passwordField.type === 'password') {
                // پیشاندانی پاسۆرد
                passwordField.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye-slash text-sm';
                toggleBtn.title = 'شاردنەوەی پاسۆرد';
                toggleBtn.classList.add('text-blue-400');
                toggleBtn.classList.remove('text-slate-500');
            } else {
                // شاردنەوەی پاسۆرد
                passwordField.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye text-sm';
                toggleBtn.title = 'پیشاندانی پاسۆرد';
                toggleBtn.classList.remove('text-blue-400');
                toggleBtn.classList.add('text-slate-500');
            }
        }
    </script>

</body>
</html>