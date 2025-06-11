<?php

namespace App\Enums;

enum FuelEntryParentTypes: string
{
    case RouteDelivery = 'Route Deliveries';
    case InterBranchTransfer = 'Inter Branch Transfer';
    case SupplierCollection = 'Supplier Collection';
    case Miscellaneous = 'Miscellaneous';
}
