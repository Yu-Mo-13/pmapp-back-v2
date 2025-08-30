<?php

namespace App\Http\Enums\Application;

use BenSampo\Enum\Enum;

class ApplicationAccountClassEnum extends Enum
{
    const NEED_ACCOUNT = '1';
    const NO_NEED_ACCOUNT = '0';

    /**
     * Get the description for an enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::NEED_ACCOUNT => 'アカウント要登録',
            self::NO_NEED_ACCOUNT => 'アカウント登録不要',
            default => '不明',
        };
    }
}
