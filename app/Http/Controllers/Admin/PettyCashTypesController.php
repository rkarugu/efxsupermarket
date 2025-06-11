<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PettyCashType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PettyCashTypesController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'petty-cash-type';
        $this->title = 'Petty Cash';
        $this->pmodule = 'petty-cash';
    }

    public function index(): View|RedirectResponse
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $title = $this->title;
            $model = $this->model;
            $lists = PettyCashType::all();
            $breadcum = [$title => route('petty-cash-types.index'), 'Listing' => ''];
            return view('admin.petty_cash.types.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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
            $breadcum = [$this->title => route('petty-cash-types.index'), 'Add' => ''];
            return view('admin.petty_cash.types.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        try {
            $row = new PettyCashType();
            $row->title = $request->title;
            $row->wa_chart_of_accounts_id = $request->gl_account;
            $row->slug = Str::slug($request->title);

            $row->save();
            Session::flash('success', 'Record added successfully.');
            return redirect()->route('petty-cash-types.index');
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {

    }


    public function edit($id)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = PettyCashType::find($id);
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route('petty-cash-types.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.petty_cash.types.edit', compact('title', 'model', 'breadcum', 'row'));
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


    public function update(Request $request, $id)
    {
        try {
            $row = PettyCashType::find($id);
            $row->title = $request->title;
            $row->wa_chart_of_accounts_id = $request->gl_account;
            $row->slug = Str::slug($request->title);

            $row->save();

            Session::flash('success', 'Record updated successfully.');
            return redirect()->route('petty-cash-types.index');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function destroy($id)
    {
        try {
            PettyCashType::find($id)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {

            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


}
