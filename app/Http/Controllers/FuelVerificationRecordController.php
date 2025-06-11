<?php

namespace App\Http\Controllers;

use App\DeliverySchedule;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Models\FuelStatement;
use App\Models\FuelVerificationRecord;
use App\NewFuelEntry;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelVerificationRecordController extends Controller
{
    protected string $model = 'fuel-verification';
    protected string $permissionModule = 'fuel-verification';

    public function showVerificationPage(): View|RedirectResponse
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Verification';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Verification' => ''];

        $branches = Restaurant::select('id', 'name')->get();

        return view('fuel_verification.index', compact('title', 'model', 'branches', 'breadcrum'));
    }

    public function showSingleRecordPage($id): View|RedirectResponse
    {
        if (!can('view', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Fuel Verification - Details';
        $model = $this->model;
        $breadcrum = ['Fuel Management' => '', 'Verification' => ''];

        $record = FuelVerificationRecord::find($id);
        $record->fueling_date = Carbon::parse($record->fueling_date)->toDateString();

        return view('fuel_verification.show', compact('title', 'model', 'breadcrum', 'record'));
    }

    public function getVerificationRecords(Request $request): JsonResponse
    {
        try {
            $records = DB::table('fuel_verification_records')
                ->select(
                    'fuel_verification_records.*',
                    'branches.name as branch',
                    DB::raw("(select count(*) from fuel_entries where date(created_at) = date(fueling_date)) as expected_entries"),
                    DB::raw("(select count(*) from fuel_entries where date(fueling_time) = date(fueling_date) and actual_fuel_quantity is not null) as fueled_entries"),
                    DB::raw("(select count(*) from fuel_statements where date(timestamp) = date(fueling_date)) as statements"),
                    DB::raw("(select count(*) from fuel_entries where date(fueling_time) = date(fueling_date) and entry_status = 'verified') as verified_entries"),
                    DB::raw("(select count(*) from fuel_entries where date(fueling_time) = date(fueling_date) and entry_status != 'verified' and actual_fuel_quantity is not null) as missing_entries"),
                    DB::raw("(select count(*) from fuel_statements where date(timestamp) = date(fueling_date) and matched_fuel_entry_id is null) as unknown_payments")
                )
                ->join('restaurants as branches', 'fuel_verification_records.branch_id', 'branches.id')
                ->orderBy('verification_date', 'DESC')
                ->get()
                ->map(function ($record) {
                    $record->verification_date = Carbon::parse($record->verification_date)->toDateString();
                    $record->fueling_date = Carbon::parse($record->fueling_date)->toDateString();
                    return $record;
                });

            return $this->jsonify($records);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getSummary(Request $request): JsonResponse
    {
        try {
            $data = [];
            $record = FuelVerificationRecord::find($request->record_id);

            $entries = DB::table('fuel_entries')->whereDate('fueling_time', '=', Carbon::parse($record->fueling_date)->toDateString());

            $data['fueled_entries'] = $entries->clone()->whereNotNull('actual_fuel_quantity')->count();
            $data['verified_entries'] = $entries->clone()->where('entry_status', 'verified')->count();
            $data['missing'] = $entries->clone()
                ->whereNot('entry_status', 'verified')
                ->whereNotNull('actual_fuel_quantity')
                ->count();

            $data['unknown'] = DB::table('fuel_statements')
                ->whereDate('timestamp', '=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereNull('matched_fuel_entry_id')
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
                    'delivery_schedules.actual_delivery_date as shift_date',
                    'delivery_schedules.shift_id as salesman_shift_id',
                    'fuel_entries.shift_id as delivery_id',
                    'attendants.name as attendant',
                    'drivers.name as driver',
                    'routes.route_name as route',
                    'routes.manual_fuel_estimate as standard_fuel',
                    'routes.manual_distance_estimate as standard_distance',
                    'routes.estimate_tonnage',
                    'vehicles.license_plate_number as vehicle',
                    DB::raw("(actual_fuel_quantity - routes.manual_fuel_estimate) as fuel_variance"),
                    DB::raw("(select coalesce(sum(items.net_weight),0) from wa_inventory_items as items 
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

                    $record->selected = $record->fuel_variance <= 0;
                    $record->can_select = $record->selected;

                    $record->dashboard_photo = "$appUrl/uploads/dashboard_photos/$record->dashboard_photo";
                    $record->receipt_photo = "$appUrl/uploads/dashboard_photos/$record->receipt_photo";

                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getMissingEntries(Request $request): JsonResponse
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);

            $appUrl = env('APP_URL');

            $entries = DB::table('fuel_entries')
                ->select(
                    'fuel_entries.id as entry_id',
                    'fuel_statements.id as statement_id',
                    'fuel_entries.receipt_number',
                    'fuel_entries.lpo_number',
                    'fuel_entries.fuel_price',
                    'fuel_entries.comments',
                    'fuel_entries.actual_fuel_quantity',
                    'fuel_entries.dashboard_photo',
                    'fuel_entries.receipt_photo',
                    'fuel_entries.manual_distance_covered',
                    'fuel_entries.fueling_time as fueling_date',
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
                    DB::raw("(select coalesce(sum(items.net_weight),0) from wa_inventory_items as items 
                    join wa_internal_requisition_items as order_items on items.id = order_items.wa_inventory_item_id 
                    join wa_internal_requisitions as orders on order_items.wa_internal_requisition_id = orders.id 
                    where orders.wa_shift_id = delivery_schedules.shift_id
                    ) as tonnage"),
                )
                ->leftJoin('fuel_statements', 'fuel_statements.matched_fuel_entry_id', 'fuel_entries.id')
                ->join('delivery_schedules', 'fuel_entries.shift_id', 'delivery_schedules.id')
                ->join('vehicles', 'fuel_entries.vehicle_id', 'vehicles.id')
                ->join('routes', 'delivery_schedules.route_id', 'routes.id')
                ->leftJoin('users as drivers', 'delivery_schedules.driver_id', 'drivers.id')
                ->leftJoin('users as attendants', 'fuel_entries.fueled_by', 'attendants.id')
                ->whereDate('fueling_time', '=', Carbon::parse($record->fueling_date))
                ->havingNull('statement_id')
                ->whereNotNull('actual_fuel_quantity')
                ->get()
                ->map(function ($record) use ($appUrl) {
                    $record->raw_total = round($record->fuel_price * $record->actual_fuel_quantity, 2);
                    $record->total = manageAmountFormat($record->fuel_price * $record->actual_fuel_quantity);
                    $record->tonnage = round($record->tonnage / 1000, 1);
                    $record->fuel_variance = round($record->fuel_variance, 2);

                    $record->selected = $record->fuel_variance <= 0;
                    $record->can_select = $record->selected;

                    $record->dashboard_photo = "$appUrl/uploads/dashboard_photos/$record->dashboard_photo";
                    $record->receipt_photo = "$appUrl/uploads/dashboard_photos/$record->receipt_photo";

                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function runVerification(Request $request): JsonResponse
    {
        try {
//            $verificationRecord = FuelVerificationRecord::find($request->record_id);
            $statements = FuelStatement::query()
                ->whereNull('matched_fuel_entry_id')
//                ->whereDate('timestamp', '=', Carbon::parse($verificationRecord->fueling_date)->toDateString())
                ->get();

            foreach ($statements as $statement) {
                $fuelEntry = NewFuelEntry::where('receipt_number', $statement->receipt_number)->first();
                if ($fuelEntry && ($fuelEntry->actual_fuel_quantity == abs($statement->quantity))) {
                    $verificationRecord = FuelVerificationRecord::whereDate('fueling_date', '=', Carbon::parse($statement->timestamp)->toDateString())
                        ->first();

                    if ($verificationRecord) {
                        $statement->matched_fuel_entry_id = $fuelEntry->id;
                        $statement->verification_record_id = $verificationRecord->id;
                        $statement->save();

                        $fuelEntry->fueling_time = $statement->timestamp;
                        $fuelEntry->entry_status = 'verified';
                        $fuelEntry->save();
                    }
                }
            }

            return $this->jsonify(['message' => 'Verification completed successfully'], 200);
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
                ->get()->map(function ($record) {
                    $record->fuel_total = manageAmountFormat($record->fuel_total);
                    return $record;
                });

            return $this->jsonify($payments);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function resolveUnknown(Request $request): JsonResponse
    {
        try {
            FuelStatement::find($request->id)->update([
                'unknown_resolved' => true,
                'comments' => $request->comments,
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function resetUnknown(Request $request): JsonResponse
    {
        try {
            FuelStatement::find($request->id)->update([
                'unknown_resolved' => false,
                'comments' => null,
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getUnfueledRoutes(Request $request): JsonResponse
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);
            $entries = DB::table('fuel_entries')
                ->select(
                    'fuel_entries.id as entry_id',
                    'fuel_entries.lpo_number',
                    'fuel_entries.unfueled_resolved',
                    'fuel_entries.unfueled_approved',
                    'fuel_entries.comments',
                    'fuel_entries.created_at as lpo_date',
                    'fuel_entries.actual_fuel_quantity',
                    'fuel_entries.fueling_time',
                    'delivery_schedules.actual_delivery_date as shift_date',
                    'delivery_schedules.shift_id as salesman_shift_id',
                    'drivers.name as driver',
                    'routes.route_name as route',
                    'vehicles.license_plate_number as vehicle',
                    DB::raw("(select coalesce(sum(items.net_weight),0) from wa_inventory_items as items 
                    join wa_internal_requisition_items as order_items on items.id = order_items.wa_inventory_item_id 
                    join wa_internal_requisitions as orders on order_items.wa_internal_requisition_id = orders.id 
                    where orders.wa_shift_id = delivery_schedules.shift_id
                    ) as tonnage"),
                )
                ->leftJoin('delivery_schedules', 'fuel_entries.shift_id', 'delivery_schedules.id')
                ->leftJoin('vehicles', 'fuel_entries.vehicle_id', 'vehicles.id')
                ->leftJoin('routes', 'delivery_schedules.route_id', 'routes.id')
                ->leftJoin('users as drivers', 'delivery_schedules.driver_id', 'drivers.id')
                ->whereDate('fuel_entries.created_at', '=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereDate('fuel_entries.fueling_time', '!=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereNotNull('actual_fuel_quantity')
                ->get()
                ->map(function ($record) {
                    $record->tonnage = round($record->tonnage / 1000, 1);
                    $record->fueling_time = Carbon::parse($record->fueling_time)->toDateTimeString();

                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function resolveUnfueled(Request $request): JsonResponse
    {
        try {
            FuelStatement::find($request->id)->update([
                'unfueled_resolved' => true,
                'comments' => $request->comments,
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function resetUnfueled(Request $request): JsonResponse
    {
        try {
            FuelStatement::find($request->id)->update([
                'unfueled_resolved' => false,
                'comments' => null,
            ]);

            return $this->jsonify([]);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function getUnUtilizedLpos(Request $request): JsonResponse
    {
        try {
            $record = FuelVerificationRecord::find($request->record_id);
            $entries = DB::table('fuel_entries')
                ->select(
                    'fuel_entries.id as entry_id',
                    'fuel_entries.lpo_number',
                    'fuel_entries.unfueled_resolved',
                    'fuel_entries.unfueled_approved',
                    'fuel_entries.comments',
                    'fuel_entries.created_at as lpo_date',
                    'delivery_schedules.actual_delivery_date as shift_date',
                    'delivery_schedules.shift_id as salesman_shift_id',
                    'drivers.name as driver',
                    'routes.route_name as route',
                    'vehicles.license_plate_number as vehicle',
                    DB::raw("(select coalesce(sum(items.net_weight),0) from wa_inventory_items as items 
                    join wa_internal_requisition_items as order_items on items.id = order_items.wa_inventory_item_id 
                    join wa_internal_requisitions as orders on order_items.wa_internal_requisition_id = orders.id 
                    where orders.wa_shift_id = delivery_schedules.shift_id
                    ) as tonnage"),
                )
                ->leftJoin('delivery_schedules', 'fuel_entries.shift_id', 'delivery_schedules.id')
                ->leftJoin('vehicles', 'fuel_entries.vehicle_id', 'vehicles.id')
                ->leftJoin('routes', 'delivery_schedules.route_id', 'routes.id')
                ->leftJoin('users as drivers', 'delivery_schedules.driver_id', 'drivers.id')
                ->whereDate('fuel_entries.created_at', '=', Carbon::parse($record->fueling_date)->toDateString())
                ->whereNull('actual_fuel_quantity')
                ->get()
                ->map(function ($record) {
                    $record->tonnage = round($record->tonnage / 1000, 1);
                    return $record;
                });

            return $this->jsonify($entries);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
