<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\DeliveryCentres;
use App\Model\Route;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class RouteManagerController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'route-manager';
        $this->title = 'Route Manager';
        $this->pmodule = 'route-manager';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = Route::orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.route_manager.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.route_manager.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_name' => 'required|unique:routes,route_name|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'result' => 0]);
        }
        $route = new Route;
        $route->route_name = $request->route_name;
        $route->save();

        $message = 'Route Created successfully';
        if (!$request->ajax()) {
            return redirect()->route($this->model . '.index')->with('success', $message);
        }

        return response()->json(['message' => $message, 'result' => 1, 'location' => route($this->model . '.index')]);
    }

    public function edit($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $route = Route::findOrFail($id);
            return view('admin.route_manager.edit', compact('route', 'title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'route_name' => 'required|unique:routes,route_name,' . $id . '|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'result' => 0]);
        }
        try {
            //code...
            $route = Route::findOrFail($id);
            $route->route_name = $request->route_name;
            $route->save();
            return response()->json(['message' => 'Route Updated successfully', 'result' => 1, 'location' => route($this->model . '.index')]);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong', 'result' => -1]);
        }
    }
}
