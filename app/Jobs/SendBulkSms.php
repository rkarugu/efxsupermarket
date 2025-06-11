<?php

namespace App\Jobs;

use App\Models\BulkSms;
use App\Models\BulkSmsMessage;
use App\Services\AirTouchSmsService;
use App\Services\InfoSkySmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SendBulkSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
   

    /**
     * Create a new job instance.
     */
    public function __construct(public $message, public $phoneNumbers, public $provider, public $title, public $contact_group, public $branch, public $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = $this->message;
        $phoneNumbers = $this->phoneNumbers;
        $issn = $this->provider;
        $title = $this->title;
        $contact_group = $this->contact_group;
        $branch = $this->branch;
        $user = $this->user;

        try {
            $bulkSms = BulkSms::create([
                'title' => $title,
                'send_group' => $contact_group,
                'branch_id' => $branch =='all' ? NULL:$branch,
            ]); 
            $insertData=[];
            foreach ($phoneNumbers as $key => $phone) {
                $telephone = $this->phoneNumberCleanup($phone);
                // Log::info($telephone);
                switch ($issn) {
                    case env("KANINI_SMS_SENDER_ID_2"):
                        $infoSkyService = new InfoSkySmsService();
                        $response = $infoSkyService->sendMessageResponse($message,$telephone,env("KANINI_SMS_SENDER_ID_2"));
                        break;
                    case env("KANINI_SMS_SENDER_ID"):
                        $infoSkyService = new InfoSkySmsService();
                        $response = $infoSkyService->sendMessageResponse($message,$telephone,env("KANINI_SMS_SENDER_ID"));
                        break;
                    case env("AIRTOUCH_ISSN"):
                        $airTouchService = new AirTouchSmsService();
                        $response = $airTouchService->sendMessageResponse($message,$telephone);
                        break;
                    
                    default:
                        # code...
                        break;
                }
                
                if ($response) {
                    $stat = 0;
                    if($response==1){
                        $stat = 1;
                        $check="";
                    } else {
                        $check=$response;
                    }
                    $insertData[] =  [
                        'bulk_sms_id' => $bulkSms->id,
                        'created_by' => $user,
                        'issn' => $issn,
                        'phone_number' => $telephone,
                        'message' => $message,
                        'category' => 'Bulk SMS',
                        'send_status' => $stat,
                        'sms_length' => mb_strlen($message),
                        'sms_response'=> @$check,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            $chunks = array_chunk($insertData, 500);

            foreach ($chunks as $chunk) {
                BulkSmsMessage::insert($chunk);
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    private function phoneNumberCleanup($phone)
    {
        $phone = trim($phone);$original = $phone;
        $phone = str_replace('+','',$phone);
        $phone = str_replace('254 ','254',$phone);
        $phone = str_replace('--','',$phone);
        $phone = str_replace('-',' ',$phone);
        
        if (str_contains($phone, ' ')) {
            $parts = explode(' ', $phone);
            
            if (!is_numeric($parts[0])) {
                array_shift($parts);
            }
            if (count($parts) > 1) {
                $phone = implode(' ', $parts);
                $phone = trim($phone);
                $parts2 = explode(' ', $phone);
                
                if (!is_numeric($parts2[0])) {
                    array_shift($parts2);
                }
                
                if (count($parts2) > 1) {
                    
                    $phone = implode('', $parts2);
                   
                    $phone = trim($phone);
                } else {
                    $phone = implode('', $parts2);
                }
            } else {
                $phone = implode('', $parts);
            }
            
        }
        
        if(str_contains($phone, '-')){
            $dashed = explode('-', $phone);
            if (!is_numeric($dashed[0])) {
                array_shift($dashed);
            }
            if (count($dashed) > 1) {
                $phone = implode('-', $dashed);
            } else {
                $phone = $dashed[0];
            }
        }
        if(str_contains($phone, ',')){
            $comma = explode(',', $phone);
            if (!is_numeric($comma[0])) {
                array_shift($comma);
            }
            if (count($comma) > 1) {
                $phone = implode(',', $comma);
            } else {
                $phone = $comma[0];
            }
        }
        $phone = (int)$phone;
        if (strpos($phone, '254') !== 0) {
            $phone = '254' . $phone;
        }
        return (int)$phone;
    }
}
