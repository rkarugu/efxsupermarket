<?php

namespace App\Http\Controllers\Admin;

use Session;
use Exception;
use Illuminate\Http\Request;
use App\Model\FdSeasonsAttribute;
use App\Model\IndisciplineCategory;
use App\Http\Controllers\Controller;

class IndisciplineCategoryController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'indiscipline-category';
        $this->title = 'Indiscipline Category';
        $this->pmodule = 'Indiscipline Category';
        $this->pageUrl = 'Indiscipline Category';
    }

    public function index(Request $request) {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = FdSeasonsAttribute::orderBy('id', 'desc')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.IndisciplineCategory.index',compact(
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
       return view('admin.IndisciplineCategory.create',compact('title','model'));  
    }


    public function store(Request $request){
         try{
                $row = new IndisciplineCategory;
                $row->indiscipline_category= $request->indiscipline_category;
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
            'id', 'indiscipline_category', 'description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  IndisciplineCategory::select('wa_indiscipline_category.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('indiscipline_category', 'LIKE', "%{$search}%")
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
                $nestedData['indiscipline_category'] = $row->indiscipline_category;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('indiscipline-category.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('indiscipline-category.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
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
    $row =  IndisciplineCategory::where('id',$editID)->first();
    return view('admin.IndisciplineCategory.edit',compact('title','model','row'));
  }

  public function update(Request $request,$updata){
    $upDate =  IndisciplineCategory::where('id',$updata)->first();
 try{
                $upDate->indiscipline_category= $request->indiscipline_category;
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
            IndisciplineCategory::where('id',$slug)->delete();
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
    public function indisciplineCategoryList()
    {
        return response()->json(IndisciplineCategory::orderBy('indiscipline_category')->get());
    }

    public function indisciplineCategoryCreate(Request $request)
    {
        $data = $request->validate([
            'indiscipline_category' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $indisciplineCategory = IndisciplineCategory::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Category added successfully',
            'data' => $indisciplineCategory
        ], 201);
    }

    public function indisciplineCategoryEdit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wa_indiscipline_category,id',
            'indiscipline_category' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $indisciplineCategory = IndisciplineCategory::find($request->id);

            $indisciplineCategory->update([
                'indiscipline_category' => $request->indiscipline_category,
                'description' => $request->description,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Category updated successfully',
            'data' => $indisciplineCategory
        ]);
    }

    public function indisciplineCategoryDelete($id)
    {
        try {
            $indisciplineCategory = IndisciplineCategory::find($id);

            $indisciplineCategory->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Indiscipline Category deleted successfully',
        ]);
    }
}