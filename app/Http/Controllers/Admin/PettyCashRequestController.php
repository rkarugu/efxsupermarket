<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\User;
use App\Model\Route;
use App\Model\WaGlTran;
use App\Model\Restaurant;
use App\Model\TaxManager;
use App\Model\WaBanktran;
use App\Model\WaBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Model\WaChartsOfAccount;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Models\WaPettyCashRequest;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\WaPettyCashRequestItem;
use App\Models\WaPettyCashRequestType;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\WaPettyCashRequestItemFile;
use App\Models\WaPettyCashRequestItemWithdrawal;

class PettyCashRequestController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'petty-cash-requests';
        $this->title = 'Petty Cash Requests';
        $this->pmodule = 'petty-cash-requests';
    }

    public function showTypesPage()
    {
        if (can('view', 'petty-cash-request-types')) {
            $title = $this->title;
            $model = 'petty-cash-request-types';
            $breadcum = ['Petty Cash Requests' => '', 'Types' => ''];

            $user = Auth::user();

            return view('admin.petty-cash-requests.types', compact('title', 'model', 'breadcum', 'user'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showCreatePage()
    {
        if (can('create', 'petty-cash-requests-request')) {
            $title = $this->title;
            $model = $this->model;
            $breadcum = ['Petty Cash Requests' => '', 'Create' => ''];

            $user = Auth::user();

            return view('admin.petty-cash-requests.create', compact('title', 'model', 'breadcum', 'user'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showInitialApprovalPage(Request $request)
    {
        if (can('view', $this->pmodule)) {
            $title = $this->title;
            $model = 'petty-cash-requests-initial-approval';
            $breadcum = ['Petty Cash Requests' => '', 'Initial Approval' => ''];

            $user = Auth::user();
            $page = 'Initial';

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $pettyCashRequests = WaPettyCashRequest::with([
                'restaurant', 
                'department', 
                'chartOfAccount', 
                'createdBy', 
                'pettyCashType', 
                'pettyCashRequestItems.route',
                'pettyCashRequestItems.grn',
                'pettyCashRequestItems.transfer',
            ])
                ->withSum('pettyCashRequestItems as total_amount', 'amount')
                ->whereIn('type', $pettyCashTypes->pluck('slug')->toArray())
                ->where('initial_approval', false)
                ->where('rejected', false)
                ->when($request->start_date && $request->end_date, fn($query) => $query->whereBetween('created_at', [$start, $end]))
                ->when($request->branch, fn($query) => $query->where('restaurant_id', $request->branch))
                ->when($request->type, fn($query) => $query->where('type', $request->type))
                ->get();

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.listing', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequests', 'page', 'branches', 'pettyCashTypes'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showInitialApprovalApprovePage($id)
    {
        if (can('approve', 'petty-cash-requests-initial-approval')) {
            $pettyCashRequest = WaPettyCashRequest::with(
                'restaurant',
                'department',
                'chartOfAccount',
                'pettyCashRequestItems.employee',
                'pettyCashRequestItems.supplier',
                'pettyCashRequestItems.deliverySchedule',
                'pettyCashRequestItems.grn',
                'pettyCashRequestItems.transfer',
                'pettyCashRequestItems.pettyCashRequestItemFiles'
            )
                ->find($id);

            if (!$pettyCashRequest || $pettyCashRequest->initial_approval || $pettyCashRequest->rejected) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }

            $title = $this->title;
            $model = 'petty-cash-requests-initial-approval';
            $breadcum = ['Petty Cash Requests' => '', 'Initial Approval' => route('petty-cash-request.initial-approval'), 'Approve' => ''];
            $page = "Initial";

            $user = Auth::user();
            $redirectRoute = '"' . route('petty-cash-request.initial-approval') . '"';

            return view('admin.petty-cash-requests.edit', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequest', 'redirectRoute', 'page'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showFinalApprovalPage(Request $request)
    {
        $pmodule = 'petty-cash-requests-final-approval';
        if (can('view', $pmodule)) {
            $title = $this->title;
            $model = 'petty-cash-requests-final-approval';
            $breadcum = ['Petty Cash Requests' => '', 'Final Approval' => ''];

            $user = Auth::user();
            $page = 'Final';

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $pettyCashRequests = WaPettyCashRequest::with([
                'restaurant', 
                'department', 
                'chartOfAccount', 
                'createdBy', 
                'pettyCashType', 
                'pettyCashRequestItems.route',
                'pettyCashRequestItems.grn',
                'pettyCashRequestItems.transfer',
            ])
                ->withSum('pettyCashRequestItems as total_amount', 'amount')
                ->whereIn('type', $pettyCashTypes->pluck('slug')->toArray())
                ->where('initial_approval', true)
                ->where('final_approval', false)
                ->where('rejected', false)
                ->when($request->start_date && $request->end_date, fn($query) => $query->whereBetween('created_at', [$start, $end]))
                ->when($request->branch, fn($query) => $query->where('restaurant_id', $request->branch))
                ->when($request->type, fn($query) => $query->where('type', $request->type))
                ->get();

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.listing', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequests', 'page', 'branches', 'pettyCashTypes'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showFinalApprovalApprovePage($id)
    {
        if (can('approve', 'petty-cash-requests-final-approval')) {
            $pettyCashRequest = WaPettyCashRequest::with(
                'restaurant',
                'department',
                'chartOfAccount',
                'pettyCashRequestItems.employee',
                'pettyCashRequestItems.supplier',
                'pettyCashRequestItems.deliverySchedule',
                'pettyCashRequestItems.pettyCashRequestItemFiles'
            )->find($id);

            if (!$pettyCashRequest || !$pettyCashRequest->initial_approval || $pettyCashRequest->final_approval || $pettyCashRequest->rejected) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }

            $title = $this->title;
            $model = 'petty-cash-requests-final-approval';
            $breadcum = ['Petty Cash Requests' => '', 'Initial Approval' => route('petty-cash-request.final-approval'), 'Approve' => ''];
            $page = "Final";

            $user = Auth::user();
            $redirectRoute = '"' . route('petty-cash-request.final-approval') . '"';

            return view('admin.petty-cash-requests.edit', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequest', 'redirectRoute', 'page'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showProcessedRequestsPage(Request $request)
    {
        if (can('view', 'petty-cash-requests-processed')) {
            $title = $this->title;
            $model = 'petty-cash-requests-processed';
            $breadcum = ['Petty Cash Requests' => '', 'Processed Requests' => ''];

            $user = Auth::user();

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $requests = WaPettyCashRequestItem::with([
                'pettyCashRequest' => fn($query) => $query->with('restaurant', 'chartOfAccount', 'pettyCashType'),
                'grn',
                'transfer',
            ])
                ->whereHas('pettyCashRequest', fn($query) => $query->whereIn('type', $pettyCashTypes->pluck('slug')->toArray()))
                ->whereHas('latestWithdrawal', function ($query) {
                    $query->where('call_back_status', 'complete');
                })
                ->when($request->branch, fn($query) => $query->whereHas('pettyCashRequest', fn($query) => $query->where('restaurant_id', $request->branch)))
                ->when($request->type, fn($query) => $query->whereHas('pettyCashRequest', fn($query) => $query->where('type', $request->type)))
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.processed', compact('title', 'model', 'breadcum', 'user', 'requests', 'branches', 'pettyCashTypes'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showProcessedRequestDetailsPage($pettyCashNo)
    {
        if (can('view', 'petty-cash-requests-processed')) {
            $title = $this->title;
            $model = 'petty-cash-requests-processed';
            $breadcum = ['Petty Cash Requests' => '', 'Processed Requests' => route('petty-cash-request.processed'), $pettyCashNo => ''];

            $user = Auth::user();

            $page = 'Approved';

            $pettyCashRequest = WaPettyCashRequest::with([
                'restaurant',
                'department',
                'createdBy',
                'finalApprover',
                'chartOfAccount',
                'initialApprover',
                'finalApprover',
                'vehicle',
                'pettyCashType',
                'pettyCashRequestItems' => fn($query) => $query->with([
                    'taxManger',
                    'route',
                    'grn',
                    'transfer',
                    'deliverySchedule',
                    'pettyCashRequestItemFiles'
                ])
            ])
                ->where('petty_cash_no', $pettyCashNo)
                ->where('final_approval', true)
                ->where('rejected', false)
                ->first();

            if (!$pettyCashRequest) {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }

            return view('admin.petty-cash-requests.request-details', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequest', 'page'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showFailedRequestsPage(Request $request)
    {
        if (can('view', 'petty-cash-requests-failed')) {
            $title = $this->title;
            $model = 'petty-cash-requests-failed';
            $breadcum = ['Petty Cash Requests' => '', 'Failed Requests' => ''];

            $user = Auth::user();

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes(Auth::user());

            $failedRequestItems = $this->getFailedRequestItems($start, $end, $request->branch, $request->type, $pettyCashTypes);

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.failed', compact('title', 'model', 'breadcum', 'user', 'failedRequestItems', 'branches', 'pettyCashTypes'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function failedRequestsBatchAction(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        try {
            if (isset($request->expunge)) {
                $totalExpunged = 0;
            
                DB::beginTransaction();
                try {
                    foreach ($ids as $id) {
                        if (($request->get("resend_$id") == 'on')) {
                            $requestItem = WaPettyCashRequestItem::find($id);
        
                            if ($requestItem) {
                                $requestItem->update([
                                    'expunged' => true,
                                    'expunged_by' => $request->user()->id,
                                    'expunged_at' => now()
                                ]);
        
                                $totalExpunged++;
                            }
                        }
                    }
    
                    DB::commit();

                    Session::flash('success', "$totalExpunged failed requests expunged");

                } catch (\Exception $e) {
                    DB::rollback();
                    
                    Session::flash('danger', $e->getMessage());
                }

            } else {
                $ids = json_decode($request->ids, true);
    
                $pfTokenPayload = [
                    'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                    'secret' => env('PESAFLOW_B2C_AUTH_SECRET'),
                ];
                $tokenUrl = env('PESAFLOW_B2C_URL') . '/oauth/generate/token';
    
                $apiResponse = Http::post($tokenUrl, $pfTokenPayload);
                if (!$apiResponse->ok()) {
                    Session::flash('error', 'PF Token error');
    
                    return redirect()->back();
                }
    
                $token = $apiResponse->json()['token'];
                $statusUrl = env('PESAFLOW_B2C_URL') . '/payment/withdrawal/status';
    
                $total = 0;
                $updatedCount = 0;
                $initiatedCount = 0;
                foreach ($ids as $id) {
                    if (($request->get("resend_$id") == 'on')) {
                        $total += 1;
    
                        $requestItem = WaPettyCashRequestItem::with('latestWithdrawal')->find($id);
                        $transactionReference = $requestItem->latestWithdrawal->reference;
    
                        try {
                            $statusPayload = [
                                'trx_ref' => $transactionReference
                            ];
    
                            $response = Http::withToken($token)->post($statusUrl, $statusPayload);
                            if ($response->ok()) {
                                $responsePayload = json_decode($response->body(), true);
    
                                $status = $responsePayload['status'];
                                
                                $updated = $this->processTransactionFromCallback($requestItem->latestWithdrawal, $status);
                                
                                if ($updated) {
                                    $updatedCount++;
                                }
    
                                if ($status != 'complete') {
                                    $this->processRequestItem($requestItem);
                                    $initiatedCount += 1;
                                }
    
                            } else {
                                Log::info("Failed for $transactionReference with " . $response->body());
                            }
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    }
                }
    
                Session::flash('success', "$updatedCount requests updated, $initiatedCount requests resent.");
            }

        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
        }

        return redirect()->back();
    }

    public function showRequestRejectedPage(Request $request)
    {
        if (can('view', 'petty-cash-requests-rejected')) {
            $title = $this->title;
            $model = 'petty-cash-requests-rejected';
            $breadcum = ['Petty Cash Requests' => '', 'Rejected' => ''];

            $user = Auth::user();

            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $pettyCashRequests = WaPettyCashRequest::with([
                'restaurant', 
                'department', 
                'chartOfAccount', 
                'rejectedBy', 
                'pettyCashType', 
                'pettyCashRequestItems.route',
                'pettyCashRequestItems.grn',
                'pettyCashRequestItems.transfer',
            ])
                ->withSum('pettyCashRequestItems as total_amount', 'amount')
                ->whereIn('type', $pettyCashTypes->pluck('slug')->toArray())
                ->where('rejected', true)
                ->when($request->start_date && $request->end_date, fn($query) => $query->whereBetween('rejected_date', [$start, $end]))
                ->when($request->branch, fn($query) => $query->where('restaurant_id', $request->branch))
                ->when($request->type, fn($query) => $query->where('type', $request->type))
                ->get();

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.rejected', compact('title', 'model', 'breadcum', 'pettyCashRequests', 'branches', 'pettyCashTypes'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showRejectedRequestDetailsPage($pettyCashNo)
    {
        if (can('view', 'petty-cash-requests-rejected')) {
            $title = $this->title;
            $model = 'petty-cash-requests-rejected';
            $breadcum = ['Petty Cash Requests' => '', 'Rejected Requests' => route('petty-cash-request.rejected'), $pettyCashNo => ''];

            $user = Auth::user();

            $page = 'Rejected';

            $pettyCashRequest = WaPettyCashRequest::with([
                'restaurant',
                'department',
                'createdBy',
                'rejectedBy',
                'chartOfAccount',
                'initialApprover',
                'finalApprover',
                'vehicle',
                'pettyCashType',
                'pettyCashRequestItems' => fn($query) => $query->with([
                    'taxManger',
                    'route',
                    'grn',
                    'transfer',
                    'deliverySchedule',
                    'pettyCashRequestItemFiles'
                ])
            ])
                ->where('petty_cash_no', $pettyCashNo)
                ->where('rejected', true)
                ->first();

            if (!$pettyCashRequest) {
                Session::flash('warning', 'Invalid request');
                return redirect()->back();
            }

            return view('admin.petty-cash-requests.request-details', compact('title', 'model', 'breadcum', 'user', 'pettyCashRequest', 'page'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showRequestExpungedPage(Request $request)
    {
        if (can('view', 'petty-cash-requests-expunged')) {
            $title = $this->title;
            $model = 'petty-cash-requests-expunged';
            $breadcum = ['Petty Cash Requests' => '', 'Expunged' => ''];

            $user = Auth::user();

            $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
            $end = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $pettyCashRequestItems = WaPettyCashRequestItem::withoutGlobalScope('expunged')
                ->with('pettyCashRequest')
                ->whereHas('pettyCashRequest', function ($query) use ($request, $pettyCashTypes) {
                    $query->whereIn('type', $pettyCashTypes->pluck('slug')->toArray())
                        ->when($request->type, fn ($query) => $query->where('type', $request->type))
                        ->when($request->branch, fn ($query) => $query->where('restaurant_id', $request->branch));
                })
                ->where('expunged', true)
                ->when($start && $end, fn ($query) => $query->whereBetween('expunged_at', [$start, $end]))
                ->get();

            $branches = Restaurant::all();

            return view('admin.petty-cash-requests.expunged', compact('title', 'model', 'breadcum', 'pettyCashRequestItems', 'branches', 'pettyCashTypes'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    public function showRequestLogsPage(Request $request)
    {
        if (can('view', 'petty-cash-requests-logs')) {
            
            $title = $this->title;
            $model = 'petty-cash-requests-logs';
            $breadcum = ['Petty Cash Requests' => '', 'Logs' => ''];

            $user = Auth::user();

            $startDate = ($request->start_date ? Carbon::parse($request->start_date) : now()->subWeek())->startOfDay();
            $endDate = ($request->end_date ? Carbon::parse($request->end_date) : now())->endOfDay();

            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes($user);

            $requests = WaPettyCashRequest::with(['initialApprover', 'finalApprover', 'pettyCashType'])
                ->withCount([
                    'pettyCashRequestItems as request_items_count',
                    'pettyCashRequestItems as successful_payments' => fn($query) => $query->whereHas('latestWithdrawal', fn($query) => $query->where('call_back_status', 'complete')),
                    'pettyCashRequestItems as failed_payments' => fn($query) => $query->whereHas('latestWithdrawal', fn($query) => $query->whereNot('call_back_status', 'complete')),
                ])
                ->withSum('pettyCashRequestItems as total_amount', 'amount')
                ->withSum(
                    ['pettyCashRequestItems as successful_amount' => fn($query) => $query->whereHas('latestWithdrawal', fn($query) => $query->where('call_back_status', 'complete'))],
                    'amount'
                )
                ->withSum(
                    ['pettyCashRequestItems as failed_amount' => fn($query) => $query->whereHas('latestWithdrawal', fn($query) => $query->whereNot('call_back_status', 'complete'))],
                    'amount'
                )
                ->whereIn('type', $pettyCashTypes->pluck('slug')->toArray())
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($request->type, fn($query) => $query->where('type', $request->type))
                ->get();

            $dates = [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')];

            if ($request->export) {
                $headings = [
                    'Petty Cash Type',
                    'Initiated Date',
                    'Initiated By',
                    'Approved Date',
                    'Approved By',
                    'Total Payees',
                    'Total Amount',
                    'Failed Payments',
                    'Failed Amount',
                    'Successful Payments',
                    'Disbursed Amount',
                ];

                $data = [];
                foreach ($requests as $request) {
                    $data[] = [
                        $request->pettyCashType?->name,
                        $request->initial_approval_date,
                        $request->initialApprover?->name,
                        $request->final_approval_date,
                        $request->finalApprover?->name,
                        $request->request_items_count,
                        number_format($request->total_amount, 2),
                        $request->failed_payments,
                        number_format($request->failed_amount, 2),
                        $request->successful_payments,
                        number_format($request->successful_amount, 2),
                    ];
                }

                $data[] = [
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Totals',
                    number_format($requests->sum('total_amount'), 2),
                    '',
                    '',
                    '',
                    number_format($requests->sum('successful_amount'), 2)
                ];

                $filename = 'petty_cash_requests_log_' . date('Y_m_d_H_i_A');

                return ExcelDownloadService::download($filename, collect($data), $headings);

            } else {
                return view('admin.petty-cash-requests.logs', compact('title', 'model', 'breadcum', 'user', 'dates', 'requests', 'pettyCashTypes'));
            }

        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function showRequestLogTransactionsPage(Request $request, $id)
    {
        if (can('view', 'petty-cash-requests-logs')) {
            $title = $this->title;
            $model = 'petty-cash-requests-logs';
            $breadcum = ['Petty Cash Requests' => '', 'Logs' => route('petty-cash-request.logs'), 'Transactions' => ''];

            $requestItems = WaPettyCashRequestItem::with('pettyCashRequest', 'latestWithdrawal')
                ->where('wa_petty_cash_request_id', $id)
                ->get();

            return view('admin.petty-cash-requests.log-transactions', compact('title', 'model', 'breadcum', 'requestItems'));
        } else {
            Session::flash('warning', 'Access Denied');
            return redirect()->back();
        }
    }

    public function pettyCashRequestCreate(Request $request)
    {
        DB::beginTransaction();
        try {
            $restrictedTypes = ['parking-fees'];

            if (in_array($request->type, $restrictedTypes)) {
                $employeeIds = [];
                foreach ($request->lineItems as $lineItem) {
                    $lineItem = json_decode($lineItem);
                    if ($lineItem->employee_id) {
                        array_push($employeeIds, $lineItem->employee_id);
                    }
                }

                $requestsCount = WaPettyCashRequest::whereIn('type', $restrictedTypes)
                    ->whereDate('created_at', Carbon::today())
                    ->where('rejected', false)
                    ->whereHas('pettyCashRequestItems', fn($query) => $query->whereIn('employee_id', $employeeIds))
                    ->count();

                if ($requestsCount) {
                    return response()->json([
                        'message' => 'Employee has already made a similar request today'
                    ], 422);
                }
            }

            $series = getCodeWithNumberSeries('PETTY_CASH');

            $pettyCashRequest = WaPettyCashRequest::create([
                'restaurant_id' => $request->branch_id,
                'wa_department_id' => $request->department_id,
                'created_by' => $request->user_id,
                'wa_charts_of_account_id' => $request->account_id,
                'petty_cash_no' => $series,
                'type' => $request->type,
                'vehicle_id' => $request->vehicle_id,
                'repair_type' => $request->repair_type,
                'tax_type' => $request->tax_type,
            ]);

            updateUniqueNumberSeries('PETTY_CASH', $series);

            foreach ($request->lineItems as $i => $lineItem) {
                $lineItem = json_decode($lineItem);

                $pettyCashRequestItem = $pettyCashRequest->pettyCashRequestItems()->create([
                    'tax_manager_id' => $lineItem->vat_id ?: null,
                    'grn_number' => $lineItem->grn_number ?: null,
                    'transfer_id' => $lineItem->transfer_id ?: null,
                    'delivery_schedule_id' => $lineItem->delivery_schedule_id ?: null,
                    'route_id' => $lineItem->route_id ?: null,
                    'employee_id' => $lineItem->employee_id ?: null,
                    'supplier_id' => $lineItem->supplier_id ?: null,
                    'payee_name' => $lineItem->payee_name,
                    'payee_phone_no' => $lineItem->phone_number,
                    'amount' => str_replace(',', '', $lineItem->amount),
                    'tax_value' => $lineItem->vat_id ? TaxManager::find($lineItem->vat_id)->tax_value : null,
                    'vat_amount' => $lineItem->vat_amount,
                    'sub_total' => $lineItem->sub_total,
                    'cu_invoice_no' => $lineItem->cu_invoice_no,
                    'payment_reason' => $lineItem->payment_reason,
                ]);

                if (!empty($request->allFiles())) {
                    foreach ($request->file("files$i") as $file) {
                        $path = $file->store('uploads/petty_cash_request_files', 'public');

                        $pettyCashRequestItem->pettyCashRequestItemFiles()->create([
                            'path' => $path
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Petty cash request created successfully'
        ]);
    }

    public function pettyCashRequestSave(Request $request)
    {
        DB::beginTransaction();
        try {

            $pettyCashRequest = WaPettyCashRequest::find($request->id);

            $pettyCashRequest->update([
                'wa_charts_of_account_id' => $request->account_id,
                'type' => $request->type,
                'vehicle_id' => $request->vehicle_id,
                'repair_type' => $request->repair_type,
                'tax_type' => $request->tax_type,
            ]);

            $this->editLineItems($request, $pettyCashRequest);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Petty cash request saved successfully'
        ]);
    }

    public function pettyCashRequestReject(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:wa_petty_cash_requests,id',
            'stage' => 'required|in:initial,final'
        ]);

        $pettyCashRequest = WaPettyCashRequest::find($request->id);

        try {
            $pettyCashRequest->update([
                'rejected' => true,
                'rejected_stage' => $request->stage,
                'rejected_by' => $request->user()->id,
                'rejected_date' => now()
            ]);

            return response()->json([
                'message' => 'Request rejected successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pettyCashRequestBatchApprove(Request $request)
    {
        $request->validate([
            'requestIds' => 'required|array',
            'stage' => 'required|in:initial,final'
        ]);

        Db::beginTransaction();
        try {
            if ($request->stage == 'initial') {
                WaPettyCashRequest::whereIn('id', $request->requestIds)
                    ->update([
                        'initial_approval' => true,
                        'initial_approver' => $request->user()->id,
                        'initial_approval_date' => now()
                    ]);

                DB::commit();

                return response()->json([
                    'message' => 'Requests approved successfully'
                ]);
            } else if ($request->stage == 'final') {
                $totalRequestItems = 0;
                $totalInitiated = 0;
                foreach ($request->requestIds as $id) {
                    $initiated = 0;
                    $pettyCashRequest = WaPettyCashRequest::with('pettyCashRequestItems')->find($id);

                    $pettyCashRequestItems = $pettyCashRequest->pettyCashRequestItems;
                    $totalRequestItems += $pettyCashRequestItems->count();
                    foreach ($pettyCashRequestItems as $requestItem) {
                        try {
                            $this->processRequestItem($requestItem);

                            $initiated += 1;
                            $totalInitiated += 1;
                        } catch (Exception $e) {
                            Log::info("Failed for $requestItem->id: " . $e->getMessage());
                        }
                    }

                    if ($initiated != 0) {
                        $pettyCashRequest->update([
                            'final_approval' => true,
                            'final_approver' => $request->user()->id,
                            'final_approval_date' => now()
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'message' => "Petty cash request approved successfully. $totalInitiated out of $totalRequestItems deposits have been initiated."
                ]);
            }
        } catch (Exception $e) {
            Db::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pettyCashRequestBatchReject(Request $request)
    {
        $request->validate([
            'requestIds' => 'required|array',
            'stage' => 'required|in:initial,final'
        ]);

        Db::beginTransaction();
        try {
            WaPettyCashRequest::whereIn('id', $request->requestIds)
                ->update([
                    'rejected' => true,
                    'rejected_stage' => $request->stage,
                    'rejected_by' => $request->user()->id,
                    'rejected_date' => now()
                ]);

            DB::commit();
        } catch (Exception $e) {
            Db::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Requests rejected successfully'
        ]);
    }

    public function pettyCashRequestApprove(Request $request)
    {
        DB::beginTransaction();
        try {

            $pettyCashRequest = WaPettyCashRequest::find($request->id);

            $this->editLineItems($request, $pettyCashRequest);

            $pettyCashRequest->update([
                'wa_charts_of_account_id' => $request->account_id,
                'type' => $request->type,
                'vehicle_id' => $request->vehicle_id,
                'repair_type' => $request->repair_type,
                'tax_type' => $request->tax_type,
                'initial_approval' => true,
                'initial_approver' => $request->user_id,
                'initial_approval_date' => now()
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Petty cash request approved successfully'
        ]);
    }

    public function pettyCashRequestFinalApprove(Request $request)
    {
        DB::beginTransaction();
        try {

            $pettyCashRequest = WaPettyCashRequest::find($request->id);

            $this->editLineItems($request, $pettyCashRequest);

            $pettyCashRequest->update([
                'wa_charts_of_account_id' => $request->account_id,
                'type' => $request->type,
                'vehicle_id' => $request->vehicle_id,
                'repair_type' => $request->repair_type,
                'tax_type' => $request->tax_type,
            ]);

            $initiated = 0;
            $pettyCashRequestItems = $pettyCashRequest->pettyCashRequestItems;
            foreach ($pettyCashRequestItems as $requestItem) {
                try {
                    $this->processRequestItem($requestItem);

                    $initiated += 1;
                } catch (Exception $e) {
                    Log::info("Failed for $requestItem->id: " . $e->getMessage());
                }
            }

            if ($initiated != 0) {
                $pettyCashRequest->update([
                    'final_approval' => true,
                    'final_approver' => $request->user_id,
                    'final_approval_date' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Petty cash request approved successfully. $initiated out of {$pettyCashRequestItems->count()} deposits have been initiated."
            ]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function pettyCashRequestItemFileCreate(Request $request, $id)
    {
        try {
            $pettyCashRequestItem = WaPettyCashRequestItem::find($id);

            foreach ($request->file("files") as $file) {
                $path = $file->store('uploads/petty_cash_request_files', 'public');

                $pettyCashRequestItem->pettyCashRequestItemFiles()->create([
                    'path' => $path
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'File added successfully'
        ]);
    }

    public function pettyCashRequestItemFileDelete($id)
    {
        try {
            $file = WaPettyCashRequestItemFile::find($id);

            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            $file->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'File deleted successfully'
        ]);
    }

    public function pesaflowCallback(Request $request, $transactionId)
    {
        Log::info("Callback received from pesaflow b2c");
        Log::info(json_encode($request->all()));

        try {
            $transaction = WaPettyCashRequestItemWithdrawal::with('requestItem')->find($transactionId);

            if ($transaction) {
                $this->processTransactionFromCallback($transaction, $request);
            } else {
                Log::info("Trans reference $request->reference not found");
            }
        } catch (\Throwable $e) {
            Log::info("Call back PF failed");
            Log::error($e->getMessage(), $e->getTrace());
        }
    }

    private function authenticatePesaFlow(): array
    {
        $response = ['success' => true];
        try {
            $payload = [
                'key' => env('PESAFLOW_B2C_AUTH_KEY'),
                'secret' => env('PESAFLOW_B2C_AUTH_SECRET'),
            ];
            $url = env('PESAFLOW_B2C_URL') . '/oauth/generate/token';
            $apiResponse = Http::post($url, $payload);
            Log::info("PF Token response  " . $apiResponse->body());
            if (!$apiResponse->ok()) {
                $response['success'] = false;
                $response['message'] = $apiResponse->body();
            } else {
                $response['token'] = $apiResponse->json()['token'];
            }
        } catch (\Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            Log::info("Failed to get PF Token, citing " . $e->getMessage());
        }

        return $response;
    }

    public function processRequestItem($requestItem)
    {
        $amount = $requestItem->amount;
        $phoneNumber = $requestItem->payee_phone_no;

        $documentNumber = getCodeWithNumberSeries('PETTY_CASH');

        $transaction = $requestItem->withdrawals()->create([
            'document_no' => $documentNumber,
            'amount' => -$amount,
            'narrative' => "Petty cash deposit to $requestItem->payee_name - $phoneNumber",
        ]);

        $tokenResponse = $this->authenticatePesaFlow();
        $token = $tokenResponse['token'];
        $hashString = env('PESAFLOW_B2C_CLIENT_ID') . $phoneNumber . "$amount" . "KES" . env('PESAFLOW_B2C_CLIENT_SECRET');
        $hash = base64_encode(hash_hmac('sha256', $hashString, env('PESAFLOW_B2C_CLIENT_KEY')));
        $payload = [
            'api_client_id' => env('PESAFLOW_B2C_CLIENT_ID'),
            'source_account_id' => env('PESAFLOW_B2C_SOURCE_ACCOUNT'),
            'amount' => "$amount",
            'currency' => 'KES',
            'party_b' => $phoneNumber,
            'secure_hash' => $hash,
            'type' => 'b2c',
            'notification_url' => env('APP_URL') . '/api/petty-cash-request/pesaflow/callback/' . $transaction->id
        ];

        Log::info("PF B2C R Payload: " . json_encode($payload));

        $url = env('PESAFLOW_B2C_URL') . '/payment/withdraw';

        $response = Http::withToken($token)->post($url, $payload);
        Log::info("PF R Response: " . $response->body());

        if ($response->ok()) {
            $transaction->update(['reference' => $response->json()['reference']]);

            updateUniqueNumberSeries('PETTY_CASH', $documentNumber);
        } else {
            $transaction->delete();
            throw new Exception($response->body());
        }
    }

    public function editLineItems($request, $pettyCashRequest)
    {
        try {
            foreach ($request->lineItems as $lineItem) {
                $lineItem = json_decode($lineItem);

                if (!$lineItem->id) {
                    $pettyCashRequestItem = $pettyCashRequest->pettyCashRequestItems()->create([
                        'tax_manager_id' => $lineItem->vat_id ?: null,
                        'grn_number' => $lineItem->grn_number ?: null,
                        'transfer_id' => $lineItem->transfer_id ?: null,
                        'delivery_schedule_id' => $lineItem->delivery_schedule_id ?: null,
                        'route_id' => $lineItem->route_id ?: null,
                        'employee_id' => $lineItem->employee_id ?: null,
                        'supplier_id' => $lineItem->supplier_id ?: null,
                        'payee_name' => $lineItem->payee_name,
                        'payee_phone_no' => $lineItem->phone_number,
                        'amount' => str_replace(',', '', $lineItem->amount),
                        'tax_value' => $lineItem->vat_id ? TaxManager::find($lineItem->vat_id)->tax_value : null,
                        'vat_amount' => $lineItem->vat_amount,
                        'sub_total' => $lineItem->sub_total,
                        'cu_invoice_no' => $lineItem->cu_invoice_no,
                        'payment_reason' => $lineItem->payment_reason,
                    ]);
                } else {
                    $pettyCashRequestItem = WaPettyCashRequestItem::find($lineItem->id);

                    $pettyCashRequestItem->update([
                        'grn_number' => $lineItem->grn_number ?: null,
                        'transfer_id' => $lineItem->transfer_id ?: null,
                        'delivery_schedule_id' => $lineItem->delivery_schedule_id ?: null,
                        'route_id' => $lineItem->route_id ?: null,
                        'employee_id' => $lineItem->employee_id ?: null,
                        'supplier_id' => $lineItem->supplier_id ?: null,
                        'payee_name' => $lineItem->payee_name,
                        'payee_phone_no' => $lineItem->phone_number,
                        'amount' => str_replace(',', '', $lineItem->amount),
                        'tax_value' => $lineItem->vat_id ? TaxManager::find($lineItem->vat_id)->tax_value : null,
                        'vat_amount' => $lineItem->vat_amount,
                        'sub_total' => $lineItem->sub_total,
                        'cu_invoice_no' => $lineItem->cu_invoice_no,
                        'payment_reason' => $lineItem->payment_reason,
                    ]);
                }
            }

            if ($request->deletedItems) {
                foreach (explode(',', $request->deletedItems) as $deletedItem) {
                    $pettyCashRequestItem = WaPettyCashRequestItem::with('pettyCashRequestItemFiles')->find($deletedItem);

                    foreach ($pettyCashRequestItem->pettyCashRequestItemFiles as $file) {
                        if (Storage::disk('public')->exists($file->path)) {
                            Storage::disk('public')->delete($file->path);
                        }

                        $file->delete();
                    }

                    $pettyCashRequestItem->delete();
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function processTransactionFromCallback($transaction, $status)
    {
        try {
            $transaction->update(['call_back_status' => $status]);

            if ($status == 'complete') {
                $requestItem = $transaction->requestItem;
                $pettyCashRequest = $requestItem->pettyCashRequest;

                $series_module = WaNumerSeriesCode::where('module', 'PETTY_CASH')->first();
                $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();

                $date = now();
                $taxType = $pettyCashRequest->tax_type;
                $narrative = "{$requestItem->petty_cash_no} / {$requestItem->payee_name} / {$requestItem->payee_phone_number}";

                // CREDIT BANK ACCOUNT
                $bankTranAmount = match ($taxType) {
                    'exclusive' => $requestItem->amount + $requestItem->vat_amount,
                    default => $requestItem->amount
                };

                $bank_account = WaBankAccount::where('account_code', '988329')->first();
                $btran = new WaBanktran();
                $btran->type_number = $series_module->type_number;
                $btran->document_no = $transaction->document_no;
                $btran->bank_gl_account_code = $bank_account->getGlDetail?->account_code;
                $btran->reference = $transaction->reference;
                $btran->trans_date = $date;
                $btran->wa_payment_method_id = 11; //PETTY CASH
                $btran->amount = $bankTranAmount * -1;
                $btran->wa_curreny_id = 0;
                $btran->cashier_id = 1;
                $btran->save();

                // DEBIT EXPENSE ACCOUNT
                $expenseDebitAmount = match ($taxType) {
                    'inclusive' => $requestItem->amount - $requestItem->vat_amount,
                    default => $requestItem->amount
                };

                $dr = new WaGlTran();
                $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $dr->grn_type_number = $series_module->type_number;
                $dr->trans_date = $date;
                $dr->restaurant_id = 10; // MAKONGENI;
                $dr->tb_reporting_branch = 10; // MAKONGENI;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $transaction->document_no;
                $dr->narrative = $narrative;
                $dr->reference = $transaction->reference;
                $dr->account = WaChartsOfAccount::find($pettyCashRequest->wa_charts_of_account_id)->account_code;
                $dr->amount = $expenseDebitAmount;
                $dr->save();

                if (in_array($taxType, ['inclusive', 'exclusive'])) {
                    // Debit VAT Control
                    $taxVat = TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
                    $dr = new WaGlTran();
                    $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $dr->grn_type_number = $series_module->type_number;
                    $dr->trans_date = $date;
                    $dr->restaurant_id = 10;
                    $dr->tb_reporting_branch = 10;
                    $dr->grn_last_used_number = $series_module->last_number_used;
                    $dr->transaction_type = $series_module->description;
                    $dr->transaction_no = $transaction->document_no;
                    $dr->narrative = $narrative;
                    $dr->reference = $transaction->reference;
                    $dr->account = $taxVat->getOutputGlAccount->account_code;
                    $dr->amount = $requestItem->vat_amount;
                    $dr->save();
                }

                // CREDIT BANK ACCOUNT
                $bank_account = WaBankAccount::where('account_code', '988329')->first();
                $bankCreditAmount = match ($taxType) {
                    'exclusive' => $requestItem->amount + $requestItem->vat_amount,
                    default => $requestItem->amount
                };
                $dr = new WaGlTran();
                $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $dr->grn_type_number = $series_module->type_number;
                $dr->trans_date = $date;
                $dr->restaurant_id = 10; // MAKONGENI;
                $dr->tb_reporting_branch = 10; // MAKONGENI;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $transaction->document_no;
                $dr->narrative = $narrative;
                $dr->reference = $transaction->reference;
                $dr->account = $bank_account->getGlDetail->account_code;
                $dr->amount = $bankCreditAmount * -1;
                $dr->save();

                return true;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getFailedRequestItems($start, $end, $branch, $type, $pettyCashTypes = null)
    {
        if (!$pettyCashTypes) {
            $pettyCashTypes = (new PettyCashRequestTypeController)->getUserPettyCashRequestTypes(Auth::user());
        }

        return WaPettyCashRequestItem::with([
            'pettyCashRequest' => fn($query) => $query->with('restaurant', 'chartOfAccount', 'pettyCashType'),
            'latestWithdrawal',
            'grn',
            'transfer'
        ])
            ->whereHas('pettyCashRequest', fn($query) => $query->whereIn('type', $pettyCashTypes->pluck('slug')->toArray()))
            ->whereHas('latestWithdrawal', function ($query) {
                $query->whereNot('call_back_status', 'complete');
            })
            ->whereDoesntHave('latestWithdrawal', function ($query) {
                $query->where('call_back_status', 'complete');
            })
            ->when($branch, fn($query) => $query->whereHas('pettyCashRequest', fn($query) => $query->where('restaurant_id', $branch)))
            ->when($type, fn($query) => $query->whereHas('pettyCashRequest', fn($query) => $query->where('type', $type)))
            ->whereBetween('created_at', [$start, $end])
            ->get();
    }

    // Mobile APIs
    public function requestPettyCash(Request $request)
    {
        $request->validate([
            'petty_cash_type' => 'required|string|exists:wa_petty_cash_request_types,slug',
            'amount' => 'required|integer',
            'files' => 'required',
        ]);

        $user = $request->user();

        $pendingRequests = WaPettyCashRequest::query()
            ->where('created_by', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->where(function ($query) {
                $query->where('final_approval', false)
                    ->orWhere('initial_approval', false);
            })
            ->where('rejected', false)
            ->count();

        if ($pendingRequests) {
            return response()->json([
                'message' => 'You have already made a request today'
            ], 422);
        }

        $route = null;
        $deliverySchedule = null;
        if ($user->role_id == 4) {
            $route = $user->routes()
                ->with('activeDeliverySchedule')
                ->whereHas('activeDeliverySchedule')
                ->first();
    
        } else if ($user->role_id == 6) {
            $deliverySchedule = DB::table('delivery_schedules')
                ->where('driver_id', $user->id)
                ->where('status', 'in_progress')
                ->latest()
                ->first();
        }

        if (!$route && !$deliverySchedule) {
            return response()->json([
                'message' => 'You do not have an active delivery shift'
            ], 422);
        }

        $pettyCashType = WaPettyCashRequestType::with('chartOfAccount')->where('slug', $request->petty_cash_type)->first();

        DB::beginTransaction();
        try {
            $series = getCodeWithNumberSeries('PETTY_CASH');

            $pettyCashRequest = WaPettyCashRequest::create([
                'restaurant_id' => $user->restaurant_id,
                'wa_department_id' => $user->wa_department_id,
                'created_by' => $user->id,
                'wa_charts_of_account_id' => $pettyCashType->chartOfAccount->id,
                'petty_cash_no' => $series,
                'type' => $pettyCashType->slug,
                'tax_type' => 'without',
            ]);

            updateUniqueNumberSeries('PETTY_CASH', $series);

            $pettyCashRequestItem = $pettyCashRequest->pettyCashRequestItems()->create([
                'delivery_schedule_id' => $route?->activeDeliverySchedule?->id ?? $deliverySchedule->id,
                'route_id' => $route?->id ?? $deliverySchedule->route_id,
                'employee_id' => $user->id,
                'payee_name' => $user->name,
                'payee_phone_no' => $user->phone_number,
                'amount' => $request->amount,
                'vat_amount' => 0,
                'sub_total' => $request->sub_total,
                'payment_reason' => "$user->name / $pettyCashType->name",
            ]);

            $path = $request->file("files")->store('uploads/petty_cash_request_files', 'public');

            $pettyCashRequestItem->pettyCashRequestItemFiles()->create([
                'path' => $path
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Petty cash requested successfully'
        ]);
    }

    public function userPettyCashRequests(Request $request)
    {
        $user = $request->user();

        $pendingRequests = WaPettyCashRequest::query()
            ->where('created_by', $user->id)
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfDay(), Carbon::now()])
            ->where('rejected', false)
            ->get();

        return response()->json($pendingRequests);
    }
}
