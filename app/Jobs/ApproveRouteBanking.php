<?php

namespace App\Jobs;

use App\Model\PaymentMethod;
use App\Model\WaChartsOfAccount;
use App\Model\WaCompanyPreference;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveRouteBanking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $debtorTrans)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            foreach ($this->debtorTrans as $incomingTrans) {
                $trans = WaDebtorTran::find($incomingTrans->id);
                $trans->update(['verification_status' => 'approved']);

                $customerAccount = WaCustomer::find($trans->wa_customer_id);
                $paymentMethod = PaymentMethod::find($trans->wa_payment_method_id);
                if (!$paymentMethod) {
                    Log::info("Approve route banking failed: Payment method not found for trans $trans->document_no $trans->channel");
                }

                $account = WaChartsOfAccount::find($paymentMethod->gl_account_id);
                $companyPreference = WaCompanyPreference::find(1);
                $debtorsControl = $companyPreference->debtorsControlGlAccount?->account_code;
                $fraudAccount = '55001-007';

                // Debtors Control (Credit)
                WaGlTran::create([
                    'restaurant_id' => $trans->branch_id,
                    'tb_reporting_branch' => $trans->branch_id,
                    'transaction_type' => 'Customer Payment',
                    'transaction_no' => $trans->document_no,
                    'trans_date' => Carbon::now(),
                    'account' => $debtorsControl,
                    'amount' => abs($trans->amount) * -1,
                    'reference' => $trans->reference,
                    'narrative' => "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                ]);

                if (substr($trans->document_no, 0, 3) == 'RCT') {
                    WaGlTran::create([
                        'restaurant_id' => $trans->branch_id,
                        'tb_reporting_branch' => $trans->branch_id,
                        'transaction_type' => 'Customer Payment',
                        'transaction_no' => $trans->document_no,
                        'trans_date' => Carbon::now(),
                        'account' => $account->account_code,
                        'amount' => abs($trans->amount),
                        'reference' => $trans->reference,
                        'narrative' =>  "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                    ]);
                } else {
                    WaGlTran::create([
                        'restaurant_id' => $trans->branch_id,
                        'tb_reporting_branch' => $trans->branch_id,
                        'transaction_type' => 'Fraud Journal',
                        'transaction_no' => $trans->document_no,
                        'trans_date' => Carbon::now(),
                        'account' => $fraudAccount,
                        'amount' => abs($trans->amount),
                        'reference' => $trans->reference,
                        'narrative' => "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            Log::info("Approve route banking failed: " . $th->getMessage());
            DB::rollBack();
        }
    }
}
