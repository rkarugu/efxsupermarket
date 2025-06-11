<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Model\WaRouteCustomer;
use App\PosCustomer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Model\WaCustomer;
//use App\Model\WaRouteCustomer;
use Illuminate\Support\Str;
use JWTAuth;

class PosCustomerController extends Controller
{
    public function __construct()
    {
        $this->default_customer = "CUST-00001";
        $this->model = 'wa-route-customer';
        $this->title = 'Route Customers';
        $this->pmodule = 'wa-route-customer';

    }

    /**
     * @throws Exception
     */
    public function get_parent()
    {

        $parent = WaCustomer::where('customer_code', $this->default_customer)->first();
        if (!$parent) {
            throw new Exception("Customer Account Not Found! Please contact administration department");
        }
        return $parent;
    }

    public function getPosCustomers(Request $request): JsonResponse
    {
        $parent = $this->get_parent();
        $search = $request->search;

        try {
            $user = JWTAuth::toUser($request->token);
            $route = Route::where('is_pos_route', true)->where('restaurant_id', $user->restaurant_id)->first();
            $customers = DB::table('wa_route_customers')
                ->where('customer_id',$parent->id)
                ->where('route_id', $route->id)
                ->where('status', '!=', 'duplicate')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->select('id', 'name', 'phone','kra_pin')
                ->get();
            return $this->jsonify(['data' => $customers], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function addPosCustomer(Request $request): JsonResponse
    {
        try {
            $parent = $this->get_parent();

            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'customer_id' => 'sometimes|nullable|exists:wa_route_customers,id',
                'pin' => [
                    'sometimes',
                    'nullable',
                    'regex:/^[AP]\d{9}[A-Z]$/',
                ],
                'phone_number' => [
                    'required',
                    'numeric',
                    'regex:/^((\+254|254|0)[71]\d{8})$/',
                    function ($attribute, $value, $fail) use ($parent, $request) {
                        if (!$request->customer_id && WaRouteCustomer::where('phone', $value)->where('customer_id',$parent->id)->exists()) {
                            $fail('Customer With same Phone Number Exits.');
                        }
                    },
                ],
            ],[
                'phone_number.regex' => 'Phone Number Provided is not valid',
            ]);

            if ($validation->fails()) {
                return $this->jsonify(['message' => $validation->errors()], 422);
            }


            $user = JWTAuth::toUser($request->token);
            $route = Route::where('is_pos_route', true)->where('restaurant_id', $user->restaurant_id)->first();


            $customerData = [
                'created_by' => JWTAuth::toUser($request->token)->id,
                'name' => Str::title($request->name),
                'phone' => $request->phone_number,
                'bussiness_name' => $request->name,
                'town' => $parent->town ?? null,
                'kra_pin' => $request->pin ?? null,
                'customer_id' => $parent->id,
                'route_id' => $route->id,
                'is_verified' => true,
                'status' => 'approved',
            ];

            $customer = WaRouteCustomer::updateOrCreate(
                ['id' => $request->customer_id ?? null],  // Find by customer_id if provided
                $customerData  // Data to update or create with
            );


            return $this->jsonify(['data' => $customer], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
