<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tables/map', [TableController::class, 'map']);
Route::post('/reservations', [ReservationController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/tables/{table}', [TableController::class, 'update']);
});
