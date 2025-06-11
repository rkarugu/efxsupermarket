<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Model\Route;
use App\Model\WaInternalRequisition;
use App\SalesmanShift;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use App\Model\WaInventoryItem;
use Illuminate\Support\Str;


class GroupPerfomanceReportController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'group-performance-report';
        $this->base_route = 'group-performance-report';
        $this->resource_folder = 'admin.salesreceiablesreports';
        $this->base_title = 'Group Performance Report';
        $this->permissions_module = 'sales-and-receivables-reports';
    }

    public function index(Request $request)
    {
        $branches = Restaurant::latest()->get();
        if (!can('group-performance-report', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }
          //implement new filter based on range
          if(!$request->datePicker){
            $startDate = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');

        }else{
              if ($request->selectionType  == 'single'){
                if(Str::contains($request->datePicker, 'to')){
                    $dates = explode(' to ', $request->datePicker);
                    $startDate = Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s');
                    $endDate = Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s');
                }else{
                    $startDate = Carbon::parse($request->datePicker)->startOfDay()->format('Y-m-d H:i:s');
                    $endDate = Carbon::parse($request->datePicker)->endOfDay()->format('Y-m-d H:i:s');

                }
      

        }else{
            $dates = explode(' to ', $request->datePicker);
            $startDate = Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s');
        }

        }

        // $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
        // $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');

        $query = DB::table('routes');
        if ($request->route) {
            $query = $query->where('routes.id', $request->route);
        }
        if ($request->branch) {
            $query = $query->where('routes.restaurant_id', $request->branch);
        }else{
            $query = $query->where('routes.restaurant_id', 10);

        }

        $shifts = SalesmanShift::withCount('orders')->whereNotNull('start_time')
            ->whereBetween('start_time', [$startDate, $endDate])->having('orders_count', '>', 0)->get();

        $data = $query->select([
            'routes.id as route_id',
            'routes.route_name as route',
            'routes.group as group',
            'users.name as salesman',
            'routes.order_frequency',
            'routes.order_taking_days',
            'routes.tonnage_target',
            'routes.sales_target',
            'routes.ctn_target',
            'routes.dzn_target',
            DB::raw("(select count(*) from delivery_centres where delivery_centres.route_id = routes.id and delivery_centres.deleted_at is null) as centre_count"),
            DB::raw("(select count(*) from wa_route_customers where wa_route_customers.route_id = routes.id and wa_route_customers.deleted_at is null) as shop_count"),
            // DB::raw("(select count(distinct wa_route_customer_id) from wa_internal_requisitions where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.created_at between '$startDate' and '$endDate') as met_shops"),
            DB::raw("(select count(distinct salesman_shift_customers.route_customer_id) 
            from wa_internal_requisitions 
            join salesman_shift_customers on wa_internal_requisitions.wa_shift_id = salesman_shift_customers.salesman_shift_id
            where salesman_shift_customers.visited = 1 and wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.created_at between '$startDate' and '$endDate') as met_shops"),
            DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
                as gross_sales"),
            DB::raw("(select sum(wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price)
                from wa_inventory_location_transfer_item_returns
                join wa_inventory_location_transfers on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id = wa_inventory_location_transfers.id
                join wa_inventory_location_transfer_items on wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id = wa_inventory_location_transfer_items.id
                where wa_inventory_location_transfers.route = routes.route_name and wa_inventory_location_transfer_item_returns.created_at between '$startDate' and '$endDate')
                as returns"),
            DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
                as tonnage"),
            // DB::raw("(select sum(CASE WHEN pack_sizes.title = 'CTN' THEN wa_internal_requisition_items.quantity ELSE 0 END) from wa_internal_requisition_items
            //     left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
            //     left join pack_sizes on wa_inventory_items.pack_size_id = pack_sizes.id
            //     left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
            //     where wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
            //     as ctns"),
            DB::raw("(select count(distinct concat(wa_inventory_items.id ,date(wa_internal_requisition_items.created_at)) ) from wa_internal_requisition_items
            left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
            left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
            where wa_inventory_items.pack_size_id = 3 and wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
            as ctns"),
            
            // DB::raw("(select sum(CASE WHEN pack_sizes.id in (1,6,9,17,4,10) THEN wa_internal_requisition_items.quantity ELSE 0 END) from wa_internal_requisition_items
            //     left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
            //     left join pack_sizes on wa_inventory_items.pack_size_id = pack_sizes.id
            //     left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
            //     where wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
            //     as dzns"),
            DB::raw("(select count(distinct concat(wa_inventory_items.id, date(wa_internal_requisition_items.created_at))) from wa_internal_requisition_items
            left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
            left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
            where wa_inventory_items.pack_size_id in (6,9,17,4,10,1) and wa_internal_requisitions.route_id = routes.id and wa_internal_requisition_items.created_at between '$startDate' and '$endDate')
            as dzns"),
        ])
            ->join('route_user', 'routes.id', '=', 'route_user.route_id')
            ->join('users', function ($join) {
                $join->on('route_user.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->get()
            ->map(function ($record) use ($request,  $startDate, $endDate){
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                $multiplier = 1;
                if($start and $end) {
                    $number_of_days = $end->diffInDays($start) + 1;
                if($number_of_days == 1) {
                        $multiplier = 1;
                }else{
                    $multiplier = number_format((($number_of_days / 7) * $record->order_frequency), 2);
                }                     
                }
                $record->multiplier  = $multiplier;
                $record->total_order_taking_days = $multiplier;

                // $orderTakingDays = $record->order_frequency;
                $orderTakingDays = $multiplier;

                $record->net_sales = $record->gross_sales - $record->returns;
                $record->unmet = $record->shop_count - $record->met_shops;
                $record->total_order_taking_days = $orderTakingDays;
                $record->freq = $orderTakingDays;
                $record->tonnage_target = $record->tonnage_target * $orderTakingDays;
                $record->sales_target = $record->sales_target * $orderTakingDays;
                $record->ctn_target = $record->ctn_target * $orderTakingDays;
                $record->dzn_target = $record->dzn_target * $orderTakingDays;
                $record->met_customers_percentage = $record->shop_count != 0 ? ($record->met_shops / $record->shop_count) * 100 : 0;
                $record->sales_percentage = ($record->sales_target * $record->total_order_taking_days) != 0 ? (($record->net_sales / ($record->sales_target * $record->total_order_taking_days)) * 100) : 0;
                $record->tonnage_percentage = ($record->tonnage_target * $record->total_order_taking_days) != 0 ? (($record->tonnage / ($record->tonnage_target * $record->total_order_taking_days)) * 100) : 0;
                $record->ctns_percentage = ($record->ctn_target * $record->total_order_taking_days) != 0 ? (($record->ctns / ($record->ctn_target * $record->total_order_taking_days)) * 100) : 0;
                $record->dzns_percentage = ($record->dzn_target * $record->total_order_taking_days) != 0 ? (($record->dzns / ($record->dzn_target * $record->total_order_taking_days)) * 100) : 0;
                $record->avg_percentage = ($record->met_customers_percentage + $record->sales_percentage + $record->tonnage_percentage + $record->ctns_percentage + $record->dzns_percentage) / 5;

                $record->met_customers_percentage = round($record->met_customers_percentage, 2);
                $record->sales_percentage = round($record->sales_percentage, 2);
                $record->tonnage_percentage = round($record->tonnage_percentage, 2);
                $record->ctns_percentage = round($record->ctns_percentage, 2);
                $record->dzns_percentage = round($record->dzns_percentage, 2);
                $record->avg_percentage = round($record->avg_percentage, 2);

                return $record;
            });

        if ($request->group) {
            $data = $data->where('group', $request->group)->all();
        }

        $data = collect($data);

        $groupedData = $data->groupBy('group');

        $groupSums = [];
        $groupedRecords = [];

        foreach ($data as $record) {
            $group = $record->group;
            if (!isset($groupedRecords[$group])) {
                $groupedRecords[$group] = [];
            }

            $groupedRecords[$group][] = $record;
        }

        foreach ($groupedRecords as $group => $groupRecords) {
            $tonnageTargetSum = collect($groupRecords)->sum('tonnage_target');
            $ctnsTargetSum = collect($groupRecords)->sum('ctn_target');
            $dznsTargetSum = collect($groupRecords)->sum('dzn_target');
            $salesTargetSum = collect($groupRecords)->sum('sales_target');
            $groupSums[$group]['tonnage_target_sum'] = $tonnageTargetSum;
            $groupSums[$group]['ctns_target_sum'] = $ctnsTargetSum;
            $groupSums[$group]['dzns_target_sum'] = $dznsTargetSum;
            $groupSums[$group]['target_sales'] = $salesTargetSum;
        }

        if ($request->filter === 'tonnage') {
            $groupedData = $groupedData->sortBy(function ($groupData) {
                return $groupData->sum('tonnage');
            });
        }

        foreach ($groupedData as $group => $groupData) {
            $groupSums[$group] = [
                'ctns' => $groupData->sum('ctns'),
                'dzns' => $groupData->sum('dzns'),
                'tonnage' => $groupData->sum('tonnage'),
                'returns' => $groupData->sum('returns'),
                'gross_sales' => $groupData->sum('gross_sales'),
                'met_shops' => $groupData->sum('met_shops'), 
                'shop_count' => $groupData->sum('shop_count'), 
                'centre_count' => $groupData->sum('centre_count'), 
                'unmet' => $groupData->sum('unmet'), 
                'freq' => $groupData->sum('freq'), 
                'met_customers_percentage' => $groupData->sum('met_customers_percentage'),
                'sales_percentage' => $groupData->sum('sales_percentage'),
                'tonnage_percentage' => $groupData->sum('tonnage_percentage'),
                'ctns_percentage' => $groupData->sum('ctns_percentage'),
                'dzns_percentage' => $groupData->sum('dzns_percentage'),
            ];
        }

        $totalGroupSums = collect($groupSums)->reduce(function ($carry, $item) {
            $carry['ctns'] += $item['ctns'];
            $carry['dzns'] += $item['dzns'];
            $carry['tonnage'] += $item['tonnage'];
            $carry['returns'] += $item['returns'];
            $carry['gross_sales'] += $item['gross_sales'];
            $carry['met_shops'] += $item['met_shops'];
            $carry['shop_count'] += $item['shop_count'];
            $carry['centre_count'] += $item['centre_count'];
            $carry['unmet'] += $item['unmet'];
            $carry['freq'] += $item['freq'];
            return $carry;
        }, [
            'ctns' => 0,
            'dzns' => 0,
            'tonnage' => 0,
            'returns' => 0,
            'gross_sales' => 0,
            'met_shops' => 0,
            'shop_count' => 0,
            'centre_count' => 0,
            'unmet' => 0,
            'freq' => 0,
        ]);

        $groupCounts = [];
        foreach ($groupedData as $group => $groupData) {
            $groupCounts[$group] = $groupData->count();
        }

        $groupAverages = [];
        foreach ($groupSums as $group => $sums) {
            $groupAverages[$group] = [
                'met_customers_percentage' => $sums['met_customers_percentage'] / $groupCounts[$group],
                'sales_percentage' => $sums['sales_percentage'] / $groupCounts[$group],
                'tonnage_percentage' => $sums['tonnage_percentage'] / $groupCounts[$group],
                'ctns_percentage' => $sums['ctns_percentage'] / $groupCounts[$group],
                'dzns_percentage' => $sums['dzns_percentage'] / $groupCounts[$group],
            ];
        }

        if ($request->intent == 'EXCEL') {
            $headings = ['GROUP', 'FREQUENCY', 'CENTERS', 'SHOPS', 'MET', 'UNMET', 'MET CUSTOMERS (%)', 'TARGET CTNS', 'CTNS', 'CTNS (%)', 'TARGET DZNS', 'DZNS', 'DZNS (%)', 'TARGET TONNAGE', 'TONNAGE', 'TONNAGE (%)', 'TARGET SALES', 'GROSS SALES', 'RETURNS', 'NET SALES', 'SALES (%)', 'OVERALL AVG % PERFORMANCE'];
            $filename = "GROUP PERFORMANCE REPORT $startDate - $endDate";
            $excelData = [];
        
            foreach ($groupedRecords as $group => $groupRecords) {
                $tonnageTargetSum = collect($groupRecords)->sum('tonnage_target');
                $ctnsTargetSum = collect($groupRecords)->sum('ctn_target');
                $dznsTargetSum = collect($groupRecords)->sum('dzn_target');
                $salesTargetSum = collect($groupRecords)->sum('sales_target');
                $groupSums[$group]['tonnage_target_sum'] = $tonnageTargetSum;
                $groupSums[$group]['ctns_target_sum'] = $ctnsTargetSum;
                $groupSums[$group]['dzns_target_sum'] = $dznsTargetSum;
                $groupSums[$group]['target_sales'] = $salesTargetSum;
            }
        
            foreach ($groupedData as $group => $groupData) {
                $tonnage_percentage = ($groupData->sum('tonnage') != 0) ? ($groupData->sum('tonnage') * 100 ) / $groupSums[$group]['tonnage_target_sum'] : 0;
                $dzns_percentage = ($groupData->sum('dzns') != 0) ? ($groupData->sum('dzns') * 100) / $groupSums[$group]['dzns_target_sum'] : 0;
                $ctns_percentage = ($groupData->sum('ctns') != 0) ? ($groupData->sum('ctns') * 100) / $groupSums[$group]['ctns_target_sum'] : 0;
                $sales_percentage = ($groupData->sum('gross_sales') != 0) ? ($groupData->sum('gross_sales') * 100 ) / $groupSums[$group]['target_sales'] : 0;
                $met_customers_percentage = ($groupData->sum('met_shops') != 0) ? ($groupData->sum('met_shops') * 100) / $groupData->sum('shop_count') : 0;
                $average_percentage = ($met_customers_percentage + $sales_percentage + $tonnage_percentage + $ctns_percentage + $dzns_percentage) / 5;
        
                $payload = [
                    'group' => $group,
                    'freq' => $groupData->sum('freq'),
                    'centre_count' => $groupData->sum('centre_count'),
                    'shop_count' => $groupData->sum('shop_count'),
                    'met_shops' => $groupData->sum('met_shops'),
                    'unmet' => $groupData->sum('unmet'),
                    'met_customers_percentage' => round($met_customers_percentage, 2),
                    'target_ctns' => $groupSums[$group]['ctns_target_sum'],
                    'ctns' => $groupData->sum('ctns'),
                    'ctns_percentage' => round($ctns_percentage, 2),
                    'target_dzns' => $groupSums[$group]['dzns_target_sum'],
                    'dzns' => $groupData->sum('dzns'),
                    'dzns_percentage' => round($dzns_percentage, 2),
                    'target_tonnage' => $groupSums[$group]['tonnage_target_sum'],
                    'tonnage' => round($groupData->sum('tonnage'), 2),
                    'tonnage_percentage' => round($tonnage_percentage, 2),
                    'target_sales' => round($groupSums[$group]['target_sales'], 2),
                    'gross_sales' => round($groupData->sum('gross_sales'), 2),
                    'returns' => round($groupData->sum('returns'), 2),
                    'net_sales' => round(($groupData->sum('gross_sales')) - ($groupData->sum('returns')), 2),
                    'sales_percentage' => round($sales_percentage, 2),
                    'average_percentage' => round($average_percentage, 2),
                ];
        
                $excelData[] = $payload;
            }
        
            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }
        
        $title = $this->base_title;
        $model = $this->model;
        $breadcum = ['Sales & Receivables' => '', 'Reports' => '', 'Group Performance Report' => ''];
        $routes = Route::select('id', 'route_name')->get();
        $start_date = Carbon::parse($startDate)->toDateString();
        $end_date = Carbon::parse($endDate)->toDateString();
        return view("$this->resource_folder.group_performance", compact('title', 'model', 'breadcum', 'routes', 'groupedData', 'groupSums', 'totalGroupSums', 'start_date', 'end_date','branches'));
    }

}
