<?php

namespace App\Services\Inventory;

use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryItem;
use App\Model\WaInventoryItemSupplier;
use App\Model\WaStockMove;
use App\Model\WaUserSupplier;
use Illuminate\Support\Facades\DB;

class StockStats
{
    public function __construct(
        protected $store
    ) {}

    protected function buildQuery()
    {
        $itemIds = [];

        if (!auth()->user()->isAdministrator()) {
            $supplierIds = WaUserSupplier::where('user_id', auth()->user()->id)->get()
                ->pluck('wa_supplier_id')->toArray();
            $itemIds = WaInventoryItemSupplier::whereIn('wa_supplier_id', $supplierIds)->get()
                ->pluck('wa_inventory_item_id')->toArray();
        }

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('IFNULL(SUM(qauntity),0) AS qty_on_hand'),
            ])
            ->where('wa_location_and_store_id', $this->store)
            ->groupBy('wa_inventory_item_id');

        return WaInventoryItem::query()
            ->select([
                DB::raw("IFNULL(stock_status.max_stock, 0) as max_stock"),
                DB::raw("IFNULL(stock_status.re_order_level, 0) as re_order_level"),
                DB::raw('IFNULL(moves.qty_on_hand,0) as qty_on_hand')
            ])
            ->leftJoinSub($qohSub, 'moves', 'moves.wa_inventory_item_id', 'wa_inventory_items.id')
            ->join('wa_inventory_location_stock_status as stock_status', function ($query) {
                $query->on('stock_status.wa_inventory_item_id', 'wa_inventory_items.id')
                    ->where('stock_status.wa_location_and_stores_id', $this->store);
            })
            ->join('pack_sizes AS sizes', 'sizes.id', 'wa_inventory_items.pack_size_id')
            ->where('sizes.can_order', 1)
            ->where('wa_inventory_items.status', 1)
            ->when(count($itemIds) > 0, function ($query) use ($itemIds) {
                $query->whereIn('wa_inventory_items.id', $itemIds);
            });
    }

    public function getOverStockedItemCount()
    {
        return $this->buildQuery()
            ->whereRaw('qty_on_hand > stock_status.max_stock')
            ->get()
            ->count();
    }

    public function getReorderItemsCount()
    {
        return $this->buildQuery()
            ->selectRaw('IFNULL(sales.total_sales,0)')
            ->leftJoinSub($this->salesSub(), 'sales', 'sales.wa_inventory_item_id', 'wa_inventory_items.id')
            ->whereRaw('IFNULL(qty_on_hand, 0) <= re_order_level')
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('total_sales > 0')
            ->get()
            ->count();
    }

    public function getMissingItemsCount()
    {
        return $this->buildQuery()
            ->selectRaw('IFNULL(sales.total_sales,0)')
            ->leftJoinSub($this->salesSub(), 'sales', 'sales.wa_inventory_item_id', 'wa_inventory_items.id')
            ->whereRaw('IFNULL(qty_on_hand,0) = 0')
            ->whereRaw('total_sales > 0')
            ->get()
            ->count();
    }

    public function getSlowMovingStockCount()
    {
        return $this->buildQuery()
            ->selectRaw('IFNULL(sales.total_sales,0)')
            ->leftJoinSub($this->salesSub(), 'sales', 'sales.wa_inventory_item_id', 'wa_inventory_items.id')
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('IFNULL(total_sales, 0) > 0')
            ->whereRaw('IFNULL(total_sales, 0) <= 5')
            ->get()
            ->count();
    }

    public function getDeadStockCount()
    {
        return $this->buildQuery()
            ->selectRaw('IFNULL(sales.total_sales,0)')
            ->leftJoinSub($this->salesSub(), 'sales', 'sales.wa_inventory_item_id', 'wa_inventory_items.id')
            ->whereRaw('qty_on_hand > 0')
            ->whereRaw('IFNULL(total_sales,0) = 0')
            ->get()
            ->count();
    }

    public function salesSub()
    {
        $from = now()->subDays(30)->startOfDay()->toDateString();
        $to = now()->endOfDay()->toDateString();

        return WaInternalRequisitionItem::join('wa_internal_requisitions as wir', 'wir.id', 'wa_internal_requisition_items.wa_internal_requisition_id')
            ->leftJoin('wa_location_and_stores as wls', 'wls.wa_branch_id', 'wir.restaurant_id')
            ->select(
                'wa_internal_requisition_items.wa_inventory_item_id',
                DB::raw('IFNULL(SUM(quantity),0) AS total_sales')

            )
            ->where('wls.id', $this->store)
            ->whereBetween('wir.created_at', [$from, $to])
            ->groupBy('wa_internal_requisition_items.wa_inventory_item_id');
    }
}
