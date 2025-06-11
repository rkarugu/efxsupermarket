<?php

namespace App\Enums;

enum PackSizeEnum: int
{
    case SIZE_8 = 8;
    case SIZE_7 = 7;
    case SIZE_3 = 3;
    case SIZE_5 = 5;
    case SIZE_2 = 2;
    case SIZE_4 = 4;
    case SIZE_13 = 13;

    public static function ids(): array
    {
        return [
            self::SIZE_8->value,
            self::SIZE_7->value,
            self::SIZE_3->value,
            self::SIZE_5->value,
            self::SIZE_2->value,
            self::SIZE_4->value,
            self::SIZE_13->value,
        ];
    }
}
