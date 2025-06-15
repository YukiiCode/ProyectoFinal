<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadUserSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar este middleware a usuarios del tipo User (no Client)
        if (Auth::check() && $request->user() instanceof \App\Models\User) {
            $user = $request->user();
            
            if (!$user->relationLoaded('settings')) {
                $user->load('settings');
                
                // Si el usuario no tiene configuraciones todavía, crear unas por defecto
                if (!$user->settings) {
                    $user->settings()->create(\App\Models\UserSettings::getDefaultSettings());
                    $user->load('settings');
                }
            }
        }
        
        return $next($request);
    }
}
