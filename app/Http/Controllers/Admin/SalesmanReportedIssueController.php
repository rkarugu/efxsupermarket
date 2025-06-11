<?php

namespace App\Http\Controllers\Admin;

use JWTAuth;
use Throwable;
use App\Model\Route;
use Carbon\Carbon;
use App\SalesmanShift;
use App\SalesmanShiftIssue;
use App\Services\ExcelDownloadService;
use App\Services\MappingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use function Ramsey\Uuid\v1;
use Illuminate\Http\Request;
use App\Model\WaRouteCustomer;
use Illuminate\Support\Facades\Validator;

class SalesmanReportedIssueController extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;

    public function __construct(protected SmsService $smsService)
    {
        $this->model = 'reported-shift-issues';
        $this->base_route = 'reported-shift-issues';
        $this->resource_folder = 'admin.reported_shift_issues';
        $this->base_title = 'Reported Shift Issues';
        $this->permissions_module = 'reported-shift-issues';
    }

    public function getReportingScenarios(Request $request): JsonResponse
    {
        try {
            $scenarios = [
                [
                    'form_key' => 'price_conflict',
                    'form_name' => 'Price Conflict',
                    'fields' => [
                        [
                            'key' => 'item_id',
                            'label' => 'Item',
                            'required' => true,
                            'source' => [
                                'type' => 'dropdown',
                                'url' => '/get-item-codes'
                            ]
                        ],
                        [
                            'key' => 'new_price',
                            'label' => 'New Price',
                            'format' => 'amount',
                            'required' => true,
                            'source' => [
                                'type' => 'input',
                            ]
                        ],
                        [
                            'key' => 'image',
                            'label' => 'Photo (optional)',
                            'source' => [
                                'type' => 'camera',
                            ]
                        ],
                    ],
                ],
                [
                    'form_key' => 'new_product',
                    'form_name' => 'New Product',
                    'fields' => [
                        [
                            'key' => 'product_name',
                            'label' => 'Product Name',
                            'format' => 'varchar',
                            'required' => true,
                            'source' => [
                                'type' => 'input',
                            ]
                        ],
                        [
                            'key' => 'image',
                            'label' => 'Photo (optional)',
                            'source' => [
                                'type' => 'camera',
                            ]
                        ],
                    ],
                ],
                [
                    'form_key' => 'no_order',
                    'form_name' => 'No Order',
                    'fields' => [],
                ],
                [
                    'form_key' => 'shop_closed',
                    'form_name' => 'Shop Closed',
                    'fields' => [
                        [
                            'key' => 'image',
                            'label' => 'Photo',
                            'source' => [
                                'type' => 'camera',
                            ]
                        ],
                    ],
                ]
            ];

            return $this->jsonify(['data' => $scenarios], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function reportIssue(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required',
                'form_key' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $this->validationHandle($validator->messages());
                return $this->jsonify(['message' => $errors], 422);
            }

            $user = JWTAuth::toUser($request->token);
            $scenario = $request->form_key;
            $shift = SalesmanShift::latest()->where('salesman_id', $user->id)->where('status', 'open')->first();
            if (!$shift) {
                return $this->jsonify(['message' => 'You do not have an open shift.'], 422);
            }
            //check location
            $route = Route::find($shift->route_id);
            $shop = WaRouteCustomer::find($request->shop_id);
            if ($shift->shift_type == 'onsite') {
                $salesmanLat = $request->latitude;
                $salesmanLng = $request->longitude;

                $distance = MappingService::getTheaterDistanceBetweenTwoPoints($salesmanLat, $salesmanLng, $shop->lat, $shop->lng);

                if ($distance > $route->salesman_proximity) {

                    return $this->jsonify(['message' => "You are outside the allowed order taking distance ($distance) from the shop."], 422);
                }
            }

            $fileName = null;
            if ($request->image) {
                $uploadPath = 'uploads/shift_issues';
                $file = $request->file('image');
                if (!file_exists($uploadPath)) {

                    File::makeDirectory($uploadPath, $mode = 0777, true, true);
                }
                $fileName = $file->hashName();
                $file->move(public_path($uploadPath), $fileName);
            }

            $reported_shift_issue = SalesmanShiftIssue::where('customer_id', $request->shop_id)
                ->where('shift_id', $shift->id)->where('status','verified')->first();

            if ($reported_shift_issue) {
                return $this->jsonify(['message' => 'Issue for ' . $shop?->bussiness_name . ' has already been reported'], 422);
            }

            switch ($scenario) {
                case 'price_conflict':
                    $validator = Validator::make($request->all(), [
                        'item_id' => 'required',
                        'new_price' => 'required|gt:0',
                    ], [
                        'new_price.gt' => 'The new price must be greater than 0.',
                    ]);

                    if ($validator->fails()) {
                        $errors = $this->validationHandle($validator->messages());
                        return $this->jsonify(['message' => $errors], 422);
                    }

                    SalesmanShiftIssue::create([
                        'shift_id' => $shift->id,
                        'route_id' => $shift->route_id,
                        'salesman_id' => $user->id,
                        'customer_id' => $request->shop_id,
                        'scenario' => $request->form_key,
                        'inventory_item_id' => $request->item_id,
                        'new_price' => $request->new_price,
                        'image' => $fileName,
                        'status' => 'verified',
                    ]);

                    return $this->jsonify(['message' => 'Issue reported successfully', 'show_new_fields' => false], 200);
                case 'new_product':
                    $validator = Validator::make($request->all(), [
                        'product_name' => 'required',
                    ]);

                    if ($validator->fails()) {
                        $errors = $this->validationHandle($validator->messages());
                        return $this->jsonify(['message' => $errors], 422);
                    }

                    SalesmanShiftIssue::create([
                        'shift_id' => $shift->id,
                        'route_id' => $shift->route_id,
                        'salesman_id' => $user->id,
                        'customer_id' => $request->shop_id,
                        'scenario' => $request->form_key,
                        'product_name' => $request->product_name,
                        'image' => $fileName,
                        'status' => 'verified',

                    ]);

                    return $this->jsonify(['message' => 'Issue reported successfully', 'show_new_fields' => false], 200);
                case 'no_order':
                    $code = random_int(100000, 999999);
                    SalesmanShiftIssue::create([
                        'shift_id' => $shift->id,
                        'route_id' => $shift->route_id,
                        'salesman_id' => $user->id,
                        'customer_id' => $request->shop_id,
                        'scenario' => $request->form_key,
                        'verification_code' => $code,
                    ]);
                    //mark shop as  visited
                    // $routeCustomerRecord = $shift->shiftCustomers()->where('route_customer_id', $request->shop_id)->first();
                    // if ($routeCustomerRecord) {
                    //     $routeCustomerRecord->update(['visited' => 1]);
                    //     $routeCustomerRecord->update(['salesman_shift_type' => $shift->shift_type]);
                    // }

                    $newForm = [
                        'submission_url' => '/verify-price-conflict-verification-code',
                        'fields' => [
                            [
                                'key' => 'verification_code',
                                'label' => 'Customer Verification Code',
                                'format' => 'varchar',
                                'required' => true,
                                'source' => [
                                    'type' => 'input',
                                ]
                            ],
                        ]
                    ];

                    $customer = WaRouteCustomer::select('id', 'phone')->find($request->shop_id);
                    $message = "Your no_order verification code is $code";
                    try {
                        // sendMessage($message, $customer->phone);
                        $this->smsService->sendMessage($message, $customer->phone);

                    } catch (Throwable $e) {
                        // pass
                    }

                    return $this->jsonify(['message' => 'Issue reported successfully', 'show_new_fields' => true, 'form' => $newForm], 200);
                case 'shop_closed':
                    // $code = random_int(100000, 999999);
                    if (!$request->hasFile('image')) {
                        return $this->jsonify(['message' => 'An image file is required.'], 422);
                    }

                    SalesmanShiftIssue::create([
                        'shift_id' => $shift->id,
                        'route_id' => $shift->route_id,
                        'salesman_id' => $user->id,
                        'customer_id' => $request->shop_id,
                        'scenario' => $request->form_key,
                        'image' => $fileName,
                        'status' => 'verified',

                        // 'verification_code' => $code,
                    ]);
                    //mark shop as  visited
                    $routeCustomerRecord = $shift->shiftCustomers()->where('route_customer_id', $request->shop_id)->first();
                    if ($routeCustomerRecord) {
                        $routeCustomerRecord->update(['visited' => 1]);
                        $routeCustomerRecord->update(['salesman_shift_type' => $shift->shift_type]);
                    }

                    // $newForm = [
                    //     'submission_url' => '/verify-shop-closed-verification-code',
                    //     'fields' => [
                    //         [
                    //             'key' => 'verification_code',
                    //             'label' => 'Customer Verification Code',
                    //             'format' => 'varchar',
                    //             'required' => true,
                    //             'source' => [
                    //                 'type' => 'input',
                    //             ]
                    //         ],
                    //     ]
                    // ];

                    // $customer = WaRouteCustomer::select('id', 'phone')->find($request->shop_id);
                    // $message = "You verification code is $code";
                    // try {
                    //     sendMessage($message, $customer->phone);
                    // } catch (Throwable $e) {
                    //     // pass
                    // }

                    // return $this->jsonify(['message' => 'Issue reported successfully', 'show_new_fields' => false], 200);
                    return $this->jsonify(['message' => 'Issue reported successfully', 'show_new_fields' => false], 200);
                default:
                    return $this->jsonify(['message' => 'Invalid scenario received'], 422);
            }
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function verifyPriceConflictCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'verification_code' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $this->validationHandle($validator->messages());
                return $this->jsonify(['message' => $errors], 422);
            }

            $user = JWTAuth::toUser($request->token);
            $shift = SalesmanShift::latest()->where('salesman_id', $user->id)->where('status', 'open')->first();
            $issue = SalesmanShiftIssue::latest()
                ->where('shift_id', $shift->id)
                ->where('scenario', 'no_order')
                ->where('customer_id', $request->shop_id)
                ->where('status', 'pending')
                ->first();
            if ($issue->verification_code != $request->verification_code) {
                return $this->jsonify(['message' => 'Incorrect verification code'], 422);
            }

            $issue->update(['status' => 'verified']);
            $routeCustomerRecord = $shift->shiftCustomers()->where('route_customer_id', $request->shop_id)->first();
            if ($routeCustomerRecord) {
                $routeCustomerRecord->update(['visited' => 1]);
                $routeCustomerRecord->update(['salesman_shift_type' => $shift->shift_type]);
            }
            return $this->jsonify(['message' => 'Code verified successfully.'], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function verifyShopClosedCode(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'verification_code' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $this->validationHandle($validator->messages());
                return $this->jsonify(['message' => $errors], 422);
            }

            $user = JWTAuth::toUser($request->token);
            $shift = SalesmanShift::latest()->where('salesman_id', $user->id)->where('status', 'open')->first();
            $issue = SalesmanShiftIssue::latest()
                ->where('shift_id', $shift->id)
                ->where('scenario', 'shop_closed')
                ->where('customer_id', $request->shop_id)
                ->where('status', 'pending')
                ->first();
            if ($issue->verification_code != $request->verification_code) {
                return $this->jsonify(['message' => 'Incorrect verification code'], 422);
            }

            $issue->update(['status' => 'verified']);
            return $this->jsonify(['message' => 'Code verified successfully.'], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $title = $this->base_title;
        $model = $this->model;
        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();


        if (!can('view', $this->permissions_module)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = [$this->base_title => route("$this->base_route.index"), 'Listing' => ''];
        $base_route = $this->base_route;

        $selectedRouteId = $request->route_name;
        if (!$request->has('start_date') || !$request->has('end_date')) {
            $startDate = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $startDate = Carbon::parse($request->start_date)->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s');
        }

        $displayScenario = $request->display_scenario;

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
            ->get()
            ->pluck('scenario')
            ->unique()
            ->map(function ($scenario) {
                $formattedScenario = strtolower(str_replace('_', ' ', $scenario));
                return $formattedScenario;
            });

        $query = DB::table('salesman_shift_issues')

            ->leftJoin('routes', 'salesman_shift_issues.route_id', '=', 'routes.id')
            ->leftJoin('users', 'salesman_shift_issues.salesman_id', '=', 'users.id')
            ->leftJoin('wa_route_customers', 'salesman_shift_issues.customer_id', '=', 'wa_route_customers.id')
            ->leftJoin('resolved_salesman_reported_issues', 'salesman_shift_issues.id', '=', 'resolved_salesman_reported_issues.salesman_shift_issues_id')
            ->select(
                'salesman_shift_issues.*',
                'routes.route_name as route',
                'routes.group',
                'users.name as salesman',
                'wa_route_customers.bussiness_name as customer',
                'wa_route_customers.image_url as original_image',
                'resolved_salesman_reported_issues.resolved as resolved_status'
            )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('salesman_shift_issues.created_at', [$startDate, $endDate]);
            })
            ->when($selectedRouteId, function ($query) use ($selectedRouteId) {
                return $query->where('routes.id', $selectedRouteId);
            })
            ->when($displayScenario, function ($query) use ($displayScenario) {
                return $query->where('salesman_shift_issues.scenario', $displayScenario);
            })
            ->when($request->group_name, function ($query) use ($request) {
                return $query->where('routes.group', $request->group_name);
            });

        // if (isset($startDate) && $startDate >= '2024-06-10 00:00:00') {
            $query->where('salesman_shift_issues.status', 'verified');
        // }

        $query = $query->orderBy('salesman_shift_issues.created_at', 'desc');

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
