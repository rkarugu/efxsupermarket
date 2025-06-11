<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\Pension;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class PensionController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'pension';
        $this->title = 'Pension';
        $this->pmodule = 'pension';
        $this->pageUrl = 'pension';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Pension.index',compact(
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
       return view('admin.Pension.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new Pension();
                $row->pension = $request->pension;
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
            'id', 'pension', 'code','rate','use_rate','recurring','taxable'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Pension::select('wa_pension.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('pension', 'LIKE', "%{$search}%")
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
                $nestedData['pension'] = $row->pension;
                $nestedData['code'] = $row->code;
                $nestedData['rate'] = $row->rate;
                $nestedData['use_rate'] = $row->use_rate;
                $nestedData['recurring'] = $row->recurring;
                $nestedData['taxable'] = $row->taxable;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('pension.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('pension.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  Pension::where('id',$editID)->first();
    return view('admin.Pension.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  Pension::where('id',$updata)->first();
 try{
                $upDate->pension = $request->pension;
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
            Pension::where('id',$slug)->delete();
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
    public function pensionList()
    {
        return response()->json(Pension::orderBy('pension')->get());
    }

    public function pensionCreate(Request $request)
    {
        $data = $request->validate([
            'pension' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'rate' => 'required',
            'use_rate' => 'required|boolean',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $pension = Pension::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Pension added successfully',
            'data' => $pension
        ], 201);
    }

    public function pensionEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_pension,id',
            'pension' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'rate' => 'required',
            'use_rate' => 'required|boolean',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $pension = Pension::find($request->id);

            array_shift($data);
            $pension->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Pension updated successfully',
            'data' => $pension
        ]);
    }

    public function pensionDelete($id)
    {
        try {
            $pension = Pension::find($id);

            $pension->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Pension deleted successfully',
        ]);
    }
}