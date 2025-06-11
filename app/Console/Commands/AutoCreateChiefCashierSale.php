<?php

namespace App\Console\Commands;

use App\Model\WaPosCashSales;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCreateChiefCashierSale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-create-chief-cashier-sale';

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

        $today = Carbon::now();
        $day = $today->day; 
        $month = $today->month;
        $posSale = new WaPosCashSales();
        $posSale->user_id = 1099;
        $posSale->sales_no = 'CIV-CC-'. $day . '_' . $month; 
        $posSale->status = 'Completed';
        $posSale->paid_at = $today->toDateTimeString();
        $posSale->created_at = $today->toDateTimeString();
        $posSale->updated_at = $today->toDateTimeString();
        $posSale->wa_route_customer_id = 56;
        $posSale->branch_id = 1;
        $posSale->attending_cashier = 1099;
        $posSale->is_suspended = false;
        $posSale->customer = 'Customer Name';
        $posSale->customer_phone_number = '0706257826';
        $posSale->date = Carbon::now()->format('Y-m-d');
        $posSale->time = Carbon::now()->format('H:i:s');
        $posSale->save();
    }
}
