<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaChartsOfAccount;
use App\Model\WaPosCashSalesPayments;
use App\Models\WaAccountTransaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;
use Yajra\DataTables\Facades\DataTables;

class DoubleEntryController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'tender-entry';
        $this->title = 'Tender Entry';
        $this->pmodule = 'tender-entry';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
        $branch = $request->restaurant_id;
        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
        $accountIds = [
            $companyPreference->cash_sales_control_account,
            $companyPreference -> sales_control_account
        ];
        $acs = WaChartsOfAccount::whereIn('id', $accountIds)->pluck('account_name','id');
        $current_id = $request->account_id ?? $companyPreference->cash_sales_control_account;
        if (request()->wantsJson()) {
            $query = WaAccountTransaction::query()
                ->with('branch')
                ->with('posSale')
                ->with('account')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($branch, function ($q, $branch) {
                 return $q->where('restaurant_id', $branch);
               })
            ->latest();
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('debit', function ($row) {
                    return $row->amount < 0 ? number_format(abs($row->amount), 2, '.', '') * -1 : 0;
                })

                ->toJson();
        }

        return view('admin.DoubleEntry.index', compact('acs','branches','user','title', 'model', 'breadcum', 'pmodule', 'permission'));

    }

    public function byChannel(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();
        $paymentMethods = getPaymentmeList();
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];

        $active_method = $request->payment_method;
        if ($permission != 'superadmin') {
            $branch =     Auth::user()->restuarant_id;

        }else{
            $branch = $request->restaurant_id;
        }


        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
        if (request()->wantsJson()) {
            $query = WaPosCashSalesPayments::query()
                ->whereNotNull('payment_method_id')
                ->with('parent')
                ->with('balancing_account')
                ->with('parent.branch')
                ->with('parent.user')
                ->with('method')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($active_method, function ($q, $active_method) {
                    return $q->where('payment_method_id', $active_method);
                })
                ->when($branch, function ($q, $branch) {
                    return $q->whereHas('parent.branch', function ($q) use ($branch) {
                        $q->where('branch_id', $branch);
                    });
                })
                ->latest();

            return DataTables::eloquent($query)
                ->with('total', function () use ($query) {
                    return number_format($query->sum('amount'), 2,'.',',') ;
                })
                ->editColumn('reconciled', function($row) {
                    return $row->reconciled ? 'Yes' : 'No';
                })
                ->editColumn('posted', function($row) {
                    return $row->posted ? 'Yes' : 'No';
                })
                ->editColumn('amount', function($row) {
                    return number_format($row->amount, 2,'.',',');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s');
                })
                ->addIndexColumn()
                ->toJson();
        }
        return view('admin.DoubleEntry.transaction-by-channel', compact('paymentMethods','branches','user','title', 'model', 'breadcum', 'pmodule', 'permission'));

    }

    public function sumery(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = 'tender-entry-channel-summery';
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();
        $paymentMethods = getPaymentmeList();
        $breadcum = [$title => route($pmodule . '.index'), 'Listing' => ''];
        $branch = $request->restaurant_id;
        if ($permission != 'superadmin'){
            $branch = Auth::user()->restaurant_id;
        }

        $active_method = $request->payment_method;

        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();

        if (request()->wantsJson()) {
            $query = WaPosCashSalesPayments::query()
                ->selectRaw('*, SUM(amount) as total_amount')
                ->whereNotNull('payment_method_id')
                ->with('parent')
                ->with('balancing_account')
                ->with('parent.branch')
                ->with('method')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($active_method, function ($q, $active_method) {
                    return $q->where('payment_method_id', $active_method);
                })
                ->when($branch, function ($q, $branch) {
                    return $q->whereHas('parent.branch', function ($q) use ($branch) {
                        $q->where('branch_id', $branch);
                    });
                })
                ->groupBy('payment_method_id')
                ->latest();

            return DataTables::eloquent($query)
                ->editColumn('total_amount', function($row) {
                    return number_format($row->total_amount, 2,'.',',');
                })
                ->addColumn('action', function ($model) {
                    return ' <a href="'. route('tender-entry.transactions-by-channel').'?payment_method='.$model->method->id.'" class="btn btn-sm btn-outline-success"><i class="fa fa-eye"></i></a>';
                })
                ->addIndexColumn()
                ->toJson();
        }
        return view('admin.DoubleEntry.channels-summery', compact('paymentMethods','branches','user','title', 'model', 'breadcum', 'pmodule', 'permission'));

    }
}
