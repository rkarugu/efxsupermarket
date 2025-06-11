<?php
namespace App\Http\Controllers\Admin;

use App\Model\ReportShop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AdminReportShopController extends Controller
{
    //

    protected $model;
    protected $title;
     protected $pmodule;
    public function __construct()
    {
        $this->model = 'reported-shops';
        $this->title = 'Reported Shops';
        $this->pmodule = 'reported-shops';
    } 


    public function index()
    {
        //

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $reports = ReportShop::with('reason')->with('shop')->get();
        


        return view('admin.reported_shops.index', compact('title','reports','model','pmodule'));
    }
}
