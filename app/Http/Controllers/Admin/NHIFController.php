<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Model\NHIF;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;

class NHIFController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'Nhif';
        $this->title = 'Nhif';
        $this->pmodule = 'Nhif';
        $this->pageUrl = 'Nhif';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.NHIF.index',compact(
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
       return view('admin.NHIF.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new NHIF();
                $row->from = $request->from;
                $row->to = $request->to;
                $row->rate = $request->rate;
                $row->amount =  $request->amount;
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
            'id', 'from','to','rate','amount', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  NHIF::select('wa_nhif.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('from', 'LIKE', "%{$search}%")
                    ->orWhere('to', 'LIKE', "%{$search}%")
                    ->orWhere('rate', 'LIKE', "%{$search}%")
                    ->orWhere('amount', 'LIKE', "%{$search}%");
                    
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
                $nestedData['from'] = $row->from;
                $nestedData['to'] = $row->to;
                $nestedData['rate'] = $row->rate;
                $nestedData['amount'] = $row->amount;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('Nhif.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('Nhif.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  NHIF::where('id',$editID)->first();
    return view('admin.NHIF.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  NHIF::where('id',$updata)->first();
 try{
                $upDate->from = $request->from;
                $upDate->to = $request->to;
                $upDate->rate = $request->rate;
                $upDate->amount =  $request->amount;
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
            NHIF::where('id',$slug)->delete();
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
    public function nhifList()
    {
        return response()->json(NHIF::orderBy('from')->get());
    }

    public function nhifCreate(Request $request)
    {
        $data = $request->validate([
            'from' => 'required',
            'to' => 'required',
            'rate' => 'required',
            'amount' => 'required',
        ]);

        try {
            $nhif = NHIF::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'NHIF added successfully',
            'data' => $nhif
        ], 201);
    }

    public function nhifEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_nhif,id',
            'from' => 'required',
            'to' => 'required',
            'rate' => 'required',
            'amount' => 'required',
        ]);

        try {
            $nhif = NHIF::find($request->id);

            array_shift($data);
            $nhif->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'NHIF updated successfully',
            'data' => $nhif
        ]);
    }

    public function nhifDelete($id)
    {
        try {
            $nhif = NHIF::find($id);

            $nhif->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'NHIF deleted successfully',
        ]);
    }
}