<?php

namespace App\Console\Commands\Utilities;

use App\Model\WaGrn;
use App\Model\WaSuppTran;
use App\WaSupplierInvoice;
use App\WaSupplierInvoiceItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateInvoicesForPastSuppTrans extends Command
{
    protected $signature = 'invoices:create';

    protected $description = 'Create invoices for past supplier transactions';

    public function handle()
    {
        $transactions = WaSuppTran::query()
            ->whereRaw('wa_supp_trans.suppreference = wa_supp_trans.document_no')
            ->whereHas('purchaseOrder')
            ->doesntHave('invoice')
            ->get();


        $this->line($transactions->count() . ' transactions found without invoices');

        DB::beginTransaction();

        try {
            foreach ($transactions as $transaction) {

                $grns = WaGrn::where('supplier_invoice_no', $transaction->suppreference)->get();

                if (!$grns->count()) {
                    continue;
                }

                $invoice = WaSupplierInvoice::create([
                    'wa_purchase_order_id' => $transaction->purchaseOrder->id,
                    'wa_supp_tran_id' => $transaction->id,
                    'grn_number' => $grns->first()->grn_number,
                    'grn_date' => $grns->first()->created_at,
                    'supplier_invoice_date' => $transaction->trans_date,
                    'invoice_number' => getCodeWithNumberSeries('SUPPLIER_INVOICE_NO'),
                    'supplier_invoice_number' => $transaction->suppreference,
                    'cu_invoice_number' => $transaction->cu_invoice_number,
                    'supplier_id' => $transaction->supplier->id,
                    'prepared_by' => $transaction->prepared_by ?? 411,
                    'vat_amount' => $transaction->vat_amount,
                    'amount' => $transaction->total_amount_inc_vat,
                ]);

                foreach ($grns as $grn) {
                    $invoiceDetails = json_decode($grn->invoice_info);
                    $total = (float)$invoiceDetails->order_price * (float)$invoiceDetails->qty;
                    $vat = $total * ((float) $invoiceDetails->vat_rate / 100);

                    WaSupplierInvoiceItem::create([
                        'wa_supplier_invoice_id' => $invoice->id,
                        'code' => $grn->item_code,
                        'description' => $grn->item_description,
                        'quantity' =>  $invoiceDetails->qty ?? 0,
                        'standart_cost_unit' => $invoiceDetails->order_price,
                        'discount_amount' => 0,
                        'vat_amount' => $vat,
                        'amount' => $total,
                    ]);
                }
            }

            DB::commit();

            $this->line('Invoices created successfully');
        } catch (\Throwable $th) {
            DB::rollback();

            $this->error($th);
        }
    }
}
