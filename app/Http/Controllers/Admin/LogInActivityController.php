<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogInActivityController extends Controller
{
     protected $resource_folder;
     protected $module;
     protected $title;
     protected $pmodule;
     protected $model;
 
     public function __construct()
     {
         $this->resource_folder = "admin.log_in_activity";
         $this->module = "log-in-activity";
         $this->title = "Log In Activity";
         $this->pmodule = "log-in-activity";
         $this->model = "log-in-activity";

     }
 
     public function index(Request $request)
     {
        $start_date = $request->start_date ?? \Carbon\Carbon::now()->toDateString();
        $end_date = $request->end_date ??  \Carbon\Carbon::now()->toDateString();
         $module = $this->module;
         $resource_folder = $this->resource_folder;
         $title = $this->title;
         $model = $this->model;
         $pmodule = $this->pmodule;
         $roles = Role::all();
         $loginEvents  = DB::table('user_logs')
            ->select(
                'user_logs.created_at as log_date',
                'users.name as user_name',
                'role.title  as  user_role',
                'user_logs.user_agent',
               )
            ->leftJoin('users', 'users.id', '=', 'user_logs.user_id')
            ->leftJoin('roles as role', 'users.role_id', '=', 'role.id')
            ->where('user_logs.activity', '=', 'Logged into the system.')
            ->whereBetween('user_logs.created_at', ["$start_date 00:00:00","$end_date 23:59:59"]);
        if($request->role){
            $loginEvents = $loginEvents->where('role.id', $request->role);
        }
           $loginEvents= $loginEvents ->get();
        if($request->type && $request->type == 'Excel'){
            $user = Auth::user();
            return ExcelDownloadService::download("User-Log-In-Activity-$start_date-$end_date", $loginEvents, ['DATE','NAME', 'ROLE', 'AGENT']);

        } 
        if($request->type && $request->type == 'PDF'){
            $user = Auth::user();
            $pdf = Pdf::loadView('admin.log_in_activity.login_activity_pdf', compact('user','loginEvents'))->setPaper('a4', 'portrait');
            return $pdf->download('User-Log-In-Activity' . $start_date . '-' . $end_date . '.pdf');

        }                
         return view('admin.log_in_activity.index', compact('model', 'title', 'resource_folder', 'roles', 'loginEvents'));
     }
}
