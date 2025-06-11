<?php


namespace App\Model;

use App\RoutePlanCentre;
use App\Models\RouteRepresentatives;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryCentres extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'has_valid_location'
    ];

    public function getLatAttribute($value): float
    {
        return (float)$value;
    }

    public function getLngAttribute($value): float
    {
        return (float)$value;
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }

    public function waCustomers()
    {
        return $this->hasMany(WaCustomer::class);
    }

    public function routePlanCenter()
    {
        return $this->hasMany(RoutePlanCentre::class);
    }

    public function waRouteCustomers()
    {
        return $this->hasMany(WaRouteCustomer::class, 'delivery_centres_id', 'id');
    }

    public function hasValidLocation(): Attribute
    {
        return Attribute::make(get: fn() => $this->lat && $this->lng && ($this->lat != 0) && ($this->lng != 0));
    }

    public function preferredCenterRadius(): Attribute
    {
        return Attribute::make(get: fn($value) => $value == 0 ? 1000 : (int)$value);
    }

    public function lat(): Attribute
    {
        return Attribute::make(get: fn($value) => (float)$value);
    }

    public function lng(): Attribute
    {
        return Attribute::make(get: fn($value) => (float)$value);
    }
}
