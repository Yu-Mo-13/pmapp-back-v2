<?php

// アカウント管理
use App\Http\Controllers\Account\AccountIndexController;
use App\Http\Controllers\Account\AccountCreateController;
use App\Http\Controllers\Account\AccountApplicationIndexController;

Route::prefix('/accounts')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', AccountIndexController::class)
            ->name('accounts');
        Route::get('/applications', AccountApplicationIndexController::class)
            ->name('accounts.applications');
        Route::post('/', AccountCreateController::class)
            ->name('accounts');
    });
});
