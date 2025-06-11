<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WeightedAverageHistory;
use PDF;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class WeightedAverageHistoryController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'weighted-average-history';
        $this->title = 'Weighted Averages';
        $this->pmodule = 'weighted-average-history';
    }

    public function index(Request $request){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WeightedAverageHistory::where('status', 1)
            ->where(function($e) use ($request){
                if($request->from && $request->to){
                    $e->whereBetween('date',[$request->from,$request->to]);
                }else{
                    $e->whereDate('date','>=',date('Y-m-d',strtotime('-7 Days')));
                }
            })
            ->where(function($e) use ($request){
                if($request->show != ''){
                    $e->where('opening_standard_cost','!=',DB::RAW('grn_standard_cost'));
                }
            })
            ->orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.'.$this->model.'.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    
}
