<?php

namespace App\Console\Commands;

use App\Model\Restaurant;
use App\Models\EndOfDayRoutine;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateEodRoutineRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-eod-routine-records';

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
        $now = Carbon::now()->toDateString();
        $restaurants = Restaurant::whereIn('id', [1,10])->get();
        foreach ($restaurants as $restaurant) {
            $documentNo = getCodeWithNumberSeries('END_OF_DAY');
            updateUniqueNumberSeries('END_OF_DAY', $documentNo);
            $eodRoutine =new EndOfDayRoutine(); 
            $eodRoutine->day = $now;
            $eodRoutine->branch_id = $restaurant->id;
            $eodRoutine->routine_no = $documentNo;
           $eodRoutine->save();

        }

    }
}
