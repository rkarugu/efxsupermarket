<?php

namespace App\Console\Commands\Telematics;

use App\Interfaces\SmsService;
use App\Models\VehicleExemptionSchedule;
use App\Models\VehicleImmobilization;
use App\Models\VehicleTelematicsData;
use App\Services\MappingService;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class AutoSwitchOffVehiclesAtFivePm extends Command
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
    protected $signature = 'app:auto-switch-off-vehicles-at-five-pm';

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
            ->where('schedule_type', '5pm Switch Off')
            ->first();
        $exemptedVehicleIds = [];
        if ($exemptionSchedule){
            $exemptionSchedule->status = 'closed';
            $exemptionSchedule->save();
            $exemptedVehicleIds = explode(',', $exemptionSchedule->vehicle_ids);
        }        
        $vehicles = Vehicle::whereNotIn('id', $exemptedVehicleIds)->where('primary_responsibility', 'Route Deliveries')->get();
        $godown_lat = -1.04638;
        $godown_lng = 37.10104;
        $shell_lat = -1.051152;
        $shell_lng = 37.095042;
        foreach ($vehicles as $record) {
            try {
                $vehicle = Vehicle::where('license_plate_number', $record->license_plate_number)->first();
                $lastTelematicsData = VehicleTelematicsData::where('device_number', $vehicle->license_plate_number)
                    ->orderBy('timestamp', 'desc')
                    ->first();
                if($lastTelematicsData){
                    $data = json_decode($lastTelematicsData->data);
                    $vehicle_lat = $data[0]->{'position.latitude'};
                    $vehicle_lng = $data[0]->{'position.longitude'};
                    $distance_from_go_down = MappingService::getTheaterDistanceBetweenTwoPoints($vehicle_lat, $vehicle_lng, $godown_lat, $godown_lng);
                    $distance_from_shell = MappingService::getTheaterDistanceBetweenTwoPoints($vehicle_lat, $vehicle_lng, $shell_lat, $shell_lng);
                    if($distance_from_go_down > 200 && $distance_from_shell > 200){
                        $vehicle->switch_off_status = 'off';
                        $vehicle->save();
                        $switchOffMsg = 'setdigout 1 84600 8';
                        $this->smsService->sendMessage($switchOffMsg, $vehicle->sim_card_number);

                        $immobilizationEvent = new VehicleImmobilization();
                        $immobilizationEvent->vehicle_id = $vehicle->id;
                        $immobilizationEvent->causer_id = 1; //rep system
                        $immobilizationEvent->reason = 'Auto shut down at 5:30';
                        $immobilizationEvent->time = 86400;
                        $immobilizationEvent->speed = 8;
                        $immobilizationEvent->save();
                    }
                }
            } catch (\Throwable $e) {
                // $this->smsService->sendMessage('Error in AutoSwitchOffVehicles Command: '. $record->license_plate_number . $e->getMessage(), '254729825703'); // notify dev
                Log::error('Error in AutoSwitchOffVehicles Command: '. $record->license_plate_number . $e->getMessage());
            }
            
        }

    }
}
