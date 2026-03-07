<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfNotBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_banned) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['error' => 'حسابك معطل. تواصل مع الإدارة.']);
        }

        return $next($request);
    }
}
