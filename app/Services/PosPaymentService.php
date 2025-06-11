<?php

namespace App\Services;

use App\Events\PaymentReceived;
use App\Interfaces\MpesaPaymentInterface;
use App\InvoicePayment;
use App\Model\PaymentMethod;
use App\Model\WaPosCashSales;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosPaymentService
{
    public function initiatePayment(WaPosCashSales $order, $amount,  $paymentMethod_id, $phone_number)
    {
        $statusCode = 200;
        $message = 'Payment initiated successfully.';

        $payment = InvoicePayment::where('payable_id', $order->id)
            ->where('payable_type', get_class($order))
            ->orderBy('created_at', 'DESC')
            ->first();
        $paymentMethod = PaymentMethod::with('provider')
            ->find($paymentMethod_id);
        if (!$payment)
        {

            $payment = InvoicePayment::create([
                'order_no' => $order->sales_no,
                'truncated_order_no' => str_replace('CS-', '', $order->sales_no),
                'payment_gateway' => $paymentMethod->slug,
                'initiating_number' => $phone_number ?? $order->customer_phone_number,
                'invoice_amount' => (int)ceil( $amount),
                'status' => 'pending',
                'payable_id' => $order->id,
                'payable_type' => get_class($order)
            ]);
        }

        if ($payment->status == 'settled') {
            return response()->json([
                'message' =>'Payment already Made',
                'results' => 0
            ], $statusCode);

        }
        $payment->update([
            'invoice_amount' => (int)ceil( $amount),
        ]);

        $payment->refresh();

        $service  = new MpesaService();
        $service->stkPush($phone_number, $payment);

        return response()->json([
            'message' => $message,
            'results' => 1
        ], $statusCode);
    }

    public function paymentCallback($payload, $invoice_payment): void
    {

        /*update cash sales status and throw event*/
        if ($payload['paid_amount'] == $invoice_payment->invoice_amount )
        {
            /*update payemnt invoice*/
            $invoice_payment->update([
                'status'=>'settled',
                'payment_invoice_no' => $payload['client_invoice_ref'],
                'payment_channel' => $payload['payment_channel'],
                'payment_reference' => $payload['payment_reference'],
                'paid_amount' => $payload['paid_amount'],
                'paying_number' => $payload['paying_number'],
                'payment_date' => $payload['payment_date'],
            ]);
            /*update cash sale*/

            /*run Post pay */
            $cashsale = WaPosCashSales::find($invoice_payment->payable_id);
            $payment_method = PaymentMethod::where('slug', $invoice_payment->payment_gateway)->first();
            $payment_methods = [
                [
                    'method_id'=>$payment_method->id,
                    'amount'=> $invoice_payment->invoice_amount,
                    'tender_entry_id'=> $payload['tender_entry_id']
                ]
            ];
            PosCashSaleService::postPay($cashsale, $payment_methods);
            $invoice_payment->refresh();
            $payload = [
                'sales_id'=>$invoice_payment->payable_id,
                'paid'=> true,
                'details'=>"Payment was successful: Reference ".$invoice_payment->payment_reference,
            ];
        }else
        {
            /*payment failed*/
            $payload = [
                'sales_id'=>$invoice_payment->payable_id,
                'paid'=> false,
                'details'=>"Payment Failed",
            ];
        }
        Log::info('event Dispatched');
        event(new PaymentReceived($payload));
    }
}