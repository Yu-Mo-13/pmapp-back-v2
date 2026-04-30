<?php

// アカウント管理
use App\Http\Controllers\Account\AccountIndexController;
use App\Http\Controllers\Account\AccountCreateController;
use App\Http\Controllers\Account\AccountApplicationIndexController;
use App\Http\Controllers\Account\AccountShowController;
use App\Http\Controllers\Account\AccountDeleteController;
use App\Http\Controllers\Account\AccountUpdateController;
use App\Http\Enums\Role\RoleEnum;

Route::prefix('/accounts')->group(function () {
    Route::middleware([
        'auth:api',
        'can:' . RoleEnum::ADMIN . ',' . RoleEnum::WEB_USER . ',' . RoleEnum::MOBILE_USER,
    ])->group(function () {
        Route::get('/', AccountIndexController::class)
            ->name('accounts');
    });

    Route::middleware(['auth:api', 'can:' . RoleEnum::ADMIN])->group(function () {
        Route::get('/applications', AccountApplicationIndexController::class)
            ->name('accounts.applications');
        Route::get('/{account}', AccountShowController::class)
            ->name('account');
        Route::post('/', AccountCreateController::class)
            ->name('accounts');
        Route::put('/{account}', AccountUpdateController::class)
            ->name('account');
        Route::delete('/{account}', AccountDeleteController::class)
            ->name('account');
    });
});
