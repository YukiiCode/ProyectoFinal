<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Si hay un cliente logueado, redirigir al home
        if (Auth::guard('client')->check()) {
            return redirect('/')->with('error', 'Acceso denegado. Esta área es solo para administradores.');
        }
        
        // Si no hay ningún usuario logueado como admin, redirigir al login
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();
        
        // Verificar que el usuario tenga rol de admin
        if (!$user || $user->role !== 'admin') {
            return redirect('/')->with('error', 'Acceso denegado. Se requieren permisos de administrador.');
        }

        return $next($request);
    }
}
