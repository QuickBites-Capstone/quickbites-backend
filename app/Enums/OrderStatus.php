<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 1;
    case InProgress = 2;
    case Completed = 3;
    case Canceled = 4;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
            self::Canceled => 'Canceled',
        };
    }
}
