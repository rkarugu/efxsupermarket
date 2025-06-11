<?php

namespace App\Http\Controllers\Admin;
use Session;
use Exception;
use App\Model\Bank;
use App\Model\Relif;
use App\Model\Branch;
use App\Model\Gender;
use App\Model\Relief;
use App\Model\JobGrade;
use App\Model\JobGroup;
use App\Model\JobTitle;
use App\Model\WaEmpNext;
use App\Models\Employee;
use App\Model\Restaurant;
use App\Model\Salutation;
use App\Model\WaBankInfo;
use App\Model\WaContract;
use App\Model\EmpReferees;
use App\Model\PaymentModes;
use App\Model\WaDepartment;
use Illuminate\Support\Str;
use App\Model\MaritalStatus;
use App\Model\WaEmpContacts;
use Illuminate\Http\Request;
use App\Model\EducationLevel;
use App\Model\EmploymentType;
use App\Model\WaEmpDocuments;
use App\Model\WaEmpEducation;
use App\Model\WaEmpDependents;
use App\Model\WaEmpExperience;
use App\Model\WaWorkExprience;
use App\Model\EmploymentStatus;
use App\Model\PaymentFrequency;
use App\Models\EmployeeDocument;
use App\Model\IndisciplineAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Model\IndisciplineCategory;
use App\Models\EmployeeBeneficiary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Model\WaEmpIndisciplineCategory;
use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Model\WaCompanyPreference as CompanyPreference;

class EmployeeController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'employee';
        $this->title = 'Employee';
        $this->pmodule = 'employee';
        $this->pageUrl = 'employee';

    } 

    public function index()
    {
       
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin'){
            $lists = Branch::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.Emp.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function create()
    {
        $branchData = Branch::pluck('branch','id');
        $empType = EmploymentType::pluck('type','id');
        $departmentData = WaDepartment::pluck('department_name','id');
        $jobData = JobTitle::pluck('job_title','id');
        $genderData = Gender::pluck('gender','id');
        $marital_status = MaritalStatus::pluck('marital_status','id');
        $salutation = Salutation::pluck('salutation','id');
        $job_group = JobGroup::pluck('job_group','id');
        $payment_frequency = PaymentFrequency::pluck('frequency','id');
        $bankData = Bank::pluck('bank','id');
        $restaurantData = Restaurant::pluck('name','id');
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.Emp.create',compact('title','model','breadcum','branchData','empType','departmentData','jobData','genderData','marital_status','salutation','job_group','payment_frequency','bankData','restaurantData'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    public function store(Request $request){
     

        try
        {
             $validator = Validator::make($request->all(), [
                'staff_number' => 'required|unique:wa_employee|max:255',
                'emp_number' => 'required|unique:wa_employee|max:255',
                'branch_id' => 'required',
                'job_title_id' => 'required',
                'department_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender_id' => 'required',
                'id_number' => 'required',
                'nssf_no' => 'required',
                'photo_emp' => 'required',
                'marital_status_id' => 'required',
                'date_of_birth' => 'required',
                'date_employeed' => 'required',
                'salutation' => 'required',
                'pin_number' => 'required',
                'job_group' => 'required',
                'bank_id' => 'required',
                'account_number' => 'required',
                'basic_pay' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new Employee();
                 if (!empty($request->photo_emp)) {
           $image = $request->file('photo_emp');
                 $name = time().'.'.$image->getClientOriginalExtension();
           $destinationPath = public_path('/uploads/EmpImage');
           $image->move($destinationPath, $name);
       }
                $row->staff_number = $request->staff_number;
                $row->emp_number = $request->emp_number;
                $row->branch_id = $request->branch_id;
                $row->job_title = $request->job_title_id;
                $row->middle_name = $request->middle_name;
                $row->id_number = $request->id_number;
                $row->nssf_no = $request->nssf_no;
                $row->marital_status = $request->marital_status_id;
                $row->salutation_id = $request->salutation;
                $row->cellphone = $request->cellphone;
                $row->job_group_id = $request->job_group;
                $row->bank_id = $request->bank_id;
                if (!empty($request->status) == 'On') {
                    $row->status = 'Active';
                }else{
                    $row->status = 'DeActive';
                }
                $row->account_no = $request->account_number;
                $row->passport_number = $request->password_number;
                $row->postal_address = $request->postal_address;
                $row->town = $request->town;
                $row->country = $request->country;
                $row->emp_image = $name;
                $row->home_phone = $request->home_phone;
                $row->employment_type_id = $request->type_id;
                $row->department_id = $request->department_id;
                $row->first_name = $request->first_name;
                $row->last_name = $request->last_name;
                $row->nhif_no = $request->nhif_no;
                $row->gender_id = $request->gender_id;
                $row->date_of_birth = $request->date_of_birth;
                $row->date_employed = $request->date_employeed;
                $row->pin_number = $request->pin_number;
                $row->pay_frequency_id = $request->payment_frequency_id;
                $row->basic_pay = $request->basic_pay;
                $row->email_address = $request->email_address;
                $row->postal_code = $request->postal_code;
                $row->country = $request->country;
                $row->home_district = $request->home_district;
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
            'id', 'staff_number','first_name','date_of_birth','job_title','branch_id','date_of_birth','date_employed','last_name'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  Employee::select('wa_employee.*')->where('status','Active');
    
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
                $nestedData['first_name'] = $row->first_name;
                $nestedData['last_name'] = $row->last_name;
                $nestedData['department_id'] = $depData->department_name;
                $nestedData['job_title'] = $job_Data->job_title;
                $nestedData['date_employed'] = $row->date_employed;
                $nestedData['date_of_birth'] = $row->date_of_birth;
                $nestedData['branch_id'] = $branch_Data->branch;
                $nestedData['action'] =  "<span class='' ><a data-toggle='tooltip'  class='' style='color:black;font-size:18px;' title='Edit' href='" . route('employee.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>

                <span class='' ><a data-toggle='tooltip'  class='' style='color:black;font-size:18px;' title='Edit' href='" . route('employee.delete',['id'=> $row->id])." '><i class='fa fa-trash' aria-hidden='true'></i></a></span>

                     <span class='f-left margin-r-5'><a style='color:black;font-size:18px;' data-toggle='tooltip'  class='' title='Manage Employee' href='" . route('employee.manage',['id'=> $row->id])." '><i class='fa fa-adjust' aria-hidden='true'></i></a></span>
                         <span class='f-left margin-r-5'><a  style='color:black;font-size:18px;' data-toggle='tooltip'  class=' small-btn' title='Contract' href='" . route('emp.contract',['id'=> $row->id])." '><i class='fa fa-file' aria-hidden='true'></i></a></span>
                         <span class='f-left margin-r-5'><a  style='color:black;font-size:18px;' data-toggle='tooltip'  class=' small-btn' title='Indiscipline Category' href='" . route('emp.indisciplineCategory',['empData'=> $row->id])." '><i class='fa fa-fw fa-archive' aria-hidden='true'></i></a></span>";
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



    public function edit($slug){
        $branchDataUp = Branch::pluck('branch','id');
        $empTypeUp = EmploymentType::pluck('type','id');
        $departmentDataUp = WaDepartment::pluck('department_name','id');
        $jobDataUp = JobTitle::pluck('job_title','id');
        $genderDataUp = Gender::pluck('gender','id');
        $marital_statusUp = MaritalStatus::pluck('marital_status','id');
        $salutationUp = Salutation::pluck('salutation','id');
        $job_groupUp = JobGroup::pluck('job_group','id');
        $payment_frequencyUp = PaymentFrequency::pluck('frequency','id');
        $bankDataUp = Bank::pluck('bank','id');
        $restaurantDataEdit = Restaurant::pluck('name','id');
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  Employee::where('id',$slug)->first();
                if($row)
                {
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.Emp.edit',compact('title','model','breadcum','row','branchDataUp','empTypeUp','departmentDataUp','jobDataUp','genderDataUp','marital_statusUp','salutationUp','job_groupUp','payment_frequencyUp','bankDataUp','restaurantDataEdit')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
         try
        {
             $validator = Validator::make($request->all(), [
                'staff_number' => 'required|unique:wa_employee,staff_number,'.$slug.',id',
                'emp_number' => 'required|unique:wa_employee,emp_number,'.$slug.',id',
                'branch_id' => 'required',
                'job_title_id' => 'required',
                'department_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender_id' => 'required',
                'id_number' => 'required',
                'nssf_no' => 'required',
                'marital_status_id' => 'required',
                'date_of_birth' => 'required',
                'date_employeed' => 'required',
                'salutation' => 'required',
                'pin_number' => 'required',
                'job_group' => 'required',
                'bank_id' => 'required',
                'account_number' => 'required',
                'basic_pay' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $updateData =  Employee::where('id',$slug)->first();
                $updateData->staff_number = $request->staff_number;
                $updateData->branch_id = $request->branch_id;
                $updateData->job_title = $request->job_title_id;
                $updateData->middle_name = $request->middle_name;
                $updateData->id_number = $request->id_number;
                $updateData->emp_number = $request->emp_number;
                $updateData->nssf_no = $request->nssf_no;
                $updateData->marital_status = $request->marital_status_id;
                $updateData->salutation_id = $request->salutation;
                $updateData->cellphone = $request->cellphone;
                $updateData->job_group_id = $request->job_group;
                $updateData->bank_id = $request->bank_id;
                $updateData->account_no = $request->account_number;
                $updateData->passport_number = $request->password_number;
                $updateData->postal_address = $request->postal_address;
                $updateData->town = $request->town;
                $updateData->country = $request->country;
                $updateData->home_phone = $request->home_phone;
                $updateData->employment_type_id = $request->type_id;
                $updateData->department_id = $request->department_id;
                $updateData->first_name = $request->first_name;
                $updateData->last_name = $request->last_name;
                $updateData->nhif_no = $request->nhif_no;
                $updateData->gender_id = $request->gender_id;
                $updateData->date_of_birth = $request->date_of_birth;
                $updateData->date_employed = $request->date_employeed;
                $updateData->pin_number = $request->pin_number;
                $updateData->pay_frequency_id = $request->payment_frequency_id;
                $updateData->basic_pay = $request->basic_pay;
                $updateData->email_address = $request->email_address;
                $updateData->postal_code = $request->postal_code;
                $updateData->country = $request->country;
                $updateData->home_district = $request->home_district;
                $updateData->save();
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

    public function EmployeeManagee($emp_id){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $empData = Employee::where('id',$emp_id)->first();
        $genderDataM = Gender::pluck('gender','id');
        $marital_statusM = MaritalStatus::pluck('marital_status','id');
        $salutation_manage = Salutation::pluck('salutation','id');
        $bankDataM = Bank::pluck('bank','id');
        $branchDataM = Branch::pluck('branch','id');
        $pay_frequency =  PaymentFrequency::pluck('frequency','id');
        $payment_mode = PaymentModes::pluck('mode','id');
        $job_title = JobTitle::pluck('job_title','id');
        $relief = Relief::pluck('Relief','id');
        $bankDetail = WaBankInfo::where('emp_id',$empData->id)->first();
        $empExpData = WaEmpExperience::where('emp_id',$empData->id)->get();
        $nextKinStoreData = WaEmpNext::where('emp_id',$emp_id)->get();
        $dependents_Dataa = WaEmpDependents::where('emp_id',$emp_id)->get();
        $companyPreferenceData = CompanyPreference::pluck('name','id');
        $departmentDataMange = WaDepartment::pluck('department_name','id');
        $job_groupManage = JobGroup::pluck('job_group','id');
        $jobGradeManage = JobGrade::pluck('job_grade','id');
        $emp_status = EmploymentStatus::pluck('employment_status','id');
        $empTypeData = EmploymentType::pluck('type','id');
        $jobDataManage = JobTitle::pluck('job_title','id');
        $dataWorkExp = WaWorkExprience::where('emp_id',$emp_id)->first();
        $WaEmpEducationDtaa = WaEmpEducation::where('emp_id',$emp_id)->get();
        $WaEmpDocumentsData = WaEmpDocuments::where('emp_id',$emp_id)->get();
        $waEmpContactsData =WaEmpContacts::where('emp_id',$emp_id)->get();
        $empRefereesData =EmpReferees::where('emp_id',$emp_id)->get();
        $dataEducationLevel = EducationLevel::pluck('education_level','id');
        return view('admin.Emp.manage-employee',compact('pmodule','title','model','empData','genderDataM','marital_statusM','salutation_manage','bankDataM','branchDataM','pay_frequency','payment_mode','job_title','relief','bankDetail','empExpData','nextKinStoreData','dependents_Dataa','companyPreferenceData','departmentDataMange','job_groupManage','jobGradeManage','emp_status','empTypeData','jobDataManage','dataWorkExp','dataEducationLevel','WaEmpEducationDtaa','WaEmpDocumentsData','waEmpContactsData','empRefereesData'));
    }


    public function delete($slug){
        try
        {
             Employee::where('id',$slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

   public function RefereesDelete($refereesDeleteID){
        try
        {
             EmpReferees::where('id',$refereesDeleteID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function EmpExperienceStore(Request $request){
         try{
             $validator = Validator::make($request->all(), [
                'organization' => 'required|max:255',
                'from' => 'required',
                'reason_for_leaving' => 'required',
                'job_title_id' => 'required',
                'to' => 'required',
                'memo' => 'required',
                ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $empExperienceCreate = new WaEmpExperience;
                $empExperienceCreate->organization = $request->organization;
                $empExperienceCreate->emp_id = $request->emp_id;
                $empExperienceCreate->from = $request->from;
                $empExperienceCreate->reason_for_leaving = $request->reason_for_leaving;
                $empExperienceCreate->job_title_id = $request->job_title_id;
                $empExperienceCreate->to = $request->to;
                $empExperienceCreate->memo = $request->memo;
                $empExperienceCreate->save();
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

    public function EmpBankStore(Request $request){
        $bank_Info = new WaBankInfo;
        $bank_Info->bank_id = $request->bank_id;
        $bank_Info->emp_id = $request->emp_id;
        $bank_Info->pay_frequency_id = $request->pay_frequency_id;
        $bank_Info->relief_id = $request->relief_id;
        $bank_Info->valuntary_nssf = $request->valuntary_nssf;
        $bank_Info->branch_id = $request->branch_id;
        $bank_Info->account_name = $request->account_name;
        $bank_Info->account_number = $request->account_number;
        $bank_Info->payment_mode_id = $request->payment_mode_id;
        if ($bank_Info->save()) {
             Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index'); 
        }else{
             Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function EmpBankUpdate(Request $request,$upid){
          try
        {
             $validator = Validator::make($request->all(), [
                'bank_id' => 'required|max:255',
                'pay_frequency_id' => 'required',
                'relief_id' => 'required',
                'valuntary_nssf' => 'required',
                'branch_id' => 'required',
                'account_number' => 'required',
                'account_name' => 'required',
                'payment_mode_id' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $bank_Infoupdate = WaBankInfo::where('id',$upid)->first();
                $bank_Infoupdate->bank_id = $request->bank_id;
                $bank_Infoupdate->pay_frequency_id = $request->pay_frequency_id;
                $bank_Infoupdate->relief_id = $request->relief_id;
                $bank_Infoupdate->valuntary_nssf = $request->valuntary_nssf;
                $bank_Infoupdate->branch_id = $request->branch_id;
                $bank_Infoupdate->account_name = $request->account_name;
                $bank_Infoupdate->account_number = $request->account_number;
                $bank_Infoupdate->payment_mode_id = $request->payment_mode_id;
                $bank_Infoupdate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function DependentsStore(Request $request){
    try{
             $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'memo' => 'required',
                'relationship' => 'required',
                'date_of_birth' => 'required',
                'cellphone' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $emp_dependentsCreate = new WaEmpDependents;
                $emp_dependentsCreate->emp_id = $request->emp_id;
                $emp_dependentsCreate->name = $request->name;
                $emp_dependentsCreate->memo = $request->memo;
                $emp_dependentsCreate->relationship = $request->relationship;
                $emp_dependentsCreate->date_of_birth = $request->date_of_birth;
                $emp_dependentsCreate->cellphone = $request->cellphone;
                $emp_dependentsCreate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function NextKinStore(Request $request){
         try
          {
             $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'job_title_id' => 'required',
                'relationship' => 'required',
                'email' => 'required|email',
                'postal_address' => 'required',
                'organization' => 'required',
                'memo' => 'required',
                'profession' => 'required',
                'cellphone' => 'required',
                'physical_address' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $empNextCreate = new WaEmpNext;
                $empNextCreate->emp_id = $request->emp_id;
                $empNextCreate->name = $request->name;
                $empNextCreate->job_title_id = $request->job_title_id;
                $empNextCreate->relationship = $request->relationship;
                $empNextCreate->email = $request->email;
                $empNextCreate->postal_address = $request->postal_address;
                $empNextCreate->organization = $request->organization;
                $empNextCreate->memo = $request->memo;
                $empNextCreate->profession = $request->profession;
                $empNextCreate->cellphone = $request->cellphone;
                $empNextCreate->physical_address = $request->physical_address;
                $empNextCreate->save();
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

    public function Contract($dataid){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $empType = EmploymentType::pluck('type','id');
        $empDataGet = WaContract::where('emp_id',$dataid)->get();
        $emDatat2 = Employee::where('id',$dataid)->first();
        return view('admin.Emp.contract',compact('title','model','empType','empDataGet','emDatat2'));
    }

    public function ContractStore(Request $request){
        try
        {
             $validator = Validator::make($request->all(), [
                'contract_start_date' => 'required|max:255',
                'comment' => 'required',
                'emp_type' => 'required',
                'contract_end_date' => 'required',
                'staff_no' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                // dd('sds');
                $contactAdd = new WaContract;
                $contactAdd->contract_start_date = $request->contract_start_date;
                $contactAdd->emp_id = $request->emp_id;
                $contactAdd->comment = $request->comment;
                $contactAdd->emp_type = $request->emp_type;
                $contactAdd->contract_end_date = $request->contract_end_date;
                $contactAdd->staff_no = $request->staff_no; 
                $contactAdd->save();
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


    public function ContractDelete($deleteData){
        try
         {
             WaContract::where('id',$deleteData)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function ContactsDelete($eaEmpContactsID){
        try
         {
             WaEmpContacts::where('id',$eaEmpContactsID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }


    public function BioDataUpdate(Request $request ,$upDateID){
      try
          {
             $validator = Validator::make($request->all(), [
                'last_name' => 'required|max:255',
                'nhif_no' => 'required',
                'first_name' => 'required',
                'pin_number' => 'required',
                'date_of_birth' => 'required',
                'marital_status_id' => 'required',
                'cellphone' => 'required',
                'password_number' => 'required',
                'middle_name' => 'required',
                'staff_number' => 'required',
                'nssf_no' => 'required',
                'Id_number' => 'required',
                'gender_id' => 'required',
                'email_address' => 'required',
                'date_employeed' => 'required',
                'salutation' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
            $empUpdateData = Employee::where('id',$upDateID)->first();
            $name2 = $empUpdateData->emp_image;
          if (!empty($request->photo_emp)) {
              $image = $request->file('photo_emp');
              $name2 = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/uploads/EmpImage');
              $image->move($destinationPath, $name2);
            }
            if (!empty($request->curiculum_vitae)) {
              $image = $request->file('curiculum_vitae');
              $curiculum_vitae = time().'.'.$image->getClientOriginalExtension();
              $destinationPath = public_path('/uploads/EmpImage');
              $image->move($destinationPath, $curiculum_vitae);
            }


                $empUpdateData->first_name = $request->first_name;
                $empUpdateData->last_name = $request->last_name;
                $empUpdateData->nhif_no = $request->nhif_no;
                $empUpdateData->pin_number = $request->pin_number;
                $empUpdateData->date_of_birth = $request->date_of_birth;
                $empUpdateData->marital_status = $request->marital_status_id;
                $empUpdateData->cellphone = $request->cellphone;
                $empUpdateData->passport_number = $request->password_number;
                $empUpdateData->years_of_service = $request->years_of_service;
                $empUpdateData->driving_license = $request->driving_license;
                $empUpdateData->staff_number = $request->staff_number;
                $empUpdateData->middle_name = $request->middle_name;
                $empUpdateData->home_district = $request->home_district;
                $empUpdateData->nssf_no = $request->nssf_no;
                $empUpdateData->Id_number = $request->Id_number;
                $empUpdateData->gender_id = $request->gender_id;
                $empUpdateData->email_address = $request->email_address;
                $empUpdateData->salutation_id = $request->salutation;
                $empUpdateData->date_employed = $request->date_employeed;
                $empUpdateData->date_terminated = $request->date_terminated;
                $empUpdateData->ethnicity = $request->ethnicity;
                if (!empty($request->status) == 'On') {
                     $empUpdateData->status = 'Active';
                }else{
                     $empUpdateData->status = 'DeActive';
                }
                $empUpdateData->pension_number = $request->pension_number;
                $empUpdateData->sacco_member_no = $request->sacco_member_no;
                $empUpdateData->helb_number = $request->helb_number;
                if (!empty($request->curiculum_vitae)) {
                   $empUpdateData->curiculum_vitae = $curiculum_vitae;
                }
                $empUpdateData->emp_image = $name2;
                $empUpdateData->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->back();
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

    public function IndisciplineCategory($cateID){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $dataEmp3 = Employee::where('id',$cateID)->first(); 
        $waEmpIndisciplineCategoryData =  WaEmpIndisciplineCategory::where('emp_id',$cateID)->get();
        $indisciplineCat = IndisciplineCategory::pluck('indiscipline_category','id');
        $indisciplineCAction = IndisciplineAction::pluck('indiscipline_action','id');
        return view('admin.Emp.indisciplineCategory',compact('dataEmp3','title','indisciplineCat','indisciplineCAction','waEmpIndisciplineCategoryData'));
    }

    public function IndisciplineCreate(Request $request){
          try
        {
             $validator = Validator::make($request->all(), [
                'indiscipline_category_id' => 'required|max:255',
                'effective_date' => 'required',
                'action_id' => 'required',
                'cost_charge' => 'required',
                'indiscipline' => 'required',
                'loction' => 'required',
                'descrption' => 'required',
                'attach_letter' => 'required',
                
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
             $wa_empindiscipline_Create = new WaEmpIndisciplineCategory;
            if (!empty($request->attach_letter)) {
                $image = $request->file('attach_letter');
                $attach_letterData = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/EmpImage');
                $image->move($destinationPath, $attach_letterData);
       }
                $wa_empindiscipline_Create->emp_id = $request->emp_id;
                $wa_empindiscipline_Create->indiscipline_category_id = $request->indiscipline_category_id;
                $wa_empindiscipline_Create->effective_date = $request->effective_date;
                $wa_empindiscipline_Create->action_id = $request->action_id;
                $wa_empindiscipline_Create->cost_charge = $request->cost_charge;
                $wa_empindiscipline_Create->indiscipline = $request->indiscipline;
                $wa_empindiscipline_Create->loction = $request->loction;
                $wa_empindiscipline_Create->descrption = $request->descrption;
                $wa_empindiscipline_Create->attach_letter = $attach_letterData;
                $wa_empindiscipline_Create->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

      public function IndisciplineDelete($deleteData){
        try
         {
             WaEmpIndisciplineCategory::where('id',$deleteData)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function EmpExperience($deleteData){
        try
         {
             WaEmpExperience::where('id',$deleteData)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function NextKinDelete($next_KinDeleteID){
        try
         {
             WaEmpNext::where('id',$next_KinDeleteID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    } 

    public function DependentsDelete($DependentsDelete){
        try
         {
             WaEmpDependents::where('id',$DependentsDelete)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function EductionDelete($EductionDeleteID){
        try
         {
             WaEmpEducation::where('id',$EductionDeleteID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function DocusDelete($DocusDeleteID){
        try
         {
             WaEmpDocuments::where('id',$DocusDeleteID)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function BankInformation(Request $request){
        // dd($request->all());
        try{
             $validator = Validator::make($request->all(), [
                'company_id' => 'required|max:255',
                'region' => 'required',
                'department_id' => 'required',
                'job_group_id' => 'required',
                'job_grade_id' => 'required',
                'shift' => 'required',
                'manager' => 'required',
                'probation_start_date' => 'required',
                'employement_status' => 'required',
                'branch_id' => 'required',
                'station' => 'required',
                'section' => 'required',
                'designation_id' => 'required',
                'employement_type_id' => 'required',
                'home_phone' => 'required',
                'hod' => 'required',
                'date_of_confirmation' => 'required',
                'probation_end_date' => 'required',
                
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $waWorkExprienceSave = new WaWorkExprience;
                $waWorkExprienceSave->emp_id = $request->emp_id;
                $waWorkExprienceSave->company_id = $request->company_id;
                $waWorkExprienceSave->region = $request->region;
                $waWorkExprienceSave->department_id = $request->department_id;
                $waWorkExprienceSave->job_group_id = $request->job_group_id;
                $waWorkExprienceSave->job_grade_id = $request->job_grade_id;
                $waWorkExprienceSave->shift = $request->shift;
                $waWorkExprienceSave->manager = $request->manager;
                $waWorkExprienceSave->employement_status = $request->employement_status;
                $waWorkExprienceSave->probation_start_date = $request->probation_start_date;
                $waWorkExprienceSave->branch_id = $request->branch_id;
                $waWorkExprienceSave->station = $request->station;
                $waWorkExprienceSave->section = $request->section;
                $waWorkExprienceSave->designation_id = $request->designation_id;
                $waWorkExprienceSave->employement_type_id = $request->employement_type_id;
                $waWorkExprienceSave->home_phone = $request->home_phone;
                $waWorkExprienceSave->hod = $request->hod;
                $waWorkExprienceSave->date_of_confirmation = $request->date_of_confirmation;
                $waWorkExprienceSave->probation_end_date = $request->probation_end_date;
                $waWorkExprienceSave->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }


    public function BankInformationUpdate(Request $request,$upIDEr){
        // dd($request->all());
        try{
             $validator = Validator::make($request->all(), [
                'company_id' => 'required|max:255',
                'region' => 'required',
                'department_id' => 'required',
                'job_group_id' => 'required',
                'job_grade_id' => 'required',
                'shift' => 'required',
                'manager' => 'required',
                'probation_start_date' => 'required',
                'employement_status' => 'required',
                'branch_id' => 'required',
                'station' => 'required',
                'section' => 'required',
                'designation_id' => 'required',
                'employement_type_id' => 'required',
                'home_phone' => 'required',
                'hod' => 'required',
                'date_of_confirmation' => 'required',
                'probation_end_date' => 'required',
                
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $waWorkExprienceUpdate = WaWorkExprience::where('id',$upIDEr)->first();
                $waWorkExprienceUpdate->emp_id = $request->emp_id;
                $waWorkExprienceUpdate->company_id = $request->company_id;
                $waWorkExprienceUpdate->region = $request->region;
                $waWorkExprienceUpdate->department_id = $request->department_id;
                $waWorkExprienceUpdate->job_group_id = $request->job_group_id;
                $waWorkExprienceUpdate->job_grade_id = $request->job_grade_id;
                $waWorkExprienceUpdate->shift = $request->shift;
                $waWorkExprienceUpdate->manager = $request->manager;
                $waWorkExprienceUpdate->employement_status = $request->employement_status;
                $waWorkExprienceUpdate->probation_start_date = $request->probation_start_date;
                $waWorkExprienceUpdate->branch_id = $request->branch_id;
                $waWorkExprienceUpdate->station = $request->station;
                $waWorkExprienceUpdate->section = $request->section;
                $waWorkExprienceUpdate->designation_id = $request->designation_id;
                $waWorkExprienceUpdate->employement_type_id = $request->employement_type_id;
                $waWorkExprienceUpdate->home_phone = $request->home_phone;
                $waWorkExprienceUpdate->hod = $request->hod;
                $waWorkExprienceUpdate->date_of_confirmation = $request->date_of_confirmation;
                $waWorkExprienceUpdate->probation_end_date = $request->probation_end_date;
                $waWorkExprienceUpdate->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }

    public function EductionCreate(Request $request){
         // dd($request->all());
        try{
             $validator = Validator::make($request->all(), [
                'course' => 'required|max:255',
                'institution' => 'required',
                'to' => 'required',
                'point' => 'required',
                'memo' => 'required',
                'education_level_id' => 'required',
                'from' => 'required',
                'job_grade_id' => 'required',
                'ranking' => 'required',
                
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $waEmpEducationCreate = new WaEmpEducation;
                $waEmpEducationCreate->course = $request->course;
                $waEmpEducationCreate->to = $request->to;
                $waEmpEducationCreate->emp_id = $request->emp_id;
                $waEmpEducationCreate->memo = $request->memo;
                $waEmpEducationCreate->education_level_id = $request->education_level_id;
                $waEmpEducationCreate->from = $request->from;
                $waEmpEducationCreate->point = $request->point;
                $waEmpEducationCreate->ranking = $request->ranking;
                $waEmpEducationCreate->job_grade_id = $request->job_grade_id;
                $waEmpEducationCreate->institution = $request->institution;
                $waEmpEducationCreate->save();
                Session::flash('success', 'Record Created successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }

    public function EductionDocumentCreate(Request $request){
        try{
             $validator = Validator::make($request->all(), [
                'document' => 'required|max:255',
                'ref_number' => 'required',
                'issued_by' => 'required',
                'expiry_date' => 'required',
                'issue_date' => 'required',
                'received_date' => 'required',
                'select_file' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $waEmpDocumentsCreate = new WaEmpDocuments;

                if (!empty($request->select_file)) {
                $image = $request->file('select_file');
                $select_fileImg = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/EmpDocument');
                $image->move($destinationPath, $select_fileImg);
                }
                $waEmpDocumentsCreate->ref_number = $request->ref_number;
                $waEmpDocumentsCreate->document = $request->document;
                $waEmpDocumentsCreate->issued_by = $request->issued_by;
                $waEmpDocumentsCreate->emp_id = $request->emp_id;
                $waEmpDocumentsCreate->expiry_date = $request->expiry_date;
                $waEmpDocumentsCreate->received_date = $request->received_date;
                $waEmpDocumentsCreate->issue_date = $request->issue_date;
                $waEmpDocumentsCreate->descrption = $request->descrption;
                $waEmpDocumentsCreate->select_file = $select_fileImg;
                $waEmpDocumentsCreate->save();
                Session::flash('success', 'Record Created successfully.');
                return redirect()->back();
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }


    public function ContactsCreate(Request $request){
        // dd($request->all());
        try{
             $validator = Validator::make($request->all(), [
                'postal_addess' => 'required|max:255',
                'postal_code' => 'required',
                'country' => 'required',
                'street_address' => 'required',
                'town' => 'required',
                'mobile' => 'required|max:14',
                'emergency_contact_cellphone' => 'required|max:14',
                'home_telephone' => 'required|max:14',
                'work_telephone' => 'required|max:14',
                'emergency_contact_person' => 'required|max:14',
                'emergency_contact_relationship' => 'required|max:14',
                'email_address' => 'required|email',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $waContactsCreate = new WaEmpContacts;
                $waContactsCreate->emp_id = $request->emp_id;
                $waContactsCreate->postal_addess= $request->postal_addess;
                $waContactsCreate->postal_code= $request->postal_code;
                $waContactsCreate->country= $request->country;
                $waContactsCreate->mobile= $request->mobile;
                $waContactsCreate->email_address= $request->email_address;
                $waContactsCreate->emergency_contact_cellphone= $request->emergency_contact_cellphone;
                $waContactsCreate->street_address= $request->street_address;
                $waContactsCreate->town= $request->town;
                $waContactsCreate->home_telephone= $request->home_telephone;
                $waContactsCreate->work_telephone= $request->work_telephone;
                $waContactsCreate->emergency_contact_person= $request->emergency_contact_person;
                $waContactsCreate->emergency_contact_relationship= $request->emergency_contact_relationship;
                $waContactsCreate->save();
                Session::flash('success', 'Record Created successfully.');
                return redirect()->back();
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }

    public function EmpRefereesCreate(Request $request){
        // dd($request->all());
        try{
             $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'postal_address' => 'required',
                'postal_code' => 'required',
                'organization' => 'required',
                'profession' => 'required',
                'cellphone' => 'required',
                'notes' => 'required',
                'memo' => 'required',
                'physical_address' => 'required',
                'email' => 'required|email',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else{
                $empRefereesCreate = new EmpReferees;
                $empRefereesCreate->emp_id = $request->emp_id;
                $empRefereesCreate->name = $request->name;
                $empRefereesCreate->postal_code = $request->postal_code;
                $empRefereesCreate->email = $request->email;
                $empRefereesCreate->postal_address = $request->postal_address;
                $empRefereesCreate->notes = $request->notes;
                $empRefereesCreate->organization = $request->organization;
                $empRefereesCreate->cellphone = $request->cellphone;
                $empRefereesCreate->memo = $request->memo;
                $empRefereesCreate->profession = $request->profession;
                $empRefereesCreate->physical_address = $request->physical_address;
                $empRefereesCreate->save();
                Session::flash('success', 'Record Created successfully.');
                return redirect()->back();
            }
        }
        catch(\Exception $e){
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        } 
    }

    // API

    public function employeesList()
    {
        $employees = Employee::with('branch', 'jobTitle', 'employmentType')
            ->latest()
            ->get();
        
        return response()->json($employees);
    }

    public function employeesCreate(EmployeeRequest $request)
    {        
        $data = $request->validated();

        $bankData = $request->validate([
            'bank_id' => 'nullable|integer|exists:banks,id',
            'bank_branch_id' => 'nullable|integer|exists:bank_branches,id',
            'account_name' => 'nullable|string',
            'account_no' => 'nullable|string',
        ]);

        $request->validate([
            'emergency_contacts' => 'required_if:is_draft,false|array',
            'emergency_contacts.*' => 'required_if:is_draft,false'
        ]);
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('uploads/employee_images', 'public');
        }

        DB::beginTransaction();
        try {
            $employee = Employee::create($data);

            // Create employee contract
            if (isset(
                $request->branch_id, 
                $request->department_id, 
                $request->employment_type_id, 
                $request->job_title_id, 
                $request->employment_date, 
                $request->contract_end_date
            )) {
                $employee->contracts()->create([
                    'branch_id' => $request->branch_id,
                    'department_id' => $request->department_id,
                    'employment_type_id' => $request->employment_type_id,
                    'job_title_id' => $request->job_title_id,
                    'start_date' => $request->employment_date,
                    'end_date' => $request->contract_end_date,
                ]);
            }
            
            // Create bank account
            if (isset(
                $bankData['bank_id'],
                $bankData['bank_branch_id'],
                $bankData['account_name'],
                $bankData['account_no']
            )) {
                $bankData['primary'] = true;
                $employee->employeeBankAccounts()->create($bankData);
            }

            // Create Emergency Contacts
            if ($request->emergency_contacts) {
                foreach($request->emergency_contacts as $emergencyContact) {
                    $emergencyContact = json_decode($emergencyContact, true);
    
                    $employee->emergencyContacts()->create($emergencyContact);
                }
            }

            // Create beneficiaries
            if ($request->beneficiaries) {
                foreach($request->beneficiaries as $beneficiary) {
                    $beneficiary = json_decode($beneficiary, true);
    
                    $employee->beneficiaries()->create($beneficiary);
                }
            }

            // Create employee documents
            $documentKeys = array_filter(array_keys($request->all()), function ($key) {
                return Str::startsWith($key, 'document');
            });
    
            foreach($documentKeys as $documentKey) {
                if ($request->hasFile($documentKey)) {
                    $documentKeyArray = explode('-', $documentKey);
                    $employee->documents()->create([
                        'document_type_id' => array_pop($documentKeyArray),
                        'file_path' => $request->file($documentKey)->store('uploads/employee_documents', 'public')
                    ]);
                }
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        if ($request->is_draft == 'false') {
            $message = "Employee created successfully";
        } else {
            $message = "Saved as draft successfully";
        }

        return response()->json([
            'data' => $employee,
            'message' => $message
        ], 201);
    }

    public function employeesDraftEdit(EmployeeRequest $request)
    {        
        $data = $request->validated();

        $bankData = $request->validate([
            'bank_id' => 'nullable|integer|exists:banks,id',
            'bank_branch_id' => 'nullable|integer|exists:bank_branches,id',
            'account_name' => 'nullable|string',
            'account_no' => 'nullable|string',
        ]);

        $employee = Employee::withoutGlobalScope('draft')
            ->with('currentContract')
            ->find($data['id']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('uploads/employee_images', 'public');

            if ($employee->image) {
                if (Storage::disk('public')->exists($employee->image)) {
                    Storage::disk('public')->delete($employee->image);
                }
            }
        }

        DB::beginTransaction();
        try {
            $employee->update($data);

            if (isset(
                $bankData['bank_id'],
                $bankData['bank_branch_id'],
                $bankData['account_name'],
                $bankData['account_no']
            )) {

                $bankAccount = $employee->primaryBankAccount;
                if ($bankAccount) {
                    $bankAccount->update($bankData);
                } else {
                    $bankData['primary'] = true;
                    $employee->employeeBankAccounts()->create($bankData);
                }
            }

            // Update employee contract
            if (isset(
                $request->branch_id, 
                $request->department_id, 
                $request->employment_type_id, 
                $request->job_title_id, 
                $request->employment_date, 
                $request->contract_end_date
            )) {
                $contract = $employee->currentContract;
                
                $contractData = [
                    'branch_id' => $request->branch_id,
                    'department_id' => $request->department_id,
                    'employment_type_id' => $request->employment_type_id,
                    'job_title_id' => $request->job_title_id,
                    'start_date' => $request->employment_date,
                    'end_date' => $request->contract_end_date,
                ];
                
                if ($contract) {
                    $contract->update($contractData);
                } else {
                    $employee->contracts()->create($contractData);
                }
            }

            // Update emergency contacts
            if ($request->emergency_contacts) {
                foreach($request->emergency_contacts as $emergencyContact) {
                    $emergencyContact = json_decode($emergencyContact, true);

                    $id = array_shift($emergencyContact);
                    if ($id) {
                        $contact = EmployeeEmergencyContact::find($id);

                        $contact->update($emergencyContact);
                    } else {
                        $employee->emergencyContacts()->create($emergencyContact);
                    }
                }
            }
            if ($request->deleted_emergency_contacts) {
                EmployeeEmergencyContact::whereIn('id', $request->deleted_emergency_contacts)->delete();
            }

            // Update beneficiaries
            if ($request->beneficiaries) {
                foreach($request->beneficiaries as $beneficiary) {
                    $beneficiary = json_decode($beneficiary, true);

                    $id = array_shift($beneficiary);
                    if ($id) {
                        $contact = EmployeeBeneficiary::find($id);
                                          
                        $contact->update($beneficiary);
                    } else {
                        $employee->beneficiaries()->create($beneficiary);
                    }
                }
            }
            if ($request->deleted_beneficiaries) {
                EmployeeBeneficiary::whereIn('id', $request->deleted_beneficiaries)->delete();
            }

            // Update employee documents
            $documentKeys = array_filter(array_keys($request->all()), function ($key) {
                return Str::startsWith($key, 'document');
            });
    
            foreach($documentKeys as $documentKey) {
                if ($request->hasFile($documentKey)) {
                    $documentKeyArray = explode('-', $documentKey);
                    $documentTypeId = array_pop($documentKeyArray);

                    $document = EmployeeDocument::where('document_type_id', $documentTypeId)->where('employee_id', $employee->id)->first();

                    if ($document) {
                        if ($document->file_path) {
                            if (Storage::disk('public')->exists($document->file_path)) {
                                Storage::disk('public')->delete($document->file_path);
                            }
                        }
                        
                        $document->update([
                            'file_path' => $request->file($documentKey)->store('uploads/employee_documents', 'public')
                        ]);
                    } else {
                        $employee->documents()->create([
                            'document_type_id' => $documentTypeId,
                            'file_path' => $request->file($documentKey)->store('uploads/employee_documents', 'public')
                        ]);
                    }
                    
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        if ($request->is_draft == 'false') {
            $message = "Employee created successfully";
        } else {
            $message = "Saved as draft successfully";
        }

        return response()->json([
            'message' => $message
        ]);
    }

    public function employeesEdit(EmployeeRequest $request)
    {
        $data = $request->validated();

        $bankData = $request->validate([
            'bank_id' => 'nullable|integer|exists:banks,id',
            'bank_branch_id' => 'nullable|integer|exists:bank_branches,id',
            'account_name' => 'nullable|string',
            'account_no' => 'nullable|string',
        ]);

        $employee = Employee::find($data['id']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('uploads/employee_images', 'public');

            if ($employee->image) {
                if (Storage::disk('public')->exists($employee->image)) {
                    Storage::disk('public')->delete($employee->image);
                }
            }
        }

        try {
            array_pop($data);
            
            $employee->update($data);

            if (!empty($bankData)) {
                $primaryBankAccount = $employee->primaryBankAccount;
                if ($primaryBankAccount) {
                    $employee->primaryBankAccount()->update($bankData);
                } else {
                    $bankData['primary'] = true;
                    $employee->employeeBankAccounts()->create($bankData);
                }
            }

            // Update employee contract
            $contract = $employee->currentContract;
            if ($contract && isset($data['contract_end_date'])) {
                $contract->update([
                    'branch_id' => $request->branch_id,
                    'department_id' => $request->department_id,
                    'employment_type_id' => $request->employment_type_id,
                    'job_title_id' => $request->job_title_id,
                    'start_date' => $request->employment_date,
                    'end_date' => $request->contract_end_date,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'data' => $employee,
            'message' => 'Employee updated successfully'
        ]);
    }

    public function lineManagers()
    {
        return response()->json(Employee::lineManagers()->get());
    }

    public function lineManagersByBranch($branchId)
    {
        return response()->json(Employee::lineManagers()->where('branch_id', $branchId)->get());
    }
}
