<?php

namespace App\Jobs;

use App\Model\WaInternalRequisition;
use App\Model\WaStockMove;
use App\Services\SupplierIncentiveCalculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PerformPostReturnActions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public WaStockMove $stockMove) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->recordSupplierIncentives();
    }

    public function recordSupplierIncentives(): void
    {

        SupplierIncentiveCalculator::add($this->stockMove);
    }
}
