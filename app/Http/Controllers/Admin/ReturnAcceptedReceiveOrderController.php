<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaReceivePurchaseOrder;
use Session;

class ReturnAcceptedReceiveOrderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    private $path = "admin.receivepurchaseorders.returns_with_credit_note.";
    private $consts = [
        'Accepted'=>'Accepted'
    ];
    public function __construct()
    {
        $this->model = 'return-accepted-receive-order';
        $this->title = 'Returned Receive Purchase Order';
        $this->pmodule = 'return-accepted-receive-order';
    }

    public function index(Request $request)
    {
        try {
            $status = $this->consts[$request->status];
        } catch (\Throwable $th) {
            $status = $this->consts['Accepted'];
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaReceivePurchaseOrder::where('return_status', $status)->whereNotNull('return_no')
            ->whereHas('parent',function($e) use ($request, $permission){
                $e->where('is_hide','No');
                if($request->supplier){
                    $e = $e->where('wa_supplier_id',$request->supplier);
                }
                if ($permission != 'superadmin') {
                    $e = $e->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                }
            })->with([
                'parent','uom','getStoreLocation','parent.getSupplier','child_items','return_initiator'
            ]);
            
            if($request->store){
                $lists = $lists->where('wa_location_and_store_id',$request->store);
            }
            $lists = $lists->orderBy('id', 'desc')->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view($this->path.'index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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

}
