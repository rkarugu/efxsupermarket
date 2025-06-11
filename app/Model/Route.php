<?php

namespace App\Model;

use App\DeliverySchedule;
use App\RoutePlan;
use App\RoutePolyline;
use App\RouteSection;
use App\Model\User as Employee;
use App\Models\BaseModel;
use App\Models\RouteRepresentatives;
use App\Models\RouteSupervisors;
use App\SalesmanShift;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Input;

class Route extends BaseModel
{
    protected $guarded = [];

    protected $appends = [
        'has_valid_location',
    ];

    use Sluggable;
    use SluggableScopeHelpers;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'route_name'
            ]
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function centers(): HasMany
    {
        return $this->hasMany(DeliveryCentres::class, 'route_id', 'id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(RouteSection::class, 'route_id', 'id');
    }


    public function waRouteCustomer(): HasMany
    {

        return $this->hasMany(WaRouteCustomer::class);
    }

    public function reports(): HasMany
    {

        return $this->hasMany(RouteReport::class);
    }

    public function internalRequisitions(): HasMany
    {
        return $this->hasMany(WaInternalRequisition::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function salesman()
    {
        return $this->users->filter(function (Employee $user) {
            return $user->role_id == 4;
        })->first();
    }

    public function salesmanUser()
    {
        return $this->belongsToMany(User::class, 'route_user', 'route_id', 'user_id')
            ->where('role_id', 4);
    }

    public function dispatcher()
    {
        return $this->users->filter(function (Employee $user) {
            return $user->userRole?->slug == 'chief-dispatcher';
        })->first();
    }

    public function routeManager(): Employee|null
    {
        return $this->users->filter(function (Employee $user) {
            return $user->userRole?->slug == 'route-manager';
        })->first();
    }

    public function store()
    {
        return WaLocationAndStore::where('route_id', $this->id)->first();
    }

    public function getAssignedCustomerAccount()
    {
        return WaCustomer::where('route_id', $this->id)->first();
    }

    public function hasValidLocation(): Attribute
    {
        return Attribute::make(get: fn() => $this->start_lat && $this->start_lng && ($this->start_lat != 0) && ($this->start_lng != 0));
    }

    public function polylines(): HasMany
    {
        return $this->hasMany(RoutePolyline::class);
    }

    public function waCustomer(): HasOne
    {
        return $this->hasOne(WaCustomer::class,'route_id');
    }

    public function shifts(): HasMany
    {
        return  $this->hasMany(SalesmanShift::class,'route_id');
    }

    public function activeDeliverySchedule()
    {
        return $this->hasOne(DeliverySchedule::class)
            ->where('status', 'in_progress');
    }

    public function representatives(): HasMany
    {
        return $this->hasMany(RouteRepresentatives::class, 'route_id');
    }

    public function currentRepresentative(): HasOne
    {
        return $this->hasOne(RouteRepresentatives::class, 'route_id')->latestOfMany();
    }

    public function supervisors(): HasMany
    {
        return $this->hasMany(RouteSupervisors::class, 'route_id');
    }

    public function currentSupervisor(): HasOne
    {
        return $this->hasOne(RouteSupervisors::class, 'route_id')->latestOfMany();
    }
    
}
