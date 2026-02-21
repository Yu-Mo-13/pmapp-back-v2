<?php

// アプリケーション管理

use App\Http\Controllers\Application\ApplicationIndexController;
use App\Http\Controllers\Application\ApplicationCreateController;
use App\Http\Controllers\Application\ApplicationShowController;
use App\Http\Controllers\Application\ApplicationUpdateController;
use App\Http\Controllers\Application\ApplicationDeleteController;

Route::prefix('/applications')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', ApplicationIndexController::class)
            ->name('applications.index');
        Route::post('/', ApplicationCreateController::class)
            ->name('applications.create');
        Route::get('/{application}', ApplicationShowController::class)
            ->name('applications.show');
        Route::put('/{application}', ApplicationUpdateController::class)
            ->name('applications.update');
        Route::delete('/{application}', ApplicationDeleteController::class)
            ->name('applications.delete');
    });
});
