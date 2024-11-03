<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 1;
    case InProgress = 2;
    case Ready = 3;
    case Completed = 4;
    case Cancelled = 5;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Ready => 'Ready for pick-up',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
