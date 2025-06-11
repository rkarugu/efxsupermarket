<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuggestedOrder;
use App\Http\Requests\UpdateSuggestedOrderRequest;
use Illuminate\Http\Request;
use App\Services\ApiService;

class SuggestedOrderController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'suggested_order';
        $this->title = 'Suggested Orders';
        $this->pmodule = 'suggested-order';
    }
    protected $status = [
        'pending'=>['Pending'],
        'completed'=>['Accepted'],
        'rejected'=>['Rejected']
    ];

    public function index(Request $request){
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (isset($permission[$pmodule . '___list']) || $permission == 'superadmin') {
                $list = SuggestedOrder::with(['getSupplier','items'])
                ->whereBetween('order_date',[$request->from ?? date('Y-m-d'), $request->to ?? date('Y-m-d')])
                ->where(function($e) use ($request){
                    if($request->supplier){
                        $e->where('wa_supplier_id',$request->supplier);
                    }
                })->orderBy('id',"DESC")->simplePaginate(10);
                $model = $this->model;
                return view('admin.'.$model.'.index', compact('list','title','model'));
            }
            throw new \Exception("You don't have enough permissions");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }

    public function show($id){
        try {
            $permission = $this->mypermissionsforAModule();
            $pmodule = $this->pmodule;
            $title = $this->title;
            $model = $this->model;
            if (isset($permission[$pmodule . '___list']) || $permission == 'superadmin') {
                $order = SuggestedOrder::with(['getSupplier','items.inventory_item'])->findOrFail($id);
                return view('admin.'.$model.'.show', compact('order','title','model'));
            }
            throw new \Exception("You don't have enough permissions");
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", $th->getMessage());
        }
    }

    public function update(UpdateSuggestedOrderRequest $request, $id){
        try {
            $order = SuggestedOrder::with(['getSupplier','items.inventory_item'])->findOrFail($id);
            $order->status = $request->status;
            $order->reject_reason = $request->reject_reason ?? "";
            $order->finished_at = date('Y-m-d H:i:s');
            $order->save();
            (new ApiService(env('SUPPLIER_PORTAL_URI')))->postRequest('/api/suggested-order/get/'.$order->order_number,[
                'status'=>$request->status,
                'reject_reason'=>$request->reject_reason
            ]);
            return response()->json([
                'result'=>1,
                'message'=>'Suggested Order Status Updated',
                'location'=>route('suggested-order.index')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'result'=>-1,
                'message'=>$th->getMessage(),
            ]);
        }
    }

}


