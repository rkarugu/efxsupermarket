<?php

namespace App\Http\Controllers\Admin;

use App\WaItemSubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ItemSubCategories;

use DB;
use Illuminate\Support\Facades\File;
use Session;
use Illuminate\Support\Facades\Validator;

class ItemSubCategoriesController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'item-sub-categories';
        $this->title = 'Item Sub Categories';
        $this->pmodule = 'item-sub-categories';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = ItemSubCategories::orderBy('id', 'desc')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.item_sub_categories.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.item_sub_categories.create', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }

    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'title' => 'required|max:250',
            'description' => 'required|max:250',
        ]);

        if ($validations->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }

        $subcategory = WaItemSubCategory::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $uploadPath = 'uploads/item_sub_categories';
        $file = $request->file('image');
        if (!file_exists($uploadPath)) {
            File::makeDirectory($uploadPath, $mode = 0777, true, true);
        }

        $name = $file->hashName();
        $file->move(public_path($uploadPath), $name);
        $subcategory->update(['image' => "$uploadPath/$name"]);

        return response()->json([
            'result' => 1,
            'message' => 'Item Sub Categories Added successfully',
            'location' => route($this->model . '.index'),
        ]);
    }

    public function edit($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
            $pack = WaItemSubCategory::findOrFail($id);
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.item_sub_categories.edit', compact('title', 'model', 'pack', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function update($id, Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;

        if (!isset($permission[$pmodule . '___edit']) && !$permission == 'superadmin') {
            return response()->json([
                'result' => -1,
                'message' => 'Restricted: You dont have enough permissions',
            ]);
        }

        $validations = Validator::make($request->all(), [
            'title' => 'required|max:250',
            'description' => 'required|max:250',
        ]);

        if ($validations->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }

        $subcategory = WaItemSubCategory::findOrFail($id);
        $subcategory->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $uploadPath = 'uploads/item_sub_categories';
        $file = $request->file('image');
        if ($fileName = $subcategory->image) {
            File::delete(public_path($uploadPath) . "/$fileName");
        }

        if (!file_exists($uploadPath)) {
            File::makeDirectory($uploadPath, $mode = 0777, true, true);
        }

        $name = $file->hashName();
        $file->move(public_path($uploadPath), $name);
        $subcategory->update(['image' => "$uploadPath/$name"]);

        return response()->json([
            'result' => 1,
            'message' => 'Item Sub Categories Updated successfully',
            'location' => route($this->model . '.index'),
        ]);
    }

    public function destroy($id, Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (!isset($permission[$pmodule . '___delete']) && !$permission == 'superadmin') {
            return response()->json([
                'result' => -1,
                'message' => 'Restricted: You dont have enough permissions',
            ]);
        }
        $new = ItemSubCategories::findOrFail($id);
        if ($new->category_relation->count() > 0) {
            return response()->json([
                'result' => 0,
                'message' => 'Restricted: Invalid Request',
            ]);
        }
        $new->delete();
        return response()->json([
            'result' => 1,
            'message' => 'Item Sub Categories deleted successfully',
            'location' => route($this->model . '.index'),
        ]);
    }

    public function dropdown_search(Request $request)
    {
        $l = '';
        if ($request->id) {
            $l = "AND category_id != " . $request->id;
        }
        return ItemSubCategories::where(function ($e) use ($request) {
            if ($request->search) {
                $e->where('title', 'LIKE', "%$request->search%");
            }
        })
            ->having(DB::RAW("(select count(*) from wa_inventory_category_sub_category_relation where sub_category_id = wa_item_sub_categories.id " . $l . ")"), '=', '0')
            ->get();
    }
}