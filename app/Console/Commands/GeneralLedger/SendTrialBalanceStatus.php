<?php

namespace App\Console\Commands\GeneralLedger;

use App\Alert;
use App\Model\User;
use App\Model\WaGlTran;
use App\Model\WaSuppTran;
use App\Notifications\GeneralLedger\TrialBalanceStatus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendTrialBalanceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-trial-balance-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trial balance status notification to specific users';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $roleIds = [];
        $users = [];

        $credit_amount = 0;
        $debit_amount = 0;
        $data_credit_amount = 0;
        $data_debit_amount = 0;
        $credit_debit_variance = 0;
        $trans_amount = 0;

        $start_date = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
        $end_date = Carbon::today()->endOfDay()->format('Y-m-d H:i:s');

        // $start_date = Carbon::create(2024, 8, 12, 0, 0, 0)->format('Y-m-d H:i:s');
        // $end_date = Carbon::create(2024, 8, 12, 23, 59, 59)->format('Y-m-d H:i:s');

        $alert = Alert::where('alert_name', 'trial_balance_status')->first();
        if ($alert->recipient_type == 'role') {
            $roleIds = explode(',', $alert->recipients);
            $users = User::whereIn('role_id', $roleIds)->get();
        } else if ($alert->recipient_type == 'user') {
            $userIds = explode(',', $alert->recipients);
            $users = User::whereIn('id', $userIds)->get();
        } else {
            $this->error('Invalid recipient type.');
            return Command::FAILURE;
        }

        $all_item = WaGlTran::with(['getAccountDetail.getRelatedGroup'])->select([
            'account',
            DB::RAW("COALESCE(sum(amount),0) as sm")
        ])
            ->whereBetween('trans_date', [$start_date, $end_date])
            ->whereHas('getAccountDetail')
            ->groupBy('account')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($all_item as $item) {
            if ($item->sm > 0) {
                $credit_amount += $item->sm;
            } else if ($item->sm < 0) {
                $debit_amount += abs($item->sm);
            }
        }

        $credit_debit_variance = abs($credit_amount - $debit_amount);

        $data = WaGlTran::where('account', '55001-017')
            ->whereBetween('trans_date', [$start_date, $end_date])
            ->get();

        foreach ($data as $data_amount) {
            if ($data_amount->amount > 0) {
                $data_credit_amount += $data_amount->amount;
            } else if ($data_amount->amount < 0) {
                $data_debit_amount += $data_amount->amount;
            }
        }

        $trans = WaSuppTran::whereBetween('trans_date', [$start_date, $end_date])
            ->sum('total_amount_inc_vat');

        $trans_amount = abs($trans);

        $trans_amount_variance = abs($data_credit_amount - $trans_amount);

        foreach ($users as $user) {
            $user->notify(new TrialBalanceStatus(
                $start_date,
                $end_date,
                $user,
                $credit_amount,
                $debit_amount,
                $credit_debit_variance,
                $data_credit_amount,
                $trans_amount,
                $trans_amount_variance
            ));
        }
    }
}
