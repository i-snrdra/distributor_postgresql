<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\PurchaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes untuk Supplier
Route::apiResource('suppliers', SupplierController::class);

// API Routes untuk Product
Route::apiResource('products', ProductController::class);

// API Routes untuk Sales (Penjualan)
Route::apiResource('sales', SaleController::class);

// API Routes untuk Purchases (Pembelian)
Route::apiResource('purchases', PurchaseController::class);

// Additional route untuk mendapatkan produk berdasarkan supplier
Route::get('suppliers/{supplier}/products', [ProductController::class, 'getBySupplier']); 