<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 
use App\Model\WaCustomer;
use App\Model\WaRouteCustomer;
use DB;
use Session;
use PDF,Excel;


class RouteUserController extends Controller
{


    public function index(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = 'route-customers';
        $title = 'Route Customers';
        $model = 'route-customers';

        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin'){
            $user = getLoggeduserProfile();
            // $data['customer'] =  WaCustomer::where('id',$id)->first();
            $data['title'] = 'Route Customers';
            $listsQry = WaRouteCustomer::select('*',
                DB::RAW(' (Select SUM(wa_debtor_trans.amount) from wa_debtor_trans where wa_debtor_trans.wa_route_customer_id=wa_route_customers.id group by wa_debtor_trans.wa_route_customer_id) as total_sales  '));
            if($user->id != 1){
                $listsQry->where('created_by',$user->id);
            }
            $data['lists'] =  $listsQry->orderBy('id','DESC')->get();
            $data['model'] = $model;
            return view('admin.myroutecustomer.route_customer_list')->with($data);
        }else{
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
    }

    public function edit(Request $request,$id){
            $pmodule = 'route-customers';
            $title = 'Route Customers';
            $model = 'route-customers';

        $data['customer'] =  WaRouteCustomer::where('id',$id)->first();
        $data['title'] = 'Edit Route Customers';
        $data['model'] = $model;
        return view('admin.myroutecustomer.route_customer_edit')->with($data);
    }





    public function route_customer_update(Request $request,$id){
        $validations = Validator::make($request->all(),[
            'route_id'=>'required|exists:wa_customers,route_id',
            'customer_id'=>'required|exists:wa_customers,id',
            'name'=>'required|string|min:1|max:200',
            'phone_no'=>'required|numeric|digits_between:9,12',
            'business_name'=>'required|string|min:1|max:200',
            'town'=>'nullable|string|min:1|max:200',
            'contact_person'=>'nullable|string|min:1|max:200',
        ]);
        if($validations->fails()){
            return response()->json([
                'result'=>0,
                'errors'=>$validations->errors()
            ]);
        }

        // $id = $request->customer_id;
        $check = DB::transaction(function () use ($request,$id){
            $user = getLoggeduserProfile();

            $data = WaRouteCustomer::where('id',$id)->first();
            $data->created_by = $user->id;
            $data->route_id = $request->route_id;
            $data->customer_id = $request->customer_id;
            $data->name = $request->name;
            $data->phone = $request->phone_no;
            $data->bussiness_name = $request->business_name;
            $data->town = $request->town;
            $data->contact_person = $request->contact_person;
            $data->save();
            return true;
        });
        if($check){

            return $request->ajax() ? response()->json([
                'result'=>1,
                'message'=>'Customer update Successfully',
                'location'=>route('my-route-customers.index'),
            ]) : redirect()->route('my-route-customers.index')->with('success', 'Customer update Successfully');
        }
         return $request->ajax() ? response()->json([
                'result'=>-1,
                'message'=>'Something went wrong',
            ]) : redirect()->back()->with('danger', 'Something went wrong');
    }

    
}
