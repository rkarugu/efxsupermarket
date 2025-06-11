<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\WaUnitOfMeasure;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;


class UnitOfMeasureController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'unit-of-measures';
        $this->title = 'Bin Location';
        $this->pmodule = 'unit-of-measures';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaUnitOfMeasure::orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.unitofmeasure.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            // $userIds = WaUnitOfMeasure::pluck('chief_storekeeper')->toArray();
            $users = User::where('role_id', 152)->get();
            return view('admin.unitofmeasure.create', compact('title', 'model', 'breadcum', 'users'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }






    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row = new WaUnitOfMeasure();
                $row->title = $request->title;
                if ($request->is_display) {
                    $row->is_display = 1;
                }
                $row->chief_storekeeper = $request->chief_storekeeper;
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


    public function show($id) {}


    public function edit($slug)
    {
        try {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row =  WaUnitOfMeasure::whereSlug($slug)->first();
                if ($row) {
                    $users = User::where('role_id', 152)->get();

                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.unitofmeasure.edit', compact('title', 'model', 'breadcum', 'row', 'users'));
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
            $row =  WaUnitOfMeasure::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row->title = $request->title;
                if ($request->is_display) {
                    $row->is_display = 1;
                }else{
                    $row->is_display = 0;
                }
                $row->chief_storekeeper = $request->chief_storekeeper;

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


    public function destroy($slug)
    {
        try {
            $row = WaUnitOfMeasure::whereSlug($slug)->first();
            if ($row->get_uom_linked->count() > 0) {
                throw new Exception("Error Processing Request", 1);
            }
            $row->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function dropdown_search(Request $request)
    {
        $l = '';
        if ($request->id) {
            $l = "AND location_id != " . $request->id;
        }
        return WaUnitOfMeasure::where(function ($e) use ($request) {
            if ($request->search) {
                $e->where('title', 'LIKE', "%$request->search%");
            }
        })
            ->having(DB::RAW("(select count(*) from wa_location_store_uom where uom_id = wa_unit_of_measures.id " . $l . ")"), '=', '0')
            ->get();
    }

    public function search_by_item_location(Request $request)
    {
        if ($request->item) {
            $data = WaUnitOfMeasure::select('title', 'wa_unit_of_measures.id')->where(function ($e) use ($request) {
                if ($request->search) {
                    $e->where('title', 'LIKE', "%$request->search%");
                }
            })->join('wa_inventory_location_uom', function ($e) {
                $e->on('wa_inventory_location_uom.uom_id', '=', 'wa_unit_of_measures.id');
            })->where(function ($e) use ($request) {
                if ($request->id) {
                    $e->where('wa_inventory_location_uom.location_id', $request->id);
                }
                $e->where('wa_inventory_location_uom.inventory_id', $request->item);
            })
                ->get();
            if (count($data) > 0) {
                return $data;
            }
        }
        return WaUnitOfMeasure::select('title', 'wa_unit_of_measures.id')->where(function ($e) use ($request) {
            if ($request->search) {
                $e->where('title', 'LIKE', "%$request->search%");
            }
        })->join('wa_location_store_uom', function ($e) {
            $e->on('wa_location_store_uom.uom_id', '=', 'wa_unit_of_measures.id');
        })->where(function ($e) use ($request) {
            if ($request->id) {
                $e->where('wa_location_store_uom.location_id', $request->id);
            }
        })
            ->get();
    }
}
