<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaAssetMaintenanceTask extends Model
{
    protected $guarded = [];
    protected $table = 'wa_asset_maintenance_task';

    // public function responsible()
    // {
    //     return $this->belongsTo(User::class,'responsible_id');
    // }
    // public function manager()
    // {
    //     return $this->belongsTo(User::class,'manager_id');
    // }
    // public function category()
    // {
    //     return $this->belongsTo(WaAssetCategory::class,'wa_asset_category_id');
    // }
    public static function getDataModel($limit , $start , $search, $orderby, $order)
    {
        $order = $order ? $order : 'DESC';
        $orderby = $orderby ? $orderby : 'wa_asset_maintenance_task.id';
        $query = WaAssetMaintenanceTask::select([
            'wa_asset_maintenance_task.id',
            'wa_asset_maintenance_task.task_description',
            'category.asset_description_short',
            'category.serial_number',
            'category.depreciation_rate',
            'responsible.name as responsible_name',
            'manager.name as manager_name',
        ])
        ->join('users as responsible',function($join){
            $join->on('responsible.id','=','wa_asset_maintenance_task.responsible_id');
        })
        ->join('users as manager',function($joisn){
            $joisn->on('manager.id','=','wa_asset_maintenance_task.manager_id');
        })
        ->join('wa_assets as category',function($joins){
            $joins->on('category.id','=','wa_asset_maintenance_task.wa_asset_category_id');
        });
        if($search)
        {
            $query = $query->where(function($q) use ($search){
                $q->orWhere('wa_asset_maintenance_task.task_description','LIKE','%'.$search.'%');
                $q->orWhere('category.asset_description_short','LIKE','%'.$search.'%');
                $q->orWhere('responsible.name','LIKE','%'.$search.'%');
                $q->orWhere('manager.name','LIKE','%'.$search.'%');
            });
        }
        $count = $query->count('wa_asset_maintenance_task.id');
        $query = $query->orderBy($orderby,$order)->limit($limit)->offset($start)->get();
        return ['count'=>$count,'response'=>$query];
    }
}
