<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaPurchaseOrderPermission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wa_purchase_order_id',
        'user_id',
        'approve_level',
        'status',
        'note'
    ];
    
    public function getPurchaseOrder() {
        return $this->belongsTo('App\Model\WaPurchaseOrder', 'wa_purchase_order_id');
    }

    public function getExternalAuthorizerProfile() {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
}


