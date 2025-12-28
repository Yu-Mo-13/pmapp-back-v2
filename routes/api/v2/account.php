<?php

// アプリケーション管理
use App\Http\Controllers\Account\AccountIndexController;

Route::prefix('/accounts')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', AccountIndexController::class)
            ->name('accounts.index');
    });
});
