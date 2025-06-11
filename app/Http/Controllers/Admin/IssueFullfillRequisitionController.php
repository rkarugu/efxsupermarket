<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\PerformPostSaleActions;
use App\LoadingSheetDispatch;
use App\Model\User;
use App\Services\CreditSalesService;
use App\VehicleAssignment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaGlTran;
use App\Model\WaAccountingPeriod;
use App\Model\DispatchLoadedProducts;
use App\Model\WaEsdDetails;
use App\Model\WaRouteCustomer;
use App\Model\WaShift;
use App\Model\Vehicle;

use Illuminate\Support\Facades\DB;
use PDF;
use Session;
use Illuminate\Support\Facades\Validator;

class IssueFullfillRequisitionController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'confirm-invoice';
        $this->title = 'Confirm Invoice';
        $this->pmodule = 'confirm-invoice';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaInternalRequisition::with('getRouteCustomer')
                ->with('getRelatedFromLocationAndStore')->where('status', '=', 'UNAPPROVED')
                ->where('status', '=', 'COMPLETED');

            if ($permission != 'superadmin') {
                $lists = $lists->where('to_store_id', getLoggeduserProfile()->wa_location_and_store_id);
            }
            $lists = $lists->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.issuefullfillrequisition.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function save_esd(Request $request)
    {
        $newEsd = new WaEsdDetails();
        if ($request->status == true) {
            $request_data = json_decode($request->apiData);
            $newEsd->invoice_number = $request_data->invoice_number ?? NULL;
            $newEsd->cu_serial_number = $request_data->cu_serial_number ?? NULL;
            $newEsd->cu_invoice_number = $request_data->cu_invoice_number ?? NULL;
            $newEsd->verify_url = $request_data->verify_url ?? NULL;
            $newEsd->description = $request_data->description ?? NULL;
            $newEsd->status = 1;
            $newEsd->save();
            return response()->json(['result' => 1, 'message' => 'Esd save successfully']);
        } else {
            $newEsd->invoice_number = $request->invoice_number ?? NULL;
            $newEsd->description = $request->error ?? NULL;
            $newEsd->status = 0;
            $newEsd->save();
        }
    }

    public function show($slug)
    {
        $row = WaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
        $routeCustomers = WaRouteCustomer::get();

        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
            $model = $this->model;
            $route = \App\Model\Route::pluck('route_name', 'id');
            return view('admin.issuefullfillrequisition.show', compact('route', 'title', 'model', 'breadcum', 'row', 'routeCustomers'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->route('confirm-invoice.index');
        }
    }

    public function exportToPdf($slug)
    {


        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = WaInternalRequisition::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.issuefullfillrequisition.print', compact('title', 'model', 'breadcum', 'row'));
        $report_name = 'internal_requisition_' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }

    public function printPage(Request $request)
    {

        $slug = $request->slug;
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row = WaInternalRequisition::whereSlug($slug)->first();
        return view('admin.issuefullfillrequisition.print', compact('title', 'model', 'breadcum', 'row'));
    }

    public function destroy($slug)
    {
        try {
            $a = DB::transaction(function () use ($slug) {
                $row = WaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
                if ($row->id) {
                    WaInternalRequisitionItem::where('wa_internal_requisition_id', $row->id)->delete();
                    $row->delete();
                    return true;
                }
                return false;
            });
            if (!$a) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function update(Request $request, $slug)
    {

        try {
            $internal_requisition_row = WaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
            if (!$internal_requisition_row) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->route('confirm-invoice.index');
            }
            $itemslist = WaInternalRequisitionItem::with(['getInventoryItemDetail', 'getInventoryItemDetail.getAllFromStockMoves'])->where('wa_internal_requisition_id', $internal_requisition_row->id)->get();
            $totalinventorycost = 0;
            foreach ($itemslist as $it) {

                $stockQty = WaStockMove::where('stock_id_code', @$it->getInventoryItemDetail->stock_id_code)->sum('qauntity');

                if (!isset($it->getInventoryItemDetail->getAllFromStockMoves) || $stockQty < $it->quantity) {

                    Session::flash('warning', @$it->getInventoryItemDetail->stock_id_code . ' Item Available quantity is not enough');
                    return redirect()->back()->withInput();
                }
                if ($it->getInventoryItemDetail && $it->getInventoryItemDetail->block_this == 1) {
                    Session::flash('warning', $it->getInventoryItemDetail->stock_id_code . ': The product has been blocked from sale due to a change in standard cost');
                    return redirect()->back()->withInput();
                }
                $totalinventorycost += $it->total_cost_with_vat;
            }


            $customer = \App\Model\WaCustomer::where('id', $internal_requisition_row->customer_id)->first();
            $location = \App\Model\WaLocationAndStore::with(['user'])->where('id', $internal_requisition_row->to_store_id)->first();
            $credit_limit = $customer->credit_limit ?? 0;
            $used_limit = \App\Model\WaDebtorTran::where('wa_customer_id', @$customer->id)->sum('amount');
            $available_limit = $credit_limit - $used_limit;
            if ($available_limit < $totalinventorycost) {
                Session::flash('warning', "You cannot process the Invoice as it will exceed your allowed Credit Limit");
                return redirect()->back()->withInput();
            }

            /*call Post sale Service*/
//            $service = new CreditSalesService($internal_requisition_row);
//            $processed =  $service->index();
            $internal_requisition_row->update(['status'=>'COMPLETED']);
            PerformPostSaleActions::dispatch($internal_requisition_row);
            return redirect()->route('sales-invoice.index');
//            if ($processed)
//            {
//                $internal_requisition_row->update(['status'=>'COMPLETED']);
//                Session::flash('success', 'Processed Successfully');
//
//                return redirect()->route('sales-invoice.index');
//            }else
//            {
//                Session::flash('warning', 'Error Preprocessing');
//                return redirect()->back()->withInput();
//            }

        } catch (\Throwable $e) {
//            $msg = $e->getTraceAsString();
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function checkQuantity($locationid, $itemid, $qty)
    {
        try {
            $qtyOnHand = WaStockMove::where('wa_location_and_store_id', $locationid)->where('stock_id_code', $itemid)->sum('qauntity');
            // echo $qtyOnHand; die;
            //   $item = WaInventoryItem::select('stock_id_code','id')->where('stock_id_code',$itemid)->first();

            //   $item_id = $item->id;


            $myqty = $qty;
            $qtyOnHand = $qtyOnHand;
            if ($myqty <= $qtyOnHand) {
                return '0';
            } else {

                return '1';
            }
        } catch (\Exception $e) {

            return '1';
        }
    }


    public function invoice_dispatch_report(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'invoice-dispatch-report';
        $title = 'Invoice Dispatch Report';
        $model = 'dispatch-invoice-report';

        if (!request()->type) {
            return redirect()->route('confirm-invoice.invoice_dispatch_report', ['type' => 'detailed']);
        }

        if (request()->type == 'detailed' && (!isset($permission['invoice-dispatch-report___detailed']) && $permission != 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        if (request()->type == 'summary' && (!isset($permission['invoice-dispatch-report___summary']) && $permission != 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $type = request()->type;
        if ($request->input('manage-request')) {
            $shifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('id')->toArray();
            $allshifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('shift_id')->toArray();
            $salesman = \App\Model\User::where('id', $request->salesman_id)->first();
            $inventory = WaInventoryItem::where('store_location_id', $request->store)->get();
            $funcShift = function ($w) use ($shifts) {
                $w->whereIn('shift_id', $shifts);
            };
            $storeLocation = WaLocationAndStore::where('id', $request->store)->first();
            $items = WaInventoryLocationTransferItem::select(['*', DB::RAW('SUM(quantity - return_quantity) as qty')])
                ->with(['getTransferLocation' => $funcShift])
                ->where('store_location_id', $request->store)
                // ->where('is_return',0)
                // ->whereHas('getRequisitionItem',function($w) {$w->where('is_dispatched',1);})
                ->whereHas('getTransferLocation', $funcShift)->orderBy('created_at', 'DESC')
                ->groupBy('wa_inventory_location_transfer_id', 'wa_inventory_item_id')
                ->get();
            // dd($items);
            $vehicleId = $request->vehicle_id;
            $vehicle = Vehicle::find($vehicleId);
            $orders = WaInternalRequisition::where('wa_shift_id', $request->shift_id)
                ->update(['vehicle_id' => $vehicleId]);

            if ($request->input('manage-request') == "pdf_profit") {
                $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_facade_report_pdf', compact('items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
                $report_name = 'profitability_report_' . date('Y_m_d_H_i_A');
            } else {
                if ($type == 'detailed') {
                    $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_report_pdf', compact('items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
                } else {
                    // Replicated for testing. Will be removed
                    $salesman = User::with('getroute')->find($request->salesman_id);
                    $salesManShiftId = ($request->shift_id)[0];
                    $shift = WaShift::find((int)$salesManShiftId);
                    $storeLocation = WaLocationAndStore::find($request->store);
                    $route = $salesman->getroute;
                    $invoiceIds = WaInternalRequisition::where('wa_shift_id', $shift->id)->pluck('id');
                    $invoiceItems = WaInternalRequisitionItem::with(['getInventoryItemDetail'])->whereIn('wa_internal_requisition_id', $invoiceIds)
                        ->select('*', DB::raw('sum(quantity) as total_quantity'))
                        ->groupBy(['wa_inventory_item_id'])
                        ->get();

                    $totalTonnage = 0;
                    foreach ($invoiceItems as $invoiceItem) {
                        $invoiceItem->total_tonnage = $invoiceItem->getInventoryItemDetail->gross_weight * $invoiceItem->total_quantity;
                        $totalTonnage += $invoiceItem->total_tonnage;
                    }

                    $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_report_summary_pdf', compact('shift', 'salesman', 'storeLocation', 'route', 'invoiceItems', 'totalTonnage'));
                }
                $report_name = 'invoice_dispatch_report_' . date('Y_m_d_H_i_A');
            }


            // return $pdf->stream();
            return $pdf->download($report_name . '.pdf');
        }

        $assignedVehicles = VehicleAssignment::pluck('vehicle_list_id');
        $vehicles = Vehicle::with('vehicle')->whereIn('id', $assignedVehicles)->get();
        $storeLocation = WaLocationAndStore::pluck('location_name', 'id')->toArray();
        $breadcum = [$title => route('confirm-invoice.invoice_dispatch_report'), 'Listing' => ''];
        return view('admin.issuefullfillrequisition.invoice_dispatch_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'type', 'storeLocation', 'vehicles'));

    }

    public function invoice_dispatch_report_profit(Request $request)
    {

        $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_facade_report_pdf');
        $report_name = 'profitability_report_' . date('Y_m_d_H_i_A');

        return $pdf->download($report_name . '.pdf');

    }


    public function dispatch_and_close_loading_sheet(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'dispatch-and-close-loading-sheet';
        $title = 'Invoice Dispatch Report';
        $model = 'dispatch-and-close-loading-sheet';

        if (!request()->type) {
            return redirect()->route('confirm-invoice.dispatch_and_close_loading_sheet', ['type' => 'detailed']);
        }

        if ((!isset($permission['dispatch-and-close-loading-sheet___view']) && $permission != 'superadmin')) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $type = request()->type;
        $items = [];
        $allshifts = [];
        $inventory = [];
        $salesman = [];

        if ($request->input('manage-request')) {
            $checkExists = DispatchLoadedProducts::where(['salesman_id' => $request->salesman_id, 'store_location_id' => $request->store])->whereIn('shift_id', $request->shift_id)->get();

            if ($checkExists->count() > 0) {
                \Session::flash('warning', 'The Loading sheet for this shift already been Processed');
                return redirect()->back();
            }

            $shifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('id')->toArray();
            $allshifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('shift_id')->toArray();
            $salesman = \App\Model\User::where('id', $request->salesman_id)->first();
            $inventory = WaInventoryItem::where('store_location_id', $request->store)->get();
            $funcShift = function ($w) use ($shifts) {
                $w->whereIn('shift_id', $shifts);
            };
            $storeLocation = WaLocationAndStore::where('id', $request->store)->first();
            $items = WaInventoryLocationTransferItem::select(['*', DB::RAW('SUM(quantity - return_quantity) as qty')])
                ->with(['getTransferLocation' => $funcShift])
                ->where('store_location_id', $request->store)
                // ->where('is_return',0)
                // ->whereHas('getRequisitionItem',function($w) {$w->where('is_dispatched',1);})
                ->whereHas('getTransferLocation', $funcShift)->orderBy('created_at', 'DESC')
                ->groupBy('wa_inventory_location_transfer_id', 'wa_inventory_item_id')
                ->get();

            /* if($type == 'detailed'){
                 $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_report_pdf',compact('items','inventory','allshifts','salesman','storeLocation'));
             }else {
                 $pdf = PDF::loadView('admin.issuefullfillrequisition.invoice_dispatch_report_summary_pdf',compact('items','inventory','allshifts','salesman','storeLocation'));
             }*/
            //$report_name = 'invoice_dispatch_report_' . date('Y_m_d_H_i_A');
            // return $pdf->stream();
            //return $pdf->download($report_name.'.pdf'); 
        }

        $storeLocation = WaLocationAndStore::pluck('location_name', 'id')->toArray();
        $breadcum = [$title => route('confirm-invoice.invoice_dispatch_report'), 'Listing' => ''];
        return view('admin.issuefullfillrequisition.dispatch_and_close_loading_sheet', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'type', 'allshifts', 'storeLocation', 'salesman', 'items', 'inventory'));

    }

    public function dispatch_and_close_loading_sheet_post(Request $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
//            $permission = $this->mypermissionsforAModule();
//            $shift_data = json_decode($request->shift_data, true);
//            $getLoggeduserProfile = getLoggeduserProfile();
//
//            $item_arr = $request->item_id;
//            $store_qty_loaded_arr = $request->store_qty_loaded;
//            $item_total_qty_arr = $request->item_total_qty;
//
//            if (!empty($shift_data) && !empty($request->store_qty_loaded)) {
//                foreach ($shift_data as $shift) {
//                    $formdata = array('status' => 'close');
//                    foreach ($request->store_qty_loaded as $key => $loaded_qty) {
//
//                        $loaded_qty = ($loaded_qty != "") ? $loaded_qty : 0;
//                        $dispatchItem = new DispatchLoadedProducts();
//                        $dispatchItem->user_id = $getLoggeduserProfile->id;
//                        $dispatchItem->salesman_id = $request->salesman_id;
//                        $dispatchItem->shift_id = $shift;
//                        $dispatchItem->store_location_id = @$request->store_location_id;
//                        $dispatchItem->inventory_item_id = @$item_arr[$key];
//                        $dispatchItem->total_qty = @$item_total_qty_arr[$key];
//                        $dispatchItem->qty_loaded = @$loaded_qty;
//                        $dispatchItem->balance_qty = @($dispatchItem->total_qty - $loaded_qty);
//                        $dispatchItem->save();
//                    }
//
//                    WaShift::where('id', $shift)->update($formdata);
//                }
//            }

            $shift = WaShift::find($request->shift_id);
            $shift->parking_list_status = 'closed';
            $shift->save();
            $dispatchedLoadingSheet = LoadingSheetDispatch::where('shift_id', $request->shift_id)->first();
            if ($dispatchedLoadingSheet) {
                return redirect()->route('confirm-invoice.dispatch_and_close_loading_sheet', ['type' => 'detailed'])->withErrors(['errors' => 'Loading sheet for this shift has already been dispatched']);
            }

            $loadingSheetDispatch = LoadingSheetDispatch::create([
                'delivery_note_number' => $this->getOrCreateDeliveryNoteNo(),
                'route_id' => $shift->getSalesManDetail->route,
                'shift_id' => $request->shift_id,
                'vehicle_id' => $request->vehicle_id,
                'user_id' => $request->user_id,
            ]);

            updateUniqueNumberSeries('DELIVERY NOTE', $loadingSheetDispatch->delivery_note_number);

            foreach ($request->inventory_item_ids as $key => $inventory_item_id) {
                $loadingSheetDispatch->items()->create([
                    'wa_inventory_item_id' => $inventory_item_id,
                    'requested_quantity' => ($request->requested_quantities)[$key],
                    'loaded_quantity' => ($request->loaded_quantities)[$key],
                ]);

//                $dispatchItem = new DispatchLoadedProducts();
//                $dispatchItem->user_id = $request->user_id;
//                $dispatchItem->salesman_id = $shift->salesman_id;
//                $dispatchItem->shift_id = $shift->id;
//                $dispatchItem->store_location_id = ($request->store_location_ids)[$key];
//                $dispatchItem->inventory_item_id = $inventory_item_id;
//                $dispatchItem->total_qty = ($request->requested_quantities)[$key];
//                $dispatchItem->qty_loaded = ($request->loaded_quantities)[$key];
//                $dispatchItem->balance_qty = (($request->requested_quantities)[$key]) - (($request->loaded_quantities)[$key]);
//                $dispatchItem->save();
            }

            DB::commit();
            return redirect()->route('confirm-invoice.dispatch_and_close_loading_sheet', ['type' => 'detailed'])->with('success', 'Loading Sheet Dispatched Successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('confirm-invoice.dispatch_and_close_loading_sheet', ['type' => 'detailed'])->withErrors(['errors' => $e->getMessage()]);
        }
    }

    private function getOrCreateDeliveryNoteNo(): string
    {
        $seriesCode = WaNumerSeriesCode::where('module', 'DELIVERY NOTE')->first();
        if (!$seriesCode) {
            WaNumerSeriesCode::create([
                'code' => 'DN',
                'module' => 'DELIVERY NOTE',
                'description' => 'Loading Sheet Delivery Nots',
                'starting_number' => 0,
                'last_date_used' => Carbon::now()->toDateString(),
                'last_number_used' => 0,
                'type_number' => 1,
            ]);
        }

        return getCodeWithNumberSeries('DELIVERY NOTE');
    }
}
