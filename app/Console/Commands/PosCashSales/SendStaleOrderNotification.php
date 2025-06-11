<?php

namespace App\Console\Commands\PosCashSales;

use App\Alert;
use App\Model\WaPosCashSales;
use App\Notifications\PosCashSales\StaleOrdersNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendStaleOrderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-stale-order-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification for Stale order. Orders in complete state for more than 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffTime = \Carbon\Carbon::now()->subMinutes(15);

        // Fetch orders that are paid and in the dispatching state for more than 10 minutes
        $orders = WaPosCashSales::where('status', 'Completed')
            ->whereHas('dispatch', function ($q){
                $q ->where('status','dispatching');
            })
            ->where('paid_at', '<', $cutoffTime)
            ->where('created_at', '>=', Carbon::today())
            ->get();

        if ($orders->isNotEmpty()) {
            // Send notification to admin
            $alert = Alert::where('alert_name','delayed-orders')->first();

            if ($alert instanceof Alert){
                $recipientType = $alert->recipient_type;
                $recipientId = $alert->recipients;
                if ($recipientType === 'user'){
                    $ids = explode(',', $alert->recipients);
                    $recipients = User::whereIn('id', $ids)->get();
                } else if ($recipientType === 'role') {
                    // Fetch users with the specified role
                     $roleids = explode(',', $alert->recipients);
                    $recipients = User::whereIn('role_id', $roleids)->get();
                }

                if ($recipients) {
                    foreach ($orders as $order)
                    {
                        foreach ($recipients as $recipient)
                        {
                            $recipient->notify(new StaleOrdersNotification($order));
                        }

                    }}
            }
        }
    }
}
