<?php

namespace App\Console\Commands;

use App\Model\User;
use App\Services\InfoSkySmsService;
use Illuminate\Console\Command;

class StockTakeReminders extends Command
{
    public function __construct(protected InfoSkySmsService $smsService)
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stock-take-reminders';

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
        $users = User::where('role_id', 152)->with(['uom'])->get();
        foreach ($users as $user) {
            $message = "Dear, ". $user->name . ". Remember to do stock counts for atleast 2 categories in your bin (".$user->uom?->title.") daily, and all Items Weekly  to avoid being denied access to the system. If unable, please contact administration to be exempted";
            $this->smsService->sendMessage($message, $user->phone_number);
        }
    }
}
