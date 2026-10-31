<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CouponController;
use App\Http\Controllers\api\OfferController;
use App\Http\Controllers\api\WebsiteSettingController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ProfileController;


Route::post('products/sync', [ProductController::class, 'syncStore']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Route::put('users', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');

// Profile
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth:sanctum');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth:sanctum');



Route::get('categories', [CategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);



Route::get('products/{slug}', [ProductController::class, 'show']);



Route::post('orders', [OrderController::class, 'create']);
// Route::get('orders', [OrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('user/orders', [OrderController::class, 'userOrders'])->middleware('auth:sanctum');
Route::get('user/orders/{invoice_no}', [OrderController::class, 'show'])->middleware('auth:sanctum');

Route::post('/get-coupon', [CouponController::class, 'getCouponByCode']);
Route::get('/website-settings', [WebsiteSettingController::class, 'index']);

Route::get('/offers', [OfferController::class, 'index']);
Route::get('/offers/check-offer/{slug}', [ProductController::class, 'checkOffer']);
Route::get('/offers/{slug}/products', [OfferController::class, 'products']);

