<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Loader;
class LoaderController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct(Request $request)
    {
        $this->model = 'loaders';
        $this->title = 'Loaders';
        $this->pmodule = 'employees';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $loaders = Loader::with('branches')->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('loaders.index'), 'Listing' => ''];
            return view('admin.loaders.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'loaders'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create(){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('loaders.index'), 'Listing' => ''];
            return view('admin.loaders.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function store(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        try{
        $loader = new Loader();
        $loader->name = $request->name;
        $loader->id_number = $request->id_number;
        $loader->phone_number = $request->phone_number;
        $loader->restaurant_id = $request->restaurant_id;

        $loader->save();
        return redirect()->route("loaders.index")->with('success', 'Loader Created successfully' );


        }catch(\Throwable $e){
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
}

public function edit($id){
    $permission = $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;
    $loader = Loader::find($id);
    if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
        $breadcum = [$title => route('loaders.index'), 'Listing' => ''];
        return view('admin.loaders.edit', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'loader'));
    } else {
        Session::flash('warning', 'Invalid Request');
        return redirect()->back();
    }

}
public function update(Request $request, $id)
{
    $permission = $this->mypermissionsforAModule();
    $pmodule = $this->pmodule;
    $title = $this->title;
    $model = $this->model;

    try{
    $loader =  Loader::find($id);
    $loader->name = $request->name;
    $loader->id_number = $request->id_number;
    $loader->phone_number = $request->phone_number;
    $loader->restaurant_id = $request->restaurant_id;

    $loader->save();
    return redirect()->route("loaders.index")->with('success', 'Loader Updated successfully' );


    }catch(\Throwable $e){
        return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

    }
}
}
