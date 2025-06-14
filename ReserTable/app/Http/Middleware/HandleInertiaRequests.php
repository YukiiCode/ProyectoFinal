<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $this->getAuthenticatedUser($request),
            ],
            'flash' => [
                'message' => $request->session()->get('message'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'success' => $request->session()->get('success'),
                'login_required' => $request->session()->get('login_required'),
            ],
        ];
    }

    /**
     * Get the authenticated user from either admin or client guard.
     */
    private function getAuthenticatedUser(Request $request)
    {
        // Verificar si hay un admin autenticado
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'admin',
                'type' => 'admin',
            ];
            
            return $userData;
        }

        // Verificar si hay un cliente autenticado
        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            return [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone ?? null,
                'type' => 'client',
            ];
        }

        return null;
    }
}
