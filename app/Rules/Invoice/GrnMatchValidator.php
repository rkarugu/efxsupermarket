<?php

namespace App\Rules\Invoice;

use App\Model\WaGrn;
use App\Model\WaPurchaseOrder;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class GrnMatchValidator implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $grn = WaGrn::select([
            DB::raw('SUM((invoice_info->"$.order_price" * invoice_info->"$.qty" - IFNULL(invoice_info->"$.total_discount", 0)) * invoice_info->"$.vat_rate" / (100 + invoice_info->"$.vat_rate")) AS vat_amount'),
            DB::raw('SUM(invoice_info->"$.order_price" * invoice_info->"$.qty"- IFNULL(invoice_info->"$.total_discount", 0)) AS total_amount'),
        ])
            ->where('grn_number', $value)
            ->first();

        $order = WaPurchaseOrder::with(['getRelatedItem'])
            ->where('id', request()->id)
            ->first();

        $total_cost_with_vat = 0;
        $vat_amount = 0;
        foreach ($order->getRelatedItem as $key => $value) {
            if (!isset(request()->price[$value->id])) {
                continue;
            }

            $total = request()->total[$value->id];
            $vat = $total - (($total * 100) / (isset(request()->vat_rate[$value->id]) ? request()->vat_rate[$value->id] + 100 : $value->vat_rate + 100));
            $vat_amount += $vat;
            $total_cost_with_vat += $total;
        }

        if(round($grn->vat_amount,2) != round($vat_amount,2)){
            $fail("The GRN VAT amount does not match Invoice VAT");
        }

        if(round($grn->total_amount,2) != round($total_cost_with_vat,2)){
            $fail("The GRN Total Amount does not match Invoice Total Amount");
        }
    }
}
