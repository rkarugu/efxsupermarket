<?php

namespace App\Console\Commands\Utilities;

use App\CustomerEquityPayment;
use App\WaTenderEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateTenderEntries extends Command
{
    protected $signature = 'tender-entries:update';

    protected $description = 'Fix previous transactions to separate reference';

    public function handle()
    {
        $entries = WaTenderEntry::query()->get();
        $entries->each(function ($entry) {
            $entry->update([
                'reference' => trim($entry->reference)
            ]);
        });

        $payments = CustomerEquityPayment::query()
            ->with('tenderEntry')
            ->whereHas('tenderEntry')
            ->get();

        $this->line("Updating {$payments->count()} trender entries");

        foreach ($payments as $payment) {
            if ($payment->tenderEntry) {
                $payment->tenderEntry->update([
                    'additional_info' => $payment->narrative,
                    'trans_date' => $payment->created_at,
                ]);
            }
        }

        $this->line("Updating trender entries completed");
    }
}
