<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Reports\SupplierBankListing;
use App\Http\Controllers\Controller;
use App\Imports\SupplierAccountsDataImport;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierBankListingController extends Controller
{
    protected $model;
    protected $title;

    public function __construct()
    {
        $this->model = 'supplier-bank-listing';
        $this->title = 'Supplier Bank Listing';
    }


    public function index(Request $request)
    {
        // revert and update permissions

        if (!can('view', 'supplier-bank-listing')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $breadcum = [
            'Accounts Payables' => '',
            'Reports' => '',
            $this->title => ''
        ];

        if ($request->wantsJson() || $request->filled('action')) {

            $query = WaSupplier::query()
                ->whereNotNull('supplier_code');

            if ($request->filled('action')) {
                $export = new SupplierBankListing($query->get());

                return Excel::download($export, 'supplier_bank_listing' . date('Y-m-d-H-i-s') . '.xlsx');
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('tax_withhold', function ($supplier) {
                    return $supplier->tax_withhold ? "Yes" : "No";
                })
                ->toJson();
        }

        return view('admin.reports.supplier_bank_listing.index', [
            'title' => $this->title,
            'model' => $this->model,
            'breadcum' => $breadcum
        ]);
    }

    public function update(Request $request)
    {
        $this->validate($request, ['file' => 'required|file']);

        Excel::import(new SupplierAccountsDataImport, request()->file('file'));

        Session::flash('success', 'Data processed successfully');

        return redirect()->back();
    }
}
