<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Inertia\Inertia::share([
            'auth' => function () {
                // Verificar ambos guardias: web (User) y client (Client)
                $user = Auth::user();
                $client = Auth::guard('client')->user();
                
                if ($user && $user instanceof \App\Models\User) {
                    // Usuario admin/empleado autenticado
                    return [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role ?? 'user',
                            'settings' => $user->settings
                        ],
                        'client' => null
                    ];
                } elseif ($client && $client instanceof \App\Models\Client) {
                    // Cliente autenticado
                    return [
                        'user' => null,
                        'client' => [
                            'id' => $client->id,
                            'name' => $client->name,
                            'email' => $client->email,
                            'phone' => $client->phone
                        ]
                    ];
                }
                
                return [
                    'user' => null,
                    'client' => null
                ];
            },
        ]);
    }
}
