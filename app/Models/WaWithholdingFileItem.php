<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaWithholdingFileItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bankFile()
    {
        return $this->belongsTo(WaBankFile::class, 'wa_bank_file_id');
    }
}
