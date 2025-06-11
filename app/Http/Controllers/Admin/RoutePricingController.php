<?php

namespace App\Http\Controllers\Admin;

use App\Model\Route;
use App\Model\Restaurant;
use App\Models\RoutePricing;
use Illuminate\Http\Request;
use App\Model\WaInventoryItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoutePricingController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'maintain-items';
        $this->title = 'Maintain items';
        $this->pmodule = 'maintain-items';
        $this->basePath = 'admin.route_pricing';
    }
    public function index($itemId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $inventoryItem = WaInventoryItem::find($itemId);
        $routePricing = RoutePricing::latest()->where('wa_inventory_item_id', $itemId)->get();


        if (isset($permission[$pmodule . '___route-pricing']) || $permission == 'superadmin') {

            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view($basePath . '.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'inventoryItem', 'routePricing'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function create($itemId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $breadcum = [$title => route('route.pricing.listing', $itemId), 'Create' => ''];
        // $branches = Restaurant::with('location')->get();
        $routes = Route::all();
        $authuser = Auth::user();
        $authuserlocation = $authuser->wa_location_and_store_id;
        $isAdmin = $authuser->role_id == 1;
        $hasPermission = isset($permission['maintain-items___view-all-stocks']);

        $inventoryItem = WaInventoryItem::find($itemId);
        $disableDropdown = !$isAdmin && !$hasPermission;

        if (!$isAdmin) {
            $branches = Restaurant::with('location')
                ->whereHas('location', function ($query) use ($authuserlocation) {
                    $query->where('id', $authuserlocation);
                })
                ->get();
        } else {
            $branches = Restaurant::with('location')->get();
        }


        if (isset($permission[$this->pmodule . '___route-pricing']) || $permission == 'superadmin') {

            return view($basePath . '.create', compact('title', 'model', 'breadcum', 'inventoryItem', 'branches', 'routes', 'authuser', 'permission', 'disableDropdown'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function store(Request $request, $itemId)
    {
        $inventoryItem = WaInventoryItem::findOrFail($itemId);
        $validator = Validator::make($request->all(), [
            'price' => [
                'required',
                function ($attribute, $value, $fail) use ($inventoryItem) {
                    if ($value < $inventoryItem->standard_cost) {
                        $fail("The price should be greater than the cost.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $routePricing = new RoutePricing();
        $routePricing->wa_inventory_item_id = $itemId;
        $routePricing->restaurant_id = $request->branch;
        $routePricing->route_id = implode(',', $request->routes);
        $routePricing->price = $request->price;
        $routePricing->is_flash = $request->type;
        $routePricing->created_by = getLoggeduserProfile()->id;
        $routePricing->save();

        return redirect()->route("item-centre.show", $itemId)->with('success', 'Route Pricing Created successfully');
    }
    public function getRoutesByBranch(Request $request)
    {
        $branchId = $request->input('branch_id');

        $routes = Route::where('restaurant_id', $branchId)->get();

        return response()->json(['routes' => $routes]);
    }
    public function edit($itemId, $pricingId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches  = Restaurant::all();
        $routes = Route::all();
        $authuser = Auth::user();
        $authuserlocation = $authuser->wa_location_and_store_id;
        $isAdmin = $authuser->role_id == 1;
        $hasPermission = isset($permission['maintain-items___view-all-stocks']);

        $pricing = RoutePricing::find($pricingId);
        $breadcum = [$title => route("route.pricing.listing", $itemId), 'Create' => ''];
        $disableDropdown = !$isAdmin && !$hasPermission;

        if (isset($permission[$this->pmodule . '___route-pricing']) || $permission == 'superadmin') {

            return view($basePath . '.edit', compact('title', 'model', 'breadcum', 'pricing', 'branches', 'routes', 'authuser', 'permission', 'disableDropdown'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function update(Request $request, $pricingId)
    {
        $routePricing = RoutePricing::find($pricingId);
        $inventoryItem = WaInventoryItem::findOrFail($routePricing->wa_inventory_item_id);
        $validator = Validator::make($request->all(), [
            'price' => [
                'required',
                function ($attribute, $value, $fail) use ($inventoryItem) {
                    if ($value < $inventoryItem->standard_cost) {
                        $fail("The price should be greater than the cost.");
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $routePricing->restaurant_id = $request->branch;
        $routePricing->route_id = implode(',', $request->routes);
        $routePricing->price = $request->price;
        $routePricing->is_flash = $request->type;
        $routePricing->status = $request->status;

        $routePricing->save();
        return redirect()->route("item-centre.show", $routePricing->wa_inventory_item_id)->with('success', 'Route Pricing Created successfully');
    }
}
