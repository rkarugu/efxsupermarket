<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class WaJournalEntry extends Model
{
    
    use Sluggable;
    public function sluggable(): array {
        return ['slug'=>[
            'source'=>'journal_entry_no',
            'onUpdate'=>true
        ]];
    }

      public function getRelatedItem() {
         return $this->hasMany('App\Model\WaJournalEntrieItem', 'wa_journal_entry_id');
    }

    

    

     
}


