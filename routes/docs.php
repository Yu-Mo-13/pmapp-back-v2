<?php

use App\Http\Controllers\Docs\OpenApiDocumentationShowController;
use App\Http\Controllers\Docs\OpenApiSpecificationShowController;
use Illuminate\Support\Facades\Route;

Route::get('/docs/openapi.json', OpenApiSpecificationShowController::class)
    ->name('docs.openapi');

Route::get('/docs/api', OpenApiDocumentationShowController::class)
    ->name('docs.api');
