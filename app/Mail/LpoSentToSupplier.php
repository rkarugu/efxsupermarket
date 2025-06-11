<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LpoSentToSupplier extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $lpo;
    /**
     * Create a new message instance.
     */
    public function __construct(WaSupplier $supplier, WaPurchaseOrder $lpo)
    {
        $this->supplier = $supplier;
        $this->lpo = $lpo;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->from(name: env(key: 'PROCUREMENT_NAME'), address: env(key: 'PROCUREMENT_EMAIL'))->view('emails.lpo_sent_to_supplier',['row'=>$this->supplier, 'lpo'=>$this->lpo]);
    }
}
