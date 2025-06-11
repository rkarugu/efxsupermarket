<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InventoryExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\Restaurant;
use App\Model\WaLocationAndStore;
use App\Model\WaStockFamilyGroup;
use App\Model\WaSupplier;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;

class PurchaseReportsController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'purchase-orders';
        $this->title = 'Purchase Orders';
        $this->pmodule = 'purchase-orders';
    }

    public function purchasesByStoreLocation(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $rmodel = 'purchases-by-store-location';
        $permission = $this->mypermissionsforAModule();

        $breadcum = ['Purchases' => '', 'Reports' => '', 'Purchases By Store Location' => ''];
        $is_pdf = 0;
        
        if ($request->has('manage-request')) {
            $form_data = $request->all();
            $start_date = $form_data['start-date'];
            $end_date = $form_data['end-date'];
            $data = $this->purchasesByStoreLocationData($form_data);
            if (!empty($request->cost_centre)) {
                $cost_centre_title = (WaLocationAndStore::where('id', $request->cost_centre)->first()->location_name) ? WaLocationAndStore::where('id', $request->cost_centre)->first()->location_name : '';
            } else {
                $cost_centre_title = "";
            }
            if (empty($form_data['show_details'])) {
                 
                if ($request->input('manage-request') == 'xls') {
                     
                   
                    $fileName = $title.'.xls';
 
                  

                   return Excel::download(function ($excel) use ($data, $title, $start_date, $cost_centre_title, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $cost_centre_title, $end_date) {
                            $sheet->setOrientation('portrait');
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
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                    
                            $is_pdf = 0;
                            $sheet->loadView('admin.purchase_reports.purchases_by_store_location_report', compact('title', 'data', 'start_date', 'cost_centre_title', 'end_date', 'is_pdf'));
                        });
                    }, $fileName);

                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_store_location_report', compact('title', 'data', 'cost_centre_title', 'start_date', 'end_date', 'is_pdf'));
                    return $pdf->download('purchasesByStoreLocation.pdf');
                } else if ($request->input('manage-request') == 'filter') {
                    // echo "<pre>";
                    // print_r($data);
                    // die;
                    if (!empty($request->cost_centre)) {
                        $cost_centre_title = (WaLocationAndStore::where('id', $request->cost_centre)->first()->location_name) ? WaLocationAndStore::where('id', $request->cost_centre)->first()->location_name : '';
                    } else {
                        $cost_centre_title = "";
                    }
                    return view('admin.purchase_reports.purchases_by_store_location', compact('title', 'rmodel', 'data', 'start_date', 'cost_centre_title', 'end_date', 'breadcum'));
                }
            } else {
               
                if ($request->input('manage-request') == 'xls') {
                   
                    

                    $fileName = $title.'.xls';
                    
                   return  Excel::download(function ($excel) use ($data, $title, $start_date, $cost_centre_title, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $cost_centre_title, $end_date) {
                            $sheet->setOrientation('portrait');
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
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                    
                            $is_pdf = 0;
                            $sheet->loadView('admin.purchase_reports.purchases_by_store_location_report', compact('title', 'data', 'start_date', 'cost_centre_title', 'end_date', 'is_pdf'));
                        });
                    }, $fileName);
                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_store_location_report_all', compact('title', 'cost_centre_title', 'data', 'start_date', 'end_date', 'is_pdf'));
                    $pdf->setPaper('A4', 'landscape');
                    return $pdf->download('purchasesByStoreLocation.pdf');
                } else if ($request->input('manage-request') == 'filter') {

                    $detail = $data;
                    return view('admin.purchase_reports.purchases_by_store_location', compact('title', 'rmodel', 'detail', 'start_date', 'cost_centre_title', 'end_date', 'breadcum'));
                }
            }
        }

        return view('admin.purchase_reports.purchases_by_store_location', compact('title', 'is_pdf', 'breadcum', 'rmodel'));
    }

    protected function purchasesByStoreLocationData($form_data)
    {
        if (empty($form_data['show_details'])) {
            $result = WaPurchaseOrder::leftJoin('wa_purchase_order_items', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['cost_centre'])) {
                $result = $result->where('wa_purchase_orders.wa_location_and_store_id', $form_data['cost_centre']);
            }

            $result = $result->groupBy('wa_purchase_orders.wa_location_and_store_id');
            $result = $result->select(DB::raw('SUM(wa_purchase_order_items.total_cost_with_vat) as total_cost_with_vat_sum'), 'wa_purchase_orders.wa_location_and_store_id');
            $result = $result->get();
            return $result;
        } else {
            $result = WaPurchaseOrderItem::leftJoin('wa_purchase_orders', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['cost_centre'])) {
                $result = $result->where('wa_purchase_orders.wa_location_and_store_id', $form_data['cost_centre']);
            }

            $result = $result->get();
            $data = [];
            foreach ($result as $key => $row) {
                if (isset($row->getPurchaseOrder->getStoreLocation->id)) {
                    $location = $row->getPurchaseOrder->getStoreLocation;
                    $data[$row->getPurchaseOrder->getStoreLocation->id]['location'] = $location;
                    $data[$row->getPurchaseOrder->getStoreLocation->id]['data'][] = $row;
                }
            }
            return $data;
        }
    }

    public function purchasesByFamilyGroup(Request $request)
    {
        $title = 'Purchases by Family Group';
        $model = $this->model;
        $pmodule = $this->pmodule;

        $rmodel = 'purchases-by-family-group';
        $breadcum = ['Purchases' => '', 'Reports' => '', 'Purchases By Family Group' => ''];

        $is_pdf = 0;
        if ($request->has('manage-request')) {
            $form_data = $request->all();
            //  echo "<pre>"; print_r($form_data); die;
            $start_date = $form_data['start-date'];
            $end_date = $form_data['end-date'];
            $data = $this->purchasesByFamilyGroupData($form_data);
            if (!empty($request->family_group)) {
                $family_group_title = (WaStockFamilyGroup::where('id', $request->family_group)->first()->title) ? WaStockFamilyGroup::where('id', $request->family_group)->first()->title : '';
            } else {
                $family_group_title = "";
            }
            if (empty($form_data['show_details'])) {

                if ($request->input('manage-request') == 'xls') {
                 
                    $fileName = $title.'.xls';

                    return Excel::download(function ($excel) use ($data, $title, $start_date, $family_group_title, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $family_group_title, $end_date) {
                            $sheet->setOrientation('portrait');
                             $sheet->setWidth(array(
                                 'A' => 20,
                                 'B' => 20,
                                 'C' => 20,
                                 'D' => 20,
                                 'E' => 20,
                                 'F' => 20, 
                             ));
                             $sheet->setFontSize(10);
                             $sheet->setFontFamily('ARIAL');
                     
                             $is_pdf = 0;
                             $sheet->loadView('admin.purchase_reports.purchases_by_family_group_report', compact('title', 'family_group_title', 'data', 'start_date', 'end_date', 'is_pdf'));
                         });
                     }, $fileName);
 
                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_family_group_report', compact('title', 'data', 'family_group_title', 'start_date', 'end_date', 'is_pdf'));
                    return $pdf->download('purchasesByFamilyGroup.pdf');
                } else if ($request->input('manage-request') == 'filter') {
                    // echo "<pre>";
                    // print_r($data);
                    // die;

                    // $detail = $data;
                    return view('admin.purchase_reports.purchases_by_family_group', compact('title', 'rmodel', 'data', 'start_date', 'family_group_title', 'end_date', 'breadcum'));
                }
            } else {

                if ($request->input('manage-request') == 'xls') {
                     

                    $fileName = $title.'.xls';

                    return Excel::download(function ($excel) use ($data, $title, $start_date, $family_group_title, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $family_group_title, $end_date) {
                            $sheet->setOrientation('portrait');
                             $sheet->setWidth(array(
                                 'A' => 20,
                                 'B' => 20,
                                 'C' => 20,
                                 'D' => 20,
                                 'E' => 20,
                                 'F' => 20, 
                             ));
                             $sheet->setFontSize(10);
                             $sheet->setFontFamily('ARIAL');
                     
                             $is_pdf = 0;
                             $sheet->loadView('admin.purchase_reports.purchases_by_family_group_report', compact('title', 'family_group_title', 'data', 'start_date', 'end_date', 'is_pdf'));
                         });
                     }, $fileName);


                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_family_group_report_all', compact('title', 'data', 'family_group_title', 'start_date', 'end_date', 'is_pdf'));
                    $pdf->setPaper('A4', 'landscape');
                    return $pdf->download('purchasesByFamilyGroup.pdf');
                } else if ($request->input('manage-request') == 'filter') {
                    // echo "<pre>";
                    // print_r($data);
                    // die;
                    if (!empty($request->family_group)) {
                        $family_group_title = (WaStockFamilyGroup::where('id', $request->family_group)->first()->title) ? WaStockFamilyGroup::where('id', $request->family_group)->first()->title : '';
                    } else {
                        $family_group_title = "";
                    }
                    $detail = $data;
                    return view('admin.purchase_reports.purchases_by_family_group', compact('title', 'rmodel', 'detail', 'start_date', 'family_group_title', 'end_date', 'breadcum'));
                }
            }
        }
        return view('admin.purchase_reports.purchases_by_family_group', compact('title', 'rmodel', 'breadcum'));
    }

    protected function purchasesByFamilyGroupData($form_data)
    {
        if (empty($form_data['show_details'])) {
            $result = WaPurchaseOrder::leftJoin('wa_purchase_order_items', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->leftJoin('wa_inventory_items', 'wa_purchase_order_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
                ->leftJoin('wa_stock_family_groups', 'wa_inventory_categories.wa_stock_family_group_id', '=', 'wa_stock_family_groups.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['family_group'])) {
                $result = $result->where('wa_stock_family_groups.id', $form_data['family_group']);
            }

            $result = $result->groupBy('wa_stock_family_groups.id');
            $result = $result->select(DB::raw('SUM(wa_purchase_order_items.total_cost_with_vat) as total_cost_with_vat_sum'), 'wa_stock_family_groups.id as family_group', 'wa_purchase_order_items.wa_inventory_item_id');
            $result = $result->get();
            return $result;
        } else {
            $result = WaPurchaseOrderItem::leftJoin('wa_purchase_orders', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->leftJoin('wa_inventory_items', 'wa_purchase_order_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                ->leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
                ->leftJoin('wa_stock_family_groups', 'wa_inventory_categories.wa_stock_family_group_id', '=', 'wa_stock_family_groups.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['family_group'])) {
                $result = $result->where('wa_stock_family_groups.id', $form_data['family_group']);
            }

            $result = $result->get();
            $data = [];
            foreach ($result as $key => $row) {

                if (isset($row->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->id)) {
                    $family_group = $row->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup;
                    $data[$row->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->id]['family_group'] = $family_group;
                    $data[$row->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->id]['data'][] = $row;
                }
            }
            return $data;
        }
    }

    public function purchasesBySupplier(Request $request)
    {

        $title = 'Purchases by Supplier';
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        $rmodel = 'purchases-by-supplier';
        $breadcum = ['Purchases' => '', 'Reports' => '', 'Purchases By Supplier' => ''];

        $is_pdf = 0;
        if ($request->has('manage-request')) {
            $form_data = $request->all();
            // echo "<pre>"; print_r($form_data); die;
            $start_date = $form_data['start-date'];
            $end_date = $form_data['end-date'];
            $data = $this->purchasesBySupplierData($form_data);
            if (!empty($request->supplier)) {
                $suppliername = (WaSupplier::where('id', $request->supplier)->first()->name) ? WaSupplier::where('id', $request->supplier)->first()->name : '';
            } else {
                $suppliername = "";
            }
            if (empty($form_data['show_details'])) {


                if ($request->input('manage-request') == 'xls') {
                  
                    $fileName = $title.'.xls';

                    return Excel::download(function ($excel) use ($data, $title, $start_date, $suppliername, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $suppliername, $end_date) {
                            $sheet->setOrientation('potrate');
                            $sheet->setWidth(array(
                                'A' => 20,
                                'B' => 20,
                                'C' => 20,
                                'D' => 17,
                                'E' => 17,
                                'F' => 17,
                            ));
                            //echo $start_date.'vd'.$end_date;die;
                            $sheet->setFontSize(10);
                            $sheet->setFontFamily('ARIAL');
                            $is_pdf = 0;
                            $sheet->loadView('admin.purchase_reports.purchases_by_supplier_report', compact('title', 'suppliername', 'data', 'start_date', 'end_date', 'is_pdf')); 
                        });
                     }, $fileName);
                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_supplier_report', compact('title', 'data', 'start_date', 'suppliername', 'end_date', 'is_pdf'));
                    return $pdf->download('purchasesBySupplier.pdf');
                } else if ($request->input('manage-request') == 'filter') {
                    // echo "<pre>";
                    // print_r($data);
                    // die;

                    return view('admin.purchase_reports.purchases_by_supplier', compact('title', 'rmodel', 'data', 'start_date', 'suppliername', 'end_date', 'breadcum'));
                }
            } else {
                $data = $this->purchasesBySupplierData($form_data);

                if ($request->input('manage-request') == 'xls') {

                    
                    $fileName = $title.'.xls';

                    return Excel::download(function ($excel) use ($data, $title, $start_date, $suppliername, $end_date) {
                        $excel->sheet('Excel sheet', function ($sheet) use ($data, $title, $start_date, $suppliername, $end_date) {
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
                            $sheet->loadView('admin.purchase_reports.purchases_by_supplier_report', compact('title', 'suppliername', 'data', 'start_date', 'end_date', 'is_pdf')); 
                        });
                     }, $fileName);
                     
                } else if ($request->input('manage-request') == 'pdf') {
                    $is_pdf = 1;
                    $pdf = PDF::loadView('admin.purchase_reports.purchases_by_supplier_report_all', compact('title', 'data', 'suppliername', 'start_date', 'end_date', 'is_pdf'));
                    $pdf->setPaper('A4', 'landscape');
                    return $pdf->download('purchasesBySupplier.pdf');
                } else if ($request->input('manage-request') == 'filter') {
                    // echo "<pre>";
                    // print_r($data);
                    // die;
                    if (!empty($request->supplier)) {
                        $suppliername = (WaSupplier::where('id', $request->supplier)->first()->name) ? WaSupplier::where('id', $request->supplier)->first()->name : '';
                    } else {
                        $suppliername = "";
                    }
                    $detail = $data;
                    return view('admin.purchase_reports.purchases_by_supplier', compact('title', 'rmodel', 'detail', 'start_date', 'suppliername', 'end_date', 'breadcum'));
                }
            }
        }
        return view('admin.purchase_reports.purchases_by_supplier', compact('title', 'rmodel', 'breadcum'));
    }

    protected function purchasesBySupplierData($form_data)
    {
        //  echo "<pre>"; print_r($form_data); die;
        if (empty($form_data['show_details'])) {
            $result = WaPurchaseOrder::leftJoin('wa_purchase_order_items', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['supplier'])) {
                $result = $result->where('wa_purchase_orders.wa_supplier_id', $form_data['supplier']);
            }

            $result = $result->groupBy('wa_purchase_orders.wa_supplier_id');
            $result = $result->select(DB::raw('SUM(wa_purchase_order_items.total_cost_with_vat) as total_cost_with_vat_sum'), 'wa_purchase_orders.wa_supplier_id');
            $result = $result->get();
            return $result;
        } else {
            $result = WaPurchaseOrderItem::leftJoin('wa_purchase_orders', 'wa_purchase_order_items.wa_purchase_order_id', '=', 'wa_purchase_orders.id')
                ->where('wa_purchase_orders.status', 'COMPLETED');
            if (!empty($form_data['start-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '>=', $form_data['start-date']);
            }
            if (!empty($form_data['end-date'])) {
                $result = $result->where('wa_purchase_orders.purchase_date', '<=', $form_data['end-date']);
            }
            if (!empty($form_data['wa_supplier_id'])) {
                $result = $result->where('wa_purchase_orders.wa_supplier_id', $form_data['supplier']);
            }
            if (!empty($form_data['supplier'])) {
                $result = $result->where('wa_purchase_orders.wa_supplier_id', $form_data['supplier']);
            }
            $result = $result->get();
            $data = [];
            foreach ($result as $key => $row) {
                if (isset($row->getPurchaseOrder->getSupplier->id)) {
                    $supplier = $row->getPurchaseOrder->getSupplier;
                    $data[$row->getPurchaseOrder->getSupplier->id]['supplier'] = $supplier;
                    $data[$row->getPurchaseOrder->getSupplier->id]['data'][] = $row;
                }
            }
            return $data;
        }
    }
}
