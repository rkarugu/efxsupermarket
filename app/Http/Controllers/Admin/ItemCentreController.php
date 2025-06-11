<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaCategory;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaInventoryPriceHistory;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ItemCentreController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'maintain-items';
        $this->title = 'Item Centre';
    }

    public function show(WaInventoryItem $item)
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $authuser = Auth::user();
        $authuserLocation = Auth::user()->wa_location_and_store_id;
        $permission = $this->mypermissionsforAModule();

        $this->title .= '-' . $item->stock_id_code;

        $breadcum = [
            'Maintain Items' => route('maintain-items.index'),
            $item->stock_id_code => route('item-centre.show', $item)
        ];

        $maxStockSub = WaInventoryLocationStockStatus::query()
            ->select([
                'wa_location_and_stores_id',
                're_order_level',
                'max_stock'
            ])->where('wa_inventory_item_id', $item->id);

        $qohSub = WaStockMove::query()
            ->select([
                'wa_location_and_store_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->where('wa_inventory_item_id', $item->id)
            ->groupBy('wa_location_and_store_id');


        $locations = WaLocationAndStore::query()
            ->select([
                'wa_location_and_stores.id',
                'wa_location_and_stores.location_name',
                DB::raw('IFNULL(quantities.quantity, 0) AS qoh'),
                DB::raw('IFNULL(levels.max_stock, 0) AS max_stock'),
                DB::raw('IFNULL(levels.re_order_level, 0) AS re_order_level'),
            ])
            ->leftJoinSub($qohSub, 'quantities', 'quantities.wa_location_and_store_id', 'wa_location_and_stores.id')
            ->leftJoinSub($maxStockSub, 'levels', 'levels.wa_location_and_stores_id', 'wa_location_and_stores.id')
            ->get();

        // $locationsQuery = WaLocationAndStore::query()
        //     ->select([
        //         'wa_location_and_stores.id',
        //         'wa_location_and_stores.location_name',
        //         DB::raw('IFNULL(quantities.quantity, 0) AS qoh'),
        //         DB::raw('IFNULL(levels.max_stock, 0) AS max_stock'),
        //         DB::raw('IFNULL(levels.re_order_level, 0) AS re_order_level'),
        //     ])
        //     ->leftJoinSub($qohSub, 'quantities', 'quantities.wa_location_and_store_id', 'wa_location_and_stores.id')
        //     ->leftJoinSub($maxStockSub, 'levels', 'levels.wa_location_and_stores_id', 'wa_location_and_stores.id');

        // if ($authuser->role_id != 1 && !isset($permission['maintain-items___view-all-stocks'])) {
        //     $locationsQuery->where('wa_location_and_stores.id', $authuserLocation);
        // }

        // $locations = $locationsQuery->get();

        $getLocations = WaLocationAndStore::pluck('location_name', 'id')->toArray();

        return view('admin.item_centre.show', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => $breadcum,
            'item' => $item,
            'locations' => $locations,
            'getLocations' => $getLocations,
            'permission' => $permission,
            'authuser' => $authuser,
            'categories' => WaCategory::get()
        ]);
    }

    public function stockMovements($stockId)
    {
        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');

        $query = WaStockMove::query()
            ->select([
                'wa_stock_moves.*',
                'users.name as user_name',
                DB::raw('(CASE WHEN qauntity > 0 THEN qauntity ELSE 0 END) as qty_in'),
                DB::raw('(CASE WHEN qauntity < 0 THEN ABS(qauntity) ELSE 0 END) as qty_out'),
            ])
            ->with([
                'location'
            ])
            ->join('users', 'users.id', '=', 'wa_stock_moves.user_id')
            ->where('wa_stock_moves.wa_inventory_item_id', $stockId)
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->when(request()->location, function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->location);
            });

        if (request()->move_type) {
            if (request()->move_type == 'adjustment') {
                $query->whereNot('stock_adjustment_id', null);
            } else if (request()->move_type == 'cash-sales') {
                $query->where('document_no', 'like', '%CIV%');
            } else if (request()->move_type == 'delivery-note') {
                $query->whereNot('wa_inventory_location_transfer_id', null);
            } else if (request()->move_type == 'ingredients-booking') {
                $query->whereNot('ordered_item_id', null);
            } else if (request()->move_type == 'internal-requisition-store-c') {
                $query->where('document_no', 'like', '%IRSC%');
            } else if (request()->move_type == 'purchase') {
                $query->whereNot('wa_purchase_order_id', null);
            } else if (request()->move_type == 'recieve-stock-store-c') {
                $query->where('document_no', 'like', '%RSSC%');
            } else if (request()->move_type == 'return-from-store') {
                $query->where('document_no', 'like', '%RFS%');
            } else if (request()->move_type == 'return') {
                $query->where('document_no', 'like', '%RTN%');
            } else if (request()->move_type == 'sales-invoice') {
                $query->where(function ($query) {
                    $query->whereNot('wa_internal_requisition_id', null)
                        ->orWhere('document_no', 'like', '%INV%')
                        ->orWhere('document_no', 'like', '%CIV%');
                });
            } else if (request()->move_type == 'stock-break') {
                $query->where('document_no', 'like', '%STB%');
            } else if (request()->move_type == 'transfer') {
                $query->where('document_no', 'like', '%TRANS%');
            } else if (request()->move_type == 'transfer') {
                $query->where('document_no', 'like', '%MARCH24%');
            }else if (request()->move_type == 'stock-charge') {
                $query->where('document_no', 'like', 'SA%');
            }
        }

        return DataTables::eloquent($query)
            ->editColumn('created_at', function ($move) {
                return $move->created_at->format('d-m-Y H:i:s');
            })
            ->addColumn('type', function ($move) {
                return getStockMoveType($move);
            })
            ->toJson();
    }

    public function priceChangeHistory($stockId)
    {
        $query = WaInventoryPriceHistory::query()
            ->with([
                'creator',
                'approver'
            ])
            ->when(request()->filled('supplier'), function ($query) {
                $query->whereHas('item', function ($query) {
                    $query->whereHas('suppliers', function ($query) {
                        $query->where('wa_suppliers.id', request()->supplier);
                    });
                });
            })
            ->where('wa_inventory_item_id', $stockId)
            ->where('status', '<>', 'Pending');

        return DataTables::eloquent($query)
            ->editColumn('updated_at', function ($move) {
                return $move->updated_at->format('d-m-Y H:i:s');
            })->editColumn('approver.name', function($move){
                return $move->approver? $move->approver?->name : '';
            })
            ->toJson();
    }
}
