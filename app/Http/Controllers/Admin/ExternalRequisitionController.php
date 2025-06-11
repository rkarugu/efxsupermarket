<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaExternalRequisition;
use App\Model\WaExternalRequisitionItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use App\Model\WaSupplier;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaUserSupplier;
use App\Model\WaStockMove;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryItemSupplierData;
use App\Model\WaInventoryItemSupplier;
use Carbon\Carbon;
use PDF;
use DB;
use Session;
use Auth;
use App\Interfaces\SmsService;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaPurchaseOrderItem;
use App\Rules\PurchaseOrder\MaxStockValidator;
use Illuminate\Support\Facades\Validator;

class ExternalRequisitionController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    protected SmsService $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->model = 'external-requisitions';
        $this->title = 'Branch External Requisitions';
        $this->pmodule = 'external-requisitions';
        $this->smsService = $smsService;
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $users = Auth::user();
        if($users->role_id == 152){
            $branches = WaLocationAndStore::where('id', $users->wa_location_and_store_id)->get();
        }else{
            $branches = WaLocationAndStore::all();
        }

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin' || isset($permission[$pmodule . '___view-all'])) {
            $lists = WaExternalRequisition::where('status', '!=', 'RESOLVED')->where('is_hide', 'No');
            $user = getLoggeduserProfile()->id;
            $userrole = getLoggeduserProfile()->role_id;
            $uomId = getLoggeduserProfile()->wa_unit_of_measures_id;

            if ($userrole == 154) {

                $userSuppliers = WaUserSupplier::where('user_id', $user)->pluck('wa_supplier_id')->toArray();
                $lists = $lists->whereIn('wa_supplier_id', $userSuppliers);
            } else if ($permission != 'superadmin' && !isset($permission[$pmodule . '___view-all'])) {

                // $lists = $lists->where('user_id', $user);
                $lists = $lists->where('wa_unit_of_measures_id', $uomId);
             }


            $lists = $lists->where(function ($e) use ($request) {
                if ($request->store) {
                    $e->where('wa_store_location_id', $request->store);
                }
                if ($request->supplier) {
                    $e->where('wa_supplier_id', $request->supplier);
                }
            })->with(['supplier', 'getrelatedEmployee', 'getBranch', 'getDepartment', 'getRelatedItem', 'unit_of_measure', 'store_location'])->orderBy('wa_external_requisitions.id', 'desc')->get();

            if ($userrole == 1 || isset($permission[$pmodule . '___view-all'])) {
                $suppliers = WaSupplier::get();
            } else if ($userrole == 152) {
                $suppliers = WaInventoryItemSupplier::join('wa_inventory_location_uom', 'wa_inventory_item_suppliers.wa_inventory_item_id', '=', 'wa_inventory_location_uom.inventory_id')
                    ->join('wa_suppliers', 'wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                    ->where('wa_inventory_location_uom.uom_id', $uomId)
                    ->distinct('wa_inventory_item_suppliers.wa_supplier_id')
                    ->get(['wa_suppliers.*']);
            } else {
                $suppliers = WaUserSupplier::where('user_id', $user)
                    ->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                    ->get();
            }

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.externalrequisition.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'suppliers', 'branches', 'users'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function hideexternalquisition($slug)
    {
        $row =  WaExternalRequisition::whereSlug($slug)->update(['is_hide' => 'Yes']);
        if ($row) {
            Session::flash('success', 'Unwanted External Requisition hide successfully.');
            return redirect()->back();
        }
    }
    public function create(Request $request)
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        if ($getLoggeduserProfile->wa_department_id && $getLoggeduserProfile->restaurant_id && $getLoggeduserProfile->wa_unit_of_measures_id && $getLoggeduserProfile->wa_location_and_store_id) {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
                $title = 'Add ' . $this->title;
                $model = $this->model;

                $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
                $user = getLoggeduserProfile();

                $userrole = $user->role_id;


                if ($userrole == 1) {

                    $suppliers = WaSupplier::get();
                } else if ($userrole == 152) {

                    $suppliers = WaInventoryItemSupplier::join('wa_inventory_location_uom', 'wa_inventory_item_suppliers.wa_inventory_item_id', '=', 'wa_inventory_location_uom.inventory_id')
                        ->join('wa_suppliers', 'wa_inventory_item_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')
                        ->where('wa_inventory_location_uom.uom_id', $user->wa_unit_of_measures_id)
                        ->distinct('wa_inventory_item_suppliers.wa_supplier_id')
                        ->get(['wa_suppliers.*']);
                } else {
                    $suppliers = WaUserSupplier::where('user_id', $user->id)->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')->get();
                }


                return view('admin.externalrequisition.create', compact('title', 'model', 'breadcum', 'suppliers'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } else {
            Session::flash('warning', 'Please update your branch, Bin Location, Store Location and department');
            return redirect()->back();
        }
    }

    public function print(Request $request)
    {

        $slug = $request->slug;
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row =  WaExternalRequisition::whereSlug($slug)->first();
        return view('admin.externalrequisition.print', compact('title', 'model', 'breadcum', 'row'));
    }

    public function exportToPdf($slug)
    {


        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row =  WaExternalRequisition::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.externalrequisition.print', compact('title', 'model', 'breadcum', 'row'));
        $report_name = 'external_requisition_' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }



    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'purchase_no' => 'required|unique:wa_external_requisitions',
                'item_id' => 'required|array',
                'item_quantity.*' => ['required', 'numeric', 'min:1', new MaxStockValidator],
                'item_discount_per.*' => 'nullable|numeric|min:0',
                'wa_supplier_id' => 'required|exists:wa_suppliers,id'
            ], [], [
                'item_quantity.*' => 'Item Quantity',
                'item_discount_per.*' => 'Discount',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'message' => $validator->errors()
                ]);
            } else {
                $inventory = WaInventoryItem::select([
                    'wa_inventory_items.*',
                    'wa_inventory_location_stock_status.max_stock as max_stock_f',
                    'wa_inventory_location_stock_status.re_order_level',
                    'wa_inventory_item_supplier_data.price as new_standard_cost'
                ])->with(['getTaxesOfItem'])
                    ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($request) {
                        $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                            ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $request->store_location_id);
                    })
                    ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($request) {
                        $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                            ->where('wa_inventory_item_supplier_data.wa_supplier_id', $request->wa_supplier_id);
                    })
                    ->whereIn('wa_inventory_items.id', $request->item_id)->groupBy('wa_inventory_items.id')->get();
                $errors = [];

                if (count($inventory) == 0) {
                    $errors['testIn'] = ['Add items to proceed'];
                } else {
                    foreach ($inventory as $key => $val) {
                        if ($val->max_stock_f < $request->item_quantity[$val->id]) {
                            $errors['item_id.' . $val->id] = ['Qty cannot be greater than the Max Stock'];
                        }
                        if (empty($val->re_order_level)) {
                            $errors['item_id.' . $val->id] = ['Re-Order Level is mandatory'];
                        }
                        // if(empty($val->new_standard_cost)){
                        //     $errors['item_id.'.$val->id] = ['Supplier price is not available!'];
                        // }
                    }
                }
                if (count($errors) > 0) {
                    return response()->json(['result' => 0, 'message' => $errors]);
                }
                $check = DB::transaction(function () use ($inventory, $request) {
                    $getLoggeduserProfile = getLoggeduserProfile();
                    $row = new WaExternalRequisition();
                    $row->purchase_no = $request->purchase_no;
                    $row->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $row->wa_department_id = $getLoggeduserProfile->wa_department_id;
                    $row->user_id = $getLoggeduserProfile->id;
                    $row->wa_priority_level_id = $request->wa_priority_level_id;
                    $row->wa_store_location_id = $getLoggeduserProfile->wa_location_and_store_id;
                    $row->wa_supplier_id = $request->wa_supplier_id;
                    $row->wa_unit_of_measures_id = $getLoggeduserProfile->wa_unit_of_measures_id;
                    $row->note = $request->note ?? "";
                    $row->requisition_date = $request->requisition_date;
                    $row->status = 'PENDING';
                    $row->save();
                    foreach ($inventory as $key => $val) {
                        $item = new WaExternalRequisitionItem();
                        $item->wa_external_requisition_id = $row->id;
                        $item->item_no = $val->stock_id_code;
                        $item->wa_inventory_item_id = $val->id;
                        $item->quantity = $request->item_quantity[$val->id];
                        $item->note = "";
                        $item->standard_cost = $val->new_standard_cost ? $val->new_standard_cost : $val->standard_cost;
                        $item->total_cost = ($val->new_standard_cost ? $val->new_standard_cost : $val->standard_cost) * $item->quantity;
                        $vat_rate = 0;
                        $vat_amount = 0;
                        if ($val->tax_manager_id && $val->getTaxesOfItem) {
                            $vat_rate = $val->getTaxesOfItem->tax_value;
                            if ($item->total_cost > 0) {
                                $vat_amount = $item->total_cost - (($item->total_cost * 100) / ($vat_rate + 100)); //($val->getTaxesOfItem->tax_value*$item->total_cost)/100;
                            }
                        }
                        $item->vat_rate = $vat_rate;
                        $item->vat_amount = $vat_amount;
                        $item->total_cost = $item->total_cost - $vat_amount;
                        $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
                        $item->save();
                    }
                    //get user supplier  phone and send message 
                    $userSupplier = WaUserSupplier::where('wa_supplier_id', $request->wa_supplier_id)->pluck('user_id')->first();
                    $phonenumber = User::where('id', $userSupplier)->where('role_id', 154)->pluck('phone_number')->first();
                    $purchase_no =  $request->purchase_no;
                    $username = User::where('id', $getLoggeduserProfile->id)->pluck('name')->first();
                    $storeloc = WaLocationAndStore::where('id', $getLoggeduserProfile->wa_location_and_store_id)->pluck('location_name')->first();
                    $itemsNo = $inventory->count();
                    $suppliername = WaSupplier::where('id', $request->wa_supplier_id)->pluck('name')->first();
                    $allNote = $request->note ?? '';

                    try {
                        $requisition_message =  "A new Branch Requisition No $purchase_no from $username of $storeloc with $itemsNo item for $suppliername has been created, with the following note: $allNote";

                        $this->smsService->sendMessage($requisition_message,  $phonenumber);
                    } catch (\Throwable $e) {
                    }

                    updateUniqueNumberSeries('EXTERNAL REQUISITIONS', $request->purchase_no);
                    addExternalRequisitionPermissions($row->id, $row->wa_department_id);
                    return true;
                });
                if ($check) {
                    return response()->json(['result' => 1, 'message' => 'External requisition added successfully and Request sent successfully.', 'location' => route($this->model . '.index')]);
                }
                return response()->json(['result' => -1, 'message' => 'Something went wrong']);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['result' => -1, 'message' => $msg]);
        }
    }

    public function sendRequisitionRequest($purchase_no)
    {
        try {

            $row =  WaExternalRequisition::where('status', 'UNAPPROVED')->where('purchase_no', $purchase_no)->first();
            if ($row) {
                $row->status = 'PENDING';
                $row->save();
                addExternalRequisitionPermissions($row->id, $row->wa_department_id);
                Session::flash('success', 'Request sent successfully.');
                return redirect()->route($this->model . '.index');
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }


    public function show($slug)
    {

        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $row =  WaExternalRequisition::whereSlug($slug)->with('getRelatedItem')->first();

        if ($row) {
            foreach ($row->getRelatedItem as $item) {
                $quantity = WaStockMove::where('wa_inventory_item_id', $item->wa_inventory_item_id)
                    ->where('wa_location_and_store_id', $row->wa_store_location_id)->sum('qauntity');
                $itemQoo = WaPurchaseOrderItem::query()
                    ->whereHas('getPurchaseOrder', function ($query) use ($item, $row) {
                        $query->where('status', 'APPROVED')
                            ->where('is_hide', '<>', 'Yes')
                            ->doesntHave('grns')
                            ->where('wa_location_and_store_id', $row->wa_store_location_id);
                    })
                    ->where('wa_inventory_item_id', $item->wa_inventory_item_id)
                    ->sum('quantity');

                $itemSales = WaStockMove::query()
                    ->where('wa_location_and_store_id',  $row->wa_store_location_id)
                    ->where('wa_inventory_item_id', $item->wa_inventory_item_id)
                    ->where(function ($query) {
                        $query->where('document_no', 'like', 'INV-%')
                            ->orWhere('document_no', 'like', 'RTN-%');
                    })
                    ->whereBetween('created_at', [$start_30, $end_30])
                    ->sum('qauntity');

                $maindata = WaInventoryLocationStockStatus::where('wa_inventory_item_id', $item->wa_inventory_item_id)
                    ->where('wa_location_and_stores_id', $row->wa_store_location_id)
                    ->first();

                $item->qoo = $itemQoo;
                $item->sales = abs($itemSales);
                $item->quantity = $quantity;
                $item->maindata = $maindata;
            }

            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            return view('admin.externalrequisition.show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function edit($slug)
    {
        try {

            $row =  WaExternalRequisition::whereSlug($slug)->first();
            if ($row) {
                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                $model = $this->model;
                return view('admin.externalrequisition.edit', compact('title', 'model', 'breadcum', 'row'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
        try {
            $row =  WaExternalRequisition::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'purchase_no' => 'required|unique:wa_external_requisitions,purchase_no,' . $row->id,
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $item = new WaExternalRequisitionItem();
                $item->wa_external_requisition_id = $row->id;
                $item->wa_inventory_item_id = $request->wa_inventory_item_id;
                $item->quantity = $request->quantity;
                $item->note = $request->note;
                $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
                $item->standard_cost = $item_detail->standard_cost;
                $item->total_cost = $item_detail->standard_cost * $request->quantity;
                $item->item_no = $item_detail->stock_id_code;
                $vat_rate = 0;
                $vat_amount = 0;
                if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                    $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                    if ($item->total_cost > 0) {
                        $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $item->total_cost) / 100;
                    }
                }


                $item->vat_rate = $vat_rate;
                $item->vat_amount = $vat_amount;
                $item->total_cost_with_vat =  $item->total_cost + $vat_amount;

                $item->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.edit', $row->slug);
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {
            WaExternalRequisition::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getDapartments(Request $request)
    {
        $rows = WaDepartment::orderBy('department_name', 'asc')->get();
        $data = '<option  value="">Please select department</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->department_name . '</option>';
        }

        return $data;
    }

    public function getItems(Request $request)
    {
        $rows = WaInventoryItem::where('wa_inventory_category_id', $request->selected_inventory_category)->orderBy('title', 'asc')->get();
        $data = '<option  value="">Please select item</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->title . '</option>';
        }

        return $data;
    }

    public function getItemDetail(Request $request)
    {
        $rows = WaInventoryItem::where('id', $request->selected_item_id)->first();

        if ($rows->minimum_order_quantity < 1) {
            $rows->minimum_order_quantity = 1;
        }


        return json_encode(['stock_id_code' => $rows->stock_id_code, 'unit_of_measure' => $rows->wa_unit_of_measure_id ? $rows->wa_unit_of_measure_id : '', 'minimum_order_quantity' => $rows->minimum_order_quantity]);
    }
    public function deletingItemRelation($purchase_no, $id)
    {
        try {
            WaExternalRequisitionItem::whereId($id)->delete();


            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }



    public function downloadPrint($purchase_no)
    {

        $row =  WaExternalRequisition::where('purchase_no', $purchase_no)->first();

        $pdf = PDF::loadView('admin.externalrequisition.print', compact('row'));
        return $pdf->download($purchase_no . '.pdf');
    }


    public function editPurchaseItem($purchase_no, $id)
    {
        try {

            $row =  WaExternalRequisition::where('purchase_no', $purchase_no)
                ->whereHas('getRelatedItem', function ($sql_query) use ($id) {
                    $sql_query->where('id', $id);
                })

                ->first();
            if ($row) {

                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), $row->purchase_no => '', 'Edit' => ''];
                $model = $this->model;


                $form_url = [$model . '.updatePurchaseItem', $row->getRelatedItem->find($id)->id];
                return view('admin.externalrequisition.editItem', compact('title', 'model', 'breadcum', 'row', 'id', 'form_url'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function updatePurchaseItem(Request $request, $id)
    {
        try {


            $item =  WaExternalRequisitionItem::where('id', $id)->first();

            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item_detail->standard_cost * $request->quantity;
            $vat_rate = 0;
            $vat_amount = 0;
            if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                if ($item->total_cost > 0) {
                    $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $item->total_cost) / 100;
                }
            }
            $item->vat_rate = $vat_rate;
            $item->vat_amount = $vat_amount;
            $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.edit', $item->getExternalPurchaseId->slug);
        } catch (\Exception $e) {
            dd($e);
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
    public function archivedRequisition(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $users = Auth::user();
        if (isset($permission[$pmodule . '___archived-requisition']) || $permission == 'superadmin') {
            $lists = WaExternalRequisition::where('status', '!=', 'RESOLVED')->where('is_hide', 'Yes');
            if ($permission != 'superadmin') {
                $lists = $lists->where('user_id', getLoggeduserProfile()->id);
            }
            $lists = $lists->where(function ($e) use ($request) {
                if ($request->branch) {
                    $e->where('restaurant_id', $request->branch);
                }
            })->orderBy('id', 'desc')->get();
            $branches = Restaurant::get();
            $user = getLoggeduserProfile()->id;

            $userrole = getLoggeduserProfile()->role_id;

            if ($userrole == 1  || isset($permission[$pmodule . '___view-all'])) {
                $suppliers = WaSupplier::get();
            } else {
                $suppliers = WaUserSupplier::where('user_id', $user)->join('wa_suppliers', 'wa_user_suppliers.wa_supplier_id', '=', 'wa_suppliers.id')->get();
            }

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.externalrequisition.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'suppliers', 'users'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function get_WaUnitOfMeasure(Request $request)
    {
        $data = \App\Model\WaUnitOfMeasure::select([
            'title as text', 'id as id'
        ])->where(function ($e) use ($request) {
            if ($request->q) {
                $e->where('title', $request->q . '%');
            }
        })->limit(20)->get();
        return response()->json($data);
    }
    public function create_non_stock()
    {
        if (getLoggeduserProfile()->wa_department_id && getLoggeduserProfile()->restaurant_id) {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
                $title = 'Add Non-Stock ' . $this->title;
                $projects = \App\Model\Projects::pluck('title', 'id')->toArray();
                $projectLevel = [];
                $projectLevel['High'] = 'High';
                $projectLevel['Medium'] = 'Medium';
                $projectLevel['Low'] = 'Low';
                $model = $this->model;
                $chart_of_accounts = \App\Model\WaChartsOfAccount::orderBy('id', 'DESC')->get();
                $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
                return view('admin.externalrequisition.create_non_stock', compact('title', 'model', 'breadcum', 'projects', 'projectLevel', 'chart_of_accounts'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } else {
            Session::flash('warning', 'Please update your branch and department');
            return redirect()->back();
        }
    }

    public function store_non_stock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                /* 'purchase_no' => 'required|unique:wa_external_requisitions,purchase_no',*/
                'purchase_date' => 'required|date|date_format:Y-m-d',
                'required_date' => 'required|date|date_format:Y-m-d',
                // 'project_id'=>'required|exists:projects,id',
                'project_level' => 'required|in:High,Medium,Low',
                'item' => 'required|array',
                'quantity.*' => 'required|numeric',
                'note.*' => 'required|string|max:250',
                'note_main' => 'required|string|max:1000',
                // 'lead_man'=>'required|string|max:250',
                'uom.*' => 'required|exists:wa_unit_of_measures,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['result' => 0, 'message' => $validator->errors()]);
            } else {
                $user = getLoggeduserProfile();
                $purchase_no = getCodeWithNumberSeries('EXTERNAL REQUISITIONS');
                $row = new WaExternalRequisition();

                $row->purchase_no = $purchase_no;

                $row->note = $request->note_main;
                // $row->leadman= $request->lead_man;
                $row->required_date = $request->required_date;
                $row->restaurant_id = $user->restaurant_id;
                $row->wa_department_id = $user->wa_department_id;
                $row->user_id = $user->id;
                $row->requisition_date = $request->purchase_date;
                // $row->project_id = $request->project_id;
                $row->project_level = $request->project_level;
                $row->status = 'PENDING';
                $row->type = 'Non-Stock';
                $row->save();
                foreach ($request->item as $key => $item1) {
                    $item = new WaExternalRequisitionItem();
                    $item->wa_external_requisition_id = $row->id;
                    $item->wa_inventory_item_id = NULL;
                    $item->item_no = $item1;
                    $item->gl_code_id = $request->item_gl[$key] ?? 0;
                    $item->unit_of_measure_id = $request->uom[$key];
                    $item->quantity = $request->quantity[$key] ?? 0;
                    $item->note = $request->note[$key] ?? NULL;
                    $item->standard_cost = 0;
                    $item->total_cost = 0;
                    $vat_rate = 0;
                    $vat_amount = 0;
                    $item->vat_rate = $vat_rate;
                    $item->vat_amount = $vat_amount;
                    $item->total_cost_with_vat =  0;
                    $item->save();
                }
                updateUniqueNumberSeries('EXTERNAL REQUISITIONS', $purchase_no);
                addExternalRequisition_nonstock_Permissions($row->id, $row->wa_department_id);

                return response()->json(['result' => 1, 'message' => 'Non-Stock External Requisition Send Successfully.', 'location' => route('external-requisitions.index')]);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['result' => -1, 'message' => $msg]);
        }
    }


    public function getInventryItemDetails(Request $request)
    {
        $data = WaInventoryItem::select([
            'wa_inventory_items.*',
            'wa_inventory_location_stock_status.max_stock as max_stock_f',
            'wa_inventory_location_stock_status.re_order_level',
            \DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . $request->store_location_id . ') as quantity')
        ])
            ->leftJoin('wa_inventory_location_stock_status', function ($e) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', DB::RAW('wa_inventory_items.store_location_id'));
            })->with(['getTaxesOfItem', 'pack_size', 'location'])->where('wa_inventory_items.id', $request->id)->first();
        $view = '';
        if ($data) {
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id[' . $data->id . ']" class="itemid" value="' . $data->id . '">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' . $data->stock_id_code . '">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td>' . $data->description . '</td>
            <td>' . ($data->pack_size->title ?? NULL) . '</td>
            <td><input style="padding: 3px 3px;" data-max_stock="' . $data->max_stock_f . '" type="text" name="item_quantity[' . $data->id . ']" data-id="' . $data->id . '"  class="quantity item_quantity_max_stock form-control" value=""></td>
            <td>' . ($data->quantity ?? 0) . '</td>
            <td>' . $data->re_order_level . '</td>
            <td>' . $data->max_stock_f . '</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>
            </tr>';
        }
        return response()->json($view);
    }

    public function getOutOfStockItems(Request $request)
    {
        try{
        $from = Carbon::now()->subDays(30)->startOfDay();
        $salesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                \DB::raw('ABS(SUM(qauntity)) as total_sales')
            ])
            ->where('wa_location_and_store_id', $request->store_id)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$from, Carbon::now()->endOfDay()])
            ->groupBy('stock_id_code');

        $smallPacksSub = WaStockMove::query()
            ->select([
                'items.wa_inventory_item_id',
                \DB::raw('ABS(SUM(qauntity) / conversion_factor) as pack_sales')
            ])
            ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wa_stock_moves.wa_inventory_item_id')
            ->where('wa_location_and_store_id', $request->store_id)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$from, Carbon::now()->endOfDay()])
            ->groupBy('stock_id_code');

        $qooSub = WaPurchaseOrderItem::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(quantity) as qty_on_order')
            ])
            ->whereHas('getPurchaseOrder', function ($query) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->doesntHave('grns');

                $query->where('wa_location_and_store_id', request()->store_id);
            })->groupBy('wa_inventory_item_id');

        $data = WaInventoryItem::select([
            'wa_inventory_items.*',
            'wa_inventory_location_stock_status.max_stock as max_stock_f',
            'wa_inventory_location_stock_status.re_order_level',
            'wa_inventory_item_supplier_data.price as new_standard_cost',
            \DB::raw('ROUND(IFNULL(sales.total_sales,0) + IFNULL(packs.pack_sales, 0),2) as total_sales'),
            \DB::raw('ROUND(IFNULL(packs.pack_sales, 0), 2) as pack_sales'),
            \DB::raw('IFNULL(lpo.qty_on_order, 0) as qty_on_order'),
            \DB::RAW('(select COALESCE(SUM(wa_stock_moves.qauntity), 0) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . $request->store_id . ') as quantity')
        ])
            ->leftJoin('wa_inventory_location_uom', function ($e) use ($request) {
                $e->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id');
                // $e->where('wa_inventory_location_uom.uom_id', '=', $request->bin_location_id);
            })
            ->whereNotNull('wa_inventory_location_uom.inventory_id')

            ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($request) {
                $e->on('wa_inventory_items.id', '=', 'wa_inventory_item_supplier_data.wa_inventory_item_id');
                $e->where('wa_inventory_item_supplier_data.wa_supplier_id', '=', $request->supplier_id);
            })
            ->whereNotNull('wa_inventory_item_supplier_data.wa_inventory_item_id')
            ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($request) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $request->store_id);
            })->with(['getTaxesOfItem', 'pack_size', 'location'])
            ->whereHas('inventory_item_suppliers', function ($e) use ($request) {
                $e->where('wa_supplier_id', $request->supplier_id);
            })
            ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
            ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($smallPacksSub, 'packs', 'packs.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qooSub, 'lpo', 'lpo.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->where('pack_sizes.can_order', 1)
            //    ->havingRaw("quantity <= max_stock_f")
            ->whereNot('status', '0')
            ->get();

        return response()->json($data->unique('stock_id_code'));
    } catch (\Throwable $th) {
        return response()->json(['error' => 'Error: ' . $th->getMessage()], 500);
    }
    }
}
