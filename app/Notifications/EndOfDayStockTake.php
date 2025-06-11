<?php

namespace App\Notifications;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EndOfDayStockTake extends Notification
{
    use Queueable;


    public $data;
    public $branch;
    public $shift_data;
    public $shift;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->branch = $data['branch'];
        $this->shift = $data['shift'];
        $this->shift_data = $data['shift_data']['stock_take'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', SmsMessage::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $greeting = isset($this->notifiable->name) ? "Dear {$this->notifiable->name}," : "Greetings!";

        $date = $this->shift->date;
        return  (new MailMessage())
            ->subject("END OF DAY ROUTINE STOCK COUNT FOR $this->branch - $date")
            ->action('View Shift Details', route('operation_shifts.show', $this->shift->id))
            ->view('emails.operationshift',[
                'greeting' => $greeting,
                'subject' => "END OF DAY ROUTINE FOR $this->branch - $date",
                'branch' => $this->branch,
                'shift' => $this->shift,
                'shift_data' => $this->shift_data['data'],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function toSms(object $notifiable)
    {
//        $smsMessage = "End of Day Stock Count Report for Branch: {$this->branch} on {$this->shift->date}\n";
//
//        foreach ($this->shift_data['data'] as $checkName => $check) {
//            $firstItem = true;
//            foreach ($check as $key => $value) {
//                if ($firstItem) {
//                    $smsMessage .= ucfirst(str_replace('_', ' ', $key))." : ".$value ."\n";
//                    $firstItem = false;
//                } else {
//                    $smsMessage .= "    ".ucfirst(str_replace('_', ' ', $key))." : ".$value ."\n";
//                }
//            }
//        }
//        return $smsMessage;

        $smsMessages = [];
        $currentMessage = "End of Day Stock Count Report for Branch: {$this->branch} on {$this->shift->date}\n";
        $checkCount = 0;

        foreach ($this->shift_data['data'] as $checkName => $check) {
            $firstItem = true;
            foreach ($check as $key => $value) {
                if ($checkCount >= 7) {
                    $smsMessages[] = $currentMessage;
                    $currentMessage = "End of Day Stock Count Report for Branch: {$this->branch} on {$this->shift->date}\n";
                    $checkCount = 0;
                }

                if ($firstItem) {
                    $currentMessage .= ucfirst(str_replace('_', ' ', $key))." : ".$value ."\n";
                    $firstItem = false;
                } else {
                    $currentMessage .= "    ".ucfirst(str_replace('_', ' ', $key))." : ".$value ."\n";
                }
            }
            $checkCount++;
        }

        // Add the last message if it has any content
        if (trim($currentMessage) !== "End of Day Stock Count Report for Branch: {$this->branch} on {$this->shift->date}\n") {
            $smsMessages[] = $currentMessage;
        }

        return $smsMessages;
    }
}
