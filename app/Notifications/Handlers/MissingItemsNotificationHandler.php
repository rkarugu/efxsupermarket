<?php

namespace App\Notifications\Handlers;

use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\DB;

class MissingItemsNotificationHandler
{
    public static function getContext()
    {
        // Define the time period and location
        $from = now()->subDays(30)->format('Y-m-d 00:00:00');
        $to = now()->format('Y-m-d 23:59:59');
        $location = 46;

        // Sales data subquery
        $salesSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity)) as total_sales')
            ])
            ->where('wa_location_and_store_id', $location)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('stock_id_code');

        // Small packs data subquery
        $smallPacksSub = WaStockMove::query()
            ->select([
                'items.wa_inventory_item_id',
                DB::raw('ABS(SUM(qauntity) / conversion_factor) as pack_sales')
            ])
            ->leftJoin('wa_inventory_assigned_items as items', 'items.destination_item_id', '=', 'wa_stock_moves.wa_inventory_item_id')
            ->where('wa_location_and_store_id', $location)
            ->where(function ($query) {
                $query->where('document_no', 'like', 'INV-%')
                    ->orWhere('document_no', 'like', 'RTN-%');
            })
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->groupBy('stock_id_code');

        // Quantity on hand data subquery
        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->where('wa_location_and_store_id', $location)
            ->groupBy('stock_id_code');

        // Main query to get inventory items
        $stocks = WaInventoryItem::query()
            ->select('wa_inventory_items.id')
            ->with(['suppliers.users'])
            ->leftJoinSub($salesSub, 'sales', 'sales.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($smallPacksSub, 'packs', 'packs.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->whereIn('wa_inventory_items.pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->where(function ($query) {
                $query->where('qoh.quantity', 0)
                    ->orWhereNull('qoh.quantity');
            })
            ->where(DB::raw('IFNULL(total_sales,0) + IFNULL(pack_sales,0)'), '>', 0)
            ->where('wa_inventory_items.status', 1)
            ->get();

        $suppliers = [];
        $users = [];
        $user_unique_stocks = [];
        $user_unique_suppliers = [];

        // Iterate over each stock
        foreach ($stocks as $stock) {
            foreach ($stock->suppliers as $supplier) {
                // Get unique suppliers
                $suppliers[$supplier->id] = $supplier->name;

                // Get unique users for the notification and their stock and supplier count
                foreach ($supplier->users as $user) {
                    // Initialize user's entry if not already present
                    if (!isset($users[$user->id])) {
                        $users[$user->id] = $user;
                        $users[$user->id]->stocks_count = 0;
                        $users[$user->id]->suppliers_count = 0;
                        $user_unique_stocks[$user->id]['stocks'] = [];
                        $user_unique_suppliers[$user->id]['suppliers'] = [];
                    }

                    // Count unique stocks
                    if (!in_array($stock->id, $user_unique_stocks[$user->id]['stocks'])) {
                        $user_unique_stocks[$user->id]['stocks'][] = $stock->id;
                        $users[$user->id]->stocks_count++;
                    }

                    // Count unique suppliers
                    if (!in_array($supplier->id, $user_unique_suppliers[$user->id]['suppliers'])) {
                        $user_unique_suppliers[$user->id]['suppliers'][] = $supplier->id;
                        $users[$user->id]->suppliers_count++;
                    }
                }
            }
        }

        return [
            'stocks_count' => $stocks->count(),
            'suppliers_count' => count($suppliers),
            'users' => $users
        ];
    }
}
