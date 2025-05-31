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
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !$request->user()->relationLoaded('settings')) {
            $request->user()->load('settings');
            
            // Si el usuario no tiene configuraciones todavía, crear unas por defecto
            if (!$request->user()->settings) {
                $request->user()->settings()->create(\App\Models\UserSettings::getDefaultSettings());
                $request->user()->load('settings');
            }
        }
        
        return $next($request);
    }
}
