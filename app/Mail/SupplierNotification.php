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

class SupplierNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $supplier;
    public $location;
    /**
     * Create a new message instance.
     */
    public function __construct(WaSupplier $supplier, $location)
    {
        $this->supplier = $supplier;
        $this->location = $location;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->from(env('PROCUREMENT_EMAIL'), env('PROCUREMENT_NAME'))
            ->markdown(
                'admin.maintainsuppliers.notification_new_supplier',
                [
                    'row' => $this->supplier,
                    'location' => $this->location
                ]
            );
    }
}
