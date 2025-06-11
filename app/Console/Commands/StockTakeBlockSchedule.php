<?php

namespace App\Console\Commands;

use App\Models\BlockUsersExemptionSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class StockTakeBlockSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stock-take-block-schedule';

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
        //create a schedule for the next day
        $today = Carbon::now()->addDay()->toDateString();
        $schedule = new  BlockUsersExemptionSchedule();
        $schedule->target_date = $today;
        $schedule->save();
    }
}
