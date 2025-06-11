<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierVehicleTypeRequest;
use App\Http\Requests\UpdateSupplierVehicleTypeRequest;
use App\Models\SupplierVehicleType;

class SupplierVehicleTypeController extends Controller
{
    protected $base_route;
    protected $resource_folder;
    protected $base_title;

    public function __construct()
    {
        $this->base_title = 'Supplier Vehicle Type';
        $this->base_route = 'supplier-vehicle-type';
        $this->resource_folder = 'admin.supplier_vehicle_types';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', 'supplier-vehicle-type')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $vehicleTypes = SupplierVehicleType::all();
        $title = $this->base_title;
        $model = $this->base_route;
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;        

        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'vehicleTypes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!can('view', 'supplier-vehicle-type')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $title = $this->base_title;
        $model = $this->base_route;
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;        

        return view("$this->resource_folder.create", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierVehicleTypeRequest $request)
    {
        try {
            SupplierVehicleType::create($request->validated());
            return response()->json(['result' => 1, 'message' => 'Vehicle Type stored successfully.', 'location' => route($this->base_route.'.index')], 200);
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
            return response()->json(['result' => -1, 'error' => $msg], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierVehicleType $supplierVehicleType)
    {
        if (!can('view', 'supplier-vehicle-type')) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }
        $title = $this->base_title;
        $model = $this->base_route;
        $breadcum = [$this->base_title => '', $title => ''];
        $base_route = $this->base_route;        

        return view("$this->resource_folder.edit", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'supplierVehicleType'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierVehicleTypeRequest $request, SupplierVehicleType $supplierVehicleType)
    {
        try {
            $supplierVehicleType->update($request->validated());
            return response()->json(['result' => 1, 'message' => 'Vehicle Type updated successfully.', 'location' => route($this->base_route.'.index')], 200);
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
            return response()->json(['result' => -1, 'error' => $msg], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierVehicleType $supplierVehicleType)
    {
        try {
            $supplierVehicleType->delete();
            return response()->json(['result' => 1, 'message' => 'Vehicle Type deleted successfully.', 'location' => route($this->base_route.'.index')], 200);
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
            return response()->json(['result' => -1, 'error' => $msg], 500);
        }
    }
}
