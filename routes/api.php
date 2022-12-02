<?php

use App\Http\Controllers\Gateway\v1\GatewayController;
use Illuminate\Support\Facades\Route;

require 'auth.php';

Route::prefix('payment')->middleware('auth:sanctum')->group(function () {
    // ex checkout
    Route::post('/', [GatewayController::class, 'pay'])->name('payment.pay');
    // auto submit
    Route::get('navigate', [GatewayController::class, 'navigate'])->name('payment.navigate');
    // verify it
    Route::post('verify', [GatewayController::class, 'verify'])->name('payment.verify');
});
