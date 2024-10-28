<?php

namespace App\Enums;

enum ProductStatus: int
{
    case Available = 1;
    case SoldOut = 2;

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::SoldOut => 'Sold Out',
       
        };
    }
}
