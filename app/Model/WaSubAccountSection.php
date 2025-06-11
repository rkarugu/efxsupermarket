<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Input;

class WaSubAccountSection extends Model
{
    public $table = "wa_sub_account_sections";
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => [
            'source' => 'section_name',
            'onUpdate' => true
        ]];
    }

    public function getAccountSection()
    {
        return $this->belongsTo('App\Model\WaAccountSection', 'wa_account_section_id');
    }

    public function getParentAccountGroup()
    {
        return $this->belongsTo('App\Model\WaAccountGroup', 'wa_account_group_id');
    }

    public function accounts()
    {
        return $this->hasMany('App\Model\WaChartsOfAccount', 'wa_account_sub_section_id');
    }

}
