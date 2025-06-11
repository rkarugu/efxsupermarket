<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\FdSeasonsAttribute;
use Excel;
use App\Model\Employee;
use App\Model\WaDepartment;
use App\Model\LoanType;
use App\Model\Branch;
use App\Model\JobTitle;
use App\Model\OvertimeHours;
use App\Model\LoanEntries;
use Illuminate\Support\Facades\Validator;

class LoanEntriesController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'termination-types';
        $this->title = 'Loan Entries';
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
            return view('admin.LoanEntries.index',compact(
                    'title','lists','model','breadcum','pmodule','permission'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'id', 'staff_number','first_name','date_of_birth','job_title','branch_id','date_of_birth','date_employed','last_name','Id_number'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Employee::select('wa_employee.*')->where('status','Active');;
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('staff_number', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('date_of_birth', 'LIKE', "%{$search}%");
                    
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
               $depData = WaDepartment::where('id',$row->department_id)->first();                 
               $job_Data = JobTitle::where('id',$row->job_title)->first();                 
               $branch_Data = Branch::where('id',$row->branch_id)->first();                 
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['staff_number'] = $row->staff_number;
                $nestedData['first_name'] = $row->first_name . ' '. $row->middle_name . ' '. $row->last_name;
                $nestedData['Id_number'] = $row->Id_number;
                $nestedData['nhif_no'] = $row->nhif_no;
                $nestedData['nssf_no'] = $row->nssf_no;
                $nestedData['action'] =  "<a href='" . route('LoanEntries.addLoan',['id'=> $row->id])." '><button class='btn btn-primary'>Add Loan</button></a>";
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

  public function AddLoan($payID){
    $title = $this->title;
    $model = $this->model;
    $pmodule = $this->pmodule;
    $permission = $this->mypermissionsforAModule();
    $empData = Employee::where('id',$payID)->first();
    $loanTypeData = LoanType::pluck('loan_type','id');
    $loanEntriesData = LoanEntries::where([['emp_id',$payID],['active','Yes']])->get();
    $loanEntriesDeData = LoanEntries::where([['emp_id',$payID],['active','No']])->get();
    return view('admin.LoanEntries.manage',compact('empData','title','loanTypeData','loanEntriesData','loanEntriesDeData'));
  }



     public function CreateLoan(Request $request){
      try{
             $validator = Validator::make($request->all(), [
                'loan_type_id' => 'required|max:255',
                'no_of_installments' => 'required|max:255',
                'ref_number' => 'required|max:255',
                'monthly_deduction' => 'required|integer',
                'date' => 'required',
                'amount_applied' => "required|regex:/^\d+(\.\d{1,2})?$/",
                'memo' => 'required',
                ]);

            if ($validator->fails()){
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $loanEntriesCreate = new LoanEntries;
                $loanEntriesCreate->emp_id = $request->emp_id;
                $loanEntriesCreate->loan_type_id = $request->loan_type_id;
                $loanEntriesCreate->no_of_installments = $request->no_of_installments;
                $loanEntriesCreate->ref_number = $request->ref_number;
                $loanEntriesCreate->monthly_deduction = $request->monthly_deduction;
                $loanEntriesCreate->amount_applied = $request->amount_applied;
                $loanEntriesCreate->date = $request->date;
                $loanEntriesCreate->memo = $request->memo;
                $loanEntriesCreate->active = $request->active;
                $loanEntriesCreate->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back(); 
            }
        }
        catch(\Exception $e) {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
     }

   public function delete($deleteid){
     LoanEntries::where('id',$deleteid)->delete();
         Session::flash('success', 'Record Deleted successfully.');
                return redirect()->back(); 
   }
}