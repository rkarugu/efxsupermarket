<?php

namespace App\Console\Commands\Telematics;

use App\NewFuelEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireFuelLpos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-fuel-lpos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lpos = NewFuelEntry::where('entry_status', 'pending')->get();
        foreach ($lpos as $lpo) {
            $lpo->entry_status = 'expired';
            $lpo->save();
        }
    }
}
