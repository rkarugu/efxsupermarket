<?php

namespace App\Model;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaRouteCustomer extends BaseModel
{
    use SoftDeletes;

    protected $table = 'wa_route_customers';

    protected $guarded = [];

    protected $appends = [
        'has_valid_location'
    ];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function parent()
    {
        return $this->belongsTo(WaCustomer::class, 'route_id', 'route_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    public function report(): HasMany
    {
        return $this->hasMany(ReportShop::class);
    }

    public function center()
    {
        return $this->belongsTo(DeliveryCentres::class, 'delivery_centres_id');
    }

    public function debtor_trans()
    {
        return $this->hasMany(WaDebtorTran::class);
    }

    public function getDisplayStatus(): string
    {
        return ucfirst($this->status);
    }

    public function hasValidLocation(): Attribute
    {
        return Attribute::make(get: fn () => $this->lat && $this->lng && ($this->lat != 0) && ($this->lng != 0));
    }

    public function TimeServed(): ?string
    {
        $currentUpdatedAt = $this->updated_at;
        $dateStart = Carbon::parse($currentUpdatedAt)->startOfDay();
        $dateEnd = Carbon::parse($currentUpdatedAt)->endOfDay();

        $nextCustomer = self::where('updated_at', '>', $currentUpdatedAt)
            ->whereBetween('updated_at', [$dateStart, $dateEnd])
            ->orderBy('updated_at', 'asc')
            ->first();

        if (!$nextCustomer) {
//            $nextCustomer = self::where('updated_at', '>', $currentUpdatedAt)
//                ->whereBetween('updated_at', [$dateStart, $dateEnd])
//                ->orderBy('updated_at', 'asc')
//                ->first();
            return 0;
        }

        $timeDifferenceInMinutes = $currentUpdatedAt->diffInMinutes($nextCustomer->updated_at);
        $hours = floor($timeDifferenceInMinutes / 60);
        $minutes = $timeDifferenceInMinutes % 60;

        $timeDifferenceString = '';
        if ($hours > 0) {
            $timeDifferenceString .= $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            if ($hours > 0) {
                $timeDifferenceString .= ' and ';
            }
            $timeDifferenceString .= $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return $timeDifferenceString ? $timeDifferenceString : '0 minutes';
    }

    public function internalRequisitions(): HasMany
    {
        return $this->hasMany(WaInternalRequisition::class,'customer_id');
    }

    public function lastOrder(): HasOne
    {
       return $this->hasOne(WaInternalRequisition::class)->latest();
    }



}