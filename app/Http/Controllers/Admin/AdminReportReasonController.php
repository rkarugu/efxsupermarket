<?php

namespace App\Http\Controllers\Admin;

use App\Model\ReportReason;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Session;

class AdminReportReasonController extends Controller
{
    //

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'report_reasons';
        $this->title = 'Report Reasons';
        $this->pmodule = 'report_reasons';
    }


    public function index()
    {
        //

        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        $reasons = ReportReason::all();




        return view('admin.report_reasons.index', compact('title', 'reasons', 'model', 'pmodule'));
    }

    public function create(Request $request)
    {


        $title = $this->title;
        $model = $this->model;

        return view('admin.report_reasons.create', compact('title', 'model'));

    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {

                $row = new ReportReason();
                $row->name = $request->name;
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


    public function edit(Request $request, $editID)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $row = ReportReason::where('id', $editID)->first();
        return view('admin.report_reasons.edit', compact('title', 'model', 'row'));
    }


    public function update(Request $request, $updata)
    {
        try {
            $upDate = ReportReason::where('id', $updata)->first();
            $validator = Validator::make($request->all(), [
                'name' => 'required',

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $upDate->name = $request->name;
                $upDate->save();
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
            ReportReason::where('id', $slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}