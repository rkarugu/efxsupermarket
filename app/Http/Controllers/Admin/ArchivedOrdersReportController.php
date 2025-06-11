<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaPosCashSales;
use App\Models\CashDropTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ArchivedOrdersReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'pos-cash-sales-archived';
        $this->title = 'Archived Order Report';
        $this->pmodule = 'archived_orders_report';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $user = getLoggeduserProfile();
        $branches = $this->getRestaurantList();

        $branch = $request->restaurant_id;
        $startDate = $request->from ?? now()->startOfDay();
        $endDate = Carbon::parse($request->to)->endOfDay()  ?? now();

        if ($request->ajax()) {
            $query = WaPosCashSales::query()
                ->with('user')
                ->with('branch')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($permission == 'superadmin', function ($q) use ($branch) {
                    if ($branch) {
                        $q->where('branch_id', $branch);
                    }
                })
                ->when($permission != 'superadmin', function ($q) use ($user) {
                    $q->where('branch_id', $user->restaurant_id);
                })

                ->where('status', 'Archived')
                ->latest();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('total', function ($row) {
                    return number_format($row->total, 2, '.', ',');
                })
                ->editColumn('archived_at', function ($row) {
                    return Carbon::parse($row->archived_at)->format('d/m/Y, H:i:s');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y, H:i:s');
                })
                ->addColumn('links', function($row){
                    return '<a href="'. route('pos-cash-sales.archive-report.show', base64_encode($row->id)).'" title="Details">
                    <i class="fa fa-eye text-info fa-lg"></i></a>';
                })
                ->rawColumns(['links'])
                ->toJson();
        }


        return view('admin.pos_cash_sales.archived_orders_report', compact('branches','title', 'model', 'pmodule', 'permission'));

    }
    public function show($id)
    {
        $id = base64_decode($id);
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('pos-cash-sales.archive-report'), 'Listing' => ''];
            $data = WaPosCashSales::with([
                'items' => function ($query) {
                    $query->where('qty', '>', 0);
                },
                'user',
                'items.item',
                'items.item.pack_size',
                'items.location',
                'items.dispatch_by'
            ])->where('id', $id)->first();
            if (!$data) {
                Session::flash('warning', 'No Archived Orders');
                return redirect()->back();
            }
            return view('admin.pos_cash_sales.show_archieved', compact('data', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
