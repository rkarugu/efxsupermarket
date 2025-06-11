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
use App\Model\WaCashSales;
use App\Model\WaCustomer;
use App\Model\WaCashSalesItem;
use App\Model\WaChartsOfAccount;
use App\Model\WaDebtorTran;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use App\Model\WaGlTran;
use App\Model\WaStockMove;
use App\Model\WaCompanyPreference;
use App\Model\TaxManager;
use App\Model\ReversedCashSale;

class CashSalesController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'cash-sales';
        $this->title = 'Cash Sales';
        $this->pmodule = 'cash-sales';
    }

    public function index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
       if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
/*
            if ($permission != 'superadmin') {
                if (isset($permission[$pmodule . '___view-all'])) {
                    $lists = WaCashSales::orderBy('id', 'desc')->get();
                } else {
*/
                    // $lists = WaCashSales::where('creater_id', getLoggeduserProfile()->id);
                    // $lists = $lists->orderBy('id', 'desc')->get();
                    $lists = WaCashSales::select('id','cash_sales_number','slug','vehicle_reg_no','route','order_date','document_no','creater_id','wa_customer_id',DB::raw('(select sum(`unit_price`*`quantity`) as relatedItemTotal from wa_cash_sales_items where `wa_cash_sales_id`=`wa_cash_sales`.`id`) as relatedItemTotal'));
			        if ($request->has('start-date'))
			        {
			           
			            $lists = $lists->where('order_date','>=',$request->input('start-date'));
			        }
			        if ($request->has('end-date'))
			        { 
			            $lists = $lists->where('order_date','<=',$request->input('end-date'));
			        }

                    $lists = $lists->orderBy('id','DESC');
                    $lists= $lists->get();
                    //echo "<pre>"; print_r($lists); die;
//                }
           // } else {
          //      $lists = WaCashSales::orderBy('id', 'desc')->get();
           // }
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.cashsales.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
       } else {
           Session::flash('warning', 'Invalid Request');
           return redirect()->back();
       }
    }

    public function getCustomerDetail(Request $request)
    {
        $rows = WaCustomer::where('id', $request->customer_id)->first();




        return json_encode(['customer_name' => $rows->customer_name, 'customer_code' => $rows->customer_code ? $rows->customer_code : '', 'address' => $rows->address, 'telephone' => $rows->telephone]);
    }

    public function create()
    {
        if (getLoggeduserProfile()->wa_department_id && getLoggeduserProfile()->restaurant_id) {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
                $title = 'Add ' . $this->title;
                $model = $this->model;
                $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
                return view('admin.cashsales.create', compact('title', 'model', 'breadcum'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } else {
            Session::flash('warning', 'Please update your branch and department');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {
            //echo "<pre>"; print_r($request->all()); die;
            if ($request->get('selected_item_type') != "gl-code") {
                $checkqty = WaStockMove::where('stock_id_code', $request->get('item_no'))->where('wa_location_and_store_id', getLoggeduserProfile()->wa_location_and_store_id)->sum('qauntity');
                if ($checkqty < $request->get('quantity')) {
                    Session::flash('danger', 'The available quantity is less than the quantity, You are trying to enter.');
                    return redirect()->back();
                }
            }
            // echo $request->get('wa_inventory_item_id')." ----- ".$checkqty; die;
            $row = new WaCashSales();
            $row->sales_invoice_number = $request->sales_invoice_number;
            $row->wa_customer_id = $request->selected_customer_id;
            $row->creater_id = getLoggeduserProfile()->id;
            $row->order_date = $request->selected_order_date;
            $row->request_or_delivery = $request->selected_request_or_delivery;
            $row->status = $request->selected_status;
            $row->save();
            updateUniqueNumberSeries('SALES_INVOICE', $request->sales_invoice_number);



            $item = new WaCashSalesItem();
            $item->wa_sales_invoice_id = $row->id;

            $item->item_type = $request->selected_item_type;

            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->item_no = $request->item_no;
            if ($request->selected_item_type == 'item') {
                $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->title;
            } else {
                $item_detail = WaChartsOfAccount::where('id', $request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->account_name;
            }


            $item->standard_cost = $request->standard_cost;
            $item->unit_price = $request->unit_price;
            $item->actual_unit_price = $request->unit_price;
            $item->unit_of_measure_id = $request->unit_of_measure;
            $item->total_cost = $request->quantity * $request->unit_price;
            $item->vat_rate = $request->vat_rate;
            $item->vat_amount = 0;
            $item->service_charge_amount = 0;
            $item->catering_levy_amount = 0;
            $totalTaxation_percent = 100;
            if ($request->vat_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->vat_rate;
            }
            if ($request->service_charge_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->service_charge_rate;
            }
            if ($request->catering_levy_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->catering_levy_rate;
            }

            $base_value = $item->total_cost;


            if ($totalTaxation_percent > 100) {
                $base_value = ($item->total_cost * 100) / $totalTaxation_percent;
            }




            if ($request->vat_rate > '0') {
                $item->vat_amount =  ($request->vat_rate * $base_value) / 100;
            }

            $item->service_charge_rate = $request->service_charge_rate;

            if ($request->service_charge_rate > '0') {
                $item->service_charge_amount =  ($request->service_charge_rate * $base_value) / 100;
            }
            $item->catering_levy_rate = $request->catering_levy_rate;

            if ($request->catering_levy_rate > '0') {
                $item->catering_levy_amount =  ($request->catering_levy_rate * $base_value) / 100;
            }
            $item->total_cost_with_vat = $item->total_cost;
            $item->save();




            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model . '.edit', $row->slug);
        } catch (\Exception $e) {
            dd($e);
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function sendRequisitionRequest($purchase_no)
    {

        try {

            $row =  WaPurchaseOrder::where('status', 'UNAPPROVED')->where('purchase_no', $purchase_no)->first();
            if ($row) {
                $row->status = 'PENDING';
                $row->save();
                addPurchaseOrderPermissions($row->id, $row->wa_department_id);
                Session::flash('success', 'Request sent successfully.');
                return redirect()->route($this->model . '.index');
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }


    public function show($slug)
    {

        $row =  WaCashSales::whereSlug($slug)->first();
     //   echo "<pre>"; print_r($row); die;
        if ($row) {
            $title = 'View ' . $this->title;
            $breadcum = [$this->title => route($this->model . '.index').getReportDefaultFilter(), $row->cash_sales_number => ''];
            $model = $this->model;
            return view('admin.cashsales.show', compact('title', 'model', 'breadcum', 'row'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function print(Request $request)
    {

        $slug = $request->slug;
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row =  WaCashSales::whereSlug($slug)->first();
        return view('admin.cashsales.print', compact('title', 'model', 'breadcum', 'row'));
    }

    public function exportToPdf($slug)
    {
        $title = 'Add ' . $this->title;
        $model = $this->model;
        $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
        $row =  WaCashSales::whereSlug($slug)->first();
        $pdf = PDF::loadView('admin.cashsales.print', compact('title', 'model', 'breadcum', 'row'));
        $report_name = 'sales_invoice_' . date('Y_m_d_H_i_A');
        return $pdf->download($report_name . '.pdf');
    }






    public function edit($slug)
    {
        try {

            $row =  WaCashSales::whereSlug($slug)->first();
            if ($row) {
                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                $model = $this->model;
                return view('admin.cashsales.edit', compact('title', 'model', 'breadcum', 'row'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }



    public function process(Request $request, $slug)
    {
        // echo "<pre>"; print_r($request->all()); die;

        try {


            $dateTime = date('Y-m-d H:i:s');
            $row =  WaCashSales::whereSlug($slug)->first();
            $companyPreference =  WaCompanyPreference::where('id', '1')->first();
            $accountuingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
            $series_module = WaNumerSeriesCode::where('module', 'SALES_INVOICE')->first();
            $customer =  WaCustomer::where('id', $row->wa_customer_id)->first();
            //   echo "<pre>"; print_r($customer); die;
            foreach ($row->getRelatedItem as $item) {
                $key = 'discount_percent_' . $item->id;
                if (isset($request->$key)) {
                    $discount_percent = $request->$key;
                    if ($discount_percent > 0) {

                        $itemRow = WaCashSalesItem::where('id', $item->id)->first();
                        $unit_price =  $itemRow->unit_price;
                        $discount_amount = ($discount_percent * $unit_price) / 100;
                        $new_unit_price = $unit_price - $discount_amount;
                        $itemRow->unit_price = $new_unit_price;
                        $itemRow->total_cost =  $itemRow->unit_price * $itemRow->quantity;
                        $total_cost_with_vat = $itemRow->total_cost;
                        if ($itemRow->vat_rate > 0 && 1 == 2) {
                            $vat_amount =  ($itemRow->vat_rate * $total_cost_with_vat) / 100;
                            $itemRow->vat_amount =  $vat_amount;
                            $total_cost_with_vat = $total_cost_with_vat + $vat_amount;
                        }
                        $itemRow->discount_percent = $discount_percent;
                        $itemRow->discount_amount = $discount_amount * $itemRow->quantity;
                        $itemRow->total_cost_with_vat = $total_cost_with_vat;
                        $itemRow->save();
                    }
                }
            }
            $row->order_creating_status = 'completed';
            $row->save();
            $this->addDataToDebtorTrans($row->id);

            //adding data inot gl trans start

            $invoice_items = WaCashSalesItem::where('wa_sales_invoice_id', $row->id)
                // ->where('item_type','gl-code')
                ->get();
            $total_invoice_amount = [];
            $total_vat_amount = 0;
            $total_ctl_amount = 0;
            $total_service_amount = 0;
            $itemno = "";
            //echo "<pre>"; print_r($invoice_items); die;
            $sale_invoiceno = getCodeWithNumberSeries('SALES_INVOICE');
            // echo $sale_invoiceno; die;
            foreach ($invoice_items as $invoice_item) {


                if ($invoice_item->item_type != "gl-code") {

                    $stockMove = new WaStockMove();
                    $stockMove->user_id = getLoggeduserProfile()->id;
                    $stockMove->wa_purchase_order_id = $invoice_item->item_no;
                    $stockMove->restaurant_id = getLoggeduserProfile()->restaurant_id;
                    $stockMove->wa_location_and_store_id = getLoggeduserProfile()->wa_location_and_store_id;
                    $stockMove->stock_id_code = $invoice_item->item_no;
                    // $stockMove->stock_id_code = $invoice_item->item_no;
                    $stockMove->document_no =   $row->sales_invoice_number;
                    $stockMove->price = $invoice_item->unit_price;
                    $stockMove->grn_type_number = $series_module->type_number;
                    $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                    $stockMove->refrence = $customer->customer_code . ':' . $customer->customer_name;
                    $stockMove->qauntity = - ($invoice_item->quantity);
                    $stockMove->standard_cost = $invoice_item->total_cost;
                    $stockMove->save();
                    //move to stock moves end
                }

                if ($invoice_item->item_type == "item") {
                    $getItemaccountno = WaInventoryItem::where('stock_id_code', $invoice_item->item_no)->first();
                    $description = $invoice_item->item_name;
                    // $accno = $getItemaccountno->getInventoryCategoryDetail->getStockGlDetail->account_code;
                    $accno = $getItemaccountno->getInventoryCategoryDetail->getWIPGlDetail->account_code;
                } else {
                    $description = $invoice_item->item_name;
                    // $description =  $customer->customer_code . ':' . $customer->customer_name;
                    //~ $accno = $companyPreference->debtorsControlGlAccount->account_code;
                    $accno = $invoice_item->item_no; //$companyPreference->debtorsControlGlAccount->account_code;

                }




                //cr entries start
                $cr = new WaGlTran();
                $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $cr->wa_sales_invoice_id = $row->id;
                $cr->grn_type_number = $series_module->type_number;
                $cr->trans_date = $dateTime;
                $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $row->sales_invoice_number;
                $cr->narrative = $description;
                $cr->account = $accno;
                $cr->amount = '-' . ($invoice_item->total_cost_with_vat - $invoice_item->vat_amount - $invoice_item->catering_levy_amount - $invoice_item->service_charge_amount);
                $cr->save();




                $total_invoice_amount[] = ($invoice_item->total_cost_with_vat);

                if ($invoice_item->vat_rate > 0) {
                    $total_vat_amount += $invoice_item->vat_amount;
                }
                if ($invoice_item->catering_levy_rate > 0) {
                    $total_ctl_amount += $invoice_item->catering_levy_amount;
                }
                if ($invoice_item->service_charge_rate > 0) {
                    $total_service_amount += $invoice_item->service_charge_amount;
                }

                //cr entries end
            }

            if ($total_vat_amount > 0) {
                $taxVat = TaxManager::where('slug', 'vat')->first();

                $cr = new WaGlTran();
                $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $cr->wa_sales_invoice_id = $row->id;
                $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $cr->grn_type_number = $series_module->type_number;
                $cr->trans_date = $dateTime;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $row->sales_invoice_number;
                $cr->narrative = "VAT";
                $cr->account = $taxVat->getInputGlAccount->account_code; //$invoice_item->item_no;
                $cr->amount = '-' . $total_vat_amount;
                $cr->save();
                // $total_invoice_amount[] = $invoice_item->total_cost_with_vat;                    
            }
            if ($total_ctl_amount > 0) {
                $taxVat = TaxManager::where('slug', 'ctl')->first();

                $cr = new WaGlTran();
                $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $cr->wa_sales_invoice_id = $row->id;
                $cr->grn_type_number = $series_module->type_number;
                $cr->trans_date = $dateTime;
                $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $row->sales_invoice_number;
                $cr->narrative = "Catering Levy";
                $cr->account =  $taxVat->getInputGlAccount->account_code;  //$invoice_item->item_no;
                $cr->amount = '-' . $total_ctl_amount;
                $cr->save();
                //  $total_invoice_amount[] = $invoice_item->total_cost_with_vat;
            }
            if ($total_service_amount > 0) {
                $taxVat = TaxManager::where('slug', 'service-tax')->first();

                $cr = new WaGlTran();
                $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $cr->wa_sales_invoice_id = $row->id;
                $cr->grn_type_number = $series_module->type_number;
                $cr->trans_date = $dateTime;
                $cr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $cr->grn_last_used_number = $series_module->last_number_used;
                $cr->transaction_type = $series_module->description;
                $cr->transaction_no = $row->sales_invoice_number;
                $cr->narrative = "Service Tax";
                $cr->account =  $taxVat->getInputGlAccount->account_code;  //$invoice_item->item_no;
                $cr->amount = '-' . $total_service_amount;
                $cr->save();
                // $total_invoice_amount[] = $invoice_item->total_cost_with_vat;
            }
            if (count($total_invoice_amount) > 0) {

                //dr entries start
                $dr = new WaGlTran();
                $dr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                $dr->wa_sales_invoice_id = $row->id;
                $dr->grn_type_number = $series_module->type_number;
                $dr->trans_date = $dateTime;
                $dr->restaurant_id = getLoggeduserProfile()->restaurant_id;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $row->sales_invoice_number;
                $dr->narrative = $customer->customer_code . ':' . $customer->customer_name;
                $dr->account = $companyPreference->debtorsControlGlAccount->account_code;
                $dr->amount = array_sum($total_invoice_amount);
                //$dr->amount = $invoice_item->total_cost_with_vat; //array_sum($total_invoice_amount);
                $dr->save();
                //dr entries end
            }


            //adding data into gl trans end

            Session::flash('success', 'Processed successfully.');
            return redirect()->route($this->model . '.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }

    public function addDataToDebtorTrans($invoiceId)
    {
        $accountuingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
        $invoice =  WaCashSales::where('id', $invoiceId)->first();
        $series_module = WaNumerSeriesCode::where('module', 'SALES_INVOICE')->first();
        $debtorTran = new WaDebtorTran();
        $debtorTran->wa_sales_invoice_id = $invoiceId;
        $debtorTran->type_number =  $series_module ? $series_module->type_number : '';
        $debtorTran->wa_customer_id = $invoice->getRelatedCustomer->id;

        $debtorTran->customer_number = $invoice->getRelatedCustomer->customer_code;
        $debtorTran->trans_date = $invoice->order_date;
        $debtorTran->trans_date = $invoice->order_date;
        $debtorTran->input_date = date('Y-m-d H:i:s');
        $debtorTran->wa_accounting_period_id = $accountuingPeriod ? $accountuingPeriod->id : null;
        $debtorTran->amount = $invoice->getRelatedItem->sum('total_cost_with_vat');
        $debtorTran->document_no = $invoice->sales_invoice_number;
        $debtorTran->save();
    }


    public function addMore(Request $request, $slug)
    {


        try {
            // $checkqty = WaStockMove::where('wa_inventory_item_id', $request->get('wa_inventory_item_id'))->sum('qauntity');
            //  echo $checkqty; die;
            $row =  WaCashSales::whereSlug($slug)->first();
            $item = new WaCashSalesItem();
            $item->wa_sales_invoice_id = $row->id;

            $item->item_type = $request->selected_item_type;

            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->item_no = $request->item_no;
            if ($request->selected_item_type == 'item') {
                $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->title;
            } else {
                $item_detail = WaChartsOfAccount::where('id', $request->wa_inventory_item_id)->first();
                $item->item_name = $item_detail->account_name;
            }


            $item->standard_cost = $request->standard_cost;
            $item->unit_price = $request->unit_price;
            $item->actual_unit_price = $request->unit_price;
            $item->unit_of_measure_id = $request->unit_of_measure;
            $item->total_cost = $request->quantity * $request->unit_price;
            $item->vat_rate = $request->vat_rate;
            $item->vat_amount = 0;
            $item->service_charge_amount = 0;
            $item->catering_levy_amount = 0;
            $totalTaxation_percent = 100;
            if ($request->vat_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->vat_rate;
            }
            if ($request->service_charge_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->service_charge_rate;
            }
            if ($request->catering_levy_rate > '0') {

                $totalTaxation_percent = $totalTaxation_percent + $request->catering_levy_rate;
            }

            $base_value = $item->total_cost;


            if ($totalTaxation_percent > 100) {
                $base_value = ($item->total_cost * 100) / $totalTaxation_percent;
            }
            // echo $totalTaxation_percent;die;




            if ($request->vat_rate > '0') {
                $item->vat_amount =  ($request->vat_rate * $base_value) / 100;
            }

            $item->service_charge_rate = $request->service_charge_rate;

            if ($request->service_charge_rate > '0') {
                $item->service_charge_amount =  ($request->service_charge_rate * $base_value) / 100;
            }
            $item->catering_levy_rate = $request->catering_levy_rate;

            if ($request->catering_levy_rate > '0') {
                $item->catering_levy_amount =  ($request->catering_levy_rate * $base_value) / 100;
            }
            //  $item->total_cost_with_vat = $item->total_cost+$item->vat_amount;
            $item->total_cost_with_vat = $item->total_cost;
            $item->save();
            
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model . '.edit', $row->slug);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function update(Request $request, $slug)
    {
    }


    public function destroy($slug)
    {
        try {
            WaCashSales::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getDapartments(Request $request)
    {
        $rows = WaDepartment::where('restaurant_id', $request->branch_id)->orderBy('department_name', 'asc')->get();
        $data = '<option  value="">Please select department</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->department_name . '</option>';
        }

        return $data;
    }

    public function getItems(Request $request)
    {
        if ($request->selected_item_type == 'item') {
            $rows = WaInventoryItem::orderBy('title', 'asc')->get();
            $data = '<option  value="">Please select item</option>';
            foreach ($rows as $row) {
                $data .= '<option  value="' . $row->id . '">' . $row->title . '</option>';
            }

            return $data;
        } else {
            $rows = getChartOfAccountsDropdown(); //WaChartsOfAccount::whereIn('wa_account_group_id', [19, 20])->orderBy('account_name', 'asc')->get();
            $data = '<option  value="">Please select item</option>';
            foreach ($rows as $key=> $row) {
                $data .= '<option  value="' . $key . '">' . $row . '</option>';
            }

            return $data;
        }
    }




    public function getItemDetail(Request $request)
    {

        if ($request->item_type == 'item') {
            $rows = WaInventoryItem::where('id', $request->selected_item_id)->first();
            return json_encode(['stock_id_code' => $rows->stock_id_code, 'unit_of_measure' => $rows->wa_unit_of_measure_id ? $rows->wa_unit_of_measure_id : '', 'standard_cost' => $rows->standard_cost, 'prev_standard_cost' => $rows->prev_standard_cost]);
        } else {
            $rows =  WaChartsOfAccount::where('id', $request->selected_item_id)->first();
            return json_encode(['stock_id_code' => $rows->account_code, 'unit_of_measure' => '', 'standard_cost' => 0, 'prev_standard_cost' => 0]);
        }
    }
    public function deletingItemRelation($purchase_no, $id)
    {
        try {
            WaPurchaseOrderItem::whereId($id)->delete();


            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function reverseItem($id)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if(isset($permission[$pmodule.'___reserve-transaction']) || $permission == 'superadmin')
        {

      //  try {
		    $cashsalesdata = WaCashSales::where('document_no',$id)->first();
		    $cashsalesitemdata = WaCashSalesItem::where('document_no',$id)->pluck('id');
		    $total_cost = WaCashSalesItem::where('document_no',$id)->sum('total_cost');

	        $savereverse		 			  = new ReversedCashSale();
	        $savereverse->cash_sale_id 		  = $cashsalesdata->id;
	        $savereverse->user_id			  =	getLoggeduserProfile()->id;
	        $savereverse->cash_sales_item_id  = json_encode($cashsalesitemdata);
	        $savereverse->total_amount 		  = $total_cost;
	        $savereverse->save();
	        
            WaCashSales::where('document_no',$id)->delete();
            WaCashSalesItem::where('document_no',$id)->delete();
            WaGlTran::where('transaction_no',$id)->delete();
            WaStockMove::where('document_no',$id)->delete();
            WaDebtorTran::where('document_no',$id)->delete();


            Session::flash('success', 'Record Deleted successfully.');
            return redirect()->back();
       // } catch (\Exception $e) {
/*
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
*/
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }  

    }




    public function editPurchaseItem($purchase_no, $id)
    {
        try {

            $row =  WaPurchaseOrder::where('purchase_no', $purchase_no)
                ->whereHas('getRelatedItem', function ($sql_query) use ($id) {
                    $sql_query->where('id', $id);
                })

                ->first();
            if ($row) {

                $title = 'Edit ' . $this->title;
                $breadcum = [$this->title => route($this->model . '.index'), $row->purchase_no => '', 'Edit' => ''];
                $model = $this->model;


                $form_url = [$model . '.updatePurchaseItem', $row->getRelatedItem->find($id)->id];
                return view('admin.purchaseorders.editItem', compact('title', 'model', 'breadcum', 'row', 'id', 'form_url'));
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }

    public function updatePurchaseItem(Request $request, $id)
    {
        try {

            // dd('here');


            $item =  WaPurchaseOrderItem::where('id', $id)->first();
            $item->wa_inventory_item_id = (string) $request->wa_inventory_item_id;
            $item->quantity = $request->quantity;
            $item->note = $request->note;
            $item->prev_standard_cost = $request->prev_standard_cost;
            $item->order_price = $request->order_price;
            $item->supplier_uom_id = $request->supplier_uom_id;
            $item->supplier_quantity = $request->supplier_quantity;
            $item->unit_conversion = $request->unit_conversion;
            $item->item_no = $request->item_no;

            $item->unit_of_measure = $request->unit_of_measure;
            $item_detail = WaInventoryItem::where('id', $request->wa_inventory_item_id)->first();
            $item->standard_cost = $item_detail->standard_cost;
            $item->total_cost = $item->order_price * $request->supplier_quantity;
            $vat_rate = 0;
            $vat_amount = 0;
            if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
                $vat_rate = $item_detail->getTaxesOfItem->tax_value;
                if ($item->total_cost > 0) {
                    $vat_amount = ($item_detail->getTaxesOfItem->tax_value * $item->total_cost) / 100;
                }
            }
            $item->vat_rate = $vat_rate;
            $item->vat_amount = $vat_amount;
            $item->total_cost_with_vat =  $item->total_cost + $vat_amount;
            $item->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.edit', $item->getPurchaseOrder->slug);
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }
}
