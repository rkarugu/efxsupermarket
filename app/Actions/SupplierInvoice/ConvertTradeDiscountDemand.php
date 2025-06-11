<?php

namespace App\Actions\SupplierInvoice;

use App\FinancialNote;
use App\Model\User;
use App\Model\WaAccountingPeriod;
use App\Model\WaChartsOfAccount;
use App\Model\WaCompanyPreference;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaSuppTran;
use App\Models\TradeDiscountDemand;

class ConvertTradeDiscountDemand
{
    public function convert(TradeDiscountDemand $demand, array $data, User $user)
    {
        $accountuingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        $series_module = WaNumerSeriesCode::where('module', 'TRADE_DISCOUNT_DEMANDS')->first();
        $purchasesAccount = WaChartsOfAccount::where('account_name', 'PURCHASES')->first();

        $demand->update([
            'supplier_reference' => $data['supplier_reference'],
            'cu_invoice_number' => $data['cu_invoice_number'],
            'note_date' => $data['note_date'],
            'memo' => $data['memo'],
            'processed' => true,
            'processed_by' => $user->id,
            'processed_at' => now(),
        ]);

        // Credit Note
        $financialNote = FinancialNote::create([
            'note_no' => getCodeWithNumberSeries('FINANCIAL_NOTES'),
            'type' => 'CREDIT',
            'supplier_id' => $demand->supplier_id,
            'note_date' => $demand->note_date,
            'location_id' => $user->restaurant_id,
            'cu_invoice_number' => $demand->cu_invoice_number,
            'supplier_invoice_number' => $demand->supplier_reference,
            'memo' => $demand->memo,
            'tax_amount' => 0,
            'withholding_amount' => 0,
            'amount' => $demand->amount,
            'created_by' => $demand->processed_by,
        ]);

        $financialNote->items()->create([
            'financial_note_id' => $financialNote->id,
            'account_id' => $purchasesAccount->id,
            'memo' => $demand->memo,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'withholding_amount' => 0,
            'amount' => $demand->amount,
        ]);

        $demand->update([
            'credit_note_no' => $financialNote->note_no,
        ]);

        $newSupplierTrans = new WaSuppTran();
        $newSupplierTrans->document_no = $document_no = $demand->demand_no;
        $newSupplierTrans->trans_date = $demand->created_at;
        $newSupplierTrans->suppreference = $demand->supplier_reference;
        $newSupplierTrans->supplier_no = $demand->supplier->supplier_code;
        $newSupplierTrans->grn_type_number = $series_module->type_number;
        $newSupplierTrans->prepared_by = $user->id;
        $newSupplierTrans->total_amount_inc_vat =  $demand->amount * -1;
        $newSupplierTrans->save();

        // DEBIT Creditors
        $dr = new WaGlTran();
        $dr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
        $dr->wa_supp_tran_id = $newSupplierTrans->id;
        $dr->grn_type_number = $series_module->type_number;
        $dr->trans_date = $demand->created_at;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $series_module->description;
        $dr->transaction_no = $document_no;
        $dr->reference = $document_no;
        $dr->restaurant_id = $user->restaurant_id;
        $dr->tb_reporting_branch = $user->restaurant_id;
        $dr->narrative = "Trade discount demand - $demand->demand_no";
        $dr->supplier_account_number =  $demand->supplier->supplier_code;
        $companyPreference = WaCompanyPreference::where('id', '1')->first();
        $dr->account = $companyPreference->creditorControlGlAccount->account_code;
        $dr->amount = $demand->amount;
        $dr->save();

        // CREDIT Discounts
        $cr = new WaGlTran();
        $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
        $cr->wa_supp_tran_id = $newSupplierTrans->id;
        $cr->grn_type_number = $series_module->type_number;
        $cr->trans_date = $demand->created_at;
        $cr->restaurant_id = $user->restaurant_id;
        $cr->tb_reporting_branch = $user->restaurant_id;
        $cr->grn_last_used_number = $series_module->last_number_used;
        $cr->transaction_type = $series_module->description;
        $cr->transaction_no = $document_no;
        $cr->reference = $document_no;
        $cr->supplier_account_number = $demand->supplier->supplier_code;
        $cr->narrative = "Trade discount demand - $demand->demand_no";
        $companyPreference = WaCompanyPreference::where('id', '1')->first();
        $cr->account = $companyPreference->discountReceivedGlAccount->account_code;
        $cr->amount = $demand->amount * -1;
        $cr->save();
    }
}
