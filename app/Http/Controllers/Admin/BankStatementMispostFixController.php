<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use Illuminate\Http\Request;
use App\Models\BankStatementMispostHistory;
use App\Models\PaymentVerificationBank;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BankStatementMispostFixController extends Controller
{
    protected $model;
    protected $title;

    public function __construct() {
        $this->model = 'bank-statement-mispost';
        $this->title = 'Bank Statement Mispost';
    }

    public function index()
    {
        if (!can('view', $this->model)) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', $title => ''];


        if (request()->ajax()) {
            $query = BankStatementMispostHistory::with('createdBy','bankStatement')->select('bank_statement_mispost_histories.*')->orderBy('created_at','DESC');
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('bank_statement.amount', function($query){
                    return manageAmountFormat(abs($query->bankStatement->amount));
                })
                ->editColumn('created_at', function ($voucher) {
                    return $voucher->created_at->format('Y-m-d');
                })
                ->toJson();
        }

        return view('admin.bank_statement_mispost.index', compact('title', 'model','breadcum'));
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

        return view('admin.bank_statement_mispost.create', compact('title', 'model','breadcum','channels'));
    }

    public function fetch_statement(Request $request)
    {
        try {
            $trans = DB::table('payment_verification_banks')
                ->where('reference','like','%'.$request->reference.'%')
                ->get();
                
            if (!count($trans)) {
                return response()->json([
                    'result'=>-1,
                    'error'=>"No Statement matching reference no $request->reference was not found"
                ], 422);
            }
            return response()->json([
                'result'=>1,
                'message'=>'Statement Found.',
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
                $bank = PaymentVerificationBank::find($key);

                if ($bank->status =='Pending') {
                    BankStatementMispostHistory::create([
                        'created_by' => Auth::user()->id,
                        'old_channel' => $bank->channel,
                        'new_channel' => $record,
                        'payment_verification_bank_id' => $bank->id,
                        'old_bank_date' => $bank->bank_date,
                        'new_bank_date' => $request->bank_date[$key],
                        'status' => $bank->status,
                    ]);

                    $bank->update([
                        'channel'=>$record,
                        'bank_date'=>$request->bank_date[$key]
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'result'=>1,
                'message'=>'Statements Updated successfully.',
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
}
