<?php

namespace App\Services;

use App\User;
use App\UserLinkedDevice;
use Illuminate\Support\Facades\DB;

 
class UserLinkedDeviceService 
{

   public function getLinkedDEvice(User $user): UserLinkedDevice|null
   {
    return $user->linkedDevice();
   }
}