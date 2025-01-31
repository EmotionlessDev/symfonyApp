<?php

namespace App\Enum;

enum ActionEnum: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    public function getOpposite(): self
    {
        return match ($this) {
            self::BUY => self::SELL,
            self::SELL => self::BUY,
        };
    }
}


