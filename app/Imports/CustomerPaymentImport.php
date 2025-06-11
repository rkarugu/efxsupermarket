<?php

namespace App\Imports;

use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaBanktran;
use App\WaTenderEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerPaymentImport implements ToCollection, WithHeadingRow, WithChunkReading
{

    public $bank_account;
    public $customer;
    public $companyPreference;
    public $accountuingPeriod;
    public $series_module;
    public $document_no;
    public $user;
    public $request;
    public $u;
    public $dateTime;


    public function __construct($bank_account, $customer, $companyPreference, $accountuingPeriod, $series_module, $document_no, $user, $request, $u)
    {
        $this->bank_account = $bank_account;
        $this->customer = $customer;
        $this->companyPreference = $companyPreference;
        $this->accountuingPeriod = $accountuingPeriod;
        $this->series_module = $series_module;
        $this->document_no = $document_no;
        $this->user = $user;
        $this->request = $request;
        $this->u = $u;
        $this->dateTime = date('Y-m-d H:i:s');
    }


    /**
     * @param Collection $collection
     */

    public function collection(Collection $collection): void
    {
        DB::transaction(function () use ($collection) {
            foreach ($collection as $row) {
                if (!$row['date'] || !$row['amount'] || !$row['approval_code']) {
                    continue;
                }
                $debtorTran = new WaDebtorTran();
                $debtorTran->type_number = $this->series_module ? $this->series_module->type_number : '';
                $debtorTran->wa_customer_id = $this->customer->id;
                $debtorTran->customer_number = $this->customer->customer_code;
                $debtorTran->trans_date = Carbon::createFromFormat('d/m/Y', $row['date']);
                $debtorTran->input_date = $this->dateTime;
                $debtorTran->wa_accounting_period_id = $this->accountuingPeriod ? $this->accountuingPeriod->id : null;
                $debtorTran->amount = '-' . $row['amount'];
                $debtorTran->document_no = $this->document_no;
                $debtorTran->wa_payment_method_id = $this->request->payment_type_id;
                $debtorTran->paid_by = $this->request->paid_by ?? $this->u->name;
                $debtorTran->user_id = $this->u->id;
                $debtorTran->reference = $row['approval_code'] . ' ' . $this->request->narrative;
                $debtorTran->salesman_id = @$this->user->wa_location_and_store_id;
                $debtorTran->salesman_user_id = @$this->user->id;
                $debtorTran->save();

                $cr = new WaGlTran();
                $cr->period_number = $this->accountuingPeriod ? $this->accountuingPeriod->period_no : null;
                $cr->wa_debtor_tran_id = $debtorTran->id;
                $cr->grn_type_number = $this->series_module->type_number;
                $cr->trans_date = $debtorTran->trans_date;
                $cr->restaurant_id = $this->u->restaurant_id;
                $cr->grn_last_used_number = $this->series_module->last_number_used;
                $cr->transaction_type = $this->series_module->description;
                $cr->transaction_no = $this->document_no;
                $cr->narrative = $this->customer->customer_code . ':' . $this->customer->customer_name;
                $cr->account = $this->bank_account->getGlDetail->account_code;
                $cr->amount = $row['amount'];
                $cr->save();

                $dr = new WaGlTran();
                $dr->period_number = $this->accountuingPeriod ? $this->accountuingPeriod->period_no : null;
                $dr->wa_debtor_tran_id = $debtorTran->id;
                $dr->grn_type_number = $this->series_module->type_number;
                $dr->trans_date = $debtorTran->trans_date;
                $dr->restaurant_id = $this->u->restaurant_id;
                $dr->grn_last_used_number = $this->series_module->last_number_used;
                $dr->transaction_type = $this->series_module->description;
                $dr->transaction_no = $this->document_no;
                $dr->narrative = $this->customer->customer_code . ':' . $this->customer->customer_name;
                $dr->account = $this->companyPreference->debtorsControlGlAccount->account_code;
                $dr->amount = '-' . $row['amount'];
                $dr->save();


                $btran = new WaBanktran();
                $btran->type_number = $this->series_module->type_number;
                $btran->document_no = $this->document_no;
                $btran->bank_gl_account_code = $this->bank_account->getGlDetail->account_code;
                $btran->reference = $row['approval_code'] . ' ' . $this->request->narrative;
                $btran->trans_date = $debtorTran->trans_date;
                $btran->wa_payment_method_id = $this->request->payment_type_id;
                $btran->amount = $row['amount'];
                $btran->wa_curreny_id = $this->request->wa_currency_manager_id;
                $btran->cashier_id = $this->u->id;
                $btran->save();

                $btran = new WaTenderEntry();
                $btran->document_no = $this->document_no;
                $btran->channel = 'Eazzy';
                $btran->account_code = $this->bank_account->getGlDetail->account_code;
                $btran->reference = $row['approval_code'];
                $btran->customer_id = $this->customer->id;
                $btran->trans_date = $debtorTran->trans_date;
                $btran->wa_payment_method_id = $this->request->payment_type_id;
                $btran->amount = $row['amount'];
                $btran->paid_by = $this->request->paid_by ?? $this->u->name;
                $btran->cashier_id = $this->u->id;
                $btran->save();
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
