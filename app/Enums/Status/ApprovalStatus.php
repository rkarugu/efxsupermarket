<?php

namespace App\Enums\Status;

enum ApprovalStatus:string
{
    case Approved='Approved';
    case PendingNewApproval='Pending New Approval';
    case PendingEditApproval='Pending Edit Approval';
    case Disapproved='Disapproved';
    case Rejected = 'Rejected';
}