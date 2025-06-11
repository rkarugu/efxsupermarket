<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use App\Model\Route;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaRouteCustomer;
use App\Models\PaymentVerificationBank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\Finance\DebtorTransactionsExport;
use App\WaTenderEntry;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use App\Enums\PaymentChannel;
use App\Model\PaymentMethod;
use App\Models\StockDebtorTran;
use App\Model\User;
class ManualUploadController extends Controller
{


    public function manual_upload_transaction(Request $request)
    {
        DB::beginTransaction();
        try {
            $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
            $documentNo = getCodeWithNumberSeries('RECEIPT');
            $waCustomer = WaCustomer::where('id',$request->route)->first();
            $bankInfo = PaymentVerificationBank::find($request->bankId);            
            $route = Route::find($waCustomer->route_id);
            
            if (DB::table('wa_debtor_trans')->where('bank_statement_id', $request->bankId)->doesntExist()) {
                WaDebtorTran::create([
                    'salesman_id' => 46, // Find a way to make this dynamic
                    'salesman_user_id' => $route->salesman()?->id,
                    'type_number' => $series_module?->type_number,
                    'wa_customer_id' => $waCustomer->id,
                    'customer_number' => $waCustomer->customer_code,
                    'trans_date' => date('Y-m-d H:i:s'),
                    'input_date' => date('Y-m-d H:i:s'),
                    'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                    'shift_id' => null,
                    'invoice_customer_name' => null,
                    'reference' => $bankInfo->reference,
                    'amount' => -($bankInfo->amount),
                    'document_no' => $documentNo,
                    'branch_id' => $route->restaurant_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'channel' => $bankInfo->channel,
                    'manual_upload_status' => true,
                    'bank_statement_id' => $request->bankId,
                    'verification_status' => 'manual upload',
                ]);

                $paymentMethod = PaymentMethod::where('title',$bankInfo->channel)->first();
                
                WaTenderEntry::create([
                    'document_no' => $documentNo,
                    'channel' => $bankInfo->channel,
                    'reference' => $bankInfo->reference,
                    'additional_info' => $bankInfo->reference,
                    'trans_date' => date('Y-m-d H:i:s'),
                    'cashier_id' => 1,
                    'amount' => $bankInfo->amount,
                    'account_code' => $paymentMethod->paymentGlAccount?->account_code,
                    'customer_id' => $waCustomer->id,
                    'wa_payment_method_id' => $paymentMethod,
                    'paid_by' => $bankInfo->channel,
                    'branch_id' => $route->restaurant_id,
                ]);

                $request->session()->flash('success','Transaction Created Successfully');
            } else{
                $request->session()->flash('danger','Transaction Already exists');
            }
            
            DB::commit();
            
        } catch (\Throwable $th) {
            $request->session()->flash('danger', $th->getMessage());
        }

        request()->filled('startDate') ? $request->session()->flash('startDate',request()->startDate): '';
        request()->filled('endDate') ? $request->session()->flash('endDate',request()->endDate): '';
        
        return redirect()->back();
    }

    public function manual_upload_list()
    {
        if (!can('view-manual-upload', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Manual Uploads';
        $model= 'view-manual-upload';
        $permission = $this->mypermissionsforAModule();

        $breadcum = [
            'Reconciliation' => '',
            $title => 'Manual Uploads'
        ];

        $transactionTypes = WaNumerSeriesCode::whereIn('id',[12])->get()->pluck('id');

        $startDate = request()->filled('start_date') ? request()->start_date . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $endDate = request()->filled('end_date') ? request()->end_date . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $debtors = DB::table('wa_debtor_trans')  //WaDebtorTran::query()
                    ->whereIn('wa_debtor_trans.type_number',$transactionTypes)
                    ->where('wa_debtor_trans.channel','!=',NULL)
                    ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
                    ->where('wa_debtor_trans.manual_upload_status',1)
                    ->select([
                        'wa_debtor_trans.id',
                        'wa_debtor_trans.trans_date',
                        'wa_debtor_trans.document_no',
                        'wa_debtor_trans.channel',
                        'wa_debtor_trans.reference',
                        'wa_debtor_trans.amount',
                        'wa_debtor_trans.verification_status',
                        'wa_customers.customer_name',
                        'restaurants.name as branch_name',
                        'users.name as approved_by',
                    ])
                    ->join('restaurants','restaurants.id','wa_debtor_trans.branch_id')
                    ->join('wa_customers','wa_customers.id','wa_debtor_trans.wa_customer_id')
                    ->leftjoin('users','users.id','wa_debtor_trans.manual_upload_approved_by')
                    ->orderBy('wa_debtor_trans.created_at','desc');     
                     
        // if(request()->status != 'all'){
        //     if (request()->status == 'Approved') {
        //         $debtors->where('wa_debtor_trans.verification_status','!=', 'manual upload');
        //     } else {
        //         $debtors->where('wa_debtor_trans.verification_status', 'manual upload');
        //     }  
        // }
        // if(request()->channel != 'all'){
        //     $debtors->where('wa_debtor_trans.channel', request()->channel);
        // }
        // if(request()->branch != 'all'){
        //     $debtors->where('restaurants.id', request()->branch);
        // }
        // if(request()->route != 'all'){
        //     $debtors->where('wa_customers.id', request()->route);
        // }
        // if(request()->start_date && request()->end_date){
        //     $debtors->whereBetween('trans_date',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
        // }
        if (request()->filled('type')) {
            if (request()->type == 'pdf') {
                $debtors = $debtors->get();
                $pdf = \PDF::loadView('admin.Finance.debtor_trans.pdf', compact('debtors'));
                $report_name = 'debtor-transactions' . date('Y_m_d_H_i_A');
                // return $pdf->stream();
                return $pdf->download($report_name . '.pdf');
            }
            if (request()->type == 'excel') {
                $customerPayments = $debtors->get()->map(function ($debtor) {
                return [
                    'trans_date' => Carbon::parse($debtor->trans_date)->format('Y-m-d'),
                    'document_no' => $debtor->document_no,
                    'amount' => number_format(abs($debtor->amount)),
                    'channel' => $debtor->channel,
                    'branch' => $debtor->branch_name,
                    'route' => $debtor->customer_name,
                    'reference' => $debtor->reference ?? '-',
                    'verification' => $debtor->verification_status,
                ];
            });


        $export = new DebtorTransactionsExport(collect($customerPayments));
        return Excel::download($export, 'System Transations.xlsx');
            }
        }

        if (request()->wantsJson()) {
            
            $debtors->where('wa_debtor_trans.verification_status','!=', 'manual upload')
            ->whereBetween('wa_debtor_trans.created_at', [$startDate, $endDate]);
            return DataTables::of($debtors)
                ->editColumn('amount', function ($amount) {
                    return manageAmountFormat(abs($amount->amount));
                })
                ->editColumn('trans_date', function ($date) {
                    return date('Y-m-d',strtotime($date->trans_date));
                })
                ->editColumn('verification_status', function ($debtor){
                    return ucfirst($debtor->verification_status);
                })
                ->editColumn('verification_status', function ($debtor){
                    if ($debtor->verification_status != 'manual upload') {
                        return 'Approve';
                    } else {
                        return 'Pending';
                    }
                })
                ->editColumn('branch_name', function ($branch) {
                    if ($branch->branch_name) {
                        return $branch->branch_name;
                    }
                    return '-';
                })
                ->editColumn('approved_by', function ($user) {
                    if ($user->approved_by) {
                        return $user->approved_by;
                    }
                    return '-';
                })
                ->with('total_amount', function () use ($debtors) {
                    return manageAmountFormat(abs($debtors->get()->sum('amount')));
                })
                ->toJson();
        }
        $channels = DB::table('wa_debtor_trans')->where('channel','!=',NULL)->select('channel')->distinct()->get()->pluck('channel');
        $branches = DB::table('restaurants')->select('id','name')->get();

        $debtors->where('wa_debtor_trans.verification_status', 'manual upload');
        $pendings = $debtors->get();
        return view('admin.Finance.manual_uploads.list',compact('title','model','permission','channels','branches','pendings'));
    }

    public function manual_update_status(Request $request)
    {
        DB::beginTransaction();
        try {
            WaDebtorTran::where('id',$request->transaction)->update(['verification_status'=>'pending','manual_upload_approved_by'=>Auth::user()->id]);
            
            DB::commit();
            $request->session()->flash('success','Transaction Updated Successfully');
        } catch (\Exception $e) {
            $request->session()->flash('danger',$e->getMessage());
        }
         
    }

    public function manual_upload_transaction_stock_debtor(Request $request)
    {
        if (!can('store-loading-sheets', 'stock-non-debtors')) {
            return returnAccessDeniedPage();
        }



        DB::beginTransaction();
        try {
            $bankInfo = PaymentVerificationBank::find($request->stockDebtorId);     

            $series_module = WaNumerSeriesCode::where('module', 'STOCK_DEBT_RECEIPT')->first();
            $lastNumberUsed = $series_module->last_number_used;
            $newNumber = (int)$lastNumberUsed + 1;
            $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
            $series_module->update(['last_number_used' => $newNumber]);

            $user = User::find($request->employee);
            if ($user->stockDebtor) {
                $stockDebtor = StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_debtors_id' => $user->stockDebtor->id,
                    'document_no' => $newCode,
                    'total' => -(abs($bankInfo->amount)),
                ]);
                
            } else{
                $stockDebtor = StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_non_debtor_id' => $request->employee,
                    'document_no' => $newCode,
                    'total' => -(abs($bankInfo->amount)),
                ]);
            }

            $bankInfo->update([
                'status' => 'Verifying',
                'stock_debtor_tran_id' => $stockDebtor->id
            ]);
            
            DB::commit();
            $request->session()->flash('success','Stock Debtor Allocated Successfully');
        } catch (\Throwable $th) {
            DB::commit();
            $request->session()->flash('danger', $th->getMessage());
        }

        return redirect()->back();        
    }
}
