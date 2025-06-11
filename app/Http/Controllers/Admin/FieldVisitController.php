<?php

namespace App\Http\Controllers\Admin;

use App\Alert;
use App\FieldVisitSchedule;
use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Model\Route;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FieldVisitController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'field-visits';
        $this->base_route = 'field-visits';
        $this->resource_folder = 'admin.field_visits';
        $this->base_title = 'Field Visit Schedules';
        $this->permissions_module = 'field-visits';
    }

    public function index(): View|RedirectResponse
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $dates = FieldVisitSchedule::distinct('date')->orderBy('date')->pluck('date');
        $list = [];
        foreach ($dates as $date) {
            $payload = [
                'date' => Carbon::parse($date)->toFormattedDayDateString(),
                'routes' => []
            ];

            $searchDate = Carbon::parse($date)->toDateString();
            $previousDate = Carbon::parse($date)->subDay()->toDateString();
            // $searchDate = '2024-01-29';
            // $previousDate = '2024-01-28';
            $dateSchedule = FieldVisitSchedule::whereDate('date', '=', $searchDate)->get();
            foreach ($dateSchedule as $dateRow) {
                $route = Route::with(['centers', 'waRouteCustomer'])->find($dateRow->route_id);
                $routePayload = [
                    'route_id' => $route->id,
                    'route_name' => $route->route_name,
                    'salesman' => $route->salesman()?->name ?? 'MISSING',
                    'hq_rep' => "$dateRow->hq_rep ($dateRow->hq_rep_contact)",
                    'bw_rep' => "$dateRow->bw_rep",
                    'initial_customers' => $route->waRouteCustomer()->whereDate('created_at', '!=', $searchDate)->count(),
                    'visited' => $route->waRouteCustomer()->whereDate('created_at', '!=', $searchDate)->where('created_by', '!=', 0)->count(),
                    'not_visited' => $route->waRouteCustomer()->whereDate('created_at', '!=', $searchDate)->where('created_by', 0)->count(),
                    'new' => $route->waRouteCustomer()->whereDate('created_at', '>', $previousDate)->count(),
                ];

                $payload['routes'][] = $routePayload;
            }

            $list[] = $payload;
        }

        return view("$this->resource_folder.index", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'list'
        ));
    }

    public function create(): View|RedirectResponse
    {
        $title = "$this->base_title | Add";
        $model = $this->model;

        if (!can('create', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Create' => ''];
        $base_route = $this->base_route;

        $routes = Route::whereNotNull('order_taking_days')->get()->map(function (Route $route) {
            $route->salesman = $route->salesman();
            $orderTakingDates = [];
            $orderTakingDays = explode(',', $route->order_taking_days);
            $today = Carbon::now()->dayOfWeek;
            if ($today == 0) {
                $today = 7;
            }

            foreach ($orderTakingDays as $orderTakingDay) {
                $orderTakingDayAsInt = (int)$orderTakingDay;
                if ($orderTakingDayAsInt == 0) {
                    $orderTakingDayAsInt = 7;
                }

                switch (true) {
                    case ($orderTakingDayAsInt == $today):
                        $orderTakingDates[] = Carbon::now()->toDateString();
                        break;
                    case ($orderTakingDayAsInt < $today):
                        $difference = $today - $orderTakingDayAsInt;
                        $newDate = Carbon::now()->subDays($difference);
                        $orderTakingDates[] = $newDate->toDateString();
                        break;
                    case ($orderTakingDayAsInt > $today):
                        $difference = $orderTakingDayAsInt - $today;
                        $newDate = Carbon::now()->addDays($difference);
                        $orderTakingDates[] = $newDate->toDateString();
                        break;
                    default:
                        break;
                }
            }

            $route->order_taking_dates = $orderTakingDates;
            return $route;
        });

        return view("$this->resource_folder.create", compact(
            'title',
            'model',
            'breadcum',
            'base_route',
            'routes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        foreach ($request->route_id as $index => $routeId) {
            if(($request->visit)[$index] == 'yes') {
                FieldVisitSchedule::create([
                    'date' => $request->date,
                    'route_id' => $routeId,
                    'hq_rep' => ($request->hq_rep)[$index],
                    'hq_rep_contact' => ($request->hq_rep_contact)[$index],
                    'bw_rep' => ($request->bw_rep)[$index],
                    'bw_rep_contact' => ($request->bw_rep_contact)[$index],
                ]);
            }
        }

        return redirect()->route("$this->base_route.index")->with('success', 'Schedule created successfully');
    }
}
