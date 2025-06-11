<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Models\StockDebtor;
use App\Models\StockDebtorTran;
use App\Model\WaNumerSeriesCode;
use App\Models\StockDebtorTranItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Session;

class StockDebtorsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'stock-debtors';
        $this->title = 'Stock Debtors';
        $this->pmodule = 'stock-debtors';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', 'stock-debtors')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Debtors';
        $model= 'stock-debtors';
        

        $breadcum = [
            'Stock Debtors' => '',
            $title => ''
        ];

        $debtors = StockDebtor::with('employee','employee.location_stores','employee.userRestaurent','employee.userRole','employee.uom','stockDebtorTrans')
        ->select('stock_debtors.*');
        if(request()->filled('role')){
            $debtors->whereHas('employee.userRole', function ($query) {
                $query->where('id', request()->role);
           });
        }

        if (request()->wantsJson()) {
           
            return DataTables::of($debtors)
                    ->editColumn('employee.uom.title', function($debtors){
                        if($debtors->employee->uom){
                            return $debtors->employee->uom->title;
                        } 
                        return "-";
                    })
                    ->addColumn('total', function($debtors){
                        return manageAmountFormat($debtors->stockDebtorTrans->sum('total'));
                    })
                    ->with('grand_total',function() use($debtors){
                        $ids = $debtors->get()->pluck('id');
                        $items = DB::table('stock_debtor_trans')->whereIn('stock_debtors_id',$ids)->sum('total');
                        return manageAmountFormat($items);
                    })
                    ->toJson();
        }

        if (request()->get('print') == "pdf") {
            $debtors = $debtors->get();
            $pdf = \PDF::loadView('admin.stock_debtors.list_pdf', compact('debtors'));
            return $pdf->download('stock_debtor_list_' . time() . '.pdf');
        }

        $employees = User::doesntHave('stockDebtor')
                    // ->whereIn('role_id',[152,169,170])
                    ->get();
        $debtors = StockDebtor::with('employee')->get();
        
        return view('admin.stock_debtors.list', compact('title','model','breadcum','permissions','employees','debtors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show($id)
    {
        if (!can('view', 'stock-debtors')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Debtors View';
        $model= 'stock-debtors';

        $breadcum = [
            'Stock Debtors View' => '',
            $title => ''
        ];
        $debtor = StockDebtor::with('employee','employee.location_stores','employee.userRestaurent','employee.userRole','employee.uom','stockDebtorTrans','stockDebtorTrans.items.inventoryItem')->find($id);
        $total = $debtor->getCurrentBalance();
        $items = StockDebtorTran::with('items','items.uom')->where('stock_debtors_id',$id)
        ->whereBetween('created_at', [$from, $to]);
        
        $openingBalance = StockDebtorTran::query()
            ->where('stock_debtors_id', $id)
            ->where('created_at', '<', $from)
            ->sum('total');

        if (request()->get('manage-request') == "pdf" || request()->get('manage-request') == "print"  || request()->get('manage-request') == "excel"  ) {
            $lists = $items->get();
            $number_series_list = WaNumerSeriesCode::getNumberSeriesTypeList();
            $pdf = \PDF::loadView('admin.stock_debtors.pdf', compact('lists', 'debtor', 'from', 'to','number_series_list','openingBalance'));
            // return $pdf->stream();
            return $pdf->download('stock_debtor_' . time() . '.pdf');
        }

        $numericSeries = DB::table('wa_numer_series_codes')->get();
        
        if (request()->wantsJson()) {
            return DataTables::of($items)                    
                    ->addColumn('total', function ($item) {
                        $total=0;
                        foreach ($item->items as $tot){
                            $total=$total + $tot->total;
                        }
                        return manageAmountFormat($item->total);
                    })
                    ->editColumn('created_at',function($item){
                        return date('d-m-Y',strtotime($item->created_at));
                    })
                    ->addColumn('debit', function ($query) {
                        
                        $total=$query->total;
                        $debit=0;
                        if ($total > 0) {
                            $debit = $total;
                        }
                        return manageAmountFormat($debit);
                    })
                    ->addColumn('credit', function ($query) {
                        $total=$query->total;
                        $credit=0;
                        if ($total < 0) {
                            $credit = $total;
                        }
                        return manageAmountFormat($credit);
                    })
                    ->addColumn('running_balance', function ($record) {
                        return manageAmountFormat($record->opening_balance + $record->total);
                    })
                    ->addColumn('transaction_type',function($transaction) use($numericSeries){
                        $exploded = explode('-',$transaction->document_no);
                        $type = $numericSeries->where('code',$exploded[0])->first();
                        return $type? ucfirst(strtolower(str_replace('_',' ',$type->module))) : '';
                    })
                    ->addColumn('stock_date',function($date){
                        $item = $date->items->first();
                        if(isset($item->stockCountVariation)){
                            return date('d-m-Y',strtotime($item->stockCountVariation->created_at));
                        }
                        return '-';
                    })
                    ->with('total', function () use ($total) {
                        return  manageAmountFormat($total);
                    })
                    ->with('opening_balance', function () use ($openingBalance) {
                        return manageAmountFormat($openingBalance);
                    })
                    ->toJson();
        }     
        
        return view('admin.stock_debtors.view', compact('title','model','breadcum','permissions','debtor','total','openingBalance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!can('add', 'stock-debtors')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            StockDebtor::create([
                'created_by'=> Auth::user()->id,
                'employee_id' => $request->employee
            ]);
            
            DB::commit();
            request()->session()->flash('success', 'Stock Debtor Added Successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }

        return redirect()->back();
    }

    public function get_balance($id)
    {
        return StockDebtorTran::where('stock_debtors_id',$id)->sum('price');
    }

    public function split(Request $request)
    {
        
        $totalSplit = array_sum($request->split_amount);
        
        if ($totalSplit > $request->total) {
            return response()->json([
                'result'=>-1,
                'message'=>'Split Amount Cannot be more than '.manageAmountFormat($request->total)
            ]);
        }

        if ($totalSplit ==0) {
            return response()->json([
                'result'=>-1,
                'message'=>'Split Amount Cannot be 0.00'
            ]);
        }
       
        
        $check = DB::transaction(function () use ($request,$totalSplit){
            
            $series_module = WaNumerSeriesCode::where('module', 'STOCK_DEBT_SPLIT')->first();
            $lastNumberUsed = $series_module->last_number_used;
            $newNumber = (int)$lastNumberUsed + 1;
            $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
            $series_module->update(['last_number_used' => $newNumber]);
    
            foreach ($request->split_debtor as $key => $debtor) {
                StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_debtors_id' => $debtor,
                    'document_no' => $newCode,
                    'total' => $request->split_amount[$key],
                ]);
            }
            StockDebtorTran::create([
                'created_by'=>Auth::user()->id,
                'stock_debtors_id' => $request->debtor,
                'document_no' => $newCode,
                'total' => -($totalSplit),
            ]);
            return true;
        });

        if($check){
            return response()->json([
                'result'=>1,
                'message'=>'Split Successfully.',
                ]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);   
    }

    public function split_non_debtors(Request $request)
    {
        
        $totalSplit = array_sum($request->split_amount);
        
        if ($totalSplit > $request->total) {
            return response()->json([
                'result'=>-1,
                'message'=>'Split Amount Cannot be more than '.manageAmountFormat($request->total)
            ]);
        }

        if ($totalSplit ==0) {
            return response()->json([
                'result'=>-1,
                'message'=>'Split Amount Cannot be 0.00'
            ]);
        }
       
        
        $check = DB::transaction(function () use ($request,$totalSplit){
            
            $series_module = WaNumerSeriesCode::where('module', 'STOCK_DEBT_SPLIT')->first();
            $lastNumberUsed = $series_module->last_number_used;
            $newNumber = (int)$lastNumberUsed + 1;
            $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
            $series_module->update(['last_number_used' => $newNumber]);
    
            foreach ($request->split_debtor as $key => $debtor) {
                StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_non_debtor_id' => $debtor,
                    'document_no' => $newCode,
                    'total' => $request->split_amount[$key],
                ]);
            }
            
            StockDebtorTran::create([
                'created_by'=>Auth::user()->id,
                'stock_debtors_id' => $request->debtor,
                'document_no' => $newCode,
                'total' => -($totalSplit),
            ]);
            return true;
        });

        if($check){
            return response()->json([
                'result'=>1,
                'message'=>'Split Successfully.',
                ]);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong']);   
    }

    public function split_users($id,$bin)
    {
        // GET ONLY STORE KEEPERS 
        // added POS Salesman and Cashier
        $employee = DB::table('stock_debtors')
        //->where('users.wa_unit_of_measures_id',$bin)
        ->select('stock_debtors.id','users.name','users.wa_unit_of_measures_id as bin')
        ->join('users','users.id','stock_debtors.employee_id')
        ->where('stock_debtors.id','!=',$id)
        ->whereIn('users.role_id',[152,169,170])
        ->get();
        return $employee;
    }

    public function split_users_non_debtors()
    {
        // GET ALL USERS EXCEPT DEBTORS
        $employee = User::doesntHave('stockDebtor')
        ->select('id','name')
        ->get();
        return $employee;
    }

    public function stock_non_debtors()
    {
        if (!can('store-loading-sheets', 'stock-non-debtors')) {
            return returnAccessDeniedPage();
        }

        $permissions = $this->mypermissionsforAModule();
        $title = 'Non Stock Debtors';
        $model= 'non-stock-debtors';
        
        $breadcum = [
            'Non Stock Debtors' => '',
            $title => ''
        ];

        $debtors = StockDebtorTran::whereHas('nonDebtor')->with('nonDebtor','nonDebtor.userRole')
                    ->select('stock_debtor_trans.*',
                    DB::Raw('SUM(stock_debtor_trans.total) as debtor_total'))
                    ->groupBy('stock_non_debtor_id');

        if (request()->wantsJson()) {
           
            $debtorItems = $debtors->get();
            $grandTotal = $debtorItems->sum('debtor_total'); 
            
            return DataTables::of($debtors)
                    ->addColumn('total', function($debtors){
                        return manageAmountFormat($debtors->debtor_total);
                    })
                    ->with('grand_total', function () use ($grandTotal) {
                        return manageAmountFormat($grandTotal);
                    })
                    ->toJson();
        }
        
        return view('admin.stock_debtors.non_debtors', compact('title','model','breadcum','permissions'));
    }

    public function stock_non_debtor_view($id)
    {
        if (!can('view', 'stock-non-debtors')) {
            return returnAccessDeniedPage();
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Non Stock Debtors View';
        $model= 'stock-non-debtors';

        $breadcum = [
            'Non Stock Debtors View' => '',
            $title => ''
        ];

        $debtor = User::find($id);
        $total = StockDebtorTran::whereHas('nonDebtor')->with('nonDebtor','nonDebtor.userRole')->where('stock_non_debtor_id',$id)->sum('total');
        $items = StockDebtorTran::whereHas('nonDebtor')->with('nonDebtor','nonDebtor.userRole')->where('stock_non_debtor_id',$id)
                    ->whereBetween('created_at', [$from, $to]);
        
        $openingBalance = StockDebtorTran::query()
            ->where('stock_non_debtor_id', $id)
            ->where('created_at', '<', $from)
            ->sum('total');

        if (request()->get('manage-request') == "pdf" || request()->get('manage-request') == "print"  || request()->get('manage-request') == "excel"  ) {
            $lists = $items->get();
            $number_series_list = WaNumerSeriesCode::getNumberSeriesTypeList();
            $pdf = \PDF::loadView('admin.stock_debtors.pdf', compact('lists', 'debtor', 'from', 'to','number_series_list','openingBalance'));
            // return $pdf->stream();
            return $pdf->download('stock_debtor_' . time() . '.pdf');
        }

        
        $numericSeries = DB::table('wa_numer_series_codes')->get();
        
        if (request()->wantsJson()) {
            $allStockDebtors = StockDebtor::all();
            return DataTables::of($items)                    
                    ->addColumn('total', function($debtors){
                        return manageAmountFormat($debtors->total);
                    })
                    ->editColumn('created_at',function($item){
                        return date('d-m-Y',strtotime($item->created_at));
                    })
                    ->addColumn('debit', function ($query) {
                        
                        $total=$query->total;
                        $debit=0;
                        if ($total > 0) {
                            $debit = $total;
                        }
                        return manageAmountFormat($debit);
                    })
                    ->addColumn('credit', function ($query) {
                        $total=$query->total;
                        $credit=0;
                        if ($total < 0) {
                            $credit = $total;
                        }
                        return manageAmountFormat($credit);
                    })
                    ->addColumn('running_balance', function ($record) {
                        return manageAmountFormat($record->opening_balance + $record->total);
                    })
                    ->addColumn('transaction_type', function($transaction) use($numericSeries){
                        $exploded = explode('-', $transaction->document_no);
                        $type = $numericSeries->where('code', $exploded[0])->first();
                        // Calculate the transaction type and store it in a custom property
                        $transaction->transaction_type = $type ? ucfirst(strtolower(str_replace('_', ' ', $type->module))) : '';
                        return $transaction->transaction_type;
                    })
                    ->addColumn('description', function($transaction){
                        $transType = $transaction->transaction_type;
                        $description = "";
                        if ($transType == "Stock debt split") {
                            $query = DB::table('stock_debtor_trans')
                                            ->select('users.name')
                                            ->where('stock_debtor_trans.document_no',$transaction->document_no)
                                            ->where('stock_debtor_trans.stock_non_debtor_id',null)
                                            ->join('stock_debtors','stock_debtors.id','stock_debtor_trans.stock_debtors_id')
                                            ->join('users','users.id','stock_debtors.employee_id')
                                            ->first();
                            $description = 'Debt Split from '. $query->name;
                        }
                        return $description;
                    })
                    ->addColumn('stock_date',function($date){
                        $item = $date->items->first();
                        if(isset($item->stockCountVariation)){
                            return date('d-m-Y',strtotime($item->stockCountVariation->created_at));
                        }
                        return '-';
                    })
                    ->with('total', function () use ($total) {
                        return  manageAmountFormat($total);
                    })
                    ->with('opening_balance', function () use ($openingBalance) {
                        return manageAmountFormat($openingBalance);
                    })
                    ->toJson();
        }     
        
        return view('admin.stock_debtors.non_debtor_view', compact('title','model','breadcum','permissions','debtor','total','openingBalance'));
    }

}

