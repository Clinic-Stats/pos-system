<!DOCTYPE html>
<html lang="ckb" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەسەڵاتی بەکارهێنەران</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { font-family: 'Noto Sans Arabic', sans-serif; } </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex justify-between items-center bg-slate-800 p-4 rounded-2xl border border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2 text-white">
                <i class="fa-solid fa-user-shield text-purple-400"></i>
                بەڕێوەبردنی کارمەندان و دەسەڵاتەکان
            </h1>
            <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition">گەڕانەوە بۆ POS</a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-600/20 border border-emerald-500 text-emerald-400 p-3 rounded-xl text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-950/40 border border-rose-500 text-rose-300 p-3 rounded-xl text-sm font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- فۆڕمی زیادکردنی کارمەندی نوێ -->
            <div class="bg-slate-800 p-6 rounded-2xl border border-slate-700 space-y-4 h-fit">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-blue-400"></i> زیادکردنی کارمەند
                </h2>
                <form action="{{ route('users.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-slate-300 text-xs mb-1">ناو:</label>
                        <input type="text" name="name" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-xs focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs mb-1">ئیمەیڵ:</label>
                        <input type="email" name="email" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white font-mono text-xs focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs mb-1">وشەی نهێنی (Password):</label>
                        <input type="password" name="password" required class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-xs focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 text-xs mb-1">پلە (Role):</label>
                        <select name="role" id="new-user-role" onchange="togglePermissionsBlock(this.value, 'create-permissions-box')" class="w-full p-2.5 rounded-xl border border-slate-600 bg-slate-700 text-white text-xs focus:outline-none focus:border-blue-500">
                            <option value="mandub">مەندووب (نوێنەری فرۆشتن)</option>
                            <option value="cashier">کاشێر (دەسەڵاتی سنووردار)</option>
                            <option value="admin">ئەدمین (هەموو دەسەڵاتێک)</option>
                        </select>
                    </div>

                    <!-- بەشی دیاریکردنی دەسەڵاتەکان بۆ کارمەند -->
                    <div id="create-permissions-box" class="space-y-2 pt-3 border-t border-slate-700">
                        <span class="block text-xs font-bold text-amber-400 mb-1">دیاریکردنی بەشە ڕێگەپێدراوەکان:</span>
                        
                        <div class="space-y-1.5 text-xs">
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="pos" checked class="rounded text-blue-600">
                                <span>سیستەمی فرۆشتن (POS)</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="mandub_dashboard" checked class="rounded text-blue-600">
                                <span>چالاکییەکانی من (کورتەی مەندووب)</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="customers" checked class="rounded text-blue-600">
                                <span>کڕیاران و وەرگرتنەوەی قەرز</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="returns" checked class="rounded text-blue-600">
                                <span>گەڕاوەکان (وەسڵی گەڕانەوە)</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="purchases" class="rounded text-blue-600">
                                <span>کڕینی نوێ و دابینکەران</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="products" class="rounded text-blue-600">
                                <span>کۆگا و دەستکاری کاڵاکان</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="partners" class="rounded text-blue-600">
                                <span>هاوبەشەکان و پشکەکان</span>
                            </label>
                            <label class="flex items-center gap-2 bg-slate-700/50 p-2 rounded-lg cursor-pointer hover:bg-slate-700">
                                <input type="checkbox" name="permissions[]" value="reports" class="rounded text-blue-600">
                                <span>ڕاپۆرتە گشتییەکان و قازانج</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl transition text-xs shadow">
                        تۆمارکردنی کارمەند
                    </button>
                </form>
            </div>

            <!-- خشتەی بەکارهێنەران -->
            <div class="lg:col-span-2 bg-slate-800 p-6 rounded-2xl border border-slate-700 overflow-x-auto space-y-4">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-users text-blue-400"></i> لیستی بەکارهێنەران و دەسەڵاتەکان
                </h2>
                
                @php
                    $permissionLabels = [
                        'pos'              => 'فرۆشتن POS',
                        'mandub_dashboard' => 'چالاکییەکان',
                        'customers'        => 'کڕیاران و قەرز',
                        'returns'          => 'گەڕاوەکان',
                        'purchases'        => 'کڕین',
                        'products'         => 'کۆگا',
                        'partners'         => 'هاوبەشەکان',
                        'reports'          => 'ڕاپۆرتەکان',
                    ];
                @endphp

                <table class="w-full text-xs text-right text-slate-300">
                    <thead class="bg-slate-700/50 text-slate-400">
                        <tr>
                            <th class="p-3">ناو</th>
                            <th class="p-3">ئیمەیڵ</th>
                            <th class="p-3">پلە</th>
                            <th class="p-3">دەسەڵاتە کراوەکان</th>
                            <th class="p-3 text-center">کردار</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($users as $u)
                        <tr class="hover:bg-slate-700/30">
                            <td class="p-3 font-bold text-white">{{ $u->name }}</td>
                            <td class="p-3 font-mono text-cyan-400">{{ $u->email }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold 
                                    {{ $u->role === 'admin' ? 'bg-purple-500/20 text-purple-400' : ($u->role === 'mandub' ? 'bg-amber-500/20 text-amber-400' : 'bg-blue-500/20 text-blue-400') }}">
                                    {{ $u->role === 'admin' ? 'ئەدمین' : ($u->role === 'mandub' ? 'مەندووب' : 'کاشێر') }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if($u->role === 'admin')
                                    <span class="text-emerald-400 font-bold">هەموو دەسەڵاتەکان کراوەن</span>
                                @else
                                    @if(!empty($u->permissions) && count($u->permissions) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($u->permissions as $p)
                                                <span class="bg-slate-700 text-slate-200 px-1.5 py-0.5 rounded text-[10px] border border-slate-600">
                                                    {{ $permissionLabels[$p] ?? $p }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-rose-400">هیچ دەسەڵاتێکی نییە</span>
                                    @endif
                                @endif
                            </td>
                           <td class="p-3 text-center">
    <div class="flex items-center justify-center gap-2">
        <!-- دوگمەی دەستکاری بۆ هەمووان (ئەدمینی ئێستاش دەگرێتەوە بۆ گۆڕینی پاسۆرد) -->
        <button onclick="openEditModal({{ json_encode($u) }})" class="bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
            <i class="fa-solid fa-pen-to-square"></i> دەستکاری
        </button>

        <!-- دوگمەی سڕینەوە: تەنها بۆ ئەکاونتەکانی ترە نەک هی خۆت -->
        @if(auth()->id() != $u->id)
            <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('دڵنیایت لە سڕینەوەی ئەم کارمەندە؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1">
                    <i class="fa-solid fa-trash"></i> سڕینەوە
                </button>
            </form>
        @endif
    </div>
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- مۆداڵی دەستکاریکردنی دەسەڵاتەکان -->
    <div id="edit-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-md p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-amber-400"></i> دەستکاریکردنی کارمەند
                </h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            <form id="edit-form" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-300 mb-1">ناوی کارمەند:</label>
                    <input type="text" name="name" id="edit-name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">پلە (Role):</label>
                    <select name="role" id="edit-role" onchange="togglePermissionsBlock(this.value, 'edit-permissions-box')" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="mandub">مەندووب (نوێنەری فرۆشتن)</option>
                        <option value="cashier">کاشێر (دەسەڵاتی سنووردار)</option>
                        <option value="admin">ئەدمین (هەموو دەسەڵاتێک)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">وشەی نهێنی نوێ (ئەگەر ناتەوێت بیگۆڕیت بە بەتاڵی جێیبهێڵە):</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div id="edit-permissions-box" class="space-y-1.5 pt-2 border-t border-slate-700">
                    <span class="block font-bold text-amber-400 mb-1">دەسەڵاتە ڕێگەپێدراوەکان:</span>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="pos" class="edit-perm-check rounded text-blue-600">
                            <span>سیستەمی فرۆشتن (POS)</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="mandub_dashboard" class="edit-perm-check rounded text-blue-600">
                            <span>چالاکییەکانی من (کورتەی مەندووب)</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="customers" class="edit-perm-check rounded text-blue-600">
                            <span>کڕیاران و وەرگرتنەوەی قەرز</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="returns" class="edit-perm-check rounded text-blue-600">
                            <span>گەڕاوەکان (وەسڵی گەڕانەوە)</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="purchases" class="edit-perm-check rounded text-blue-600">
                            <span>کڕینی نوێ و دابینکەران</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="products" class="edit-perm-check rounded text-blue-600">
                            <span>کۆگا و دەستکاری کاڵاکان</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="partners" class="edit-perm-check rounded text-blue-600">
                            <span>هاوبەشەکان و پشکەکان</span>
                        </label>
                        <label class="flex items-center gap-2 bg-slate-900/60 p-2 rounded-lg cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="reports" class="edit-perm-check rounded text-blue-600">
                            <span>ڕاپۆرتە گشتییەکان و قازانج</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-700">
                    <button type="button" onclick="closeEditModal()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-xl">پاشگەزبوونەوە</button>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl">سەیڤکردن</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePermissionsBlock(role, boxId) {
            const box = document.getElementById(boxId);
            if (box) {
                box.style.display = (role === 'admin') ? 'none' : 'block';
            }
        }

        function openEditModal(user) {
            document.getElementById('edit-name').value = user.name;
            document.getElementById('edit-role').value = user.role;
            document.getElementById('edit-form').action = `/users/${user.id}`;

            togglePermissionsBlock(user.role, 'edit-permissions-box');

            const userPerms = Array.isArray(user.permissions) ? user.permissions : [];
            document.querySelectorAll('.edit-perm-check').forEach(chk => {
                chk.checked = userPerms.includes(chk.value);
            });

            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }
    </script>
</body>
</html>