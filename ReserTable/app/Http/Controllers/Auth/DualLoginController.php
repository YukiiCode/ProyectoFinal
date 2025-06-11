<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DualLoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login for both admins and clients.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;
        $remember = $request->boolean('remember');

        // Primero intentar login como admin (User)
        $admin = User::where('email', $email)->first();
        
        if ($admin && Hash::check($password, $admin->password)) {
            // Es un admin, usar el guard 'web' por defecto
            Auth::guard('web')->login($admin, $remember);
            
            $request->session()->regenerate();
            
            // Redirigir al dashboard de admin
            return redirect()->intended('/admin/dashboard');
        }

        // Si no es admin, intentar login como cliente
        $client = Client::where('email', $email)->first();
        
        if ($client && Hash::check($password, $client->password)) {
            // Es un cliente, usar el guard 'client'
            Auth::guard('client')->login($client, $remember);
            
            $request->session()->regenerate();
            
            // Redirigir a la página principal
            return redirect()->intended('/');
        }

        // Si no es ni admin ni cliente, o la contraseña es incorrecta
        throw ValidationException::withMessages([
            'email' => ['Las credenciales no coinciden con nuestros registros.'],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Logout de ambos guards
        Auth::guard('web')->logout();
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
