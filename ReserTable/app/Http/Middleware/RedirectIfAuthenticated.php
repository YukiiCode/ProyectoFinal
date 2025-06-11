<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si hay un admin logueado, redirigir al dashboard
        if (Auth::guard('web')->check()) {
            return redirect('/admin/dashboard');
        }
        
        // Si hay un cliente logueado, redirigir al home
        if (Auth::guard('client')->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
