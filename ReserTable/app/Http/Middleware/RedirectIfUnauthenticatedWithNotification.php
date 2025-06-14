<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUnauthenticatedWithNotification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() && !Auth::guard('client')->check()) {
            // Si es una petición de Inertia, devolver respuesta con redirección
            if ($request->header('X-Inertia')) {
                return redirect()->route('login')
                    ->with('flash', [
                        'type' => 'warning',
                        'title' => 'Inicio de sesión requerido',
                        'message' => 'Para hacer una reserva necesitas iniciar sesión o registrarte.'
                    ]);
            }
            
            // Si es una petición AJAX/JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Para hacer una reserva necesitas iniciar sesión.',
                    'login_required' => true
                ], 401);
            }
            
            // Para peticiones normales, redirigir al login con mensaje
            return redirect()->route('login')
                ->with('warning', 'Para hacer una reserva necesitas iniciar sesión o registrarte.');
        }

        return $next($request);
    }
}
