<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MaintainWalletsExport;
use App\Http\Controllers\Controller;
use App\Interfaces\SmsService;
use App\Model\User;
use App\Models\PettyCashTransaction;
use App\WalletTran;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use JWTAuth;
use Tymon\JWTAuth\Payload;

class MaintainWalletsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected $basePath;

    public function __construct(Request $request, protected SmsService $smsService)
    {
        $this->model = 'maintain-wallet';
        $this->title = 'Maintain Wallet';
        $this->pmodule = 'maintain-wallet';
        $this->basePath = 'admin.maintain_wallets';
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!can('view', $this->pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $users = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'phone_number',
                'title as role',
                'restaurants.name as branch',
                DB::raw("(select sum(amount) from petty_cash_transactions where petty_cash_transactions.user_id = users.id) as balance")
            )
            ->join('roles', function ($join) {
                $join->on('users.role_id', '=', 'roles.id')->whereNot('roles.id', 1);
            })
            ->join('restaurants', 'users.restaurant_id', '=', 'restaurants.id')
            ->orderBy('balance', 'desc')
            ->get();

        $title = 'User Wallets';
        $model = $this->model;
        $basePath = $this->basePath;

        return view($basePath . '.index', compact('title', 'model', 'users'));
    }

//        $permission = $this->mypermissionsforAModule();
//        $pmodule = $this->pmodule;
//
//        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
//            $users = User::select([
//                '*',
//                DB::RAW('(select SUM(amount) from wallet_trans where wallet_trans.employee_id = users.id) as balance'),
//            ])->with(['app_permissions', 'userRestaurent', 'routes', 'userRole'])
//                ->whereNot('role_id', 1)
//                ->orderBy('id')
//                ->get()
//                ->map(function (User $user) {
//                    if ((count($user->routes) == 0) && $user->route) {
//                        $user->routes()->attach($user->route);
//                    }
//
//                    $user->routes = implode(",", $user->routes()->pluck('route_name')->toArray());
//                    return $user;
//                });
//
//            if ($request->download && $request->download == 'download') {
//                $data = [];
//                foreach ($users as $user) {
//                    $payload = [
//                        'name' => ucfirst($user->name),
//                        'phone_number' => $user->phone_number,
//                        'role' => $user->userRole?->title ?? '-',
//                        'branch' => ucfirst(@$user->userRestaurent->name),
//                        'balance' => manageAmountFormat($user->balance)
//                    ];
//                    $data [] = $payload;
//
//                }
//                $export = new MaintainWalletsExport(collect($data));
//                $today = Carbon::now()->toDateString();
//                return Excel::download($export, "employee_wallets$today.xlsx");
//
//            }

    public
    function viewWalletTransactions($employeeId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;

        $transactions = PettyCashTransaction::where('user_id', $employeeId)->get();
        $user = User::find($employeeId);
        return view($basePath . '.view_wallet_transactions', compact('title', 'model', 'pmodule', 'permission', 'transactions', 'employeeId', 'user'));
    }

    public
    function myWalletTransactions($employeeId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = "my-wallet";
        $basePath = $this->basePath;
        $currentBalance = WalletTran::where('employee_id', $employeeId)->sum('amount');
        // calculate available balance based on role and day of  week
        $availableBalance = 0;


        $transactions = WalletTran::where('employee_id', $employeeId)->orderBy('id', 'asc')->get();
        $user = User::find($employeeId);
        return view($basePath . '.my_wallet', compact('title', 'model', 'pmodule', 'permission', 'transactions', 'employeeId', 'user', 'currentBalance', 'availableBalance'));
    }


    public
    function getWalletBalance(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['errors' => 'A user matching the provided token was not found.'], 422);
            }
            $walletBalance = DB::table('petty_cash_transactions')->where('user_id', $user->id)->where('call_back_status', 'complete')
                ->sum('amount') ?? 0;
            return $this->jsonify(['balance' => format_amount_with_currency($walletBalance)], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public
    function getWalletTransactions(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['errors' => 'A user matching the provided token was not found.'], 422);
            }
            $transactions = WalletTran::where('employee_id', $user->id);
            //filter transactions by date
            if ($request->from) {
                $fromDate = Carbon::parse($request->from)->toDateString();
                $transactions = $transactions->whereDate('created_at', '>=', $fromDate);
            }
            if ($request->to) {
                $toDate = Carbon::parse($request->from)->toDateString();
                $transactions = $transactions->whereDate('created_at', '<=', $toDate);

            }
            $transactions = $transactions->cursorPaginate();
            $balance = WalletTran::where('employee_id', $user->id)->sum('amount');
            return response()->json(['status' => true, 'transactions' => $transactions, 'balance' => manageAmountFormat($balance)], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }

    }


    public
    function withdrawFromWallet(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::toUser($request->token);
            if (!$user) {
                return response()->json(['errors' => 'A user matching the provided token was not found.'], 422);
            }
            $validator = Validator::make($request->all(), [
                'amount' => 'required',
                'phone_number' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $amount = $request->amount;
            $balance = WalletTran::where('employee_id', $user->id)->sum('amount');

            if ($amount > $balance) {
                return response()->json(['errors' => 'Insufficient balance', 'available_balance' => $balance], 422);
            }
            $sms = "Dear $user->name, your withdraw request of" . manageAmountFormat($amount) . " has been received and is being processed.";

            $this->smsService->sendMessage($sms, $user->phone_number);

            //check available balance vs amount


            //check withdrawal limit based on day  of the week and route expense
            $now = \Carbon\Carbon::now();
            //call B2C endpoint
            //if successful deduct wallet balance
            $walletTrans = new WalletTran();
            $walletTrans->employee_id = $user->id;
            $walletTrans->narration = "$amount-$user->name-$now";
            $walletTrans->transaction_type = 'withdraw';
            $walletTrans->amount = $amount * -1;
            $walletTrans->save();

            $newBalance = WalletTran::where('employee_id', $user->id)->sum('amount');


            return response()->json(['status' => true, 'balance' => $newBalance, 'amount' => $amount], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    public
    function withdrawFromWalletWeb(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'amount' => 'required',
                'phone_number' => 'required',
            ]);
            $user = getLoggeduserProfile();
            $user = User::find($user->id);
            if ($validator->fails()) {
                Session::flash('warning', 'Please provide a valid number and amount');
                return redirect()->back();
            }

            $amount = $request->amount;
            $balance = WalletTran::where('employee_id', $user->id)->sum('amount');

            if ($amount > $balance) {
                Session::flash('warning', 'Insufficient balance');
                return redirect()->back();
            }


            //check withdrawal limit based on day  of the week and route expense
            $newBalance = WalletTran::where('employee_id', $user->id)->sum('amount');

            $sms = "Dear $user->name, your withdraw request of KES. " . manageAmountFormat($amount) . " has been received and is being processed.";

            $this->smsService->sendMessage($sms, $user->phone_number);
            $now = \Carbon\Carbon::now();
            //call B2C endpoint
            //if successful deduct wallet balance
            $walletTrans = new WalletTran();
            $walletTrans->employee_id = $user->id;
            $walletTrans->narration = "$amount-$user->name-$now";
            $walletTrans->transaction_type = 'withdraw';
            $walletTrans->amount = $amount * -1;
            $walletTrans->save();


            Session::flash('success', 'Withdraw request placed successfully');
            return redirect()->route('maintain-wallet.my-wallet', $user->id);
        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }

    }


}
