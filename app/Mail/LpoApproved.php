<?php

namespace App\Mail;

use App\Model\Restaurant;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;

class LpoApproved extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct(
        public WaPurchaseOrder $lpo,
        public WaSupplier $supplier,
        public $content,
        public $pdf,
        public $subject = ""
    ) {}

    public function envelope(): Envelope
    {
        $branch = Restaurant::find($this->lpo->restaurant_id);

        return new Envelope(
            from: new Address(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME')),
            subject: $this->subject ?? "PURCHASE ORDER {$this->lpo->purchase_no} FOR $branch?->name",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.lpo-approved',
        );
    }

    public function attachments(): array
    {
        if ($this->pdf) {
            return [
                Attachment::fromData(fn() => $this->pdf, "KANINI HARAKA {$this->lpo->purchase_no}.pdf")->withMime('application/pdf')
            ];
        }

        return [];
    }
}
