<?php

namespace App\Jobs;

use App\Alert;
use App\Model\WaInternalRequisition;
use App\Notifications\UnbalancedInvoices;
use App\Notifications\UnbalanceOrders;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnbalancedCompletedInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /*get all invoices for the day*/
        $invoices = WaInternalRequisition::whereDate('requisition_date', today())
            ->with('getRelatedItem','stockMoves','debtorTrans')
            ->withCount(['stockMoves', 'getRelatedItem'])
            ->get();
        $unbalanced_moves=0;
        $unbalanced_amounts=0;
        $unbalance_invoices = [];
        foreach ($invoices as $invoice)
        {

            /*check  invoice items  vs stock moves*/
            $item_count = $invoice->get_related_item_count;
            $move_cont = $invoice->stock_moves_count;

            if ($item_count != $move_cont)
            {
                $unbalance_invoices[]= $invoice;
                $unbalanced_moves +=1;
            }

            /*check invoice amount vs debtor trans amount*/
            $invoice_total = (int) $invoice->getOrderTotal();
            $debtor_tran_amount = (int) $invoice->debtorTrans->amount;
            if ($invoice_total != $debtor_tran_amount)
            {
                $unbalance_invoices[]= $invoice;
                $unbalanced_amounts +=1;
            }
        }
        $items = collect($unbalance_invoices)->unique();

        if ($items->count() > 0)
        {
            /* send notification */
            $alert = Alert::where('alert_name','unbalance-orders')->first();

            $recipients =[];
            if ($alert instanceof Alert) {
                $recipientType = $alert->recipient_type;
                if ($recipientType === 'user') {
                    $ids = explode(',', $alert->recipients);
                    $recipients = User::whereIn('id', $ids)->get();
                } else if ($recipientType === 'role') {
                    // Fetch users with the specified role
                    $roleids = explode(',', $alert->recipients);
                    $recipients = User::whereIn('role_id', $roleids)->get();
                }

                if ($recipients) {
                    foreach ($recipients as $recipient) {
                        $data = [
                            'unbalanced_moves'=> $unbalanced_moves,
                            'unbalanced_amounts'=> $unbalanced_amounts,
                        ];
                        $recipient->notify(new UnbalancedInvoices($data));
                    }
                }
            }
        }
    }
}
