<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesInvoiceValidationController extends Controller
{
    public function validateInvoice(Request $request): JsonResponse
    {
        $invoice = DB::table('wa_internal_requisitions')
            ->where('requisition_no', $request->invoice_number)
            ->orWhere('requisition_no', "INV-$request->invoice_number")
            ->orderBy('wa_internal_requisitions.created_at', 'DESC')
            ->first();

            return $this->jsonify([
                'invoice' => $invoice
            ]);

        // if (!$invoice) {
        //     return $this->jsonify([
        //         'message' => 'The invoice number does not exist in our records.',
        //         'validated' => false,
        //         'invoice_name' => "",
        //         'invoice_number' => ""
        //     ]);
        // }

        // return $this->jsonify([
        //     'message' => 'Invoice validated.',
        //     'validated' => true,
        //     'invoice_name' => $invoice->name,
        //     'invoice_number' => $invoice->requisition_no
        // ]);
    }

    public function validateInvoiceFromKcb(Request $request): JsonResponse
    {
        try {
            $requestInvoiceNumber = $request->customerReference;
            $messageId = Str::uuid();

            $invoice = DB::table('wa_internal_requisitions')
                ->select(
                    'wa_internal_requisitions.*',
                    DB::raw("(
                select sum(total_cost_with_vat) from wa_internal_requisition_items 
                where wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                ) as invoice_total")
                )
                ->where('requisition_no', $requestInvoiceNumber)
                ->orWhere('requisition_no', "INV-$requestInvoiceNumber")
                ->orderBy('wa_internal_requisitions.created_at', 'DESC')
                ->first();

            if (!$invoice) {
                return $this->jsonify([
                    'statusCode' => "422",
                    'statusMessage' => "Customer Reference Not Found"
                ], 422);
            }

            return $this->jsonify([
                'transactionID' => $messageId,
                'statusCode' => "0",
                'statusMessage' => "Success",
                'CustomerName' => "$invoice->name",
                'billAmount' => "$invoice->invoice_total",
                "currency" => "KES",
                "billType" => "PARTIAL",
                "creditAccountIdentifier" => "1234567800001"
            ]);
        } catch (\Throwable $e) {
            return $this->jsonify([
                'statusCode' => "500",
                'statusMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function validateInvoiceFromKcbVooma(Request $request): JsonResponse
    {
        $responsePayload = [];
        $statusCode = "1";
        $messageId = "";

        try {
            $incomingData = json_decode(json_encode($request->all()), true);
            $requestInvoiceNumber = $incomingData['requestPayload']['additionalData']['queryData']['businessKey'];
            $messageId = $incomingData['header']['messageID'];

            $invoice = DB::table('wa_internal_requisitions')
                ->select(
                    'wa_internal_requisitions.*',
                    DB::raw("(
                    select sum(total_cost_with_vat) from wa_internal_requisition_items 
                    where wa_internal_requisition_items.wa_internal_requisition_id = wa_internal_requisitions.id
                    ) as invoice_total")
                )
                ->where('requisition_no', $requestInvoiceNumber)
                ->orWhere('requisition_no', "INV-$requestInvoiceNumber")
                ->orderBy('wa_internal_requisitions.created_at', 'DESC')
                ->first();

            if ($invoice) {
                $statusCode = "0";
                $responsePayload = [
                    "transactionInfo" => [
                        "transactionId" => Str::uuid(),
                        "utilityName" => "QUERY BILL API",
                        "customerName" => $invoice->name,
                        "amount" => "$invoice->invoice_total",
                        "currency" => "KES",
                        "billType" => "PARTIAL",
                        "billDueDate" => ""
                    ]
                ];
            }
        } catch (\Throwable $th) {
            //
        }

        return $this->jsonify([
            "header" => [
                "messageID" => "$messageId",
                "originatorConversationID" => Str::uuid(),
                "statusCode" => $statusCode,
                "statusMessage" => "Processed Successfully"
            ],
            "responsePayload" => $responsePayload
        ]);
    }
}
