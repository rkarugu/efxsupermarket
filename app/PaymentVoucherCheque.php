<?php

namespace App;

use App\Models\GlReconStatement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PaymentVoucherCheque extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function glMatched(): MorphOne
    {
        return $this->morphOne(GlReconStatement::class, 'matched');
    }
}
