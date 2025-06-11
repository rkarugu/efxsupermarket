<?php

namespace App\Http\Controllers\Shared;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Model\WaLocationAndStore;
use Illuminate\Support\Facades\File;
use App\Models\ReportedPriceConflict;
use App\SalesmanShift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportPriceConflict extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Price Conflicts';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.reported_price_conflicts';
    }
    public function reportPriceConflict(Request $request)
    { 
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required',
                'price' => 'required',
                'image'  => 'required',
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
            $item = WaInventoryItem::find($request->item_id);
            if(!$item){
                return response()->json(['status' => false,'message' => 'Item not found'], 402);
            }
            $conflict = new ReportedPriceConflict();
            $conflict->wa_inventory_item_id = $request->item_id;
            $conflict->reported_by = $user->id;
            $conflict->current_selling_price = $item->selling_price;
            $conflict->current_standard_cost = $item->standard_cost;
            $conflict->reported_price = $request->price;

            $uploadPath = 'uploads/shift_issues';
            $file = $request->file('image');
            if (!file_exists($uploadPath)) {
                File::makeDirectory($uploadPath, $mode = 0777, true, true);
            }
            $fileName = $file->hashName() . "." . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $fileName);
            $conflict->image = $fileName;
            
            if($request->comment){
                $conflict->comment = $request->comment;
            }
            $conflict->save();

            return response()->json(['status' => true, 'message' => 'Price Conflict Reported Successfully'], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => false,'message' => $th->getTrace()], 500);
        }

    }
    public  function getReportedPriceConflicts(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Token mismatch'], 402);
            }
            $appUrl = env('APP_URL');
            $reportedItems = ReportedPriceConflict::with('getRelatedItem')->whereDate('created_at', Carbon::now()->toDateString())
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


        $reportedItems = ReportedPriceConflict::select('reported_price_conflicts.*', 'users.name', 'wa_inventory_items.stock_id_code', 'wa_inventory_items.title')
            ->leftJoin('users', 'users.id', 'reported_price_conflicts.reported_by')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'reported_price_conflicts.wa_inventory_item_id')
            ->whereBetween('reported_price_conflicts.created_at', [$from, $to]);    
        if($request->branch){
            $reportedItems = $reportedItems->where('users.wa_location_and_store_id', $request->branch);
        }
        $reportedItems = $reportedItems->get();
        if (isset($permission[$pmodule . '___reported-price-conflicts']) || $permission == 'superadmin') {
            $breadcum = [$title => route('reported-price-conflicts.index'), 'Listing' => ''];
            return view('admin.reported_price_conflicts.index', compact('permission', 'pmodule', 'title','model', 'basePath', 'branches', 'reportedItems'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }
    public function reportPriceConflictsWeb(Request $request)
    {
        try {
            $user = Auth::user();
            $item = WaInventoryItem::find($request->item_id);
            if(!$item){
                return response()->json(['status' => false,'message' => 'Item not found'], 402);
            }
            $conflict = new ReportedPriceConflict();
            $conflict->wa_inventory_item_id = $request->item_id;
            $conflict->reported_by = $user->id;
            $conflict->current_selling_price = $item->selling_price;
            $conflict->current_standard_cost = $item->standard_cost;
            $conflict->reported_price = $request->price;
            
            if($request->comment){
                $conflict->comment = $request->comment;
            }
            $conflict->save();
            return response()->json(['success' => true, 'message' => 'Price Conflict reported successfully.']);

        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => $th->getMessage()]);
        }

    }
}
