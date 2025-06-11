<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaSubAccountSection;

use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Facades\Validator;

class SubAccountSectionController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'sub-account-sections';
        $this->title = 'Account Sub Sections';
        $this->pmodule = 'sub-account-sections';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaSubAccountSection::orderBy('id', 'asc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.subaccountsections.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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
            return view('admin.subaccountsections.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|max:255',
                'section_code' => 'required|max:255',
                'wa_account_group_id' => 'required',
                'wa_account_section_id' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {

                $row = new WaSubAccountSection();
                $row->section_name = strtoupper($request->section_name);
                $row->wa_account_section_id = $request->wa_account_section_id;
                $row->section_code = $request->section_code;
                // $row->profit_and_loss= $request->profit_and_loss;
                // $row->sequence_in_tb= $request->sequence_in_tb;
                $row->wa_account_group_id = $request->wa_account_group_id ? $request->wa_account_group_id : null;
                // $row->is_parent= $request->parent_id?'0':'1';
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


    public function show($id)
    {

    }


    public function edit($slug)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = WaSubAccountSection::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.subaccountsections.edit', compact('title', 'model', 'breadcum', 'row'));
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
            $row = WaSubAccountSection::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|max:255',
                'section_code' => 'required|max:255',
                'wa_account_group_id' => 'required',
                'wa_account_section_id' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $row->section_name = strtoupper($request->section_name);
                $row->wa_account_section_id = $request->wa_account_section_id;
                $row->section_code = $request->section_code;
                // $row->profit_and_loss= $request->profit_and_loss;
                // $row->sequence_in_tb= $request->sequence_in_tb;
                $row->wa_account_group_id = $request->wa_account_group_id ? $request->wa_account_group_id : null;
                // $row->is_parent= $request->parent_id?'0':'1';
                $row->save();
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

            WaSubAccountSection::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function account_section_search(Request $request)
    {
        $data = DB::table('wa_account_groups')->select(['id as id', 'group_name as text']);
        if ($request->q) {
            $data = $data->where('group_name', 'LIKE', "%$request->q%");
        }
        if ($request->id) {
            $data = $data->where('wa_account_section_id', $request->id);
        }
        $data = $data->get();
        return $data;
    }

    public function get_account_detail(Request $request)
    {
        $data = WaSubAccountSection::with(['getParentAccountGroup'])->where('id', $request->id)->first();
        return [
            'group' => [
                'id' => $data->getParentAccountGroup->id,
                'name' => $data->getParentAccountGroup->group_name
            ],
            'section' => [
                'id' => $data->getAccountSection->id,
                'name' => $data->getAccountSection->section_name
            ]
        ];
    }

}
