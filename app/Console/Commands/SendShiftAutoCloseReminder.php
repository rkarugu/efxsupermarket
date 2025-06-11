<?php

namespace App\Console\Commands;

use App\Interfaces\SmsService;
use App\SalesmanShift;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SendShiftAutoCloseReminder extends Command
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
    protected $signature = 'app:send-shift-auto-close-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies all salesmen with open shifts that the shifts will be automatically closed in a while';

    /**
     * Execute the console command.
     */
    public function handle(): JsonResponse
    {
        try {
            $openAndPendingShifts = SalesmanShift::where('status', 'open')->get();
            foreach ($openAndPendingShifts as $shift) {
                $message = "Hello, " . $shift->salesman->name . ". You have an open shift for the route " . $shift->salesman_route->route_name . " that will automatically close at 6:00 PM. Please finalize on the remaining orders for this route.";
                $this->smsService->sendMessage($message, $shift->salesman->phone_number);
            }

            return response()->json(['status' => 'Reminders successful']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Reminders failed', 'msg' => $e->getMessage()]);
        }
    }
}
