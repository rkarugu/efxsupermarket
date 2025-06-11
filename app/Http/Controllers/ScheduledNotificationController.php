<?php

namespace App\Http\Controllers;

use App\Model\Role;
use App\Model\User;
use App\Models\ScheduledNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ScheduledNotificationController extends Controller
{
    protected string $title;
    protected string $model;

    public function __construct()
    {
        $this->title = 'Scheduled Notifications';
        $this->model = 'scheduled-notifications';
    }

    private function getNotifications()
    {
        $notificationPath = app_path('Notifications');
        $notifications = [];

        if (File::isDirectory($notificationPath)) {
            $files = File::allFiles($notificationPath);

            foreach ($files as $file) {
                $relativePath = $file->getRelativePathname();
                $className = $this->getClassFromFile($relativePath);

                if ($className && is_subclass_of($className, Notification::class)) {
                    $notifications[] = (object)[
                        'class_name' => $className,
                        'name' => $this->convertToSpaceSeparatedName(class_basename($className))
                    ];
                }
            }
        }

        return $notifications;
    }

    private function getClassFromFile($relativePath)
    {
        $class = str_replace(['/', '.php'], ['\\', ''], $relativePath);
        $fullClass = 'App\\Notifications\\' . $class;

        return class_exists($fullClass) ? $fullClass : null;
    }

    private function convertToSpaceSeparatedName($name)
    {
        return Str::title(Str::snake($name, ' '));
    }

    private function getFrequencies()
    {
        return [
            'daily' => 'Daily',
        ];
    }

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $query = ScheduledNotification::query();

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->editColumn('class_name', function ($notification) {
                    return $this->convertToSpaceSeparatedName(class_basename($notification->class_name));
                })
                ->editColumn('roles', function ($notification) {
                    return view('admin.scheduled_notifications.labels', [
                        'items' => Role::findMany($notification->roles)->pluck('title')->toArray()
                    ]);
                })
                ->editColumn('users', function ($notification) {
                    return view('admin.scheduled_notifications.labels', [
                        'items' => User::findMany($notification->users)->pluck('name')->toArray()
                    ]);
                })
                ->editColumn('emails', function ($notification) {
                    return view('admin.scheduled_notifications.labels', [
                        'items' => $notification->emails
                    ]);
                })
                ->editColumn('phone_numbers', function ($notification) {
                    return view('admin.scheduled_notifications.labels', [
                        'items' =>  $notification->phone_numbers
                    ]);
                })
                ->addColumn('actions', function ($notification) {
                    return view('admin.scheduled_notifications.actions', [
                        'notification' => $notification
                    ]);
                })
                ->toJson();
        }

        return view('admin.scheduled_notifications.index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => [
                'Scheduled Notifications' => route('scheduled-notifications.index')
            ],
            'notifications' => $this->getNotifications(),
            'frequencies' => $this->getFrequencies(),
            'roles' => Role::get(),
            'users' => User::get(),
        ]);
    }

    public function store(Request $request)
    {
        if (!can('add', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, $this->rules());

        ScheduledNotification::create([
            'class_name' => $request->input('notification'),
            'frequency' => $request->input('frequency'),
            'time' => $request->input('time'),
            'roles' => $request->input('roles'),
            'users' => $request->input('users'),
            'emails' => $request->filled('emails') ? explode(',', $request->input('emails')) : null,
            'phone_numbers' => $request->filled('phone_numbers') ? explode(',', $request->input('phone_numbers')) : null,
        ]);

        Cache::flush('scheduled_notifications');

        Session::flash('success', 'Notification schedule created successfully');

        return redirect()->route('scheduled-notifications.index');
    }

    public function update(Request $request, ScheduledNotification $scheduledNotification)
    {
        if (!can('edit', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $this->validate($request, $this->rules());

        $scheduledNotification->update([
            'class_name' => $request->input('notification'),
            'frequency' => $request->input('frequency'),
            'time' => $request->input('time'),
            'roles' => $request->input('roles'),
            'users' =>  $request->input('users'),
            'emails' => $request->filled('emails') ? explode(',', $request->input('emails')) : null,
            'phone_numbers' => $request->filled('phone_numbers') ? explode(',', $request->input('phone_numbers')) : null,
        ]);

        Cache::flush('scheduled_notifications');

        Session::flash('success', 'Notification schedule updated successfully');

        return redirect()->route('scheduled-notifications.index');
    }

    public function destroy(ScheduledNotification $scheduledNotification)
    {
        if (!can('delete', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $scheduledNotification->delete();

        Session::flash('success', 'Notification schedule deleted successfully');

        return redirect()->route('scheduled-notifications.index');
    }

    private function rules()
    {
        return [
            'notification' => 'required',
            'frequency' => 'required',
            'roles' => 'required_without_all:users,emails,phone_numbers',
            'users' => 'required_without_all:roles,emails,phone_numbers',
        ];
    }
}
