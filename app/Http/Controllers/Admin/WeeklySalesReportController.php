<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\DailySalesExport;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaRouteCustomer;
use App\SalesmanShift;
use Maatwebsite\Excel\Facades\Excel;

class WeeklySalesReportController extends Controller
{
    
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'route-weekly-sales-report';
        $this->base_route = 'route-reports';
        $this->resource_folder = 'admin.route_reports';
        $this->base_title = 'Weekly Sales Controller';
    }

    public function generate(Request $request)
    {
        $title = $this->base_title;
        $breadcum = ['Route Reports' => '', 'Weekly Sales' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $branches = Restaurant::select('id', 'name')->get();
        $routes = Route::select('id', 'route_name')->get();

        $allRoutes = Route::where('is_physical_route', 1);
        if ($request->branch) {
            $allRoutes = $allRoutes->where('restaurant_id', $request->branch);
        }

        if ($request->route) {
            $allRoutes = $allRoutes->where('id', $request->route);

        }

        $allRoutes = $allRoutes->get();

        $data = [];
        $startDate = \Carbon\Carbon::now()->toDateString();
        $endDate = \Carbon\Carbon::now()->toDateString();
        if ($request->startDate) {
            $startDate = \Carbon\Carbon::parse($request->startDate)->toDateString();
        }

        if ($request->endDate){
            $endDate = \Carbon\Carbon::parse($request->endDate)->toDateString();

        }
        $noOfDays = \Carbon\Carbon::parse($request->startDate)->diffInDays(\Carbon\Carbon::parse($request->endDate)) +1;
        
        foreach ($allRoutes as $route) {
            $routeShift = SalesmanShift::where('route_id', $route->id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();
            //Logic for number of days
            // $noOfDays =  count(explode(',', $route->order_taking_days));
            $tonnageValue  = 0;
            $tonnageTarget  = $route->tonnage_target * $noOfDays;
            $salesValue = 0;
            $salesTarget = $route->sales_target * $noOfDays;
            $ctnValue = 0;
            $ctnTarget = $route->ctn_target * $noOfDays;
            $dznsValue =  0;
            $dznsTarget = $route->dzn_target  * $noOfDays;
            $totalCustomers = WaRouteCustomer::where('route_id', $route->id)->count() * $noOfDays;
            $metCustomers = 0;
            $unmetCustomers = 0;






            foreach($routeShift as $shift){
                $tonnageValue = $tonnageValue + $shift?->shift_tonnage ?? 0;
                $salesValue = $salesValue + $shift?->shift_total ?? 0;
                $ctnValue = $ctnValue + $shift?->shift_ctns ?? 0;
                $dznsValue = $dznsValue + $shift?->shift_dzns ?? 0;
                // $totalCustomers = $totalCustomers + $shift?->shiftCustomers()?->count() ?? 0;
                $metCustomers = $metCustomers + $shift?->shiftCustomers()?->where('order_taken', 1)->count() ?? 0;
                $unmetCustomers = $unmetCustomers + $shift?->shiftCustomers()?->where('order_taken', 0)->count() ?? 0;
                
               



            }
            $entry = [
                'route' => $route->route_name,
                'salesman' => $route->salesman()?->name,
                // 'freq' => count(explode(',', $route->order_taking_days)),
                'tonnage' => [
                    'value' => number_format($tonnageValue ?? 0,2),
                    'target' => number_format($tonnageTarget ?? 0,2),
                    'percentage' => $this->computePercentage($tonnageValue ?? 0, $tonnageTarget)
                ],
                'sales' => [
                    'value' => number_format($salesValue ?? 0, 2),
                    'target' => number_format($salesTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($salesValue ?? 0, $salesTarget)
                ],
                'ctns' => [
                    'value' => number_format($ctnValue ?? 0, 2),
                    'target' => number_format($ctnTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($ctnValue ?? 0, $ctnTarget)
                ],
                'dzns' => [
                    'value' => number_format($dznsValue ?? 0, 2),
                    'target' => number_format($dznsTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($dznsValue ?? 0, $dznsTarget)
                ],
                'custs'=>[
                    'total'=>$totalCustomers,
                    'met'=>$metCustomers,
                    'unmet'=>$unmetCustomers
                ],
            ];
            $data[] = $entry;

         

        }

        return view("$this->resource_folder.weekly_sales", compact('title', 'breadcum', 'base_route', 'model', 'branches', 'routes', 'data'));
    }




    private function computePercentage($actual, $target)
    {
        if ($target == 0) {
            return 0;
        }

        return number_format((($actual / $target) * 100), 2);
    }


    

    public function download(Request $request)
    {

      

        $allRoutes = Route::where('is_physical_route', 1);
        if ($request->branch) {
            $allRoutes = $allRoutes->where('restaurant_id', $request->branch);
        }

        if ($request->route) {
            $allRoutes = $allRoutes->where('id', $request->route);

        }

        $allRoutes = $allRoutes->get();

        $data = [];
        $startDate = \Carbon\Carbon::now()->toDateString();
        $endDate = \Carbon\Carbon::now()->toDateString();
        if ($request->startDate) {
            $startDate = \Carbon\Carbon::parse($request->startDate)->toDateString();
        }
        if ($request->endDate){
            $endDate = \Carbon\Carbon::parse($request->endDate)->toDateString();
        }
        


        $noOfDays = \Carbon\Carbon::parse($request->startDate)->diffInDays(\Carbon\Carbon::parse($request->endDate)) +1;

        
        foreach ($allRoutes as $route) {
            $routeShift = SalesmanShift::where('route_id', $route->id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();
            //Logic for number of days
            // $noOfDays =  count(explode(',', $route->order_taking_days));

            $tonnageValue  = 0;
            $tonnageTarget  = $route->tonnage_target * $noOfDays;
            $salesValue = 0;
            $salesTarget = $route->sales_target * $noOfDays;
            $ctnValue = 0;
            $ctnTarget = $route->ctn_target * $noOfDays;
            $dznsValue =  0;
            $dznsTarget = $route->dzn_target  * $noOfDays;
            $totalCustomers = WaRouteCustomer::where('route_id', $route->id)->count() * $noOfDays;
            $metCustomers = 0;
            $unmetCustomers = 0;
            foreach($routeShift as $shift){
                $tonnageValue = $tonnageValue + $shift?->shift_tonnage ?? 0;
                $salesValue = $salesValue + $shift?->shift_total ?? 0;
                $ctnValue = $ctnValue + $shift?->shift_ctns ?? 0;
                $dznsValue = $dznsValue + $shift?->shift_dzns ?? 0;
                $metCustomers = $metCustomers + $shift?->shiftCustomers()?->where('order_taken', 1)->count() ?? 0;
                $unmetCustomers = $unmetCustomers + $shift?->shiftCustomers()?->where('order_taken', 0)->count() ?? 0;

            

            }
            $entry = [
                'route' => $route->route_name,
                'salesman' => $route->salesman()?->name,
                'tonnage' => [
                    'value' => number_format($tonnageValue ?? 0,2),
                    'target' => number_format($tonnageTarget ?? 0,2),
                    'percentage' => $this->computePercentage($tonnageValue ?? 0, $tonnageTarget)
                ],
                'sales' => [
                    'value' => number_format($salesValue ?? 0, 2),
                    'target' => number_format($salesTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($salesValue ?? 0, $salesTarget)
                ],
                'ctns' => [
                    'value' => number_format($ctnValue ?? 0, 2),
                    'target' => number_format($ctnTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($ctnValue ?? 0, $ctnTarget)
                ],
                'dzns' => [
                    'value' => number_format($dznsValue ?? 0, 2),
                    'target' => number_format($dznsTarget ?? 0, 2),
                    'percentage' => $this->computePercentage($dznsValue ?? 0, $dznsTarget)
                ],
                'custs'=>[
                    'total'=>$totalCustomers,
                    'met'=>$metCustomers,
                    'unmet'=>$unmetCustomers
                ],
            ];
            $data[] = $entry;

           
        }



           $data = collect($data)->map(function ($item) {
                $payload = [
                    'route' => $item['route'],
                    'salesman' =>$item['salesman'] ?? 'N/A',
                    'ton' => $item['tonnage_value']  ?? 0,
                    'ton_targ' => $item['tonnage_target'] ?? 0,
                    'ton_per%' =>$item['tonnage_percentage'] ?? 0,
                    'amount' => $item['sales_value'] ?? 0,
                    'amount_targ' =>$item['sales_target'] ?? 0,
                    'amount_per%'=>$item['sales_percentage'] ?? 0,
                    'ctn'=>$item['ctns_value'] ?? 0,
                    'ctn_tar'=>$item['ctns_target'] ?? 0,
                    'ctn_per%'=>$item['ctns_percentage'] ?? 0,
                    'dzn'=>$item['dzns_value'] ?? 0,
                    'dzn_targ'=>$item['dzns_target'] ?? 0,
                    'dzn_per%'=>$item['dzns_percentage'] ?? 0,
                    'cust'=>$item['custs_total'] ?? 0,
                    'met'=>$item['custs_met'] ?? 0,
                    'unmet'=>$item['custs_unmet'] ?? 0,
                ];

                return $payload;
            })->sortBy('route');

        $export = new DailySalesExport($data);
        return Excel::download($export, 'weeklysalesreport'. $startDate.'-'.$endDate.'.xlsx');
        
    }
}
