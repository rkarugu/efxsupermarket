<?php

namespace App\Http\Controllers\Admin;

use App\Alert;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Model\WaInventoryPriceHistory;
use App\ItemSupplierDemand;
use App\Mail\DeltaRequest;
use App\Model\WaSupplier;
use App\Mail\SupplierNotification;
use App\Model\User;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryItemSupplierData;
use App\Model\WaStockMove;
use App\Models\PriceTimeline;
use App\WaDemand;
use App\WaDemandItem;
use Illuminate\Support\Facades\Mail;
use PDF;
use Carbon\Carbon;
use Excel;
use App\Exports\PriceTimelineReportDataExport;

class PriceChangeController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'maintain-items';
        $this->title = 'Maintain items';
        $this->pmodule = 'maintain-items';
    }
    public function showBatchChangePage(Request $request): View | RedirectResponse
    {
        $model = 'batch-price-change';
        $title = "Batch Price Change";
        if (!can('manage-standard-cost', 'maintain-items',)) {
            return redirect()->back()->withErrors(['message' => pageRestrictedMessage()]);
        }

        $breadcum = ['Inventory' => route("maintain-items.index"), 'Price Change' => ''];

        $suppliers = DB::table('wa_suppliers')
        ->select('id', 'name')
        ->get();

        // $qohQuery = DB::table('wa_stock_moves')
        // ->select(
        //     'wa_inventory_item_id'
        //     )
        // ->selectRaw('COALESCE(SUM(qauntity), 0) as qoh')
        // ->groupBy('wa_inventory_item_id');
    
        $suppliersSubquery = DB::table('wa_suppliers')
            ->select(
                'wa_inventory_item_suppliers.wa_inventory_item_id',
                DB::raw('GROUP_CONCAT(wa_suppliers.name) as supplier_names'),
                DB::raw('GROUP_CONCAT(wa_suppliers.id) as supplier_ids')
                )
            ->join('wa_inventory_item_suppliers', 'wa_suppliers.id', '=', 'wa_inventory_item_suppliers.wa_supplier_id')
            ->groupBy('wa_inventory_item_suppliers.wa_inventory_item_id');
        $qohSubQuery = "SELECT 
            SUM(qauntity)
        FROM
            `wa_stock_moves`
        WHERE
            `wa_inventory_item_id` = `wa_inventory_items`.`id`";
        
        $inventoryItems = DB::table('wa_inventory_items')
            ->select(
                'wa_inventory_items.id',
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                'wa_inventory_items.percentage_margin',
                // 'quantities.qoh',
                'wa_inventory_items.margin_type',
                'wa_inventory_items.price_list_cost',
                DB::raw('IFNULL(suppliers.supplier_names, "") as supplier_names'),
                DB::raw('IFNULL(suppliers.supplier_ids, "") as supplier_ids'),
                DB::raw("($qohSubQuery) as qoh"),

            )
            // ->leftJoinSub($qohQuery, 'quantities', 'quantities.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->leftJoinSub($suppliersSubquery, 'suppliers', 'suppliers.wa_inventory_item_id', '=', 'wa_inventory_items.id')
            ->where('wa_inventory_items.status', '1')
            ->get()
            ->map(function ($item) {
                $item->supplier_ids = array_map('intval', explode(',', $item->supplier_ids));
                return $item;
            });


        return view('admin.price_change.batch-change', compact('title', 'model', 'suppliers', 'inventoryItems', 'breadcum'));
    }

    public function processBatchPriceChange(Request $request)
    {
        DB::beginTransaction($request->items);
        try {
            if($request->demand_total > 0){
                $demandCode = getCodeWithNumberSeries('DELTA');
                $delta = new WaDemand();
                $delta->demand_no = $demandCode;
                $delta->created_by = $request->user_id;
                $delta->wa_supplier_id = WaSupplier::latest()->where('name', $request->items[0]['supplier_names'])->first()->id;
                $delta->demand_amount = $request->demand_total;
                $delta->edited_demand_amount = $request->demand_total;
                $delta->save();
                updateUniqueNumberSeries('DELTA',$demandCode);

            }

            $vatAmounts = [];
            foreach ($request->items as $row) {
                $inventoryItem = WaInventoryItem::with('taxManager')->find($row['id']);
                $old_cost = $inventoryItem->standard_cost;
                $qoh = WaStockMove::where('stock_id_code', $inventoryItem->stock_id_code)->where('wa_location_and_store_id', 46)->sum('qauntity');
                $inventoryItem->standard_cost = $row['new_cost'];
                $inventoryItem->selling_price = $row['new_price'];
                $old_weighted_average_cost = $inventoryItem->weighted_average_cost;
                if($qoh > 0){
                    $newWeightedCost = (($qoh * $old_cost) + ($qoh * $row['new_cost'])) / ($qoh * 2);
                }else{
                    $newWeightedCost = $inventoryItem->weighted_average_cost;
                }
                $inventoryItem->weighted_average_cost = $row['new_cost'];
                $inventoryItem->price_list_cost = $row['new_price_list_cost'] ?? 0;
                if($inventoryItem->margin_type == 1){
                    $inventoryItem->actual_margin = $row['new_cost'] > 0 ? (($row['new_price'] - $row['new_cost']) / $row['new_cost'] ) * 100 : 0;
                }else{
                    $inventoryItem->actual_margin =  ($row['new_price'] - $row['new_cost']);

                }
                $inventoryItem->save();

                $vatRate = (float)$inventoryItem->taxManager->tax_value;
                $demandAmount = ((float)$row['current_cost'] - (float)$row['new_cost']) * (float)$row['qoh'];
                $vat = ($vatRate * $demandAmount) / (100 + $vatRate);
                array_push($vatAmounts, $vat);

                //update purchase data
                $supplier = WaSupplier::latest()->where('name', $row['supplier_names'])->first();
                if ($supplier) {
                    $supplierId = $supplier->id;
                    $purchaseData = WaInventoryItemSupplierData::where('wa_supplier_id', $supplierId)->where('wa_inventory_item_id', $row['id'])->first();
                    if ($purchaseData) {
                        $purchaseData->price = $row['new_cost'];
                        $purchaseData->save();
                    }
                }
                //save  history
                $history = new WaInventoryPriceHistory();
                $history->wa_inventory_item_id = $row['id'];
                $history->old_standard_cost = $row['current_cost'];
                $history->standard_cost = $row['new_cost'];
                $history->old_selling_price = $row['current_price'];
                $history->selling_price = $row['new_price'];
                $history->old_price_list_cost = $row['price_list_cost'] ?? 0;
                $history->price_list_cost = $row['new_price_list_cost'] ?? 0;
                $history->weighted_cost = $newWeightedCost;
                $history->old_weighted_cost = $old_weighted_average_cost;
                $history->initiated_by = $request->user_id;
                $history->approved_by = $request->user_id;
                $history->status = 'Approved';
                $history->created_at = date('Y-m-d H:i:s');
                $history->updated_at = date('Y-m-d H:i:s');
                $history->block_this = False;

                $history->save();

                $pt = new PriceTimeline();
                $pt->wa_inventory_item_id = $row['id'];
                $pt->current_standard_cost = $row['current_cost'];
                $pt->standart_cost_unit = $row['new_cost'];
                $pt->current_selling_price = $row['current_price'];
                $pt->selling_price = $row['new_price'];
                $pt->user_id = $request->user_id;
                $pt->stock_id_code = $row['stock_id_code'];
                $pt->qoh_before = $row['qoh'];
                $pt->transcation_type = 'Price Change';
                $pt->delta = $request->demand_total; 

                $pt->save();

                //Handle child Items 
                $childItems  = WaInventoryAssignedItems::where('wa_inventory_item_id', $inventoryItem->id)->get();
                if($childItems){
                    foreach ($childItems as $child) {
                        //update item
                       $childItem = WaInventoryItem::find($child->destination_item_id);
                       $childQoh = WaStockMove::where('stock_id_code', $childItem->stock_id_code)->where('wa_location_and_store_id', 46)->sum('qauntity');
                       $childOldWeightedCost = $childItem->standard_cost;
                       if($childQoh > 0){
                        $childNewWeightedCost = (($childQoh * $childItem->standard_cost) + ($childQoh * ((double)$row['new_cost'] / (double)$child->conversion_factor))) / ($childQoh * 2);
                       }else{
                        $childNewWeightedCost = $childItem->weighted_average_cost ;
                       }
                       $childItem->prev_standard_cost = $childItem->standard_cost;
                       $old_child_price_list_cost = $childItem->price_list_cost;
                       $new_price_list_cost = (double)$row['new_price_list_cost'] / (double)$child->conversion_factor;
                       $childItemNewCost = (double)$row['new_cost'] / (double)$child->conversion_factor;
                       $childItem->standard_cost = (double)$row['new_cost'] / (double)$child->conversion_factor;
                       $childItem->weighted_average_cost =  $childNewWeightedCost;
                       $childNewSellingPrice =  ((double)$row['new_cost'] / (double)$child->conversion_factor) * (($childItem->percentage_margin + 100) / 100);
                       $childNewSellingPrice = (ceil($childNewSellingPrice / 5)  * 5);
                       $childItem->selling_price = $childNewSellingPrice;

                       if($childItem->margin_type == 1){
                            $childItem->actual_margin = $childItemNewCost > 0 ? (($childNewSellingPrice - $childItemNewCost) / $childItemNewCost ) * 100 : 0;
                        }else{
                            $childItem->actual_margin =  ($childNewSellingPrice - $childItemNewCost);
        
                        }

                       $childItem->save(); 

                       //save child item history
                        $childHistory = new WaInventoryPriceHistory();
                        $childHistory->wa_inventory_item_id = $childItem->id;
                        $childHistory->old_standard_cost = $childItem->prev_standard_cost ?? 0;
                        $childHistory->standard_cost = $childItem->standard_cost ?? 0;
                        $childHistory->old_price_list_cost = $old_child_price_list_cost;
                        $childHistory->price_list_cost = $new_price_list_cost;
                        $childHistory->weighted_cost = $newWeightedCost;
                        $childHistory->old_weighted_cost = $old_weighted_average_cost;
                        $childHistory->old_selling_price = $childItem->old_selling_price ?? 0;   
                        $childHistory->selling_price = $childItem->selling_price ?? 0;
                        $childHistory->initiated_by = $request->user_id;
                        $childHistory->approved_by = $request->user_id;
                        $childHistory->status = 'Approved';
                        $childHistory->created_at = date('Y-m-d H:i:s');
                        $childHistory->updated_at = date('Y-m-d H:i:s');
                        $childHistory->block_this = False;

                        $childHistory->save();

                        $childpt = new PriceTimeline();
                        $childpt->wa_inventory_item_id = $childItem->id;
                        $childpt->current_standard_cost = $childItem->prev_standard_cost ?? 0;
                        $childpt->standart_cost_unit = $childItem->standard_cost ?? 0;
                        $childpt->current_selling_price = $childItem->old_selling_price ?? 0;   
                        $childpt->selling_price = $childItem->selling_price ?? 0;
                        $childpt->user_id = $request->user_id;
                        $childpt->transcation_type = 'Price Change';
                        $childpt->stock_id_code = $childItem->stock_id_code;
                        $childpt->qoh_before = $childItem->qoh ?? 0;
                        $childpt->$request->demand_total ?? 0; 

                        $childpt->save();

                    }

                }



                //check total demand
                if ($row['demand'] > 0) {
                    $supplier  = WaSupplier::latest()->where('name', $row['supplier_names'])->first()->id;
                    $demand = ItemSupplierDemand::create([
                        'wa_inventory_item_id' => $row['id'],
                        'wa_supplier_id' => $supplier,
                        'current_cost' => $row['current_cost'],
                        'new_cost' => $row['new_cost'],
                        'current_price' => $row['current_price'],
                        'new_price' => $row['new_price'],
                        'demand_quantity' => $row['qoh'],
                    ]);
                    $demand = WaDemandItem::create([
                        'wa_inventory_item_id' => $row['id'],
                        'wa_demand_id' => $delta->id,
                        'current_cost' => $row['current_cost'],
                        'new_cost' => $row['new_cost'],
                        'current_price' => $row['current_price'],
                        'new_price' => $row['new_price'],
                        'demand_quantity' => $row['qoh'],
                    ]);
                }


                }   
                $data = [];
                    foreach ($request->items as $row) {
                        if($row['demand'] > 0){
                            $payload = [];
                            $inventoryItem = WaInventoryItem::find($row['id']);
                            $payload['item'] = $inventoryItem->title;
                            $payload['Qoh'] = $row['qoh'];
                            $payload['current_cost'] = $row['current_cost'];
                            $payload['new_cost'] = $row['new_cost'];
                            $payload['demand'] = $row['demand'];
                            $data []= $payload;

                        }
                    
                    }
                    if($request->demand_total  > 0){
                        $userEmail = User::find($request->user_id)->email;
                        $supplier = WaSupplier::latest()->where('name', $request->items[0]['supplier_names'])->first();
                        $pdf_d = true;
                
                        $pdf = PDF::loadView('admin.price_change.print', compact('data', 'supplier', 'demandCode', 'delta'))->set_option("enable_php", true);
                        $mail = new DeltaRequest($supplier, $pdf->output());
                        Mail::to($supplier->email)
                        ->cc($userEmail)
                        ->send($mail);

                    }
            
            if (isset($delta)) {
                $delta->vat_amount = array_sum($vatAmounts);
                $delta->save();
            }


            DB::commit();
            return response()->json(['message' => 'Batch price change processed successfully'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->jsonify(['error'=>true, 'message' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    // use this tto send email on single price change process 
    public function sendToSupplier(Request $request)
    {
        try {
            $data = [];
            foreach ($request->items as $row) {
                if ($row['demand'] > 0) {
                    $payload = [];
                    $inventoryItem = WaInventoryItem::find($row['id']);
                    $payload['item'] = $inventoryItem->title;
                    $payload['Qoh'] = $row['qoh'];
                    $payload['current_cost'] = $row['current_cost'];
                    $payload['new_cost'] = $row['new_cost'];
                    $payload['demand'] = $row['demand'];
                    $data[] = $payload;
                }
            }
            $userEmail = User::find($request->user_id)->email;
            $supplier = WaSupplier::latest()->where('name', $request->items[0]['supplier_names'])->first();
            $pdf_d = true;

            $pdf = PDF::loadView('admin.price_change.print', compact('data', 'supplier'))->set_option("enable_php", true);
            $mail = new DeltaRequest($supplier, $pdf->output());
            Mail::to($supplier->email)
                ->cc($userEmail)
                ->send($mail);
            return response()->json(['message' => 'Batch price change processed successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function priceTimeline(Request $request)
    {
        //dd($request->end_date);

       if (!can('price-timeline-report', 'inventory-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }
        $this->model = 'price-timeline-report';
        $this->title = 'Price Timeline Report';
        $this->pmodule = 'price-timeline-report';
        $this->breadcum = [$this->title => route('reports.price_timeline_report'), 'Listing' => ''];

        $start_date =$request->start_date ?  $request->start_date : date('Y-m-d');
        $end_date =$request->end_date ?  $request->end_date : date('Y-m-d', strtotime('+ 1 days'));
        $type =$request->transcation_type;
        $selectedID = $request->id;

        $this->timelines =[];

        $timeline= PriceTimeline::leftjoin('wa_inventory_items', 'price_timelines.wa_inventory_item_id', '=', 'wa_inventory_items.id')
         
         ->leftjoin('users', 'price_timelines.user_id', '=','users.id')
         ->leftjoin('wa_location_and_stores', 'price_timelines.wa_location_and_store_id','=','wa_location_and_stores.id')
       ->select(
            'price_timelines.stock_id_code',
            'price_timelines.transcation_type',
            'price_timelines.standart_cost_unit',
            'price_timelines.qty_received',
            'price_timelines.qoh_new',
            'price_timelines.qoh_before',
            'price_timelines.updated_at',
            'price_timelines.selling_price',
            'price_timelines.delta',
            'price_timelines.current_selling_price',
            'price_timelines.current_standard_cost',
            'wa_inventory_items.title',
            'wa_location_and_stores.location_name as branch',
            'users.name as username'
        )
        ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) 
        {           
         return $query->whereBetween('price_timelines.updated_at', [$start_date,$end_date]);
        })
        ->when($type, function ($query) use ($type) 
        {           
         return $query->where('price_timelines.transcation_type', [$type]);
        })
        ->when($selectedID, function ($query) use ($selectedID) 
        {           
         return $query->where('wa_inventory_items.id', $selectedID);
        })
        ->orderBy('price_timelines.created_at', 'Desc');
        $timeline->addSelect([
        'current_stock' => WaStockMove::select('new_qoh')
        ->whereColumn('price_timelines.stock_id_code', '=', 'wa_stock_moves.stock_id_code')
        ->where('wa_stock_moves.created_at', '<', DB::raw('price_timelines.created_at'))
        ->skip(0) 
        ->take(1) 
        ->orderBy('wa_stock_moves.created_at', 'desc')
        ->limit(1)
        ]);
        $timeline->addSelect([
        'current_standard_cos_moves' => WaStockMove::select('standard_cost')
        ->whereColumn('price_timelines.stock_id_code', '=', 'wa_stock_moves.stock_id_code')
        ->where('wa_stock_moves.created_at', '<', DB::raw('price_timelines.created_at'))
        ->skip(0) 
        ->take(1) 
        ->orderBy('wa_stock_moves.created_at', 'desc')
        ->limit(1)
        ]);
         $timeline->addSelect([
        'current_selling_moves' => WaStockMove::select('selling_price')
        ->whereColumn('price_timelines.stock_id_code', '=', 'wa_stock_moves.stock_id_code')
        ->where('wa_stock_moves.created_at', '<', DB::raw('price_timelines.created_at'))
        ->skip(0) 
        ->take(1) 
        ->orderBy('wa_stock_moves.created_at', 'desc')
        ->limit(1)
        ]);

        $timelines = $timeline->get();
        $inventoryItems = WaInventoryItem::get();

        if ($request->manage == 'excel') {
            /*dd($this->timelines);*/
            $view = view('admin.maintaininvetoryitems.data_price_timeline_pdf',
            [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'timelines' => $timeline->get(),
            'start_date'=>  $start_date = Carbon::parse($start_date)->toDateString(),
            'end_date'=>$end_date = Carbon::parse($end_date)->toDateString(),
            'type'=>$type,
            'inventoryItems'=>$inventoryItems,
            
            
            ]);
            return Excel::download(new PriceTimelineReportDataExport($view), $this->title . '.xlsx');
           }

           /*if ($request->manage == 'pdf') {
    $pdf = PDF::loadView('admin.maintaininvetoryitems.data_price_timeline_pdf', [
        'model' => $this->model,
        'title' => $this->title,
        'pmodule' => $this->pmodule,
        'breadcum' => $this->breadcum,
        'timelines' => $timeline->get(),
        'start_date' => $start_date = Carbon::parse($start_date)->toDateString(),
        'end_date' => $end_date = Carbon::parse($end_date)->toDateString(),
        'type' => $type
    ]);

    return $pdf->download($this->title . '_' . time() . '.pdf');
}
*/

        return view('admin.maintaininvetoryitems.price_timeline_report', [
            'model' => $this->model,
            'title' => $this->title,
            'pmodule' => $this->pmodule,
            'breadcum' => $this->breadcum,
            'timelines' => $timelines,
            'start_date' =>$start_date,
            'end_date' =>$end_date,
            'type'=>$type,
            'inventoryItems'=>$inventoryItems,
        ]);
    
}
}
