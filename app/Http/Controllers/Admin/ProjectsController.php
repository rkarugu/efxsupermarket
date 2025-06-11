<?php

namespace App\Http\Controllers\Admin;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\Projects;
use DB;
use PDF;
use Session;
use App\Interfaces\ProjectInterface;

class ProjectsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    private ProjectInterface $projectRepository;

    public function __construct(ProjectInterface $projectRepository) {
        $this->model = 'projects';
        $this->title = 'Projects';
        $this->pmodule = 'projects';
        $this->projectRepository = $projectRepository;
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
            $response       = Projects::getData($limit , $start , $search, $sortable_columns[$orderby], $order,$request);            
            $totalCms       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = '<div style="display:flex">';
                 if(isset($permission['projects___edit']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<a href="'.route('projects.edit',$re['id']).'" data-id="'.$re['id'].'" onclick="openEditForm(this);return false;" class="btn btn-biz-greenish btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if(isset($permission['projects___delete']) || $permission == 'superadmin'){
                     $data[$key]['links'] .= '<form action="'.route('projects.destroy',$re['id']).'" method="POST"  class="deleteMe"><button class="btn btn-sm btn-danger" style="margin-left:4px" type="submit"><i class="fas fa-trash" aria-hidden="true"></i></button>
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
        
        return view('admin.projects.index')->with($data);
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
        $data['data'] = Projects::where('id',$id)->first();
        $data['url'] = route('projects.update',$id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data['permission'] =  $this->mypermissionsforAModule();        
        if(!$this->modulePermissions($data['permission'],'create')){
            return response()->json(['result'=>-1,'message'=>'Restricted! You dont have permissions']);
        }
        $validator = Validator::make($request->all(),[
            'title'=>'required|unique:projects,title|max:200',
            'description'=>'nullable|max:250',
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $response = $this->projectRepository->storeProject($request);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('projects.index')
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
        $response = $this->projectRepository->destroyProject($id);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('projects.index')
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
            'id'=>'required|exists:projects,id',
            'title'=>'required|unique:projects,title,'.$id.'|max:200',
            'description'=>'nullable|max:250',
                        
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ]);
        }
        $response = $this->projectRepository->updateProject($request->id, $request);
        if ($response->status() ==200) {
            return response()->json([
                'result'=>1,
                'message'=>$response->content(),
                'location'=>route('projects.index')
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
        return $this->projectRepository->getProjects();
    }

    public function monthlyProjectSummary(Request $request)
    {

        $title = $this->title;
        $model = 'project-report';
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $myData = [];
        $title = '';
        $lists=[];
        $monthRange=0;
        $selectedMonthArr=[];
        $gl_tags =  \App\Model\GlTags::get();
        $projects =  \App\Model\Projects::get();
        if (isset($permission['project-report___monthly']) || $permission == 'superadmin') {
            if($request->has('manage-request')){
                // $lists = \App\Model\WaGlTran::orderBy('id', 'desc');
                $start_date = null;
                $end_date = null;
                if ($request->has('start-date')){
                    $start_date=$request->input('start-date');
                    // $lists = $lists->where('created_at','>=',date('Y-m-01',strtotime($request->input('start-date'))));
                }
                if ($request->has('end-date')) {
                    $end_date=$request->input('end-date');
                    // $lists = $lists->where('created_at', '<=', date('Y-m-t',strtotime($request->input('end-date'))));
                }

                // $lists = $lists->get();
                $selectedMonthArr=getMonthsBetweenDates($start_date,$end_date);
                $monthRange=getMonthRangeBetweenDate($start_date,$end_date);

                
                if($monthRange > 12){
                    Session::flash('warning', "You can't select more than 12 months.");
                }  

                if($request->input('manage-request') == 'export'){
                    $pdf = PDF::loadView('admin.projects.projectsreportpdf', compact('projects','title', 'myData' ,'lists', 'model', 'breadcum','selectedMonthArr','monthRange','gl_tags'));
                    return $pdf->download('Monthly-Project-Summary.pdf');
                }
                if($request->input('manage-request') == 'excel'){
                    $data = [];
                    foreach($gl_tags as $gl_tag){
                        $child = [];
                        $child['GL'] = $gl_tag->title;
                        if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                            foreach($selectedMonthArr['m'] as $key => $month){
                                $year=$selectedMonthArr['y'][$key]; 
                                $created_from=date($year.'-'.$month.'-01');
                                $created_to=date($year.'-'.$month.'-t');
                                
                                $monthlyStock=0;
                                $monthlyStock=\App\Model\WaGlTran::where('gl_tag',$gl_tag->id)->where(function($e){
                                    if(request()->project){
                                        $e->where('project_id',request()->project);
                                    }
                                })->whereRaw(\DB::RAW("(CASE WHEN wa_gl_trans.transaction_type = 'Journal' THEN amount > 0 ELSE amount >= 0 OR amount <= 0 END)"))->whereYear('trans_date', $year)->whereMonth('trans_date', $month)->sum('amount'); 
                                
                                
                                $child[getMonthsNameToNumber($month)]=manageAmountFormat($monthlyStock);
                                $new_final_arr[$month.'-'.$year][]=$monthlyStock;

                            }
                        }
                        $data[] = $child;
                    }
                    if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])){
                        $child = [];
                        $child['GL'] = '';
                        foreach($selectedMonthArr['m'] as $key => $month){
                            $year=$selectedMonthArr['y'][$key]; 
                            $child[getMonthsNameToNumber($month)] = manageAmountFormat(array_sum($new_final_arr[$month.'-'.$year] ?? [0.00]));
                        }
                        $data[] = $child;
                    }
                    return \Excel::create('Monthly-Project-Summary', function($excel) use ($data) {
                        $from = "A1"; // or any value
                        $to = "G5"; // or any value
                            $excel->sheet('mySheet', function($sheet) use ($data)
                            {
                                $sheet->fromArray($data);
                            });
                        })->download('xls');
                }

            } 
            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.projects.projectsreport', compact('projects','title', 'myData' ,'lists', 'model', 'breadcum','selectedMonthArr','monthRange','gl_tags'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function gl_transaction_report(Request $request)
    {

        $title = "GL Account Summary";
        $model = 'gl-account-report-summary';
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $lists=[];
        if (isset($permission['gl-account-report___summary']) || $permission == 'superadmin') {
            if($request->has('manage-request')){
                $lists = \App\Model\WaGlTran::select([
                    DB::RAW('COALESCE(SUM(amount),0) as sum_am'),
                    'trans_date',
                    'transaction_no'
                ])->orderBy('id', 'desc');
                $start_date = null;
                $end_date = null;
                if ($request->has('start-date')){
                    $start_date=$request->input('start-date');
                    $lists = $lists->where('trans_date','>=',$request->input('start-date').' 00:00:00');
                }
                if ($request->has('end-date')) {
                    $end_date=$request->input('end-date');
                    $lists = $lists->where('trans_date', '<=', $request->input('end-date')." 23:59:59");
                }
                $lists = $lists->having('sum_am','!=',0)->groupBy('transaction_no')->get();
               
                if($request->input('manage-request') == 'excel'){
                    $data = [];
                    foreach($lists as $trans){
                        $child = [];
                        $child['Transaction Date'] = date('d/M/Y',strtotime($trans->trans_date));
                        $child['Transaction No'] = $trans->transaction_no;          
                        $child['Amount'] = manageAmountFormat($trans->sum_am);          
                        $data[] = $child;
                    }
                    $child = [];
                    $child['Transaction Date'] = 'Total';
                    $child['Transaction No'] = "";          
                    $child['Amount'] = manageAmountFormat($lists->sum('sum_am'));          
                    $data[] = $child;
                    return \Excel::create('GL-Transaction-Summary', function($excel) use ($data) {
                        $from = "A1"; // or any value
                        $to = "G5"; // or any value
                            $excel->sheet('mySheet', function($sheet) use ($data)
                            {
                                $sheet->fromArray($data);
                            });
                        })->download('xls');
                }
            } 
            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.projects.gl_transaction_report', compact('title', 'lists', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

}
