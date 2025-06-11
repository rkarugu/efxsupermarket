<?php

namespace App\Http\Controllers\Admin;

use App\Model\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaLocationAndStore;
use App\Model\Route;
use App\Model\WaStoreLocationUom;
use App\Model\WaUnitOfMeasure;
use App\Models\WaLocationStoreUom;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class LocationAndStoreController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'location-and-stores';
        $this->title = 'Location And Stores';
        $this->pmodule = 'location-and-stores';
    }

    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaLocationAndStore::with('getBranchDetail')->orderBy('location_name')->paginate(20);
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.locationandstores.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $restroList = $this->getRestaurantList();
            $route = Route::all()->filter(function (Route $route) {
                return (!$route->store()) || ($route->route_name == 'Cash Sales');
            })->pluck('route_name', 'id');

            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            return view('admin.locationandstores.create', compact('route', 'title', 'model', 'breadcum', 'restroList'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'location_name' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $branch = Restaurant::find($request->wa_branch_id);
                $prefix = $branch != null ? $branch->branch_code: "KHEL";
                $newStoreCode = $prefix . str_pad(WaLocationAndStore::count() + 1, 3, '0', STR_PAD_LEFT);

                $row = new WaLocationAndStore();
                $row->location_code = $newStoreCode;
                $row->location_name = $request->location_name;
                $row->biller_no = $request->biller_no ?? NULL;
                $row->account_no = $request->account_no ?? NULL;
                $row->wa_branch_id = $request->wa_branch_id;
                $row->route_id = $request->route_id;
                $row->is_cost_centre = ($request->is_cost_centre) ? '1' : '0';
                $row->is_physical_store = ($request->is_physical_store) ? '1' : '0';
                $row->save();
                if($request->bin_locations && count($request->bin_locations)>0){
                    foreach ($request->bin_locations as $key => $category) {
                        $sub = new WaStoreLocationUom();
                        $sub->location_id = $row->id;
                        $sub->uom_id = $category;
                        $sub->created_at = date('Y-m-d H:i:s');
                        $sub->updated_at = date('Y-m-d H:i:s');
                        $sub->save();
                    }
                }
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
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = WaLocationAndStore::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    $restroList = $this->getRestaurantList();

                    $route = Route::all()->filter(function (Route $route) use ($row) {
                        return (!$route->store()) || ($route->slug == 'cash-sales') || ($route->id == $row->route_id);
                    })->pluck('route_name', 'id');

                    $debt = $row->credit_limit - \App\Model\WaDebtorTran::where('salesman_id', $row->id)->sum('amount');

                    return view('admin.locationandstores.edit', compact('route', 'title', 'model', 'breadcum', 'row', 'restroList', 'debt'));
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
            $row = WaLocationAndStore::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'location_code' => 'required|unique:wa_location_and_stores,location_code,' . $row->id,
                // 'credit_limit'=>'required|numeric|min:0|max:9999999999'               
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {

                $row->location_code = $request->location_code;
                $row->location_name = $request->location_name;
                // $row->credit_limit= $request->credit_limit;
                $row->biller_no = $request->biller_no ?? NULL;
                $row->account_no = $request->account_no ?? NULL;
                $row->is_physical_store = ($request->is_physical_store) ? '1' : '0';

                $row->wa_branch_id = $request->wa_branch_id;
                $row->route_id = $request->route_id;
                $row->is_cost_centre = ($request->is_cost_centre) ? '1' : '0';
                $row->save();
                WaStoreLocationUom::where('location_id',$row->id)->delete();
                if($request->bin_locations && count($request->bin_locations)>0){
                    foreach ($request->bin_locations as $key => $category) {
                        $sub = new WaStoreLocationUom();
                        $sub->location_id = $row->id;
                        $sub->uom_id = $category;
                        $sub->created_at = date('Y-m-d H:i:s');
                        $sub->updated_at = date('Y-m-d H:i:s');
                        $sub->save();
                    }
                }
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
            $row = WaLocationAndStore::whereSlug($slug)->first();
            if (!$row || count($row->stock_moves) > 0) {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            $row->delete();
            // WaLocationAndStore::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getLocationsByBrach(Request $request)
    {
        $rows = WaLocationAndStore::where('wa_branch_id', $request->branch_id)->where('is_cost_centre', 1)->orderBy('location_name', 'asc')->with('bin_locations')->get();
        $data = '<option  value="">Please select location</option>';
        foreach ($rows as $row) {
            $data .= '<option  value="' . $row->id . '">' . $row->location_name . ' (' . $row->location_code . ')' . '</option>';
        }

        return $data;
    }

    public function getBinsByLocation(Request $request)
    {
        $location_uoms = WaLocationStoreUom::where('location_id', $request->location_id)->pluck('uom_id');
        $rows = WaUnitOfMeasure::whereIn('id', $location_uoms)->get();
        return response()->json($rows);
    }

}
