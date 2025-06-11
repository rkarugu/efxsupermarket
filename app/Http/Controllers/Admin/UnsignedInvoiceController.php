<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaEsdDetails;
use App\Model\WaInternalRequisition;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class UnsignedInvoiceController extends Controller
{
   static public function resignAll(Request $request): RedirectResponse
    {
        try {
            $signedInvoices = [];
            $signedInvoiceCount = 0;
            $failedInvoiceCount = 0;
            $totalInvoices = 0;

            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now();
            if ($request->start_date) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
            }

            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
            }

            $query = WaEsdDetails::select('wa_esd_details.*')->where('wa_esd_details.status', false)->whereBetween('wa_esd_details.created_at', [$startDate, $endDate])->join('wa_internal_requisitions', function (JoinClause $join) use ($request) {
                $joinQuery = $join->on('wa_internal_requisitions.requisition_no', '=', 'wa_esd_details.invoice_number');
                if ($request->route) {
                    $joinQuery = $joinQuery->where('wa_internal_requisitions.route_id', $request->route);
                }
            });

            $totalInvoices = $query->clone()->count();
            $unsignedInvoices = $query->clone()->get();
            foreach ($unsignedInvoices as $unsignedInvoice) {
                if (!in_array($unsignedInvoice->invoice_number, $signedInvoices)) {
                    $invoice = WaInternalRequisition::with('getRelatedItem')->where('requisition_no', $unsignedInvoice->invoice_number)->first();

                    $settings = getAllSettings();
                    $clientPin = $settings['PIN_NO'];
                    $esdUrl = $settings['ESD_URL'];
                    $apiUrl = "$esdUrl/api/sign?invoice+1";

                    $payload = [
                        "invoice_date" => Carbon::parse($invoice->created_at)->format('d_m_Y'),
                        "invoice_number" => $invoice->requisition_no,
                        "invoice_pin" => $clientPin,
                        "customer_pin" => "",
                        "customer_exid" => "",
                        "grand_total" => 0,
                        "net_subtotal" => 0,
                        "tax_total" => 0,
                        "net_discount_total" => "0",
                        "sel_currency" => "KSH",
                        "rel_doc_number" => "",
                        "items_list" => []
                    ];

                    $grandTotal = 0;
                    $vatAmount = 0;
                    $taxManagers = DB::table('tax_managers')->select('id', 'title', 'tax_value')->get();
                    foreach ($invoice->getRelatedItem as $item) {
                        $itemTotal = $item->selling_price * $item->quantity;
                        $grandTotal += $itemTotal;

                        $inventoryItem = DB::table('wa_inventory_items')->find($item->wa_inventory_item_id);
                        $taxManager = $taxManagers->where('id', $inventoryItem->tax_manager_id)->first();
                        if ($taxManager) {
                            $vatRate = (float)$taxManager->tax_value;
                            $vatAmount += ($vatRate / (100 + $vatRate)) * $itemTotal;
                        }

                        $itemTotal = manageAmountFormat($itemTotal);
                        $item->selling_price = manageAmountFormat($item->selling_price);
                        $line = "$inventoryItem->slug $item->quantity $item->selling_price $itemTotal";
                        if ($inventoryItem->hs_code) {
                            $line = "$inventoryItem->hs_code " . $line;
                        }

                        $payload['items_list'][] = $line;
                    }

                    $payload['tax_total'] = number_format($vatAmount, 2);
                    $payload['grand_total'] = number_format($grandTotal, 2);
                    $payload['net_subtotal'] = number_format($grandTotal - $vatAmount, 2);

                    $response = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Basic ZxZoaZMUQbUJDljA7kTExQ==',
                    ])->post($apiUrl, $payload);

                    $responseData = json_decode($response->body(), true);

                    if ($response->ok()) {
                        $unsignedInvoice->cu_serial_number = $responseData['cu_serial_number'];
                        $unsignedInvoice->cu_invoice_number = $responseData['cu_invoice_number'];
                        $unsignedInvoice->verify_url = $responseData['verify_url'] ?? null;
                        $unsignedInvoice->description = $responseData['description'] ?? null;
                        $unsignedInvoice->status = 1;
                        $unsignedInvoice->save();

                        $signedInvoices[] = $unsignedInvoice->invoice_number;
                        $signedInvoiceCount++;
                    } else {
                        $failedInvoiceCount++;
                    }
                }
            }

            Session::flash('success', "Successfully signed $signedInvoiceCount out of $totalInvoices unsigned invoices");
            return redirect()->route('sales-and-receivables-reports.unassigned_invoices');
        } catch (\Throwable $e) {
            Session::flash('danger', $e->getMessage());
            return redirect()->route('sales-and-receivables-reports.unassigned_invoices');
        }
    }
}
