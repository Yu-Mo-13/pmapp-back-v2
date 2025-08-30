<?php

namespace App\Http\Enums\Application;

use BenSampo\Enum\Enum;

class ApplicationNoticeClassEnum extends Enum
{
    const NEED_NOTICE = '1';
    const NO_NEED_NOTICE = '0';

    /**
     * Get the description for an enum value
     *
     * @param mixed $value
     * @return string
     */
    public static function getDescription(mixed $value): string
    {
        return match ($value) {
            self::NEED_NOTICE => '定期通知対象',
            self::NO_NEED_NOTICE => '定期通知対象外',
            default => '不明',
        };
    }
}
