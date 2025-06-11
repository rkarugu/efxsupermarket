<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeniedAccess extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'user_denied_accesses';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
