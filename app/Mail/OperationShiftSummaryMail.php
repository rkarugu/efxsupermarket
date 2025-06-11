<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OperationShiftSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $shift_data;
    public $branch;
    public $shift;

    public function __construct($shift_data, $branch, $shift)
    {
        $this->shift_data = $shift_data;
        $this->branch = $branch;
        $this->shift = $shift;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Operation Shift Summary Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $greeting = isset($this->notifiable->name) ? "Dear {$this->notifiable->name}," : "Greetings!";
        $date = $this->shift->date;

//        return $this->subject("END OF DAY ROUTINE FOR $this->branch - $date")
//            ->view('emails.daily_summary')
//            ->with([
//                'greeting' => $greeting,
//                'shift_data' => $this->shift_data,
//                'branch' => $this->branch,
//                'shift' => $this->shift,
//            ]);
        return new Content(
            view: 'emails.operationshift',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
