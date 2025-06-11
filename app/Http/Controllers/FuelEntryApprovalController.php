<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Fuelentry;
use App\Model\Restaurant;
use App\Models\FuelVerificationRecord;
use App\NewFuelEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelEntryApprovalController extends Controller
{
    protected string $model = 'fuel-approval';
    protected string $permissionModule = 'fuel-approval';

    public function showVerificationPage(): View|RedirectResponse
    {
        if (!can('approve', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Approval';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Approval' => ''];

        $branches = Restaurant::select('id', 'name')->get();

        return view('fuel_approval.index', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function getCompleteRecords(Request $request): JsonResponse
    {
        try {
            $period = CarbonPeriod::create('2024-08-06', Carbon::yesterday()->toDateString());
            $days = [];
            foreach ($period as $date) {
                $days[] = $date->format('Y-m-d');
            }

            $records = [];
            $days = array_reverse($days);

            foreach ($days as $day) {
                $statementsQuery = DB::table('fuel_statements')->whereDate('timestamp', '=', $day);
                $fuelQuery = DB::table('fuel_entries')->whereDate('created_at', '=', $day);

                $grossTotal = $statementsQuery->clone()->sum('quantity') * 171.6;
                $discount = $statementsQuery->clone()->sum('quantity') * 167.6;
                $record = [
                    'date' => Carbon::parse($day)->format('d-m-Y'),
                    'branch' => 'THIKA MAKONGENI',
                    'expected' => $fuelQuery->clone()->count(),
                    'statements' => $statementsQuery->clone()->count(),
                    'direct_matches' => $statementsQuery->clone()->whereNotNull('matched_fuel_entry_id')->count(),
                    'unknown_entries' => $statementsQuery->clone()->whereNull('matched_fuel_entry_id')->where('unknown_resolved', true)->count(),
                    'unknown_entries_pending' => $statementsQuery->clone()->whereNull('matched_fuel_entry_id')->where('unknown_resolved', false)->count(),
                    'gross_total' => manageAmountFormat($grossTotal),
                    'discount' => manageAmountFormat($discount),
                    'net_total' => manageAmountFormat($grossTotal),
                ];

                $records[] = $record;
            }

            return $this->jsonify($records);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }


    public function showSingleRecordPage(Request $request): View|RedirectResponse
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Approval - Details';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Approval' => ''];

        $record = FuelVerificationRecord::whereDate('fueling_date', '=', Carbon::parse($request->date)->toDateString())->first();
        $record->fueling_date = Carbon::parse($record->fueling_date)->toDateString();

        return view('fuel_approval.show', compact('title', 'model', 'breadcrum', 'record'));
    }

    public function getSummary(Request $request): JsonResponse
    {
        try {
            $data = [];
            $record = FuelVerificationRecord::find($request->record_id);

            $entries = DB::table('fuel_entries')->whereDate('fueling_time', '=', Carbon::parse($record->fueling_date)->toDateString());

            $data['fueled_entries'] = $entries->clone()->whereNotNull('actual_fuel_quantity')->count();
            $data['verified_entries'] = $entries->clone()->where('entry_status', 'verified')->count();

            $data['unknown'] = DB::table('fuel_statements')
                ->whereDate('timestamp', '=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereNull('matched_fuel_entry_id')
                ->where('unknown_resolved', true)
                ->count();

            $data['unknown_pending'] = DB::table('fuel_statements')
                ->whereDate('timestamp', '=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereNull('matched_fuel_entry_id')
                ->where('unknown_resolved', false)
                ->count();

            return $this->jsonify($data);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getVerifiedEntries(Request $request): JsonResponse
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);

            $appUrl = env('APP_URL');

            $entries = DB::table('fuel_entries')
                ->select(
                    'fuel_entries.id as entry_id',
                    'fuel_statements.id as statement_id',
                    'fuel_statements.receipt_number',
                    'fuel_entries.lpo_number',
                    'fuel_entries.fuel_price',
                    'fuel_entries.comments',
                    'fuel_entries.actual_fuel_quantity',
                    'fuel_entries.dashboard_photo',
                    'fuel_entries.receipt_photo',
                    'fuel_entries.manual_distance_covered',
                    'fuel_entries.fueling_time as fueling_date',
                    'fuel_entries.shift_id as delivery_id',
                    'delivery_schedules.actual_delivery_date as shift_date',
                    'delivery_schedules.shift_id as salesman_shift_id',
                    'attendants.name as attendant',
                    'drivers.name as driver',
                    'routes.route_name as route',
                    'routes.manual_fuel_estimate as standard_fuel',
                    'routes.manual_distance_estimate as standard_distance',
                    'routes.estimate_tonnage',
                    'vehicles.license_plate_number as vehicle',
                    DB::raw("(actual_fuel_quantity - routes.manual_fuel_estimate) as fuel_variance"),
                    DB::raw("(select coalesce(sum(items.net_weight * order_items.quantity),0) from wa_inventory_items as items 
                    join wa_internal_requisition_items as order_items on items.id = order_items.wa_inventory_item_id 
                    join wa_internal_requisitions as orders on order_items.wa_internal_requisition_id = orders.id 
                    where orders.wa_shift_id = delivery_schedules.shift_id
                    ) as tonnage"),
                )
                ->join('fuel_statements', 'fuel_statements.matched_fuel_entry_id', 'fuel_entries.id')
                ->leftJoin('delivery_schedules', 'fuel_entries.shift_id', 'delivery_schedules.id')
                ->join('vehicles', 'fuel_entries.vehicle_id', 'vehicles.id')
                ->leftJoin('routes', 'delivery_schedules.route_id', 'routes.id')
                ->leftJoin('users as drivers', 'delivery_schedules.driver_id', 'drivers.id')
                ->leftJoin('users as attendants', 'fuel_entries.fueled_by', 'attendants.id')
                ->whereDate('fueling_time', '=', Carbon::parse($record->fueling_date))
                ->where('entry_status', 'verified')
                ->get()
                ->map(function ($record) use ($appUrl) {
                    $record->raw_total = round($record->fuel_price * $record->actual_fuel_quantity, 2);
                    $record->total = manageAmountFormat($record->fuel_price * $record->actual_fuel_quantity);
                    $record->tonnage = round($record->tonnage / 1000, 1);
                    $record->fuel_variance = round($record->fuel_variance, 2);

                    $record->selected = false;

                    $record->dashboard_photo = "$appUrl/uploads/dashboard_photos/$record->dashboard_photo";
                    $record->receipt_photo = "$appUrl/uploads/dashboard_photos/$record->receipt_photo";

                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getUnknownPayments(Request $request)
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);
            $payments = DB::table('fuel_statements')
                ->select(
                    '*',
                    DB::raw("(quantity * terminal_price) as fuel_total")
                )
                ->whereDate('timestamp', '=', Carbon::parse($record->fueling_date))
                ->whereNull('matched_fuel_entry_id')
                ->where('unknown_resolved', true)
                ->get()->map(function ($record) {
                    $record->raw_fuel_total = $record->fuel_total;
                    $record->fuel_total = manageAmountFormat($record->fuel_total);
                    $record->selected = false;

                    return $record;
                });

            return $this->jsonify($payments);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getUnknownPending(Request $request)
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);
            $payments = DB::table('fuel_statements')
                ->select(
                    '*',
                    DB::raw("(quantity * terminal_price) as fuel_total")
                )
                ->whereDate('timestamp', '=', Carbon::parse($record->fueling_date))
                ->whereNull('matched_fuel_entry_id')
                ->where('unknown_resolved', false)
                ->get()->map(function ($record) {
                    $record->raw_fuel_total = $record->fuel_total;
                    $record->fuel_total = manageAmountFormat($record->fuel_total);
                    return $record;
                });

            return $this->jsonify($payments);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function approveVerified(Request $request): JsonResponse
    {
        try {
            $ids = $request->ids;
            NewFuelEntry::whereIn('id', $ids)->update([
                'entry_status' => 'approved',
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
