<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaPettyCashRequestItemFile extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function pettyCashRequestItem()
    {
        return $this->belongsTo(WaPettyCashRequestItem::class);
    }
}
