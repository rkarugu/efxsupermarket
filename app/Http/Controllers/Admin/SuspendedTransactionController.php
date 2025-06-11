<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentChannel;
use App\Enums\Status\PaymentVerification;
use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Model\UserLog;
use App\Model\WaAccountingPeriod;
use App\Model\WaBanktran;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Models\SuspendedTransaction;
use App\WaTenderEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SuspendedTransactionController extends Controller
{
    public function index()
    {
        $title = 'Suspended Transactions';
        $model = 'bank-reconciliation';
        $breadcum = [$title => route("suspended-transactions.index"), 'Listing' => ''];

        $suspendedTransactions = DB::table('suspended_transactions')
            ->where('suspended_transactions.status', 'suspended')
            ->select('suspended_transactions.*', 'users.name as user')
            ->join('users', 'suspended_transactions.suspended_by', '=', 'users.id')
            ->orderBy('suspended_transactions.created_at', 'DESC')->get();

        $routes = DB::table('wa_customers')->select('id', 'customer_name')->get();
        return view('admin.suspended_transactions.listing', compact('title', 'model', 'breadcum', 'suspendedTransactions', 'routes'));
    }

    public function create()
    {
        $title = 'Suspend Transactions';
        $model = 'bank-reconciliation';
        $breadcum = [$title => route("suspended-transactions.index"), 'Suspend' => ''];

        $processingUpload = false;
        return view('admin.suspended_transactions.create', compact('title', 'model', 'breadcum', 'processingUpload'));
    }

    public function upload(Request $request)
    {
        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(false);
            $fileName = $request->file('cleanup_list');
            $spreadsheet = $reader->load($fileName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $trans = [];
            $debtorTrans = DB::table('wa_debtor_trans')
                ->select(
                    'wa_debtor_trans.id',
                    'wa_debtor_trans.trans_date',
                    'wa_debtor_trans.created_at',
                    'wa_debtor_trans.reference',
                    'wa_debtor_trans.amount',
                    'wa_debtor_trans.document_no',
                    'wa_customers.id as wa_customer_id',
                    'wa_customers.customer_name',
                    'wa_debtor_trans.verification_status'
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->get()->map(function ($record) {
                    $record->amount = abs($record->amount);
                    return $record;
                });
            foreach ($data as $index => $record) {
                if ($index != 0) {
                    $tran = $debtorTrans->where('document_no', $record[0])->first();
                    if (!$tran) {
                        Session::flash('warning', "A transaction matching document no $record[0] was not found");
                        return redirect()->back();
                    }

                    if ((float)$tran->amount != (float)$record[1]) {
                        Session::flash('warning', "Uploaded transaction $record[0]'s amount ($record[1]) does not match system transaction amount ($tran->amount)");
                        return redirect()->back();
                    }

                    $trans[] = [
                        'id' => $tran->id,
                        'wa_customer_id' => $tran->wa_customer_id,
                        'trans_date' => $tran->trans_date,
                        'input_date' => $tran->created_at,
                        'reference' => $tran->reference,
                        'document_no' => $tran->document_no,
                        'amount' => $tran->amount,
                        'route' => $tran->customer_name,
                        'reason' => $record[2],
                        'status' =>$tran->verification_status,
                    ];
                }
            }

            $title = 'Suspend Transactions';
            $model = 'bank-reconciliation';
            $breadcum = [$title => route("suspended-transactions.index"), 'Suspend' => ''];
            $processingUpload = true;

            return view('admin.suspended_transactions.create', compact('title', 'model', 'breadcum', 'processingUpload', 'trans'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $documentNos = [];
            $user = getLoggeduserProfile();
            foreach ($request->reason as $key => $record) {
                $trans = WaDebtorTran::with('customerDetail')->find($key);

                if ($trans->verification_status !='Approved') {
                    SuspendedTransaction::create([
                        'wa_customer_id' => $trans->customerDetail->id,
                        'suspended_by' => $user->id,
                        'document_no' => $trans->document_no,
                        'reference' => $trans->reference,
                        'amount' => abs($trans->amount),
                        'trans_date' => $trans->trans_date,
                        'input_date' => $trans->created_at,
                        'route' => $trans->customerDetail->customer_name,
                        'reason' => $record,
                        'branch_id' => $trans->branch_id,
                        'channel' => $trans->channel,
                        'verification_record_id' => $trans->verification_record_id,
                        'manual_upload_status' => $trans->manual_upload_status,
                        'manual_upload_approved_by' => $trans->manual_upload_approved_by
                    ]);
    
                    // Tender Entries Delete
                    WaTenderEntry::where('document_no',$trans->document_no)
                                ->where('amount',abs($trans->amount))
                                ->whereBetween('trans_date', [date('Y-m-d',strtotime($trans->trans_date)) . ' 00:00:00', date('Y-m-d',strtotime($trans->trans_date)) . " 23:59:59"])
                                ->where(function ($q) use($trans) {
                                    $q->where('reference', $trans->reference)
                                    ->orWhere('additional_info', $trans->reference);
                                })
                            ->delete();
                   
                    if($trans->verification_status == 'verified'){
                        DB::table('payment_verification_banks')
                            ->where('matched_debtors_id', $trans->id)
                            ->update([
                                'status' => PaymentVerification::Pending->value,
                                'matched_debtors_id' => NULL,
                            ]);
                    }
                    $trans->delete();
                    $documentNos[] = $trans->document_no;
                }
            }

            $deletedTrans = implode(',', $documentNos);
            UserLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'module' => 'reconciliation',
                'activity' => "Suspended transactions $deletedTrans",
                'entity_id' => $user->id,
                'user_agent' => 'Bizwiz WEB',
            ]);

            DB::commit();
            return response()->json([
                'result'=>1,
                'message'=>'Transactions suspended successfully.',
                ], 200);    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result'=>-1,
                'message'=>$e->getMessage(),
                ], 402); 
            return redirect()->back();
        }
          
        // DB::beginTransaction();

        // try {
        //     $records = json_decode($request->records, true);
        //     $user = getLoggeduserProfile();
        //     $documentNos = [];
        //     foreach ($records as $record) {
        //         $trans = WaDebtorTran::find($record['id']);

        //         if ($trans->verification_status !='Approved') {
        //             SuspendedTransaction::create([
        //                 'wa_customer_id' => $record['wa_customer_id'],
        //                 'suspended_by' => $user->id,
        //                 'document_no' => $record['document_no'],
        //                 'reference' => $record['reference'],
        //                 'amount' => $record['amount'],
        //                 'trans_date' => $record['trans_date'],
        //                 'input_date' => $record['input_date'],
        //                 'route' => $record['route'],
        //                 'reason' => $record['reason'],
        //                 'branch_id' => $trans->branch_id,
        //                 'channel' => $trans->channel
        //             ]);
    
        //             // Tender Entries Delete
        //             WaTenderEntry::where([['reference',$trans->reference],['document_no',$trans->document_no],['amount',abs($trans->amount)],['trans_date',$trans->trans_date]])->delete();
        //             if($trans->verification_status == 'verified'){
        //                 DB::table('payment_verification_banks')
        //                     ->where('matched_debtors_id', $trans->id)
        //                     ->update([
        //                         'status' => PaymentVerification::Pending->value,
        //                         'matched_debtors_id' => NULL,
        //                     ]);
        //             }
        //             $trans->delete();
        //             $documentNos[] = $record['document_no'];
        //         }
        //     }

        //     $deletedTrans = implode(',', $documentNos);
        //     UserLog::create([
        //         'user_id' => $user->id,
        //         'user_name' => $user->name,
        //         'module' => 'reconciliation',
        //         'activity' => "Suspended transactions $deletedTrans",
        //         'entity_id' => $user->id,
        //         'user_agent' => 'Bizwiz WEB',
        //     ]);

        //     DB::commit();
        //     Session::flash('success', 'Transactions suspended successfully');
        //     return redirect()->route('suspended-transactions.index');
        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     Session::flash('warning', $e->getMessage());
        // }
    }

    public function expunge($document_no)
    {
        DB::beginTransaction();

        try {
            $user = getLoggeduserProfile();
            $trans = SuspendedTransaction::where('document_no', $document_no)->first();
            $trans->update(['status' => 'expunged', 'resolved_by' => $user->id]);

            $glTrans = DB::table('wa_gl_trans')->where('transaction_no', $document_no)->pluck('id')->toArray();
            WaGlTran::destroy($glTrans);

            $bankTrans = WaBanktran::where('document_no', $document_no)->first();
            $bankTrans->delete();

            $tenderEntry = WaTenderEntry::where('document_no', $document_no)->first();
            $tenderEntry->delete();

            UserLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'module' => 'reconciliation',
                'activity' => "Purge transaction $document_no",
                'entity_id' => $trans->id,
                'user_agent' => 'Bizwiz WEB',
            ]);

            DB::commit();
            Session::flash('success', 'Transaction expunged successfully');
            return redirect()->route('suspended-transactions.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function restore(Request $request, $document_no)
    {
        DB::beginTransaction();

        try {
            $user = getLoggeduserProfile();
            $trans = SuspendedTransaction::where('document_no', $document_no)->first();
            $checkReference = WaDebtorTran::where('reference',$request->edited_reference)->count();
            if($checkReference){
                Session::flash('danger', 'Confirm reference for document '.$trans->document_no. ', this '.$request->edited_reference.' already exist.' );
                return redirect()->back();
            }
            $trans->update([
                'status' => 'restored',
                'resolved_by' => $user->id,
                'edited_wa_customer_id' => $request->edited_wa_customer_id,
                'edited_reference' => $request->edited_reference,
                'edited_amount' => $request->edited_amount,
            ]);

            $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
            $waCustomer = WaCustomer::find($request->edited_wa_customer_id);
            $route = Route::find($waCustomer->route_id);

            $debtorTrans = WaDebtorTran::create([
                'salesman_id' => 46,
                'salesman_user_id' => $route->salesman()?->id,
                'type_number' => $series_module?->type_number,
                'wa_customer_id' => $waCustomer->id,
                'customer_number' => $waCustomer->customer_code,
                'trans_date' => $trans->trans_date,
                'input_date' => $trans->input_date,
                'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                'shift_id' => null,
                'invoice_customer_name' => null,
                'reference' => "$request->edited_reference",
                'amount' => -($request->edited_amount),
                'document_no' => $trans->document_no,
                'branch_id' => $trans->branch_id,
                'channel' => $trans->channel,
                'verification_record_id' => $trans->verification_record_id ?? NULL,
                'manual_upload_status' => $trans->manual_upload_status ?? false,
                'manual_upload_approved_by' => $trans->manual_upload_approved_by ?? NULL
            ]);

            if ($trans->channel == PaymentChannel::Eazzy->value) {
                $paymentMethod = 7;
            } elseif ($trans->channel == PaymentChannel::Equity->value) {
                $paymentMethod = 10;
            }  elseif ($trans->channel == PaymentChannel::Vooma->value) {
                $paymentMethod = 8;
            }  elseif ($trans->channel == PaymentChannel::KCB->value) {
                $paymentMethod = 9;
            } else {
                $paymentMethod = 3;
            }
            
            $account = DB::table('wa_bank_accounts')->where('account_name',$trans->channel)->first();
            WaTenderEntry::create([
                'document_no' => $trans->document_no,
                'channel' => $trans->channel,
                'reference' => "$request->edited_reference",
                'additional_info' => "$request->edited_reference",
                'trans_date' => $trans->trans_date,
                'cashier_id' => 1,
                'amount' => $request->edited_amount,
                'account_code' => $account->account_code,
                'customer_id' => $waCustomer->id,
                'wa_payment_method_id' => $paymentMethod,
                'paid_by' => $trans->channel 
            ]);
                    
            UserLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'module' => 'reconciliation',
                'activity' => "Restored transaction $document_no",
                'entity_id' => $trans->id,
                'user_agent' => 'Bizwiz WEB',
            ]);

            DB::commit();
            Session::flash('success', 'Transaction Restored successfully');
            return redirect()->route('suspended-transactions.index');
        } catch (\Throwable $e) {
            DB::rollBack();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function expunged()
    {
        $title = 'Expunged Transactions';
        $model = 'bank-reconciliation';
        $breadcum = [$title => route("suspended-transactions.index"), 'Expunged' => ''];

        $expungedTransactions = DB::table('suspended_transactions')
            ->where('suspended_transactions.status', 'expunged')
            ->select('suspended_transactions.*', 'suspender.name as suspender', 'resolver.name as resolver')
            ->join('users as suspender', 'suspended_transactions.suspended_by', '=', 'suspender.id')
            ->join('users as resolver', 'suspended_transactions.resolved_by', '=', 'resolver.id')
            ->orderBy('suspended_transactions.created_at', 'DESC')->get();

        return view('admin.suspended_transactions.expunged', compact('title', 'model', 'breadcum', 'expungedTransactions'));
    }

    public function restored()
    {
        $title = 'Restored Transactions';
        $model = 'bank-reconciliation';
        $breadcum = [$title => route("suspended-transactions.index"), 'Restored' => ''];

        $expungedTransactions = DB::table('suspended_transactions')
            ->where('suspended_transactions.status', 'restored')
            ->select('suspended_transactions.*', 'suspender.name as suspender', 'resolver.name as resolver')
            ->join('users as suspender', 'suspended_transactions.suspended_by', '=', 'suspender.id')
            ->join('users as resolver', 'suspended_transactions.resolved_by', '=', 'resolver.id')
            ->orderBy('suspended_transactions.created_at', 'DESC')->get();

        return view('admin.suspended_transactions.restored', compact('title', 'model', 'breadcum', 'expungedTransactions'));
    }

    public function fetch_transaction(Request $request)
    {
        try {
            $trans = DB::table('wa_debtor_trans')
                ->select(
                    'wa_debtor_trans.id',
                    'wa_debtor_trans.trans_date',
                    'wa_debtor_trans.created_at',
                    'wa_debtor_trans.reference',
                    DB::raw('ABS(wa_debtor_trans.amount) as amount'),
                    'wa_debtor_trans.document_no',
                    'wa_customers.id as wa_customer_id',
                    'wa_customers.customer_name',
                    'wa_debtor_trans.verification_status'
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->where('document_no',$request->document_no)
                ->first();
                // ->get()->map(function ($record) {
                //     $record->amount = abs($record->amount);
                //     return $record;
                // });
                
            if (!$trans) {
                return response()->json([
                    'result'=>0,
                    'errors'=>"No transactions matching document no $request->document_no was not found"
                ], 422);
            }
            return response()->json([
                'result'=>1,
                'message'=>'Transction Found.',
                'data' => $trans
                ], 200);  
            $title = 'Suspend Transactions';
            $model = 'bank-reconciliation';
            $breadcum = [$title => route("suspended-transactions.index"), 'Suspend' => ''];
            $processingUpload = true;

            return view('admin.suspended_transactions.create', compact('title', 'model', 'breadcum', 'processingUpload', 'trans'));
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
}
