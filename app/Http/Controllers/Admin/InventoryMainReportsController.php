<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InventoryMainReportsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'inventory-reports';
        $this->title = 'Inventory Reports';
        $this->pmodule = 'inventory-reports';
    }

    public function index()
    {
        try {
            $model = $this->model;
            $title = $this->title;
            $pmodule = $this->pmodule;
            $sidebartitle = 'Inventory';

            $permission =  $this->mypermissionsforAModule();
            $mainpermission = 'inventory-reports___view';

            if (isset($permission[$model . '___view']) || $permission == 'superadmin') {
                $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
                $reports = Module::where('module_title', $sidebartitle)->with('modulereportcategories.modulereports')->firstOrFail();
                return view('admin.purchases_reports_list.purchases_reports', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'mainpermission', 'sidebartitle', 'reports'));
            
            } else {
                throw new \Exception("Invalid Request");
            }
        } catch (\Throwable $th) {
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
        
    }
}
