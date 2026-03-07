<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
public function handle($request, Closure $next)
{
    if ($request->is('admin/*')) {
        return $next($request);
    }

    if (!\App\Models\Setting::get('site_enabled', true)) {
        return inertia('Maintenance', [
            'message' => \App\Models\Setting::get('maintenance_message', 'الموقع تحت الصيانة حالياً.')
        ]);
    }

    return $next($request);
}
}
