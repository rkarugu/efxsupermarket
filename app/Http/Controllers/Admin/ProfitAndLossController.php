<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaChartsOfAccount;
use App\Model\WaAccountSection;
use App\Model\WaGlTran;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class ProfitAndLossController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'profit-and-loss';
        $this->title = 'Profit & Loss';
        $this->pmodule = 'profit-and-loss';
    }
    public function glEntriesByAccountcode(Request $request,$accountcode){
        $permission = $this->mypermissionsforAModule();
        $pmodule = "genralLedger";
        $title = 'GL Entries';
        $model = 'genralLedger-gl_entries';
        
			$date1 = $request->get('to');
			$date2 = $request->get('from');

			$restroList = $this->getRestaurantList();
            
            $data = WaGlTran::orderBy('id', 'desc')->with('restaurant');
            $data->where('account', $accountcode);
            $data->groupBy('transaction_no');

			if($request->restaurant){
			$data->where('restaurant_id', $request->restaurant);
			}
			if($date1!="" && $date2!=""){
				$data->whereDate('trans_date', '>=', $date1)->whereDate('trans_date', '<=', $date2);
			}
            $data = $data->get();
			
			// $dataarr = [];
			// foreach($data as $key=> $val){
	  //           $data = WaGlTran::orderBy('id', 'desc')->with('restaurant');
	  //           $data->where('account', $accountcode);
   //              $data->groupBy('transaction_no');

	  //           //$data->orWhere('transaction_no', $val->transaction_no);
			// 	if($request->restaurant){
			// 	$data->where('restaurant_id', $request->restaurant);
			// 	}
			// 	if($date1!="" && $date2!=""){
			// 		$data->whereDate('trans_date', '>=', $date1)->whereDate('trans_date', '<=', $date2);
			// 	}
	  //           $data = $data->get();

			// 	$dataarr[$key] = $data;

	  //           $negativeAMount =  WaGlTran::where('amount', '<=', '0');
		 //        $negativeAMount->where('account', $accountcode);
		 //      //  $negativeAMount->orWhere('transaction_no', $val->transaction_no);
	  //           $negativeAMount = $negativeAMount->sum('amount');
	
	  //           $positiveAMount =  WaGlTran::where('amount', '>=', '0');
			// 	$positiveAMount->where('account', $accountcode);
			// 	//$positiveAMount->orWhere('transaction_no', $val->transaction_no);
			// 	$positiveAMount = $positiveAMount->sum('amount');

			// }
			$negativeAMount =  0;
            $positiveAMount =  0;

            $breadcum = ["Profit & Loss" => route('profit-and-loss.index'), 'Listing' => ''];
           // $data = $dataarr;
			//echo "<pre>"; print_r($data); die;
            return view('admin.profit_and_loss.gl_entries', compact('data','title','restroList','model', 'breadcum', 'pmodule', 'permission','negativeAMount','positiveAMount'));

    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
       // if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
                // ->whereHas('getWaAccountGroup', function ($sql_query) {
                //     $sql_query->where('profit_and_loss', 'Y');
                // })
                ->orderBy('section_number', 'ASC')
                ->whereIn('section_name', ['INCOME', 'COST OF SALES', 'OVERHEADS'])
                ->get()->toArray();
            $restroList = $this->getRestaurantList();

             //echo "<pre>"; print_r($lists); die;
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.profit_and_loss.index', compact('title', 'restroList', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
     //   } else {
     //       Session::flash('warning', 'Invalid Request');
     //       return redirect()->back();
     //   }
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            return view('admin.chartofaccount.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_name' => 'required|max:255',
                'account_code' => 'required|unique:wa_charts_of_accounts',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {

                $row = new WaChartsOfAccount();
                $row->account_name = $request->account_name;
                $row->account_code = $request->account_code;
                $row->wa_account_group_id = $request->wa_account_group_id;
                $row->pl_or_bs = $request->pl_or_bs;

                $row->save();
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model . '.index');
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {
    }


    public function edit($slug)
    {
        try {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row =  WaChartsOfAccount::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.chartofaccount.edit', compact('title', 'model', 'breadcum', 'row'));
                } else {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
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


    public function update(Request $request, $slug)
    {
        try {

            $row =  WaChartsOfAccount::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'account_name' => 'required|max:255',
                'account_code' => 'required|unique:wa_charts_of_accounts,account_code,' . $row->id,

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {

                $row->account_name = $request->account_name;
                // $row->account_code= $request->account_code;
                $row->wa_account_group_id = $request->wa_account_group_id;
                $row->pl_or_bs = $request->pl_or_bs;
                $row->save();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.index');
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {

            WaChartsOfAccount::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
