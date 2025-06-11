<?php

namespace App\Model;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pension extends BaseModel{
    protected $table = 'wa_pension';
    public $timestamps = false;

    protected $guarded = [];

    protected function useRate(): Attribute
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

    protected function taxable(): Attribute
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


