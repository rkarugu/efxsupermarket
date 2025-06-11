<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaLocationAndStore;
use App\Services\ExcelDownloadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailySalesAndMovesSummaryController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'sales_and_moves_report';
        $this->title = 'Stock Movement Report';
        $this->pmodule = 'inventory-reports';
    }
    public function index(Request $request)
    {
        
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $breadcum = [$title => route('salesVsMovesReport.index'), 'Listing' => ''];
        $data = null;
        $branches = WaLocationAndStore::all();
        

        if($request->branch){
            $store = WaLocationAndStore::find($request->branch);
            $restaurant = Restaurant::find($store->wa_branch_id);
            $movesSubQuery = DB::table('wa_stock_moves')
                ->select(
                    'wa_inventory_item_id',
                    DB::raw('SUM(CASE WHEN DATE(created_at) < "'.$request->date.'" THEN qauntity ELSE 0 END) as opening_qoh'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND (document_no like "INV%" OR document_no LIKE "CIV%") THEN qauntity ELSE 0 END) as sales'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND document_no like "RTN%" THEN qauntity ELSE 0 END) as returns'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND document_no like "GRN%" THEN qauntity ELSE 0 END) as grns'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND (document_no like "TRANS%" OR document_no LIKE "MAR%") AND qauntity < 0 THEN qauntity ELSE 0 END) as transfer_out'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND (document_no like "TRANS%" OR document_no LIKE "MAR%") AND qauntity > 0 THEN qauntity ELSE 0 END) as transfer_in'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND document_no like "STB%" AND qauntity < 0 THEN qauntity ELSE 0 END) as break_out'),
                    DB::raw('SUM(CASE WHEN DATE(created_at) = "'.$request->date.'" AND document_no like "STB%" AND qauntity > 0 THEN qauntity ELSE 0 END) as break_in'),
                    DB::raw("SUM(qauntity) as closing_qoh"),
                )
                ->where('wa_location_and_store_id', $request->branch)
                ->whereDate('created_at', '<=', $request->date)
                ->groupBy('wa_stock_moves.wa_inventory_item_id');

            $data = DB::table('wa_inventory_items')
                ->select(
                    'wa_inventory_items.stock_id_code',
                    'wa_inventory_items.title',
                    'wa_unit_of_measures.title as bin',
                    'moves.opening_qoh',
                    'moves.sales',
                    'moves.returns',
                    'moves.grns',
                    'moves.transfer_in',
                    'moves.transfer_out',
                    'moves.break_in',
                    'moves.break_out',
                    'moves.closing_qoh',
                    
                )
                ->leftJoinSub($movesSubQuery, 'moves', 'moves.wa_inventory_item_id', 'wa_inventory_items.id')
                ->leftJoin('wa_inventory_location_uom', function($query) use ($request){
                    $query->on('wa_inventory_items.id', 'wa_inventory_location_uom.inventory_id')
                    ->where('wa_inventory_location_uom.location_id', $request->branch);
                })
                ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', 'wa_inventory_location_uom.uom_id')
                ->where('wa_inventory_items.status', 1)
                ->orderBy('wa_unit_of_measures.title', 'desc')
                ->get();
                $headers = ['STOCK ID CODE', 'TITLE','BIN', 'OPENING QOH', 'SALES', 'RETURNS', 'GRN','TRANSFERS IN', 'TRANSFERS  OUT', 'BREAK IN', 'BREAK OUT','CLOSING QOH'];
                return ExcelDownloadService::download('daily_moves_report_'.$request->date, $data, $headers);

        }

        return view('admin.salesvsmoves.sales_vs_moves', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'data', 'branches'));

        // if (isset($permission[$pmodule . '___eod-report']) || $permission == 'superadmin') {
        //     $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        //     return view('admin.eod_report.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        // } else {
        //     Session::flash('warning', 'Invalid Request');
        //     return redirect()->back();
        // }
    }
    
}
