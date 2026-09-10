<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6',
            'role'        => 'required|in:admin,cashier,mandub',
            'permissions' => 'nullable|array',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'permissions' => $request->role === 'admin' ? [] : ($request->permissions ?? []),
        ]);

        return redirect()->back()->with('success', 'کارمەند بە سەرکەوتوویی دروستکرا');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'role'        => 'required|in:admin,cashier,mandub',
            'password'    => 'nullable|min:6',
            'permissions' => 'nullable|array',
        ]);

        $data = [
            'name'        => $request->name,
            'role'        => $request->role,
            'permissions' => $request->role === 'admin' ? [] : ($request->permissions ?? []),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'زانیاری و دەسەڵاتەکانی کارمەند نوێکرانەوە');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['ناتوانیت ئەکاونتی ئێستای خۆت بسڕیتەوە!']);
        }

        $user->delete();
        return redirect()->back()->with('success', 'کارمەند سڕایەوە');
    }
}