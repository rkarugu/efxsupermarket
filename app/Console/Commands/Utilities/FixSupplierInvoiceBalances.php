<?php

namespace App\Console\Commands\Utilities;

use App\Model\WaSuppTran;
use Illuminate\Console\Command;

class FixSupplierInvoiceBalances extends Command
{
    protected $signature = 'supplier-invoices:fix-balances {supplier-code?}';

    protected $description = 'fix allocated amounts for supplier invoices';

    public function handle()
    {
        $invoices = WaSuppTran::query()
            ->withSum([
                'payments' => function ($query) {
                    $query->whereHas('voucher', function ($query) {
                        $query->processed();
                    });
                }
            ], 'amount')
            ->when($this->argument('supplier-code'), function ($query) {
                $query->where('supplier_no', $this->argument('supplier-code'));
            })
            ->get();

        $this->line($invoices->count() . ' invoices found');

        $bar = $this->output->createProgressBar(count($invoices));

        $bar->start();

        foreach ($invoices as $invoice) {
            $bar->advance();
            if (!$invoice->payments_sum_amount) {
                $invoice->allocated_amount = $invoice->payments_sum_amount ?? 0;
                $invoice->settled = 0;
                $invoice->save();

                continue;
            }

            $balance = $invoice->total_amount_inc_vat - $invoice->withholding_amount - $invoice->payments_sum_amount;
            if ($balance == 0) {
                $invoice->allocated_amount = $invoice->payments_sum_amount;
                $invoice->settled = 1;
                $invoice->save();

                continue;
            }

            $invoice->allocated_amount = $invoice->payments_sum_amount ?? 0;
            $invoice->settled = 0;
            $invoice->save();
        }

        $bar->finish();
    }
}
