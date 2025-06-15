<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Crear usuario administrador
$admin = User::create([
    'name' => 'Administrador',
    'email' => 'admin@gmail.com',
    'password' => Hash::make('adminadmin'),
    'role' => 'admin',
    'email_verified_at' => now(),
]);

echo "✅ Usuario administrador creado exitosamente!\n";
echo "📧 Email: " . $admin->email . "\n";
echo "👤 Nombre: " . $admin->name . "\n";
echo "🔐 Rol: " . $admin->role . "\n";
echo "🆔 ID: " . $admin->id . "\n";
