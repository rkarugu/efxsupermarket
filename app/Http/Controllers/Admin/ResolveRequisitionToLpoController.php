<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\User;
use App\Model\WaExternalRequisition;
use App\Model\WaExternalRequisitionItem;
use App\Model\WaUserSupplier;
use App\Model\WaSupplier;
use App\Model\WaStockMove;
use App\Model\WaInventoryLocationStockStatus;
use App\Models\TradeAgreement;
use App\Services\LpoService;
use PDF;
use DB;
use Exception;
use Session;
use Illuminate\Support\Facades\Validator;

class ResolveRequisitionToLpoController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'resolve-requisition-to-lpo';
        $this->title = 'Resolve Branch Requisition To LPO';
        $this->pmodule = 'resolve-requisition-to-lpo';
    }

    public function index(Request $request)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaExternalRequisition::query()
                ->with([
                    'supplier',
                    'getrelatedEmployee',
                    'getBranch',
                    'getDepartment',
                    'getRelatedItem',
                    'unit_of_measure',
                    'store_location'
                ])
                ->where('status', '!=', 'RESOLVED')
                ->where('is_hide', 'No')
                ->where('status', 'APPROVED')
                ->when(request()->filled('store'), function ($query) {
                    $query->where('wa_store_location_id', request()->store);
                })
                ->when(request()->filled('supplier'), function ($query) {
                    $query->where('orders.wa_supplier_id', request()->supplier);
                })
                ->when(!request()->filled('supplier') && !auth()->user()->isAdministrator(), function ($query) {
                    $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                        ->pluck('wa_supplier_id')->toArray();
                    $query->whereIn('wa_supplier_id', $supplierIds);
                })
                ->orderBy('id', 'desc')
                ->get();

            $user = getLoggeduserProfile()->id;
            $userrole = getLoggeduserProfile()->role_id;

            if ($userrole == 1  || isset($permission[$pmodule . '___view-all'])) {
                $suppliers = WaSupplier::get();
            } else {
                $suppliers = WaUserSupplier::where('user_id', $user)
                    ->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                    ->get();
            }

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.resolverequisitiontolpo.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create()
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        if ($getLoggeduserProfile->wa_department_id && $getLoggeduserProfile->restaurant_id && $getLoggeduserProfile->wa_unit_of_measures_id && $getLoggeduserProfile->wa_location_and_store_id) {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
                $title = $this->title;
                $model = $this->model;
                $breadcum = [$this->title => route($model . '.create'), 'Add' => ''];
                return view('admin.resolverequisitiontolpo.create', compact('title', 'model', 'breadcum'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } else {
            Session::flash('warning', 'Please update your branch, Bin Location, Store Location and department');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'purchase_no' => 'required|unique:wa_purchase_orders',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row = new WaPurchaseOrder();
                $row->purchase_no = $request->purchase_no;
                $row->restaurant_id = $request->restaurant_id;
                $row->wa_department_id = $request->wa_department_id;
                $row->user_id = $request->user_id;
                $row->purchase_date = $request->purchase_date;
                $row->wa_supplier_id = $request->wa_supplier_id;
                $row->wa_location_and_store_id = $request->wa_location_and_store_id;
                $row->status = 'PRELPO';
                $row->save();
                updateUniqueNumberSeries('PURCHASE ORDERS', $request->purchase_no);

                return redirect()->route($this->model . '.edit', $row->slug);
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }




    public function show($slug) {}









    public function edit($id)
    {
        try {
            $sevenDayLessDate = Date('Y-m-d', strtotime('-7 days'));
            $row = WaExternalRequisition::with(['supplier', 'getrelatedEmployee', 'getBranch', 'getDepartment', 'unit_of_measure', 'store_location'])->where('status', 'APPROVED')->where('id', $id)->first();
            $requisition_items = WaExternalRequisitionItem::with(['getInventoryItemDetail.getInventoryCategoryDetail'])->select([
                'wa_external_requisition_items.*',
                'wa_inventory_location_stock_status.max_stock as max_stock_f',
                'wa_inventory_location_stock_status.re_order_level',
                DB::RAW('(select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = wa_external_requisitions.wa_store_location_id) as current_qty'),
                DB::RAW('(select sum(qauntity) from wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id != wa_external_requisitions.wa_store_location_id) as other_branches_qty'),
                DB::RAW('(select sum(qauntity) from wa_stock_moves where DATE(wa_stock_moves.created_at) >= "' . $sevenDayLessDate . '" AND wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id and wa_stock_moves.wa_location_and_store_id = wa_external_requisitions.wa_store_location_id) as movements'),

            ])->leftJoin('wa_inventory_items', function ($e) {
                $e->on('wa_inventory_items.id', 'wa_external_requisition_items.wa_inventory_item_id');
            })->leftJoin('wa_external_requisitions', function ($e) {
                $e->on('wa_external_requisitions.id', 'wa_external_requisition_items.wa_external_requisition_id');
            })->leftJoin('wa_inventory_location_stock_status', function ($e) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', DB::RAW('wa_external_requisitions.wa_store_location_id'));
            })->groupBy('wa_external_requisition_items.id')->where('wa_external_requisition_id', $row->id)->get();
            $title = $this->title;
            $breadcum = [$this->title => route($this->model . '.edit', $row->id), 'Add' => ''];
            $model = $this->model;
            return view('admin.resolverequisitiontolpo.edit', compact('title', 'model', 'breadcum', 'row', 'requisition_items'));
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $requisition = WaExternalRequisition::with(['supplier', 'getrelatedEmployee', 'getBranch', 'getDepartment', 'getRelatedItem', 'unit_of_measure', 'store_location'])
                ->where('status', 'APPROVED')
                ->where('id', $id)
                ->first();

            $tradeAgreement = TradeAgreement::where([
                'wa_supplier_id' => $requisition->wa_supplier_id
            ])->first();

            if (is_null($tradeAgreement)) {
                throw new Exception("The supplier does not have a trade agreement", 422);
            }

            if (!$tradeAgreement->is_locked) {
                throw new Exception("The supplier trade agreement is not locked", 422);
            }

            $check = DB::transaction(function () use ($request, $requisition) {
                $row = new WaPurchaseOrder();
                $row->purchase_no = getCodeWithNumberSeries('PURCHASE ORDERS');
                $row->restaurant_id = $requisition->restaurant_id;
                $row->wa_department_id = $requisition->wa_department_id;
                $row->user_id = $requisition->user_id;
                $row->purchase_date = $requisition->requisition_date;
                $row->wa_supplier_id = $requisition->wa_supplier_id;
                $row->wa_unit_of_measures_id = $requisition->wa_unit_of_measures_id;
                $row->wa_location_and_store_id = $requisition->wa_store_location_id;
                $row->status = 'PENDING';
                $row->save();
                updateUniqueNumberSeries('PURCHASE ORDERS', $row->purchase_no);
                $selectedExtReqIds = [];
                foreach ($requisition->getRelatedItem as $key => $externalItemRow) {
                    $item = new WaPurchaseOrderItem();
                    $item->wa_purchase_order_id = $row->id;
                    $item->wa_inventory_item_id = $externalItemRow->wa_inventory_item_id;
                    $item->quantity = $externalItemRow->quantity;
                    $item->note = $externalItemRow->note;
                    $item->prev_standard_cost = $externalItemRow->getInventoryItemDetail->prev_standard_cost;
                    $item->order_price = $externalItemRow->standard_cost;
                    $item->supplier_quantity = $item->quantity;
                    $item->item_no = $externalItemRow->getInventoryItemDetail->stock_id_code;
                    $item->unit_of_measure = $requisition->wa_unit_of_measures_id;
                    $item->unit_conversion = 1;
                    $item->standard_cost = $externalItemRow->standard_cost;
                    $item->total_cost = $item->order_price * $item->quantity;
                    $vat_rate = 0;
                    $vat_amount = 0;
                    if ($externalItemRow->vat_rate && $externalItemRow->vat_rate > 0) {
                        $vat_rate = $externalItemRow->vat_rate;
                        if ($item->total_cost > 0) {
                            //    $vat_amount = ($externalItemRow->vat_rate*$item->total_cost)/100;
                            $vat_amount = $item->total_cost - (($item->total_cost * 100) / ($vat_rate + 100));
                        }
                    }
                    $item->vat_rate = $vat_rate;
                    $item->vat_amount = $vat_amount;
                    $item->total_cost = $item->total_cost - $vat_amount;
                    $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
                    $item->save();
                    $selectedExtReqIds[] = $externalItemRow->wa_external_requisition_id;
                    WaExternalRequisitionItem::where('id', $externalItemRow->id)->update(['is_resolved' => '1']);
                }
                $alldone = WaExternalRequisitionItem::whereIn('wa_external_requisition_id', $selectedExtReqIds)
                    ->where('is_resolved', '0')->count();
                if ($alldone == 0) {
                    WaExternalRequisition::whereIn('id', $selectedExtReqIds)->update(['status' => 'RESOLVED']);
                }
                addPurchaseOrderPermissions($row->id, $row->wa_department_id);
                return true;
            });
            if ($check) {
                return response()->json(['result' => 1, 'message' => 'Requisition Resolved Successfully.', 'redirect_url' => route($this->model . '.index')], 200);
            }
            return response()->json(['result' => -1, 'message' => 'Something went wrong'], 500);
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
            return response()->json(['result' => false, 'message' => $msg], $th->getCode());
        }
    }

    public function removeItem(Request $request)
    {
        try {
            $item = WaExternalRequisitionItem::with('getExternalPurchaseId')->find($request->id);
            $externalRequisition = $item->getExternalPurchaseId;


            if ($externalRequisition->getRelatedItem()->count() == 1) {
                $externalRequisition->status = 'RESOLVED';
                $externalRequisition->save();
            }

            $item->delete();

            return response()->json(['result' => 1, 'message' => 'Requisition Resolved Successfully.', 'location' => route($this->model . '.index')]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'result' => -1]);
        }
    }

    public function __update(Request $request, $slug)
    {
        try {
            if ($request->requisition_item_row_id && count($request->requisition_item_row_id) > 0) {
                $purchaseOrder =  WaPurchaseOrder::whereSlug($slug)->first();
                $purchaseOrder->wa_supplier_id = $request->wa_supplier_id;
                $purchaseOrder->restaurant_id = $request->restaurant_id;
                $purchaseOrder->wa_department_id = $request->wa_department_id;
                $purchaseOrder->wa_location_and_store_id = $request->wa_location_and_store_id;
                $purchaseOrder->save();
                $selectedExtReqIds = [];
                foreach ($request->requisition_item_row_id as $requisition_item_row_id) {

                    $externalItemRow = WaExternalRequisitionItem::where('id', $requisition_item_row_id)->first();
                    $item = new WaPurchaseOrderItem();
                    $item->wa_purchase_order_id = $purchaseOrder->id;
                    $item->wa_inventory_item_id = $externalItemRow->wa_inventory_item_id;
                    $qty_param = 'quantity_' . $requisition_item_row_id;
                    $item->quantity = $request->$qty_param;
                    $note_param = 'note_' . $requisition_item_row_id;
                    $item->note = $request->$note_param;
                    $item->prev_standard_cost = $externalItemRow->getInventoryItemDetail->prev_standard_cost;
                    $order_price_param = 'order_price_' . $requisition_item_row_id;
                    $item->order_price = $request->$order_price_param;
                    // $supplier_uom_id_param = 'supplier_uom_id_'.$requisition_item_row_id;
                    // $item->supplier_uom_id = $request->$supplier_uom_id_param;
                    // $supplier_quantity_param = 'supplier_quantity_'.$requisition_item_row_id;
                    // $item->supplier_quantity = $request->$supplier_quantity_param;
                    $item->supplier_quantity = $item->quantity;

                    // $unit_conversion_param = 'unit_conversion_'.$requisition_item_row_id;
                    // $item->unit_conversion = $request->$unit_conversion_param;
                    $item->item_no = $externalItemRow->getInventoryItemDetail->stock_id_code;
                    $item->unit_of_measure = $externalItemRow->getInventoryItemDetail->wa_unit_of_measure_id;
                    $item->standard_cost = $externalItemRow->standard_cost;

                    $item->total_cost = $item->order_price * $item->quantity;
                    $vat_rate = 0;
                    $vat_amount = 0;
                    if ($externalItemRow->vat_rate && $externalItemRow->vat_rate > 0) {
                        $vat_rate = $externalItemRow->vat_rate;
                        if ($item->total_cost > 0) {
                            $vat_amount = ($externalItemRow->vat_rate * $item->total_cost) / 100;
                        }
                    }
                    $item->vat_rate = $vat_rate;
                    $item->vat_amount = $vat_amount;
                    $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
                    $item->save();
                    $selectedExtReqIds[] = $externalItemRow->wa_external_requisition_id;
                    WaExternalRequisitionItem::where('id', $requisition_item_row_id)->update(['is_resolved' => '1']);
                }
                $alldone = WaExternalRequisitionItem::whereIn('wa_external_requisition_id', $selectedExtReqIds)
                    ->where('is_resolved', '0')->count();
                if ($alldone == 0) {
                    WaExternalRequisition::whereIn('id', $selectedExtReqIds)->update(['status' => 'RESOLVED']);
                }
                $this->sendRequisitionRequest($purchaseOrder->purchase_no);
                Session::flash('success', 'Item moved successfully to lop.');
                return redirect()->route($this->model . '.create');
            } else {
                Session::flash('warning', 'Please select at least one item');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug) {}

    public function userDetail(Request $request)
    {
        $user_detail = User::where('id', $request->user_id)->first();
        $arr =  ['restaurant_id' => $user_detail->restaurant_id, 'wa_department_id' => $user_detail->wa_department_id];
        if ($user_detail->restaurant_id) {
            $arr['restaurant_name'] = @$user_detail->userRestaurent->name;
        }

        if ($user_detail->wa_department_id) {
            $arr['department_name'] = @$user_detail->userDepartment->department_name;
        }
        return json_encode($arr);
    }


    public function sendRequisitionRequest($purchase_no)
    {
        $row =  WaPurchaseOrder::where('purchase_no', $purchase_no)->first();
        $row->status = 'PENDING';
        $row->save();
        addPurchaseOrderPermissions($row->id, $row->wa_department_id);
    }

    public function showAvailableLposMerge(Request $request, $lpoId)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaExternalRequisition::where('status', '!=', 'RESOLVED')->where('is_hide', 'No');
            $lists = $lists->where(function ($e) use ($request) {
                if ($request->store) {
                    $e->where('wa_store_location_id', $request->store);
                }
                if ($request->supplier) {
                    $e->where('wa_supplier_id', $request->supplier);
                }
            })->with(['supplier', 'getrelatedEmployee', 'getBranch', 'getDepartment', 'getRelatedItem', 'unit_of_measure', 'store_location'])->where('status', 'APPROVED')->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];

            $mergeId = WaExternalRequisition::where('id', $lpoId)->first();
            return view('admin.resolverequisitiontolpo.merge', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'mergeId'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function mergeLpos(Request $request)

    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];

        $selectedIds = $request->input('selectedIds');
        $mergeId = $request->input('mergeId');


        try {
            LpoService::mergeLpos($selectedIds,  $mergeId);

            Session::flash('success', 'LPO  Merged Successfully ');
            return redirect()->route($this->model . '.index');
        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edititem($slug)
    {
        $row = WaExternalRequisition::whereSlug($slug)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;

            $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
            $end_30 = now()->format('Y-m-d 23:59:59');

            foreach ($row->getRelatedItem as $getRelatedItem) {
                $sales = WaStockMove::query()
                    ->where('wa_location_and_store_id', 46)
                    ->where('wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->where(function ($query) {
                        $query->where('document_no', 'like', 'INV-%')
                            ->orWhere('document_no', 'like', 'RTN-%');
                    })
                    ->whereBetween('created_at', [$start_30, $end_30])
                    ->sum('qauntity');

                $smallPackSale = WaStockMove::query()
                    ->select([
                        'items.wa_inventory_item_id',
                        DB::raw('ABS(SUM(qauntity) / conversion_factor) as amount')
                    ])
                    ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wa_stock_moves.wa_inventory_item_id')
                    ->where('wa_location_and_store_id', 46)
                    ->where('items.wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->where(function ($query) {
                        $query->where('document_no', 'like', 'INV-%')
                            ->orWhere('document_no', 'like', 'RTN-%');
                    })
                    ->whereBetween('wa_stock_moves.created_at', [$start_30, $end_30])
                    ->first();

                $getRelatedItem->totalSales = $sales + ($smallPackSale->amount ?? 0);

                $qoh = WaStockMove::where('wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->sum('qauntity');
                $getRelatedItem->qoh = $qoh ?? 0;

                $qoo = WaPurchaseOrderItem::query()
                    ->whereHas('getPurchaseOrder', function ($query) {
                        $query->where('status', 'APPROVED')
                            ->where('is_hide', '<>', 'Yes')
                            ->doesntHave('grns');
                    })->where('wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->sum('quantity');
                $getRelatedItem->qoo = $qoo ?? 0;

                $max = WaInventoryLocationStockStatus::where('wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->pluck('max_stock')->first();

                $getRelatedItem->max = $max ?? 0;

                $reorder = WaInventoryLocationStockStatus::where('wa_inventory_item_id', $getRelatedItem->wa_inventory_item_id)
                    ->pluck('re_order_level')->first();

                $getRelatedItem->reorder = $reorder ?? 0;
            }

            return view('admin.resolverequisitiontolpo.edititem', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function updateItem(Request $request)
    {
        try {
            $itemsData = $request->input('items');

            foreach ($itemsData as $id => $data) {
                $item = WaExternalRequisitionItem::findOrFail($id);
                $item->quantity = $data['quantity'];
                $item->total_cost = $data['total_cost'];
                $item->vat_amount = $data['vat_amount'];
                $item->note = $data['note'];
                $item->total_cost_with_vat = $data['total_cost_with_vat'];
                $item->save();
            }
            return response()->json([
                'message' => 'LPO Items updated Successfully',
                'redirect_url' => route($this->model . '.index')
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
