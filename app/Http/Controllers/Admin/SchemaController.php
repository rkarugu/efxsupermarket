<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogSchemaFetch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Auth;

class SchemaController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'get-my-schema';
        $this->title = 'Get My Schema';
    }

    public function index(Request $request) 
    {
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 

        $title = $this->title;
        $model = $this->model;
        $permission = $this->mypermissionsforAModule();

        $breadcum = ['Get My Schema'=>''];
        $branches = DB::table('restaurants')->select('id','name')->get();
        return view('admin.get_my_schema.index',compact('title','model','breadcum','permission','branches'));
    }

    public function fetch_schema(Request $request)
    {   
        if (Auth::user()->role_id != 1) {
            return returnAccessDeniedPage();
        } 
        
        if (!Schema::hasTable(strtolower($request->table_name))) {
            return response()->json([
                'result'=>0,
                'error'=>'Table does not exist.'
            ], 422);
        }
        
        try {
            if (request()->filled('excel') && request()->excel ==1) {
                $columns = Schema::getColumnListing($request->table_name);
    
                // Fetch all data from the table
                $query = DB::table($request->table_name);
                
                if (request()->filled('branch_filter') && request()->branch != 'all') {
                    if (request()->filled('branch_column')) {
                        $branch_column = 'restaurant_id';
                    } else {
                        $branch_column = request()->branch_column;
                    }
                    $query->where($branch_column, request()->branch);
                }
    
                if(request()->filled('date_filter') && request()->filled('from') && request()->filled('to')){
                    if (request()->filled('branch_column')) {
                        $branch_column = 'created_at';
                    } else {
                        $branch_column = request()->branch_column;
                    }
                    $query->whereBetween($branch_column,[request()->from.' 00:00:00',request()->to.' 23:59:59']);
                }

                LogSchemaFetch::create([
                    'fetch_by'=>Auth::user()->id,
                    'fetch_data'=>json_encode([
                        "table_name"=> request()->table_name,
                        "date_filter" => request()->filled('date_filter'),
                        "date_from"=> request()->from,
                        "date_to" => request()->to,
                        "date_column"=> request()->date_column,
                        "branch_filter" => request()->filled('branch_filter'),
                        "branch"=> request()->branch,
                        "branch_column"=> request()->branch_column
                    ]),
                ]);
    
                $data= $query->get();
                $data= collect($data);
                return ExcelDownloadService::download($request->table_name.'_data', $data, $columns);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500); 
        }
        
    }


}
