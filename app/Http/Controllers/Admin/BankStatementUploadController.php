<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentChannel;
use App\Enums\Status\PaymentVerification;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\PaymentVerificationBank;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\Finance\BankStatementsExport;
use App\Model\PaymentMethod;
use App\Model\Role;
use App\Model\UserPermission;
use App\Model\WaDebtorTran;
use App\Models\BankStatementBankError;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Services\ExcelDownloadService;
use App\Model\WaInternalRequisition;
use App\Model\WaAccountingPeriod;
use App\Model\WaNumerSeriesCode;
use App\Model\WaBankAccount;
use App\WaTenderEntry;
use App\Model\WaCustomer;
use App\Models\MpesaIpnNotification;



class BankStatementUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ini_set('max_execution_time', 600);

        if (!can('bank-statement-upload', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Bank Statements';
        $model = 'bank-statement-upload';
        $permission = $this->mypermissionsforAModule();

        $breadcum = [
            'Reconciliation' => '',
            $title => ''
        ];
        $query = PaymentVerificationBank::query()
            ->select(
                'payment_verification_banks.id',
                'payment_verification_banks.bank_date',
                'payment_verification_banks.amount',
                'payment_verification_banks.reference',
                'payment_verification_banks.channel',
                'payment_verification_banks.type',
                'payment_verification_banks.status',
                'payment_verification_banks.trans_ref',
                'wa_debtor_trans.reference as debtor_reference',
                'wa_debtor_trans.document_no as debtor_document',
                'wa_debtor_trans.verification_record_id',
                'wa_debtor_trans.verification_status'
            )
            ->with('debtors')
            ->leftJoin('wa_debtor_trans', 'payment_verification_banks.matched_debtors_id', '=', 'wa_debtor_trans.id')
            ->where('payment_verification_banks.amount', '!=', 0)
            ->where('payment_verification_banks.status', '!=', 'Bank Error');
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('payment_verification_banks.bank_date', [request()->start_date . ' 00:00:00', request()->end_date . ' 23:59:59']);
        }

        if (request()->filled('status')) {
            if (request()->status == 'Matched') {
                $query->whereNotNull('wa_debtor_trans.reference');
            } elseif (request()->status == 'Not Matched') {
                $query->whereNull('wa_debtor_trans.reference');
            } elseif (request()->status == 'Duplicate') {
                $query->whereIn(
                    DB::raw('(payment_verification_banks.amount, payment_verification_banks.reference)'),
                    function ($query) {
                        $query->select('amount', 'reference')
                            ->from('payment_verification_banks')
                            ->groupBy('amount', 'reference')
                            ->havingRaw('COUNT(*) > 1');
                    }
                );
                $query->orderBy('reference', 'desc');
            }
        }
        // $channels = [PaymentChannel::Mpesa->value, PaymentChannel::Eazzy->value, PaymentChannel::Vooma->value];
        $channels = [];
        $channelQuery = PaymentMethod::where([
            ['use_for_receipts', 1],
            ['use_as_channel', 1]
        ])
            ->select('title');

        if (isset($permission['reconciliation___bank-statement-main-account']) || $permission == 'superadmin') {
            // $channels[] = PaymentChannel::KCB->value;
            // $channels[] = PaymentChannel::Equity->value;

        } else {
            $channelQuery->where('payment_methods.title', '!=', PaymentChannel::KCB->value)
                ->where('payment_methods.title', '!=', PaymentChannel::Equity->value)
                ->join('wa_chart_of_accounts_branches as branches', 'branches.wa_chart_of_account_id', 'payment_methods.gl_account_id')
                ->where('branches.restaurant_id', Auth::user()->restaurant_id)
                ->select('payment_methods.title');
            $query->whereNot('payment_verification_banks.type', 'debit');
        }

        $channelQuery->get()->map(function ($channel) use (&$channels) {
            return $channels[] = $channel->title;
        });

        $query->whereIn('payment_verification_banks.channel', $channels);

        if (request()->filled('channel')) {
            $query->where('payment_verification_banks.channel', request()->channel);
        }

        $sum = $query->clone()->sum('payment_verification_banks.amount');

        if (request()->wantsJson()) {
            $totalDebits = manageAmountFormat(abs($query->clone()->where('payment_verification_banks.type', 'debit')->sum('payment_verification_banks.amount')));
            $totalCredits = manageAmountFormat(abs($query->clone()->where('payment_verification_banks.type', 'credit')->sum('payment_verification_banks.amount')));

            return DataTables::eloquent($query)
                ->editColumn('debtor_reference', function ($payment) {
                    if ($payment->debtor_reference) {
                        return $payment->debtor_reference;
                    }
                    if ($payment->stockDebtor) {
                        $payment->stockDebtor;
                    }
                    return '--';
                })
                ->addColumn('debit', function ($query) use (&$runningDebitTotal) {
                    $amount = 0;
                    if ($query->type == 'debit') {
                        $amount = $query->amount;
                        $runningDebitTotal += $amount;
                    }
                    return manageAmountFormat($amount);
                })
                ->addColumn('credit', function ($query) use (&$runningCreditTotal) {
                    $amount = 0;
                    if ($query->type == 'credit') {
                        $amount = $query->amount;
                        $runningCreditTotal += $amount;
                    }
                    return manageAmountFormat($amount);
                })
                ->addColumn('date_difference', function ($query) {
                    return today()->diffInDays($query->bank_date);
                })
                ->with('total_debits', function () use ($totalDebits) {
                    return $totalDebits;
                })
                ->with('total_credits', function () use ($totalCredits) {
                    return $totalCredits;
                })
                ->toJson();
        }

        if (request()->filled('print')) {
            if (request()->print == 'pdf') {
                $banks = $query->get();
                $pdf = \PDF::loadView('admin.Finance.bank_statement.pdf', compact('banks'));
                $report_name = 'debtor-transactions' . date('Y_m_d_H_i_A');
                return $pdf->download($report_name . '.pdf');
            }
            if (request()->print == 'excel') {
                $customerPayments = $query->get()->map(function ($bank) {
                    return [
                        'trans_date' => Carbon::parse($bank->bank_date)->format('Y-m-d'),
                        'amount' => number_format(abs($bank->amount)),
                        'channel' => $bank->channel,
                        'reference' => $bank->reference,
                        'status' => $bank->status,
                    ];
                });

                $export = new BankStatementsExport(collect($customerPayments));
                return Excel::download($export, 'Bank Statements.xlsx');
            }
        }

        $routes =  DB::table('wa_customers')
            ->select('wa_customers.customer_name', 'wa_customers.customer_code', 'wa_customers.id')
            ->join('routes', 'routes.id', 'wa_customers.route_id');

        if (!Auth::user()->is_hq_user) {
            $routes->where('routes.restaurant_id', Auth::user()->restaurant_id);
        }

        $routes = $routes->get()
            ->map(function ($route) {
                return [
                    'id' => $route->id,
                    'name' => $route->customer_name . ' ( ' . $route->customer_code . ' )'
                ];
            });
        $allocateStatusRoles = UserPermission::where('module_action', 'bank-statement-allocate')->pluck('role_id');
        $allocateStatusCount = UserPermission::where('module_action', 'bank-statement-allocate-allow')->whereIn('role_id', $allocateStatusRoles)->count();

        return view('admin.Finance.bank_statement.list', [
            'title' => $title,
            'model' => $model,
            'breadcum' => $breadcum,
            'channels' => $channels,
            'routes' => $routes,
            'permission' =>  $permission,
            'allocateStatusRoles' => $allocateStatusRoles,
            'allocateStatusCount' => $allocateStatusCount,
            'users' => collect(),
            'total' => $sum
        ]);
    }

    public function edit_channel(Request $request)
    {
        DB::beginTransaction();
        try {
            $bank = PaymentVerificationBank::find($request->bankId);
            $bank->channel = $request->channel;
            $bank->save();

            DB::commit();
            $request->session()->flash('success', 'Bank Statement Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $request->session()->flash('danger', $e->getMessage());
        }
        return redirect()->back();
    }

    public function top_up()
    {
        if (!can('bank-statement-topup', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Topup Bank Statements';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];

        $processingUpload = false;
        $channels = PaymentMethod::where([
            ['use_for_receipts', 1],
            ['use_as_channel', 1]
        ])->get();
        return view('admin.Finance.bank_statement.top_up', compact('title', 'model', 'breadcum', 'processingUpload', 'channels'));
    }

    public function upload(Request $request)
    {
        if (!can('bank-statement-topup', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Topup Bank Statements';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];
        try {

            $channel = strtolower($request->channel);

            $reader = new Xlsx();
            if (str_contains($channel, 'vooma') || str_contains($channel, 'commercial') || str_contains($channel, 'kcb')) {
                $reader->setReadDataOnly(true);
            } else {
                $reader->setReadDataOnly(true);
            }

            $fileName = $request->file('topup_list');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $statements = [];
            $statementsUnique = [];
            $statementsDuplicate = [];

            $debitTotal = 0;
            $creditTotal = 0;

            $allBanks = DB::table('payment_verification_banks')
                // ->where('channel', $request->channel)
                ->get()
                ->map(function ($record) {
                    $record->amount = abs($record->amount); // Convert amount to absolute value
                    return $record;
                });
            $channelHeader = $this->checkUploadHeaders($data[0], $channel);

            /**
             * Bypassing this temporarily @isaacmutie
             */
            // if (!$channelHeader) {
            //     Session::flash('warning', 'Choose Correct Channel For the Statement!!');
            //     return redirect()->back();
            // }

            foreach ($data as $index => $record) {
                if ($index == 0) continue;

                // Extract data based on the channel
                $recordAmount = 0;
                $ref2Column = '';

                if (str_contains($channel, 'vooma') || str_contains($channel, 'commercial') || str_contains($channel, 'kcb')) {
                    if (!empty($record[3]) && $record[3] != 0) {
                        $amountStatus = 'debit';
                        $recordAmount = (float)str_replace(',', '', trim($record[3]));
                        $recordAmount = abs($recordAmount);
                        $debitTotal += $recordAmount;
                    } else {
                        $amountStatus = 'credit';
                        $recordAmount = (float)str_replace(',', '', trim($record[4]));
                        $creditTotal += $recordAmount;
                    }

                    $refColumn = $record[1];
                    try {
                        $date = Carbon::parse(Date::excelToDateTimeObject($record[2]))->format('Y-m-d');
                    } catch (\Throwable $th) {
                        $date = Carbon::createFromFormat('m/d/Y', $record[2])->format('Y-m-d');
                    }
                } elseif (str_contains($channel, 'equity')) {

                    if (!empty($record[4]) && $record[4] != 0) {
                        $amountStatus = 'debit';
                        $recordAmount = (float)str_replace(',', '', trim($record[4]));
                        $recordAmount = abs($recordAmount);
                        $debitTotal += $recordAmount;
                    } else {
                        $amountStatus = 'credit';
                        $recordAmount = (float)str_replace(',', '', trim($record[5]));
                        $creditTotal += $recordAmount;
                    }

                    $refColumn = $record[2];
                    if ($record[7]) {
                        $refColumn = "$refColumn / $record[7]";
                    }

                    if ($record[8]) {
                        $refColumn = "$refColumn / $record[8]";
                    }

                    $ref2Column = $record[3];

                    try {
                        if (is_numeric($record[1])) {
                            // Handle as Excel date serial number
                            $date = Carbon::parse(Date::excelToDateTimeObject($record[1]))->format('Y-m-d');
                        } elseif (is_string($record[1])) {
                            // Handle as a string date in dd-mm-yyyy format
                            $date = Carbon::createFromFormat('d-m-Y', $record[1])->format('Y-m-d');
                        }

                        // $date = Carbon::parse(Date::excelToDateTimeObject($record[0]))->format('Y-m-d');
                    } catch (\Throwable $th) {
                        $date = Carbon::createFromFormat('m/d/Y', $record[1])->format('Y-m-d');
                    }
                } elseif (str_contains($channel, 'mpesa')) {
                    $refColumn = $record[0];
                    try {
                        $date = Carbon::parse(Date::excelToDateTimeObject($record[1]))->format('Y-m-d');
                    } catch (\Throwable $th) {
                        try {
                            $date = Carbon::createFromFormat('d-m-Y H:i:s', $record[1])->format('Y-m-d');
                        } catch (\Throwable $th2) {
                            $date = Carbon::createFromFormat('m/d/Y', $record[1])->format('Y-m-d');
                        }
                    }

                    if (!empty($record[6]) && $record[6] != 0) {
                        $amountStatus = 'debit';
                        $recordAmount = (float)$record[6];
                        $recordAmount = abs($recordAmount);
                        $debitTotal += $recordAmount;
                    } else {
                        $amountStatus = 'credit';
                        $recordAmount = (float)$record[5];
                        $creditTotal += $recordAmount;
                    }
                }

                if ($recordAmount != 0) {

                    $filtered = $allBanks->filter(function ($item) use ($refColumn, $ref2Column, $recordAmount, $date) {
                        return (
                            ($item->reference === $refColumn) &&
                            // ($item->trans_ref === $ref2Column) &&
                            $item->amount === $recordAmount &&
                            $item->bank_date === $date
                        );
                    });

                    $status = $filtered->count();

                    $arr = [
                        'reference' => $refColumn,
                        'reference_2' => $ref2Column,
                        'amount' => $recordAmount,
                        'bank_date' => $date,
                        'channel' => $request->channel,
                        'original_reference' => $refColumn,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'status' => $status == 0 ? 0 : 1,
                        'amountStatus' => $amountStatus,
                    ];
                    $statements[] = $arr;
                    if ($status) {
                        $bankInfo = $filtered->first();
                        PaymentVerificationBank::where('id', $bankInfo->id)
                            ->update([
                                'bank_date' => $date
                                // 'reference' => $refColumn,
                                // 'original_reference' => $refColumn
                            ]);
                        $statementsDuplicate[] = $arr;
                    } else {
                        $statementsUnique[] = $arr;
                    }
                }
            }

            $processingUpload = true;
            $channels = PaymentMethod::where([
                ['use_for_receipts', 1],
                ['use_as_channel', 1]
            ])->get();
            return view('admin.Finance.bank_statement.top_up', compact('title', 'model', 'breadcum', 'processingUpload', 'channels', 'statements', 'statementsDuplicate', 'statementsUnique'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function top_up_store(Request $request)
    {

        try {
            $check = DB::transaction(function () use ($request) {
                $statements = json_decode($request->statements, true);
                $allBanks = DB::table('payment_verification_banks')
                    // ->where('channel', $request->channel)
                    ->get()
                    ->map(function ($record) {
                        $record->amount = abs($record->amount); // Convert amount to absolute value
                        return $record;
                    });
                foreach ($statements as $statement) {

                    $amount = $statement['amount'];
                    if ($statement['amountStatus'] == 'debit') {
                        $amount = $amount * -1;
                    }

                    $refColumn = $statement['reference'];
                    $ref2Column = $statement['reference_2'];
                    $recordAmount = $statement['amount'];
                    $date = $statement['bank_date'];
                    $filtered = $allBanks->filter(function ($item) use ($refColumn, $ref2Column, $recordAmount, $date) {
                        return (
                            ($item->reference === $refColumn || $item->trans_ref === $ref2Column) &&
                            $item->amount === $recordAmount &&
                            $item->bank_date === $date
                        );
                    });
                    $counter = $filtered->count();

                    if ($counter == 0) {

                        $paymentMethod = PaymentMethod::where('title', $statement['channel'])->first();
                        PaymentVerificationBank::create([
                            'reference' => $refColumn,
                            'trans_ref' => $ref2Column,
                            'type' => $statement['amountStatus'],
                            'amount' => $amount,
                            'bank_date' => $date,
                            'original_reference' => $statement['reference'],
                            'channel' => $statement['channel'],
                            'payment_method_id' => $paymentMethod ? $paymentMethod->id : null,
                        ]);
                    }
                }
                return true;
            });

            if ($check) {
                return response()->json([
                    'result' => 1,
                    'message' => 'Bank Statement Uploaded Successfully',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function export_duplicate(Request $request)
    {
        $records = json_decode($request->duplicates, true);
        $columns = ['Bank Date', 'Reference', 'Trans Ref.', 'Amount', 'Channel'];
        return ExcelDownloadService::download('fuel_entries', collect($records), $columns);
    }

    public function allocate_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $roles = json_decode($request->roles, true);

            if ($request->status) {
                foreach ($roles as $value) {
                    $role = UserPermission::where('role_id', $value)->where('module_action', 'bank-statement-allocate')->first();
                    $savePer = new UserPermission();
                    $savePer->role_id = $value;
                    $savePer->module_name = $role->module_name;
                    $savePer->module_action = 'bank-statement-allocate-allow';
                    $savePer->save();
                }
            } else {
                UserPermission::where('module_action', 'bank-statement-allocate-allow')->whereIn('role_id', $roles)->delete();
            }

            DB::commit();
            Session::flash('success', 'Bank Statement Allocate Status Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
        }
        return redirect()->back();
    }

    public function top_up_debit()
    {
        if (!can('bank-statement-topup-debit', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Topup Bank Statements';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];

        $processingUpload = false;
        $channels = PaymentMethod::where([
            ['use_for_receipts', 1],
            ['use_as_channel', 1]
        ])->get();
        return view('admin.Finance.bank_statement.top_up_debit', compact('title', 'model', 'breadcum', 'processingUpload', 'channels'));
    }

    public function upload_debit(Request $request)
    {
        if (!can('bank-statement-topup-debit', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Debit Topup Bank Statements';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];
        try {

            $reader = new Xlsx();
            $reader->setReadDataOnly(false);
            $fileName = $request->file('topup_list');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $statements = [];
            $channel = strtolower($request->channel);
            $debitTotal = 0;

            $allBanks = DB::table('payment_verification_banks')
                // ->where('channel', $request->channel)
                // ->where('status','Pending')
                // ->where('type','credit')
                ->get()
                ->map(function ($record) {
                    $record->amount = abs($record->amount);
                    return $record;
                });

            foreach ($data as $index => $record) {
                if ($index == 0) continue;

                $date = date('Y-m-d', strtotime(str_replace('-', '/', $record[0])));
                $refColumn = $record[1];
                $recordAmount = str_replace('-', '', trim($record[2]));
                $recordAmount = (float)str_replace(',', '', trim($recordAmount));
                $recordAmount = abs($recordAmount);
                $debitTotal += $recordAmount;
                if ($recordAmount != 0) {
                    $filtered = $allBanks->filter(function ($item) use ($refColumn, $date) {
                        return $item->reference === $refColumn && $item->bank_date === $date;
                    });

                    // Check if there's any matched item
                    $status = $filtered->count();

                    // If there's a matched item, get the first one to extract the original `channel`,`type` and `amount`
                    if ($status > 0) {
                        $firstMatchedItem = $filtered->first();
                        $originalChannel = $firstMatchedItem->channel;
                        $originalAmount = $firstMatchedItem->amount;
                        $verificationStatus = $firstMatchedItem->status;
                        $originalType = $firstMatchedItem->type;
                    } else {
                        $originalChannel = null;
                        $originalAmount = null;
                        $originalType = null;
                        $verificationStatus = null;
                    }

                    $arr = [
                        'reference' => $refColumn,
                        'amount' => $recordAmount,
                        'bank_date' => $date,
                        'channel' => $request->channel,
                        'original_amount' => $originalAmount, // Add original amount to the array
                        'original_channel' => $originalChannel, // Add original channel to the array
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'status' => $status == 0 ? 0 : 1,
                        'verification_status' => $verificationStatus,
                        'type' => $originalType
                    ];

                    $statements[] = $arr;
                }
            }

            $processingUpload = true;
            $channels = PaymentMethod::where([
                ['use_for_receipts', 1],
                ['use_as_channel', 1]
            ])->get();
            return view('admin.Finance.bank_statement.top_up_debit', compact('title', 'model', 'breadcum', 'processingUpload', 'channels', 'statements'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function top_up_store_debit(Request $request)
    {
        if (!can('bank-statement-topup-debit', 'reconciliation')) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            $statements = json_decode($request->statements, true);
            $test = [];
            foreach ($statements as $statement) {

                $amount = $statement['amount'];
                $amount = $amount * -1;

                if ($statement["verification_status"] == "Pending" || $statement["verification_status"] == NULL) {
                    $test[] = $statement;
                    if ($statement['status']) {
                        PaymentVerificationBank::where('reference', $statement['reference'])
                            ->where('bank_date', $statement['bank_date'])
                            ->update([
                                'type' => 'debit',
                                'amount' => $amount,
                                'channel' => $statement['channel'],
                            ]);
                    } else {
                        PaymentVerificationBank::create([
                            'reference' => $statement['reference'],
                            'type' => 'debit',
                            'amount' => $amount,
                            'bank_date' => $statement['bank_date'],
                            'original_reference' => $statement['reference'],
                            'channel' => $statement['channel'],
                        ]);
                    }
                } else if ($statement["verification_status"] == "Verified" || $statement["verification_status"] == "Approved") {
                    PaymentVerificationBank::where('reference', $statement['reference'])
                        ->where('bank_date', $statement['bank_date'])
                        ->update([
                            'type' => 'debit',
                            'channel' => $statement['channel'],
                        ]);
                }
            }

            DB::commit();
            Session::flash('success', 'Debit Statement Uploaded Successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    // to remove trailing null headers
    function removeTrailingNulls(array $header)
    {
        while (end($header) === null) {
            array_pop($header);
        }
        return $header;
    }

    public function checkUploadHeaders($header, $channel)
    {
        $normalizedHeader = array_map(function ($headerValue) {
            return is_string($headerValue) ? strtolower(trim($headerValue)) : $headerValue;
        }, $this->removeTrailingNulls($header));

        switch ($normalizedHeader) {
            case ["txn date", "description", "value date", "money out", "money in", "ledger balance"]:
                if (str_contains($channel, 'vooma') || str_contains($channel, 'commercial') || str_contains($channel, 'kcb')) {
                    return True;
                } else {
                    return False;
                }
            case ["date", "invoice no", "route", "pesaflow code", "amount due", "amount paid", "reference", "amount out"]:
                return true;

            case ["transaction date", "value date", "narrative", "transaction reference", "debit", "credit", "running balance"]:
                if (str_contains($channel, 'equity')) {
                    return True;
                } else {
                    return False;
                }
            default:
                return null;
        }
    }

    public function bank_error_flag(Request $request)
    {
        if (!can('flag-bank-error', 'reconciliation')) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {

            $statement = PaymentVerificationBank::find($request->bankId);
            if ($statement->status != 'Approved') {
                BankStatementBankError::create([
                    'created_by' => Auth::user()->id,
                    'payment_verification_bank_id' => $request->bankId,
                    'reason' => $request->flag_reason,
                    'status' => 'Bank Error',
                ]);

                if ($statement->matched_debtors_id) {
                    // update debtor trans status
                    WaDebtorTran::find($statement->matched_debtors_id)->update([
                        'verification_status' => 'pending',
                    ]);
                }

                $statement->update([
                    'status' => 'Bank Error',
                    'matched_debtors_id' => null,
                ]);

                DB::commit();
                Session::flash('success', 'Bank Statement Flagged Successfully');
                return redirect()->back();
            }


            Session::flash('warning', 'Bank Statement Approved Cannot be Flagged');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function bank_error_logs()
    {
        if (!can('bank-error-logs', 'reconciliation')) {
            return returnAccessDeniedPage();
        }

        $title = 'Bank Error Log';
        $model = 'bank-error-logs';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];

        $query = BankStatementBankError::with('statement')->orderBy('created_at', 'DESC');
        if (request()->filled('start_date') && request()->filled('end_date')) {
            $query->whereBetween('created_at', [request()->start_date . ' 00:00:00', request()->end_date . ' 23:59:59']);
        }
        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('statement.amount', function ($query) {
                    return manageAmountFormat($query->statement->amount);
                })
                ->editColumn('created_at', function ($query) {
                    return date('Y-m-d H:i', strtotime($query->created_at));
                })
                ->toJson();
        }

        return view('admin.Finance.bank_statement.bank_error_logs', compact('title', 'model', 'breadcum'));
    }
    public function top_up_debit_mpesa()
    {
        if (!can('mpesa-statement-topup-debit', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Topup Mpesa Debits';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];

        $processingUpload = false;
        //fetch  new mpesa payment method only
        $channels = PaymentMethod::where([
            ['use_for_receipts', 1],
            ['use_as_channel', 1]
        ])->get();
        return view('admin.Finance.bank_statement.mpesa_top_up_debits', compact('title', 'model', 'breadcum', 'processingUpload', 'channels'));
    }
    public function upload_debit_mpesa(Request $request)
    {
        if (!can('mpesa-statement-topup-debit', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Debit Topup Bank Statements';
        $model = 'bank-statement-upload';
        $breadcum = ['Bank Statements' => route("bank-statements"), $title => ''];
        try {

            $reader = new Xlsx();
            $reader->setReadDataOnly(false);
            $fileName = $request->file('topup_list');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $statements = [];
            $foundInvoices  =  [];
            $unfoundInvoices = [];

            $channel = strtolower($request->channel);

            foreach ($data as $index => $record) {
                if ($index == 0) continue;

                $ref = $record[0];
                $date = $record[1];
                $invoice = $record[2];
                $amount = $record[3];

                $invoiceData = WaInternalRequisition::where('requisition_no', 'like', 'INV-' . $invoice)->first();

                if ($invoiceData) {
                    $arr = [
                        'REF' => $ref,
                        'DATE' => $date,
                        'ACC' => $invoice,
                        'AMOUNT' => $amount,
                        'INVOICE_NUMBER' => $invoiceData->requisition_no,
                        'CHANNEL' => $request->channel,
                        'REQUISITION_ID' => $invoiceData->id,

                    ];
                    $foundInvoices[] = $arr;
                } else {
                    $arr = [
                        'REF' => $ref,
                        'DATE' => $date,
                        'ACC' => $invoice,
                        'AMOUNT' => $amount,
                        'INVOICE_NUMBER' => null,
                        'CHANNEL' => $request->channel,
                        'REQUISITION_ID' => null,

                    ];
                    $unfoundInvoices[] = $arr;
                }

                $statements[] = $arr;
            }

            $processingUpload = true;
            $channels = PaymentMethod::where([
                ['use_for_receipts', 1],
                ['use_as_channel', 1]
            ])->get();
            return view('admin.Finance.bank_statement.mpesa_top_up_debits', compact('title', 'model', 'breadcum', 'processingUpload', 'channels', 'statements', 'foundInvoices', 'unfoundInvoices'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function top_up_store_debit_mpesa(Request $request)
    {
        if (!can('mpesa-statement-topup-debit', 'reconciliation')) {
            return returnAccessDeniedPage();
        }
        // dd(json_decode($request->statements, true));

        DB::beginTransaction();
        try {
            $statements = json_decode($request->statements, true);
            $test = [];
            $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
            $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();

            foreach ($statements as $statement) {
                //create_payment_verification_banks
                $statementRecord = PaymentVerificationBank::where('reference', $statement['REF'])->first();
                if ($statementRecord) {
                    DB::rollBack();
                    Session::flash('warning', 'Statement record with reference ' . $statementRecord->reference . ' exists');
                    return redirect()->back();
                }
                $amount = (float) str_replace(',', '',  $statement['AMOUNT']);
                $date = str_replace('"', '', $statement['DATE']);
                $datetime = preg_replace('/\s+/', ' ', trim($date));

                PaymentVerificationBank::create([
                    'reference' => $statement['REF'],
                    'type' => 'credit',
                    'amount' => $amount,
                    'bank_date' => $statement['DATE'],
                    'original_reference' => $statement['REF'],
                    'channel' => $statement['CHANNEL'],
                ]);
                $data = [
                    'amount' => $amount,
                    'date' => Carbon::parse($datetime)->toDateString(),
                    'ref' =>  $statement['REF'],
                    'channel' => $statement['CHANNEL'],
                    'acc' => $statement['ACC'],
                ];

                $jsonEncodedIpn = json_encode($data);

                $notification = MpesaIpnNotification::create([
                    'payment_details' => $jsonEncodedIpn,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                //fetch invoice and mark it as paid 
                $invoiceDetails = WaInternalRequisition::with('customer', 'getRouteCustomer', 'route', 'route.waCustomer')->find($statement['REQUISITION_ID']);
                if (!$invoiceDetails) {
                    continue;
                }
                $invoiceDetails->status = 'PAID';
                $invoiceDetails->save();
                $documentNo = getCodeWithNumberSeries('RECEIPT');
                updateUniqueNumberSeries('RECEIPT', $documentNo);
                $paymentMethod = PaymentMethod::where('title', $statement['CHANNEL'])->first();
                if ($paymentMethod) {
                    $bank_account = WaBankAccount::where('bank_account_gl_code_id', $paymentMethod->gl_account_id)->first();
                } else {
                    $bank_account = WaBankAccount::where('account_name', 'EQUITY MAKONGENI')->first();
                }
                $debtorTrans = WaDebtorTran::create([
                    'notification_id' => $notification->id,
                    'wa_sales_invoice_id' => $invoiceDetails->id,
                    'salesman_id' => $invoiceDetails->customer_id,
                    'salesman_user_id' => $invoiceDetails->user_id,
                    'type_number' => $series_module?->type_number,
                    'wa_customer_id' => $invoiceDetails->customer_id,
                    'customer_number' => WaCustomer::find($invoiceDetails->customer_id)->customer_code,
                    'trans_date' => Carbon::parse($datetime)->toDateString(),
                    'input_date' => date('Y-m-d H:i:s'),
                    'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                    'shift_id' => null,
                    'invoice_customer_name' => $invoiceDetails->getRouteCustomer?->name,
                    'reference' => $statement['REF'],
                    'amount' => - ($amount),
                    'document_no' => $documentNo,
                    'branch_id' => $invoiceDetails->restaurant_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'channel' => $statement['CHANNEL'],
                    'wa_payment_method_id' => $paymentMethod->id,
                    'route_id' => $invoiceDetails->route_id,
                ]);
                $tenderEntry = new WaTenderEntry();
                $tenderEntry->document_no = $documentNo;
                $tenderEntry->notification_id = $notification->id;
                $tenderEntry->wa_sales_invoice_id = $invoiceDetails->id;
                $tenderEntry->channel = $paymentMethod?->title;
                $tenderEntry->account_code = $bank_account->getGlDetail?->account_code;
                $tenderEntry->reference = $statement['REF'];
                $tenderEntry->additional_info = $statement['REF'];
                $tenderEntry->customer_id = $invoiceDetails->customer_id;
                $tenderEntry->trans_date = Carbon::parse($datetime)->toDateString();
                $tenderEntry->wa_payment_method_id = $paymentMethod?->id;
                $tenderEntry->amount = $amount;
                $tenderEntry->paid_by = $statement['CHANNEL'];
                $tenderEntry->cashier_id = 1;
                $tenderEntry->branch_id = $invoiceDetails->restaurant_id;
                $tenderEntry->save();
            }

            DB::commit();
            Session::flash('success', 'Statement Uploaded Successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
}
