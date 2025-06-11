<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Model\Restaurant;
use App\Model\WaStockMove;
use App\Models\StockDebtor;
use App\Model\WaInventoryItem;
use App\Models\StockDebtorTran;
use App\Models\StockDebtorTranItem;
use App\Model\WaNumerSeriesCode;
use App\Model\WaUnitOfMeasure;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
 
class StockUncompletedProcessingController extends Controller
{
    public function stock_uncompleted_sales()
    {
        if (!can('view', 'stock-uncompleted-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Uncompleted Sales';
        $model= 'stock-uncompleted-sales';

        $breadcum = [
            'Stock Processing' => '',
            $title => ''
        ];

        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::all();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }


        $debtors = DB::table('stock_debtor_tran_items')
        ->where('stock_debtor_trans.document_no','like','SAS%')
        ->where('stock_debtor_tran_items.is_processed',0)
        ->join('stock_debtor_trans','stock_debtor_tran_items.stock_debtor_trans_id','stock_debtor_trans.id')
        ->join('stock_debtors','stock_debtors.id','stock_debtor_trans.stock_debtors_id')
        ->join('users','users.id','stock_debtors.employee_id')
        ->join('wa_location_and_stores','wa_location_and_stores.id','users.wa_location_and_store_id')
        ->join('wa_inventory_items','wa_inventory_items.id','stock_debtor_tran_items.inventory_item_id')
        ->join('wa_unit_of_measures','wa_unit_of_measures.id','users.wa_unit_of_measures_id')
        ->select(
                'stock_debtor_tran_items.document_no',
                'stock_debtor_tran_items.created_at',
                'stock_debtor_tran_items.quantity',
                'stock_debtor_tran_items.quantity_pending',
                'wa_location_and_stores.location_name',
                'stock_debtor_tran_items.total',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title as item_title',
                'wa_unit_of_measures.title as uom_title',
                'stock_debtor_trans.id as trans_id',
                'stock_debtor_tran_items.stock_date'
            );
        if(request()->start_date && request()->end_date){
            $debtors->whereBetween('stock_debtor_trans.created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
        }
        if (request()->filled('bin')) {
            $debtors->where('wa_unit_of_measures.id',request()->bin);
        }
        $debtors->orderBy('created_at','desc');
        if (request()->wantsJson()) {
            return DataTables::of($debtors)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($debtors){
                        return date('Y-m-d',strtotime($debtors->created_at));
                    })
                    ->editColumn('stock_date', function($debtors){
                        $stockDate='-';
                        if($debtors->stock_date){
                            $stockDate=date('Y-m-d',strtotime($debtors->stock_date));
                        }
                        return $stockDate;
                    })
                    ->editColumn('total', function($debtors){
                        return manageAmountFormat($debtors->total);
                    })
                    
                    ->toJson();
        }

        if (request()->get('manage-request') && request()->get('manage-request') == 'PDF') {
            $debtors = $debtors->get();
            $startDate = request()->start_date? date('d/m/y',strtotime(request()->start_date)) : '-';
            $endDate = request()->end_date? date('d/m/y',strtotime(request()->end_date)): '-';
            $bin = '-';
            if (request()->filled('bin')) {
                $data = WaUnitOfMeasure::find(request()->bin);
                $bin = $data->title;
            }
            
            $pdf = PDF::loadView('admin.stock_processing.uncompleted.sale.pdf', compact('debtors','startDate','endDate','bin'));
            $report_name = 'stock_uncompleted_sale_' . date('Y_m_d_H_i_A');
            return $pdf->download($report_name . '.pdf');
        }

        $bins = WaUnitOfMeasure::all();
        return view('admin.stock_processing.uncompleted.sale.list', compact('title','model','breadcum','permissions','bins'));
    }

    public function stock_uncompleted_sales_show($id)
    {
        if (!can('show', 'stock-uncompleted-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Uncompleted Sales';
        $model= 'stock-uncompleted-sales';
        

        $breadcum = [
            'Stock Processing' => '',
            $title => ''
        ];

        $debtor = StockDebtorTran::with('debtor','debtor.employee','itemsNotProcessed')->find($id); 

        return view('admin.stock_processing.uncompleted.sale.view', compact('title','model','breadcum','permissions','debtor'));
    }

    public function process(Request $request)
    {
        if (!can('process', 'stock-uncompleted-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        DB::beginTransaction();
        try {
            $debtor = StockDebtorTranItem::where('document_no','like','SAS%')
            ->where('is_processed',0)->get(); 
        
            foreach ($debtor as $key => $item) {
                $inventoryItem = WaInventoryItem::find($item->inventory_item_id);
                $stockDebtor = StockDebtor::with('employee')->where('id',$item->stock_debtors_id)->first();

                $stockQuery = DB::table('wa_stock_moves')
                            ->where('wa_inventory_item_id',$inventoryItem->id)
                            ->where('wa_location_and_store_id', $stockDebtor->employee->wa_location_and_store_id);
                $stockMoveInfo = $stockQuery->first();
                $current_qoh = $stockQuery->sum('qauntity');

                $quantity = abs($item->quantity_pending);
                $documentExpoded = explode('-',$item->document_no);
                    
                $series_module = WaNumerSeriesCode::where('code', $documentExpoded[0])->first();
                $sellingPrice = $item->price;
                
                $quantityPending = $quantity;
                if ($current_qoh >= $quantity) {
                    $quantityPending = 0;
                    WaStockMove::create([
                        'user_id' => $debtor->debtorTran->created_by,
                        'grn_type_number' => $series_module->type_number,
                        'grn_last_nuber_used' => (int)$documentExpoded[1],
                        'qauntity' => -($quantity),
                        'new_qoh' => $current_qoh - $quantity,
                        'standard_cost' => $inventoryItem->standard_cost,
                        'selling_price' => $sellingPrice,
                        'price' => $sellingPrice,
                        'stock_id_code' => $inventoryItem->stock_id_code,
                        'wa_inventory_item_id' => $inventoryItem->id,
                        'wa_location_and_store_id' => $stockMoveInfo->wa_location_and_store_id,
                        'restaurant_id' => $stockMoveInfo->restaurant_id,
                        'document_no' => $item->document_no,
                        'refrence' => $stockDebtor->employee->name .'/'.$item->document_no,
                        'total_cost' => $item->total,
                    ]);

                    StockDebtorTranItem::where('id', $item->id)
                        ->update(['is_processed'=>1,'quantity_pending' => $quantityPending]);
                        
                } elseif($current_qoh < $quantity && $current_qoh !=0){
                    $quantityPending = $quantity - $current_qoh;
                    $quantity = $current_qoh;
                    
                    WaStockMove::create([
                        'user_id' => $debtor->debtorTran->created_by,
                        'grn_type_number' => $series_module->type_number,
                        'grn_last_nuber_used' => (int)$documentExpoded[1],
                        'qauntity' => -($quantity),
                        'new_qoh' => 0,
                        'standard_cost' => $inventoryItem->standard_cost,
                        'selling_price' => $sellingPrice,
                        'price' => $sellingPrice,
                        'stock_id_code' => $inventoryItem->stock_id_code,
                        'wa_inventory_item_id' => $inventoryItem->id,
                        'wa_location_and_store_id' => $stockMoveInfo->wa_location_and_store_id,
                        'restaurant_id' => $stockMoveInfo->restaurant_id,
                        'document_no' => $item->document_no,
                        'refrence' => $stockDebtor->employee->name .'/'.$item->document_no,
                        'total_cost' => $item->total,
                    ]);

                    $isProcessed=0;
                    if($quantityPending==0){
                        $isProcessed=1;
                    }
                    StockDebtorTranItem::where('id', $item->id)
                        ->update(['is_processed'=>$isProcessed,'quantity_pending' => $quantityPending]);
                }
                
            }
            request()->session()->flash('success', 'Stock Process Successfully.');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }
        
        return redirect(route('stock-uncompleted-sales.index'));
        
    }
}
