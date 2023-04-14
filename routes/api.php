<?php

use App\Http\Controllers\Cart\v1\CartController;
use App\Http\Controllers\Order\v1\OrderController;
use App\Http\Controllers\Payment\v1\PaymentController;
use Illuminate\Support\Facades\Route;

require 'auth.php';

Route::prefix('shop/v1')->middleware('auth:api')->group(function () {
    // append to cart
    Route::post('cart/{product:slug}/add', [CartController::class, 'store'])->name('cart.store');

    // show cart
    Route::get('cart', [CartController::class, 'show'])->name('cart.show');

    // submit order
    Route::post('orders', [OrderController::class, 'submitOrder'])->name('order.store');

    // link
    Route::post('payment', [PaymentController::class, 'store'])->name('payment.port');

    Route::post('welcome', [PaymentController::class, 'callback'])->name('welcome');
//    Route::post('verify', [PaymentController::class, 'verify'])->name('payment.verify');
});
