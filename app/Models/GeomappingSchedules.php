<?php

namespace App\Models;

use App\Model\Restaurant;
use App\Model\Route;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeomappingSchedules extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'branch', 'route_id', 'route_manager', 
    'route_manager_contact', 'bizwiz_rep', 'bizwiz_rep_contact', 
    'golden_africa_rep', 'golden_africa_rep_contact', 'status', 'completed_by', 'comment', 'HQ_approved_by',
'supervisor', 'supervisor_contact'];
    protected $table = 'geomapping_schedules';

    protected $casts =[
        'date'=>'datetime'
    ];

    public function route(){
        return $this->belongsTo(Route::class, 'route_id', 'id');
    }
    public function branchDetails(){
        return $this->belongsTo(Restaurant::class, 'branch', 'id');
    }
}
