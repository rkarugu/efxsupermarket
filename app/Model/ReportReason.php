<?php


namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportReason extends Model
{
    //


    protected $fillable = [
        'name',
    ];

    public function shopReport():HasMany{
        return $this->hasMany(ReportShop::class);
    }

    public function routeReport() : HasMany {
        
        return $this->hasMany(RouteReport::class);
    }
  
}
