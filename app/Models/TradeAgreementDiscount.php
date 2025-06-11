<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeAgreementDiscount extends Model
{
    use HasFactory;

    public static function typeList(){
        return[
            'Base Discount'                     =>['stage'=>'LPO Processing', 'name'=>'Base Discount'],
            'Invoice Discount'                  =>['stage'=>'LPO Processing', 'name'=>'Invoice Discount'],
            'Purchase Quantity Offer'           =>['stage'=>'LPO Processing', 'name'=>'Purchase Quantity Offer'],
            'Distribution Discount'             =>['stage'=>'LPO Processing', 'name'=>'Distribution Discount on Invoice'],
            'Distribution Discount on Delivery' =>['stage'=>'LPO Processing', 'name'=>'Distribution Discount on Delivery'],
            'Bank Guarantee Discount'           =>['stage'=>'LPO Processing', 'name'=>'Bank Guarantee Discount'],
            'Transport rebate per unit'         =>['stage'=>'LPO Processing', 'name'=>'Transport Rebate Per Unit'],
            'Transport rebate percentage'       =>['stage'=>'LPO Processing', 'name'=>'Transport Rebate % of Invoice'],
            'Transport rebate per tonnage'      =>['stage'=>'LPO Processing', 'name'=>'Transport Rebate Per Tonnage'],
            'Payment Discount'                  =>['stage'=>'Payment Voucher', 'name'=>'Payment Discount'],
            'End month Discount'                =>['stage'=>'END Month', 'name'=>'End month Discount'],
            'Quarterly Discount'                =>['stage'=>'Quarterly', 'name'=>'Quarterly Discount'],
            'Target discount on quantity'       =>['stage'=>'END Month', 'name'=>'Target Discount on Quantity'],
            'Target discount on value'          =>['stage'=>'END Month', 'name'=>'Target Discount on Value'],
            'Target discount on total value'    =>['stage'=>'END Month', 'name'=>'Target Discount on Total Value'],
            'No Goods Return Discount'          =>['stage'=>'LPO Processing', 'name'=>'No Goods Return Discount'],
            'Performance Discount'              =>['stage'=>'LPO Processing', 'name'=>'Performance Discount'],
        ];
    }
}
