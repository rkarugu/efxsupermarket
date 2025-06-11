<?php

namespace App\Console\Commands;

use App\Model\User;
use App\Model\WaLocationAndStore;
use App\Models\BlockUsersExemptionSchedule;
use App\Models\BlockUsersExemptionScheduleUser;
use App\Models\StockTakeUserAssignment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockStockTakeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:block-stock-take-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();
        // $dailyStockTakeAssignment = StockTakeUserAssignment::whereDate('stock_take_date', $today)->get();
        // $dailyStockTakeCategoriesArray = [];
        // foreach($dailyStockTakeAssignment as $stockTakeAssignment){
        //     $categories = explode(',', $stockTakeAssignment->category_ids);
        //     $dailyStockTakeCategoriesArray = array_merge($dailyStockTakeCategoriesArray, $categories);
        // }
        // $totalCategoriesSubquery = DB::table('wa_inventory_items as wii')
        //         ->select(
        //             'wilu.uom_id',
        //             DB::raw('COUNT(DISTINCT wii.wa_inventory_category_id) as total_categories')
        //         )
        //         ->join('wa_inventory_location_uom as wilu', 'wilu.inventory_id', '=', 'wii.id')
        //         ->leftJoin('wa_inventory_categories', 'wa_inventory_categories.id', 'wii.wa_inventory_category_id')
        //         ->whereIn('wa_inventory_categories.id', $dailyStockTakeCategoriesArray)
        //         ->where('wii.status', 1)
        //         ->groupBy('wilu.uom_id');
        // $dailyStockTakeCategoriesString = implode(',', $dailyStockTakeCategoriesArray);

        // $results = DB::table('wa_unit_of_measures as bin')
        //     ->select(
        //         'bin.id as id',
        //         'bin.title as bin',
        //         DB::raw('COALESCE(total_cats.total_categories, 0) as total_categories'),
        //         DB::raw('COALESCE(COUNT(DISTINCT wscv.category_id), 0) as counted_categories'),
        //         DB::raw('COALESCE(COUNT(DISTINCT wscv.wa_inventory_item_id), 0) as counted_items'),
        //         DB::raw("(SELECT COUNT(DISTINCT wa_inventory_items.id) FROM wa_inventory_items
        //             LEFT JOIN wa_inventory_location_uom ON wa_inventory_items.id = wa_inventory_location_uom.inventory_id
        //             LEFT JOIN wa_inventory_categories ON wa_inventory_items.wa_inventory_category_id = wa_inventory_categories.id
        //             WHERE wa_inventory_location_uom.uom_id = bin.id 
        //             AND wa_inventory_categories.id IN ($dailyStockTakeCategoriesString) ) AS total_items")
        //     )
        //     ->leftJoin('wa_stock_count_variation as wscv', function ($join) use($today) {
        //         $join->on('bin.id', '=', 'wscv.uom_id')
        //             ->whereNotNull('wscv.variation')
        //             ->whereDate('wscv.created_at', $today);
        //     })
        //     ->leftJoin('wa_inventory_items as wii', 'wscv.wa_inventory_item_id', '=', 'wii.id')
        //     ->leftJoin('wa_inventory_location_uom as wilu', 'bin.id', '=', 'wilu.uom_id') 
        //     ->leftJoinSub($totalCategoriesSubquery, 'total_cats', function ($join) {
        //         $join->on('bin.id', '=', 'total_cats.uom_id');
        //     })
        //     ->where('wii.status', 1)
        //     ->groupBy('bin.title', 'total_cats.total_categories')
        //     ->get(); 
        //     $exemptionSchedule = BlockUsersExemptionSchedule::where('target_date', Carbon::tomorrow()->toDateString())->first();
        //     if($exemptionSchedule){
        //         $exemptedUsers = BlockUsersExemptionScheduleUser::where('schedule_id', $exemptionSchedule->id)->pluck('user_id')->toArray();
        //     }else{
        //         $exemptedUsers = [];
        //     }
        // foreach($results as $result){
        //     if(isset($result->counted_categories) && ($result->counted_categories < 2)){
        //         $storeKeepers = User::where('wa_unit_of_measures_id', $result->id)->where('role_id', 152)->whereNotIn('id', $exemptedUsers)->get();
        //         foreach($storeKeepers as $keeper){
        //             $categoriesInBin = DB::table('wa_inventory_location_uom')
        //                 ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wa_inventory_location_uom.inventory_id')
        //                 ->where('wa_inventory_location_uom.uom_id', $keeper->wa_unit_of_measure_id)
        //                 ->where('wa_inventory_items.status', 1)
        //                 ->distinct('wa_inventory_items.id')
        //                 ->count('wa_inventory_items.wa_inventory_category_id');
        //             if($categoriesInBin == $result->counted_categories ){
        //             }else{
        //             $keeper->update(['is_blocked' => 1]);
        //             $keeper->update(['block_reason' => 'You did not hit your Daily stock count targets hence access denied. Contact your stocks Controller']);
        //             }
        //         }
        //     }
        // }


        //newer Block Implementation
             $exemptionSchedule = BlockUsersExemptionSchedule::where('target_date', Carbon::tomorrow()->toDateString())->first();
            if($exemptionSchedule){
                $exemptedUsers = BlockUsersExemptionScheduleUser::where('schedule_id', $exemptionSchedule->id)->pluck('user_id')->toArray();
            }else{
                $exemptedUsers = [];
            }
        $users = DB::table('users')
            ->select(
                'users.id as storekeeper_id',
                'bin.id as id',
                'bin.title as bin',
                DB::raw("(SELECT COUNT(DISTINCT wii.wa_inventory_category_id)
                    FROM wa_inventory_items as wii
                    LEFT JOIN wa_inventory_location_uom ON wa_inventory_location_uom.inventory_id = wii.id 
                    WHERE wii.status = '1'
                    AND wa_inventory_location_uom.uom_id = bin.id
                ) as total_categories"),
                DB::raw('COALESCE(COUNT(DISTINCT wscv.category_id), 0) as counted_categories'),
                DB::raw('COALESCE(COUNT(DISTINCT wscv.wa_inventory_item_id), 0) as counted_items'),
            )
            ->leftJoin('wa_unit_of_measures as bin', 'bin.id', 'users.wa_unit_of_measures_id')
            ->leftJoin('wa_stock_count_variation as wscv', function ($join) use($today) {
                $join->on('bin.id', '=', 'wscv.uom_id')
                    ->whereNotNull('wscv.variation')
                    ->whereDate('wscv.created_at', $today);
            })
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'wscv.wa_inventory_item_id')
            ->where('wa_inventory_items.status', '1')
            ->where('users.role_id', 152)
            ->whereNotIn('users.id', $exemptedUsers)
            ->groupBy('users.id')
            ->get();

        foreach($users as $user){
            if ($user->total_categories == 1 && $user->counted_categories == $user->total_categories) {
                $fullCategoryCount = DB::table('wa_stock_count_variation as wscv')
                ->join('wa_inventory_items as wii', 'wii.id', '=', 'wscv.wa_inventory_item_id')
                ->where('wscv.uom_id', $user->id)
                ->where('wii.status', '1')
                ->select('wii.wa_inventory_category_id')
                ->groupBy('wii.wa_inventory_category_id')
                ->havingRaw('COUNT(DISTINCT wscv.wa_inventory_item_id) = (SELECT COUNT(wa_inventory_items.id) FROM wa_inventory_items WHERE wa_inventory_items.wa_inventory_category_id = wii.wa_inventory_category_id AND wa_inventory_items.status = "1")')
                ->count();
    
                if ($fullCategoryCount < 1) {
                    // Block the user if they haven't counted 100% in the one categories
                    $userRecord = User::find($user->storekeeper_id);
                    $userRecord->update([
                        'is_blocked' => 1,
                        'block_reason' => 'You did not achieve your stock count targets, hence access denied. Contact your stocks controller.'
                    ]);
                }
                    continue;
            }
            if(($user->counted_categories < 2) && ($user->counted_categories != $user->total_categories)){
                //fetch User and block
                $userRecord = User::find($user->storekeeper_id);
                $userRecord->update(['is_blocked' => 1]);
                $userRecord->update(['block_reason' => 'You did not hit your Daily stock count targets hence access denied. Contact your stocks Controller']);
            }else{
                //check if they counted 100% in any two categories and block if not
                $fullCategoryCount = DB::table('wa_stock_count_variation as wscv')
                ->join('wa_inventory_items as wii', 'wii.id', '=', 'wscv.wa_inventory_item_id')
                ->where('wscv.uom_id', $user->id)
                ->where('wii.status', '1')
                ->select('wii.wa_inventory_category_id')
                ->groupBy('wii.wa_inventory_category_id')
                ->havingRaw('COUNT(DISTINCT wscv.wa_inventory_item_id) = (SELECT COUNT(wa_inventory_items.id) FROM wa_inventory_items WHERE wa_inventory_items.wa_inventory_category_id = wii.wa_inventory_category_id AND wa_inventory_items.status = "1")')
                ->count();
    
            if ($fullCategoryCount < 2) {
                // Block the user if they haven't counted 100% in at least two categories
                $userRecord = User::find($user->storekeeper_id);
                $userRecord->update([
                    'is_blocked' => 1,
                    'block_reason' => 'You did not fully count two or more categories, hence access denied. Contact your stocks controller.'
                ]);
            }
            }

        }
    }       
}
