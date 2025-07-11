<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Rutas de autenticación personalizadas (solo para usuarios no autenticados)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\DualLoginController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\DualLoginController::class, 'store']);
    
    Route::get('/register', [\App\Http\Controllers\Auth\ClientRegisterController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\ClientRegisterController::class, 'store']);
    
    // Rutas de Google OAuth
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Ruta de logout (disponible para todos los autenticados)
Route::post('/logout', [\App\Http\Controllers\Auth\DualLoginController::class, 'destroy'])->name('logout');

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Página pública de reservas
Route::get('/reservas', [\App\Http\Controllers\HomeController::class, 'reservas'])->name('reservas');

// Página pública del menú
Route::get('/menu', [\App\Http\Controllers\HomeController::class, 'menu'])->name('menu');

// Rutas públicas para reservas (con notificación si no autenticado)
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/tables', [\App\Http\Controllers\TableController::class, 'publicIndex'])->name('tables');
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'publicStore'])
        ->middleware('auth.notify')
        ->name('reservations.store');
    Route::get('/reservations/available-tables', [\App\Http\Controllers\ReservationController::class, 'getAvailableTables'])->name('reservations.available-tables');
    Route::get('/reservations/available-hours', [\App\Http\Controllers\ReservationController::class, 'getAvailableHours'])->name('reservations.available-hours');
    Route::get('/coupons/available', [\App\Http\Controllers\PublicCouponController::class, 'getAvailableCoupons'])->name('coupons.available');
    Route::post('/coupons/validate', [\App\Http\Controllers\PublicCouponController::class, 'validateCoupon'])->name('coupons.validate');
});

// Rutas administrativas (solo para admins)
Route::middleware(['admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tables', [\App\Http\Controllers\AdminController::class, 'tables'])->name('tables');
        Route::get('/reservations', [\App\Http\Controllers\AdminController::class, 'reservations'])->name('reservations');
        Route::get('/menu', [\App\Http\Controllers\AdminController::class, 'menu'])->name('menu');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        
        // Rutas CRUD para Tables
        Route::post('/tables', [\App\Http\Controllers\TableController::class, 'store'])->name('tables.store');
        Route::put('/tables/{table}', [\App\Http\Controllers\TableController::class, 'update'])->name('tables.update');
        Route::delete('/tables/{table}', [\App\Http\Controllers\TableController::class, 'destroy'])->name('tables.destroy');
        Route::patch('/tables/{table}/status', [\App\Http\Controllers\TableController::class, 'updateStatus'])->name('tables.status');
        
        // Rutas CRUD para Reservations
        Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'adminStore'])->name('reservations.store');
        Route::put('/reservations/{reservation}', [\App\Http\Controllers\ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/reservations/{reservation}', [\App\Http\Controllers\ReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::patch('/reservations/{reservation}/status', [\App\Http\Controllers\ReservationController::class, 'updateStatus'])->name('reservations.status');
        Route::get('/reservations/available-tables', [\App\Http\Controllers\ReservationController::class, 'getAvailableTables'])->name('reservations.available-tables');
        Route::get('/reservations/available-hours', [\App\Http\Controllers\ReservationController::class, 'getAvailableHours'])->name('reservations.available-hours');
        
        // Rutas para Códigos de Descuento
        Route::get('/discount-coupons/generate-code', [\App\Http\Controllers\DiscountCouponController::class, 'generateCode'])->name('discount-coupons.generate-code');
        Route::post('/discount-coupons/validate', [\App\Http\Controllers\DiscountCouponController::class, 'validateCoupon'])->name('discount-coupons.validate');
        Route::post('/discount-coupons/apply', [\App\Http\Controllers\DiscountCouponController::class, 'applyCoupon'])->name('discount-coupons.apply');
        Route::get('/discount-coupons/statistics', [\App\Http\Controllers\DiscountCouponController::class, 'getStatistics'])->name('discount-coupons.statistics');
        Route::get('/discount-coupons/client-coupons', [\App\Http\Controllers\DiscountCouponController::class, 'getClientCoupons'])->name('discount-coupons.client-coupons');
        Route::post('/discount-coupons/reward', [\App\Http\Controllers\DiscountCouponController::class, 'createRewardCoupon'])->name('discount-coupons.reward');
        Route::post('/discount-coupons/bulk-generate', [\App\Http\Controllers\DiscountCouponController::class, 'generateBulkCoupons'])->name('discount-coupons.bulk-generate');
        Route::get('/discount-coupons/stats', [\App\Http\Controllers\DiscountCouponController::class, 'getStats'])->name('discount-coupons.stats');
        Route::get('/discount-coupons/export', [\App\Http\Controllers\DiscountCouponController::class, 'export'])->name('discount-coupons.export');
        Route::get('/discount-coupons/report', [\App\Http\Controllers\DiscountCouponController::class, 'generateReport'])->name('discount-coupons.report');
        Route::post('/discount-coupons/cleanup', [\App\Http\Controllers\DiscountCouponController::class, 'cleanupExpired'])->name('discount-coupons.cleanup');
        Route::patch('/discount-coupons/{discountCoupon}/mark-used', [\App\Http\Controllers\DiscountCouponController::class, 'markAsUsed'])->name('discount-coupons.mark-used');
        Route::resource('discount-coupons', \App\Http\Controllers\DiscountCouponController::class);
        
        // Rutas CRUD para Menu (usando AdminMenuController con Inertia)
        Route::post('/menu', [\App\Http\Controllers\AdminMenuController::class, 'store'])->name('menu.store');
        Route::put('/menu/{product}', [\App\Http\Controllers\AdminMenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{product}', [\App\Http\Controllers\AdminMenuController::class, 'destroy'])->name('menu.destroy');
        Route::patch('/menu/{product}/toggle', [\App\Http\Controllers\AdminMenuController::class, 'toggle'])->name('menu.toggle');
        
        // Mantener las rutas del ProductController original para APIs si es necesario
        Route::post('/menu/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::put('/menu/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('/menu/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
        Route::patch('/menu/products/{product}/toggle', [\App\Http\Controllers\ProductController::class, 'toggleAvailability'])->name('products.toggle');
        Route::get('/menu/products/all', [\App\Http\Controllers\ProductController::class, 'getAllProducts'])->name('products.all');
        Route::get('/menu/categories', [\App\Http\Controllers\ProductController::class, 'getCategories'])->name('products.categories');
        
        // Rutas existentes de usuarios (mantener compatibilidad)
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        
        // Rutas para configuraciones
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
        Route::patch('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::patch('/settings/dark-mode', [\App\Http\Controllers\SettingsController::class, 'toggleDarkMode'])->name('settings.dark-mode');
        Route::patch('/settings/language', [\App\Http\Controllers\SettingsController::class, 'updateLanguage'])->name('settings.language');
        // Alias para acceder como admin.settings.dark-mode (solución a la inconsistencia de rutas)
        Route::patch('/settings/dark-mode-alias', [\App\Http\Controllers\SettingsController::class, 'toggleDarkMode'])->name('admin.settings.dark-mode');
        Route::patch('/settings/language-alias', [\App\Http\Controllers\SettingsController::class, 'updateLanguage'])->name('admin.settings.language');
    });
});

// Rutas para clientes autenticados
Route::middleware(['client'])->group(function () {
    // Dashboard/Perfil del cliente
    Route::get('/mi-cuenta', [\App\Http\Controllers\ClientDashboardController::class, 'account'])->name('client.account');
    Route::get('/mi-dashboard', [\App\Http\Controllers\ClientDashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/mi-perfil', [\App\Http\Controllers\ClientDashboardController::class, 'profile'])->name('client.profile');
    Route::put('/mi-perfil', [\App\Http\Controllers\ClientDashboardController::class, 'updateProfile'])->name('client.profile.update');
    Route::post('/mi-perfil/eliminar-cuenta', [\App\Http\Controllers\ClientDashboardController::class, 'requestAccountDeletion'])->name('client.account.delete');
    Route::delete('/mi-perfil/cancelar-eliminacion', [\App\Http\Controllers\ClientDashboardController::class, 'cancelAccountDeletion'])->name('client.account.cancel-deletion');
    Route::patch('/mi-perfil/preferencias-email', [\App\Http\Controllers\ClientDashboardController::class, 'updateEmailPreferences'])->name('client.preferences.email');
    
    // Página de reserva
    Route::get('/reserva', [\App\Http\Controllers\ClientReservationController::class, 'create'])->name('reserva');
    
    // Rutas para reservas de clientes
    Route::prefix('client')->name('client.')->group(function () {
        // Reservas
        Route::get('/reservations', [\App\Http\Controllers\ClientReservationController::class, 'index'])->name('reservations');
        Route::post('/reservations', [\App\Http\Controllers\ClientReservationController::class, 'store'])->name('reservations.store');
        Route::put('/reservations/{reservation}/cancel', [\App\Http\Controllers\ClientReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/reservations/available-tables', [\App\Http\Controllers\ClientReservationController::class, 'getAvailableTables'])->name('reservations.available-tables');
        
        // Menú con alérgenos
        Route::get('/menu', [\App\Http\Controllers\ClientMenuController::class, 'index'])->name('menu');
        Route::get('/menu/product/{id}', [\App\Http\Controllers\ClientMenuController::class, 'show'])->name('menu.product');
        Route::get('/menu/search-by-allergen', [\App\Http\Controllers\ClientMenuController::class, 'searchByAllergen'])->name('menu.search-allergen');
    });
    
    // Alias para compatibilidad con rutas existentes
    Route::get('/mis-reservas', [\App\Http\Controllers\ClientReservationController::class, 'index'])->name('mis-reservas');
});