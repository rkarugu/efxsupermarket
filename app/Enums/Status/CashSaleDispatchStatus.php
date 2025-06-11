<?php

namespace App\Enums\Status;

enum CashSaleDispatchStatus: string
{
    case dispatched='dispatched';
    case dispatching = 'dispatching';
    case collected = 'Collected';
}