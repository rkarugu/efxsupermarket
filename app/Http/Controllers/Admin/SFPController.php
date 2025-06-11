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

class SFPController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'balance-sheet';
        $this->title = 'Balance Sheet';
        $this->pmodule = 'balance-sheet';
    }
    public function index()
    {
        $lists['permission'] =  $this->mypermissionsforAModule();
        $lists['pmodule'] = $this->pmodule;
        $lists['title'] = $this->title;
        $lists['model'] = $this->model;


			$ASSESTSlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','!=','NON CURRENT ASSETS');
            })
			->orderBy('section_name', 'ASC')
			->whereIn('section_name', ['FIXED ASSESTS','CURRENT ASSESTS','INVENTORY'])
			->get();

            $NONASSESTSlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','NON CURRENT ASSETS');
            })
			->orderBy('section_name', 'ASC')
			->whereIn('section_name', ['FIXED ASSESTS','CURRENT ASSESTS','INVENTORY'])
			->get();

			$LIABILITIESlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['CURRENT LIABILITIES','LONG TERM LIABILITIES'])
			->get();
            $nonLIABILITIESlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','NON CURRENT LIABILITIES');
            })
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['NON CURRENT LIABILITIES'])
			->get();

			$EQUITYlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['EQUITY'])
			->get();
                

            $lists['ASSETS'] = $ASSESTSlists;
            $lists['LIABILITIES'] = $LIABILITIESlists;
            $lists['EQUITY'] = $EQUITYlists;
            $lists['NONCURRENTASSESTS'] = $NONASSESTSlists;
            $lists['NONCURRENTLIABILITIES'] = $nonLIABILITIESlists;
                
            $lists['restroList'] = $this->getRestaurantList();
/*
			foreach($lists as $key=> $val){
            echo $key."<br />"; //print_r($val[0]); die;
			}
*/
//			die;
            $lists['breadcum']= [$lists['title'] => '', 'Listing' => ''];
            return view('admin.balance_sheet.sfp')->with($lists);
     //   } else {
     //       Session::flash('warning', 'Invalid Request');
     //       return redirect()->back();
     //   }
    }

    public function excel()
    {
        $ASSESTSlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','!=','NON CURRENT ASSETS');
            })
			->orderBy('section_name', 'ASC')
			->whereIn('section_name', ['FIXED ASSESTS','CURRENT ASSESTS','INVENTORY'])
			->get();

            $NONASSESTSlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','NON CURRENT ASSETS');
            })
			->orderBy('section_name', 'ASC')
			->get();

			$LIABILITIESlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['CURRENT LIABILITIES','LONG TERM LIABILITIES'])
			->get();
            $nonLIABILITIESlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
            ->whereHas('getWaAccountGroup',function($r){
                $r->where('group_name','NON CURRENT LIABILITIES');
            })
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['NON CURRENT LIABILITIES'])
			->get();

			$EQUITYlists = WaAccountSection::with('getWaAccountGroup', 'getWaAccountGroup.getChartAccount')
			->orderBy('id', 'ASC')
			->whereIn('section_name', ['EQUITY'])
			->get();
                

            $lists['ASSETS'] = $ASSESTSlists;
            $lists['LIABILITIES'] = $LIABILITIESlists;
            $lists['EQUITY'] = $EQUITYlists;
            $lists['NONCURRENTASSESTS'] = $NONASSESTSlists;
            $lists['NONCURRENTLIABILITIES'] = $nonLIABILITIESlists;
        return \Excel::create('statement-of-financial-position-'.date('Y-m-d-H-i-s'), function($excel) use ($lists) {
            $excel->sheet('mySheet', function($sheet) use ($lists)
            {                
                $sheet->loadView('admin.balance_sheet.sfpexcel')->with($lists);    
                // $sheet->fromArray($arrays);
            });
        })->export('xlsx'); 
    }
}
