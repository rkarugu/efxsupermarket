<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GeneralExcelExport; 
use App\Exports\CommonReportDataExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Model\WaPurchaseOrder;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInternalRequisition;
use App\Model\WaStockMove;
use App\Model\Order;
use App\Model\Restaurant;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use Validator;
use App\Model\WaSupplier;
use Session;
use DateTime;
use Excel;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use PDF;

class InventoryReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'inventory-reports';
        $this->title = 'Reports';
        $this->pmodule = 'inventory-reports';
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }

    public function grnReports(Request $request)
    {

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();


        $lists = WaPurchaseOrder::with('getRelatedGrn', 'getRelatedGrn.getRelatedInventoryItem', 'getRelatedGrn.getRelatedInventoryItem.getSupplierUomDetail')->where('status', 'COMPLETED');
        $start_date = null;
        $end_date = null;



        if ($request->has('start-date')) {
            $start_date = $request->input('start-date');

            if (strtotime($start_date) === false) {
                return redirect()->back();
            }
            $lists = $lists->where('purchase_date', '>=', $start_date);
        }


        if ($request->has('end-date')) {
            $end_date = $request->input('end-date');
            if (strtotime($end_date) === false) {
                return redirect()->back();
            }
            $lists = $lists->where('purchase_date', '<=', $request->input('end-date'));
            $start_date = $request->input('start-date');
            $end_date = $request->input('end-date');
        }
        if (!empty($request->restaurant_id)) {
            $lists = $lists->where('restaurant_id', $request->restaurant_id);
        }

        $lists = $lists->orderBy('id', 'desc')->get();
        $myData = [];
        $title = '';


        foreach ($lists as $list) {
            $myData[$list->wa_supplier_id]['supplier_id'] =  $list->wa_supplier_id;
            $myData[$list->wa_supplier_id]['supplier_name'] =  ucfirst($list->getSupplier->name);
            foreach ($list->getRelatedGrn as $items) {
                // echo "<pre>";
                // print_r($items->getRelatedInventoryItem);
                // die;

                if (!isset($myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id])) {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['item_description'] = ucfirst($items->item_description);
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['unit'] = ucfirst(@$items->getRelatedInventoryItem->getSupplierUomDetail->title);
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['item_id'] = @$items->getRelatedInventoryItem->wa_inventory_item_id;
                }

                if (!isset($myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['quantity'])) {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['quantity'] = round($items->qty_received, 2);
                } else {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['quantity'] = round(@$items->qty_received + $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['quantity'], 2);
                }

                $invoice_info = json_decode($items->invoice_info);
                $nett = $invoice_info->order_price * $invoice_info->qty;
                $net_price = $nett;
                if ($invoice_info->discount_percent > '0') {
                    $discount_amount = ($invoice_info->discount_percent * $nett) / 100;
                    $nett = $nett - $discount_amount;
                }
                $vat_amount = 0;
                if ($invoice_info->vat_rate > '0') {
                    $vat_amount = ($invoice_info->vat_rate * $nett) / 100;
                }
                if (!isset($myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['vat_amount'])) {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['vat_amount'] = $vat_amount;
                } else {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['vat_amount'] = $vat_amount + $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['vat_amount'];
                }

                if (!isset($myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['nett'])) {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['nett'] = $nett;
                } else {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['nett'] = $nett + $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['nett'];
                }

                $totalForThis = $vat_amount + $nett;
                if (!isset($myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['total_amount'])) {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['total_amount'] = $totalForThis;
                } else {
                    $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['total_amount'] = $totalForThis + $myData[$list->wa_supplier_id]['items'][@$items->getRelatedInventoryItem->wa_inventory_item_id]['total_amount'];
                }
            }
        }
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if (!empty($request->restaurant_id)) {
                $restuarantname = (Restaurant::where('id', $request->restaurant_id)->first()->name) ? Restaurant::where('id', $request->restaurant_id)->first()->name : '';
            } else {
                $restuarantname = "";
            }
            if ($request->has('manage-request') && ($request->input('manage-request') == 'xls' || $request->input('manage-request') == 'pdf')) {
                if ($request->input('manage-request') == 'xls') {
 
                    $fileName = $title.'.xls';
                    return Excel::download(function ($excel) use ($myData, $restuarantname, $title, $start_date, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($myData, $restuarantname, $title, $start_date, $end_date) {
                            $sheet->setOrientation('potrate');
                            $sheet->setWidth(array(
                                'A'     => 17,
                                'B'     =>  15,
                                'C'     =>  17,
                                'D'     =>  17,
                                'E'     =>  17,
                                'F'     =>  17,
                            ));

                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                            $sheet->loadView('admin.inventoryreports.grnexcel', compact('title', 'myData', 'start_date', 'restuarantname', 'end_date')); 
                        });
                    }, $fileName);
               
               


                } elseif ($request->input('manage-request') == 'pdf') {
                    $pdf = PDF::loadView('admin.inventoryreports.grnexcel', compact('title', 'myData', 'start_date', 'end_date'));
                    return $pdf->download('Grn Report.pdf');
                }
            }



            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.inventoryreports.grnreports', compact('title', 'myData', 'restuarantname', 'lists', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function supplierProductReports(Request $request)
    {

        $title = 'Supplier Sales Product Report';
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $myData = [];
        $lists = [];
        $monthRange = 0;
        $selectedMonthArr = [];
        if (isset($permission[$pmodule . '___supplier-product-reports']) || $permission == 'superadmin') {
            if ($request->has('manage-request')) {
                $lists = WaSupplier::with('products');
                $start_date = null;
                $end_date = null;

                if ($request->has('start-date')) {
                    $start_date = $request->input('start-date');
                    //$lists = $lists->where('created_at','>=',$request->input('start-date'));
                }
                if ($request->has('end-date')) {
                    $end_date = $request->input('end-date');
                    //$lists = $lists->where('created_at', '<=', $request->input('end-date'));
                }
                if (!empty($request->supplier_id)) {
                    $lists = $lists->where('id', $request->supplier_id);
                }

                

                $lists = $lists->orderBy('id', 'desc')->get();
                $selectedMonthArr = getMonthsBetweenDates($start_date, $end_date);

                $monthRange = getMonthRangeBetweenDate($start_date, $end_date);


                if ($monthRange > 12) {
                    Session::flash('warning', "You can't select more than 12 months.");
                }
                if($request->input('manage-request') == 'xlspppp'){
                    if(empty($request->supplier_id)){
                        Session::flash('warning', "Please Select Supplier and Try Again.");


                    }else{
                        $exportData = [];
                        $start  = \Carbon\Carbon::parse($start_date)->toDateTimeString();
                        $end  = \Carbon\Carbon::parse($end_date)->toDateTimeString();
                        
                        foreach($lists as $arr){
                            foreach($arr['products'] as $item){
                                $qoh = $item->getstockmoves?->where('wa_inventory_item_id',$item->id,);
                                if (request()->filled('branch') && isset($item->getstockmoves) && !empty($item->getstockmoves)) {
                                    $qoh->where('restaurant_id', request()->branch);
                                }                               
                                
                                    
                            $payload = [
                                'product' => $item->description,
                                'qoh' => $qoh,
                                'qty_sold' =>( $item->getstockmoves?->where('wa_inventory_item_id', $item->id)
                                ->whereRaw('(document_no LIKE "%INV%" OR document_no LIKE "%CS%" OR document_no LIKE "%RTN%") && DATE(created_at) >= "'.$start.'" && DATE(created_at) <= "'.$end.'"')
                                ->sum('qauntity')) * -1,

                            ];
                            $exportData[] = $payload; 

                            }

                        }
                       
                        $excelExport = new GeneralExcelExport(collect($exportData), ['PRODUCT', 'QOH', 'SOLD QTY']);
                        return Excel::download($excelExport, $lists[0]->name."-sales-".$start.$end.".xlsx");
                    }
                    

                }

            }

             if($request->input('manage-request') == 'xls') {
            $view = view('admin.inventoryreports.supplierproductreports_pdf',
            [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'myData' => $myData,
            'lists' => $lists,
            'selectedMonthArr' => $selectedMonthArr,
            'monthRange' => $monthRange,
            'branch' => request()->filled('branch') ? request()->branch:''
            ]);
            return Excel::download(new CommonReportDataExport($view), 'Supplier Product Report'. '.xlsx');
           }


            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.inventoryreports.supplierproductreports', compact('title', 'myData', 'lists', 'model', 'breadcum', 'selectedMonthArr', 'monthRange'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    // start
    public function supplierProductReports2(Request $request)
    {


        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $myData = [];
        $title = '';
        $lists = [];
        $monthRange = 0;
        $selectedMonthArr = [];
        if (isset($permission[$pmodule . '___supplier-product-reports']) || $permission == 'superadmin') {
            if ($request->has('manage-request')) {
                $lists = WaSupplier::with('products');
                $start_date = null;
                $end_date = null;

                if ($request->has('start-date')) {
                    $start_date = $request->input('start-date');
                }
                if ($request->has('end-date')) {
                    $end_date = $request->input('end-date');
                }
                if (!empty($request->supplier_id)) {
                    $lists = $lists->where('id', $request->supplier_id);
                }

                $lists = $lists->orderBy('id', 'desc')->get();
                $selectedMonthArr = getMonthsBetweenDates($start_date, $end_date);
                $monthRange = getMonthRangeBetweenDate($start_date, $end_date);
                // pre($monthRange);

                if ($monthRange > 12) {
                    Session::flash('warning', "You can't select more than 12 months.");
                }
            }
            $breadcum = ['Reports' => '', 'GRN Reports' => ''];
            return view('admin.inventoryreports.supplierproductreports2', compact('title', 'myData', 'lists', 'model', 'breadcum', 'selectedMonthArr', 'monthRange'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    // End

    public function exportTransferGeneral(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if ($request->isMethod('post')) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];
                if (!empty($start_date)) {
                    $conditions[] = ['transfer_date', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['transfer_date', '<=', $end_date];
                }

                if (!empty($request->restaurant_id)) {
                    $conditions[] = ['restaurant_id', '=', $request->restaurant_id];
                }
                $result = WaInventoryLocationTransfer::with('getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail', 'fromStoreDetail', 'toStoreDetail');

                if (!empty($conditions)) {
                    $result = $result->where($conditions);
                }
                $result = $result->get();

                // dd($result);
                if (!empty($request->restaurant_id)) {
                    $restuarantname = (Restaurant::where('id', $request->restaurant_id)->first()->name) ? Restaurant::where('id', $request->restaurant_id)->first()->name : '';
                } else {
                    $restuarantname = "";
                }
                $print = 0;
                $manage_request = $request->manage_request;
                if ($request->manage_request == 'xls') {
                   
                    $fileName = $title . '.xls';
                    return Excel::download(function ($excel) use ($result, $restuarantname, $title, $start_date, $end_date, $manage_request) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($result, $restuarantname, $title, $start_date, $end_date, $manage_request) {
                            $sheet->setOrientation('potrate');
                            $sheet->setWidth(array(
                                'A'     => 17,
                                'B'     =>  15,
                                'C'     =>  17,
                                'D'     =>  17,
                                'E'     =>  17,
                                'F'     =>  17,
                            ));
                            //echo $start_date.'vd'.$end_date;die;
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                            $is_pdf = 0;
                            $sheet->loadView('admin.inventoryreports.export_transfer_general_excel', compact('title', 'result', 'start_date', 'restuarantname', 'end_date', 'manage_request'));
                        });
                    }, $fileName);
                } elseif ($request->manage_request == 'pdf') {
                    $pdf = PDF::loadView('admin.inventoryreports.export_transfer_general_excel', compact('title', 'result', 'start_date', 'end_date', 'manage_request'));
                    return $pdf->download('Transfer General.pdf');
                } elseif ($request->manage_request == 'filter') {
                    //echo "<pre>"; print_r($result); die;
                    $breadcum = ['Reports' => '', 'Transfer General' => ''];
                    return view('admin.inventoryreports.export_transfer_general', compact('title', 'model', 'result',  'restuarantname', 'manage_request', 'start_date', 'end_date', 'breadcum'));
                } else {
                    $print = 1;
                    return view('admin.inventoryreports.export_transfer_general_excel', compact('title', 'result', 'start_date', 'end_date', 'print', 'manage_request'));
                }
            }
            $breadcum = ['Reports' => '', 'Transfer General' => ''];
            return view('admin.inventoryreports.export_transfer_general', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function exportInternalRequisitions(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if ($request->isMethod('post')) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];
                $conditions[] = ['status', '=', 'COMPLETED'];
                if (!empty($start_date)) {
                    $conditions[] = ['requisition_date', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['requisition_date', '<=', $end_date];
                }

                if (!empty($request->restaurant_id)) {
                    $conditions[] = ['restaurant_id', '=', $request->restaurant_id];
                }
                $result = WaInternalRequisition::with('getRelatedItem.getInventoryItemDetail.getUnitOfMeausureDetail', 'getRelatedFromLocationAndStore', 'getRelatedToLocationAndStore');

                if (!empty($conditions)) {
                    $result = $result->where($conditions);
                }
                $result = $result->get();
                $data_formatted = [];
                // echo $request->manage_request; die;
                if (!empty($request->restaurant_id)) {
                    $restuarantname = (Restaurant::where('id', $request->restaurant_id)->first()->name) ? Restaurant::where('id', $request->restaurant_id)->first()->name : '';
                } else {
                    $restuarantname = "";
                }
                foreach ($result as $key => $row) {

                    $items = $row->getRelatedItem;
                    foreach ($items as $item_key => $item_row) {
                        $wa_inventory_item_id = $item_row->wa_inventory_item_id;
                        $data_formatted[$row->wa_location_and_store_id . '-' . $row->to_store_id][$wa_inventory_item_id][] = $item_row;
                    }
                }

                $manage_request = $request->manage_request;
                if ($request->manage_request == 'xls') {
                    
                    $fileName = $title . '.xls';

                        return Excel::download(function ($excel) use ($data_formatted, $title, $start_date, $end_date, $restuarantname, $manage_request) {
                            $excel->sheet('Excel sheet', function ($sheet) use ($data_formatted, $title, $start_date, $end_date, $restuarantname, $manage_request) {
                                $sheet->setOrientation('potrate');
                                $sheet->setWidth(array(
                                    'A'     => 17,
                                    'B'     =>  15,
                                    'C'     =>  17,
                                    'D'     =>  17,
                                    'E'     =>  17,
                                    'F'     =>  17,
                                ));
                                //echo $start_date.'vd'.$end_date;die;
                                $sheet->setFontSize(10);
                                $sheet->setFontFamily('ARIAL');
                                $is_pdf = 0;
                                $sheet->loadView('admin.inventoryreports.export_internal_requisitions_excel', compact('title', 'data_formatted', 'restuarantname', 'start_date', 'end_date', 'manage_request'));
                            });
                        }, $fileName);

                } elseif ($request->manage_request == 'pdf') {
                    $pdf = PDF::loadView('admin.inventoryreports.export_internal_requisitions_excel', compact('title', 'data_formatted', 'start_date', 'end_date', 'manage_request','restuarantname'));
                    return $pdf->download('Transfer Movement Report.pdf');
                } elseif ($request->manage_request == 'filter') {
                    $breadcum = ['Reports' => '', 'Transfer General' => ''];
                    return view('admin.inventoryreports.export_internal_requisitions', compact('title', 'model', 'data_formatted', 'breadcum', 'restuarantname', 'start_date', 'end_date', 'manage_request'));
                } else {
                    $print = 1;
                    return view('admin.inventoryreports.export_internal_requisitions_excel', compact('title', 'data_formatted', 'restuarantname', 'start_date', 'end_date', 'print', 'manage_request'));
                }
            }
            $breadcum = ['Reports' => '', 'Transfer General' => ''];
            return view('admin.inventoryreports.export_internal_requisitions', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function locationWiseMovement(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            //echo "<pre>"; print_r($request->all()); die;
            $data = [];
            if ($request->isMethod('post')) {

                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];
                if (!empty($start_date)) {
                    $conditions[] = ['created_at', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['created_at', '<=', $end_date];
                }
                $result = WaStockMove::with('getInventoryItemDetail.getUnitOfMeausureDetail');

                if (!empty($conditions)) {


                    $result = $result->where($conditions);
                }

                $result = $result->groupBy('wa_inventory_item_id')->get();
                if ($request->manage_request == 'xls') {
                    $data = WaInventoryCategory::with('getinventoryitems', 'getinventoryitems.getstockmoves', 'getinventoryitems.unitofmeasures')
                        ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $start_date, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->whereBetween('created_at', [$start_date, $end_date]);
                            if ($request->has('location')) {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        });
                    if ($request->input('wa_inventory_category_id')[0] != "-1") {
                        $data->whereIn('id', $request->input('wa_inventory_category_id'));
                    }
                    $data  =  $data->get()->toArray();
                    // dd($data);
                    $storeBiseQty = [];
                    $purchaseBiseQty = [];
                    $transfersBiseQty = [];
                    $issuesBiseQty = [];
                    $salesBiseQty = [];
                    $summrytype = 2;
                    // $restuarantname = "res";
                    // $locationname = "locationname";
                    $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
                    $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
                    foreach ($data as $k => $val) {
                        $get_quantity = 0;
                        foreach ($val['getinventoryitems'] as $key => $value) {
                            /** qty start **/
                            $get_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code']);
                            if ($request->input('location') != "") {
                                $get_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_quantity->where('created_at', '<', $start_date);
                            $get_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_quantity =  $get_quantity->sum('qauntity');

                            $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;

                            /** qty end **/

                            /** qty purchases **/

                            $get_purchases_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_purchase_order_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_purchases_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_purchases_quantity->whereBetween('created_at', [$start_date, $end_date]);
                            $get_purchases_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_purchases_quantity =  $get_purchases_quantity->sum('qauntity');

                            $purchaseBiseQty[$value['getstockmoves']['id']] = $get_purchases_quantity;

                            /** qty purchases **/

                            /** qty transfersBiseQty **/

                            $get_transfers_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_inventory_location_transfer_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_transfers_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_transfers_quantity->whereBetween('created_at', [$start_date, $end_date]);

                            $get_transfers_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_transfers_quantity =  $get_transfers_quantity->sum('qauntity');

                            $transfersBiseQty[$value['getstockmoves']['id']] = $get_transfers_quantity;

                            /** qty transfersBiseQty **/


                            /** qty wa_internal_requisition **/

                            $get_issues_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_internal_requisition_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_issues_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_issues_quantity->whereBetween('created_at', [$start_date, $end_date]);

                            $get_issues_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_issues_quantity =  $get_issues_quantity->sum('qauntity');

                            $issuesBiseQty[$value['getstockmoves']['id']] = $get_issues_quantity;

                            /** qty wa_internal_requisition **/

                            /** qty ordered_item_id **/

                            $get_sales_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('ordered_item_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_sales_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_sales_quantity->whereBetween('created_at', [$start_date, $end_date]);

                            $get_sales_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_sales_quantity =  $get_sales_quantity->sum('qauntity');

                            $salesBiseQty[$value['getstockmoves']['id']] = $get_sales_quantity;

                            /** qty ordered_item_id **/
                        }
                    }


                  
                    $fileName = $title. ".xls";
                    return Excel::download(function ($excel) use ($data, $storeBiseQty, $purchaseBiseQty, $transfersBiseQty, $issuesBiseQty, $salesBiseQty, $restuarantname, $locationname, $title, $start_date, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $storeBiseQty, $purchaseBiseQty, $restuarantname, $locationname, $transfersBiseQty, $issuesBiseQty, $salesBiseQty, $end_date) {
                            $sheet->setOrientation('potrate');
                            $sheet->setWidth(array(
                                'A'     => 17,
                                'B'     =>  15,
                                'C'     =>  17,
                                'D'     =>  17,
                                'E'     =>  17,
                                'F'     =>  17,
                            ));
                            //echo $start_date.'vd'.$end_date;die;
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                            $is_pdf = 0;
                            $sheet->loadView('admin.inventoryreports.location_wise_movement_excel', compact('title', 'data', 'start_date',  'salesBiseQty', 'issuesBiseQty', 'purchaseBiseQty', 'storeBiseQty', 'transfersBiseQty', 'locationname', 'restuarantname', 'start_date', 'end_date'));                        });
                    }, $fileName);
                } elseif ($request->manage_request == 'pdf') {
                    $data = WaInventoryCategory::with('getinventoryitems', 'getinventoryitems.getstockmoves', 'getinventoryitems.unitofmeasures')
                        ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $start_date, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->whereBetween('created_at', [$start_date, $end_date]);
                            if ($request->has('location')) {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        });
                    if ($request->input('wa_inventory_category_id')[0] != "-1") {
                        $data->whereIn('id', $request->input('wa_inventory_category_id'));
                    }
                    $data  =  $data->get()->toArray();
                    // dd($data);
                    $storeBiseQty = [];
                    $purchaseBiseQty = [];
                    $transfersBiseQty = [];
                    $issuesBiseQty = [];
                    $salesBiseQty = [];
                    $summrytype = 2;
                    // $restuarantname = "res";
                    // $locationname = "locationname";
                    $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
                    $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
                    foreach ($data as $k => $val) {
                        $get_quantity = 0;
                        foreach ($val['getinventoryitems'] as $key => $value) {
                            /** qty start **/
                            $get_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code']);
                            if ($request->input('location') != "") {
                                $get_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_quantity->where('created_at', '<', $start_date);
                            $get_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_quantity =  $get_quantity->sum('qauntity');

                            $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;

                            /** qty end **/

                            /** qty purchases **/

                            $get_purchases_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_purchase_order_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_purchases_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_purchases_quantity->whereBetween('created_at', [$start_date, $end_date]);
                            $get_purchases_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_purchases_quantity =  $get_purchases_quantity->sum('qauntity');

                            $purchaseBiseQty[$value['getstockmoves']['id']] = $get_purchases_quantity;

                            /** qty purchases **/

                            /** qty transfersBiseQty **/

                            $get_transfers_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_inventory_location_transfer_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_transfers_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_transfers_quantity->where('created_at', [$start_date, $end_date]);

                            $get_transfers_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_transfers_quantity =  $get_transfers_quantity->sum('qauntity');

                            $transfersBiseQty[$value['getstockmoves']['id']] = $get_transfers_quantity;

                            /** qty transfersBiseQty **/


                            /** qty wa_internal_requisition **/

                            $get_issues_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_internal_requisition_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_issues_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_issues_quantity->whereBetween('created_at', [$start_date, $end_date]);

                            $get_issues_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_issues_quantity =  $get_issues_quantity->sum('qauntity');

                            $issuesBiseQty[$value['getstockmoves']['id']] = $get_issues_quantity;

                            /** qty wa_internal_requisition **/

                            /** qty ordered_item_id **/

                            $get_sales_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('ordered_item_id', '!=', null);
                            if ($request->input('location') != "") {
                                $get_sales_quantity->where('wa_location_and_store_id', $request->input('location'));
                            }
                            $get_sales_quantity->whereBetween('created_at', [$start_date, $end_date]);

                            $get_sales_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                            $get_sales_quantity =  $get_sales_quantity->sum('qauntity');

                            $salesBiseQty[$value['getstockmoves']['id']] = $get_sales_quantity;

                            /** qty ordered_item_id **/
                        }
                    }


                    $pdf = PDF::loadView('admin.inventoryreports.location_wise_movement_excel', compact('title', 'data', 'start_date',  'salesBiseQty', 'issuesBiseQty', 'purchaseBiseQty', 'storeBiseQty', 'transfersBiseQty', 'locationname', 'restuarantname', 'end_date'));
                    return $pdf->download('Location wise Product Movement.pdf');
                } else {
                    if ($request->input('filter') == 'filter') {
                        //    DB::enableQueryLog(); // Enable query log
                        // echo $request->input('location'); die("d");
                        //echo "<pre>"; print_r($request->input('wa_inventory_category_id')[0]); die;
                        $data = WaInventoryCategory::with('getinventoryitems', 'getinventoryitems.getstockmoves', 'getinventoryitems.unitofmeasures')
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $start_date, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->whereBetween('created_at', [$start_date, $end_date]);
                                if ($request->has('location')) {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();
                        // dd($data);
                        $storeBiseQty = [];
                        $purchaseBiseQty = [];
                        $transfersBiseQty = [];
                        $issuesBiseQty = [];
                        $salesBiseQty = [];
                        $summrytype = 2;
                        // $restuarantname = "res";
                        // $locationname = "locationname";
                        $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
                        $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            //  echo  $start_date; die;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                /** qty start **/


                                $get_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code']);
                                if ($request->input('location') != "") {
                                    $get_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_quantity->where('created_at', '<', $end_date);
                                //                                $get_quantity->where('created_at', '<', $start_date);
                                $get_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_quantity =  $get_quantity->sum('qauntity');
                                //                                echo $get_quantity; die;


                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;

                                /** qty end **/

                                /** qty purchases **/
                                //  DB::enableQueryLog();
                                $get_purchases_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_purchase_order_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_purchases_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_purchases_quantity->whereBetween('created_at',  [$start_date, $end_date]);
                                $get_purchases_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_purchases_quantity =  $get_purchases_quantity->sum('qauntity');
                                // dd(DB::getQueryLog());
                                $purchaseBiseQty[$value['getstockmoves']['id']] = $get_purchases_quantity;

                                /** qty purchases **/

                                /** qty transfersBiseQty **/

                                $get_transfers_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_inventory_location_transfer_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_transfers_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_transfers_quantity->whereBetween('created_at', [$start_date, $end_date]);

                                $get_transfers_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_transfers_quantity =  $get_transfers_quantity->sum('qauntity');

                                $transfersBiseQty[$value['getstockmoves']['id']] = $get_transfers_quantity;

                                /** qty transfersBiseQty **/


                                /** qty wa_internal_requisition **/

                                $get_issues_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_internal_requisition_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_issues_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_issues_quantity->whereBetween('created_at', [$start_date, $end_date]);

                                $get_issues_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_issues_quantity =  $get_issues_quantity->sum('qauntity');

                                $issuesBiseQty[$value['getstockmoves']['id']] = $get_issues_quantity;

                                /** qty wa_internal_requisition **/

                                /** qty ordered_item_id **/

                                $get_sales_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('ordered_item_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_sales_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_sales_quantity->whereBetween('created_at', [$start_date, $end_date]);

                                $get_sales_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_sales_quantity =  $get_sales_quantity->sum('qauntity');

                                $salesBiseQty[$value['getstockmoves']['id']] = $get_sales_quantity;

                                /** qty ordered_item_id **/
                            }
                        }
                    }

                    return view('admin.inventoryreports.location_wise_movement', compact('title', 'result', 'data', 'summrytype', 'salesBiseQty', 'issuesBiseQty', 'purchaseBiseQty', 'storeBiseQty', 'transfersBiseQty', 'locationname', 'restuarantname', 'start_date', 'end_date'));
                }
            }
            $breadcum = ['Reports' => '', ' Location wise Product Movement' => ''];
            return view('admin.inventoryreports.location_wise_movement', compact('title', 'data', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function inventoryMomentReport(Request $request)
    {

        

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $list = [];
        // echo "<pre>"; print_r($request->all()); die;
        //        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
        $start_date = $request->get('start-date');
        $end_date = $request->get('end-date');

        if (!empty($start_date)) {
            $list = WaStockMove::select('*');
            if (!empty($start_date)) {
                $list->whereDate('created_at', '>=', $start_date);
            }
            if (!empty($end_date)) {
                $list->whereDate('created_at', '<=', $end_date);
            }
            if ($request->has('location_id') && $request->get('location_id')) {
                $list->where('wa_location_and_store_id', $request->get('location_id'));
            }
            if ($request->has('stock_code') && $request->get('stock_code') != "") {
                $list->where('stock_id_code', $request->get('stock_code'));
            }
            $list = $list->get();
        }

        //echo "<pre>"; print_r($list); die;
        $breadcum = ['Reports' => '', ' Location wise Product Movement' => ''];
        return view('admin.inventoryreports.inventory_moment_report', compact('title', 'model', 'breadcum', 'list'));
        /*
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
*/
    }

    public function getDeliveryNoteReport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        $list = [];
        // echo "<pre>"; print_r($request->all()); die;
        //        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
        $start_date = $request->get('start-date');
        $end_date = $request->get('end-date');

        //            if (!empty($start_date)) {
        $list = WaInternalRequisition::select('*');
        if (!empty($start_date)) {
            $list->whereDate('created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $list->whereDate('created_at', '<=', $end_date);
        }
        $list = $list->get();
        //			} 

        //echo "<pre>"; print_r($list); die;
        $breadcum = ['Reports' => '', ' Delivery Note Report' => ''];
        return view('admin.inventoryreports.delivery_note_report', compact('title', 'model', 'breadcum', 'list'));
        /*
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
*/
    }


    public function grnSummary(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if ($request->isMethod('post')) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];
                if (!empty($start_date)) {
                    $conditions[] = ['purchase_date', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['purchase_date', '<=', $end_date];
                }
                $result = WaPurchaseOrder::where('status', 'COMPLETED');

                if (!empty($conditions)) {
                    $result = $result->where($conditions);
                }
                $result = $result->orderBy('id', 'desc')->get();
                if ($request->manage_request == 'xls') {
                     
                    $fileName = $title.'.xls';
                    return Excel::download(function ($excel) use ($result, $title, $start_date, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($result, $title, $start_date, $end_date) {
                            $sheet->setOrientation('potrate');

                            $sheet->setFontSize(10);
                            //$sheet->autoSize();
                            $sheet->setFontFamily('Calibri');
                            $sheet->loadView('admin.inventoryreports.grn_summary_excel', compact('title', 'result', 'start_date', 'end_date'));
                        });
                    }, $fileName);
                } elseif ($request->manage_request == 'pdf') {
                    $pdf = 1;
                    $pdf = PDF::loadView('admin.inventoryreports.grn_summary_excel', compact('title', 'result', 'start_date', 'end_date', 'pdf'));
                    return $pdf->download('GRN Summary Report.pdf');
                } elseif ($request->manage_request == 'filter') {
                    $breadcum = ['Reports' => '', ' GRN Summary' => ''];
                    // echo "<pre>";
                    // print_r($result);
                    // die; 
                    return view('admin.inventoryreports.grn_summary', compact('title', 'result', 'model', 'breadcum', 'start_date', 'end_date'));
                } else {
                    return view('admin.inventoryreports.grn_summary_excel', compact('title', 'result', 'start_date', 'end_date'));
                }
            }
            $breadcum = ['Reports' => '', ' Location wise Product Movement' => ''];
            return view('admin.inventoryreports.grn_summary', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function salesReport(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            if ($request->isMethod('post')) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];

                if (!empty($start_date)) {
                    $conditions[] = ['created_at', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['created_at', '<=', $end_date];
                }
                $result = Order::where('status', 'COMPLETED');
                if (!empty($conditions)) {
                    $result = $result->where($conditions);
                }
                $result = $result->orderBy('id', 'desc')->get();
                if ($request->manage_request == 'xls') {
                   
                    $fileName = $title.'.xls';
                    return Excel::download(function ($excel) use ($result, $title, $start_date, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($result, $title, $start_date, $end_date) {
                            $sheet->setOrientation('potrate');
                             
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                            $sheet->loadView('admin.inventoryreports.sales_report_excel', compact('title', 'result', 'start_date', 'end_date'));
                         });
                    }, $fileName);
                } elseif ($request->manage_request == 'pdf') {
                    $pdf = PDF::loadView('admin.inventoryreports.sales_report_excel', compact('title', 'result', 'start_date', 'end_date'));
                    return $pdf->download('GRN Summary Report.pdf');
                } else {
                    return view('admin.inventoryreports.grn_summary_excel', compact('title', 'result', 'start_date', 'end_date'));
                }
            }
            $breadcum = ['Reports' => '', ' Location wise Product Movement' => ''];
            return view('admin.inventoryreports.sales_report', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function inventoryValuationReport(Request $request)
    {
        $title = 'Inventory valuation report';
        $model = $this->model;
        $pmodule = $this->pmodule;
        $storeBiseQty = [];
        //echo "<pre>"; print_r(getLoggeduserProfile()); die;
        $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
        $permission =  $this->mypermissionsforAModule();
        $locationname = "";
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $data = [];
            $summrytype =  '1';
            $locationid = '';
            if ($request->isMethod('post')) {
                $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
                $form_data = $request->all();
                //   echo "<pre>"; print_r($form_data); die;
                $start_date = ''; //$form_data['start-date'];
                $summrytype =  $form_data['show_type'];
                $end_date = $form_data['end-date'] ? $form_data['end-date'] : date('Y-m-d H:i:s');
                //                $data = $this->inventoryValuationReportData($form_data);
                $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                    if ($request->wa_unit_of_measure_id) {
                        $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                    }
                }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                    $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                    $q->where('created_at', '<=', $end_date);
                    if ($request->input('location') != "") {
                        $q->where('wa_location_and_store_id', $request->input('location'));
                        $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                    }
                }, 'getinventoryitems.unitofmeasures'])
                    ->whereHas('getinventoryitems', function ($q) use ($request) {
                        if ($request->wa_unit_of_measure_id) {
                            $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                        }
                    })
                    ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                        $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                        $q->where('created_at', '<=', $end_date);
                        if ($request->input('location') != "") {
                            $q->where('wa_location_and_store_id', $request->input('location'));
                            $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                        }
                    });
                if ($request->input('wa_inventory_category_id')[0] != "-1") {
                    $data->whereIn('id', $request->input('wa_inventory_category_id'));
                }
                $data  =  $data->get()->toArray();
                $storeBiseQty = [];
                $stockMoves =  WaStockMove::where('created_at', '<=', $end_date)->where(function ($e) use ($request) {
                    if ($request->input('location') != "") {
                        $e->where('wa_location_and_store_id', $request->input('location'));
                    }
                })->get();
                foreach ($data as $k => $val) {
                    $get_quantity = 0;

                    foreach ($val['getinventoryitems'] as $key => $value) {
                        $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                        $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                    }
                }
                if (empty($form_data['show_details'])) {
                    //					echo "<pre> ==".$summrytype; print_r($data); die;
                    if ($request->input('manage-request') == 'xls') {
                        $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->where('created_at', '<=', $end_date);
                            if ($request->input('location') != "") {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        }, 'getinventoryitems.unitofmeasures'])->whereHas('getinventoryitems', function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        })
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->where('created_at', '<=', $end_date);
                                if ($request->input('location') != "") {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();
                        $storeBiseQty = [];
                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                            }
                        }

 
                        $fileName = $title . '.xls';

                        return Excel::download(function ($excel) use ($data, $title, $summrytype, $locationname, $restuarantname, $storeBiseQty, $start_date, $end_date) {
                            $excel->sheet('Excel sheet', function ($sheet) use ($data, $summrytype, $locationname, $restuarantname, $title, $start_date, $storeBiseQty, $end_date) {
                                $sheet->setOrientation('potrate');
                                $sheet->setWidth(array(
                                    'A' => 25,
                                    'B' => 25,
                                    'C' => 25,
                                    'D' => 25,
                                    'E' => 25,
                                    'F' => 25,
                                ));
                                //echo $start_date.'vd'.$end_date;die;
                                $sheet->setFontSize(10);
                                $sheet->setFontFamily('ARIAL');
                                $is_pdf = 0;
                                $sheet->loadView('admin.inventoryreports.inventory_valuation_report', compact('title', 'data', 'summrytype', 'restuarantname', 'storeBiseQty', 'locationname', 'start_date', 'end_date', 'is_pdf')); // Added by me.
                            });
                        }, $fileName);
                    } else if ($request->input('manage-request') == 'pdf') {
                        $is_pdf = 1;

                        $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->where('created_at', '<=', $end_date);
                            if ($request->input('location') != "") {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        }, 'getinventoryitems.unitofmeasures'])->whereHas('getinventoryitems', function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        })
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->where('created_at', '<=', $end_date);
                                if ($request->input('location') != "") {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();

                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                            }
                        }
                        //                echo "<pre>"; print_r($data); die;
                        $pdf = PDF::loadView('admin.inventoryreports.inventory_valuation_report', compact('title', 'data', 'start_date',  'summrytype', 'storeBiseQty', 'restuarantname', 'locationname', 'end_date', 'is_pdf'));
                        return $pdf->download('inventoryValuationReport.pdf');
                    }
                } else {

                    if ($request->input('manage-request') == 'xls') {
                        $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->where('created_at', '<=', $end_date);
                            if ($request->input('location') != "") {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        }, 'getinventoryitems.unitofmeasures'])->whereHas('getinventoryitems', function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        })
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->where('created_at', '<=', $end_date);
                                if ($request->input('location') != "") {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();

                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                            }
                        }
                       
                        $fileName = $title . '.xls';

                        return Excel::download(function ($excel) use ($data, $title,  $summrytype, $locationname, $restuarantname, $start_date, $end_date) {
                            $excel->sheet('Excel sheet', function ($sheet) use ($data, $summrytype, $locationname, $restuarantname, $title, $start_date, $end_date) {
                                $sheet->setOrientation('potrate');
                                $sheet->setWidth(array(
                                    'A' => 20,
                                    'B' => 20,
                                    'C' => 20,
                                    'D' => 20,
                                    'E' => 20,
                                    'F' => 20,
                                    'G' => 20,
                                    'H' => 20,
                                    'I' => 20,
                                    'J' => 20,
                                ));
                                //echo $start_date.'vd'.$end_date;die;
                                $sheet->setFontSize(10);
                                $sheet->setFontFamily('ARIAL');
                                $is_pdf = 0;
                                $sheet->loadView('admin.inventoryreports.inventory_valuation_report_all', compact('title', 'data', 'start_date', 'summrytype', 'storeBiseQty', 'restuarantname', 'locationname', 'end_date', 'is_pdf'));
                            });
                        }, $fileName);
                    } else if ($request->input('manage-request') == 'pdf') {
                        $is_pdf = 1;
                        $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->where('created_at', '<=', $end_date);
                            if ($request->input('location') != "") {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        }, 'getinventoryitems.unitofmeasures'])->whereHas('getinventoryitems', function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        })
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->where('created_at', '<=', $end_date);
                                if ($request->input('location') != "") {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();

                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                            }
                        }

                        $pdf = PDF::loadView('admin.inventoryreports.inventory_valuation_report_all', compact('title', 'data', 'start_date', 'end_date', 'summrytype', 'storeBiseQty', 'restuarantname', 'locationname', 'is_pdf'));
                        $pdf->setPaper('A4', 'landscape');
                        return $pdf->download('inventoryValuationReport.pdf');
                    }
                }

                if ($request->input('filter') == 'filter') {
                    //    DB::enableQueryLog(); // Enable query log

                    // echo $request->input('location'); die("d");
                    //echo "<pre>"; print_r($request->input('wa_inventory_category_id')[0]); die;
                    $data = WaInventoryCategory::with(['getinventoryitems' => function ($q) use ($request) {
                        if ($request->wa_unit_of_measure_id) {
                            $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                        }
                    }, 'getinventoryitems.getstockmoves' => function ($q) use ($request, $end_date) {
                        $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                        $q->where('created_at', '<=', $end_date);
                        if ($request->input('location') != "") {
                            $q->where('wa_location_and_store_id', $request->input('location'));
                            $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                        }
                    }, 'getinventoryitems.unitofmeasures'])
                        ->whereHas('getinventoryitems', function ($q) use ($request) {
                            if ($request->wa_unit_of_measure_id) {
                                $q->where('wa_unit_of_measure_id', $request->wa_unit_of_measure_id);
                            }
                        })
                        ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $end_date) {
                            $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                            $q->where('created_at', '<=', $end_date);
                            if ($request->input('location') != "") {
                                $q->where('wa_location_and_store_id', $request->input('location'));
                                $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                            }
                        });
                    if ($request->input('wa_inventory_category_id')[0] != "-1") {
                        $data->whereIn('id', $request->input('wa_inventory_category_id'));
                    }
                    $data  =  $data->get()->toArray();

                    foreach ($data as $k => $val) {
                        $get_quantity = 0;
                        foreach ($val['getinventoryitems'] as $key => $value) {
                            $get_quantity = @$stockMoves->where('stock_id_code', $value['stock_id_code'])->sum('qauntity');

                            $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                        }
                    }
                }
                $locationid = $request->input('location');
            }

            $breadcum = ['Reports' => '', $title => ''];
            return view('admin.inventoryreports.inventory_valuation', compact('request', 'title', 'locationid', 'data', 'model', 'breadcum', 'restuarantname', 'storeBiseQty', 'summrytype', 'locationname'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function suggestedOrderReport(Request $request)
    {
        $title = 'Suggested order report';
        $model = $this->model;
        $pmodule = $this->pmodule;
        $storeBiseQty = [];
        //echo "<pre>"; print_r(getLoggeduserProfile()); die;
        $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
        $permission =  $this->mypermissionsforAModule();
        $locationname = "";
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $data = [];
            $summrytype =  '2';
            $locationid = '';
            //             if ($request->isMethod('post')) {
            $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
            $form_data = $request->all();
            $start_date = ''; //$form_data['start-date'];
            $summrytype =  2;
            $end_date = ""; //$form_data['end-date'];


            $data = WaInventoryCategory::with('getinventoryitemshowroomstock', 'getinventoryitemshowroomstock.getstockmoves', 'getinventoryitemshowroomstock.unitofmeasures')
                ->whereHas('getinventoryitemshowroomstock.getstockmoves', function ($q) use ($request, $end_date) {
                    $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                    //                        $q->where('created_at', '>=', $end_date);
                    if ($request->input('location') != "") {
                        $q->where('wa_location_and_store_id', $request->input('location'));
                        $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                    }
                });

            $data  =  $data->get()->toArray();

            foreach ($data as $k => $val) {
                $get_quantity = 0;
                foreach ($val['getinventoryitemshowroomstock'] as $key => $value) {
                    $get_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code']);
                    if ($request->input('location') != "") {
                        $get_quantity->where('wa_location_and_store_id', $request->input('location'));
                    }
                    // $get_quantity->where('created_at', '<=', $end_date);

                    $get_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                    $get_quantity =  $get_quantity->sum('qauntity');

                    $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;
                }
            }
            $pdf = PDF::loadView('admin.inventoryreports.suggested_order_report', compact('title', 'data', 'start_date', 'end_date', 'summrytype', 'storeBiseQty', 'restuarantname', 'locationname',));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('suggested_order_report.pdf');
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    protected function inventoryValuationReportData($form_data)
    {
        if ($form_data['show_type'] == 1) {
            $inventory_catory_arr = [];
            $result = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
                ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

            // if (!empty($form_data['start-date'])) {
            //     $result = $result->where('wa_stock_moves.created_at', '>=', $form_data['start-date']);
            // }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_stock_moves.created_at', '<=', $form_data['end-date']);
            }
            if ($form_data['location']) {
                $result = $result->where('wa_stock_moves.wa_location_and_store_id', '=', $form_data['location']);
            }

            if (!empty($form_data['wa_inventory_category_id'])) {
                if (!in_array('-1', $form_data['wa_inventory_category_id'])) {
                    $inventory_catory_arr = $form_data['wa_inventory_category_id'];
                    $result = $result->whereIn('wa_inventory_categories.id', $inventory_catory_arr);
                }
            }


            $result = $result->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_inventory_items.standard_cost) as standard_cost_sum'), 'wa_inventory_items.wa_inventory_category_id')->groupBy('wa_inventory_items.wa_inventory_category_id')->get();
            return $result;
        } else {
            $result = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
                ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

            // if (!empty($form_data['start-date'])) {
            //     $result = $result->where('wa_stock_moves.created_at', '>=', $form_data['start-date']);
            // }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_stock_moves.created_at', '<=', $form_data['end-date']);
            }
            if ($form_data['location']) {
                $result = $result->where('wa_stock_moves.wa_location_and_store_id', '=', $form_data['location']);
            }

            if (!empty($form_data['wa_inventory_category_id'])) {
                if (!in_array('-1', $form_data['wa_inventory_category_id'])) {
                    $inventory_catory_arr = $form_data['wa_inventory_category_id'];
                    $result = $result->whereIn('wa_inventory_categories.id', $inventory_catory_arr);
                }
            }
            $result = $result->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), 'wa_inventory_items.*')->groupBy('wa_inventory_items.id')->get();
            $data = [];
            foreach ($result as $key => $item) {
                if (isset($item->getInventoryCategoryDetail->id)) {
                    $category_data = $item->getInventoryCategoryDetail;
                    $data[$item->getInventoryCategoryDetail->id]['category'] = $category_data;
                    $data[$item->getInventoryCategoryDetail->id][] = $item;
                }
            }
            return $data;
        }
    }
}
