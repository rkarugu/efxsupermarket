<?php

namespace App\Enums\Status;

enum PaymentVerification: string
{
    case Verifying = 'Verifying';
    case Processing = 'Processing';
    case PartiallyVerified = 'Partially Verified';
    case Verified = 'Verified';
    case Pending = 'Pending';
    case Approved = 'Approved';
    case PartiallyApproved = 'Partially Approved';
    case Discard = 'Discard';
    case Duplicate = 'Duplicate';
    case SameReference = 'Same Reference';
}
