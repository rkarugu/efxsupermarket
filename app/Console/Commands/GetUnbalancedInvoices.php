<?php

namespace App\Console\Commands;

use App\Alert;
use App\Jobs\UnbalancedCompletedInvoices;
use App\Model\WaInternalRequisition;
use App\Notifications\UnbalancedInvoices;
use App\User;
use Illuminate\Console\Command;

class GetUnbalancedInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-unbalanced-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoices = WaInternalRequisition::whereDate('requisition_date', today())
            ->with('getRelatedItem','stockMoves','debtorTrans')
            ->withCount(['stockMoves', 'getRelatedItem'])
            ->get();
        $unbalanced_moves=0;
        $unbalanced_amounts=0;
        $unbalance_invoices = [];
        $unbalance_moves = [];
        $unbalance_debtors = [];
        foreach ($invoices as $invoice)
        {

            /*check  invoice items  vs stock moves*/
            $item_count = $invoice->get_related_item_count;
            $move_cont = $invoice->stock_moves_count;

            if ($item_count != $move_cont)
            {
                $unbalance_invoices[]= $invoice;
                $unbalanced_moves[]= $invoice;
                $unbalanced_moves +=1;
            }

            /*check invoice amount vs debtor trans amount*/
            $invoice_total = (int) $invoice->getOrderTotalForEsd();
            $debtor_tran_amount = (int) $invoice->debtorTrans->amount;
            if ($invoice_total != $debtor_tran_amount)
            {
                $unbalance_invoices[]= $invoice;
                $unbalance_debtors[]= $invoice;
                $unbalanced_amounts +=1;
            }
        }
        $items = collect($unbalance_invoices)->unique();

        if ($items->count() > 0)
        {

           return [
               'unbalanced_moves'=> $unbalanced_moves,
               'unbalanced_debtors'=> $unbalance_debtors,
           ];
        }
    }
}
