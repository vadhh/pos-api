<?php

use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->group(function () {
    // Product Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Customer Routes
    Route::apiResource('customers', CustomerController::class);

    // Sale Routes
    Route::apiResource('sales', SaleController::class);
    Route::get('sales/{sale}/items', [SaleController::class, 'items'])->name('sales.items');
});