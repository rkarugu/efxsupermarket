<?php

namespace App\Http\Controllers\Admin;

use App\Pesaflow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaInventoryLocationTransfer;
use App\Model\WaInventoryLocationTransferItem;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Model\WaGlTran;
use App\Model\WaAccountingPeriod;
use App\Model\Setting;
use App\Model\WaEsdDetails;
use PDF, DB, Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IssueFullfillRequisitionTestController extends Controller
{

  protected $model;
  protected $title;
  protected $pmodule;

  public function __construct()
  {

    $this->model = 'confirm-invoice-test';
    $this->title = 'Confirm Invoice Test';
    $this->pmodule = 'confirm-invoice-r';
  }

  public function index()
  {
    $permission = $this->mypermissionsforAModule();
    $user_permission = $this->myUserPermissionsforAModule();

    //dd($user_permission);

    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    if (isset($permission[$this->pmodule . '___view']) || $permission == 'superadmin') {
      $lists = WaInternalRequisition::where('status', '=', 'APPROVED');

      if (isset($permission[$this->pmodule . '___view-all'])) {
        $lists = $lists->orderBy('id', 'desc')->get();
      } else {
        $lists = $lists->where('to_store_id', getLoggeduserProfile()->wa_location_and_store_id)->get();
      }

      $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
      return view('admin.issuefullfillrequisitiontest.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
    } else {
      Session::flash('warning', 'Invalid Request');
      return redirect()->back();
    }
  }

  public function show($slug)
  {

    $row = WaInternalRequisition::with('getRouteCustomer')->whereSlug($slug)->where('status', '=', 'APPROVED')->first();
    if ($row) {
      $title = 'View ' . $this->title;
      $breadcum = [$this->title => route($this->model . '.index'), 'Show' => ''];
      $model = $this->model;
      $route = \App\Model\Route::pluck('route_name', 'id');
      $esd_setting = Setting::whereSlug('esd-url')->first();
      $esd_url = $esd_setting->description;
      return view('admin.issuefullfillrequisitiontest.show', compact('route', 'title', 'model', 'breadcum', 'row', 'esd_url'));

    } else {
      Session::flash('warning', 'Invalid Request');
      return redirect()->route('confirm-invoice-test.index');
    }
  }

  public function exportToPdf($slug)
  {


    $title = 'Add ' . $this->title;
    $model = $this->model;
    $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
    $row = WaInternalRequisition::whereSlug($slug)->first();
    $pdf = PDF::loadView('admin.issuefullfillrequisitiontest.print', compact('title', 'model', 'breadcum', 'row'));
    $report_name = 'internal_requisition_' . date('Y_m_d_H_i_A');
    return $pdf->download($report_name . '.pdf');
  }

  public function printPage(Request $request)
  {

    $slug = $request->slug;
    $title = 'Add ' . $this->title;
    $model = $this->model;
    $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
    $row = WaInternalRequisition::whereSlug($slug)->first();


    return view('admin.issuefullfillrequisitiontest.print', compact('title', 'model', 'breadcum', 'row', 'esd_details'));
  }

  public function destroy($slug)
  {
    try {
      $a = DB::transaction(function () use ($slug) {
        $row = WaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();
        if ($row->id) {
          WaInternalRequisitionItem::where('wa_internal_requisition_id', $row->id)->delete();
          $row->delete();
          return true;
        }
        return false;
      });
      if (!$a) {
        Session::flash('warning', 'Invalid Request');
        return redirect()->back();
      }
      Session::flash('success', 'Deleted successfully.');
      return redirect()->back();
    } catch (\Exception $e) {
      Session::flash('warning', 'Invalid Request');
      return redirect()->back();
    }
  }


  public function update(Request $request, $slug)
  {


    // print_r($request->all());die;
    // die('working');

    try {
      $allInternal = WaInternalRequisitionItem::with(['getInventoryItemDetail',
        'getInventoryItemDetail.getAssignedItem',
        'getInventoryItemDetail.getInventoryCategoryDetail', 'getInventoryItemDetail.getAllFromStockMoves',
        'getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
        'getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail',
        'getInventoryItemDetail.getInventoryCategoryDetail.getWIPGlDetail',
      ])->whereIn('id', $request->related_item_ids)->get();


      $grand_total_arr = [];
      $vat_amount_arr = [];
      $items_list_arr = [];
      $net_subtotal_arr = [];

      foreach ($request->related_item_ids as $related_item_id) {
        $related_item_row = $allInternal->find($related_item_id);
        $delivery_quantity = 'delivered_quantity_' . $related_item_id;
        $issued_quantity = $related_item_row->quantity;

        $grand_total_arr[] = $related_item_row->total_cost;
        $net_subtotal_arr[] = ($related_item_row->total_cost - $related_item_row->vat_amount);
        $vat_amount_arr[] = $related_item_row->vat_amount;


        $item_description = @$related_item_row->getInventoryItemDetail->title;
        $item_quantity = @$related_item_row->quantity;
        $item_cost = @$related_item_row->total_cost; // total_cost_with_vat
        $item_total = number_format(@$related_item_row->total_cost * $item_quantity, 2);

        if ($related_item_row->vat_amount == 0.00) {
          $hs_code = $related_item_row->hs_code;
          $items_list_arr[] = $hs_code . ' ' . $item_description . ' ' . $item_quantity . ' ' . number_format($related_item_row->selling_price, 2) . ' ' . number_format($item_cost, 2);
        } else {
          $items_list_arr[] = $item_description . ' ' . $item_quantity . ' ' . number_format($related_item_row->selling_price, 2) . ' ' . number_format($item_cost, 2);

        }

      }
      $internal_requisition_row = WaInternalRequisition::whereSlug($slug)->where('status', '=', 'APPROVED')->first();

      $invoice_pin = Setting::whereSlug('pin-no')->first()->description;
      $invoiceRequestArr = [
        "invoice_date" => date('d_m_Y', strtotime($request->purchase_date)),
        "invoice_number" => $request->requisition_no,
        "invoice_pin" => $invoice_pin,
        "customer_pin" => $internal_requisition_row->customer_pin, // optional
        "customer_exid" => "", // tax exception number
        "grand_total" => manageAmountFormat(array_sum($grand_total_arr)),
        "net_subtotal" => manageAmountFormat(array_sum($net_subtotal_arr)),
        "tax_total" => manageAmountFormat(array_sum($vat_amount_arr)),
        "net_discount_total" => "0",
        "sel_currency" => "KSH",
        "rel_doc_number" => "",
        "items_list" => $items_list_arr
      ];

      /* echo '<pre>';
       print_r(json_encode($invoiceRequestArr)); die;*/


      /*print_r(json_encode($invoiceRequestArr));
          die;*/

      if (!$internal_requisition_row) {

        return response()->json(['result' => -1, 'message' => 'Invalid Request']);
        /*Session::flash('warning','Invalid Request');
        return redirect()->route('confirm-invoice-test.index');*/
      }
      $itemslist = WaInternalRequisitionItem::with(['getInventoryItemDetail', 'getInventoryItemDetail.getAllFromStockMoves', 'getInventoryItemDetail.getAssignedItem.destinated_item.getAllFromStockMoves'])->where('wa_internal_requisition_id', $internal_requisition_row->id)->get();
      $totalinventorycost = 0;
      foreach ($itemslist as $it) {

        if (!isset($it->getInventoryItemDetail->getAllFromStockMoves) || @$it->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', $it->store_location_id)->sum('qauntity') < $it->quantity) {

          return response()->json(['result' => -1, 'message' => @$it->getInventoryItemDetail->stock_id_code . ' Item Available quantity is not enough']);

          /*Session::flash('warning', @$it->getInventoryItemDetail->stock_id_code.' Item Available quantity is not enough');
          return redirect()->back()->withInput();*/
        }
        if ($it->getInventoryItemDetail && $it->getInventoryItemDetail->block_this == 1) {


          return response()->json(['result' => -1, 'message' => $it->getInventoryItemDetail->stock_id_code . ': The product has been blocked from sale due to a change in standard cost']);

          /*Session::flash('warning', $it->getInventoryItemDetail->stock_id_code.': The product has been blocked from sale due to a change in standard cost');
          return redirect()->back()->withInput();*/
        }


        /*if($it->getInventoryItemDetail->getAssignedItem){


            if(!isset($it->getInventoryItemDetail->getAssignedItem->quantity) || @$it->getInventoryItemDetail->getAssignedItem->quantity < ($it->quantity/$it->getInventoryItemDetail->getAssignedItem->conversion_factor)){


                return response()->json(['result'=>-1,'message'=>@$it->getInventoryItemDetail->getAssignedItem->stock_id_code.' Item Available quantity is not enough']);


            }
            if($it->getInventoryItemDetail->getAssignedItem && $it->getInventoryItemDetail->getAssignedItem->block_this == 1){


                return response()->json(['result'=>-1,'message'=>$it->getInventoryItemDetail->getAssignedItem->stock_id_code.': The product has been blocked from sale due to a change in standard cost']);


            }
        }else{
            if(!isset($it->getInventoryItemDetail->getAllFromStockMoves) || @$it->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$it->store_location_id)->sum('qauntity') < $it->quantity){

                return response()->json(['result'=>-1,'message'=>@$it->getInventoryItemDetail->stock_id_code.' Item Available quantity is not enough']);


            }
            if($it->getInventoryItemDetail && $it->getInventoryItemDetail->block_this == 1){


                return response()->json(['result'=>-1,'message'=>$it->getInventoryItemDetail->stock_id_code.': The product has been blocked from sale due to a change in standard cost']);


            }
        }*/
        $totalinventorycost += $it->total_cost_with_vat;
      }

      $customer = \App\Model\WaCustomer::where('id', $internal_requisition_row->customer_id)->first();
      $location = \App\Model\WaLocationAndStore::with(['user'])->where('id', $internal_requisition_row->to_store_id)->first();
      $credit_limit = $customer->credit_limit ?? 0;
      $used_limit = \App\Model\WaDebtorTran::where('wa_customer_id', @$customer->id)->sum('amount');
      // if($location->user){
      //     $used_limit = \App\Model\WaDebtorTran::where('salesman_user_id',$location->user->id)->sum('amount');
      // }else
      // {
      //     $used_limit = 0;
      // }
      $available_limit = $credit_limit - $used_limit;
      if ($available_limit < $totalinventorycost) {

        return response()->json(['result' => -1, 'message' => "You cannot process the Invoice as it will exceed your allowed Credit Limit"]);

        /*Session::flash('warning', "You cannot process the Invoice as it will exceed your allowed Credit Limit");
        return redirect()->back()->withInput();           */
      }

      $parent = DB::transaction(function () use ($request, $slug, $internal_requisition_row, $itemslist, $location, $customer) {
        $dateTime = date('Y-m-d H:i:s');
        $vat_amount_arr = [];
        $cr_amount = [];
        $series_module = WaNumerSeriesCode::where('module', 'INTERNAL REQUISITIONS')->first();
        $intr_smodule = $series_module;//WaNumerSeriesCode::where('module','INTERNAL REQUISITIONS')->first();
        $WaAccountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
        $grn_number = $internal_requisition_row->requisition_no;//getCodeWithNumberSeries('INTERNAL REQUISITIONS');
        $getLoggeduserProfile = getLoggeduserProfile();
        $row = new WaInventoryLocationTransfer();
        $row->transfer_no = $internal_requisition_row->requisition_no;
        $row->transfer_date = $internal_requisition_row->requisition_date;
        $row->restaurant_id = $getLoggeduserProfile->restaurant_id;
        $row->wa_department_id = $getLoggeduserProfile->wa_department_id;
        $row->user_id = $getLoggeduserProfile->id;
        // $row->from_store_location_id = $internal_requisition_row->wa_location_and_store_id;
        $row->to_store_location_id = $internal_requisition_row->to_store_id;
        $row->vehicle_register_no = $internal_requisition_row->vehicle_register_no;
        $route = \App\Model\Route::where('id', $request->route_id)->first();
        $row->route = @$route->route_name;
        $row->customer = $internal_requisition_row->customer;
        $row->name = $internal_requisition_row->name;
        $row->route_id = $request->route_id;
        $row->customer_id = $internal_requisition_row->customer_id;
        $row->customer_pin = $internal_requisition_row->customer_pin;
        $row->customer_phone_number = $internal_requisition_row->customer_phone_number;
        $row->customer_discount = $customer->customer_discount;
        // $customer = \App\Model\WaCustomer::where('id', $internal_requisition_row->customer_id)->first();
        $row->status = 'COMPLETED';
        $file = '';
        // if($getLoggeduserProfile->upload_data == 0){
        $upData = \App\Model\WaEsd::whereDate('created_at', date('Y-m-d'))->inRandomOrder()->first();
        if ($upData) {
          $file = $upData->signature;
          $upData->is_used = 1;
          $upData->last_used_by = $getLoggeduserProfile->id;
          $upData->save();
        } else {
          $upData = \App\Model\WaEsd::whereDate('created_at', '>=', date('Y-m-d', strtotime('-1 days')))->inRandomOrder()->first();
          if ($upData) {
            $file = $upData->signature;
            $upData->is_used = 1;
            $upData->last_used_by = $getLoggeduserProfile->id;
            $upData->save();
          }
        }
        // }

        $row->upload_data = $file;
        $row->save();


        foreach ($itemslist as $value) {
          $delivery_quantity = 'delivered_quantity_' . $value->id;
          $issued_quantity = $value->quantity;
          if (!isset($value->quantity) || $issued_quantity <= 0) {
            continue;
          }
          $item_detail = $value->getInventoryItemDetail;
          $item = new WaInventoryLocationTransferItem();
          $item->wa_inventory_location_transfer_id = $row->id;
          $item->wa_inventory_item_id = $value->wa_inventory_item_id;
          $item->quantity = $value->quantity;
          $item->wa_internal_requisition_item_id = $value->id;
          $item->issued_quantity = $issued_quantity;
          $item->note = "";
          $item->standard_cost = @$item_detail->standard_cost;
          // $item->standard_cost = @$value->standard_cost;
          $item->tax_manager_id = @$value->tax_manager_id;
          $item->discount_amount = $value->discount_amount;
          $item->selling_price = @$item_detail->selling_price;
          $item->total_cost = @$item_detail->selling_price * $value->quantity;
          // $item->selling_price = @$value->selling_price;
          $item->store_location_id = @$value->store_location_id;
          $item->to_store_location_id = $row->to_store_location_id;
          // $item->total_cost = @$value->selling_price*$value->quantity;


          $vat_rate = 0;
          $vat_amount = 0;
          if ($item_detail->tax_manager_id && $item_detail->getTaxesOfItem) {
            $vat_rate = $item_detail->getTaxesOfItem->tax_value;
            if ($item->total_cost > 0) {
              $vat_amount = $item->total_cost - ($item->total_cost * 100 / ($item_detail->getTaxesOfItem->tax_value + 100));
            }
          }


          $item->vat_rate = $vat_rate;
          $item->vat_amount = $vat_amount;
          $item->total_cost_with_vat = $item->total_cost;

          $item->save();
        }
        $getUserData = \App\Model\User::where('wa_location_and_store_id', $row->to_store_location_id)->first();
        if ($request->create_new_shift) {
          $shifts = \App\Model\WaShift::where('salesman_id', @$getUserData->id)->where('shift_date', date('Y-m-d'))->where('status', 'open')->orderBy('id', 'desc')->get();
          if (count($shifts) >= 2) {
            $shift = $shifts->first();
          } else {
            if (count($shifts) == 0) {
              $shift_id = date('d/m/Y') . '/' . $row->route;
            } else {
              $shift_id = date('d/m/Y') . '/' . $row->route . '/shift-' . count($shifts);
            }
            $shift = new \App\Model\WaShift();
            $shift->shift_id = $shift_id;
            $shift->route = $row->route;
            $shift->salesman_id = @$getUserData->id;
            $shift->delivery_note = $row->id;
            $shift->shift_date = date('Y-m-d');
            $shift->vehicle_register_no = $row->vehicle_register_no;
            $shift->status = 'open';
            $shift->save();
          }
        } else {
          $shift_id = date('d/m/Y') . '/' . $row->route;
          $shift = \App\Model\WaShift::where('salesman_id', @$getUserData->id)->where('shift_date', date('Y-m-d'))->where('status', 'open')->orderBy('id', 'DESC')->first();
          if (!$shift) {
            $shift = new \App\Model\WaShift();
            $shift->shift_id = $shift_id;
            $shift->route = $row->route;
            $shift->salesman_id = @$getUserData->id;
            $shift->delivery_note = $row->id;
            $shift->shift_date = date('Y-m-d');
            $shift->vehicle_register_no = $row->vehicle_register_no;
            $shift->status = 'open';
            $shift->save();
          }
        }
        $logged_user_profile = $getLoggeduserProfile;

        $totalPriceExcVAT = 0;
        $totalVatAmount = 0;
        $totalAmount = 0;
        $dateTime = date('Y-m-d H:i:s');

        $rowcashsales = new \App\Model\WaCashSales();
        $rowcashsales->cash_sales_number = getCodeWithNumberSeriesPOS('SE');
        $rowcashsales->shift_id = $shift->id;
        $rowcashsales->route = $row->route ?? NULL;
        $rowcashsales->vehicle_reg_no = $row->vehicle_register_no ?? NULL;
        $rowcashsales->wa_customer_id = $row->customer_id;
        $rowcashsales->creater_id = $getLoggeduserProfile->id;
        $rowcashsales->order_date = date('Y-m-d');
        $rowcashsales->document_no = $grn_number;

        $rowcashsales->save();

        $cashsalesid = $rowcashsales->id;

        updateUniqueNumberSeries('SE', $rowcashsales->cash_sales_number);
        // updateUniqueNumberSeries('INTERNAL REQUISITIONS', $grn_number);
        $taxVat = \App\Model\TaxManager::with(['getOutputGlAccount'])->where('slug', 'vat')->first();
        $methodAccId = \App\Model\WaChartsOfAccount::where('account_code', '13004')->first();
        $WaGlTran = [];
        $WaStockMove = [];
        $WaCashSalesItem = [];
        $WaDebtorTran = [];
        $WaDebtorTrantotal = 0;
        $file_name = "";
        WaInventoryLocationTransfer::where('id', $row->id)->update(['shift_id' => $shift->id]);
        $allInternal = WaInternalRequisitionItem::with(['getInventoryItemDetail',
          'getInventoryItemDetail.getInventoryCategoryDetail', 'getInventoryItemDetail.getAllFromStockMoves',
          'getInventoryItemDetail.getInventoryCategoryDetail.getStockGlDetail',
          'getInventoryItemDetail.getInventoryCategoryDetail.getIssueGlDetail',
          'getInventoryItemDetail.getInventoryCategoryDetail.getWIPGlDetail',
        ])->whereIn('id', $request->related_item_ids)->get();
        foreach ($request->related_item_ids as $related_item_id) {
          $related_item_row = $allInternal->find($related_item_id);
          $delivery_quantity = 'delivered_quantity_' . $related_item_id;
          $issued_quantity = $related_item_row->quantity;

          $pvstock_qoh = @$related_item_row->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', $internal_requisition_row->to_store_id)->sum('qauntity') ?? 0;
          //positive entry
          $currstockMove = [];
          $pvstock_qoh += $related_item_row->quantity;
          // $currstockMove[] = [
          //     'user_id'=>$logged_user_profile->id,
          //     'restaurant_id'=>$logged_user_profile->restaurant_id,
          //     'wa_location_and_store_id'=>$internal_requisition_row->to_store_id,
          //     'wa_inventory_item_id'=>$related_item_row->wa_inventory_item_id,
          //     'standard_cost'=>$related_item_row->selling_price,
          //     'qauntity'=>$related_item_row->quantity,
          //     'new_qoh'=>$pvstock_qoh,
          //     'stock_id_code'=>$related_item_row->getInventoryItemDetail->stock_id_code,
          //     'grn_type_number'=>$series_module->type_number,
          //     'grn_last_nuber_used'=>$series_module->last_number_used,
          //     'price'=>$related_item_row->total_cost,
          //     'refrence'=>@$customer->customer_name.' : '.@$customer->customer_code,
          //     'document_no'=>$internal_requisition_row->requisition_no,
          //     'wa_internal_requisition_id'=>$internal_requisition_row->id,
          //     'updated_at'=>date('Y-m-d H:i:s'),
          //     'created_at'=>date('Y-m-d H:i:s'),
          // ];


          $stock_qoh = @$related_item_row->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id', $related_item_row->store_location_id)->sum('qauntity') ?? 0;

          $stock_qoh -= $related_item_row->quantity;

          //negative entry
          $currstockMove[] = [
            'user_id' => $logged_user_profile->id,
            'restaurant_id' => $logged_user_profile->restaurant_id,
            'wa_location_and_store_id' => $related_item_row->store_location_id,
            'wa_inventory_item_id' => $related_item_row->wa_inventory_item_id,
            // 'standard_cost'=>$related_item_row->selling_price,
            'standard_cost' => $request->standard_cost,
            'qauntity' => -($related_item_row->quantity),
            'new_qoh' => $stock_qoh,
            'stock_id_code' => $related_item_row->getInventoryItemDetail->stock_id_code,
            'grn_type_number' => $series_module->type_number,
            'grn_last_nuber_used' => $series_module->last_number_used,
            'price' => $related_item_row->total_cost,
            'refrence' => @$customer->customer_name . ' : ' . @$customer->customer_code,
            'document_no' => $internal_requisition_row->requisition_no,
            'wa_internal_requisition_id' => $internal_requisition_row->id,
            'total_cost' => $related_item_row->quantity && $request->standard_cost ? abs($related_item_row->quantity * $request->standard_cost) : null,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
          ];
          WaStockMove::insert($currstockMove);


          $related_item_row->issued_quanity = $issued_quantity;
          $related_item_row->save();


          // if($this->checkQuantity($internal_requisition_row->to_store_id,$related_item_row->getInventoryItemDetail->stock_id_code,$issued_quantity)=='0'){


          $pvstock_qoh -= $related_item_row->quantity;

          // $WaStockMove[] = [
          //     'user_id'=>$getLoggeduserProfile->id,
          //     'restaurant_id'=>$getUserData->restaurant_id,
          //     'wa_location_and_store_id'=>$getUserData->wa_location_and_store_id,
          //     'wa_inventory_item_id'=>$related_item_row->getInventoryItemDetail->id,
          //     'standard_cost'=>$related_item_row->selling_price,
          //     'qauntity'=>-($related_item_row->quantity),
          //     'new_qoh'=>$pvstock_qoh,
          //     'stock_id_code'=>$related_item_row->getInventoryItemDetail->stock_id_code,
          //     'grn_type_number'=>$series_module->type_number,
          //     'shift_id'=>$shift->id ?? NULL,
          //     'grn_last_nuber_used'=>$series_module->last_number_used,
          //     'price'=>$related_item_row->total_cost,
          //     'refrence'=>$shift->id ?? NULL,
          //     'document_no'=>$grn_number,
          //     'updated_at'=>date('Y-m-d H:i:s'),
          //     'created_at'=>date('Y-m-d H:i:s'),
          //   ];
          $vatrate = $taxVat->tax_value;

          $totalPrice = $related_item_row->total_cost;
          $WaDebtorTrantotal += $totalPrice;

          $PriceExcVAT = (($related_item_row->standard_cost * $related_item_row->quantity) * 100) / (100 + $vatrate);


          $craccountno = @$related_item_row->getInventoryItemDetail->getInventoryCategoryDetail->getStockGlDetail->account_code; //Inventory Account

          $draccountno = @$related_item_row->getInventoryItemDetail->getInventoryCategoryDetail->getIssueGlDetail->account_code; //COS GL

          $WaCashSalesItem[] = [
            'wa_cash_sales_id' => $cashsalesid,
            'quantity' => $related_item_row->quantity,
            'item_no' => $related_item_row->getInventoryItemDetail->id,
            'item_name' => $related_item_row->getInventoryItemDetail->title,
            'standard_cost' => $related_item_row->standard_cost,
            'unit_price' => $related_item_row->selling_price,
            'actual_unit_price' => $related_item_row->selling_price,
            'total_cost' => ($related_item_row->quantity * $related_item_row->selling_price),
            'document_no' => $grn_number,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
          ];

          if ($related_item_row->vat_rate > 0) {

            $related_item_row->standard_cost = ($related_item_row->standard_cost * 100) / ($related_item_row->vat_rate + 100);

          }
          $WaGlTran[] = [
            'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
            'grn_type_number' => $series_module->type_number,
            'grn_last_used_number' => $series_module->last_number_used,
            'transaction_type' => $series_module->description,
            'transaction_no' => $grn_number,
            'trans_date' => $dateTime,
            'restaurant_id' => $getUserData->restaurant_id,
            'shift_id' => $shift->id,
            'account' => $craccountno,
            'amount' => '-' . $related_item_row->standard_cost * $related_item_row->quantity,
            'narrative' => $related_item_row->getInventoryItemDetail->stock_id_code . '/' . $related_item_row->getInventoryItemDetail->title . '/' . $related_item_row->standard_cost . '@' . $related_item_row->quantity,
            'reference' => NULL,
            'supplier_account_number' => NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'cheque_image' => NULL
          ];

          $WaGlTran[] = [
            'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
            'grn_type_number' => $series_module->type_number,
            'grn_last_used_number' => $series_module->last_number_used,
            'transaction_type' => $series_module->description,
            'transaction_no' => $grn_number,
            'trans_date' => $dateTime,
            'restaurant_id' => $getUserData->restaurant_id,
            'shift_id' => $shift->id,
            'account' => $draccountno,
            'amount' => $related_item_row->standard_cost * $related_item_row->quantity,
            'narrative' => $related_item_row->getInventoryItemDetail->stock_id_code . '/' . $related_item_row->getInventoryItemDetail->title . '/' . $related_item_row->standard_cost . '@' . $related_item_row->quantity,
            'reference' => NULL,
            'supplier_account_number' => NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'cheque_image' => NULL
          ];


          $totalPriceExcVAT = $PriceExcVAT;
          $totalVatAmount = (($related_item_row->standard_cost * $related_item_row->quantity) - $PriceExcVAT);//($taxVat->tax_value * $totalPrice) / 100; //($PriceExcVAT - $totalPrice);
          $totalAmount = $related_item_row->standard_cost * $related_item_row->quantity;
          // }

          $salesaccountno = @$related_item_row->getInventoryItemDetail->getInventoryCategoryDetail->getWIPGlDetail->account_code; //Sales GL

          $WaGlTran[] = [
            'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
            'grn_type_number' => $series_module->type_number,
            'grn_last_used_number' => $series_module->last_number_used,
            'transaction_type' => $series_module->description,
            'transaction_no' => $grn_number,
            'trans_date' => $dateTime,
            'restaurant_id' => $getUserData->restaurant_id,
            'shift_id' => $shift->id,
            'account' => $salesaccountno,
            'amount' => '-' . $totalPriceExcVAT,
            'narrative' => NULL,
            'reference' => NULL,
            'supplier_account_number' => NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'cheque_image' => NULL
          ];


          if ($taxVat && $totalVatAmount > 0) {
            $WaGlTran[] = [
              'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
              'grn_type_number' => $series_module->type_number,
              'grn_last_used_number' => $series_module->last_number_used,
              'transaction_type' => $series_module->description,
              'transaction_no' => $grn_number,
              'trans_date' => $dateTime,
              'restaurant_id' => $getUserData->restaurant_id,
              'shift_id' => $shift->id,
              'account' => $taxVat->getOutputGlAccount->account_code,
              'amount' => '-' . $totalVatAmount,
              'narrative' => NULL,
              'reference' => NULL,
              'supplier_account_number' => NULL,
              'updated_at' => date('Y-m-d H:i:s'),
              'created_at' => date('Y-m-d H:i:s'),
              'cheque_image' => NULL
            ];

          }

          $accountNo = @$methodAccId->account_code;


          $WaGlTran[] = [
            'period_number' => $WaAccountingPeriod ? $WaAccountingPeriod->period_no : null,
            'grn_type_number' => $series_module->type_number,
            'grn_last_used_number' => $series_module->last_number_used,
            'transaction_type' => $series_module->description,
            'transaction_no' => $grn_number,
            'trans_date' => $dateTime,
            'restaurant_id' => $getUserData->restaurant_id,
            'shift_id' => $shift->id,
            'account' => $accountNo,
            'amount' => $totalAmount,
            'narrative' => NULL,
            'reference' => NULL,
            'supplier_account_number' => NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'cheque_image' => NULL,
          ];
        }


        $customer_discount = ($customer->customer_discount > 0) ? $customer->customer_discount : 0;
        $customer_discount_amount = ($WaDebtorTrantotal / 100) * $customer_discount;
        $WaDebtorTrantotal -= $customer_discount_amount;

        $WaDebtorTran[] = [
          'salesman_id' => $row->to_store_location_id,
          'salesman_user_id' => @$getUserData->id,
          'type_number' => $series_module->type_number,
          'wa_customer_id' => @$customer->id,
          'wa_route_customer_id' => @$internal_requisition_row->wa_route_customer_id,
          'customer_number' => @$customer->customer_code,
          'invoice_customer_name' => $internal_requisition_row->name,
          'trans_date' => $dateTime,
          'input_date' => $dateTime,
          'wa_accounting_period_id' => $WaAccountingPeriod ? $WaAccountingPeriod->id : null,
          'shift_id' => $shift->id ?? NULL,
          'reference' => $shift->shift_id ?? NULL,
          'amount' => $WaDebtorTrantotal,
          'document_no' => $grn_number,
          'route_id' => $request->route_id,
          'updated_at' => date('Y-m-d H:i:s'),
          'created_at' => date('Y-m-d H:i:s'),
        ];
        if (count($WaDebtorTran) > 0) {
          \App\Model\WaDebtorTran::insert($WaDebtorTran);
        }
        if (count($WaDebtorTran) > 0) {
          \App\Model\WaSalesmanTran::insert($WaDebtorTran);
        }
        if (count($WaCashSalesItem) > 0) {
          \App\Model\WaCashSalesItem::insert($WaCashSalesItem);
        }
        if (count($WaStockMove) > 0) {
          \App\Model\WaStockMove::insert($WaStockMove);
        }
        if (count($WaGlTran) > 0) {
          \App\Model\WaGlTran::insert($WaGlTran);
        }
        $internal_requisition_row->status = 'COMPLETED';
        $internal_requisition_row->save();
        return $row;
      });


      $cost = 0;
      foreach ($request->related_item_ids as $id) {
        $cost += WaInternalRequisitionItem::where('id', $id)->sum('total_cost');
      }

      if ($parent) {
        $clientIDNumber = '123456';
        $clientMSISDN = '254711111111';
        $clientEmail = 'abc@example.com';
        $billRefNumber = $request->requisition_no;
        $billDesc = 'Test';
        $clientNames = $request->emp_name;

        $pesaflow = new Pesaflow();
        $Responsee = $pesaflow->initiate($cost,$clientIDNumber,$clientMSISDN,$clientEmail,$billRefNumber,$billDesc,$clientNames);

        Log::info("PAYLOAD RESPONSE", [$Responsee]);
        if ($Responsee) {
          // Send on pesa flow checkout page
          // return response()->json([
          //        'result'=>1,
          //        'data'=>'$invoiceRequestArr',
          //        'message'=>"Processed Successfully",
          //        'location'=>$Responsee->invoice_link
          // ]);
          // Print Page
          return response()->json([
            'result' => 1,
            'data' => $invoiceRequestArr,
            'message' => "Processed Successfully",
            'location' => route($this->model . '.index') . '?pringrn=' . base64_encode($parent->transfer_no).'&ref='.base64_encode($Responsee->invoice_number)
          ]);
        } else {
          Session::flash('warning', 'something Went wrong');
          return redirect()->route($this->model . '.index');
        }
      }


    } catch (\Exception $e) {
      $msg = $e->getMessage();
      Session::flash('warning', $msg);
      return redirect()->back()->withInput();
    }
  }


  public function checkQuantity($locationid, $itemid, $qty)
  {
    try {
      $qtyOnHand = WaStockMove::where('wa_location_and_store_id', $locationid)->where('stock_id_code', $itemid)->sum('qauntity');
      // echo $qtyOnHand; die;
      //   $item = WaInventoryItem::select('stock_id_code','id')->where('stock_id_code',$itemid)->first();

      //   $item_id = $item->id;


      $myqty = $qty;
      $qtyOnHand = $qtyOnHand;
      if ($myqty <= $qtyOnHand) {
        return '0';
      } else {

        return '1';
      }
    } catch (\Exception $e) {

      return '1';
    }
  }


  public function invoice_dispatch_report(Request $request)
  {
    $permission = $this->mypermissionsforAModule();
    $pmodule = 'invoice-dispatch-report';
    $title = 'Invoice Dispatch Report';
    $model = 'dispatch-invoice-report';
    if (!request()->type) {
      return redirect()->route('confirm-invoice-test.invoice_dispatch_report', ['type' => 'detailed']);
    }
    if (request()->type == 'detailed' && (!isset($permission['invoice-dispatch-report___detailed']) && $permission != 'superadmin')) {
      Session::flash('warning', 'Invalid Request');
      return redirect()->back();
    }
    if (request()->type == 'summary' && (!isset($permission['invoice-dispatch-report___summary']) && $permission != 'superadmin')) {
      Session::flash('warning', 'Invalid Request');
      return redirect()->back();
    }
    $type = request()->type;
    if ($request->input('manage-request')) {
      $shifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('id')->toArray();
      $allshifts = \App\Model\WaShift::where('salesman_id', $request->salesman_id)->whereIn('id', $request->shift_id)->pluck('shift_id')->toArray();
      $salesman = \App\Model\User::where('id', $request->salesman_id)->first();
      $inventory = WaInventoryItem::where('store_location_id', $request->store)->get();
      $funcShift = function ($w) use ($shifts) {
        $w->whereIn('shift_id', $shifts);
      };
      $storeLocation = WaLocationAndStore::where('id', $request->store)->first();
      $items = WaInventoryLocationTransferItem::select(['*', DB::RAW('SUM(quantity - return_quantity) as qty'), DB::RAW('SUM((quantity - return_quantity)*(selling_price+total_cost+commission)) as finalAmount')])
        ->with(['getTransferLocation' => $funcShift])
        ->where('store_location_id', $request->store)
        // ->where('is_return',0)
        // ->whereHas('getRequisitionItem',function($w) {$w->where('is_dispatched',1);})
        ->whereHas('getTransferLocation', $funcShift)->orderBy('created_at', 'DESC')
        ->groupBy('wa_inventory_location_transfer_id', 'wa_inventory_item_id')
        ->get();
      // dd($items);


      if ($request->print) {
        if ($type == 'detailed') {
          return view('admin.issuefullfillrequisitiontest.invoice_dispatch_report_pdf', compact('uom', 'items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
        } else {
          return view('admin.issuefullfillrequisitiontest.invoice_dispatch_report_summary_pdf', compact('uom', 'items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
        }
      }


      if ($type == 'detailed') {
        $pdf = PDF::loadView('admin.issuefullfillrequisitiontest.invoice_dispatch_report_pdf', compact('items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
      } else {
        $pdf = PDF::loadView('admin.issuefullfillrequisitiontest.invoice_dispatch_report_summary_pdf', compact('items', 'inventory', 'allshifts', 'salesman', 'storeLocation'));
      }

      $report_name = 'invoice_dispatch_report_' . date('Y_m_d_H_i_A');
      // return $pdf->stream();

      $x = 250;
      $y = 10;
      $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
      $font = null;
      $size = 14;
      $color = array(255, 0, 0);
      $word_space = 0.0;  //  default
      $char_space = 0.0;  //  default
      $angle = 0.0;   //  default
      //$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


      return $pdf->download($report_name . '.pdf');
    }
    $storeLocation = WaLocationAndStore::pluck('location_name', 'id')->toArray();
    $breadcum = [$title => route('confirm-invoice-test.invoice_dispatch_report'), 'Listing' => ''];
    return view('admin.issuefullfillrequisitiontest.invoice_dispatch_report', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'type', 'storeLocation'));

  }
}
