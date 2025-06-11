<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Sacco extends BaseModel{
    protected $table = 'wa_sacco';
    public $timestamps = false;

    protected $guarded = [];

    protected function recurring(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                if ($value == 'On') {
                    return true;
                } else if ($value == 'Off') {
                    return false;
                }
            },
            set: fn (bool $value) => $value ? 'On' : 'Off',
        );
    }

}


