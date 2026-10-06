<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە بۆ سیستەم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> 
        body { font-family: 'Noto Sans Arabic', sans-serif; } 
        
        /* شێوازی لیستی ناوەکان */
        .username-dropdown {
            animation: slideDown 0.2s ease-out;
            max-height: 250px;
            overflow-y: auto;
        }
        
        .username-dropdown::-webkit-scrollbar { width: 4px; }
        .username-dropdown::-webkit-scrollbar-track { background: transparent; }
        .username-dropdown::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .username-item {
            transition: all 0.15s ease;
            cursor: pointer;
        }
        
        .username-item:hover {
            background: rgba(59, 130, 246, 0.1);
            padding-right: 16px;
        }
        
        .username-item:active {
            background: rgba(59, 130, 246, 0.2);
        }
    </style>
    @include('partials.system-head')
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

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs" autocomplete="off" data-form-type="other" id="loginForm">
            @csrf
            
            <!-- ============================================ -->
            <!-- خانەی ناوی بەکارهێنەر - لەگەڵ لیستی ناوەکان -->
            <!-- ============================================ -->
            <div>
                <label class="block font-bold text-slate-300 mb-1.5">ناوی بەکارهێنەر:</label>
                <div class="relative">
                    <input type="text" 
                           name="name" 
                           id="usernameField"
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           placeholder="ناوی کارمەند" 
                           autocomplete="off" 
                           autocorrect="off" 
                           autocapitalize="off" 
                           spellcheck="false"
                           data-form-type="other"
                           onfocus="showUsernameDropdown()"
                           oninput="filterUsernames()"
                           class="w-full p-3 pl-11 bg-[#0b1329] border border-slate-700 rounded-xl text-white font-mono placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    
                    <!-- دوگمەی پیشاندانی هەموو ناوەکان -->
                    <button type="button" 
                            onclick="toggleAllUsernames(event)" 
                            id="dropdownToggleBtn"
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-blue-400 transition-colors p-1"
                            title="پیشاندانی هەموو ناوەکان">
                        <i id="dropdownIcon" class="fa-solid fa-chevron-down text-xs"></i>
                    </button>
                    
                    <!-- لیستی ناوەکان -->
                    <div id="usernameDropdown" 
                         class="username-dropdown hidden absolute top-full right-0 left-0 mt-1 bg-[#1a2444] border border-slate-700 rounded-xl shadow-2xl z-50">
                        <!-- ناوەکان بە JavaScript زیاد دەکرێن -->
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- خانەی وشەی نهێنی - لەگەڵ دوگمەی پیشاندان -->
            <!-- ============================================ -->
            <div>
                <label class="block font-bold text-slate-300 mb-1.5">وشەی نهێنی (Password):</label>
                <div class="relative">
                    <!-- ئینپوتی fake بۆ چەواشەکردنی Google Password Manager -->
                    <input type="text" name="fake_user" style="display:none" autocomplete="off" tabindex="-1">
                    <input type="password" name="fake_pass" style="display:none" autocomplete="off" tabindex="-1">
                    
                    <input type="password" 
                           name="password" 
                           id="passwordField"
                           required 
                           placeholder="••••••••" 
                           autocomplete="off"
                           autocorrect="off" 
                           autocapitalize="off" 
                           spellcheck="false"
                           data-form-type="other"
                           data-lpignore="true"
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
        // ============================================
        // پیشاندانی و شاردنەوەی پاسۆرد
        // ============================================
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');
            const toggleBtn = document.getElementById('togglePasswordBtn');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye-slash text-sm';
                toggleBtn.title = 'شاردنەوەی پاسۆرد';
                toggleBtn.classList.add('text-blue-400');
                toggleBtn.classList.remove('text-slate-500');
            } else {
                passwordField.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye text-sm';
                toggleBtn.title = 'پیشاندانی پاسۆرد';
                toggleBtn.classList.remove('text-blue-400');
                toggleBtn.classList.add('text-slate-500');
            }
        }
        
        // ============================================
        // سیستەمی ناوەکانی بەکارهێنەر
        // ============================================
        const USERNAMES_KEY = 'pos_saved_usernames';
        
        // وەرگرتنی ناوە پاشەکەوتکراوەکان
        function getSavedUsernames() {
            try {
                return JSON.parse(localStorage.getItem(USERNAMES_KEY) || '[]');
            } catch(e) {
                return [];
            }
        }
        
        // پاشەکەوتکردنی ناوێکی نوێ
        function saveUsername(username) {
            if (!username || !username.trim()) return;
            
            username = username.trim();
            let usernames = getSavedUsernames();
            
            // لابردنی دووبارە
            usernames = usernames.filter(u => u !== username);
            
            // زیادکردن بۆ سەرەتا
            usernames.unshift(username);
            
            // تەنها ١٥ ناو هەڵبگرە
            usernames = usernames.slice(0, 15);
            
            localStorage.setItem(USERNAMES_KEY, JSON.stringify(usernames));
        }
        
        // پیشاندانی لیستی ناوەکان
        function showUsernameDropdown(filter = true) {
            const dropdown = document.getElementById('usernameDropdown');
            const field = document.getElementById('usernameField');
            const usernames = getSavedUsernames();
            
            if (usernames.length === 0) {
                dropdown.classList.add('hidden');
                return;
            }
            
            // فلتەرکردن بەپێی ئەوەی نووسراوە
            let filtered = usernames;
            if (filter) {
                const query = field.value.toLowerCase().trim();
                if (query) {
                    filtered = usernames.filter(u => u.toLowerCase().includes(query));
                }
            }
            
            if (filtered.length === 0) {
                dropdown.classList.add('hidden');
                return;
            }
            
            // دروستکردنی لیست
            dropdown.innerHTML = `
                <div class="p-2">
                    <div class="flex items-center justify-between px-3 py-2 border-b border-slate-700/50 mb-1">
                        <span class="text-[10px] font-bold text-slate-400">
                            <i class="fa-solid fa-history text-[9px]"></i> ناوەکانی پێشوو (${filtered.length})
                        </span>
                        <button type="button" onclick="clearAllUsernames(event)" class="text-[9px] text-rose-400 hover:text-rose-300 font-bold">
                            <i class="fa-solid fa-trash-can text-[8px]"></i> سڕینەوە
                        </button>
                    </div>
                    ${filtered.map((username, idx) => `
                        <div onclick="selectUsername('${username.replace(/'/g, "\\'")}')" 
                             class="username-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-[11px] font-black">
                                ${username.charAt(0).toUpperCase()}
                            </div>
                            <span class="font-bold text-xs flex-1">${username}</span>
                            <i class="fa-solid fa-arrow-left text-[10px] text-slate-500"></i>
                        </div>
                    `).join('')}
                </div>
            `;
            
            dropdown.classList.remove('hidden');
        }
        
        // فلتەرکردنی ناوەکان کاتێک دەنووسرێت
        function filterUsernames() {
            showUsernameDropdown(true);
        }
        
        // هەڵبژاردنی ناوێک
        function selectUsername(username) {
            document.getElementById('usernameField').value = username;
            document.getElementById('usernameDropdown').classList.add('hidden');
            document.getElementById('passwordField').focus();
        }
        
        // پیشاندان / شاردنەوەی هەموو ناوەکان
        function toggleAllUsernames(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('usernameDropdown');
            const icon = document.getElementById('dropdownIcon');
            
            if (dropdown.classList.contains('hidden')) {
                showUsernameDropdown(false); // بێ فلتەرکردن، هەموو پیشان بدە
                icon.className = 'fa-solid fa-chevron-up text-xs';
            } else {
                dropdown.classList.add('hidden');
                icon.className = 'fa-solid fa-chevron-down text-xs';
            }
        }
        
        // سڕینەوەی هەموو ناوەکان
        function clearAllUsernames(event) {
            event.stopPropagation();
            if (confirm('ئایا دڵنیایت لە سڕینەوەی هەموو ناوە پاشەکەوتکراوەکان؟')) {
                localStorage.removeItem(USERNAMES_KEY);
                document.getElementById('usernameDropdown').classList.add('hidden');
            }
        }
        
        // داخستنی لیست کاتێک کلیک لە دەرەوە دەکرێت
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('usernameDropdown');
            const field = document.getElementById('usernameField');
            const toggleBtn = document.getElementById('dropdownToggleBtn');
            
            if (dropdown && !dropdown.contains(e.target) && 
                e.target !== field && 
                !toggleBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.getElementById('dropdownIcon').className = 'fa-solid fa-chevron-down text-xs';
            }
        });
        
        // داخستن بە Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('usernameDropdown').classList.add('hidden');
                document.getElementById('dropdownIcon').className = 'fa-solid fa-chevron-down text-xs';
            }
        });
        
        // پاشەکەوتکردنی ناو لە کاتی ناردنی فۆرم
        document.getElementById('loginForm').addEventListener('submit', function() {
            const username = document.getElementById('usernameField').value.trim();
            if (username) {
                saveUsername(username);
            }
        });
    </script>

</body>
</html>