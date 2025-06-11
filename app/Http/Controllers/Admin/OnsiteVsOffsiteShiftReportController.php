<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\OffsiteShiftRequest;
use App\SalesmanShift;
use App\SalesmanShiftCustomer;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnsiteVsOffsiteShiftReportController extends Controller
{
    protected $base_route;
    protected $resource_folder;

    public function __construct()
    {
        $this->model = "onsite-vs-offsite-shifts-report";
        $this->title = "Onsite Vs Offsite Shifts";
        $this->base_route = "onsite-vs-offsite-shifts-report";
        $this->resource_folder = "admin.onsitevsoffsiteshifsreport";
    }

    public function index(Request $request)
    {

        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $model = $this->model;
        $title = $this->title;
        $base_route = $this->base_route;
        $resource_folder = $this->resource_folder;

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $routes = DB::table('routes')->get();

        $shiftsQuery = DB::table('salesman_shifts')
    ->join('routes', 'salesman_shifts.route_id', '=', 'routes.id')
    ->join('users', 'salesman_shifts.salesman_id', '=', 'users.id')
    ->select(
        'routes.route_name',
        'users.name as salesman',
        'salesman_shifts.id',
        'salesman_shifts.start_time',
        'salesman_shifts.closed_time'
    );

if ($request->route) {
    $shiftsQuery->where('route_id', $request->route);
}

$shift = $shiftsQuery->whereBetween('salesman_shifts.start_time', [$startDate, $endDate])->get();

$shiftIds = $shift->pluck('id');

// Fetch approved offsite shift requests
$offSiteRequests = DB::table('offsite_shift_requests')
    ->where('status', 'approved')
    ->whereIn('shift_id', $shiftIds)
    ->get()
    ->keyBy('shift_id');


$relatedData = DB::table('wa_internal_requisitions')
    ->join('salesman_shift_customers', 'wa_internal_requisitions.wa_shift_id', '=', 'salesman_shift_customers.salesman_shift_id')
    ->whereIn('wa_internal_requisitions.wa_shift_id', $shiftIds)
    ->select(
        'wa_internal_requisitions.wa_shift_id',
        'salesman_shift_customers.salesman_shift_type',
        'salesman_shift_customers.visited',
        'salesman_shift_customers.order_taken',
        'salesman_shift_customers.route_customer_id',
        'salesman_shift_customers.created_at'
    )
    ->get()
    ->groupBy('wa_shift_id');

    $shifts = $shift->map(function ($shift) use ($offSiteRequests, $relatedData) {
        $shiftId = $shift->id;
        $shiftRelatedData = $relatedData->get($shiftId, collect());

        $data = [
            'route' => $shift->route_name,
            'salesman' => $shift->salesman,
            'onsite_start' => Carbon::parse($shift->start_time)->format('Y-m-d H:i:s'),
            'onsite_end' => Carbon::parse($shift->closed_time)->format('Y-m-d H:i:s'),
            'onsite_duration' => '',
            'onsite_customers_served' => '',
            'offsite_start' => '',
            'offsite_end' => '',
            'offsite_duration' => '',
            'offsite_customers_served' => '',
            'met_with_no_orders' => '',
            'totally_unmet' => ''
        ];

        $offSiteRequest = $offSiteRequests->get($shiftId);

        if ($offSiteRequest) {
            $data['onsite_end'] = Carbon::parse($offSiteRequest->updated_at)->format('Y-m-d H:i:s');
            $onsiteDuration = Carbon::parse($offSiteRequest->updated_at)->diffInMinutes(Carbon::parse($shift->start_time));
            $data['onsite_duration'] = CarbonInterval::minutes($onsiteDuration)->cascade()->forHumans();

            // $onsiteCustomersServed = $shiftRelatedData->where('salesman_shift_type', 'onsite')
            //     ->where('visited', 1)
            //     ->where('order_taken', 1)
            //     ->unique('route_customer_id')
            //     ->count();
            $onsiteCustomersServed = DB::table('wa_internal_requisitions')
                ->where('wa_shift_id', $shiftId)
                ->where('shift_type', 'onsite')
                ->distinct('wa_route_customer_id');
            $onsiteIds = $onsiteCustomersServed->pluck('wa_route_customer_id');
            $onsiteCustomersServed = $onsiteCustomersServed->count();
            $data['onsite_customers_served'] = $onsiteCustomersServed;

            $data['offsite_start'] = $data['onsite_end'];
            $data['offsite_end'] = Carbon::parse($shift->closed_time)->format('Y-m-d H:i:s');
            $offsiteDuration = Carbon::parse($shift->closed_time)->diffInMinutes(Carbon::parse($offSiteRequest->updated_at));
            $data['offsite_duration'] = CarbonInterval::minutes($offsiteDuration)->cascade()->forHumans();

            // $offsiteCustomersServed = $shiftRelatedData->where('salesman_shift_type', 'offsite')
            //     ->where('visited', 1)
            //     ->where('order_taken', 1)
            //     ->unique('route_customer_id')
            //     ->count();
            $offsiteCustomersServed = DB::table('wa_internal_requisitions')
            ->where('wa_shift_id', $shiftId)
            ->where('shift_type', 'offsite')
            ->whereNotIn('wa_route_customer_id', $onsiteIds)
            ->distinct('wa_route_customer_id')
            ->count();
            $data['offsite_customers_served'] = $offsiteCustomersServed;
        } else {
            $onsiteDuration = Carbon::parse($shift->closed_time)->diffInMinutes(Carbon::parse($shift->start_time));
            $data['onsite_duration'] = CarbonInterval::minutes($onsiteDuration)->cascade()->forHumans();

            // $onsiteCustomersServed = $shiftRelatedData->where('salesman_shift_type', 'onsite')
            //    ->where('visited', 1)
            //     ->where('order_taken', 1)
            //     ->unique('route_customer_id')
            //     ->count(); 
            
            $onsiteCustomersServed = DB::table('wa_internal_requisitions')
            ->where('wa_shift_id', $shiftId)
            ->where('shift_type', 'onsite')
            ->distinct('wa_route_customer_id')
            ->count();
            $data['onsite_customers_served'] = $onsiteCustomersServed;
        }

        // $metWithNoOrders = $shiftRelatedData->where('salesman_shift_type', '')
        $metWithNoOrders = $shiftRelatedData->where('visited', 1)
            ->where('order_taken', 0)
            ->whereBetween('created_at', [
                Carbon::parse($shift->start_time),
                Carbon::parse($shift->closed_time)
            ])
            ->unique('route_customer_id')
            ->count();
        $data['met_with_no_orders'] = $metWithNoOrders;

        $totallyUnmet = $shiftRelatedData->where('salesman_shift_type', '')
            ->where('visited', 0)
            ->where('order_taken', 0)
            ->unique('route_customer_id')
            ->count();
        $data['totally_unmet'] = $totallyUnmet;

        $onsitemetwithnoorders = DB::table('salesman_shift_customers')
            ->where('salesman_shift_id', $shiftId)
            ->where('salesman_shift_type', 'onsite')
            ->where('order_taken', 0)
            ->where('visited', 1)
            ->whereBetween('updated_at', [
                Carbon::parse($shift->start_time),
                Carbon::parse($shift->closed_time)
            ])
            ->distinct('route_customer_id')
            ->count();
        $offsitemetwithnoorders = DB::table('salesman_shift_customers')
            ->where('salesman_shift_id', $shiftId)
            ->where('salesman_shift_type', 'offsite')
            ->where('order_taken', 0)
            ->where('visited', 1)
            ->whereBetween('updated_at', [
                Carbon::parse($shift->start_time),
                Carbon::parse($shift->closed_time)
            ])
            ->distinct('route_customer_id')
            ->count();

        $data['onsite_met_with_no_orders'] = $onsitemetwithnoorders;
        $data['offsite_met_with_no_orders'] = $offsitemetwithnoorders;

        return $data;
    })->toArray();

        if ($request->type && $request->type == 'Download') {
            $filename = "Onsite Vs Offsite Shift Report";
            $shifts = collect($shifts);
            $headings = ['route', 'salesman', 'onsite_start', 'onsite_end', 'onsite_duration', 'onsite_customers_served', 'offsite_start', 'offsite_end', 'offsite_duration', 'offsite_customers_served','met_with_no_orders','totally_unmet'];
            return ExcelDownloadService::download($filename, $shifts, $headings);
        }
        return view("admin.onsitevsoffsiteshiftsreport.index", compact('shifts', 'model', 'title', 'routes'));
    }
}
