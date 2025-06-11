<?php

namespace App\Console\Commands\Telematics;

use App\Events\OverspeedingEvent;
use App\Model\User;
use App\Model\UserPermission;
use App\Notifications\Telematics\OverspeedingNotification;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OverspeedingAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:overspeeding-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vehicle Overspeeding Alert';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roles = UserPermission::where('module_name', 'vehicles-overview')->pluck('role_id')->toArray();
        $users = User::whereIn('role_id', $roles)->get();

        try {
            $subquery = DB::connection('telematics')->table('vehicle_telematics')
            ->select('device_number', DB::raw('MAX(id) as max_id'))
            ->groupBy('device_number');
    
            $vehicles = DB::connection('telematics')->table('vehicle_telematics')
            ->select('vehicle_telematics.device_number',
            )
            ->joinSub($subquery, 'sub', function ($join) {
                $join->on('vehicle_telematics.id', '=', 'sub.max_id');
            })
            ->get();

            foreach ($vehicles as $vehicle) {
                $telematicsData = DB::connection('telematics')->table('vehicle_telematics')
                    ->where('device_number', $vehicle->device_number)
                    ->where('timestamp', '>=', Carbon::now()->subMinutes(2))
                    ->get();
                if($telematicsData){
                    $speedSum = 0;
                    $recordCount = 0;
                    foreach ($telematicsData as $data) {
                        $decodedData = json_decode($data->data);
                            $speedSum += $decodedData[0]->{'position.speed'};
                            $recordCount++;
                    }
                    // Log::info('speed sum: '.$speedSum.' record count: '.$recordCount);
                    if ($recordCount > 0) {
                        $averageSpeed = $speedSum / $recordCount;
                        if ($averageSpeed > 70) {
                            $message = " is overspeeding at ";
                            $time = Carbon::now()->toTimeString();
                            Notification::sendNow($users , new OverspeedingNotification($vehicle->device_number, $averageSpeed));
                            event(new OverspeedingEvent($vehicle->device_number, ceil($averageSpeed), $message, $time));

                        }
                    }
                  
                }
               
            }
            
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
