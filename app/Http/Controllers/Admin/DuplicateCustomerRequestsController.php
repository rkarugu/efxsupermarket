<?php

namespace App\Http\Controllers\Admin;

use App\Model\Route;
use App\Model\Restaurant;
use Illuminate\Http\Request;
use App\Model\DeliveryCentres;

use App\Model\WaRouteCustomer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DuplicateCustomerRequestsController extends Controller
{
    protected $model;
    protected $baseRouteName;
    protected $baseTitle;
    protected $resourceFolder;
    protected $permissionsModule;
    protected $pmodule;


    public function __construct()
    {
        $this->model = 'route-customers';
        $this->baseRouteName = 'route-customers';
        $this->baseTitle = 'Route Customers';
        $this->resourceFolder = 'admin.route_customers';
        $this->pmodule = 'wa-route-customer';

        
    }
    public function index()
    {
        $title = "$this->baseTitle - Verification Requests";

        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Verification Requests' => ''
        ];

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();
        // dd($permission);

        $route_customers = WaRouteCustomer::with(['route', 'route.users', 'center'])->where('status', 'duplicate')
        ->when(request()->filled('branch'), function ($query) {
            $query->whereHas('route.branch', function ($q) {
                $q->where('id', request()->branch);
            });
        })
        ->when(request()->filled('route'), function ($query) {
            $query->whereHas('route', function ($q) {
                $q->where('id', request()->route);
            });
        });
        // ->latest()->get();

        if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
            $route_customers->whereHas('route', function ($q) use ($authuser) {
                $q->where('restaurant_id', $authuser->userRestaurent->id);
            }); 
        }

        $route_customers = $route_customers->latest()->get();

        // $branch = Restaurant::get();
        if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
            $branch = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }else {
            $branch = Restaurant::get();
        }
        
        $routes =[];
        if (request()->filled('branch')) {
            $routes = Route::when(request()->has('branch'), function ($q) {
                $q->where('restaurant_id', request()->branch);
            })->get();
        }
        

        return view("$this->resourceFolder.duplicate", [
            'route_customers' => $route_customers,
            'model' => 'route-customers-duplicate-requests',
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
            'branch' => $branch,
            'routes' => $routes,
        ]);


    }
    public function show($id)
    {
        $shopdetails = WaRouteCustomer::with(['route', 'route.users', 'center'])->find($id);
        $duplicateShopDetails = WaRouteCustomer::with(['route', 'route.users', 'center'])
            ->latest()
            ->where('phone', $shopdetails->phone)
            // ->whereIn('status', ['verified', 'approved','rejected'])
            ->whereNot('id', $id)
            ->first();
        $title = "$this->baseTitle - Show Details";
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Add' => ''
        ];
        $googleMapsApiKey = config('app.google_maps_api_key');
        $shoplocationdata['shop_lat'] = $shopdetails->lat;
        $shoplocationdata['shop_lng'] = $shopdetails->lng;
        $shoplocationdata['shop_name'] = $shopdetails->name;
        $shoplocationdata['location_name'] = $shopdetails->locationame;

        $duplicateshoplocationdata['duplicate_shop_lat'] = $duplicateShopDetails->lat;
        $duplicateshoplocationdata['duplicate_shop_lng'] = $duplicateShopDetails->lng;
        $duplicateshoplocationdata['duplicate_shop_name'] = $duplicateShopDetails->name;
        $duplicateshoplocationdata['duplicate_location_name'] = $duplicateShopDetails->locationame;

        return view("$this->resourceFolder.show_duplicate", [
            'title' => $title,
            'breadcum' => $breadcum,
            'model' => 'route-customers-duplicate-requests',
            'googleMapsApiKey' => $googleMapsApiKey,
            'shopdetails' => $shopdetails,
            'base_route_name' => $this->baseRouteName,
            'shop_location_data' => $shoplocationdata,
            'duplicate_shop_data' => $duplicateshoplocationdata,
            'duplicateShopDetails' => $duplicateShopDetails
        ]);

    }
    public function approve($id)
    {
        try {
            $shopdetails = WaRouteCustomer::find($id);
            $shopdetails->status = 'approved';
            $shopdetails->save();
            Session::flash('success', 'Customer Approved Successfully');
            return redirect()->route('duplicate-route-customers');
        } catch (\Throwable $th) {
            Session::flash('error', 'Customer Approval failed with error'. $th->getMessage() );
            return redirect()->route('duplicate-route-customers');
        }
     
    }
    public function reject($id)
    {
        try {
            $shopdetails = WaRouteCustomer::find($id);
            $shopdetails->status = 'rejected';
            $shopdetails->save();
            Session::flash('success', 'Customer Rejected Successfully');
            return redirect()->route('duplicate-route-customers');
        } catch (\Throwable $th) {
            Session::flash('error', 'Customer Approval failed with error'. $th->getMessage() );
            return redirect()->route('duplicate-route-customers');
        }
     
    }

}
