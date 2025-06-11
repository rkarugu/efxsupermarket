<?php

namespace App\Jobs;

use App\Interfaces\Finance\BankReconciliationInterface;
use App\Model\User;
use App\Models\PaymentVerification;
use App\Notifications\Reconciliation\PaymentApprovalsCompletedNotification;
use App\Enums\Status\PaymentVerification as StatusPaymentVerification;
use App\Model\WaDebtorTran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApproveReconciliationPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timeout = 600;

    public function __construct(
        protected array $data
    ) {
    }

    public function handle(BankReconciliationInterface $bankReconRepository): void
    {
        $bankReconRepository->approvePaymentReconciliations($this->data);

        $verification = PaymentVerification::with('debtorTrans')->find($this->data['verification']);
        $debtorTrans = WaDebtorTran::where([['verification_record_id',$verification->id],['verification_status', 'pending']])->count();
        if (!$debtorTrans) {
            $verification->update([
                'status' => StatusPaymentVerification::Approved->value
            ]);    
        }
        
        $user = User::find($this->data['user_id']);

        $user->notify(new PaymentApprovalsCompletedNotification($verification));
    }
}
