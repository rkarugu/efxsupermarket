<?php

namespace App\Console\Commands\Telematics;

use App\Interfaces\SmsService;
use App\Model\User;
use App\Models\VehicleCustomSchedule;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExecuteCustomCommands extends Command
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
    protected $signature = 'app:execute-custom-commands';

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
        $schedules = VehicleCustomSchedule::where('time', '<=', $now)->where('status', 'pending')->get();
        foreach ($schedules as $schedule) {
            $vehicles = explode(',', $schedule->vehicle_ids);
            //get action
            $action = $schedule->action;
            $vehicle_plates = [];
            foreach ($vehicles as $vehicleId) {
                $vehicle = Vehicle::find($vehicleId);
                array_push($vehicle_plates, $vehicle->license_plate_number);
                if($action == 0){
                    $vehicle->switch_off_status = 'on';
                    $vehicle->save();
                    $switchOffMsg = 'setdigout 0';
                    $this->smsService->sendMessage($switchOffMsg, $vehicle->sim_card_number); 

                }else{
                    $vehicle->switch_off_status = 'off';
                    $vehicle->save();
                    $switchOffMsg = 'setdigout 1 84600 8';
                    $this->smsService->sendMessage($switchOffMsg, $vehicle->sim_card_number);
                }
            }
            $schedule->status = 'completed';
            $schedule->save();
            //notify user
            $user = User::find($schedule->created_by);
            $this->smsService->sendMessage("Custom command executed successfully for vehicle(s): " . implode(", ", $vehicle_plates), $user->phone_number); 
        }
        
    }
}
