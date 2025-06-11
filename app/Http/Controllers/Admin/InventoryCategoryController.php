<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryCategorySubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Session;
use Illuminate\Support\Facades\Validator;

class InventoryCategoryController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'inventory-categories';
        $this->title = 'Inventory Categories';
        $this->pmodule = 'inventory-categories';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaInventoryCategory::with('getusageGlDetail', 'getPricevarianceGlDetail')->orderBy('id', 'desc')->get();
            // echo "<pre>"; print_r($lists); die;
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.inventorycategories.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
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
            return view('admin.inventorycategories.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }

    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'category_code' => 'required|unique:wa_inventory_categories',
                'item_sub_categories' => 'nullable|array'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {
                $row = new WaInventoryCategory();
                $row->category_code = strtoupper($request->category_code);
                $row->category_description = $request->category_description;
                $row->wa_stock_type_category_id = $request->wa_stock_type_category_id;
                $row->wa_stock_family_group_id = $request->wa_stock_family_group_id;
                $row->stock_gl_code_id = $request->stock_gl_code_id;
                $row->wip_gl_code_id = $request->wip_gl_code_id;
                $row->internal_stock_issues_gl_code_id = $request->internal_stock_issues_gl_code_id;
                $row->price_variance_gl_code_id = $request->price_variance_gl_code_id;
                $row->usage_variance_gl_code_id = $request->usage_variance_gl_code_id;
                $row->stock_adjustments_gl_code_id = $request->stock_adjustments_gl_code_id;
                $row->save();
                if ($request->item_sub_categories && count($request->item_sub_categories) > 0) {
                    foreach ($request->item_sub_categories as $key => $category) {
                        $sub = new WaInventoryCategorySubCategory();
                        $sub->category_id = $row->id;
                        $sub->sub_category_id = $category;
                        $sub->created_at = date('Y-m-d H:i:s');
                        $sub->updated_at = date('Y-m-d H:i:s');
                        $sub->save();
                    }
                }

                if ($file = $request->file('image')) {
                    $uploadPath = 'uploads/item_categories';
                    if (!file_exists($uploadPath)) {
                        File::makeDirectory($uploadPath, $mode = 0777, true, true);
                    }

                    $name = $file->hashName();
                    $file->move(public_path($uploadPath), $name);
                    $row->image = "$uploadPath/$name";
                    $row->save();
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

    public function search_sub_categories(Request $request)
    {
        $row = WaInventoryCategory::with(['sub_categories' => function ($e) use ($request) {
            if ($request->search) {
                $e->where('title', 'LIKE', "%$request->search%");
            }
        }])->where('id', $request->id)->first();
        $data = [];
        foreach ($row->sub_categories as $key => $value) {
            $data[] = ['id' => $value->id, 'title' => $value->title];
        }
        return $data;
    }


    public function show($id)
    {

    }


    public function edit($slug)
    {
        try {
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row = WaInventoryCategory::whereSlug($slug)->first();
                if ($row) {
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    return view('admin.inventorycategories.edit', compact('title', 'model', 'breadcum', 'row'));
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
            $row = WaInventoryCategory::whereSlug($slug)->first();
            $validator = Validator::make($request->all(), [

                'category_code' => 'required|unique:wa_inventory_categories,category_code,' . $row->id,
                'item_sub_categories' => 'nullable|array'

            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            } else {


                $row->category_code = strtoupper($request->category_code);
                $row->category_description = $request->category_description;
                $row->wa_stock_type_category_id = $request->wa_stock_type_category_id;
                $row->wa_stock_family_group_id = $request->wa_stock_family_group_id;
                $row->stock_gl_code_id = $request->stock_gl_code_id;
                $row->wip_gl_code_id = $request->wip_gl_code_id;
                $row->internal_stock_issues_gl_code_id = $request->internal_stock_issues_gl_code_id;
                $row->price_variance_gl_code_id = $request->price_variance_gl_code_id;
                $row->usage_variance_gl_code_id = $request->usage_variance_gl_code_id;
                $row->stock_adjustments_gl_code_id = $request->stock_adjustments_gl_code_id;
                $row->save();
                WaInventoryCategorySubCategory::where('category_id', $row->id)->delete();
                if ($request->item_sub_categories && count($request->item_sub_categories) > 0) {
                    foreach ($request->item_sub_categories as $key => $category) {
                        $sub = new WaInventoryCategorySubCategory();
                        $sub->category_id = $row->id;
                        $sub->sub_category_id = $category;
                        $sub->created_at = date('Y-m-d H:i:s');
                        $sub->updated_at = date('Y-m-d H:i:s');
                        $sub->save();
                    }
                }

                $uploadPath = 'uploads/item_categories';
                if ($fileName = $row->image) {
                    File::delete(public_path($uploadPath) . "/$fileName");
                }

                if($file = $request->file('image')) {
                    if (!file_exists($uploadPath)) {
                        File::makeDirectory($uploadPath, $mode = 0777, true, true);
                    }

                    $name = $file->hashName();
                    $file->move(public_path($uploadPath), $name);
                    $row->image = "$uploadPath/$name";
                    $row->save();
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

            // WaInventoryCategory::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function getInventoryCategories(Request $request): JsonResponse
    {
        try {
            $categories = WaInventoryCategory::select('id', 'category_description as name', 'image');

            if ($request->search_query) {
                $matchingItems = DB::table('wa_inventory_items')->where('title', 'like', "%$request->search_query%")
                    ->pluck('wa_inventory_category_id')->toArray();
                $categories = $categories->where('category_description', 'LIKE', "%$request->search_query%")->orWhereIn('id', $matchingItems);
            }
            if ($request->origin && $request->origin == 'pos') {
                $categories = $categories->orderBy('name')->get();
            }else{
                $categories = $categories->orderBy('name')
                ->cursorPaginate(20)
                ->through(function (WaInventoryCategory $category) {
                    return $category;
                });
            }
            
            return $this->jsonify($categories, 200);
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}
