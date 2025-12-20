<?php

// 認証

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LoginStatusController;

Route::post('/login', LoginController::class)
    ->name('auth.login');

Route::get('/login/status', LoginStatusController::class)
    ->middleware('auth:api')
    ->name('auth.login.status');
