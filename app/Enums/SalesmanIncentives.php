<?php

namespace App\Enums;

enum SalesmanIncentives: string
{
    case TONNAGE = 'Tonnage';
    case PACK_TYPE = 'pack_type';
    case MET_CUSTOMERS = 'met_customers';
    case ONSITE = 'onsite';
    case EARLY_SHIFTS = 'early_shifts';
    case RETURNS = 'returns';
    case TIME_MANAGEMENT = 'time_management';
    case PAY_ON_DELIVERY = 'pay_on_delivery';

    case SHIFT_BY_6AM = 'shift_by_6am';
    case LOAD_PREV_DAY = 'load_prev_day';
    case BACK_ON_TIME = 'back_on_time';
    case SYSTEM_USAGE = 'system_usage';
    case FUEL = 'fuel';
}