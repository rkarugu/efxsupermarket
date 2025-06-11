<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommonReportDataExport;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaUnitOfMeasure;
use App\Models\WaStockCountVariation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockCountVarianceReportController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    protected $module;

    public function __construct()
    {
        $this->model = 'stock-count-variance';
        $this->module = 'stock-count-variance';
        $this->title = 'Stock Count Variance';
        $this->pmodule = 'stock-count-variancestock-count-variance';
    }
    public function index(Request $request)
    {
        $model = 'detailed-stock-count-variance';
        $module = $this->module;
        $title = 'detailed_stock_count_variance_report';
        $pmodule = $this->pmodule;
        $branches = WaLocationAndStore::all();
        $user = Auth::user();
        if (isset($user->role_id) && $user->role_id == 152) {
            $uoms = WaUnitOfMeasure::where('id', $user->wa_unit_of_measures_id)->get();
        } else {
            $uoms = WaUnitOfMeasure::all();
        }

        $data = WaStockCountVariation::latest()
            ->with([
                'getInventoryItemDetail',
                'getUomDetail',
                'stockDebtorItem',
                'stockDebtorItem.debtor'
            ]);

        $data = $data->whereHas('getInventoryItemDetail', function ($query) {
            $query->where('status', 1);
        });

        $uniqueDates = WaStockCountVariation::select(DB::raw('DATE(created_at) as created_date'));

        if ($request->branch) {
            $branch = WaLocationAndStore::find($request->branch);
            $data = $data->where('wa_location_and_store_id', $request->branch);
            $uniqueDates = $uniqueDates->where('wa_location_and_store_id', $request->branch);
            $uoms = WaUnitOfMeasure::select(
                'wa_unit_of_measures.id as id',
                'wa_unit_of_measures.title as title',
            )
                ->leftJoin('wa_location_store_uom', 'wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id')
                ->where('wa_location_store_uom.location_id', $request->branch)
                ->get();
        } else {
            $branch = WaLocationAndStore::find(46);
            $data = $data->where('wa_location_and_store_id', 46);
            $uniqueDates = $uniqueDates->where('wa_location_and_store_id', 46);
        }

        if ($request->uom) {
            $bin = WaUnitOfMeasure::find($request->uom);
            $data = $data->where('uom_id', $request->uom);
            $uniqueDates = $uniqueDates->where('uom_id', $request->uom);
        } else {
            $bin = null;
        }
        if (isset($user->role_id) && $user->role_id == 152) {
            $data = $data->where('uom_id', $user->wa_unit_of_measures_id);
            $uniqueDates = $uniqueDates->where('uom_id', $user->wa_unit_of_measures_id);
        }
        if ($request->start_date) {
            $date1 = Carbon::parse($request->get('start_date'))->toDateString() . " 00:00:00";
            $date2 = Carbon::parse($request->get('start_date'))->toDateString() . " 23:59:59";
            $data  = $data->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
            $uniqueDates = $uniqueDates->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
        } else {
            $todaysDate = \Carbon\Carbon::now()->toDateString();
            $data  = $data->whereDate('created_at', '=', $todaysDate);
            $uniqueDates = $uniqueDates->whereDate('created_at', '=', $todaysDate);
        }
        $categoryIds = $data->distinct('category_id')->pluck('category_id')->toArray();

        $data = $data->orderBy('variation', 'desc')->get();

        $uniqueDates = $uniqueDates
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $uniqueDatesArray = $uniqueDates->pluck('created_date')->toArray();
        if (!can('view', $module)) {
            return redirect()->back()->withErrors(['message' => 'You don\'t have sufficient permissions to access the requested page.']);
        }
        if (isset($request->type) && $request->type === 'Download') {
            $categories = WaInventoryCategory::whereIn('id', $categoryIds)->get();
            $start_date = $date1;
            $end_date = $date2;
            $pdf = Pdf::loadView('admin.stockCountVariance.stock_count_variance_excel', compact('end_date', 'start_date', 'data', 'uniqueDatesArray', 'categories', 'bin', 'branch'))->setPaper('a4', 'portrait');

            return $pdf->download($this->title . $request->start_date . '.pdf');
        }

        $breadcum = [$title => "", 'Listing' => ''];
        return view("admin.stockCountVariance.stock_count_variance", compact('model', 'title', 'breadcum', 'branches', 'data', 'uniqueDatesArray', 'uoms', 'user'));
    }
    public function summary(Request $request)
    {
        $model = 'summary-stock-count-variance';
        $module = $this->module;
        $title = 'summary-stock-count-variance';
        $pmodule = $this->pmodule;
        $branches = WaLocationAndStore::all();
        $user = Auth::user();
        $storeKeepers = User::orderBy('role_id', 'desc')->get();
        if (isset($user->role_id) && $user->role_id == 152) {
            $uoms = WaUnitOfMeasure::where('id', $user->wa_unit_of_measures_id)->get();
        } else {
            $uoms = WaUnitOfMeasure::all();
        }

        $data = WaStockCountVariation::with('getInventoryItemDetail', 'getUomDetail')->latest();
        if($request->storekeeper){
            $data = $data->where('wa_stock_count_variation.user_id', $request->storekeeper);

        }
        $data = $data->whereHas('getInventoryItemDetail', function ($query) {
            $query->where('status', 1);
        });
        $uniqueDates = WaStockCountVariation::latest();
        if($request->storekeeper){
            $uniqueDates = $uniqueDates->where('wa_stock_count_variation.user_id', $request->storekeeper);

        }
        $uniqueDates = $uniqueDates->select(DB::raw('DATE(created_at) as created_date'));

        if ($request->branch) {
            $data = $data->where('wa_location_and_store_id', $request->branch);
            $uniqueDates = $uniqueDates->where('wa_location_and_store_id', $request->branch);
            $uoms = WaUnitOfMeasure::select(
                'wa_unit_of_measures.id as id',
                'wa_unit_of_measures.title as title',
            )
                ->leftJoin('wa_location_store_uom', 'wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id')
                ->where('wa_location_store_uom.location_id', $request->branch)
                ->get();
        }
        if ($request->uom) {
            $data = $data->where('uom_id', $request->uom);
            $uniqueDates = $uniqueDates->where('uom_id', $request->uom);
        }
        if (isset($user->role_id) && $user->role_id == 152) {
            $data = $data->where('uom_id', $user->wa_unit_of_measures_id);
            $uniqueDates = $uniqueDates->where('uom_id', $user->wa_unit_of_measures_id);
        }
        if ($request->start_date && $request->end_date) {
            $date1 = Carbon::parse($request->get('start_date'))->toDateString() . " 00:00:00";
            $date2 = Carbon::parse($request->get('end_date'))->toDateString() . " 23:59:59";
            $data  = $data->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
            $uniqueDates = $uniqueDates->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
        } else {
            $todaysDate = \Carbon\Carbon::now()->toDateString();
            $data  = $data->whereDate('created_at', '=', $todaysDate);
            $uniqueDates = $uniqueDates->whereDate('created_at', '=', $todaysDate);
        }
        $categoryIds = $data->distinct('category_id')->pluck('category_id')->toArray();

        $data = $data->get()->groupBy(['wa_inventory_item_id']);
        $uniqueDates = $uniqueDates
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        $uniqueDatesArray = $uniqueDates->pluck('created_date')->toArray();
        if (!can('view', $module)) {
            return redirect()->back()->withErrors(['message' => 'You don\'t have sufficient permissions to access the requested page.']);
        }
        if (isset($request->type) && $request->type === 'Download') {
            $categories = WaInventoryCategory::whereIn('id', $categoryIds)->get();
            $start_date = $date1;
            $end_date = $date2;
            $pdf = Pdf::loadView('admin.stockCountVariance.summary_stock_count_variance_excel', compact('categories', 'end_date', 'start_date', 'data', 'uniqueDatesArray'))->setPaper('a4', 'landscape');

            return $pdf->download($this->title . $request->start_date . '.pdf');
        }

        $breadcum = [$title => "", 'Listing' => ''];
        return view("admin.stockCountVariance.summary_stock_count_variance", compact('model', 'title', 'breadcum', 'branches', 'data', 'uniqueDatesArray', 'uoms', 'user', 'storeKeepers'));
    }
    public function print(Request $request)
    {
        $model = 'detailed-stock-count-variance';
        $module = $this->module;
        $title = 'detailed_stock_count_variance_report';
        $pmodule = $this->pmodule;
        $branches = WaLocationAndStore::all();
        $user = Auth::user();
        if (isset($user->role_id) && $user->role_id == 152) {
            $uoms = WaUnitOfMeasure::where('id', $user->wa_unit_of_measures_id)->get();
        } else {
            $uoms = WaUnitOfMeasure::all();
        }

        $data = WaStockCountVariation::latest()->with(['getInventoryItemDetail', 'getUomDetail']);

        $uniqueDates = WaStockCountVariation::select(DB::raw('DATE(created_at) as created_date'));

        if ($request->branch) {
            $data = $data->where('wa_location_and_store_id', $request->branch);
            $uniqueDates = $uniqueDates->where('wa_location_and_store_id', $request->branch);
        }
        if ($request->uom) {
            $data = $data->where('uom_id', $request->uom);
            $uniqueDates = $uniqueDates->where('uom_id', $request->uom);
        }
        if (isset($user->role_id) && $user->role_id == 152) {
            $data = $data->where('uom_id', $user->wa_unit_of_measures_id);
            $uniqueDates = $uniqueDates->where('uom_id', $user->wa_unit_of_measures_id);
        }
        if ($request->start_date) {
            $date1 = Carbon::parse($request->get('start_date'))->toDateString() . " 00:00:00";
            $date2 = Carbon::parse($request->get('start_date'))->toDateString() . " 23:59:59";
            $data  = $data->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
            $uniqueDates = $uniqueDates->whereDate('created_at', '>=', $date1)->whereDate('created_at', '<=', $date2);
        } else {
            $todaysDate = \Carbon\Carbon::now()->toDateString();
            $data  = $data->whereDate('created_at', '=', $todaysDate);
            $uniqueDates = $uniqueDates->whereDate('created_at', '=', $todaysDate);
        }
        $categoryIds = $data->distinct('category_id')->pluck('category_id')->toArray();

        $data = $data->orderBy('quantity_recorded', 'desc')->get();
        $uniqueDates = $uniqueDates
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $uniqueDatesArray = $uniqueDates->pluck('created_date')->toArray();
        if (!can('view', $module)) {
            return redirect()->back()->withErrors(['message' => 'You don\'t have sufficient permissions to access the requested page.']);
        }
        $categories = WaInventoryCategory::whereIn('id', $categoryIds)->get();

        $start_date = $date1;
        $end_date = $date2;

        $breadcum = [$title => "", 'Listing' => ''];
        return view('admin.stockCountVariance.stock_count_variance_excel', compact('end_date', 'start_date', 'data', 'uniqueDatesArray', 'categories'));
    }
}
