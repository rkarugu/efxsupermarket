<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiCallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class ApiCallLogController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'api-call-logs';
        $this->title = 'Api Call Logs';
        $this->pmodule = 'api-call-logs';
    }

    public function index(Request $request)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            throw_if((!isset($permission[$pmodule . '___view']) && $permission != 'superadmin'), "Not Enough Permissions");
            $title = $this->title;
            $model = $this->model;
            $logs = ApiCallLog::whereBetween('created_at',[
                ($request->from_date ?? date('Y-m-d')). ' 00:00:00', ($request->to_date ?? date('Y-m-d')). ' 23:59:59'
            ])->orderBy('created_at', 'desc')->simplePaginate(100);
            return view('admin.api_call_logs.index', compact('logs', 'title', 'model', 'pmodule', 'permission'));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }
}
