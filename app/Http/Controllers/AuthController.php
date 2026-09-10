<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('pos.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // ئەگەر مەندووب بوو ڕاستەوخۆ دەچێتە سەر POS یان داشبۆردی خۆی
            return redirect()->intended(route('pos.index'))->with('success', 'بە سەرکەوتوویی چوویتە ژوورەوە');
        }

        return back()->withErrors([
            'email' => 'ئیمەیڵ یان وشەی نهێنی هەڵەیە!',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}