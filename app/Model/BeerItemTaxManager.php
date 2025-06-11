<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class BeerItemTaxManager extends Model
{
    protected $fillable = array('beer_delivery_item_id', 'tax_manager_id');

     public function getRelativeTaxdetail() {
        return $this->belongsTo('App\Model\TaxManager', 'tax_manager_id');
    }
    

    
}


