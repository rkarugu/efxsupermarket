<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Restaurant;
use App\Model\Route;
use App\SalesmanShift;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\DeliverySchedule;
use Carbon\Carbon;
use App\Model\WaRouteCustomer;
use Illuminate\Support\Facades\DB;
use PDF;




class DeliveryReportController extends Controller
{
    // protected $model;
    protected $base_route;
    protected $resource_folder;

    public function __construct()
    {
        // $this->model = "order-taking-schedules";
        $this->base_route = "order-taking-schedules";
        $this->resource_folder = "admin.order_taking_schedules";
    }
    public function index(Request $request)
    {
        $module = "salesmanShift";
        $title = "Salesman Shift";
        $model = "shift-delivery-report";
        $branches = Restaurant::all();
        $routes = Route::all();
        $selectedRouteId = $request->route;
        $selectedBranchId = $request->branch;
        $date1 = $request->get('from');
        $date1 = \Carbon\Carbon::parse($date1)->toDateString();
        $shift = [];
        if($selectedRouteId && $date1){
            $shift = SalesmanShift::with(['orders', 'salesman', 'relatedRoute'])->where('route_id', $selectedRouteId)->whereDate('created_at', '=', $date1)->first(); 
            if (!$shift) {
                return redirect()->back()->withErrors(['message' => 'The selected route did not have a sales shift on the selected date']);
            }
            $schedule = DeliverySchedule::latest()->with(['vehicle', 'driver'])->where('shift_id', $shift->id)->first();
            $branch = Restaurant::find($shift->relatedRoute->restaurant_id)->name;
    
            $shift->date = Carbon::parse($shift->created_at)->toFormattedDateString();
            $shift->invoices = implode(',', DB::table('wa_internal_requisitions')->where('wa_shift_id', $shift->id)->pluck('requisition_no')->toArray());
            $actual_orders = WaInternalRequisition::latest()->where('wa_shift_id', $shift->id);
            $shift->actual_orders = WaInternalRequisition::latest()->where('wa_shift_id', $shift->id)->get()->map(function ($order) {
                    $routeCustomer = WaRouteCustomer::with(['center'])->find($order->wa_route_customer_id);
                    $orderItems = WaInternalRequisitionItem::where('wa_internal_requisition_id', $order->id)->get();
                    $orderTonnage = 0;
                    foreach ($orderItems as $item) {
                        $orderedItemQuantity = $item->quantity;
                        $orderedItemWeight = (WaInventoryItem::find($item->wa_inventory_item_id)?->net_weight) / 1000;
                        $orderedItemTonnage = $orderedItemQuantity * $orderedItemWeight;
                        $orderTonnage = $orderTonnage + $orderedItemTonnage;
                }
                    return [
                        'invoice_id' => $order->requisition_no,
                        'customer_account' => $routeCustomer?->account_number ,
                        'customer_name' => $routeCustomer?->bussiness_name,
                        'location' => $routeCustomer?->center?->name,
                        'balance' => 0,
                        'tonnage' => $orderTonnage,
                        'total' => $order->getOrderTotal(),
                    ];
                });
        }      
        if($request->get('manage-request') == 'download' ){
            $report_name = "{$shift->relatedRoute->route_name}-Delivery-Report";
            $pdf = PDF::loadView('admin.delivery_schedules.delivery_report', compact('report_name', 'schedule', 'shift', 'branch'));
            return $pdf->download($report_name . '.pdf');
        }
      
        $breadcum = [$title => "", 'Listing' => ''];
        return view("admin.sales_delivery_reports.index", compact('model', 'title', 'breadcum','routes', 'branches', 'selectedRouteId', 'selectedBranchId', 'shift'));
    }


}
