<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaPurchaseOrder;
use App\Model\WaPurchaseOrderItem;
use App\Model\WaDepartment;
use App\Model\WaInventoryItem;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Model\WaSalesInvoice;
use App\Model\WaCustomer;
use App\Model\WaSalesInvoiceItem;
use App\Model\WaChartsOfAccount;
use App\Model\WaDebtorTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaStockMove;
use App\Model\WaCompanyPreference;
use App\Model\TaxManager;
use App\Model\WaInventoryCategory;
use App\Model\Restaurant;
use App\Model\WaLocationAndStore;


use App\Model\WaStockCheckFreeze;
use App\Model\WaStockCheckFreezeItem;
use App\Model\WaStockVarience;
use App\Model\WaStockVarienceItem;
use App\Model\WaStockVarienceMain;

use App\Model\StockAdjustment;
use Excel;

class StockVarienceController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        // $this->model = 'stock-counts';
        $this->model = 'new-stock-variance';
        $this->title = 'Stock Counts';
        $this->pmodule = 'stock-counts';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000000);
        ini_set('post_max_size', 128000);
        ini_set('max_input_vars', 9999);
        set_time_limit(30000000); // Extends to 5 minutes.
    }
    public function modulePermissions($type)
    {
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule .'___view']) || $permission == 'superadmin') {
            $data = WaStockVarience::select(['wa_stock_variance.parent_id as parent_id',DB::RAW('COUNT(item.id) as totalitems')])
            ->join('wa_stock_variance_items as item','item.parent_id','=','wa_stock_variance.id')
            ->orderBy('wa_stock_variance.parent_id','DESC')->groupBy('wa_stock_variance.parent_id')->get();
            $breadcum = [$title => '', 'Listing' => ''];
            return view('admin.stockvarience.index', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function addNew(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
             $data = [];
            if ($request->isMethod('post')) {               
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $conditions = [];
                if (!empty($start_date)) {
                    $conditions[] = ['created_at', '>=', $start_date];
                }

                if (!empty($end_date)) {
                    $conditions[] = ['created_at', '<=', $end_date];
                }
                $result = WaStockMove::with('getInventoryItemDetail.getUnitOfMeausureDetail');

                if (!empty($conditions)) {


                    $result = $result->where($conditions);
                }

                $result = $result->groupBy('wa_inventory_item_id')->get();
               
                    if ($request->input('filter') == 'filter') {
                        $data = WaInventoryCategory::with('getinventoryitems', 'getinventoryitems.getstockmoves', 'getinventoryitems.unitofmeasures')
                            ->whereHas('getinventoryitems.getstockmoves', function ($q) use ($request, $start_date, $end_date) {
                                $q->where('restaurant_id', getLoggeduserProfile()->restaurant_id);
                                $q->whereBetween('created_at', [$start_date, $end_date]); 
                                if ($request->has('location')) {
                                    $q->where('wa_location_and_store_id', $request->input('location'));
                                    $q->groupBy('stock_id_code', 'wa_location_and_store_id');
                                }
                            });
                        if ($request->input('wa_inventory_category_id')[0] != "-1") {
                            $data->whereIn('id', $request->input('wa_inventory_category_id'));
                        }
                        $data  =  $data->get()->toArray();
                       // dd($data);
                        $storeBiseQty = [];
                        $purchaseBiseQty = [];
                        $transfersBiseQty = [];
                        $issuesBiseQty = [];
                        $salesBiseQty = [];
                        $summrytype = 2;
                        $restuarantname = (Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name) ? Restaurant::where('id', getLoggeduserProfile()->restaurant_id)->first()->name : '';
                        $locationname = (isset(WaLocationAndStore::where('id', $request->input('location'))->first()->location_name)) ? WaLocationAndStore::where('id', $request->input('location'))->first()->location_name : '';
                        foreach ($data as $k => $val) {
                            $get_quantity = 0;
                            foreach ($val['getinventoryitems'] as $key => $value) {
                                /** qty start **/
                               
                                $get_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code']);
                                if ($request->input('location') != "") {
                                    $get_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_quantity->where('created_at', '<', $start_date);
                                $get_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_quantity =  $get_quantity->sum('qauntity');
                                $storeBiseQty[$value['getstockmoves']['id']] = $get_quantity;

                                /** qty end **/
                                /** qty purchases **/
                              //  DB::enableQueryLog();
                                $get_purchases_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_purchase_order_id','!=',null);
                                if ($request->input('location') != "") {
                                    $get_purchases_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_purchases_quantity->where('created_at', '>=', $start_date);
                                $get_purchases_quantity->where('created_at', '<=', $end_date);
                                $get_purchases_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_purchases_quantity =  $get_purchases_quantity->sum('qauntity');
                               // dd(DB::getQueryLog());
                                $purchaseBiseQty[$value['getstockmoves']['id']] = $get_purchases_quantity;

                                /** qty purchases **/

                                /** qty transfersBiseQty **/

                                $get_transfers_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_inventory_location_transfer_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_transfers_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_transfers_quantity->where('created_at', '>=', $start_date);
                                $get_transfers_quantity->where('created_at', '<=', $end_date);

                                $get_transfers_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_transfers_quantity =  $get_transfers_quantity->sum('qauntity');

                                $transfersBiseQty[$value['getstockmoves']['id']] = $get_transfers_quantity;

                                /** qty transfersBiseQty **/


                                /** qty wa_internal_requisition **/

                                $get_issues_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('wa_internal_requisition_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_issues_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_issues_quantity->where('created_at', '>=', $start_date);
                                $get_issues_quantity->where('created_at', '<=', $end_date);

                                $get_issues_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_issues_quantity =  $get_issues_quantity->sum('qauntity');

                                $issuesBiseQty[$value['getstockmoves']['id']] = $get_issues_quantity;

                                /** qty wa_internal_requisition **/

                                /** qty ordered_item_id **/

                                $get_sales_quantity = WaStockMove::where('stock_id_code', $value['stock_id_code'])->where('ordered_item_id', '!=', null);
                                if ($request->input('location') != "") {
                                    $get_sales_quantity->where('wa_location_and_store_id', $request->input('location'));
                                }
                                $get_sales_quantity->where('created_at', '>=', $start_date);
                                $get_sales_quantity->where('created_at', '<=', $end_date);

                                $get_sales_quantity->groupBy('stock_id_code', 'wa_location_and_store_id');
                                $get_sales_quantity =  $get_sales_quantity->sum('qauntity');

                                $salesBiseQty[$value['getstockmoves']['id']] = $get_sales_quantity;

                                /** qty ordered_item_id **/
                            }
                        }
                    }

                    return view('admin.stockvarience.addNew', compact('model','title', 'result', 'data', 'summrytype', 'salesBiseQty', 'issuesBiseQty', 'purchaseBiseQty','storeBiseQty', 'transfersBiseQty','locationname', 'restuarantname', 'start_date', 'end_date'));
             


            }
            $breadcum = ['Reports' => '', '' => ''];
            return view('admin.stockvarience.addNew', compact('title', 'data', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create(Request $request)
    {
        try {
            $test = DB::transaction(function () use ($request){
                $batchDate = date('Y-m-d H:i:s');
                if(isset($request->category_code) && count($request->category_code) > 0)
                {
                    $abc = new WaStockVarienceMain;
                    $abc->categories = $request->categories;
                    $abc->start_date = $request->start_date;
                    $abc->end_date = $request->end_date;
                    $abc->location = $request->location;
                    $abc->save();
                    foreach ($request->category_code as $key => $value) {
                        $new = new WaStockVarience;
                        $new->category_code = $value;
                        $new->category_description = $request->category_description[$key];
                        $new->batch_date = $batchDate;
                        $new->parent_id = $abc->id;
                        $new->save();
                        foreach ($request->stock_id_code[$key] as $ckey => $child) {
                            $item = new WaStockVarienceItem;
                            $item->parent_id = $new->id;
                            $item->category_code = $request->stock_id_code[$key][$ckey] ?? NULL;
                            $item->category_name = $request->title[$key][$ckey] ?? NULL;
                            $item->uom = $request->unitofmeasures[$key][$ckey] ?? NULL;
                            
                            $item->opening_stock = $request->storeBiseQty[$key][$ckey] ?? NULL;
                            $item->purchase = $request->purchaseBiseQty[$key][$ckey] ?? NULL;
                            $item->transfers = $request->transfersBiseQty[$key][$ckey] ?? NULL;

                            $item->issues = $request->issuesBiseQty[$key][$ckey] ?? NULL;
                            $item->total = $request->total[$key][$ckey] ?? NULL;
                            $item->closing_stocks = $request->closing_stock[$key][$ckey] ?? NULL;

                            $item->potential_stocks = $request->potential_stock[$key][$ckey] ?? NULL;
                            $item->actual_sales = $request->salesBiseQty[$key][$ckey] ?? NULL;
                            $item->variance = $request->variance[$key][$ckey] ?? NULL;

                            $item->batch_date = $batchDate;
                            $item->save();
                        }
                    }
                }
                
                return true;
            });
            if(!$test)
            {
                throw new \Exception("Error Processing Request", 1);                
            }
            Session::flash('success', 'Stock Variance Added Successfully');
            return redirect()->route('admin.stock-variance.index');
        } catch (\Exception $th) {
            Session::flash('warning', 'Something went wrong');
            return redirect()->back();
        }

    }

    public function ReportPdf($id)
    {
        $data['data'] = WaStockVarienceMain::where('id',$id)->firstOrFail();
        $pdf = PDF::loadView('admin.stockvarience.report',$data)->setPaper('a4','landscape');
        return $pdf->download('stockvarience_report_' . date('Y_m_d_h_i_s') . '.pdf');
    }

    public function ReportExcel($id)
    {
        $data['data'] = WaStockVarienceMain::where('id', $id)->firstOrFail();

        return Excel::download(function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data) {
                $sheet->loadView('admin.stockvarience.report', $data);
            });
        }, 'stockvarience_report_' . date('Y_m_d_h_i_s') . '.xlsx');

    }
}