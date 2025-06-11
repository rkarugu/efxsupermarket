<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PettyCashTransaction extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expunged' => 'boolean'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('expunged', function (Builder $builder) {
            $builder->whereNot('expunged', true);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
    
    public function child()
    {
        return $this->hasOne(self::class, 'parent_id', 'id')->latest();
    }

    public function travelExpenseTransaction()
    {
        return $this->belongsTo(TravelExpenseTransaction::class, 'document_no', 'document_no');
    }
}
