<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\Model\Route;
use App\Model\WaRouteCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RouteSplittingController extends Controller
{
    protected $model;
    protected $permissions_module;
    protected $permissions_module_add;

    public function __construct()
    {
        $this->model = 'route-split';
        $this->permissions_module = 'view';
        $this->permissions_module_add = 'add';
    }

    public function index()
    {

        if (!can('route-split', $this->permissions_module)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Split Routes';
        $model = $this->model;

        $routes = Route::with('centers', 'centers.waRouteCustomers')->get();
        $routes->each(function ($route) {
            $route->centers->each(function ($center) {
                $center->wa_route_customers_count = $center->waRouteCustomers->count();
            });
        });
        return view('admin.utility.sales_and_receivables.split_routes', compact('title', 'model', 'routes'));
    }

    public function processRouteSplitting(Request $request)
    {

        if (!can('route-split', $this->permissions_module_add)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $route_id = $request->input('route');
        $new_route_name = $request->input('new_route_name');
        $center_ids = explode(',', $request->input('selected_centers'));

        DB::beginTransaction();
        try {

            $old_route = Route::find($route_id);

            $route = Route::create([
                'route_name' => $new_route_name,
                'restaurant_id' => $old_route->restaurant_id,
            ]);

            $oldCenterData = DeliveryCentres::whereIn('id', $center_ids)->get()->map(function ($center) use ($route) {
                $center->setAppends([]);
                $center->route_id = $route->id;
                $center->created_at = Carbon::now();
                $center->updated_at = Carbon::now();
                return $center;
            });

            $newShopInserts = [];
            foreach ($oldCenterData as $oldCenter) {
                $oldCenterId = $oldCenter->id;
                unset($oldCenter->id);
                $oldCenter->route_id = $route->id;
                $oldCenter->created_at = Carbon::now();
                $oldCenter->updated_at = Carbon::now();
                $newCenter = DeliveryCentres::create($oldCenter->toArray());
                $oldShopData = WaRouteCustomer::where('delivery_centres_id', $oldCenterId)->get()->map(function ($shop) use ($newCenter) {
                    $shop->setAppends([]);
                    $shop->route_id = $newCenter->route_id;
                    $shop->customer_id = $newCenter->customer_id;
                    $shop->created_at = Carbon::now();
                    $shop->updated_at = Carbon::now();
                    $shop->created_by = auth()->user()->id;
                    $shop->delivery_centres_id = $newCenter->id;
                    unset($shop->id);
                    return $shop;
                })->toArray();

                $newShopInserts = array_merge($newShopInserts, $oldShopData);
            }

            DeliveryCentres::whereIn('id', $center_ids)->update(['name' => DB::raw("concat(name, ' - old')")]);

            WaRouteCustomer::whereIn('delivery_centres_id', $center_ids)->update([
                'phone' => DB::raw("concat(phone, ' - old')"),
                'status' => 'dormant'
            ]);

            WaRouteCustomer::insert($newShopInserts);

            DB::commit();
            return response()->json(['message' => 'Route split complete'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
