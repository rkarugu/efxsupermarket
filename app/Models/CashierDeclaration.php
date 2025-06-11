<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierDeclaration extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function cashier()
    {
        return $this->belongsTo(User::class,'cashier_id');
    }

    public function declarant()
    {
        return $this->belongsTo(User::class,'declared_by');
    }
}
