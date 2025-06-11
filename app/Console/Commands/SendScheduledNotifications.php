<?php

namespace App\Console\Commands;

use Exception;
use App\Model\User;
use App\Models\ScheduledNotification;
use App\Notifications\Handlers\NotificationContextHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send {notificationId}';

    protected $description = 'Send scheduled notifications based on notification ID';

    public function handle()
    {
        $notificationId = $this->argument('notificationId');
        $notification = ScheduledNotification::find($notificationId);

        if (!$notification) {
            $this->error("Notification with ID $notificationId not found.");
            return;
        }

        $notificationClass = $notification->class_name;
        $emails = $notification->emails;
        $phoneNumbers = $notification->phone_numbers;

        $notificationTitle = Str::title(Str::snake(class_basename($notificationClass)));

        $this->line('Sending notifications for ' . $notificationTitle);

        try {
            $context = NotificationContextHandler::getContext(Str::snake(class_basename($notificationClass)));
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        if (isset($context['users'])) {
            $this->line('Sending to assigned users: ' . $notificationTitle);
            Notification::send($context['users'], new $notificationClass($context));
            $this->line('Sending to assigned users completed: ' . $notificationTitle);
        }

        if (!empty($notification->roles)) {
            $this->line('Sending to roles: ' . $notificationTitle);
            $roleUsers = User::whereIn('role_id', $notification->roles)->get();
            if (isset($context['users'])) {
                // remove users already notified
                $roleUsers = $roleUsers->filter(function ($user) {
                    return !isset($context['users'][$user->id]);
                });
            }

            Notification::send($roleUsers, new $notificationClass($context));
            $this->line('Sending to roles completed: ' . $notificationTitle);
        }

        if (!empty($notification->users)) {
            $this->line('Sending to users: ' . $notificationTitle);
            $specificUsers = User::whereIn('id', $notification->users)->get();
            if (isset($context['users'])) {
                // remove users already notified
                $specificUsers = $specificUsers->filter(function ($user) {
                    return !isset($context['users'][$user->id]);
                });
            }

            Notification::send($specificUsers, new $notificationClass($context));
            $this->line('Sending to users completed: ' . $notificationTitle);
        }

        if (!empty($emails)) {
            foreach ($emails as $email) {
                $this->line('Sending to emails: ' . $notificationTitle);
                Notification::route('mail', $email)->notify(new $notificationClass($context));
                $this->line('Sending to emails completed: ' . $notificationTitle);
            }
        }

        if (!empty($phoneNumbers)) {
            foreach ($phoneNumbers as $phoneNumber) {
                $this->line('Sending to phone numbers: ' . $notificationTitle);
                Notification::route('sms', $phoneNumber)->notify(new $notificationClass($context));
                $this->line('Sending to phone numbers completed: ' . $notificationTitle);
            }
        }
    }
}
