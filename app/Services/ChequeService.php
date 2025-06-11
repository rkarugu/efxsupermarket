<?php

namespace App\Services;

use App\Model\RegisterCheque;
use App\Models\ChequeBank;
use Illuminate\Support\Facades\DB;

class ChequeService
{
    public function add(RegisterCheque $cheque)
    {
        DB::transaction(function() use ($cheque){

            $user = getLoggeduserProfile();
            $cheque->is_bounced_transfer = 1;
            $cheque->save();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','CQ')->first();

            $customer = \App\Model\WaCustomer::find($cheque ->wa_customer_id);
            $grn_number = getCodeWithNumberSeries('CQ');
            updateUniqueNumberSeries('CQ', $grn_number);
            $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
            $dateTime = date('Y-m-d H:i:s');
            $WaDebtorTran[] = [
                'type_number'=>$series_module->type_number,
                'wa_customer_id'=>@$customer->id,
                'customer_number'=>@$customer->customer_code,
                'invoice_customer_name'=>@$customer->customer_name,
                'trans_date'=>$dateTime,
                'input_date'=>$dateTime,
                'wa_accounting_period_id'=>$WaAccountingPeriod ? $WaAccountingPeriod->id : null,
                'shift_id'=>NULL,
                'reference'=> $customer->customer_name.' Cheque : '.$cheque->cheque_no.' Received',
                'amount'=>0,
                'document_no'=>$grn_number,
                'route_id'=>$customer->route_id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'created_at'=>date('Y-m-d H:i:s'),
                'register_cheque_id'=>$cheque->id
            ];
            if(count($WaDebtorTran)>0){
                \App\Model\WaDebtorTran::insert($WaDebtorTran);
            }

        });
    }
    public function clear(RegisterCheque $cheque, $status)
    {
        $cheque->status =$status;
        $cheque->clearance_date =now();
        $cheque->save();

        if ($status == 'Bounced')
        {
            $this->bounced($cheque);
        }else{
           $this->cleared($cheque);
        }
    }
    public function bounced(RegisterCheque $cheque)
    {
       DB::transaction(function() use ($cheque){

            $user = getLoggeduserProfile();
            $cheque->is_bounced_transfer = 1;
            $cheque->save();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','CQ')->first();
            $bank = ChequeBank::find($cheque->bank_deposited);
            $fine  = $bank->bounce_penalty;
            $customer = \App\Model\WaCustomer::find($cheque ->wa_customer_id);
            $grn_number = getCodeWithNumberSeries('CQ');
            updateUniqueNumberSeries('CQ', $grn_number);
            $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
            $dateTime = date('Y-m-d H:i:s');
            $WaDebtorTran[] = [
                'type_number'=>$series_module->type_number,
                'wa_customer_id'=>@$customer->id,
                'customer_number'=>@$customer->customer_code,
                'invoice_customer_name'=>@$customer->customer_name,
                'trans_date'=>$dateTime,
                'input_date'=>$dateTime,
                'wa_accounting_period_id'=>$WaAccountingPeriod ? $WaAccountingPeriod->id : null,
                'shift_id'=>NULL,
                'reference'=> $cheque->drawers_bank.'/Bounced Cheque : '.$cheque->cheque_no.' bank Charge',
                'amount'=>$fine,
                'document_no'=>$grn_number,
                'route_id'=>$customer->route_id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'created_at'=>date('Y-m-d H:i:s'),
                'register_cheque_id'=>$cheque->id
            ];
            if(count($WaDebtorTran)>0){
                \App\Model\WaDebtorTran::insert($WaDebtorTran);
            }

        });
    }

    public function cleared(RegisterCheque $cheque)
    {
        DB::transaction(function() use ($cheque){

            $user = getLoggeduserProfile();
            $cheque->is_bounced_transfer = 1;
            $cheque->save();
            $series_module = \App\Model\WaNumerSeriesCode::where('module','CQ')->first();
            $bank = ChequeBank::find($cheque->bank_deposited);
            $fine  = $bank->bounce_penalty;
            $customer = \App\Model\WaCustomer::find($cheque ->wa_customer_id);
            $grn_number = getCodeWithNumberSeries('CQ');
            updateUniqueNumberSeries('CQ', $grn_number);
            $WaAccountingPeriod =  \App\Model\WaAccountingPeriod::where('is_current_period','1')->first();
            $dateTime = date('Y-m-d H:i:s');
            $WaDebtorTran[] = [
                'type_number'=>$series_module->type_number,
                'wa_customer_id'=>@$customer->id,
                'customer_number'=>@$customer->customer_code,
                'invoice_customer_name'=>@$customer->customer_name,
                'trans_date'=>$dateTime,
                'input_date'=>$dateTime,
                'wa_accounting_period_id'=>$WaAccountingPeriod ? $WaAccountingPeriod->id : null,
                'shift_id'=>NULL,
                'reference'=> $cheque->drawers_bank.'/Cleared Cheque : '.$cheque->cheque_no,
                'amount'=> - $cheque->amount,
                'document_no'=>$grn_number,
                'route_id'=>$customer->route_id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'created_at'=>date('Y-m-d H:i:s'),
                'register_cheque_id'=>$cheque->id
            ];
            if(count($WaDebtorTran)>0){
                \App\Model\WaDebtorTran::insert($WaDebtorTran);
            }

        });
    }

}