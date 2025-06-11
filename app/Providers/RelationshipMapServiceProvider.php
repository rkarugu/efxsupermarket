<?php

namespace App\Providers;

use App\Model\WaSuppTran;
use App\Models\AdvancePayment;
use App\Models\SupplierBill;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class RelationshipMapServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(): void
    {
        Relation::morphMap([
            'invoice' => WaSuppTran::class,
            'advance' => AdvancePayment::class,
            'bill' => SupplierBill::class,
        ]);
    }
}
