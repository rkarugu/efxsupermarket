<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Session;
use Illuminate\Support\Carbon;
use App\User;
use App\Http\Controllers\Controller;
use App\Model\WaGlTran;
use App\Model\TaxManager;
use App\Models\StockDebtor;
use App\Model\WaStockMove;
use App\Models\StockDebtorTran;
use App\Models\StockDebtorTranItem;
use App\Model\WaInventoryItem;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaCompanyPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Model\Restaurant;
use App\Model\WaEsdDetails;
use App\Models\WaStockCountVariation;

class StockAdjustmentController extends Controller
{
    public function stock_processing_sales()
    {
        if (!can('view', 'stock-processing-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing Sales';
        $model= 'stock-processing-sales';

        $breadcum = [
            'Stock Debtors' => '',
            $title => ''
        ];

        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::all();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $routes = DB::table('routes')->select('id', 'route_name')->get();
        } else {
            $routes = DB::table('routes')->where('restaurant_id', $authuser->userRestaurent->id)->select('id', 'route_name')->get();
        }

        $stockDates = DB::table('wa_stock_count_variation')
                            ->where('wa_stock_count_variation.is_processed',1)
                            ->join('stock_debtor_tran_items','stock_debtor_tran_items.stock_count_variation_id','wa_stock_count_variation.id')
                            // ->join('stock_debtor_tran_items','stock_debtor_tran_items.stock_count_variation_id','wa_stock_count_variation.id')
                            ->select(DB::raw("DATE_FORMAT(wa_stock_count_variation.created_at, '%Y-%m-%d') as date"),"stock_debtor_tran_items.stock_debtor_trans_id")
                            ->groupBy('date','stock_debtor_tran_items.stock_debtor_trans_id')
                            ->get();

        $debtors = DB::table('stock_debtor_trans')
            ->join('stock_debtors','stock_debtors.id','stock_debtor_trans.stock_debtors_id')
            ->join('users','users.id','stock_debtors.employee_id')
            ->join('wa_unit_of_measures','users.wa_unit_of_measures_id','wa_unit_of_measures.id')
            ->join('wa_location_and_stores','wa_location_and_stores.id','users.wa_location_and_store_id')
            ->select('stock_debtor_trans.id',
                'stock_debtor_trans.document_no',
                'stock_debtor_trans.created_at',
                'users.name',
                'wa_location_and_stores.location_name',
                'stock_debtor_trans.total',
                'wa_unit_of_measures.title as uom',
                DB::RAW('(select wa_esd_details.description from wa_esd_details where wa_esd_details.invoice_number = stock_debtor_trans.document_no ORDER BY id DESC limit 1) as esd_status'),
            )
            ->where('stock_debtor_trans.document_no','like','SAS%');
            if(request()->start_date && request()->end_date){
                $debtors->whereBetween('stock_debtor_trans.created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
            }
            if (request()->filled('branch')) {
                $debtors->where('users.restaurant_id',request()->branch);
            }
            $debtors->orderBy('created_at','desc');
        if (request()->wantsJson()) {
            return DataTables::of($debtors)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($debtors){
                        return date('Y-m-d',strtotime($debtors->created_at));
                    })
                    ->editColumn('total', function($debtors){
                        return manageAmountFormat($debtors->total);
                    })
                    ->addColumn('stock_date',function($date) use($stockDates){
                        $dates = $stockDates->where('stock_debtor_trans_id',$date->id)->first();
                        if($dates){
                            return date('d-m-Y',strtotime($dates->date));
                        }
                        return '-';
                    })
                    ->addColumn('bin',function($date) use($stockDates){
                        $dates = $stockDates->where('stock_debtor_trans_id',$date->id)->first();
                        if($dates){
                            return date('d-m-Y',strtotime($dates->date));
                        }
                        return '-';
                    })
                    ->with('grand_total',function() use($debtors){
                        return manageAmountFormat($debtors->get()->sum('total'));
                    })
                    ->toJson();
        }

        if (request()->get('manage-request') && request()->get('manage-request') == 'PDF') {
            $debtors = $debtors->get();
            $startDate = request()->start_date? date('d/m/y',strtotime(request()->start_date)) : '-';
            $endDate = request()->end_date? date('d/m/y',strtotime(request()->end_date)): '-';
            $branch = '-';
            if (request()->filled('branch')) {
                $data = Restaurant::find(request()->branch);
                $branch = $data->name;
            }
            
            $pdf = PDF::loadView('admin.stock_processing.sale.pdf', compact('debtors','startDate','endDate','branch'));
            $report_name = 'stock_sales_short_' . date('Y_m_d_H_i_A');
            return $pdf->download($report_name . '.pdf');
        }
        
        return view('admin.stock_processing.sale.list', compact('title','model','breadcum','permissions','branches'));
    }

    public function stock_processing_sales_show($id)
    {
        if (!can('show', 'stock-processing-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing Sales';
        $model= 'stock-processing-sales';
        

        $breadcum = [
            'Stock Debtors' => '',
            $title => ''
        ];

        $debtor = StockDebtorTran::with('debtor','debtor.employee','items')->find($id);
        $esd_status = DB::table('wa_esd_details')->select('wa_esd_details.description')
        ->where('invoice_number',$debtor->document_no)
        ->orderBy('id','desc')
        ->first();     

        return view('admin.stock_processing.sale.view', compact('title','model','breadcum','permissions','debtor','esd_status'));
    }

    public function stock_processing_sales_add()
    {
        if (!can('add', 'stock-processing-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing - Sales';
        $model= 'stock-processing-sales';
        
        $breadcum = [
            'Stock Processing - Sales' => '',
            $title => ''
        ];

        $employees = StockDebtor::with('employee')->get();
        return view('admin.stock_processing.sale.create', compact('title','model','breadcum','permissions','employees'));
    }

    public function stock_processing_sales_store(Request $request)
    {
        if (!can('add', 'stock-processing-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        try{
        $validator = Validator::make($request->all(),[
            'internal_debtor'=>'required|exists:stock_debtors,id',
            'entry_date'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ], 422);
        }

        $check = DB::transaction(function () use ($request){
            $date = date('Y-m-d',strtotime($request->entry_date));
            $items = WaStockCountVariation::with('getInventoryItemDetail','getInventoryItemDetail.getTaxesOfItem')
                ->where('variation','<',0);
            if($request->pend_entry){
                $items->whereNotIn('id',$request->pend_entry);
            }
            $items = $items->whereBetween('created_at',[$date.' 00:00:00',$date.' 23:59:59'])
                ->where('is_processed',0)
                ->where('uom_id',$request->employee_bin_location_id)
                ->get();
            if (count($items)) {
                $accountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                
                $series_module = WaNumerSeriesCode::where('module', 'STOCK_ADJUSTMENT_SALES')->first();
                $lastNumberUsed = $series_module->last_number_used;
                $newNumber = (int)$lastNumberUsed + 1;
                $newCode = $series_module->code."-".str_pad($newNumber,5,"0",STR_PAD_LEFT);
                $series_module->update(['last_number_used' => $newNumber]);
                
                $debtorInfo = StockDebtor::with('employee')->find($request->internal_debtor);

                $StockDebtorTran = StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_debtors_id' => $request->internal_debtor,
                    'document_no' => $newCode,
                ]);
                        
                $totalSalesInclusive = 0;
                $vatAmount = 0;
                    foreach ($items as $key => $item) {

                        $inventoryItem = WaInventoryItem::find($item->wa_inventory_item_id);
                        $stockDebtor = StockDebtor::with('employee')->where('id',$request->internal_debtor)->first();
                        
                        $sellingPrice = $item->getInventoryItemDetail->selling_price;
                        $quantity = abs($item->variation);
                        $total = $sellingPrice * $quantity;
                        $current_qoh = DB::table('wa_stock_moves')
                        ->where('wa_inventory_item_id',$inventoryItem->id)
                        ->where('wa_location_and_store_id', $debtorInfo->employee->wa_location_and_store_id)
                        ->sum('qauntity');

                        $isProcessed = 0;
                        $stockMoveId = NULL;
                        $quantityPending = $quantity;
                        // CHECK QOH
                        if ($current_qoh >= $quantity) {
                            $stockMove = WaStockMove::create([
                                'user_id' => Auth::user()->id,
                                'grn_type_number' => $series_module->type_number,
                                'grn_last_nuber_used' => $series_module->last_number_used,
                                'qauntity' => -($quantity),
                                'new_qoh' => $current_qoh - $quantity,
                                'standard_cost' => $inventoryItem->standard_cost,
                                'selling_price' => $sellingPrice,
                                'price' => $sellingPrice,
                                'stock_id_code' => $inventoryItem->stock_id_code,
                                'wa_inventory_item_id' => $inventoryItem->id,
                                'wa_location_and_store_id' => $stockDebtor->employee->wa_location_and_store_id,
                                'restaurant_id' => $stockDebtor->employee->restaurant_id,
                                'document_no' => $newCode,
                                'refrence' => $stockDebtor->employee->name .'/'.$newCode,
                                'total_cost' => $total,
                            ]);
                            $isProcessed = 1;
                            $stockMoveId = $stockMove->id;
                            $quantityPending = 0;
                        } elseif($current_qoh < $quantity && $current_qoh !=0){
                            $quantityPending = $quantity - $current_qoh;
                            $stockMove = WaStockMove::create([
                                'user_id' => Auth::user()->id,
                                'grn_type_number' => $series_module->type_number,
                                'grn_last_nuber_used' => $series_module->last_number_used,
                                'qauntity' => -($current_qoh),
                                'new_qoh' => 0,
                                'standard_cost' => $inventoryItem->standard_cost,
                                'selling_price' => $sellingPrice,
                                'price' => $sellingPrice,
                                'stock_id_code' => $inventoryItem->stock_id_code,
                                'wa_inventory_item_id' => $inventoryItem->id,
                                'wa_location_and_store_id' => $stockDebtor->employee->wa_location_and_store_id,
                                'restaurant_id' => $stockDebtor->employee->restaurant_id,
                                'document_no' => $newCode,
                                'refrence' => $stockDebtor->employee->name .'/'.$newCode,
                                'total_cost' => $total,
                            ]);
                            $isProcessed = 0;
                            $stockMoveId = $stockMove->id;
                        }
                        
                        $totalSalesInclusive = $total + $totalSalesInclusive;
                        $vat = 0;
                        $vatPercentage = $item->getInventoryItemDetail->getTaxesOfItem->tax_value;
        
                        if ($vatPercentage > 0) {
                            $vat = (($vatPercentage / (100 + $vatPercentage)) * $sellingPrice) * $quantity;
                        }
                        $vatAmount += $vat;
                        
                        StockDebtorTranItem::create([
                            'stock_debtor_trans_id' => $StockDebtorTran->id,
                            'stock_debtors_id' => $request->internal_debtor,
                            'inventory_item_id' => $inventoryItem->id,
                            'quantity' => $quantity,
                            'quantity_pending' => $quantityPending,
                            'document_no' => $newCode,
                            'stock_moves_id' => $stockMoveId,
                            'price' => $sellingPrice,
                            'vat' => $vat,
                            'vat_percentage' => $vatPercentage,
                            'discount' => 0,
                            'discount_percentage' => 0,
                            'total' => $total,
                            'is_processed'=>$isProcessed,
                            'stock_count_variation_id' => $item->id,
                            'stock_date' => $item->created_at,
                            'uom_id' => $item->uom_id
                        ]);
        
                        // Update WaStockCountVariation 
                        WaStockCountVariation::where('id',$item->id)->update(['is_processed'=>1]);
                    }
         
                    $totalSalesExclusive = $totalSalesInclusive - $vatAmount;
        
                    $salesAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('account_code', '56002-003')->first();
                    $salesCredit = new WaGlTran();
                    $salesCredit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $salesCredit->reference = $debtorInfo->employee->name;
                    $salesCredit->trans_date = Carbon::now();
                    $salesCredit->restaurant_id = $debtorInfo->employee->restaurant_id;
                    $salesCredit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                    // $salesCredit->grn_last_used_number = $series_module?->last_number_used;
                    $salesCredit->transaction_type = 'STOCK_PROCESSING_SALES';
                    $salesCredit->transaction_no = $newCode;
                    $salesCredit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                    $salesCredit->account = $salesAccount->account_code;
                    $salesCredit->amount = $totalSalesExclusive * -1;
                    $salesCredit->save();
        
                    $taxManager = TaxManager::find(1);
                    $vatControlAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('id', $taxManager->output_tax_gl_account)->first();
                    $vatCredit = new WaGlTran();
                    $vatCredit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $vatCredit->reference = $debtorInfo->employee->name;
                    $vatCredit->trans_date = Carbon::now();
                    $vatCredit->restaurant_id = $debtorInfo->employee->restaurant_id;
                    $vatCredit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                    // $vatCredit->grn_last_used_number = $series_module?->last_number_used;
                    $vatCredit->transaction_type = 'STOCK_PROCESSING_SALES';
                    $vatCredit->transaction_no = $newCode;
                    $vatCredit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                    $vatCredit->account = $vatControlAccount->account_code;
                    $vatCredit->amount = $vatAmount * -1;
                    $vatCredit->save();
        
                    $companyPreferences = WaCompanyPreference::find(1);
                    $debtorsControlAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('id', $companyPreferences->debtors_control_gl_account)->first();
                    $debtorsDebit = new WaGlTran();
                    $debtorsDebit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                    $debtorsDebit->reference = $debtorInfo->employee->name;
                    $debtorsDebit->trans_date = Carbon::now();
                    $debtorsDebit->restaurant_id = $debtorInfo->employee->restaurant_id;
                    $debtorsDebit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                    // $debtorsDebit->grn_last_used_number = $series_module?->last_number_used;
                    $debtorsDebit->transaction_type = 'STOCK_PROCESSING_SALES';
                    $debtorsDebit->transaction_no = $newCode;
                    $debtorsDebit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                    $debtorsDebit->account = $debtorsControlAccount->account_code;
                    $debtorsDebit->amount = $totalSalesInclusive;
                    $debtorsDebit->save();
        
                    DB::table('stock_debtor_trans')->where('id',$StockDebtorTran->id)->update(['total'=>$totalSalesInclusive]);
                    
                    if($request->pend_entry){
                        // Update WaStockCountVariation To Pending
                        foreach ($request->pend_entry as $key => $item) {
                            WaStockCountVariation::where('id',$item)->update(['is_processed'=>2]);
                        }
                    }

                    return $StockDebtorTran->id;
                }

                if($request->pend_entry){
                    // Update WaStockCountVariation To Pending
                    foreach ($request->pend_entry as $key => $item) {
                        WaStockCountVariation::where('id',$item)->update(['is_processed'=>2]);
                    }
                }
            return 'No Items';
        });
        
        if($check){
            if($check == "No Items"){
                return response()->json([
                    'result'=>1,
                    'message'=>'Stock Debtor Trans Added Successfully.',
                    ], 200);         
            }
            
            $this->signInvoice($check);

            return response()->json([
                'result'=>1,
                'message'=>'Stock Debtor Trans Added Successfully.',
                'id'=>$check], 200);         
        }
        // return response()->json(['result'=>-1,'message'=>'Something went wrong'], 500); 
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
    }

    public function stock_processing_sales_edit(Request $request, $id)
    {
        if (!can('edit', 'stock-processing-sales')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing - Sales';
        $model= 'stock-processing-sales';
        
        $breadcum = [
            'Stock Processing - Sales' => '',
            $title => ''
        ];

        $employees = StockDebtor::with('employee')->get();
        $trans = StockDebtorTran::with('debtor','debtor.employee','debtor.employee.location_stores','items','items.inventoryItem','items.inventoryItem.packSize')->find($id);
        
        return view('admin.stock_processing.sale.edit', compact('title','model','breadcum','permissions','employees','trans'));
    }

    public function stock_processing_sales_file($format,$id)
    {
        $list = StockDebtorTran::with('debtor','debtor.employee','debtor.employee.location_stores','items','items.inventoryItem','items.uom')->find($id);

            if ($format == 'PDF') {                
                $esd_details = WaEsdDetails::where('invoice_number', $list->document_no)->first();
                $pdf = PDF::loadView('admin.stock_processing.sale.pdf_single', compact('list','esd_details'));
                $report_name = 'stock_sales_short_' . date('Y_m_d_H_i_A');
                return $pdf->download($report_name . '.pdf');
            }
            if ($format == 'PDF_List') {
                $pdf = PDF::loadView('admin.stock_processing.sale.pdf_single_list', compact('list'));
                $report_name = 'debtor_stock_sales_short_' . date('Y_m_d_H_i_A');
                return $pdf->download($report_name . '.pdf');
            }
    }

    public function stock_processing_return()
    {
        if (!can('view', 'stock-processing-return')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing Return';
        $model= 'stock-processing-return';
        

        $breadcum = [
            'Stock Debtors' => '',
            $title => ''
        ];

        $authuser = Auth::user();
        $userwithrestaurants = $authuser->load('userRestaurent');
        $isAdmin = $authuser->role_id == 1;
        $permission = $this->mypermissionsforAModule();

        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $branches = Restaurant::all();
        } else {
            $branches = Restaurant::where('id', $authuser->userRestaurent->id)->get();
        }

        if ($isAdmin && !isset($permission['employees' . '___view_all_branches_data'])) {
            $routes = DB::table('routes')->select('id', 'route_name')->get();
        } else {
            $routes = DB::table('routes')->where('restaurant_id', $authuser->userRestaurent->id)->select('id', 'route_name')->get();
        }

        $stockDates = DB::table('wa_stock_count_variation')
                    ->where('wa_stock_count_variation.is_processed',1)
                    ->join('stock_debtor_tran_items','stock_debtor_tran_items.stock_count_variation_id','wa_stock_count_variation.id')
                    ->select(DB::raw("DATE_FORMAT(wa_stock_count_variation.created_at, '%Y-%m-%d') as date"),"stock_debtor_tran_items.stock_debtor_trans_id")
                    ->groupBy('date','stock_debtor_tran_items.stock_debtor_trans_id')
                    ->get();
                    
        $debtors = DB::table('stock_debtor_trans')
            ->join('stock_debtors','stock_debtors.id','stock_debtor_trans.stock_debtors_id')
            ->join('users','users.id','stock_debtors.employee_id')
            ->join('wa_unit_of_measures','users.wa_unit_of_measures_id','wa_unit_of_measures.id')
            ->join('wa_location_and_stores','wa_location_and_stores.id','users.wa_location_and_store_id')
            ->select('stock_debtor_trans.id',
                'stock_debtor_trans.document_no',
                'stock_debtor_trans.created_at',
                'users.name',
                'wa_location_and_stores.location_name',
                'wa_unit_of_measures.title as uom',
                DB::raw("(select sum(total) from stock_debtor_tran_items where stock_debtor_trans_id =stock_debtor_trans.id) as total"),
                DB::RAW('(select wa_esd_details.description from wa_esd_details where wa_esd_details.invoice_number = stock_debtor_trans.document_no ORDER BY id DESC limit 1) as esd_status'),
            )
            ->where('stock_debtor_trans.document_no','like','SAR%');
            if(request()->start_date && request()->end_date){
                $debtors->whereBetween('stock_debtor_trans.created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
            }
            if (request()->filled('branch')) {
                $debtors->where('users.restaurant_id',request()->branch);
            }
            $debtors->orderBy('created_at','desc');
        if (request()->wantsJson()) {
            return DataTables::of($debtors)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($debtors){
                        return date('Y-m-d',strtotime($debtors->created_at));
                    })
                    ->editColumn('total', function($debtors){
                        return manageAmountFormat(abs($debtors->total));
                    })
                    ->addColumn('stock_date',function($date) use($stockDates){
                        
                        $dates = $stockDates->where('stock_debtor_trans_id',$date->id)->first();
                        if($dates){
                            return date('d-m-Y',strtotime($dates->date));
                        }
                        return '-';
                    })
                    ->with('grand_total',function() use($debtors){
                        return manageAmountFormat(abs($debtors->get()->sum('total')));
                    })
                    ->toJson();
        }

        if (request()->get('manage-request') && request()->get('manage-request') == 'PDF') {
            $debtors = $debtors->get();
            $startDate = request()->start_date? date('d/m/y',strtotime(request()->start_date)) : '-';
            $endDate = request()->end_date? date('d/m/y',strtotime(request()->end_date)): '-';
            $branch = '-';
            if (request()->filled('branch')) {
                $data = Restaurant::find(request()->branch);
                $branch = $data->name;
            }
            
            $pdf = PDF::loadView('admin.stock_processing.return.pdf', compact('debtors','startDate','endDate','branch'));
            $report_name = 'stock_return_excess_' . date('Y_m_d_H_i_A');
            return $pdf->download($report_name . '.pdf');
        }
        
        return view('admin.stock_processing.return.list', compact('title','model','breadcum','permissions','branches'));
    }

    public function stock_processing_return_show($id)
    {
        if (!can('show', 'stock-processing-return')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing Return';
        $model= 'stock-processing-return';
        

        $breadcum = [
            'Stock Debtors' => '',
            $title => ''
        ];

        $debtor = StockDebtorTran::with('debtor','debtor.employee','items')->find($id);
        $esd_status = DB::table('wa_esd_details')->select('wa_esd_details.description')
        ->where('invoice_number',$debtor->document_no)
        ->orderBy('id','desc')
        ->first();  
        
        return view('admin.stock_processing.return.view', compact('title','model','breadcum','permissions','debtor','esd_status'));
    }

    public function stock_processing_return_add()
    {
        if (!can('add', 'stock-processing-return')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $permissions = $this->mypermissionsforAModule();
        $title = 'Stock Processing - Returns';
        $model= 'stock-processing-return';
        
        $breadcum = [
            'Stock Processing - Returns' => '',
            $title => ''
        ];

        $employees = StockDebtor::with('employee')->get();
        return view('admin.stock_processing.return.create', compact('title','model','breadcum','permissions','employees'));
    }

    public function stock_processing_return_store(Request $request)
    {
        if (!can('add', 'stock-processing-return')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        try {

        $validator = Validator::make($request->all(),[
            'internal_debtor'=>'required|exists:stock_debtors,id',
            'entry_date'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validator->errors()
            ], 422);
        }

        $check = DB::transaction(function () use ($request){
            $date = date('Y-m-d',strtotime($request->entry_date));
            $items = WaStockCountVariation::with('getInventoryItemDetail','getInventoryItemDetail.getTaxesOfItem')
                ->where('variation','>',0);
                if($request->pend_entry){
                    $items->whereNotIn('id',$request->pend_entry);
                }
                $items = $items->whereBetween('created_at',[$date.' 00:00:00',$date.' 23:59:59'])
                    ->where('is_processed',0)
                    ->where('uom_id',$request->employee_bin_location_id)
                    ->get();

            if (count($items)) {        
                $accountingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();

                $debtorInfo = StockDebtor::with('employee')->find($request->internal_debtor);
                        
                $series_module = WaNumerSeriesCode::where('module', 'STOCK_ADJUSTMENT_RETURN')->first();
                $lastNumberUsed = $series_module->last_number_used;
                $newNumber = (int)$lastNumberUsed + 1;
                $newCode = $series_module->code.'-'.str_pad($newNumber,5,"0",STR_PAD_LEFT);
                $series_module->update(['last_number_used' => $newNumber]);
                
                $StockDebtorTran = StockDebtorTran::create([
                    'created_by'=>Auth::user()->id,
                    'stock_debtors_id' => $request->internal_debtor,
                    'document_no' => $newCode,
                ]);

                
                $totalSalesInclusive = 0;
                $vatAmount = 0;

                foreach ($items as $item) {
                    
                    $inventoryItem = WaInventoryItem::find($item->wa_inventory_item_id);
                    $stockDebtor = StockDebtor::with('employee')->where('id',$request->internal_debtor)->first();

                    $sellingPrice = $item->getInventoryItemDetail->selling_price;
                    $quantity = abs($item->variation);
                    $total = $sellingPrice * $quantity;
                    $current_qoh = DB::table('wa_stock_moves')
                    ->where('wa_inventory_item_id',$inventoryItem->id)
                    ->where('wa_location_and_store_id', $debtorInfo->employee->wa_location_and_store_id)
                    ->sum('qauntity');

                    
                    $stockMove = WaStockMove::create([
                        'user_id' => Auth::user()->id,
                        'grn_type_number' => $series_module->type_number,
                        'grn_last_nuber_used' => $series_module->last_number_used,
                        'qauntity' => $quantity,
                        'new_qoh' => $current_qoh + $quantity,
                        'standard_cost' => $inventoryItem->standard_cost,
                        'selling_price' => $sellingPrice,
                        'price' => $sellingPrice,
                        'stock_id_code' => $inventoryItem->stock_id_code,
                        'wa_inventory_item_id' => $inventoryItem->id,
                        'wa_location_and_store_id' => $stockDebtor->employee->wa_location_and_store_id,
                        'restaurant_id' => $stockDebtor->employee->restaurant_id,
                        'document_no' => $newCode,
                        'refrence' => $stockDebtor->employee->name .'/'.$newCode,
                        'total_cost' => $total,
                    ]);                    

                    $totalSalesInclusive = $total + $totalSalesInclusive;
                    $vat = 0;
                    $vatPercentage = $item->getInventoryItemDetail->getTaxesOfItem->tax_value;

                    if ($vatPercentage > 0) {
                        $vat = (($vatPercentage / (100 + $vatPercentage)) * $sellingPrice) * $quantity;
                    }
                    $vatAmount += $vat;
                    
                    StockDebtorTranItem::create([
                        'stock_debtor_trans_id' => $StockDebtorTran->id,
                        'stock_debtors_id' => $request->internal_debtor,
                        'inventory_item_id' => $inventoryItem->id,
                        'quantity' => $quantity,
                        'document_no' => $newCode,
                        'stock_moves_id' => $stockMove->id,
                        'price' => -($sellingPrice),
                        'vat' => $vat,
                        'vat_percentage' => $vatPercentage,
                        'discount' => 0,
                        'discount_percentage' => 0,
                        'total' => -($total),
                        'is_processed'=>1,
                        'stock_count_variation_id' => $item->id,
                        'stock_date' => $item->created_at,
                        'uom_id' => $item->uom_id
                    ]);

                    // Update WaStockCountVariation 
                    WaStockCountVariation::where('id',$item->id)->update(['is_processed'=>1]);

                }

                // $totalSalesInclusive = $this->internalRequisition->getRelatedItem()->sum('total_cost_with_vat');
                // $vatAmount = $this->internalRequisition->getRelatedItem()->sum('vat_amount');
                $totalSalesExclusive = $totalSalesInclusive - $vatAmount;

                $salesAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('account_code', '56002-003')->first();
                $salesCredit = new WaGlTran();
                $salesCredit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $salesCredit->reference = $debtorInfo->employee->name;
                $salesCredit->trans_date = Carbon::now();
                $salesCredit->restaurant_id = $debtorInfo->employee->restaurant_id;
                $salesCredit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                // $salesCredit->grn_last_used_number = $series_module?->last_number_used;
                $salesCredit->transaction_type = 'STOCK_PROCESSING_RETURN';
                $salesCredit->transaction_no = $newCode;
                $salesCredit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                $salesCredit->account = $salesAccount->account_code;
                $salesCredit->amount = $totalSalesExclusive;
                $salesCredit->save();

                $taxManager = TaxManager::find(1);
                $vatControlAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('id', $taxManager->output_tax_gl_account)->first();
                $vatCredit = new WaGlTran();
                $vatCredit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $vatCredit->reference = $debtorInfo->employee->name;
                $vatCredit->trans_date = Carbon::now();
                $vatCredit->restaurant_id = $debtorInfo->employee->restaurant_id;
                $vatCredit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                // $vatCredit->grn_last_used_number = $series_module?->last_number_used;
                $vatCredit->transaction_type = 'STOCK_PROCESSING_RETURN';
                $vatCredit->transaction_no = $newCode;
                $vatCredit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                $vatCredit->account = $vatControlAccount->account_code;
                $vatCredit->amount = $vatAmount;
                $vatCredit->save();

                $companyPreferences = WaCompanyPreference::find(1);
                $debtorsControlAccount = DB::table('wa_charts_of_accounts')->select('id', 'account_code')->where('id', $companyPreferences->debtors_control_gl_account)->first();
                $debtorsDebit = new WaGlTran();
                $debtorsDebit->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $debtorsDebit->reference = $debtorInfo->employee->name;
                $debtorsDebit->trans_date = Carbon::now();
                $debtorsDebit->restaurant_id = $debtorInfo->employee->restaurant_id;
                $debtorsDebit->tb_reporting_branch = $debtorInfo->employee->restaurant_id;
                // $debtorsDebit->grn_last_used_number = $series_module?->last_number_used;
                $debtorsDebit->transaction_type = 'STOCK_PROCESSING_RETURN';
                $debtorsDebit->transaction_no = $newCode;
                $debtorsDebit->narrative = $debtorInfo->employee->uom->title."::".$debtorInfo->employee->location_stores->location_name;
                $debtorsDebit->account = $debtorsControlAccount->account_code;
                $debtorsDebit->amount = $totalSalesInclusive * -1;
                $debtorsDebit->save();

                DB::table('stock_debtor_trans')->where('id',$StockDebtorTran->id)->update(['total'=>-($totalSalesInclusive)]);
                
                if($request->pend_entry){
                    // Update WaStockCountVariation To Pending
                    foreach ($request->pend_entry as $key => $item) {
                        WaStockCountVariation::where('id',$item)->update(['is_processed'=>2]);
                    }
                }

                return $StockDebtorTran->id;
            }

        if($request->pend_entry){
            // Update WaStockCountVariation To Pending
            foreach ($request->pend_entry as $key => $item) {
                WaStockCountVariation::where('id',$item)->update(['is_processed'=>2]);
            }
        }
        return 'No Items';
        });
        
        if($check){
            if($check == "No Items"){
                return response()->json([
                    'result'=>1,
                    'message'=>'Stock Debtor Trans Added Successfully.',
                    ], 200);         
            }
            
            $this->signInvoice($check);

            return response()->json([
                'result'=>1,
                'message'=>'Stock Debtor Trans Added Successfully.',
                'id'=>$check], 200);         
        }
        return response()->json(['result'=>-1,'message'=>'Something went wrong'], 500);  
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
    }

    public function stock_processing_return_file($format,$id)
    {
        $list = StockDebtorTran::with('debtor','debtor.employee','debtor.employee.location_stores','items','items.inventoryItem','items.uom')->find($id);

        if ($format == 'PDF') {                
            $esd_details = WaEsdDetails::where('invoice_number', $list->document_no)->first();
            $pdf = PDF::loadView('admin.stock_processing.return.pdf_single', compact('list','esd_details'));
            $report_name = 'stock_return_excess_' . date('Y_m_d_H_i_A');
            // return $pdf->stream();
            return $pdf->download($report_name . '.pdf');
        }

        if ($format == 'PDF_List') {
            $pdf = PDF::loadView('admin.stock_processing.return.pdf_single_list', compact('list'));
            $report_name = 'debtor_stock_return_excess_' . date('Y_m_d_H_i_A');
            return $pdf->download($report_name . '.pdf');
        }

    }

    private function signInvoice($id): void
    {
        try {
            $invoice = StockDebtorTran::find($id);
            $settings = getAllSettings();
            $clientPin = $settings['PIN_NO'];
            $esdUrl = $settings['ESD_URL'];
            $apiUrl = "$esdUrl/api/sign?invoice+1";
            $vatAmount = $invoice->items->sum('vat');
            $payload = [
                "invoice_date" => Carbon::parse($invoice->created_at)->format('d_m_Y'),
                "invoice_number" => $invoice->document_no,
                "invoice_pin" => $clientPin,
                "customer_pin" => "",
                "customer_exid" => "",
                "grand_total" => number_format(abs($invoice->items->sum('total')), 2),
                "net_subtotal" => number_format(abs($invoice->items->sum('total')) - $vatAmount, 2),
                "tax_total" => number_format($vatAmount, 2),
                "net_discount_total" => "0",
                "sel_currency" => "KSH",
                "rel_doc_number" => "",
                "items_list" => []
            ];

            foreach ($invoice->items as $item) {
                $inventoryItem = DB::table('wa_inventory_items')->find($item->inventory_item_id);
                $price = manageAmountFormat(abs($item->price));
                $quantity = abs($item->quantity);
                $itemTotal = $quantity * abs($item->price);
                $itemTotal = manageAmountFormat($itemTotal);
                $line = "$inventoryItem->title $quantity $price $itemTotal";
                if ($inventoryItem->hs_code) {
                    $line = "$inventoryItem->hs_code " . $line;
                }
                $payload['items_list'][] = $line;
            }
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ZxZoaZMUQbUJDljA7kTExQ==',
            ])->post($apiUrl, $payload);

            $responseData = json_decode($response->body(), true);
            $newEsd = new WaEsdDetails();

            if ($response->ok()) {
                $newEsd->invoice_number = $responseData['invoice_number'];
                $newEsd->cu_serial_number = $responseData['cu_serial_number'];
                $newEsd->cu_invoice_number = $responseData['cu_invoice_number'];
                $newEsd->verify_url = $responseData['verify_url'] ?? null;
                $newEsd->description = $responseData['description'] ?? null;
                $newEsd->status = 1;
                $newEsd->save();
            } else {
                $newEsd->invoice_number = $invoice->document_no;
                $newEsd->description = $response->body();
                $newEsd->status = 0;
                $newEsd->save();
            }
        } catch (\Throwable $e) {
            $newEsd = new WaEsdDetails();
            $newEsd->invoice_number = $invoice->document_no;
            $newEsd->description = $e->getMessage();
            $newEsd->status = 0;
            $newEsd->save();
        }
    }

    public function resign_esd($id)
    {
        try {
            $invoice = StockDebtorTran::find($id);
            $settings = getAllSettings();
            $clientPin = $settings['PIN_NO'];
            $esdUrl = $settings['ESD_URL'];
            $apiUrl = "$esdUrl/api/sign?invoice+1";
            
            $vatAmount = 0;
            $payload = [
                "invoice_date" => Carbon::parse($invoice->created_at)->format('d_m_Y'),
                "invoice_number" => $invoice->document_no,
                "invoice_pin" => $clientPin,
                "customer_pin" => "",
                "customer_exid" => "",
                "grand_total" => number_format(abs($invoice->items->sum('total')), 2),
                "net_subtotal" => number_format(abs($invoice->items->sum('total')) - $vatAmount, 2),
                "tax_total" => number_format($vatAmount, 2),
                "net_discount_total" => "0",
                "sel_currency" => "KSH",
                "rel_doc_number" => "",
                "items_list" => []
            ];

            $grandTotal = 0;
            $taxManagers = DB::table('tax_managers')->select('id', 'title', 'tax_value')->get();
            foreach ($invoice->items as $item) {
                $inventoryItem = DB::table('wa_inventory_items')->find($item->inventory_item_id);
                
                $price = abs($item->price);
                $quantity = abs($item->quantity);
                $itemTotal = $quantity * $price;
                $grandTotal += $itemTotal;

                $taxManager = $taxManagers->where('id', $inventoryItem->tax_manager_id)->first();
                if ($taxManager) {
                    $vatRate = (float)$taxManager->tax_value;
                    $vatAmount += (($vatRate / (100 + $vatRate)) * $price) * $quantity;
                }
                
                $itemTotal = manageAmountFormat($itemTotal);
                $price = manageAmountFormat($price);

                $line = "$inventoryItem->title $quantity $price $itemTotal";
                if ($inventoryItem->hs_code) {
                    $line = "$inventoryItem->hs_code " . $line;
                }
                $payload['items_list'][] = $line;                
            }
            
            $payload['tax_total'] = number_format($vatAmount, 2);
            $payload['grand_total'] = number_format($grandTotal, 2);
            $payload['net_subtotal'] = number_format($grandTotal - $vatAmount, 2);
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ZxZoaZMUQbUJDljA7kTExQ==',
            ])->post($apiUrl, $payload);

            $responseData = json_decode($response->body(), true);
            if ($response->ok()) {
                $esdRecord = WaEsdDetails::where('invoice_number', $invoice->document_no)->first();
                $esdRecord->cu_serial_number = $responseData['cu_serial_number'];
                $esdRecord->cu_invoice_number = $responseData['cu_invoice_number'];
                $esdRecord->verify_url = $responseData['verify_url'] ?? null;
                $esdRecord->description = $responseData['description'] ?? null;
                $esdRecord->status = 1;
                $esdRecord->save();

                request()->session()->flash('success', 'Invoice resigned Successfully.');
                return redirect()->back();
            } else {    
                return redirect()->back()->withErrors(['error' => "Resigning failed with {$response->body()}. " . json_encode($payload)]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function resign_esd_return($id)
    {
        try {
            $invoice = StockDebtorTran::find($id);
            $settings = getAllSettings();
            $clientPin = $settings['PIN_NO'];
            $esdUrl = $settings['ESD_URL'];
            $apiUrl = "$esdUrl/api/sign?invoice+1";
            
            $vatAmount = 0;
            $payload = [
                "invoice_date" => Carbon::parse($invoice->created_at)->format('d_m_Y'),
                "invoice_number" => $invoice->document_no,
                "invoice_pin" => $clientPin,
                "customer_pin" => "",
                "customer_exid" => "",
                "grand_total" => number_format(abs($invoice->items->sum('total')), 2),
                "net_subtotal" => number_format(abs($invoice->items->sum('total')) - $vatAmount, 2),
                "tax_total" => number_format($vatAmount, 2),
                "net_discount_total" => "0",
                "sel_currency" => "KSH",
                "rel_doc_number" => "",
                "items_list" => []
            ];

            $grandTotal = 0;
            $taxManagers = DB::table('tax_managers')->select('id', 'title', 'tax_value')->get();
            foreach ($invoice->items as $item) {
                $inventoryItem = DB::table('wa_inventory_items')->find($item->inventory_item_id);
                $price = abs($item->price);
                $quantity = abs($item->quantity);
                $itemTotal = $quantity * $price;
                $grandTotal += $itemTotal;

                $taxManager = $taxManagers->where('id', $inventoryItem->tax_manager_id)->first();
                if ($taxManager) {
                    $vatRate = (float)$taxManager->tax_value;
                    $vatAmount += (($vatRate / (100 + $vatRate)) * $price) * $quantity;
                }
                
                $itemTotal = manageAmountFormat($itemTotal);
                $price = manageAmountFormat($price);

                $line = "$inventoryItem->title $quantity $price $itemTotal";
                if ($inventoryItem->hs_code) {
                    $line = "$inventoryItem->hs_code " . $line;
                }
                $payload['items_list'][] = $line;                
            }
            
            $payload['tax_total'] = number_format($vatAmount, 2);
            $payload['grand_total'] = number_format($grandTotal, 2);
            $payload['net_subtotal'] = number_format($grandTotal - $vatAmount, 2);
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ZxZoaZMUQbUJDljA7kTExQ==',
            ])->post($apiUrl, $payload);

            $responseData = json_decode($response->body(), true);
            if ($response->ok()) {
                $esdRecord = WaEsdDetails::where('invoice_number', $invoice->document_no)->first();
                $esdRecord->cu_serial_number = $responseData['cu_serial_number'];
                $esdRecord->cu_invoice_number = $responseData['cu_invoice_number'];
                $esdRecord->verify_url = $responseData['verify_url'] ?? null;
                $esdRecord->description = $responseData['description'] ?? null;
                $esdRecord->status = 1;
                $esdRecord->save();

                request()->session()->flash('success', 'Invoice resigned Successfully.');
                return redirect()->back();
            } else {    
                return redirect()->back()->withErrors(['error' => "Resigning failed with {$response->body()}. " . json_encode($payload)]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function get_stock_dates($id)
    {
        $dates = WaStockCountVariation::where('uom_id',$id)
                    ->where('is_processed',0);
                    if (request()->type =='excess') {
                        $dates->where('variation','>',0);
                    } else{
                        $dates->where('variation','<',0);
                    }

        $newDates = $dates->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date" ))
                    ->groupBy(DB::raw('Date(created_at)'))
                    ->get()->map(function ($date) {
                        return $date->date;
                    });
        return response()->json($newDates);
    }

    public function get_stock_date_data(Request $request){
        try{
           
        $date = date('Y-m-d',strtotime($request->date));
        $query = WaStockCountVariation::with('getInventoryItemDetail','getInventoryItemDetail.getTaxesOfItem')
                    ->where('uom_id',$request->bin_location);
        if (request()->type =='excess') {
            $query->where('variation','>',0);
        } else{
            $query->where('variation','<',0);
        }
        
        $data = $query->whereBetween('created_at',[$date.' 00:00:00',$date.' 23:59:59'])
                    ->where('is_processed',0)
                    ->get()
                    ->map(function($item){
                        $sellingPrice = $item->getInventoryItemDetail->selling_price;
                        $quantity = abs($item->variation);
                        $total_price = $quantity * $sellingPrice;
                        $vat = 0;
                        $vatPercentage = $item->getInventoryItemDetail->getTaxesOfItem?->tax_value ?? 0;
                        if ($vatPercentage > 0) {
                            $vat = $vat = (($sellingPrice*$vatPercentage)/100) * $quantity;//($vatPercentage/(100 + $vatPercentage)) * $quantity*$sellingPrice;
                        }
                        return [
                            'id' => $item->id,
                            'code' => $item->getInventoryItemDetail->stock_id_code,
                            'title' => $item->getInventoryItemDetail->title,
                            'price' => manageAmountFormat($sellingPrice),
                            'quantity' => number_format($quantity),
                            'total_price' => manageAmountFormat($total_price),
                            'total_price_raw' => $total_price,
                            'vat_type' =>$item->getInventoryItemDetail->getTaxesOfItem?->title ?? 'Not Set',
                            'vat_amount' => manageAmountFormat($vat),
                            'vat_amount_raw' => $vat,
                        ];
                    });
        $total_vat = $data->sum('vat_amount_raw');
        $total_amount = $data->sum('total_price_raw');
        // return ['data'=>$data,'total_vat'=>manageAmountFormat($total_vat),'total_amount'=>manageAmountFormat($total_amount-$total_vat),'total'=>manageAmountFormat($total_amount)];
        return response()->json([
            'data' => $data,
            'total_vat' => manageAmountFormat($total_vat),
            'total_amount' => manageAmountFormat($total_amount - $total_vat),
            'total' => manageAmountFormat($total_amount)
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    }
    }
}
