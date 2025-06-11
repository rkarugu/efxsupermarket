<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\WaBankAccount;
use App\Model\WaChartsOfAccount;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaSupplier;
use App\Models\GlReconcile;
use App\Models\GlReconcileInterestExpense;
use App\Models\GlReconStatement;
use App\Models\PaymentVerificationBank;
use App\Models\WaBankFile;
use App\Models\WaBankFileItem;
use App\PaymentVoucher;
use App\PaymentVoucherCheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Model\WaNumerSeriesCode;
use App\Model\WaBanktran;
use App\Model\WaAccountingPeriod;
use Illuminate\Support\Carbon;
class GlReconciliationController extends Controller
{
    protected $model;
    protected $title;

    public function __construct() {
        $this->model = 'gl_reconciliation';
        $this->title = 'General Ledgers';
    }

    public function overview()
    {
        if (!can('overview', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = 'GL Reconcile Overview';
        $model = $this->model.'-overview';
        $breadcum = ['General Ledgers' => '', $title => ''];

        $accounts = DB::table('gl_reconciles')
                    ->join('wa_charts_of_accounts','wa_charts_of_accounts.id','gl_reconciles.bank_account_id')
                    ->select('wa_charts_of_accounts.account_code')
                    ->distinct()
                    ->get();

        if (request()->wantsJson()) 
        {
            $debtorTrans = "select sum(wa_debtor_trans.amount) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and channel=channel";
            $query = GlReconcile::with('bankAccount')
                        ->select(
                            'gl_reconciles.*',
                            DB::raw("(select title from payment_methods where gl_account_id=gl_reconciles.bank_account_id  ) as payment_channel"),
                            DB::raw("(select sum(amount) from payment_vouchers where wa_bank_account_id=gl_reconciles.bank_account_id and gl_reconcile_id = gl_reconciles.id and status = 1 ) as paymet_voucher_sum "),
                            DB::raw("(select ABS(sum(wa_debtor_trans.amount)) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_amount"),
                            DB::raw("(select count(wa_debtor_trans.amount) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_count"),
                            DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount < 0) as unknown_banking_debit"),
                            DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount > 0) as unknown_banking_credit"),
                            DB::raw("(select count(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_count"),
                            DB::raw("(select sum(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_amount")
                        )
                        ->orderBy('created_at','desc');

                        if (request()->start_date && request()->end_date) {
                            $query->whereDate('start_date', '>=', request()->start_date.' 00:00:00')->whereDate('end_date', '<=', request()->end_date. ' 23:59:59');
                        }

                        if(request()->account != 'all'){
                            $query->whereHas('bankAccount', function ($q) {
                                $q->where('account_code', request()->account);
                            });
                        }

            return DataTables::eloquent($query)
                ->editColumn('bank_account.account_name',function($query){
                    return $query->bankAccount->account_name .'('.$query->bankAccount->account_code.')';
                })
                ->editColumn('beginning_balance', function($query){
                    return manageAmountFormat(abs($query->beginning_balance));
                })
                ->editColumn('ending_balance', function($query){
                    return manageAmountFormat(abs($query->ending_balance));
                })
                ->addColumn('matched',function($query){
                    return number_format($query->matched_count).'('.manageAmountFormat($query->matched_amount).')';
                })
                ->addColumn('missing_trans',function($query){
                    return number_format($query->missing_banks_count).'('.manageAmountFormat($query->missing_banks_amount).')';
                })
                ->addColumn('unknown_bankings',function($query){
                    $unknown_banking_debit = $query->unknown_banking_debit ?? 0;
                    $total = $query->unknown_banking_credit + $unknown_banking_debit;
                    return manageAmountFormat($total);
                })
                ->addColumn('variance',function($query){
                    
                    $beginningBalance = abs($query->beginning_balance);

                    // Calculate the sum of unpresented cheques
                    $unpresentedCheque = $query->paymet_voucher_sum ?? 0;

                    // Calculate the sum of uncredited items
                    $uncreditedItems = $query->missing_banks_amount;

                    // Total cash in bank calculation
                    $totalCashBank = ($beginningBalance + $unpresentedCheque) - $uncreditedItems;

                    $endingBalance = $query->ending_balance;
                    $c6 = $totalCashBank-$endingBalance;
                    
                    // Calculate the total from bank statements
                    $unknown_banking_debit = $query->unknown_banking_debit ?? 0;
                    $totalBankStatement = $query->unknown_banking_credit - $unknown_banking_debit;

                    // Calculate the variance
                    $variance = manageAmountFormat($c6 + abs($totalBankStatement));

                    return $variance;
                })
                ->setRowClass(function ($query) {
                    if ($query->status == 'pending') {
                        return 'alert-danger';
                    }
                })
                ->toJson();
        }
        
        return view('admin.gl_reconciliation.overview', compact('title', 'model','breadcum','accounts'));

    }

    public function list(Request $request)
    {
        if (!can('view', $this->model)) {
           return returnAccessDeniedPage();
        }

        $title = 'GL reconciliation';
        $model = $this->model;
        $breadcum = ['General Ledgers' => '', $title => ''];

        if (request()->wantsJson()) 
        {
            $query = GlReconcile::with('bankAccount')
            ->select(
                'gl_reconciles.*',
                DB::raw("(select title from payment_methods where gl_account_id=gl_reconciles.bank_account_id  ) as payment_channel"),
                DB::raw("(select sum(amount) from payment_vouchers where wa_bank_account_id=gl_reconciles.bank_account_id and gl_reconcile_id = gl_reconciles.id and status = 1 ) as paymet_voucher_sum "),
                DB::raw("(select ABS(sum(wa_debtor_trans.amount)) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_amount"),
                DB::raw("(select count(wa_debtor_trans.amount) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_count"),
                DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount < 0) as unknown_banking_debit"),
                DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount > 0) as unknown_banking_credit"),
                DB::raw("(select count(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_count"),
                DB::raw("(select sum(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_amount")
            );
            if ($request->status =='closed') {
                $query->where('status','closed')->orderBy('closed_on','desc');
            } else {
                $query->where('status','pending')->orderBy('created_at','desc');
            }
            
            return DataTables::eloquent($query)
                ->editColumn('bank_account.account_name',function($query){
                    return $query->bankAccount->account_name .'('.$query->bankAccount->account_code.')';
                })
                ->editColumn('beginning_balance', function($query){
                    return manageAmountFormat(abs($query->beginning_balance));
                })
                ->editColumn('ending_balance', function($query){
                    return manageAmountFormat(abs($query->ending_balance));
                })
                ->addColumn('variance',function($query){
                    $beginningBalance = abs($query->beginning_balance);

                    // Calculate the sum of unpresented cheques
                    $unpresentedCheque = $query->paymet_voucher_sum ?? 0;

                    // Calculate the sum of uncredited items
                    $uncreditedItems = $query->missing_banks_amount;

                    // Total cash in bank calculation
                    $totalCashBank = ($beginningBalance + $unpresentedCheque) - $uncreditedItems;

                    $endingBalance = $query->ending_balance;
                    $c6 = $totalCashBank-$endingBalance;
                    
                    // Calculate the total from bank statements
                    $unknown_banking_debit = $query->unknown_banking_debit ?? 0;
                    $totalBankStatement = $query->unknown_banking_credit - $unknown_banking_debit;

                    // Calculate the variance
                    $variance = manageAmountFormat($c6 + abs($totalBankStatement));

                    return $variance;
                })
                ->toJson();
        }
        
        return view('admin.gl_reconciliation.list', compact('title', 'model','breadcum'));
    }

    public function create()
    {
        if (!can('create', $this->model)) {
            return returnAccessDeniedPage();
        }

        
 
        $title = 'GL Reconcile';
        $model = $this->model;
        $breadcum = ['General Ledgers' => '', $title => ''];

        $bankAccounts = WaChartsOfAccount::where('wa_account_sub_section_id',5)->get();//WaBankAccount::get();
    
        $accounts = WaChartsOfAccount::with('getRelatedGroup','getRelatedGroup.getAccountSection')->get();

        return view('admin.gl_reconciliation.create', compact('title', 'model','breadcum','bankAccounts','accounts'));
    }

    public function store(Request $request)
    {
        if (!can('create', $this->model)) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            $channel = DB::table('payment_methods')
                        ->select('payment_methods.*')
                        ->join('wa_charts_of_accounts','wa_charts_of_accounts.id','payment_methods.gl_account_id')
                        ->where('wa_charts_of_accounts.account_code',$request->bank_account)
                        ->first();

            $beginBalance = DB::table('wa_gl_trans')->where('created_at', '<', $request->start_date)
                        ->where('account',$request->bank_account)->sum('amount');

            $bankStatements = PaymentVerificationBank::select('id','reference','bank_date','status','channel','gl_reconcile_id','gl_recon_statement_id',DB::raw('ABS(amount) as amount'))
                            ->whereNull('gl_reconcile_id')
                            ->where('channel',$channel->title)
                            ->get();
            
            $bankStatements = collect($bankStatements);

            $chartofaccount = WaChartsOfAccount::where('account_code',$request->bank_account)->first();
            
            $reconcile = GlReconcile::create([
                'created_by' => Auth::user()->id,
                'bank_account_id' => $chartofaccount->id,
                'beginning_balance' => $beginBalance,
                'ending_balance' => str_replace( ',', '', $request->ending_balance ),
                'start_date' => $request->start_date,
                'end_date' => $request->ending_date,
                'status' => 'pending'
            ]);

            if ($request->expense_date) {
                foreach ($request->expense_date as $key => $value) {
                    if($request->expense_charge[$key] != 0){
                        $amount = str_replace( ',', '', $request->expense_charge[$key]); // Debit: Equity Main Credit: Selected Account
                        $series_module = WaNumerSeriesCode::where('module', 'GL_RECON_CHARGES')->first();
                        $lastNumberUsed = $series_module->last_number_used;
                        $newNumber = (int)$lastNumberUsed + 1;
                        $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
                        $series_module->update(['last_number_used' => $newNumber]);
                        $expense = GlReconcileInterestExpense::create([
                            'gl_reconcile_id' => $reconcile->id,
                            'type' => 'Expense',
                            'date' => $value,
                            'amount' => $amount,
                            'chart_of_account_id' => $request->expense_account[$key],
                            'reference' => $request->expense_reference[$key],
                            'document_no' => $newCode
                        ]);

                        $filteredStatements = $bankStatements->filter(function($statement) use ($request, $key, $amount) {
                            return str_contains($statement->reference, $request->expense_reference[$key]) && 
                                    (float)abs($statement->amount) == (float)abs($amount);
                        });
                        
                        // Update the filtered statements
                        $filteredStatements->each(function($statement) use ($reconcile, $expense) {
                            $matchedStatement = new GlReconStatement;
                            $matchedStatement->reference = $statement->reference;
                            $matchedStatement->gl_reconcile_id = $reconcile->id;
                            $matchedStatement->bank_id = $statement->id;
                            $matchedStatement->current_status = 'pending';
                            $expense->statement()->save($matchedStatement);
                            $statement->update([
                                'gl_reconcile_id' => $reconcile->id,
                                'gl_recon_statement_id' => $matchedStatement->id,
                            ]);
                        });

                        // Remove matched statements from the collection to prevent reuse
                        $bankStatements = $bankStatements->diff($filteredStatements);

                    }
                }
            }

            if ($request->income_date) {
                foreach ($request->income_date as $key => $value) {
                    if ($request->income_earned[$key] !=0) {
                        $amount = str_replace( ',', '', $request->income_earned[$key]);  // Credit: Equity Main Debit: Selected Account
                        $series_module = WaNumerSeriesCode::where('module', 'GL_RECON_INTERESTS')->first();
                        $lastNumberUsed = $series_module->last_number_used;
                        $newNumber = (int)$lastNumberUsed + 1;
                        $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
                        $series_module->update(['last_number_used' => $newNumber]);
                        
                        $interest = GlReconcileInterestExpense::create([
                            'gl_reconcile_id' => $reconcile->id,
                            'type' => 'Interest',
                            'date' => $value,
                            'amount' => $amount,
                            'chart_of_account_id' => $request->income_account[$key],
                            'reference' => $request->income_reference[$key],
                            'document_no' => $newCode
                        ]);

                        $filteredStatements = $bankStatements->filter(function($statement) use ($request, $key, $amount) {
                            return str_contains($statement->reference, $request->income_reference[$key]) && 
                                (float)abs($statement->amount) == (float)abs($amount);
                        });
                   
                        // Update the filtered statements
                        $filteredStatements->each(function($statement) use ($reconcile, $interest) {
                            $matchedStatement = new GlReconStatement;
                            $matchedStatement->reference = $statement->reference;
                            $matchedStatement->gl_reconcile_id = $reconcile->id;
                            $matchedStatement->bank_id = $statement->id;
                            $matchedStatement->current_status = 'pending';
                            $interest->statement()->save($matchedStatement);
                            $statement->update([
                                'gl_reconcile_id' => $reconcile->id,
                                'gl_recon_statement_id' => $matchedStatement->id,
                            ]);
                        });

                        // Remove matched statements from the collection to prevent reuse
                        $bankStatements = $bankStatements->diff($filteredStatements);
                    }                
                }
            }

            // payment_verification_banks; ONLY FILLED ON MATCHING
            $filteredStatements = $bankStatements->filter(function ($statement) use ($request) {
                $startDate = $request->start_date . ' 00:00:00';
                $endDate = $request->ending_date . ' 23:59:59';
                return $statement->bank_date >= $startDate && $statement->bank_date <= $endDate;
            });
            
            // DebtorTrans
            WaDebtorTran::whereBetween('trans_date', [$request->start_date, $request->ending_date])
                ->whereNull('gl_reconcile_id')
                ->where('document_no', 'like', 'RCT%')
                ->where('channel',$channel->title)
                ->update([
                    'gl_reconcile_id' => $reconcile->id
                ]);
            WaGlTran::whereBetween('created_at', [$request->start_date, $request->ending_date])
                    ->whereNull('gl_reconcile_id')
                    ->where('account', $request->bank_account)
                    ->update([
                        'gl_reconcile_id' => $reconcile->id
                    ]);
                    

            DB::commit();
            Session::flash('success', 'Reconciliation created Successfully');
            return redirect(route('gl-reconciliation.view',$reconcile->id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function update(Request $request,$id)
    {
        if (!can('edit', $this->model)) {
            return returnAccessDeniedPage();
        }

        DB::beginTransaction();
        try {
            
            $reconcile = GlReconcile::find($id);
            $reconcile->ending_balance = str_replace( ',', '', $request->ending_balance );
            $reconcile->end_date = $request->ending_date;
            $reconcile->start_date = $request->start_date;
            
            GlReconcileInterestExpense::where('gl_reconcile_id',$id)->delete();
            foreach ($request->expense_date as $key => $value) {
                if($request->expense_charge[$key] != 0 || $request->expense_charge[$key] !=''){
                    GlReconcileInterestExpense::create([
                        'gl_reconcile_id' => $reconcile->id,
                        'type' => 'Expense',
                        'date' => $value,
                        'amount' => str_replace( ',', '', $request->expense_charge[$key]) * -1,
                        'chart_of_account_id' => $request->expense_account[$key],
                        'reference' => $request->expense_reference[$key],
                    ]);
                }
            }

            foreach ($request->income_date as $key => $value) {
                if($request->income_earned[$key] != 0 || $request->income_earned[$key] !=''){
                    GlReconcileInterestExpense::create([
                    'gl_reconcile_id' => $reconcile->id,
                    'type' => 'Interest',
                    'date' => $value,
                    'amount' => str_replace( ',', '', $request->income_earned[$key]),
                    'chart_of_account_id' => $request->income_account[$key],
                    'reference' => $request->income_reference[$key],
                    ]);
                }
            }

            DB::commit();
            Session::flash('success', 'Reconciliation created Successfully');
            return redirect(route('gl-reconciliation.view',$reconcile->id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function view($id)
    {
        if (!can('show', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = 'GL Reconcile Info';
        $model = $this->model;
        $breadcum = ['GL Reconcile' => '', $title => ''];

        $data = GlReconcile::with('extras','bankAccount','extras.chartOfAccount')->find($id);
        $channel = DB::table('payment_methods')
                            ->where('gl_account_id',$data->bank_account_id)
                            ->first();
        $missingInBank = DB::table('wa_debtor_trans')
                            ->select(
                                'wa_debtor_trans.id as debtors_id',
                                'reference',
                                'document_no',
                                'channel',
                                'trans_date',
                                'customer_name',
                                'verification_status',
                                'amount'
                            )
                            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                            ->where('document_no', 'like', 'RCT%')
                            ->where('verification_status', '!=','Approved')
                            ->where('gl_reconcile_id', $data->id)
                            ->where('channel',$channel->title)
                            ->get();
        
        
        $vouchers = PaymentVoucher::with('supplier', 'account', 'paymentMode', 'voucherItems.transaction')
            ->withCount('voucherItems')
            ->where('wa_bank_account_id',$data->bank_account_id)
            ->where('gl_reconcile_id', $data->id)
            ->where('status',1)
            ->get();

        $supplierKeywords = [
            'contra',
            'inward cheque',
            'AA Loan'
        ];
        // $paymentBanks = PaymentVerificationBank::where('amount','!=',0)
        //                 ->select('id','reference','amount','channel','bank_date','type','status')
        //                 ->where('gl_reconcile_id', $data->id)
        //                 ->get();
        // $paymentBanks = collect($paymentBanks);
        // $paymentBanks = $paymentBanks->filter(function ($payment) use ($supplierKeywords) {
        //     foreach ($supplierKeywords as $keyword) {
        //         if (stripos($payment->reference, $keyword) !== false) {
        //             return true; // If the reference contains any of the keywords, keep this item
        //         }
        //     }
        //     return false; // Otherwise, filter it out
        // });

        $unknownBankings = DB::table('payment_verification_banks')
            ->select(
                'id',
                'reference',
                'original_reference',
                'amount',
                'bank_date',
                'channel',
                'type',
                'status',
                'trans_ref',
            )
            ->where('status', 'Pending')
            ->whereNull('gl_reconcile_id')
            ->where('channel',$channel->title)
            ->whereBetween('bank_date', [$data->start_date . ' 00:00:00', $data->end_date . ' 23:59:59'])
            ->get();
        
        $bankStatements=[];

        foreach ($unknownBankings as $value) {
            if(str_contains(strtolower($value->reference), 'bulk payments') || 
                str_contains(strtolower($value->reference), 'inward cheque')){
                $bankStatements['payments'][]=$value;
            } elseif (str_contains(strtolower($value->reference), 'aa loan')) {
                $bankStatements['loans'][]=$value;
            } elseif (str_contains(strtolower($value->reference), 'taxpmt')) {
                $bankStatements['tax'][]=$value;
            } elseif (str_contains(strtolower($value->reference), 'inhouse')) {
                $bankStatements['cheque payments'][]=$value;
            } elseif (str_contains(strtolower($value->reference), 'charges')) {
                $bankStatements['charges'][]=$value;
            } else{
                $bankStatements['unknowns'][]=$value;
            }
            
        }

        // dd($bankStatements);
        
        return view('admin.gl_reconciliation.view', compact('title', 'model','breadcum','data','missingInBank','unknownBankings','vouchers','bankStatements'));
    }

    public function edit($id)
    {
        if (!can('edit', $this->model)) {
            return returnAccessDeniedPage();
        }
 
        $title = 'Edir GL Reconcile';
        $model = $this->model;
        $breadcum = ['General Ledgers' => '', $title => ''];

        $bankAccounts = WaBankAccount::get();
        $expenses = WaChartsOfAccount::with(['getRelatedGroup', 'getRelatedGroup.getAccountSection'])
            ->whereHas('getRelatedGroup.getAccountSection', function ($query) {
                $query->where('section_name', 'EXPENSES');
            })
            ->get();
    
        $incomes = WaChartsOfAccount::with('getRelatedGroup','getRelatedGroup.getAccountSection')->get();
        $data = GlReconcile::with('extras','bankAccount','extras.chartOfAccount')->find($id);

        return view('admin.gl_reconciliation.edit', compact('title', 'model','breadcum','bankAccounts','expenses','incomes','data'));
    }

    public function get_bank_balance(Request $request){
        if(!$request->ajax()){
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $validator = Validator::make($request->all(),[
            'account'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'result' => 0,
                'errors'=>$validator->errors(),
            ]);
        }
        
        try {
            // $bank = WaBankAccount::where('id',$request->account)->first();
            // $chartofaccount = WaChartsOfAccount::where('id',$bank->bank_account_gl_code_id)->first();
            $query = DB::table('wa_gl_trans');
            if ($request->start_date) {
                $query->where('created_at', '<', $request->start_date);
                // $query->whereBetween('trans_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }
            $bank_trans = $query->where('account',$request->account)->sum('amount');
            return response()->json([
                'result' => 1,
                'message' => 'Bank Balance',
                'amount'=>$bank_trans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => -1,
                'message' => $e->getMessage()
            ]);
        }
        
    }

    public function payment_datatable($id)
    {
        if (request()->wantsJson()) 
        {
            $query = GlReconcile::find($id);
            $channel = DB::table('payment_methods')
                            ->where('gl_account_id',$query->bank_account_id)
                            ->first();
            $payments = WaDebtorTran::with('customerDetail')
                        ->where('document_no','like','RCT%')
                        ->where('verification_status', '!=','Approved')
                        ->where('gl_reconcile_id', $query->id)
                        ->where('channel',$channel->title);
            return DataTables::eloquent($payments)
                ->editColumn('trans_date', function($data){
                    return date('d-m-Y', strtotime($data->trans_date));
                })
                ->editColumn('amount',function($payment){
                    return manageAmountFormat(abs($payment->amount));
                })
                ->with('total', function () use($payments) {
                    $amount = $payments->get();
                    return manageAmountFormat($amount->sum('amount'));
                })
                
                ->toJson();
        }
    }

    public function begin_balance_datatable($id)
    {
        $query = GlReconcile::find($id);
        $openingBalance = WaGlTran::query()
            ->where('account', $query->bankAccount->account_code)
            ->where('created_at', '<', $query->start_date)
            ->sum('amount');

        $runningBalance = $openingBalance;
        $data = WaGlTran::with('branch')
            ->where('account', $query->bankAccount->account_code)
            ->where('gl_reconcile_id', $query->id)
            ->whereNull('gl_recon_statement_id')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction->amount;
                $transaction->running_balance = $runningBalance;
                return $transaction;
            });

        if (request()->wantsJson()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($date) {
                    return date('Y-m-d H:i:s', strtotime($date->created_at));
                })
                ->addColumn('debit', function ($item) {
                    return $item->amount > 0 ? manageAmountFormat($item->amount) : '';
                })
                ->addColumn('credit', function ($item) {
                    return $item->amount < 0 ? manageAmountFormat($item->amount) : '';
                })    
                ->editColumn('running_balance',function($record){
                    return manageAmountFormat($record->running_balance);
                })    
                ->with('opening_balance', function () use ($openingBalance) {
                    return manageAmountFormat($openingBalance);
                })        
                ->with('closing_balance', function () use($data,$openingBalance) {
                    return manageAmountFormat($openingBalance + $data->sum('amount'));
                })
                ->with('debitCreditTotal', function () use ($data) {
                    $debit = 0;
                    $credit = 0;
                    foreach ($data as $item) {
                        if ($item->amount > 0) {
                            $debit = $debit + $item->amount;
                        } else {
                            $credit = $credit + $item->amount;
                        }
                    }
                    return ['debit' => manageAmountFormat($debit), 'credit' => manageAmountFormat($credit)];
                })
                ->editColumn('branch.name', function ($data) {
                    $branch = '-';
                    if ($data->branch) {
                        $branch = $data->branch->name;
                    }
                    return $branch;
                })
                ->toJson();

        }
    }

    public function matched_datatable($id)
    {
        
        $matches = GlReconStatement::with('matched','bankStatement')
                    ->select('gl_recon_statements.*')            
                    ->where('gl_reconcile_id', $id);

        return DataTables::of($matches)
                ->addIndexColumn()
                ->editColumn('created_at', function ($date) {
                    return date('Y-m-d H:i:s', strtotime($date->created_at));
                })
                ->editColumn('bank_statement.amount', function ($data) {
                    return manageAmountFormat(abs($data->bankStatement->amount));
                })
                ->addColumn('document_no', function ($data){
                    $doc = '-';

                    if ($data->matched->number) {
                        $doc = $data->matched->number;
                    }
                    if ($data->matched->document_no) {
                        $doc = $data->matched->document_no;
                    }
                    return $doc;
                })
                ->toJson();
    }

    public function re_verify($id)
    {  
        if (!can('re-verify', $this->model)) {
            return returnAccessDeniedPage();
        }

        $data = GlReconcile::with(['extras', 'bankAccount', 'extras.chartOfAccount'])->findOrFail($id);
        $channel = DB::table('payment_methods')
                            ->where('gl_account_id',$data->bank_account_id)
                            ->first();
        $openingBalance = WaGlTran::query()
            ->where('account', $data->bankAccount->account_code)
            ->where('created_at', '<', $data->start_date)
            ->sum('amount');
        if($data->beginning_balance != $openingBalance){
            $data->update([
                'beginning_balance' => $openingBalance,
            ]);
        }
        
        $accounts = WaGlTran::where('gl_reconcile_id', $data->id)
                    ->whereNull('gl_recon_statement_id')
                    ->get();
        
        // Fetch and collect bank statements only once
        $bankStatements = PaymentVerificationBank::select('id', 'reference', 'bank_date', 'status', 'channel', 'gl_reconcile_id', 'gl_recon_statement_id', DB::raw('ABS(amount) as amount'))
            ->whereNull('gl_reconcile_id')
            ->where('channel',$channel->title)
            ->get()
            ->keyBy('id');

        DB::beginTransaction();
        try {
            // Process Payment Vouchers
            foreach ($accounts as $value) {
                $transNo = explode('-', $value->transaction_no);
                if ($transNo[0] === 'PMV') {
                    $paymentVoucher = PaymentVoucher::where('number', $value->transaction_no)->where('wa_bank_account_id',$data->bank_account_id)->first();
                    if ($paymentVoucher) {
                        $voucherid = $paymentVoucher->id;
                        $voucherAmount = $paymentVoucher->amount;
                        $voucherRef = str_replace('-', '', $value->transaction_no);

                        $filteredVouchers = $bankStatements->first(function ($statement) use ($voucherid, $voucherAmount, $voucherRef) {
                            return (
                                str_contains($statement->reference, $voucherid) || 
                                str_contains($statement->reference, $voucherRef)
                            ) && $statement->amount == $voucherAmount;
                        });

                        if ($filteredVouchers) {
                            $voucher = PaymentVoucher::find($voucherid);
                            $matchedStatement = new GlReconStatement;
                            $matchedStatement->reference = $filteredVouchers->reference;
                            $matchedStatement->gl_reconcile_id = $id;
                            $matchedStatement->bank_id = $filteredVouchers->id;
                            $matchedStatement->current_status = 'pending';
                            // Update Voucher With Matched Statement
                            $voucher->glMatched()->save($matchedStatement);
                            // Update Bank Statement with the matched Statement
                            $filteredVouchers->update([
                                'gl_reconcile_id' => $id,
                                'gl_recon_statement_id' => $matchedStatement->id,
                            ]);
                            // Update GL with The matched Statement
                            DB::table('wa_gl_trans')
                                ->where('id', $value->id)
                                ->update(['gl_recon_statement_id' => $matchedStatement->id]);

                            // Update the Voucher Cheques
                            foreach ($paymentVoucher->cheques as $cheque) {
                                $voucherCheque = PaymentVoucherCheque::find($cheque->payment_voucher_id);
                                $voucherCheque->update([
                                    'gl_reconcile_id' => $id,
                                    'gl_recon_statement_id' => $matchedStatement->id,
                                ]);
                            }

                            // Remove matched statement
                            $bankStatements->forget($filteredVouchers->id);
                        } else {
                            
                            foreach ($paymentVoucher->cheques as $cheque) {
                                if ($cheque->gl_recon_statement_id == NULL) {
                                    
                                    $chequeAmount = $cheque->amount;
                                    $chequeRef = $cheque->number;
                                    $chequeRef2 = str_replace('-', '', $cheque->number);

                                    // Filter matched Cheques from Bank statement
                                    $filteredCheque = $bankStatements->first(function ($statement) use ($chequeRef, $chequeAmount, $chequeRef2) {
                                        return (
                                            str_contains($statement->reference, $chequeRef) || 
                                            str_contains($statement->reference, $chequeRef2)
                                        ) && (float)abs($statement->amount) == (float)abs($chequeAmount);
                                    });

                                    if ($filteredCheque) {
                                        $voucherCheque = PaymentVoucherCheque::find($cheque->id);;
                                        $matchedStatement = new GlReconStatement;
                                        $matchedStatement->reference = $filteredCheque->reference;
                                        $matchedStatement->gl_reconcile_id = $id;
                                        $matchedStatement->bank_id = $filteredCheque->id;
                                        $matchedStatement->current_status = 'pending';
                                        // Update Voucher Cheque With Matched Statement
                                        $voucherCheque->glMatched()->save($matchedStatement);
                                        // Update Bank Statement with the matched Statement
                                        $filteredCheque->update([
                                            'gl_reconcile_id' => $id,
                                            'gl_recon_statement_id' => $matchedStatement->id,
                                        ]);
                                        // Update GL with The matched Statement
                                        // DB::table('wa_gl_trans')
                                        //     ->where('id', $value->id)
                                        //     ->update(['gl_recon_statement_id' => $matchedStatement->id]);
            
                                        // Remove matched statement
                                        $bankStatements->forget($filteredCheque->id);
                                    } 
                                }                                
                            }
                        }
                    }
                }
            }

            // Process Debtors
            $debtors = WaDebtorTran::with('verificationBank')
                ->where('gl_reconcile_id', $id)
                ->where('document_no', 'like', 'RCT%')
                ->where('verification_status', 'Approved')
                ->where('channel',$channel->title)
                ->get();

            foreach ($debtors as $debtor) {
                $bankData = $bankStatements->get($debtor->verificationBank->id);

                if ($bankData) {
                    $debtordata = WaDebtorTran::find($debtor->id);
                    $matchedStatement = new GlReconStatement;
                    $matchedStatement->reference = $bankData->reference;
                    $matchedStatement->gl_reconcile_id = $id;
                    $matchedStatement->bank_id = $bankData->id;
                    $matchedStatement->current_status = 'pending';

                    $debtordata->glMatched()->save($matchedStatement);

                    $bankData->update([
                        'gl_reconcile_id' => $id,
                        'gl_recon_statement_id' => $matchedStatement->id,
                    ]);

                    // Remove matched statement
                    $bankStatements->forget($bankData->id);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        Session::flash('success', 'Re-Recon Complete.');
        return redirect(route('gl-reconciliation.view', $id));
    }

    public function close_recon(Request $request,$id)
    {
        if (!can('re-verify', $this->model)) {
            return returnAccessDeniedPage();
        }
        

        $query = GlReconcile::with('bankAccount')
            ->select(
                'gl_reconciles.*',
                DB::raw("(select title from payment_methods where gl_account_id=gl_reconciles.bank_account_id  ) as payment_channel"),
                DB::raw("(select sum(amount) from payment_vouchers where wa_bank_account_id=gl_reconciles.bank_account_id and gl_reconcile_id = gl_reconciles.id and status = 1 ) as paymet_voucher_sum "),
                DB::raw("(select ABS(sum(wa_debtor_trans.amount)) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_amount"),
                DB::raw("(select count(wa_debtor_trans.amount) from wa_debtor_trans inner join wa_customers on wa_customers.id = wa_debtor_trans.wa_customer_id and wa_debtor_trans.document_no like 'RCT%' and wa_debtor_trans.channel=payment_channel and wa_debtor_trans.gl_reconcile_id=gl_reconciles.id and wa_debtor_trans.verification_status != 'Approved') as missing_banks_count"),
                DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount < 0) as unknown_banking_debit"),
                DB::raw("(select ABS(sum(payment_verification_banks.amount)) from payment_verification_banks where payment_verification_banks.status ='Pending' and payment_verification_banks.bank_date between CONCAT(gl_reconciles.start_date, ' 00:00:00') and CONCAT(gl_reconciles.end_date, ' 23:59:59') and payment_verification_banks.gl_reconcile_id is null and payment_verification_banks.channel = payment_channel and payment_verification_banks.amount > 0) as unknown_banking_credit"),
                DB::raw("(select count(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_count"),
                DB::raw("(select sum(payment_verification_banks.amount) from gl_recon_statements inner join payment_verification_banks on payment_verification_banks.id =gl_recon_statements.bank_id where gl_recon_statements.gl_reconcile_id  = gl_reconciles.id ) as matched_amount")
            )
            ->findOrFail($id);

            $beginningBalance = abs($query->beginning_balance);

            // Calculate the sum of unpresented cheques
            $unpresentedCheque = $query->paymet_voucher_sum ?? 0;

            // Calculate the sum of uncredited items
            $uncreditedItems = $query->missing_banks_amount;

            // Total cash in bank calculation
            $totalCashBank = ($beginningBalance + $unpresentedCheque) - $uncreditedItems;

            $endingBalance = $query->ending_balance;
            $c6 = $totalCashBank-$endingBalance;
            
            // Calculate the total from bank statements
            $unknown_banking_debit = $query->unknown_banking_debit ?? 0;
            $totalBankStatement = $query->unknown_banking_credit - $unknown_banking_debit;

            // Calculate the variance
            $variance = manageAmountFormat($c6 + abs($totalBankStatement));

            // if($variance != 0){
            //     Session::flash('warning', 'Cannot close Recon. Because of Variance');
            //     return redirect()->back();
            // }
            DB::beginTransaction();
            try {
                $bankPostCode = getCodeWithNumberSeries('BANK_POSTINGS');
                updateUniqueNumberSeries('BANK_POSTINGS', $bankPostCode);
                $statements = GlReconcileInterestExpense::with('chartOfAccount')->where('gl_reconcile_id',$id)->get();
                
                $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                foreach ($statements as $key => $value) {
                    $amount = abs($value->amount);
                    $creditCode = $value->chartOfAccount->account_code;
                    $debitCode = $query->bankAccount->account_code;
                    
                    if($value->type=='Expense'){
                        $creditAmount = $amount;
                        $debitAmount = '-' . $amount;
                    } else {
                        $debitAmount = $amount;
                        $creditAmount = '-' . $amount;
                    }
                    
                    $branch = $value->chartOfAccount->branches->first()->id;
                    $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();

                    $btran = new WaBanktran();
                    $btran->type_number = $series_module->type_number;
                    $btran->document_no = $value->document_no;
                    $btran->bank_gl_account_code = $creditCode;
                    $btran->reference = $query->start_date.' - '.$query->end_date .' / '.$value->document_no;
                    $btran->trans_date = Carbon::now();
                    $btran->wa_payment_method_id = $value->chartOfAccount->paymentMethod?$value->chartOfAccount->paymentMethod->id : NULL;
                    $btran->amount = $value->amount;
                    $btran->wa_curreny_id = 1;
                    $btran->save();

                    $cr = new WaGlTran();
                    $cr->period_number = $accountingPeriod ? $accountingPeriod->id : null;
                    $cr->grn_type_number = $series_module->type_number;
                    $cr->trans_date = Carbon::now();
                    $cr->restaurant_id = $branch;
                    $cr->tb_reporting_branch = $branch;
                    $cr->grn_last_used_number = $series_module->last_number_used;
                    $cr->transaction_type = $series_module->description;
                    $cr->transaction_no = $bankPostCode;
                    $cr->narrative = $query->start_date.' - '.$query->end_date .' / '.$value->document_no;
                    $cr->account = $creditCode;
                    $cr->amount = $creditAmount;
                    $cr->save();

                    $dr = new WaGlTran();
                    $dr->period_number = $accountingPeriod ? $accountingPeriod->id : null;
                    $dr->grn_type_number = $series_module->type_number;
                    $dr->trans_date = Carbon::now();
                    $dr->restaurant_id = $branch;
                    $dr->tb_reporting_branch = $branch;
                    $dr->grn_last_used_number = $series_module->last_number_used;
                    $dr->transaction_type = $series_module->description;
                    $dr->transaction_no = $bankPostCode;
                    $dr->narrative = $query->start_date.' - '.$query->end_date .' / '.$value->document_no;
                    $dr->account = $debitCode;
                    $dr->amount = $debitAmount;
                    $dr->save(); 
                }

                GlReconStatement::where('gl_reconcile_id',$id)->update([
                    'current_status' => 'closed',
                ]);

                DB::table('gl_reconciles')->where('id',$id)->update(['status'=>'closed']);              

                DB::commit();
                Session::flash('success', 'Recon Closed Successfully.');
            } catch (\Exception $e) {
                DB::rollback();
                Session::flash('warning', $e->getMessage());
            }
            return redirect()->back();
    }

}
