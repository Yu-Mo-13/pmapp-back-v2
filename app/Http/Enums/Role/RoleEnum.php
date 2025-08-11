<?php

namespace App\Http\Enums\Role;

use BenSampo\Enum\Enum;

final class RoleEnum extends Enum
{
    public const ADMIN = 'ADMIN';
    public const WEB_USER = 'WEB_USER';
    public const MOBILE_USER = 'MOBILE_USER';

    /**
     * Get the description for an enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        switch ($value) {
            case self::ADMIN:
                return 'システム管理者';
            case self::WEB_USER:
                return 'WEB一般ユーザー';
            case self::MOBILE_USER:
                return 'Mobile一般ユーザー';
            default:
                return parent::getDescription($value);
        }
    }
}