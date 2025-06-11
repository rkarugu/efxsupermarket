<?php

namespace App\Http\Controllers;

use App\Enums\FuelEntryParentTypes;
use App\Enums\VehicleResponsibilityTypes;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Models\CustomDeliveryShift;
use App\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomDeliveryShiftController extends Controller
{
    protected string $model;
    protected string $permissionModule;
    protected string $baseRoute;
    protected string $resourceFolder;

    public function __construct()
    {
        $this->model = 'custom-delivery-shifts';
        $this->permissionModule = 'custom-delivery-shifts';
        $this->baseRoute = 'custom-delivery-shifts';
        $this->resourceFolder = 'custom_delivery_shifts';
    }

    public function showListingPage(Request $request): View
    {
        if (!can('view', $this->permissionModule)) {
            returnAccessDeniedPage();
        }

        $title = 'Custom Delivery Shifts';
        $breadcum = [$title => route("$this->baseRoute.index"), 'Listing' => ''];
        $model = $this->model;
        $base_route = $this->baseRoute;
        $listing = CustomDeliveryShift::all();
        return view("{$this->resourceFolder}.listing", compact('listing', ''));
    }

    public function showCreatePage(Request $request): View
    {
        if (!can('create', $this->permissionModule)) {
            returnAccessDeniedPage();
        }

        $title = 'Create Custom Delivery Shift';
        $breadcum = [$title => route("$this->baseRoute.index"), 'Create' => ''];
        $model = $this->model;
        $base_route = $this->baseRoute;

        $vehicles = Vehicle::select('vehicles.id', 'vehicles.license_plate_number', 'users.name')
            ->where('primary_responsibility', VehicleResponsibilityTypes::CartonTruck)
            ->join('users', 'vehicles.driver_id', 'users.id')
            ->get();

        $shiftTypes = collect(FuelEntryParentTypes::cases())->map(function ($type) {
            return $type->value;
        });

        $branches = Restaurant::all();

        return view("{$this->resourceFolder}.create", compact('title', 'breadcum', 'model', 'base_route', 'vehicles', 'shiftTypes', 'branches'));
    }
}
