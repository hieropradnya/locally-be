<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VoucherController;
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


    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    // Route::get('/cart/{id}', [CartController::class, 'show'])
    // Route::put('/cart/{id}', [CartController::class, 'update']));
    // Route::delete('/cart/{id}', [CartController::class, 'destroy'])');


    Route::apiResource('address', AddressController::class);


    Route::get('/voucher', [VoucherController::class, 'index']);
    Route::post('/voucher', [VoucherController::class, 'store']);
    Route::post('/voucher/{id}', [VoucherController::class, 'show']);
});

Route::get('/seller', [SellerController::class, 'index']);
Route::get('/seller/{id}', [SellerController::class, 'show']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/products/{productId}/variants', [ProductVariantController::class, 'index']);








Route::get('/province', function () {
    $apiKey = env('RAJAONGKIR_API_KEY');

    // Mengirimkan request ke API RajaOngkir untuk mendapatkan daftar provinsi
    $response = Http::withHeaders([
        'key' => $apiKey,
    ])->get('https://api.rajaongkir.com/starter/city');

    // Mengecek apakah response berhasil
    if ($response->successful()) {
        // Mengembalikan response dalam format JSON
        return response()->json($response->json());
    }

    // Jika gagal, mengembalikan response error
    return response()->json([
        'status' => 'error',
        'message' => 'Failed to fetch provinces data.'
    ], 400);
});
