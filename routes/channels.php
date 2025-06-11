<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Model\User;
use App\Model\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('notifications', function (User $user) {
    $authUser  = Auth::user();
    $roles = UserPermission::where('module_name', 'vehicles-overview')->pluck('role_id')->toArray();
    return (in_array($user->role_id, $roles) || $user->role_id == 1 );
});
Broadcast::channel('vehicle-location', function (User $user) {
    $authUser  = Auth::user();
    $roles = UserPermission::where('module_name', 'vehicles-overview')->pluck('role_id')->toArray();
    return (in_array($user->role_id, $roles) || $user->role_id == 1 );
});
