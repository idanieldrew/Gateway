<?php

use App\Http\Controllers\Cart\v1\CartController;
use App\Http\Controllers\Order\v1\OrderController;
use App\Http\Controllers\Payment\v1\PaymentController;
use Illuminate\Support\Facades\Route;

require 'auth.php';

Route::prefix('shop/v1')->middleware('auth:sanctum')->group(function () {
    // append to cart
    Route::post('cart/{product:slug}/add', [CartController::class, 'store'])->name('cart.store');

    // submit order
    Route::post('/orders/{cart}', [OrderController::class, 'submitOrder'])->name('order.store');

    // link
    Route::post('payment/{order}', [PaymentController::class, 'store'])->name('payment.port');
});

/*Route::prefix('payment')->middleware('auth:sanctum')->group(function () {
    // ex checkout
    Route::post('/', [GatewayController::class, 'pay'])->name('payment.pay');
    // auto submit
    Route::get('navigate', [GatewayController::class, 'navigate'])->name('payment.navigate');
    // verify it
    Route::post('verify', [GatewayController::class, 'verify'])->name('payment.verify');
});*/
