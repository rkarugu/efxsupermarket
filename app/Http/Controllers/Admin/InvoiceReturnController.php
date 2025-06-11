<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\WaInventoryLocationTransferItemReturn;

class InvoiceReturnController extends Controller
{
    public function showProcessedReturnsPage( Request $request)
    {
        if (!can('view', 'processed-returns')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Processed Returns';
        $breadcum = ['Transfers' => route('transfers.index'), 'Processed Returns' => ''];
        $model = 'processed-returns';

        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        $user = getLoggeduserProfile();
        // $routes = DB::table('routes')->select('id', 'route_name')->get();   
        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $routes = DB::table('routes')->select('id', 'route_name')->get();
        } else {
            $routes = DB::table('routes')->where('restaurant_id', $authuser->userRestaurent->id)->select('id', 'route_name')->get();
        }    

        return view('admin.invoice_returns.processed', compact('title', 'model', 'breadcum','routes'));
    }

    public function showProcessedReturnsPageDatatable(Request $request)
    {
        $user = getLoggeduserProfile();
        $returnItems = $returnItems = DB::table('wa_inventory_location_transfer_item_returns')
            ->whereIn('wa_inventory_location_transfer_item_returns.status', ['received', 'partially_received'])
            ->where('wa_inventory_location_transfer_item_returns.received_quantity', '>', 0)
            ->select(
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.description',
                'wa_unit_of_measures.title as bin',
                'wa_inventory_location_transfer_item_returns.id',
                'wa_inventory_location_transfer_item_returns.return_quantity',
                'wa_inventory_location_transfer_item_returns.received_quantity',
                'wa_inventory_location_transfer_item_returns.return_number',
                'wa_inventory_location_transfer_item_returns.created_at as return_date',
                'wa_inventory_location_transfers.transfer_no as invoice_number',
                'wa_inventory_location_transfers.created_at as invoice_date',
                'wa_inventory_location_transfer_item_returns.updated_at as approved_date',
                'wa_inventory_location_transfers.name as customer',
                'wa_inventory_location_transfers.route as route',
                'initiators.name as initiator',
                'receivers.name as receiver',
                'wa_inventory_location_transfer_item_returns.confirmed_by as confirmer',
                'wa_inventory_location_transfer_item_returns.comment as confirmed_by_note',
                DB::raw('wa_inventory_location_transfer_item_returns.received_quantity * wa_inventory_location_transfer_items.selling_price as total_value'),
            )
            ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_location_transfer_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('users as initiators', 'wa_inventory_location_transfer_item_returns.return_by', '=', 'initiators.id')
            ->leftJoin('users as receivers', 'wa_inventory_location_transfer_item_returns.received_by', '=', 'receivers.id')
            ->leftJoin('wa_inventory_location_transfers', function ($join) use ($request) {

                $query = $join->on('wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id');
                if ($request->route_id) {
                    $query = $query->where('wa_inventory_location_transfers.route', $request->route_id);
                }
                if ($request->start_date) {
                    $query = $query->whereDate('wa_inventory_location_transfer_item_returns.updated_at', '>=', $request->input('start_date'));
                }
                if ($request->end_date) {
                    $query = $query->whereDate('wa_inventory_location_transfer_item_returns.updated_at', '<=', $request->input('end_date'));
                }

            })
            ->join('wa_inventory_location_uom', function ($join) use ($user) {
                $query = $join->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
                    ->whereColumn('wa_inventory_location_uom.location_id', '=', 'wa_inventory_location_transfers.to_store_location_id');
                if ($user->role_id != 1) {
                    $query = $query->where('wa_inventory_location_uom.uom_id', $user->wa_unit_of_measures_id);
                }
            })
            ->leftJoin('wa_unit_of_measures', 'wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id');
            return DataTables::of($returnItems)
                    ->addIndexColumn()
                    ->addColumn('item_name', function($item) {
                        return $item->stock_id_code .' - '. $item->description ;
                    })
                    ->editColumn('return_quantity', function($item) {
                        return number_format($item->return_quantity, 0);
                    })
                    ->editColumn('total_value', function($item) {
                        return number_format($item->total_value);
                    })
                ->toJson();
    }

    public function showRejectedReturnsPage(Request $request): View|RedirectResponse
    {
        if (!can('view', 'rejected-returns')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Rejected Returns';
        $breadcum = ['Transfers' => route('transfers.index'), 'Rejected Returns' => ''];
        $model = 'rejected-returns';

        $user = getLoggeduserProfile();

        $routes = DB::table('routes')->select('id', 'route_name')->get();
        // $returnItems = DB::table('wa_inventory_location_transfer_item_returns')
        //     ->where('wa_inventory_location_transfer_item_returns.rejected_quantity', '>', 0)
        //     ->select(
        //         'wa_inventory_items.stock_id_code',
        //         'wa_inventory_items.description',
        //         'wa_unit_of_measures.title as bin',
        //         'wa_inventory_location_transfer_item_returns.id',
        //         'wa_inventory_location_transfer_item_returns.return_quantity',
        //         'wa_inventory_location_transfer_item_returns.received_quantity',
        //         'wa_inventory_location_transfer_item_returns.rejected_quantity',
        //         'wa_inventory_location_transfer_item_returns.return_number',
        //         'wa_inventory_location_transfer_item_returns.created_at as return_date',
        //         'wa_inventory_location_transfers.transfer_no as invoice_number',
        //         'wa_inventory_location_transfers.created_at as invoice_date',
        //         'wa_inventory_location_transfer_item_returns.updated_at as approved_date',
        //         'wa_inventory_location_transfers.name as customer',
        //         'wa_inventory_location_transfers.route as route',
        //         'initiators.name as initiator',
        //         'receivers.name as receiver',
        //         DB::raw('wa_inventory_location_transfer_item_returns.rejected_quantity * wa_inventory_location_transfer_items.selling_price as total_value'),

        //     )
        //     ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
        //     // ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
        //      ->leftJoin('wa_inventory_location_transfers', function ($join) use ($request) {
        //         $query = $join->on('wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id');
                
        //         //ROUTE  & date filters
        //         if ($request->route_id) {
        //             $query = $query->where('wa_inventory_location_transfers.route', $request->route_id);
        //         }
        //         if ($request->start_date) {
        //             $query = $query->whereDate('wa_inventory_location_transfers.updated_at', '>=', $request->input('start_date'));
        //         }
        //         if ($request->end_date) {
        //             $query = $query->whereDate('wa_inventory_location_transfers.updated_at', '<=', $request->input('end_date'));
        //         }
        //     })
        //     ->leftJoin('wa_inventory_items', 'wa_inventory_location_transfer_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
        //     ->leftJoin('users as initiators', 'wa_inventory_location_transfer_item_returns.return_by', '=', 'initiators.id')
        //     ->leftJoin('users as receivers', 'wa_inventory_location_transfer_item_returns.received_by', '=', 'receivers.id')
        //     ->join('wa_inventory_location_uom', function ($join) use ($user) {
        //         $query = $join->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
        //             ->whereColumn('wa_inventory_location_uom.location_id', '=', 'wa_inventory_location_transfers.to_store_location_id');
        //         if ($user->role_id != 1) {
        //             $query = $query->where('wa_inventory_location_uom.uom_id', $user->wa_unit_of_measures_id);
        //         }
        //     })
        //     ->leftJoin('wa_unit_of_measures', 'wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id')
        //     ->get();

        return view('admin.invoice_returns.rejected', compact('title', 'model', 'breadcum','routes'));
    }

    public function showRejectedReturnsPageDatatable(Request $request)
    {
        $user = getLoggeduserProfile();
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $returnItems = DB::table('wa_inventory_location_transfer_item_returns')
            ->whereDate('wa_inventory_location_transfer_item_returns.created_at', '>=',$start)
            ->whereDate('wa_inventory_location_transfer_item_returns.created_at', '<=',$end)
            ->where('wa_inventory_location_transfer_item_returns.rejected_quantity', '>', 0)
            ->select(
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.description',
                'wa_unit_of_measures.title as bin',
                'wa_inventory_location_transfer_item_returns.id',
                'wa_inventory_location_transfer_item_returns.return_quantity',
                'wa_inventory_location_transfer_item_returns.received_quantity',
                'wa_inventory_location_transfer_item_returns.rejected_quantity',
                'wa_inventory_location_transfer_item_returns.return_number',
                'wa_inventory_location_transfer_item_returns.created_at as return_date',
                'wa_inventory_location_transfers.transfer_no as invoice_number',
                'wa_inventory_location_transfers.created_at as invoice_date',
                'wa_inventory_location_transfer_item_returns.updated_at as approved_date',
                'wa_inventory_location_transfers.name as customer',
                'wa_inventory_location_transfers.route as route',
                'initiators.name as initiator',
                'receivers.name as receiver',
                DB::raw('wa_inventory_location_transfer_item_returns.rejected_quantity * wa_inventory_location_transfer_items.selling_price as total_value'),

            )
            ->leftJoin('wa_inventory_location_transfer_items', 'wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id', '=', 'wa_inventory_location_transfer_items.id')
            // ->leftJoin('wa_inventory_location_transfers', 'wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id')
             ->leftJoin('wa_inventory_location_transfers', function ($join) use ($request) {
                $query = $join->on('wa_inventory_location_transfer_items.wa_inventory_location_transfer_id', '=', 'wa_inventory_location_transfers.id');
                
                //ROUTE  & date filters
                if ($request->route_id) {
                    $query = $query->where('wa_inventory_location_transfers.route', $request->route_id);
                }
                // if ($request->start_date) {
                //     $query = $query->whereDate('wa_inventory_location_transfers.updated_at', '>=', $request->input('start_date'));
                // }
                // if ($request->end_date) {
                //     $query = $query->whereDate('wa_inventory_location_transfers.updated_at', '<=', $request->input('end_date'));
                // }
            })
            ->leftJoin('wa_inventory_items', 'wa_inventory_location_transfer_items.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoin('users as initiators', 'wa_inventory_location_transfer_item_returns.return_by', '=', 'initiators.id')
            ->leftJoin('users as receivers', 'wa_inventory_location_transfer_item_returns.received_by', '=', 'receivers.id')
            ->join('wa_inventory_location_uom', function ($join) use ($user) {
                $query = $join->on('wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
                    ->whereColumn('wa_inventory_location_uom.location_id', '=', 'wa_inventory_location_transfers.to_store_location_id');
                if ($user->role_id != 1) {
                    $query = $query->where('wa_inventory_location_uom.uom_id', $user->wa_unit_of_measures_id);
                }
            })
            ->leftJoin('wa_unit_of_measures', 'wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id');
            return DataTables::of($returnItems)
                    ->addIndexColumn()
                    ->addColumn('item_name', function($item) {
                        return $item->stock_id_code .' - '. $item->description ;
                    })
                    ->editColumn('return_quantity', function($item) {
                        return number_format($item->return_quantity, 0);
                    })
                    ->editColumn('total_value', function($item) {
                        return number_format($item->total_value);
                    })
                ->toJson();
    }
}
