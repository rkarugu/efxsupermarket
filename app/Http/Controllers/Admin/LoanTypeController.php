<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\LoanType;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class LoanTypeController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'loan-type';
        $this->title = 'Loan Type';
        $this->pmodule = 'loan-type';
        $this->pageUrl = 'loan-type';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.LoanType.index',compact(
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
       return view('admin.LoanType.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new LoanType();
                $row->loan_type = $request->loan_type;
                $row->interest_method = $request->interest_method;
                $row->annual_interest_rate = $request->annual_interest_rate;
                $row->code = $request->code;
                $row->description = $request->description;
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
            'id', 'loan_type', 'interest_method','annual_interest_rate','code'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  LoanType::select('wa_loan_type.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('loan_type', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('interest_method', 'LIKE', "%{$search}%")
                    ->orWhere('annual_interest_rate', 'LIKE', "%{$search}%");
                    
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
                $nestedData['loan_type'] = $row->loan_type;
                $nestedData['code'] = $row->code;
                $nestedData['interest_method'] = $row->interest_method;
                $nestedData['annual_interest_rate'] = $row->annual_interest_rate;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('loan-type.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('loan-type.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  LoanType::where('id',$editID)->first();
    return view('admin.LoanType.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  LoanType::where('id',$updata)->first();
 try{
                $upDate->loan_type = $request->loan_type;
                $upDate->interest_method = $request->interest_method;
                $upDate->annual_interest_rate = $request->annual_interest_rate;
                $upDate->code = $request->code;
                $upDate->description = $request->description;
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
            LoanType::where('id',$slug)->delete();
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
    public function loanTypeList()
    {
        return response()->json(LoanType::orderBy('loan_type')->get());
    }

    public function loanTypeCreate(Request $request)
    {
        $data = $request->validate([
            'loan_type' => 'required|string|max:255',
            'interest_method' => 'required|string|max:255',
            'annual_interest_rate' => 'required',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $loanType = LoanType::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Loan Type added successfully',
            'data' => $loanType
        ], 201);
    }

    public function loanTypeEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_loan_type,id',
            'loan_type' => 'required|string|max:255',
            'interest_method' => 'required|string|max:255',
            'annual_interest_rate' => 'required',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $loanType = LoanType::find($request->id);

            array_shift($data);
            $loanType->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Loan Type updated successfully',
            'data' => $loanType
        ]);
    }

    public function loanTypeDelete($id)
    {
        try {
            $loanType = LoanType::find($id);

            $loanType->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Loan Type deleted successfully',
        ]);
    }
}