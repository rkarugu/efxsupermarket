<?php

namespace App\Console\Commands\Telematics;

use App\Interfaces\SmsService;
use App\Models\VehicleExemptionSchedule;
use App\Models\VehicleImmobilization;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoSwitchOnVehicles extends Command
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
    protected $signature = 'app:auto-switch-on-vehicles';

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
        $start_of_day = Carbon::yesterday()->startOfDay();
        $end_of_day = Carbon::yesterday()->endOfDay();
        $exemptionSchedule = VehicleExemptionSchedule::latest()->whereBetween('created_at',[$start_of_day, $end_of_day])
            ->where('schedule_type', '4am Switch On')
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
                    $vehicle->switch_off_status = 'on';
                    $vehicle->save(); 
                    $switchOnMsg = 'setdigout 0';
                    $this->smsService->sendMessage($switchOnMsg, $vehicle->sim_card_number);
                    $now = Carbon::now()->toDateTimeString();
                    $immobilizationEvent = VehicleImmobilization::latest()
                        ->where('vehicle_id', $vehicle->id)
                        ->whereNull('switch_on_date')
                        ->first();
                    if($immobilizationEvent){
                        $immobilizationEvent->switch_on_date = $now;
                        $immobilizationEvent->switch_on_by = 1;
                        $immobilizationEvent->save();
                    }
                   
                }
            }
        } catch (\Throwable $th) {
            Log::error('Error in AutoSwitchOffVehicles Command: ' . $th->getMessage());
            // $this->smsService->sendMessage($th->getMessage(), '254729825703');
        }
    }
}
