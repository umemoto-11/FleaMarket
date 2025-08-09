<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

enum Condition: int
{
    case Good =   1;
    case NoMajorDamage =   2;
    case SlightlyDirty = 3;
    case Bad = 4;

    public function label(): string
    {
        return match($this)
        {
            self::Good => '良好',
            self::NoMajorDamage => '目立った傷や汚れなし',
            self::SlightlyDirty => 'やや傷や汚れあり',
            self::Bad => '状態が悪い',
        };
    }
}
