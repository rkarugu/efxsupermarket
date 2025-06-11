<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Model\IndisciplineAction;
use App\Http\Controllers\Controller;

class IndisciplineActionController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'indiscipline-action';
        $this->title = 'Indiscipline Action';
        $this->pmodule = 'Indiscipline Action';
        $this->pageUrl = 'Indiscipline Action';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.IndisciplineAction.index',compact(
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
       return view('admin.IndisciplineAction.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new IndisciplineAction;
                $row->indiscipline_action= $request->indiscipline_action;
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
            'id', 'indiscipline_action', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  IndisciplineAction::select('wa_indiscipline_action.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('indiscipline_action', 'LIKE', "%{$search}%")
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
                $nestedData['indiscipline_action'] = $row->indiscipline_action;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('indiscipline-action.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('indiscipline-action.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  IndisciplineAction::where('id',$editID)->first();
    return view('admin.IndisciplineAction.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  IndisciplineAction::where('id',$updata)->first();
 try{
                $upDate->indiscipline_action= $request->indiscipline_action;
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
            IndisciplineAction::where('id',$slug)->delete();
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
    public function indisciplineActionList()
    {
        return response()->json(IndisciplineAction::orderBy('indiscipline_action')->get());
    }

    public function indisciplineActionCreate(Request $request)
    {
        $data = $request->validate([
            'indiscipline_action' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $indisciplineAction = IndisciplineAction::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Action added successfully',
            'data' => $indisciplineAction
        ], 201);
    }

    public function indisciplineActionEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wa_indiscipline_action,id',
            'indiscipline_action' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $indisciplineAction = IndisciplineAction::find($request->id);

            $indisciplineAction->update([
                'indiscipline_action' => $request->indiscipline_action,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Action updated successfully',
            'data' => $indisciplineAction
        ]);
    }

    public function indisciplineActionDelete($id)
    {
        try {
            $indisciplineAction = IndisciplineAction::find($id);

            $indisciplineAction->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Action deleted successfully',
        ]);
    }
}