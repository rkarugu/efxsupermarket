<?php

namespace App\Console\Commands;

use App\Model\WaCustomer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BlockRouteAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:block-route-accounts';

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
            $customers = WaCustomer::select([
                'wa_customers.*',
                DB::raw("(SELECT SUM(amount) FROM wa_debtor_trans AS trans WHERE trans.wa_customer_id = wa_customers.id) AS balance")
            ])->get();
            foreach($customers as $customer) {
                if($customer->balance > 0){
                    DB::table('wa_customers')->where('id', $customer->id)->update(['is_blocked' => '1']);
                }
            }
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
       

 
     

    }
}
