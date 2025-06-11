<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PerformPostSaleActions;
use App\Model\RegisterCheque;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaInternalRequisition;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaLocationAndStore;
use App\Model\WaPosCashSales;
use App\Model\WaStockMove;
use App\Models\EndOfDayRoutine;
use App\Models\PosStockBreakRequest;
use App\Models\WaCloseBranchEndOfDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\BankingApproval;


class RunEodController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected $formattedDate;
    protected $previousDayDate;
    protected $startDate;
    protected $endDate;

    public function __construct()
    {
        $this->model = 'end_of_day_utility';
        $this->title = 'EOD Utility';
        $this->pmodule = 'end-of-day-utility';
        $this->formattedDate = today()->format('Y-m-d 00:00:00');
        $this->previousDayDate = Carbon::now()->subDay()->format('Y-m-d 00:00:00');
        $this->startDate = today()->format('Y-m-d 0000:00');
        $this->endDate = today()->format('Y-m-d 23:59:59');
    }
    public function index(Request $request)
    {

        if (!can('view', $this->pmodule)) {
            return returnAccessDeniedPage();
        }
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'EOD Utility';
        $model = $this->model;

        $branches = Restaurant::select('id', 'name');
        if (!can('view-all-branches', $this->pmodule)) {
            $branches = $branches->where('id', Auth::user()->id);
        }
        $branches = $branches->get();

        $start = $request->date ?? Carbon::now()->todateString();
        $end = $request->to_date ?? Carbon::now()->todateString();

        $branch = $request->branch_id ?? Auth::user()->restaurant_id;

        $eodRoutines = DB::table('end_of_day_routines')
            ->whereBetween('day', [$start, $end])
            ->where('branch_id', $branch)
            ->get();

        if (isset($permission[$pmodule . '___detailed']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.eod_routine.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'eodRoutines'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function runRoutine(Request $request)
    {
        if (!can('run-routine', $this->pmodule)) {
            return returnAccessDeniedPage();
        }
        //check cashier declaration 
        $day = $request->date;
        $branch = $request->select_branch;

        $eodRecord = DB::table("end_of_day_routines")
            ->where('day', $day)
            ->where('branch_id', $branch)
            ->where('lock_users', 0)
            ->first();
        if(!$eodRecord) {
            $allcashiers =  WaPosCashSales::whereDate('created_at', $day)
            ->where('status', 'Completed')->where('branch_id', $branch)->pluck('attending_cashier');

        foreach($allcashiers as $cashier) {
            $cashierDeclaration = DB::table('cashier_declarations')
                ->whereDate('created_at', $day)
                ->where('cashier_id', $cashier)
                ->whereNotNull('declared_at')
                ->first();
            if(!$cashierDeclaration){
                $undeclaredCashier = User::find($cashier);
                return redirect()->back()->with('warning', 'Cashier declaration not complete for cashier '. $undeclaredCashier->name);
            }

        }

        }

        
        $branchDetails = Restaurant::find($request->select_branch);
        $previousdate = $this->previousDayDate;
        $date = $request ->date ? : today();
        $eodRoutineDetails = EndOfDayRoutine::where('day', $day)->where('branch_id', $branch)->first();
     
        $model = $this->model;
        $title = 'End of Day Process';
        $breadcum = ['End of Day' => '', 'End of Day Process' => ''];

        return view('admin.eod_routine.eod_routine', compact('title', 'model', 'breadcum', 'previousdate','branch','date', 'day',  'branchDetails', 'eodRoutineDetails'));

    }

    public function fetchReturnsSummary(Request $request){
        try{
            $allReturns = DB::table('wa_pos_cash_sales_items_return')
                ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
                ->where('wa_pos_cash_sales_items_return.return_date', $request->day)
                ->where('wa_pos_cash_sales.status', 'Completed')
                ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
                ->count();
            $pendingReturns =  DB::table('wa_pos_cash_sales_items_return')
            ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
            ->where('wa_pos_cash_sales_items_return.return_date', $request->day)
            ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
            ->where('wa_pos_cash_sales.status', 'Completed')
            ->whereNull('wa_pos_cash_sales_items_return.accepted_at')
            ->count();
            $returnSummary = (object) [
                'allReturns' => $allReturns,
                'pendingReturns' => $pendingReturns,
            ];
            $pendingReturnsDetails =  DB::table('wa_pos_cash_sales_items_return')
                ->select(
                    'wa_pos_cash_sales_items_return.return_grn as return_no',
                    'wa_pos_cash_sales.sales_no as sale_no',
                    'wa_inventory_items.title as item',
                    'wa_unit_of_measures.title  as bin',
                    'wa_pos_cash_sales_items_return.return_quantity  as quantity',

                    )
                ->join('wa_pos_cash_sales_items', 'wa_pos_cash_sales_items.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_item_id')
                ->join('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
                ->join('wa_inventory_items', 'wa_inventory_items.id', 'wa_pos_cash_sales_items.wa_inventory_item_id')
                ->leftJoin('restaurants', 'restaurants.id', 'wa_pos_cash_sales_items_return.branch_id')
                ->leftJoin('wa_location_and_stores', 'restaurants.id', 'wa_location_and_stores.wa_branch_id')
                ->join('wa_inventory_location_uom', function($q) {
                    $q->on('wa_inventory_location_uom.location_id', 'wa_location_and_stores.id')
                    ->on('wa_inventory_location_uom.inventory_id', 'wa_inventory_items.id');
                })
                ->join('wa_unit_of_measures', 'wa_unit_of_measures.id', 'wa_inventory_location_uom.uom_id')
                ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
                ->where('wa_pos_cash_sales_items_return.return_date', $request->day)
                ->whereNull('wa_pos_cash_sales_items_return.accepted_at')
                ->get();

            return $this->jsonify(['returnSummary' => $returnSummary, 'pendingReturns' => $pendingReturnsDetails]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    } 
    public function verifyReturns(Request $request){
        try{
            $allReturns = DB::table('wa_pos_cash_sales_items_return')
                ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
                ->where('wa_pos_cash_sales_items_return.return_date', $request->day)
                ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
                ->count();
            $pendingReturns =  DB::table('wa_pos_cash_sales_items_return')
            ->leftJoin('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items_return.wa_pos_cash_sales_id')
            ->where('wa_pos_cash_sales_items_return.return_date', $request->day)
            ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
            ->whereNull('wa_pos_cash_sales_items_return.accepted_at')
            ->count();
            if($pendingReturns == 0){
                $returnsPassed = true;
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                $eodRoutineDetails->returns_passed = true;
                $eodRoutineDetails->save();
            }else{
                $returnsPassed = false;
            }
            return $this->jsonify(['returnsPassed' => $returnsPassed]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    } 
    public function fetchSplits(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $childrenWithoutQty = DB::table('wa_inventory_assigned_items')
                ->select(
                    'mother.title as mother_title',
                    'mother.stock_id_code as  mother_stock_id_code',
                    DB::raw("( SELECT SUM(qauntity)
                        FROM wa_stock_moves where wa_inventory_item_id = mother.id
                        AND  wa_stock_moves.wa_location_and_store_id = '$store->id'
                    ) as mother_quantity"),
                    'mother_bin.title as mother_bin_location',

                    'child.title as child_title',
                    'child.stock_id_code as  child_stock_id_code',
                    DB::raw("( SELECT SUM(qauntity)
                        FROM wa_stock_moves where wa_inventory_item_id = child.id
                        AND  wa_stock_moves.wa_location_and_store_id = '$store->id'
                    ) as child_quantity"),
                    'child_bin.title as child_bin_location',
                )
                ->leftJoin('wa_inventory_items as mother', 'wa_inventory_assigned_items.wa_inventory_item_id', 'mother.id')
                ->leftJoin('wa_inventory_items as child', 'wa_inventory_assigned_items.destination_item_id', 'child.id')
                ->leftJoin('wa_inventory_location_uom as mother_uom', function($q) use($store){
                        $q->on('mother_uom.inventory_id', 'mother.id')
                        ->where('mother_uom.location_id', $store->id);
                })
                ->leftJoin('wa_inventory_location_uom as child_uom', function($q) use($store){
                        $q->on('child_uom.inventory_id', 'child.id')
                        ->where('child_uom.location_id', $store->id);
                })
                ->leftJoin('wa_unit_of_measures as mother_bin', function($q) use($store){
                        $q->on('mother_uom.uom_id', 'mother_bin.id');
                })
                ->leftJoin('wa_unit_of_measures as child_bin', function($q) use($store){
                        $q->on('child_uom.uom_id', 'child_bin.id');
                })
                ->where('mother.status', 1)
                ->where('child.status', 1)
                ->havingRaw('mother_quantity > 0')
                ->havingRaw('child_quantity <= 0')
               ->get();

            $pendingSplitDispatch = DB::table('wa_stock_breaking')
                ->select(
                    'users.name',
                    'wa_stock_breaking.breaking_code',
                    DB::raw("(SELECT COUNT(id)
                        FROM wa_stock_breaking_items
                        WHERE  wa_stock_breaking_items.wa_stock_breaking_id = wa_stock_breaking.id
                    ) AS item_count"),
                )
                ->leftJoin('users', 'users.id', 'wa_stock_breaking.user_id')
                ->whereDate('wa_stock_breaking.created_at', $request->day)
                ->where('wa_stock_breaking.dispatched', 0)
                ->where('users.restaurant_id',  $request->branch_id)
                ->get();
           
            return $this->jsonify(['childrenWithoutQty' => $childrenWithoutQty, 'pendingSplitDispatch'=>$pendingSplitDispatch]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    } 

    public function verifySplits(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $childrenWithoutQty = DB::table('wa_inventory_assigned_items')
                ->select(
                    'mother.title as mother_title',
                    'mother.stock_id_code as  mother_stock_id_code',
                    DB::raw("( SELECT SUM(qauntity)
                        FROM wa_stock_moves where wa_inventory_item_id = mother.id
                        AND  wa_stock_moves.wa_location_and_store_id = '$store->id'
                    ) as mother_quantity"),
                    'mother_bin.title as mother_bin_location',

                    'child.title as child_title',
                    'child.stock_id_code as  child_stock_id_code',
                    DB::raw("( SELECT SUM(qauntity)
                        FROM wa_stock_moves where wa_inventory_item_id = child.id
                        AND  wa_stock_moves.wa_location_and_store_id = '$store->id'
                    ) as child_quantity"),
                    'child_bin.title as child_bin_location',
                )
                ->leftJoin('wa_inventory_items as mother', 'wa_inventory_assigned_items.wa_inventory_item_id', 'mother.id')
                ->leftJoin('wa_inventory_items as child', 'wa_inventory_assigned_items.destination_item_id', 'child.id')
                ->leftJoin('wa_inventory_location_uom as mother_uom', function($q) use($store){
                        $q->on('mother_uom.inventory_id', 'mother.id')
                        ->where('mother_uom.location_id', $store->id);
                })
                ->leftJoin('wa_inventory_location_uom as child_uom', function($q) use($store){
                        $q->on('child_uom.inventory_id', 'child.id')
                        ->where('child_uom.location_id', $store->id);
                })
                ->leftJoin('wa_unit_of_measures as mother_bin', function($q) use($store){
                        $q->on('mother_uom.uom_id', 'mother_bin.id');
                })
                ->leftJoin('wa_unit_of_measures as child_bin', function($q) use($store){
                        $q->on('child_uom.uom_id', 'child_bin.id');
                })
                ->where('mother.status', 1)
                ->where('child.status', 1)
                ->havingRaw('mother_quantity > 0')
                ->havingRaw('child_quantity <= 0')
               ->count();

            $pendingSplitDispatch = DB::table('wa_stock_breaking')
                ->select(
                    'users.name',
                    'wa_stock_breaking.breaking_code',
                    DB::raw("(SELECT COUNT(id)
                        FROM wa_stock_breaking_items
                        WHERE  wa_stock_breaking_items.wa_stock_breaking_id = wa_stock_breaking.id
                    ) AS item_count"),
                )
                ->leftJoin('users', 'users.id', 'wa_stock_breaking.user_id')
                ->whereDate('wa_stock_breaking.created_at', $request->day)
                ->where('wa_stock_breaking.dispatched', 0)
                ->where('users.restaurant_id',  $request->branch_id)
                ->count();
           
            if($childrenWithoutQty == 0 && $pendingSplitDispatch == 0){
                $splitsPassed = true;
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                $eodRoutineDetails->splits_passed = true;
                $eodRoutineDetails->save();
            }else{
                $splitsPassed = false;
            }
            return $this->jsonify(['splitsPassed' => $splitsPassed]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    }
    public function fetchBinlessItems(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $binlessItems = DB::table('wa_inventory_items')
                ->select(
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'pack_sizes.title as pack_size',
                    DB::raw("(SELECT SUM(qauntity) FROM wa_stock_moves where wa_stock_moves.wa_location_and_store_id = '$store->id' AND wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id) as qoh")

                )
                ->leftJoin('wa_inventory_location_uom', function($q)  use($store){
                    $q->on('wa_inventory_items.id', 'wa_inventory_location_uom.inventory_id')
                    ->where('wa_inventory_location_uom.location_id', $store->id);
                })
                ->leftJoin('pack_sizes', 'wa_inventory_items.pack_size_id', 'pack_sizes.id')
                ->whereNull('wa_inventory_location_uom.id')
                ->where('wa_inventory_items.status', 1)
                ->havingRaw('qoh > 0')
                ->get();
            return $this->jsonify(['binlessItems' => $binlessItems]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    } 
    public function verifyBinlessItems(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $binlessItems = DB::table('wa_inventory_items')
            ->select(
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'pack_sizes.title as pack_size',
                DB::raw("(SELECT SUM(qauntity) FROM wa_stock_moves where wa_stock_moves.wa_location_and_store_id = '$store->id' AND wa_stock_moves.wa_inventory_item_id = wa_inventory_items.id) as qoh")
            )
            ->leftJoin('wa_inventory_location_uom', function($q)  use($store){
                $q->on('wa_inventory_items.id', 'wa_inventory_location_uom.inventory_id')
                ->where('wa_inventory_location_uom.location_id', $store->id);
            })
            ->leftJoin('pack_sizes', 'wa_inventory_items.pack_size_id', 'pack_sizes.id')
            ->whereNull('wa_inventory_location_uom.id')
            ->where('wa_inventory_items.status', 1)
            ->havingRaw('qoh > 0')
            ->count();
           
            if($binlessItems == 0 ){
                $binlessItemsPassed = true;
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                $eodRoutineDetails->binless_items_passed = true;
                $eodRoutineDetails->save();
            }else{
                $binlessItemsPassed = false;
            }
            return $this->jsonify(['binlessItemsPassed' => $binlessItemsPassed]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    }

    public function fetchSalesVsStocks(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            //use internal requisition instead of pos cash sales query
            $invoicesCount = DB::table('wa_internal_requisition_items')
            ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
            ->whereDate('wa_internal_requisitions.created_at', $request->day)
            ->where(function($query){
                $query->where('requisition_no', 'like', 'CIV%')
                ->orWhere(function($query2){
                    $query2->where('requisition_no', 'like', 'INV%')
                    ->where('wa_internal_requisitions.invoice_type', 'Backend');
                } );
            })
            ->distinct('wa_internal_requisitions.requisition_no')
            ->count();
            $soldQohInv = DB::table('wa_internal_requisition_items')
                ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
                ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
                ->whereDate('wa_internal_requisitions.created_at', $request->day)
                ->where(function($query){
                    $query->where('requisition_no', 'like', 'CIV%')
                    ->orWhere(function($query2){
                        $query2->where('requisition_no', 'like', 'INV%')
                        ->where('wa_internal_requisitions.invoice_type', 'Backend');
                    } );
                })
                ->sum('wa_internal_requisition_items.quantity');
            // $soldQohPos = DB::table('wa_pos_cash_sales_items')
            //     ->join('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
            //     ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
            //     ->whereDate('wa_pos_cash_sales_items.created_at', $request->day)
            //     ->where('wa_pos_cash_sales.status', 'Completed')
            //     ->sum('wa_pos_cash_sales_items.qty');
            $soldQoh = $soldQohInv;
            $movedQoh = DB::table('wa_stock_moves')
                ->where('wa_stock_moves.wa_location_and_store_id', $store->id)
                ->where(function($query){
                    $query->where('document_no', 'like', 'CIV%')
                    ->orWhere('document_no', 'like', 'INV%' );
                })
                // ->where('document_no', 'like', 'CIV%')
                ->whereDate('wa_stock_moves.created_at', $request->day)
                ->sum('qauntity');
            $movedQoh = abs($movedQoh);
            $movesCount = DB::table('wa_stock_moves')
                ->where('wa_stock_moves.wa_location_and_store_id', $store->id)
                ->where(function($query){
                    $query->where('document_no', 'like', 'CIV%')
                    ->orWhere('document_no', 'like', 'INV%' );
                })
                // ->where('document_no', 'like', 'CIV%')
                ->whereDate('wa_stock_moves.created_at', $request->day)
                ->distinct('document_no')
                ->count();

            $soldAmountInv = DB::table('wa_internal_requisition_items')
            ->select(
                DB::raw("(SUM(wa_internal_requisition_items.selling_price * wa_internal_requisition_items.quantity)) as amount_sold")
                )
            ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
            ->whereDate('wa_internal_requisitions.created_at', $request->day)
            ->where(function($query){
                $query->where('requisition_no', 'like', 'CIV%')
                ->orWhere(function($query2){
                    $query2->where('requisition_no', 'like', 'INV%')
                    ->where('wa_internal_requisitions.invoice_type', 'Backend');
                } );
            })
            ->get()
            ->value('amount_sold') ?? 0;


            // $soldAmount = DB::table('wa_pos_cash_sales_items')
            //     ->select(
            //         DB::raw("(SUM(wa_pos_cash_sales_items.selling_price * wa_pos_cash_sales_items.qty)) as amount_sold")
            //         )
            //     ->join('wa_pos_cash_sales', 'wa_pos_cash_sales.id', 'wa_pos_cash_sales_items.wa_pos_cash_sales_id')
            //     ->where('wa_pos_cash_sales.status', 'Completed')
            //     ->where('wa_pos_cash_sales.branch_id', $request->branch_id)
            //     ->whereDate('wa_pos_cash_sales_items.created_at', $request->day)
            //     ->get()
            //     ->value('amount_sold') ?? 0;
            $soldAmount = $soldAmountInv;   
            $movedAmount =  DB::table('wa_stock_moves')
                ->select(
                    DB::raw("(abs(SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price))) as amount_sold")
                )
                ->where('wa_stock_moves.wa_location_and_store_id', $store->id)
                ->where(function($query){
                    $query->where('document_no', 'like', 'CIV%')
                    ->orWhere('document_no', 'like', 'INV%' );
                })
                // ->where('document_no', 'like', 'CIV%')
                ->whereDate('wa_stock_moves.created_at', $request->day)
                ->get()
                ->value('amount_sold') ?? 0;
            
            //fetch Payments Records 
            $posPayments = DB::table('wa_pos_cash_sales_payments')
                ->where('branch_id', $request->branch_id)
                ->whereDate('created_at', $request->day)->get(['amount']);
            $posPaymentsAmount = $posPayments->sum('amount'); 
            $posPaymentsRecords = $posPayments->count();

            //debtors
            $debtorsquery = DB::table('wa_debtor_trans as trans')
                ->join('wa_customers as customers', 'customers.id', '=', 'trans.wa_customer_id')
                ->leftJoin('routes', 'routes.id', 'customers.route_id')
                ->where('routes.restaurant_id', $request->branch_id)
                ->where('routes.is_pos_route', 0)
                ->whereDate('trans.trans_date', $request->day)
                ->where('trans.amount', '>', 0)->get(['trans.amount']);
            $debtorsAmount = $debtorsquery->sum('trans.amount');
            $debtorsRecords = $debtorsquery->count();
            

            $salesVsStocks =  (object) [
                'debtorsAmount' => manageAmountFormat($debtorsAmount),
                'debtorsRecords' => $debtorsRecords,
                'posPaymentsAmount' => manageAmountFormat($posPaymentsAmount),
                'posPaymentsRecords' =>$posPaymentsRecords,
                'invoicesCount' => $invoicesCount,
                'movesCount' => $movesCount,
                'soldQoh' => manageAmountFormat($soldQoh),
                'movedQoh' => manageAmountFormat($movedQoh),
                'soldAmount' => manageAmountFormat($soldAmount),
                'movedAmount' => manageAmountFormat($movedAmount),
            ];
            $unbalanced_invoices = [];
            $unbalancedInvoicesExist = false;
            if($soldQoh != $movedQoh || $movedAmount != $soldAmount ){
                        $unbalancedInvoicesExist = true;
                        WaInternalRequisition::whereDate('wa_internal_requisitions.created_at', $request->day)
                         ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
                         ->with('getRelatedItem','getRelatedItem.getInventoryItemDetail', 'stockMoves', 'debtorTrans')
                         ->withCount(['stockMoves', 'getRelatedItem'])
                         ->chunk(100, function ($invoices) use (&$unbalanced_invoices) {
                             foreach ($invoices as $invoice) {
                                 $item_count = $invoice->get_related_item_count;
                                 $move_count = $invoice->stock_moves_count;
            
                                 if ($item_count != $move_count) {
                                     $unbalanced_invoices[] = $invoice;
                                 }
                                
                             }
                         });
            
                     $unbalanced_invoices = collect($unbalanced_invoices)->unique();
            }
            return $this->jsonify(['salesVsStocks' => $salesVsStocks, 'unbalanced_invoices' => $unbalanced_invoices, 'unbalancedInvoicesExist' => $unbalancedInvoicesExist]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    } 
    public function verifySalesVsStocks(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $soldQoh =  DB::table('wa_internal_requisition_items')
            ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
            ->whereDate('wa_internal_requisitions.created_at', $request->day)
            ->where(function($query){
                $query->where('requisition_no', 'like', 'CIV%')
                ->orWhere(function($query2){
                    $query2->where('requisition_no', 'like', 'INV%')
                    ->where('wa_internal_requisitions.invoice_type', 'Backend');
                } );
            })
            ->sum('wa_internal_requisition_items.quantity');
            $movedQoh = DB::table('wa_stock_moves')
            ->where('wa_stock_moves.wa_location_and_store_id', $store->id)
            ->where(function($query){
                $query->where('document_no', 'like', 'CIV%')
                ->orWhere('document_no', 'like', 'INV%' );
            })
            // ->where('document_no', 'like', 'CIV%')
            ->whereDate('wa_stock_moves.created_at', $request->day)
            ->sum('qauntity');
            $movedQoh = abs($movedQoh);
            $soldAmount = DB::table('wa_internal_requisition_items')
            ->select(
                DB::raw("(SUM(wa_internal_requisition_items.selling_price * wa_internal_requisition_items.quantity)) as amount_sold")
                )
            ->join('wa_internal_requisitions', 'wa_internal_requisitions.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->where('wa_internal_requisitions.restaurant_id', $request->branch_id)
            ->whereDate('wa_internal_requisitions.created_at', $request->day)
            ->where(function($query){
                $query->where('requisition_no', 'like', 'CIV%')
                ->orWhere(function($query2){
                    $query2->where('requisition_no', 'like', 'INV%')
                    ->where('wa_internal_requisitions.invoice_type', 'Backend');
                } );
            })
            ->get()
            ->value('amount_sold') ?? 0;
            $movedAmount =  DB::table('wa_stock_moves')
                ->select(
                    DB::raw("(abs(SUM(wa_stock_moves.qauntity * wa_stock_moves.selling_price))) as amount_sold")
                )
                ->where('wa_stock_moves.wa_location_and_store_id', $store->id)
                ->where(function($query){
                    $query->where('document_no', 'like', 'CIV%')
                    ->orWhere('document_no', 'like', 'INV%' );
                })
                // ->where('document_no', 'like', 'CIV%')
                ->whereDate('wa_stock_moves.created_at', $request->day)
                ->get()
                ->value('amount_sold') ?? 0;

            if($soldQoh == $movedQoh && $movedAmount == $soldAmount ){
                $unbalancedTransactionsPassed = true;
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                $eodRoutineDetails->unbalanced_transactions_passed = true;
                $eodRoutineDetails->save();
            }else{
                $unbalancedTransactionsPassed = false;
            }
            return $this->jsonify(['unbalancedTransactionsPassed' => $unbalancedTransactionsPassed]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    }
    public function balanceTransactions(Request $request){
        try{
            foreach($request->invoice_ids as $invoice){
                DB::beginTransaction();
                $requisition = WaInternalRequisition::with('getRelatedItem','getRelatedItem.getInventoryItemDetail', 'stockMoves', 'debtorTrans')->find($invoice);
                WaStockMove::where('document_no', $requisition->requisition_no)->delete();
                $trasfer  = WaInventoryLocationTransfer::where('transfer_no', $requisition->requisition_no)->first();
                if ($trasfer)
                {
                    WaInventoryLocationTransferItem::where('wa_inventory_location_transfer_id', $trasfer->id)->delete();
                    $trasfer->delete();
                }
                WaDebtorTran::where('document_no',  $requisition->requisition_no)->delete();
                WaGlTran::where('transaction_no', $requisition->requisition_no)->delete();
                PerformPostSaleActions::dispatch($requisition)->afterCommit();
                DB::commit();
            }
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
          
            return $this->jsonify(['message' => 'Invoices Balanced Successfully']);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    }
    public function fetchNumberSeries(Request $request){
        try{
            $store = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->first();
            $sales = WaPosCashSales::where('branch_id', $request->branch_id)
                // ->whereDate('created_at', $request->day)
                ;
            $completeSales = $sales->where('status', 'Completed')->count();
            $archivedSales = $sales->whereNot('status', 'Completed')->count();
            $salesSummary =  (object)[
                'completeSales' => $completeSales,
                'archivedSales' => $archivedSales,
            ];

            return $this->jsonify(['salesSummary' => $salesSummary]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    } 
    public function closeDay(Request $request){
        try{
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                //check all parameters before continuing
                if($eodRoutineDetails->returns_passed == 0){
                    return $this->jsonify(['message' => 'Returns Verification Pending!'], 500);
                }
                if($eodRoutineDetails->splits_passed == 0){
                    return $this->jsonify(['message' => 'Splits Verification Pending!'], 500);
                }
                if($eodRoutineDetails->binless_items_passed == 0){
                    return $this->jsonify(['message' => 'Bin Items Verification Pending!'], 500);
                }
                if($eodRoutineDetails->unbalanced_transactions_passed == 0){
                    return $this->jsonify(['message' => 'Stocks Vs Sales Verification Pending!'], 500);
                }
                if($eodRoutineDetails->pos_cash_at_hand_passed == 0){
                    return $this->jsonify(['message' => 'Cash At Hand Verification Pending!'], 500);
                }
                $eodRoutineDetails->lock_users = false;
                $eodRoutineDetails->closed_at = Carbon::now()->toDateTimeString();
                $eodRoutineDetails->closed_by = Auth::user()->id;
                $eodRoutineDetails->status = 'Closed';
                $eodRoutineDetails->save();
                session()->flash('success', 'Day closed successfully!');

                return $this->jsonify(['success'=>'Day Closed Successsfully',
            'redirectUrl' => route('eod-routine.index', ['message' => 'Day closed successfully!'])]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }

    }
    public function fetchCashAtHand(Request $request)
    {
        try {
            $date = $request->day;
            $fromDate = Carbon::parse($request->day)->startOfDay();
            $endDate = Carbon::parse($request->day)->endOfDay();

            $sales = DB::table('wa_pos_cash_sales_items as items')
                ->select(
                    DB::raw("('$date') as date"),
                    // DB::raw("(sum(items.total)) as cs"),
                    DB::raw("(sum(items.selling_price * items.qty)) as cs"),
                    DB::raw("(sum(items.discount_amount)) as disc"),

                    DB::raw("(select coalesce(sum(selling_price * r.return_quantity), 0) from wa_pos_cash_sales_items_return as r
                        join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                        where r.accepted = 1 and (r.accepted_at between '$fromDate' and '$endDate')) as returns"),

                    //expenses
                    DB::raw("(SELECT SUM(pos_cash_payments.amount)
                        FROM pos_cash_payments
                        WHERE pos_cash_payments.branch_id = $request->branch_id 
                        AND pos_cash_payments.status = 'Disbursed'
                        AND (pos_cash_payments.disbursed_at between '$fromDate' and '$endDate')
                    ) as expenses"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 13) as eazzy"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 10) as eb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 12) as vooma"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and csp.payment_method_id = 9) as kcb_main"),

                    DB::raw("(select sum(amount) from wa_pos_cash_sales_payments as csp
                        join wa_pos_cash_sales as sales on csp.wa_pos_cash_sales_id = sales.id 
                        where (sales.created_at between '$fromDate' and '$endDate')
                        and sales.branch_id = $request->branch_id 
                        and sales.status = 'Completed'
                        and (csp.payment_method_id = 3 or csp.payment_method_id = 15)) as mpesa"),

                    DB::raw("(select sum(banked_amount) from cash_drop_transactions 
                        where (created_at between '$fromDate' and '$endDate')) as cdm"),

                    DB::raw("(select coalesce(sum(amount), 0) from wa_pos_cash_sales_payments csp 
                        join payment_methods p on csp.payment_method_id = p.id and p.is_cash != 1 
                        where (csp.created_at between '$fromDate' and '$endDate') and csp.verified = true) as verified"),

                    DB::raw("(select coalesce(sum(bd.amount), 0) from banked_drop_transactions bd 
                            join cash_drop_transactions cd on bd.cash_drop_transaction_id = cd.id and (cd.created_at between '$fromDate' and '$endDate')
                            join users on cd.cashier_id = users.id and users.restaurant_id = $request->branch_id 
                            where bd.created_at > '$fromDate' and bd.manually_allocated = true) as allocated_cdms"),

                    DB::raw("(select coalesce(sum(cb.banked_amount), 0) from chief_cashier_declarations cb 
                        where (cb.created_at between '$fromDate' and '$endDate') and branch_id = $request->branch_id) as allocated_cb")
                )
                ->join('wa_pos_cash_sales as sales', 'items.wa_pos_cash_sales_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$fromDate, $endDate])
                ->where('sales.branch_id', $request->branch_id)
                ->where('sales.status', 'Completed')
                ->first();

            $sales->sales = $sales->cs - $sales->disc;
            $sales->net_sales = $sales->sales - $sales->returns - $sales->expenses;
            $sales->total_bankings = $sales->eazzy + $sales->eb_main + $sales->vooma + $sales->kcb_main + $sales->mpesa + $sales->cdm;

            $sales->verified = $sales->verified + $sales->cdm;
            // $sales->sales_variance = $sales->net_sales - $sales->verified;
            $sales->sales_variance =  $sales->net_sales - $sales->total_bankings;
            $sales->balance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            // $todaysBalance = $sales->sales_variance - $sales->allocated_cdms - $sales->allocated_cb;
            $todaysBalance = $sales->net_sales - $sales->total_bankings;

            $sales->raw_rcts = $sales->total_bankings;
            $sales->raw_verified = $sales->verified;
            $sales->raw_balance = $sales->balance;


            $sales->sales = manageAmountFormat($sales->sales);
            $sales->returns = manageAmountFormat($sales->returns);
            $sales->expenses = manageAmountFormat($sales->expenses);
            $sales->net_sales = manageAmountFormat($sales->net_sales);
            $sales->eazzy = manageAmountFormat($sales->eazzy);
            $sales->eb_main = manageAmountFormat($sales->eb_main);
            $sales->vooma = manageAmountFormat($sales->vooma);
            $sales->kcb_main = manageAmountFormat($sales->kcb_main);
            $sales->mpesa = manageAmountFormat($sales->mpesa);
            $sales->cdm = manageAmountFormat($sales->cdm);
            $sales->total_bankings = manageAmountFormat($sales->total_bankings);
            $sales->verified = manageAmountFormat($sales->verified);
            $sales->sales_variance = manageAmountFormat($sales->sales_variance);
            $sales->allocated_cdms = manageAmountFormat($sales->allocated_cdms);
            $sales->allocated_cb = manageAmountFormat($sales->allocated_cb);
            $sales->balance = manageAmountFormat($sales->balance);

            return $this->jsonify(['posCashBanking' => $sales, 'todaysBalance' => $todaysBalance]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function verifyCashAtHand(Request $request){
        try{
            if(isset($request->entered_amount) && (double)$request->entered_amount == $request->system_amount){
                DB::beginTransaction();
                $bankingRecord = BankingApproval::whereDate('payment_date', $request->day)->where('branch_id', $request->branch_id)->where('sales_type', 1)->first();
                if (!$bankingRecord) {
                    $bankingRecord = BankingApproval::create([
                        'sales_date' => $request->day,
                        'branch_id' => $request->branch_id,
                        'payment_date' => $request->day,
                        'sales_type' => 1
                    ]);
                }    

                $cashAtHandPassed = true;
                $eodRoutineDetails = EndOfDayRoutine::where('day', $request->day)->where('branch_id', $request->branch_id)->first();
                $eodRoutineDetails->pos_cash_at_hand_passed = true;
                $eodRoutineDetails->system_cah = $request->system_amount;
                $eodRoutineDetails->cashier_cah = $request->entered_amount;
                $eodRoutineDetails->save();
                DB::commit();
            }else{
                $cashAtHandPassed = false;
            }
            return $this->jsonify(['cashAtHandPassed' => $cashAtHandPassed]);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
    
}
