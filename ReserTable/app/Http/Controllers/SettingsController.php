<?php

namespace App\Http\Controllers;

use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Cargar la relación settings o usar los valores predeterminados
        $user->load('settings');
        $settings = $user->settings ?? UserSettings::getDefaultSettings();
        
        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
            'availableThemes' => [
                'blue' => 'Azul',
                'green' => 'Verde',
                'purple' => 'Morado',
                'orange' => 'Naranja',
                'red' => 'Rojo',
                'gray' => 'Gris'
            ],
            'availableLanguages' => [
                'es' => 'Español',
                'en' => 'Inglés',
                'fr' => 'Francés'
            ]
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'dark_mode' => 'boolean',
            'language' => 'string|in:es,en,fr',
            'theme_color' => 'string|in:blue,green,purple,orange,red,gray',
            'notifications_enabled' => 'boolean',
            'sound_enabled' => 'boolean',
            'sidebar_style' => 'string|in:expanded,collapsed',
            'dashboard_widgets' => 'array',
        ]);

        $user = Auth::user();
        $user->load('settings');
        
        if ($user->settings) {
            $user->settings->update($validated);
        } else {
            $user->settings()->create(array_merge(
                UserSettings::getDefaultSettings(),
                $validated,
                ['user_id' => $user->id]
            ));
        }

        return back()->with('success', 'Configuración actualizada exitosamente');
    }

    public function toggleDarkMode(Request $request)
    {
        $user = Auth::user();
        $darkMode = $request->boolean('dark_mode');
        $user->load('settings');
        
        if ($user->settings) {
            $user->settings->update(['dark_mode' => $darkMode]);
        } else {
            $settings = UserSettings::getDefaultSettings();
            $settings['dark_mode'] = $darkMode;
            $user->settings()->create(array_merge($settings, ['user_id' => $user->id]));
        }

        return back()->with('success', 'Modo oscuro ' . ($darkMode ? 'activado' : 'desactivado'));
    }
}
