<?php

namespace App\Enums;

enum FuelEntryStatus: string
{
    case Pending = 'pending';
    case FueledIncomplete = 'fueled_incomplete';
    case Fueled = 'fueled';
    case Verified = 'verified';
    case Approved = 'approved';
    case Processed = 'processed';
    case Expired = 'expired';
    case Reactivated = 'reactivated';
}
