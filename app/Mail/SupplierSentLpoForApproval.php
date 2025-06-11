<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierSentLpoForApproval extends Mailable
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
        return $this->from($this->supplier->name, $this->supplier->email)->view('emails.supplier_sent_lpo_for_approval',['row'=>$this->supplier, 'lpo'=>$this->lpo]);
    }
}
