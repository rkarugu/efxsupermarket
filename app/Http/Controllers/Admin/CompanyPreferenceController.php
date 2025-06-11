<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaCompanyPreference as CompanyPreference;

use DB;
use Session;
use Illuminate\Support\Facades\Validator;

class CompanyPreferenceController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'company-preferences';
        $this->title = 'Company Preferences';
        $this->pmodule = 'company-preferences';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = CompanyPreference::orderBy('id', 'DESC')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.companypreference.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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
            return view('admin.companypreference.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email_address' => 'required|email',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row = new CompanyPreference();
                $row->name = strtoupper($request->name);
                $row->official_company_number = $request->official_company_number;
                $row->tax_authority_reference = $request->tax_authority_reference;
                $row->address = $request->address;
                $row->telephone_number = $request->telephone_number;
                $row->facsimile_number = $request->facsimile_number;
                $row->email_address = $request->email_address;
                $row->home_currency = $request->home_currency;
                $row->debtors_control_gl_account = $request->debtors_control_gl_account;
                $row->creditors_control_gl_account = $request->creditors_control_gl_account;
                $row->payroll_net_pay_clearing_gl_account = $request->payroll_net_pay_clearing_gl_account;
                $row->goods_received_clearing_gl_account = $request->goods_received_clearing_gl_account;
                $row->retained_earning_clearing_gl_account = $request->retained_earning_clearing_gl_account;
                $row->freight_recharged_gl_account = $request->freight_recharged_gl_account;
                $row->sales_exchange_variances_gl_account = $request->sales_exchange_variances_gl_account;
                $row->purchases_exchange_variances_gl_account = $request->purchases_exchange_variances_gl_account;
                $row->payment_discount_gl_account = $request->payment_discount_gl_account;

                $row->cash_sales_control_account = $request->cash_sales_control_account;
                $row->sales_control_account = $request->sales_control_account;
                $row->vat_control_account = $request->vat_control_account;
                $row->discount_recieved_gl_account = $request->discount_recieved_gl_account;
                $row->withholding_vat_gl_account = $request->withholding_vat_gl_account;

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
                $row =  CompanyPreference::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.companypreference.edit', compact('title', 'model', 'breadcum', 'row'));
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
            $row =  CompanyPreference::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email_address' => 'required|email',

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $row->name = strtoupper($request->name);
                $row->official_company_number = $request->official_company_number;
                $row->tax_authority_reference = $request->tax_authority_reference;
                $row->address = $request->address;
                $row->telephone_number = $request->telephone_number;
                $row->facsimile_number = $request->facsimile_number;
                $row->email_address = $request->email_address;
                $row->home_currency = $request->home_currency;
                $row->debtors_control_gl_account = $request->debtors_control_gl_account;
                $row->creditors_control_gl_account = $request->creditors_control_gl_account;
                $row->payroll_net_pay_clearing_gl_account = $request->payroll_net_pay_clearing_gl_account;
                $row->goods_received_clearing_gl_account = $request->goods_received_clearing_gl_account;
                $row->retained_earning_clearing_gl_account = $request->retained_earning_clearing_gl_account;
                $row->freight_recharged_gl_account = $request->freight_recharged_gl_account;
                $row->sales_exchange_variances_gl_account = $request->sales_exchange_variances_gl_account;
                $row->purchases_exchange_variances_gl_account = $request->purchases_exchange_variances_gl_account;
                $row->payment_discount_gl_account = $request->payment_discount_gl_account;

                $row->cash_sales_control_account = $request->cash_sales_control_account;
                $row->sales_control_account = $request->sales_control_account;
                $row->vat_control_account = $request->vat_control_account;
                $row->discount_recieved_gl_account = $request->discount_recieved_gl_account;
                $row->withholding_vat_gl_account = $request->withholding_vat_gl_account;

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

            CompanyPreference::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
}
