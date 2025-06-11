<?php

namespace App\Console\Commands;

use App\Model\User;
use App\Interfaces\SmsService;
use Illuminate\Console\Command;
use App\Models\WaPettyCashRequest;
use Illuminate\Support\Collection;

class PettyCashRequestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:petty-cash-request-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(protected SmsService $smsService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingInitialApprovalCount = WaPettyCashRequest::where('initial_approval', false)
            ->where('rejected', false)
            ->count();
            
        if ($pendingInitialApprovalCount) {
            $message = "You have $pendingInitialApprovalCount petty cash requests pending initial approval";
            
            $initialApprovalUsers = User::withPermission('petty-cash-requests-initial-approval', 'approve')->get();
            foreach($initialApprovalUsers as $user) {
                $this->smsService->sendMessage($message, $user->phone_number);
            }
        }
        
        $pendingFinalApprovalCount = WaPettyCashRequest::where('initial_approval', true)
            ->where('final_approval', false)
            ->where('rejected', false)
            ->count();
        
        if ($pendingFinalApprovalCount) {
            $message = "You have $pendingFinalApprovalCount petty cash requests pending initial approval";
            
            $finalApprovalUsers = User::withPermission('petty-cash-requests-final-approval', 'approve')->get();
            foreach($finalApprovalUsers as $user) {
                $this->smsService->sendMessage($message, $user->phone_number);
            }
        }
            
    }
}
