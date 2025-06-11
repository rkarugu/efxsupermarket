<?php

namespace App\Console\Commands\Telematics;

use App\Models\VehicleExemptionSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateVehicleExemptionSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-vehicle-exemption-schedules';

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
        $now = Carbon::now()->toDateTimeString();
        $schedules = [
            [
                'schedule_type'=>'5pm Switch Off',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'schedule_type'=>'10pm Switch Off',
                'created_at'=>$now,
                'updated_at'=>$now
            ],
            [
                'schedule_type'=>'4am Switch On',
                'created_at'=>$now,
                'updated_at'=>$now],
        ];
        VehicleExemptionSchedule::insert($schedules);
    }
}
