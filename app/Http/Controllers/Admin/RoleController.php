<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Role;
use App\Model\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Model\UserPermission;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Facades\LogActivity;

class RoleController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'roles';
        $this->title = 'Roles';
        $this->pmodule = 'roles';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {

            $title = $this->title;
            $model = $this->model;
            $lists = Role::select([
                '*',
                DB::RAW('(select count(user_permissions.id) from user_permissions where role_id = roles.id) as permissioned')
            ])->whereNotIn('id', ['10', '11', '1', '100', '105'])->orderBy('id', 'DESC')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.roles.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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
            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            return view('admin.roles.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {

            $row = new Role();
            $row->title = $request->title;
            $row->is_hq_role = $request->is_hq_role == 'on';
            $row->save();
            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model . '.index');
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


                $row = Role::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = 'roles';
                    return view('admin.roles.edit', compact('title', 'model', 'breadcum', 'row'));
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
            $row = Role::whereSlug($slug)->first();
            $row->title = $request->title;
            $row->is_hq_role = $request->is_hq_role == 'on';
            $row->save();
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model . '.index');
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try {

            $role = Role::select([
                '*',
                DB::RAW('(select count(user_permissions.id) from user_permissions where role_id = roles.id) as permissioned')
            ])->whereSlug($slug)->having('permissioned', 0)->first();


            if ($role) {
                Role::whereSlug($slug)->delete();
                Session::flash('success', 'Deleted successfully.');
            }

            return redirect()->back();
        } catch (\Exception $e) {

            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getPermissions($slug)
    {
        try {
            $row = Role::whereSlug($slug)->first();
            if ($row) {
                $previous_permissions = [];
                $previousPermissions = [];
                $permissions = UserPermission::where('role_id', $row->id)->get();
                foreach ($permissions as $data) {
                    $previous_permissions[$data->module_name . '___' . $data->module_action] = $data->module_action;
                }

                $flattenedPermissions = [];
                $flattenedManagementDashboardPermissions = [];
                $flattenedAccountPayablesPermissions = [];
                $flattenedManagementDashboardPermissions = [];
                $flattenSystemPermissions = [];
                $flattenFleetManagementPermissions = [];
                $flattenGeneralLedgerPermissions = [];
                $flattenInventoryPermissions = [];
                $flattenPurchasesPermissions = [];
                $flattenSalesAndReceivablesPermissions = [];
                $flattenHRAndPayrollPermissions = [];
                $flattenAssetManagementPermissions = [];
                $flattenCommunicationsCentrePermissions = [];
                $flattenHelpDeskPermissions = [];
                $flattenSupplierPortalPermissions = [];
                $flattenDeliveryAndLogisticsPermissions = [];

                $permissionsManagementDashboardToShow = managementDashboardPermissionFunction();
                $permissionsAccountPayablesToShow = accountPayablesPermissionFunction();
                $permissionsSystemAdministrationShow = systemAdministrationPermissionFunction();
                $fleetManagementShow = fleetManagementPermissionFunction();
                $generalLedgerShow = generalLedgerPermissionFunction();
                $inventoryShow = inventoryPermissionFunction();
                $purchasesShow = purchasesPermissionFunction();
                $salesAndReceivablesShow = salesAndReceivablesPermissionFunction();
                $hrAndPayrollShow = hrAndPayrollPermissionFunction();
                $assetManagementShow = assetManagementPermissionFunction();
                $communicationsCentreShow = communicationsCentrePermissionFunction();
                $helpDeskShow = helpDeskPermissionFunction();
                $supplierPortalShow = supplierPortalPermissionFunction();
                $deliveryAndLogisticsShow = deliveryAndLogisticsPermissionFunction();

                flattenManagementDashboardPermissions($permissionsManagementDashboardToShow, $flattenedPermissions);
                flattenAccountPayablesPermissions($permissionsAccountPayablesToShow, $flattenedPermissions);
                flattenSystemPermissions($permissionsSystemAdministrationShow, $flattenedPermissions);
                flattenFleetManagementPermissions($fleetManagementShow, $flattenedPermissions);
                flattenGeneralLedgerPermissions($generalLedgerShow, $flattenedPermissions);
                flattenInventoryPermissions($inventoryShow, $flattenedPermissions);
                flattenPurchasesPermissions($purchasesShow, $flattenedPermissions);
                flattenSalesAndReceivablesPermissions($salesAndReceivablesShow, $flattenedPermissions);
                flattenHRAndPayrollPermissions($hrAndPayrollShow, $flattenedPermissions);
                flattenAssetManagementPermissions($assetManagementShow, $flattenedPermissions);
                flattenCommunicationsCentrePermissions($communicationsCentreShow, $flattenedPermissions);
                flattenHelpDeskPermissions($helpDeskShow, $flattenedPermissions);
                flattenSupplierPortalPermissions($supplierPortalShow, $flattenedPermissions);
                flattenDeliveryAndLogisticsPermissions($deliveryAndLogisticsShow, $flattenedPermissions);

                $title = 'Roles Permissions';
                $breadcum = [$this->title => route($this->model . '.index'), 'Permissions' => ''];
                $model = $this->model;
                $permisssion_array = setUpPermissions();
                return view('admin.roles.permissions', compact(
                    'title',
                    'model',
                    'breadcum',
                    'row',
                    'permisssion_array',
                    'previous_permissions',
                    'flattenedPermissions',
                    'flattenedManagementDashboardPermissions',
                    'flattenFleetManagementPermissions',
                    'flattenSystemPermissions',
                    'flattenInventoryPermissions',
                    'flattenPurchasesPermissions',
                    'flattenSalesAndReceivablesPermissions',
                    'flattenHRAndPayrollPermissions',
                    'flattenAssetManagementPermissions',
                    'flattenCommunicationsCentrePermissions',
                    'flattenHelpDeskPermissions',
                    'flattenSupplierPortalPermissions',
                    'flattenDeliveryAndLogisticsPermissions',
                    'flattenedManagementDashboardPermissions'
                ));
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

    public function setPermissions(Request $request, $slug)
    {
        DB::beginTransaction();
        try {
            $row = Role::whereSlug($slug)->first();
            if ($row) {
                $rows = [];
                $newLogs = [];
                foreach ($request->input() as $key => $data) {

                    $detail = explode('___', $key);
                    if (count($detail) == 2) {
                        // if ($detail[1] != 'view') {
                        //     $inner_array = [
                        //         'role_id' => $row->id,
                        //         'module_name' => $detail[0],
                        //         'module_action' => $detail[1]
                        //     ];
                        //     $rows[] = $inner_array;
                        //     $newLogs[]=[
                        //         'module_name' => $detail[0],
                        //         'module_action' => $detail[1]
                        //     ];
                            
                        // } else {
                        //     if ($data && $data != '') {
                        //         $inner_array = [
                        //             'role_id' => $row->id,
                        //             'module_name' => $detail[0],
                        //             'module_action' => $detail[1]
                        //         ];
                        //         $rows[] = $inner_array;
                        //         $newLogs[]=[
                        //             'module_name' => $detail[0],
                        //             'module_action' => $detail[1]
                        //         ];
                        //     }
                        // }
                        if ($data && $data != '') {
                            $inner_array = [
                                'role_id' => $row->id,
                                'module_name' => $detail[0],
                                'module_action' => $detail[1]
                            ];
                            $rows[] = $inner_array;
                            $newLogs[]=[
                                'module_name' => $detail[0],
                                'module_action' => $detail[1]
                            ];
                        }
                    }
                }
                $oldlogs = UserPermission::where('role_id', $row->id)->select('module_name','module_action')->get()->toArray();
                
                if (count($rows) > 0) {
                    UserPermission::where('role_id', $row->id)->delete();

                    UserPermission::insert($rows); // Eloquent approach
                    // Set ActivityLog
                    activity()
                    ->performedOn(new UserPermission())
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old' => json_encode($oldlogs), 
                        'attributes' => json_encode($newLogs), 
                    ])
                    ->log('update Permission');
                    Session::flash('success', 'Updated Successfully');
                } else{
                    Session::flash('warning', 'Permissions Not Updated. Please Try Again.');
                }
                
            } else {
                Session::flash('warning', 'Invalid Request');
            }
            DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }


    public function setPermissionsByUser(Request $request)
    {
        try {

            $user_id = base64_decode($request->user_id);
            $userData = User::findOrFail($user_id);

            $module_name_arr = ["sales-invoice", "confirm-invoice-r"];
            $module_action_arr = ["confirm-invoice-r", "view"];

            if ($request->is_checked == 1) {
                foreach ($module_name_arr as $key => $module_name) {
                    $savePer = new UserPermission();
                    $savePer->role_id = @$userData->userRole->id;
                    $savePer->user_id = @$user_id;
                    $savePer->module_name = @$module_name_arr[$key];
                    $savePer->module_action = @$module_action_arr[$key];
                    $savePer->save();
                }
                return response()->json(['result' => 1, 'message' => 'Permission have set successfully']);
            } elseif ($request->is_checked == 0) {
                UserPermission::where('role_id', $userData->userRole->id)->where('user_id', $user_id)->delete();
                return response()->json(['result' => 1, 'message' => 'Permission have removed successfully']);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['result' => -1, 'message' => $msg]);
        }
    }

    public function getAllRoles(): JsonResponse
    {
        $roles = Role::select(['id', 'slug', 'title'])->whereNotIn('id', [1])->get();
        return $this->jsonify(['data' => $roles], 200);
    }
}
