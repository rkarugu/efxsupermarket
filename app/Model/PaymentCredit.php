<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class PaymentCredit extends Model {
    
    public static function savePaymentCredit($params){
        $entity = new PaymentCredit();
        $entity->order_id = $params['order_id'];
        $entity->order_item_id = $params['order_item_id'];
        $entity->period = $params['period'];
        $entity->gl_code_id = $params['gl_code_id'];
        $entity->narration = $params['narration'];
        $entity->transaction_type = $params['transaction_type'];
        $entity->transaction_no = $params['transaction_no'];
        $entity->gross_amount = $params['gross_amount'];
        $entity->amount = $params['amount'];
        $entity->date = $params['date'];
        $entity->type = $params['type'];
        
        
        $entity->save();
    }
    
}
