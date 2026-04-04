<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Services\OpenApiSpecificationFactory;
use Illuminate\Http\JsonResponse;

class OpenApiSpecificationShowController extends Controller
{
    public function __invoke(OpenApiSpecificationFactory $factory): JsonResponse
    {
        return response()->json($factory->make());
    }
}
