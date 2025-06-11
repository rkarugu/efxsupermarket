<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaCurrencyManager;
use Illuminate\Http\Request;
use App\Models\TradeAgreement;
use App\Models\TradeAgreementDiscount;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaInventoryPriceHistory;
use App\Model\WaStockMove;
use App\Models\TradeProductOffer;
use App\Models\WaSupplierDistributor;
use App\Model\WaUserSupplier;
use App\Model\WaSupplier;
use App\Models\WaInventoryItemApprovalStatus;
use App\Models\TradeBillingPlan;
use App\Models\TradeDiscount;
use Illuminate\Validation\Rule;
use Session;
use App\Services\ApiService;
use App\Services\ExcelDownloadService;
use PDF;
use DB;
use Exception;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class TradeAgreementController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'trade-agreement';
        $this->title = 'Trade Agreement';
        $this->pmodule = 'trade-agreement';
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = [];
            $user = getLoggeduserProfile();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $assigned = WaUserSupplier::where('user_id', $user->id)->pluck('wa_supplier_id')->toArray();

            $trades = TradeAgreement::with(['supplier'])
                ->whereHas('supplier', function ($e) use ($assigned) {
                    if (!can('view-all', $this->model)) {
                        $e->whereIn('wa_suppliers.id', $assigned);
                    }
                })->where('status', ($request->status ?? 'Approved'))
                ->get();

            $locked_count = $trades->where('is_locked', true)->count();
            $unlocked_count = $trades->where('is_locked', false)->count();
            $signed_in_portal_count = $trades->where('linked_to_portal', 1)->count();
            $total_count = $trades->count();

            return view('admin.trade_agreement.index', compact(
                'title',
                'lists',
                'model',
                'breadcum',
                'pmodule',
                'permission',
                'trades',
                'locked_count',
                'unlocked_count',
                'signed_in_portal_count',
                'total_count'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function lock_agreement($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___lock']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::findOrFail($id);
            $trade->is_locked = !$trade->is_locked;
            $trade->save();

            if (request()->has('editing')) {
                return response()->json([
                    'result' => 1,
                    'message' => 'Status Updated Successfully',
                    'location' => route($model . '.edit', $trade)
                ]);
            }

            return response()->json([
                'result' => 1,
                'message' => 'Status Updated Successfully',
                'location' => route($model . '.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function create()
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $suppliers = WaSupplier::get();
            return view('admin.trade_agreement.create', compact(
                'title',
                'model',
                'breadcum',
                'pmodule',
                'permission',
                'suppliers'
            ));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'summary' => 'required|array',
                'summary.*' => 'required|string|max:250',
                'wa_supplier_id' => ['required', 'exists:wa_suppliers,id', Rule::unique('trade_agreements', 'wa_supplier_id')->where(function ($query) {
                    return $query->where('status', '!=', 'Rejected');
                })]
            ], [
                'wa_supplier_id.exists' => 'Not a valid supplier',
                'wa_supplier_id.unique' => 'Agreement already been created for the supplier',
            ], [
                'summary.*' => 'Summary',
                'wa_supplier_id' => 'Supplier',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = new TradeAgreement();
            $summary = json_encode($request->summary);
            $trade->name = 'Kanini Haraka Enterprises Limited';
            $trade->date = date('Y-m-d');
            $trade->wa_supplier_id = $request->wa_supplier_id;
            $trade->reference = "KH-TA-";
            $trade->summary = $summary;
            $trade->status = 'Approved';
            $trade->linked_to_portal = false;
            $trade->comment = "";
            $trade->save();
            $trade->linked_to_portal = 0;
            $trade->reference = $trade->reference . str_pad($trade->id, 5, '0', STR_PAD_LEFT);
            $trade->save();

            return response()->json([
                'result' => 1,
                'message' => 'Agreement created successfully',
                'location' => route($model . '.edit', $trade->id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $lists = [];
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $trade = TradeAgreement::with(['supplier'])->findOrFail($id);
            $discount_types = TradeAgreementDiscount::typeList();
            $discounts = TradeAgreementDiscount::where('trade_agreements_id', $id)->get();
            $parent = WaSupplierDistributor::where('distributors', $trade->supplier->id)->first()?->supplier_id;

            $query = WaInventoryItem::select(
                [
                    'wa_inventory_items.*',
                    "trade_product_offers.target_quantity",
                    "trade_product_offers.offer_amount",
                    "trade_product_offers.created_at as offer_date",
                    "pack_sizes.title as pack_size",
                ]
            )
                ->leftJoin('pack_sizes',  'pack_sizes.id', 'wa_inventory_items.pack_size_id')
                ->join('trade_product_offers', function ($e) use ($trade) {
                    $e->on('trade_product_offers.inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('trade_product_offers.trade_agreements_id', $trade->id);
                })
                // ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
                ->where('pack_sizes.can_order', 1)
                ->where('wa_inventory_items.status', 1)
                ->whereHas('inventory_item_suppliers', function ($e) use ($trade, $parent) {
                    $e->whereIn('wa_supplier_id', [$trade->supplier->id, $parent]);
                });

            if (request()->wantsJson()) {
                return DataTables::eloquent($query)
                    ->editColumn('created_at', function ($item) {
                        return $item->created_at->format('d/m/Y');
                    })
                    ->editColumn('offer_date', function ($item) {
                        return $item->created_at->format('d/m/Y');
                    })
                    ->editColumn('price_list_cost', function ($item) {
                        return manageAmountFormat($item->price_list_cost);
                    })
                    ->editColumn('standard_cost', function ($item) {
                        return manageAmountFormat($item->standard_cost);
                    })
                    ->addColumn('base_discount', function ($item) use ($trade) {
                        $discount = $trade->discounts()->where('discount_type', 'Base Discount')->first();
                        if (is_null($discount)) {
                            return 0;
                        }

                        $options = json_decode($discount->other_options);
                        $id = $item->id;
                        if (!isset($options->$id)) {
                            return 0;
                        }

                        if ($options->$id->type == 'Value') {
                            return "KES " . $options->$id->discount;
                        }

                        return $options->$id->discount . "%";
                    })
                    ->addColumn('invoice_discount', function ($item) use ($trade) {
                        $discount = $trade->discounts()->where('discount_type', 'Invoice Discount')->first();
                        if (is_null($discount)) {
                            return 0;
                        }
                        $options = json_decode($discount->other_options);
                        $id = $item->id;
                        if (!isset($options->$id)) {
                            return 0;
                        }

                        return $options->$id->discount . "%";
                    })
                    ->toJson();
            }

            $allProducts = WaInventoryItem::query()
                ->select([
                    'wa_inventory_items.id',
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                ])
                ->leftJoin('pack_sizes',  'pack_sizes.id', 'wa_inventory_items.pack_size_id')
                ->leftJoin('trade_product_offers', function ($e) use ($trade) {
                    $e->on('trade_product_offers.inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('trade_product_offers.trade_agreements_id', $trade->id);
                })
                ->whereHas('inventory_item_suppliers', function ($e) use ($trade, $parent) {
                    $e->whereIn('wa_supplier_id', [$trade->supplier->id, $parent]);
                })
                // ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
                ->where('pack_sizes.can_order', 1)
                ->where('wa_inventory_items.status', 1)
                ->whereNull('trade_product_offers.inventory_item_id')
                ->get();

            $locations = WaLocationAndStore::get();
            $inventory = $query->get();

            $months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

            return view('admin.trade_agreement.edit', compact(
                'title',
                'lists',
                'model',
                'breadcum',
                'pmodule',
                'allProducts',
                'permission',
                'trade',
                'discount_types',
                'discounts',
                'months',
                'locations',
                'inventory'
            ));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function retireItem($id)
    {
        try {
            $item_qoh = WaStockMove::where('wa_inventory_item_id', $id)->sum('qauntity');
            if ($item_qoh >= 0) {
                $inventory_item = WaInventoryItem::find($id);

                $new_data = [
                    '_token' => csrf_token(),
                    'status' => 0,
                    '_method' => 'PATCH',
                ];

                $inventory_item->approval_status = 'Pending Edit Approval';
                $inventory_item->save();

                $approval_status = new WaInventoryItemApprovalStatus();
                $approval_status->wa_inventory_items_id = $id;
                $approval_status->approval_by = auth()->id();
                $approval_status->status = 'Pending Edit Approval';
                $approval_status->changes = json_encode([
                    ["Status" => ["Active", "Retired"]]
                ]);
                $approval_status->new_data = json_encode($new_data);
                $approval_status->save();
            } else {
                return response()->json(['error' => 'Item has outstanding QOH and cannot be retired'], 500);
            }
            return response()->json(['message' => 'Item retired successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateItemCost(Request $request)
    {
        try {
            $items = $request->input('items', []);
            foreach ($items as $item) {
                $inventory_item = WaInventoryItem::find($item['item_id']);
                if ($inventory_item) {
                    $item['price_list_cost'] = (float) str_replace(',', '', $item['price_list_cost']);
                    if ($inventory_item->price_list_cost != $item['price_list_cost']) {
                        $old_price_list_cost = $inventory_item->price_list_cost;
                        $inventory_item->price_list_cost = $item['price_list_cost'];
                        $inventory_item->save();

                        $history = new WaInventoryPriceHistory();
                        $history->wa_inventory_item_id = $inventory_item->id;
                        $history->old_price_list_cost = $old_price_list_cost;
                        $history->price_list_cost = $item['price_list_cost'];
                        $history->standard_cost = $inventory_item->standard_cost;
                        $history->old_standard_cost = $inventory_item->standard_cost;
                        $history->weighted_cost = $inventory_item->weighted_average_cost;
                        $history->old_weighted_cost = $inventory_item->weighted_average_cost;
                        $history->selling_price = $inventory_item->selling_price;
                        $history->old_selling_price = $inventory_item->selling_price;
                        $history->initiated_by = auth()->user()->id;
                        $history->approved_by = auth()->user()->id;
                        $history->status = 'Approved';
                        $history->created_at = date('Y-m-d H:i:s');
                        $history->updated_at = date('Y-m-d H:i:s');
                        $history->block_this = False;
                        $history->save();
                    }
                }
            }
            return response()->json(['message' => 'Items updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function get_document($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::with(['supplier'])->findOrFail($id);
            return $this->document($trade);
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function get_document_trade_reference($reference)
    {
        try {
            $trade = TradeAgreement::with(['supplier'])->where('reference', $reference)->firstOrFail();
            return $this->document($trade);
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function document($trade)
    {
        $discount_types = TradeAgreementDiscount::typeList();
        $discounts = TradeAgreementDiscount::where('trade_agreements_id', $trade->id)->get();
        $pdf = PDF::loadView('admin.trade_agreement.pdf', compact(
            'trade',
            'discount_types',
            'discounts'
        ));
        return $pdf->download('Trade Agreement ' . $trade->reference . '.pdf');
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Approved,Rejected',
                'comment' => 'required|string|max:255'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::with(['supplier'])->findOrFail($id);

            $post_data = [
                'reference' => $trade->reference,
                'comment' => $request->comment,
                'status' => $request->status
            ];
            $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $a = $api->postRequest('/api/trade-agreement/request-update', $post_data);
            if (isset($a['result'])) {
                if ($a['result'] == 1) {
                    $trade->status = $request->status;
                    $trade->comment = $request->comment;
                    $trade->save();
                }
                if ($a['result'] == 0) {
                    return response()->json([
                        'result' => 0,
                        'errors' => $a['errors']
                    ]);
                }
                if ($a['result'] == -1) {
                    throw new \Exception($a['message']);
                }
            } else {
                throw new \Exception("Error Processing Request");
            }

            return response()->json([
                'result' => 1,
                'message' => 'Status Updated Successfully',
                'location' => route($model . '.edit', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function summary_update(Request $request, $id)
    {

        try {
            $validator = Validator::make($request->all(), [
                'summary' => 'required|array',
                'summary.*' => 'required|string|max:250'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::with(['supplier'])->findOrFail($id);
            $summary = json_encode($request->summary);
            $post_data = [
                'summary' => $request->summary,
                'reference' => $trade->reference
            ];
            if ($trade->linked_to_portal) {
                $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                $a = $api->postRequest('/api/trade-agreement/summary-update', $post_data);
                if (isset($a['result'])) {
                    if ($a['result'] == 1) {
                        $trade->summary = $summary;
                        $trade->save();
                    }
                    if ($a['result'] == 0) {
                        return response()->json([
                            'result' => 0,
                            'errors' => $a['errors']
                        ]);
                    }
                    if ($a['result'] == -1) {
                        throw new \Exception($a['message']);
                    }
                } else {
                    throw new \Exception("Error Processing Request");
                }
            } else {
                $trade->summary = $summary;
                $trade->save();
            }

            $trade->quarterly_cycle_start = $request->quarterly_cycle_start;
            $trade->save();

            return response()->json([
                'result' => 1,
                'message' => 'Agreement Summary Updated Successfully',
                'location' => route($model . '.edit', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function get_discount($id, Request $request)
    {
        try {
            $discount = TradeAgreementDiscount::where('trade_agreements_id', $id)->findOrFail(
                $request->discount_id
            );
            return response()->json([
                'result' => 1,
                'message' => 'Ok!',
                'data' => $discount
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function delete_discount($id)
    {
        try {
            $discount = TradeAgreementDiscount::findOrFail(
                $id
            );
            $trade = TradeAgreement::findOrFail($discount->trade_agreements_id);
            if ($trade->linked_to_portal) {
                $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                $a = $api->postRequest('/api/supplier/discount-delete/' . $trade->reference . '/' . $discount->discount_type, []);
            }
            $location = route('trade-agreement.edit', $trade->id);
            if (!$trade->linked_to_portal || (isset($a) && isset($a['result']) && $a['result'] == 1)) {
                $discount->delete();
            }
            return response()->json([
                'result' => 1,
                'message' => 'Discount Deleted Successfully',
                'location' => $location
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function store_discount($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'discount_type' => 'required|in:' . implode(",", array_keys(TradeAgreementDiscount::typeList())),
                'discount_value' => 'required_if:discount_type,No Goods Return Discount,Target discount on total value|nullable|numeric',
                'applies_to_all_item' => 'nullable|in:1',
                'selected_product_discount' => 'required_if:discount_type,End month Discount,Quarterly Discount,Invoice Discount,Base Discount,Bank Guarantee Discount|array',
                'selected_product_discount.*' => 'required_if:discount_type,End month Discount,Quarterly Discount,Invoice Discount,Base Discount,Bank Guarantee Discount',
                'selected_product_discount_type' => 'required_if:discount_type,End month Discount,Base Discount,Bank Guarantee Discount|array',
                'selected_product_discount_type.*' => 'required_if:discount_type,End month Discount,Base Discount,Bank Guarantee Discount',
                'selected_products' => 'array|required_if:discount_type,End month Discount,Quarterly Discount,Purchase Quantity Offer,Target discount on value,Target discount on quantity,Invoice Discount,Base Discount,Bank Guarantee Discount',
                'selected_products.*' => 'required_if:discount_type,End month Discount,Quarterly Discount,Purchase Quantity Offer,Target discount on value,Target discount on quantity,Invoice Discount,Base Discount,Bank Guarantee Discount',
                'selected_product_quantity' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value|array',
                'selected_product_quantity.*' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value',
                'selected_product_offer' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value|array',
                'selected_product_offer.*' => 'required_if:discount_type,Purchase Quantity Offer,Target discount on value',
                'discount_value_type' => 'required_if:discount_type,Quarterly Discount,End month Discount,Target discount on total value',
                'payment_period_discount' => 'required_if:discount_type,Payment Discount|array',
                'payment_period_discount.*' => 'required_if:discount_type,Payment Discount|numeric',
                'target_quantity' => 'required_if:discount_type,Target discount on quantity|array',
                'target_quantity.*' => 'required_if:discount_type,Target discount on quantity|numeric',
                'target_discount' => 'required_if:discount_type,Target discount on quantity|array',
                'target_discount.*' => 'required_if:discount_type,Target discount on quantity|numeric',
                'discount_target_type' => 'nullable',
                'store_location.*' => 'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage',
                'inventory_id.*' => 'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage',
                'inventory_title.*' => 'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage',
                'stock.*' => 'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage',
                // 'per_unit_discount.*'=>'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage|numeric',
                'trade_discount.*.*' => 'nullable|numeric|min:0',
                // 'percentage_of_invoice.*'=>'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage|numeric',
                // 'per_tonnage_discount_value.*'=>'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage|numeric',
                'application_stage.*' => 'required_if:discount_type,Distribution Discount,Distribution Discount on Delivery,Transport rebate per unit,Transport rebate percentage,Transport rebate per tonnage',
                'from' => 'required_if:discount_type,Performance Discount|array',
                'from.*' => 'required_if:discount_type,Performance Discount',

                'to' => 'required_if:discount_type,Performance Discount|array',
                'to.*' => 'required_if:discount_type,Performance Discount',

                'value' => 'required_if:discount_type,Performance Discount|array',
                'value.*' => 'required_if:discount_type,Performance Discount',

                'target_type' => 'required_if:discount_type,Target discount on total value|in:Monthly,Quarterly',
                'max_discount' => 'required_if:discount_type,Target discount on total value|numeric|min:1'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }

            \DB::transaction(function () use ($id, $request) {
                $trade = TradeAgreement::findOrFail($id);
                if ($trade->linked_to_portal) {
                    $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                    $a = $api->postRequest('/api/supplier/discount-store/' . $trade->reference, $request->all());
                }
                if (!$trade->linked_to_portal || (isset($a) && isset($a['result']) && $a['result'] == 1)) {
                    $discount = TradeAgreementDiscount::where('trade_agreements_id', $trade->id)->where(
                        'discount_type',
                        $request->discount_type
                    )->first();
                    if (!$discount) {
                        $discount = new TradeAgreementDiscount();
                        $discount->trade_agreements_id = $trade->id;
                        $discount->discount_type = $request->discount_type;
                    }
                    $discount->discount_value = $request->discount_value;
                    $discount->applies_to_all_item = $request->applies_to_all_item;
                    $discount->discount_value_type = $request->discount_value_type ?? 'Percentage';
                    $discount->purchased_product_quantity = $request->purchased_product_quantity;
                    $discount->free_product_quantity = $request->free_product_quantity;
                    $other_options = "";
                    if (in_array($request->discount_type, ['End month Discount', 'Quarterly Discount', 'Base Discount', 'Invoice Discount', 'Bank Guarantee Discount'])) {
                        $other_options = [];
                        if ($request->selected_products && count($request->selected_products) > 0) {
                            foreach ($request->selected_products as $key => $selected_product) {
                                if ($request->selected_product_discount[$key] > 0) {
                                    $a = ['stock_id' => $request->selected_products[$key], 'discount' => $request->selected_product_discount[$key]];
                                    if (in_array($request->discount_type, ['End month Discount', 'Base Discount', 'Bank Guarantee Discount'])) {
                                        $a['type'] = $request->selected_product_discount_type[$key];
                                    }
                                    $other_options[$key] = (object)$a;
                                }
                            }
                        }
                        $discount->discount_value = 0;
                    }

                    if ($request->discount_type == 'Purchase Quantity Offer' || $request->discount_type == 'Target discount on value') {
                        $other_options = [];
                        if ($request->selected_product_quantity && count($request->selected_product_quantity) > 0) {
                            foreach ($request->selected_product_quantity as $key => $selected_product) {
                                $other_options[$key] = (object)['stock_id' => $request->selected_products[$key], 'free_stock' => (float)$request->selected_product_offer[$key], 'purchase_quantity' => (float)$selected_product];
                            }
                            $discount->purchased_product_quantity = 0;
                            $discount->free_product_quantity = 0;
                        }
                    }
                    if ($request->discount_type == 'Target discount on total value') {
                        $other_options = (object)[
                            'target_type' => $request->target_type,
                            'max_discount' => $request->max_discount
                        ];
                    }
                    if ($request->discount_type == 'Payment Discount') {
                        $other_options = array_map(function ($item) {
                            return (float)$item;
                        }, $request->payment_period_discount);
                    }
                    if ($request->discount_type == 'Performance Discount') {
                        $other_options = [];
                        foreach ($request->from as $key => $value) {
                            $other_options[] = (object)[
                                'from' => $value,
                                'to' => $request->to[$key],
                                'value' => $request->value[$key],
                            ];
                        }
                    }
                    if ($request->discount_type == 'Target discount on quantity') {
                        $other_options = [];
                        foreach ($request->target_quantity as $key => $quantity) {
                            $other_options[$key] = (object)[
                                'stock_id' => $request->selected_products[$key],
                                'quantity' => $quantity,
                                'discount' => (float)$request->target_discount[$key]
                            ];
                        }
                    }
                    if (in_array($request->discount_type, ['Distribution Discount', 'Distribution Discount on Delivery', 'Transport rebate per unit', 'Transport rebate percentage', 'Transport rebate per tonnage'])) {
                        $other_options = [
                            'discount_target_type' => $request->discount_target_type
                        ];
                        foreach ($request->store_location as $key => $store_location) {
                            $discounts = [];
                            foreach ($request->trade_discount[$store_location] as $k => $dis) {
                                if ($dis > 0) {
                                    $discounts[] = (object)[
                                        'discount' => $request->trade_discount[$store_location][$k],
                                        'stock' => $request->stock[$store_location][$k],
                                        'inventory_id' => $request->inventory_id[$store_location][$k],
                                        'inventory_title' => $request->inventory_title[$store_location][$k]
                                    ];
                                }
                            }
                            $other_options['location_discounts'][] = (object)[
                                'location' => $store_location,
                                'discount' => $discounts,
                                'application_stage' => $request->application_stage[$key],

                            ];
                        }
                    }
                    $discount->other_options = json_encode($other_options);
                    $discount->save();
                } else {
                    throw new \Exception("Error Processing Request");
                }
            });
            return response()->json([
                'result' => 1,
                'message' => 'Discount Stored and Sent to portal Successfully',
                'data' => [],
                'location' => route($model . '.edit', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function store_offer_amount($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stock_id_code' => 'required|string|max:255',
                'inventory_item_id' => 'required|numeric|min:0|exists:wa_inventory_items,id',
                'offer_amount' => 'min:0|required|numeric',
                'target_quantity' => 'min:0|required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }

            \DB::transaction(function () use ($id, $request) {
                $trade = TradeAgreement::findOrFail($id);
                if ($trade->linked_to_portal) {
                    $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                    $a = $api->postRequest('/api/supplier/trade-offer-store/' . $trade->reference, $request->all());
                }
                if (!$trade->linked_to_portal || (isset($a) && isset($a['result']) && $a['result'] == 1)) {
                    $offer = TradeProductOffer::where('trade_agreements_id', $trade->id)->where(
                        'stock_id_code',
                        $request->stock_id_code
                    )->first();
                    if (!$offer) {
                        $offer = new TradeProductOffer();
                        $offer->trade_agreements_id = $trade->id;
                        $offer->stock_id_code = $request->stock_id_code;
                    }
                    $offer->inventory_item_id = $request->inventory_item_id;
                    $offer->offer_amount = $request->offer_amount;
                    $offer->target_quantity = $request->target_quantity;
                    $offer->save();

                    $checkSupplier = WaInventoryItemSupplier::where('wa_supplier_id', $trade->wa_supplier_id)->where('wa_inventory_item_id', $request->inventory_item_id)->first();
                    if (!$checkSupplier) {
                        $supplierData = new WaInventoryItemSupplier();
                        $supplierData->wa_inventory_item_id = $request->inventory_item_id;
                        $supplierData->wa_supplier_id = $trade->wa_supplier_id;
                        $supplierData->save();
                    }
                } else {
                    throw new \Exception("Error Processing Request");
                }
            });

            return response()->json([
                'result' => 1,
                'message' => 'Offer Stored and Sent to portal Successfully',
                'data' => [],
                'location' => route($model . '.edit', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function store_all_offer_amount($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'offer_amount' => 'min:0|required|numeric',
                'target_quantity' => 'min:0|required|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors(), 'result' => 0]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }

            \DB::transaction(function () use ($id, $request) {
                $trade = TradeAgreement::findOrFail($id);
                $items = WaInventoryItem::leftJoin('pack_sizes',  'pack_sizes.id', 'wa_inventory_items.pack_size_id')
                    ->where('pack_sizes.can_order', 1)
                    // ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
                    ->whereHas('inventory_item_suppliers', function ($e) use ($trade) {
                        $e->where('wa_supplier_id', $trade->supplier->id);
                    })->select('wa_inventory_items.*')->get();
                // if($trade->linked_to_portal){
                //     $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                //     $a = $api->postRequest('/api/supplier/trade-offer-store/'.$trade->reference, $request->all());
                // }
                // if(!$trade->linked_to_portal || (isset($a) && isset($a['result']) && $a['result'] == 1)){
                foreach ($items as $key => $item) {
                    $offer = TradeProductOffer::where('trade_agreements_id', $trade->id)->where(
                        'stock_id_code',
                        $item->stock_id_code
                    )->first();
                    if (!$offer) {
                        $offer = new TradeProductOffer();
                        $offer->trade_agreements_id = $trade->id;
                        $offer->stock_id_code = $item->stock_id_code;
                    }
                    $offer->inventory_item_id = $item->id;
                    $offer->offer_amount = $request->offer_amount;
                    $offer->target_quantity = $request->target_quantity;
                    $offer->save();
                }
                // }
                // else{
                //     throw new \Exception("Error Processing Request");
                // }
            });

            return response()->json([
                'result' => 1,
                'message' => 'Offer Stored and Sent to portal Successfully',
                'data' => [],
                'location' => route($model . '.edit', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function subscription_charges($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $trade = TradeAgreement::with(['supplier'])->findOrFail($id);
            $plans = TradeBillingPlan::where('trade_agreement_id', $trade->id)->get();
            $plan_types = TradeBillingPlan::plans();
            $currencies = WaCurrencyManager::get();
            return view('admin.trade_agreement.subscription_charges', compact(
                'title',
                'model',
                'breadcum',
                'pmodule',
                'permission',
                'trade',
                'plans',
                'plan_types',
                'currencies'
            ));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function store_subscription_charges(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'monthly_charge' => 'required|min:0|max:999999|numeric',
                'yearly_charge' => 'required|min:0|numeric',
                'currency_id' => 'required|exists:wa_currency_managers,id'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ]);
            }
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::findOrFail($id);

            $monthly = TradeBillingPlan::where('billing_period', 'Monthly')->where('trade_agreement_id', $trade->id)->first();
            if (!$monthly) {
                $monthly = new TradeBillingPlan();
                $monthly->trade_agreement_id = $trade->id;
                $monthly->title = 'Monthly Plan';
                $monthly->billing_period = 'Monthly';
            }
            $monthly->charges = $request->monthly_charge;
            $monthly->wa_currency_manager_id = $request->currency_id;
            $monthly->save();


            $yearly = TradeBillingPlan::where('billing_period', 'Yearly')->where('trade_agreement_id', $trade->id)->first();
            if (!$yearly) {
                $yearly = new TradeBillingPlan();
                $yearly->trade_agreement_id = $trade->id;
                $yearly->title = 'Yearly Plan';
                $yearly->billing_period = 'Yearly';
            }
            $yearly->charges = $request->yearly_charge;
            $yearly->wa_currency_manager_id = $request->currency_id;
            $yearly->save();
            $trade = TradeAgreement::with(['billing_charges.currency'])->findOrFail($id);
            if ($trade->linked_to_portal) {
                $post_data = [
                    'trade' => $trade,
                    'reference' => $trade->reference,
                ];
                $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
                $api->postRequest('/api/supplier/store-subscription-charges', $post_data);
            }
            return response()->json([
                'result' => 1,
                'message' => 'Charges Updated Successfully',
                'location' => route($model . '.subscription_charges', $id)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result' => -1,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function email_subscribers($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $model = $this->model;
            $title = 'Supplier Emails';
            if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
                throw new \Exception("Don't have enough permission to view this page");
            }
            $trade = TradeAgreement::with('supplier')->findOrFail($id);
            throw_if(!$trade->linked_to_portal, "Trade Agreement not linked yet");
            $post_data = [
                'supplier_code' => $trade->supplier->supplier_code,
                'supplier_email' => $trade->supplier->email,
            ];
            $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $data = $api->postRequest('/api/email-subscriber', $post_data)['data'];
            return view('admin.trade_agreement.email_subscribers', compact(
                'title',
                'model',
                'pmodule',
                'permission',
                'data',
                'trade'
            ));
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        DB::beginTransaction();

        try {
            $trade = TradeAgreement::findOrFail($id);

            if ($trade->linked_to_portal) {
                throw new Exception('This trade agreement cannot be deleted');
            }

            $trade->discounts()->delete();
            $trade->offers()->delete();
            $trade->delete();

            DB::commit();

            Session::flash('success', 'Deleted successfully.');

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();

            Session::flash('warning', $e->getMessage());

            return redirect()->back();
        }
    }
}
