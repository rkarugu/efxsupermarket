<?php

namespace App\Http\Controllers\Admin;

use App\CustomerEquityPayment;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\PaymentMethod;
use App\Model\Route;
use App\Model\WaAccountingPeriod;
use App\Model\WaBankAccount;
use App\Model\WaBanktran;
use App\Model\WaCompanyPreference;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaRouteCustomer;
use App\WaTenderEntry;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\PaymentChannel;
use App\Model\Restaurant;
use App\Model\WaInternalRequisition;
use App\Models\EquityIpnTransaction;
use App\Services\InfoSkySmsService;

class CustomerEquityPaymentController extends Controller
{
    public function __construct(protected InfoSkySmsService $smsService) {}

    public function receive(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $newReference = $request->get('transactionRefNo');
            $newAmount = $request->get('amount');
            if (!($existingTransaction = CustomerEquityPayment::latest()->where('transaction_reference', $newReference)->where('paid_amount', $newAmount)->first())) {

                $paymentRecord = CustomerEquityPayment::create([
                    'receiving_till' => $request->get('TillNumber'),
                    'paid_amount' => $request->get('amount'),
                    'eb_timestamp' => $request->get('timeStamp'),
                    'transaction_reference' => $request->get('transactionRefNo'),
                    'customer_number' => $request->get('mobileNumber'),
                    'customer_name' => $request->get('customerName'),
                    'narrative' => $request->get('additionalInfo'),
                    'service_provider' => $request->get('servedBy'),
                ]);


                $trimmedTill = ltrim($request->get('TillNumber'), '0');
                $altTrimmedTill = ltrim($request->get('TillNumber'), '254');
                $altTrimmedTillWithLeadingZero = "0$altTrimmedTill";
                $matchedWaCustomer = WaCustomer::where('equity_till', $request->get('TillNumber'))
                    ->orWhere('equity_till', $trimmedTill)
                    ->orWhere('equity_till', $altTrimmedTill)
                    ->orWhere('equity_till', $altTrimmedTillWithLeadingZero)
                    ->first();
                if ($matchedWaCustomer) {
                    $paymentRecord->update(['matched_wa_customer_id' => $matchedWaCustomer->id]);
                }

                $trimmedNumber = ltrim($request->get('mobileNumber'), '254');
                $alternativeNumberQuery = "0$trimmedNumber";
                $matchedRouteCustomer = WaRouteCustomer::where('phone', $request->get('mobileNumber'))->orWhere('phone', $alternativeNumberQuery)->first();
                if ($matchedRouteCustomer) {
                    $paymentRecord->update(['matched_route_customer_id' => $matchedRouteCustomer->id]);
                }

                if ($matchedWaCustomer) {
                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
                    $documentNo = getCodeWithNumberSeries('RECEIPT');

                    CustomerEquityPayment::where('id', $paymentRecord->id)
                        ->update(['document_no' => $documentNo,]);

                    $route = Route::find($matchedWaCustomer->route_id);
                    $paymentMethod = PaymentMethod::find($matchedWaCustomer->equity_payment_method_id);

                    //get  phone and name of the salesmans
                    $sales_phone = $route->salesman()?->phone_number;
                    $sales_name = $route->salesman()?->name;
                    $route_name = $route->name;
                    $route_customer_phone = $matchedWaCustomer->telephone;
                    $route_customer_name = $matchedWaCustomer->customer_name;

                    $debtorTrans = WaDebtorTran::create([
                        'salesman_id' => $matchedWaCustomer->id,
                        'salesman_user_id' => $route->salesman()?->id,
                        'type_number' => $series_module?->type_number,
                        'wa_customer_id' => $matchedWaCustomer->id,
                        'customer_number' => $matchedWaCustomer->customer_code,
                        'trans_date' => date('Y-m-d H:i:s'),
                        'input_date' => date('Y-m-d H:i:s'),
                        'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                        'shift_id' => null,
                        'invoice_customer_name' => $matchedRouteCustomer?->name ?? $paymentRecord->customer_name,
                        'reference' => (($paymentRecord->service_provider == 'EQUITY-SAFARICOM') || ($paymentRecord->customer_name == 'Cr')) ? $paymentRecord->narrative : $paymentRecord->transaction_reference,
                        'amount' => - ($paymentRecord->paid_amount),
                        'document_no' => $documentNo,
                        'branch_id' => $route->restaurant_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'channel' => $paymentMethod?->title ?? PaymentChannel::Eazzy->value,
                        'wa_payment_method_id' => $paymentMethod?->id,
                    ]);

                    $reference = $paymentRecord->service_provider == 'EQUITY-SAFARICOM' ? $paymentRecord->narrative : $paymentRecord->transaction_reference;

                    if ($paymentMethod) {
                        $bank_account = WaBankAccount::where('bank_account_gl_code_id', $paymentMethod->gl_account_id)->first();
                    } else {
                        $bank_account = WaBankAccount::where('account_name', 'EQUITY MAKONGENI')->first();
                    }

                    $tenderEntry = new WaTenderEntry();
                    $tenderEntry->document_no = $documentNo;
                    $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::Vooma->value;
                    $tenderEntry->account_code = $bank_account->getGlDetail?->account_code;
                    $tenderEntry->reference = $reference;
                    $tenderEntry->additional_info = $paymentRecord->narrative;
                    $tenderEntry->customer_id = $matchedWaCustomer->id;
                    $tenderEntry->trans_date = $paymentRecord->created_at;
                    $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
                    $tenderEntry->amount = $paymentRecord->paid_amount;
                    $tenderEntry->paid_by = $paymentRecord->customer_name;
                    $tenderEntry->cashier_id = 1;
                    $tenderEntry->branch_id = $route->restaurant_id;
                    $tenderEntry->save();

                    updateUniqueNumberSeries('RECEIPT', $documentNo);
                    $paymentRecord->update(['status' => 'settled']);
                    //unblock customer
                    $customerBalance =  WaDebtorTran::where('wa_customer_id', $matchedWaCustomer->id)->sum('amount');
                    if ($customerBalance <= 0) {
                        $matchedWaCustomer->is_blocked = 0;
                        $matchedWaCustomer->save();
                    }
                } else {
                    $paymentRecord->update(['status' => 'hanging']);
                }

                $formattedAmount = format_amount_with_currency($paymentRecord->paid_amount);
                //            $message = "Hello $paymentRecord->customer_name, we have received your payment of $formattedAmount. \nThank you for doing business with KHEL.";
                //            $this->smsService->sendMessage($message, $alternativeNumberQuery);


                //send sms to salesman

                /* try {
                    $sales_message =  "Hi $sales_name, A payment of $formattedAmount. has been received from $paymentRecord->customer_name against $route_name  account with  reference $reference";
                    $this->smsService->sendMessage($sales_message, $sales_phone);
                } catch (\Throwable $e) {
                    //echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
                */
                try {
                    $sales_message = "Hi  $route_customer_name, A payment of $formattedAmount. has been received from $paymentRecord->customer_name against $route->route_name  account with  reference $reference";
                    $this->smsService->sendMessage($sales_message, $route_customer_phone);
                } catch (\Throwable $e) {
                    //echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }


            DB::commit();
            return $this->jsonify(['message' => 'Payment notification received successfully'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info("Failed to receive equity payment notification, citing {$e->getMessage()}");
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function receiveEquityIpn(Request $request): JsonResponse
    {
        $incomingPayload = json_encode($request->all());
        $decodedPayload = json_decode($incomingPayload, true);
        if ($transactionExists = EquityIpnTransaction::latest()->where('transaction_reference', $decodedPayload['transaction_reference'])->first()) {
            return response()->json([
                "responseCode" => "FAIL",
                "responseMessage" => "DUPLICATE TRANSACTION"
            ]);
        }

        try {
            $notification = EquityIpnTransaction::create($decodedPayload);

            $invoice = WaInternalRequisition::with(['getRouteCustomer'])->where('requisition_no', $decodedPayload['invoice_number'])
                ->orWhere('requisition_no', 'INV-' . $decodedPayload['invoice_number'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($invoice) {
                $matchedWaCustomer = WaCustomer::find($invoice->customer_id);
                $route = Route::find($matchedWaCustomer->route_id);
                $branch = Restaurant::find($route->restaurant_id);
                if ($branch->equity_paybill == $decodedPayload['paybill']) {
                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
                    $documentNo = getCodeWithNumberSeries('RECEIPT');
                    $paymentMethod = PaymentMethod::find($matchedWaCustomer->equity_payment_method_id);
                    $route = Route::find($matchedWaCustomer->route_id);

                    $reference = $decodedPayload['transaction_reference'];
                    if (strncmp($reference, '202', 3) == 0) {
                        $reference = substr($reference, 8);
                    }

                    $debtorTrans = WaDebtorTran::create([
                        'notification_id' => $notification->id,
                        'wa_sales_invoice_id' => $invoice->id,
                        'salesman_id' => $matchedWaCustomer->id,
                        'type_number' => $series_module?->type_number,
                        'wa_customer_id' => $matchedWaCustomer->id,
                        'customer_number' => $matchedWaCustomer->customer_code,
                        'trans_date' => date('Y-m-d H:i:s'),
                        'input_date' => date('Y-m-d H:i:s'),
                        'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                        'shift_id' => null,
                        'reference' => $reference,
                        'amount' => - ($decodedPayload['invoice_amount']),
                        'document_no' => $documentNo,
                        'branch_id' => $route->restaurant_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'channel' => $paymentMethod?->title ?? PaymentChannel::Eazzy->value,
                        'wa_payment_method_id' => $paymentMethod?->id,
                    ]);

                    $tenderEntry = new WaTenderEntry();
                    $tenderEntry->document_no = $documentNo;
                    $tenderEntry->notification_id = $notification->id;
                    $tenderEntry->wa_sales_invoice_id = $invoice->id;
                    $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::Vooma->value;
                    $tenderEntry->reference = $debtorTrans->reference;
                    $tenderEntry->additional_info = $decodedPayload['narration'];
                    $tenderEntry->customer_id = $matchedWaCustomer->id;
                    $tenderEntry->trans_date = $debtorTrans->created_at;
                    $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
                    $tenderEntry->amount = abs($debtorTrans->amount);
                    $tenderEntry->paid_by = $decodedPayload['customer_Mobile_No'];
                    $tenderEntry->cashier_id = 1;
                    $tenderEntry->branch_id = $route->restaurant_id;
                    $tenderEntry->save();


                    $notification->update(['status' => 'settled']);
                    $invoice->update(['status' => 'PAID']);

                    try {
                        $formattedAmount = format_amount_with_currency(abs($debtorTrans->amount));
                        $sales_message = "Hi $matchedWaCustomer->customer_name, A payment of $formattedAmount has been received from {$invoice->getRouteCustomer?->name} against $invoice->requisition_no with  reference $debtorTrans->reference";
                        $this->smsService->sendMessage($sales_message, $matchedWaCustomer->telephone);
                    } catch (\Throwable $e) {
                        //echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $formattedAmount = format_amount_with_currency(abs($debtorTrans->amount));
                        $sales_message = "Dear customer, we have received your payment of $formattedAmount against $invoice->requisition_no for {$invoice->getRouteCustomer?->bussiness_name} for route $route->route_name with  reference $debtorTrans->reference. Thank you for shopping with us.";
                        $this->smsService->sendMessage($sales_message, $invoice->getRouteCustomer?->phone);
                    } catch (\Throwable $e) {
                        //echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }
                }
            }
        } catch (\Throwable $th) {
            Log::info("Failed to log EB IPN notif $incomingPayload , citing {$th->getMessage()}");
        }

        return response()->json([
            "responseCode" => "OK",
            "responseMessage" => "SUCCESSFUL"
        ]);
    }
}
