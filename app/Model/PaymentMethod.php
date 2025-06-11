<?php
namespace App\Model;
use App\PaymentProvider;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use Sluggable;

    protected $guarded = [];

    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'title',
            'onUpdate'=>true
        ]];
    }

     public function paymentGlAccount() {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'gl_account_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class, 'payment_provider_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return  $this->belongsTo(Restaurant::class, 'branch_id');
    }
}


