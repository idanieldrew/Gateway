<?php

use App\Http\Controllers\auth\v1\AuthController;
use Illuminate\Support\Facades\Route;

// register
Route::post('register', [AuthController::class, 'register'])->name('register');
