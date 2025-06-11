<?php

namespace App\Mail;

use App\ItemSupplierDemand;
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

class DeltaRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public WaSupplier $supplier, public $pdf)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // $branch = Restaurant::find($this->lpo->restaurant_id);
        return new Envelope(
            from: new Address(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME')),
            subject: "SUPPLIER  DELTA",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier_delta',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdf, "KANINI HARAKA SUPPLIER DELTA.pdf")->withMime('application/pdf')
        ];
    }
}
