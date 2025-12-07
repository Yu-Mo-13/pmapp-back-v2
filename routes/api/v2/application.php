<?php

// アプリケーション管理

use App\Http\Controllers\Application\ApplicationIndexController;
use App\Http\Controllers\Application\ApplicationCreateController;
use App\Http\Controllers\Application\ApplicationShowController;
use App\Http\Controllers\Application\ApplicationDeleteController;

Route::prefix('/applications')->group(function () {
    Route::get('/', ApplicationIndexController::class)
        ->name('applications.index');
    Route::post('/', ApplicationCreateController::class)
        ->name('applications.create');
    Route::get('/{application}', ApplicationShowController::class)
        ->name('applications.show');
    Route::delete('/{application}', ApplicationDeleteController::class)
        ->name('applications.delete');
});
