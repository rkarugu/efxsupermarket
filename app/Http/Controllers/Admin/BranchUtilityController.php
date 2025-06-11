<?php

namespace App\Http\Controllers\Admin;

use App\Model\PackSize;
use App\Model\TaxManager;
use App\Model\WaSupplier;
use Illuminate\Http\Request;
use App\Model\WaUnitOfMeasure;
use App\Model\ItemSubCategories;
use App\Model\WaLocationAndStore;
use App\Model\WaInventoryCategory;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\Session;

class BranchUtilityController extends Controller
{

    protected $model;
    protected $pmodel;
    protected $title;
    protected $permission;

    public function __construct()
    {
        $this->model = 'download-branch-utilities';
        $this->pmodel = 'utility';
        $this->title = 'Branch Utilities';
        $this->permission = 'branch-utilities';
    }

    public function index()
    {
        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Branch Utilities' => ''];

        if (!can($this->permission, $this->pmodel)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        return view('admin.utility.branch_utility.branch_utility', compact('title', 'model', 'breadcum'));
    }

    public function downloadExcels(Request $request)
    {
        $data = [];
        $headings = ['ID', 'TITLE'];
        $filename = '';

        if ($request->has('category')) {
            $data = WaInventoryCategory::select('id', 'category_description as name')->get();
            $filename = 'inventory_category.xlsx';
        } elseif ($request->has('subcategory')) {
            $data = ItemSubCategories::select('id', 'description as name')->get();
            $filename = 'inventory_sub_category.xlsx';
        } elseif ($request->has('suppliers')) {
            $data = WaSupplier::select('id', 'name')->get();
            $filename = 'inventory_suppliers.xlsx';
        } elseif ($request->has('taxcategory')) {
            $data = TaxManager::select('id', 'title as name')->get();
            $filename = 'tax_category.xlsx';
        } elseif ($request->has('packsize')) {
            $data = PackSize::select('id', 'title as name')->get();
            $filename = 'pack_size.xlsx';
        } elseif ($request->has('binlocations')) {
            $data = WaLocationAndStore::with('bin_locations:id,title')
                ->get()
                ->flatMap(function ($location) {
                    $locationData = [['id' => '', 'name' => $location->location_name]];
                    $binLocations = $location->bin_locations->map(function ($bin_location) {
                        return ['id' => $bin_location->id, 'name' => $bin_location->title];
                    });
                    return array_merge($locationData, $binLocations->toArray(), [['id' => '', 'name' => '']]);
                });
            $filename = 'bin_locations.xlsx';
        }

        if (!empty($data)) {
            return ExcelDownloadService::download($filename, collect($data), $headings);
        }

        return redirect()->back()->with('error', 'No data found for the selected category.');
    }
}
