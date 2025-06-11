<?php

namespace App\Http\Controllers\Admin;

use App\DeliverySchedule;
use App\Enums\FuelEntryStatus;
use App\Enums\VehicleResponsibilityTypes;
use App\FuelLpo;
use App\FuelStation;
use App\Model\Restaurant;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NewFuelEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class FuelLPOController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->model = 'fuel-lpos';
        $this->base_route = 'fuel-lpos';
        $this->resource_folder = 'admin.fuel_lpos';
        $this->base_title = 'Fuel Purchase Orders';
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!can('view', $this->model)) {
            // TODO: Implement general access denied page
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $permission = $this->mypermissionsforAModule();
        $title = $this->base_title;
        $breadcum = [$title => route("$this->base_route.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $lpos = NewFuelEntry::where('entry_status', 'pending')->get();

        return view("$this->resource_folder.index", compact('title', 'breadcum', 'base_route', 'model', 'lpos', 'permission'));
    }

    public function create(): View|RedirectResponse
    {
        if (!can('create', $this->model)) {
            // TODO: Implement general access denied page
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $title = 'Create Fuel Purchase Order';
        $breadcum = [$title => route("$this->base_route.index"), 'Create' => ''];
        $model = $this->model;
        $base_route = $this->base_route;

        $branches = Restaurant::select('name', 'id')->get();
        $vehicleResponsibilityTypes = collect(VehicleResponsibilityTypes::cases())->map(function ($type) {
            return $type->value;
        });
        $vehicles = Vehicle::all();

        return view("$this->resource_folder.create", compact('title', 'breadcum', 'base_route', 'model', 'branches', 'vehicleResponsibilityTypes', 'vehicles'));
    }

    public function showPending(Request $request): View
    {
        $date =  $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $to_date =  $request->to_date? Carbon::parse($request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        $title = 'Pending Fuel LPOs';
        $breadcum = [$title => route("$this->base_route.pending"), 'Pending' => ''];
        $model = 'pending-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpos = NewFuelEntry::whereIn('entry_status', ['fueled', 'fueled_incomplete'])
            ->whereBetween('created_at', [$date, $to_date])
            ->get();
        $branches = DB::table('restaurants')->get();

        return view('admin.fuel_lpos.pending', compact('base_route', 'breadcum', 'model', 'title', 'branches','permission','lpos'));
    }
    public function expireLpo($id)
    {
        try {
            $lpo = NewFuelEntry::find($id);
            $lpo->entry_status = FuelEntryStatus::Expired->value;
            $lpo->save();
            return redirect()->route('fuel-lpos.index')->with('success', 'Fuel LPO expired successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('fuel-lpos.index')->with('warning', $th->getMessage());
        }

    }
    public function pendingFuelEntriesDetails($id)
    {
        $title = 'Pending Fuel LPOs';
        $breadcum = [$title => route("fuel-lpos.pending"), 'Pending' => ''];
        $model = 'pending-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        $user = Auth::user();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpo = NewFuelEntry::where('id', $id)->with(['getRelatedVehicle', 'getRelatedShift'])->first();
        return view('admin.fuel_lpos.pending_details', compact('base_route', 'breadcum', 'model', 'title','permission','lpo', 'user'));

    }
    public function approveLpo($id)
    {
        $model = 'pending-fuel-lpos';

        try {
            if (!can('approve', $model)) {
                return redirect()->back()->withErrors(['errors' => 'permission denied']);
            }
            $lpo = NewFuelEntry::find($id);
            $lpo->entry_status = FuelEntryStatus::Approved->value;
            $lpo->save();
            return redirect()->route('fuel-lpos.pending')->with('success', 'Fuel LPO approved successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('fuel-lpos.pending')->with('warning', $th->getMessage());
        }
    }
    public function confirmedEntries(Request $request): View
    {
        $date =  $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $to_date = $request->date ? Carbon::parse($request->date)->endOfDay() : Carbon::now()->endOfDay();  
        $selectedBranch = request()->branch ?? 10;
        $title = 'Pending Fuel LPOs';
        $breadcum = [$title => route("$this->base_route.pending"), 'Pending' => ''];
        $model = 'confirmed-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();

        $expectedDeliveries = DeliverySchedule::whereBetween('expected_delivery_date', [$date, $to_date])->count();
        $actualDeliveries = DeliverySchedule::whereBetween('actual_delivery_date', [$date, $to_date])->count();
        $expectedFuelEntries = NewFuelEntry::whereBetween('created_at', [$date, $to_date])
            ->whereNot('entry_status', 'reactivated')
            ->where('shift_type', 'Route Deliveries');
        $fueledEntries = NewFuelEntry::whereBetween('fueling_time', [$date, $to_date])
            ->whereNot('entry_status', 'reactivated')
            ->where('shift_type', 'Route Deliveries');
        $actualCost = $fueledEntries->sum(DB::raw('fuel_price * actual_fuel_quantity'));
        $expectedFuelEntries = $expectedFuelEntries->get();
        $fueledEntries = $fueledEntries->get();
        $stationFuelPrice =  FuelStation::where('branch_id', $selectedBranch)->first()->fuel_price;
        $savedFuelValue = NewFuelEntry::whereBetween('fueling_time', [$date, $to_date])
            ->whereNot('entry_status', 'reactivated')
            ->where('shift_type', 'Route Deliveries')
            ->whereRaw('(manual_distance_covered / manual_consumption_rate) > actual_fuel_quantity')
            ->selectRaw('SUM(((manual_distance_covered / manual_consumption_rate) - actual_fuel_quantity) * fuel_price) as fuel_difference')
            ->value('fuel_difference');
        $savedFuelEntries = NewFuelEntry::whereBetween('fueling_time', [$date, $to_date])
            ->whereNot('entry_status', 'reactivated')
            ->where('shift_type', 'Route Deliveries')
            ->whereRaw('(manual_distance_covered / manual_consumption_rate) > actual_fuel_quantity')
            ->get();


        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpos = NewFuelEntry::whereIn('entry_status', ['approved'])
            ->whereBetween('created_at', [$date, $to_date])
            ->get();
        $processedLpos = NewFuelEntry::whereIn('entry_status', ['processed'])
            ->whereBetween('created_at', [$date, $to_date])
            ->get();

        $branches = DB::table('restaurants')->get();
        return view('admin.fuel_lpos.confirmed', compact(
            'base_route', 
            'breadcum', 
            'model', 
            'title', 
            'branches',
            'permission',
            'lpos',
            'processedLpos',
            'expectedDeliveries',
            'actualDeliveries',
            'fueledEntries',
            'expectedFuelEntries',
            'selectedBranch',
            'actualCost',
            'stationFuelPrice',
            'savedFuelValue',
            'savedFuelEntries',
            'date',
        ));
    }
    public function confirmFuelEntriesDetails($id)
    {
        $title = 'Approved Fuel LPOs';
        $breadcum = [$title => route("fuel-lpos.confirmed"), 'Pending' => ''];
        $model = 'confirmed-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        $user = Auth::user();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpo = NewFuelEntry::where('id', $id)->with(['getRelatedVehicle', 'getRelatedShift'])->first();
        return view('admin.fuel_lpos.confirmed_details', compact('base_route', 'breadcum', 'model', 'title','permission','lpo', 'user'));

    }
    public function confirmLpo($id)
    {
        $model = 'confirmed-fuel-lpos';

        try {
            if (!can('confirm', $model)) {
                return redirect()->back()->withErrors(['errors' => 'permission denied']);
            }
            $lpo = NewFuelEntry::find($id);
            $lpo->entry_status = FuelEntryStatus::Processed->value;
            $lpo->save();
            return redirect()->route('fuel-lpos.confirmed')->with('success', 'Fuel LPO approved successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('fuel-lpos.confirmed')->with('warning', $th->getMessage());
        }
    }
    public function confirmSelected(Request $request)
    {
        
        try {
            foreach($request->approved_lpos as $lpoId){
                $lpo = NewFuelEntry::find($lpoId);
                $lpo->entry_status =FuelEntryStatus::Processed->value;
                $lpo->save();
               
            }
            return redirect()->route('fuel-lpos.confirmed')->with('success', 'Lpos Approved Successfully');
            
        } catch (\Throwable $th) {
            return redirect()->route('fuel-lpos.confirmed')->with('warning', $th->getMessage());
        }
        
    
    }
    
    public function expiredEntries(Request $request): View
    {
        $date =  $request->date ? Carbon::parse($request->date)->toDateString() : Carbon::now()->toDateString();
        $title = 'Expired Fuel LPOs';
        $breadcum = [$title => route("$this->base_route.expired"), 'Expired' => ''];
        $model = 'expired-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpos = NewFuelEntry::where('entry_status', 'expired')
            ->whereDate('created_at', $date)
            ->get();
        $branches = DB::table('restaurants')->get();

        return view('admin.fuel_lpos.expired', compact('base_route', 'breadcum', 'model', 'title', 'branches','permission','lpos'));
    }

    public function reactivateExpiredLpo(Request $request)
    {
        
        try {
            foreach($request->expired_lpos as $lpoId){
                $lpo = NewFuelEntry::find($lpoId);
                $lpo->entry_status = FuelEntryStatus::Reactivated->value;
                $lpo->save();
                $lpoNumber = getCodeWithNumberSeries('FUEL LPO');
                $fuelEntry = NewFuelEntry::create([
                    'lpo_number' => $lpoNumber,
                    'vehicle_id' => $lpo->vehicle_id,
                    'shift_type' => $lpo->shift_type,
                    'created_at' => Carbon::now(),
                    'comments' => $lpo->comments,
                    'entry_status' => 'pending',
                    'shift_id' => $lpo->shift_id,
                    'last_fuel_entry_level' => $lpo->last_fuel_entry_level,
                    'last_fuel_entry_mileage' => $lpo->last_fuel_entry_mileage,
                    'start_shift_time' => $lpo->start_shift_time,
                    'start_shift_fuel_level' => $lpo->start_shift_fuel_level,
                    'start_shift_mileage' => $lpo->start_shift_mileage,
                    'end_shift_time' => $lpo->end_shift_time,
                    'end_shift_fuel_level' => $lpo->end_shift_fuel_level,
                    'end_shift_mileage' => $lpo->end_shift_mileage,
                    'end_shift_odometer' => $lpo->end_shift_odometer,
                    'manual_distance_covered' => $lpo->manual_distance_covered,
                    'manual_consumption_rate' => $lpo->manual_consumption_rate,
                    'required_fuel_quantity' => $lpo->required_fuel_quantity,
                    'fueling_time' => $lpo->fueling_time,
                    'post_fueling_level' => $lpo->post_fueling_level,
                    'actual_fuel_quantity' => $lpo->actual_fuel_quantity,
                    'shift_distance_estimate' => $lpo->shift_distance_estimate,
                    'shift_fuel_estimate' => $lpo->shift_fuel_estimate,
                    'shift_consumption_rate_estimate' => $lpo->shift_consumption_rate_estimate,
                    'fueled_by' => $lpo->fueled_by,
                    'fuel_station_id' => $lpo->fuel_station_id,
                    'fuel_price' => $lpo->fuel_price,
             
                ]);
                $fuelEntry->save();
                updateUniqueNumberSeries('FUEL LPO', $lpoNumber);
    
            }
            return redirect()->route('fuel-lpos.expired')->with('success', 'Expired Fuel LPOs reactivated successfully.');
            
        } catch (\Throwable $th) {
            return redirect()->route('fuel-lpos.expired')->with('warning', $th->getMessage());
        }
        
    
    }
    public function processedEntries(Request $request): View
    {
        $date =  $request->date ? Carbon::parse($request->date)->startOfDay() : Carbon::now()->startOfDay();
        $to_date = $request->toDate ? Carbon::parse($request->toDate)->endOfDay() : Carbon::now()->endOfDay();
        $title = 'Approved Fuel LPOs';
        $breadcum = [$title => route("$this->base_route.pending"), 'Pending' => ''];
        $model = 'approved-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $lpos = NewFuelEntry::where('entry_status', 'processed')
            ->whereBetween('created_at', [$date, $to_date])
            ->get();

        $branches = DB::table('restaurants')->get();
        return view('admin.fuel_lpos.processed', compact('base_route', 'breadcum', 'model', 'title', 'branches','permission','lpos'));
    }

    public function confirmedEntriesDeliveries($date, $branch): View
    {
        $date = Carbon::parse($date)->startOfDay() ;
        $to_date =  Carbon::parse($date)->endOfDay();  
        $selectedBranch = $branch;
        $title = 'Pending Fuel LPOs';
        $breadcum = [$title => route("$this->base_route.pending"), 'Pending' => ''];
        $model = 'confirmed-fuel-lpos';
        $base_route = $this->base_route;
        $permission = $this->mypermissionsforAModule();
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $branch = Restaurant::find($branch);
        $actualDeliveries = DeliverySchedule::with(['shift', 'route', 'vehicle', 'driver'])->whereBetween('actual_delivery_date', [$date, $to_date])
            ->get()
            ->map(function ($record){
                $record->fuel_entry = NewFuelEntry::where('shift_type', 'Route Deliveries')
                    ->whereNotIn('entry_status', ['reactivated, expired'])
                    ->where('shift_id', $record->id)
                    ->first();
                return $record;
            });
        $actualDeliveriesid = $actualDeliveries->pluck('id')->toArray();
        $expectedDeliveries = DeliverySchedule::with(['shift', 'route', 'vehicle', 'driver'])->whereBetween('expected_delivery_date', [$date, $to_date])->get();
        $expectedDeliveriesid = $expectedDeliveries->pluck('id')->toArray();
        $unexpectedButDelivered =  $actualDeliveries->whereNotIn('id', $expectedDeliveriesid);
        $undeliveredDeliveries = $expectedDeliveries->whereNotIn('id', $actualDeliveriesid);
        $expectedAndDeliveredRoutes = $actualDeliveries->whereIn('id', $expectedDeliveriesid);

        return view('admin.fuel_lpos.deliveries', compact(
            'base_route', 
            'breadcum', 
            'model', 
            'title', 
            'permission',
            'date',
            'branch',
            'undeliveredDeliveries',
            'expectedAndDeliveredRoutes',
            'unexpectedButDelivered',
           
        ));
    }
    
}
