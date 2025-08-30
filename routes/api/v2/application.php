<?php

// アプリケーション管理

use App\Http\Controllers\Application\ApplicationIndexController;
use App\Http\Controllers\Application\ApplicationCreateController;

Route::prefix('/applications')->group(function () {
    Route::get('/', ApplicationIndexController::class)
        ->name('applications.index');
    Route::post('/', ApplicationCreateController::class)
        ->name('applications.create');
});
