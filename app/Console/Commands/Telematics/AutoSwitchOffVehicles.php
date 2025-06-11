<?php

namespace App\Console\Commands\Telematics;

use App\Interfaces\SmsService;
use App\Models\VehicleExemptionSchedule;
use App\Models\VehicleImmobilization;
use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSwitchOffVehicles extends Command
{
    public function __construct(protected SmsService $smsService)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-switch-off-vehicles';

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
        $start_of_day = Carbon::now()->startOfDay();
        $end_of_day = Carbon::now()->endOfDay();
        $exemptionSchedule = VehicleExemptionSchedule::latest()->whereBetween('created_at',[$start_of_day, $end_of_day])
            ->where('schedule_type', '10pm Switch Off')
            ->first();
        $exemptedVehicleIds = [];
        if ($exemptionSchedule){
            $exemptionSchedule->status = 'closed';
            $exemptionSchedule->save();
            $exemptedVehicleIds = explode(',', $exemptionSchedule->vehicle_ids);
        }        
        $vehicles = Vehicle::whereNotIn('id', $exemptedVehicleIds)->where('primary_responsibility', 'Route Deliveries')->get();
        try {
            foreach ($vehicles as $vehicle) {
                if(isset($vehicle->sim_card_number)){
                    $vehicle->switch_off_status = 'off';
                    $vehicle->save();
                    $time = 86400;
                    $speed = 8; 
                    $switchOffMsg = 'setdigout 1 '.$time.' '.$speed;
                    $this->smsService->sendMessage($switchOffMsg, $vehicle->sim_card_number);
                    $immobilizationEvent = new VehicleImmobilization();
                    $immobilizationEvent->vehicle_id = $vehicle->id;
                    $immobilizationEvent->causer_id = 1; //admin to rep system
                    $immobilizationEvent->reason = 'auto switch off at 10pm';
                    $immobilizationEvent->time = $time;
                    $immobilizationEvent->speed = $speed;
                    $immobilizationEvent->save();
                    
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error in AutoSwitchOffVehicles Command: ' . $th->getMessage());
            // $this->smsService->sendMessage($th->getMessage(), '254729825703');
        }
      
    }
}
