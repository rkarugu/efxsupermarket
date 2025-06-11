<?php

namespace App\Listeners\SupplierInvoice;

use App\Actions\SupplierInvoice\CreateTradeDiscount;
use App\Events\SupplierInvoice\SupplierInvoiceCreated;

class ApplyTradeDiscount
{
    public function handle(SupplierInvoiceCreated $event): void
    {
        app(CreateTradeDiscount::class)->create($event->invoice);
    }
}
