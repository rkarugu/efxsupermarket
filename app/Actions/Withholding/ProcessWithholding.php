<?php

namespace App\Actions\Withholding;

use App\Model\WaAccountingPeriod;
use App\Model\WaBanktran;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Models\WithholdingPaymentVoucher;

class ProcessWithholding
{
    public function process(WithholdingPaymentVoucher $withholdingVoucher)
    {
        $accountuingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        $series_module = WaNumerSeriesCode::where('module', 'WITHHOLDING_TAX_PAYMENT_VOUCHERS')->first();
        $bank_account = $withholdingVoucher->bankAccount;       

        $btran = new WaBanktran();
        $btran->type_number = $series_module->type_number;
        $btran->document_no = $document_no = $withholdingVoucher->number;
        $btran->bank_gl_account_code = $bank_account->getGlDetail->account_code;
        $btran->reference = $withholdingVoucher->number;
        $btran->trans_date = $withholdingVoucher->created_at;
        $btran->amount =  $withholdingVoucher->withholding_amount * -1;
        $btran->wa_curreny_id = 1;
        $btran->save();        

        // CREDIT Bank
        $cr = new WaGlTran();
        $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
        $cr->grn_type_number = $series_module->type_number;
        $cr->trans_date = $withholdingVoucher->created_at;
        $cr->restaurant_id = $withholdingVoucher->restaurant_id;
        $cr->tb_reporting_branch = $withholdingVoucher->restaurant_id;
        $cr->grn_last_used_number = $series_module->last_number_used;
        $cr->transaction_type = $series_module->description;
        $cr->transaction_no = $document_no;
        $cr->reference = $withholdingVoucher->withholdingFile->file_no;
        $cr->narrative = "Withholding Tax Payment - $withholdingVoucher->number";
        $cr->account = $bank_account->getGlDetail->account_code;
        $cr->amount = $withholdingVoucher->amount * -1;
        $cr->save();

        // DEBIT Withholding
        $dr = new WaGlTran();
        $dr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
        $dr->grn_type_number = $series_module->type_number;
        $dr->trans_date = $withholdingVoucher->created_at;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $series_module->description;
        $dr->transaction_no = $document_no;
        $dr->reference = $withholdingVoucher->withholdingFile->file_no;
        $dr->restaurant_id = $withholdingVoucher->restaurant_id;
        $dr->tb_reporting_branch = $withholdingVoucher->restaurant_id;
        $dr->narrative = "Withholding Tax Payment - $withholdingVoucher->number";
        $dr->account = $withholdingVoucher->withholdingGlAccount->account_code;
        $dr->amount = $withholdingVoucher->amount;
        $dr->save();
    }
}
