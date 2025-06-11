<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status\PaymentVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Model\PaymentMethod;
use App\Model\WaDebtorTran;
use App\Models\TransactionMispostHistory;
use App\WaTenderEntry;
use Yajra\DataTables\Facades\DataTables;

class TransactionMispostFixController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'transaction-mispost';
        $this->title = 'Transaction Mispost';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', $title => ''];


        if (request()->ajax()) {
            $query = TransactionMispostHistory::with('debtorTrans','createdBy')
            ->select('transaction_mispost_histories.*')
            ->orderBy('created_at','DESC');
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('debtor_trans.amount', function($query){
                    return manageAmountFormat(abs($query->debtorTrans->amount));
                })
                ->editColumn('created_at', function ($voucher) {
                    return $voucher->created_at->format('Y-m-d');
                })
                ->toJson();
        }

        return view('admin.transaction_mispost.index', compact('title', 'model','breadcum'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!can('add', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title.' Create';
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', $title => ''];
        $channels = PaymentMethod::where([
            ['use_for_receipts',1],
            ['use_as_channel',1]
            ])->get();

        return view('admin.transaction_mispost.create', compact('title', 'model','breadcum','channels'));
    }

    public function fetch_transaction(Request $request)
    {
        try {
            $trans = DB::table('wa_debtor_trans')
                ->select(
                    'wa_debtor_trans.id',
                    'wa_debtor_trans.trans_date',
                    'wa_debtor_trans.created_at',
                    'wa_debtor_trans.channel',
                    DB::raw('ABS(wa_debtor_trans.amount) as amount'),
                    'wa_debtor_trans.document_no',
                    'wa_customers.id as wa_customer_id',
                    'wa_customers.customer_name',
                    'wa_debtor_trans.verification_status'
                )
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
                ->where('document_no',$request->document_no)
                ->first();
                
            if (!$trans) {
                return response()->json([
                    'result'=>-1,
                    'error'=>"No transactions matching document no $request->document_no was not found"
                ], 422);
            }
            return response()->json([
                'result'=>1,
                'message'=>'Transction Found.',
                'data' => $trans
                ], 200);  
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return returnAccessDeniedPage();
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($request->channel as $key => $record) {
                $trans = WaDebtorTran::with('customerDetail')->find($key);

                if ($trans->verification_status !='Approved') {
                    TransactionMispostHistory::create([
                        'created_by' => Auth::user()->id,
                        'old_channel' => $trans->channel,
                        'new_channel' => $record,
                        'wa_debtor_trans_id' => $trans->id,
                        'wa_customer_id' => $trans->wa_customer_id,
                        'status' => $trans->verification_status,
                    ]);

                    
                    
                    if($trans->verification_status == 'verified'){
                        DB::table('payment_verification_banks')
                            ->where('matched_debtors_id', $trans->id)
                            ->update([
                                'channel' => $record,
                            ]);
                    }

                    WaTenderEntry::where('document_no',$trans->document_no)
                                ->where('amount',abs($trans->amount))
                                ->whereBetween('trans_date', [date('Y-m-d',strtotime($trans->trans_date)) . ' 00:00:00', date('Y-m-d',strtotime($trans->trans_date)) . " 23:59:59"])
                                ->update(['channel'=>$record]);

                    $trans->update(['channel'=>$record]);
                }
            }

            DB::commit();
            return response()->json([
                'result'=>1,
                'message'=>'Transactions Channel Updated successfully.',
                ], 200);    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'result'=>-1,
                'message'=>$e->getMessage(),
                ], 402); 
            return redirect()->back();
        }
    }

    public function store_single(Request $request)
    {     
        DB::beginTransaction();
        
        try {
                $trans = WaDebtorTran::with('customerDetail')->find($request->transaction);

                if ($trans->verification_status !='Approved') {
                    TransactionMispostHistory::create([
                        'created_by' => Auth::user()->id,
                        'old_channel' => $trans->channel,
                        'new_channel' => $request->channel,
                        'wa_debtor_trans_id' => $trans->id,
                        'wa_customer_id' => $trans->wa_customer_id,
                        'status' => $trans->verification_status,
                    ]);
                    
                    if($trans->verification_status == 'verified'){
                        DB::table('payment_verification_banks')
                            ->where('matched_debtors_id', $trans->id)
                            ->update([
                                'channel' => $request->channel,
                            ]);
                    }

                    WaTenderEntry::where('document_no',$trans->document_no)
                                ->where('amount',abs($trans->amount))
                                ->whereBetween('trans_date', [date('Y-m-d',strtotime($trans->trans_date)) . ' 00:00:00', date('Y-m-d',strtotime($trans->trans_date)) . " 23:59:59"])
                                ->update(['channel'=>$request->channel]);

                    $trans->update(['channel'=>$request->channel]);
                }
            

            DB::commit();
            Session::flash('success', 'Transactions Channel Updated successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
}
