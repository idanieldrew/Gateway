<?php

use App\Http\Controllers\Auth\v1\AuthController;
use Illuminate\Support\Facades\Route;

// register
Route::post('register', [AuthController::class, 'register'])->name('register');

// login
Route::post('login', [AuthController::class, 'login'])->name('login');
