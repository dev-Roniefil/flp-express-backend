<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController; //
use App\Http\Controllers\CartController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\UploadController;

use App\Http\Controllers\ConvergeController;

use App\Http\Controllers\SettingController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes (for now)
// Protected admin routes
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('products', ProductController::class);
//     // other admin routes...
// });
// Route::apiResource('products', ProductController::class);
// Route::post('/products', [ProductController::class, 'store']);

// Public read (or send token from admin — either works)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
// Protected write
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('users', UserController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Specific routes first
Route::get('/orders/by-number/{orderNumber}', [OrderController::class, 'showByNumber']);

// Public reads (if you want them public)
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);

// Protected writes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::patch('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
});

Route::apiResource('packages', PackageController::class); //

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);      // main add
    Route::post('/add', [CartController::class, 'store']);   // alias so /cart/add works
    Route::delete('/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
});

Route::apiResource('package-categories', PackageCategoryController::class);
// Feature management
Route::post('/packages/{id}/features', [PackageController::class, 'addFeature']);
Route::delete('/packages/{id}/features/{index}', [PackageController::class, 'removeFeature']);

Route::post('/upload/option-image', [UploadController::class, 'uploadOptionImage']);

// Or protect them:
// Route::apiResource('products', ProductController::class)->middleware('auth:sanctum');

Route::post('/converge/token', [ConvergeController::class, 'token']);
Route::post('/converge/complete', [ConvergeController::class, 'complete']);


// Settings - Public (storefront)
Route::get('/settings/public', [SettingController::class, 'publicIndex']);

// Admin (auth + role checked in controller)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);
});