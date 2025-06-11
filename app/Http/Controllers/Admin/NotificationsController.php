<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\DatabaseNotification as Notification;
use Illuminate\Support\Facades\Session;

class NotificationsController extends Controller
{
    public function index()
    {
        $user = getLoggeduserProfile();

        $breadcrumb = ['notifications' => route('notifications.index')];

        return view('admin.notifications.index', [
            'title' => 'Notifications',
            'model' => 'notifications',
            'breadcum' => $breadcrumb,
            'notifications' => $user->notifications()->paginate(Config::get('params.list_limit_admin'))
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, ['notifications' => 'required'], ['notifications' => 'Select at least one notification']);

        $notifications = Notification::find($request->notifications);

        if ($request->action == 'read') {
            $notifications->each->markAsRead();

            Session::flash('success', 'Notifications marked as read');

            return redirect()->back();
        }

        $notifications->each->delete();

        Session::flash('success', 'Notifications deleted');

        return redirect()->back();
    }
}
