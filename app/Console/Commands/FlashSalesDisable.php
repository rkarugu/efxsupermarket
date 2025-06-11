<?php

namespace App\Console\Commands;

use App\Models\RoutePricing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FlashSalesDisable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:flash-sales-disable';

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
        try {
            $flashPrices = RoutePricing::where('status', 0)->where('is_flash', 1)->get();
            if($flashPrices){
                foreach($flashPrices as $flashPrice){
                    $flashPrice->status = 1;
                    $flashPrice->save();
                }
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'Failed', 'msg' => $e->getMessage()]);
        }
      
    }
}
