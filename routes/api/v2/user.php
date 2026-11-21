<?php

use App\Http\Controllers\User\UserIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/users', UserIndexController::class)->name('users.index');
