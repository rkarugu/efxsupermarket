<?php

namespace App\Console\Commands\Utilities;

use App\FinancialNote;
use Illuminate\Console\Command;

class AddWithholdingToNotes extends Command
{
    protected $signature = 'finanacial-notes:withholding';


    protected $description = 'Add withholding tax to previous financial notes';

    public function handle()
    {
        $notes = FinancialNote::with('items')
            ->whereHas('supplier', function ($query) {
                $query->where('tax_withhold', 1);
            })
            ->get();

        $this->line('Updating financial notes withholding amounts');

        $notes->each(function ($note) {
            $withholding = 0;
            $note->items->each(function ($item) use (&$withholding) {
                $withholding += $withAmt = ceil($item->tax_amount * (2 / 16));
                $item->withholding_amount = $withAmt;
                $item->save();
            });

            $note->withholding_amount = $withholding;
            $note->save();
        });

        $this->line('Updating financial notes withholding amounts completed');
    }
}
