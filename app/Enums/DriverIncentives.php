<?php

namespace App\Enums;

enum DriverIncentives: string
{
    case SHIFT_BY_6AM = 'shift_by_6am';
    case LOAD_PREV_DAY = 'load_prev_day';
    case BACK_ON_TIME = 'back_on_time';
    case SYSTEM_USAGE = 'system_usage';
    case FUEL = 'fuel';
}
