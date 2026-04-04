<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class OpenApiDocumentationShowController extends Controller
{
    public function __invoke(): View
    {
        return view('docs.openapi', [
            'title' => config('app.name') . ' API Docs',
            'specUrl' => route('docs.openapi', [
                'v' => md5_file(app_path('Services/OpenApiSpecificationFactory.php')) ?: '1',
            ]),
        ]);
    }
}
