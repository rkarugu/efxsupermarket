<?php

namespace App\Console\Commands;

use App\Model\Setting;
use App\Model\WaPurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ArchivePurchaseOrders extends Command
{
    protected $signature = 'purchase-orders:archive';

    protected $description = 'Archive purchase orders not received after 60 days';

    public function handle()
    {
        $days = Setting::where('name', 'UNDELIVERED_LPO_EXPRIRY')->first()->description;

        $start = now()->subDays($days)->startOfDay();

        $orders = WaPurchaseOrder::whereNotIn('status', ['PRELPO', 'COMPLETED'])
            ->where('is_hide', 'No')
            ->where('created_at', '<', $start)
            ->doesntHave('grns');

        $this->line($orders->count() . ' records found');

        foreach ($orders->get() as $order) {
            $order->update([
                'is_hide' => 'Yes'
            ]);
        }

        $this->comment('archiving purchase orders completed');
    }
}
