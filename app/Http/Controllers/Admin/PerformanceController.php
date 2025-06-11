<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Exports\CommonReportDataExport;
use App\Exports\SalesmanPerformanceExport;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaLocationAndStore;
use App\Models\IncentiveSettings;
use App\SalesmanShift;
use App\SalesmanShiftStoreDispatch;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class PerformanceController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'sales-and-receivables-reports';
        $this->title = 'Performance Reports';
        $this->pmodule = 'sales-and-receivables-reports';
        $this->basePath = 'admin.performance_reports';
    }
    public function calculateIncentive($current, $bands)
    {
        $earned = 0;
        usort($bands, function($a, $b) {
            return $b->target <=> $a->target;
        });
        foreach ($bands as $incentive) {
            $target = $incentive->target;
            $reward = $incentive->reward;

            if ($current >= $target) {
                $earned = $reward;
                break;
            }
        }
        return $earned;
    }
    public function calculateReturnIncentive($current, $bands)
    {
        $earned = 0;
        usort($bands, function($a, $b) {
            return $a->target <=> $b->target;
        });
        foreach ($bands as $incentive) {
            $target = $incentive->target;
            $reward = $incentive->reward;

            if ($current <= $target) {
                $earned = $reward;
                break;
            }
        }
        return $earned;
    }

    public function calculateCategorizedTonnageReward($ctnsReward, $dznsReward, $bulkReward){
        if( $ctnsReward = 0 || $dznsReward = 0 || $bulkReward = 0){
            return 0;
        }else{
            return ($ctnsReward + $dznsReward + $bulkReward) / 3;
        }

    }
    public function calculateFuelIncentive($fueledBelowExpectationPercentage, $fueledWithinExpectationPercentage, $bands)
    {
        $earned = 0;
        $found = false;

        foreach ($bands as $band) {
            if ($band->operation === 'less_than' && $fueledBelowExpectationPercentage >= $band->target) {
                $earned = $band->reward;
                $found = true; 
                break;  
            }
        }
    
        if (!$found) {
            foreach ($bands as $band) {
                if ($band->operation === 'equal' && $fueledWithinExpectationPercentage == $band->target) {
                    $earned = $band->reward;
                    break;  
                }
            }
        }
    
        return $earned;
    }
    public function salesmanPerformance(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = Restaurant::all();
        $excelData = null;
        $totals = null;
        if($request->start && $request->end){
            $start = Carbon::parse($request->start)->startOfDay();
            $end =  Carbon::parse($request->end)->endOfDay();
            $achieved_tonnage_subquery = DB::table('wa_internal_requisition_items')
                ->select(
                    DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as sub_query_route_sales'),
                    DB::raw('SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000) as sub_query_achieved_tonnage'),
                    DB::raw('COUNT(DISTINCT CASE WHEN wa_inventory_items.pack_size_id IN (6, 9, 17, 4, 10, 1) THEN CONCAT(DATE(wa_internal_requisition_items.created_at), "-", wa_internal_requisition_items.wa_inventory_item_id) ELSE NULL END) as dzns_tonnage'),
                    DB::raw('COUNT(DISTINCT CASE WHEN wa_inventory_items.pack_size_id = 3 THEN CONCAT(DATE(wa_internal_requisition_items.created_at), "-", wa_internal_requisition_items.wa_inventory_item_id) ELSE NULL END) as ctns_tonnage'),
                    DB::raw('COUNT(DISTINCT CASE WHEN wa_inventory_items.pack_size_id NOT IN (6, 9, 17, 4, 10, 1, 3) THEN CONCAT(DATE(wa_internal_requisition_items.created_at), "-", wa_internal_requisition_items.wa_inventory_item_id) ELSE NULL END) as bulk_tonnage'),
                    'wa_internal_requisitions.route_id',

                )
                ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
                ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_internal_requisition_items.wa_inventory_item_id')
                ->where('wa_internal_requisitions.requisition_no', 'like', 'INV%')
                ->whereBetween('wa_internal_requisitions.created_at', [$start, $end])
                ->groupBy('wa_internal_requisitions.route_id');
            $data = DB::table('routes')
                ->select(
                    'routes.id as route_id',
                    'routes.route_name as route',
                    'routes.tonnage_target',
                    'routes.ctn_target',
                    'routes.dzn_target',
                    'routes.group',
                    'achieved_tonnage_subquery.sub_query_route_sales AS sales',
                    'achieved_tonnage_subquery.sub_query_achieved_tonnage AS achieved_tonnage',
                    'achieved_tonnage_subquery.dzns_tonnage AS dzns_tonnage',
                    'achieved_tonnage_subquery.ctns_tonnage AS ctns_tonnage',
                    'achieved_tonnage_subquery.bulk_tonnage AS bulk_tonnage',
                    DB::raw("(SELECT route_users.name
                        FROM salesman_shifts
                        LEFT JOIN users route_users ON salesman_shifts.salesman_id = route_users.id
                        WHERE salesman_shifts.route_id = routes.id
                            AND salesman_shifts.status IN ('open', 'close', 'not_started')
                            AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                        ORDER BY salesman_shifts.created_at DESC
                        LIMIT 1
                    ) AS salesman"),
                    
                    DB::raw("(SELECT COUNT(DISTINCT DATE(salesman_shifts.created_at)) 
                        FROM salesman_shifts WHERE salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close', 'not_started')
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                    ) AS actual_frequency"),
                    DB::raw("(SELECT COUNT(DISTINCT DATE(salesman_shifts.created_at)) 
                        FROM salesman_shifts WHERE salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close')
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                    ) AS days_with_actual_shifts"),
                
                    DB::raw("(SELECT COUNT(wa_route_customers.id) 
                        FROM wa_route_customers 
                        WHERE wa_route_customers.route_id = routes.id 
                        AND wa_route_customers.deleted_at IS NULL
                    ) AS route_customers_count"),
                    DB::raw("((SELECT COUNT(DISTINCT DATE(salesman_shifts.created_at)) 
                        FROM salesman_shifts WHERE salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close', 'not_started')
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                    ) *
                    (SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers 
                    WHERE wa_route_customers.route_id = routes.id
                    AND wa_route_customers.deleted_at IS NULL
                    )
                    ) AS total_customers"),
                    DB::raw("(SELECT COUNT(DISTINCT CONCAT(salesman_shift_customers.route_customer_id, '-', DATE(salesman_shift_customers.created_at)))
                        FROM salesman_shift_customers
                        LEFT JOIN salesman_shifts ON salesman_shifts.id = salesman_shift_customers.salesman_shift_id
                        WHERE salesman_shift_customers.visited = '1'
                        AND salesman_shifts.route_id = routes.id
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                    ) AS met_customers"),
                    DB::raw("(SELECT COUNT(salesman_shifts.id)
                        FROM salesman_shifts 
                        WHERE salesman_shifts.shift_type = 'onsite'
                        AND salesman_shifts.route_id = routes.id
                        AND salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close')
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                    ) AS full_onsite"),
                    DB::raw("(SELECT COUNT(salesman_shifts.id)
                        FROM salesman_shifts 
                        WHERE salesman_shifts.route_id = routes.id
                        AND salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close')
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                        AND (TIMESTAMPDIFF(HOUR, salesman_shifts.start_time, salesman_shifts.closed_time) <= routes.estimated_shift_time )
                    ) AS shifts_closed_past_time"),
                    DB::raw("(SELECT COUNT(salesman_shifts.id)
                        FROM salesman_shifts 
                        WHERE salesman_shifts.route_id = routes.id
                        AND (salesman_shifts.created_at BETWEEN '$start' AND '$end')
                        AND TIME(salesman_shifts.start_time) <= '06:50:00'
                    ) AS opened_ontime"),
                    DB::raw("(SELECT SUM(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                        FROM wa_inventory_location_transfer_item_returns 
                        LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
                        LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id
                        WHERE wa_inventory_location_transfers.route_id = routes.id 
                        AND wa_inventory_location_transfer_item_returns.status = 'received'
                        AND wa_inventory_location_transfer_item_returns.return_status = '1'
                        AND (wa_inventory_location_transfer_item_returns.updated_at BETWEEN '$start' AND '$end')
                    ) AS returns"),
              
                )->leftJoin('route_user', 'route_user.route_id', 'routes.id')
                ->leftJoin('users', 'users.id', 'route_user.user_id')
                ->leftJoinSub($achieved_tonnage_subquery, 'achieved_tonnage_subquery', 'achieved_tonnage_subquery.route_id', 'routes.id')

                ->where('users.role_id', 4)
                ->whereNot('routes.id', 2)
                ->where('routes.is_physical_route', 1);
            if ($request->branch){
                $data = $data->where('routes.restaurant_id', $request->branch);
            }
            if($request->group){
                $data = $data->where('routes.group', $request->group);
            }
            $data = $data->get();  
            $setIncentives = IncentiveSettings::select('slug','target_reward','group')->get();

            $combinedIncentives = [];
            foreach ($setIncentives->toArray() as $incentive) {
                $combinedIncentives[$incentive['slug']] = json_decode($incentive['target_reward']);
            }
            
            $excelData = [];
            foreach ($data as $record){
                $total_shifts = $record->actual_frequency;
                $total_shifts_started = $record->days_with_actual_shifts;
                $totalShiftsStartedPercentage = $total_shifts > 0 ? ( $total_shifts_started / $total_shifts ) * 100 : 0;
                $hasSales = true;
                $expectedTonnage = ($record->tonnage_target * $record->actual_frequency);

                $expectedCtns = ($record->ctn_target * $record->actual_frequency);
                $expectedDzns = ($record->dzn_target * $record->actual_frequency);

                $achievedTonnagePercentage = $expectedTonnage > 0 ? ($record->achieved_tonnage / $expectedTonnage)*100 : 0;
                $fullOnsiteShiftsPercentage = $total_shifts > 0 ? ($record->full_onsite / $total_shifts)*100 : 0;
                $shiftsOpenedOnTimePercentage = $total_shifts > 0 ? ($record->opened_ontime / $total_shifts)*100 : 0;
                $shiftsClosedPastTimePercentage = $total_shifts > 0 ? ($record->shifts_closed_past_time / $total_shifts)*100 : 0;
                // $ctnTonnagePercent = !$record->achieved_tonnage ? 0: ($record->ctns_tonnage / $record->achieved_tonnage) * 100;
                // $dznTonnagePercent = !$record->achieved_tonnage ? 0 : ($record->dzns_tonnage / $record->achieved_tonnage) * 100;

                $ctnPercent = $expectedCtns > 0 ? ($record->ctns_tonnage / $expectedCtns) * 100 : 0;
                $dznPercent = $expectedCtns > 0 ? ($record->dzns_tonnage / $expectedDzns) * 100 : 0;

                $bulkTonnagePercent = !$record->achieved_tonnage ? 0 : ($record->bulk_tonnage / $record->achieved_tonnage) * 100;
                $metPercentage = !$record->total_customers ? 0: ($record->met_customers / $record->total_customers) * 100;
                $fullOnsightPercentage = !$record->actual_frequency ? 0 : ($record->full_onsite / $record->actual_frequency) * 100;
                $ontimeShiftPercentage = !$record->actual_frequency ? 0 : ($record->opened_ontime / $record->actual_frequency) * 100;
                $ontimeCloseShiftPercentage = !$record->actual_frequency ? 0 : ($record->shifts_closed_past_time / $record->actual_frequency) * 100;
                $expectedTotalReward = $this->calculateIncentive(100, $combinedIncentives['tonnage'])  + $this->calculateIncentive(100, $combinedIncentives['met_customers']) + $this->calculateIncentive(100, $combinedIncentives['onsite']) + $this->calculateIncentive(100, $combinedIncentives['early_shifts']) + $this->calculateIncentive(100, $combinedIncentives['time_management']) +  $this->calculateIncentive(30, $combinedIncentives['cartons']) +  $this->calculateIncentive(30, $combinedIncentives['dozens']) + $this->calculateReturnIncentive(0, $combinedIncentives['returns']);
                $tonnageReward = $this->calculateIncentive($achievedTonnagePercentage, $combinedIncentives['tonnage']);
                $metReward = $this->calculateIncentive($metPercentage, $combinedIncentives['met_customers']);
                $fullyOnsiteReward = $this->calculateIncentive($fullOnsiteShiftsPercentage, $combinedIncentives['onsite']);
                $shiftsOpenedOntimeReward = $this->calculateIncentive($ontimeShiftPercentage, $combinedIncentives['early_shifts']);
                $returnsReward = $record->sales > 0 ? $this->calculateReturnIncentive($record->returns, $combinedIncentives['returns']) : 0;
                $shiftsClosedOnTimeReward = $this->calculateIncentive($ontimeCloseShiftPercentage, $combinedIncentives['time_management']);
                // $categoryTonnageReward = $this->calculateCategorizedTonnageReward($this->calculateIncentive($ctnTonnagePercent, $combinedIncentives['cartons']), $this->calculateIncentive($dznTonnagePercent, $combinedIncentives['dozens']), $this->calculateIncentive($bulkTonnagePercent, $combinedIncentives['bulk']));
                $ctnsReward = $this->calculateIncentive($ctnPercent, $combinedIncentives['cartons']);
                $dznsReward = $this->calculateIncentive($dznPercent, $combinedIncentives['dozens']);

                $payload = [
                    'route_id' => $record->route_id,
                    'route' => $record->route,
                    'group' => $record->group,
                    'salesman' => $record->salesman,
                    'sales' => $record->sales,
                    'shift_tonnage_target' => $record->tonnage_target,
                    'total_shifts' => $total_shifts,
                    'actual_shifts' => $total_shifts_started . '(' .manageAmountFormat($totalShiftsStartedPercentage) . '%)',
                    'expected_tonnage' => $expectedTonnage,
                    'achieved_tonnage' => manageAmountFormat($record->achieved_tonnage) . '('.manageAmountFormat($achievedTonnagePercentage).'%)',
                    'tonnage_reward' => $tonnageReward,
                    'expected_ctns' => $expectedCtns,
                    'expected_dzns' => $expectedDzns,
                    'ctn_tonnage' => $record->ctns_tonnage . ' / '. $expectedCtns . '('.manageAmountFormat($ctnPercent).'%)',
                    'dzn_tonnage' => $record->dzns_tonnage . ' / ' . $expectedDzns . '('.manageAmountFormat($dznPercent).'%)',
                    // 'bulk_tonnage' => manageAmountFormat($record->bulk_tonnage) . '('.manageAmountFormat($bulkTonnagePercent).'%)',
                    'ctns_reward' => $ctnsReward,
                    'dzns_reward' => $dznsReward,
                    // 'category_tonnage_reward' => $categoryTonnageReward,
                    'expected_met' => $record->total_customers,
                    'actual_met' => $record->met_customers,
                    'met_percentage' => manageAmountFormat($metPercentage) .'%',
                    'met_reward' =>  $metReward,
                    'fully_onsite_shifts' => $record->full_onsite . '('.manageAmountFormat($fullOnsiteShiftsPercentage).'%)',
                    'fully_onsite_reward' => $fullyOnsiteReward,
                    'shifts_opened_ontime' => $record->opened_ontime .'(' .manageAmountFormat($shiftsOpenedOnTimePercentage) .'%)',
                    'shifts_opened_ontime_reward' => $shiftsOpenedOntimeReward,
                    'shifts_closed_past_time' => $record->shifts_closed_past_time .'(' .manageAmountFormat($shiftsClosedPastTimePercentage) .'%)',
                    'time_management_reward' => $shiftsClosedOnTimeReward,
                    'returns' => manageAmountFormat($record->returns),
                    'returns_reward' => $returnsReward,
                    'expected_rewards' => $expectedTotalReward,
                    'total_rewards' => $tonnageReward + $ctnsReward + $dznsReward + $metReward + $fullyOnsiteReward + $shiftsOpenedOntimeReward + $returnsReward + $shiftsClosedOnTimeReward,
    
                ];
                $excelData[] = $payload;
            }
            $totals = [
                'sales'=>collect($excelData)->sum('sales'),
                'shift_tonnage_target'=>collect($excelData)->sum('shift_tonnage_target'),
                'expected_shifts'=>collect($excelData)->sum('total_shifts'),
                'actual_shifts'=>$data->sum('days_with_actual_shifts'),
                'expected_tonnage'=>collect($excelData)->sum('expected_tonnage'),
                'achieved_tonnage'=>$data->sum('achieved_tonnage'),
                'tonnage_reward'=>collect($excelData)->sum('tonnage_reward'),
                'ctns_tonnage'=>$data->sum('ctns_tonnage'),
                'dzns_tonnage'=>$data->sum('dzns_tonnage'),
                // 'bulk_tonnage'=>$data->sum('bulk_tonnage'),
                'expected_ctns' => collect($excelData)->sum('expected_ctns'),
                'expected_dzns' => collect($excelData)->sum('expected_dzns'),
                'ctns_reward' => collect($excelData)->sum('ctns_reward'),
                'dzns_reward' => collect($excelData)->sum('dzns_reward'),
                // 'category_tonnage_reward'=>collect($excelData)->sum('category_tonnage_reward'),

                'expected_met'=>$data->sum('expected_met'),
                'actual_met'=>$data->sum('met_customers'),
                'met_reward'=>collect($excelData)->sum('met_reward'),
                'fully_onsite_shifts'=>$data->sum('full_onsite'),
                'fully_onsite_reward'=>collect($excelData)->sum('fully_onsite_reward'),
                'shifts_opened_ontime'=>$data->sum('opened_ontime'),
                'shifts_opened_ontime_reward'=>collect($excelData)->sum('shifts_opened_ontime_reward'),
                'shifts_closed_past_time'=>$data->sum('shifts_closed_past_time '),
                'time_management_reward'=>collect($excelData)->sum('time_management_reward'),
                'returns'=>$data->sum('returns'),
                'returns_reward'=>collect($excelData)->sum('returns_reward'),

            ];
            $totalExpectedRewards = collect($excelData)->sum('expected_rewards');
            $totalActualRewards = collect($excelData)->sum('total_rewards');
            if($request->intent && $request->intent == 'Excel'){
              
                $view = view(
                    'admin.route_performance_reports.salesman',
                    [
                        'from' => $start,
                        'to' => $end,
                        'excelData' => $excelData,
                        'totals' =>$totals,
                    ]
                );
    
                return Excel::download(new CommonReportDataExport($view), 'salesman_performance_reports' . date('Ymdhis') . '.xlsx');
    
            }

        }
        
        if (isset($permission[$pmodule . '___salesman-performance-report']) || $permission == 'superadmin') {
            $breadcum = [$title => route('salesman-performance-report'), 'Salesman' => ''];
            return view('admin.performance_reports.salesman', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'excelData', 'totals'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }
    public function driverPerformance(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = Restaurant::all();
        $excelData = null;
        $totals  = null;
        if($request->start && $request->end){
            $start = Carbon::parse($request->start)->startOfDay();
            $end =  Carbon::parse($request->end)->endOfDay();
        
            $data = DB::table('users')
                ->select(
                    'users.id as user_id',
                    'users.name as driver',
                    DB::raw("(SELECT COUNT(delivery_schedules.id)
                        FROM delivery_schedules
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at  BETWEEN '$start' AND '$end')
                    ) AS driver_shifts"),
                    DB::raw("(SELECT COUNT(delivery_schedules.id)
                        FROM delivery_schedules
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at  BETWEEN '$start' AND '$end')
                        AND (TIME(delivery_schedules.actual_delivery_date) <= '06:00:00')
                    ) AS shifts_started_on_time"),
                    DB::raw("(SELECT COUNT(delivery_schedules.id)
                        FROM delivery_schedules
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at  BETWEEN '$start' AND '$end')
                        AND (TIME(delivery_schedules.finish_time) <= '17:00:00')
                        AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                    ) AS shifts_ended_on_time"),
                    DB::raw("(SELECT COUNT(wa_internal_requisitions.id)
                        FROM delivery_schedules
                        LEFT JOIN salesman_shifts ON salesman_shifts.id = delivery_schedules.shift_id
                        LEFT JOIN wa_internal_requisitions ON salesman_shifts.id = wa_internal_requisitions.wa_shift_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at  BETWEEN '$start' AND '$end')
                    ) AS expected_deliveries"),
                    DB::raw("(SELECT COUNT(wa_internal_requisitions.id)
                        FROM delivery_schedules
                        LEFT JOIN salesman_shifts ON salesman_shifts.id = delivery_schedules.shift_id
                        LEFT JOIN wa_internal_requisitions ON salesman_shifts.id = wa_internal_requisitions.wa_shift_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                        AND wa_internal_requisitions.status  = 'DELIVERED'
                    ) AS actual_deliveries"),
                    DB::raw("(SELECT COUNT(fuel_entries.id)
                        FROM delivery_schedules
                        LEFT JOIN fuel_entries  ON fuel_entries.shift_id = delivery_schedules.id
                        WHERE delivery_schedules.driver_id = users.id
                        AND fuel_entries.shift_type = 'Route Deliveries'
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                        AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                    ) AS total_fuel_entries"),
                    DB::raw("(SELECT COUNT(fuel_entries.id)
                        FROM delivery_schedules
                        LEFT JOIN fuel_entries ON fuel_entries.shift_id = delivery_schedules.id
                        LEFT JOIN routes ON routes.id = delivery_schedules.route_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND fuel_entries.shift_type = 'Route Deliveries'
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                        AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                        AND fuel_entries.actual_fuel_quantity < routes.manual_fuel_estimate
                    ) AS fueled_less_than_expected"),
                    DB::raw("(SELECT COUNT(fuel_entries.id)
                        FROM delivery_schedules
                        LEFT JOIN fuel_entries  ON fuel_entries.shift_id = delivery_schedules.id
                        LEFT JOIN routes ON routes.id = delivery_schedules.route_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND fuel_entries.shift_type = 'Route Deliveries'
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                        AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                        AND fuel_entries.actual_fuel_quantity <= routes.manual_fuel_estimate
                    ) AS fueled_within_expected"),
                    DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                        FROM delivery_schedules
                        LEFT JOIN salesman_shifts ON salesman_shifts.id = delivery_schedules.shift_id
                        LEFT JOIN salesman_shift_store_dispatches ON salesman_shifts.id = salesman_shift_store_dispatches.shift_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                        AND DATE(delivery_schedules.created_at) != DATE(salesman_shift_store_dispatches.dispatch_time)
                    ) AS shifts_dispatched_next_day"),
                    DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                        FROM delivery_schedules
                        LEFT JOIN salesman_shifts ON salesman_shifts.id = delivery_schedules.shift_id
                        LEFT JOIN salesman_shift_store_dispatches ON salesman_shifts.id = salesman_shift_store_dispatches.shift_id
                        WHERE delivery_schedules.driver_id = users.id
                        AND (delivery_schedules.created_at BETWEEN '$start' AND '$end')
                    ) AS total_dispatches"),
                    DB::raw("(SELECT users.name
                        FROM users
                        WHERE vehicles.turn_boy_id = users.id
                        LIMIT  1
                    ) AS turnboy"),
                    )
                ->where('users.role_id', 6)
                ->having('driver_shifts', '>', 0)
                ->leftJoin('vehicles', 'vehicles.driver_id', 'users.id');
            if($request->branch){
                $data = $data->where('users.restaurant_id', $request->branch);
            }
            $data = $data->get();
            $excelData = [];

            $setIncentives = IncentiveSettings::select('slug','target_reward','group')->get();

            $combinedIncentives = [];
            foreach ($setIncentives->toArray() as $incentive) {
                $combinedIncentives[$incentive['slug']] = json_decode($incentive['target_reward']);
            }
            foreach ($data as $record){
                $shiftsStartedOnTimePercentage = $record->driver_shifts > 0 ? ($record->shifts_started_on_time / $record->driver_shifts) * 100 : 0;
                $shiftsEndedOnTimePercentage = $record->driver_shifts > 0 ? ($record->shifts_ended_on_time / $record->driver_shifts) * 100 : 0;
                $systemUsagePercentage = $record->expected_deliveries > 0 ? ($record->actual_deliveries / $record->expected_deliveries) * 100 : 0;
                $fueledBelowExpectationPercentage = $record->total_fuel_entries > 0 ? ($record->fueled_less_than_expected / $record->total_fuel_entries) * 100 : 0;
                $fueledWithinExpectationPercentage = $record->total_fuel_entries > 0 ? ($record->fueled_within_expected / $record->total_fuel_entries) * 100 : 0;
                $dispatchPercentage = $record->total_dispatches > 0 ? ( $record->shifts_dispatched_next_day / $record->total_dispatches) * 100 : 0;
                $startShiftReward = $this->calculateIncentive($shiftsStartedOnTimePercentage, $combinedIncentives['shift_by_6am']);
                $endShiftReward = $this->calculateIncentive($shiftsEndedOnTimePercentage, $combinedIncentives['back_on_time']);
                $dispatchReward = $record->total_dispatches > 0 ? $this->calculateIncentive((100 - $dispatchPercentage), $combinedIncentives['load_prev_day']) : 0;
                $systemUsageReward = $this->calculateIncentive($systemUsagePercentage, $combinedIncentives['system_usage']);
                $fuelingReward = $this->calculateFuelIncentive($fueledBelowExpectationPercentage, $fueledWithinExpectationPercentage, $combinedIncentives['fuel']);
                $expectedRewards = $this->calculateIncentive(100, $combinedIncentives['shift_by_6am']) + $this->calculateIncentive(100, $combinedIncentives['back_on_time']) + $this->calculateIncentive(100, $combinedIncentives['load_prev_day']) + $this->calculateIncentive(100, $combinedIncentives['system_usage']) + $this->calculateFuelIncentive(100, 0, $combinedIncentives['fuel']);

                $payload = [
                    'user_id' => $record->user_id,
                    'driver' => $record->driver,
                    'turnboy' => $record->turnboy,
                    'total_shifts' => $record->driver_shifts,
                    'shifts_started_on_time' => $record->shifts_started_on_time . '('. manageAmountFormat($shiftsStartedOnTimePercentage) .'%)',
                    'start_shift_reward'  => $startShiftReward,
                    'end_shifts_on_time' => $record->shifts_ended_on_time . '(' .manageAmountFormat($shiftsEndedOnTimePercentage).'%)',
                    'end_shift_reward'  =>$endShiftReward,
                    'total_dispatches' => $record->total_dispatches,
                    'store_dispatches_loaded_next_day' => $record->shifts_dispatched_next_day . '(' . manageAmountFormat($dispatchPercentage) . '%)',
                    'dispatch_reward' => $dispatchReward,
                    'expected_deliveries' => $record->expected_deliveries,
                    'actual_deliveries' => $record->actual_deliveries . '('. manageAmountFormat($systemUsagePercentage).'%)',
                    'system_usage_reward' => $systemUsageReward,
                    'total_fuel_entries' => $record->total_fuel_entries,
                    'fueled_below_expected'  => $record->fueled_less_than_expected .'('. manageAmountFormat($fueledBelowExpectationPercentage) .'%)',
                    'fueled_within_expected' => $record->fueled_within_expected . '(' . manageAmountFormat($fueledWithinExpectationPercentage) . '%)' ,
                    'fuelling_reward' => $fuelingReward,
                    'expected_rewards' => $expectedRewards,
                    'total_reward' => $startShiftReward + $endShiftReward + $dispatchReward + $systemUsageReward + $fuelingReward,
                  
                ];
                $excelData[] = $payload;

            }
            $totals = [
                'total_shifts' => $data->sum('total_shifts'),
                'shifts_started_on_time' => $data->sum('shifts_started_on_time'),
                'start_shift_reward' =>collect($excelData)->sum('start_shift_reward'),
                'end_shifts_on_time' => $data->sum('shifts_ended_on_time'),
                'end_shift_reward' => collect($excelData)->sum('end_shift_reward'),
                'total_dispatches' => $data->sum('total_dispatches'),
                'store_dispatches_loaded_next_day' => $data->sum('shifts_dispatched_next_day'),
                'dispatch_reward' => collect($excelData)->sum('dispatch_reward'),
                'expected_deliveries' => $data->sum('expected_deliveries'),
                'actual_deliveries' => $data->sum('actual_deliveries'),
                'system_usage_reward' => collect($excelData)->sum('system_usage_reward'),
                'total_fuel_entries' => $data->sum('total_fuel_entries'),
                'fueled_below_expected' => $data->sum('fueled_less_than_expected'),
                'fueled_within_expected' => $data->sum('fueled_within_expected'),
                'fuelling_reward' => collect($excelData)->sum('fuelling_reward'),
              
            ];
        }
        if($request->intent && $request->intent == 'Excel'){

            $view = view(
                'admin.route_performance_reports.driver',
                [
                    'from' => $start,
                    'to' => $end,
                    'excelData' => $excelData,
                    'totals' => $totals,
                ]
            );

            return Excel::download(new CommonReportDataExport($view), 'driver_performance_reports' . date('Ymdhis') . '.xlsx');
        }
        if (isset($permission[$pmodule . '___driver-performance-report']) || $permission == 'superadmin') {
            $breadcum = [$title => route('driver-performance-report'), 'Driver' => ''];
            return view('admin.performance_reports.driver', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'excelData', 'totals'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }

    }
    
    public function salesmanShiftdetails($routeId, $start, $end)
    {
        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();
        $route_sales_subquery = DB::table('wa_internal_requisition_items')
        ->select(
            DB::raw('SUM(wa_internal_requisition_items.total_cost_with_vat) as sub_query_route_sales'),
            DB::raw('SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000) as sub_query_achieved_tonnage'),
            DB::raw('SUM(CASE WHEN wa_inventory_items.pack_size_id IN (6, 9, 17, 4, 10, 1) THEN (wa_internal_requisition_items.quantity * wa_inventory_items.net_weight) / 1000 ELSE 0 END) as dzns_tonnage'),
            DB::raw('SUM(CASE WHEN wa_inventory_items.pack_size_id = 3 THEN (wa_internal_requisition_items.quantity * wa_inventory_items.net_weight) / 1000 ELSE 0 END) as ctns_tonnage'),
            DB::raw('SUM(CASE WHEN wa_inventory_items.pack_size_id NOT IN (6, 9, 17, 4, 10, 1, 3) THEN (wa_internal_requisition_items.quantity * wa_inventory_items.net_weight) / 1000 ELSE 0 END) as bulk_tonnage'),
            DB::raw('DATE(wa_internal_requisitions.created_at) AS created_at')
            )
        ->leftJoin('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
        ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_internal_requisition_items.wa_inventory_item_id')
       ->where('wa_internal_requisitions.requisition_no', 'like', 'INV%')
        ->whereBetween('wa_internal_requisitions.created_at', [$start, $end])
        ->where('wa_internal_requisitions.route_id', $routeId)
        ->groupBy(DB::raw('DATE(wa_internal_requisitions.created_at)'));
       
        $data = DB::table('salesman_shifts')
            ->select(
                'salesman_shifts.id as salesman_shift_id',
                'salesman_shifts.created_at AS date',
                'salesman_shifts.start_time AS start_time',
                'salesman_shifts.closed_time AS closing_time',
                'routes.tonnage_target',
                'route_sales_subquery.sub_query_route_sales AS sales',
                'route_sales_subquery.sub_query_achieved_tonnage AS achieved_tonnage',
                'route_sales_subquery.dzns_tonnage AS dzns_tonnage',
                'route_sales_subquery.ctns_tonnage AS ctns_tonnage',
                'route_sales_subquery.bulk_tonnage AS bulk_tonnage',

                DB::raw('COUNT(*) AS group_count'), 
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers 
                    LEFT JOIN wa_route_customers ON wa_route_customers.id = salesman_shift_customers.route_customer_id
                    WHERE salesman_shift_customers.salesman_shift_id = salesman_shifts.id
                    AND wa_route_customers.deleted_at IS NULL
                ) AS total_customers"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE salesman_shift_customers.salesman_shift_id = salesman_shifts.id
                    AND salesman_shift_customers.visited = '1'
                ) AS met_customers"),
                DB::raw("(SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id
                    AND wa_route_customers.deleted_at IS NULL
                ) AS expected_customers"),
            
            )
            ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
            ->leftJoinSub($route_sales_subquery, 'route_sales_subquery', function ($join) {
                $join->on(DB::raw('DATE(salesman_shifts.created_at)'), '=', 'route_sales_subquery.created_at');
            })
            ->where('salesman_shifts.route_id', $routeId)
            ->whereBetween('salesman_shifts.created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(salesman_shifts.created_at)'))
            ->get()->map(function ($record){
                $record->sales = (float)$record->sales ;
                $record->met_customer_percentage = manageAmountFormat($record->total_customers > 0 ? ($record->met_customers / $record->total_customers)*100 : 0);
                $record->achieved_tonnage = $record->achieved_tonnage  ?? 0;
                $record->achieved_tonnage_percentage = manageAmountFormat(($record->achieved_tonnage / $record->tonnage_target)*100);
                $record->ctns_tonnage = $record->ctns_tonnage ?? 0;
                $record->ctns_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->ctns_tonnage / $record->achieved_tonnage)*100 : 0);
                $record->dzns_tonnage = $record->dzns_tonnage ?? 0;
                $record->dzns_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->dzns_tonnage / $record->achieved_tonnage)*100 : 0);
                $record->bulk_tonnage = $record->bulk_tonnage ?? 0;
                $record->bulk_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->bulk_tonnage / $record->achieved_tonnage)*100 : 0);
                $record->date = Carbon::parse($record->date)->toDateString();
                $record->start_time = Carbon::parse($record->start_time)->toTimeString();
                $record->closing_time = Carbon::parse($record->closing_time)->toTimeString();
                return $record;
            });

        return response()->json($data);


    }

    public function routeTonnageDetails(Request $request)
    {
        $route_id  =$request->route_id;
        $start = $request->start;
        $end = $request->end;

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();
        $shifts = SalesmanShift::where('route_id', $route_id)
            ->whereIn('status', ['open', 'close'])
            ->whereBetween('created_at', [$start, $end])
            ->get();
        $shift_ids = $shifts->pluck('id');
        $items = DB::table('wa_internal_requisition_items')
            ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->join('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_internal_requisition_items.wa_inventory_item_id')
            ->whereIn('wa_internal_requisitions.wa_shift_id', $shift_ids)
            ->join('salesman_shifts', 'wa_internal_requisitions.wa_shift_id', '=', 'salesman_shifts.id')
            ->join('routes', 'salesman_shifts.route_id', '=', 'routes.id')
            ->selectRaw('
                 routes.tonnage_target, 
                routes.sales_target, 
                routes.ctn_target, 
                routes.dzn_target, 
                routes.route_name, 
                wa_inventory_items.title,
                wa_inventory_items.stock_id_code,
                wa_inventory_items.net_weight,
                wa_inventory_items.selling_price,
                SUM(wa_internal_requisition_items.total_cost_with_vat)   as total_amount_sold, 
                 SUM(wa_internal_requisition_items.quantity) as item_count, 
                SUM(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) as total_weight, 
                CASE 
                    WHEN wa_inventory_items.pack_size_id = 3 THEN "Cartons"
                    WHEN wa_inventory_items.pack_size_id IN (6, 9, 17, 4, 10, 1) THEN "Dozens"
                    ELSE "Bulk"
                END as category
            ')
            ->groupBy('wa_inventory_items.stock_id_code', 'category')
            ->get();
        return view('admin.performance_reports.route_tonnage', compact('title', 'model',  'pmodule', 'permission', 'items'));
    }

    public function metUnmetSummary(Request $request)
    {
        $route_id  =$request->route_id;
        $start = $request->start;
        $end = $request->end;
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $route = Route::find($route_id);
        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();
        $data = DB::table('salesman_shifts')
            ->select(
                'salesman_shifts.id as salesman_shift_id',
                'salesman_shifts.created_at AS date',
                'salesman_shifts.start_time AS start_time',
                'salesman_shifts.closed_time AS closing_time',
                'routes.tonnage_target',
                DB::raw('COUNT(*) AS group_count'), 
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers 
                    LEFT JOIN wa_route_customers ON wa_route_customers.id = salesman_shift_customers.route_customer_id
                    WHERE salesman_shift_customers.salesman_shift_id = salesman_shifts.id
                    AND wa_route_customers.deleted_at IS NULL
                ) AS total_customers"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE salesman_shift_customers.salesman_shift_id = salesman_shifts.id
                    AND salesman_shift_customers.visited = '1'
                ) AS met_customers"),
                DB::raw("(SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers
                WHERE wa_route_customers.route_id = routes.id
                AND wa_route_customers.deleted_at IS NULL
                ) AS expected_customers"),
                // DB::raw("SUM((
                //     SELECT SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000) 
                //             FROM wa_internal_requisition_items LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                //             LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_internal_requisition_items.wa_inventory_item_id
                //             WHERE wa_internal_requisitions.wa_shift_id = salesman_shifts.id
                // )) AS achieved_tonnage"),
                // DB::raw("SUM((
                //     SELECT SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000)
                //         FROM wa_internal_requisition_items
                //         LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                //         LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_internal_requisition_items.wa_inventory_item_id
                //         WHERE wa_internal_requisitions.wa_shift_id = salesman_shifts.id
                //         AND wa_inventory_items.pack_size_id = '3'
                // )) AS ctns_tonnage"),
                // DB::raw("SUM((
                //     SELECT SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000)
                //         FROM wa_internal_requisition_items
                //         LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                //         LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_internal_requisition_items.wa_inventory_item_id
                //         WHERE wa_internal_requisitions.wa_shift_id = salesman_shifts.id
                //         AND wa_inventory_items.pack_size_id IN ('6', '9', '17', '4', '10', '1')                    
                // )) AS dzns_tonnage"),
                // DB::raw("SUM((
                //     SELECT SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight) / 1000)
                //         FROM wa_internal_requisition_items
                //         LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
                //         LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_internal_requisition_items.wa_inventory_item_id
                //         WHERE wa_internal_requisitions.wa_shift_id = salesman_shifts.id
                //         AND wa_inventory_items.pack_size_id NOT IN ('6', '9', '17', '4', '10', '1', '3')
                // )) AS bulk_tonnage"),
            )
            ->leftJoin('routes', 'routes.id', 'salesman_shifts.route_id')
            ->where('salesman_shifts.route_id', $route->id)
            ->whereBetween('salesman_shifts.created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(salesman_shifts.created_at)'))
            ->get()->map(function ($record){
                $record->met_customer_percentage = manageAmountFormat($record->total_customers > 0 ? ($record->met_customers / $record->total_customers)*100 : 0);
                // $record->achieved_tonnage = $record->achieved_tonnage;
                // $record->achieved_tonnage_percentage = manageAmountFormat(($record->achieved_tonnage / $record->tonnage_target)*100);
                // $record->ctns_tonnage = $record->ctns_tonnage ?? 0;
                // $record->ctns_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->ctns_tonnage / $record->achieved_tonnage)*100 : 0);
                // $record->dzns_tonnage = $record->dzns_tonnage ?? 0;
                // $record->dzns_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->dzns_tonnage / $record->achieved_tonnage)*100 : 0);
                // $record->bulk_tonnage = $record->bulk_tonnage ?? 0;
                // $record->bulk_tonnage_percentage = manageAmountFormat($record->achieved_tonnage > 0 ? ($record->bulk_tonnage / $record->achieved_tonnage)*100 : 0);
                $record->date = Carbon::parse($record->date)->toDateString();
                $record->start_time = Carbon::parse($record->start_time)->toTimeString();
                $record->closing_time = Carbon::parse($record->closing_time)->toTimeString();
                return $record;
            });

        return view('admin.performance_reports.met_unmet_summary', compact('title', 'model',  'pmodule', 'permission', 'data', 'route'));
    }

    public function metUnmetSummaryDetails($routeId, $date)
    {
        $met = DB::table('salesman_shift_customers')
            ->select(
                'wa_route_customers.id as wa_route_customer_id',
                'wa_route_customers.name',
                'wa_route_customers.bussiness_name',
                'wa_route_customers.phone',
                'salesman_shift_customers.visited',
                'salesman_shift_customers.order_taken',
                )
            ->leftJoin('wa_route_customers', 'wa_route_customers.id', 'salesman_shift_customers.route_customer_id')
            ->whereDate('salesman_shift_customers.created_at', $date)
            ->where('wa_route_customers.route_id', $routeId)
            ->where('salesman_shift_customers.visited', 1)
            ->whereNull('wa_route_customers.deleted_at')
            ->distinct('wa_route_customers.id')
            ->orderBy('salesman_shift_customers.order_taken', 'desc')
            ->get();
        $unmet = DB::table('salesman_shift_customers')
            ->select(
                'wa_route_customers.id as wa_route_customer_id',
                'wa_route_customers.name',
                'wa_route_customers.bussiness_name',
                'wa_route_customers.phone',
                'salesman_shift_customers.visited',
                'salesman_shift_customers.order_taken',
                )
            ->leftJoin('wa_route_customers', 'wa_route_customers.id', 'salesman_shift_customers.route_customer_id')
            ->whereDate('salesman_shift_customers.created_at', $date)
            ->where('wa_route_customers.route_id', $routeId)
            ->where('salesman_shift_customers.visited', 0)
            ->whereNull('wa_route_customers.deleted_at')
            ->whereNotIn('salesman_shift_customers.route_customer_id', $met->pluck('wa_route_customer_id')->toArray())
            ->distinct('wa_route_customers.id')
            ->get();        
        return response()->json(['met'=>$met, 'unmet'=>$unmet]);

    }

    public function driverShiftdetails($userId, $start, $end)
    {
        $user_id = $userId;
        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();
        $shifts = DeliverySchedule::where('driver_id', $user_id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $data = DB::table('delivery_schedules')
            ->select(
                'delivery_schedules.id as schedule_id',
                'delivery_schedules.actual_delivery_date',
                'delivery_schedules.finish_time',
                'delivery_schedules.actual_delivery_date as actual_delivery_date',
                'routes.route_name',
                'routes.manual_fuel_estimate',
                'fuel_entries.actual_fuel_quantity',
                DB::raw("(SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers
                    WHERE wa_route_customers.route_id = delivery_schedules.route_id
                    AND wa_route_customers.deleted_at IS NULL
                ) AS customers"),
                DB::raw("(SELECT COUNT(DISTINCT wa_internal_requisitions.wa_route_customer_id)
                    FROM wa_internal_requisitions WHERE wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id
                ) AS customers_with_orders"),
                DB::raw("(SELECT COUNT(wa_internal_requisitions.id)
                    FROM salesman_shifts
                    LEFT JOIN wa_internal_requisitions ON salesman_shifts.id = wa_internal_requisitions.wa_shift_id
                    WHERE salesman_shifts.id = delivery_schedules.shift_id
                ) AS expected_deliveries"),
                DB::raw("(SELECT COUNT(wa_internal_requisitions.id)
                    FROM salesman_shifts
                    LEFT JOIN wa_internal_requisitions ON salesman_shifts.id = wa_internal_requisitions.wa_shift_id
                    WHERE salesman_shifts.id = delivery_schedules.shift_id
                    AND wa_internal_requisitions.status = 'DELIVERED'
                ) AS actual_deliveries"),
                DB::raw("(SELECT COUNT(fuel_entries.id)
                    FROM fuel_entries
                    WHERE fuel_entries.shift_id = delivery_schedules.id
                    AND fuel_entries.shift_type = 'Route Deliveries'
                    AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                ) AS total_fuel_entries"),
                DB::raw("(SELECT COUNT(fuel_entries.id)
                    FROM fuel_entries
                    WHERE fuel_entries.shift_id = delivery_schedules.id
                    AND fuel_entries.shift_type = 'Route Deliveries'
                    AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                    AND fuel_entries.actual_fuel_quantity < fuel_entries.shift_fuel_estimate
                ) AS fueled_less_than_expected"),
                DB::raw("(SELECT COUNT(fuel_entries.id)
                    FROM fuel_entries
                    WHERE fuel_entries.shift_id = delivery_schedules.id
                    AND fuel_entries.shift_type = 'Route Deliveries'
                    AND DATE(delivery_schedules.actual_delivery_date) = DATE(delivery_schedules.finish_time)
                    AND fuel_entries.actual_fuel_quantity <= fuel_entries.shift_fuel_estimate
                ) AS fueled_within_expected"),
                DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                    FROM salesman_shift_store_dispatches
                    WHERE salesman_shift_store_dispatches.shift_id = delivery_schedules.shift_id
                    AND DATE(salesman_shift_store_dispatches.dispatch_time) != DATE(delivery_schedules.created_at)
                ) AS shifts_dispatched_next_day"),
                DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                    FROM salesman_shift_store_dispatches
                    WHERE salesman_shift_store_dispatches.shift_id = delivery_schedules.shift_id
                ) AS total_store_dispatches"),
            )
            ->where('delivery_schedules.driver_id', $user_id)
            ->leftJoin('routes', 'routes.id', 'delivery_schedules.route_id')
            ->leftJoin('fuel_entries', function ($query){
                $query->on('fuel_entries.shift_id', '=', 'delivery_schedules.id');
                $query->where('fuel_entries.shift_type', 'Route Deliveries');
            })
            ->whereBetween('delivery_schedules.created_at', [$start, $end])
            ->get()->map(function ($record){
                $record->delivery_date =  Carbon::parse($record->actual_delivery_date)->toDateString();
                $record->start_time =  Carbon::parse($record->actual_delivery_date)->toTimeString();
                $record->finish_time =  Carbon::parse($record->finish_time)->toTimeString();
                $record->dispatch_percentage = $record->total_store_dispatches > 0 ? ($record->shifts_dispatched_next_day / $record->total_store_dispatches) * 100 : 0;
                $record->system_usage_percentage = $record->expected_deliveries > 0 ? ($record->actual_deliveries / $record->expected_deliveries) * 100 : 0;
                return $record;
            });
        return response()->json($data);
    }
    public function diverDispatchDetails($userId, $start , $end)
    {
        

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        $data = DB::table('delivery_schedules')
            ->select(
                'delivery_schedules.id as schedule_id',
                'delivery_schedules.actual_delivery_date',
                'delivery_schedules.finish_time',
                'delivery_schedules.actual_delivery_date as actual_delivery_date',
                'routes.route_name',
                'routes.manual_fuel_estimate',
                'fuel_entries.actual_fuel_quantity',
                DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                    FROM salesman_shift_store_dispatches
                    WHERE salesman_shift_store_dispatches.shift_id = delivery_schedules.shift_id
                    AND DATE(salesman_shift_store_dispatches.dispatch_time) != DATE(delivery_schedules.created_at)
                ) AS shifts_dispatched_next_day"),
                DB::raw("(SELECT COUNT(salesman_shift_store_dispatches.id)
                    FROM salesman_shift_store_dispatches
                    WHERE salesman_shift_store_dispatches.shift_id = delivery_schedules.shift_id
                ) AS total_store_dispatches"),
            )
            ->where('delivery_schedules.driver_id', $userId)
            ->leftJoin('routes', 'routes.id', 'delivery_schedules.route_id')
            ->leftJoin('fuel_entries', function ($query){
                $query->on('fuel_entries.shift_id', '=', 'delivery_schedules.id');
                $query->where('fuel_entries.shift_type', 'Route Deliveries');
            })
            ->whereBetween('delivery_schedules.created_at', [$start, $end])
            ->get()->map(function ($record){
                $record->delivery_date =  Carbon::parse($record->actual_delivery_date)->toDateString();
                $record->start_time =  Carbon::parse($record->actual_delivery_date)->toTimeString();
                $record->finish_time =  Carbon::parse($record->finish_time)->toTimeString();
                $record->dispatch_percentage = $record->total_store_dispatches > 0 ? ($record->shifts_dispatched_next_day / $record->total_store_dispatches) * 100 : 0;
                return $record;
            });
       
        return view('admin.performance_reports.dispatch_details', compact('title', 'model',  'pmodule', 'permission', 'data'));
    }
    public function getDeliveryScheduleLateDispatches($scheduleId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $data = DB::table('salesman_shift_store_dispatches')
            ->select(
                'wa_location_and_stores.location_name as store',
                'delivery_schedules.actual_delivery_date',
                'delivery_schedules.created_at',
                'salesman_shift_store_dispatches.dispatch_time',
                'wa_unit_of_measures.title as bin',
                DB::raw("(SELECT COUNT(salesman_shift_store_dispatch_items.id)
                    FROM salesman_shift_store_dispatch_items
                    WHERE salesman_shift_store_dispatch_items.dispatch_id = salesman_shift_store_dispatches.id
                ) AS dispatch_items")
            )
            ->leftJoin('delivery_schedules', 'delivery_schedules.shift_id', 'salesman_shift_store_dispatches.shift_id' )
            ->leftJoin('wa_location_and_stores', 'wa_location_and_stores.id', 'salesman_shift_store_dispatches.store_id')
            ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', 'salesman_shift_store_dispatches.bin_location_id')
            ->where('delivery_schedules.id', $scheduleId)
            ->get()->map(function ($record){
                $record->is_late = false;
                $record->created_at = Carbon::parse($record->created_at)->toDateString();
                if($record->created_at != Carbon::parse($record->dispatch_time)->toDateString()){
                    $record->is_late = true;
                }
                return $record;
            });
        return response()->json($data);

    }
}
