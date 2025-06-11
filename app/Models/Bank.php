<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    
    public function branches()
    {
        return $this->hasMany(BankBranch::class);
    }
    
    public function bankAccounts()
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }
}
