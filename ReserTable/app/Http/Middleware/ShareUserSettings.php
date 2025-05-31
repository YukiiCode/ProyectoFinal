<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareUserSettings
{    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Cargar configuraciones del usuario
            $user = auth()->user();
            $userSettings = $user->getUserSettings();
            
            // Compartir configuraciones con Inertia
            Inertia::share([
                'auth.user.settings' => $userSettings ? $userSettings->toArray() : null,
            ]);
        }

        return $next($request);
    }
}
