<?php

namespace App\Http\Controllers\Admin;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\Bodytype;
use DB;
class BodyTypeController extends Controller {
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'bodytype';
        $this->title = 'Body Type';
        $this->pmodule = 'bodytype';
    }
    public function modulePermissions($permission,$type)
    {
        // $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }
    public function index(Request $request)
    {
        $data['pmodule'] = $this->pmodule;
        $data['permission'] = $permission = $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'view')){
            return redirect()->back();
        }
        if($request->ajax()){
            $sortable_columns = ['id','title'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = Bodytype::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['bodytype___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('bodytype.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-danger btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if(isset($permission['bodytype___delete']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<form action="'.route('bodytype.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fa fa-trash" aria-hidden="true"></i></button>
                     <input type="hidden" value="DELETE" name="_method">
                     '.csrf_field().'
                     </form>';
                }
                $data[$key]['links'] .= '</div>';

                $data[$key]['dated'] = getDateFormatted($re['created_at']);
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totalCms),
                "recordsTotal"      =>  intval( $totalCms),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        
        return view('admin.bodytype.index')->with($data);
    }

    public function create()
    {
        abort(404);
    }
    public function edit($id)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'edit')){
            return response()->json(['status'=>0]);
        }
        $data['data'] = Bodytype::where('id',$id)->first();
        $data['url'] = route('bodytype.update',$id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'create')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $validator = Validator::make($request->all(),[
            'title'=>'required|unique:bodytype,title|max:200',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $check = DB::transaction(function () use ($request){
            $new = new Bodytype;
            $new->title = $request->title;
            $new->save();
            return true;
        });
        if($check){
            return response()->json([
                'result'=>1,
                'message'=>'Body type Stored Successfully',
                'location'=>route('bodytype.index')
            ]);
        }
        return response()->json([
                'result'=>-1,
                'message'=>'Somethine went wrong'
            ]);
    }
    public function destroy($id)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'delete')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $delete = Bodytype::where('id',$id)->first();
        if($delete){
            $delete->delete();
            return response()->json([
                'result'=>1,
                'message'=>'Body type Deleted Successfully',
                'location'=>route('bodytype.index')
            ]);
        }
        return response()->json([
                'result'=>-1,
                'message'=>'Somethine went wrong'
            ]);
    }
    public function update(Request $request,$id)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'edit')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:bodytype,id',
            'title'=>'required|unique:bodytype,title,'.$id.'|max:200',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $check = DB::transaction(function () use ($request){
            $new = Bodytype::where('id',$request->id)->first();
            $new->title = $request->title;
            $new->save();
            return true;
        });
        if($check){
            return response()->json([
                'result'=>1,
                'message'=>'Body type Updated Successfully',
                'location'=>route('bodytype.index')
            ]);
        }
        return response()->json([
                'result'=>-1,
                'message'=>'Somethine went wrong'
            ]);
    }
}