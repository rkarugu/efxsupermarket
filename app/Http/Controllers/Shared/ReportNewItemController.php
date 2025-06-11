<?php

namespace App\Http\Controllers\Shared;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ReportedNewItem;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Model\WaLocationAndStore;
use App\SalesmanShift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReportNewItemController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'New Items';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.reported_new_items';
    }
    public function reportNewItem(Request $request)
    { 
        try {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            }

            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 402);
            }
            if($user->role_id == 4){
                $shift = SalesmanShift::where('salesman_id', $user->id)
                     ->whereDate('created_at', Carbon::now()->toDateString())
                     ->where('status', 'open')
                     ->first();
                 if(!$shift){
                     return response()->json(['status' => false, 'message' => 'You do not have an open shift'], 402);
                 }
 
             }
            $item = new ReportedNewItem();
            $item->product_name = $request->product_name;
            $item->reported_by = $user->id;
            if ($request->image) {
                $uploadPath = 'uploads/shift_issues';
                $file = $request->file('image');
                if (!file_exists($uploadPath)) {
                    File::makeDirectory($uploadPath, $mode = 0777, true, true);
                }
                $fileName = $file->hashName() . "." . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $fileName);
                $item->image = $fileName;
            }
            if($request->comment){
                $item->comment = $request->comment;
            }
            $item->save();

            return response()->json(['status' => true, 'message' => 'New Item reported successfully'], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }

    }
    public  function getReportedNewItems(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 402);
            }
            $appUrl = env('APP_URL');
            $reportedItems = ReportedNewItem::whereDate('created_at', Carbon::now()->toDateString())
                ->where('reported_by', $user->id)
                ->get()->map(function ($item) use ($appUrl){
                    $item->image = "$appUrl/uploads/shift_issues/" . $item->image;
                    return $item;
                });
            return response()->json(['status' => true, 'data' => $reportedItems], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getMessage()], 500);
        }
       
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $from  = $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $to = $request->todate ? Carbon::parse($request->todate)->endOfDay() : Carbon::now()->endOfDay();
        $branches = WaLocationAndStore::all();


        $reportedItems = ReportedNewItem::select('reported_new_items.*', 'users.name')
            ->leftJoin('users', 'users.id', 'reported_new_items.reported_by')
            ->whereBetween('reported_new_items.created_at', [$from, $to]);
        if($request->branch){
            $reportedItems = $reportedItems->where('users.wa_location_and_store_id', $request->branch);
        }
        $reportedItems = $reportedItems->get();
        if (isset($permission[$pmodule . '___reported-new-items']) || $permission == 'superadmin') {
            $breadcum = [$title => route('reported-new-items.index'), 'Listing' => ''];
            return view('admin.reported_new_items.index', compact('permission', 'pmodule', 'title','model', 'basePath', 'branches', 'reportedItems'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }
    public function reportNewItemsWeb(Request $request) 
    {
        try {
            $user = Auth::user();
            $item = new ReportedNewItem();
            $item->product_name = $request->product_name;
            $item->reported_by = $user->id;
            if($request->comment){
                $item->comment = $request->comment;
            }
            $item->save();
            return response()->json(['success' => true, 'message' => 'New item reported successfully.']);

        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getMessage()]);
        }
    }
}
