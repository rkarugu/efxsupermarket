<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\PayrollMonthDetail;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class Payslip extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public PayrollMonthDetail $payrollMonthDetail, public $payslip)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payslip for {$this->payrollMonthDetail->payrollMonth->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payslip',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $filename = $this->payrollMonthDetail->employee->full_name . str_replace('/', ' ', $this->payrollMonthDetail->payrollMonth->name) .  ' Payslip.pdf';
        return [
            Attachment::fromData(fn () => $this->payslip, $filename)
                ->withMime('application/pdf')
        ];
    }
}
