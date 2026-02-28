<?php

// パスワード本登録

use App\Http\Controllers\Password\PasswordCreateController;

Route::prefix('/passwords')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/', PasswordCreateController::class)
            ->name('passwords.create');
    });
});
