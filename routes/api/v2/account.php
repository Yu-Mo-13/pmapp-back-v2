<?php

// アカウント管理
use App\Http\Controllers\Account\AccountIndexController;
use App\Http\Controllers\Account\AccountCreateController;
use App\Http\Controllers\Account\AccountApplicationIndexController;
use App\Http\Controllers\Account\AccountShowController;
use App\Http\Controllers\Account\AccountDeleteController;

Route::prefix('/accounts')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', AccountIndexController::class)
            ->name('accounts');
        Route::get('/{account}', AccountShowController::class)
            ->name('account');
        Route::get('/applications', AccountApplicationIndexController::class)
            ->name('accounts.applications');
        Route::post('/', AccountCreateController::class)
            ->name('accounts');
        Route::delete('/{account}', AccountDeleteController::class)
            ->name('account');
    });
});
