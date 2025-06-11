<?php

namespace App\Notifications\GeneralLedger;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialBalanceStatus extends Notification
{
    use Queueable;

    protected $start_date;
    protected $end_date;
    protected $user;
    protected $credit_amount;
    protected $debit_amount;
    protected $credit_debit_variance;
    protected $data_credit_amount;
    protected $trans_amount;
    protected $trans_amount_variance;

    /**
     * Create a new notification instance.
     */
    public function __construct($start_date, $end_date, $user, $credit_amount, $debit_amount, $credit_debit_variance, $data_credit_amount, $trans_amount, $trans_amount_variance)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user = $user;
        $this->credit_amount = $credit_amount;
        $this->debit_amount = $debit_amount;
        $this->credit_debit_variance = $credit_debit_variance;
        $this->data_credit_amount = $data_credit_amount;
        $this->trans_amount = $trans_amount;
        $this->trans_amount_variance = $trans_amount_variance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = Carbon::parse($this->start_date)->format('Y-m-d');
        return (new MailMessage)
            ->subject("END OF DAY FINANCE ROUTINE FOR THIKA MAKONGENI - $date")
            ->greeting("Hello " . $this->user->name . ",")
            ->line('Below is the summary of the trial balance report:')
            ->view('emails.trialbalancestatus', [
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'date' => $date,
                'user' => $this->user,
                'credit_amount' => $this->credit_amount,
                'debit_amount' => $this->debit_amount,
                'credit_debit_variance' => $this->credit_debit_variance,
                'data_credit_amount' => $this->data_credit_amount,
                'trans_amount' => $this->trans_amount,
                'trans_amount_variance' => $this->trans_amount_variance,
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
}
