<?php

namespace App\Services;

use App\Alert;
use App\Model\User;
use Illuminate\Support\Facades\Log;

class AlertService
{
    static public function send(string $alert, string $message, $routeId = null): void
    {
        if ($alert = Alert::where('alert_name', $alert)->first()) {
            $recipients = [];
            switch ($alert->recipient_type) {
                case 'user':
                    $user = User::find($alert->recipient_id);
                    if ($user) {
                        $recipients[] = $user->phone_number;
                    }

                    break;
                case 'role':
                    $users = User::where('role_id', $alert->recipient_id)->get();
                    if ($routeId) {
                        $users = $users->filter(function ($user) use ($routeId) {
                            $routeIds = [];
                            foreach ($user->routes as $route) {
                                $routeIds[] = $route->id;
                            }
                            return in_array($routeId, $routeIds);
                        });
                    }
                    Log::info('users after: ' . json_encode($users));
                    foreach ($users as $user) {
                        $recipients[] = $user->phone_number;
                    }

                    break;
                default:
                    break;
            }

            foreach ($recipients as $recipient) {
                try {
                    // send_sms($recipient, $message);
                    sendMessage($message, $recipient);
                } catch (\Throwable $e) {
                    // pass
                }
            }
        }
    }
}