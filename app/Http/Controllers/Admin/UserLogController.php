<?php

namespace App\Http\Controllers\Admin;

use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class UserLogController extends Controller
{

//    protected $model;
//    protected $title;
//    protected $pmodule;
//    protected $top_up_type;
//
//    public function __construct()
//    {
//        $this->model = 'employees';
//        $this->title = 'User logs';
//    }

//    public function index(Request $request)
//    {
//        $user = getLoggeduserProfile();
//        if ($user->role_id != 1) {
//            return redirect()->route('admin.dashboard')->withErrors(['error' => 'The page you tried to access is restricted.']);
//        }
//
//        $start = $request->from ? $request->from . ' 00:00:00' : now()->format('Y-m-d 00:00:00');
//        $end = $request->to ? $request->to . ' 00:00:00' : now()->format('Y-m-d 23:59:59');
//
//        $query = UserLog::query()
//            ->select([
//                'user_logs.*',
//                'roles.title as role',
//                'users.name as user',
//                'restaurants.name as restaurant',
//                'wa_departments.department_name'
//            ])
//            ->leftJoin('users', 'user_logs.user_id', '=', 'users.id')
//            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
//            ->leftJoin('restaurants', 'users.restaurant_id', '=', 'restaurants.id')
//            ->leftJoin('wa_departments', 'users.wa_department_id', '=', 'wa_departments.id')
//            ->whereBetween('user_logs.created_at', [$start, $end]);
//
//        if ($request->wantsJson()) {
//            return DataTables::eloquent($query)
//            ->addIndexColumn()
//                ->editColumn('created_at', function ($log) {
//                    return $log->created_at->format('d/m/Y H:i:s');
//                })
//                ->toJson();
//        }
//
//        return view('admin.user_log.index', [
//            'title' => $this->title,
//            'model' => $this->model
//        ]);
//    }


    protected $model;
    protected $title;
    protected $pmodule;
    protected $top_up_type;

    public function __construct()
    {
        $this->model = 'UserLog';
        $this->title = 'User logs';
        $this->sortable_columns = [
            'id', 'user_name', 'user_ip', 'user_agent', 'created_at'
        ];

        $this->pmodule = 'UserLog';
        $this->top_up_type = ['Mpesa Top Up' => 'Mpesa Top Up', 'Card Top Up' => 'Card Top Up', 'Loyalty Top Up' => 'Loyalty Top Up', 'Cash Top Up' => 'Cash Top Up'];
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
            return redirect()->route('admin.dashboard');
        }

        if ($request->manage == 'pdf') {

            $response = \App\Model\UserLog::getDataModel(999999999, 0, NULL, 'id', 'DESC', $request);
            $totaldepartment = $response['count'];
            $data = $response['response'];
            $pdf = PDF::loadView('admin.user_log.user_log', compact('data'));
            return $pdf->download('User-Logs.pdf');

        }
        if ($request->manage == 'excel') {

            $response = \App\Model\UserLog::getDataModel(999999999, 0, NULL, 'id', 'DESC', $request);
            $totaldepartment = $response['count'];
            $data = $response['response'];

            $headings = ['Timestamp', 'User Name', 'User IP', 'User Agent'];
            $records = $data->map(function ($record) {
                $childRow = [];
                $childRow['created_at'] = $record->created_at;
                $childRow['User Name'] = $record->user_name;
                $childRow['User Ip'] = $record->user_ip;
                $childRow['User Agent'] = $record->user_agent;

                return $childRow;
            });

            return ExcelDownloadService::download('user_logs', $records, $headings);

//            return Excel::create('logs_excel', function($excel) use($data) {
//                return  $excel->sheet('Sheet 1', function($sheet) use($data) {
//                    foreach ($data as $key => $valuePay) {
//
//                        $childRow = [];
//                        $childRow['S.No'] = $valuePay->id;
//                        $childRow['User Name'] = $valuePay->user_name;
//                        $childRow['User Ip'] = $valuePay->user_ip;
//                        $childRow['User Agent'] = $valuePay->user_agent;
//                        $childRow['created_at'] = $valuePay->created_at;
//
//                        $datasheet[] = $childRow;
//                    }
//                    $sheet->fromArray($datasheet);
//
//                });
//
//            })->download('xlsx')->view('exports.invoices');
        }

        if ($request->ajax()) {
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request['search']['value'];
            $orderby = $request['order']['0']['column'];
            $order = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw = $request['draw'];
            $response = \App\Model\UserLog::getDataModel($limit, $start, $search, $this->sortable_columns[$orderby], $order, $request);
            $totaldepartment = $response['count'];
            $data = $response['response']->toArray();

            $response['response'] = $data;
            // dd($response['response']);

            $return = [
                "draw" => intval($draw),
                "recordsFiltered" => intval($totaldepartment),
                "recordsTotal" => intval($totaldepartment),
                "data" => $response['response']
            ];
            return $return;
        }
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        return view('admin.user_log.index')->with($data);
    }
}
