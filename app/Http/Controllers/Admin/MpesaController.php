<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentChannel;
use App\Events\PaymentReceived;
use App\Http\Controllers\Controller;
use App\InvoicePayment;
use App\Model\PaymentMethod;
use App\Model\WaAccountingPeriod;
use App\Model\WaBankAccount;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaPosCashSales;
use App\Model\WaRouteCustomer;
use App\Models\MpesaOperation;
use App\Services\MpesaService;
use App\Services\PaymentService;
use App\WaTenderEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function callback(Request $request)
    {

        $mpesaService = new MpesaService();
        $response = $mpesaService->callBack($request);


        $data = $response['data'];
        $paymentRecord = InvoicePayment::find($data->invoice_payment_id);
        $sale = WaPosCashSales::find($paymentRecord->payable_id);
        $paymentMethod = PaymentMethod::where('slug', $paymentRecord->payment_gateway)->first();
        $bank_account = WaBankAccount::where('bank_account_gl_code_id', $paymentMethod->gl_account_id)->first();
        $route_customer = WaRouteCustomer::find($sale->wa_route_customer_id);
        $matchedWaCustomer = WaCustomer::find($route_customer->customer_id);
        $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        $series_module = \App\Model\WaNumerSeriesCode::where('module', 'CASH_SALES')->first();


            $payload = [
                'client_invoice_ref' => $paymentRecord->order_no,
                'payment_channel' => PaymentChannel::Mpesa->value,
                'payment_reference' => $data->mpesa_receipt_number,
                'paid_amount' => $data->transaction_amount,
                'paying_number' => $data->phone_number,
                'payment_date' => $data->transaction_date,
            ];

            try {

                $tenderEntry = new WaTenderEntry();
                $tenderEntry->document_no = $sale->sales_no;
                $tenderEntry->channel = $paymentMethod?->title ?? PaymentChannel::Mpesa->value;
                $tenderEntry->reference = $data->mpesa_receipt_number;
                $tenderEntry->account_code = $bank_account->getGlDetail?->account_code;
                $tenderEntry->customer_id = $matchedWaCustomer->id;
                $tenderEntry->trans_date = $paymentRecord->created_at;
                $tenderEntry->wa_payment_method_id = $paymentMethod?->id ?? 7;
                $tenderEntry->amount = $data->transaction_amount;
                $tenderEntry->paid_by = "$sale->customer ($data->phone_number)";
                $tenderEntry->cashier_id = $sale->user_id;
                $tenderEntry->consumed = true;
                $tenderEntry->save();

                /*save debtor trans*/
                $debtorTrans = WaDebtorTran::create([
                    'type_number' => $series_module?->type_number,
                    'wa_customer_id' => $sale->wa_customer_id,
                    'customer_number' => $matchedWaCustomer->customer_code,
                    'trans_date' => $paymentRecord->created_at,
                    'input_date' => $paymentRecord->created_at,
                    'wa_accounting_period_id' => $accountingPeriod ? $accountingPeriod->id : null,
                    'shift_id' => null,
                    'invoice_customer_name' => "$sale?->customer",
                    'reference' => "$data->mpesa_receipt_number",
                    'amount' => - ($data->transaction_amount),
                    'document_no' => $sale->sales_no,
                    'branch_id' => $sale->branch_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'channel' => $paymentMethod?->title ?? PaymentChannel::Mpesa->value,
                    'wa_payment_method_id' => $paymentMethod?->id,
                ]);

                $payment_service = new PaymentService();
                $payload['tender_entry_id'] =  $tenderEntry->id;
                $payment_service->successCallback($payload);
            } catch (\Throwable $exception) {
                Log::error($exception->getMessage());
            }



    }

    public function query($checkout)
    {
        $mpesaService = new MpesaService();
        return $mpesaService->stkQuery($checkout);
    }
}
