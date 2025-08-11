<?php

// 認証

use App\Http\Controllers\Auth\LoginController;

Route::post('/login', LoginController::class)
    ->name('auth.login');
