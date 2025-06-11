<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ApproveRouteBanking;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\WaBankAccount;
use App\Model\WaBanktran;
use App\Model\WaChartsOfAccount;
use App\Model\WaCompanyPreference;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Models\BankingApproval;
use App\Models\FraudJournal;
use App\Models\SuspendedTransaction;
use App\WaTenderEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BankingApprovalController extends Controller
{
    protected string $model = 'banking-approval';
    protected string $permissionModule = 'reconciliation';

    public function showRouteOverviewPage(): View|RedirectResponse
    {
        if (!can('see-overview', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Route Banking Overview';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];

        $branches = Restaurant::select('id', 'name')->get();

        return view('banking_approval.route_overview', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function getRecords(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $salesQuery = DB::table('wa_internal_requisition_items as items')
                ->select(
                    DB::raw('DATE(items.created_at) as date'),
                    DB::raw("SUM(total_cost_with_vat) as sales")
                )
                ->join('wa_internal_requisitions as invoices', 'items.wa_internal_requisition_id', 'invoices.id')
                ->whereBetween('items.created_at', [$fromSalesDate, $toSalesDate])
                ->where('invoices.restaurant_id', $request->branch_id)
                ->groupBy(DB::raw('DATE(items.created_at)'))
                ->get();

            $todaySalesQuery = DB::table('wa_internal_requisition_items as items')
                ->select(
                    DB::raw('DATE(items.created_at) as date'),
                    DB::raw("SUM(total_cost_with_vat) as sales")
                )
                ->join('wa_internal_requisitions as invoices', 'items.wa_internal_requisition_id', 'invoices.id')
                ->whereBetween('items.created_at', [$fromDate, $toDate])
                ->where('invoices.restaurant_id', $request->branch_id)
                ->groupBy(DB::raw('DATE(items.created_at)'))
                ->get();

            $debtorsQuery = DB::table('wa_debtor_trans')
                ->select(
                    DB::raw('trans_date as date'),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RTN%' AND amount < 0) THEN amount ELSE 0 END)) as returns"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND channel like 'vooma%') THEN amount ELSE 0 END)) as vooma"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND channel = 'EQUITY MAKONGENI') THEN amount ELSE 0 END)) as eazzy"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND channel = 'EQUITY BANK') THEN amount ELSE 0 END)) as equityMain"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND channel = 'KENYA COMMERCIAL BANK') THEN amount ELSE 0 END)) as kcbMain"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND channel = 'MPESA MAKONGENI') THEN amount ELSE 0 END)) as mpesa"),
                    DB::raw("abs(SUM(CASE WHEN (document_no like 'RCT%' AND (verification_status = 'verified' OR verification_status = 'approved')) THEN amount ELSE 0 END)) as verified"),
                    DB::raw("abs(SUM(CASE WHEN verification_status = 'approved' THEN amount ELSE 0 END)) as approved"),
                    DB::raw("abs(SUM(CASE WHEN document_no like 'FJ%' THEN amount ELSE 0 END)) as fraud"),
                    DB::raw("(SELECT SUM(amount)
                        FROM wa_debtor_trans as debtor
                        JOIN wa_customers as customer ON customer.id = debtor.wa_customer_id
                        JOIN routes as route ON route.id = customer.route_id 
                        WHERE route.restaurant_id = '$request->branch_id'
                        AND debtor.trans_date <= wa_debtor_trans.trans_date
                    ) as todayBalance"),

                )
                ->join('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
                ->join('routes', 'routes.id', 'wa_customers.route_id')
                ->whereBetween('wa_debtor_trans.trans_date', [$fromDate, $toDate])
                ->where('routes.restaurant_id', $request->branch_id)
                ->groupBy("trans_date")
                ->get();

            $period = CarbonPeriod::create($fromDate, $toDate);
            $days = [];
           
            foreach ($period as $date) {
                $day = $date->format('Y-m-d');
                $salesDate = Carbon::parse($day)->subDay()->toDateString();
                $startOfDayTomorrow = Carbon::parse($day)->addDay()->startOfDay();
                $sales =  $salesQuery->firstWhere('date', $salesDate)->sales ?? 0;
                $todaySales = $todaySalesQuery->firstWhere('date', $day)->sales?? 0;
                $returns = $debtorsQuery->firstWhere('date', $day)->returns ?? 0;
                $eazzy =  $debtorsQuery->firstWhere('date', $day)->eazzy ?? 0;
                $vooma = $debtorsQuery->firstWhere('date', $day)->vooma ?? 0;
                $equity = $debtorsQuery->firstWhere('date', $day)->equityMain?? 0;
                $kcb = $debtorsQuery->firstWhere('date', $day)->kcbMain ?? 0;
                $mpesa =  $debtorsQuery->firstWhere('date', $day)->mpesa ?? 0;
                $verified = $debtorsQuery->firstWhere('date', $day)->verified ?? 0;
                $fraudTrans = $debtorsQuery->firstWhere('date', $day)->fraud ?? 0;
                $netSales = $sales;
                $totalReceipts = $eazzy + $equity + $vooma + $kcb + $mpesa;
                $unverifiedReceipts = $totalReceipts - $verified;
                $debtorsVariance = $netSales - $verified - $returns - $fraudTrans;

                $todayBalance = $debtorsQuery->firstWhere('date', $day)->todayBalance ?? 0;

                $runningBalance = $todayBalance - $todaySales;

                $approved = $debtorsQuery->firstWhere('date', $day)->approved ?? 0;

                $status = (abs($approved) == ($totalReceipts + $fraudTrans)) ? 'Closed' : 'Pending';

              
                $days[] = [
                    'date' => $day,
                    'branch' => $request->branch_id,
                    'sales' => $netSales,
                    'returns' => $returns,
                    'vooma' => $vooma,
                    'eazzy' => $eazzy,
                    'equity' => $equity,
                    'kcb' => $kcb,
                    'mpesa' => $mpesa,
                    'total_receipts' => $totalReceipts,
                    'verified_receipts' => $verified,
                    'variance' => $unverifiedReceipts,
                    'debtors_variance' => $debtorsVariance,
                    'fraud' => $fraudTrans,
                    'running_balance' => $runningBalance,
                    'status' => $status
                ];

            }

            $days = collect($days)->map(function ($record) {
                $record['sales'] = manageAmountFormat($record['sales']);
                $record['returns'] = manageAmountFormat($record['returns']);
                $record['vooma'] = manageAmountFormat($record['vooma']);
                $record['eazzy'] = manageAmountFormat($record['eazzy']);
                $record['equity'] = manageAmountFormat($record['equity']);
                $record['kcb'] = manageAmountFormat($record['kcb']);
                $record['mpesa'] = manageAmountFormat($record['mpesa']);
                $record['total_receipts'] = manageAmountFormat($record['total_receipts']);
                $record['verified_receipts'] = manageAmountFormat($record['verified_receipts']);
                $record['variance'] = manageAmountFormat($record['variance']);
                $record['debtors_variance'] = manageAmountFormat($record['debtors_variance']);
                $record['fraud'] = manageAmountFormat($record['fraud']);
                $record['running_balance'] = manageAmountFormat($record['running_balance']);

                return $record;
            });

            return $this->jsonify($days);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function showRouteDetailsPage(Request $request): View|RedirectResponse
    {
        if (!can('see-overview', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Route Banking Overview';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Banking' => ''];

        $date = $request->date;
        $branch = $request->branch;
        $user = Auth::user();

        return view('banking_approval.route_banking_show', compact('title', 'model', 'breadcrum', 'date', 'branch', 'user'));
    }

    public function getVerifiedReceipts(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $verifiedQuery = DB::select("
                select d.id,d.created_at,trans_date,document_no,customer_name as route,d.channel,d.reference,abs(d.amount) as amount,bank_date,b.reference as bank_ref,verification_status from wa_debtor_trans as d 
                left join payment_verification_banks as b on d.id = b.matched_debtors_id 
                join wa_customers as c on d.wa_customer_id = c.id
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RCT%' and (verification_status = 'verified' or verification_status = 'approved') 
                and branch_id = $request->branch_id
            ");

            $verifiedReceipts = collect($verifiedQuery)->map(function ($record) use ($verifiedQuery) {
                $record->total = collect($verifiedQuery)->sum('amount');
                return $record;
            });

            $verifiedReceipts = $verifiedReceipts->map(function ($record) {
                $record->amount = manageAmountFormat($record->amount);
                $record->total = manageAmountFormat($record->total);
                $record->verification_status = ucfirst($record->verification_status);

                return $record;
            });

            return $this->jsonify($verifiedReceipts);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getUnVerifiedReceipts(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $unVerifiedQuery = DB::select("
                select d.id,trans_date,document_no,customer_name as route,d.channel,d.reference,abs(d.amount) as amount from wa_debtor_trans as d 
                join wa_customers as c on d.wa_customer_id = c.id
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RCT%' and (verification_status = 'pending') and unverified_resolved = false
                and branch_id = $request->branch_id
            ");

            $unVerifiedReceipts = collect($unVerifiedQuery)->map(function ($record) use ($unVerifiedQuery) {
                $record->total = collect($unVerifiedQuery)->sum('amount');
                $record->selected = false;

                return $record;
            });

            $unVerifiedReceipts = $unVerifiedReceipts->map(function ($record) {
                $record->raw_amount = $record->amount;
                $record->amount = manageAmountFormat($record->amount);
                $record->total = manageAmountFormat($record->total);

                return $record;
            });

            return $this->jsonify($unVerifiedReceipts);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }


    public function getFraudTransactions(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fraudTransQuery = DB::select("
                select d.id,trans_date,d.document_no,customer_name as route,d.channel,d.reference,abs(d.amount) as amount, users.name as blamable,f.comments,f.narrative,f.created_at as posting_date from wa_debtor_trans as d 
                join wa_customers as c on d.wa_customer_id = c.id 
                left join users on d.user_id = users.id 
                join fraud_journals f on d.register_cheque_id = f.id
                where (trans_date between '$fromDate' and '$toDate') 
                and d.document_no like 'FJ%' 
                and d.branch_id = $request->branch_id
            ");

            $fraudTrans = collect($fraudTransQuery)->map(function ($record) use ($fraudTransQuery) {
                $record->total = collect($fraudTransQuery)->sum('amount');
                return $record;
            });

            $fraudTrans = $fraudTrans->map(function ($record) {
                $record->raw_amount = $record->amount;
                $record->amount = manageAmountFormat($record->amount);
                $record->total = manageAmountFormat($record->total);

                return $record;
            });

            return $this->jsonify($fraudTrans);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getOtherReceivables(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $query = DB::select("
                select trans_date,document_no,customer_name as route,d.reference,abs(d.amount) as amount from wa_debtor_trans as d 
                join wa_customers as c on d.wa_customer_id = c.id
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RTN%' and amount < 0
                and branch_id = $request->branch_id
            ");

            $others = collect($query)->map(function ($record) use ($query) {
                $record->total = collect($query)->sum('amount');
                return $record;
            });

            $others = $others->map(function ($record) {
                $record->amount = manageAmountFormat($record->amount);
                $record->total = manageAmountFormat($record->total);
                return $record;
            });

            return $this->jsonify($others);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getUnpaidAccounts(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $salesQuery = DB::select("
            select date(trans_date) as date,sum(amount) as sales from wa_debtor_trans 
            where (trans_date between '$fromSalesDate' and '$toSalesDate') 
            and document_no like 'INV%' and branch_id = $request->branch_id 
            group by date(trans_date)
        ");

            $returnsQuery = DB::select("
            select date(trans_date) as date,sum(abs(amount)) as returns from wa_debtor_trans 
            where (trans_date between '$fromDate' and '$toDate') 
            and document_no like 'RTN%' and branch_id = $request->branch_id 
            group by date(trans_date)
        ");

            $others = collect($query)->map(function ($record) use ($query) {
                $record->total = collect($query)->sum('amount');
                return $record;
            });

            $others = $others->map(function ($record) {
                $record->amount = manageAmountFormat($record->amount);
                $record->total = manageAmountFormat($record->total);
                return $record;
            });

            return $this->jsonify($others);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function suspendUnverified(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $debtorIds = $request->ids;
            $debtorTrans = WaDebtorTran::whereIn('id', $debtorIds)->get();

            foreach ($debtorTrans as $trans) {
                SuspendedTransaction::create([
                    'wa_customer_id' => $trans->wa_customer_id,
                    'suspended_by' => $request->blamable,
                    'resolved_by' => $request->blamable,
                    'document_no' => $trans->document_no,
                    'reference' => $trans->reference,
                    'amount' => abs($trans->amount),
                    'trans_date' => $trans->trans_date,
                    'input_date' => $trans->created_at,
                    'route' => $trans->customerDetail->customer_name,
                    'reason' => $request->comment,
                    'branch_id' => $trans->branch_id,
                    'channel' => $trans->channel,
                    'verification_record_id' => $trans->verification_record_id,
                    'manual_upload_status' => $trans->manual_upload_status,
                    'manual_upload_approved_by' => $trans->manual_upload_approved_by,
                    'status' => 'expunged',
                ]);

                $tenderEntry = WaTenderEntry::where('document_no', $trans->document_no)
                    ->where('amount', abs($trans->amount))
                    ->first();
                $tenderEntry?->delete();

                $documentNumber = getCodeWithNumberSeries('FRAUD AND INVESTIGATION');
                $journal = FraudJournal::create([
                    'journal_number' => $documentNumber,
                    'reference_date' => $trans->trans_date,
                    'document_no' => $trans->document_no,
                    'document_reference' => $trans->reference,
                    'customer_account_id' => $trans->wa_customer_id,
                    'comments' => $request->comment,
                    'narrative' => "Missing Trans $trans->document_no Ref $trans->reference",
                    'amount' => abs($trans->amount),
                    'branch_id' => $trans->branch_id,
                    'posted_by' => $request->blamable
                ]);

                $matchedWaCustomer = WaCustomer::find($trans->wa_customer_id);
                $fraudPaymentMethod = PaymentMethod::where('slug', 'fraud-journals')->first();
                $debtorTrans = WaDebtorTran::create([
                    'wa_customer_id' => $matchedWaCustomer->id,
                    'customer_number' => $matchedWaCustomer->customer_code,
                    'trans_date' => $trans->trans_date,
                    'input_date' => $trans->input_date,
                    'invoice_customer_name' => "$matchedWaCustomer->customer_name",
                    'reference' => "$trans->reference",
                    'amount' => $trans->amount,
                    'document_no' => $documentNumber,
                    'branch_id' => $trans->branch_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'channel' => $fraudPaymentMethod?->title,
                    'wa_payment_method_id' => $fraudPaymentMethod?->id,
                    'user_id' => $request->blamable,
                    'register_cheque_id' => $journal->id
                ]);

                updateUniqueNumberSeries('FRAUD AND INVESTIGATION', $documentNumber);
                $trans->delete();
            }

            DB::commit();
            return $this->jsonify([]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function approveBanking(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $debtorIds = $request->ids;
            $debtorTrans = WaDebtorTran::whereIn('id', $debtorIds)
                ->whereNot('verification_status', 'approved')
                ->get();

            $glTrans = [];
            $bankTrans = [];

            foreach ($debtorTrans as $trans) {
                $trans->update(['verification_status' => 'approved']);

                $customerAccount = WaCustomer::find($trans->wa_customer_id);
                $paymentMethod = PaymentMethod::find($trans->wa_payment_method_id);
                if (!$paymentMethod) {
                    return $this->jsonify(['message' => "Approve route banking failed: Payment method not found for trans $trans->document_no $trans->channel"], 500);
                }

                $account = WaChartsOfAccount::find($paymentMethod->gl_account_id);
                $companyPreference = WaCompanyPreference::find(1);
                $debtorsControl = $companyPreference->debtorsControlGlAccount?->account_code;
                $fraudAccount = '55001-007';

                // Debtors Control (Credit)
                $glTrans[] = [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'restaurant_id' => $trans->branch_id,
                    'tb_reporting_branch' => $trans->branch_id,
                    'transaction_type' => 'Customer Payment',
                    'transaction_no' => $trans->document_no,
                    'trans_date' => $trans->trans_date,
                    'account' => $debtorsControl,
                    'amount' => abs($trans->amount) * -1,
                    'reference' => $trans->reference,
                    'narrative' => "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                ];

                if (substr($trans->document_no, 0, 3) == 'RCT') {
                    $glTrans[] = [
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'restaurant_id' => $trans->branch_id,
                        'tb_reporting_branch' => $trans->branch_id,
                        'transaction_type' => 'Customer Payment',
                        'transaction_no' => $trans->document_no,
                        'trans_date' => $trans->trans_date,
                        'account' => $account->account_code,
                        'amount' => abs($trans->amount),
                        'reference' => $trans->reference,
                        'narrative' =>  "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                    ];

                    $bankTrans[] = [
                        'document_no' => $trans->document_no,
                        'trans_date' => $trans->trans_date,
                        'bank_gl_account_code' => $account->account_code,
                        'wa_payment_method_id' => $paymentMethod->id,
                        'amount' => abs($trans->amount),
                        'reference' => $trans->reference,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                } else {
                    $glTrans[] = [
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'restaurant_id' => $trans->branch_id,
                        'tb_reporting_branch' => $trans->branch_id,
                        'transaction_type' => 'Fraud Journal',
                        'transaction_no' => $trans->document_no,
                        'trans_date' => $trans->trans_date,
                        'account' => $fraudAccount,
                        'amount' => abs($trans->amount),
                        'reference' => $trans->reference,
                        'narrative' => "$customerAccount->customer_name / $trans->document_no / $trans->reference",
                    ];
                }
            }

            WaGlTran::insert($glTrans);
            WaBanktran::insert($bankTrans);

            DB::commit();
            return $this->jsonify([]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getEazzy(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $verified = DB::table('wa_debtor_trans as d')
                ->select(
                    'd.id',
                    'd.created_at',
                    'trans_date',
                    'document_no',
                    'c.customer_name as route',
                    'd.channel',
                    'd.reference',
                    DB::raw('ABS(d.amount) as amount'),
                    'b.reference as bank_ref',
                    'd.verification_status'
                )
                ->leftJoin('payment_verification_banks as b', 'd.id', 'b.matched_debtors_id')
                ->join('wa_customers as c', 'c.id', 'd.wa_customer_id')
                ->whereDate('d.trans_date','>=', $fromDate)
                ->whereDate('d.trans_date','<=', $endDate)
                ->where('d.document_no', 'like', 'RCT%')
                ->where('branch_id', $request->branch_id)
                ->where(function ($record){
                    $record->where('verification_status', 'verified')
                        ->orWhere('verification_status', 'approved');
                })
                ->where('d.channel', 'EQUITY MAKONGENI')
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getEbMain(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $verified = DB::table('wa_debtor_trans as d')
                ->select(
                    'd.id',
                    'd.created_at',
                    'trans_date',
                    'document_no',
                    'c.customer_name as route',
                    'd.channel',
                    'd.reference',
                    DB::raw('ABS(d.amount) as amount'),
                    'b.reference as bank_ref',
                    'd.verification_status'
                )
                ->leftJoin('payment_verification_banks as b', 'd.id', 'b.matched_debtors_id')
                ->join('wa_customers as c', 'c.id', 'd.wa_customer_id')
                ->whereDate('d.trans_date','>=', $fromDate)
                ->whereDate('d.trans_date','<=', $endDate)
                ->where('d.document_no', 'like', 'RCT%')
                ->where('branch_id', $request->branch_id)
                ->where(function ($record){
                    $record->where('verification_status', 'verified')
                        ->orWhere('verification_status', 'approved');
                })
                ->where('d.channel', 'EQUITY BANK')
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%equity%')
                        ->where('payment_methods.account_type', 2)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getVooma(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $verified = DB::table('wa_debtor_trans as d')
                ->select(
                    'd.id',
                    'd.created_at',
                    'trans_date',
                    'document_no',
                    'c.customer_name as route',
                    'd.channel',
                    'd.reference',
                    DB::raw('ABS(d.amount) as amount'),
                    'b.reference as bank_ref',
                    'd.verification_status'
                )
                ->leftJoin('payment_verification_banks as b', 'd.id', 'b.matched_debtors_id')
                ->join('wa_customers as c', 'c.id', 'd.wa_customer_id')
                ->whereDate('d.trans_date','>=', $fromDate)
                ->whereDate('d.trans_date','<=', $endDate)
                ->where('d.document_no', 'like', 'RCT%')
                ->where('branch_id', $request->branch_id)
                ->where(function ($record){
                    $record->where('verification_status', 'verified')
                        ->orWhere('verification_status', 'approved');
                })
                ->where('d.channel','like', 'vooma%')
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', '%vooma%')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getKcbMain(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $verified = DB::table('wa_debtor_trans as d')
                ->select(
                    'd.id',
                    'd.created_at',
                    'trans_date',
                    'document_no',
                    'c.customer_name as route',
                    'd.channel',
                    'd.reference',
                    DB::raw('ABS(d.amount) as amount'),
                    'b.reference as bank_ref',
                    'd.verification_status'
                )
                ->leftJoin('payment_verification_banks as b', 'd.id', 'b.matched_debtors_id')
                ->join('wa_customers as c', 'c.id', 'd.wa_customer_id')
                ->whereDate('d.trans_date','>=', $fromDate)
                ->whereDate('d.trans_date','<=', $endDate)
                ->where('d.document_no', 'like', 'RCT%')
                ->where('branch_id', $request->branch_id)
                ->where(function ($record){
                    $record->where('verification_status', 'verified')
                        ->orWhere('verification_status', 'approved');
                })
                ->where('d.channel', 'KENYA COMMERCIAL BANK')
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', 'KENYA COMMERCIAL BANK')
                        ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getMpesa(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;
            $verified = DB::table('wa_debtor_trans as d')
                ->select(
                    'd.id',
                    'd.created_at',
                    'trans_date',
                    'document_no',
                    'c.customer_name as route',
                    'd.channel',
                    'd.reference',
                    DB::raw('ABS(d.amount) as amount'),
                    'b.reference as bank_ref',
                    'd.verification_status'
                )
                ->leftJoin('payment_verification_banks as b', 'd.id', 'b.matched_debtors_id')
                ->join('wa_customers as c', 'c.id', 'd.wa_customer_id')
                ->whereDate('d.trans_date','>=', $fromDate)
                ->whereDate('d.trans_date','<=', $endDate)
                ->where('d.document_no', 'like', 'RCT%')
                ->where('branch_id', $request->branch_id)
                ->where(function ($record){
                    $record->where('verification_status', 'verified')
                        ->orWhere('verification_status', 'approved');
                })
                ->where('d.channel', 'MPESA MAKONGENI')
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $verifiedTotal = manageAmountFormat($verified->sum('amount'));

            $unknown = DB::table('payment_verification_banks as pvb')
                ->select(
                    'pvb.*'
                )
                ->join('payment_methods', function ($join) use ($branchId) {
                    $join->on('pvb.payment_method_id', '=', 'payment_methods.id')
                        ->where('payment_methods.title', 'like', 'MPESA MAKONGENI')
                        // ->where('payment_methods.account_type', 1)
                        ->where('payment_methods.branch_id', $branchId);
                })
                ->where('status', 'Pending')
                ->where('amount', '>', 0)
                ->whereBetween('pvb.bank_date', [$fromDate, $endDate])
                ->get()
                ->map(function ($record) {
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $unknownTotal = manageAmountFormat($unknown->sum('amount'));

            return $this->jsonify(['verified' => $verified, 'unknown' => $unknown, 'verifiedTotal' => $verifiedTotal, 'unknownTotal' => $unknownTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getReturns(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $returns = DB::table('wa_debtor_trans')
                ->select(
                    'wa_debtor_trans.trans_date as return_date',
                    DB::raw('SUM(ABS(amount)) as amount'),
                    'routes.route_name as route',
                    'wa_debtor_trans.document_no'
                )
                ->join('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
                ->join('routes', 'wa_customers.route_id', 'routes.id')
                ->whereDate('trans_date', '>=', $fromDate)
                ->whereDate('trans_date', '<=', $endDate)
                ->where('routes.restaurant_id', $branchId)
                ->where('amount', '<', 0)
                ->where('document_no', 'like', 'RTN%')
                ->groupBy('document_no')
                ->get()
                ->map(function ($record){
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $returnsTotal = manageAmountFormat($returns->sum('amount'));

            return $this->jsonify(['returns' => $returns, 'returnsTotal' => $returnsTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    public function getSales(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $endDate = Carbon::parse($request->to_date)->subDay()->endOfDay();
            $branchId = $request->branch_id;

            $sales = DB::table('wa_internal_requisition_items as wiri')
                ->select(
                    'wir.requisition_no as sales_no',
                    'wir.created_at',
                    DB::raw("SUM(wiri.total_cost_with_vat) as amount"),
                    'routes.route_name as route'

                )
                ->join('wa_internal_requisitions as wir', 'wir.id', 'wiri.wa_internal_requisition_id')
                ->join('routes', 'routes.id', 'wir.route_id')
                ->whereDate('wir.created_at', '>=', $fromDate)
                ->whereDate('wir.created_at', '<=', $endDate)
                ->where('wir.restaurant_id', $branchId)
                ->groupBy('wir.id')
                ->get()->map(function ($record){
                    $record->formatted_amount = manageAmountFormat($record->amount);
                    return $record;
                });

            $salesGrossTotal = manageAmountFormat($sales->sum('amount'));

            return $this->jsonify(['records' => $sales, 'gross_total' => $salesGrossTotal]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function getBankingSummary(Request $request)
    {
        try {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branchId = (int)$request->branch_id;

            $records = DB::table('payment_methods as pm')
                ->select(
                    'pm.title as collection_account',
                    'sweep_account.title as sweep_account',

                    //same trans date on debtors
                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks  as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) = date(debtors.trans_date)
                    ) as same_day_collections"),

                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) != date(debtors.trans_date)
                    ) as late_utilizations"),

                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) != date(debtors.trans_date)
                    ) as utilized_unknowns"),

                    DB::raw("(select sum(statement.amount)
                    from payment_verification_banks as statement
                    join wa_debtor_trans as debtors on debtors.id = statement.matched_debtors_id
                    where statement.payment_method_id = pm.id
                    and date(statement.bank_date between '$fromDate' and '$endDate')
                    and date(statement.bank_date) != date(debtors.trans_date) 

                    ) as utilized_unknowns"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                    where payment_verification_banks.status = 'Pending' 
                    and payment_verification_banks.amount > 0
                    and (bank_date between '$fromDate' and '$endDate')
                    and payment_verification_banks.payment_method_id = pm.id) as actual_unknowns"),

                    DB::raw("(select sum(amount) from payment_verification_banks 
                    where amount < 0
                    and (bank_date between '$fromDate' and '$endDate')
                    and payment_method_id = pm.id) as sweep_total"),
                )
                ->join('payment_methods as sweep_account', 'pm.sweep_account_id', '=', 'sweep_account.id')
                ->where('pm.branch_id', $request->branch_id)
                ->where('pm.account_type', 1)
                ->get()
                ->map(function ($record) {
                    $record->formatted_same_day_collections = manageAmountFormat($record->same_day_collections);
                    $record->formatted_late_utilizations = manageAmountFormat($record->late_utilizations);

                    $record->total_collection = $record->same_day_collections + $record->late_utilizations;
                    $record->formatted_total_collection = manageAmountFormat($record->total_collection);

                    $record->formatted_utilized_unknowns = manageAmountFormat($record->utilized_unknowns);
                    $record->formatted_actual_unknowns = manageAmountFormat($record->actual_unknowns);

                    $record->total_unknowns = $record->utilized_unknowns + $record->actual_unknowns;
                    $record->formatted_total_unknowns = manageAmountFormat($record->total_unknowns);

                    $record->nominal_total = $record->same_day_collections + $record->total_unknowns;
                    $record->formatted_nominal_total = manageAmountFormat($record->nominal_total);
                   
                    $record->formatted_sweep_total = manageAmountFormat(abs($record->sweep_total));
                    $record->variance = abs($record->sweep_total) - $record->nominal_total;
                    $record->formatted_variance = manageAmountFormat($record->variance);

                    return $record;
                });

            return $this->jsonify(['records' => $records]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function printRouteSaleBankingOverview(Request $request)
    {
        try {
            $date = $request->date;
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();
            $branch = Restaurant::find($request->branch_id);
            $user = Auth::user();
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();

            $fromSalesDate = Carbon::parse($request->from_date)->subDay()->startOfDay();
            $toSalesDate = Carbon::parse($request->to_date)->subDay()->endOfDay();

            $salesQuery = DB::select("
                            select date(items.created_at) as date,sum(total_cost_with_vat) as sales from wa_internal_requisition_items as items
                            join wa_internal_requisitions as invoices on items.wa_internal_requisition_id = invoices.id
                            where (items.created_at between '$fromSalesDate' and '$toSalesDate') and invoices.restaurant_id = $request->branch_id
                            group by date(items.created_at)
                        ");

            $todaySalesQuery = DB::select("
                            select date(items.created_at) as date,sum(total_cost_with_vat) as sales from wa_internal_requisition_items as items
                            join wa_internal_requisitions as invoices on items.wa_internal_requisition_id = invoices.id
                            where (items.created_at between '$fromDate' and '$toDate') and invoices.restaurant_id = $request->branch_id
                            group by date(items.created_at)
                        ");

            $returnsQuery = DB::select("
                        select date(trans_date) as date,sum(abs(amount)) as returns from wa_debtor_trans 
                        join wa_customers on wa_debtor_trans.wa_customer_id = wa_customers.id 
                        join routes on wa_customers.route_id = routes.id
                        where (trans_date between '$fromDate' and '$toDate') 
                        and document_no like 'RTN%' 
                        and routes.restaurant_id = $request->branch_id 
                        and amount < 0 
                        group by date(trans_date)
                    ");

            $voomaQuery = DB::select("
                        select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                        where (trans_date between '$fromDate' and '$toDate') 
                        and document_no like 'RCT%' and channel like 'vooma%' and branch_id = $request->branch_id 
                        group by date(trans_date)
                    ");

            $eazzyQuery = DB::select("
                        select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                        where (trans_date between '$fromDate' and '$toDate') 
                        and document_no like 'RCT%' and channel = 'EQUITY MAKONGENI' and branch_id = $request->branch_id 
                        group by date(trans_date)
                    ");

            $equityMainQuery = DB::select("
                    select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                    where (trans_date between '$fromDate' and '$toDate') 
                    and document_no like 'RCT%' and channel = 'EQUITY BANK' and branch_id = $request->branch_id 
                    group by date(trans_date)
                ");

            $kcbMainQuery = DB::select("
                select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RCT%' and channel = 'KENYA COMMERCIAL BANK' and branch_id = $request->branch_id 
                group by date(trans_date)
            ");

            $mpesaQuery = DB::select("
                select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RCT%' and channel = 'MPESA MAKONGENI' and branch_id = $request->branch_id 
                group by date(trans_date)
            ");

            $verificationQuery = DB::select("
                select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
                where (trans_date between '$fromDate' and '$toDate') 
                and document_no like 'RCT%' and (verification_status = 'verified' or verification_status = 'approved') and branch_id = $request->branch_id 
                group by date(trans_date)
            ");

            $fraudTransQuery = DB::select("
            select date(trans_date) as date,sum(amount) as rcts from wa_debtor_trans 
            where (trans_date between '$fromDate' and '$toDate') 
            and document_no like 'FJ%' and branch_id = $request->branch_id 
            group by date(trans_date)
        ");

            $period = CarbonPeriod::create($fromDate, $toDate);
            $days = [];
            foreach ($period as $date) {
                $day = $date->format('Y-m-d');
                $salesDate = Carbon::parse($day)->subDay()->toDateString();
                $startOfDayTomorrow = Carbon::parse($day)->addDay()->startOfDay();

                $sales = collect($salesQuery)->where('date', $salesDate)->first()?->sales ?? 0;
                $todaySales = collect($todaySalesQuery)->where('date', $day)->first()?->sales ?? 0;
                $returns = collect($returnsQuery)->where('date', $day)->first()?->returns ?? 0;
                $vooma = collect($voomaQuery)->where('date', $day)->first()?->rcts ?? 0;
                $vooma = abs($vooma);
                $eazzy = collect($eazzyQuery)->where('date', $day)->first()?->rcts ?? 0;
                $eazzy = abs($eazzy);
                $equity = collect($equityMainQuery)->where('date', $day)->first()?->rcts ?? 0;
                $equity = abs($equity);
                $kcb = collect($kcbMainQuery)->where('date', $day)->first()?->rcts ?? 0;
                $kcb = abs($kcb);
                $verified = collect($verificationQuery)->where('date', $day)->first()?->rcts ?? 0;
                $verified = abs($verified);
                $fraudTrans = collect($fraudTransQuery)->where('date', $day)->first()?->rcts ?? 0;
                $fraudTrans = abs($fraudTrans);
                $mpesa = collect($mpesaQuery)->where('date', $day)->first()?->rcts ?? 0;
                $mpesa = abs($mpesa);

                $netSales = $sales;
                $totalReceipts = $eazzy + $equity + $vooma + $kcb + $mpesa;
                $unverifiedReceipts = $totalReceipts - $verified;
                $debtorsVariance = $netSales - $verified - $returns - $fraudTrans;

                $todayBalance = DB::table('wa_debtor_trans')->where('trans_date', '<', $startOfDayTomorrow)
                    ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                    ->join('routes', function ($join) use ($request) {
                        $join->on('wa_customers.route_id', '=', 'routes.id')
                            ->where('restaurant_id', $request->branch_id);
                    })
                    ->sum('amount');

                $runningBalance = $todayBalance - $todaySales;

                $approved = DB::table('wa_debtor_trans')->whereDate('trans_date', '=', $day)
                    ->where('verification_status', 'approved')
                    ->where('branch_id', $request->branch_id)
                    ->sum('amount');

                $status = (abs($approved) == ($totalReceipts + $fraudTrans)) ? 'Closed' : 'Pending';

                $days[] = [
                    'date' => $day,
                    'branch' => $request->branch_id,
                    'sales' => $netSales,
                    'returns' => $returns,
                    'vooma' => $vooma,
                    'eazzy' => $eazzy,
                    'equity' => $equity,
                    'kcb' => $kcb,
                    'mpesa' => $mpesa,
                    'total_receipts' => $totalReceipts,
                    'verified_receipts' => $verified,
                    'variance' => $unverifiedReceipts,
                    'debtors_variance' => $debtorsVariance,
                    'fraud' => $fraudTrans,
                    'running_balance' => $runningBalance,
                    'status' => $status
                ];
            }

            $sales = collect($days)->map(function ($record) {
                $record['sales'] = manageAmountFormat($record['sales']);
                $record['returns'] = manageAmountFormat($record['returns']);
                $record['vooma'] = manageAmountFormat($record['vooma']);
                $record['eazzy'] = manageAmountFormat($record['eazzy']);
                $record['equity'] = manageAmountFormat($record['equity']);
                $record['kcb'] = manageAmountFormat($record['kcb']);
                $record['mpesa'] = manageAmountFormat($record['mpesa']);
                $record['total_receipts'] = manageAmountFormat($record['total_receipts']);
                $record['verified_receipts'] = manageAmountFormat($record['verified_receipts']);
                $record['variance'] = manageAmountFormat($record['variance']);
                $record['debtors_variance'] = manageAmountFormat($record['debtors_variance']);
                $record['fraud'] = manageAmountFormat($record['fraud']);
                $record['running_balance'] = manageAmountFormat($record['running_balance']);

                return $record;
            });

            $branchId = $request->branch_id;
            $summary =  DB::table('payment_methods as pm')
                ->select(
                    'pm.title as collection_account',
                    'sweep_account.title as sweep_account',

                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks  as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) = date(debtors.trans_date)
                    ) as same_day_collections"),

                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) != date(debtors.trans_date)
                    ) as late_utilizations"),

                    DB::raw("(select abs(sum(debtors.amount))
                        from wa_debtor_trans as debtors
                        left join payment_verification_banks as statement on statement.matched_debtors_id = debtors.id
                        where debtors.wa_payment_method_id = pm.id
                        and debtors.branch_id = '$branchId'
                        and (debtors.trans_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) != date(debtors.trans_date)
                    ) as utilized_unknowns"),

                    DB::raw("(select sum(statement.amount)
                        from payment_verification_banks as statement
                        join wa_debtor_trans as debtors on debtors.id = statement.matched_debtors_id
                        where statement.payment_method_id = pm.id
                        and date(statement.bank_date between '$fromDate' and '$endDate')
                        and date(statement.bank_date) != date(debtors.trans_date) 
                    ) as utilized_unknowns"),

                    DB::raw("(select sum(payment_verification_banks.amount) from payment_verification_banks 
                        where payment_verification_banks.status = 'Pending' 
                        and payment_verification_banks.amount > 0
                        and (bank_date between '$fromDate' and '$endDate')
                        and payment_verification_banks.payment_method_id = pm.id
                    ) as actual_unknowns"),

                    DB::raw("(select sum(amount) from payment_verification_banks 
                        where amount < 0
                        and (bank_date between '$fromDate' and '$endDate')
                        and payment_method_id = pm.id
                    ) as sweep_total"),
                )
                ->join('payment_methods as sweep_account', 'pm.sweep_account_id', '=', 'sweep_account.id')
                ->where('pm.branch_id', $request->branch_id)
                ->where('pm.account_type', 1)
                ->get()
                ->map(function ($record) {
                    $record->formatted_same_day_collections = manageAmountFormat($record->same_day_collections);
                    $record->formatted_late_utilizations = manageAmountFormat($record->late_utilizations);

                    $record->total_collection = $record->same_day_collections + $record->late_utilizations;
                    $record->formatted_total_collection = manageAmountFormat($record->total_collection);

                    $record->formatted_utilized_unknowns = manageAmountFormat($record->utilized_unknowns);
                    $record->formatted_actual_unknowns = manageAmountFormat($record->actual_unknowns);

                    $record->total_unknowns = $record->utilized_unknowns + $record->actual_unknowns;
                    $record->formatted_total_unknowns = manageAmountFormat($record->total_unknowns);

                    $record->nominal_total = $record->same_day_collections + $record->total_unknowns;
                    $record->formatted_nominal_total = manageAmountFormat($record->nominal_total);
                   
                    $record->formatted_sweep_total = manageAmountFormat(abs($record->sweep_total));
                    $record->variance = abs($record->sweep_total) - $record->nominal_total;
                    $record->formatted_variance = manageAmountFormat($record->variance);

                    return $record;
                });
          
            $pdf = Pdf::loadView('banking_approval.partials.route.banking_summary_pdf', compact('sales', 'user', 'branch', 'date', 'summary',))->setPaper('a4', 'landscape');

            return $pdf->download('Route_Sales_banking_overview' . $request->date . '.pdf');
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }


}
