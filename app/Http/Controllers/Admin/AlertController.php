<?php

namespace App\Http\Controllers\Admin;

use App\Alert;
use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Model\User;
use App\Model\WaDepartment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    protected array $alerts = [
        'price_conflict_report',
        'new_product_report',
        'offsite_shift_requests',
        'delayed-orders',
        'unbalance-orders',
        'unbalanced-trail-balance',
        'close-operational-shift',
        'delivery_reports',
        'trial_balance_status',
        'credit-sales-otp',
        'reject-pos-return-otp',
        'drop-limit-alert'
    ];

    public function __construct()
    {
        $this->model = 'alerts';
        $this->base_route = 'alerts';
        $this->resource_folder = 'admin.alerts';
        $this->base_title = 'Alerts';
        $this->permissions_module = 'alerts';
    }

    public function index(): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Setup' => ''];
        $base_route = $this->base_route;
        $alerts = collect($this->alerts)->map(function (string $alert) {
            return [
                'alert_name' => $alert,
                'label' => ucwords(str_replace('_', ' ', $alert)),
                'recipient_type' => 'user',
                'user_recipients' => [],
                'role_recipients' => [],
                'sms_notification' => false
            ];
        })->toArray();

        foreach ($alerts as $index => $alert) {
            if ($savedAlert = Alert::where('alert_name', $alert['alert_name'])->first()) {
                $alerts[$index]['id'] = $savedAlert->id;
                $alerts[$index]['recipient_type'] = $savedAlert->recipient_type;
                $alerts[$index]['sms_notification'] = $savedAlert->sms_notification;

                $recipients = explode(',', $savedAlert->recipients);
                if ($alerts[$index]['recipient_type'] == 'user') {
                    $alerts[$index]['user_recipients'] = $recipients;
                } else {
                    $alerts[$index]['role_recipients'] = $recipients;
                }

                $alerts[$index]['role_recipients'] = collect($alerts[$index]['role_recipients'])->map(function ($value) {
                    return (int)$value;
                });

                $alerts[$index]['user_recipients'] = collect($alerts[$index]['user_recipients'])->map(function ($value) {
                    return (int)$value;
                });
            }
        }

        $users = User::all();
        $roles = Role::select(['id', 'slug', 'title'])->get();

        $alertRecipientTypes = collect([
            ['value' => 'user', 'label' => 'User'],
            ['value' => 'role', 'label' => 'Role'],
        ]);

        $alerts = collect($alerts);
        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'alerts',
            'users',
            'roles',
            'alertRecipientTypes',
        ));
    }

    public function update(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $payload = json_decode($request->payload, true);
            foreach ($payload['alerts'] as $alert) {
                if (isset($alert['id'])) {
                    $savedAlert = Alert::find($alert['id']);
                    $savedAlert->update([
                        'recipient_type' => $alert['recipient_type'],
                        'recipients' => implode(',', $alert['recipient_type'] == 'user' ? $alert['user_recipients'] : $alert['role_recipients']),
                        'sms_notification' => $alert['sms_notification'],
                    ]);
                } else {
                    Alert::create([
                        'alert_name' => $alert['alert_name'],
                        'recipient_type' => $alert['recipient_type'],
                        'recipients' => implode(',', $alert['recipient_type'] == 'user' ? $alert['user_recipients'] : $alert['role_recipients']),
                        'sms_notification' => $alert['sms_notification'],
                    ]);
                }
            }

            DB::commit();
            return $this->jsonify([], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
