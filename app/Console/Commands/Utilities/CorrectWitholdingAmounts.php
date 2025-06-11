<?php

namespace App\Console\Commands\Utilities;

use App\Models\WaWithholdingFile;
use Illuminate\Console\Command;

class CorrectWitholdingAmounts extends Command
{
    protected $signature = 'withholding-files:correct-amounts';

    protected $description = 'Correct withholding amounts for each file';

    public function handle()
    {
        $files = WaWithholdingFile::with('items.bankFile')->get();

        $this->line('Correcting withholding file amounts');

        $bar = $this->output->createProgressBar(count($files));

        $bar->start();

        foreach ($files as $file) {
            $bar->advance();
            $amount = 0;
            foreach ($file->items as $fileItem) {
                $itemsAmount = 0;
                foreach ($fileItem->bankFile->items as $bankFileItem) {
                    $itemsAmount += $bankFileItem->voucher->withholding_amount;
                }

                $fileItem->update([
                    'amount' => $itemsAmount
                ]);                

                $amount += $itemsAmount;
            }

            $file->update([
                'amount' => $amount
            ]);
        }

        $bar->finish();

        $this->newLine();

        $this->comment('Correcting withholding file amounts completed');
    }
}
