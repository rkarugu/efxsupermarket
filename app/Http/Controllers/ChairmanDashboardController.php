<?php

namespace App\Http\Controllers;

use App\DeliverySchedule;
use App\Model\Restaurant;
use App\Models\WaPettyCashRequestItem;
use App\NewFuelEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChairmanDashboardController extends Controller
{
    protected $title;
    protected $model;
    protected $pmodel;

    public function __construct()
    {
        $this->title = 'Chairman Dashboard';
        $this->model = 'chairman-dashboard';
        $this->pmodel = 'chairman-dashboard';
    }

    public function index(Request $request){
        if (can('view', 'chairmans-dashboard')) {
            $title = $this->title;
            $model = $this->model;

            $branches = Restaurant::get();
            $branchId = $request->query('branch_id', $request->user()->restaurant_id);

            return view('admin.chairman_dashboard.index', compact(
                'title',
                'model',
                'branches',
                'branchId'
            ));
        } else {
            return returnAccessDeniedPage();
        }
    }
    public function indexSalesReport(Request $request){
        if (can('view', 'chairmans-dashboard')) {
            $title = $this->title;
            $model = $this->model;

            $branches = Restaurant::get();
            $branchId = $request->query('branch_id', $request->user()->restaurant_id);

            return view('admin.chairman_dashboard.salesreport', compact(
                'title',
                'model',
                'branches',
                'branchId'
            ));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function salesmanShifts($branchId, $startDate){
        $data = DB::table('salesman_shifts as ss')
            ->select(
                'ss.*',
                'routes.route_name', 
                'routes.tonnage_target',
                'users.name as salesman',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"),
                DB::raw("(SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id
                    AND wa_route_customers.deleted_at IS NULL
                ) as customer_count"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE visited = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as shift_met_customers"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE order_taken = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as met_with_order"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE order_taken = '0'
                    AND visited = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as met_without_order"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE visited != '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as unmet"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers
                    WHERE salesman_shift_id = ss.id 
                    AND salesman_shift_type = 'onsite'
                    AND visited = '1'
                ) as met_onsite"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers
                    WHERE salesman_shift_id = ss.id 
                    AND salesman_shift_type = 'offsite'
                    AND visited = '1'
                ) as met_offsite"),
                'ds.status as delivery_status',
                'ds.id as delivery_id',
                'driver.name as driver_name',
                'vehicles.license_plate_number',
                'fe.lpo_number',
                'fe.actual_fuel_quantity as fuel_consumed',
                'fe.id as fuel_entry_id',
            )
            ->leftJoin('routes', 'routes.id', 'ss.route_id')
            ->leftJoin('users', 'users.id', 'ss.salesman_id')
            ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
            ->leftJoin('fuel_entries as fe', 'fe.shift_id', 'ds.id')
            ->leftJoin('users as driver', 'driver.id', 'ds.driver_id')
            ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
            ->whereDate('ss.created_at', $startDate)
            ->where('routes.restaurant_id', $branchId)
            ->orderBy('shift_total', 'desc')
            ->havingRaw('shift_total > 0')
            ->get()->map(function($record){
                $record->shift_start_time = $record->status != "not_started" ?  Carbon::parse($record->start_time)->toTimeString() : '-';
                $record->shift_close_time = $record->status == "close" ? Carbon::parse($record->closed_time)->toTimeString() : '-';
                $record->duration = ($record->start_time != null && $record->closed_time != null) 
                ? Carbon::parse($record->start_time)
                    ->diff(Carbon::parse($record->closed_time))
                    ->format('%Hhrs %Imin %Ssec')
                : '-';            
                $record->tonnage_percentage = ($record->tonnage_target > 0) ? ($record->shift_tonnage / $record->tonnage_target) * 100 : 0;
                $record->customer_percentage = ($record->customer_count > 0) ? ($record->shift_met_customers / $record->customer_count) * 100 : 0;
                $record->shift_tonnage = manageAmountFormat($record->shift_tonnage);
                $record->shift_total = (float)$record->shift_total;
                $record->computed_shift_type = ($record->met_onsite > 0 ? "Onsite(".$record->met_onsite.")" : "").(($record->met_offsite > 0 ? " Offsite(".$record->met_offsite.")" : ""));
                return $record;
            });
        return response()->json($data);
    }
    public function salesmanShiftsWithoutOrders($branchId, $startDate){
        $data = DB::table('salesman_shifts as ss')
            ->select(
                'ss.*',
                'routes.route_name', 
                'routes.tonnage_target',
                'users.name as salesman',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"),
                DB::raw("(SELECT COUNT(wa_route_customers.id)
                    FROM wa_route_customers
                    WHERE wa_route_customers.route_id = routes.id
                    AND wa_route_customers.deleted_at IS NULL
                ) as customer_count"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE visited = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as shift_met_customers"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE order_taken = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as met_with_order"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE order_taken = '0'
                    AND visited = '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as met_without_order"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id)
                    FROM salesman_shift_customers
                    WHERE visited != '1'
                    AND salesman_shift_customers.salesman_shift_id = ss.id
                ) as unmet"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers
                    WHERE salesman_shift_id = ss.id 
                    AND salesman_shift_type = 'onsite'
                    AND visited = '1'
                ) as met_onsite"),
                DB::raw("(SELECT COUNT(salesman_shift_customers.id) 
                    FROM salesman_shift_customers
                    WHERE salesman_shift_id = ss.id 
                    AND salesman_shift_type = 'offsite'
                    AND visited = '1'
                ) as met_offsite"),
                'ds.status as delivery_status',
                'ds.id as delivery_id',
                'driver.name as driver_name',
                'vehicles.license_plate_number',
                'fe.lpo_number',
                'fe.actual_fuel_quantity as fuel_consumed',
                'fe.id as fuel_entry_id',
            )
            ->leftJoin('routes', 'routes.id', 'ss.route_id')
            ->leftJoin('users', 'users.id', 'ss.salesman_id')
            ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
            ->leftJoin('fuel_entries as fe', 'fe.shift_id', 'ds.id')
            ->leftJoin('users as driver', 'driver.id', 'ds.driver_id')
            ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
            ->whereDate('ss.created_at', $startDate)
            ->where('routes.restaurant_id', $branchId)
            ->orderBy('shift_total', 'desc')
            ->havingRaw('shift_total IS NULL OR shift_total = 0') 
            ->get()->map(function($record){
                $record->shift_start_time = $record->status != "not_started" ?  Carbon::parse($record->start_time)->toTimeString() : '-';
                $record->shift_close_time = $record->status == "close" ? Carbon::parse($record->closed_time)->toTimeString() : '-';
                $record->duration = ($record->start_time != null && $record->closed_time != null) 
                ? Carbon::parse($record->start_time)
                    ->diff(Carbon::parse($record->closed_time))
                    ->format('%Hhrs %Imin %Ssec')
                : '-';            
                $record->tonnage_percentage = ($record->tonnage_target > 0) ? ($record->shift_tonnage / $record->tonnage_target) * 100 : 0;
                $record->customer_percentage = ($record->customer_count > 0) ? ($record->shift_met_customers / $record->customer_count) * 100 : 0;
                $record->shift_tonnage = manageAmountFormat($record->shift_tonnage);
                $record->shift_total = (float)$record->shift_total;
                $record->computed_shift_type = ($record->met_onsite > 0 ? "Onsite(".$record->met_onsite.")" : "").(($record->met_offsite > 0 ? " Offsite(".$record->met_offsite.")" : ""));
                return $record;
            });
        return response()->json($data);
    }

    public function yesterdaySales($branchId, $startDate, $type){
        $yesterday =  Carbon::parse($startDate)->subDay()->toDateString();
        //yesterday sales
        $salesSubQuery = DB::table('wa_internal_requisition_items as wiri')
            ->select(
                'wir.route_id',
                DB::raw("SUM(CASE WHEN wir.invoice_type IS NULL THEN COALESCE(wiri.total_cost_with_vat, 0) ELSE 0 END) AS y_sales"),
                DB::raw("SUM(CASE WHEN wir.invoice_type = 'Backend' THEN COALESCE(wiri.total_cost_with_vat, 0) ELSE 0 END) AS y_inv_sales")
            )
            ->leftJoin('wa_internal_requisitions as wir', 'wir.id', 'wiri.wa_internal_requisition_id')
            ->whereDate('wir.created_at', $yesterday)
            ->where('wir.restaurant_id', $branchId)
            ->groupBy('wir.route_id');

        //returns  done today 
        $returnsSubQuery = DB::table('wa_inventory_location_transfer_item_returns as wiltir')
                ->select(
                    'wilt.route_id',
                    DB::raw("SUM(wiltir.received_quantity * wilti.selling_price) AS returns")
                )
                ->leftJoin('wa_inventory_location_transfer_items as wilti', 'wilti.id', 'wiltir.wa_inventory_location_transfer_item_id')
                ->leftJoin('wa_inventory_location_transfers as wilt', 'wilt.id', 'wilti.wa_inventory_location_transfer_id')
                ->whereDate('wiltir.updated_at', $startDate)
                ->where('wilt.restaurant_id', $branchId)
                ->groupBY('wilt.route_id');

        //payments done today
        $tenderEntries = DB::table('wa_tender_entries')
                ->select(
                    'routes.id',
                    DB::raw("SUM(CASE WHEN payment_providers.slug = 'equity-bank' THEN wa_tender_entries.amount ELSE 0 END) as Eazzy"),
                    DB::raw("SUM(CASE WHEN payment_providers.slug = 'kcb' THEN wa_tender_entries.amount ELSE 0 END) as Vooma"),
                    DB::raw("SUM(CASE WHEN payment_providers.slug = 'mpesa' THEN wa_tender_entries.amount ELSE 0 END) as Mpesa"),
                    DB::raw("COUNT(CASE WHEN payment_providers.slug = 'equity-bank' THEN wa_tender_entries.amount ELSE NULL END) as Eazzy_count"),
                    DB::raw("COUNT(CASE WHEN payment_providers.slug = 'kcb' THEN wa_tender_entries.amount ELSE NULL END) as Vooma_count"),
                    DB::raw("COUNT(CASE WHEN payment_providers.slug = 'mpesa' THEN wa_tender_entries.amount ELSE NULL END) as Mpesa_count")
                )
                ->leftJoin('payment_methods', 'payment_methods.id', 'wa_tender_entries.wa_payment_method_id')
                ->leftJoin('payment_providers', 'payment_providers.id', 'payment_methods.payment_provider_id')
                ->leftJoin('wa_customers', 'wa_customers.id', 'wa_tender_entries.customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                ->whereDate('wa_tender_entries.trans_date', $startDate)
                ->where('routes.restaurant_id', $branchId)
                ->groupBy('routes.id');
        $chequesSubQuery = DB::table('register_cheque')
                ->select(
                    'routes.id',
                    DB::raw('SUM(register_cheque.amount) as cheque'),
                    DB::raw('COUNT(register_cheque.amount) as count'),

                )
                ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                ->where('register_cheque.status', 'Cleared')
                ->whereDate('register_cheque.clearance_date',  $startDate)
                ->where('routes.restaurant_id', $branchId)
                ->groupBy('routes.id');
            
        $crcSubquery = DB::table('wa_debtor_trans')
                    ->select(
                        'routes.id',
                        DB::raw("SUM(wa_debtor_trans.amount) As crc")
                    )
                    ->leftJoin('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
                    ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                    ->whereDate('wa_debtor_trans.trans_date', $startDate)
                    ->where('routes.restaurant_id', $branchId)
                    ->where('wa_debtor_trans.document_no', 'like', 'CRC%')
                    ->groupBy('wa_customers.route_id');

        $data = DB::table('routes')
            ->select(
                'routes.id',
                'routes.route_name',
                'salesSubQuery.y_sales as y_sales',
                'salesSubQuery.y_inv_sales as y_inv_sales',
                'returnsSubQuery.returns as returns',
                'tenderEntries.Eazzy as eazzy',
                'tenderEntries.Vooma as vooma',
                'tenderEntries.Mpesa as mpesa',
                'chequesSubQuery.cheque as cheque',
                'crcSubquery.crc as crc',
            )
            ->leftJoinSub($salesSubQuery, 'salesSubQuery', 'salesSubQuery.route_id', 'routes.id')
            ->leftJoinSub($returnsSubQuery, 'returnsSubQuery', 'returnsSubQuery.route_id', 'routes.id')
            ->leftJoinSub($tenderEntries, 'tenderEntries', 'tenderEntries.id', 'routes.id')
            ->leftJoinSub($chequesSubQuery, 'chequesSubQuery', 'chequesSubQuery.id', 'routes.id')
            ->leftJoinSub($crcSubquery, 'crcSubquery', 'crcSubquery.id', 'routes.id')
            ->where('routes.restaurant_id', $branchId);
            if($type == 'receivables'){
                $data = $data->having('y_sales', '>', 0);
            }elseif($type == 'otherreceivables'){
                $data = $data->havingRaw('y_sales IS NULL');
            }
            $data = $data->orderBy('y_sales', 'desc')->orderBy('returns', 'desc')
            ->get()->map(function($record){
                $record->y_sales = (float)$record->y_sales;
                $record->y_inv_sales = (float)($record->y_inv_sales ?? 0);
                $record->returns = (float)($record->returns ?? 0);
                $record->eazzy = (float)($record->eazzy ?? 0);
                $record->vooma = (float)($record->vooma ?? 0);
                $record->mpesa = (float)($record->mpesa ?? 0);
                $record->cheque = (float)($record->cheque ?? 0);
                $record->crc = (float)($record->crc ?? 0);
                return $record;
            });
        return response()->json($data); 
    }

    public function debtors($branchId, $startDate){
        $yesterday = Carbon::parse($startDate)->subDay()->toDateString();
        $data = DB::table('wa_debtor_trans as trans')
                ->select(
                    'routes.route_name',
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) < "' . $yesterday . '" THEN trans.amount ELSE 0 END) as balance_bf'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $yesterday . '" AND trans.reference not like "%- INV%" THEN trans.amount ELSE 0 END) as yesterday_credits'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $yesterday . '" AND trans.reference like "%- INV%" THEN trans.amount ELSE 0 END) as debits'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $startDate . '" AND trans.document_no like "RCT%" THEN trans.amount ELSE 0 END) as credits'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $startDate . '" AND trans.document_no like "RTN%" AND trans.amount < 0 THEN trans.amount ELSE 0 END) as returns'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $startDate . '" AND trans.document_no like "RTN%" AND trans.amount > 0 THEN trans.amount ELSE 0 END) as discount_returns'),
                    DB::raw('SUM(CASE WHEN DATE(trans.trans_date) = "' . $startDate . '" AND trans.document_no like "INV%" AND trans.reference not like "%- INV%" THEN trans.amount ELSE 0 END) as discount_returns_reposts'),
                    DB::raw('MAX(CASE WHEN DATE(trans.trans_date) = "' . $startDate . '" THEN trans.trans_date ELSE NULL END) as last_trans_time')
                )
                ->join('wa_customers as customers', 'customers.id', '=', 'trans.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'customers.route_id')
                ->where('routes.restaurant_id', $branchId)
                ->groupBy('customers.customer_name')
                ->get()
                ->map(function ($record) {
                        $record->route_name = $record->route_name;
                        $record->balance_bf = (float)($record->balance_bf + $record->yesterday_credits);
                        $record->debits = (float)($record->debits);
                        $record->totalDebitsBalance = (float)($record->balance_bf + $record->debits);
                        $record->credits = (float)($record->credits);
                        $record->returns = (float)($record->returns);
                        $record->totalCollections = (float)($record->credits + $record->returns);
                        $record->discountReturns = (float)($record->discount_returns - abs($record->discount_returns_reposts));
                        $record->last_trans_time = $record->last_trans_time ? Carbon::parse($record->last_trans_time)->format('d/m/Y H:i:s') : '-';
                        $record->pd_cheques = 0;
                        $record->balance = (float)(($record->balance_bf + $record->debits) + $record->credits + $record->returns + $record->discountReturns);
                        return  $record;
            });
        return response()->json($data);
    }

    public function  salesmanPettyCash($branchId, $startDate){
        $data = DB::table('petty_cash_transactions')
            ->select(
                'petty_cash_transactions.reference',
                'travel_expense_transactions.route_id',
                'users.name as recipient',
                'users.phone_number',
                'routes.route_name',
                // 'salesman_shifts.shift_type',

                DB::raw("(COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT wir.shift_type SEPARATOR ' ')
                        FROM wa_internal_requisitions wir
                        WHERE wir.wa_shift_id = salesman_shifts.id AND wir.shift_type IS NOT NULL
                    ),
                    salesman_shifts.shift_type
                )) as shift_type"),
    
                DB::raw("(ABS(petty_cash_transactions.amount)) as amount"),
                DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                    join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                    where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                    as gross_sales"),
                DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                as tonnage"),
            )
            ->join('users', function ($join){
                $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 4);
            })
            ->join('travel_expense_transactions', 'petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id')
            ->join('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.shift_id')
            ->join('salesman_shifts', 'delivery_schedules.shift_id', '=', 'salesman_shifts.id')
            ->join('routes', 'travel_expense_transactions.route_id', '=', 'routes.id')
            ->whereDate('petty_cash_transactions.created_at', $startDate)
            ->where('petty_cash_transactions.amount', '>', 10)
            ->where('petty_cash_transactions.initial_approval_status', 'approved')
            ->where('routes.restaurant_id', $branchId)->get()
            ->map(function ($record) {
                $record->gross_sales = (float)$record->gross_sales;
                $record->tonnage = (float)$record->tonnage;
                $record->amount = (float)$record->amount;
                return $record;
            });
        return response()->json($data);
    }
    public function deliveryPettyCash($branchId, $startDate){
        $data = DB::table('petty_cash_transactions')
        ->select(
            'petty_cash_transactions.reference',
            'travel_expense_transactions.route_id',
            DB::raw("(ABS(petty_cash_transactions.amount)) as amount"),
            'users.name as recipient',
            'users.phone_number',
            'routes.route_name',
            DB::raw("'TRAVEL - DELIVERY' as description"),
            DB::raw("(select sum(wa_internal_requisition_items.total_cost_with_vat) from wa_internal_requisition_items
                join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                as gross_sales"),
            DB::raw("(select sum(COALESCE(wa_inventory_items.net_weight * wa_internal_requisition_items.quantity, 0) / 1000) from wa_internal_requisition_items
                left join wa_inventory_items on wa_internal_requisition_items.wa_inventory_item_id = wa_inventory_items.id
                left join wa_internal_requisitions on wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                where wa_internal_requisitions.route_id = routes.id and wa_internal_requisitions.wa_shift_id = delivery_schedules.shift_id)
                as tonnage"),
        )
        ->join('users', function ($join) {
            $join->on('petty_cash_transactions.user_id', '=', 'users.id')->where('users.role_id', 6);
        })
        ->join('travel_expense_transactions', 'petty_cash_transactions.parent_id', '=', 'travel_expense_transactions.id')
        ->join('delivery_schedules', 'travel_expense_transactions.shift_id', '=', 'delivery_schedules.id')
        ->join('routes', 'travel_expense_transactions.route_id', '=', 'routes.id')
        ->whereDate('petty_cash_transactions.created_at', $startDate)
        ->where('petty_cash_transactions.amount', '>', 10)
        ->where('petty_cash_transactions.initial_approval_status', 'approved')
        ->where('routes.restaurant_id', $branchId)
        ->get()
        ->map(function ($record) {
            $record->gross_sales = (float)$record->gross_sales;
            $record->tonnage = (float)$record->tonnage;
            $record->amount = (float)$record->amount;
            return $record;
        });
        return response()->json($data);

    }

    public function pettyCashRequestTypes(){

        //expenses 
        $pettyCashRequestTypes = DB::table('wa_petty_cash_request_types')
        ->select('name', 'slug', 'id')
        ->whereNotIn('id', [3, 4])
        ->orderBy('id', 'desc')
        ->get();
        return response()->json($pettyCashRequestTypes);
    }
    public function pettyCashData($branchId, $startDate){
        $pettyCashRequestTypesData = WaPettyCashRequestItem::with(['pettyCashRequest', 'pettyCashRequest.vehicle', 'deliverySchedule', 'deliverySchedule.vehicle', 'deliverySchedule.driver', 'route', 'employee','deliverySchedule.route', 'grn', 'grn.supplier', 'transfer', 'transfer.fromStoreDetail', 'transfer.toStoreDetail'])
                ->whereHas('pettyCashRequest', function($query) use($branchId){
                        $query->where('restaurant_id', $branchId)
                        ->where('final_approval', 1);
                    
                })
                ->whereDate('created_at', $startDate)->get();
        return response()->json($pettyCashRequestTypesData); 
    }

    public function fuelEntryDetails($id){
        if (can('view', 'chairmans-dashboard')) {
            $title = $this->title;
            $model = $this->model;
            $fuel_entry = NewFuelEntry::with('getRelatedVehicle', 'getRelatedShift', 'getRelatedShift.route', 'getRelatedShift.driver')->where('id', $id)->first();

            return view('admin.chairman_dashboard.fuel_entry_details', compact(
                'title',
                'model',
                'fuel_entry',
            ));
        } else {
            return returnAccessDeniedPage();
        }

    }
    public function receivablesSummary($branchId, $startDate){
       $yesterday =  Carbon::parse($startDate)->subDay()->toDateString();
            //yesterday sales
            $salesSubQuery = DB::table('wa_internal_requisition_items as wiri')
                ->select(
                    'wir.route_id',
                    DB::raw("SUM(CASE WHEN wir.invoice_type IS NULL THEN COALESCE(wiri.total_cost_with_vat, 0) ELSE 0 END) AS y_sales"),
                    DB::raw("SUM(CASE WHEN wir.invoice_type = 'Backend' THEN COALESCE(wiri.total_cost_with_vat, 0) ELSE 0 END) AS y_inv_sales")
                )
                ->leftJoin('wa_internal_requisitions as wir', 'wir.id', 'wiri.wa_internal_requisition_id')
                ->whereDate('wir.created_at', $yesterday)
                ->where('wir.restaurant_id', $branchId)
                ->groupBy('wir.route_id');
    
            //returns  done today 
            $returnsSubQuery = DB::table('wa_inventory_location_transfer_item_returns as wiltir')
                    ->select(
                        'wilt.route_id',
                        DB::raw("SUM(wiltir.received_quantity * wilti.selling_price) AS returns")
                    )
                    ->leftJoin('wa_inventory_location_transfer_items as wilti', 'wilti.id', 'wiltir.wa_inventory_location_transfer_item_id')
                    ->leftJoin('wa_inventory_location_transfers as wilt', 'wilt.id', 'wilti.wa_inventory_location_transfer_id')
                    ->whereDate('wiltir.updated_at', $startDate)
                    ->where('wilt.restaurant_id', $branchId)
                    ->groupBY('wilt.route_id');
    
            //payments done today
            $tenderEntries = DB::table('wa_tender_entries')
                    ->select(
                        'routes.id',
                        DB::raw("SUM(CASE WHEN payment_providers.slug = 'equity-bank' THEN wa_tender_entries.amount ELSE 0 END) as Eazzy"),
                        DB::raw("SUM(CASE WHEN payment_providers.slug = 'kcb' THEN wa_tender_entries.amount ELSE 0 END) as Vooma"),
                        DB::raw("SUM(CASE WHEN payment_providers.slug = 'mpesa' THEN wa_tender_entries.amount ELSE 0 END) as Mpesa"),
                        DB::raw("COUNT(CASE WHEN payment_providers.slug = 'equity-bank' THEN wa_tender_entries.amount ELSE NULL END) as Eazzy_count"),
                        DB::raw("COUNT(CASE WHEN payment_providers.slug = 'kcb' THEN wa_tender_entries.amount ELSE NULL END) as Vooma_count"),
                        DB::raw("COUNT(CASE WHEN payment_providers.slug = 'mpesa' THEN wa_tender_entries.amount ELSE NULL END) as Mpesa_count")
                    )
                    ->leftJoin('payment_methods', 'payment_methods.id', 'wa_tender_entries.wa_payment_method_id')
                    ->leftJoin('payment_providers', 'payment_providers.id', 'payment_methods.payment_provider_id')
                    ->leftJoin('wa_customers', 'wa_customers.id', 'wa_tender_entries.customer_id')
                    ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                    ->whereDate('wa_tender_entries.trans_date', $startDate)
                    ->where('routes.restaurant_id', $branchId)
                    ->groupBy('routes.id');
            $chequesSubQuery = DB::table('register_cheque')
                    ->select(
                        'routes.id',
                        DB::raw('SUM(register_cheque.amount) as cheque'),
                        DB::raw('COUNT(register_cheque.amount) as count'),
    
                    )
                    ->leftJoin('wa_customers', 'wa_customers.id', 'register_cheque.wa_customer_id')
                    ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                    ->leftJoin('users', 'users.id', 'register_cheque.deposited_by')
                    ->where('register_cheque.status', 'Cleared')
                    ->whereDate('register_cheque.clearance_date',  $startDate)
                    ->where('routes.restaurant_id', $branchId)
                    ->groupBy('routes.id');
                
            $crcSubquery = DB::table('wa_debtor_trans')
                        ->select(
                            'routes.id',
                            DB::raw("SUM(wa_debtor_trans.amount) As crc")
                        )
                        ->leftJoin('wa_customers', 'wa_customers.id', 'wa_debtor_trans.wa_customer_id')
                        ->leftJoin('routes', 'routes.id', 'wa_customers.route_id')
                        ->whereDate('wa_debtor_trans.trans_date', $startDate)
                        ->where('routes.restaurant_id', $branchId)
                        ->where('wa_debtor_trans.document_no', 'like', 'CRC%')
                        ->groupBy('wa_customers.route_id');
    
            $data = DB::table('routes')
                ->select(
                    'routes.id',
                    'routes.route_name',
                    'salesSubQuery.y_sales as y_sales',
                    'salesSubQuery.y_inv_sales as y_inv_sales',
                    'returnsSubQuery.returns as returns',
                    'tenderEntries.Eazzy as eazzy',
                    'tenderEntries.Vooma as vooma',
                    'tenderEntries.Mpesa as mpesa',
                    'chequesSubQuery.cheque as cheque',
                    'crcSubquery.crc as crc',
                )
                ->leftJoinSub($salesSubQuery, 'salesSubQuery', 'salesSubQuery.route_id', 'routes.id')
                ->leftJoinSub($returnsSubQuery, 'returnsSubQuery', 'returnsSubQuery.route_id', 'routes.id')
                ->leftJoinSub($tenderEntries, 'tenderEntries', 'tenderEntries.id', 'routes.id')
                ->leftJoinSub($chequesSubQuery, 'chequesSubQuery', 'chequesSubQuery.id', 'routes.id')
                ->leftJoinSub($crcSubquery, 'crcSubquery', 'crcSubquery.id', 'routes.id')
                ->where('routes.restaurant_id', $branchId)
                ->having('y_sales', '>', 0)
               ->orderBy('y_sales', 'desc')->orderBy('returns', 'desc')
                ->get()->map(function($record){
                    $record->y_sales = (float)$record->y_sales;
                    $record->y_inv_sales = (float)($record->y_inv_sales ?? 0);
                    $record->returns = (float)($record->returns ?? 0);
                    $record->eazzy = (float)($record->eazzy ?? 0);
                    $record->vooma = (float)($record->vooma ?? 0);
                    $record->mpesa = (float)($record->mpesa ?? 0);
                    $record->cheque = (float)($record->cheque ?? 0);
                    $record->crc = (float)($record->crc ?? 0);
                    return $record;
                });
                $data2 = DB::table('routes')
                ->select(
                    'routes.id',
                    'routes.route_name',
                    'salesSubQuery.y_sales as y_sales',
                    'salesSubQuery.y_inv_sales as y_inv_sales',
                    'returnsSubQuery.returns as returns',
                    'tenderEntries.Eazzy as eazzy',
                    'tenderEntries.Vooma as vooma',
                    'tenderEntries.Mpesa as mpesa',
                    'chequesSubQuery.cheque as cheque',
                    'crcSubquery.crc as crc',
                )
                ->leftJoinSub($salesSubQuery, 'salesSubQuery', 'salesSubQuery.route_id', 'routes.id')
                ->leftJoinSub($returnsSubQuery, 'returnsSubQuery', 'returnsSubQuery.route_id', 'routes.id')
                ->leftJoinSub($tenderEntries, 'tenderEntries', 'tenderEntries.id', 'routes.id')
                ->leftJoinSub($chequesSubQuery, 'chequesSubQuery', 'chequesSubQuery.id', 'routes.id')
                ->leftJoinSub($crcSubquery, 'crcSubquery', 'crcSubquery.id', 'routes.id')
                ->where('routes.restaurant_id', $branchId)
                ->havingRaw('y_sales IS NULL')
                ->orderBy('y_sales', 'desc')
                ->orderBy('returns', 'desc')
                ->get()->map(function($record){
                    $record->y_sales = (float)$record->y_sales;
                    $record->y_inv_sales = (float)($record->y_inv_sales ?? 0);
                    $record->returns = (float)($record->returns ?? 0);
                    $record->eazzy = (float)($record->eazzy ?? 0);
                    $record->vooma = (float)($record->vooma ?? 0);
                    $record->mpesa = (float)($record->mpesa ?? 0);
                    $record->cheque = (float)($record->cheque ?? 0);
                    $record->crc = (float)($record->crc ?? 0);
                    return $record;
                });
            $yesterdaySales = $data->sum('y_sales') + $data->sum('y_inv_sales');
            $yesterdayReturns = $data->sum('returns');
            $yesterdayCollections = $data->sum('eazzy') + $data->sum('vooma') + $data->sum('mpesa') + $data->sum('crc');
            $otherReturns = $data2->sum('returns');
            $otherCollections = $data2->sum('eazzy') + $data2->sum('vooma') + $data2->sum('mpesa') + $data2->sum('crc');
    
        return $this->jsonify([
            'yesterdaySales' => $yesterdaySales, 
            'yesterdayReturns' => $yesterdayReturns, 
            'yesterdayCollections' => $yesterdayCollections,
            'otherReturns' => $otherReturns,
            'otherCollections' => $otherCollections
        ]);


    }

    public function fuelConsumedYesterday($branchId, $startDate){
        $yesterday = Carbon::parse($startDate)->subDay()->toDateString();
        $data =  DB::table('fuel_entries')
            ->select(
                'fuel_entries.*',
                'vehicles.license_plate_number as vehicle',
                'users.name as driver',
                'routes.route_name as route',
                'routes.manual_fuel_estimate',
                'routes.manual_distance_estimate',
                'ss.created_at as shift_date',
                'ds.actual_delivery_date as delivery_date',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),  
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"),        
            )
            ->leftJoin('delivery_schedules as ds', 'ds.id', 'fuel_entries.shift_id')
            ->leftJoin('salesman_shifts as ss', 'ss.id', 'ds.shift_id')
            ->leftJoin('routes', 'routes.id', 'ss.route_id')
            ->leftJoin('users', 'users.id', 'ds.driver_id')
            ->leftJoin('vehicles', 'vehicles.id', 'fuel_entries.vehicle_id')
            ->whereDate('fuel_entries.fueling_time', $startDate)
            ->where('routes.restaurant_id', $branchId)
            ->whereIn('fuel_entries.entry_status', ['fueled', 'verified'])
            ->orderBy('ss.created_at', 'asc')
            ->orderBy('shift_total', 'desc')
            ->get()
            ->map(function($record){
                $record->shift_total = (float)($record->shift_total?? 0);
                $record->shift_tonnage = (float)($record->shift_tonnage?? 0);
                $record->shift_date = Carbon::parse($record->shift_date)->toDateString();
                $record->delivery_date = Carbon::parse($record->delivery_date)->toDateString();
                return $record;
            });
        
        return response()->json($data);
    } 
    public function unfuelledShifts($branchId, $startDate){
        $yesterday = Carbon::parse($startDate)->subDay()->toDateString();
        $data = DB::table('salesman_shifts as ss')
            ->select(
                'routes.route_name as route',
                'vehicles.license_plate_number as vehicle',
                'users.name as driver',
                'ds.status as delivery_status',
                'ds.id as delivery_id',
                'ss.created_at as shift_date',
                'ds.actual_delivery_date as delivery_date',
            DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                FROM wa_internal_requisition_items as oi
                LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                WHERE wir.wa_shift_id = ss.id
            ) AS shift_total"),  
            DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                FROM wa_internal_requisition_items as oi
                LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                WHERE wir.wa_shift_id = ss.id
            ) AS shift_tonnage"), 
            )
            ->leftJoin('routes', 'routes.id', 'ss.route_id')
            ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
            ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
            ->leftJoin('users', 'users.id', 'ds.driver_id')
            ->leftJoin('fuel_entries', 'fuel_entries.shift_id', 'ds.id')
            ->whereDate('fuel_entries.fueling_time', '!=', $startDate)
            // ->whereDate('ss.created_at', $yesterday)
            ->whereDate('ds.expected_delivery_date', $startDate)
            ->where('routes.restaurant_id', $branchId)
            ->get() 
            ->map(function($record){
                $record->shift_total = (float)($record->shift_total?? 0);
                $record->shift_tonnage = (float)($record->shift_tonnage?? 0);
                $record->shift_date = Carbon::parse($record->shift_date)->toDateString();
                $record->delivery_date = Carbon::parse($record->delivery_date)->toDateString();
                return $record;
            });
        return response()->json($data);

    }
    public function deliveries($branchId, $startDate){
        $data = DB::table('salesman_shifts as ss')
                ->select(
                    'routes.route_name as route',
                    'vehicles.license_plate_number as vehicle',
                    'users.name as driver',
                    'ds.status as delivery_status',
                    'ss.created_at as shift_date',
                    'ds.actual_delivery_date as delivery_date',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),  
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"), 
                'fuel_entries.actual_fuel_quantity',
                'fuel_entries.lpo_number',
                'fuel_entries.id as fuel_entry_id'
                )
                ->leftJoin('routes', 'routes.id', 'ss.route_id')
                ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
                ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
                ->leftJoin('users', 'users.id', 'ds.driver_id')
                ->leftJoin('fuel_entries', 'fuel_entries.shift_id', 'ds.id')
                // ->whereDate('fuel_entries.fueling_time', '!=', $startDate)
                // ->whereDate('ss.created_at', $yesterday)
                ->whereDate('ds.actual_delivery_date', $startDate)
                ->whereDate('ds.expected_delivery_date', $startDate)
                ->where('routes.restaurant_id', $branchId)
                ->orderBy('shift_total', 'desc')
                ->get() 
                ->map(function($record){
                    $record->shift_total = (float)($record->shift_total?? 0);
                    $record->shift_tonnage = (float)($record->shift_tonnage?? 0);
                    $record->actual_fuel_quantity = (float)($record->actual_fuel_quantity ?? 0);
                    $record->shift_date = Carbon::parse($record->shift_date)->toDateString();
                    $record->delivery_date = Carbon::parse($record->delivery_date)->toDateString();
                    return $record;
        });
    return response()->json($data);

    }
    public function otherDeliveries($branchId, $startDate){
        $data = DB::table('salesman_shifts as ss')
                ->select(
                    'routes.route_name as route',
                    'vehicles.license_plate_number as vehicle',
                    'users.name as driver',
                    'ds.status as delivery_status',
                    'ds.id as delivery_id',
                    'ss.created_at as shift_date',
                    'ds.actual_delivery_date as delivery_date',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),  
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"), 
                'fuel_entries.actual_fuel_quantity',
                'fuel_entries.lpo_number',
                'fuel_entries.id as fuel_entry_id'
                )
                ->leftJoin('routes', 'routes.id', 'ss.route_id')
                ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
                ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
                ->leftJoin('users', 'users.id', 'ds.driver_id')
                ->leftJoin('fuel_entries', 'fuel_entries.shift_id', 'ds.id')
                // ->whereDate('fuel_entries.fueling_time', '!=', $startDate)
                // ->whereDate('ss.created_at', $yesterday)
                ->whereDate('ds.actual_delivery_date', $startDate)
                ->whereDate('ds.expected_delivery_date', '!=',$startDate)
                ->where('routes.restaurant_id', $branchId)
                ->orderBy('shift_total', 'desc')
                ->get() 
                ->map(function($record){
                    $record->shift_total = (float)($record->shift_total?? 0);
                    $record->shift_tonnage = (float)($record->shift_tonnage?? 0);
                    $record->actual_fuel_quantity = (float)($record->actual_fuel_quantity ?? 0);
                    $record->shift_date = Carbon::parse($record->shift_date)->toDateString();
                    $record->delivery_date = Carbon::parse($record->delivery_date)->toDateString();
                    return $record;
        });
    return response()->json($data);

    }
    public function undelivered($branchId, $startDate){
        $data = DB::table('salesman_shifts as ss')
                ->select(
                    'routes.route_name as route',
                    'vehicles.license_plate_number as vehicle',
                    'users.name as driver',
                    'ds.status as delivery_status',
                    'ds.id as delivery_id',
                    'ss.created_at as shift_date',
                    'ds.expected_delivery_date as delivery_date',
                DB::raw("(SELECT SUM(COALESCE(oi.total_cost_with_vat, 0))
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_total"),  
                DB::raw("(SELECT SUM(COALESCE(wii.net_weight * oi.quantity, 0) / 1000)
                    FROM wa_internal_requisition_items as oi
                    LEFT JOIN wa_internal_requisitions wir ON wir.id = oi.wa_internal_requisition_id
                    LEFT JOIN wa_inventory_items wii ON wii.id = oi.wa_inventory_item_id 
                    WHERE wir.wa_shift_id = ss.id
                ) AS shift_tonnage"), 
                'fuel_entries.actual_fuel_quantity',
                'fuel_entries.lpo_number',
                'fuel_entries.id as fuel_entry_id'
                )
                ->leftJoin('routes', 'routes.id', 'ss.route_id')
                ->leftJoin('delivery_schedules as ds', 'ds.shift_id', 'ss.id')
                ->leftJoin('vehicles', 'vehicles.id', 'ds.vehicle_id')
                ->leftJoin('users', 'users.id', 'ds.driver_id')
                ->leftJoin('fuel_entries', 'fuel_entries.shift_id', 'ds.id')
                // ->whereDate('fuel_entries.fueling_time', '!=', $startDate)
                // ->whereDate('ss.created_at', $yesterday)
                ->whereDate('ds.actual_delivery_date', '!=',$startDate)
                ->whereDate('ds.expected_delivery_date', $startDate)
                ->where('routes.restaurant_id', $branchId)
                ->orderBy('shift_total', 'desc')
                ->get() 
                ->map(function($record){
                    $record->shift_total = (float)($record->shift_total?? 0);
                    $record->shift_tonnage = (float)($record->shift_tonnage?? 0);
                    $record->actual_fuel_quantity = (float)($record->actual_fuel_quantity ?? 0);
                    $record->shift_date = Carbon::parse($record->shift_date)->toDateString();
                    $record->delivery_date = Carbon::parse($record->delivery_date)->toDateString();
                    return $record;
        });
    return response()->json($data);

    }
    public function unassignedVehicles($branchId, $startDate){
        $assignedVehicleIds  = DeliverySchedule::whereDate('expected_delivery_date', $startDate)->whereNotNull('vehicle_id')->pluck('vehicle_id')->toArray();
        $data = DB::table('vehicles')
            ->select(
                'vehicles.license_plate_number as vehicle',
                'users.name as driver',
                'users.phone_number as driver_contact',
                'vm.name as model'
                )
            ->leftJoin('users', 'users.id', 'vehicles.driver_id')
            ->leftJoin('vehicle_models as vm', 'vm.id', 'vehicles.vehicle_model_id')
            ->where('vehicles.branch_id', $branchId)
            ->where('vehicles.primary_responsibility', 'Route Deliveries')
            ->whereNotIn('vehicles.id', $assignedVehicleIds)
            ->get();
    return response()->json($data);

    }
}
