<?php

namespace App\Mail\Inventory;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class LocationStockSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    protected $pdf;

    protected $supplier;

    public function __construct($pdf, $content, $supplier)
    {
        $this->pdf = $pdf;
        $this->content = $content;
        $this->supplier = $supplier;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME')),
            subject: 'Location Stock Summary',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inventory.location_stock_summary',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdf, str($this->supplier->name)->upper()->replace(' ', '_') . "_STOCK_REPORT.pdf")
                ->withMime('application/pdf')
        ];
    }
}
