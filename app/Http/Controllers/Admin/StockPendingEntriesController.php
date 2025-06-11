<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Http\Controllers\Controller;
use App\Model\WaUnitOfMeasure;
use App\Models\WaStockCountVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockExpungedVariation;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class StockPendingEntriesController extends Controller
{
    public function index()
    {
        if (!can('view', 'stock-pending-entries')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Pending Entries';
        $model= 'stock-pending-entries';

        $breadcum = [
            'Stock Processing' => '',
            $title => ''
        ];

        $permission = $this->mypermissionsforAModule();

        $stock = WaStockCountVariation::with('getInventoryItemDetail','getUomDetail')->where('is_processed',2);
        if(request()->filled('bin')){
            $stock->where('uom_id', request()->bin);
        }
        if(request()->start_date && request()->end_date){
            $stock->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
        }
        if (request()->wantsJson()) {
            return DataTables::of($stock)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($date) {
                        return date('Y-m-d',strtotime($date->created_at));
                    })
                    ->editColumn('variation',function($variation){
                        return number_format($variation->variation);
                    })
                    ->toJson();
        }

        if (request()->filled('type')) {
            if (request()->type == 'pdf') {
                $stocks = $stock->get();
                $startDate = request()->start_date;
                $endDate = request()->end_date;
                $branch = '-';
                
                $pdf = PDF::loadView('admin.stock_processing.pending_entries.pdf', compact('stocks','startDate','endDate','branch'));
                $report_name = 'pending_entries_' . date('Y_m_d_H_i_A');
                return $pdf->download($report_name . '.pdf');
            }
        }
        $bins = WaUnitOfMeasure::get();
        
        return view('admin.stock_processing.pending_entries.index', compact('title','model','breadcum','permissions','bins'));
    }

    
    public function restore(Request $request)
    {
        DB::beginTransaction();
        try {
            $stock= WaStockCountVariation::find($request->variation);
            $stock->is_processed = 0;
            $stock->save();

            DB::commit();
            $request->session()->flash('success','Stock Variation Restored Successfully');
        } catch (\Exception $e) {
            $request->session()->flash('danger',$e->getMessage());
        }
    }

    public function expunge(Request $request)
    {
        DB::beginTransaction();
        try {
            $stock = WaStockCountVariation::find($request->variation);
            StockExpungedVariation::create([
                'user_id' =>$stock->user_id,
                "wa_location_and_store_id" => $stock->wa_location_and_store_id,
                "wa_inventory_item_id" => $stock->wa_inventory_item_id,
                "category_id" => $stock->category_id,
                "quantity_recorded" => $stock->quantity_recorded,
                "current_qoh" => $stock->current_qoh,
                "variation" => $stock->variation,
                "uom_id" => $stock->uom_id,
                "reference" => $stock->reference,
                "created_on" => $stock->created_at,
                "expunged_by" => Auth::user()->id,
            ]);
            $stock->delete();
            
            DB::commit();
            $request->session()->flash('success','Stock Variance Expunged Successfully');
        } catch (\Exception $e) {
            $request->session()->flash('danger',$e->getMessage());
        }
    }

    public function expunged_entries()
    {
        if (!can('view', 'stock-expunge-entries')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Expunged Entries';
        $model= 'stock-expunged-entries';

        $breadcum = [
            'Stock Processing' => '',
            $title => ''
        ];

        $permission = $this->mypermissionsforAModule();

        $stock = StockExpungedVariation::with('getInventoryItemDetail','getUomDetail','expungedBy');
        if(request()->filled('bin')){
            $stock->where('uom_id', request()->bin);
        }
        if(request()->start_date && request()->end_date){
            $stock->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
        }
        if (request()->wantsJson()) {
            return DataTables::of($stock)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($date) {
                        return date('Y-m-d',strtotime($date->created_at));
                    })
                    ->editColumn('created_on', function ($date) {
                        return date('Y-m-d',strtotime($date->created_on));
                    })
                    ->editColumn('expunged_by.name', function ($date) {
                        return $date->expungedBy ? $date->expungedBy->name : '-';
                    })
                    ->editColumn('variation',function($variation){
                        return number_format($variation->variation);
                    })
                    ->toJson();
        }

        if (request()->filled('type')) {
            if (request()->type == 'pdf') {
                $stocks = $stock->get();
                $startDate = request()->start_date;
                $endDate = request()->end_date;
                $branch = '-';
                
                $pdf = PDF::loadView('admin.stock_processing.expunged_entries.pdf', compact('stocks','startDate','endDate','branch'));
                $report_name = 'expunged_entries_' . date('Y_m_d_H_i_A');
                return $pdf->download($report_name . '.pdf');
            }
        }
        $bins = WaUnitOfMeasure::get();
        
        return view('admin.stock_processing.expunged_entries.index', compact('title','model','breadcum','permissions','bins'));
    }
}
