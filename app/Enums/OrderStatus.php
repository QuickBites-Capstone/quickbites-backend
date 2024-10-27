<?php

namespace App\Enums;

enum OrderStatus:int
{
    case Pending = 1;
    case InProgress = 2;
    case Ready = 3;
    case Complete = 4;
    case Cancelled = 5;
}
