<?php

namespace App\Http\Controllers\Admin;

use DB;
use Excel;
use Session;
use Exception;
use App\Model\Relief;
use Illuminate\Http\Request;
use App\Model\CustomParameter;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class CustomParameterController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'custom-parameter';
        $this->title = 'Custom Parameter';
        $this->pmodule = 'custom-parameter';
        $this->pageUrl = 'custom-parameter';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.CustomParameter.index',compact(
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
       return view('admin.CustomParameter.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new CustomParameter();
                $row->parameter = $request->parameter;
                $row->parameter_type = $request->parameter_type;
                $row->code = $request->code;
                if (!empty($request->recurring)) {
                    $row->recurring = $request->recurring;
                }
                if (!empty($request->taxable)) {
                    $row->taxable = $request->taxable;
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
            'id', 'parameter', 'code','parameter_type','taxable','recurring'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  CustomParameter::select('wa_custom_parameter.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('parameter', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('parameter_type', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('taxable', 'LIKE', "%{$search}%")
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
                $nestedData['parameter'] = $row->parameter;
                $nestedData['code'] = $row->code;
                $nestedData['parameter_type'] = $row->parameter_type;
                $nestedData['taxable'] = $row->taxable;
                $nestedData['recurring'] = $row->recurring;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('custom-parameter.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('custom-parameter.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  CustomParameter::where('id',$editID)->first();
    return view('admin.CustomParameter.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  CustomParameter::where('id',$updata)->first();
 try{
                $upDate->parameter_type = $request->parameter_type;
                $upDate->parameter = $request->parameter;
                $upDate->code = $request->code;
                if (!empty($request->recurring)) {
                    $upDate->recurring = $request->recurring;
                }else{
                    $upDate->recurring = 'Off'; 
                }
                if (!empty($request->taxable)) {
                    $upDate->taxable = $request->taxable;
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
            CustomParameter::where('id',$slug)->delete();
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
    public function customParameterList()
    {
        return response()->json(CustomParameter::orderBy('parameter')->get());
    }

    public function customParameterCreate(Request $request)
    {
        $data = $request->validate([
            'parameter' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'parameter_type' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $customParameter = CustomParameter::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Custom Parameter added successfully',
            'data' => $customParameter
        ], 201);
    }

    public function customParameterEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_custom_parameter,id',
            'parameter' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'parameter_type' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $customParameter = CustomParameter::find($request->id);

            array_shift($data);
            $customParameter->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Custom Parameter updated successfully',
            'data' => $customParameter
        ]);
    }

    public function customParameterDelete($id)
    {
        try {
            $customParameter = CustomParameter::find($id);

            $customParameter->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Custom Parameter deleted successfully',
        ]);
    }
}