<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\Sacco;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class SaccoController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'Sacco';
        $this->title = 'Sacco';
        $this->pmodule = 'Sacco';
        $this->pageUrl = 'Sacco';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Sacco.index',compact(
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
       return view('admin.Sacco.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new Sacco();
                $row->sacco = $request->sacco;
                $row->code = $request->code;
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
            'id', 'sacco', 'code','recurring'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Sacco::select('wa_sacco.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('sacco', 'LIKE', "%{$search}%")
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
                $nestedData['sacco'] = $row->sacco;
                $nestedData['code'] = $row->code;
                $nestedData['recurring'] = $row->recurring;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('Sacco.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('Sacco.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  Sacco::where('id',$editID)->first();
    return view('admin.Sacco.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  Sacco::where('id',$updata)->first();
 try{
                $upDate->sacco = $request->sacco;
                $upDate->code = $request->code;
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
            Sacco::where('id',$slug)->delete();
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
    public function saccoList()
    {
        return response()->json(Sacco::orderBy('sacco')->get());
    }

    public function saccoCreate(Request $request)
    {
        $data = $request->validate([
            'sacco' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
        ]);

        try {
            $sacco = Sacco::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Sacco added successfully',
            'data' => $sacco
        ], 201);
    }

    public function saccoEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_sacco,id',
            'sacco' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'recurring' => 'required|boolean',
        ]);

        try {
            $sacco = Sacco::find($request->id);

            array_shift($data);
            $sacco->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Sacco updated successfully',
            'data' => $sacco
        ]);
    }

    public function saccoDelete($id)
    {
        try {
            $sacco = Sacco::find($id);

            $sacco->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Sacco deleted successfully',
        ]);
    }
}