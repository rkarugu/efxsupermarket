<?php

namespace App\Console\Commands;

use App\Model\Restaurant;
use App\Services\OperationShiftService;
use Illuminate\Console\Command;

class RunEndOfDayChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-end-of-day-checks';

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
        /*get all branches*/
//        $branches = Restaurant::all();
//        foreach ($branches as $branch) {
//            $service =new OperationShiftService($branch);
//            $service->index();
//        }

        /*run for makongeni branch only*/
        $branch = Restaurant::find(10);
        $service =new OperationShiftService($branch);
        $service->index();
    }
}
