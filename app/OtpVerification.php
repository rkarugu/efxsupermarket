<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    //
    protected $fillable = ['otp', 'phone_number', 'status'];
}
