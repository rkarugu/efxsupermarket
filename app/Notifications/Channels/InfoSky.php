<?php

namespace App\Notifications\Channels;

use App\Services\InfoSkySmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InfoSky
{
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSMS($notifiable);
        $infoskyService = new InfoSkySmsService();
        if (is_array($message)) {
            if (isset($notifiable->phone_number)) {
                foreach ($message as $item)
                {
                    $infoskyService->sendOtp($item, $notifiable->phone_number);

                }
                return;
            }
        }else
        {
            if (isset($notifiable->phone_number)) {
                $infoskyService->sendOtp($message, $notifiable->phone_number);

                return;
            }

            if (isset($notifiable->routes['sms'])) {
                $infoskyService->sendOtp($message, $notifiable->routes['sms']);

                return;
            }
        }



    }
}
