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

class ProfitLossDetailController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'profit-and-loss';
        $this->title = 'Profit & Loss';
        $this->pmodule = 'profit-and-loss';
        ini_set('memory_limit', '100000M');
        ini_set('max_execution_time', 180); //3 minutes
        set_time_limit(300000000); // Extends to 5 minutes.
    }

    public function index(Request $request)
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
            ->whereIn('section_name', ['INCOME', 'COST OF SALES', 'OVERHEADS'])
            ->orderBy('section_number', 'ASC')
            ->get();
        $restroList = $this->getRestaurantList();

        //  echo "<pre>"; dd($lists); die;
        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        return view('admin.profit_and_loss.detailindex', compact('title', 'restroList', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        //   } else {
        //       Session::flash('warning', 'Invalid Request');
        //       return redirect()->back();
        //   }
    }

    public function excel(Request $request)
    {
        $lists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            // ->whereHas('getWaAccountGroup', function ($sql_query) {
                //     $sql_query->where('profit_and_loss', 'Y');
                // })
                ->whereIn('section_name', ['INCOME', 'COST OF SALES', 'OVERHEADS'])
                ->orderBy('section_number', 'ASC')
                ->get();
               
        return \Excel::create('profit-loss-details-'.date('Y-m-d-H-i-s'), function($excel) use ($lists) {
            $excel->sheet('mySheet', function($sheet) use ($lists)
            {                
                $sheet->loadView('admin.profit_and_loss.profitlossDetailsexcel')->with(['lists'=>$lists]);    
                // $sheet->fromArray($arrays);
            });
        })->export('xlsx');      
    }
}
