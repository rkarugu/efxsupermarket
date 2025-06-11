<?php

namespace App\Console\Commands;

use App\Interfaces\SmsService;
use App\Model\User;
use App\Model\UserPermission;
use App\WaInventoryLocationTransferItemReturn;
use Illuminate\Console\Command;

class ReturnSmsReminder extends Command
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
    protected $signature = 'app:return-sms-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Reminder to Users to Approve Returns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //get roles with permissions approver 1 and 2
            $roles = UserPermission::where('module_action', 'approver-1')->pluck('role_id')->toArray();
            $users = User::whereIn('role_id', $roles)->get();
            $approver1Returns = WaInventoryLocationTransferItemReturn::where('return_status', 0);
            if(!empty($approver1Returns)){
                foreach($users as $user){
                    $message = "Dear, " . $user->name . ". You have returns pending approval under Approval 1 module. Please resolve to allow store keeper to complete return process.";
                    $this->smsService->sendMessage($message, $user->phone_number);
                }
              
            }

            $roles = UserPermission::where('module_action', 'approver-2')->pluck('role_id')->toArray();
            $users = User::whereIn('role_id', $roles)->get();
            $approver1Returns = WaInventoryLocationTransferItemReturn::where('return_status', 2);
            if(!empty($approver1Returns)){
                foreach($users as $user){
                    $message = "Dear, " . $user->name . ". You have returns pending approval under Approval 2 module. Please resolve to allow store keeper to complete return process.";
                    $this->smsService->sendMessage($message, $user->phone_number);
                }
              
            }
            $roles = UserPermission::where('module_action', 'late-returns')->pluck('role_id')->toArray();
            $users = User::whereIn('role_id', $roles)->get();
            $approver1Returns = WaInventoryLocationTransferItemReturn::where('return_status', 3);
            if(!empty($approver1Returns)){
                foreach($users as $user){
                    $message = "Dear, " . $user->name . ". You have returns pending approval under Late Returns module. Please resolve to allow store keeper to complete return process.";
                    $this->smsService->sendMessage($message, $user->phone_number);
                }
              
            }

            
    }
}
