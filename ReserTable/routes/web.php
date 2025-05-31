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
        
        // Rutas CRUD para Products (Menu)
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
    });
});