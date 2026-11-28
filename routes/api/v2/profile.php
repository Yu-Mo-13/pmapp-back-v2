<?php

// マイプロフィール

use App\Http\Controllers\Profile\ProfileEmailUpdateController;
use App\Http\Controllers\Profile\ProfilePasswordUpdateController;
use App\Http\Controllers\Profile\ProfileShowController;
use App\Http\Enums\Role\RoleEnum;

Route::prefix('/profile')->group(function () {
    Route::middleware([
        'auth:api',
        'can:' . RoleEnum::ADMIN . ',' . RoleEnum::WEB_USER . ',' . RoleEnum::MOBILE_USER,
    ])->group(function () {
        Route::get('/', ProfileShowController::class)
            ->name('profile.show');
        Route::put('/email', ProfileEmailUpdateController::class)
            ->name('profile.email.update');
        Route::put('/password', ProfilePasswordUpdateController::class)
            ->name('profile.password.update');
    });
});
