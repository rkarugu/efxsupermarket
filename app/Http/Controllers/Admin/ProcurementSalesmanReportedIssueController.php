<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ExcelDownloadService;
use Illuminate\Contracts\Database\Query\Builder;

class ProcurementSalesmanReportedIssueController extends Controller
{

    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct()
    {
        $this->model = 'procurement-reported-shift-issues';
        $this->base_route = 'procurement-reported-shift-issues';
        $this->resource_folder = 'admin.procurement_reported_shift_issues';
        $this->base_title = 'Reported Shift Issues';
        $this->permissions_module = 'reported-shift-issues';
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $model = $this->model;

        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        $selectedRouteId = $request->route_name;
        $displayScenario = $request->display_scenario;
        if (!$request->has('start_date') || !$request->has('end_date')) {
            $startDate = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        }

        // $routes = DB::table('routes')->select('id', 'route_name')->get();

        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $routes = DB::table('routes')->select('id', 'route_name')->get();
        } else {
            $routes = DB::table('routes')->where('restaurant_id', $authuser->userRestaurent->id)->select('id', 'route_name')->get();
        }

        $groups = DB::table('routes')
            ->select('group')
            ->distinct()
            ->pluck('group')
            ->prepend('NO GROUP');

        $uniqueScenarios = DB::table('salesman_shift_issues')
            ->select('scenario')
            ->distinct()
            ->whereIn('scenario', ['new_product', 'price_conflict'])
            ->pluck('scenario')
            ->unique()
            ->map(function ($scenario) {
                $formattedScenario = strtolower(str_replace('_', ' ', $scenario));
                return $formattedScenario;
            });

        $query = DB::table('salesman_shift_issues')
            ->join('routes', 'salesman_shift_issues.route_id', '=', 'routes.id')
            ->leftJoin('users', 'salesman_shift_issues.salesman_id', '=', 'users.id')
            ->leftJoin('wa_route_customers', 'salesman_shift_issues.customer_id', '=', 'wa_route_customers.id')
            ->leftJoin('resolved_salesman_reported_issues', 'salesman_shift_issues.id', '=', 'resolved_salesman_reported_issues.salesman_shift_issues_id')
            ->select(
                'salesman_shift_issues.*',
                'routes.route_name as route',
                'routes.group',
                'users.name as salesman',
                'wa_route_customers.bussiness_name as customer',
                'resolved_salesman_reported_issues.resolved as resolved_status'
            )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('salesman_shift_issues.created_at', [$startDate, $endDate]);
            })
            ->when($selectedRouteId, function ($query) use ($selectedRouteId) {
                return $query->where('routes.id', $selectedRouteId);
            })
            ->when($request->group_name, function ($query) use ($request) {
                return $query->where('routes.group', $request->group_name);
            })->orderBy('salesman_shift_issues.created_at', 'desc');
        if (isset($displayScenario)) {
            $query->where('salesman_shift_issues.scenario', $displayScenario);
        } else {
            $query->where(function ($query) {
                $query->where('salesman_shift_issues.scenario', 'price_conflict')
                    ->orWhere('salesman_shift_issues.scenario', 'new_product');
            });
        }
        if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
            $query->where('routes.restaurant_id', $authuser->userRestaurent->id);
        }

        $issues = $query->get();

        $inventoryItems = DB::table('wa_inventory_items')->get()->keyBy('id');
        $issues = $issues->map(function ($issue) use ($inventoryItems) {
            $inventoryItem = $inventoryItems->get($issue->inventory_item_id);
            $issue->wainventoryitem = $inventoryItem;
            if (property_exists($issue, 'scenario')) {
                $issue->display_scenario = ucwords(str_replace('_', ' ', $issue->scenario));
            }
            return $issue;
        });

        $allScenarios = $issues->pluck('display_scenario')->unique();

        if (request()->intent && request()->intent == 'Excel') {
            $headings = ['Date', 'Reported Issue', 'Route', 'Group', 'Salesman', 'Customer', 'Item Desc', 'Item Price', 'Competitor Price', 'Image URL'];
            $filename = "Salesman_Shift_Reported_Issues_$startDate-$endDate.xlsx";
            $excelData = [];

            foreach ($issues as $issue) {
                $imageLinkText = isset($issue->image) ? 'View Image' : '';
                $imageURL = isset($issue->image) ? asset('uploads/shift_issues/' . $issue->image) : '';
                $rowData = [
                    $issue->created_at,
                    $issue->display_scenario,
                    $issue->route,
                    $issue->group,
                    $issue->salesman,
                    $issue->customer,
                    isset($issue->wainventoryitem) ? $issue->wainventoryitem->description : (isset($issue->product_name) ? $issue->product_name : ''),
                    isset($issue->wainventoryitem) ? $issue->wainventoryitem->selling_price : '',
                    isset($issue->new_price) ? $issue->new_price : '',
                    '=HYPERLINK("' . $imageURL . '", "' . $imageLinkText . '")'
                ];
                $excelData[] = $rowData;
            }

            return ExcelDownloadService::download($filename, collect($excelData), $headings);
        }


        return view("$this->resource_folder.index", compact(
            'title',
            'routes',
            'groups',
            'model',
            'breadcum',
            'base_route',
            'issues',
            'allScenarios',
            'uniqueScenarios'
        ));
    }
}
