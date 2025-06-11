<?php

namespace App\Console\Commands\Utilities;

use App\Model\WaPurchaseOrder;
use Illuminate\Console\Command;

class UpdateOrderToShowMissingGrns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:missing-grns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Return missing grns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = WaPurchaseOrder::query()
            ->where('invoiced', 'yes')
            ->get();            

        $this->line($orders->count().' orders found');

        $this->line('updating started');

        foreach ($orders as $order) {
            $balance = $order->getRelatedItem->sum('total_cost_with_vat') -
                $order->grns->sum(function ($grn) {
                    $invoice_info = json_decode($grn->invoice_info);
                    return ((float)$invoice_info->order_price * (float)$invoice_info->qty) * ((float)$invoice_info->vat_rate / 100);
                }) -
                $order->invoices->sum('total_amount_inc_vat');

            $order->invoiced = $balance == 0 ? 'Yes' : 'No';
            $order->save();
        }

        $this->line('updating finished');
    }
}
