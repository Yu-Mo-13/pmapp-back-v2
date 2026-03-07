<?php

// パスワード本登録

use App\Http\Controllers\Password\PasswordCreateController;
use App\Http\Controllers\Password\PasswordIndexController;

Route::prefix('/passwords')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', PasswordIndexController::class)
            ->name('passwords.index');
        Route::post('/', PasswordCreateController::class)
            ->name('passwords.create');
    });
});
