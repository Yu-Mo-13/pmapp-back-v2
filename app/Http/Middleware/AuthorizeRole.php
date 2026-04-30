<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseFormatter;
use Closure;
use Illuminate\Http\Request;

class AuthorizeRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        $roleCode = optional($user?->role)->code;

        if (!in_array($roleCode, $roles, true)) {
            return ApiResponseFormatter::notfound();
        }

        return $next($request);
    }
}
