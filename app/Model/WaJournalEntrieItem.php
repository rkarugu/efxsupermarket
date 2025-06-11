<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaJournalEntrieItem extends Model
{
    
     public function getGlDetail() 
    {
        return $this->belongsTo('App\Model\WaChartsOfAccount', 'gl_account_id');
    }

    public function getBankDetail()
    {
        return $this->belongsTo('App\Model\WaBankAccount', 'gl_account_id', 'bank_account_gl_code_id');
    }

    public function getCustDetail()
    {
        return $this->belongsTo('App\Model\WaCustomer', 'gl_account_id', 'id');
    }
    public function getSuppDetail()
    {
        return $this->belongsTo('App\Model\WaSupplier', 'gl_account_id', 'id');
    }
}


