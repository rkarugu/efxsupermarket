<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Model\WaPurchaseOrder;
use App\Model\WaSupplier;
use Illuminate\Mail\Mailable;
use App\Model\WaLpoPortalReqApproval;
use Illuminate\Queue\SerializesModels;

class LpoSupplierChangesApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $lpo;
    public $portal_request;
    /**
     * Create a new message instance.
     */
    public function __construct(WaSupplier $supplier, WaPurchaseOrder $lpo, WaLpoPortalReqApproval $portal_request)
    {
        $this->supplier = $supplier;
        $this->lpo = $lpo;
        $this->portal_request = $portal_request;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->from($this->supplier->name, $this->supplier->email)->view('emails.lpo_supplier_changes_approved',['row'=>$this->supplier, 'lpo'=>$this->lpo,'portal_request'=>$this->portal_request]);
    }
}
