<?php

namespace App\Http\Controllers\Admin;

use App\Model\RouteReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminReportRouteController extends Controller
{
    //

    //

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'reported-routes';
        $this->title = 'Reported Routes';
        $this->pmodule = 'reported-routes';
    }


    public function index()
    {
        //

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $reports = RouteReport::with('reason')->with('route')->get();



        return view('admin.reported_routes.index', compact('title', 'reports', 'model', 'pmodule'));
    }
}