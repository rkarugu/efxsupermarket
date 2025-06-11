<?php

namespace App\Notifications\Handlers;

use App\Model\WaInventoryItem;
use App\Model\WaInventoryLocationStockStatus;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\DB;

class OverStockedItemsNotificationHandler
{
    public static function getContext()
    {
        $maxStockSub = WaInventoryLocationStockStatus::query()
            ->select([
                'wa_inventory_item_id',
                're_order_level',
                'max_stock'
            ])
            ->where('wa_location_and_stores_id', 46);

        $qohSub = WaStockMove::query()
            ->select([
                'wa_inventory_item_id',
                DB::raw('SUM(qauntity) as quantity')
            ])
            ->where('wa_location_and_store_id', 46)
            ->groupBy('wa_inventory_item_id');

        $stocks = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'max_stocks.max_stock',
                'qoh.quantity'
            ])
            ->with('suppliers.users')
            ->joinSub($maxStockSub, 'max_stocks', 'max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->joinSub($qohSub, 'qoh', 'qoh.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->whereIn('pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->whereColumn('qoh.quantity', '>', 'max_stocks.max_stock')
            ->where('wa_inventory_items.status', 1)
            ->whereHas('getstockmoves')
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
