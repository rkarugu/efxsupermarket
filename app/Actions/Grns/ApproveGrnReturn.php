<?php

namespace App\Actions\Grns;

use App\Model\TaxManager;
use App\Model\User;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaStockMove;
use App\Models\WaReturnDemand;
use App\Models\WaReturnDemandItem;
use App\ReturnedGrn;

class ApproveGrnReturn
{
    public function approve(array $lineItems, User $user)
    {
        $processedLineItems = [];
        $totalCost = 0;
        $vatAmount = [];

        $series_module = WaNumerSeriesCode::where('module', 'RETURN')->first();

        foreach ($lineItems as $lineItem) {
            $return = ReturnedGrn::with(['grn' => function ($query) {
                $query->with('purchaseOrder');
            }])->find($lineItem['id']);

            $return->update([
                'returned_quantity' => $lineItem['quantity'],
                'reason' => $lineItem['reason'],
                'approved' => true,
                'approved_by' => $user->id,
                'approved_date' => now(),
            ]);

            $return->load('user', 'inventoryItem', 'supplier')->fresh();
            $accountno = $return->inventoryItem->getInventoryCategoryDetail->getStockGlDetail->account_code;
            $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $purchaseOrder = $return->grn->purchaseOrder;
            $returnNumber = $return->return_number;

            $invoiceInfo = json_decode($return->grn->invoice_info);

            $stockMove = new WaStockMove();
            $stockMove->user_id = $return->user->id;
            $stockMove->restaurant_id = $purchaseOrder->restaurant_id;
            $stockMove->wa_location_and_store_id = $purchaseOrder->wa_location_and_store_id;
            $stockMove->wa_inventory_item_id = $return->inventoryItem->id;
            $stockMove->standard_cost = $return->inventoryItem->standard_cost;
            $stockMove->qauntity = $return->returned_quantity * -1;
            $stockMove->new_qoh = ($return->inventoryItem->getAllFromStockMoves->where('wa_location_and_store_id', $purchaseOrder->wa_location_and_store_id)->sum('qauntity') ?? 0) - $return->returned_quantity;
            $stockMove->stock_id_code = $return->inventoryItem->stock_id_code;
            $stockMove->document_no = $return->return_number;
            $stockMove->refrence = "Return from GRN $return->grn_number";
            $stockMove->price = $invoiceInfo->order_price;
            $stockMove->total_cost = $itemCost = (float)$invoiceInfo->order_price * (float)$return->returned_quantity;
            $stockMove->save();

            // Credit Purchases
            $cr =  new WaGlTran();
            $cr->grn_type_number = $series_module->type_number;
            $cr->grn_last_used_number = $series_module->last_number_used;
            $cr->transaction_type = $series_module->description;
            $cr->transaction_no = $return->return_number;
            $cr->reference = $return->inventoryItem->stock_id_code;
            $cr->trans_date = $dateTime = now();
            $cr->restaurant_id = $purchaseOrder->restaurant_id;
            $cr->tb_reporting_branch = $purchaseOrder->restaurant_id;
            $cr->wa_purchase_order_id = $purchaseOrder->id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->supplier_account_number = $purchaseOrder->supplier->supplier_code;
            $cr->account = $accountno;
            $cr->amount = ($lineItem['vat'] > 0 ? getExclusiveAmount($itemCost) :  $itemCost) * -1;
            $cr->narrative = "Return from GRN $return->grn_number";
            $cr->save();

            $totalCost += $stockMove->total_cost;
            array_push($processedLineItems, $return);
            array_push($vatAmount, (float)$lineItem['vat']);
        }

        // Credit Tax
        $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
        if ($taxVat && $taxVat->getOutputGlAccount && array_sum($vatAmount) > 0) {
            $vat = new WaGlTran();
            $vat->grn_type_number = $series_module->type_number;
            $vat->transaction_type = $series_module->description;
            $vat->transaction_no = $return->return_number;
            $vat->reference = $return->grn->grn_number;
            $vat->grn_last_used_number = $series_module->last_number_used;
            $vat->trans_date = $dateTime;
            $vat->restaurant_id = $purchaseOrder->restaurant_id;
            $vat->tb_reporting_branch = $purchaseOrder->restaurant_id;
            $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $vat->supplier_account_number = $purchaseOrder->supplier->supplier_code;
            $vat->account = $taxVat->getOutputGlAccount->account_code;
            $vat->amount = array_sum($vatAmount) * -1;
            $vat->narrative = "Return from GRN $return->grn_number";
            $vat->wa_purchase_order_id = $purchaseOrder->id;
            $vat->save();
        }

        // Debit GIT
        $dr = new WaGlTran();
        $dr->grn_type_number = $series_module->type_number;
        $dr->transaction_type = $series_module->description;
        $dr->transaction_no = $return->return_number;
        $dr->reference = $return->grn->grn_number;
        $dr->grn_last_used_number = $series_module->last_number_used;
        $dr->trans_date = $dateTime;
        $dr->restaurant_id = $purchaseOrder->restaurant_id;
        $dr->tb_reporting_branch = $purchaseOrder->restaurant_id;
        $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
        $dr->supplier_account_number = $purchaseOrder->supplier->supplier_code;
        $dr->account =  $purchaseOrder->getBranch->getAssociateCompany->good_receive->account_code;
        $dr->amount =  $totalCost;
        $dr->narrative = "Return from GRN $return->grn_number";;
        $dr->wa_purchase_order_id = $purchaseOrder->id;
        $dr->save();

        // Create demand
        $demandCode = getCodeWithNumberSeries('DELTA');

        $delta = new WaReturnDemand();
        $delta->demand_no = $demandCode;
        $delta->created_by = $return->user->id;
        $delta->wa_supplier_id = $return->wa_supplier_id;
        $delta->return_document_no = $return->return_number;
        $delta->demand_amount = $totalCost;
        $delta->edited_demand_amount = $totalCost;
        $delta->vat_amount = array_sum($vatAmount);
        $delta->save();

        updateUniqueNumberSeries('DELTA', $demandCode);

        // Create demand items
        foreach ($processedLineItems as $lineItem) {
            $return = ReturnedGrn::with('grn')->find($lineItem['id']);

            $invoiceInfo = json_decode($return->grn->invoice_info);

            WaReturnDemandItem::create([
                'wa_inventory_item_id' => $return->inventoryItem->id,
                'wa_return_demand_id' => $delta->id,
                'quantity' => $return->returned_quantity,
                'cost' => $invoiceInfo->order_price,
                'demand_cost' => (float)$invoiceInfo->order_price * (float)$return->returned_quantity,
            ]);
        }

        if ($purchaseOrder->getSupplier->locked_trade) {
            $postData = [
                'lpo_number' => $purchaseOrder->purchase_no,
                'order_from' => env('SUPPLIER_SOURCE'),
                'return_no' => $returnNumber
            ];

            foreach ($processedLineItems as $return) {
                $postData['order_item_id'][] = $return->inventoryItem->id;
                $postData['item_code'][] = $return->inventoryItem->stock_id_code;
                $postData['return_quantity'][] =  $return->returned_quantity;
                $postData['return_reason'][] =  $return->reason;
                $postData['return_doc'][] = '';
            }

            $api = new \App\Services\ApiService(env('SUPPLIER_PORTAL_URI'));
            $api->postRequest('/api/lpo/update-order-returns', $postData);
        }
    }
}
