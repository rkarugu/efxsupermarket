<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use Illuminate\Http\Request;
use App\Model\TerminationTypes;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class TerminationTypesController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Termination Types';
        $this->pmodule = 'termination-types';
        $this->pageUrl = 'termination-types';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.TerminationTypes.index',compact(
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
       return view('admin.TerminationTypes.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new TerminationTypes();
                $row->separation_type= $request->separation_type;
                $row->description= $request->description;
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
            'id', 'separation_type', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  TerminationTypes::select('wa_termination_types.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('separation_type', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
                    
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
                $nestedData['separation_type'] = $row->separation_type;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('termination-types.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('termination-types.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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

  

  public function edit(Request $request,$editID){
    $permission =  $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    $row =  TerminationTypes::where('id',$editID)->first();
    return view('admin.TerminationTypes.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  TerminationTypes::where('id',$updata)->first();
 try{
                $upDate->separation_type= $request->separation_type;
                $upDate->description= $request->description;
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
            TerminationTypes::where('id',$slug)->delete();
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
    public function terminationTypeList()
    {
        return response()->json(TerminationTypes::orderBy('separation_type')->get());
    }

    public function terminationTypeCreate(Request $request)
    {
        $data = $request->validate([
            'separation_type' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $terminationType = TerminationTypes::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type added successfully',
            'data' => $terminationType
        ], 201);
    }

    public function terminationTypeEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wa_termination_types,id',
            'separation_type' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $terminationType = TerminationTypes::find($request->id);

            $terminationType->update([
                'separation_type' => $request->separation_type,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type updated successfully',
            'data' => $terminationType
        ]);
    }

    public function terminationTypeDelete($id)
    {
        try {
            $terminationType = TerminationTypes::find($id);

            $terminationType->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Termination Type deleted successfully',
        ]);
    }
}