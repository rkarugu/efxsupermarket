<?php

namespace App\Models;

use App\Model\User;
use App\Model\WaGlTran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlAccountUpdateHistory extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function glTrans(): BelongsTo
    {
        return $this->belongsTo(WaGlTran::class,'gl_trans_id');
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
