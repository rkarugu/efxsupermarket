<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use App\Models\Gender;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GenderController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'gender';
        $this->title = 'Gender';
        $this->pmodule = 'gender';
        $this->pageUrl = 'gender';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Gender.list',compact(
                    'title','lists','model','breadcum','pmodule','permission'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function store(Request $request) {
       $title =$this->title; 
       $model = $this->model;
       return view('admin.Gender.create',compact('title','model'));       return view('admin.Gender.create',compact('title','model'));    return view('admin.gender.edit',compact());    }




     public function FromSave(Request $request)
    {
        try
        {
             $validator = Validator::make($request->all(), [
                'gender' => 'required|max:255|unique:wa_gender',
                'description' => 'required',
                ]);

            if ($validator->fails()) 
            {
                //echo "<pre>"; print_r($validator->errors()); die;
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new Gender();
                $row->gender= $request->gender;
                $row->description= $request->description;
                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e)
        {

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
            'id', 'gender', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Gender::select('wa_gender.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('gender', 'LIKE', "%{$search}%")
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
                $nestedData['Gender'] = $row->gender;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('gender.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('gender.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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

    public function StockadPrint(Request $request,$pData){
        $dataPdf = AdjustmentsSummary::where('id',$pData)->first();
        $userData = User::where('id',$dataPdf->user_id)->first();
        $pdf = PDF::loadView('admin.pdfviewStock', ['dataPdf' => $dataPdf,'userData' => $userData]);
      return $pdf->download('Stock Adjustments Reports.pdf');
  }

  public function edit(Request $request,$editID){
    $permission =  $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    $row =  Gender::where('id',$editID)->first();
    return view('admin.Gender.edit',compact('title','model','row'));
  }

 

   public function update(Request $request, $slug){
        try{
            $upDate =  Gender::where('id',$slug)->first();
            $validator = Validator::make($request->all(), [
                'gender' => 'required|max:255|unique:wa_gender,gender,' . $upDate->id,
                'description' => 'required',
               
                ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $upDate->gender= $request->gender;
                $upDate->description= $request->description;
                $upDate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
            }
           
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


   public function delete($slug)
    {
        try
        {
            Gender::where('id',$slug)->delete();
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
    public function genderList()
    {
        $genders = Gender::withCount('employees')->orderBy('name')->get();
        
        return response()->json($genders);
    }

    public function genderCreate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $gender = Gender::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Gender added successfully',
            'data' => $gender
        ], 201);
    }

    public function genderEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:genders,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $gender = Gender::find($request->id);

            $gender->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Gender updated successfully',
            'data' => $gender
        ]);
    }

    public function genderDelete($id)
    {
        request()->validate([
            'id' => 'exists:genders,id'
        ]);
        
        $gender = Gender::find($id);

        try {
            $gender->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Gender deleted successfully',
        ]);
    }

}