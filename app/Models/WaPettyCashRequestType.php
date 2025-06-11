<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Support\Str;
use App\Model\WaChartsOfAccount;
use App\Models\Scopes\CustomOrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPettyCashRequestType extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CustomOrderScope);

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'wa_user_petty_cash_request_types', 'type_id', 'user_id')
            ->select('id', 'name');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(WaChartsOfAccount::class, 'wa_charts_of_account_id');
    }
}
