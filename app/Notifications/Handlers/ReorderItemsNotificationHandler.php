<?php

namespace App\Notifications\Handlers;

use App\Model\WaInventoryItem;
use App\Model\WaStockMove;
use Illuminate\Support\Facades\DB;

class ReorderItemsNotificationHandler
{
    public static function getContext()
    {
        $location = 46;

        $qohQuery = "SELECT SUM(qauntity) FROM 
                        `wa_stock_moves`  
                    WHERE `wa_inventory_item_id` = `wa_inventory_items`.`id` 
                        AND wa_location_and_store_id = " . $location;

        // 7 Days         
        $start_7 = now()->subDays(7)->format('Y-m-d 00:00:00');
        $end_7 = now()->format('Y-m-d 23:59:59');
        $sales_7 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_7' AND '$end_7'
                AND wa_location_and_store_id = " . $location;

        // 30 Days         
        $start_30 = now()->subDays(30)->format('Y-m-d 00:00:00');
        $end_30 = now()->format('Y-m-d 23:59:59');

        $sales_30 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_30' AND '$end_30'
                AND wa_location_and_store_id = " . $location;

        // 180 Days         
        $start_150 = now()->subDays(180)->format('Y-m-d 00:00:00');
        $end_150 = now()->subDays(30)->format('Y-m-d 23:59:59');

        $sales_180 = "SELECT
                SUM(qauntity)
            FROM
                wa_stock_moves
            WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`
                AND (document_no LIKE 'INV-%' OR document_no LIKE 'RTN-%')
                AND created_at BETWEEN '$start_150' AND '$end_150'
                AND wa_location_and_store_id = " . $location;

        $stocks = WaInventoryItem::query()
            ->select([
                'wa_inventory_items.id',
                'max_stocks.re_order_level'
            ])
            ->with('suppliers.users')
            ->join('wa_inventory_location_stock_status as max_stocks', function ($join) use ($location) {
                $join->on('max_stocks.wa_inventory_item_id', '=', 'wa_inventory_items.id')
                    ->where('max_stocks.wa_location_and_stores_id', $location);
            })
            ->whereIn('pack_size_id', [8, 7, 3, 5, 2, 4, 13])
            ->whereRaw("($qohQuery) <= 1.1 * re_order_level")
            ->whereRaw("(ABS(($sales_7)) > 0 OR ABS(($sales_30)) > 0 OR ABS(($sales_180)) > 0)")
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
