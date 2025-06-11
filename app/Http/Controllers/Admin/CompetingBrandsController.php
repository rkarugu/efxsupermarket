<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CompetingItemsImport;
use App\Model\WaInventoryAssignedItems;
use App\Model\WaInventoryItem;
use App\Models\CompetingBrand;
use App\Models\CompetingBrandItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CompetingBrandsController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'competing-brands';
        $this->title = 'Competing Brands';
        $this->pmodule = 'competing-brands';
        $this->basePath = 'admin.competing_brands';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $items = WaInventoryItem::all(); 
        // $competingBrands = CompetingBrand::with(['getRelatedItems', 'getRelatedUser'])->get();
        $competingBrands = CompetingBrand::with(['getRelatedItems', 'getRelatedUser'])
            ->withCount('getRelatedItems');
        if ($request->item){
            $competingBrands = $competingBrands->whereHas('getRelatedItems', function ($query) use ($request) {
                $query->where('competing_brand_items.wa_inventory_item_id', $request->item);
            });
        }
        $competingBrands = $competingBrands->get();
  
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route('competing-brands.index'), 'Listing' => ''];
            return view('admin.competing_brands.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission','competingBrands', 'items'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function create(){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $existingRelationships = CompetingBrandItem::all()->pluck('wa_inventory_item_id')->toArray();
        $childItems = WaInventoryAssignedItems::all()->pluck('destination_item_id')->toArray();
        $items = WaInventoryItem::whereNotIn('id', $existingRelationships)->whereNotIn('id', $childItems)->get();
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            $breadcum = [$title => route('competing-brands.index'), 'Listing' => ''];
            return view('admin.competing_brands.create', compact('title','model','pmodule','permission', 'items'));
        } else {
            Session::flash('warning', 'Access denied');
            return redirect()->back();
        }
    }
    public function store(Request $request)
    {
        try {
          
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'items.*' => 'required|exists:wa_inventory_items,id',  
            ]);
            if ($validated->fails()) {
              return  redirect()->back()->withErrors($validated->errors())->withInput();
            }
             
            $user = Auth::user();
            $competingBrand = new CompetingBrand();
            $competingBrand->name = strtoupper($validated['name']);
            $competingBrand->created_by = $user->id;
            $competingBrand->save();
        
           foreach ($request->items as $item) {
                $existing = CompetingBrandItem::where('wa_inventory_item_id', $item)->first();
                if($existing){
                    continue;
                }
                $competingBrandItem = new CompetingBrandItem();
                $competingBrandItem->wa_inventory_item_id = $item;
                $competingBrandItem->competing_brand_id = $competingBrand->id;
                $competingBrandItem->added_by = $user->id;
                $competingBrandItem->save();
            }
            return redirect()->route('competing-brands.index')->with('success', 'Competing brands saved successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }
    public function fetchCompetingBrands($id){
        $item = WaInventoryItem::find($id);
        $competingBrandItem  = CompetingBrandItem::where('wa_inventory_item_id', $id)->first()->competing_brand_id;
        $competingItems = DB::table('competing_brand_items')
            ->select(
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                DB::raw("(SELECT SUM(qauntity)
                    FROM wa_stock_moves where stock_id_code = wa_inventory_items.stock_id_code
                 ) AS qoh"),
            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'competing_brand_items.wa_inventory_item_id')
            ->where('competing_brand_items.competing_brand_id', $competingBrandItem)
            ->whereNot('wa_inventory_items.id', $id)
            ->get();
            return response()->json([
                'itemName' => $item->stock_id_code.'-'.$item->title, 
                'competingBrands' => $competingItems
            ]);
    }
    public function edit($id){
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $user = Auth::user();
        $existingRelationships = CompetingBrandItem::whereNot('competing_brand_id', $id)->pluck('wa_inventory_item_id')->toArray();
        $childItems = WaInventoryAssignedItems::all()->pluck('destination_item_id')->toArray();
        $items = WaInventoryItem::whereNotIn('id', $existingRelationships)->whereNotIn('id', $childItems)->get();
        $competingBrand = CompetingBrand::with(['getRelatedItems', 'getRelatedItems.getRelatedItem'])->where('id', $id)->first();
        $selectedItems = CompetingBrandItem::where('competing_brand_id', $id)->pluck('wa_inventory_item_id')->toArray();
        if (isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') {
            $breadcum = [$title => route('competing-brands.index'), 'Listing' => ''];
            return view('admin.competing_brands.edit', compact('title','model','pmodule','permission', 'items', 'competingBrand', 'selectedItems', 'user'));
        } else {
            Session::flash('warning', 'Access denied');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'items.*' => 'required',
            ]);
            if ($validator->fails()) {
                return $request->ajax() ? response()->json([
                    'result' => 0,
                    'errors' => $validator->errors()
                ]) : redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $user = Auth::user();
            DB::beginTransaction();
            $competingBrand = CompetingBrand::find($id);
            $competingBrand->name = strtoupper($request->name);
            $competingBrand->save();
            $itemsArray = [];
            foreach ($request->items as $item) {
                $itemsArray[] = $item;
                
                $inventoryItem = WaInventoryItem::find($item);
                if(!$inventoryItem){
                    DB::rollBack();
                    Session::flash('warning', 'item with id ' . $item . ' does not exist');
                    return redirect()->back();
                }
                $added_by = null;
                $existing = CompetingBrandItem::where('competing_brand_id', $id)->where('wa_inventory_item_id', $item)->first();
                if($existing){
                    $added_by = $existing->added_by;
                    $existing->delete();
                }
                $competingBrandItem = new CompetingBrandItem();
                $competingBrandItem->wa_inventory_item_id = $item;
                $competingBrandItem->competing_brand_id = $competingBrand->id;
                $competingBrandItem->added_by = $added_by ? $added_by : $user->id;
                $competingBrandItem->save();
            }
           CompetingBrandItem::whereNotIn('wa_inventory_item_id', $itemsArray)->where('added_by', $user->id)->where('competing_brand_id', $id)->delete();
            DB::commit();
            return redirect()->route('competing-brands.index')->with('success', 'Competing brands edited successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Session::flash('warning', $th->getMessage());
            return redirect()->back();
        }
    }
    public function completedBrandsItems($competingBrandsId)
    {
        $competingItems = DB::table('competing_brand_items')
            ->select(
                'wa_inventory_items.stock_id_code',
                'wa_inventory_items.title',
                'wa_inventory_items.standard_cost',
                'wa_inventory_items.selling_price',
                DB::raw("(SELECT SUM(qauntity)
                    FROM wa_stock_moves where stock_id_code = wa_inventory_items.stock_id_code
                 ) AS qoh"),
            )
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', 'competing_brand_items.wa_inventory_item_id')
            ->where('competing_brand_items.competing_brand_id', $competingBrandsId)
            ->get();
        $competingBrands = CompetingBrand::where('id', $competingBrandsId)->with(['getRelatedItems', 'getRelatedUser'])->get();
        return response()->json($competingItems);
    }
    public function uploadExcel(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls',
    ]);

    $import = new CompetingItemsImport;
    Excel::import($import, $request->file('excel_file'));

    $items = $import->getItems();

    return response()->json(['items' => $items]);
}
}
