<?php

namespace App\Http\Controllers\Admin;

use App\CustomerEquityPayment;
use App\CustomerKcbPayment;
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
use App\Models\KcbIpnNotification;
use App\Services\InfoSkySmsService;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

class CustomerKcbPaymentController extends Controller
{
    public function __construct(protected InfoSkySmsService $smsService) {}

    public function receive(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $incomingMpesaReference = $request->mpesa_reference;
            if (!($existingTransaction = CustomerKcbPayment::latest()->where('mpesa_reference', $incomingMpesaReference)->first())) {
                $documentNo = getCodeWithNumberSeries('RECEIPT');
                $paymentRecord = CustomerKcbPayment::create([
                    'document_no' => $documentNo,
                    'receiving_till' => $request->receiving_till,
                    'paid_amount' => $request->paid_amount,
                    'kcb_timestamp' => $request->kcb_timestamp,
                    'kcb_reference' => $request->kcb_reference,
                    'mpesa_reference' => $request->mpesa_reference,
                    'customer_number' => $request->customer_number,
                    'customer_name' => $request->customer_name,
                    'service_provider' => $request->service_provider,
                ]);

                $matchedWaCustomer = WaCustomer::where('kcb_till', $request->receiving_till)->first();
                if ($matchedWaCustomer) {
                    $paymentRecord->update(['matched_wa_customer_id' => $matchedWaCustomer->id]);
                }

                $trimmedNumber = ltrim($request->get('customer_number'), '254');
                $alternativeNumberQuery = "0$trimmedNumber";
                $matchedRouteCustomer = WaRouteCustomer::where('phone', $request->get('customer_number'))->orWhere('phone', $alternativeNumberQuery)->first();
                if ($matchedRouteCustomer) {
                    $paymentRecord->update(['matched_route_customer_id' => $matchedRouteCustomer->id]);
                }

                if ($matchedWaCustomer) {
                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
                    $route = Route::find($matchedWaCustomer->route_id);
                    $paymentMethod = PaymentMethod::find($matchedWaCustomer->kcb_payment_method_id);

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
                        'trans_date' => $paymentRecord->created_at,
                        'input_date' => $paymentRecord->created_at,
                        'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                        'shift_id' => null,
                        'invoice_customer_name' => "$matchedRouteCustomer?->name",
                        'reference' => "$paymentRecord->mpesa_reference",
                        'amount' => - ($paymentRecord->paid_amount),
                        'document_no' => $documentNo,
                        'branch_id' => $route->restaurant_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'channel' => $paymentMethod?->title ?? PaymentChannel::Vooma->value,
                        'wa_payment_method_id' => $paymentMethod?->id,
                    ]);

                    $reference = "$paymentRecord->mpesa_reference";
                    if ($paymentMethod) {
                        $bank_account = WaBankAccount::where('bank_account_gl_code_id', $paymentMethod->gl_account_id)->first();
                    } else {
                        $bank_account = WaBankAccount::find(2);
                    }

                    $tenderEntry = new WaTenderEntry();
                    $tenderEntry->document_no = $documentNo;
                    $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::Vooma->value;
                    $tenderEntry->account_code = $bank_account->getGlDetail?->account_code;
                    $tenderEntry->reference = $paymentRecord->mpesa_reference;
                    $tenderEntry->additional_info = $paymentRecord->kcb_reference;
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
                    $customerBalance =  WaDebtorTran::where('wa_customer_id', $matchedWaCustomer->id)->sum('amount');
                    if ($customerBalance <= 0) {
                        $matchedWaCustomer->is_blocked = 0;
                        $matchedWaCustomer->save();
                    }
                } else {
                    $paymentRecord->update(['status' => 'hanging']);
                }

                $formattedAmount = format_amount_with_currency($paymentRecord->paid_amount);
                //                $message = "Hello $request->customer_name, we have received your payment of $formattedAmount. \nThank you for doing business with KHEL.";
                //                $this->smsService->sendMessage($message, $alternativeNumberQuery);

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

    public function receiveVoomaIpn(Request $request)
    {
        $messageId = "";
        try {
            Log::info("KCB Vooma IPN notif " . json_encode($request->all()));
            $notification = KcbIpnNotification::create([
                'payment_details' => json_encode($request->all())
            ]);

            $decodedPayload = json_decode($notification->payment_details, true);

            $reference = $decodedPayload['requestPayload']['additionalData']['notificationData']['transactionID'];
            if (strncmp($reference, '00', 2) == 0) {
                $reference = str_replace('-92', '', $reference);
                $reference = ltrim($reference, '00');
            }

            $amount = (float)$decodedPayload['requestPayload']['additionalData']['notificationData']['transactionAmt'] * -1;
            $invoiceNumber = $decodedPayload['requestPayload']['additionalData']['notificationData']['businessKey'];
            $paybill = $decodedPayload['requestPayload']['primaryData']['businessKey'];
            $customerNumber = $decodedPayload['requestPayload']['additionalData']['notificationData']['debitMSISDN'];

            $transactionExists = DB::table('wa_debtor_trans')
                ->where('reference', $reference)
                ->where('amount', $amount)
                ->first();

            if (!$transactionExists) {
                $invoice = WaInternalRequisition::with(['getRouteCustomer'])->where('requisition_no', $invoiceNumber)
                    ->orWhere('requisition_no', 'INV-' . $invoiceNumber)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($invoice) {
                    $matchedWaCustomer = WaCustomer::find($invoice->customer_id);
                    $route = Route::find($matchedWaCustomer->route_id);
                    $branch = Restaurant::find($route->restaurant_id);
                    if (($branch->kcb_mpesa_paybill == $paybill) || ($branch->kcb_vooma_paybill == $paybill)) {
                        $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                        $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
                        $documentNo = getCodeWithNumberSeries('RECEIPT');
                        $paymentMethod = PaymentMethod::find($matchedWaCustomer->kcb_payment_method_id);
                        $route = Route::find($matchedWaCustomer->route_id);

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
                            'amount' => $amount,
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
                        $tenderEntry->additional_info = $reference;
                        $tenderEntry->customer_id = $matchedWaCustomer->id;
                        $tenderEntry->trans_date = $debtorTrans->created_at;
                        $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
                        $tenderEntry->amount = abs($debtorTrans->amount);
                        $tenderEntry->paid_by = $customerNumber;
                        $tenderEntry->cashier_id = 1;
                        $tenderEntry->branch_id = $route->restaurant_id;
                        $tenderEntry->save();


                        $notification->update(['status' => 'settled']);
                        $invoice->update(['status' => 'PAID']);

                        try {
                            $formattedAmount = format_amount_with_currency(abs($amount));
                            $sales_message = "Hi $matchedWaCustomer->customer_name, A payment of $formattedAmount has been received from {$invoice->getRouteCustomer?->name} against $invoice->requisition_no with  reference $reference";
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
            }

            // return $this->jsonify([
            //     "transactionID" => Str::uuid(),
            //     "statusCode" => 0,
            //     "statusMessage" => "Notification received"
            // ], 200);
            return $this->jsonify([
                "header" => [
                    "messageID" => "$messageId",
                    "originatorConversationID" => Str::uuid(),
                    "statusCode" => "0",
                    "statusMessage" => "Processed Successfully"
                ],
                "responsePayload" => [
                    "transactionInfo" => [
                        "transactionId" => Str::uuid()
                    ]
                ]
            ]);
        } catch (\Throwable $th) {
            try {
                Log::info("Failed to log KCB IPN notif " . json_encode($request->all()) . ", citing {$th->getMessage()}");
            } catch (\Throwable $th) {
                //throw $th;
            }

            // return $this->jsonify([
            //     "transactionID" => Str::uuid(),
            //     "statusCode" => 0,
            //     "statusMessage" => "Notification received"
            // ], 200);

            return $this->jsonify([
                "header" => [
                    "messageID" => "$messageId",
                    "originatorConversationID" => Str::uuid(),
                    "statusCode" => "0",
                    "statusMessage" => "Processed Successfully"
                ],
                "responsePayload" => [
                    "transactionInfo" => [
                        "transactionId" => Str::uuid()
                    ]
                ]
            ]);
        }
    }

    public function receiveIpn(Request $request)
    {
        Log::info("KCB Normal IPN notif " . json_encode($request->all()));
        $messageId = "";

        try {
            KcbIpnNotification::create([
                'payment_details' => json_encode($request->all())
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->jsonify([
            "header" => [
                "messageID" => "$messageId",
                "originatorConversationID" => Str::uuid(),
                "statusCode" => "0",
                "statusMessage" => "Processed Successfully"
            ],
            "responsePayload" => [
                "transactionInfo" => [
                    "transactionId" => Str::uuid()
                ]
            ]
        ]);
    }
}
