<?php

namespace App\Console\Commands;

use App\DeliveryManShift;
use App\DeliverySchedule;
use App\Model\Route;
use App\Model\User;
use App\SalesmanShift;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-shifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new shifts at the start of the day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::select('order_taking_days', 'id')->get()->filter(function (Route $route) {
            $today = Carbon::now()->dayOfWeek;
            $orderTakingDays = explode(',', $route->order_taking_days);
            return in_array((string)$today, $orderTakingDays);
        });

        foreach ($routes as $route) {
            if ($salesman = $route->salesman()) {
                SalesmanShift::create([
                    'salesman_id' => $salesman->id,
                    'route_id' => $route->id,
                    'status' => 'not_started',
                    'shift_type' => 'onsite',
                ]);
            }
        }

        $this->info('New shifts created successfully.');
    }
}