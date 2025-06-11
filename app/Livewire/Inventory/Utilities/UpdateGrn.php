<?php

namespace App\Livewire\Inventory\Utilities;

use App\Model\TaxManager;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaGrn;
use App\Model\WaNumerSeriesCode;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaStockMove;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Validate;
use Livewire\Component;

class UpdateGrn extends Component
{
    #[Validate('required|exists:wa_grns,grn_number')]
    public $grnNumber;

    public $purchaseNo;

    public $cuInvoiceNumber;

    public $supplierInvoiceNumber;

    public Collection $grnItems;

    public $current;

    public $adjusted;

    public $supplier;

    public function mount()
    {
        $this->grnNumber = request()->grn;

        if ($this->grnNumber) {
            $this->loadGrn();
        }
    }

    public function loadGrn()
    {
        $items = WaGrn::where('grn_number', $this->grnNumber)->get();

        if (!$items->count()) {
            $this->grnNumber = '';

            return;
        }

        $this->cuInvoiceNumber = $items->first()->cu_invoice_number;
        $this->supplierInvoiceNumber = $items->first()->supplier_invoice_no;
        $this->purchaseNo =  $items->first()->purchaseOrder->purchase_no;
        $supplier = $items->first()->purchaseOrder->supplier;
        $this->supplier = $supplier->name . "($supplier->supplier_code)";

        $this->renderGrnItems($items);

        $this->calculateTotals();
    }

    public function updatedGrnItems($value, $name)
    {
        $index = explode('.', $name)[0];

        $item = $this->grnItems[$index];

        $orderItem = WaPurchaseOrderItem::find($item->purchase_order_item_id);

        $this->grnItems[$index]->adjusted = (object) [
            'vat_amount' => getVatAmount($item->price, $item->vat_rate) * $item->qty,
            'exclusive_amount' => getExclusiveAmount($item->price, $item->vat_rate) * $item->qty,
            'total_discount' => $this->getTotalDiscount($item->price, $item->qty, $orderItem),
            'total_amount' => $item->price * $item->qty
        ];

        $this->calculateTotals();
    }


    public function save()
    {
        $series_module = WaNumerSeriesCode::where('module', 'GRN')->first();
        $WaAccountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
        $grn_number = $this->grnNumber;
        $purchaseOrder = WaPurchaseOrder::where('purchase_no', $this->purchaseNo)->first();

        DB::beginTransaction();

        try {
            WaGlTran::where('transaction_no', $this->grnNumber)->delete();

            foreach ($this->grnItems as $item) {
                $grn = WaGrn::find($item->id);

                $grn->update([
                    'wa_purchase_order_item_id' => WaPurchaseOrderItem::where([
                        'wa_purchase_order_id' => $purchaseOrder->id,
                        'item_no' => $grn->item_code
                    ])->first()->id,
                    'supplier_invoice_no' => $this->supplierInvoiceNumber,
                    'cu_invoice_number' => $this->cuInvoiceNumber,

                    'invoice_info->qty' => $item->qty,
                    'invoice_info->order_price' => $item->price,
                    'invoice_info->vat_rate' => $item->vat_rate,
                    'invoice_info->total_discount' => $item->adjusted->total_discount,
                ]);

                // $stock = WaStockMove::where([
                //     'document_no' => $this->grnNumber,
                //     'stock_id_code' => $item->item_code,
                // ])->first();

                // if ($stock->qauntity !=  $item->qty) {
                //     $stock->update([
                //         'qauntity' => $item->qty
                //     ]);
                // }

                $accountno = $grn->inventoryItem->getInventoryCategoryDetail->getStockGlDetail->account_code;
                $dr =  new WaGlTran();
                $dr->grn_type_number = $series_module->type_number;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $grn_number;
                $dr->reference = $item->item_code;
                $dr->trans_date = $grn->created_at;
                $dr->restaurant_id = $purchaseOrder->restaurant_id;
                $dr->tb_reporting_branch = $purchaseOrder->restaurant_id;
                $dr->wa_purchase_order_id = $purchaseOrder->id;
                $dr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $dr->supplier_account_number = $purchaseOrder->supplier->supplier_code;
                $dr->account = $accountno;
                $dr->amount = $item->adjusted->exclusive_amount;
                $dr->narrative = $purchaseOrder->purchase_no . '/' . ($purchaseOrder->supplier->supplier_code) . '/' . $grn->inventoryItem->item_code . '/' . $grn->inventoryItem->title . '/' . $item->qty . '@' . $item->price;
                $dr->save();
            }

            $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
            if ($taxVat && $taxVat->getOutputGlAccount && $this->adjusted->total_vat_amount > 0) {
                $vat = new WaGlTran();
                $vat->grn_type_number = $series_module->type_number;
                $vat->transaction_type = $series_module->description;
                $vat->transaction_no = $grn_number;
                $vat->grn_last_used_number = $series_module->last_number_used;
                $vat->trans_date = $grn->created_at;
                $vat->restaurant_id =  $purchaseOrder->restaurant_id;
                $vat->tb_reporting_branch =  $purchaseOrder->restaurant_id;
                $vat->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
                $vat->supplier_account_number = $purchaseOrder->supplier->supplier_code;
                $vat->account = $taxVat->getOutputGlAccount->account_code;
                $vat->amount = $this->adjusted->total_vat_amount;
                $vat->narrative = $purchaseOrder->purchase_no . '/' . ($purchaseOrder->supplier->supplier_code) . '/' . $this->grnNumber;
                $vat->wa_purchase_order_id = $purchaseOrder->id;
                $vat->save();
            }

            // cr entry start
            $cr = new WaGlTran();
            $cr->grn_type_number = $series_module->type_number;
            $cr->transaction_type = $series_module->description;
            $cr->transaction_no = $grn_number;
            $cr->grn_last_used_number = $series_module->last_number_used;
            $cr->trans_date = $grn->created_at;
            $cr->restaurant_id = $purchaseOrder->restaurant_id;
            $cr->tb_reporting_branch = $purchaseOrder->restaurant_id;
            $cr->period_number = $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null;
            $cr->supplier_account_number = $purchaseOrder->supplier->supplier_code;
            $cr->account =  $purchaseOrder->getBranch->getAssociateCompany->good_receive->account_code;
            $cr->amount = $this->adjusted->total_amount * -1;
            $cr->narrative = $purchaseOrder->purchase_no . '/' . ($purchaseOrder->supplier->supplier_code) . '/' . $this->grnNumber;
            $cr->wa_purchase_order_id = $purchaseOrder->id;
            $cr->save();

            DB::commit();

            Session::flash('success', 'GRN Updated successfully');

            return redirect()->route('utilities.grn-update');
        } catch (\Throwable $th) {
            DB::rollBack();

            Session::flash('error', $th->getMessage());

            return redirect()->route('utilities.grn-update', ['grn' => $this->grnNumber]);
        }
    }

    public function render()
    {
        return view('livewire.inventory.utilities.update-grn');
    }

    private function renderGrnItems($grnItems)
    {
        $this->grnItems = $grnItems->map(function ($grnItem) {
            // Since LPO items can change, we use item code and LPO ID
            $orderItem =  WaPurchaseOrderItem::where([
                'wa_purchase_order_id' => $grnItem->wa_purchase_order_id,
                'item_no' => $grnItem->item_code,
            ])->first();

            if (is_null($orderItem)) {
                return false;
            }

            return (object) [
                "id" => $grnItem->id,
                "item_code" => $grnItem->item_code,
                "purchase_order_item_id" => $orderItem->id,
                "description" => $grnItem->item_description,
                "qty" => $grnItem->item_quantity,
                "price" => $grnItem->item_price,
                "vat_rate" => $grnItem->item_vat_rate,
                "current" => (object)[
                    "total_discount" => $grnItem->item_discount,
                    "vat_amount" => $grnItem->item_vat,
                    "exclusive_amount" => $grnItem->item_exclusive,
                    "total_amount" => $grnItem->item_total,
                ],
                "adjusted" => (object) [
                    "total_discount" => $discount = $this->getTotalDiscount($grnItem->item_price, $grnItem->item_quantity, $orderItem),
                    "vat_amount" => getVatAmount($grnItem->item_price * $grnItem->item_quantity - $discount, $grnItem->item_vat_rate),
                    "exclusive_amount" => getExclusiveAmount($grnItem->item_price * $grnItem->item_quantity - $discount, $grnItem->item_vat_rate),
                    "total_amount" => $grnItem->item_price * $grnItem->item_quantity - $discount,
                ]
            ];
        });
    }



    public function calculateTotals()
    {
        $this->current = (object)[
            "total_vat_amount" => $this->grnItems->sum('current.vat_amount'),
            "total_exclusive_amount" => $this->grnItems->sum('current.exclusive_amount'),
            "total_discount" => $this->grnItems->sum('current.total_discount'),
            "total_amount" => $this->grnItems->sum('current.total_amount')
        ];

        $this->adjusted = (object) [
            "total_vat_amount" => $this->grnItems->sum('adjusted.vat_amount'),
            "total_exclusive_amount" => $this->grnItems->sum('adjusted.exclusive_amount'),
            "total_discount" => $this->grnItems->sum('adjusted.total_discount'),
            "total_amount" => $this->grnItems->sum('adjusted.total_amount')
        ];
    }

    private function getTotalDiscount($price, $qty, $orderItem)
    {
        $baseDiscount = 0;
        $invoice_discount = 0;
        $transport_rebate = 0;
        $distribution_discount = 0;

        $settings = json_decode($orderItem->discount_settings);
        if (!$settings) {
            return 0;
        }

        if (isset($settings->base_discount_type)) {
            $baseDiscount = ($settings->base_discount_type == 'Value' ? $orderItem->discount_percentage * $qty : ($price * $orderItem->discount_percentage / 100) * $qty);
        }

        $invoiceAmount = $price * $qty -  $baseDiscount;
        $inv_percentage = (float) (isset($settings->invoice_percentage) ? $settings->invoice_percentage : 0);
        $invoice_discount += ($invoiceAmount * $inv_percentage) / 100;
        $transport_rebate_per_unit = (float) isset($settings->transport_rebate_per_unit) ? $settings->transport_rebate_per_unit : 0;
        $transport_rebate_percentage = (float) isset($settings->transport_rebate_percentage) ? $settings->transport_rebate_percentage : 0;
        $transport_rebate_per_tonnage = (float) isset($settings->transport_rebate_per_tonnage) ? $settings->transport_rebate_per_tonnage : 0;
        $distribution_discount = (float) isset($settings->distribution_discount) ? $settings->distribution_discount * $qty : 0;
        if ($transport_rebate_per_unit > 0) {
            $transport_rebate += $transport_rebate_per_unit * $qty;
        } elseif ($transport_rebate_percentage > 0) {
            $transport_rebate += ($invoiceAmount * $transport_rebate_percentage) / 100;
        } elseif ($transport_rebate_per_tonnage > 0) {
            $transport_rebate += $transport_rebate_per_tonnage * $orderItem->measure;
        }

        return $baseDiscount + $invoice_discount + $transport_rebate + $distribution_discount;
    }
}
