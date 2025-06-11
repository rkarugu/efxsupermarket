<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\PaymentChannel;
use Illuminate\Support\Facades\Log;

class DebtorsToTenderEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debtors-to-tender-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Data to Tender Entries From Debtor Trans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
            $debtors = DB::table('wa_debtor_trans')
            ->select(
                'wa_debtor_trans.id',
                'wa_debtor_trans.amount',
                'wa_debtor_trans.channel',
                'wa_debtor_trans.reference',
                'wa_debtor_trans.wa_customer_id',
                'wa_debtor_trans.trans_date',
                'wa_debtor_trans.document_no',
                'wa_debtor_trans.created_at',
                'wa_debtor_trans.updated_at'
            )
            ->where('wa_debtor_trans.document_no', 'like', 'RCT%')
            ->where('created_at', '<', '2024-05-25 14:33:47')
            ->get();
            
            foreach ($debtors as $debtor) {
                DB::beginTransaction();
                try {
                    $amount = (int)abs($debtor->amount);
                        $account_code=null;
                        switch ($debtor->channel) {
                            case PaymentChannel::Vooma->value:
                                $account_code = 2;
                                $paymentMethod = 8;
                                break;
                            
                            case PaymentChannel::KCB->value:
                                $account_code = 3;
                                $paymentMethod = 9;
                                break;
                            
                            case PaymentChannel::Eazzy->value:
                                $account_code = 4;
                                $paymentMethod = 7;
                                break;
                        
                            case PaymentChannel::Equity->value:
                                $account_code = 5;
                                $paymentMethod = 10;
                                break;
                    
                            case PaymentChannel::Mpesa->value:
                                $account_code = 6;
                                $paymentMethod = 3;
                                break;
                            
                            default:
                                $account_code=null;
                                $paymentMethod = '';
                                break;
                        }

                        DB::table('wa_tender_entries')->insert([
                            'document_no' => $debtor->document_no,
                            'channel' => $debtor->channel,
                            'account_code' => $account_code,
                            'reference' => $debtor->reference,
                            'additional_info' => $debtor->reference,
                            'customer_id' => $debtor->wa_customer_id,
                            'trans_date' => $debtor->trans_date,
                            'wa_payment_method_id' => $paymentMethod,
                            'amount' => $amount,
                            'paid_by' => $debtor->channel,
                            'cashier_id' => 1,
                            'created_at' => $debtor->created_at,
                            'updated_at' => $debtor->updated_at,
                        ]);
                        
                        DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::info("Failed for ".$debtor->id.' with '. $e->getMessage());
                }
            }
    }
}
