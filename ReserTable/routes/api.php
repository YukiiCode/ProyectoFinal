<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tables/map', [TableController::class, 'map']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index']);

// Rutas de API para códigos de descuento
Route::post('/discount-coupons/validate', [\App\Http\Controllers\DiscountCouponController::class, 'validateCoupon']);
Route::post('/discount-coupons/apply', [\App\Http\Controllers\DiscountCouponController::class, 'applyCoupon']);
Route::get('/discount-coupons/available', [\App\Http\Controllers\DiscountCouponController::class, 'getClientCoupons']);
Route::post('/discount-coupons/reward', [\App\Http\Controllers\DiscountCouponController::class, 'createRewardCoupon']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/tables/{table}', [TableController::class, 'update']);
});
