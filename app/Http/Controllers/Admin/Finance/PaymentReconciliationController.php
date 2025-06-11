<?php

namespace App\Http\Controllers\Admin\Finance;

use Throwable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Interfaces\Finance\BankReconciliationInterface;
use App\Model\WaDebtorTran;
use App\Models\SuspendedTransaction;
use App\Models\PaymentVerification;
use App\Models\PaymentVerificationSystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\UserLog;
use App\Jobs\ApproveReconciliationPayments;
use App\Services\ExcelDownloadService;
use Yajra\DataTables\Facades\DataTables;
use App\Enums\Status\PaymentVerification as StatusPaymentVerification;
use App\Model\Restaurant;
use App\Model\PaymentMethod;

class PaymentReconciliationController extends Controller
{

    private BankReconciliationInterface $bankReconRepository;

    public function __construct(BankReconciliationInterface $bankReconRepository)
    {
        $this->bankReconRepository = $bankReconRepository;
    }

    public function verification_process(Request $request)
    {
        ini_set('max_execution_time', 600);

        try {
            $data = $request->all();
            if ($request->topup_form) {
                $verificationRecord = PaymentVerification::find($request->verification);
            } else {
                $verificationRecord = new PaymentVerification();
                $verificationRecord->created_by = Auth::user()->id;
                $verificationRecord->start_date = $data['start_date'];
                $verificationRecord->end_date = $data['end_date'];
                $verificationRecord->branch_id = $data['branch'];
                $verificationRecord->save();
            }
            
            $startDate = $verificationRecord->start_date;
            $endDate = $verificationRecord->end_date;

            WaDebtorTran::whereBetween('trans_date', [$startDate, $endDate])
                ->whereNull('verification_record_id')
                ->where('branch_id', $verificationRecord->branch_id)
                ->update([
                    'verification_record_id' => $verificationRecord->id
                ]);

            $paymentChannels=[];
            PaymentMethod::query()
                    ->join('wa_chart_of_accounts_branches as branches','branches.wa_chart_of_account_id','payment_methods.gl_account_id')
                    ->where('branches.restaurant_id',$verificationRecord->branch_id)
                    ->select('payment_methods.title')
                    ->get()->map(function($channel) use(&$paymentChannels){
                        return $paymentChannels[]=$channel->title;
                    });

            $joinedRecords = DB::table('payment_verification_banks')
                ->select(
                    'payment_verification_banks.id as pid',
                    'wa_debtor_trans.id as did',
                    'wa_debtor_trans.reference as dref',
                    'wa_debtor_trans.amount as damt',
                )
                ->where('status', 'Pending')
                // ->whereIn('channel',$paymentChannels)
                ->join('wa_debtor_trans', function ($join) use ($endDate, $startDate, $verificationRecord) {
                    $join->on('payment_verification_banks.reference', 'like', DB::raw("CONCAT('%',wa_debtor_trans.reference,'%')"))
                        ->whereBetween('wa_debtor_trans.trans_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('payment_verification_banks.amount', '=', DB::raw('ABS(wa_debtor_trans.amount)'))
                        ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
                        ->where('wa_debtor_trans.reconciled', false)
                        // ->where('wa_debtor_trans.channel', 'KENYA COMMERCIAL BANK')
                        ->where('wa_debtor_trans.verification_status', 'pending')
                        ->where('wa_debtor_trans.branch_id', $verificationRecord->branch_id);
                })
                ->groupBy('wa_debtor_trans.reference', 'wa_debtor_trans.amount')
                ->get();

            foreach ($joinedRecords as $record) {
                DB::beginTransaction();
                try {
                    DB::table('wa_debtor_trans')->where('id', $record->did)->update([
                        'verification_status' => 'verified',
                        'verification_record_id' => $verificationRecord->id,
                    ]);

                    DB::table('payment_verification_banks')->where('id', $record->pid)->update([
                        'status' => 'Verified',
                        'payment_verification_id' => $verificationRecord->id,
                        'matched_debtors_id' => $record->did
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }

            // Re-verify 1.2
            $joinedRecords2 = DB::table('payment_verification_banks')
                ->select(
                    'payment_verification_banks.id as pid',
                    'wa_debtor_trans.id as did',
                    'wa_debtor_trans.reference as dref',
                    'wa_debtor_trans.amount as damt',
                )
                ->where('status', 'Pending')
                // ->whereIn('channel',$paymentChannels)
                ->join('wa_debtor_trans', function ($join) use ($endDate, $startDate, $verificationRecord) {
                    $join->on('wa_debtor_trans.reference', 'like', DB::raw("CONCAT('%',payment_verification_banks.reference,'%')"))
                        ->whereBetween('wa_debtor_trans.trans_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                        ->where('payment_verification_banks.amount', '=', DB::raw('ABS(wa_debtor_trans.amount)'))
                        ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
                        ->where('wa_debtor_trans.reconciled', false)
                        //                        ->where('wa_debtor_trans.channel', 'KENYA COMMERCIAL BANK')
                        ->where('wa_debtor_trans.verification_status', 'pending')
                        ->where('wa_debtor_trans.branch_id', $verificationRecord->branch_id);
                })
                ->groupBy('wa_debtor_trans.reference', 'wa_debtor_trans.amount')
                ->get();

            foreach ($joinedRecords2 as $record) {
                DB::beginTransaction();
                try {
                    DB::table('wa_debtor_trans')->where('id', $record->did)->update([
                        'verification_status' => 'verified',
                        'verification_record_id' => $verificationRecord->id,
                    ]);

                    DB::table('payment_verification_banks')->where('id', $record->pid)->update([
                        'status' => 'Verified',
                        'payment_verification_id' => $verificationRecord->id,
                        'matched_debtors_id' => $record->did
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }


            // Re-verify 2
            //            $channels = ['EQUITY BANK'];
            //            $channels = ['EQUITY MAKONGENI', 'VOOMA MAKONGENI', 'EQUITY BANK', 'KENYA COMMERCIAL BANK', 'MPESA PAYBILL'];
            //            $pendingDebtorRefs = DB::table('wa_debtor_trans')
            //                ->where('verification_status', 'pending')
            //                ->whereIn('channel', $channels)
            //                ->whereBetween('trans_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            //                ->where('document_no', 'like', 'RCT%')
            //                ->pluck('reference')
            //                ->toArray();
            //
            //            $pendingDebtorAmounts = DB::table('wa_debtor_trans')
            //                ->where('verification_status', 'pending')
            //                ->whereIn('channel', $channels)
            //                ->whereBetween('trans_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            //                ->where('document_no', 'like', 'RCT%')
            //                ->pluck('amount')
            //                ->toArray();
            //            $pendingDebtorIds = DB::table('wa_debtor_trans')
            //                ->where('verification_status', 'pending')
            //                ->whereIn('channel', $channels)
            //                ->whereBetween('trans_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            //                ->where('document_no', 'like', 'RCT%')
            //                ->pluck('id')
            //                ->toArray();
            //            $unUsedBankTrans = DB::table('payment_verification_banks')
            //                ->select('id', 'amount', 'reference', 'original_reference')
            //                ->where('status', 'Pending')
            //                ->get();
            //
            //            foreach ($pendingDebtorRefs as $index => $pendingDebtorRef) {
            //                $reference = $pendingDebtorRef;
            //                if (str_contains($reference, '/')) {
            //                    $referenceSegments = explode('/', $reference);
            //                    $reference = $referenceSegments[1];
            //
            //                    if (strlen($reference) < 5) {
            //                        $reference = $referenceSegments[0];
            //
            //                        if (strlen($reference) < 5) {
            //                            $reference = $pendingDebtorRef;
            //                        }
            //                    }
            //                }
            //
            //                $bankTrans = $unUsedBankTrans->filter(function ($bankTran) use ($reference, $pendingDebtorAmounts, $index) {
            //                    return str_contains($bankTran->original_reference, $reference) && ($bankTran->amount == abs($pendingDebtorAmounts[$index]));
            //                })->first();
            //
            //                // Log::info("========= $index Initial: $pendingDebtorRef Use: $reference amount {$pendingDebtorAmounts[$index]} Found " . json_encode($bankTrans) . " ===========");
            //
            //                DB::beginTransaction();
            //                try {
            //                    WaDebtorTran::find($pendingDebtorIds[$index])->update([
            //                        'verification_status' => 'verified',
            //                        'verification_record_id' => $verificationRecord->id,
            //                    ]);
            //
            //                    PaymentVerificationBank::find($bankTrans->id)->update([
            //                        'status' => 'Verified',
            //                        'payment_verification_id' => $verificationRecord->id,
            //                        'matched_debtors_id' => $pendingDebtorIds[$index]
            //                    ]);
            //
            //                    DB::commit();
            //                } catch (Throwable $e) {
            //                    Log::info("Record fails with " . $e->getMessage());
            //                    DB::rollBack();
            //                }
            //            }

            if ($request->reverify_form) {
                return 'success';
            }
            $request->session()->flash('success', 'Payments Fetch Complete.');
            return redirect(route('payment-reconciliation.verification.list', $verificationRecord->id));
        } catch (\Exception $e) {
            $request->session()->flash('danger', $e->getMessage());
            $request->session()->flash('steps', 2);
            return redirect()->back();
        }
    }

    public function verification()
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Payment Verification';
        $model = 'payment-verification';

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];

        $branch = request()->branch;
        $status = request()->status;

        if (request()->wantsJson()) {
            $totalMissing = "select SUM(ABS(amount)) from wa_debtor_trans WHERE verification_record_id=payment_verifications.id AND document_no like 'RCT%'  AND verification_status ='" . StatusPaymentVerification::Pending->value . "'";
            $countMissing = "select count(id) from wa_debtor_trans WHERE verification_record_id=payment_verifications.id AND document_no like 'RCT%'  AND verification_status ='" . StatusPaymentVerification::Pending->value . "'";
            $query = PaymentVerification::query()
                ->select([
                    'payment_verifications.id',
                    'payment_verifications.start_date',
                    'payment_verifications.end_date',
                    'payment_verifications.channel',
                    'restaurants.name as branch',
                    'payment_verifications.status',
                    DB::raw("(select count(id) from wa_debtor_trans WHERE verification_record_id=payment_verifications.id AND document_no like 'RCT%') as total_payments"),
                    DB::raw("(select sum(ABS(amount)) from wa_debtor_trans WHERE verification_record_id=payment_verifications.id AND document_no like 'RCT%') as total_debtors_amount"),

                    DB::raw("(select sum(ABS(amount)) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND (verification_status ='" . StatusPaymentVerification::Verified->value . "' OR verification_status ='" . StatusPaymentVerification::Approved->value . "')) as total_match"),
                    DB::raw("(select count(id) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Verified->value . "' ) as pending_approval_count"),
                    DB::raw("(select count(id) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Verifying->value . "' ) as verifying_count"),
                    DB::raw("(select count(id) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Approved->value . "' ) as approved_count"),

                    DB::raw("($totalMissing) as total_missing_system"),
                    DB::raw("($countMissing) as count_missing_system"),
                ])
                ->join('restaurants', function ($e) {
                    $e->on('restaurants.id', 'payment_verifications.branch_id');
                })
                ->when($branch, function ($query) use ($branch) {
                    $query->where('branch_id', $branch);
                })
                ->when($status == 'completed', function ($query) use ($totalMissing, $countMissing) {
                    $query->whereRaw("IFNULL(($totalMissing),0) = 0")
                        ->whereRaw("IFNULL(($countMissing),0) = 0");
                })
                ->when($status == 'pending', function ($query) use ($totalMissing, $countMissing) {
                    $query->whereRaw("($totalMissing) > 0")
                        ->whereRaw("($countMissing) > 0");
                });

            $totalMissing = "select SUM(ABS(amount)) from wa_debtor_trans where document_no like 'RCT%'  AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Pending->value . "'";
            $countMissing = "select count(id) from wa_debtor_trans where document_no like 'RCT%'  AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Pending->value . "'";
            $totals = PaymentVerification::query()
                ->select([
                    DB::raw("(select count(id) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id) as total_payments"),

                    DB::raw("(select sum(ABS(amount)) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id  AND (verification_status ='" . StatusPaymentVerification::Verified->value . "' OR verification_status ='" . StatusPaymentVerification::Approved->value . "')) as total_match"),
                    DB::raw("(select count(id) from wa_debtor_trans where  document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Verified->value . "' ) as pending_approval_count"),

                    DB::raw("($totalMissing) as total_missing_system"),
                    DB::raw("($countMissing) as count_missing_system"),
                ])
                ->when($branch, function ($query) use ($branch) {
                    $query->where('branch_id', $branch);
                })
                ->when($status == 'completed', function ($query) use ($totalMissing, $countMissing) {
                    $query->whereRaw("IFNULL(($totalMissing),0) = 0")
                        ->whereRaw("IFNULL(($countMissing),0) = 0");
                })
                ->when($status == 'pending', function ($query) use ($totalMissing, $countMissing) {
                    $query->whereRaw("($totalMissing) > 0")
                        ->whereRaw("($countMissing) > 0");
                })
                ->get();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('actions', function ($payment) {
                    return view('admin.Finance.payment_reconciliation.actions.verification', compact('payment'));
                })
                ->editColumn('total_payments', function ($payment) {
                    return number_format($payment->total_payments);
                })
                ->editColumn('pending_approval_count', function ($payment) {
                    return number_format($payment->pending_approval_count);
                })
                ->editColumn('total_match', function ($payment) {
                    return number_format($payment->pending_approval_count) . '(' . manageAmountFormat($payment->total_match) . ')';
                })
                ->editColumn('total_missing_system', function ($payment) {
                    return number_format($payment->count_missing_system) . '(' . manageAmountFormat($payment->total_missing_system) . ')';
                })
                ->with('footer_total_payments', function () use ($totals) {
                    return number_format($totals->sum('total_payments'));
                })
                ->with('footer_pending_approval_count', function () use ($totals) {
                    return number_format($totals->sum('pending_approval_count'));
                })
                ->with('footer_total_match', function () use ($totals) {
                    return number_format($totals->sum('pending_approval_count')) . '(' . manageAmountFormat($totals->sum('total_match')) . ')';
                })
                ->with('footer_total_missing_system', function () use ($totals) {
                    return number_format($totals->sum('count_missing_system')) . '(' . manageAmountFormat($totals->sum('total_missing_system')) . ')';
                })
                ->toJson();
        }

        $branches = Restaurant::all();

        return view('admin.Finance.payment_reconciliation.verification', compact('title', 'model', 'breadcum', 'branches'));
    }

    public function verificationMatchingDatatable($id)
    {
        $transactions = PaymentVerificationSystem::query()
            ->where('payment_verification_systems.payment_verification_id', $id)
            ->select('payment_verification_systems.*')
            ->with([
                'verificationRange',
                'bankVerification',
                'debtor',
                'debtor.customerDetail'
            ]);
        if (request()->type == 'verfying') {
            $transactions->where('payment_verification_systems.status', StatusPaymentVerification::Verifying->value)->get();
        }
        if (request()->type == 'verified') {
            $transactions->where('payment_verification_systems.status', StatusPaymentVerification::Verified->value)->get();
        }
        if (request()->type == 'approved') {
            $transactions->where('payment_verification_systems.status', StatusPaymentVerification::Approved->value)->get();
        }


        return DataTables::eloquent($transactions)
            ->editColumn('amount', function ($payment) {
                return manageAmountFormat($payment->amount);
            })
            ->editColumn('debtor.trans_date', function ($payment) {
                return date('Y-m-d', strtotime($payment->debtor->trans_date));
            })
            ->editColumn('debtor.channel', function ($payment) {
                if ($payment->debtor->channel) {
                    return $payment->debtor->channel;
                }
                return '-';
            })
            ->editColumn('bank_verification.bank_date', function ($payment) {
                if ($payment->bankVerification) {
                    return $payment->bankVerification->bank_date;
                }
                return '-';
            })
            ->setRowAttr([
                'class' => function ($item) {
                    if ($item->bankVerification) {
                        if (strtotime($item->bankVerification->bank_date) != strtotime($item->debtor->trans_date)) {
                            return 'bg-info';
                        } else {
                            return 'bg-light';
                        }
                    } else {
                        return 'bg-light';
                    }
                },
            ])
            ->with('total', function () use ($transactions) {
                $total_amount = $transactions->get()->sum('amount');

                return manageAmountFormat($total_amount);
            })
            ->toJson();
    }

    public function verification_create()
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $steps = 1;
        if (Session::has('steps')) {
            $steps = Session::get('steps');
        }

        $title = 'Payment Verification';
        $model = 'payment-verification';

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];

        $channels = DB::table('wa_debtor_trans')->where('channel', '!=', NULL)->select('channel')->distinct()->get()->pluck('channel');
        $banks = DB::table('payment_verification_banks')->where('status', StatusPaymentVerification::Pending->value)->count();
        return view('admin.Finance.payment_reconciliation.verification_create', [
            'title' => $title,
            'model' => $model,
            'breadcum' => $breadcum,
            'channels' => $channels,
            'steps' => $steps,
            'banks' => $banks
        ]);
    }

    public function verification_upload(Request $request)
    {
        ini_set('max_execution_time', 800);

        try {
            if (!$request->use_existing) {
                if ($request->equity_makongeni) {
                    $fileName = $request->file('equity_makongeni');
                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($fileName);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $recordsToInsert = [];

                    foreach ($data as $index => $record) {
                        if ($index != 0) {
                            $recordAmount = (float)$record[2];
                            $date = date('Y-m-d', strtotime($record[0]));
                            $refColumn = $record[1];

                            $recordsToInsert[] = [
                                'reference' => $refColumn,
                                'amount' => $recordAmount,
                                'bank_date' => $date,
                                'original_reference' => $refColumn,
                                'channel' => 'EQUITY MAKONGENI',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    $batchSize = 1000;

                    // Insert in batches                    
                    if (!empty($recordsToInsert)) {
                        foreach (array_chunk($recordsToInsert, $batchSize) as $batch) {
                            DB::table('payment_verification_banks')->insert($batch);
                        }
                    }
                }

                if ($request->equity_main) {
                    $fileName = $request->file('equity_main');
                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($fileName);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $recordsToInsert = [];

                    foreach ($data as $index => $record) {
                        if ($index != 0) {
                            $recordAmount = (float)$record[2];
                            $date = date('Y-m-d', strtotime($record[0]));
                            $refColumn = $record[1];

                            $recordsToInsert[] = [
                                'reference' => $refColumn,
                                'amount' => $recordAmount,
                                'bank_date' => $date,
                                'original_reference' => $refColumn,
                                'channel' => 'EQUITY BANK',
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    $batchSize = 1000;

                    // Insert in batches                    
                    if (!empty($recordsToInsert)) {
                        foreach (array_chunk($recordsToInsert, $batchSize) as $batch) {
                            DB::table('payment_verification_banks')->insert($batch);
                        }
                    }
                }

                if ($request->vooma) {
                    $fileName = $request->file('vooma');
                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(false);
                    $spreadsheet = $Reader->load($fileName);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $recordsToInsert = [];

                    foreach ($data as $index => $record) {
                        if ($index != 0) {
                            $recordAmount = (float)(str_replace(',', '', trim($record[2])));
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $record[0])));
                            $refColumn = $record[1];

                            $recordsToInsert[] = [
                                'reference' => $refColumn,
                                'amount' => $recordAmount,
                                'bank_date' => $date,
                                'channel' => 'VOOMA MAKONGENI',
                                'original_reference' => $refColumn,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    $batchSize = 1000;

                    // Insert in batches                    
                    if (!empty($recordsToInsert)) {
                        foreach (array_chunk($recordsToInsert, $batchSize) as $batch) {
                            DB::table('payment_verification_banks')->insert($batch);
                        }
                    }
                }

                if ($request->kcb_main) {
                    $fileName = $request->file('kcb_main');
                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(false);
                    $spreadsheet = $Reader->load($fileName);
                    $data = $spreadsheet->getActiveSheet()->toArray();

                    $recordsToInsert = [];

                    foreach ($data as $index => $record) {
                        if ($index != 0) {
                            $recordAmount = (float)(str_replace(',', '', trim($record[2])));
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $record[0])));
                            $refColumn = $record[1];

                            $recordsToInsert[] = [
                                'reference' => $refColumn,
                                'amount' => $recordAmount,
                                'bank_date' => $date,
                                'channel' => 'KENYA COMMERCIAL BANK',
                                'original_reference' => $refColumn,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                        }
                    }

                    $batchSize = 1000;

                    // Insert in batches                    
                    if (!empty($recordsToInsert)) {
                        foreach (array_chunk($recordsToInsert, $batchSize) as $batch) {
                            DB::table('payment_verification_banks')->insert($batch);
                        }
                    }
                }

                if ($request->mpesa) {
                    $fileName = $request->file('mpesa');
                    $Reader = new Xlsx();
                    $Reader->setReadDataOnly(true);
                    $spreadsheet = $Reader->load($fileName);
                    $data = $spreadsheet->getActiveSheet()->toArray();
                    foreach ($data as $index => $record) {
                        if ($index != 0) {
                            $recordAmount = (float)$record[5];
                            $lookupRef = $record[6];
                            $date = date('Y-m-d', strtotime(str_replace('-', '/', $record[0])));

                            DB::table('payment_verification_banks')->insert([
                                'reference' => $lookupRef,
                                'original_reference' => $lookupRef,
                                'amount' => $recordAmount,
                                'bank_date' => $date,
                                'channel' => 'MPESA PAYBILL',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                $request->session()->flash('success', 'Statements Uploaded Successfully.');
            }

            $request->session()->flash('steps', 2);
            return redirect()->back();
        } catch (Throwable $e) {
            $request->session()->flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function verification_store(Request $request, $id)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $data = $request->all();
        $response = $this->bankReconRepository->verifyPaymentReconciliations($id, $data);
        // Add Notifications Here
        if ($response->status() == 200) {
            $request->session()->flash('success', $response->content());
            return redirect(route('payment-reconciliation.verification'));
        } else {
            $request->session()->flash('danger', $response->content());
            return redirect()->back();
        }
    }

    public function verification_list($verificaction)
    {
        ini_set('max_execution_time', 600);

        $title = 'Payment VerificationSummary';
        $model = 'payment-verification';

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];

        $verificationRecord = PaymentVerification::select(
            'payment_verifications.id',
            'payment_verifications.start_date',
            'payment_verifications.end_date',
            'restaurants.id as branch_id',
            'restaurants.name as branch_name',
            DB::raw("(select count(*) from wa_debtor_trans where verification_record_id=payment_verifications.id and document_no like 'RCT%') as total_debtors_count"),
            DB::raw("(select sum(ABS(amount)) from wa_debtor_trans where verification_record_id=payment_verifications.id and document_no like 'RCT%') as total_debtors_amount"),
        )
            ->join('restaurants', 'payment_verifications.branch_id', '=', 'restaurants.id')
            ->find($verificaction);

        $matchingTransactionsQuery = DB::table('wa_debtor_trans')
            ->select(
                'wa_debtor_trans.id as debtors_id',
                'wa_debtor_trans.reference as debtors_ref',
                'document_no',
                'wa_debtor_trans.channel',
                'trans_date',
                'customer_name',
                'payment_verification_banks.reference as bank_ref',
                'payment_verification_banks.bank_date',
                DB::raw("(ABS(wa_debtor_trans.amount)) as amount")
            )
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->join('payment_verification_banks', 'wa_debtor_trans.id', '=', 'payment_verification_banks.matched_debtors_id')
            ->where('verification_record_id', $verificationRecord->id)
            ->where('verification_status', 'verified');

        $matchingTransactionsCount = $matchingTransactionsQuery->clone()->count();
        $matchingTransactionsTotal = abs($matchingTransactionsQuery->clone()->sum('wa_debtor_trans.amount'));
        $matchingTransactions = $matchingTransactionsQuery->clone()
            ->orderBy('trans_date')
            ->cursorPaginate(100);


        $missingInBankQuery = DB::table('wa_debtor_trans')
            ->select(
                'wa_debtor_trans.id as debtors_id',
                'reference',
                'document_no',
                'channel',
                'trans_date',
                'customer_name',
                DB::raw("(ABS(amount)) as amount")
            )
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->where('verification_record_id', $verificationRecord->id)
            // ->whereBetween('trans_date', [$verificationRecord->start_date . ' 00:00:00', $verificationRecord->end_date . ' 23:59:59'])
            ->where('verification_status', 'pending')
            ->where('document_no', 'like', 'RCT%');

        $missingInBankCount = $missingInBankQuery->clone()->count();
        $missingInBankTotal = abs($missingInBankQuery->clone()->sum('amount'));
        $missingInBank = $missingInBankQuery->clone()
            ->orderBy('trans_date')
            ->cursorPaginate(5000);

        $unknownBankingsQuery = DB::table('payment_verification_banks')
            ->select(
                'id',
                'reference',
                'original_reference',
                'amount',
                'bank_date',
            )
            ->where('status', 'Pending');

        $unknownBankingsCount = $unknownBankingsQuery->clone()->count();
        $unknownBankingsTotal = $unknownBankingsQuery->clone()->sum('amount');
        $unknownBankings = $unknownBankingsQuery->clone()
            ->orderBy('bank_date')
            ->cursorPaginate(100);

        $logged_user_info = getLoggeduserProfile();
        $channels = DB::table('wa_debtor_trans')->where('channel', '!=', NULL)->select('channel')->distinct()->get()->pluck('channel');

        return view('admin.Finance.payment_reconciliation.verification_list', [
            'title' => $title,
            'model' => $model,
            'breadcum' => $breadcum,
            'verification_record' => $verificationRecord,
            'matching_transactions' => $matchingTransactions,
            'matching_transactions_count' => $matchingTransactionsCount,
            'matching_transactions_total' => $matchingTransactionsTotal,
            'missing_in_bank' => $missingInBank,
            'missing_in_bank_total' => $missingInBankTotal,
            'missing_in_bank_count' => $missingInBankCount,
            'unknown_bankings' => $unknownBankings,
            'unknown_bankings_count' => $unknownBankingsCount,
            'unknown_bankings_total' => $unknownBankingsTotal,
            'logged_user_info' => $logged_user_info,
            'channels' => $channels
        ]);

        //

        // $payment = $this->bankReconRepository->getSinglePaymentVerification($verificaction);

        // if (request()->filled('download')) {
        //     if (request()->type == "matching-transactions") {
        //         $systemData = $this->bankReconRepository->getSinglePaymentVerificationSystem($verificaction);
        //         $allMatchData = $systemData['allMatchData'];
        //         $pdf = \PDF::loadView('admin.Finance.payment_reconciliation.pdf.matching_transactions', compact('payment', 'allMatchData'))
        //             ->setPaper('a4', 'landscape');
        //         $report_name = 'matching-transactions-' . $payment->start_date . '-' . $payment->end_date;
        //         return $pdf->download($report_name . '.pdf');
        //     }
        //     if (request()->type == "missing-in-system-transactions") {
        //         $systemData = $this->bankReconRepository->getSinglePaymentVerificationSystem($verificaction);
        //         $allMissingSystemData = $systemData['allMissingSystemData'];
        //         $pdf = \PDF::loadView('admin.Finance.payment_reconciliation.pdf.missing_in_system_transactions', compact('payment', 'allMissingSystemData'))
        //             ->setPaper('a4', 'landscape');
        //         $report_name = 'missing-in-system-transactions-' . $payment->start_date . '-' . $payment->end_date;
        //         return $pdf->download($report_name . '.pdf');
        //     }
        //     if (request()->type == "missing-in-bank-transactions") {
        //         // $bankData = $this->bankReconRepository->getSinglePaymentVerificationBank($verificaction);
        //         $allMissingBankData = PaymentVerificationBank::where('status', StatusPaymentVerification::Pending->value)->get(); //$bankData['allMissingBankData'];
        //         $pdf = \PDF::loadView('admin.Finance.payment_reconciliation.pdf.missing_in_bank_transactions', compact('payment', 'allMissingBankData'))
        //             ->setPaper('a4', 'landscape');
        //         $report_name = 'missing-in-bank-transactions-' . $payment->start_date . '-' . $payment->end_date;
        //         return $pdf->download($report_name . '.pdf');
        //     }
        // }


        // $bankData = $this->bankReconRepository->getSinglePaymentVerificationBank($verificaction);
        // $allMissingBankData = PaymentVerificationBank::where('status', StatusPaymentVerification::Pending->value)->get();
        // $systemData = $this->bankReconRepository->getSinglePaymentVerificationSystem($verificaction);
        // $logged_user_info = getLoggeduserProfile();
        // $channels = DB::table('wa_debtor_trans')->where('channel', '!=', NULL)->select('channel')->distinct()->get()->pluck('channel');

        // return view('admin.Finance.payment_reconciliation.verification_list', [
        //     'title' => $title,
        //     'model' => $model,
        //     'breadcum' => $breadcum,
        //     'payment' => $payment,
        //     'allSystemData' => $systemData['allSystemData'],
        //     'allBankData' => $bankData['allBankData'],
        //     'allMatchData' => $systemData['allMatchData'],
        //     'allMissingSystemData' => $systemData['allMissingSystemData'],
        //     'allMissingBankData' => $allMissingBankData,
        //     'logged_user_info' => $logged_user_info,
        //     'channels' => $channels
        // ]);
    }

    public function verification_update(Request $request, $type, $id)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $data = $request->all();
        $data['id'] = $id;
        $data['type'] = $type;

        $response = $this->bankReconRepository->updateTransactionAndVerify($data);
        if ($response->status() == 200) {
            $request->session()->flash('success', $response->content());
            return redirect(route('payment-reconciliation.verification.list', $request->verification));
        } else {
            $request->session()->flash('danger', $response->content());
            return redirect()->back();
        }
    }

    public function verification_revert(Request $request, $verification, $id)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            DB::table('payment_verification_systems')
                ->where('id', $id)
                ->update(['status' => StatusPaymentVerification::Verifying->value]);
            DB::table('payment_verification_banks')
                ->where([['payment_verification_id', $verification], ['verification_system_id', $id]])
                ->update([
                    'status' => StatusPaymentVerification::Verifying->value
                ]);

            DB::commit();
            request()->session()->flash('success', 'Transaction Verification Reverted Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            request()->session()->flash('danger', 'Transaction Verification Not Reverted.');
        }

        return redirect()->route('payment-reconciliation.verification.list', $verification);
    }

    public function verification_discard_date_range(Request $request, $verification)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            DB::table('payment_verifications')
                ->where('id', $verification)
                ->delete();
            DB::table('payment_verification_systems')
                ->where('payment_verification_id', $verification)
                ->delete();
            DB::table('payment_verification_banks')
                ->where('payment_verification_id', $verification)
                ->update([
                    'status' => StatusPaymentVerification::Pending->value,
                    'payment_verification_id' => NULL
                ]);

            DB::commit();
            request()->session()->flash('success', 'Payment Verification Range Discarded Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            request()->session()->flash('danger', 'Payment Verification Range Not Discarded.');
        }

        return redirect()->route('payment-reconciliation.verification');
    }

    public function verification_discard(Request $request, $verification, $id)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            DB::table('payment_verification_systems')
                ->where('id', $id)
                ->delete();

            DB::commit();
            request()->session()->flash('success', 'Transaction Discarded Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            request()->session()->flash('danger', 'Transaction Not Discarded.');
        }

        return redirect()->route('payment-reconciliation.verification.list', $verification);
    }

    public function verification_edit_reference(Request $request, $id)
    {
        if (!can('edit-debtor-reference', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $debtor = WaDebtorTran::find($id);
            if ($request->reference) {
                $debtor->reference = $request->reference;
            }
            // if ($request->channel) {
            //     $debtor->channel = $request->channel;
            // }
            $debtor->save();

            DB::commit();
            request()->session()->flash('success', 'Transaction Reference Updated Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            request()->session()->flash('danger', 'Transaction Reference Not Updated.');
        }
        return redirect()->back();
    }

    public function verification_suspend(Request $request, $verification, $id)
    {
        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $verify = PaymentVerificationSystem::find($id);

        $debtors = DB::table('wa_debtor_trans')
            ->where('wa_debtor_trans.id', $verify->debtor->id)
            ->join('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
            ->select(
                'wa_debtor_trans.wa_customer_id',
                'wa_debtor_trans.trans_date',
                'wa_debtor_trans.input_date',
                'wa_customers.route_id'
            )
            ->first();

        WaDebtorTran::find($verify->debtor_id)->delete();

        SuspendedTransaction::create([
            'wa_customer_id' => $debtors->wa_customer_id,
            'suspended_by' => Auth::user()->id,
            'document_no' => $verify->document_no,
            'reference' => $verify->reference,
            'amount' => $verify->amount,
            'trans_date' => $debtors->trans_date,
            'input_date' => $debtors->input_date,
            'route' => $debtors->route_id,
            'reason' => $request->reason,
        ]);

        UserLog::create([
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
            'module' => 'reconciliation',
            'activity' => "Suspended transaction $verify->reference",
            'entity_id' => Auth::user()->id,
            'user_agent' => 'Bizwiz WEB',
        ]);

        DB::commit();
        Session::flash('success', 'Transactions suspended successfully');
        return redirect()->route('payment-reconciliation.verification.list', $verification);
    }

    public function approval()
    {
        if (!can('approval', 'reconciliation')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $title = 'Payment Approval';
        $model = 'payment-approval';

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];

        if (request()->wantsJson()) {
            $totalCount = "SELECT COUNT(id) FROM wa_debtor_trans WHERE document_no LIKE 'RCT%' AND verification_record_id = payment_verifications.id";
            $totalPayments = "SELECT SUM(ABS(amount)) from wa_debtor_trans where document_no LIKE 'RCT%' AND verification_record_id=payment_verifications.id";
            $pendingApprovalCount = "select count(id) from wa_debtor_trans where document_no like 'RCT%' AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Verified->value . "'";
            $pendingApprovalPayments = "select sum(ABS(amount)) from wa_debtor_trans where document_no like 'RCT%'  AND verification_record_id=payment_verifications.id AND verification_status ='" . StatusPaymentVerification::Verified->value . "'";

            $query = PaymentVerification::query()
                ->select([
                    'payment_verifications.*',
                    'restaurants.name as branch_name',
                    DB::raw("($totalCount) as total_count"),
                    DB::raw("($totalPayments) as total_payments"),
                    DB::raw("($pendingApprovalCount) as pending_approval_count"),
                    DB::raw("($pendingApprovalPayments) as pending_approval_payments"),
                ])
                ->join('restaurants', 'restaurants.id', 'payment_verifications.branch_id')
                ->when(request()->branch, function ($query) {
                    return $query->where('branch_id', request()->branch);
                })
                ->whereRaw("($pendingApprovalCount) > 0")
                ->where('payment_verifications.status', StatusPaymentVerification::Verifying->value);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('total_payments', function ($payment) {
                    return number_format($payment->total_count) . '(' . manageAmountFormat($payment->total_payments) . ')';
                })
                ->editColumn('pending_approval_count', function ($payment) {
                    return number_format($payment->pending_approval_count) . '(' . manageAmountFormat($payment->pending_approval_payments) . ')';
                })
                ->editColumn('variance', function ($payment) {
                    return number_format($payment->total_count - $payment->pending_approval_count) . '(' . manageAmountFormat($payment->total_payments - $payment->pending_approval_payments) . ')';
                })
                ->addColumn('actions', function ($payment) {
                    return view('admin.Finance.payment_reconciliation.actions.approval', compact('payment'));
                })
                ->toJson();
        }

        $branches = Restaurant::all();

        return view('admin.Finance.payment_reconciliation.approval', compact('title', 'model', 'breadcum', 'branches'));
    }

    public function approval_store(Request $request)
    {
        if (!can('verification', 'reconciliation')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        dispatch(new ApproveReconciliationPayments($data));

        $verification = PaymentVerification::find($data['verification']);
        $verification->update([
            'status' => StatusPaymentVerification::Processing->value
        ]);

        return response()->json([
            'success' => 'true'
        ]);
    }

    public function verification_all()
    {
        ini_set('max_execution_time', 600);

        if (!can('verification', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        if (request()->input('debtors')) {
            $verifications = PaymentVerification::get();
            foreach ($verifications as $key => $verification) {
                // Get Debtors from the filter provided.
                $debtorTrans = WaDebtorTran::query()
                    ->where([
                        ['reconciled', false], ['branch_id', request()->branch]
                        // ,['channel', request()->channel]
                    ])
                    ->whereBetween('trans_date', [$verification->start_date, $verification->end_date])
                    ->where('document_no', 'like', 'RCT%')
                    ->whereRaw("LENGTH(reference) > 5")
                    ->doesntHave('systemVerification')
                    ->get(['id', 'amount', 'document_no', 'reference'])
                    ->map(function ($record) {
                        $record->amount = abs($record->amount);
                        return $record;
                    });

                if ($debtorTrans->count()) {
                    // Prepare data for bulk insert
                    $insertData = $debtorTrans->map(function ($value) use ($verification) {
                        return [
                            'payment_verification_id' => $verification->id,
                            'debtor_id' => $value->id,
                            'amount' => $value->amount,
                            'document_no' => $value->document_no,
                            'reference' => $value->reference,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->toArray();

                    // Perform bulk insert
                    $batchSize = 1000;

                    // Insert in batches
                    foreach (array_chunk($insertData, $batchSize) as $batch) {
                        DB::table('payment_verification_systems')->insert($batch);
                    }
                }
            }
        }


        if (request()->input('zerobank')) {
            DB::table('payment_verification_banks')
                ->where('status', StatusPaymentVerification::Pending->value)
                ->where('reference', 'like', 'Transfer%')
                ->where('amount', 0)
                ->delete();
        }

        if (request()->input('samereference')) {
            $sameData = DB::table('payment_verification_banks')
                ->where('status', StatusPaymentVerification::SameReference->value)
                ->get();

            foreach ($sameData as $key => $value) {
                $data = DB::table('payment_verification_banks')
                    ->where('reference', $value->reference)
                    ->get();

                if ($data->count() == 1) {
                    DB::table('payment_verification_systems')
                        ->where('id', $value->id)
                        ->update(['status' => StatusPaymentVerification::Pending->value]);
                }
            }
        }

        try {
            $systemData = DB::table('payment_verification_systems')
                ->where('status', StatusPaymentVerification::Pending->value)
                ->whereRaw("LENGTH(reference) > 5")
                ->orderBy('created_at', 'desc')
                ->get();

            $bankData = DB::table('payment_verification_banks')
                ->where('status', StatusPaymentVerification::Pending->value)
                ->whereRaw('amount > 0')
                ->whereRaw("LENGTH(reference) > 5")
                ->orderBy('bank_date', 'desc')
                ->get();

            // Iterate over system data
            foreach ($systemData as $value) {
                // Extract reference and amount
                if (str_contains($value->reference, '/')) {
                    $referenceParts = explode('/', $value->reference);
                    if (count($referenceParts) == 2) {
                        $lookupRef = end($referenceParts);
                        $amount = (float)$value->amount;

                        // Filter bank data based on reference and amount. Checking even if short code matches
                        $matchingBanks = $bankData->filter(function ($bank) use ($lookupRef, $amount) {
                            return strpos(trim($bank->reference), trim($lookupRef)) !== false &&
                                (float)abs($bank->amount) === $amount;
                        });

                        // If there's at least one matching bank record
                        if ($matchingBanks->isNotEmpty()) {
                            if ($matchingBanks->count() == 1) {
                                // Update statuses for both systems and banks
                                DB::table('payment_verification_systems')
                                    ->where('id', $value->id)
                                    ->update(['status' => StatusPaymentVerification::Verified->value]);
                                $matchingBank = $matchingBanks->first();
                                DB::table('payment_verification_banks')
                                    ->where('id', $matchingBank->id)
                                    ->update([
                                        'status' => StatusPaymentVerification::Verified->value,
                                        'verification_system_id' => $value->id
                                    ]);
                                // Remove the matching bank from the collection to avoid reprocessing
                                $bankData = $bankData->reject(function ($bank) use ($matchingBank) {
                                    return $bank->id === $matchingBank->id;
                                });
                            }
                        }
                    }
                } else {
                    $lookupRef = trim($value->reference);
                    $amount = (float)$value->amount;

                    // Filter bank data based on reference and amount. Checking even if short code matches
                    $matchingBanks = $bankData->filter(function ($bank) use ($lookupRef, $amount) {
                        return strpos(trim($bank->reference), trim($lookupRef)) !== false;
                    });

                    // If there's at least one matching bank record
                    if ($matchingBanks->isNotEmpty()) {
                        if ($matchingBanks->count() == 1) {
                            $matchingBank = $matchingBanks->first();
                            if ((float)abs($matchingBank->amount) === $amount) {
                                // Update statuses for both systems and banks
                                DB::table('payment_verification_systems')
                                    ->where('id', $value->id)
                                    ->update(['status' => StatusPaymentVerification::Verified->value]);

                                DB::table('payment_verification_banks')
                                    ->where('id', $matchingBank->id)
                                    ->update([
                                        'status' => StatusPaymentVerification::Verified->value,
                                        'verification_system_id' => $value->id
                                    ]);

                                // Remove the matching bank from the collection to avoid reprocessing
                                $bankData = $bankData->reject(function ($bank) use ($matchingBank) {
                                    return $bank->id === $matchingBank->id;
                                });
                            }
                        } else {
                            foreach ($matchingBanks as $bank) {
                                if ((float)abs($bank->amount) === $amount) {
                                    DB::table('payment_verification_systems')
                                        ->where('id', $value->id)
                                        ->update(['status' => StatusPaymentVerification::Verified->value]);

                                    DB::table('payment_verification_banks')
                                        ->where('id', $bank->id)
                                        ->update([
                                            'status' => StatusPaymentVerification::Verified->value,
                                            'verification_system_id' => $value->id
                                        ]);

                                    // Remove the matching bank from the collection to avoid reprocessing
                                    $bankData = $bankData->reject(function ($bank2) use ($bank) {
                                        return $bank2->id === $bank->id;
                                    });
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            request()->session()->flash('success', 'Re-Verification Success.');
        } catch (\Throwable $e) {
            request()->session()->flash('danger', 'Re-Verification Not Completed.');
        }

        return redirect()->back();
    }

    public function approval_excel($id)
    {
        $transactions = DB::table('wa_debtor_trans')
            ->select(
                'wa_debtor_trans.trans_date',
                'wa_customers.customer_name',
                'restaurants.name as branch',
                'wa_debtor_trans.channel',
                'wa_debtor_trans.reference',
                'wa_debtor_trans.document_no',
                DB::raw('ABS(wa_debtor_trans.amount) as amount'),
            )
            ->join('restaurants', 'restaurants.id', 'wa_debtor_trans.branch_id')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
            ->where('wa_debtor_trans.verification_status', 'verified')
            ->where('verification_record_id', $id)
            ->get();

        return ExcelDownloadService::download('approval-transactions', $transactions, ['TRANS DATE', 'ROUTE', 'BRANCH', 'CHANNEL', 'REFERENCE', 'DOC NO', 'AMOUNT']);
    }

    public function bank_post_logs()
    {
        if (!can('bank_post_log', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Bank Post Logs';
        $model = 'bank_post_log';

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];

        $query = DB::table('gl_posting_logs')
            ->select([
                'gl_posting_logs.id',
                'gl_posting_logs.created_at',
                'gl_posting_logs.transaction_no',
                'gl_posting_logs.payment_verification_id',
                DB::raw("COUNT(gl_posting_logs.id) AS count"),
                DB::raw("SUM(gl_posting_logs.amount) AS total"),
                'users.name'
                // DB::raw("(SELECT SUM(ABS(amount)) FROM wa_debtor_trans WHERE (trans_date BETWEEN CONCAT(payment_verifications.start_date, ' 00:00:00') AND CONCAT(payment_verifications.end_date, ' 23:59:59')) AND document_no LIKE 'RCT%') AS total_payments"),
                // DB::raw("(SELECT COUNT(id) FROM wa_debtor_trans WHERE (trans_date BETWEEN CONCAT(payment_verifications.start_date, ' 00:00:00') AND CONCAT(payment_verifications.end_date, ' 23:59:59')) AND document_no LIKE 'RCT%' AND verification_record_id = payment_verifications.id AND verification_status = '" . StatusPaymentVerification::Verified->value . "') AS pending_approval_count"),
                // DB::raw("(SELECT SUM(ABS(amount)) FROM wa_debtor_trans WHERE (trans_date BETWEEN CONCAT(payment_verifications.start_date, ' 00:00:00') AND CONCAT(payment_verifications.end_date, ' 23:59:59')) AND document_no LIKE 'RCT%' AND verification_record_id = payment_verifications.id AND verification_status = '" . StatusPaymentVerification::Verified->value . "') AS pending_approval_payments"),
            ])
            ->join('users', 'users.id', 'gl_posting_logs.created_by')
            ->orderBy('gl_posting_logs.created_at', 'desc')
            ->groupBy('gl_posting_logs.transaction_no');

        if (request()->start_date && request()->end_date) {
            $query->whereBetween('gl_posting_logs.created_at', [request()->start_date . ' 00:00:00', request()->end_date . ' 23:59:59']);
        }

        if (request()->wantsJson()) {

            // GlPostingLogs::with('createdBy')->groupBy('transaction_no');
            return DataTables::of($query)
                // ->editColumn('matchedDebtor.reference', function ($payment) {
                //     if($payment->matchedDebtor){
                //         return $payment->matchedDebtor->reference;
                //     }
                //     return '-';
                // })
                ->addColumn('approved', function ($approve) {
                    return number_format($approve->count) . '(' . manageAmountFormat($approve->total) . ')';
                })
                ->addColumn('debit', function ($approve) {
                    return manageAmountFormat($approve->total);
                })
                ->addColumn('credit', function ($approve) {
                    return '-' . manageAmountFormat($approve->total);
                })
                ->toJson();
        }
        return view('admin.Finance.payment_reconciliation.bank_posting_log', compact('title', 'model', 'breadcum'));
    }

    public function bank_post_logs_excel($id)
    {
        $transactions = DB::table('wa_debtor_trans')
            ->select(
                'gl_posting_logs.transaction_no',
                'gl_posting_logs.created_at',
                'wa_debtor_trans.trans_date',
                'wa_customers.customer_name',
                'restaurants.name as branch',
                'wa_debtor_trans.channel',
                'wa_debtor_trans.reference',
                'wa_debtor_trans.document_no',
                DB::raw('ABS(wa_debtor_trans.amount) as amount'),
            )
            ->join('restaurants', 'restaurants.id', 'wa_debtor_trans.branch_id')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->join('gl_posting_logs', 'gl_posting_logs.wa_debtor_trans_id', 'wa_debtor_trans.id')
            ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
            // ->where('wa_debtor_trans.verification_status','verified')
            ->where('gl_posting_logs.transaction_no', $id)
            ->get();
        // dd($transactions);
        return ExcelDownloadService::download('BANK-POST-TRANSCTIONS', $transactions, ['BANK POST CODE', 'BANK POST DATE', 'TRANS DATE', 'ROUTE', 'BRANCH', 'CHANNEL', 'REFERENCE', 'DOC NO', 'AMOUNT']);
    }
}
