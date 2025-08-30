<?php

namespace App\Http\Enums\Application;

use BenSampo\Enum\Enum;

class ApplicationMarkClassEnum extends Enum
{
    const NEED_MARK = '1';
    const NO_NEED_MARK = '0';

    /**
     * Get the description for an enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::NEED_MARK => 'パスワードに記号を含む',
            self::NO_NEED_MARK => 'パスワードに記号を含まない',
            default => '不明',
        };
    }
}
