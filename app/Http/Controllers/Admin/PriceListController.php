<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class PriceListController extends Controller
{
    public function getItemPriceList(Request $request): JsonResponse
    {
        $user = JWTAuth::toUser($request->token);
        try {
            $appUrl = env('APP_URL');
            $items = DB::table('wa_inventory_items')
                ->select(
                    'title', 
                    'selling_price',
                     'image',
                     DB::raw("(SELECT SUM(wa_stock_moves.qauntity) 
                     FROM wa_stock_moves WHERE wa_stock_moves.stock_id_code =  wa_inventory_items.stock_id_code
                     AND wa_stock_moves.wa_location_and_store_id  = '$user->wa_location_and_store_id'
                     ) AS qoh")
                    );

            if ($request->search_query) {
                $items = $items->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', "%$request->search_query%")
                        ->orWhere('description', 'LIKE', "%$request->search_query%")
                        ->orWhere('stock_id_code', 'LIKE', "%$request->search_query%");
                });
            }

            $items = $items->orderBy('title')
                ->cursorPaginate(20)
                ->through(function ( $item) use ($appUrl) {
                    $item->image = "$appUrl/uploads/inventory_items/" . $item->image;
                    $item->selling_price = format_amount_with_currency($item->selling_price);
                    
                    return $item;
                });

            return $this->jsonify($items, 200);
        } catch(Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
