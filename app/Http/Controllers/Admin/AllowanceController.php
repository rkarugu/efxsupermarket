<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\Allowance;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class AllowanceController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'Allowance';
        $this->title = 'Allowance';
        $this->pmodule = 'Allowance';
        $this->pageUrl = 'Allowance';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Allowance.index',compact(
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
       return view('admin.Allowance.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new Allowance();
                $row->allowance = $request->allowance;
                $row->code = $request->code;
                if (!empty($request->taxable)) {
                    $row->taxable = $request->taxable;
                }
                if (!empty($request->recurring)) {
                    $row->recurring = $request->recurring;
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
            'id', 'allowance', 'code','taxable','recurring'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Allowance::select('wa_allowance.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('allowance', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
                    
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
                $nestedData['allowance'] = $row->allowance;
                $nestedData['code'] = $row->code;
                $nestedData['taxable'] = $row->taxable;
                $nestedData['recurring'] = $row->recurring;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('Allowance.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('Allowance.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  Allowance::where('id',$editID)->first();
    return view('admin.Allowance.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  Allowance::where('id',$updata)->first();
 try{
                $upDate->allowance = $request->allowance;
                $upDate->code = $request->code;
                if (!empty($request->taxable)) {
                    $upDate->taxable = $request->taxable;
                }else{
                  $upDate->taxable = 'Off';  
                }
                if (!empty($request->recurring)) {
                    $upDate->recurring = $request->recurring;
                }else{
                   $upDate->recurring = 'Off';   
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
            Allowance::where('id',$slug)->delete();
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
    public function allowanceList()
    {
        return response()->json(Allowance::orderBy('allowance')->get());
    }

    public function allowanceCreate(Request $request)
    {
        $data = $request->validate([
            'allowance' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $allowance = Allowance::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Allowance added successfully',
            'data' => $allowance
        ], 201);
    }

    public function allowanceEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_allowance,id',
            'allowance' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
            'taxable' => 'required|boolean',
        ]);

        try {
            $allowance = Allowance::find($request->id);

            array_shift($data);
            $allowance->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Allowance updated successfully',
            'data' => $allowance
        ]);
    }

    public function allowanceDelete($id)
    {
        try {
            $allowance = Allowance::find($id);

            $allowance->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Allowance deleted successfully',
        ]);
    }
}