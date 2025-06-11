<?php

namespace App\Console\Commands;

use App\Alert;
use App\DeliverySchedule;
use App\Exports\DeliverySchedulesExport;
use App\Mail\DeliveryNotification as MailDeliveryNotification;
use App\Model\User;
use App\Notifications\DeliveryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UndeliveredOrdersAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:undelivered-orders-alerts';

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
        $deliverySchedules = DB::table('delivery_schedules')
            ->select(
                'routes.route_name as route_name',
                'users.name as driver',
                'vehicles.license_plate_number as vehicle',
                'delivery_schedules.actual_delivery_date as start_time',
                'delivery_schedules.finish_time as finish_time',
                'fuel_entries.manual_distance_covered as manual_distance',
                'fuel_entries.actual_fuel_quantity as actual_fuel_quantity',
                'fuel_entries.manual_consumption_rate as manual_consumption_rate',
                DB::raw("(SELECT COUNT(wa_internal_requisitions.id) FROM wa_internal_requisitions where wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id) as epected_deliveries"),
                DB::raw("(SELECT COUNT(wa_internal_requisitions.id) FROM wa_internal_requisitions where wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id
                AND wa_internal_requisitions.is_delivered  = '1') as actual_deliveries"),
                )
            ->leftJoin('routes', 'routes.id', '=', 'delivery_schedules.route_id')
            ->leftJoin('vehicles', 'vehicles.id', '=', 'delivery_schedules.vehicle_id')
            ->leftJoin('users', 'users.id', '=', 'delivery_schedules.driver_id')
            ->leftJoin('fuel_entries', function($join){
                $shift_type = 'route_delivery';
                $join->on('fuel_entries.shift_id', '=', 'delivery_schedules.id')
                ->where('fuel_entries.shift_type', $shift_type);
                
            })
            ->whereDate('delivery_schedules.created_at', '=', Carbon::now()->subDays(1)->toDateString())
            //  ->whereDate('delivery_schedules.created_at', '=', '2024-03-07')
            ->get()->map(function($record){
                if(!$record->finish_time){
                    $record->delivery_time = 'shift in  progress';

                }
                if(!$record->start_time){
                    $record->delivery_time = 'shift not started';
                }
                if($record->start_time && $record->finish_time){
                    $start = Carbon::parse($record->start_time);
                    $end = Carbon::parse($record->finish_time);
                    $time = $start->diffInMinutes();
                    $hours = intdiv($time, 60);
                    $minutes = $time % 60;
                    $record->delivery_time = $hours.' hrs '.$minutes . ' mins';
                }
                return $record;
            });

        $alert = Alert::where('alert_name','delivery_reports')->first();
        $recipients =[];
        if ($alert instanceof Alert) {
            $recipientType = $alert->recipient_type;
            if ($recipientType === 'user') {
                $ids = explode(',', $alert->recipients);
                $recipients = User::whereIn('id', $ids)->get();
            } else if ($recipientType === 'role') {
                $roleids = explode(',', $alert->recipients);
                $recipients = User::whereIn('role_id', $roleids)->get();
            }

            if ($recipients) {
                foreach ($recipients as $recipient) {
                    $data = [
                        'deliverySchedules'=> $deliverySchedules,
                        
                    ];
                    $recipient->notify(new DeliveryNotification($data));
                }
            }
        }
    }
}
