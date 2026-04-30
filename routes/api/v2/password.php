<?php

// パスワード本登録

use App\Http\Controllers\Password\PasswordCreateController;
use App\Http\Controllers\Password\PasswordIndexController;
use App\Http\Controllers\Password\PasswordLatestShowController;
use App\Http\Controllers\Password\PasswordUpdatePromoteIndexController;
use App\Http\Enums\Role\RoleEnum;

Route::prefix('/passwords')->group(function () {
    Route::middleware(['auth:api', 'can:' . RoleEnum::ADMIN . ',' . RoleEnum::WEB_USER . ',' . RoleEnum::MOBILE_USER])->group(function () {
        Route::get('/', PasswordIndexController::class)
            ->name('passwords.index');
        Route::get('/latest', PasswordLatestShowController::class)
            ->name('passwords.latest');
        Route::post('/', PasswordCreateController::class)
            ->name('passwords.create');
    });
});

Route::middleware(['auth:api', 'can:' . RoleEnum::ADMIN . ',' . RoleEnum::WEB_USER . ',' . RoleEnum::MOBILE_USER])->group(function () {
    Route::get('/password-update-promote', PasswordUpdatePromoteIndexController::class)
        ->name('password-update-promote.index');
});
