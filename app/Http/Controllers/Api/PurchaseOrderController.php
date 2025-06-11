<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderDeliverySlots;
use App\Model\WaReceivePurchaseOrder;
use App\Model\WaLpoPortalReqApproval;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Model\WaSupplier;
use App\Model\WaNumerSeriesCode;
use App\Model\WaReceivePurchaseOrderItem;
use App\Mail\SupplierSentLpoForApproval;
use App\Model\WaLpoPortalReqApprovalItem;
use App\Model\WaAccountingPeriod;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use Mail;
use Validator;

class PurchaseOrderController extends Controller
{
    public function receive_lpo_for_approval(Request $request)
    {
        try {
            $validations = Validator::make($request->all(), [
                'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
                'items' => 'required|array',
                'quantity' => 'required|array',
                'reason' => 'required|array',
                'order_item_id' => 'required|array'
            ]);
            if ($validations->fails()) {
                return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
            }
            $row = WaPurchaseOrder::where('purchase_no', $request->lpo_number)->first();
            DB::transaction(function () use ($request, $row) {
                $new = new WaLpoPortalReqApproval();
                $new->lpo_number = $row->purchase_no;
                $new->wa_purchase_order_id = $row->id;
                $new->status = 'Pending';
                $new->save();

                foreach ($request->items as $key => $value) {
                    $iventory = WaInventoryItem::where('stock_id_code', $value)->first();
                    $new_item = new WaLpoPortalReqApprovalItem();
                    $new_item->wa_inventory_item_id = $iventory->id;
                    $new_item->item_code = $value;
                    $new_item->wa_lpo_portal_req_approval_id = $new->id;
                    $new_item->reason = $request->reason[$key];
                    $new_item->quantity = $request->quantity[$key];
                    $new_item->ordered_quantity = $request->ordered_quantity[$key];
                    $new_item->order_item_id = $request->order_item_id[$key];
                    $new_item->free_qualified_stock = $request->free_qualified_stock[$key];


                    $new_item->unit_price = $request->unit_price[$key];
                    $new_item->vat_percentage = $request->vat_percentage[$key];
                    $new_item->vat_amount = $request->vat_amount[$key];
                    $new_item->discount_amount = $request->discount_amount[$key];
                    $new_item->discount_percentage = $request->discount_percentage[$key];
                    $new_item->discount_settings = $request->discount_settings[$key];

                    $new_item->save();
                }
            });
            $supplier = WaSupplier::where('id', $row->wa_supplier_id)->first();
            $mail = new SupplierSentLpoForApproval($supplier, $row);
            Mail::to(env('PROCUREMENT_EMAIL'))->send($mail);
            return response()->json(['message' => 'Added', 'status' => true]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false]);
        }
    }

    public function lpo_return_accepted(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
            'return_no' => 'required|exists:wa_receive_purchase_orders,return_no',
            'status' => 'required|in:Accepted,Rejected',
            'comment' => 'nullable|string|max:250'
        ]);
        if ($validations->fails()) {
            return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
        }
        DB::transaction(function () use ($request) {
            $row = WaReceivePurchaseOrder::with(['parent.getSupplier'])->whereHas('parent', function ($e) use ($request) {
                $e->where('purchase_no', $request->lpo_number);
            })->where('return_no', $request->return_no)->where('return_status', 'Pending')->first();
            $row->return_status = $request->status;
            $row->return_comment = $request->comment;
            $row->credit_note_doc = $request->credit_note;
            $row->save();
            if ($row) {
                $series_module = WaNumerSeriesCode::where('module', 'RETURN')->first();
                $receiveOrderItems = WaReceivePurchaseOrderItem::where('wa_receive_purchase_order_id', $row->id)->with([
                    'parent.getInventoryItemDetail.getAllFromStockMoves',
                    'parent.getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail'
                ])->where('return_quantity', '>', 0)->get();
                $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                if (count($receiveOrderItems) > 0) {
                    foreach ($receiveOrderItems as $purchaseOrderItem) {
                        $stock_qoh = @$purchaseOrderItem->parent->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', @$purchaseOrderItem->parent->getInventoryItemDetail->store_location_id)->sum('qauntity') ?? 0;
                        $stockMove = new WaStockMove();
                        $stockMove->user_id = $row->returned_by;
                        $stockMove->wa_purchase_order_id = $row->parent->id;
                        $stockMove->restaurant_id = $row->parent->restaurant_id;
                        $stockMove->wa_location_and_store_id = @$row->parent->wa_location_and_store_id;
                        $stockMove->wa_inventory_item_id = @$purchaseOrderItem->parent->getInventoryItemDetail->id;
                        $stockMove->stock_id_code = $purchaseOrderItem->parent->getInventoryItemDetail->stock_id_code;
                        $stockMove->grn_type_number = $series_module->type_number;
                        $stockMove->document_no = $row->return_no;
                        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                        $price = $purchaseOrderItem->order_price;
                        if ($purchaseOrderItem->supplier_discount && $purchaseOrderItem->supplier_discount > 0) {
                            $discount_percent = $purchaseOrderItem->supplier_discount;
                            $discount_amount = ($discount_percent * $price) / 100;
                            $price = $price - $discount_amount;
                            $stockMove->discount_percent = $discount_percent;
                        }
                        $stockMove->price = $price;
                        $stockMove->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $stockMove->refrence = (@$row->parent->getSupplier->supplier_code) . '/' . (@$row->parent->getSupplier->name) . '/' . $row->parent->purchase_no;
                        $stockMove->qauntity =  - ($purchaseOrderItem->return_quantity * $purchaseOrderItem->parent->unit_conversion);
                        $stockMove->standard_cost = $purchaseOrderItem->parent->standard_cost;
                        $stock_qoh += $stockMove->qauntity;
                        $stockMove->new_qoh = $stock_qoh;
                        $stockMove->save();
                    }
                }
            }
        });
        return response()->json(['message' => 'Added', 'status' => true]);
    }

    public function receive_order_details_update(Request $request, $slug)
    {
        $row = WaPurchaseOrder::where('purchase_no', $slug)->first();
        if ($row) {
            $row->cu_invoice_number = $request->cu_invoice_number ?? $row->cu_invoice_number;
            $row->vehicle_reg_no = $request->vehicle_reg_no ?? $row->vehicle_reg_no;
            $row->driver_name = $request->driver_name ?? $row->driver_name;
            $row->driver_phone = $request->driver_phone ?? $row->driver_phone;
            $row->receive_note_doc_no = $request->receive_note_doc_no ?? $row->receive_note_doc_no;
            $row->supplier_invoice_no = $request->supplier_invoice_no ?? $row->supplier_invoice_no;
            $row->invoice_control_amount = $request->invoice_control_amount ?? $row->invoice_control_amount;
            $documents = [];
            if ($request->delivery_note) {
                $documents['delivery_note'] = $request->delivery_note;
            }
            if ($request->supplier_invoice) {
                $documents['supplier_invoice'] = $request->supplier_invoice;
            }
            if ($request->other_documents) {
                $documents['other_documents'] = $request->other_documents;
            }
            $documents['from_portal'] = "true";
            $row->documents = json_encode($documents);
            $row->save();
        }
    }

    public function get_delivery_slots(Request $request, $purchase_no)
    {
        if (!$request->day) {
            return response()->json(['message' => 'Day is required', 'status' => false]);
        }
        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $purchase_no)->first();
        $slot = OrderDeliverySlots::where('branch_id', $purchaseOrder->restaurant_id)->where('day', $request->day)->first();
        if (!$slot) {
            return response()->json(['message' => 'Slot not found', 'status' => false]);
        }
        return response()->json(['message' => 'Slot found', 'data' => $slot, 'status' => true]);
    }

    public function add_sub_lpo(Request $request, $purchase_no)
    {
        $order = DB::transaction(function () use ($request, $purchase_no) {
            $purchaseOrder = WaPurchaseOrder::where('lpo_type', 'Bulk')->where('purchase_no', $purchase_no)->firstOrFail();
            $neworder = new WaPurchaseOrder();
            $neworder->parent_id = $purchaseOrder->id;
            $neworder->user_id = $purchaseOrder->user_id;
            $neworder->purchase_no = $request->purchase_no;
            $neworder->restaurant_id = $purchaseOrder->restaurant_id;
            $neworder->wa_department_id = $purchaseOrder->wa_department_id;
            $neworder->wa_supplier_id = $purchaseOrder->wa_supplier_id;
            $neworder->wa_location_and_store_id = $purchaseOrder->wa_location_and_store_id;
            $neworder->purchase_date = $purchaseOrder->purchase_date;
            $neworder->status = $purchaseOrder->status;
            $neworder->advance_payment = $purchaseOrder->advance_payment;
            $neworder->mother_lpo = $purchaseOrder->mother_lpo;
            $neworder->vehicle_reg_no = $purchaseOrder->vehicle_reg_no;
            $neworder->receive_note_doc_no = $purchaseOrder->receive_note_doc_no;
            $neworder->supplier_archived = $purchaseOrder->supplier_archived;
            $neworder->wa_priority_level_id = $purchaseOrder->wa_priority_level_id;
            $neworder->note = $purchaseOrder->note;
            $neworder->type = $purchaseOrder->type;
            $neworder->cu_invoice_number = $purchaseOrder->cu_invoice_number;
            $neworder->wa_unit_of_measures_id = $purchaseOrder->wa_unit_of_measures_id;
            $neworder->documents = $purchaseOrder->documents;
            $neworder->sent_to_supplier = $purchaseOrder->sent_to_supplier;
            $neworder->supplier_own = $purchaseOrder->supplier_own;
            $neworder->vehicle_id = $purchaseOrder->vehicle_id;
            $neworder->employee_id = $purchaseOrder->employee_id;
            $neworder->supplier_invoice_no = $purchaseOrder->supplier_invoice_no;
            $neworder->invoice_discount_per = $purchaseOrder->invoice_discount_per;
            $neworder->invoice_discount = $purchaseOrder->invoice_discount;
            $neworder->transport_rebate_discount = $purchaseOrder->transport_rebate_discount;
            $neworder->transport_rebate_discount_value = $purchaseOrder->transport_rebate_discount_value;
            $neworder->transport_rebate_discount_type = $purchaseOrder->transport_rebate_discount_type;
            $neworder->driver_name = $purchaseOrder->driver_name;
            $neworder->driver_phone = $purchaseOrder->driver_phone;
            $neworder->invoice_control_amount = $purchaseOrder->invoice_control_amount;
            $neworder->lpo_type = 'Sub Order';
            $neworder->save();

            foreach ($request->quantities as $code => $quantity) {
                $current = WaPurchaseOrderItem::where('item_no', $code)->where('wa_purchase_order_id', $purchaseOrder->id)->first();
                $new = new WaPurchaseOrderItem();
                $new->wa_purchase_order_id = $neworder->id;
                $new->wa_inventory_item_id = $current->wa_inventory_item_id;
                $new->item_no = $current->item_no;
                $new->quantity = $quantity;
                $new->standard_cost = $current->standard_cost;
                $new->prev_standard_cost = $current->prev_standard_cost;
                $new->order_price = $current->order_price;
                $new->supplier_uom_id = $current->supplier_uom_id;
                $new->unit_of_measure = $current->unit_of_measure;
                $new->supplier_quantity = $quantity;
                $new->unit_conversion = $current->unit_conversion;
                $new->vat_rate = $current->vat_rate;
                $new->is_exclusive_vat = $current->is_exclusive_vat;

                $new->note = $current->note;
                $new->discount_percentage = $current->discount_percentage;
                $new->pack_size_id = $current->pack_size_id;
                $new->already_received = 0;
                $new->store_location_id = $current->store_location_id;
                $new->selling_price = $current->selling_price;
                $new->tax_manager_id = $current->tax_manager_id;
                $new->item_type = $current->item_type;
                $new->ordered_quantity = $quantity;
                $new->discount_settings = $current->discount_settings;
                $new->total_cost = $quantity * $current->order_price;
                $discount_settings = json_decode($current->discount_settings);
                $new->discount_amount = $new->discount_percentage ? (($new->total_cost * $new->discount_percentage) / 100) : 0;
                if ($discount_settings) {
                    if ($discount_settings->base_discount_type && $discount_settings->base_discount_type == 'Value') {
                        $new->discount_amount = $new->discount_percentage ? ($quantity * $new->discount_percentage) : 0;
                    }
                }
                $new->total_cost -= $new->discount_amount;
                $new->vat_amount = $new->total_cost - $new->total_cost / (1 + $current->vat_rate / 100);
                $new->total_cost_with_vat = round($new->total_cost);
                $roundOff = fmod($new->total_cost, 1); //0.25
                if ($roundOff != 0) {
                    if ($roundOff > 0.50) {
                        $roundOff = round(1 - $roundOff, 2);
                    } else {
                        $roundOff = '-' . round($roundOff, 2);
                    }
                }
                $new->round_off = $roundOff;
                $new->total_cost -= $new->vat_amount;
                $new->free_qualified_stock = round(($current->free_qualified_stock / $current->quantity) * $quantity);
                $new->save();
            }

            return WaPurchaseOrder::with(['purchaseOrderItems'])->where('lpo_type', 'Sub Order')->where('purchase_no', $neworder->purchase_no)->firstOrFail();
        });
        if ($order) {
            return response()->json([
                'result' => 1,
                'message' => 'Sub Order added',
                'data' => $order
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
            'data' => []
        ]);
    }

    public function acceptLpo(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
        ]);

        if ($validations->fails()) {
            return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
        }

        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $request->lpo_number)->first();
        $purchaseOrder->update([
            'supplier_accepted' => true,
        ]);

        return response()->json(['message' => 'Order Accepted Successfully', 'status' => true]);
    }

    public function reverseLpo(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
        ]);

        if ($validations->fails()) {
            return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
        }

        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $request->lpo_number)->first();
        $purchaseOrder->update([
            'supplier_accepted' => false,
        ]);

        return response()->json(['message' => 'Order reversed Successfully', 'status' => true]);
    }

    public function slotBooked(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
        ]);

        if ($validations->fails()) {
            return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
        }

        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $request->lpo_number)->first();
        $purchaseOrder->update([
            'slot_booked' => true,
        ]);

        return response()->json(['message' => 'Slot booking updated Successfully', 'status' => true]);
    }

    public function goodsReleased(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'lpo_number' => 'required|exists:wa_purchase_orders,purchase_no',
        ]);

        if ($validations->fails()) {
            return response()->json(['message' => 'Validation Erros', 'errors' => $validations->errors(), 'status' => false]);
        }

        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $request->lpo_number)->first();
        $purchaseOrder->update([
            'goods_released' => true,
        ]);

        return response()->json(['message' => 'Goods release updated Successfully', 'status' => true]);
    }
}
