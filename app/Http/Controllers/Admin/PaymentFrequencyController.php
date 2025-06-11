<?php

namespace App\Http\Controllers\Admin;
use Session;
use Exception;
use App\Model\Branch;
use Illuminate\Http\Request;
use App\Model\PaymentFrequency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentFrequencyController extends Controller
{

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'payment-frequency';
        $this->title = 'Payment Frequency';
        $this->pmodule = 'payment-frequency';
        $this->pageUrl = 'payment-frequency';

    } 

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = Branch::orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.PaymentFrequency.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        } 
            
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            return view('admin.PaymentFrequency.create',compact('title','model','breadcum'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    public function store(Request $request)
    {
        try
        {
             $validator = Validator::make($request->all(), [
                'frequency' => 'required|max:255|unique:wa_payment_frequency',
                'description' => 'required',
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row = new PaymentFrequency();
                $row->frequency= $request->frequency;
                $row->description= $request->description;
                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model.'.index'); 
            }
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


   public function Datatables(Request $request) {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $columns = [
            'id', 'frequency','description'
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $data_query =  PaymentFrequency::select('wa_payment_frequency.*');
    
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $data_query = $data_query->where(function($data_query) use ($search) {
                $data_query->where('frequency', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
                    
            });
            
        }
        $data_query_count = $data_query;
        $totalFiltered = $data_query_count->count();
        $data_query = $data_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($data_query)) {
            foreach ($data_query as $key => $row) { 
                $user_link = '';
                $nestedData['ID'] = $key + 1;
                $nestedData['Frequency'] = $row->frequency;
                $nestedData['Description'] = $row->description;
                $nestedData['action'] =  "<span class='f-left margin-r-5'><a data-toggle='tooltip'  class='btn btn-primary small-btn' title='Edit' href='" . route('payment-frequency.edit',['id'=> $row->id])." '><i class='fa fa-pencil-square' aria-hidden='true'></i></a></span>
                <form  action='" . route('payment-frequency.delete',['id'=> $row->id])." ' accept-charset='UTF-8' style='display:inline'><input name='_method' value='DELETE' type='hidden'>
" . csrf_field() . "<span><button data-toggle='tooltip' title='Delete' type='submit' class='btn btn-danger small-btn'><i class='fa fa-trash' aria-hidden='true'></i></button></span></form>";
          
                $data[] = $nestedData;
            }
        
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
}



    public function edit($slug){
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {
                $row =  PaymentFrequency::where('id',$slug)->first();
                if($row){
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title=>route($this->model.'.index'),'Edit'=>''];
                    $model =$this->model;
                    return view('admin.PaymentFrequency.edit',compact('title','model','breadcum','row')); 
                }
            else{
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else{
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(Request $request, $slug)
    {
        try
        {
            $row =  PaymentFrequency::where('id',$slug)->first();
            $validator = Validator::make($request->all(), [
                'frequency' => 'required|max:255|unique:wa_payment_frequency,frequency,' . $row->id,
                'description' => 'required',
               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            else
            {
                $row->frequency= $request->frequency;
                $row->description= $request->description;
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model.'.index');
            }
           
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function delete($slug){
        try{
             PaymentFrequency::where('id',$slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {
            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    // API
    public function paymentFrequencyList()
    {
        return response()->json(PaymentFrequency::orderBy('frequency')->get());
    }

    public function paymentFrequencyCreate(Request $request)
    {
        $data = $request->validate([
            'frequency' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $paymentFrequency = PaymentFrequency::create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Frequency added successfully',
            'data' => $paymentFrequency
        ], 201);
    }

    public function paymentFrequencyEdit(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:wa_payment_frequency,id',
            'frequency' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $paymentFrequency = PaymentFrequency::find($request->id);

            array_shift($data);
            $paymentFrequency->update($data);

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Frequency updated successfully',
            'data' => $paymentFrequency
        ]);
    }

    public function paymentFrequencyDelete($id)
    {
        try {
            $paymentFrequency = PaymentFrequency::find($id);

            $paymentFrequency->delete();

        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Payment Frequency deleted successfully',
        ]);
    }
}
