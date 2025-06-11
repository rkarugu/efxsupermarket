<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedNewItem extends Model
{
    use HasFactory;
    protected $table = 'reported_new_items';

    public function getRelatedUser()
    {
        return $this->belongsTo(User::class,'reported_by');
    }
}
