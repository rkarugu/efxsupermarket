<?php

namespace App\Services;

use App\Alert;
use App\Model\Restaurant;
use App\Model\WaInternalRequisition;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Model\WaStoreLocationUom;
use App\Model\WaUnitOfMeasure;
use App\Models\OperationShift;
use App\Models\WaStockCountVariation;
use App\Notifications\EndOfDayStockTake;
use App\Notifications\OperationShiftClosing;
use App\Notifications\UnbalancedInvoices;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationShiftService
{
    public $date;
    public $restaurant_id;
    public $send = true;
    public function __construct($branch, $shift=null)
    {
        $this->date = today()->toDateString();
//        $this->date = Carbon::parse('2024-05-31')->toDateString();
        $this->restaurant_id = $branch->id;

        if ($shift)
        {
            $this->send = false;
            $this->restaurant_id = $shift->restaurant_id;
            $this->date =$shift->date;
        }

    }

    public function index()
    {
        $stockTake = $this->stocktake();
        $pendingReturns = $this->pendingReturns();
        $stockVsSales = $this->stockVsSales();
        $NoBins = $this->itemsWithNoBin();
        $unbalancedInvoices = $this->unbalancedInvoices();
        $payVsSales = $this->salesVsPayments();
        $shift_data = [
            'no_pending_returns'=> $pendingReturns,
            'all_items_have_bins'=> [
                'items_with_no_bin'=>$NoBins->count(),
                'status'=>$NoBins->count() == 0
            ],
            'balanced-invoices'=> [
                'unbalanced_invoices'=>$unbalancedInvoices->count(),
                'status'=>$unbalancedInvoices->count() == 0
            ],
            'stock_vs_sales'=> [
                'sales'=>$stockVsSales['sales'],
                'stock_moves'=>$stockVsSales['moves'],
                'variance'=>$stockVsSales['variance'],
                'status'=>$stockVsSales['variance'] == 0
            ],
            'payments_vs_sales'=> $payVsSales,
            'stock_take'=> $stockTake,
        ];


        /*save to db*/
        DB::beginTransaction();

        try {
            $now = now();

            // Determine if all checks passed
            $all_checks_passed = true;
            foreach ($shift_data as $check_data) {
                $check_status = is_array($check_data) ? (isset($check_data['status']) ? $check_data['status'] : true) : $check_data;
                if (!$check_status) {
                    $all_checks_passed = false;
                    break;
                }
            }
            // Insert operational shift
            $current_shift = DB::table('operation_shifts')
                ->where('restaurant_id', $this->restaurant_id)
                ->where('date', $this->date)
                ->first();

            // Update the current shift
            if ($current_shift) {
                DB::table('operation_shifts')
                    ->where('id', $current_shift->id)
                    ->update([
                        'open' => $all_checks_passed,
                        'balanced' => $all_checks_passed,
                        'updated_at' => Carbon::now(),
                    ]);
                $operation_shift_id = $current_shift->id;
            } else {
                // Insert a new operational shift if it doesn't exist
                $operation_shift_id = DB::table('operation_shifts')->insertGetId([
                    'date' => $this->date,
                    'open' => $all_checks_passed,
                    'restaurant_id' => $this->restaurant_id,
                    'balanced' => $all_checks_passed,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Insert shift checks
            foreach ($shift_data as $check_name => $check_data) {
                if (is_array($check_data)) {
                    $status = isset($check_data['status']) ? $check_data['status'] : true;
                } else {
                    $status = $check_data;
                }

                DB::table('operation_shift_checks')->updateOrInsert([
                    'operation_shift_id' => $operation_shift_id,
                    'check_name' => $check_name,
                ],[
                    'operation_shift_id' => $operation_shift_id,
                    'check_name' => $check_name,
                    'status' => $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $shift_check = DB::table('operation_shift_checks')
                    ->where('operation_shift_id', $operation_shift_id)
                    ->where('check_name', $check_name)
                    ->first();

                // Insert check details if available
                if (is_array($check_data)) {
                    foreach ($check_data as $detail_name => $detail_value) {
                        DB::table('operation_shift_check_details')->updateOrInsert([
                            'operation_shift_check_id' => $shift_check->id,
                            'detail_name' => $detail_name,
                        ],[
                            'operation_shift_check_id' => $shift_check->id,
                            'detail_name' => $detail_name,
                            'detail_value' => $detail_value,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            // Create a new operational shift for the next day if it doesn't exist
            DB::commit();


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to save operation shift: ' . $e->getMessage()], 500);
        }



        /*send Notification */
        if ($operation_shift_id)
        {
            if ($this->send)
            {
                $shift = OperationShift::with('shiftChecks.checkDetails')->findOrFail($operation_shift_id);
                $this->sendNotification($shift, $shift_data);
            }
//            $shift = OperationShift::with('shiftChecks.checkDetails')->findOrFail($operation_shift_id);
//            $this->sendNotification($shift, $shift_data);
        }


    }

    public function sendNotification($shift,$shift_data)
    {
        /*get recipients*/
        $alert = Alert::where('alert_name','close-operational-shift')->first();

        $recipients =[];
        if ($alert instanceof Alert) {
            $recipientType = $alert->recipient_type;
            if ($recipientType === 'user') {
                $ids = explode(',', $alert->recipients);
                $recipients = User::whereIn('id', $ids)->get();
            } else if ($recipientType === 'role') {
                // Fetch users with the specified role
                $roleids = explode(',', $alert->recipients);
                $recipients = User::whereIn('role_id', $roleids)->get();
            }

            if ($recipients) {
                foreach ($recipients as $recipient) {
                    $data = [
                        'branch'=> Restaurant::find($this->restaurant_id)->name,
                        'shift'=> $shift,
                        'shift_data'=> $shift_data,
                    ];
                    $recipient->notify(new OperationShiftClosing($data));
                    $recipient->notify(new EndOfDayStockTake($data));
                }
            }
        }

    }
    public function itemsWithNoBin()
    {
        $uniqueInventoryItemsWithoutBin = DB::table('wa_internal_requisition_items as requisition')
            ->join('wa_inventory_items as item', 'requisition.wa_inventory_item_id', '=', 'item.id')
            ->leftJoin('wa_inventory_location_uom as location', function ($join) {
                $join->on('item.id', '=', 'location.inventory_id')
                    ->join('wa_location_and_stores as store', 'location.location_id', '=', 'store.id')
                    ->where('store.wa_branch_id', '=', $this->restaurant_id);
            })
            ->whereDate('requisition.created_at', $this->date)
            ->whereNull('location.id')
            ->distinct()
            ->get(['item.*']);

        return  $uniqueInventoryItemsWithoutBin;
    }

    public function unbalancedInvoices()
    {
        $invoices = WaInternalRequisition::whereDate('requisition_date', $this->date)
            ->with('getRelatedItem','stockMoves','debtorTrans')
            ->withCount(['stockMoves', 'getRelatedItem'])
            ->get();

        $unbalance_invoices = [];
        foreach ($invoices as $invoice)
        {
            /*check  invoice items  vs stock moves*/
            $item_count = $invoice->get_related_item_count;
            $move_cont = $invoice->stock_moves_count;
            if ($item_count != $move_cont)
            {
                $unbalance_invoices[]= $invoice;
            }

            /*check invoice amount vs debtor trans amount*/
            $invoice_total = (int) $invoice->getOrderTotalForEsd();
            $debtor_tran_amount = (int) @$invoice->debtorTrans->amount;
            if ($invoice_total != $debtor_tran_amount)
            {
                $unbalance_invoices[]= $invoice;
            }
        }
        $items = collect($unbalance_invoices)->unique();
        return $items;
    }

    public function stockVsSales()
    {

        $movesTotal = WaStockMove::where('document_no', 'like', 'INV-%')
            ->whereDate('created_at', '>=', $this->date)
            ->whereDate('created_at', '<=', $this->date)
            ->where('restaurant_id', $this->restaurant_id)
            ->get()
            ->sum('price');

        /*Sales*/
        $raw_sales = $this->getSalesForSingleDay();
        $sales  = (int)$raw_sales;
        $moves = (int)$movesTotal;
        $variance  = $sales - $moves;

        return [
            'sales' =>$raw_sales,
            'moves' => $movesTotal,
            'variance' => $variance,
        ];
    }

    function getSalesForSingleDay($date = null) {
        if ($date)
        {
            $date = $date->toDateString();
        }else
        {
            $date =$this->date;
        }

        $restaurant_id = $this->restaurant_id;

        $totalCostWithVat = (float) DB::table('wa_internal_requisition_items')
            ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
            ->join('routes', 'wa_internal_requisitions.route_id', '=', 'routes.id')
            ->where('routes.restaurant_id', $this->restaurant_id)
            ->whereDate('wa_internal_requisitions.created_at', '>=', $date)
            ->whereDate('wa_internal_requisitions.created_at', '<=', $date)
            ->sum('wa_internal_requisition_items.total_cost_with_vat');

        return  $totalCostWithVat ?? 0;
    }
    function getPaymentsForSingleDay() {
        $date =$this->date;
        $restaurant_id = $this->restaurant_id;
        $payments = DB::table('wa_debtor_trans')
            ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', '=', 'wa_customers.id')
            ->join('routes', 'wa_customers.route_id', '=', 'routes.id')
            ->select(
                DB::raw('SUM(wa_debtor_trans.amount) as payments')
            )
            ->where('wa_debtor_trans.document_no', 'LIKE', 'RCT-%')
            ->where('wa_debtor_trans.trans_date', $date)
            ->where('routes.restaurant_id', $restaurant_id)
            ->groupBy(DB::raw('DATE(wa_debtor_trans.trans_date)'))->first();

        return  abs( $payments->payments ?? 0);
    }
    function getReturnsForSingleDay() {

        $salesLedgerReturns = WaStockMove::where('document_no', 'like', 'RTN-%')
            ->whereDate('created_at', '>=', $this->date)
            ->whereDate('created_at', '<=', $this->date)
            ->where('restaurant_id', $this->restaurant_id)
            ->get()
            ->sum('price');
        return  $salesLedgerReturns ?? 0;
    }

    public function pendingReturns()
    {
        /*get Pending returns*/
        $pendingReturns = DB::table('wa_inventory_location_transfer_item_returns')
            ->join('wa_inventory_location_transfers', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
            ->select(
                'wa_inventory_location_transfer_item_returns.*',
                'wa_inventory_location_transfers.*'
            )
            ->where('wa_inventory_location_transfers.restaurant_id', $this->restaurant_id)
            ->whereDate('wa_inventory_location_transfer_item_returns.created_at', $this->date)
            ->whereRaw('received_quantity + rejected_quantity != return_quantity')
            ->get();
        $items = $pendingReturns->count();

        $data = [];
        foreach ($pendingReturns as  $return)
        {
            $k = [
                'INVOICE'=>$return -> transfer_no,
                'ROUTE'=>$return -> route,
            ];
            $data[]= $k;
        }

        $numUniqueRoutes = count(array_unique(array_column($data, 'ROUTE')));
        $numUniqueInvoices = count(array_unique(array_column($data, 'INVOICE')));
        $return_amount = $this ->getReturnsForSingleDay();

        $info = [
            'Items_Pending_Returns'=> $items,
            'No_of_Routes_Pending_Returns'=> $numUniqueRoutes,
            'Invoices_Pending_Returns'=> $numUniqueInvoices,
            'Total_return_amount'=> $return_amount,
            'status'=> $items == 0,
        ];
        return $info;
    }

    public function salesVsPayments()
    {
        /*get yesterdays sales*/
        $jana = Carbon::parse($this->date)->subDay();
        $sales = $this->getSalesForSingleDay($jana);
        $payments =  $this->getPaymentsForSingleDay();
        return [
            'status'=>true,
            'sales for '.today()->subDay()->format('Y-m-d')=>$sales,
            'payments Today'=>$payments,
        ];
    }

    public function stocktake()
    {
        $store_location = WaLocationAndStore::where('wa_branch_id', $this->restaurant_id)->first()->id;

        $totalCategoriesSubquery = DB::table('wa_inventory_items as wii')
            ->join('wa_inventory_location_uom as wilu', 'wilu.inventory_id', '=', 'wii.id')
            ->select('wilu.uom_id', DB::raw('COUNT(DISTINCT wii.wa_inventory_category_id) as total_categories'))
            ->groupBy('wilu.uom_id');

        $userSubquery = DB::table('users')
            ->join('wa_stock_count_variation', 'users.id', '=', 'wa_stock_count_variation.user_id')
            ->select('users.name', 'wa_stock_count_variation.uom_id');

        $location_bin = DB::table('wa_location_store_uom')
            ->where('location_id', $store_location)
            ->select('uom_id');

        $results = DB::table('wa_unit_of_measures as bin')
            ->leftJoin('wa_stock_count_variation as wscv', function ($join) use ($store_location) {
                $join->on('bin.id', '=', 'wscv.uom_id')
                    ->where('wscv.wa_location_and_store_id', '=', $store_location)
                    ->whereDate('wscv.created_at', $this->date);
            })
            ->leftJoin('wa_inventory_items as wii', 'wscv.wa_inventory_item_id', '=', 'wii.id')
            ->leftJoinSub($totalCategoriesSubquery, 'total_cats', function ($join) {
                $join->on('bin.id', '=', 'total_cats.uom_id');
            })
            ->leftJoinSub($userSubquery, 'user', function ($join) {
                $join->on('bin.id', '=', 'user.uom_id');
            })
            ->select(
                'bin.title as bin',
                'user.name as user',
                DB::raw('COALESCE(total_cats.total_categories, 0) as total_categories'),
                DB::raw('COALESCE(COUNT(DISTINCT wscv.category_id), 0) as counted_categories'),
                DB::raw('COALESCE(wscv.variation, 0) as variation')
            )
            ->where(function ($query) use ($store_location) {
                $query->where('wscv.wa_location_and_store_id', $store_location)
                    ->orWhereNull('wscv.wa_location_and_store_id');
            })
            ->whereIn('bin.id', $location_bin)
            ->groupBy('bin.title', 'total_cats.total_categories')
            ->get();


        return [
            'status' => true,
            'data' => $results,
        ];


    }
}