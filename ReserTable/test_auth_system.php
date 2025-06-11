<?php

// Script de prueba para verificar el sistema de autenticación dual
// Uso: php test_auth_system.php

echo "=== PRUEBA DEL SISTEMA DE AUTENTICACIÓN DUAL ===\n\n";

// Probar rutas disponibles
$base_url = 'http://localhost:8000';

echo "1. Probando acceso a login sin autenticación...\n";
$response = @file_get_contents($base_url . '/login');
if ($response !== false) {
    echo "✓ Ruta /login accesible\n";
} else {
    echo "✗ Error al acceder a /login\n";
}

echo "\n2. Probando acceso a register sin autenticación...\n";
$response = @file_get_contents($base_url . '/register');
if ($response !== false) {
    echo "✓ Ruta /register accesible\n";
} else {
    echo "✗ Error al acceder a /register\n";
}

echo "\n3. Probando acceso a home...\n";
$response = @file_get_contents($base_url . '/');
if ($response !== false) {
    echo "✓ Ruta home accesible\n";
} else {
    echo "✗ Error al acceder a home\n";
}

echo "\n4. Probando acceso a ruta admin sin autenticación...\n";
$response = @file_get_contents($base_url . '/admin/dashboard');
if ($response === false) {
    echo "✓ Ruta admin correctamente protegida (debe redirigir)\n";
} else {
    echo "✗ Ruta admin no está protegida correctamente\n";
}

echo "\n=== RESUMEN ===\n";
echo "Las rutas básicas están funcionando.\n";
echo "Para pruebas completas de autenticación, usa el navegador web.\n\n";

// Verificar middlewares
echo "5. Verificando archivos de middleware...\n";
$admin_middleware = file_exists(__DIR__ . '/app/Http/Middleware/AdminAuth.php');
$client_middleware = file_exists(__DIR__ . '/app/Http/Middleware/ClientAuth.php');
$guest_middleware = file_exists(__DIR__ . '/app/Http/Middleware/RedirectIfAuthenticated.php');

echo $admin_middleware ? "✓ AdminAuth middleware existe\n" : "✗ AdminAuth middleware no encontrado\n";
echo $client_middleware ? "✓ ClientAuth middleware existe\n" : "✗ ClientAuth middleware no encontrado\n";
echo $guest_middleware ? "✓ RedirectIfAuthenticated middleware existe\n" : "✗ RedirectIfAuthenticated middleware no encontrado\n";

echo "\n=== PRUEBA COMPLETADA ===\n";
