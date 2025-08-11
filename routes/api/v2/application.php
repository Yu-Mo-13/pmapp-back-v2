<?php

// アプリケーション管理

use App\Http\Controllers\Application\ApplicationIndexController;

Route::get('/applications', ApplicationIndexController::class)
    ->name('applications.index');
