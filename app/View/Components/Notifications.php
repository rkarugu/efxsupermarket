<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notifications extends Component
{
    public function render(): View|Closure|string
    {
        $user = getLoggeduserProfile();

        return view('components.notifications', [
            'notifications' => $user->unreadNotifications
        ]);
    }
}
