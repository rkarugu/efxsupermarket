<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaDepartment;
use App\Model\User;
use DB;
use Session;

use App\Model\WaDepartmentsAuthorizationRelations;
use App\Model\WaDepartmentExternalAuthorization;
use App\Model\WaPurchaseOrderAuthorization;

use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'departments';
        $this->title = 'Departments';
        $this->pmodule = 'departments';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaDepartment::orderBy('department_name')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.departments.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add Department';
            $model = $this->model;
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];

            return view('admin.departments.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        try {
            // dd($request);
            $validator = Validator::make($request->all(), [
                'department_name' => 'required|max:255',
                'department_code' => 'required|unique:wa_departments',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $is_addable = true;

                if ($request->authorization_user_id && count($request->authorization_user_id) > 0) {
                    $all_authorizer_level = User::select('authorization_level')->whereIn('id', $request->authorization_user_id)->pluck('authorization_level')->toArray();
                    $all_authorizer_level = array_unique($all_authorizer_level);
                    if (count($request->authorization_user_id) != count($all_authorizer_level)) {
                        $is_addable = false;
                    }
                }

                if ($request->external_authorization_user_id && count($request->external_authorization_user_id) > 0) {
                    $all_external_authorizer_level = User::select('external_authorization_level')->whereIn('id', $request->external_authorization_user_id)->pluck('external_authorization_level')->toArray();
                    $all_external_authorizer_level = array_unique($all_external_authorizer_level);
                    if (count($request->external_authorization_user_id) != count($all_external_authorizer_level)) {
                        $is_addable = false;
                    }
                }

                if ($request->purchase_order_authorization_user_id && count($request->purchase_order_authorization_user_id) > 0) {
                    $all_purchase_authorizer_level = User::select('purchase_order_authorization_level')->whereIn('id', $request->purchase_order_authorization_user_id)->pluck('purchase_order_authorization_level')->toArray();
                    $all_purchase_authorizer_level = array_unique($all_purchase_authorizer_level);
                    if (count($request->purchase_order_authorization_user_id) != count($all_purchase_authorizer_level)) {
                        $is_addable = false;
                    }
                }

                if ($is_addable == true) {
                    $row = new WaDepartment();
                    $row->department_name = $request->department_name;
                    $row->department_code = $request->department_code;
                    $row->restaurant_id = $request->restaurant_id;
                    $row->save();

                    if ($request->authorization_user_id && count($request->authorization_user_id) > 0) {
                        foreach ($request->authorization_user_id as $user_id) {
                            WaDepartmentsAuthorizationRelations::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }

                    if ($request->external_authorization_user_id && count($request->external_authorization_user_id) > 0) {
                        foreach ($request->external_authorization_user_id as $user_id) {
                            WaDepartmentExternalAuthorization::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }

                    if ($request->purchase_order_authorization_user_id && count($request->purchase_order_authorization_user_id) > 0) {
                        foreach ($request->purchase_order_authorization_user_id as $user_id) {
                            WaPurchaseOrderAuthorization::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }


                    Session::flash('success', 'Record added successfully.');
                    return redirect()->route($this->model . '.index');
                } else {
                    Session::flash('warning', 'Can not select multitple authorization for same level .');
                    return redirect()->back()->withInput();
                }


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
                $row = WaDepartment::whereSlug($slug)->first();
                if ($row) {

                    $title = 'Edit Department';
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;

                    return view('admin.departments.edit', compact('title', 'model', 'breadcum', 'row'));
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
            $row = WaDepartment::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'department_name' => 'required|max:255',
                'department_code' => 'required|unique:wa_departments,department_code,' . $row->id,

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $is_addable = true;

                if ($request->authorization_user_id && count($request->authorization_user_id) > 0) {
                    $all_authorizer_level = User::select('authorization_level')->whereIn('id', $request->authorization_user_id)->pluck('authorization_level')->toArray();
                    $all_authorizer_level = array_unique($all_authorizer_level);
                    if (count($request->authorization_user_id) != count($all_authorizer_level)) {
                        $is_addable = false;
                    }
                }


                if ($request->external_authorization_user_id && count($request->external_authorization_user_id) > 0) {
                    $all_external_authorizer_level = User::select('external_authorization_level')->whereIn('id', $request->external_authorization_user_id)->pluck('external_authorization_level')->toArray();
                    $all_external_authorizer_level = array_unique($all_external_authorizer_level);
                    if (count($request->external_authorization_user_id) != count($all_external_authorizer_level)) {
                        $is_addable = false;
                    }
                }

                if ($request->purchase_order_authorization_user_id && count($request->purchase_order_authorization_user_id) > 0) {
                    $all_purchase_authorizer_level = User::select('purchase_order_authorization_level')->whereIn('id', $request->purchase_order_authorization_user_id)->pluck('purchase_order_authorization_level')->toArray();
                    $all_purchase_authorizer_level = array_unique($all_purchase_authorizer_level);
                    if (count($request->purchase_order_authorization_user_id) != count($all_purchase_authorizer_level)) {
                        $is_addable = false;
                    }
                }


                if ($is_addable == true) {

                    $row->department_name = $request->department_name;
                    // $row->department_code= $request->department_code;
                    $row->restaurant_id = $request->restaurant_id;
                    $row->save();

                    WaDepartmentsAuthorizationRelations::where('wa_department_id', $row->id)->delete();
                    WaDepartmentExternalAuthorization::where('wa_department_id', $row->id)->delete();
                    WaPurchaseOrderAuthorization::where('wa_department_id', $row->id)->delete();


                    if ($request->authorization_user_id && count($request->authorization_user_id) > 0) {
                        foreach ($request->authorization_user_id as $user_id) {
                            WaDepartmentsAuthorizationRelations::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }

                    if ($request->external_authorization_user_id && count($request->external_authorization_user_id) > 0) {
                        foreach ($request->external_authorization_user_id as $user_id) {
                            WaDepartmentExternalAuthorization::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }

                    if ($request->purchase_order_authorization_user_id && count($request->purchase_order_authorization_user_id) > 0) {
                        foreach ($request->purchase_order_authorization_user_id as $user_id) {
                            WaPurchaseOrderAuthorization::updateOrCreate(
                                ['user_id' => $user_id, 'wa_department_id' => $row->id]
                            );
                        }
                    }


                    Session::flash('success', 'Record updated successfully.');
                    return redirect()->route($this->model . '.index');
                } else {
                    Session::flash('warning', 'Can not select multitple authorization for same level .');
                    return redirect()->back()->withInput();
                }


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

            $row = WaDepartment::whereSlug($slug)->first();
            $user = User::where('wa_department_id', $row->id)->update(['wa_department_id' => null]);
            WaDepartment::whereSlug($slug)->delete();

            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getAllDepartments(): JsonResponse
    {
        try {
            $departments = WaDepartment::distinct('department_name')->get();
            return $this->jsonify(['data' => $departments], 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }

    public function departmentByBranch($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant not found'
            ], 404);
        }

        $departments = WaDepartment::select('id', 'department_name')
            ->where('restaurant_id', $restaurantId)
            ->get();

        return response()->json($departments);
    }
}
