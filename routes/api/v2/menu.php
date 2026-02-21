<?php

use App\Http\Controllers\Menu\MenuIndexController;

Route::get('/menus', MenuIndexController::class)
    ->name('menus');
