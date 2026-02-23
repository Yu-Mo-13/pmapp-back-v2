<?php

// 未登録パスワード管理

use App\Http\Controllers\UnregistedPassword\UnregistedPasswordDeleteAllController;
use App\Http\Controllers\UnregistedPassword\UnregistedPasswordIndexController;
use App\Http\Controllers\UnregistedPassword\UnregistedPasswordShowController;

Route::prefix('/unregisted-passwords')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', UnregistedPasswordIndexController::class)
            ->name('unregisted-passwords.index');
        Route::get('/{unregistedPassword}', UnregistedPasswordShowController::class)
            ->name('unregisted-passwords.show');
        Route::delete('/', UnregistedPasswordDeleteAllController::class)
            ->name('unregisted-passwords.delete-all');
    });
});
