<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LpoStatusReportExport;
use App\Mail\LpoApproved;
use App\Model\WaSupplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Model\WaPurchaseOrder;
use App\Model\Restaurant;
use App\Vehicle;
use App\Model\WaPurchaseOrderItem;
use App\Model\User;
use App\Models\WaSupplierDistributor;
use App\Model\WaDepartment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use App\Rules\PurchaseOrder\MaxStockValidator;
use PDF;
use DB;
use Illuminate\Support\FacadesDB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\WaInventoryLocationUom;
use App\Model\WaLocationAndStore;
use App\Model\WaUserSupplier;
use App\Models\TradeAgreement;
use App\Rules\PurchaseOrder\ContactsValidation;
use App\Rules\PurchaseOrder\PriceListValidator;
use App\Rules\PurchaseOrder\TradeAgreementValidator;
use Exception;
use Illuminate\Support\Facades\DB as SupportFacadesDB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseOrderController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    protected $email_template = 'place_lpo';

    public function __construct()
    {
        $this->model = 'purchase-orders';
        $this->title = 'Purchase Orders';
        $this->pmodule = 'purchase-orders';
    }

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $email_template = EmailTemplate::templateList()[$this->email_template];
        $template = EmailTemplate::where('name', $email_template->name)->first();

        return view('admin.purchaseorders.index', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                'Purchase Orders' => route('purchase-orders.index'),
            ],
            'template' => $template
        ]);
    }

    public function orders()
    {
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : '';
        $to = request()->filled('to') . ' 23:59:59' ? request()->to : '';

        $amountSub = WaPurchaseOrderItem::query()
            ->select([
                SupportFacadesDB::raw('SUM(total_cost_with_vat) - SUM(other_discounts_total)')
            ])
            ->whereColumn('wa_purchase_order_items.wa_purchase_order_id', 'wa_purchase_orders.id');

        $query = WaPurchaseOrder::query()
            ->select([
                'wa_purchase_orders.id',
                'wa_purchase_orders.type',
                'wa_purchase_orders.purchase_no',
                'wa_purchase_orders.status',
                'wa_purchase_orders.lpo_type',
                'wa_purchase_orders.purchase_date',
                'wa_purchase_orders.slug',
                'wa_purchase_orders.wa_supplier_id',
                'wa_purchase_orders.restaurant_id',
                'wa_purchase_orders.sent_to_supplier',
                'wa_purchase_orders.slot_booked',
                'wa_purchase_orders.goods_released',
                'wa_purchase_orders.is_hide',
                'wa_purchase_orders.wa_location_and_store_id',
                'wa_purchase_orders.supplier_accepted',
                'employees.name as employee_name',
            ])
            ->selectSub($amountSub, 'total_amount')
            ->with([
                'supplier.users',
                'storeLocation'
            ])
            ->join('users as employees', 'employees.id', 'wa_purchase_orders.user_id')
            ->when(request()->status == 'pending', function ($query) {
                $query->where('is_hide', 'No')
                    ->whereNotIn('wa_purchase_orders.status', ['PRELPO', 'COMPLETED'])
                    ->doesntHave('grns');
            })
            ->when(request()->status == 'archived', function ($query) {
                $query->where('is_hide', 'Yes');
            })
            ->when(request()->status == 'completed', function ($query) {
                $query->where('wa_purchase_orders.status', 'COMPLETED');
            })
            ->when(request()->branch, function ($query) {
                $query->where('restaurant_id', request()->branch);
            })
            ->when(request()->store, function ($query) {
                $query->where('wa_purchase_orders.wa_location_and_store_id', request()->store);
            })
            ->when(request()->supplier, function ($query) {
                $query->where('wa_supplier_id', request()->supplier);
            })
            ->when(!can('view-all', $this->model), function ($query) {
                $userSuppliers = WaUserSupplier::where('user_id', auth()->user()->id)
                    ->pluck('wa_supplier_id')->toArray();
                $query->whereIn('wa_supplier_id', $userSuppliers);
            })
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('wa_purchase_orders.created_at', [$from, $to]);
            });

        return DataTables::eloquent($query)
            ->editColumn('total_amount', function ($order) {
                return manageAmountFormat($order->total_amount);
            })
            ->editColumn('status',  function ($order) {
                return view('admin.purchaseorders.status', compact('order'));
            })
            ->addColumn('actions',  function ($order) {
                $model = $this->model;
                return view('admin.purchaseorders.actions', compact('order', 'model'));
            })
            ->toJson();
    }

    public function viewLastPurchasesPrice(Request $request)
    {
        $item_id = $request->item_id;

        $grn_data = [];
        if ($item_id) {
            $item_row = WaInventoryItem::where('id', $item_id)->first();
            $item_code = $item_row->stock_id_code;
            if ($item_code) {
                $grn_data = \App\Model\WaGrn::where('item_code', $item_code)
                    ->orderBy('id', 'desc')
                    ->limit(3)
                    ->get();
            }
        }

        $view_data = view('admin.purchaseorders.last_prices', compact('grn_data'));
        return $view_data;
    }

    public function hidepurchaseorder($slug)
    {
        $row = WaPurchaseOrder::whereSlug($slug)->update(['is_hide' => 'Yes']);
        if ($row) {
            Session::flash('success', 'Unwanted purchase order hide successfully.');
            return redirect()->back();
        }
    }

    public function create()
    {
        $getLoggeduserProfile = getLoggeduserProfile();
        if ($getLoggeduserProfile->wa_department_id && $getLoggeduserProfile->restaurant_id) {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
                $title = 'Add ' . $this->title;
                $model = $this->model;
                $employees = User::where('role_id', '!=', 1)->get();
                $vehicles = Vehicle::get();
                $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
                return view('admin.purchaseorders.create', compact('title', 'model', 'breadcum', 'employees', 'vehicles'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } else {
            Session::flash('warning', 'Please update your branch and department');
            return redirect()->back();
        }
    }


    public function checkSupplierType(Request $request)
    {
        $supplierId = $request->supplier_id;
        $supplier = WaSupplierDistributor::where('distributors', $supplierId)->where('status', '1')->first();


        if ($supplier) {
            $mainSupplier = DB::table('wa_suppliers')
                ->where('supplier_code', $supplier->supplier_id)
                ->first();


            if ($supplier->distributors) {
                $mainSupplierName = $mainSupplier ? $mainSupplier->name : '';
                $mainSupplierId = $mainSupplier ? $mainSupplier->id : '';

                return response()->json([
                    'isDistributor' => true,
                    'mainSupplierName' => $mainSupplierName,
                    'mainSupplierId' => $mainSupplierId,
                ]);
            } else {

                $supplierName = $supplier->name;

                return response()->json([
                    'isDistributor' => false,
                    'supplierName' => $supplierName,
                ]);
            }
        } else {

            $mainSupplierFail = DB::table('wa_suppliers')
                ->where('id', $supplierId)
                ->first();

            $mainSupplierFail = $mainSupplierFail ? $mainSupplierFail->name : '';

            return response()->json(['mainSupplierNamefail' => $mainSupplierFail]);
        }
    }



    public function store(Request $request)
    {
        if (!$request->ajax()) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        //   echo "<pre>"; print_r($request->all()); die;
        try {
            $validator = Validator::make($request->all(), [
                // 'purchase_no'=>'required|unique:wa_purchase_orders,purchase_no',
                'purchase_date' => 'required|date',
                'supplier_own' => 'required',
                'wa_supplier_id' => ['required', 'exists:wa_suppliers,id', new TradeAgreementValidator, new ContactsValidation],
                'vehicle_id' => 'required_if:supplier_own,=,OwnCollection',
                'employee_id' => 'required_if:supplier_own,=,OwnCollection',
                'store_location_id' => 'required|exists:wa_location_and_stores,id',
                // 'wa_priority_level_id'=>'required|exists:wa_priority_level,id',
                // 'wa_unit_of_measures_id'=>'required',
                'item_id' => 'required|array',
                'item_quantity.*' => ['required', 'numeric', 'min:1', new MaxStockValidator],
                'item_standard_cost.*' => ['required', 'numeric', 'min:1', new PriceListValidator],
                'item_vat.*' => 'nullable|exists:tax_managers,id',
                'item_discount_per.*' => 'nullable|numeric|min:0|max:100',
                'free_qualified_stock.*' => 'nullable|numeric|min:0|max:999999',
                'invoice_discount_per' => 'nullable|numeric|min:0|max:100',
                'invoice_discount' => 'nullable|numeric|min:0|max:999999',
                'transport_rebate_discount_value' => 'nullable|numeric|min:0',
                'invoice_percentage.*' => 'nullable|numeric|min:0',
                'distribution_discount.*' => 'nullable|numeric|min:0',
                'transport_rebate_per_unit.*' => 'nullable|numeric|min:0',
                'transport_rebate_percentage.*' => 'nullable|numeric|min:0',
                'transport_rebate_per_tonnage.*' => 'nullable|numeric|min:0',
                'lpo_type' => 'required_without:advance_payment|string|in:Bulk,Normal',
                'transport_rebate_discount' => 'nullable|numeric|min:0|max:999999',
                'transport_rebate_discount_type' => 'nullable|string|in:per_unit,invoice_amount,per_tonnage',
            ], [
                "vehicle_id" => 'Vehicle is required',
                "employee_id" => 'Employee is required',
                "supplier_own" => 'select delivery method',
                "lpo_type" => 'Select an LPO type'
            ], [
                'item_quantity.*' => 'Item Quantity',
                // 'item_standard_cost.*'=>'Price',
                'item_vat.*' => 'Vat',
                'item_discount_per.*' => 'Discount',
                'item_standard_cost.*' => 'Standard Cost',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getLoggeduserProfile = getLoggeduserProfile();

            $inventory = WaInventoryItem::select([
                'wa_inventory_items.*',
                'wa_inventory_location_stock_status.max_stock as max_stock_f',
                'wa_inventory_location_stock_status.re_order_level',
                'wa_inventory_item_supplier_data.price as new_standard_cost',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . $getLoggeduserProfile->wa_location_and_store_id . ') as quantity')
            ])
                ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($request) {
                    $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $request->store_location_id);
                    // ->where('wa_inventory_location_stock_status.wa_location_and_stores_id',DB::RAW('wa_inventory_items.store_location_id'));
                })
                ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($request) {
                    $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('wa_inventory_item_supplier_data.wa_supplier_id', $request->wa_supplier_id);
                })
                ->with(['getUnitOfMeausureDetail'])->whereIn('wa_inventory_items.id', $request->item_id)->groupBy('wa_inventory_items.id')->get();

            $errors = [];
            if (count($inventory) == 0) {
                $errors['testIn'] = ['Add items to proceed'];
            } else {
                foreach ($inventory as $key => $val) {
                    if ($val->max_stock_f < $request->item_quantity[$val->id]) {
                        // $errors['item_id.'.$val->id] = ['Qty cannot be greater than the Max Stock'];
                    }
                    if (empty($val->re_order_level)) {
                        // $errors['item_id.' . $val->id] = ['Re-Order Level is mandatory'];
                    }
                    if (empty($val->new_standard_cost)) {
                        //                        $errors['item_id.' . $val->id] = ['Supplier price is not available!'];
                    }
                }
            }
            if (count($errors) > 0) {
                return response()->json(['result' => 0, 'errors' => $errors], 500);
            }
            $check = DB::transaction(function () use ($inventory, $request, $getLoggeduserProfile) {
                $location = WaLocationAndStore::findOrFail($request->store_location_id);
                $row = new WaPurchaseOrder();
                $request->purchase_no = getCodeWithNumberSeries('PURCHASE ORDERS');
                $row->purchase_no = $request->purchase_no;
                $row->restaurant_id = $location->wa_branch_id;
                $row->wa_location_and_store_id = $location->id;
                $row->wa_department_id = $getLoggeduserProfile->wa_department_id;
                $row->user_id = $getLoggeduserProfile->id;
                $row->wa_unit_of_measures_id = $getLoggeduserProfile->wa_unit_of_measures_id;
                $row->purchase_date = $request->purchase_date;
                $row->advance_payment = $request->boolean('advance_payment');
                $row->lpo_type = $row->advance_payment ? 'Advanced' : $request->lpo_type;
                $row->wa_priority_level_id = $request->wa_priority_level_id ?? NULL;
                $row->note = $request->note ?? "";
                $row->transport_rebate_discount = $request->transport_rebate_discount;
                $row->transport_rebate_discount_value = $request->transport_rebate_discount_value;
                $row->transport_rebate_discount_type = $request->transport_rebate_discount_type;
                $row->wa_supplier_id = $request->wa_supplier_id;
                $row->invoice_discount_per = $request->invoice_discount_per;
                $row->invoice_discount = $request->invoice_discount;
                $row->supplier_own = $request->supplier_own;
                if ($request->supplier_own == "OwnCollection") {
                    $row->vehicle_id = $request->vehicle_id;
                    $row->employee_id = $request->employee_id;
                }
                $row->status = 'DRAFT';
                $row->save();
                $items = [];
                foreach ($inventory as $key => $val) {
                    $cost = $request->item_standard_cost[$val->id];

                    $item = [];
                    $item['wa_purchase_order_id'] = $row->id;
                    $item['wa_inventory_item_id'] = $val->id;
                    $item['quantity'] = $request->item_quantity[$val->id];
                    $item['free_qualified_stock'] = $request->free_qualified_stock[$val->id];
                    $item['note'] = "";
                    $item['prev_standard_cost'] = $val->prev_standard_cost;
                    $item['selling_price'] = $val->selling_price;
                    $item['order_price'] = $cost;
                    //                    $item['order_price'] = $val->new_standard_cost;
                    $item['supplier_uom_id'] = @$getLoggeduserProfile->wa_unit_of_measures_id;
                    $item['pack_size_id'] = $val->pack_size_id;
                    $item['supplier_quantity'] = $request->item_quantity[$val->id];
                    $item['unit_conversion'] = 1;
                    $item['item_no'] = $val->stock_id_code;
                    $item['is_exclusive_vat'] = isset($request->item_vat[$val->id]) ? 'Yes' : 'No'; //its in reverse order
                    $check_uom = \App\Model\WaInventoryLocationUom::where(
                        [
                            'inventory_id' => $val->id,
                            'location_id' => $row->wa_location_and_store_id
                        ]
                    )->first();
                    $item['unit_of_measure'] = @$check_uom->uom_id;
                    $item['standard_cost'] = $cost;
                    //                    $item['standard_cost'] = $val->new_standard_cost;
                    $item['store_location_id'] = $val->store_location_id;
                    $item['total_cost'] = $cost * $request->item_quantity[$val->id];
                    if (@$request->item_discount_type[$val->id] == 'Value') {
                        $item['discount_amount'] = ($request->item_discount_per[$val->id]) ? ($item['quantity'] * $request->item_discount_per[$val->id]) : 0;
                    } else {
                        $item['discount_amount'] = ($request->item_discount_per[$val->id]) ? (($item['total_cost'] * $request->item_discount_per[$val->id]) / 100) : 0;
                    }
                    $item['discount_percentage'] = $request->item_discount_per[$val->id];
                    $item['total_cost'] = $item['total_cost'] - $item['discount_amount'];
                    $item['tax_manager_id'] = $request->item_vat[$val->id] ?? NULL;
                    $item['vat_rate'] = $request->item_vat_percentage[$val->id];
                    $item['vat_amount'] = ($request->item_vat_percentage[$val->id]) ? ($item['total_cost'] - ($item['total_cost'] * 100) / ($request->item_vat_percentage[$val->id] + 100)) : 0;
                    $item['total_cost'] = $item['total_cost'] - $item['vat_amount'];
                    $total_cost_with_vat = $item['total_cost'] + $item['vat_amount'];
                    $roundOff = fmod($total_cost_with_vat, 1); //0.25
                    if ($roundOff != 0) {
                        if ($roundOff > '0.50') {
                            $roundOff = round((1 - $roundOff), 2);
                        } else {
                            $roundOff = '-' . round($roundOff, 2);
                        }
                    }
                    $item['round_off'] = $roundOff;
                    $item['total_cost_with_vat'] = round($total_cost_with_vat);
                    $item['created_at'] = date('Y-m-d H:i:s');
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    $item['discount_settings'] = json_encode([
                        'invoice_percentage' => @$request->invoice_percentage[$val->id],
                        'base_discount_type' => @$request->item_discount_type[$val->id],
                        'distribution_discount' => @$request->distribution_discount[$val->id],
                        'transport_rebate_per_unit' => @$request->transport_rebate_per_unit[$val->id],
                        'transport_rebate_percentage' => @$request->transport_rebate_percentage[$val->id],
                        'transport_rebate_per_tonnage' => @$request->transport_rebate_per_tonnage[$val->id]
                    ]);

                    $item['other_discounts_total'] = $this->getTotalDiscount($cost, $request->item_quantity[$val->id], (object) $item);

                    $items[] = $item;
                }
                WaPurchaseOrderItem::insert($items);
                if ($request->action == 'process') {
                    addPurchaseOrderPermissions($row->id, $row->wa_department_id);
                }

                updateUniqueNumberSeries('PURCHASE ORDERS', $request->purchase_no);
                return true;
            });
            if ($check) {
                return response()->json(['result' => 1, 'message' => 'Purchase order added successfully and Request sent successfully.', 'location' => route('purchase-orders.index')], 200);
            }
            return response()->json(['result' => -1, 'message' => 'Something went wrong'], 500);
        } catch (\Exception $e) {
            // Log the full exception details
            $msg = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = $e->getTraceAsString();
            
            // Log the error details to a custom file for easier access
            $logMessage = "\nError: {$msg}\nFile: {$file}\nLine: {$line}\nTrace: {$trace}\n";
            file_put_contents(public_path('purchase_order_error.log'), $logMessage, FILE_APPEND);
            
            return response()->json(['result' => -1, 'error' => $msg, 'file' => $file, 'line' => $line], 500);
        }
    }

    private function getTotalDiscount($price, $qty, $orderItem)
    {
        $baseDiscount = 0;
        $invoice_discount = 0;
        $transport_rebate = 0;
        $distribution_discount = 0;

        $settings = json_decode($orderItem->discount_settings);
        if (!$settings) {
            return 0;
        }

        if (isset($settings->base_discount_type)) {
            $baseDiscount = ($settings->base_discount_type == 'Value' ? $orderItem->discount_percentage * $qty : ($price * $orderItem->discount_percentage / 100) * $qty);
        }

        $invoiceAmount = $price * $qty -  $baseDiscount;
        $inv_percentage = (float) (isset($settings->invoice_percentage) ? $settings->invoice_percentage : 0);
        $invoice_discount += ($invoiceAmount * $inv_percentage) / 100;
        $transport_rebate_per_unit = (float) isset($settings->transport_rebate_per_unit) ? $settings->transport_rebate_per_unit : 0;
        $transport_rebate_percentage = (float) isset($settings->transport_rebate_percentage) ? $settings->transport_rebate_percentage : 0;
        $transport_rebate_per_tonnage = (float) isset($settings->transport_rebate_per_tonnage) ? $settings->transport_rebate_per_tonnage : 0;
        $distribution_discount = (float) isset($settings->distribution_discount) ? $settings->distribution_discount * $qty : 0;
        if ($transport_rebate_per_unit > 0) {
            $transport_rebate += $transport_rebate_per_unit * $qty;
        } elseif ($transport_rebate_percentage > 0) {
            $transport_rebate += ($invoiceAmount * $transport_rebate_percentage) / 100;
        } elseif ($transport_rebate_per_tonnage > 0) {
            $transport_rebate += $transport_rebate_per_tonnage * $orderItem->measure;
        }

        // We do not include the base discount since it has already been 
        // deducted from the total amount
        return $invoice_discount + $transport_rebate + $distribution_discount;
    }

    public function status_report(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'purchase-order-status';
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = \App\Model\WaPurchaseOrder::with(['getrelatedEmployee', 'getStoreLocation', 'getSupplier', 'getDepartment', 'getRelatedItem.getInventoryItemDetail'])->where(function ($e) use ($request) {
                if ($request->date_from) {
                    $e->whereBetween('created_at', [$request->date_from, $request->date_to]);
                }
                if ($request->project) {
                    $e->where('project_id', $request->project);
                }
                if ($request->purchase_no) {
                    $e->where('purchase_no', 'LIKE', $request->purchase_no);
                }
            })->orderBy('id', 'desc');
            if (($request->manage == 'pdf') || ($request->manage == 'excel')) {
                $lists = $lists->get();
            } else {
                $lists = $lists->paginate(20);
            }

            $projects = \App\Model\Projects::get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];

            if ($request->manage == 'pdf') {
                $pdf = \PDF::loadView('admin.purchaseorders.pdfpurchaseReport', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'projects'));
                return $pdf->download('purchaseorders-report-' . date('Y-m-d-H-i-s') . '.pdf');
            }

            if ($request->manage == 'excel') {
                $data = $lists->map(function ($record) {
                    $tonnage = 0;
                    foreach ($record->getRelatedItem as $getRelatedItem) {
                        $tonnage += ($getRelatedItem->getInventoryItemDetail?->net_weight ?? 0) * $getRelatedItem->quantity;
                    }

                    return [
                        'purchase_no' => $record->purchase_no,
                        'purchase_date' => Carbon::parse($record->purchase_date)->toFormattedDayDateString(),
                        'branch' => $record->getBranch?->name,
                        'user' => $record->getrelatedEmployee?->name,
                        'supplier' => $record->getSupplier?->name,
                        'note' => $record->note,
                        'lists' => count($record->getRelatedItem),
                        'status' => $record->status,
                        'grn' => $record->getGrnNo?->grn_number,
                        'tonnage' => round($tonnage, 2),
                        'amount' => number_format(($record->getRelatedItem?->sum('total_cost_with_vat') ?? 0), 2),
                    ];
                });

                $export = new LpoStatusReportExport(collect($data));
                $now = Carbon::now()->toDayDateTimeString();
                return Excel::download($export, "LPO STATUS REPORT $now.xlsx");
            }

            return view('admin.purchaseorders.status_report', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission', 'projects'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function sendRequisitionRequest($purchase_no)
    {

        try {

            $row = WaPurchaseOrder::where('status', 'UNAPPROVED')->where('purchase_no', $purchase_no)->first();
            if ($row) {
                $row->status = 'PENDING';
                $row->save();
                addPurchaseOrderPermissions($row->id, $row->wa_department_id);
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

        $row = WaPurchaseOrder::whereSlug($slug)->first();
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            return view('admin.purchaseorders.show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function print(Request $request)
    {
        // No permission checks for printing approved LPOs
        $slug = $request->slug;
        $title = 'Print ' . $this->title;
        $model = $this->model;
        
        // Get the LPO
        $row = WaPurchaseOrder::with(['getBranch', 'getRelatedItem.pack_size', 'getRelatedItem.getInventoryItemDetail'])->whereSlug($slug)->first();
        
        // Check if LPO exists
        if (!$row) {
            Session::flash('warning', 'LPO not found');
            return redirect()->back();
        }
        
        // Generate QR code
        $qr_code = QrCode::generate(
            $row->purchase_date . " - " . $row->purchase_no . " - " . @$row->getBranch->name . " - " . manageAmountFormat(@$row->getRelatedItem->sum('total_cost_with_vat')),
        );
        
        $settings = getAllSettings();
        $breadcum = [$this->title => route($model . '.index'), 'Print' => ''];
        
        // Log the print action
        \Illuminate\Support\Facades\Log::info('Printing LPO: ' . $row->purchase_no);
        
        return view('admin.purchaseorders.print', compact('title', 'model', 'breadcum', 'row', 'qr_code', 'settings'));
    }

    public function exportToPdf($slug)
    {
        // Increase PHP execution time limit to prevent timeouts during PDF generation
        ini_set('max_execution_time', 120); // 2 minutes
        
        // No permission checks for exporting approved LPOs
        $title = 'Export ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Export' => ''];
        
        try {
            // Get the LPO with minimal relations to improve performance
            $row = WaPurchaseOrder::with([
                'getBranch:id,name', 
                'getRelatedItem' => function($query) {
                    $query->select('id', 'wa_purchase_order_id', 'wa_inventory_item_id', 'quantity', 'order_price', 'total_cost', 'vat_rate', 'vat_amount', 'total_cost_with_vat', 'supplier_quantity');
                },
                'getRelatedItem.pack_size:id,title',
                'getRelatedItem.getInventoryItemDetail:id,title,stock_id_code'
            ])->whereSlug($slug)->first();
            
            // Check if LPO exists
            if (!$row) {
                Session::flash('warning', 'LPO not found');
                return redirect()->back();
            }
            
            // Generate simpler QR code with less data
            $qr_code = QrCode::format('svg')->size(100)->generate(
                $row->purchase_no . " - " . $row->purchase_date
            );
            
            // Log the export action
            \Illuminate\Support\Facades\Log::info('Starting PDF export for LPO: ' . $row->purchase_no);
            
            $pdf_d = true;
            $settings = getAllSettings();
            
            // Use optimized PDF generation options
            $pdf = PDF::loadView('admin.purchaseorders.print', compact(
                'title',
                'model',
                'breadcum',
                'row',
                'qr_code',
                'pdf_d',
                'settings'
            ))
            ->set_option("enable_php", true)
            ->set_option("isHtml5ParserEnabled", true)
            ->set_option("isRemoteEnabled", false); // Disable remote image fetching for performance
    
            $report_name = 'purchase_order_' . date('Y_m_d_H_i_A');
    
            // Force download instead of stream to avoid browser rendering issues
            \Illuminate\Support\Facades\Log::info('Completed PDF generation for LPO: ' . $row->purchase_no);
            return $pdf->download($report_name . '.pdf');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PDF generation failed: ' . $e->getMessage());
            Session::flash('warning', 'PDF generation failed. Please try again or contact support.');
            return redirect()->back();
        }
    }


    public function edit($slug)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $order = WaPurchaseOrder::with('getRelatedItem')->whereSlug($slug)->first();

        if ($order->status == 'COMPLETED' && !auth()->user()->isAdministrator()) {
            return redirect()->back()->withErrors(['errors' => 'This order can not be edited']);
        }

        try {

            if ($order) {
                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                $model = $this->model;
                $employees = User::where('role_id', '!=', 1)->get();
                $vehicles = Vehicle::get();
                $suppliers = WaSupplier::get()->pluck('name', 'id')->toArray();
                $items = [];
                foreach ($order->getRelatedItem as $inventory) {
                    $items[] = $this->getItemView([
                        'id' => $inventory->wa_inventory_item_id,
                        'store_location_id' => $order->wa_location_and_store_id,
                        'wa_supplier_id' => $order->wa_supplier_id,
                        'quantity' => $inventory->quantity,
                        'free' => $inventory->free_qualified_stock,
                        'discount_percentage' => $inventory->discount_percentage,
                        'discount_amount' => $inventory->discount_amount,
                        'vat_rate' => $inventory->vat_rate,
                        'vat' => $inventory->vat,
                    ]);
                }

                return view('admin.purchaseorders.edit', compact('title', 'model', 'breadcum', 'order', 'employees', 'vehicles', 'suppliers', 'items'));
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
            if (!can('edit', $this->model)) {
                throw new Exception(pageRestrictedMessage(), 403);
            }

            $order = WaPurchaseOrder::findOrFail($slug);

            $validator = Validator::make($request->all(), [
                'purchase_date' => 'required|date',
                'wa_supplier_id' => ['required', 'exists:wa_suppliers,id', new TradeAgreementValidator(true)],
                'vehicle_id' => 'required_if:supplier_own,=,OwnCollection',
                'employee_id' => 'required_if:supplier_own,=,OwnCollection',
                'item_id' => 'required|array',
                'item_quantity.*' => ['required', 'numeric', 'min:1', new MaxStockValidator($order)],
                'item_standard_cost.*' => ['required', 'numeric', 'min:1', new PriceListValidator],
                'item_vat.*' => 'nullable|exists:tax_managers,id',
                'item_discount_per.*' => 'nullable|numeric|min:0|max:100',
                'free_qualified_stock.*' => 'nullable|numeric|min:0|max:999999',
                'invoice_discount_per' => 'nullable|numeric|min:0|max:100',
                'invoice_discount' => 'nullable|numeric|min:0|max:999999',
                'invoice_percentage.*' => 'nullable|numeric|min:0',
                'transport_rebate_per_unit.*' => 'nullable|numeric|min:0',
                'distribution_discount.*' => 'nullable|numeric|min:0',
                'transport_rebate_percentage.*' => 'nullable|numeric|min:0',
                'transport_rebate_per_tonnage.*' => 'nullable|numeric|min:0',
                'lpo_type' => 'required_without:advance_payment|string|in:Bulk,Normal',
                'transport_rebate_discount_type' => 'nullable|string|in:per_unit,invoice_amount,per_tonnage',
            ], [
                "vehicle_id" => 'Vehicle is required',
                "employee_id" => 'Employee is required',
                "lpo_type" => 'Select an LPO type'
            ], [
                'item_quantity.*' => 'Item Quantity',
                'item_vat.*' => 'Vat',
                'item_discount_per.*' => 'Discount',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $getLoggeduserProfile = getLoggeduserProfile();

            $inventory = WaInventoryItem::select([
                'wa_inventory_items.*',
                'wa_inventory_location_stock_status.max_stock as max_stock_f',
                'wa_inventory_location_stock_status.re_order_level',
                'wa_inventory_item_supplier_data.price as new_standard_cost',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . $getLoggeduserProfile->wa_location_and_store_id . ') as quantity')
            ])
                ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($order) {
                    $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $order->wa_location_and_store_id);
                })
                ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($order) {
                    $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('wa_inventory_item_supplier_data.wa_supplier_id', $order->wa_supplier_id);
                })
                ->with(['getUnitOfMeausureDetail'])->whereIn('wa_inventory_items.id', $request->item_id)
                ->groupBy('wa_inventory_items.id')
                ->get();

            $errors = [];
            if (count($inventory) == 0) {
                $errors['testIn'] = ['Add items to proceed'];
            } else {
                foreach ($inventory as $key => $val) {
                    if ($val->max_stock_f < $request->item_quantity[$val->id]) {
                        // $errors['item_id.'.$val->id] = ['Qty cannot be greater than the Max Stock'];
                    }
                    if (empty($val->re_order_level)) {
                        $errors['item_id.' . $val->id] = ['Re-Order Level is mandatory'];
                    }
                    if (empty($val->new_standard_cost)) {
                        //                        $errors['item_id.' . $val->id] = ['Supplier price is not available!'];
                    }
                }
            }
            if (count($errors) > 0) {
                return response()->json(['result' => 0, 'errors' => $errors], 500);
            }
            $check = DB::transaction(function () use ($order, $inventory, $request, $getLoggeduserProfile) {
                $order->purchase_date = $request->purchase_date;
                $order->wa_priority_level_id = $request->wa_priority_level_id ?? NULL;
                $order->note = $request->note ?? "";
                $order->invoice_discount_per = $request->invoice_discount_per;
                $order->invoice_discount = $request->invoice_discount;
                $order->advance_payment = $request->boolean('advance_payment');
                $order->lpo_type = $order->advance_payment ? 'Advanced' : $request->lpo_type;
                $order->supplier_own = $request->supplier_own;
                $order->transport_rebate_discount_type = $request->transport_rebate_discount_type;
                if ($request->supplier_own == "OwnCollection") {
                    $order->vehicle_id = $request->vehicle_id;
                    $order->employee_id = $request->employee_id;
                }

                $order->sent_to_supplier = false;
                $order->save();

                $order->getRelatedItem()->delete();

                $items = [];
                foreach ($inventory as $key => $val) {
                    $cost = $request->item_standard_cost[$val->id];

                    $item = [];
                    $item['wa_purchase_order_id'] = $order->id;
                    $item['wa_inventory_item_id'] = $val->id;
                    $item['quantity'] = $request->item_quantity[$val->id];
                    $item['free_qualified_stock'] = $request->free_qualified_stock[$val->id];
                    $item['note'] = "";
                    $item['prev_standard_cost'] = $val->prev_standard_cost;
                    $item['selling_price'] = $val->selling_price;
                    $item['order_price'] = $cost;
                    //                    $item['order_price'] = $val->new_standard_cost;
                    $item['supplier_uom_id'] = @$getLoggeduserProfile->wa_unit_of_measures_id;
                    $item['pack_size_id'] = $val->pack_size_id;
                    $item['supplier_quantity'] = $request->item_quantity[$val->id];
                    $item['unit_conversion'] = 1;
                    $item['item_no'] = $val->stock_id_code;
                    $item['is_exclusive_vat'] = isset($request->item_vat[$val->id]) ? 'Yes' : 'No'; //its in reverse order
                    $check_uom = \App\Model\WaInventoryLocationUom::where(
                        [
                            'inventory_id' => $val->id,
                            'location_id' => $order->wa_location_and_store_id
                        ]
                    )->first();
                    $item['unit_of_measure'] = @$check_uom->uom_id;
                    $item['standard_cost'] = $cost;
                    //                    $item['standard_cost'] = $val->new_standard_cost;
                    $item['store_location_id'] = $val->store_location_id;
                    $item['total_cost'] = $cost * $request->item_quantity[$val->id];
                    if (@$request->item_discount_type[$val->id] == 'Value') {
                        $item['discount_amount'] = ($request->item_discount_per[$val->id]) ? ($item['quantity'] * $request->item_discount_per[$val->id]) : 0;
                    } else {
                        $item['discount_amount'] = ($request->item_discount_per[$val->id]) ? (($item['total_cost'] * $request->item_discount_per[$val->id]) / 100) : 0;
                    }
                    $item['discount_percentage'] = $request->item_discount_per[$val->id];
                    $item['total_cost'] = $item['total_cost'] - $item['discount_amount'];
                    $item['tax_manager_id'] = $request->item_vat[$val->id] ?? NULL;
                    $item['vat_rate'] = $request->item_vat_percentage[$val->id];
                    $item['vat_amount'] = ($request->item_vat_percentage[$val->id]) ? ($item['total_cost'] - ($item['total_cost'] * 100) / ($request->item_vat_percentage[$val->id] + 100)) : 0;
                    $item['total_cost'] = $item['total_cost'] - $item['vat_amount'];
                    $total_cost_with_vat = $item['total_cost'] + $item['vat_amount'];
                    $roundOff = fmod($total_cost_with_vat, 1); //0.25
                    if ($roundOff != 0) {
                        if ($roundOff > '0.50') {
                            $roundOff = round((1 - $roundOff), 2);
                        } else {
                            $roundOff = '-' . round($roundOff, 2);
                        }
                    }
                    $item['round_off'] = $roundOff;
                    $item['total_cost_with_vat'] = round($total_cost_with_vat);
                    $item['created_at'] = date('Y-m-d H:i:s');
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    $item['discount_settings'] = json_encode([
                        'invoice_percentage' => @$request->invoice_percentage[$val->id],
                        'base_discount_type' => @$request->item_discount_type[$val->id],
                        'distribution_discount' => @$request->distribution_discount[$val->id],
                        'transport_rebate_per_unit' => @$request->transport_rebate_per_unit[$val->id],
                        'transport_rebate_percentage' => @$request->transport_rebate_percentage[$val->id],
                        'transport_rebate_per_tonnage' => @$request->transport_rebate_per_tonnage[$val->id]
                    ]);

                    $item['other_discounts_total'] = $this->getTotalDiscount($cost, $request->item_quantity[$val->id], (object) $item);

                    $items[] = $item;
                }

                WaPurchaseOrderItem::insert($items);

                if ($request->action == 'process') {
                    addPurchaseOrderPermissions($order->id, $order->wa_department_id);
                }

                return true;
            });

            if ($check) {
                return response()->json(['result' => 1, 'message' => 'Purchase order updated successfully', 'location' => route('purchase-orders.index')], 200);
            }

            return response()->json(['result' => -1, 'error' => 'Something went wrong'], 500);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['result' => -1, 'error' => $msg], 500);
        }
    }


    public function destroy($slug)
    {
        try {
            WaPurchaseOrder::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getDapartments(Request $request)
    {
        $rows = WaDepartment::where('restaurant_id', $request->branch_id)->orderBy('department_name', 'asc')->get();
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

    public function getItemsList(Request $request)
    {
        $rows = WaInventoryItem::select('*', DB::RAW(' (select sum(qauntity) from wa_stock_moves where wa_stock_moves.stock_id_code=wa_inventory_items.stock_id_code) as item_total_qunatity'))->where('wa_inventory_category_id', $request->selected_inventory_category)->orderBy('description', 'asc')->get();

        $view_data = view('admin.purchaseorders.items_list', compact('rows'));
        return $view_data;
    }


    public function getItemDetail(Request $request)
    {
        $rows = WaInventoryItem::where('id', $request->selected_item_id)->first();
        $vat_rate = 0;
        if ($rows->tax_manager_id && $rows->getTaxesOfItem) {
            $vat_rate = $rows->getTaxesOfItem->tax_value;
        }


        return json_encode(['vat_rate' => $vat_rate, 'stock_id_code' => $rows->stock_id_code, 'unit_of_measure' => $rows->wa_unit_of_measure_id ? $rows->wa_unit_of_measure_id : '', 'standard_cost' => $rows->price_list_cost, 'prev_standard_cost' => $rows->prev_standard_cost]);
    }

    public function deletingItemRelation($purchase_no, $id)
    {
        try {
            WaPurchaseOrderItem::whereId($id)->delete();


            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function editPurchaseItem($purchase_no, $id)
    {
        try {

            $row = WaPurchaseOrder::where('purchase_no', $purchase_no)
                ->whereHas('getRelatedItem', function ($sql_query) use ($id) {
                    $sql_query->where('id', $id);
                })
                ->first();
            if ($row) {

                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), $row->purchase_no => '', 'Edit' => ''];
                $model = $this->model;


                $form_url = [$model . '.updatePurchaseItem', $row->getRelatedItem->find($id)->id];
                return view('admin.purchaseorders.editItem', compact('title', 'model', 'breadcum', 'row', 'id', 'form_url'));
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

            // dd('here');

            $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
            if ($request->is_exclusive_vat == "Yes") {
                $request->order_price = ($request->order_price * 100) / (100 + $item_detail->getTaxesOfItem->tax_value);
            } else {
                $request->order_price = $request->order_price;
            }


            $item = WaPurchaseOrderItem::where('id', $id)->first();
            $item->wa_inventory_item_id = (string)$request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->prev_standard_cost = $request->prev_standard_cost;
            $item->order_price = $request->order_price;
            $item->supplier_uom_id = $request->supplier_uom_id;
            $item->supplier_quantity = $request->supplier_quantity;
            $item->is_exclusive_vat = $request->is_exclusive_vat;

            $item->unit_conversion = $request->unit_conversion;
            $item->item_no = $request->item_no;

            $item->unit_of_measure = $request->unit_of_measure;
            $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item->order_price * $request->supplier_quantity;
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

            $total_cost_with_vat = $item->total_cost + $vat_amount;
            $roundOff = fmod($total_cost_with_vat, 1); //0.25
            if ($roundOff != 0) {
                if ($roundOff > '0.50') {
                    $roundOff = round($roundOff, 2);
                } else {
                    $roundOff = '-' . round($roundOff, 2);
                }
            }

            $item->round_off = $roundOff;
            $item->total_cost_with_vat = round($total_cost_with_vat);

            //	            $item->total_cost_with_vat =  $item->total_cost+$vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.edit', $item->getPurchaseOrder->slug);
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function inventoryItems(Request $request)
    {
        $trade = TradeAgreement::where([
            'wa_supplier_id' => $request->supplier_id
        ])->first();

        if (is_null($trade)) {
            $data = [];
        } else {
            $data = WaInventoryItem::select([
                'wa_inventory_items.*',
                DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . $request->store_location_id . ') as quantity'),
            ])
                ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
                ->join('trade_product_offers', function ($e) use ($trade) {
                    $e->on('trade_product_offers.inventory_item_id', '=', 'wa_inventory_items.id')
                        ->where('trade_product_offers.trade_agreements_id', $trade->id);
                })
                ->where([['pack_sizes.can_order', 1], ['status', 1]])
                ->where(function ($q) use ($request) {
                    if ($request->search) {
                        $q->where('wa_inventory_items.title', 'LIKE', "%$request->search%");
                        $q->orWhere('wa_inventory_items.stock_id_code', 'LIKE', "%$request->search%");
                    }
                })->where(function ($e) use ($request) {
                    if ($request->store_c) {
                        $e->where('store_c_deleted', 0);
                    }
                })
                ->whereHas('suppliers', function ($query) use ($request) {
                    if ($request->supplier_id) {
                        $query->where('wa_suppliers.id', $request->supplier_id);
                    }
                })
                ->limit(15)->get();
        }

        $view = '<table class="table table-bordered table-hover" id="stock_inventory" style="
        display: block;
        right: auto !important;
        position: absolute;
        min-width: 400px;
        left: 0 !important;
        max-height: 350px;
        margin-top: 4px!important;
        overflow: auto;
        padding: 0;
        background:#fff;
        ">';
        $view .= "<thead>";
        $view .= '<tr>';
        $view .= '<th style="width:20%">Code</th>';
        $view .= '<th style="width:70%">Description</th>';
        $view .= '<th style="width:10%">QOH</th>';
        $view .= '</tr>';
        $view .= '</thead>';
        $view .= "<tbody>";
        foreach ($data as $key => $value) {
            // $qoh = WaStockMove::where('wa_inventory_item_id', $value->id)
            //     ->where('wa_location_and_store_id', getLoggeduserProfile()->wa_location_and_store_id)
            //     ->sum('qauntity');
            $view .= '<tr onclick="fetchInventoryDetails(this)" ' . ($key == 0 ? 'class="SelectedLi"' : NULL) . ' data-id="' . $value->id . '" data-title="' . $value->title . '(' . $value->stock_id_code . ')">';
            $view .= '<td style="width:20%">' . $value->stock_id_code . '</td>';
            $view .= '<td style="width:70%">' . $value->title . '</td>';
            // $view .= '<td style="width:10%">' . ($value->quantity ?? 0) . '</td>';
            $view .= '<td style="width:10%">' . ($value->quantity ?? 0) . '</td>';

            $view .= '</tr>';
        }
        $view .= '</tbody>';
        $view .= '</table>';
        return response()->json($view);
    }

    public function inventoryItemsTransfers(Request $request)
    {

        $data = WaInventoryItem::select([
            //  '*',
            'wa_inventory_items.*',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($request->store_location_id ?? 'wa_inventory_items.store_location_id') . ') as quantity'),
        ])
            ->join('pack_sizes', 'wa_inventory_items.pack_size_id', '=', 'pack_sizes.id')
            // ->where([['pack_sizes.can_order', 1], ['status', 1]])
            ->where(function ($q) use ($request) {
                if ($request->search) {
                    $q->where('wa_inventory_items.title', 'LIKE', "%$request->search%");
                    $q->orWhere('stock_id_code', 'LIKE', "%$request->search%");
                }
            })->where(function ($e) use ($request) {
                if ($request->store_c) {
                    $e->where('store_c_deleted', 0);
                }
            })->limit(15)->get();
        $view = '<table class="table table-bordered table-hover" id="stock_inventory" style="
        display: block;
        right: auto !important;
        position: absolute;
        min-width: 400px;
        left: 0 !important;
        max-height: 350px;
        margin-top: 4px!important;
        overflow: auto;
        padding: 0;
        background:#fff;
        ">';
        $view .= "<thead>";
        $view .= '<tr>';
        $view .= '<th style="width:20%">Code</th>';
        $view .= '<th style="width:70%">Description</th>';
        $view .= '<th style="width:10%">QOH</th>';
        $view .= '</tr>';
        $view .= '</thead>';
        $view .= "<tbody>";
        foreach ($data as $key => $value) {
            $qoh = WaStockMove::where('stock_id_code', $value->stock_id_code)->where('wa_location_and_store_id', $request->store_location_id)->sum('qauntity');
            $view .= '<tr onclick="fetchInventoryDetails(this)" ' . ($key == 0 ? 'class="SelectedLi"' : NULL) . ' data-id="' . $value->id . '" data-title="' . $value->title . '(' . $value->stock_id_code . ')">';
            $view .= '<td style="width:20%">' . $value->stock_id_code . '</td>';
            $view .= '<td style="width:70%">' . $value->title . '</td>';
            // $view .= '<td style="width:10%">' . ($value->quantity ?? 0) . '</td>';
            $view .= '<td style="width:10%">' . ($qoh ?? 0) . '</td>';

            $view .= '</tr>';
        }
        $view .= '</tbody>';
        $view .= '</table>';
        return response()->json($view);
    }

    public function unarchive_lpo($slug)
    {
        $row = WaPurchaseOrder::whereSlug($slug)->first();
        $row->is_hide = 'No';
        $row->save();
        if ($row) {
            return redirect()->route('purchase-orders.archived-lpo')->with('success', 'Unarchive Succesfully');
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function archivedLPOs(Request $request)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.purchaseorders.archived-lpo', [
            'model' => 'archived-lpo',
            'title' => "Archived LPO's",
            'breadcum' => [
                'Purchase Orders' => route('purchase-orders.index'),
                'Archived LPOs' => '',
            ],
        ]);
    }

    public function completedLPOs(Request $request)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        return view('admin.purchaseorders.completed-lpo', [
            'model' => 'completed-lpo',
            'title' => "Completed LPO's",
            'breadcum' => [
                'Purchase Orders' => route('purchase-orders.index'),
                'Completed LPOs' => '',
            ],
        ]);
    }

    protected function getItemView($item)
    {
        $data = WaInventoryItem::select([
            'wa_inventory_items.*',
            'wa_inventory_location_stock_status.max_stock as max_stock_f',
            'wa_inventory_location_stock_status.re_order_level',
            'wa_inventory_item_supplier_data.price as new_standard_cost',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($item['store_location_id'] ?? 'wa_inventory_items.store_location_id') . ') as quantity')
        ])
            ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($item) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $item['store_location_id']);
            })
            ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($item) {
                $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_item_supplier_data.wa_supplier_id', $item['wa_supplier_id']);
            })
            ->with(['getTaxesOfItem', 'pack_size'])->where('wa_inventory_items.id', $item['id'])->first();

        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $qoo = WaPurchaseOrderItem::query()
            ->whereHas('getPurchaseOrder', function ($query) use ($item) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->doesntHave('grns');

                $query->where('wa_location_and_store_id', $item['store_location_id']);
            })->where('wa_inventory_item_id', $item['id'])
            ->sum('quantity');

        $sales = WaStockMove::query()
            ->where('wa_location_and_store_id', $item['store_location_id'])
            ->where('wa_inventory_item_id', $item['id'])
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
            ->where('wa_location_and_store_id', $item['store_location_id'])
            ->where('items.wa_inventory_item_id', $item['id'])
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$start_30, $end_30])
            ->first();

        $view = '';
        if ($data) {
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="invoice_percentage[' . $data->id . ']" class="invoice_percentage" value="0">
                <input type="hidden" name="distribution_discount[' . $data->id . ']" class="distribution_discount" value="0">
                <input type="hidden" name="transport_rebate_per_unit[' . $data->id . ']" class="transport_rebate_per_unit" value="0">
                <input type="hidden" name="transport_rebate_percentage[' . $data->id . ']" class="transport_rebate_percentage" value="0">
                <input type="hidden" name="transport_rebate_per_tonnage[' . $data->id . ']" class="transport_rebate_per_tonnage" value="0">
                <input type="hidden" name="item_id[' . $data->id . ']" class="itemid" value="' . $data->id . '">
                <input type="hidden" name="item_net_weight[' . $data->id . ']" class="item_net_weight" value="' . $data->net_weight . '">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' . $data->stock_id_code . '">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td>' . $data->description . '</td>
            <td>' . ($data->pack_size->title ?? NULL) . '</td>
            <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" data-max_stock="' . $data->max_stock_f . '" onchange="getTotal(this)"  type="text" name="item_quantity[' . $data->id . ']" data-id="' . $data->id . '"  class="quantity item_quantity_max_stock form-control" value="' . ($item['quantity'] ?? 0) . '"></td>
            <td><input type="text" class="free_stock form-control" readonly name="free_qualified_stock[' . $data->id . ']" value="' . ($item['free'] ?? 0) . '"></td>
            <td>' . number_format($qoo ?? 0) . '</td>
            <td>' . number_format($data->quantity ?? 0) . '</td>
            <td>' . manageAmountFormat(abs($sales) + $smallPackSale->amount ?? 0) . '</td>
            <td>' . $data->re_order_level . '</td>
            <td>' . $data->max_stock_f . '</td>
            <td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" readonly name="item_standard_cost[' . $data->id . ']" data-id="' . $data->id . '"  class="standard_cost form-control" value="' . $data->price_list_cost . '"></td>';
            $view .= '<td><select class="form-control vat_list" name="item_vat[' . $data->id . ']">';
            $per = 0;
            $vat = 0.00;

            if (isset($item['vat'])) {
                $view .= '<option value="' . $item['vat']->id . '">' . $item['vat']->title . '</option>';
                $per = $item['vat']->tax_value;
                $vat = ($data->new_standard_cost * $per) / 100;
            } elseif ($data->getTaxesOfItem) {
                $view .= '<option value="' . $data->getTaxesOfItem->id . '" selected>' . $data->getTaxesOfItem->title . '</option>';
                $per = $data->getTaxesOfItem->tax_value;
                $vat = ($data->new_standard_cost * $per) / 100;
            }
            $dis_type = "";
            if (isset($item['discount_settings'])) {
                $discount_settings = json_decode($item['discount_settings']);
                if ($discount_settings && $discount_settings->base_discount_type) {
                    $dis_type = $discount_settings->base_discount_type;
                }
            }

            $view .= '</select>
            <input type="hidden" class="vat_percentage" value="' . $per . '"  name="item_vat_percentage[' . $data->id . ']" value="' . $per . '"></td>
            <td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_discount_per[' . $data->id . ']" data-id="' . $data->id . '" class="discount_per form-control" value="' . ($item['discount_percentage'] ?? 0) . '" readonly></td>
            <td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_discount_type[' . $data->id . ']" data-id="' . $data->id . '" class="discount_type form-control" value="' . ($dis_type) . '" readonly></td>
            <td><input style="padding: 3px 3px;"  type="text" name="item_discount[' . $data->id . ']" data-id="' . $data->id . '"  class="discount form-control" value="' . ($item['discount_amount'] ?? 0) . '" readonly></td>';
            $view .= '<td><span class="exclusive">0</span></td>
            <td><span class="vat">0</span></td>
            <td><span class="total">0</span></td>
            <td>
            <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>
            </tr>';
        }

        return $view;
    }


    public function getInventryItemDetails(Request $request)
    {
        $view = $this->getItemView($request->all());

        return response()->json($view);
    }
    public function getInventryItemDetailsExtension(Request $request)
    {
        $view = $this->getItemViewExtension($request->all());

        return response()->json($view);
    }


    protected function getItemViewExtension($item)
    {
        $data = WaInventoryItem::select([
            'wa_inventory_items.*',
            'wa_inventory_location_stock_status.max_stock as max_stock_f',
            'wa_inventory_location_stock_status.re_order_level',
            'wa_inventory_item_supplier_data.price as new_standard_cost',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($item['store_location_id'] ?? 'wa_inventory_items.store_location_id') . ') as quantity')
        ])
            ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($item) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', $item['store_location_id']);
            })
            ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($item) {
                $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_item_supplier_data.wa_supplier_id', $item['wa_supplier_id']);
            })
            ->with(['getTaxesOfItem', 'pack_size'])->where('wa_inventory_items.id', $item['id'])->first();

        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $qoo = WaPurchaseOrderItem::query()
            ->whereHas('getPurchaseOrder', function ($query) use ($item) {
                $query->where('status', 'APPROVED')
                    ->where('is_hide', '<>', 'Yes')
                    ->doesntHave('grns');

                $query->where('wa_location_and_store_id', $item['store_location_id']);
            })->where('wa_inventory_item_id', $item['id'])
            ->sum('quantity');

        $sales = WaStockMove::query()
            ->where('wa_location_and_store_id', $item['store_location_id'])
            ->where('wa_inventory_item_id', $item['id'])
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
            ->where('wa_location_and_store_id', $item['store_location_id'])
            ->where('items.wa_inventory_item_id', $item['id'])
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$start_30, $end_30])
            ->first();

        $view = '';
        if ($data) {
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id[' . $data->id . ']" class="itemid" value="' . $data->id . '">
                <input type="hidden" name="item_net_weight[' . $data->id . ']" class="item_net_weight" value="' . $data->net_weight . '">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' . $data->stock_id_code . '">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td>' . $data->description . '</td>
            <td>' . ($data->pack_size->title ?? NULL) . '</td>
            <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" data-max_stock="' . $data->max_stock_f . '" onchange="getTotal(this)"  type="text" name="item_quantity[' . $data->id . ']" data-id="' . $data->id . '"  class="quantity item_quantity_max_stock form-control" value="' . ($item['quantity'] ?? 0) . '"></td>
           
            <td>' . number_format($qoo) . '</td>
            <td>' . number_format($data->quantity ?? 0) . '</td>
            <td>' . manageAmountFormat(abs($sales) + $smallPackSale->amount ?? 0) . '</td>
            <td>' . $data->re_order_level . '</td>
            <td>' . $data->max_stock_f . '</td>        
            <td>
            <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </td>
            </tr>';
        }

        return $view;
    }



    public function getInventryItemDetailsRow(Request $request)
    {
        $data = WaInventoryItem::select([
            'wa_inventory_items.*',
            'wa_inventory_location_stock_status.max_stock as max_stock_f',
            'wa_inventory_location_stock_status.re_order_level',
            'wa_inventory_item_supplier_data.price as new_standard_cost',
            DB::RAW('(select SUM(wa_stock_moves.qauntity) FROM wa_stock_moves where wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id AND wa_stock_moves.wa_location_and_store_id = ' . ($request->store_location_id ?? 'wa_inventory_items.store_location_id') . ') as quantity')
        ])
            ->leftJoin('wa_inventory_location_stock_status', function ($e) use ($request) {
                $e->on('wa_inventory_location_stock_status.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    // ->where('wa_inventory_location_stock_status.wa_location_and_stores_id',DB::RAW('wa_inventory_items.store_location_id'));
                    ->where('wa_inventory_location_stock_status.wa_location_and_stores_id', ($request->store_location_id ?? DB::RAW('wa_inventory_items.store_location_id')));
            })
            ->leftJoin('wa_inventory_item_supplier_data', function ($e) use ($request) {
                $e->on('wa_inventory_item_supplier_data.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('wa_inventory_item_supplier_data.wa_supplier_id', $request->wa_supplier_id);
            })
            ->with(['getTaxesOfItem', 'pack_size'])->where('wa_inventory_items.id', $request->id)->first();
        $view = '';
        if ($data) {
            $view .= '<tr>                                      
            <td>
                <input type="hidden" name="item_id[' . $data->id . ']" class="itemid" value="' . $data->id . '">
                <input type="hidden" name="item_net_weight[' . $data->id . ']" class="item_net_weight" value="' . $data->net_weight . '">
                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' . $data->stock_id_code . '">
                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
            </td>
            <td>' . $data->description . '</td>
            <td>' . ($data->pack_size->title ?? NULL) . '</td>
            <td><input style="padding: 3px 3px;" autofocus onkeyup="getTotal(this)" data-max_stock="' . $data->max_stock_f . '" onchange="getTotal(this)"  type="text" name="item_quantity[' . $data->id . ']" data-id="' . $data->id . '"  class="quantity item_quantity_max_stock form-control" value="' . ($data->max_stock_f) - ($data->quantity) . '"></td>
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


    public function sendToSupplier(Request $request): RedirectResponse
    {
        try {
            $supplier = WaSupplier::find($request->supplier_id);

            if (!$supplier->email) {
                return redirect()->route('purchase-orders.index')
                    ->withErrors(['message' => 'This supplier does not have a valid email address.']);
            }

            $lpo = WaPurchaseOrder::with([
                'getBranch',
                'getRelatedItem.pack_size',
                'getRelatedItem.getInventoryItemDetail'
            ])
                ->whereSlug($request->lpo_slug)->first();

            $qr_code = QrCode::generate(
                $lpo->purchase_date . " - " . $lpo->purchase_no . " - " . $lpo->getBranch->name . " - " . manageAmountFormat($lpo->getRelatedItem->sum('total_cost_with_vat')),
            );
            $settings = getAllSettings();
            $pdf_d = true;
            $row = $lpo;
            $pdf = PDF::loadView('admin.purchaseorders.print', compact('row', 'qr_code', 'pdf_d', 'settings'))->set_option("enable_php", true);

            $email_template = EmailTemplate::templateList()[$this->email_template];
            $template = EmailTemplate::where('name', $email_template->name)->first();
            $makesubject = @$template->subject ?? $email_template->subject;
            $subject = str_replace(['${purchase_no}', '${branch}'], [$lpo?->purchase_no, $lpo?->getBranch?->name], $makesubject);

            $mail = new LpoApproved($lpo, $supplier, $request->message, $pdf->output(), $subject);

            $recipients = collect(array_merge(explode(',', $request->recipient), [$supplier->email]));
            $cc = collect(explode(',', $request->cc));

            Mail::to($recipients->unique())
                ->cc($cc)
                ->send($mail);

            $lpo->update([
                'sent_to_supplier' => true
            ]);

            return redirect()->route('purchase-orders.index')->with('success', 'LPO send successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('purchase-orders.index')->withErrors(['message' => $e->getMessage()]);
        }
    }
}
