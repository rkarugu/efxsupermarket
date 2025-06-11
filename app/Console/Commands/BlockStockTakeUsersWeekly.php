<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class BlockStockTakeUsersWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:block-stock-take-users-weekly';

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
        $today = Carbon::now()->endOfDay();
        $sevenDaysAgo = Carbon::now()->subDays(7)->startOfDay();

        $locations  = WaLocationAndStore::get();
            $totalCategoriesSubquery = DB::table('wa_inventory_items as wii')
            ->join('wa_inventory_location_uom as wilu', 'wilu.inventory_id', '=', 'wii.id')
            ->select('wilu.uom_id', DB::raw('COUNT(DISTINCT wii.wa_inventory_category_id) as total_categories'))
            ->groupBy('wilu.uom_id');

        $results = DB::table('wa_unit_of_measures as bin')
        ->leftJoin('wa_stock_count_variation as wscv', function ($join) use($today, $sevenDaysAgo) {
            $join->on('bin.id', '=', 'wscv.uom_id')
                ->whereNotNull('wscv.variation')
                ->whereBetween('wscv.created_at', [$sevenDaysAgo,$today]);
        })
            ->leftJoin('wa_inventory_items as wii', 'wscv.wa_inventory_item_id', '=', 'wii.id')
            ->leftJoin('wa_inventory_location_uom as wilu', 'bin.id', '=', 'wilu.uom_id') 
            ->leftJoinSub($totalCategoriesSubquery, 'total_cats', function ($join) {
                $join->on('bin.id', '=', 'total_cats.uom_id');
            })
            ->select(
                'bin.id as id',
                'bin.title as bin',
                DB::raw('COALESCE(total_cats.total_categories, 0) as total_categories'),
                DB::raw('COALESCE(COUNT(DISTINCT wscv.category_id), 0) as counted_categories'),
                DB::raw('COALESCE(COUNT(DISTINCT wscv.wa_inventory_item_id), 0) as counted_items'),
                DB::raw('COALESCE(COUNT(DISTINCT wilu.inventory_id), 0) as total_items'),
            )
            ->groupBy('bin.title', 'total_cats.total_categories')
            ->get(); 
            foreach($results as $result){
                if(isset($result->counted_categories) && ($result->counted_categories < $results->total_categories)){
                    $storeKeepers = User::where('wa_unit_of_measures_id', $result->id)->where('role_id', 152)->get();
                    foreach($storeKeepers as $keeper){
                        $keeper->update(['is_blocked' => 1]);
                        $keeper->update(['block_reason' => 'You did not hit your Weekly stock count targets  hence access denied. Contact your stocks Controller']);
                    }

                }
            }
    }
}
