<?php

use App\Http\Controllers\Healthcheck\HealthcheckCreateController;
use App\Http\Controllers\Healthcheck\HealthcheckStatusShowController;

Route::prefix('/healthchecks')->group(function () {
    Route::post('/', HealthcheckCreateController::class)
        ->name('healthchecks.create');

    Route::get('/status', HealthcheckStatusShowController::class)
        ->name('healthchecks.status');
});
