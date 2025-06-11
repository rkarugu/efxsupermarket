<?php

namespace App\Console\Commands;

use App\Alert;
use App\Model\WaGlTran;
use App\Notifications\PosCashSales\StaleOrdersNotification;
use App\Notifications\UnbalancedTrailBalance;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnbalancedTrailBalanceChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:unbalanced-trail-balance-checker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if trail balances are balancing';

    /**
     * Execute the console command.
     */
    public function handle()
    {

//        $date = Carbon::parse('2024-06-09');
        $date = today()->subDays(1)->format('Y-m-d');
        $balance = WaGlTran::whereDate('created_at', $date)->sum('amount');
        if ($balance != 0)
        {
            $alert = Alert::where('alert_name','unbalanced-trail-balance')->first();

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
                    foreach ($recipients as $recipient)
                    {
                        $recipient->notify(new UnbalancedTrailBalance());
                    }

                }
            }
        }
    }
}
