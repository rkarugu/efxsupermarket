<?php

namespace App\Http\Controllers\Shared;

use App\Model\WaPosCashSalesPayments;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Collection;
use Session;
use App\DeliverySchedule;
use App\Exports\RouteCustomerExport;
use App\Interfaces\SmsService;
use App\Jobs\ExecutedQueuedJobs;
use App\Jobs\GetShopDistanceEstimates;
use App\Jobs\GetShopRoutePolylines;
use App\Jobs\GetShopRouteSections;
use App\Jobs\PrepareStoreParkingList;
use App\Model\Route;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaCustomer;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaRouteCustomer;
use App\Model\WaShift;
use App\SalesmanShift;
use App\Services\MappingService;
use App\Services\NewRouteCustomerService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessShopImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use PDF;
use App\Model\UserLog;
use App\Model\DeliveryCentres;
use App\Models\GeomappingSchedules;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session as SessionSuccess;


class RouteCustomerController extends Controller
{
    protected $model;
    protected $baseRouteName;
    protected $baseTitle;
    protected $resourceFolder;
    protected $permissionsModule;
    protected $pmodule;


    public function __construct(protected SmsService $smsService)
    {
        $this->model = 'route-customers';
        $this->baseRouteName = 'route-customers';
        $this->baseTitle = 'Route Customers';
        $this->resourceFolder = 'admin.route_customers';
        $this->pmodule = 'wa-route-customer';
    }

    public function index()
    {
        if (!can('listing', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if (request()->wantsJson()) {
            $query = WaRouteCustomer::with([
                'route',
                'center'
            ])
                ->when(request()->filled('branch'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('restaurant_id', request()->branch);
                    });
                })
                ->when(request()->filled('route'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('id', request()->route);
                    });
                })
                ->when(request()->filled('center'), function ($query) {
                    $query->whereHas('center', function ($q) {
                        $q->where('id', request()->center);
                    });
                });

            if (!$isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
                $query->whereHas('route', function ($q) use ($authuser) {
                    $q->where('restaurant_id', $authuser->userRestaurent->id);
                }); 
            }

            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($customer) {
                    return $customer->created_at->format('D, M j, Y');
                })->editColumn('route.route_name', function ($customer) {
                    return $customer->route?->route_name;
                })->editColumn('center.name', function ($customer) {
                    return $customer->center?->name;
                })
                ->addColumn('actions', function ($customer) {
                    return view('admin.route_customers.actions.list', compact('customer'));
                })
                ->toJson();
        }

        // $branches = Restaurant::get();

        if ($isAdmin || isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::get();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        return view('admin.route_customers.index', [
            'title' => $this->baseTitle,
            'model' => 'route-customers-listing',
            'breadcum' => [
                $this->baseTitle => route("$this->baseRouteName.index"),
                'Listing' => ''
            ],
            'branch' => $branches
        ]);
    }

    public function routes()
    {
        $this->validate(request(), ['branch' => 'required']);

        $routes = Route::select('id', 'route_name')->where('restaurant_id', request()->branch)->get();

        return response()->json([
            'routes' => $routes
        ]);
    }

    public function centers()
    {
        $this->validate(request(), ['route' => 'required']);

        $centers = DeliveryCentres::select('id', 'name')->where('route_id', request()->route)->get();

        return response()->json([
            'centers' => $centers
        ]);
    }

    public function overview()
    {

        $title = "$this->baseTitle - Overview";

        $breadcum = [
            $title => route("$this->baseRouteName.index")
        ];
        $shopMap = WaRouteCustomer::with(['route', 'route.users', 'center'])->get();

        $googleMapsApiKey = config('app.google_maps_api_key');
        $shoplocationdata = [];
        $branches = Restaurant::all();

        $statusCounts = DB::table('wa_route_customers')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereIn('status', ['approved', 'unverified', 'verified'])
            ->groupBy('status')
            ->get();
        $all = DB::table('wa_route_customers')->count();

        $approvedCount = 0;
        $unverifiedCount = 0;
        $verifiedCount = 0;
        $dormantCount = 0;

        foreach ($statusCounts as $statusCount) {
            if ($statusCount->status === 'approved') {
                $approvedCount = $statusCount->count;
            } elseif ($statusCount->status === 'unverified') {
                $unverifiedCount = $statusCount->count;
            } elseif ($statusCount->status === 'verified') {
                $verifiedCount = $statusCount->count;
            } elseif ($statusCount->status === 'dormant') {
                $dormantCount = $statusCount->count;
            }
        }

        foreach ($shopMap as $customer) {

            if (isset($customer->center)) {
                $shoplocationdata[] = [
                    'shop_lat' => $customer->center->lat,
                    'shop_lng' => $customer->center->lng,
                    'shop_town' => $customer->center->town,
                    'shop_bussiness_name' => $customer->center->bussiness_name,
                    'shop_name' => $customer->name,

                ];
            }
        }

        // dd($shoplocationdata);

        return view("$this->resourceFolder.overview", [
            'route_customers' => $shopMap,
            'model' => $this->model,
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
            'googleMapsApiKey' => $googleMapsApiKey,
            'shopdetails' => $shopMap,
            'shop_location_data' => $shoplocationdata,
            'branches' => $branches,
            'approvedCount' => $approvedCount,
            'unverifiedCount' => $unverifiedCount,
            'verifiedCount' => $verifiedCount,
            'dormantCount' => $dormantCount,
            'all' => $all
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $limit = $request->input('length');
        $start = $request->input('start');

        $query = WaRouteCustomer::with(['route', 'route.users', 'center'])->latest();
        if ($request->unverified == 1) {
            $query = $query->where('status', 'unverified');
        }

        if ($request->verified == 1) {
            $query = $query->where('status', 'verified');
        }


        $routeCustomers = $query->offset($start)->limit($limit)->get();

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $routeCustomers = $routeCustomers->filter(function (WaRouteCustomer $shop) use ($search) {
                $nameCheck = str_contains(strtolower($shop->name), strtolower($search));
                $businessCheck = str_contains(strtolower($shop->bussiness_name), strtolower($search));
                $routeCheck = str_contains(strtolower($shop->route->route_name), strtolower($search));
                $centerCheck = str_contains(strtolower($shop->center->name), strtolower($search));
                $phoneNumberCheck = str_contains(strtolower($shop->phone), strtolower($search));
                $onboardingDateCheck = str_contains(strtolower(Carbon::parse($shop->created_at)->toFormattedDayDateString()), strtolower($search));

                return $nameCheck || $businessCheck || $routeCheck || $centerCheck || $phoneNumberCheck || $onboardingDateCheck;
            })->values();
        }

        $routeCustomers = $routeCustomers->map(function (WaRouteCustomer $shop) use ($request) {
            $shop->onboarding_date = Carbon::parse($shop->created_at)->toFormattedDayDateString();
            $shop->route_name = $shop->route->route_name;
            $shop->center_name = $shop->center->name;

            $shop->salesman = 'Unassigned';
            $routeUsers = $shop->route->users;
            foreach ($routeUsers as $routeUser) {
                if ($routeUser->role_id == 4) {
                    $shop->salesman = $routeUser->name;
                    break;
                }
            }

            $shop->display_status = $shop->getDisplayStatus();

            $actionLinks = "<div class='action-button-div'>";

            $viewShopLink = "<a href='" . route("$this->baseRouteName.show", $shop->id) . "' title='View Route Customer'>
                                <i class='fa fa-eye text-info fa-lg'></i>
                             </a>";
            $actionLinks .= $viewShopLink;

            $loggedInUser = getLoggeduserProfile();
            $userPermissions = $loggedInUser->permissions;

            // $canEditShop = ($loggedInUser->userRole->slug == 'super-admin') || isset($userPermissions['route-customers___edit']);
            // if ($canEditShop && ($request->all == 1)) {
            //     $editShopLink = "<a href='" . route("$this->baseRouteName.edit", $shop->id) . "' title='Edit Route Customer'>
            //                     <i class='fa fa-edit text-primary fa-lg'></i>
            //                  </a>";
            //     $actionLinks .= $editShopLink;
            // }

            if (($shop->status == 'unverified') && ($request->unverified == 1)) {
                $canVerifyShop = ($loggedInUser->userRole->slug == 'super-admin') || (isset($userPermissions['route-customers___verify']));
                if ($canVerifyShop) {
                    $verifyShopLink = "<button title='Verify Route Customer' data-toggle='modal' data-target='#confirm-verify-shop-modal' data-backdrop='static'>
                                        <i class='fa fa-check-circle text-success fa-lg'></i>
                                        <form action='" . route("$this->baseRouteName.verify", $shop->id) . "' method='post' id='verify-shop-form'>
                                            " . csrf_field() . "
                                            <input type='hidden' id='source' name='source'>
                                        </form>
                                    </button>";
                    $actionLinks .= $verifyShopLink;
                }
            }

            if (($shop->status == 'verified') && ($request->verified == 1)) {
                $canApproveShop = ($loggedInUser->userRole->slug == 'super-admin') || (isset($userPermissions['route-customers___approve']));
                if ($canApproveShop) {
                    $formId = "approve-shop-form-$shop->id";
                    $source = "source-$shop->id";
                    $approveShopLink = "<button title='Approve Route Customer' data-toggle='modal' data-target='#confirm-approve-shop-modal' 
                                            data-backdrop='static' data-id='$shop->id'>
                                            <i class='fa fa-check-square text-success fa-lg'></i>
                                            <form action='" . route("$this->baseRouteName.approve", $shop->id) . "' method='post' id='$formId'>
                                                " . csrf_field() . "
                                                <input type='hidden' id='$source' name='source'>
                                            </form>
                                        </button>";
                    $actionLinks .= $approveShopLink;
                }
            }

            $actionLinks .= "</div>";
            $shop->actions = $actionLinks;

            unset($shop->route);
            unset($shop->center);
            return $shop;
        });

        $responsePayload = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => count($routeCustomers),
            "recordsFiltered" => count($routeCustomers),
            "data" => $routeCustomers
        );

        return response()->json($responsePayload);
    }

    public function create()
    {
        $title = "$this->baseTitle - Add";
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Add' => ''
        ];

        return view("$this->resourceFolder.create", [
            'model' => 'route-customers-listing',
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
        ]);
    }

    public function show($id)
    {
        $shopdetails = WaRouteCustomer::with(['route', 'route.users', 'center'])->find($id);

        $title = "$this->baseTitle - Show Details";
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Add' => ''
        ];
        $googleMapsApiKey = config('app.google_maps_api_key');
        $shoplocationdata['shop_lat'] = $shopdetails->lat;
        $shoplocationdata['shop_lng'] = $shopdetails->lng;
        $shoplocationdata['shop_name'] = $shopdetails->name;
        $shoplocationdata['location_name'] = $shopdetails->locationame;
        return view("$this->resourceFolder.show", [
            'title' => $title,
            'breadcum' => $breadcum,
            'model' => 'route-customers-listing',
            'googleMapsApiKey' => $googleMapsApiKey,
            'shopdetails' => $shopdetails,
            'base_route_name' => $this->baseRouteName,
            'shop_location_data' => $shoplocationdata
        ]);
    }
    public function customShow(Request $request, $id, $model)
    {
        if (isset($model)) {
            $modeString = $model;
        } else {
            $modeString = 'route-customers-listing';
        }
        $shopdetails = WaRouteCustomer::with(['route', 'route.users', 'center'])->find($id);

        $title = "$this->baseTitle - Show Details";
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Add' => ''
        ];
        if($request->schedule_id){
            $schedule_id = $request->schedule_id;
        }else{
            $schedule_id = null;
        }
        $googleMapsApiKey = config('app.google_maps_api_key');
        $shoplocationdata['shop_lat'] = $shopdetails->lat;
        $shoplocationdata['shop_lng'] = $shopdetails->lng;
        $shoplocationdata['shop_name'] = $shopdetails->name;
        $shoplocationdata['location_name'] = $shopdetails->locationame;
        return view("$this->resourceFolder.show", [
            'title' => $title,
            'breadcum' => $breadcum,
            'model' => $modeString,
            'googleMapsApiKey' => $googleMapsApiKey,
            'shopdetails' => $shopdetails,
            'base_route_name' => $this->baseRouteName,
            'shop_location_data' => $shoplocationdata,
            'schedule_id' => $schedule_id
        ]);
    }

    public function storeFromApi(Request $request): JsonResponse
    {
        $validations = Validator::make($request->all(), [
            'route_id' => 'required',
            'name' => 'required',
            'phone_no' => 'required|numeric|digits_between:9,12',
//            'customer_type' => 'required',
            'business_name' => 'required|string|min:1|max:200',
            'town' => 'required|string|min:1|max:200',
            'contact_person' => 'nullable|string|min:1|max:200',
            'center_id' => 'required|exists:delivery_centres,id',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validations->fails()) {
            return response()->json(['result' => 0, 'message' => $validations->errors()], 422);
        }

        $customerWithPhoneNumber = WaRouteCustomer::latest()->where('phone', $request->phone_no)->whereIn('status', ['verified', 'approved', 'duplicate'])->first();
        if ($customerWithPhoneNumber) {
            $status = 'duplicate';
        } else {
            $status = 'verified';
        }

        DB::beginTransaction();
        try {
            $getUserData = JWTAuth::toUser($request->token);
            $route = Route::find($request->route_id);
            $customer = WaCustomer::where("route_id", $route->id)->first();
            if (!$customer) {
                $customer = new WaCustomer();
                $customer->customer_code = getCodeWithNumberSeries('CUSTOMERS');
                $customer->customer_name = $route->route_name;
                $customer->credit_limit = 0;
                $customer->route_id = $route->id;
                $customer->save();

                updateUniqueNumberSeries('CUSTOMERS', $customer->customer_code);
            }

            $new = new WaRouteCustomer;
            $new->created_by = $getUserData->id;
            $new->route_id = $request->route_id;
            $new->delivery_centres_id = $request->center_id;
            $new->customer_id = $customer->id;
            $new->kra_pin = $request->kra_pin;
            $new->name = strtoupper($request->name);
            $new->phone = $request->phone_no;
            $new->bussiness_name = strtoupper($request->business_name);
            $new->town = $request->town;
            $new->contact_person = $request->contact_person;
            $new->lat = $request->latitude;
            $new->lng = $request->longitude;
            $new->gender = $request->gender;
            $new->comment = $request->comment;
            $new->status = $status;
            $new->secondary_name = $request->secondary_name;
            $new->secondary_phone_no = $request->secondary_phone_no;
            $new->customer_type = $request->customer_type ?? null;


            // if ($request->has('is_dormant') && $request->input('is_dormant') === 'true') {
            //     $new->status = 'dormant';
            // }
            $new->save();


            if ($request->file('photo')) {
                $file = $request->file('photo');
                $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('uploads/shops/') . $fileName;
                $file->move(public_path('uploads/shops/'), $fileName);
                ProcessShopImage::dispatch($filePath, $new->id);
            }
            


            //            update shop distance from the starting point if the shop co-ordinates are not zero or null
            $route = $new->route;

            if ($new->has_valid_location && $route->has_valid_location) {
                //                GetShopDistanceEstimates::dispatch($new);
                //                GetShopRoutePolylines::dispatch($new);
                //                GetShopRouteSections::dispatch($new);
            }

            $customerMessage = "Greetings! Your shop has been on-boarded with Kanini Haraka enterprise. We are looking forward to more business with you.";
            try {
                $this->smsService->sendMessage($customerMessage, $new->phone);
            } catch (\Throwable $e) {
            }

            // Send SMS to Route Manager
            //            try {
            //                $routeManager = $route->routeManager();
            //                if ($routeManager) {
            //                    $message = "You have a new onboarding request for $new->bussiness_name on route $route->route_name";
            //                    send_sms(substr($routeManager->phone_number, 1), $message);
            //                }
            //            } catch (\Throwable $e) {
            //                // pass
            //            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Customer Added Successfully']);
        } catch (\Throwable $e) {
            Log::info('Error adding shop');
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function updateFromApi(Request $request): JsonResponse
    {
        $validations = Validator::make($request->all(), [
            'shop_id' => 'required',
            'name' => 'required',
            'phone_no' => 'required',
            'business_name' => 'required',
            'center_id' => 'required|exists:delivery_centres,id',
            'latitude' => 'required',
            'longitude' => 'required',
//            'customer_type' => 'required',
        ]);

        if ($validations->fails()) {
            return response()->json(['result' => 0, 'errors' => $validations->errors()], 422);
        }
        $customerWithPhoneNumber = WaRouteCustomer::latest()->where('phone', $request->phone_no)->whereIn('status',  ['verified', 'approved', 'duplicate'])->whereNot('id', $request->id)->first();
        if ($customerWithPhoneNumber) {
            $status = 'duplicate';
        } else {
            $status = 'verified';
        }

        DB::beginTransaction();
        try {
            $getUserData = JWTAuth::toUser($request->token);
            $new = WaRouteCustomer::find($request->shop_id);
            $new->created_by = $getUserData->id;
            $new->delivery_centres_id = $request->center_id;
            $new->kra_pin = $request->kra_pin;
            $new->name = strtoupper($request->name);
            $new->phone = $request->phone_no;
            $new->bussiness_name = strtoupper($request->business_name);
            $new->town = $request->town;
            $new->lat = $request->latitude;
            $new->lng = $request->longitude;
            $new->gender = $request->gender;
            $new->comment = $request->comment;
            $new->secondary_name = $request->secondary_name;
            $new->secondary_phone_no = $request->secondary_phone_no;
            $new->customer_type = $request->customer_type ?? null;
            $new->status = $status;

            if ($request->file('photo')) {
                $file = $request->file('photo');
                $fileName = time() . rand(111111111, 9999999999) . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('uploads/shops/') . $fileName;
                $file->move(public_path('uploads/shops/'), $fileName);
                ProcessShopImage::dispatch($filePath, $new->id);
            }
            // if (($request->is_dormant && ($request->is_dormant == 'true'))) {
            //     $new->status = 'dormant';
            // }
            $new->save();
            $route = $new->route;
            if ($new->has_valid_location && $route->has_valid_location) {
                //                GetShopDistanceEstimates::dispatch($new);
                //                GetShopRoutePolylines::dispatch($new);
                //                GetShopRouteSections::dispatch($new);
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Shop updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public function getUnverifiedShops(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => 'User token is required', 'errors' => $validation->errors()], 422);
        }

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['message' => 'A user matching the provided token was not found.'], 422);
            }

            $unverifiedShops = WaRouteCustomer::with('route')->where('status', 'unverified')->get()->map(function (WaRouteCustomer $shop) {
                $shop->display_status = ucfirst($shop->status);
                $shop->route_name = $shop->route->route_name;
                unset($shop->route);
                unset($shop->is_verified);
                unset($shop->created_by);
                unset($shop->center_id);

                return $shop;
            });

            return response()->json(['message' => 'Success', 'data' => $unverifiedShops]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'A server error was encountered.'], 500);
        }
    }

    public function unverifiedIndex()
    {
        if (!can('listing', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if (request()->wantsJson()) {
            $query = WaRouteCustomer::with([
                'route',
                'center'
            ])
                ->where('status', 'unverified')
                ->when(request()->filled('branch'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('restaurant_id', request()->branch);
                    });
                })
                ->when(request()->filled('route'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('id', request()->route);
                    });
                })
                ->when(request()->filled('center'), function ($query) {
                    $query->whereHas('center', function ($q) {
                        $q->where('id', request()->center);
                    });
                });

                if (!$isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
                    $query->whereHas('route', function ($q) use ($authuser) {
                        $q->where('restaurant_id', $authuser->userRestaurent->id);
                    }); 
                }

            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($customer) {
                    return $customer->created_at->format('D, M j, Y');
                })->editColumn('route.route_name', function ($customer) {
                    return $customer->route?->route_name;
                })->editColumn('center.name', function ($customer) {
                    return $customer->center?->name;
                })
                ->addColumn('actions', function ($customer) {
                    return view('admin.route_customers.actions.unverified', compact('customer'));
                })
                ->toJson();
        }

        // $branches = Restaurant::get();
        if ($isAdmin || isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::get();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        return view('admin.route_customers.unverified', [
            'title' => "$this->baseTitle - Verification Requests",
            'model' => 'route-customers-onboarding-requests',
            'breadcum' => [
                "$this->baseTitle - Verification Requests" => route("$this->baseRouteName.index"),
                'Verification Requests' => ''
            ],
            'branch' => $branches
        ]);
    }

    public function verifyShopFromApi(Request $request): JsonResponse
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required',
            'shop_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => 'Unsuccessful', 'errors' => $validation->errors()], 422);
        }

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['message' => 'A user matching the provided token was not found.'], 422);
            }

            $shop = WaRouteCustomer::find($request->shop_id);
            if (!$shop) {
                return response()->json(['message' => "A shop matching id $request->shop_id was not found."], 422);
            }

            if ($shop->status !== 'unverified') {
                return response()->json(['message' => "The shop matching id $request->shop_id is already verified."], 422);
            }

            $shop->update(['status' => 'verified']);
            $nowVerifiedShop = WaRouteCustomer::with('route')->find($request->shop_id);
            $nowVerifiedShop->display_status = ucfirst($nowVerifiedShop->status);
            $nowVerifiedShop->route_name = $nowVerifiedShop->route->route_name;

            unset($nowVerifiedShop->route);
            unset($nowVerifiedShop->is_verified);
            unset($nowVerifiedShop->created_by);
            unset($nowVerifiedShop->center_id);

            return response()->json(['message' => 'Success', 'data' => $nowVerifiedShop]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'A server error was encountered.'], 500);
        }
    }

    public function verifyShopFromWeb($id, Request $request): RedirectResponse
    {

        $validation = Validator::make($request->all(), [
            'source' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors());
        }

        try {
            $shop = WaRouteCustomer::find($id);
            $shop->update(['status' => 'verified']);

            // Redirect based on where the request originated from
            $redirectionRoute = "$this->baseRouteName.index";
            if ($request->source == 'onboarding_requests') {
                $redirectionRoute = "$this->baseRouteName.unverified";
            }

            return redirect()->route($redirectionRoute)->with('success', 'Route customer verified successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }
    public function verifyShopFromWebShow($id,): RedirectResponse
    {

        try {
            $shop = WaRouteCustomer::find($id);
            $shop->update(['status' => 'verified']);

            return redirect()->route('route-customers.unverified')->with('success', 'Route customer verified successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }

    public function rejectShopFromWeb($id, Request $request): RedirectResponse
    {

        $validation = Validator::make($request->all(), [
            // 'source' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors());
        }

        try {
            $shop = WaRouteCustomer::find($id);
            //  save logs
            UserLog::create([
                'user_id' => getLoggeduserProfile()->id,
                'user_name' => getLoggeduserProfile()->name,
                'module' => 'onboarding_customer',
                'activity' => "Rejected onboarding request  for  $shop->name",
                'user_ip' => $request->ip(),
                'entity_id' => $shop->id,
                'user_agent' => $request->header('User-Agent'),
            ]);

            //reject shops


            $shop->Delete();

            // Redirect based on where the request originated from
            $redirectionRoute = "$this->baseRouteName.index";
            if ($request->source == 'onboarding_requests') {
                $redirectionRoute = "$this->baseRouteName.unverified";
            }

            // return redirect()->route($redirectionRoute)->with('success', 'Route customer rejected successfully.');
            return redirect()->back()->with('success', 'Route customer rejected successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }
    public function rejectShopFromWebShow(Request $request, $id, $model): RedirectResponse
    {

        try {
            $shop = WaRouteCustomer::find($id);
            //send notification to salesman
            try {
                $route = Route::find($shop->route_id);
                $phone = $route->salesman() ? $route->salesman()->phone_number : '0729825703' ; //notify dev
                $name = $route->salesman() ? $route->salesman()->name : 'Jim';
                $message = "Dear $name, \n\n Your route customer $shop->bussiness_name has been rejected. \n\n Reason: $request->comment \n\n";
                $this->smsService->sendMessage($message, $phone);
            } catch (\Throwable $th) {
                //throw $th;
            }
          
            if($model == 'geomapping-schedules'){
                $schedule = GeomappingSchedules::latest()
                ->where('route_id', $shop->route_id)
                ->whereDate('date', $shop->updated_at)
                ->first();
            }
            //  save logs
            UserLog::create([
                'user_id' => getLoggeduserProfile()->id,
                'user_name' => getLoggeduserProfile()->name,
                'module' => 'onboarding_customer',
                'activity' => "Rejected onboarding request  for  $shop->name",
                'user_ip' => request()->ip(),
                'entity_id' => $shop->id,
                'user_agent' => request()->header('User-Agent'),
            ]);
            $shop->status = 'rejected';
            $shop->rejection_reason = $request->comment ?? ' ';
            $shop->rejected_by = Auth::user()->id;
            $shop->save();
            $shop->Delete();

            $redirectionRoute = "$this->baseRouteName.index";
            if ($model == 'route-customers-onboarding-requests') {
                $redirectionRoute = 'route-customers.unverified';
            } elseif($model == 'route-customers.approval-requests') {
                $redirectionRoute = 'route-customers.approval-requests';
            }else{
                return redirect()->route('geomapping-schedules.show', $schedule->id)->with('success', 'Route customer rejected successfully');
            }

            return redirect()->route($redirectionRoute)->with('success', 'Route customer rejected successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }


    public function verifyAll(): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $shops = WaRouteCustomer::where('status', 'unverified')->get();
            foreach ($shops as $shop) {
                $shop->update(['status' => 'verified']);
            }

            DB::commit();
            return redirect()->route("$this->baseRouteName.unverified")->with('success', 'Route customers verified successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }

    public function approvalRequestsView()
    {
        $title = "$this->baseTitle - Verification Requests";
        $permission = $this->mypermissionsforAModule();
        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        // return $userwithrestaurants;
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Approval Requests' => ''
        ];
        $route_customers = WaRouteCustomer::with(['route', 'route.users', 'center'])->where('status', 'verified')
            ->when(request()->filled('branch'), function ($query) {
                $query->whereHas('route.branch', function ($q) {
                    $q->where('id', request()->branch);
                });
            })
            ->when(request()->filled('route'), function ($query) {
                $query->whereHas('route', function ($q) {
                    $q->where('id', request()->route);
                });
            })
            ->when(request()->filled('centers'), function ($query) {
                $query->whereHas('center', function ($q) {
                    $q->where('id', request()->centers);
                });
            });
            // ->latest()->get();
            if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {   
                $route_customers->whereHas('route', function ($q) use ($authuser) {
                    $q->where('restaurant_id', $authuser->restaurant_id);
                }); 
            }

            $route_customers = $route_customers->get();

        // $branch = Restaurant::get();

        if ($isAdmin || isset($permission['employees' . '___view_all_branches_data'])) {
            $branch = Restaurant::all();
        } else {
            $branch = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        $routes = [];
        if (request()->filled('branch')) {
            $routes = Route::when(request()->has('branch'), function ($q) {
                $q->where('restaurant_id', request()->branch);
            })->get();
        }
        $centers = [];
        if (request()->filled('route')) {
            $centers = DeliveryCentres::when(request()->has('route'), function ($q) {
                $q->where('route_id', request()->route);
            })->get();
        }

        return view("$this->resourceFolder.onboarding_approval_requests", [
            'route_customers' => $route_customers,
            'model' => 'route-customers-approval-requests',
            'title' => $title,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
            'branch' => $branch,
            'routes' => $routes,
            'centers' => $centers
        ]);
    }

    public function approve($id, Request $request): RedirectResponse
    {
        $validation = Validator::make($request->all(), [
            'source' => 'required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors());
        }

        DB::beginTransaction();
        try {
            $shop = WaRouteCustomer::find($id);
            $this->approveShop($shop);

            // Redirect based on where the request originated from
            $redirectionRoute = "$this->baseRouteName.approval-requests";
            if ($request->source == 'approval_requests') {
                $redirectionRoute = "$this->baseRouteName.approval-requests";
            }

            DB::commit();
            return redirect()->route($redirectionRoute)->with('success', 'Route customer approved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }
    public function approveShow(Request $request, $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $shop = WaRouteCustomer::find($id);
            $this->approveShop($shop);


            DB::commit();
            SessionSuccess::flash('success', 'Route customer approved successfully.');
            if(isset($request->schedule_id)){
                return redirect()->route('geomapping-schedules.show', $request->schedule_id)->with('success', 'Route customer approved successfully.');
            }
            return redirect()->back()->with('success', 'Route customer approved successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }

    public function approveAll(): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $shops = WaRouteCustomer::where('status', 'verified')->get();
            foreach ($shops as $shop) {
                $this->approveShop($shop);
            }

            DB::commit();
            return redirect()->route("$this->baseRouteName.approval-requests")->with('success', 'Route customers approved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'A server error was encountered. Please try again.']);
        }
    }

    private function approveShop(WaRouteCustomer $shop)
    {
        $shop->update(['status' => 'approved']);

        // Add this shop to current salesman shift if they have an open one.
        $routeOpenShift = SalesmanShift::where('status', 'open')->where('route_id', $shop->route_id)->first();
        if ($routeOpenShift) {
            $routeOpenShift->shiftCustomers()->create([
                'route_customer_id' => $shop->id,
                'salesman_shift_type' => $routeOpenShift->shift_type,
            ]);
        }
    }

    public function getShopById(Request $request): JsonResponse
    {
        $payload = [
            'status' => true,
            'message' => 'success',
            'shop' => []
        ];

        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                $payload['status'] = false;
                $payload['message'] = 'Your session is invalid';
                return response()->json($payload, 422);
            }

            if (!$request->shop_id) {
                $payload['status'] = false;
                $payload['message'] = 'Shop id is required';
                return response()->json($payload, 422);
            }

            $shop = WaRouteCustomer::with('route')->find($request->shop_id);
            if (!$shop) {
                $payload['status'] = false;
                $payload['message'] = 'A shop with the provided id was not found';
                return response()->json($payload, 404);
            }

            $payload['shop'] = $this->getBasicShopInformation($shop, $user);
            return response()->json($payload);
        } catch (\Throwable $e) {
            $payload['status'] = false;
            $payload['message'] = $e->getMessage();
            $payload['trace'] = $e->getTrace();

            return response()->json($payload, 500);
        }
    }

    private function getBasicShopInformation(WaRouteCustomer $shop, $user): WaRouteCustomer
    {
        $shop->distance = '0 Km away';

        if ($shop->image_url) {
            $appUrl = env('APP_URL');
            $shop->photo = "$appUrl/uploads/shops/" . $shop->image_url;
        }

        $shop->route = $this->getRawRouteInformation($shop->route, $user);
        unset($shop->route->centers);
        unset($shop->route->sections);

        $shop->visited_by_salesman = false;
        $shop->visited_by_deliveryman = false;
        switch ($user->role_id) {
            case 4:
                $currentShift = SalesmanShift::with('shiftCustomers')->where('status', 'open')
                    ->where('salesman_id', $user->id)->first();
                if ($currentShift) {
                    $routeCustomer = $currentShift->shiftCustomers()->where('route_customer_id', $shop->id)->first();
                    if ($routeCustomer) {
                        $shop->visited_by_salesman = $routeCustomer->visited == 1;
                    }
                }

                break;

            case 6:
                $currentShift = DeliverySchedule::with('customers')->latest()->started()->forDriver($user->id)->first();
                if ($currentShift) {
                    $routeCustomer = $currentShift->customers()->where('customer_id', $shop->id)->first();
                    if ($routeCustomer) {
                        $shop->visited_by_deliveryman = $routeCustomer->visited == 1;
                    }
                }

                break;
            default:
                break;
        }

        return $shop;
    }

    public function getOnboardingReport()
    {
        $routeIds = [20, 78, 100, 77, 2, 85];
        $kaniniReps = [
            [
                'name' => 'KELVIN MAINA',
                'contact' => '0727591748'
            ],
            [
                'name' => 'SAMUEL MAINA',
                'contact' => '0714247455'
            ],
            [
                'name' => 'PETER KIMOTHO',
                'contact' => '0726765432'
            ],
            [
                'name' => 'ONESMUS NJENGA ',
                'contact' => '0798399787'
            ],
            [
                'name' => 'JOHN MBOGO',
                'contact' => '0719690718'
            ],
            [
                'name' => 'KEN ',
                'contact' => '0724156559'
            ],
        ];

        $data = [];
        foreach ($routeIds as $index => $routeId) {
            $route = Route::find($routeId);
            $row = [
                'route' => $route->route_name,
                'khel_rep' => $kaniniReps[$index]['name'],
                'khel_rep_contact' => $kaniniReps[$index]['contact'],
                'sales_rep' => $route->salesman()?->name,
                'contact' => $route->salesman()?->phone_number,
                'customer_count' => WaRouteCustomer::where('route_id', $route->id)->count(),
                'visited' => WaRouteCustomer::where('route_id', $route->id)->where('created_by', '!=', 0)->count(),
                'not_visited' => WaRouteCustomer::where('route_id', $route->id)->where('created_by', 0)->count(),
            ];

            $data[] = $row;
        }

        $today = Carbon::now()->toDateString();
        $report_name = "Onboarding-Report-$today";
        $pdf = PDF::loadView('admin.delivery_schedules.onboarding_report', compact('report_name', 'data'));
        return $pdf->download($report_name . '.pdf');
    }

    public function export()
    {
        $data = WaRouteCustomer::with(['route', 'center'])
            ->whereDate('created_at', '>', '2024-02-01')
            ->get()->map(function (WaRouteCustomer $shop) {
                $payload = [
                    'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                    'route' => $shop->route?->route_name,
                    'center' => $shop->center?->name,
                    'shop_name' => $shop->bussiness_name,
                    'owner' => $shop->name,
                    'phone_number' => $shop->phone,
                    'kra_pin' => $shop->kra_pin,
                    'mapped' => $shop->image_url ? 'YES' : 'NO',
                    'distance_from_khel' => round($shop->distance_estimate / 1000, 2),
                ];

                return $payload;
            })->sortBy('route');

        $export = new RouteCustomerExport($data);
        return Excel::download($export, 'NEW_CUSTOMERS.xlsx');
    }

    public function exportAll(Request $request)
    {
        $route = Route::find($request->route_id);
        $data = WaRouteCustomer::with(['route', 'center'])->where('route_id', $request->route_id)
            ->get()->map(function (WaRouteCustomer $shop) {
                $payload = [
                    'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                    'route' => $shop->route?->route_name,
                    'center' => $shop->center?->name,
                    'shop_name' => $shop->bussiness_name,
                    'owner' => $shop->name,
                    'phone_number' => $shop->phone,
                    'kra_pin' => $shop->kra_pin,
                    'mapped' => $shop->image_url ? 'YES' : 'NO',
                    'distance_from_khel' => round($shop->distance_estimate / 1000, 2),

                ];

                return $payload;
            })->sortBy('center');

        $export = new RouteCustomerExport($data);

        return Excel::download($export, "{$route->route_name} ALL.xlsx");
    }

    public function exportAllRouteCustomers()
    {
        $data = DB::table('wa_route_customers')
            ->select(
                'routes.route_name as route',
                'delivery_centres.name as centre',
                'wa_route_customers.bussiness_name as shop_name',
                'wa_route_customers.name as shop_owner',
                'wa_route_customers.phone as phone_number',
                'wa_route_customers.id',
            )
            ->join('routes', 'wa_route_customers.route_id', '=', 'routes.id')
            ->join('delivery_centres', 'wa_route_customers.delivery_centres_id', '=', 'delivery_centres.id')
            ->get()->map(function ($record) {
                $total = 0;
                $orders = WaInternalRequisition::where('wa_route_customer_id', $record->id)
                    ->whereBetween('created_at', [Carbon::parse('2024-03-06')->startOfDay(), Carbon::parse('2024-03-25')->endOfDay()])
                    ->get();
                foreach ($orders as $order) {
                    $total += $order->getFinalTotal();
                }

                $record->total = $total;
                unset($record->id);
                return $record;
            });

        $export = new RouteCustomerExport($data);

        return Excel::download($export, "CUSTOMER SALES REPORT 06-03 - 25-03.xlsx");
    }

    public function exportAllMapped(Request $request)
    {
        $route = Route::find($request->route_id);
        $data = WaRouteCustomer::with(['route', 'center'])
            ->where('created_by', '!=', 0)
            ->where('route_id', $request->route_id)
            ->get()->map(function (WaRouteCustomer $shop) {
                $payload = [
                    'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                    'route' => $shop->route?->route_name,
                    'center' => $shop->center?->name,
                    'shop_name' => $shop->bussiness_name,
                    'owner' => $shop->name,
                    'phone_number' => $shop->phone,
                    'distance_from_khel' => round($shop->distance_estimate / 1000, 2)
                ];

                return $payload;
            })->sortBy('route');

        $export = new RouteCustomerExport($data);

        return Excel::download($export, "{$route->route_name} CUSTOMERS.xlsx");
    }

    public function exportPhoneDuplicates(Request $request)
    {
        $duplicates = DB::table('wa_route_customers')
            ->where('route_id', $request->route_id)
            ->select('phone', DB::raw('COUNT(*) as `count`'))
            ->groupBy('phone')
            ->get();
        $data = [];
        foreach ($duplicates as $duplicate) {
            if ($duplicate->count > 1) {
                foreach (WaRouteCustomer::with(['route', 'center'])->where('phone', $duplicate->phone)->get() as $shop) {
                    $data[] = [
                        'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                        'route' => $shop->route?->route_name,
                        'center' => $shop->center?->name,
                        'shop_name' => $shop->bussiness_name,
                        'owner' => $shop->name,
                        'phone_number' => $shop->phone,
                        'kra_pin' => $shop->kra_pin,

                    ];
                }
            }
        }

        $export = new RouteCustomerExport(collect($data));
        return Excel::download($export, 'PHONE_DUPLICATES.xlsx');
    }

    public function exportNameDuplicates(Request $request)
    {
        $duplicates = DB::table('wa_route_customers')
            ->where('route_id', $request->route_id)
            ->select('bussiness_name', DB::raw('COUNT(*) as `count`'))
            ->groupBy('bussiness_name')
            ->get();
        $data = [];
        foreach ($duplicates as $duplicate) {
            if ($duplicate->count > 1) {
                foreach (WaRouteCustomer::with(['route', 'center'])->where('bussiness_name', $duplicate->bussiness_name)->get() as $shop) {
                    $data[] = [
                        'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                        'route' => $shop->route?->route_name,
                        'center' => $shop->center?->name,
                        'shop_name' => $shop->bussiness_name,
                        'owner' => $shop->name,
                        'phone_number' => $shop->phone,
                        'kra_pin' => $shop->kra_pin,

                    ];
                }
            }
        }

        $export = new RouteCustomerExport(collect($data));
        return Excel::download($export, 'NAME_DUPLICATES.xlsx');
    }

    public function exportNewCustomers(Request $request)
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $data = WaRouteCustomer::with(['route', 'center']);
        // ->whereDate('created_at', '>', '2024-02-01')

        if ($fromDate) {
            $data->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $data->whereDate('created_at', '<=', $toDate);
        }

        $data = $data->get()->map(function (WaRouteCustomer $shop) {
            $payload = [
                'date' => Carbon::parse($shop->created_at)->toFormattedDayDateString(),
                'route' => $shop->route?->route_name,
                'center' => $shop->center?->name,
                'shop_name' => $shop->bussiness_name,
                'owner' => $shop->name,
                'phone_number' => $shop->phone,
                'kra_pin' => $shop->kra_pin,
                'mapped' => $shop->image_url ? 'YES' : 'NO',
                'distance_from_khel' => round($shop->distance_estimate / 1000, 2),

            ];

            return $payload;
        })->sortBy('route');

        $export = new RouteCustomerExport($data);
        return Excel::download($export, 'NEW_CUSTOMERS.xlsx');
    }

    public function updateEstimates()
    {
        ini_set('max_execution_time', 600);

        // Estimates
        $shops = WaRouteCustomer::with('route')->get();
        foreach ($shops as $key => $shop) {
            $route = $shop->route;
            $startLat = $route->start_lat;
            $startLng = $route->start_lng;
            $endLat = $shop->lat;
            $endLng = $shop->lng;
            $shop->update([
                'distance_estimate' => MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng)
            ]);
        }

        // Polylines
        foreach (Route::with('polylines')->get() as $route) {
            $route->polylines()->delete();
            $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get();

            $numberOfWaypointGroups = ceil($shops->count() / 25);

            $skip = 0;
            $waypointGroups = [];
            for ($counter = 1; $counter <= $numberOfWaypointGroups; $counter++) {
                $waypointGroups[] = $shops->skip($skip)->take(25);
                $skip += 25;
            }

            $startLat = $route->start_lat;
            $startLng = $route->start_lng;
            foreach ($waypointGroups as $group) {
                $lastShop = $group->last();
                $group->pop();
                $waypoints = [];
                foreach ($group as $shop) {
                    $waypoints[] = [
                        "location" => [
                            "latLng" => [
                                "latitude" => $shop->lat,
                                "longitude" => $shop->lng
                            ]
                        ],
                        "vehicleStopover" => true
                    ];
                }

                $response = MappingService::getRoute($startLat, $startLng, $lastShop->lat, $lastShop->lng, waypoints: $waypoints);
                if (isset($response['routes'][0]['polyline']['encodedPolyline'])) {
                    $polyline = $route->polylines()->create([
                        'polyline' => $response['routes'][0]['polyline']['encodedPolyline'],
                        'lat_lngs' => json_encode(MappingService::decodePolyline($response['routes'][0]['polyline']['encodedPolyline'])),
                    ]);

                    if (isset($response['routes'][0]['optimizedIntermediateWaypointIndex'])) {
                        $polyline->update(['waypoint_order' => implode(",", $response['routes'][0]['optimizedIntermediateWaypointIndex'])]);
                    }
                }

                $startLat = $lastShop->lat;
                $startLng = $lastShop->lng;
            }
        }

        // Sections
        foreach (Route::with('sections')->get() as $route) {
            $route->sections()->delete();
            $shops = $route->waRouteCustomer()->orderBy('distance_estimate')->get()
                ->filter(function ($shop) {
                    return $shop->has_valid_location;
                })->map(function (WaRouteCustomer $shop) use ($route) {
                    return [
                        'id' => $shop->id,
                        'lat' => (float)$shop->lat,
                        'lng' => (float)$shop->lng,
                        'name' => $shop->bussiness_name,
                    ];
                });

            $lastShop = null;
            for ($counter = 0; $counter < count($shops); $counter++) {
                $currentShop = $shops[$counter];
                $startLat = $counter == 0 ? $route->start_lat : $lastShop['lat'];
                $startLng = $counter == 0 ? $route->start_lng : $lastShop['lng'];
                $endLat = $currentShop['lat'];
                $endLng = $currentShop['lng'];

                $distanceEstimate = MappingService::getDistanceBetweenPoints($startLat, $startLng, $endLat, $endLng);
                $timeEstimate = MappingService::getDurationBetweenPoints($startLat, $startLng, $endLat, $endLng);
                $route->sections()->create([
                    'start_shop_id' => $counter == 0 ? null : $lastShop['id'],
                    'start_lat' => $startLat,
                    'start_lng' => $startLng,
                    'end_shop_id' => $currentShop['id'],
                    'end_lat' => $endLat,
                    'end_lng' => $endLng,
                    'start_point_is_plan_start_point' => $counter == 0,
                    'fuel_estimate' => 0,
                    'road_type' => null,
                    'road_condition' => null,
                    'rainy_fuel_estimate' => 0,
                    'rainy_road_type' => null,
                    'rainy_road_condition' => null,
                    'distance_estimate' => $distanceEstimate,
                    'rainy_distance_estimate' => $distanceEstimate,
                    'time_estimate' => $timeEstimate,
                    'rainy_time_estimate' => $timeEstimate,
                ]);

                $lastShop = $currentShop;
            }
        }

        return "success";
    }

    public function customerComments(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $title = "$this->baseTitle - Comments";
        $breadcum = [
            $title => route("$this->baseRouteName.index"),
            'Customer Comments' => ''
        ];

        $startDate = $request->start_date ?? now()->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay()  ?? now();
        // $branches = $this->getRestaurantList();
        $branch = $request->branch;
        $route_id = $request->route_id;
        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;
        if ($isAdmin || isset($permission['employees___view_all_branches_data'])) {
            $branches = $this->getRestaurantList();
        } else {
            $branches = $this->getRestaurantList()->filter(function($branch, $branchid) use ($authuser) {
                return $branchid == $authuser->userRestaurent->id;
            });
        }

        if (request()->wantsJson()) {
            $query = WaRouteCustomer::with(['route', 'route.users', 'center','route.branch'])
                ->whereNotNull('comment')
                ->when($route_id, function ($q) use ($route_id){
                    $q->where('route_id', $route_id);
                })
                ->when($branch, function ($q) use ($branch) {
                    $q->whereHas('route', function ($k) use ($branch) {
                        $k->where('restaurant_id', $branch);
                    });
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->orderByDesc('updated_at');

                if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
                    $query->whereHas('route', function ($q) use ($authuser) {
                        $q->where('restaurant_id', $authuser->userRestaurent->id);
                    }); 
                }

            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s'); // Customize format as needed
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('route-customers.show', $row->id) . '" title="View Route Customer">
                    <i class="fa fa-eye text-info fa-lg"></i></a>';
                })
                ->addIndexColumn()
                ->toJson();
        }
        return view('admin.route_customers.comments', [
            'model' => 'route-customers-customer-comments',
            'title' => $title,
            'branches' => $branches,
            'branch' => $branch,
            'breadcum' => $breadcum,
            'base_route_name' => $this->baseRouteName,
        ]);
    }
    
    public function rejectedCustomers()
    {
        if (!can('rejected-customers', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $authuser = Auth::user();
        $isAdmin = $authuser->role_id == 1;

        if (request()->wantsJson()) {
            $query = WaRouteCustomer::onlyTrashed()->with([
                'route',
                'center'
            ])
                ->when(request()->filled('branch'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('restaurant_id', request()->branch);
                    });
                })
                ->when(request()->filled('route'), function ($query) {
                    $query->whereHas('route', function ($q) {
                        $q->where('id', request()->route);
                    });
                })
                ->when(request()->filled('center'), function ($query) {
                    $query->whereHas('center', function ($q) {
                        $q->where('id', request()->center);
                    });
                });

                if (!$isAdmin && !isset($permission['employees___view_all_branches_data'])) {
                    $query->whereHas('route', function ($q) use ($authuser) {
                        $q->where('restaurant_id', $authuser->userRestaurent->id);
                    }); 
                }

            return DataTables::eloquent($query)
                ->editColumn('created_at', function ($customer) {
                    return $customer->created_at->format('D, M j, Y');
                })->editColumn('route.route_name', function ($customer) {
                    return $customer->route?->route_name;
                })->editColumn('center.name', function ($customer) {
                    return $customer->center?->name;
                })
                // ->addColumn('actions', function ($customer) {
                //     return view('admin.route_customers.actions.list', compact('customer'));
                // })
                ->toJson();
        }

        // $branches = Restaurant::get();
        if ($isAdmin) {
            $branches = Restaurant::get();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        return view('admin.route_customers.rejected', [
            'title' => $this->baseTitle,
            'model' => 'rejected-customers',
            'breadcum' => [
                $this->baseTitle => route("$this->baseRouteName.index"),
                'Listing' => ''
            ],
            'branch' => $branches
        ]);
    }
}
