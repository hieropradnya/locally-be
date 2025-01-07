<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\api\SellerController;
use App\Http\Controllers\ProductVariantController;

// user
Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::post('/seller', [SellerController::class, 'store']);
    Route::put('/seller/{id}', [SellerController::class, 'update']);
    // Route::delete('/seller/{id}', [SellerController::class, 'destroy']);

    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    // Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    Route::post('/review', [ReviewController::class, 'store']);
});

Route::get('/seller', [SellerController::class, 'index']);
Route::get('/seller/{id}', [SellerController::class, 'show']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/products/{productId}/variants', [ProductVariantController::class, 'index']);
