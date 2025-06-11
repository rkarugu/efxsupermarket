<?php

namespace App\Notifications;

use App\Notifications\Channels\InfoSky as SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OperationShiftClosing extends Notification
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
        $this->shift_data = $data['shift_data'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', SmsMessage::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {

        $greeting = isset($this->notifiable->name) ? "Dear {$this->notifiable->name}," : "Greetings!";

        $date = $this->shift->date;
        return  (new MailMessage())
            ->subject("END OF DAY ROUTINE FOR $this->branch - $date")
            ->action('View Shift Details', route('operation_shifts.show', $this->shift->id))
            ->view('emails.operation_shift',[
                'greeting' => $greeting,
                'subject' => "END OF DAY ROUTINE FOR $this->branch - $date",
                'branch' => $this->branch,
                'shift' => $this->shift,
                'shift_data' => $this->shift_data,
            ]);



    }
    public function toDatabase(object $notifiable): array
    {
        return [
            'shift_id' => $this->shift->id,
            'date' => $this->shift->date,
            'balanced' => $this->shift->balanced,
            'url' => route('operation_shifts.show', $this->shift->id)
        ];
    }

    public function toSms(object $notifiable): string
    {
        $url = route('operation_shifts.show', $this->shift->id);
        $smsMessage = "End of Day Operations Shift Report for Branch: {$this->branch} on {$this->shift->date}\n";
        $smsMessage .= "Shift Balanced: " . ($this->shift->balanced ? 'Yes' : 'No') . "\n";

        foreach ($this->shift_data as $checkName => $check) {
            if ($checkName !='stock_take' )
            {
                $status = $check['status'] ? 'Passed' : 'Failed';
                $smsMessage .= ucfirst(str_replace('_', ' ', $checkName)). ": $status\n";
                foreach ($check as $detailName => $detailValue) {
                    if ($detailName !== 'status') {

                            if ($checkName == 'no_pending_returns') {
                                $smsMessage .= '  ' . ucfirst(str_replace('_', ' ', $detailName)) . " :  " . $detailValue . "\n";

                            }

    //                    elseif ($checkName =='stock_take' ){
    //                        foreach ($detailValue as $item)
    //                        {
    //                            foreach ($item as $key => $value)
    //                            {
    //                                if ($key == 'Total_return_amount')
    //                                {
    //                                    $smsMessage .=' '.ucfirst(str_replace('_', ' ', $key))." : ".number_format($value, 2) ."\n";
    //                                }else{
    //                                    $smsMessage .=' '.ucfirst(str_replace('_', ' ', $key))." : ".$value ."\n";
    //                                }
    //
    //                            }
    //
    //                        }
    //                    }
                            else{
                                $smsMessage .= '  ' . ucfirst(str_replace('_', ' ', $detailName)) . " :  " . number_format($detailValue) . "\n";
                            }

                    }
                }
            }
        }
        return $smsMessage;
    }
}
