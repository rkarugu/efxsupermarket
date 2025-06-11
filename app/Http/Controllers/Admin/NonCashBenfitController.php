<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\NonCashBenfit;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class NonCashBenfitController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'non-cash-benfit';
        $this->title = 'Non Cash Benefit';
        $this->pmodule = 'non-cash-benfit';
        $this->pageUrl = 'non-cash-benfit';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.NonCashBenfit.index',compact(
                    'title','lists','model','breadcum','pmodule','permission'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function create(Request $request) {
       $title =$this->title; 
       $model = $this->model;
       return view('admin.NonCashBenfit.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new NonCashBenfit();
                $row->non_cash_benefit = $request->non_cash_benefit;
                $row->code = $request->code;
                $row->rate = $request->rate;
                if (!empty($request->recurring)) {
                    $row->recurring = $request->recurring;
                }
                if (!empty($request->use_rate)) {
                    $row->use_rate = $request->use_rate;
                }
                if (!empty($request->tax_able)) {
                    $row->taxable = $request->tax_able;
                }
                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index');  
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'id', 'non_cash_benefit', 'code','rate','use_rate','recurring','taxable'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  NonCashBenfit::select('wa_non_cash_benefit.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('non_cash_benefit', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('rate', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('use_rate', 'LIKE', "%{$search}%")
                    ->orWhere('recurring', 'LIKE', "%{$search}%");
                    
            });
            
        }
        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) {                  
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['non_cash_benefit'] = $row->non_cash_benefit;
                $nestedData['code'] = $row->code;
                $nestedData['rate'] = $row->rate;
                $nestedData['use_rate'] = $row->use_rate;
                $nestedData['recurring'] = $row->recurring;
                $nestedData['taxable'] = $row->taxable;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('non-cash-benfit.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('non-cash-benfit.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
" . csrf_field() . "<span><button data-toggle='tooltip' title='Delete' type='submit' class='btn btn-danger small-btn'><i class='fa fa-trash' aria-hidden='true'></i></button></span></form>";
          
                $data[] = $nestedData;
            }
        
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
}

  

  public function Edit(Request $request,$editID){
    $permission =  $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    $row =  NonCashBenfit::where('id',$editID)->first();
    return view('admin.NonCashBenfit.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  NonCashBenfit::where('id',$updata)->first();
 try{
                $upDate->non_cash_benefit = $request->non_cash_benefit;
                $upDate->code = $request->code;
                $upDate->rate = $request->rate;
                if (!empty($request->recurring)) {
                    $upDate->recurring = $request->recurring;
                }else{
                    $upDate->recurring = 'Off'; 
                }
                if (!empty($request->use_rate)) {
                    $upDate->use_rate = $request->use_rate;
                }else{
                 $upDate->use_rate = 'Off';
                }
                if (!empty($request->tax_able)) {
                    $upDate->taxable = $request->tax_able;
                }else{
                 $upDate->taxable = 'Off';
                }
               
                $upDate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');  
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
  }


   public function delete($slug)
    {
        try
        {
            NonCashBenfit::where('id',$slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    // API
    public function nonCashBenefitList()
    {
        return response()->json(NonCashBenfit::orderBy('non_cash_benefit')->get());
    }

    public function nonCashBenefitCreate(Request $request)
    {
        $data = $request->validate([
            'non_cash_benefit' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'rate' => 'required',
            'use_rate' => 'required|boolean',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $nonCashBenefit = NonCashBenfit::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Non Cash Benfit added successfully',
            'data' => $nonCashBenefit
        ], 201);
    }

    public function nonCashBenefitEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_non_cash_benefit,id',
            'non_cash_benefit' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'rate' => 'required',
            'use_rate' => 'required|boolean',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $nonCashBenefit = NonCashBenfit::find($request->id);

            array_shift($data);
            $nonCashBenefit->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Non Cash Benfit updated successfully',
            'data' => $nonCashBenefit
        ]);
    }

    public function nonCashBenefitDelete($id)
    {
        try {
            $nonCashBenefit = NonCashBenfit::find($id);

            $nonCashBenefit->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Non Cash Benfit deleted successfully',
        ]);
    }
}