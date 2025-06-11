<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaRouteCustomer;
use App\Model\CustomerM;
use App\Model\WaInventoryLocationTransfer;
use PDF;
use Excel;
use DB;
use Session;
use App\Model\WaDebtorTran;

class RouteCustomerReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'route-customers';
        $this->title = 'Route Customers';
        $this->pmodule = 'route-customers';
    } 

    public function index(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {            
            $lists = CustomerM::with(['route_customers','route'])->whereHas('route_customers')->orderBy('id', 'DESC')->paginate(1);
            



            if($request->type == 'excel' && (isset($permission[$pmodule.'___excel']) || $permission == 'superadmin') ){
                

                $excelData = CustomerM::with(['route_customers','route'])->whereHas('route_customers')->orderBy('id', 'DESC')->get();

                $status = $request->status ?? 'PENDING';
                
                $mainData = [];  
                $gt = 0;
                foreach($excelData as $list){
                    
                    $break1 = [];
                    $break1['Dated'] = '';
                    $break1['Name'] = '';
                    $break1['Phone No'] = '';
                    $break1['Business'] = '';
                    $break1['Town'] = '';
                    $break1['Contact Person'] = '';
                    $mainData[] = $break1;




                    $listData = [];
                    $listData['Dated'] = 'Route Name:';
                    $listData['Name'] = @$list->route->route_name;
                    $listData['Phone No'] = '';
                    $listData['Business'] = '';
                    $listData['Town'] = '';
                    $listData['Contact Person'] = '';
                    $mainData[] = $listData;

                    $break2 = [];
                    $break2['Dated'] = '';
                    $break2['Name'] = '';
                    $break2['Phone No'] = '';
                    $break2['Business'] = '';
                    $break2['Town'] = '';
                    $break2['Contact Person'] = '';
                    $mainData[] = $break2;

                    foreach($list->route_customers as $item){
                        $childData = [];
                        $childData['Dated'] = date('d/M/Y',strtotime($item->created_at));
                        $childData['Name'] = $item->name;
                        $childData['Phone No'] = $item->phone;
                        $childData['Business'] = $item->bussiness_name;
                        $childData['Town'] = @$d->item->bussiness_name;
                        $childData['Contact Person'] = @$item->contact_person;
                        $mainData[] = $childData;
                        unset($childData);
                        $gt += @$d->total;

                    }
                }

                //dd($mainData);
                // $childData = [];
                // $childData['User'] = '';
                // $childData['Cash Sales No'] = '';
                // $childData['Date and Time'] = '';
                // $childData['Customer'] = '';
                // $childData['Item'] = '';
                // $childData['Description'] = '';
                // $childData['Unit'] = '';
                // $childData['Qty'] = '';
                // $childData['Returned Qty'] = '';
                // $childData['Selling Price'] = '';
                // $childData['Location'] = '';
                // $childData['Discount'] = '';
                // $childData['Vat'] = 'Grand Total';
                // $childData['Total'] = $gt;
                // $mainData[] = $childData;
                // unset($childData);
                return Excel::create('Route-Customer-Report-'.time(), function($excel) use ($mainData) {
                    $excel->sheet('mySheet', function($sheet) use ($mainData){
                        $sheet->fromArray($mainData);
                    });
                })->download('xlsx');
            }
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.route_customers.index',compact('title','lists','model','breadcum','pmodule','permission'));        
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }             
    }

    public function sales_old(Request $request)
    {

        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___sales']) || $permission == 'superadmin')
        {            
            $dateFrom = ($request->date_from ?? date('Y-m-d'));
            $dateTo = ($request->date_to ?? date('Y-m-d'));
            $date = [$dateFrom,$dateTo];
            $lists = WaRouteCustomer::with(['route'])->select([
                'wa_route_customers.*',
                DB::RAW('SUM(wa_debtor_trans.amount) as total_sales'),
               // DB::RAW('(select SUM(wa_debtor_trans.amount) from wa_debtor_trans where  wa_debtor_trans.route_customer_id = wa_route_customers.id and  wa_debtor_trans.document_no  Like   "%INV%") as total_sales'),
                //DB::RAW('(select SUM(wa_debtor_trans.amount) from wa_debtor_trans where wa_debtor_trans.route_customer_id = wa_route_customers.id ) as total_return')
            ])/**/
            ->join('wa_debtor_trans',function($e) use ($date){
                $e->on('wa_debtor_trans.route_customer_id','=','wa_route_customers.id');
                $e->whereBetween('wa_debtor_trans.trans_date',$date);
            })
            ->orderBy('wa_route_customers.id', 'DESC')->groupBy('wa_route_customers.route_id')->paginate(50);

            //dd($lists->toArray());
            
            if($request->manage == 'pdf'){
                $pdf = PDF::loadView('admin.route_customers.sales_pdf',compact('title','lists','model','breadcum','pmodule','permission','dateFrom','dateTo'));
            return $pdf->download('route_customers.pdf');
            
            }

            $breadcum = [$title=>route($model.'.sales'),'Listing'=>''];
            return view('admin.route_customers.sales',compact('title','lists','model','breadcum','pmodule','permission','dateFrom','dateTo'));        
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }             
    }

    public function sales(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___sales']) || $permission == 'superadmin')
        {            
            $dateFrom = ($request->date_from ?? date('Y-m-d'));
            $dateTo = ($request->date_to ?? date('Y-m-d'));
            $date = [$dateFrom,$dateTo];
            
            $lists=[];
            if($request->has('filtered')){
                $lists = WaInventoryLocationTransfer::with(['getRelatedItem_ForReturn'=>function($w){
                    $w->where('quantity','>',DB::RAW('wa_inventory_location_transfer_items.return_quantity'));

                    $w->where('is_return',1);
                },'getRelatedItem'=>function($w){
                   
                    $w->where('is_return',0);
                },'getBranch','getDepartment','fromStoreDetail','toStoreDetail','getrelatedEmployee'])->where('status','!=','UNCOMPLETED');

                if ($request->has('date_from')){
                    $lists = $lists->whereDate('created_at','>=',$request->input('date_from'));
                
                }
                if ($request->has('date_to')){
                    $lists = $lists->whereDate('created_at','<=',$request->input('date_to'));
                
                }
                $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
                $lists = $lists->orderBy('id', 'desc')->groupBy('to_store_location_id')->paginate(10);

                foreach ($lists as $key => $value) {
                    $lists[$key]['total_sales'] = getTotalSales($value->to_store_location_id,$request->input('date_from'),$request->input('date_to'));
                    $lists[$key]['total_return'] =  getTotalReturn($value->to_store_location_id,$request->input('date_from'),$request->input('date_to'));
                }
            }
            
            if($request->manage == 'pdf'){
                $pdf = PDF::loadView('admin.route_customers.sales_pdf',compact('title','lists','model','breadcum','pmodule','permission','dateFrom','dateTo'));
            return $pdf->download('Salesman_Summary.pdf');
            
            }

            $breadcum = [$title=>route($model.'.sales'),'Listing'=>''];
            return view('admin.route_customers.sales',compact('title','lists','model','breadcum','pmodule','permission','dateFrom','dateTo'));        
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }             
    }
}
