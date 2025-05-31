<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $products = \App\Models\Product::where('available', 1)->get();
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'products' => $products,
    ]);
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::get('/reserva', function () {
        return Inertia::render('Reserva');
    })->name('reserva');
    
    Route::get('/menu', function () {
        $products = \App\Models\Product::where('available', 1)->get();
        return Inertia::render('Menu', [
            'products' => $products
        ]);
    })->name('menu');

    // Rutas administrativas
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/tables', [\App\Http\Controllers\AdminController::class, 'tables'])->name('tables');
        Route::get('/reservations', [\App\Http\Controllers\AdminController::class, 'reservations'])->name('reservations');
        Route::get('/menu', [\App\Http\Controllers\AdminController::class, 'menu'])->name('menu');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        
        // Rutas existentes de usuarios (mantener compatibilidad)
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });
});