<?php

namespace App\Http\Controllers\Admin;

use App\DeliveryCentres as AppDeliveryCentres;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\Model\Route;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryCentresController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'delivery-center';
        $this->title = 'Delivery Centre';
        $this->pmodule = 'delivery-center';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //


        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $routes = Route::all();

        $centres = DeliveryCentres::all();

        return view('admin.Deliverycentres.index', compact('centres', 'model', 'title', 'pmodule', 'routes'));
    }


    public function routeDeliveryCenters($route_id)
    {
        //


        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $route = Route::find($route_id);

        $centres = DeliveryCentres::where('route_id', $route_id)->get();

        return view('admin.Deliverycentres.route_index', compact('centres', 'model', 'title', 'pmodule', 'route'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $routes = Route::all();


        return view('admin.Deliverycentres.create', compact('model', 'title', 'pmodule', 'routes'));
    }

    public function createRouteCenter(Request $request, $route_id)
    {
        //
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $route = Route::find($route_id);

        $googleMapsApiKey = config('app.google_maps_api_key');
        return view('admin.Deliverycentres.create_route_center', compact('model', 'title', 'pmodule', 'route', 'googleMapsApiKey'));
    }

    public function store(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "center_location_name" => "required",
            "center_latitude" => "required",
            "center_longitude" => "required",
            "route_id" => "required"

        ]);

        $centres = new DeliveryCentres();
        $centres->name = $request->name;
        $centres->lng = $request->center_longitude;
        $centres->lat = $request->center_latitude;
        $centres->route_id = $request->route_id;
        $centres->center_location_name = $request->center_location_name;

        $centres->save();
        return redirect()->back()->with('success', 'Center added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DeliveryCentres $deliveryCentres
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryCentres $deliveryCentres)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\DeliveryCentres $deliveryCentres
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $routes = Route::all();


        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        $center = DeliveryCentres::findOrFail($id);
        return view('admin.Deliverycentres.edit', compact('center', 'model', 'title', 'pmodule', 'breadcum', 'routes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\DeliveryCentres $deliveryCentres
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "lng" => "required",
            "lat" => "required",
            "route_id" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'result' => 0]);
        }
        try {
            //code...
            $center = DeliveryCentres::findOrFail($id);
            $center->name = $request->name;
            $center->lng = $request->lng;
            $center->lat = $request->lat;
            $center->route_id = $request->route_id;
            $center->save();
            return redirect()->route($this->model . '.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong', 'result' => -1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DeliveryCentres $deliveryCentres
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        try {
            DeliveryCentres::find($id)->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
}
