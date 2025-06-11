<?php

namespace App\Repositories\Finance;

use App\Enums\Status\PaymentVerification as StatusPaymentVerification;
use App\Enums\PaymentChannel;
use App\Interfaces\Finance\BankReconciliationInterface;
use App\Model\PaymentMethod;
use Illuminate\Support\Facades\DB;

use App\Model\WaGlTran;
use App\Model\WaDebtorTran;
use App\Model\WaBanktran;
use Illuminate\Support\Carbon;

use App\Model\WaBankAccount;
use App\Model\WaChartsOfAccount;
use App\Model\WaNumerSeriesCode;
use App\Model\WaCompanyPreference;
use App\Models\GlPostingLogs;
use App\Models\PaymentVerification;
use App\Models\PaymentVerificationBank;
use App\Models\PaymentVerificationSystem;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BankReconciliationRepository implements BankReconciliationInterface
{
    const VOOMA = 'Vooma';
    const EAZZY = 'Eazzy';
    const MPESA = 'Mpesa';

    protected string $selectedBank;

    protected float $systemTotal = 0;
    protected int $systemCount = 0;

    protected float $bankCount = 0;
    protected float $bankTotal = 0;

    protected array $reconciledTransactions = [];
    protected float $reconciledTotal = 0;

    protected array $missingTransactions = [];
    protected float $missingTotal = 0;

    protected array $mibTransactions = [];
    protected float $mibTotal = 0;

    protected array $flaggedTransactions = [];
    protected float $flaggedTotal = 0;

    protected array $ignoredTransactions = [];
    protected float $ignoredTotal = 0;

    protected array $notReconciled = [];
    protected array $doubles = [];

    protected bool $dataProcessed = false;



    public function getPaymentVerifications()
    {
        try {
            $payments = PaymentVerification::with('branch')->get();
            return response($payments, 200);
        } catch (\Exception $e) {
            return response('No Payments Verification Found', 400);
        }
    }

    public function getSinglePaymentVerification($id)
    {
        try {
            $payment = DB::table('payment_verifications')->where('payment_verifications.id', $id)
                ->join('restaurants', 'payment_verifications.branch_id', 'restaurants.id')
                ->select(
                    'payment_verifications.id',
                    'payment_verifications.start_date',
                    'payment_verifications.end_date',
                    'restaurants.id as branch_id',
                    'restaurants.name as branch_name',
                    'payment_verifications.channel',
                    'payment_verifications.status'
                )->get()->first();

            return $payment;
        } catch (\Exception $e) {

            return response('No Payments Verification Found', 400);
        }
    }

    public function getSinglePaymentVerificationBank($id)
    {
        try {
            $allBank = PaymentVerificationBank::query()  // DB::table('payment_verification_banks')
                // ->where('payment_verification_banks.payment_verification_id',$id)
                ->join('payment_verifications', 'payment_verifications.id', 'payment_verification_banks.payment_verification_id')
                ->select(
                    'payment_verification_banks.id',
                    'payment_verification_banks.amount',
                    'payment_verification_banks.bank_date',
                    'payment_verification_banks.reference'
                );
            $allBankData = $allBank->get();
            $allMissingBankData = $allBank->clone()->where('payment_verification_banks.status', StatusPaymentVerification::Pending->value)->get();
            return [
                'allBankData' => $allMissingBankData,
                'allMissingBankData' => $allMissingBankData
            ];
        } catch (\Exception $e) {
            return response('No Payments Verification Found', 400);
        }
    }

    public function getSinglePaymentVerificationSystem($id)
    {
        try {
            $allSystem = DB::table('payment_verification_systems')->where('payment_verification_systems.payment_verification_id', $id)
                ->join('payment_verifications', 'payment_verifications.id', 'payment_verification_systems.payment_verification_id')
                ->join('wa_debtor_trans', 'wa_debtor_trans.id', 'payment_verification_systems.debtor_id')
                ->join('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
                ->leftjoin('payment_verification_banks', 'payment_verification_banks.verification_system_id', 'payment_verification_systems.id')
                ->leftjoin('users as verify_user', 'payment_verification_systems.verified_by', 'verify_user.id')
                ->leftjoin('users as approval_user', 'payment_verification_systems.approved_by', 'approval_user.id')
                ->select(
                    'payment_verification_systems.id',
                    'wa_debtor_trans.trans_date',
                    'wa_debtor_trans.channel',
                    'wa_customers.customer_name',
                    'payment_verification_systems.document_no',
                    'payment_verification_systems.amount',
                    'payment_verification_systems.reference',
                    'payment_verification_banks.reference as bank_reference',
                    'payment_verification_systems.status',
                    'payment_verification_systems.verified_date',
                    'verify_user.name as verified_by',
                    'payment_verification_systems.approved_date',
                    'approval_user.name as approved_by'
                );
            $allSystemData = $allSystem->clone()->get();
            $allMatchData = $allSystem->clone()->where('payment_verification_systems.status', '!=', StatusPaymentVerification::Pending->value)->get();
            $allMissingSystemData = $allSystem->clone()->where('payment_verification_systems.status', StatusPaymentVerification::Pending->value)->get();

            return [
                'allSystemData' => $allSystemData,
                'allMatchData' => $allMatchData,
                'allMissingSystemData' => $allMissingSystemData
            ];
        } catch (\Exception $e) {
            return response('No Payments Verification Found', 400);
        }
    }

    public function getPaymentApprovals()
    {
        try {
            $approvals = PaymentVerificationSystem::with('verifiedBy', 'verificationRange')->where([['status', StatusPaymentVerification::Verified->value], ['approved_by', NULL]])->get();
            return response($approvals, 200);
        } catch (\Exception $e) {
            return response('No Approval Verification Found', 400);
        }
    }

    public function savePaymentVerification($data)
    {
        DB::beginTransaction();
        try {
            $verify = new PaymentVerification();
            $verify->created_by = Auth::user()->id;
            $verify->start_date = $data['start_date'];
            $verify->end_date = $data['end_date'];
            $verify->branch_id = $data['branch'];
            $verify->channel = $data['channel'];
            $verify->save();

            DB::commit();
            return response($verify, 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
    }

    public function saveDebtorTrans($data)
    {
        DB::beginTransaction();
        try {
            $debtorTrans = WaDebtorTran::query()
                ->where([['reconciled', false], ['branch_id', request()->branch], ['channel', request()->channel]])
                ->whereBetween('trans_date', [$data->start_date . ' 00:00:00', $data->end_date . ' 23:59:59'])
                ->where('document_no', 'like', '%RCT%')
                ->doesnthave('systemVerification')
                ->get()->map(function ($record) {
                    $record->amount = abs($record->amount);
                    return $record;
                });

            foreach ($debtorTrans as $key => $value) {
                $verify = new PaymentVerificationSystem();
                $verify->payment_verification_id = $data->id;
                $verify->debtor_id = $value->id;
                $verify->amount = $value->amount;
                $verify->document_no = $value->document_no;
                $verify->reference = $value->reference;
                $verify->save();
            }

            DB::commit();
            return response('', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
    }

    function getDatesInRange($date1, $date2, $format = 'Y-m-d')
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while ($current <= $date2) {
            $dates[] = date($format, $current);
            $current = strtotime($stepVal, $current);
        }
        return $dates;
    }

    public function saveExtractedBankTrans($data)
    {
        $record = $data['record'];
        $channel = $this->checkUploadHeaders($record[0]);
        foreach ($record as $index => $rec) {
            if ($index != 0) {

                DB::beginTransaction();
                try {
                    switch ($channel) {
                        case $this::VOOMA:
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $rec[0])));
                            $dateRange = $this->getDatesInRange($data['verification']->start_date, $data['verification']->end_date);
                            if (in_array($date, $dateRange)) {
                                DB::table('payment_verification_banks')->insert([
                                    'payment_verification_id' => $data['verification']->id,
                                    'reference' => $rec[1],
                                    'amount' => (float)(str_replace(',', '', $rec[4])),
                                    'bank_date' => $date
                                ]);
                            }
                            break;
                        case $this::EAZZY:
                            $date = date('Y-m-d', strtotime($rec[0]));
                            $dateRange = $this->getDatesInRange($data['verification']->start_date, $data['verification']->end_date);
                            if (in_array($date, $dateRange)) {
                                $banks = new PaymentVerificationBank();
                                $banks->payment_verification_id = $data['verification']->id;
                                $banks->reference = $rec[1];
                                $banks->amount = (float)(str_replace(',', '', $rec[2]));
                                $banks->bank_date = $date;
                                $banks->save();
                            }
                            break;
                        case $this::MPESA:
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $rec[0])));
                            $dateRange = $this->getDatesInRange($data['verification']->start_date, $data['verification']->end_date);
                            if (in_array($date, $dateRange)) {
                                $banks = new PaymentVerificationBank();
                                $banks->payment_verification_id = $data['verification']->id;
                                $banks->reference = $rec[6];
                                $banks->amount = (float)(str_replace(',', '', $rec[4]));
                                $banks->bank_date = $date;
                                $banks->save();
                            }
                            break;

                        default:
                            null;
                            break;
                    }

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return response($e->getMessage(), 500);
                }
            }
        }
    }


    public function processReconciliation($data)
    {
        $systemData = DB::table('payment_verification_systems')
            ->where([['payment_verification_id', $data->id], ['status', StatusPaymentVerification::Pending->value]])
            ->get();
        $bankData = DB::table('payment_verification_banks')
            ->where([['payment_verification_id', $data->id], ['status', StatusPaymentVerification::Pending->value]])
            ->get();

        foreach ($bankData as $key => $bank) {
            $lookupRef = $bank->reference;
            $dts = $systemData->filter(function ($_dt) use ($lookupRef) {
                return str_contains(trim($_dt->reference), trim($lookupRef)) || str_contains(trim($lookupRef), trim($_dt->reference));
            });
            if (count($dts)) {
                if (count($dts) == 1) {
                    $first = $dts->first();
                    if ($first->amount == $bank->amount) {
                        // Matching
                        DB::table('payment_verification_systems')
                            ->where('id', $first->id)
                            ->update(['status' => StatusPaymentVerification::Verified->value]);
                        DB::table('payment_verification_banks')
                            ->where('id', $bank->id)
                            ->update([
                                'status' => StatusPaymentVerification::Verified->value,
                                'verification_system_id' => $first->id
                            ]);
                    } else {
                        # code...
                    }
                } else {
                    // Duplicates
                }
            }
        }
    }

    public function verifyPaymentReconciliations($id, $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['reconJson'] as $key => $value) {
                DB::table('payment_verification_systems')
                    ->where('id', $value)
                    ->update([
                        'verified_by' => Auth::user()->id,
                        'verified_date' => Carbon::now(),
                        'status' => StatusPaymentVerification::Verified
                    ]);

                DB::table('payment_verification_banks')
                    ->where([['payment_verification_id', $id], ['verification_system_id', $value]])
                    ->update(['status' => StatusPaymentVerification::Verified]);
            }

            DB::commit();
            $paymentCount = PaymentVerificationSystem::where('payment_verification_id', $id)->count();
            $paymentStatusCount = PaymentVerificationSystem::where([['payment_verification_id', $id], ['status', StatusPaymentVerification::Pending->value]])->count();
            if ($paymentCount != $paymentStatusCount) {
                $status = StatusPaymentVerification::PartiallyVerified->value;
                if ($paymentStatusCount == 0) {
                    $status = StatusPaymentVerification::Verified->value;
                }
                DB::table('payment_verifications')
                    ->where('id', $id)
                    ->update(['status' => $status]);
            }
            return response('Payment Verification successfully', 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info("Failed to Verify Payment, citing {$e->getMessage()}");
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateTransactionAndVerify($data)
    {
        if ($data['type'] == 'system') {
            DB::table('payment_verification_systems')
                ->where('id', $data['id'])
                ->update(['status' => StatusPaymentVerification::Verifying->value]);
            DB::table('payment_verification_banks')
                ->where('id', $data['reference'])
                ->update([
                    'status' => StatusPaymentVerification::Verifying->value,
                    'verification_system_id' => $data['id']
                ]);
        } else {
            DB::table('payment_verification_systems')
                ->where('id', $data['reference'])
                ->update(['status' => StatusPaymentVerification::Verifying->value]);
            DB::table('payment_verification_banks')
                ->where('id', $data['id'])
                ->update([
                    'status' => StatusPaymentVerification::Verifying->value,
                    'verification_system_id' => $data['reference']
                ]);
        }
        return response('Transaction Matched Successfull', 200);
    }

    public function approvePaymentReconciliations($data)
    {
        if (isset($data['approveAll'])) {
            $debtorTrans = WaDebtorTran::query()
                ->where('verification_record_id', $data['verification'])
                ->where('verification_status', StatusPaymentVerification::Verified->value)
                ->whereHas('verificationBank');

            $bankPostCode = getCodeWithNumberSeries('BANK_POSTINGS');
            updateUniqueNumberSeries('BANK_POSTINGS', $bankPostCode);

            $companyPreference = WaCompanyPreference::find(1);
            $bank_account = WaBankAccount::where('account_name', 'EQUITY MAKONGENI')->first();

            DB::beginTransaction();

            try {
                PaymentVerificationBank::whereIn('matched_debtors_id', $debtorTrans->pluck('id')->toArray())
                    ->update([
                        'status' => StatusPaymentVerification::Approved->value
                    ]);

                $debtorTrans->with('verificationBank');

                $debtorTrans->chunk(1000, function ($transactions) use ($data, $companyPreference, $bankPostCode, $bank_account) {
                    foreach ($transactions as $value) {
                        $dataArray = [
                            'id' => $value->id,
                            'accounting_period' => $value->wa_accounting_period_id,
                            'branch' => $value->branch_id,
                            'document_no' => $value->document_no,
                            'reference' => $value->reference,
                            'amount' => abs($value->amount),
                            'bank_ref' => $value->verificationBank->reference,
                            'salesman_user_id' => $value->salesman_user_id,
                            'channel' => $value->channel,
                            'debtor_account_code' => $companyPreference->debtorsControlGlAccount?->account_code,
                            'verification_id' => $data['verification'],
                            'bank_post_code' => $bankPostCode,
                            'customer_id' => $value->wa_customer_id,
                            'customer_number' => $value->customer_number,
                            'trans_date' => $value->trans_date
                        ];

                        $this->glTransApprovedReconciliation($dataArray, $bank_account);

                        $value->update([
                            'reconciled' => true,
                            'bank_ref' => $dataArray['bank_ref'],
                            'verification_status' => 'Approved'
                        ]);
                    }
                });

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }

        if (isset($data['reconJson'])) {
            DB::beginTransaction();
            try {
                foreach ($data['reconJson'] as $key => $value) {
                    $debtorTrans = DB::table('wa_debtor_trans')->find($value);
                    $bank = PaymentVerificationBank::where('matched_debtors_id', $value)->get()->first();
                    $bankData = $bank;
                    $bank->status = StatusPaymentVerification::Approved->value;
                    $bank->save();

                    $companyPreference = WaCompanyPreference::find(1);

                    $bankPostCode = getCodeWithNumberSeries('BANK_POSTINGS');

                    updateUniqueNumberSeries('BANK_POSTINGS', $bankPostCode);
                    // Collect Data to pass to GL Trans
                    $dataArray = [
                        'id' => $debtorTrans->id,
                        'accounting_period' => $debtorTrans->wa_accounting_period_id,
                        'branch' => $debtorTrans->branch_id,
                        'document_no' => $debtorTrans->document_no,
                        'reference' => $debtorTrans->reference,
                        'amount' => abs($debtorTrans->amount),
                        'bank_ref' => $bankData->reference,
                        'salesman_user_id' => $debtorTrans->salesman_user_id,
                        'channel' => $debtorTrans->channel,
                        'debtor_account_code' => $companyPreference->debtorsControlGlAccount?->account_code,
                        'verification_id' => $data['verification'],
                        'bank_post_code' => $bankPostCode,
                        'customer_number' => $debtorTrans->customer_number,
                        'trans_date' => $debtorTrans->trans_date
                    ];

                    //  Run GL Trans
                    $this->glTransApprovedReconciliation($dataArray, $bank_account);
                }

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }
    }

    public function checkUploadHeaders($header)
    {
        switch ($header) {
            case ["\t\tTXN DATE", "DESCRIPTION", "VALUE DATE", "MONEY OUT", "MONEY IN", "LEDGER BALANCE"]:
                return $this::VOOMA;
                break;
            case ["Date", "Invoice No", "Route", "Pesaflow Code", "Amount Due", "Amount Paid", "Reference", null, null]:
                return $this::MPESA;
                break;
            case ["Transaction Date", "Narrative", "Credit", "Customer Reference", "Remarks1", "Remarks2"]:
                return $this::EAZZY;
                break;
            default:
                return NULL;
                break;
        }
    }

    public function glTransApprovedReconciliation($data, $bank_account)
    {
        $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();

        $paymentMethod = PaymentMethod::where('title',$data['channel'])->first();
        $chartOfAccount = WaChartsOfAccount::find($paymentMethod->gl_account_id);

        $btran = new WaBanktran();
        $btran->type_number = $series_module->type_number;
        $btran->document_no = $data['document_no'];
        $btran->bank_gl_account_code = $chartOfAccount->account_code;
        $btran->reference = $data['reference'];
        $btran->trans_date = Carbon::now();
        $btran->wa_payment_method_id = $paymentMethod->id;
        $btran->amount = $data['amount'];
        $btran->wa_curreny_id = 1;
        $btran->cashier_id = $data['salesman_user_id'];
        $btran->save();

        $cr = new WaGlTran();
        $cr->period_number = $data['accounting_period'];
        $cr->wa_debtor_tran_id = $data['id'];
        $cr->grn_type_number = $series_module->type_number;
        $cr->trans_date = $data['trans_date'];
        $cr->restaurant_id = $data['branch'];
        $cr->tb_reporting_branch = $data['branch'];
        $cr->grn_last_used_number = $series_module->last_number_used;
        $cr->transaction_type = $series_module->description;
        $cr->transaction_no = $data['bank_post_code'];
        $cr->narrative = $data['reference'].' / '.$data['document_no'].' / '.$data['customer_number'];
        $cr->account = $chartOfAccount->account_code;
        $cr->amount = $data['amount'];
        $cr->customer_id = $data['customer_id'];
        $cr->save();

        $dr = new WaGlTran();
        $dr->period_number = $data['accounting_period'];
        $dr->wa_debtor_tran_id = $data['id'];
        $dr->grn_type_number = $series_module->type_number;
        $dr->trans_date = $data['trans_date'];
        $dr->restaurant_id = $data['branch'];
        $dr->tb_reporting_branch = $data['branch'];
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->transaction_type = $series_module->description;
        $dr->transaction_no = $data['bank_post_code'];
        $dr->narrative = $data['reference'].' / '.$data['document_no'].' / '.$data['customer_number'];
        $dr->account = $data['debtor_account_code'];
        $dr->amount = '-' . $data['amount'];
        $dr->customer_id = $data['customer_id'];
        $dr->save();

        GlPostingLogs::create([
            'created_by' => Auth::user()->id,
            'transaction_no' => $data['bank_post_code'],
            'document_no' => $data['document_no'],
            'wa_debtor_trans_id' => $data['id'],
            'wa_banktrans_id' => $btran->id,
            'amount' => $data['amount'],
            'payment_verification_id' => $data['verification_id']
        ]);
    }

    public function updateDebtorsTable()
    {
        ini_set('max_execution_time', 900);
        // Update Debtor_Trans with Tender Entries 
        $debtorTrans = DB::table('wa_debtor_trans')->where('reconciled', false)
            ->where('document_no', 'like', '%RCT%')
            ->get();
        for ($i = 0; $i < count($debtorTrans); $i++) {
            $value = $debtorTrans[$i];
            $tender = DB::table('wa_tender_entries')
                ->where('wa_tender_entries.document_no', $value->document_no)
                ->join('wa_customers', 'wa_customers.id', 'wa_tender_entries.customer_id')
                ->join('routes', 'routes.id', 'wa_customers.route_id')
                ->select('wa_tender_entries.id', 'wa_tender_entries.channel', 'routes.restaurant_id as branch')
                ->first();

            if ($tender) {
                DB::table('wa_debtor_trans')
                    ->where('id', $value->id)
                    ->update(['channel' => $tender->channel, 'branch_id' => $tender->branch]);
            }
        }
    }
}
