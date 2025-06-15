<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Cargar la aplicación Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Verificar si ya existe un usuario admin
    $existingAdmin = User::where('email', 'admin@reserver.com')->first();
    
    if ($existingAdmin) {
        echo "El usuario admin ya existe con email: admin@reserver.com\n";
        echo "Para acceder use:\n";
        echo "Email: admin@reserver.com\n";
        echo "Contraseña: AdminPassword123!\n";
        exit(0);
    }

    // Crear usuario admin
    $admin = User::create([
        'name' => 'Administrador',
        'email' => 'admin@reserver.com',
        'password' => Hash::make('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    echo "Usuario administrador creado exitosamente!\n";
    echo "Email: admin@reserver.com\n";
    echo "Contraseña: AdminPassword123!\n";
    echo "Puedes acceder al panel de administración en: /admin/dashboard\n";

} catch (Exception $e) {
    echo "Error al crear el usuario administrador: " . $e->getMessage() . "\n";
    exit(1);
}
