<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ئەگەر ئەدمین بوو یان ئەم دەسەڵاتەی هەبوو ڕێگەی پێبدە
        if (auth()->user()->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'ببورە! تۆ دەسەڵاتی چوونەژوورەوەت نییە بۆ ئەم بەشە.');
    }
}