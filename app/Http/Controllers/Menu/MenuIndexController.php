<?php

namespace App\Http\Controllers\Menu;

use App\Helpers\ApiResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Enums\Role\RoleEnum;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MenuIndexController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            return ApiResponseFormatter::ok([]);
        }

        $roleCode = $user->role?->code;
        $visibilityColumn = $this->resolveVisibilityColumn($roleCode);
        if ($visibilityColumn === null) {
            return ApiResponseFormatter::ok([]);
        }

        $menus = Menu::query()
            ->where($visibilityColumn, true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['name', 'path'])
            ->toArray();

        return ApiResponseFormatter::ok($menus);
    }

    private function resolveVisibilityColumn(?string $roleCode): ?string
    {
        if ($roleCode === RoleEnum::ADMIN) {
            return 'admin_visible';
        }

        if ($roleCode === RoleEnum::WEB_USER) {
            return 'web_user_visible';
        }

        if ($roleCode === RoleEnum::MOBILE_USER) {
            return 'mobile_user_visible';
        }

        return null;
    }
}
