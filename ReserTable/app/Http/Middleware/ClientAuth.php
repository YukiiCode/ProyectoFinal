<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar que el cliente esté autenticado en el guard 'client'
        if (!Auth::guard('client')->check()) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
