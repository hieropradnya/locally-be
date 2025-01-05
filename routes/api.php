<?php

use App\Http\Controllers\api\SellerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


});

Route::get('/seller', [SellerController::class, 'index']);
Route::get('/seller/{id}', [SellerController::class, 'show']);
