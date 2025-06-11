<?php

namespace App\Http\Controllers;

use App\DeliverySchedule;
use App\Enums\FuelEntryParentTypes;
use App\Enums\VehicleResponsibilityTypes;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Models\FuelVerificationRecord;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FuelEntryConfirmationController extends Controller
{
    public function showOverviewPage(Request $request): View|RedirectResponse
    {
        if (!can('see-overview', 'fuel-entries')) {
            return returnAccessDeniedPage();
        }

        $branches = Restaurant::select('id', 'name')->get();

        $title = 'Fuel Entries - Overview';
        $model = 'fuel-entries';
        $breadcrum = ['Fuel Entries' => '', 'Overview' => ''];

        return view('chairman_fuel.overview', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function getFuelSavings(Request $request)
    {
        try {
            $fuelSavings = DB::table('fuel_entries')
                ->select(
                    DB::raw('((CEILING(manual_distance_covered / manual_consumption_rate)) * fuel_price) as dashboard_fuel'),
                    DB::raw('(actual_fuel_quantity * fuel_price) as actual_fuel')
                )
                ->join('vehicles', function ($join) {
                    $join->on('fuel_entries.vehicle_id', '=', 'vehicles.id')->where('vehicles.primary_responsibility', VehicleResponsibilityTypes::RouteDelivery);
                })
                ->whereNotNull('actual_fuel_quantity')
                ->havingRaw('dashboard_fuel > actual_fuel')
                ->get();

            $total = 0;
            foreach ($fuelSavings as $fuelSaving) {
                $total += $fuelSaving->dashboard_fuel - $fuelSaving->actual_fuel;
            }

            return $this->jsonify(['data' => manageAmountFormat($total)], 200);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSummary(Request $request): JsonResponse
    {
        try {
            $sales = DB::table('wa_internal_requisition_items')
                ->whereDate('created_at', Carbon::parse($request->fueling_date)->subDay()->toDateString())
                ->sum('total_cost_with_vat');
            $returns = DB::table('wa_inventory_location_transfer_item_returns as returns')
                ->select(
                    DB::raw("(items.selling_price * returns.received_quantity) as amount")
                )
                ->join('wa_inventory_location_transfer_items as items', 'returns.wa_inventory_location_transfer_item_id', '=', 'items.id')
                ->whereDate('returns.created_at', Carbon::parse())
                ->where('returns.status', 'received')
                ->get();

            $returns = $returns->sum('amount');

            $actualSales = $sales - $returns;

            $fuel = DB::table('fuel_entries')
                ->select(
                    DB::raw('SUM(actual_fuel_quantity * fuel_price) as fuel')
                )
                ->join('vehicles', function ($join) {
                    $join->on('fuel_entries.vehicle_id', '=', 'vehicles.id')->where('vehicles.primary_responsibility', VehicleResponsibilityTypes::RouteDelivery);
                })
                ->whereNotNull('actual_fuel_quantity')
                ->whereDate('fueling_time', '=', Carbon::parse($request->fueling_date)->toDateString())
                ->get();
            $fuel = $fuel->sum('fuel');

            $pettyCash = 0;

            return $this->jsonify(['data' => [
                'sales' => manageAmountFormat($actualSales),
                'fuel' => manageAmountFormat($fuel),
                'petty_cash' => manageAmountFormat($pettyCash),
                'profit' => manageAmountFormat($actualSales - $fuel - $pettyCash),
            ]], 200);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getVerifiedEntries(Request $request): JsonResponse
    {
        try {
//            $schedules = DeliverySchedule::latest()->with('shift')->get();
            $entries = DB::table('fuel_entries')
                ->select(
                    'fuel_entries.id as entry_id',
                    'fuel_statements.id as statement_id',
                    'fuel_statements.receipt_number',
                    'fuel_entries.lpo_number',
                    'fuel_entries.fuel_price',
                    'fuel_entries.comments',
                    'fuel_entries.actual_fuel_quantity',
                    'fuel_entries.fueling_time as fueling_date',
                    'fuel_entries.shift_id',
                    'delivery_schedules.actual_delivery_date as shift_date',
                    'attendants.name as attendant',
                    'drivers.name as driver',
                    'routes.route_name as route',
                    'routes.manual_fuel_estimate',
                    'routes.manual_distance_estimate',
                    'vehicles.license_plate_number as vehicle',
                )
                ->join('fuel_statements', 'fuel_statements.matched_fuel_entry_id', 'fuel_entries.id')
                ->leftJoin('delivery_schedules', 'fuel_entries.shift_id', 'delivery_schedules.id')
                ->leftJoin('vehicles', 'fuel_entries.vehicle_id', 'vehicles.id')
                ->leftJoin('routes', 'delivery_schedules.route_id', 'routes.id')
                ->leftJoin('users as drivers', 'delivery_schedules.driver_id', 'drivers.id')
                ->leftJoin('users as attendants', 'fuel_entries.fueled_by', 'attendants.id')
                ->whereDate('fueling_time', '=', Carbon::parse($request->fueling_date)->toDateString())
                ->get()
                ->map(function ($record) {
                    $record->total = manageAmountFormat($record->fuel_price * $record->actual_fuel_quantity);
                    $record->selected = true;

                    $delivery = DeliverySchedule::find($record->shift_id);
                    $record->tonnage = $delivery?->shift?->shift_tonnage ?? 0;
                    $record->tonnage = round($record->tonnage, 1);

                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
