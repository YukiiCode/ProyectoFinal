<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth for clients
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback for clients
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar cliente existente por Google ID o email
            $client = Client::where('google_id', $googleUser->id)
                           ->orWhere('email', $googleUser->email)
                           ->first();

            if ($client) {
                // Cliente existente - actualizar información de Google si es necesario
                if (!$client->google_id) {
                    $client->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'auth_provider' => 'google',
                    ]);
                }
                
                // Verificar email si viene de Google
                if (!$client->email_verified_at && $googleUser->email) {
                    $client->update(['email_verified_at' => now()]);
                }
            } else {
                // Crear nuevo cliente
                $client = Client::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'auth_provider' => 'google',
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)), // Password random para seguridad
                    'promotional_emails' => true, // Por defecto activado
                    'reservation_reminders' => true, // Por defecto activado
                ]);
            }            // Login del cliente usando el guard 'client'
            Auth::guard('client')->login($client, true);

            // Redirigir a la página de inicio
            return redirect()->intended('/');

        } catch (\Exception $e) {
            // En caso de error, redirigir al login con mensaje
            return redirect('/login')->with('error', 
                'Error al iniciar sesión con Google. Inténtalo de nuevo.'
            );
        }
    }
}
