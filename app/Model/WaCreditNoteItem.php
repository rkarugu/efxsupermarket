<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WaCreditNoteItem extends Model
{
    public function getUnitOfMeasure() {
        return $this->belongsTo('App\Model\WaUnitOfMeasure', 'unit_of_measure_id');
    }
}
