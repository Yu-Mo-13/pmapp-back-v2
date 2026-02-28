<?php

// 仮登録パスワード管理

use App\Http\Controllers\PreregistedPassword\PreregistedPasswordDeleteController;
use App\Http\Controllers\PreregistedPassword\PreregistedPasswordIndexController;
use App\Http\Controllers\PreregistedPassword\PreregistedPasswordShowController;

Route::prefix('/preregisted-passwords')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', PreregistedPasswordIndexController::class)
            ->name('preregisted-passwords.index');
        Route::get('/{preregistedPassword}', PreregistedPasswordShowController::class)
            ->name('preregisted-passwords.show');
        Route::delete('/{preregistedPassword}', PreregistedPasswordDeleteController::class)
            ->name('preregisted-passwords.delete');
    });
});
