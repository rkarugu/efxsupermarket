<?php

namespace App\Console\Commands\Utilities;

use App\Model\WaPurchaseOrderItem;
use Illuminate\Console\Command;

class CalculateTotalLpoDiscount extends Command
{
    protected $signature = 'lpo:other-discounts';

    protected $description = 'Calculate value of other discounts apart from base discount';

    public function handle()
    {
        $this->comment('Adding other discounts total');

        $purchaseOrderItems = WaPurchaseOrderItem::whereNotNull('discount_settings')->get();

        $bar = $this->output->createProgressBar(count($purchaseOrderItems));

        $bar->start();

        foreach ($purchaseOrderItems as $purchaseOrderItem) {
            $purchaseOrderItem->other_discounts_total = $this->getTotalDiscount(
                $purchaseOrderItem->order_price,
                $purchaseOrderItem->quantity,
                $purchaseOrderItem,
            );

            $purchaseOrderItem->save();

            $bar->advance();
        }

        $bar->finish();

        $this->newLine();

        $this->comment('Adding othe discounts total completed');
    }

    private function getTotalDiscount($price, $qty, $orderItem)
    {
        $baseDiscount = 0;
        $invoice_discount = 0;
        $transport_rebate = 0;
        $distribution_discount = 0;

        $settings = json_decode($orderItem->discount_settings);
        if (!$settings) {
            return 0;
        }

        if (isset($settings->base_discount_type)) {
            $baseDiscount = ($settings->base_discount_type == 'Value' ? $orderItem->discount_percentage * $qty : ($price * $orderItem->discount_percentage / 100) * $qty);
        }

        $invoiceAmount = $price * $qty -  $baseDiscount;
        $inv_percentage = (float) (isset($settings->invoice_percentage) ? $settings->invoice_percentage : 0);
        $invoice_discount += ($invoiceAmount * $inv_percentage) / 100;
        $transport_rebate_per_unit = (float) isset($settings->transport_rebate_per_unit) ? $settings->transport_rebate_per_unit : 0;
        $transport_rebate_percentage = (float) isset($settings->transport_rebate_percentage) ? $settings->transport_rebate_percentage : 0;
        $transport_rebate_per_tonnage = (float) isset($settings->transport_rebate_per_tonnage) ? $settings->transport_rebate_per_tonnage : 0;
        $distribution_discount = (float) isset($settings->distribution_discount) ? $settings->distribution_discount * $qty : 0;
        if ($transport_rebate_per_unit > 0) {
            $transport_rebate += $transport_rebate_per_unit * $qty;
        } elseif ($transport_rebate_percentage > 0) {
            $transport_rebate += ($invoiceAmount * $transport_rebate_percentage) / 100;
        } elseif ($transport_rebate_per_tonnage > 0) {
            $transport_rebate += $transport_rebate_per_tonnage * $orderItem->measure;
        }

        // We do not include the base discount since it has already been 
        // deducted from the total amount
        return round($invoice_discount + $transport_rebate + $distribution_discount, 2);
    }
}
