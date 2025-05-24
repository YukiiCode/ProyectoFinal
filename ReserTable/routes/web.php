<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home'); // Puedes nombrar esta ruta como 'home' si lo deseas

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
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');
    Route::get('/menu', function () {
        return Inertia::render('Menu');
    })->name('menu');
});

// Elimina la siguiente ruta duplicada si aún existe:
// Route::get('/', function () {
//     return view('home');
// })->name('home');