<?php

namespace App\Http\Controllers;

use App\Enums\PaymentChannel;
use App\Model\PaymentMethod;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\WaAccountingPeriod;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaInternalRequisition;
use App\Model\WaNumerSeriesCode;
use App\Models\NewMpesaIpnNotification;
use App\Services\InfoSkySmsService;
use App\WaTenderEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewMpesaIpnNotificationController extends Controller
{
    public function __construct(protected InfoSkySmsService $smsService) {}
    
    public function receiveIpn(Request $request)
    {
        try {
            $encodedRequest = json_encode($request->all());
            $decodedRequest = json_decode($encodedRequest, true);

            $invoiceNumber = isset($decodedRequest['BillRefNumber']) ? $decodedRequest['BillRefNumber'] : null;
            $reference = isset($decodedRequest['TransID']) ? $decodedRequest['TransID'] : null;
            $amount = isset($decodedRequest['TransAmount']) ? (float)$decodedRequest['TransAmount'] : null;

            $notification = NewMpesaIpnNotification::create([
                'payment_details' => $encodedRequest,
                'paybill' => isset($decodedRequest['BusinessShortCode']) ? $decodedRequest['BusinessShortCode'] : null,
                'invoice_number' => $invoiceNumber,
                'reference' => $reference,
                'amount' => $amount
            ]);

            $transactionExists = DB::table('wa_debtor_trans')
                ->where('reference', 'like', $reference)
                ->where('amount', ($amount * -1))
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

                    $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'RECEIPT')->first();
                    $documentNo = getCodeWithNumberSeries('RECEIPT');
                    $paymentMethod = PaymentMethod::find(17); // TODO: Dynamify
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
                        'amount' => - ($amount),
                        'document_no' => $documentNo,
                        'branch_id' => $route->restaurant_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'channel' => $paymentMethod?->title ?? PaymentChannel::Mpesa->value,
                        'wa_payment_method_id' => $paymentMethod?->id,
                    ]);

                    $tenderEntry = new WaTenderEntry();
                    $tenderEntry->document_no = $documentNo;
                    $tenderEntry->notification_id = $notification->id;
                    $tenderEntry->wa_sales_invoice_id = $invoice->id;
                    $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::Vooma->value;
                    $tenderEntry->reference = $debtorTrans->reference;
                    $tenderEntry->additional_info = $debtorTrans->reference;
                    $tenderEntry->customer_id = $matchedWaCustomer->id;
                    $tenderEntry->trans_date = $debtorTrans->created_at;
                    $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
                    $tenderEntry->amount = abs($debtorTrans->amount);
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
            // TODO: Sentry
            Log::info("Failed to log mpesa payment: " . $th->getMessage());
        }

        return $this->jsonify([]);
    }
}
