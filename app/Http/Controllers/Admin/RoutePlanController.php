<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\RoutePlan;
use App\RoutePlanCentre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoutePlanController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'route-plan-manager';
        $this->title = 'Route Plan Manager';
        $this->pmodule = 'route-plan-manager';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function viewRoutePlan(Request $request, $route_id)
    {

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $plans = RoutePlan::where('route_id', $route_id)->get();


        return view('admin.Routeplan.index', compact('plans', 'title', 'model', 'pmodule', 'route_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $route_id)
    {
        //

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $centers = DeliveryCentres::where('route_id', $route_id)->get();

        return view('admin.Routeplan.create', compact('title', 'model', 'pmodule', 'route_id', 'centers'));
    }

    public function createRoutePlan(Request $request, $route_id)
    {
        //

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;


        return view('admin.Routeplan.create', compact('title', 'model', 'pmodule', 'route_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'total_distance' => 'required',
            'total_time' => 'required',
            'total_fuel' => 'required',
            'route_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'result' => 0]);
        }

        // dd($request->all());

        $plan = new RoutePlan();
        $plan->total_distance = $request->total_distance;
        $plan->total_time = $request->total_time;
        $plan->total_fuel = $request->total_fuel;
        $plan->route_id = $request->route_id;
        $plan->start_time = $request->start_time;
        $plan->end_time = $request->end_time;
        $plan->save();


        $centers = $request->delivery_center_id;
        $durations = $request->duration;

        foreach ($centers as $index =>  $delivery_center) {

            $planCenter = RoutePlanCentre::create([
                'route_plan_id' => $plan->id,
                'delivery_centre_id' => $centers[$index],
                'duration' => $durations[$index]

            ]);
        }

        return redirect()->route('route-plan', $request->route_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RoutePlan  $routePlan
     * @return \Illuminate\Http\Response
     */
    public function show(RoutePlan $routePlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RoutePlan  $routePlan
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id)
    {
        //

        $routePlan = RoutePlan::with('routePlanCenter.centre')->where('id', $plan_id)->first();

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $centers = DeliveryCentres::where('route_id', $routePlan->route_id)->get();
        $route_id = $routePlan->route_id;

        // dd($routePlan);

        return view('admin.Routeplan.edit', compact('title', 'model', 'pmodule', 'route_id', 'centers', 'routePlan', 'route_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RoutePlan  $routePlan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RoutePlan $routePlan)
    {
        //

        $validator = Validator::make($request->all(), [
            'total_distance' => 'required',
            'total_time' => 'required',
            'total_fuel' => 'required',
            'route_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'result' => 0]);
        }

        // dd($request->all());

        $routePlan->total_distance = $request->total_distance;
        $routePlan->total_time = $request->total_time;
        $routePlan->total_fuel = $request->total_fuel;
        $routePlan->route_id = $request->route_id;
        $routePlan->start_time = $request->start_time;
        $routePlan->end_time = $request->end_time;
        $routePlan->save();


        $centers = $request->delivery_center_id;
        $durations = $request->duration;

        RoutePlanCentre::where('route_plan_id', $routePlan->id)->delete();

        // dd($centers);


        foreach ($centers as $index =>  $delivery_center) {

            $planCenter = RoutePlanCentre::create([
                'route_plan_id' => $routePlan->id,
                'delivery_centre_id' => $centers[$index],
                'duration' => $durations[$index]
            ]);
        }

        return redirect()->route('route-plan', $request->route_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RoutePlan  $routePlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoutePlan $routePlan)
    {
        //
    }
}
