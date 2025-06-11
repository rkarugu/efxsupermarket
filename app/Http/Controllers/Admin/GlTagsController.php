<?php

namespace App\Http\Controllers\Admin;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\GlTags;
use DB;
use App\Interfaces\GLTagInterface;

class GlTagsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    private GLTagInterface $glTagsRepository;

    public function __construct(GLTagInterface $glTagsRepository) {
        $this->model = 'gl_tags';
        $this->title = 'Gl Tags';
        $this->pmodule = 'gl_tags';
        $this->glTagsRepository = $glTagsRepository;
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
            $sortable_columns = ['id','title','description'];
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = GlTags::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['gl_tags___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('gl_tags.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-biz-greenish btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if(isset($permission['gl_tags___delete']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<form action="'.route('gl_tags.destroy',$re['id']).'" method="POST" class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fas fa-trash" aria-hidden="true"></i></button>
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
        
        return view('admin.gl_tags.index')->with($data);
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
        $data['data'] = GlTags::where('id',$id)->first();
        $data['url'] = route('gl_tags.update',$id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'create')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $validator = Validator::make($request->all(),[
            'title'=>'required|unique:gl_tags,title|max:200',
            'description'=>'nullable|max:250',
            
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $response = $this->glTagsRepository->storeGLTags($request);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('gl_tags.index')
            ]);
        } else {
            return response()->json([
                'result'=>-1,
                'message'=>$response->content()
            ]);
        }  
    }
    public function destroy($id)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'delete')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $response = $this->glTagsRepository->destroyGLTags($id);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('gl_tags.index')
            ]);
        } else {
            return response()->json([
                'result'=>-1,
                'message'=>$response->content()
            ]);
        }   
    }
    public function update(Request $request,$id)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'edit')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $validator = Validator::make($request->all(),[
            'id'=>'required|exists:gl_tags,id',
            'title'=>'required|unique:gl_tags,title,'.$id.'|max:200',
            'description'=>'nullable|max:250',
                        
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $response = $this->glTagsRepository->updateGLTags($request->id, $request);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('gl_tags.index')
            ]);
         } else {
            return response()->json([
                'result'=>-1,
                'message'=>$response->content()
            ]);
         }  
    }

    public function list()
    {
        return $this->glTagsRepository->getGLTags();
    }
}
