<?php

namespace App\Http\Controllers\Admin;

use App\FieldVisitSchedule;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaRouteCustomer;
use App\Models\GeomappingSchedules;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GeomappingSchedulesController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'geomapping-schedules';
        $this->base_route = 'geomapping-schedules';
        $this->resource_folder = 'admin.geomapping_schedules';
        $this->base_title = 'Geomapping Schedules';
        $this->permissions_module = 'route-customers';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $model = $this->model;
        $logged_user_info = Auth::user();
        // $branches = Restaurant::all();
        $my_permissions =  $this->mypermissionsforAModule();
        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
       
        if ($isAdmin || isset($my_permissions['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::get();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        if (!can('geomapping-schedules', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        $dates = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        if($isAdmin || isset($my_permissions['employees___view_all_branches_data'])){
            if ($request->branch) {
                $list = GeomappingSchedules::latest()->with('route')->where('branch', $request->branch)->get();
            } else {
                $list = GeomappingSchedules::latest()->with('route')->where('branch', 2)->get();
            }
        }else {
            if ($request->branch) {
                $list = GeomappingSchedules::latest()->with('route')->where('branch', $request->branch)->get();
            } else {
                // $list = GeomappingSchedules::latest()->with('route')->where('branch', 2)->get();
                $list = GeomappingSchedules::latest()->with('route')->where('branch', $authuser->userRestaurent->id)->get();
            }
        }
        
        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'list',
            'dates',
            'logged_user_info',
            'branches',
            'my_permissions',
        ));
    }

    public function create(): View | RedirectResponse
    {
        $title = "$this->base_title | Add";
        $model = $this->model;
        $branches = Restaurant::all();
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Create' => ''];
        $base_route = $this->base_route;

        $routes = Route::whereNotNull('order_taking_days')->get()->map(function (Route $route) {
            $route->salesman = $route->salesman();
            $orderTakingDates = [];
            $orderTakingDays = explode(',', $route->order_taking_days);
            $today = Carbon::now()->dayOfWeek;
            $route->order_taking_dates = $orderTakingDays;
            return $route;
        });
        return view("$this->resource_folder.create", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'routes',
            'branches',
        ));
    }
    public function edit($id)
    {
        $title = "$this->base_title | Edit";
        $model = $this->model;
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Edit' => ''];
        $base_route = $this->base_route;
        $logged_user_info = Auth::user();
        if (!can('edit-schedule', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $schedule =  GeomappingSchedules::find($id);
        $branch = Restaurant::find($schedule->branch);
        $route = Route::find($schedule->route_id);
        return view("$this->resource_folder.edit", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'schedule',
            'logged_user_info',
            'branch',
            'route',

        ));

    }
    public function  update(Request $request, $id)
    {
        try {
            $schedule =  GeomappingSchedules::find($id);
            $schedule->date = Carbon::parse($request->date)->toDateTimeString();
            $schedule->route_manager = $request->supervisor;
            $schedule->route_manager_contact = $request->supervisor_contact;
            $schedule->supervisor = $request->supervisor2;
            $schedule->supervisor_contact = $request->supervisor_contact2;
            $schedule->golden_africa_rep = $request->ga_rep;
            $schedule->golden_africa_rep_contact = $request->ga_rep_contact;
            $schedule->save();

            Session::flash('success', 'schedule updated successfully');
            return redirect()->route('geomapping-schedules.index');
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->route('geomapping-schedules.index');
        }
    }
    public function destroy($id)
    {
        $title = "$this->base_title | Add";
        $model = $this->model;
        $branches = Restaurant::all();
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Create' => ''];
        $base_route = $this->base_route;
        try {
            $geomappingSchedule = GeomappingSchedules::find($id);
            $geomappingSchedule->delete();
            Session::flash('success', "Schedule deleted successfully");
            return redirect()->route('geomapping-schedules.index');
        } catch (\Throwable $th) {
            Session::flash('error', $th->getMessage());
            return redirect()->route('geomapping-schedules.index');
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $branch = $request->input('branch');
        $date = $request->input('date');
        $routeIds = $request->input('route_id');
        $supervisors = $request->input('supervisor');
        $supervisorContacts = $request->input('supervisor_contact');
        $supervisors2 = $request->input('supervisor2');
        $supervisorContacts2 = $request->input('supervisor_contact2');
        $bizwizReps = $request->input('bizwiz_rep');
        $gaReps = $request->input('Ga_rep');
        $gaRepContacts = $request->input('Ga_rep_contact');

        foreach ($routeIds as $index => $routeId) {
            switch ($branch) {
                case 8:
                    $contact = '0711139513';
                    $name = 'Isabella Wangui';
                    break;
                case 5:
                    $contact = '0727094252';
                    $name = 'Roy Olayo';
                    break;
                case 6:
                    $contact = '0792836089';
                    $name = 'Peter Mbaluka';
                    break;
                case 3:
                    $contact = '0705147939';
                    $name = 'Charles Kiarie';
                    break;
                case 9:
                    $contact = '0762721981';
                    $name = 'Mercy Rono';
                    break;
                case 7:
                    $contact = '0704256554';
                    $name = 'Gideon Wambua';
                    break;
                case 4:
                    $contact = '0719610002';
                    $name = 'Elly Ochieng';
                    break;
                case 2:
                    $contact = '0712668244';
                    $name = 'Patrick Mwaura';
                    break;
                default:
                    $contact = null;
                    $name = 'no rep ';
                    break;
            }

            GeomappingSchedules::create([
                'branch' => $branch,
                'date' => $date,
                'route_id' => $routeId,
                'route_manager' => $supervisors[$index],
                'route_manager_contact' => $supervisorContacts[$index],
                'supervisor' => $supervisors2[$index],
                'supervisor_contact' => $supervisorContacts2[$index],
                'bizwiz_rep' => $name,
                'bizwiz_rep_contact' => $contact,
                'golden_africa_rep' => $gaReps[$index],
                'golden_africa_rep_contact' => $gaRepContacts[$index],
            ]);
        }

        return redirect()->route("$this->base_route.index")->with('success', 'Schedules created successfully');
    }

    public function show(Request $request, $id)
    {
        $googleMapsApiKey = config('app.google_maps_api_key');
        $title = $this->base_title.' Details';
        $model = $this->model;
        $logged_user_info = Auth::user();
        $my_permissions = $this->mypermissionsforAModule();
        if (!can('geomapping-schedules', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        $schedule = GeomappingSchedules::find($id);
        if (!$schedule)
        {
            return  redirect()->back()->with('message','No schedule found');
        }
        $date = Carbon::parse($schedule->date)->toDateString();
        $stats = WaRouteCustomer::where('route_id', $schedule->route_id)
            ->whereDate('created_at','>=', '2023-06-04 00:00:00')
            // ->whereDate('updated_at', $date)
            ->selectRaw('
        SUM(CASE WHEN status = "unverified" THEN 1 ELSE 0 END) as unverified_count,
        SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count,
        SUM(CASE WHEN status != "unverified" THEN 1 ELSE 0 END) as geomapped_count,
        COUNT(*) as total_count,
        COUNT(DISTINCT delivery_centres_id) as centers_visited_count,
        (SELECT COUNT(*) FROM delivery_centres WHERE route_id = ?) as total_centers_in_route
    ', [$schedule->route_id])
            ->first();
        $routeId = $schedule->route_id;

        $newCustomers = WaRouteCustomer::where('route_id', $schedule->route_id)
            ->whereDate('created_at', $date)
            ->where('created_by', '!=', 0)
            ->whereNot('status', 'unverified')
            ->get();
        $newCustomers  = $newCustomers->unique('created_at');
        $newCustomersIds = $newCustomers->pluck('id')->toArray();

        $existingCustomers = WaRouteCustomer::where('route_id', $schedule->route_id)
        // ->whereDate('created_at', $date)
        ->where('created_by', '!=', 0)
        ->whereNot('status', 'unverified')
        ->whereNotIn('id', $newCustomersIds)
        ->get();
        
        $newCenters = DeliveryCentres::where('route_id', $schedule->route_id)
            ->whereDate('created_at', '=', $date)
            ->get();
        $newCentersLocations = $newCenters->toJson();


        $percentageUnverified = ($stats->total_count > 0) ? ($stats->unverified_count / $stats->total_count) * 100 : 0;
        $page_stats = [
            'total_customers' => $stats->total_count,
            'new_customers' => $newCustomers->count(),
            'verified_count' => ($stats->total_count -  $stats->unverified_count),
            'unverified_count' =>  $stats->unverified_count,
            'percentage_unverified' => number_format($percentageUnverified,2),
            'percentage_verified' => 100 -  number_format($percentageUnverified,2),
            'visited_centers' => $stats->centers_visited_count,
            'centers_in_route' => $stats->total_centers_in_route,
            'new_centers' => $newCenters->count(),
        ];
        $custs =$existingCustomers->merge($newCustomers);
        $allCustomers =$custs->toJson();
        if(isset($request->download) && $request->download == 'Download'){
            $pdf = Pdf::loadView("$this->resource_folder.schedule_pdf", compact('title',
            'model',
            'breadcum',
            'base_route',
            'logged_user_info',
            'page_stats',
            'newCustomers',
            'schedule',
            'newCenters',
            'allCustomers',
            'existingCustomers',
            'googleMapsApiKey',))->setPaper('a4', 'portrait');
            return $pdf->download('Geomapping-Summary.pdf');
        }

        return view("admin.geomapping_schedules.show", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'logged_user_info',
            'page_stats',
            'newCustomers',
            'schedule',
            'newCenters',
            'allCustomers',
            'googleMapsApiKey',
            'newCentersLocations',
            'my_permissions',   
        ));
    }

    public function customerServeTime($id)
    {

        $schedule = GeomappingSchedules::find($id);
        $startDate = Carbon::parse($schedule->date)->startOfDay();
        $endDate =  Carbon::parse($schedule->date)->endOfDay();
        $route_id = $schedule->route_id;

        if (request()->wantsJson()) {
            // $query = WaRouteCustomer::with(['route', 'route.users', 'center'])
            // ->where('route_id', $route_id)
            // ->whereNot('status', 'unverified')
            // ->whereDate('created_at', $startDate)
            // ->where('created_by', '!=', 0);
            $newCustomers = WaRouteCustomer::where('route_id', $schedule->route_id)
            ->whereDate('created_at', $startDate)
            ->where('created_by', '!=', 0)
            ->whereNot('status', 'unverified')
            ->get();
            $newCustomers  = $newCustomers->unique('created_at');
            $newCustomersIds = $newCustomers->pluck('id')->toArray();
            // dd($newCustomersIds);

            $query = WaRouteCustomer::with(['route', 'route.users', 'center'])->where('wa_route_customers.route_id', $schedule->route_id)
            ->where('created_by', '!=', 0)
            ->whereNot('wa_route_customers.status', 'unverified')
            ->whereNotIn('wa_route_customers.id', $newCustomersIds);


            return DataTables::eloquent($query)
                ->editColumn('updated_at', function ($row) {
                    return Carbon::parse($row->updated_at)->toTimeString();
                })
                ->editColumn('time_taken', function ($row) {
                    return $row->TimeServed() ;
                })
                ->addColumn('action', function ($row) use($id){
                    return '<a href="' . route('route-customers.show-custom', [$row->id, 'geomapping-schedules', 'schedule_id' => $id]) . '" title="View Route Customer">
                    <i class="fa fa-eye text-info fa-lg"></i></a>';
                })
                ->addIndexColumn()
                ->toJson();
        }

    }



    public function summary(Request $request)
    {
        $title = $this->base_title;
        $model = 'geomapping-summary';
        $logged_user_info = Auth::user();
        $my_permissions =  $this->mypermissionsforAModule();
        $permission = $this->mypermissionsforAModule();
        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;

        if (!can('geomapping-summary', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $dataQuery = DB::table('restaurants')
            ->select(
                'restaurants.id',
                'restaurants.name',
                DB::raw('(SELECT COUNT(*) FROM routes WHERE routes.restaurant_id = restaurants.id) as route_count'),
                DB::raw('(SELECT COUNT(*) FROM delivery_centres 
                LEFT JOIN routes ON delivery_centres.route_id = routes.id
                WHERE routes.restaurant_id = restaurants.id
                ) as centre_count'),
                DB::raw('(SELECT COUNT(*) FROM delivery_centres 
                LEFT JOIN routes ON delivery_centres.route_id = routes.id
                WHERE routes.restaurant_id = restaurants.id AND Date(delivery_centres.created_at) >= date("2024-06-05 00:00:00")
                ) as new_centres'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                LEFT JOIN routes ON wa_route_customers.route_id = routes.id
                WHERE routes.restaurant_id = restaurants.id) as customer_count'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                LEFT JOIN routes ON wa_route_customers.route_id = routes.id
                WHERE routes.restaurant_id = restaurants.id AND wa_route_customers.status IN ("verified", "duplicate", "approved", "rejected") ) as geomapped_customer_count'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                LEFT JOIN routes ON wa_route_customers.route_id = routes.id
                WHERE routes.restaurant_id = restaurants.id AND wa_route_customers.created_by != 0 and wa_route_customers.created_at = wa_route_customers.updated_at AND Date(wa_route_customers.created_at) > "2024-06-04 00:00:00") as new_customers'),
            );         
            // ->get();

            if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
                $dataQuery->where('restaurants.id', $authuser->userRestaurent->id);
            } 
            $data = $dataQuery->get();
        
        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        if(isset($request->download) && $request->download == 'Download'){
            $pdf = Pdf::loadView("$this->resource_folder.pdf", compact('data'))->setPaper('a4', 'portrait');
            return $pdf->download('Geomapping-Summary.pdf');
        }
        return view("$this->resource_folder.summary", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'logged_user_info',
            'my_permissions',
            'data',
        ));
    }



    public function summaryShow($branchId, Request $request)
    {
        $branch  = Restaurant::find($branchId);
        $title = $this->base_title;
        $model = 'geomapping-summary';
        $logged_user_info = Auth::user();
        $my_permissions =  $this->mypermissionsforAModule();

        if (!can('geomapping-summary', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $data = DB::table('routes')
            ->select(
                'routes.route_name',
                'routes.id',
                DB::raw('(SELECT COUNT(*) FROM delivery_centres 
                WHERE delivery_centres.route_id = routes.id
                ) as centre_count'),
                DB::raw('(SELECT COUNT(*) FROM delivery_centres 
                WHERE delivery_centres.route_id = routes.id AND Date(delivery_centres.created_at) >= date("2024-06-05 00:00:00")
                ) as new_centres'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                WHERE wa_route_customers.route_id = routes.id) as customer_count'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                WHERE wa_route_customers.route_id = routes.id AND wa_route_customers.status IN ("verified", "duplicate", "approved", "rejected") ) as geomapped_customer_count'),
                DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                WHERE wa_route_customers.route_id = routes.id AND wa_route_customers.created_by != 0 and wa_route_customers.created_at = wa_route_customers.updated_at AND Date(wa_route_customers.created_at) > "2024-06-04 00:00:00") as new_customers'),
            )
            ->where('routes.restaurant_id', '=', $branchId)            
            ->get();

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;
        if(isset($request->download) && $request->download == 'Download'){
            $pdf = Pdf::loadView("$this->resource_folder.pdf_branch", compact('data', 'branch'))->setPaper('a4', 'portrait');
            return $pdf->download('Geomapping-Summary-'. $branch->name .'.pdf');
        }
        if(isset($request->detailed_download) && $request->detailed_download == 'Detailed Download'){
            ini_set('memory_limit', '10000M');
            ini_set('max_execution_time', '0');
            $routes = DB::table('routes')
                ->select( 
                    'routes.id',
                    'routes.route_name',
                    DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id) as customer_count'),
                    DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id AND wa_route_customers.status IN ("verified", "duplicate", "approved", "rejected") ) as geomapped_customer_count'),
                    DB::raw('(SELECT COUNT(*) FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id AND wa_route_customers.created_by != 0 and wa_route_customers.created_at = wa_route_customers.updated_at AND Date(wa_route_customers.created_at) > "2024-06-04 00:00:00") as new_customers'),
                    )
                ->where('restaurant_id', '=', $branchId)->get();
            $routeIds = $routes->pluck('id');
            $data =DB::table('wa_route_customers')
                ->select(
                    'wa_route_customers.route_id',
                    'wa_route_customers.name',
                    'wa_route_customers.bussiness_name',
                    'wa_route_customers.status',
                    'wa_route_customers.phone',
                    'delivery_centres.name as center'
                )
                ->leftJoin('delivery_centres', 'delivery_centres.id', '=', 'wa_route_customers.delivery_centres_id')
                ->whereIn('wa_route_customers.route_id', $routeIds)
                ->orderBy('wa_route_customers.delivery_centres_id', 'desc')
                ->get()
                ->map(function ($customer) {
                    $customer->is_mapped = in_array($customer->status, ['verified', 'approved', 'duplicate', 'rejected']) ? true : false;
                    return $customer;
                })
                ->groupBy('route_id');
               
        

            $pdf = Pdf::loadView("$this->resource_folder.detailed_pdf_branch", compact('data', 'branch', 'routes'))->setPaper('a4', 'portrait');
            return $pdf->download('Geomapping-Summary-'. $branch->name .'.pdf');
        }
        return view("$this->resource_folder.summary_show", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'logged_user_info',
            'my_permissions',
            'data',
            'branch',
        ));
    }
    public function markScheduleAsComplete(Request $request, $id)
    {
        try {
            $schedule = GeomappingSchedules::find($id);
            $schedule->status = 'completed';
            $schedule->completed_by = Auth::user()->id;
            $schedule->comment = $request->comment;
            $schedule->save();
            Session::flash('success', 'Schedule completed successfully!');
            return redirect()->route('geomapping-schedules.show', $id);
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->route('geomapping-schedules.show', $id);
        }

    }
    public function markScheduleAsHQApproved(Request $request, $id)
    {
        try {
            $schedule = GeomappingSchedules::find($id);
            $schedule->status = 'HQ-approved';
            $schedule->HQ_approved_by = Auth::user()->id;
            $schedule->save();
            Session::flash('success', 'Schedule approved successfully!');
            return redirect()->route('geomapping-schedules.show', $id);
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->route('geomapping-schedules.show', $id);
        }

    }

}
