<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Http\Controllers\Controller;
use App\Jobs\SendBulkSms;
use App\Model\Restaurant;
use App\Model\Route;
use App\Model\User;
use App\Model\WaCustomer;
use App\Model\WaSupplier;
use App\Models\BulkSms;
use App\Models\BulkSmsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\InfoSkySmsService;
use App\Services\AirTouchSmsService;
use Barryvdh\DomPDF\Facade\Pdf;

class BulkSmsController extends Controller
{
    public function __construct(protected InfoSkySmsService $infoSkyService, protected AirTouchSmsService $airTouchService)
    {
    }

    public function create_bulk_message()
    {
        if (!can('create', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $title = 'Create';
        $model= 'bulk-sms-create';

        $breadcum = [
            'Bukl SMS' => '',
            $title => ''
        ];
        $suppliers = WaSupplier::where('is_verified',1)->where('telephone','!=',NULL)->get();

        return view('admin.bulk_sms.create',compact('title','model','breadcum','suppliers'));

    }

    public function save_bulk_message(Request $request)
    {
        if (!can('create', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $request->validate([
            'title'=>'required|string',
        ]);

        if ($request->contact_group == 'Suppliers') {
            if ($request->all_suppliers) {
                
                $phoneNumbers = DB::table('wa_location_and_stores');
                if ($request->branch == 'all') {
                    $branches = Restaurant::get()->pluck('id');
                    $phoneNumbers->whereIn('wa_location_and_stores.wa_branch_id',$branches);
                } else {
                    $phoneNumbers->where('wa_location_and_stores.wa_branch_id',$request->branch);
                }
                
                $phoneNumbers = $phoneNumbers->select('wa_suppliers.telephone')
                            ->whereNotNull('wa_suppliers.telephone')
                            ->where('wa_suppliers.telephone','!=','\N')
                            ->where(DB::raw('LENGTH(wa_suppliers.telephone)'),'>','7')
                            ->join('wa_inventory_location_stock_status','wa_inventory_location_stock_status.wa_location_and_stores_id','wa_location_and_stores.id')
                            ->join('wa_inventory_item_suppliers','wa_inventory_item_suppliers.wa_inventory_item_id','wa_inventory_location_stock_status.wa_inventory_item_id')
                            ->join('wa_suppliers','wa_suppliers.id','wa_inventory_item_suppliers.wa_supplier_id')
                            ->distinct()
                            ->get()
                            ->pluck('telephone');
            } else {
                $phoneNumbers = DB::table('wa_suppliers')
                        ->whereIn('id',$request->suppliers)
                        ->whereNotNull('telephone')
                        ->where('telephone','!=','\N')
                        ->where(DB::raw('LENGTH(telephone)'),'>','7')
                        ->get()->pluck('telephone');
            }
        }

        if ($request->contact_group == 'Employees') {
            if ($request->all_employees) {
                $query = DB::table('users');
            } elseif ($request->all_employees_role) {
                $query = DB::table('users')->where('role_id',request()->role);
            } else{
                $query = DB::table('users')
                    ->whereIn('id',$request->employees);
            }
            if ($request->branch == 'all') {
                $branches = Restaurant::get()->pluck('id');
                $query->whereIn('restaurant_id',$branches);
            } else {
                $query->where('restaurant_id',$request->branch);
            }
            
            $phoneNumbers = $query->where('status','1')
                        ->whereNotNull('phone_number')
                        ->where(DB::raw('LENGTH(phone_number)'),'>','7')
                        ->get()->pluck('phone_number');
        }

        if ($request->contact_group == 'Customers') {
            $query = DB::table('wa_route_customers')
                ->leftJoin('routes', 'routes.id', 'wa_route_customers.route_id')
                ->where('routes.restaurant_id', $request->branch);
            if ($request->all_customers) {
                $phoneNumbers = $query->get()->pluck('phone');
            } elseif ($request->all_customers_route) {
                $phoneNumbers = $query->where('wa_route_customers.route_id',request()->route)
                    ->get()->pluck('phone');
            } else{
                $phoneNumbers = $query->whereIn('wa_route_customers.id',$request->customers)
                ->get()->pluck('phone');
            }
        }        
        try {
            $message = $request->title."\n"
            . $request->message;
            $user = Auth::user();

            SendBulkSms::dispatch($message, $phoneNumbers, $request->issn, $request->title, $request->contact_group, $request->branch, $user->id);

            request()->session()->flash('success', 'Messages Sent Successfully.');
        } catch (\Exception $e) {
            request()->session()->flash('danger', $e->getMessage());
        }

        return redirect()->back();
    }

    public function test_message()
    {
        if (!can('test-message', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $title = 'Test Message';
        $model= 'bulk-sms-test-message';

        $breadcum = [
            'Bukl SMS' => '',
            $title => ''
        ];

        return view('admin.bulk_sms.test_message',compact('title','model','breadcum'));
    }

    public function test_message_save(Request $request)
    {
        if (!can('test-message', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $telephone = $this->phoneNumberCleanup($request->msisdn);
            switch ($request->issn) {
                case env("KANINI_SMS_SENDER_ID_2"):
                    $response = $this->infoSkyService->sendMessageResponse($request->message,$telephone,env("KANINI_SMS_SENDER_ID_2"));
                    break;
                case env("KANINI_SMS_SENDER_ID"):
                    $response = $this->infoSkyService->sendMessageResponse($request->message,$telephone,env("KANINI_SMS_SENDER_ID"));
                    break;
                case env("AIRTOUCH_ISSN"):
                    $response = $this->airTouchService->sendMessageResponse($request->message,$telephone);
                    break;
                
                default:
                
                    break;
            }
            if ($response) {
                
                $stat = 0;
                if($response==1){
                    $stat = 1;
                    $check="";
                } else{
                    $check= $response;
                }
                BulkSmsMessage::create([
                    'created_by' => Auth::user()->id,
                    'issn' => $request->issn,
                    'phone_number' => $telephone,
                    'message' => $request->message,
                    'category' => 'Test SMS',
                    'send_status' => $stat,
                    'sms_length' => mb_strlen($request->message),
                    'sms_response'=> @$check,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
           
            DB::commit();
            request()->session()->flash('success', 'Test Message Sent Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }

        return redirect()->back();
    }

    public function message_log()
    {
        if (!can('view', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $title = 'Message Log';
        $model= 'bulk-sms-message-log';

        $breadcum = [
            'Bukl SMS' => '',
            $title => ''
        ];

        if (request()->wantsJson()) {            
            $logs = BulkSms::with('branch','messages')->select('bulk_sms.*')->orderBy('created_at','desc');
            if(request()->start_date && request()->end_date){
                $logs->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
            }
            return DataTables::eloquent($logs)
                ->addIndexColumn()     
                ->addColumn('date',function($log){
                    return date('d-m-Y',strtotime($log->created_at));
                })
                ->addColumn('time',function($log){
                    return date('h:i A',strtotime($log->created_at));
                })  
                ->addColumn('recipients',function($log){
                    return $log->messages->count();
                })     
                ->editColumn('branch.name',function($log){
                    if($log->branch){
                        return $log->branch->name;
                    } else{
                        return 'All';
                    } 
                })  
                ->toJson();
        }
        if (request()->filled('type')) {
            if (request()->type == 'pdf') {
                $logs = DB::table('bulk_sms_messages')->orderBy('created_at','desc');
                if (request()->filled('delivery_status')) {
                    $logs->where('send_status',request()->delivery_status);
                }
                if (request()->filled('issn')) {
                    $logs->where('issn',request()->issn);
                }
                if (request()->filled('category')) {
                    $logs->where('category',request()->category);
                }
                if(request()->start_date && request()->end_date){
                    $logs->whereBetween('created_at',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
                }
                $logs = $logs->get();
                $pdf = PDF::loadView('admin.bulk_sms.logs_pdf', compact('logs'));
                $report_name = 'bulk-sms-logs' . date('Y_m_d_H_i_A');
                // return $pdf->stream();
                return $pdf->download($report_name . '.pdf');
            }
            if (request()->type == 'excel') {
                $customerPayments = $debtors->get()->map(function ($debtor) {
                return [
                    'trans_date' => Carbon::parse($debtor->trans_date)->format('Y-m-d'),
                    'document_no' => $debtor->document_no,
                    'amount' => number_format(abs($debtor->amount)),
                    'channel' => $debtor->channel,
                    'branch' => $debtor->branch_name,
                    'route' => $debtor->customer_name,
                    'reference' => $debtor->reference ?? '-',
                    'verification' => $debtor->verification_status,
                ];
            });


        $export = new DebtorTransactionsExport(collect($customerPayments));
        return Excel::download($export, 'System Transations.xlsx');
            }
        }
        
        return view('admin.bulk_sms.message_log',compact('title','model','breadcum'));
    }

    public function message_log_view($id)
    {
        if (!can('view', 'bulk-sms')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $title = 'Message Log';
        $model= 'bulk-sms-message-log';

        $breadcum = [
            'Bukl SMS' => '',
            $title => ''
        ];

        $logs = BulkSms::with('messages','branch')->find($id);

        return view('admin.bulk_sms.message_log_view',compact('title','model','breadcum','logs'));
    }

    public function employee_info()
    {
        $employees = DB::table('users')->where('status','1');
        
        if (request()->filled('branch')) {
            $employees->where('restaurant_id',request()->branch);
        }
       
        if (request()->filled('role')) {
            $employees->where('role_id',request()->role);
        }

        return $employees->select('name', 'id')->get();
    }

    public function customer_info()
    {
        $customer = DB::table('wa_route_customers')->where('status','approved')->whereNull('deleted_at');

        if (request()->filled('route')) {
            $customer->where('route_id',request()->route);
        }

        return $customer->select('name', 'id')->get();
    }

    public function supplier_info($branch)
    {
        if ($branch == 'all') {
            return WaSupplier::where('is_verified',1)
                    ->where('telephone','!=',NULL)
                    ->select('name', 'id')
                    ->get();
        } else{
            return DB::table('wa_location_and_stores')
                    ->where('wa_location_and_stores.wa_branch_id',$branch)
                    ->select('wa_suppliers.name','wa_suppliers.id')
                    ->join('wa_inventory_location_stock_status','wa_inventory_location_stock_status.wa_location_and_stores_id','wa_location_and_stores.id')
                    ->join('wa_inventory_item_suppliers','wa_inventory_item_suppliers.wa_inventory_item_id','wa_inventory_location_stock_status.wa_inventory_item_id')
                    ->join('wa_suppliers','wa_suppliers.id','wa_inventory_item_suppliers.wa_supplier_id')
                    ->distinct()
                    ->get();
        }
        
    }

    public function routes($branch)
    {
        if ($branch == 'all') {
            $branches = Restaurant::get()->pluck('id');
            $routes = Route::select('id', 'route_name')->whereIn('restaurant_id', $branches)->get();
        } else{
            $routes = Route::select('id', 'route_name')->where('restaurant_id', $branch)->get();
        }       

        return response()->json([
            'routes' => $routes
        ]);
    }

    private function phoneNumberCleanup($phone)
    {
        $phone = trim($phone);$original = $phone;
        $phone = str_replace('+','',$phone);
        $phone = str_replace('254 ','254',$phone);
        $phone = str_replace('--','',$phone);
        $phone = str_replace('-',' ',$phone);
        
        if (str_contains($phone, ' ')) {
            $parts = explode(' ', $phone);
            
            if (!is_numeric($parts[0])) {
                array_shift($parts);
            }
            if (count($parts) > 1) {
                $phone = implode(' ', $parts);
                $phone = trim($phone);
                $parts2 = explode(' ', $phone);
                
                if (!is_numeric($parts2[0])) {
                    array_shift($parts2);
                }
                
                if (count($parts2) > 1) {
                    
                    $phone = implode('', $parts2);
                   
                    $phone = trim($phone);
                } else {
                    $phone = implode('', $parts2);
                }
            } else {
                $phone = implode('', $parts);
            }
            
        }
        
        if(str_contains($phone, '-')){
            $dashed = explode('-', $phone);
            if (!is_numeric($dashed[0])) {
                array_shift($dashed);
            }
            if (count($dashed) > 1) {
                $phone = implode('-', $dashed);
            } else {
                $phone = $dashed[0];
            }
        }
        if(str_contains($phone, ',')){
            $comma = explode(',', $phone);
            if (!is_numeric($comma[0])) {
                array_shift($comma);
            }
            if (count($comma) > 1) {
                $phone = implode(',', $comma);
            } else {
                $phone = $comma[0];
            }
        }
        $phone = (int)$phone;
        if (strpos($phone, '254') !== 0) {
            $phone = '254' . $phone;
        }
        return (int)$phone;
    }
}
