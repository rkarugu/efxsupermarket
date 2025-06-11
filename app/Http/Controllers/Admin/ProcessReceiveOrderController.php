<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaReceivePurchaseOrder;
use App\Model\WaReceivePurchaseOrderItem;
use App\Model\WaGrn;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WeightedAverageHistory;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\TaxManager;
use App\Model\WaLocationAndStore;
use App\Model\WaSuppTran;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ProcessReceiveOrderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    private $path = "admin.receivepurchaseorders.processing_orders.";
    private $consts = [
        'Confirm'=>'Confirmed',
        'Reject'=>'Rejected',
        'Pending'=>'Pending'
    ];
    public function __construct()
    {
        $this->model = 'process-receive-purchase-order';
        $this->title = 'Process Receive Purchase Order';
        $this->pmodule = 'process-receive-purchase-order';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $user_location = $user->wa_location_and_store_id;
        $user_restaurant_id = WaLocationAndStore::where('id', $user_location)->pluck('wa_branch_id')->first();
        $preselect_location = null;
        $disable_select = false;
        if ($user->role_id != 1 && !isset($permission['maintain-items___view-per-branch'])) {
            $preselect_location = $user_location;
            $disable_select = true;
        }

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaReceivePurchaseOrder::where('status', 'Pending')
            ->whereHas('parent',function($e) use ($request, $permission, $user, $user_restaurant_id){
                $e->where('is_hide','No');
                if($request->supplier){
                    $e = $e->where('wa_supplier_id',$request->supplier);
                }
                // if ($permission != 'superadmin') {
                //     $e = $e->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                // }
                if ($user->role_id != 1 && !isset($permission['maintain-items___view-per-branch'])) {
                    $e = $e->where('restaurant_id', $user_restaurant_id);
                }
            })->with([
                'parent.getrelatedEmployee','uom','getStoreLocation','parent.getSupplier','child_items'
            ]);
            
            if($request->store){
                $lists = $lists->where('wa_location_and_store_id',$request->store);
            }
            $lists = $lists->orderBy('id', 'desc')->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view($this->path.'index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'preselect_location', 'disable_select'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        $row =  WaReceivePurchaseOrder::with([
            'child_items.parent.getInventoryItemDetail.getInventoryCategoryDetail',
            'child_items.parent.getInventoryItemDetail.getUnitOfMeausureDetail',
            'child_items.parent.getInventoryItemDetail.location',
            'parent.getRelatedAuthorizationPermissions.getExternalAuthorizerProfile'])->whereId($id)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            $pmodule = $this->pmodule;
            $permission =  $this->mypermissionsforAModule();
            return view($this->path.'show', compact('title', 'model', 'breadcum', 'row', 'permission', 'pmodule'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function edit($slug)
    {
    }

    public function update(Request $request, $id){
        try {
            $validator = Validator::make($request->all(),[
                'approval_status'=>'required|in:Confirm,Reject',
            ]);
            if ($validator->fails()) 
            {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $row =  WaReceivePurchaseOrder::whereId($id)->firstOrFail();
            if ($row) {
                $getLoggeduserProfile = getLoggeduserProfile();
                $row->status = $this->consts[$request->approval_status];
                $row->confirmed_by = $getLoggeduserProfile->id;
                $row->confirmed_at = date('Y-m-d H:i:s');
                $row->save();
                return response()->json(['message' => 'Request '.$row->status.' Successfully', 'redirect_url' => route($this->model . '.index')], 200);
            } else {
                throw new Exception("Error Processing Request");
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
        
    }
}
