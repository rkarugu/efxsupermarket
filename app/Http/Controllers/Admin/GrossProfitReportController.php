<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\FuelLpo;
use App\NewFuelEntry;
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
use Session;
use Excel;
use PDF;

class GrossProfitReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'gross-profit';
        $this->title = 'Reports';
        $this->pmodule = 'gross-profit';
        // ini_set('memory_limit', '4096M');
        // set_time_limit(30000000); // Extends to 5 minutes.
    }


    // public function inventoryValuationReport(Request $request)
    // {   
    //     print_r($model); die;
    //     $user = getLoggeduserProfile();
    //     $pmodule = $this->pmodule;

    //     $permission =  $this->mypermissionsforAModule();


    //          $title='Gross Profit Report';
    //          $form_data = $request->all();

    //         $end_date = (isset($request->to_date)) ?  $request->to_date : date('Y-m-d H:i:s');
    //         $start_date = (isset($request->start_date)) ?  $request->start_date : date('Y-m-d H:i:s');
    //         $invoice=$request->invoice;
    //         $data = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
    //             ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

    //             if($request->invoice =='CS-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%CS-%");
    //             }
    //             if($request->invoice =='INV-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%INV-%");
    //             }
    //             if($request->invoice =='RTN-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%RTN-%");
    //             }

    //             $data = $data->whereDate('wa_stock_moves.created_at', $end_date);

    //             if($request->location && $request->location != '-1'){
    //             $data = $data->where('wa_stock_moves.wa_location_and_store_id', '=', $request->input('location'));
    //             }

    //             //dd($request->input('location'));  
    //             //location_name

    //             $location_name='';

    //             $location=WaLocationAndStore::where('id',$request->input('location'))->first();
    //             $location_name=@$location->location_name;


    //              $data = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), 'wa_inventory_items.wa_inventory_category_id','wa_inventory_categories.category_description')->groupBy('wa_inventory_items.wa_inventory_category_id')->get();
    //         $price = 0;
    //         $stockprice = 0;

    //             if($request->manage == 'pdf'){

    //               $pdf = PDF::loadView('admin.gross_profit_reports.inventory_valuation_pdf', compact('data','title','storeBiseQty','location_name','end_date','start_date','invoice'));
    //               return $pdf->download('Gross-Profit-Summary.pdf');

    //             }

    //         return view('admin.gross_profit_reports.inventory_valuation', compact('data','title','storeBiseQty','location_name'));


    // }

    public function inventoryValuationReport(Request $request)
    {
        // dd($request->all());
        $user = getLoggeduserProfile();
        $pmodule = $this->pmodule;

        $permission = $this->mypermissionsforAModule();


        $title = 'Gross Profit Report';
        $form_data = $request->all();
        $storeBiseQty = null;

        $end_date = (isset($request->to_date)) ? $request->to_date : date('Y-m-d H:i:s');
        $start_date = (isset($request->start_date)) ? $request->start_date : date('Y-m-d H:i:s');
        $invoice = $request->invoice;
        $data = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
            ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

        if ($request->invoice == 'CS-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%CS-%");
        }
        if ($request->invoice == 'INV-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%INV-%");
        }
        if ($request->invoice == 'RTN-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%RTN-%");
        }
        if ($request->invoice == 'All-') {

        }

        $data = $data->whereDate('wa_stock_moves.created_at', '>=', $start_date);
        $data = $data->whereDate('wa_stock_moves.created_at', '<=', $end_date);
        // $data = $data->whereDate('wa_stock_moves.created_at', $end_date);

        if ($request->location && $request->location != '-1') {
            $data = $data->where('wa_stock_moves.wa_location_and_store_id', '=', $request->input('location'));
        }

        //dd($request->input('location'));
        //location_name

        $location_name = '';

        $location = WaLocationAndStore::where('id', $request->input('location'))->first();
        $location_name = @$location->location_name;


        $data = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('wa_stock_moves.total_cost'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), DB::raw('SUM(wa_stock_moves.total_cost) as total_cost_sum'), 'wa_inventory_items.wa_inventory_category_id', 'wa_inventory_categories.category_description')
            // ->groupBy('wa_inventory_items.wa_inventory_category_id')
            ->groupBy('wa_inventory_items.id')
            ->get();
        $data = $data->groupBy('category_description');

        $result = [];

        foreach ($data as $category => $categoryData) {
            $categoryTotalQuantity = 0;
            $categoryStandardCostSum = 0;
            $categoryPriceSum = 0;
            $categoryTotalCostSum = 0;

            foreach ($categoryData as $item) {
                $categoryTotalQuantity += $item->total_quantity;
                $categoryStandardCostSum += $item->standard_cost_sum;
                $categoryPriceSum += abs($item->price_sum);
                $categoryTotalCostSum += abs($item->total_cost_sum);
            }

            $result[] = [
                'category_description' => $category,
                'total_quantity' => $categoryTotalQuantity,
                'standard_cost_sum' => $categoryStandardCostSum,
                'price_sum' => $categoryPriceSum,
                'total_cost_sum' => $categoryTotalCostSum,
            ];
        }
        $data = json_decode(json_encode($result, true));
//    echo "<pre>"; print_r($data); die;
        $price = 0;
        $stockprice = 0;

        if ($request->manage == 'pdf') {

            $pdf = PDF::loadView('admin.gross_profit_reports.inventory_valuation_pdf', compact('data', 'title', 'storeBiseQty', 'location_name', 'end_date', 'start_date', 'invoice'));
            return $pdf->download('Gross-Profit-Summary.pdf');

        }

        $model = "gross-profit-report";
        return view('admin.gross_profit_reports.inventory_valuation', compact('data', 'title', 'storeBiseQty', 'location_name', 'model', 'invoice'));


    }


    // public function inventoryValuationDetailedReport(Request $request)
    // {   
    //     $user = getLoggeduserProfile();
    //     $pmodule = $this->pmodule;

    //     $permission =  $this->mypermissionsforAModule();


    //          $title='Gross Profit Detailed Report';
    //          $form_data = $request->all();

    //         $end_date = (isset($request->to_date)) ?  $request->to_date : date('Y-m-d H:i:s');

    //         $data = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
    //             ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

    //             if($request->invoice =='CS-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%CS-%");
    //             }
    //             if($request->invoice =='INV-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%INV-%");
    //             }
    //             if($request->invoice =='RTN-'){                
    //             $data=$data->where('wa_stock_moves.document_no', 'LIKE',"%RTN-%");
    //             }

    //             $data = $data->whereDate('wa_stock_moves.created_at', $end_date);
    //             //$data = $data->where('wa_inventory_items.wa_inventory_category_id', 1);

    //             if($request->location && $request->location != '-1'){
    //             $data = $data->where('wa_stock_moves.wa_location_and_store_id', '=', $request->input('location'));
    //             }


    //             $data = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), 'wa_inventory_items.wa_inventory_category_id','wa_inventory_categories.category_description')->groupBy('wa_inventory_items.wa_inventory_category_id')->get();

    //             if($data->count() > 0){
    //                 foreach ($data as $key => $item) {
    //                     $inventory_cat_id=$item->wa_inventory_category_id;
    //                      $sub_items = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
    //                          ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id')->where('wa_inventory_items.wa_inventory_category_id',$inventory_cat_id);

    //                          $sub_items = $sub_items->whereDate('wa_stock_moves.created_at',$end_date)->where('wa_stock_moves.document_no', 'LIKE',"%CS-%")->get()->toArray();
    //                          $data[$key]['sub_items']=$sub_items;
    //                 }

    //             }
    //            //$data_check = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), 'wa_inventory_items.id','wa_stock_moves.wa_inventory_item_id')->groupBy('wa_stock_moves.stock_id_code')->get();
    //             /*dd($data_check);*/
    //         $price = 0;
    //         $stockprice = 0;

    //             if($request->manage == 'pdf'){

    //               $pdf = PDF::loadView('admin.gross_profit_detailed_reports.inventory_valuation_pdf', compact('data','title','storeBiseQty'));
    //               return $pdf->download('Gross-Profit-Summary.pdf');

    //             }

    //         return view('admin.gross_profit_detailed_reports.inventory_valuation', compact('data','title','storeBiseQty'));


    // }

    public function inventoryValuationDetailedReport(Request $request)
    {
        $user = getLoggeduserProfile();
        $pmodule = $this->pmodule;

        $permission = $this->mypermissionsforAModule();


        $title = 'Gross Profit Detailed Report';
        $form_data = $request->all();

        $end_date = (isset($request->to_date)) ? $request->to_date : date('Y-m-d H:i:s');

        $data = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
            ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');

        if ($request->invoice == 'CS-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%CS-%");
        }
        if ($request->invoice == 'INV-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%INV-%");
        }
        if ($request->invoice == 'RTN-') {
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%RTN-%");
        }

        $data = $data->whereDate('wa_stock_moves.created_at', $end_date);
        //$data = $data->where('wa_inventory_items.wa_inventory_category_id', 1);

        if ($request->location && $request->location != '-1') {
            $data = $data->where('wa_stock_moves.wa_location_and_store_id', '=', $request->input('location'));
        }


        $data = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), 'wa_inventory_items.wa_inventory_category_id', 'wa_inventory_categories.category_description')
            ->groupBy('wa_inventory_items.wa_inventory_category_id')
            ->get();

        if ($data->count() > 0) {
            foreach ($data as $key => $item) {
                $inventory_cat_id = $item->wa_inventory_category_id;
                $sub_items = WaInventoryItem::leftJoin('wa_inventory_categories', 'wa_inventory_items.wa_inventory_category_id', '=', 'wa_inventory_categories.id')
                    ->leftJoin('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id')->where('wa_inventory_items.wa_inventory_category_id', $inventory_cat_id);

                $sub_items = $sub_items->whereDate('wa_stock_moves.created_at', $end_date)->where('wa_stock_moves.document_no', 'LIKE', "%CS-%")

                    // ->select(DB::raw('SUM(wa_stock_moves.price) as grouping_price'))
                    ->groupBy('wa_inventory_items.title')
                    ->get()->toArray();

                $data[$key]['sub_items'] = $sub_items;
            }

        }
        //$data_check = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'), DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'), DB::raw('SUM(wa_stock_moves.price) as price_sum'), 'wa_inventory_items.id','wa_stock_moves.wa_inventory_item_id')->groupBy('wa_stock_moves.stock_id_code')->get();
        /*dd($data_check);*/
        $price = 0;
        $stockprice = 0;

        if ($request->manage == 'pdf') {

            $pdf = PDF::loadView('admin.gross_profit_detailed_reports.inventory_valuation_pdf', compact('data', 'title', 'storeBiseQty'));
            return $pdf->download('Gross-Profit-Summary.pdf');

        }

        $model = "gross-profit-detail-report";

        return view('admin.gross_profit_detailed_reports.inventory_valuation', compact('data', 'title', 'storeBiseQty', 'model'));


    }

    public function routeProfitibilityReport(Request $request)
    {
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        $permission = $this->mypermissionsforAModule();
        if (!isset($my_permissions['route-profitibility-report___view']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

        $title = 'Route Profitability Report';
        $stores = getAllsalesmanList();
        $data = [];
        if ($request->manage) {
            $data = WaInventoryItem::join('wa_stock_moves', 'wa_inventory_items.id', '=', 'wa_stock_moves.wa_inventory_item_id');
            $data = $data->where('wa_stock_moves.document_no', 'LIKE', "%INV-%");

//            if ($request->salesman_id && $request->salesman_id != '-1') {
//                $data = $data->where('wa_stock_moves.wa_location_and_store_id', $request->input('salesman_id'));
//            }

            if ($request->shift_id) {
                $data = $data->where('wa_stock_moves.shift_id', $request->shift_id);
            }

            $data = $data->select(DB::raw('SUM(wa_stock_moves.qauntity) as total_quantity'),
                DB::raw('SUM(wa_stock_moves.standard_cost * wa_stock_moves.qauntity) as standard_cost_sum'),
                DB::raw('SUM(wa_inventory_items.selling_price * wa_stock_moves.qauntity * -1) as price_sum'),
                'wa_inventory_items.title')
                ->groupBy('wa_inventory_items.id')
                ->get();
        }

        if ($request->manage == 'pdf') {
            $schedule = DeliverySchedule::with(['route', 'vehicle'])->find($request->schedule_id);
            $fuelLpos = FuelLpo::latest()->where('vehicle_id', $schedule->vehicle_id)->where('fueled', true)->first();
            $fuelEntry = NewFuelEntry::where('fuel_lpo_id')->first();
            $telematicsData = [
                'vehicle_registration_number' => $schedule->vehicle->license_plate_number,
                'distance_formatted' => "0.14 Km",
                'fuel' => 2,
                'fuel_formatted' => '2 L',
                'fuel_cost' => 200.00,
                'fuel_cost_formatted' => "KES. " . number_format(200.00, 2),
                'total_fuel_cost' => 400,
                'total_fuel_cost_formatted' => "KES. " . number_format(400, 2),
            ];


//            $telematics = new \App\Telematics();
//            $report = $telematics->getGeneralInformationReport();
//            if ($report && isset($report['url'])) {
//                $json = file_get_contents($report['url']);
//                $json = json_decode($json, true);
//                $metaData = $json['items'][0]['table']['totals'];
//
//                $fuelConsumed = $metaData['fuel_consumption_list'][0]['value'];
//                $fuelConsumedRawValue = (float)(str_replace(' L', '', $fuelConsumed));
//
//                $telematicsData['distance_formatted'] = $metaData['distance'];
//                $telematicsData['fuel_formatted'] = $fuelConsumed;
//                $telematicsData['fuel'] = $fuelConsumedRawValue;
//                $telematicsData['total_fuel_cost'] = $fuelConsumedRawValue * $telematicsData['fuel_cost'];
//                $telematicsData['total_fuel_cost_formatted'] = "KES. " . number_format($telematicsData['total_fuel_cost'], 2);
//            }

            $location_name = $request->salesman_id && $request->salesman_id != '-1' ? @$stores[$request->salesman_id] : "All";
            $pdf = PDF::loadView('admin.gross_profit_detailed_reports.route_profitibility_report_pdf', compact('data', 'title', 'location_name', 'telematicsData'));
            return $pdf->download('Route-Profitability-Report-' . $location_name . '.pdf');

        }

        $model = "route-profitibility-report";

        return view('admin.gross_profit_detailed_reports.route_profitibility_report', compact('data', 'title', 'model', 'stores'));


    }
}
