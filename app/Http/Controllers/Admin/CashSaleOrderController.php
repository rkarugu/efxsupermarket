<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaPosCashSales;
use App\Models\WaAccountTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CashSaleOrderController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'pos-cash-sales-delayed';
        $this->title = 'POS Cash Delayed Orders';
        $this->pmodule = 'pos-cash-sales-delayed';
    }
    public function index(Request $request)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();
        $companyPreference =  \App\Model\WaCompanyPreference::where('id', '1')->first();
        $branch = $request->restaurant_id;
        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();
        $cutoffTime = Carbon::now()->subMinutes(15);


        if (request()->wantsJson()) {
            $query = WaPosCashSales::query()
                ->with('items')
                ->whereHas('dispatch', function ($q){
                    $q ->where('status','dispatching');
                })
                ->with('branch')
                ->with('user')
                ->where('status', 'Completed')
                ->where('paid_at', '<', $cutoffTime)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('branch_id', $user->restaurant_id);
//                ->when($branch, function ($q, $branch) {
//                    return $q->where('branch_id', $branch);
//                });

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ->format('d/m/Y, H:i:s');
                })
                ->addColumn('item_count', function ($row) {
                    return $row->items -> count();
                })
                ->addColumn('age', function ($row) {
                    return Carbon::parse($row->paid_at)->diffForHumans(['parts' => 2, 'short' => true]) . ' ' ;
                })
                ->toJson();
        }

        return view('admin.pos_cash_sales.stale', compact('branches','user','title', 'model', 'pmodule', 'permission'));

    }
}
