<?php

namespace App\Http\Controllers\Admin;

use App\Actions\SupplierInvoice\Discount\CreateDeliveryDistributionDiscount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
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
use App\Models\TradeAgreement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReceivePurchasedOrderController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'receive-purchase-order';
        $this->title = 'Receive Purchase Order';
        $this->pmodule = 'receive-purchase-order';
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
            // Allow initiating GRN even if supplier portal flags are not set
            // Original query with supplier portal dependencies
            $lists = WaPurchaseOrder::where('status', 'APPROVED')
                ->with([
                    'getrelatedEmployee',
                    'uom',
                    'getStoreLocation',
                    'getSupplier',
                    'getDepartment',
                    'getRelatedItem'
                ])
                ->where('advance_payment', 0)
                ->doesntHave('reception')
                ->doesntHave('invoices');
                
            // Log that we're bypassing supplier portal checks
            \Illuminate\Support\Facades\Log::info('GRN initiation: Bypassing supplier portal checks to allow GRN creation for approved LPOs');

            if ($request->store) {
                $lists = $lists->where('wa_location_and_store_id', $request->store);
            }

            if ($request->supplier) {
                $lists = $lists->where('wa_supplier_id', $request->supplier);
            }

            if ($request->item) {
                $lists = $lists->whereHas('getRelatedItem', function ($query) use ($request) {
                    $query->where('wa_inventory_item_id',  $request->item);
                })->doesntHave('grns');
            }

            if ($user->role_id != 1 && !isset($permission['maintain-items___view-per-branch'])) {
                $preselect_location = $user_location;
                $disable_select = true;
                $lists = $lists->where('restaurant_id', $user_restaurant_id);
            }

            $lists = $lists->where('is_hide', 'No')->orderBy('id', 'desc')->get()->map(function ($po) {
                $grns = DB::table('wa_grns')->where('wa_purchase_order_id', $po->id)->selectRaw('SUM(qty_received * standart_cost_unit) as amount_delivered')->get();
                $po->amount_delivered = $grns->sum('amount_delivered');

                return $po;
            });

            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.receivepurchaseorders.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'preselect_location', 'disable_select'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create() {}


    public function store(Request $request) {}


    public function show($slug)
    {
        // Modified to bypass supplier_accepted check
        $row =  WaPurchaseOrder::with([
            'getRelatedItem' => function ($e) {
                $e->select([
                    '*',
                    DB::RAW('( select sum(delivered_quantity) from wa_receive_purchase_order_items where wa_receive_purchase_order_items.wa_purchase_order_item_id = wa_purchase_order_items.id AND exists (
                            select id from wa_receive_purchase_orders where wa_receive_purchase_orders.id = wa_receive_purchase_order_items.wa_receive_purchase_order_id and wa_receive_purchase_orders.status in ("Pending","Confirmed")
                        )) as pending_qty'),
                    DB::RAW('( select sum(delivered_quantity) from wa_receive_purchase_order_items where wa_receive_purchase_order_items.wa_purchase_order_item_id = wa_purchase_order_items.id AND exists (
                            select id from wa_receive_purchase_orders where wa_receive_purchase_orders.id = wa_receive_purchase_order_items.wa_receive_purchase_order_id and wa_receive_purchase_orders.status in ("Processed")
                        )) as processed_qty')
                ]);
            },
            'getRelatedItem.getInventoryItemDetail',
            'getRelatedItem.getInventoryItemDetail.getInventoryCategoryDetail',
            'getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail',
            'getRelatedItem.getInventoryItemDetail.location',
            'getRelatedAuthorizationPermissions',
            'getRelatedAuthorizationPermissions.getExternalAuthorizerProfile'
        ])
            // Removed supplier_accepted check to allow GRN creation for any approved LPO
            ->whereSlug($slug)->first();
            
        // Log that we're bypassing supplier acceptance check
        \Illuminate\Support\Facades\Log::info('GRN show: Bypassing supplier acceptance check for LPO ' . $slug);

        $trade = TradeAgreement::where('wa_supplier_id', $row->wa_supplier_id)
            ->with(['discounts' => function ($query) {
                $query->where('discount_type', 'Distribution Discount on Delivery');
            }])
            ->whereHas('discounts', function ($query) {
                $query->where('discount_type', 'Distribution Discount on Delivery');
            })->first();

        $discountItems = [];
        if ($trade) {
            $discountItems = $this->getDiscountItems($row, $trade);
        }

        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            $pmodule = $this->pmodule;
            $permission =  $this->mypermissionsforAModule();
            return view('admin.receivepurchaseorders.show', compact('title', 'model', 'breadcum', 'row', 'permission', 'pmodule', 'discountItems'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getDiscountItems($order, $trade)
    {
        $items = [];
        $settings = json_decode($trade->discounts->first()->other_options);

        foreach ($order->purchaseOrderItems as $item) {
            $discount = app(CreateDeliveryDistributionDiscount::class)->getItemDiscount($item->inventoryItem, $settings, $order->storeLocation);
            $discount->quantity = $item->quantity;

            $items[] = $discount;
        }

        return collect($items);
    }

    public function print(Request $request) {}

    public function exportToPdf($slug) {}

    public function edit($slug) {}

    public function update(Request $request, $slug)
    {
        try {
            // Modified validation to make some fields optional when supplier portal data is missing
            $validator = Validator::make($request->all(), [
                'supplier_invoice_no' => 'nullable|string|max:255', // Made optional
                'cu_invoice_number' => 'required|string|max:255|unique:wa_grns,cu_invoice_number',
                'note' => 'nullable|string',
                'vehicle_reg_no' => 'nullable|string',
                'receive_note_doc_no' => 'required|string',
                'purchase_order_ids' => 'required|array',
                'supplier_invoice' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,docx,doc|max:2048', // Made optional
                'delivery_note' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,docx,doc',
                'other_documents' => 'nullable|mimes:jpeg,png,jpg,gif,pdf,docx,doc',
                'invoice_control_amount' => 'nullable|min:0'
            ]);
            
            // Log that we're allowing GRN creation with missing supplier data
            \Illuminate\Support\Facades\Log::info('GRN creation: Allowing creation with potentially missing supplier portal data');
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $purchaseses = WaPurchaseOrderItem::with([
                'getInventoryItemDetail',
                'getInventoryItemDetail.getAllFromStockMoves',
                'getInventoryItemDetail.getInventoryCategoryDetail',
                'getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
                'getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail',
            ])->select([
                '*',
                DB::RAW('( select sum(delivered_quantity) from wa_receive_purchase_order_items where wa_receive_purchase_order_items.wa_purchase_order_item_id = wa_purchase_order_items.id AND exists (
                    select id from wa_receive_purchase_orders where wa_receive_purchase_orders.id = wa_receive_purchase_order_items.wa_receive_purchase_order_id and wa_receive_purchase_orders.status in ("Pending","Confirmed")
                )) as pending_qty'),
                DB::RAW('( select sum(delivered_quantity) from wa_receive_purchase_order_items where wa_receive_purchase_order_items.wa_purchase_order_item_id = wa_purchase_order_items.id AND exists (
                    select id from wa_receive_purchase_orders where wa_receive_purchase_orders.id = wa_receive_purchase_order_items.wa_receive_purchase_order_id and wa_receive_purchase_orders.status in ("Processed")
                )) as processed_qty')
            ])->whereIn('id', $request->purchase_order_ids)->get();

            foreach ($purchaseses as $key => $value) {
                $delivered_quantity = 'delivered_quantity_' . $value->id;
                if ($value->getInventoryItemDetail->block_this == 1) {
                    // Session::flash('warning', $value->getInventoryItemDetail->stock_id_code . ': The product has been blocked from sale due to a change in standard cost');
                    // return redirect()->back()->withInput();
                    return response()->json([
                        'errors' => [
                            'message' => [$value->getInventoryItemDetail->stock_id_code . ': The product has been blocked from sale due to a change in standard cost']
                        ]
                    ], 422);
                }
                // if(($value->pending_qty + $value->already_received + $request->$delivered_quantity) > $value->supplier_quantity){
                //     Session::flash('warning', $value->getInventoryItemDetail->stock_id_code.': Delivery QTY is higher than the required QTY!');
                //     return redirect()->back()->withInput();
                // }
            }
            $check = DB::transaction(function () use ($request, $slug, $purchaseses) {
                $getLoggeduserProfile = getLoggeduserProfile();
                $purchaseOrder =  WaPurchaseOrder::with([
                    'getSupplier.getPaymentTerm',
                    'getBranch.getAssociateCompany.good_receive'
                ])->whereSlug($slug)->first();
                $r_p = new WaReceivePurchaseOrder();
                $r_p->wa_purchase_order_id = $purchaseOrder->id;
                $r_p->status = "Confirmed";
                $r_p->initiated_by = $getLoggeduserProfile->id;
                $r_p->initiated_at = date('Y-m-d H:i:s');
                $r_p->invoice_control_amount = $request->invoice_control_amount;
                $r_p->wa_location_and_store_id = $purchaseOrder->wa_location_and_store_id;
                $r_p->wa_unit_of_measures_id = $request->wa_unit_of_measures_id;
                $r_p->cu_invoice_number = $request->cu_invoice_number;
                $r_p->supplier_invoice_no = $request->supplier_invoice_no;
                $r_p->note = $request->note;
                $r_p->vehicle_reg_no = $request->vehicle_reg_no;
                $r_p->receive_note_doc_no = $request->receive_note_doc_no;
                $r_p->confirmed_by = $getLoggeduserProfile->id;
                $r_p->confirmed_at = date('Y-m-d H:i:s');

                $documents = [];
                $path = 'uploads/purchases_docs';
                if (!$purchaseOrder->documents || count((array)json_decode($purchaseOrder->documents)) == 0) {
                    if ($request->file('delivery_note')) {
                        $file = $request->file('delivery_note');
                        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path($path), $fileName);
                        $documents['delivery_note'] = $fileName;
                    }
                    if ($request->file('supplier_invoice')) {
                        $file = $request->file('supplier_invoice');
                        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path($path), $fileName);
                        $documents['supplier_invoice'] = $fileName;
                    }
                    if ($request->file('other_documents')) {
                        $file = $request->file('other_documents');
                        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path($path), $fileName);
                        $documents['other_documents'] = $fileName;
                    }
                    $r_p->documents = json_encode($documents);
                } else {
                    $r_p->documents = $purchaseOrder->documents;
                }
                $r_p->save();

                $dateTime = date('Y-m-d H:i:s');
                if (count($purchaseses) > 0) {
                    $uoms = [];
                    $childs = [];
                    foreach ($purchaseses as $purchaseOrderItem) {
                        $order_price = 'order_price_' . $purchaseOrderItem->id;
                        $uom = 'uom_' . $purchaseOrderItem->id;
                        $purchaseOrderItem->unit_of_measure = $request->$uom;
                        $purchaseOrderItem->save();

                        $supplier_discount = 'supplier_discount_' . $purchaseOrderItem->id;
                        $delivered_quantity = 'delivered_quantity_' . $purchaseOrderItem->id;
                        $return_quantity = 'return_quantity_' . $purchaseOrderItem->id;
                        $return_reason = 'return_reason_' . $purchaseOrderItem->id;
                        $return_doc = 'return_doc_' . $purchaseOrderItem->id;
                        $return_doc_name = '';
                        if ($request->file($return_doc)) {
                            $file = $request->file($return_doc);
                            $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path($path), $fileName);
                            $return_doc_name = $fileName;
                        }
                        $childs[] = [
                            'wa_receive_purchase_order_id' => $r_p->id,
                            'wa_purchase_order_item_id' => $purchaseOrderItem->id,
                            'delivered_quantity' => $request->$delivered_quantity,
                            'supplier_discount' => $request->$supplier_discount,
                            'return_quantity' => $request->$return_quantity,
                            'return_reason' => $request->$return_reason,
                            'return_doc' => $return_doc_name,
                            'order_price' => $request->$order_price // $purchaseOrderItem->order_price
                        ];
                        $check_uom = \App\Model\WaInventoryLocationUom::where(
                            [
                                'inventory_id' => $purchaseOrderItem->wa_inventory_item_id,
                                'location_id' => $purchaseOrder->wa_location_and_store_id
                            ]
                        )->first();
                        if (!$check_uom) {
                            throw new Exception("Item " . $purchaseOrderItem->item_no . " is not allocated to a bin location");
                        }
                    }
                    if (count($uoms) > 0) {
                        \App\Model\WaInventoryLocationUom::insert($uoms);
                    }
                    WaReceivePurchaseOrderItem::insert($childs);
                }

                app(CreateDeliveryDistributionDiscount::class)->create($purchaseOrder, $request->supplier_invoice_no, auth()->user());

                return true;
            });
            if (!$check) {
                throw new \Exception("Something went wrong");
            }
            return response()->json(['message' => 'Request Sent Successfully', 'redirect_url' => route($this->model . '.index')], 200);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['error' => $msg], 500);
        }
    }

    public function __update(Request $request, $slug)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wa_location_and_store_id' => 'required|exists:wa_location_and_stores,id',
                'wa_unit_of_measures_id' => 'required',
                'cu_invoice_number' => 'required|string|max:255|unique:wa_grns,cu_invoice_number',
                'supplier_invoice_no' => 'required|string|max:255'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $purchaseses = WaPurchaseOrderItem::with([
                'getInventoryItemDetail',
                'getInventoryItemDetail.getAllFromStockMoves',
                'getInventoryItemDetail.getInventoryCategoryDetail',
                'getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
                'getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail',
            ])->whereIn('id', $request->purchase_order_ids)->get();
            foreach ($purchaseses as $key => $value) {
                $order_pirce = 'order_price_' . $value->id;
                if ($value->getInventoryItemDetail->block_this == 1) {
                    Session::flash('warning', $value->getInventoryItemDetail->stock_id_code . ': The product has been blocked from sale due to a change in standard cost');
                    return redirect()->back()->withInput();
                }
            }
            $check = DB::transaction(function () use ($request, $slug, $purchaseses) {
                $getLoggeduserProfile = getLoggeduserProfile();
                $purchaseOrder =  WaPurchaseOrder::with([
                    'getSupplier.getPaymentTerm',
                    'getBranch.getAssociateCompany.good_receive'
                ])->whereSlug($slug)->first();
                $purchaseOrder->wa_location_and_store_id = $request->wa_location_and_store_id;
                $purchaseOrder->wa_unit_of_measures_id = $request->wa_unit_of_measures_id;
                $purchaseOrder->note = $request->note;
                $documents = [];
                $path = 'uploads/purchases_docs'; // Your desired upload directory
                if ($request->file('delivery_note')) {
                    $file = $request->file('delivery_note');
                    $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($path), $fileName);
                    $documents['delivery_note'] = $fileName;
                }
                if ($request->file('supplier_invoice')) {
                    $file = $request->file('supplier_invoice');
                    $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($path), $fileName);
                    $documents['supplier_invoice'] = $fileName;
                }
                if ($request->file('other_documents')) {
                    $file = $request->file('other_documents');
                    $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($path), $fileName);
                    $documents['other_documents'] = $fileName;
                }
                $purchaseOrder->documents = json_encode($documents);
                $series_module = WaNumerSeriesCode::where('module', 'GRN')->first();
                // $SUPPLIER_INVOICE_NO_series_module = WaNumerSeriesCode::where('module', 'SUPPLIER_INVOICE_NO')->first();
                $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                $grn_number = getCodeWithNumberSeries('GRN');
                $dateTime = date('Y-m-d H:i:s');
                $vat_amount_arr = [];
                $cr_amount = [];
                $allGood = true;
                if (count($purchaseses) > 0) {

                    foreach ($purchaseses as $purchaseOrderItem) {
                        $order_pirce = 'order_price_' . $purchaseOrderItem->id;
                        $supplier_discount = 'supplier_discount_' . $purchaseOrderItem->id;
                        $delivered_quantity = 'delivered_quantity_' . $purchaseOrderItem->id;

                        $purchaseOrderItem->already_received = $purchaseOrderItem->already_received + $request->$delivered_quantity;
                        if ($purchaseOrderItem->already_received != $purchaseOrderItem->supplier_quantity) {
                            $allGood = false;
                        }
                        $purchaseOrderItem->save();
                        $accountno = '';
                        $store_location_id = NULL;
                        $accountno = @$purchaseOrderItem->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code;
                        $stock_qoh = @$purchaseOrderItem->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', @$purchaseOrderItem->getInventoryItemDetail->store_location_id)->sum('qauntity') ?? 0;

                        $grn = new WaGrn();
                        $grn->wa_purchase_order_item_id = $purchaseOrderItem->id;
                        $grn->wa_purchase_order_id = $purchaseOrder->id;
                        $grn->wa_supplier_id = $purchaseOrder->wa_supplier_id;
                        $grn->grn_number =  $grn_number;
                        $grn->item_code = $purchaseOrderItem->item_no;
                        $grn->supplier_invoice_no = $request->supplier_invoice_no;
                        $grn->cu_invoice_number = $request->cu_invoice_number;
                        $grn->delivery_date = $dateTime; //date('Y-m-d');
                        $grn->item_description = @$purchaseOrderItem->getInventoryItemDetail->title;
                        //~ $grn->qty_received = $purchaseOrderItem->quantity;
                        $grn->qty_received = $request->$delivered_quantity;
                        $grn->qty_invoiced = $purchaseOrderItem->supplier_quantity;
                        $grn->standart_cost_unit = $purchaseOrderItem->standard_cost;
                        $invoice_calculation = ['order_price' => $request->$order_pirce, 'discount_percent' => 0, 'vat_rate' => $purchaseOrderItem->vat_rate, 'qty' => $grn->qty_received, 'unit' => @$purchaseOrderItem->getSupplierUomDetail->title];
                        if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                            $invoice_calculation['discount_percent'] = $request->$supplier_discount;
                        }
                        $grn->invoice_info = json_encode($invoice_calculation);
                        $grn->save();
                        //storing grn enteries end

                        //move to stock moves start
                        $stockMove = new WaStockMove();
                        $stockMove->user_id = $getLoggeduserProfile->id;
                        $stockMove->wa_purchase_order_id = $purchaseOrder->id;
                        $stockMove->restaurant_id = $purchaseOrder->restaurant_id;
                        $stockMove->wa_location_and_store_id = @$purchaseOrder->wa_location_and_store_id;
                        $stockMove->wa_inventory_item_id = @$purchaseOrderItem->getInventoryItemDetail->id;
                        $stockMove->stock_id_code = $purchaseOrderItem->getInventoryItemDetail->stock_id_code;
                        $stockMove->grn_type_number = $series_module->type_number;
                        $stockMove->document_no = $grn_number;
                        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                        $price = $request->$order_pirce;
                        $wainventoryitem = WaInventoryItem::where('stock_id_code', $purchaseOrderItem->getInventoryItemDetail->stock_id_code)->first();


                        if ($wainventoryitem) {


                            ########################### Get New Weighted Average #########################


                            $item_available_qty = $wainventoryitem->getAllFromStockMoves->sum('qauntity');
                            $inventoryitem_standard_cost = $wainventoryitem->standard_cost;
                            $opening_inventory = $inventoryitem_standard_cost * $item_available_qty;

                            $purchased_unit_qty = $request->$delivered_quantity;
                            $purchased_standard_cost = $request->$order_pirce;

                            $grn_purchase = $purchased_standard_cost * $purchased_unit_qty;
                            $cost_of_goods_avaialble = $opening_inventory + $grn_purchase;

                            $total_units_available_for_sale = $item_available_qty + $purchased_unit_qty;

                            try {
                                $new_weighted_average = $cost_of_goods_avaialble / $total_units_available_for_sale;
                            } catch (\Throwable $th) {
                                $new_weighted_average = 0;
                            }

                            if ($item_available_qty == 0) {
                                $new_weighted_average = $purchased_standard_cost;
                            }



                            $wainventoryitem->prev_standard_cost = $wainventoryitem->standard_cost;
                            $wainventoryitem->standard_cost    = $new_weighted_average;
                            $wainventoryitem->cost_update_time = date('d-m-Y H:i:s');
                            $wainventoryitem->save();

                            $weightedAverageHistory = new WeightedAverageHistory();
                            $weightedAverageHistory->purchase_order_id = $purchaseOrderItem->wa_purchase_order_id;
                            $weightedAverageHistory->purchase_order_item_id = $purchaseOrderItem->id;
                            $weightedAverageHistory->date = date('Y-m-d');
                            $weightedAverageHistory->grn_no = $grn_number;
                            $weightedAverageHistory->lpo_no = $purchaseOrder->purchase_no;
                            $weightedAverageHistory->item_code = $purchaseOrderItem->item_no;
                            $weightedAverageHistory->item_description = @$purchaseOrderItem->getInventoryItemDetail->title;
                            $weightedAverageHistory->opening_standard_cost = $inventoryitem_standard_cost;
                            $weightedAverageHistory->opening_qty = $item_available_qty;
                            $weightedAverageHistory->opening_value = $opening_inventory;
                            $weightedAverageHistory->grn_standard_cost = $purchased_standard_cost;
                            $weightedAverageHistory->grn_qty = $purchased_unit_qty;
                            $weightedAverageHistory->grn_value = $grn_purchase;
                            $weightedAverageHistory->total_value = $cost_of_goods_avaialble;
                            $weightedAverageHistory->total_inventory = $total_units_available_for_sale;
                            $weightedAverageHistory->new_weighted_average = $new_weighted_average;
                            $weightedAverageHistory->save();
                        }
                        if ($request->$supplier_discount && $request->$supplier_discount > 0) {
                            $discount_percent = $request->$supplier_discount;
                            $discount_amount = ($discount_percent * $price) / 100;
                            $price = $price - $discount_amount;
                            $stockMove->discount_percent = $discount_percent;
                        }
                        $stockMove->price = $price;
                        $stockMove->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $stockMove->refrence = (@$purchaseOrder->getSupplier->supplier_code) . '/' . (@$purchaseOrder->getSupplier->name) . '/' . $purchaseOrder->purchase_no;
                        $stockMove->qauntity =  $request->$delivered_quantity * $purchaseOrderItem->unit_conversion;
                        //$stockMove->qauntity = $request->$delivered_quantity;;
                        $stockMove->standard_cost = $purchaseOrderItem->standard_cost;
                        $stock_qoh += $stockMove->qauntity;
                        $stockMove->new_qoh = $stock_qoh;
                        $stockMove->save();
                        //move to stock moves end


                        //managae dr accounts start/

                        $dr =  new WaGlTran();
                        $dr->grn_type_number = $series_module->type_number;
                        $dr->grn_last_used_number = $series_module->last_number_used;


                        $dr->transaction_type = $series_module->description;
                        $dr->transaction_no = $grn_number;
                        $dr->trans_date = $dateTime;
                        $dr->restaurant_id = $getLoggeduserProfile->restaurant_id;

                        $dr->wa_purchase_order_id = $purchaseOrder->id;
                        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                        $dr->supplier_account_number = null;
                        $dr->account = $accountno;
                        //$dr->amount = $price*$request->$delivered_quantity;

                        //managae dr accounts end/
                        $vat_amm = 0;
                        $total_price = $price * ($request->$delivered_quantity);
                        $cr_amount[] = $total_price;
                        if ($purchaseOrderItem->vat_rate && $purchaseOrderItem->vat_rate > 0) {
                            $vat_amm = $total_price - (($total_price * 100) / ($purchaseOrderItem->vat_rate + 100));
                            $vat_amount_arr[] = $vat_amm; //($purchaseOrderItem->vat_rate * $total_price) / 100;
                            //$cr_amount[] = $vat_amm; //($purchaseOrderItem->vat_rate * $total_price) / 100;
                        }
                        $dr->amount = $total_price - $vat_amm;
                        $dr->narrative = $purchaseOrder->purchase_no . '/' . (@$purchaseOrder->getSupplier->supplier_code) . '/' . $purchaseOrderItem->item_no . '/' . $purchaseOrderItem->getInventoryItemDetail->title . '/' . $purchaseOrderItem->quantity . '@' . $price;
                        $dr->save();
                    }
                }
                //vat entry start
                $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
                if ($taxVat && $taxVat->getOutputGlAccount && count($vat_amount_arr) > 0) {
                    $vat = new WaGlTran();
                    $vat->grn_type_number = $series_module->type_number;
                    $vat->transaction_type = $series_module->description;
                    $vat->transaction_no = $grn_number;
                    $vat->grn_last_used_number = $series_module->last_number_used;
                    $vat->trans_date = $dateTime;
                    $vat->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                    $vat->supplier_account_number = null;
                    $vat->account = $taxVat->getOutputGlAccount->account_code;
                    $vat->amount = array_sum($vat_amount_arr);
                    $vat->narrative = null;
                    $vat->wa_purchase_order_id = $purchaseOrder->id;
                    $vat->save();
                }
                // vat entry end 
                // cr entry start
                $cr = new WaGlTran();
                $cr->grn_type_number = $series_module->type_number;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $grn_number;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->trans_date = $dateTime;
                $cr->restaurant_id = $getLoggeduserProfile->restaurant_id;
                $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $cr->supplier_account_number = null;
                $cr->account =  @$purchaseOrder->getBranch->getAssociateCompany->good_receive->account_code;
                $cr->amount = '-' . round(array_sum($cr_amount));
                $cr->narrative = null;
                $cr->wa_purchase_order_id = $purchaseOrder->id;
                $cr->save();

                $total_cost_with_vat = array_sum($cr_amount);
                $roundOff = fmod($total_cost_with_vat, 1); //0.25
                if ($roundOff != 0) {
                    if ($roundOff > '0.50') {
                        $roundOff = round((1 - $roundOff), 2);
                        $crdrAmnt = '+' . $roundOff;
                    } else {
                        $roundOff = '-' . round($roundOff, 2);
                        $crdrAmnt = $roundOff;
                    }
                    $cr = new WaGlTran();
                    $cr->grn_type_number = $series_module->type_number;
                    $cr->transaction_type = $series_module->description;
                    $cr->transaction_no = $grn_number;
                    $cr->grn_last_used_number = $series_module->last_number_used;
                    $cr->trans_date = $dateTime;
                    $cr->restaurant_id = $getLoggeduserProfile->restaurant_id;
                    $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                    $cr->supplier_account_number = null;
                    $cr->account =  "202021";
                    $cr->amount = $crdrAmnt;
                    $cr->narrative = null;
                    $cr->wa_purchase_order_id = $purchaseOrder->id;
                    $cr->save();
                    //cr enter end
                }

                //  supp trans entry end    
                if ($allGood) {
                    $purchaseOrder->status = 'COMPLETED';
                }
                $purchaseOrder->vehicle_reg_no = $request->vehicle_reg_no;
                $purchaseOrder->receive_note_doc_no = $request->receive_note_doc_no;
                $purchaseOrder->save();
                updateUniqueNumberSeries('GRN', $grn_number);
                return true;
            });
            if (!$check) {
                throw new \Exception("Something went wrong");
            }
            Session::flash('success', 'GRN Processed Successfully');
            return redirect()->route($this->model . '.index')->withInput();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function complete(WaPurchaseOrder $order)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $order->update([
            'status' => 'COMPLETED'
        ]);

        Session::flash('success', 'Purchase order completed successfully');

        return redirect()->back();
    }
}
