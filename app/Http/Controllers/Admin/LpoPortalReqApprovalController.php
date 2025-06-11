<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaLpoPortalReqApproval;
use App\Model\WaPurchaseOrderItem;
use Session;
use Mail;
use DB;

class LpoPortalReqApprovalController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'lpo-portal-req-approval';
        $this->title = 'Purchase Orders Portal Request';
        $this->pmodule = 'lpo-portal-req-approval';
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $user = getLoggeduserProfile();
            $userSuppliers = \App\Model\WaUserSupplier::where('user_id', $user->id)->pluck('wa_supplier_id')->toArray();
            $lists = WaLpoPortalReqApproval::where(function ($e) use ($request) {
                if ($request->from) {
                    $from = ($request->from ?? date('Y-m-d')) . ' 00:00:00';
                    $to = ($request->to ?? date('Y-m-d')) . ' 23:59:59';
                    $e->whereBetween('created_at', [$from, $to]);
                }
            })->whereHas('purchaseOrder', function ($q) use ($pmodule, $request, $user, $userSuppliers) {
                if ($request->supplier) {
                    $q->where('wa_supplier_id', $request->supplier);
                }

                if ($user->role_id != 1 && !isset($permission[$pmodule . '___view-all'])) {
                    $q->whereIn('wa_supplier_id', $userSuppliers);
                }
                $q->where('is_hide', 'No');
            })

                ->with(['purchaseOrder.getrelatedEmployee', 'purchaseOrder.getBranch', 'purchaseOrder.getSupplier', 'purchaseOrder.getDepartment', 'purchaseOrder.getRelatedItem']);

            $lists = $lists->orderBy('id', 'desc')->get();

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.lpo_for_approvals.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function show($id)
    {
        try {

            $row = WaLpoPortalReqApproval::with([
                'purchaseOrder.getrelatedEmployee',
                'purchaseOrder.getBranch',
                'purchaseOrder.getSupplier',
                'purchaseOrder.getDepartment',
                'purchaseOrder.getRelatedItem',
                'getRelatedItem.OrderItem',
                'purchaseOrder.getRelatedItem_with_grn'
            ])->where('id', $id)->first();
            if ($row) {
                $title = 'Approve ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                $model = $this->model;
                return view('admin.lpo_for_approvals.show', compact('title', 'model', 'breadcum', 'row'));
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

    public function update(Request $request, $id)
    {
        try {
            $row = WaLpoPortalReqApproval::with(['purchaseOrder','getRelatedItem.inventory_item.taxManager'])->where('id', $id)->first();
            if ($row) {
                $row->status = $request->status;
                $row->rejection_message = $request->rejection_message ?? "";
                if ($row->status == "Approved") {
                    $total = 0;
                    foreach ($row->getRelatedItem as $key => $item) {
                        $i = WaPurchaseOrderItem::where('id', $item->order_item_id)->first();
                        if(!$i){
                            $i = new WaPurchaseOrderItem();
                            $i->wa_purchase_order_id = $row->wa_purchase_order_id;
                            $i->wa_inventory_item_id = $item->wa_inventory_item_id;
                            $i->item_no = $item->item_code;
                            $i->standard_cost = @$item->inventory_item->standard_cost;
                            $i->prev_standard_cost = @$item->inventory_item->prev_standard_cost;
                            $i->order_price = $item->unit_price;
                            $check_uom = \App\Model\WaInventoryLocationUom::where(
                                [
                                    'inventory_id' => $i->wa_inventory_item_id,
                                    'location_id' => @$row->purchaseOrder->wa_location_and_store_id
                                ]
                            )->first();
                            $i->quantity = $item->quantity;
                            $i->unit_of_measure = @$check_uom->uom_id;
                            $i->supplier_quantity = $item->quantity;
                            $i->unit_conversion = 1;
                            $i->discount_settings = $item->discount_settings;
                            $i->vat_rate = @$item->inventory_item->taxManager->tax_value;
                            $i->discount_settings = $item->discount_settings;
                            $i->discount_percentage = $item->discount_percentage;
                            $i->pack_size_id = @$item->inventory_item->pack_size_id;
                            $i->store_location_id = @$item->inventory_item->store_location_id;
                            $i->selling_price = @$item->inventory_item->selling_price;
                            $i->tax_manager_id = @$item->inventory_item->tax_manager_id;
                            $i->is_exclusive_vat = 0;
                            // $i->tax_manager_id = $item->tax_manager_id;
                            // $i->item_type = $item->item_type;
                        }
                        $i->ordered_quantity = $i->quantity;
                        $i->quantity = $item->quantity;
                        $i->free_qualified_stock = $item->free_qualified_stock;
                        $i->supplier_quantity = $item->quantity;
                        $discount_percentage = $i->discount_percentage;
                        $i->total_cost = $i->standard_cost * $i->quantity;
                        $settings = json_decode($item->discount_settings);
                        if ($settings && $settings->base_discount_type == 'Value') {
                            $i->discount_amount = ($discount_percentage) ? ($item->quantity * $discount_percentage) : 0;
                        } else {
                            $i->discount_amount = ($discount_percentage) ? (($i->total_cost * $discount_percentage) / 100) : 0;
                        }
                        $i->total_cost = $i->total_cost - $i->discount_amount;
                        $i->vat_amount = ($i->vat_rate) ? ($i->total_cost - ($i->total_cost * 100) / ($i->vat_rate + 100)) : 0;
                        $total_cost_with_vat = $i->total_cost;
                        $roundOff = fmod($total_cost_with_vat, 1); //0.25
                        if ($roundOff != 0) {
                            if ($roundOff > '0.50') {
                                $roundOff = round((1 - $roundOff), 2);
                            } else {
                                $roundOff = '-' . round($roundOff, 2);
                            }
                        }
                        $i->round_off = $roundOff;
                        $i->total_cost_with_vat = round($total_cost_with_vat);
                        $i->total_cost = $i->total_cost - $i->vat_amount;
                        $i->save();
                        $total = $i->total_cost_with_vat;
                    }
                    $invoice_discount = ($total * $row->purchaseOrder->invoice_discount_per / 100);
                    $row->purchaseOrder->invoice_discount = $invoice_discount;
                    $row->purchaseOrder->save();
                }
                $row->save();
                $data = \App\Model\WaPurchaseOrder::with([
                    'purchaseOrderItems'
                ])->where('id',$row->wa_purchase_order_id)->first();
                $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
                $api->postRequest('/api/lpo/update-quantity-changed-status/' . $row->lpo_number, ['status' => $row->status, 'order_from' => env('SUPPLIER_SOURCE'),'data'=>$data]);
                $mail = new \App\Mail\LpoSupplierChangesApproved($row->purchaseOrder->getSupplier, $row->purchaseOrder, $row);
                Mail::to($row->purchaseOrder->getSupplier->email)->send($mail);
                return response()->json(['result' => 1, 'message' => "Status sent successfully to supplier portal", 'location' => route($this->model . '.show', $id)]);
            } else {
                throw new \Exception("Error Processing Request");
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json([
                'result' => -1,
                'message' => $msg
            ]);
        }
    }
}
