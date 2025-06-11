<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentChannel;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\PaymentMethod;
use App\Model\User;
use App\Model\WaAccountingPeriod;
use App\Model\WaBankAccount;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaPosCashSales;
use App\Model\WaRouteCustomer;
use App\Models\BankedCashTransaction;
use App\Models\BankedDropTransaction;
use App\Models\CashDropTransaction;
use App\Models\ChiefCashierDeclaration;
use App\Models\CrcRecord;
use App\PaymentProvider;
use App\Services\InfoSkySmsService;
use App\SmsMessage;
use App\WaTenderEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KcbNotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        Log::info('Received Drop Notification. >>>>>>'.json_encode($request->all()));
        $banked_drop = BankedDropTransaction::firstOrCreate([
            'cash_drop_reference' =>$request->drop_number,
            'bank_reference' =>  $request->reference,
            'amount' =>  $request->amount,
        ]);
        $withoutHyphen = str_replace(['-', ' '], '', $request->drop_number);
        $withHyphen = substr_replace($withoutHyphen, '-', 3, 0);
        $reference = trim($request->reference);

        $drop = CashDropTransaction::where(function($query) use ($reference, $withHyphen) {
            $query->where('reference', $reference)
                ->orWhere('reference', $withHyphen);
        })
            ->whereDate('created_at', Carbon::today())
            ->with('user')
            ->latest()
            ->first();
        if ($drop)
        {
            $drop->update([
                'bank_receipt_number'=> $request->reference,
                'banked_amount'=> $request->amount + $drop->banked_amount,
            ]);
            $drop->refresh();

            $banked_drop->update([
                'user_id'=> $drop->user_id,
                'cash_drop_transaction_id'=> $drop->id,
                'cash_drop_reference'=> $drop->reference,
                'branch_id' =>  $drop->user->restaurant_id,
            ]);

            $tender_input = [
                'reference' => $drop->reference,
                'bank_receipt_number' => $drop->bank_receipt_number,
                'banked_amount' => $drop->banked_amount,
                'user_id' => $drop->user_id,
                'paid_by' => User::find($drop->user_id)->name,
            ];

            $this->recordTenderEntry($tender_input, false);
            /*send Notification to cahsier and Chief cashier*/
            $cheif_cashier_phone = User::find($drop->user_id)->phone_number;
            $chief_cashier_massage = "Cash drop with reference $drop->reference , has been deposited and cashier has been reactivated";
            $this->sendMessage($chief_cashier_massage, $cheif_cashier_phone);

            $cashier_message = "Cash drop $drop->reference , has been deposited and you have been reactivated to continue with sales";
            $cashier_phone = User::find($drop->cashier_id)->phone_number;
            $this->sendMessage($cashier_message, $cashier_phone);

        }
        $crc = CrcRecord::where('reference', $request->drop_number)
            ->orWhere('reference', str_replace('-', '', $request->drop_number))
            ->first();
        if ($crc)
        {
            $crc->update([
                'bank_reference'=> $request->reference,
                'banked_amount'=> $request->amount + $crc->banked_amount,
            ]);
            $crc->refresh();

            $banked_drop->update([
                'user_id'=> $crc->user_id,
                'cash_drop_reference'=> $crc->reference,
            ]);

            $tender_input = [
                'reference' => $crc->reference,
                'bank_reference' => $crc->bank_receipt_number,
                'banked_amount' => $crc->banked_amount,
                'user_id' => $crc->user_id,
                'paid_by' => User::find($crc->user_id)->name,
            ];

            $this->recordTenderEntry($tender_input, true);
        }
    }

    public function sendMessage($message, $phone)
    {
        (new InfoSkySmsService())->sendMessage($message, $phone);
    }

    public function recordTenderEntry($drop, $crc = false)
    {

       $restaurant_id = User::find($drop['user_id'])->restaurant_id;
        $paymentMethod = PaymentMethod::whereHas('paymentGlAccount.branches')->with('paymentGlAccount')->where('slug', 'kenya-commercial-bank')->first();

        $last_sale = WaPosCashSales::where('branch_id', $restaurant_id)->latest()->first();
        $route_customer = WaRouteCustomer::find($last_sale->wa_route_customer_id);
        $matchedWaCustomer = WaCustomer::find($route_customer->customer_id);
        $bank_account = WaBankAccount::where('bank_account_gl_code_id', $paymentMethod->gl_account_id)->first();


        try {
            DB::beginTransaction();

            $tenderEntry = new WaTenderEntry();
            $tenderEntry->document_no = $drop['reference'];
            $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::KCB->value;
            $tenderEntry->reference = $drop['bank_receipt_number'];
            $tenderEntry->account_code = $bank_account->getGlDetail?->account_code;
            $tenderEntry->customer_id = $matchedWaCustomer->id;
            $tenderEntry->trans_date = now();
            $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
            $tenderEntry->amount = $drop['banked_amount'];
            $tenderEntry->paid_by = $drop['paid_by'];
            $tenderEntry->cashier_id = $drop['user_id'];
            $tenderEntry->consumed = true;
            $tenderEntry->save();

            /*save debtor trans*/
            if (!$crc)
            {
                $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                $debtor =  WaDebtorTran::create([
                    'type_number' => $drop['reference'],
                    'wa_customer_id' => $matchedWaCustomer->id,
                    'customer_number' => $matchedWaCustomer->customer_code,
                    'trans_date' => now(),
                    'input_date' => now(),
                    'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                    'shift_id' => null,
                    'invoice_customer_name' => $matchedWaCustomer->customer_name,
                    'reference' => $drop['bank_receipt_number'],
                    'amount' => -($drop['banked_amount']),
                    'document_no' => $drop['reference'],
                    'branch_id' => $restaurant_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'channel' => PaymentChannel::KCB->value,
                    'wa_payment_method_id' => $paymentMethod?->id,
                ]);
            }

            DB::commit();
        }catch (\Throwable $exception)
        {
            DB::rollBack();

        }


    }

    public function postDirectDeposit(Request $request)
    {

        $banked_cash = BankedCashTransaction::firstOrCreate([
            'bank_reference' =>  $request->transactionReference,
            'amount' =>  $request->transactionAmount,
            'banked_at' =>  Carbon::parse($request->timestamp),
            'customer_reference' =>$request->customerReference ??  null,
        ]);

        $cdp = ChiefCashierDeclaration::where('reference', $request->customerReference)
            ->orWhere('reference', str_replace('-', '', $request->customerReference))
            ->first();
        if ($cdp)
        {
            $cdp->update([
                'bank_reference'=> $request->transactionReference,
                'banked_amount'=> $request->transactionAmount + $cdp->banked_amount,
                'banking_time'=> Carbon::parse($request->timestamp),
                'branch_id' =>  $cdp->branch_id,
            ]);
            $banked_cash->update([
                'user_id' =>   $cdp->user_id,
                'chief_cashier_declaration_id' =>  $cdp->id,
                'chief_cashier_declaration_reference' =>   $cdp->reference,
                'bank_reference' =>  $request->transactionReference,
                'banked_at' =>  Carbon::parse($request->timestamp),
            ]);
            $tender_input = [
                'reference' => $cdp->reference,
                'bank_receipt_number' => $request->transactionReference,
                'banked_amount' => $request->transactionAmount,
                'user_id' => $cdp->user_id,
                'paid_by' => User::find($cdp->user_id)->name,
            ];

            $this->recordTenderEntry($tender_input);
        }
    }

}