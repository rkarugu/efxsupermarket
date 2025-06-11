<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Models\PosCashPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashPaymentController extends Controller
{
   
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;
    protected $permissionModule;

    public function __construct(Request $request)
    {
        $this->model = 'pos_cash_payments';
        $this->title = 'Pos Cash Payment';
        $this->pmodule = 'pos-cash-payment';
        $this->basePath = 'admin.cash_payment';
        $this->permissionModule = 'pos_cash_payments';
    }
    public function index(Request $request)
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = Restaurant::all();
        $user = Auth::user();
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $end = $request->end_date? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $payments = PosCashPayment::with('initiator', 'recipient', 'approvedBy')
            ->whereBetween('created_at', [$start, $end]);
        if ($request->branch){
            $payments = $payments->where('branch_id', $request->branch);
        }
        if($user->role_id != 1){
            $payments = $payments->where('branch_id', $user->restaurant_id);
        }
        $payments = $payments->get();
        return view('admin.pos_cash_payments.index', compact('title', 'model', 'pmodule', 'permission',  'branches', 'payments'));
       
    }
    public  function getUsers()
    {
       $users = User::all();
       
         return response()->json([
            'users' => $users
        ]);

    }
    public  function getChartsOfAccounts()
    {
       $glAccounts = DB::table('wa_charts_of_accounts')
            ->select('wa_charts_of_accounts.*')
            ->leftJoin('wa_sub_account_sections', 'wa_charts_of_accounts.wa_account_sub_section_id' , 'wa_sub_account_sections.id')
            ->leftJoin('wa_account_sections', 'wa_account_sections.id','wa_sub_account_sections.wa_account_section_id')
            ->where('wa_account_sections.slug', 'expenses')
            ->get();

        return response()->json([
            'gl_accounts' => $glAccounts
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $documentNo = getCodeWithNumberSeries('POS_CASH_PAYMENTS');
            updateUniqueNumberSeries('POS_CASH_PAYMENTS', $documentNo);
            $cashPayment = new PosCashPayment();
            $cashPayment->document_no = $documentNo;
            $cashPayment->branch_id = $user->restaurant_id;
            $cashPayment->initiated_by = $user->id;
            $cashPayment->payee = $request->user_id;
            $cashPayment->amount = $request->amount;
            $cashPayment->payment_reason = $request->reason ?? '';

            $cashPayment->save();
        
            DB::commit();
            return response()->json(['message' => 'Payment Initiated Successfully'], 200);

        } catch (\Throwable $th) {
        DB::rollBack();
        return $this->jsonify(['error'=>true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
            
        }
    }
    public function approve(Request $request){
        try {
            $user = Auth::user();
            $cashPayment = PosCashPayment::find($request->id);
            $cashPayment->status = 'Approved';
            $cashPayment->approved_by = $user->id;
            $cashPayment->approved_at = Carbon::now()->toDateTimeString();
            $cashPayment->wa_charts_of_accounts_id = $request->gl_account_id;
            $cashPayment->save();
        
            return response()->json(['message' => 'Payment Approved Successfully'], 200);

        } catch (\Throwable $th) {
        return $this->jsonify(['error'=>true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
            
        }

    }
    public function reject(Request $request){
        try {
            $user = Auth::user();
            $cashPayment = PosCashPayment::find($request->id);
            $cashPayment->status = 'Rejected';
            $cashPayment->approved_by = $user->id;
            $cashPayment->rejected_at = $request->reason;
            $cashPayment->rejected_at = Carbon::now()->toDateTimeString();
            $cashPayment->approved_at = Carbon::now()->toDateTimeString();
            $cashPayment->save();
        
            return response()->json(['message' => 'Payment Approved Successfully'], 200);

        } catch (\Throwable $th) {
        return $this->jsonify(['error'=>true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
            
        }

    }
    public function disburse(Request $request){
        try {
            $cashPayment = PosCashPayment::find($request->id);
            $cashPayment->status = 'Disbursed';
            $cashPayment->disbursed_at = Carbon::now()->toDateTimeString();
            $cashPayment->save();
        
            return response()->json(['message' => 'Payment Disbursed Successfully'], 200);
   

        } catch (\Throwable $th) {
        return $this->jsonify(['error'=>true, 'message' => $th->getMessage(), 'trace' => $th->getTrace()], 500);
            
        }

    }
    public function downloadDisbursementReceipt(Request $request, $id)
    {
        $cashPayment = PosCashPayment::with('initiator', 'recipient', 'approvedBy')->find($request->id);
        if ($request->ajax())
        {
            return view('admin.pos_cash_payments.disbursement_receipt', compact('cashPayment'));
        }

        $pdf = \PDF::loadView('admin.pos_cash_payments.disbursement_receipt', compact('cashPayment'));
        return $pdf->download('disbursement.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
}
