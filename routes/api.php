<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v2')->group(function () {
    require base_path('routes/api/v2/auth.php');
    require base_path('routes/api/v2/application.php');
    require base_path('routes/api/v2/account.php');
    require base_path('routes/api/v2/menu.php');
    Route::get('check', function () {
        return response()->json(['status' => 'ok']);
    });
});
