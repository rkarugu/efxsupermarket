<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankBranch extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }
}
