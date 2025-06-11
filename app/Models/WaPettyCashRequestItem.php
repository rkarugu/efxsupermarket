<?php

namespace App\Models;

use App\Model\User;
use App\Model\Route;
use App\Model\WaGrn;
use App\DeliverySchedule;
use App\Model\TaxManager;
use App\Model\WaSupplier;
use Illuminate\Database\Eloquent\Builder;
use App\Model\NWaInventoryLocationTransfer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPettyCashRequestItem extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expunged' => 'boolean',
        'expunged_at' => 'datetime',
    ];

    protected $appends = [
        'status'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('expunged', function (Builder $builder) {
            $builder->where('expunged', false);
        });
    }

    public function pettyCashRequest()
    {
        return $this->belongsTo(WaPettyCashRequest::class, 'wa_petty_cash_request_id');
    }

    public function taxManger()
    {
        return $this->belongsTo(TaxManager::class);
    }

    public function deliverySchedule()
    {
        return $this->belongsTo(DeliverySchedule::class);
    }

    public function pettyCashRequestItemFiles()
    {
        return $this->hasMany(WaPettyCashRequestItemFile::class, 'request_item_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(WaPettyCashRequestItemWithdrawal::class, 'request_item_id');
    }

    public function latestWithdrawal()
    {
        return $this->hasOne(WaPettyCashRequestItemWithdrawal::class, 'request_item_id', 'id')->latest();
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WaSupplier::class, 'supplier_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function grn()
    {
        return $this->belongsTo(WaGrn::class, 'grn_number', 'grn_number');
    }

    public function transfer()
    {
        return $this->belongsTo(NWaInventoryLocationTransfer::class);
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->latestWithdrawal?->call_back_status ?: 'failed',
        );
    }
}
