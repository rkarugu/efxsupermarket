<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'subject', 'body'];

    public static function templateList(){
        return [
            // place_lpo used in purchase order controller
            'place_lpo'=>(Object)[
                'name'=>'Email on Place LPO',
                'subject'=>'PURCHASE ORDER ${purchase_no} FOR ${branch}',
                'template'=>view('email_templates.place_lpo'),
                'subject_variables'=>'${purchase_no}, ${branch}',
                'body_variables'=>'${name}, ${location}'
            ],
            'lpo_for_approval'=>(Object)[
                'name'=>'Email on send back LPO for Approval',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.lpo_for_approval'),
                'subject_variables'=>'',
                'body_variables'=>''
            ],
            'approval_of_change'=>(Object)[
                'name'=>'Email on Approval of Change',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.approval_of_change'),
                'subject_variables'=>'',
                'body_variables'=>''
            ],
            'acceptance_of_lpo'=>(Object)[
                'name'=>'Email on Acceptance of LPO',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.acceptance_of_lpo'),
                'subject_variables'=>'',
                'body_variables'=>''
            ],
            'goods_release'=>(Object)[
                'name'=>'Email on Goods Release',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.goods_release'),
                'subject_variables'=>'',
                'body_variables'=>''
            ],
            'delivery_slot_booking'=>(Object)[
                'name'=>'Email on Delivery Slot Booking',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.delivery_slot_booking'),
                'subject_variables'=>'',
                'body_variables'=>''
            ],
            'lpo_split'=>(Object)[
                'name'=>'Email on LPO Split',
                'subject'=>'LPO Number: ${lpo_number}',
                'template'=>view('email_templates.lpo_split'),
                'subject_variables'=>'',
                'body_variables'=>''
            ]
        ];
    }
}
