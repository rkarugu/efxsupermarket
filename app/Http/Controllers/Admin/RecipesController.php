<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use App\Model\WaRecipe;
use App\Model\WaRecipeIngredient;
use Excel;

class RecipesController extends Controller {

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'recipes';
        $this->title = 'Recipes';
        $this->pmodule = 'recipes';
    }

    public function index(Request $request) {

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaRecipe::with('getUnitOfMeausureDetail','getAssociateIngredient')->orderBy('id', 'desc')->get();
            $major_group_list = \App\Model\Category::getMajorGroupslist();
            $unit_of_measure_list = getUnitOfMeasureList();
            $location_list = getStoreLocationDropdown();
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.recipes.index',compact(
                    'title','lists','model','breadcum',
                    'pmodule','permission', 'major_group_list',
                    'unit_of_measure_list','location_list'
            ));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function autofillrecieptAmnt(Request $request){
        try {
            $ingredient = WaRecipeIngredient::select('cost')->where('wa_recipe_id',$request->wa_recipe_id)->get();
            $cost = 0;
            foreach($ingredient as $val){
                $cost += $val->cost;
            }
            $data['cost'] = $cost;
            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Exception $th) {
            return response()->json(['status' => false, 'data' => []]);
        }
    }
    public function addOrUpdate(Request $request) {
        $message = 'Recipe has been saved successfully.';
        if(!empty($request->id)){
            $entity = WaRecipe::find($request->id);
            
            $message = 'Recipe has been updated successfully.';
        }
        else{
            $entity = new WaRecipe();
        }
        $entity->recipe_number = $request->recipe_number;
        $entity->title = $request->title;
        $entity->major_group_id = $request->major_group_id;
        $entity->unit_of_mesaurement_id = $request->unit_of_mesaurement_id;
        $entity->user_id = getLoggeduserProfile()->id;
        $entity->wa_location_and_store_id = $request->wa_location_and_store_id;
        $entity->save();
         
        Session::flash('success', $message);
        return redirect()->back();
        
    }
    
    public function editRecipeFormAjax(Request $request){
        
        $row_data = WaRecipe::where('id', $request->recipe_id)->first()->toArray();
        return json_encode($row_data);
    }
    
    public function destroy($slug) {
        try {
            $pmodule = $this->pmodule;
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') {
                WaRecipe::whereSlug($slug)->delete();
                Session::flash('success', 'Recipe has been deleted successfully.');
                return redirect()->back();
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
            
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function show($slug){
        
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___ingredient-view']) || $permission == 'superadmin') {
            $data = WaRecipe::whereSlug($slug)->with('getAssociateIngredient', 'getAssociateLocation')->first();
            list($inventory_items_list, $inventory_items_list_data) = \App\Model\WaInventoryItem::getInventoryItemListData();
            $breadcum = [$title=>route($model.'.index'),'View'=>''];
            return view('admin.recipes.show_and_add_ingredient',compact('title','data','model','breadcum','pmodule','permission', 'inventory_items_list', 'inventory_items_list_data'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    
    public function recipesSummary(Request $request){
        
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___ingredient-view']) || $permission == 'superadmin') {
	        
            $data = WaRecipe::with('getAssociateIngredient', 'getAssociateLocation')->get();
            
            list($inventory_items_list, $inventory_items_list_data) = \App\Model\WaInventoryItem::getInventoryItemListData();
            $breadcum = [$title=>route($model.'.index'),'View'=>''];
            return view('admin.recipes.recipe_summary',compact('title','data','model','breadcum','pmodule','permission', 'inventory_items_list', 'inventory_items_list_data'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    
    public function recipeIngredientSave(Request $request, $slug){
        $entity = new WaRecipeIngredient();
        $entity->wa_recipe_id = $request->wa_recipe_id;
        $entity->wa_inventory_item_id = $request->wa_inventory_item_id;
        $entity->weight = $request->weight;
        $entity->material_cost = $request->material_cost;
        $entity->weight_portion = $request->weight_portion;
        $entity->number_of_portion = $request->number_of_portion;
        $entity->cost = $request->cost;
        $entity->save();
        Session::flash('success', 'Ingredient has been saved successfully.');
        return redirect()->back();
    }
    
    public function recipeIngredientDelete($id){
        try {
            $pmodule = $this->pmodule;
            $permission = $this->mypermissionsforAModule();
            if (isset($permission[$pmodule . '___ingredient-delete']) || $permission == 'superadmin') {
                WaRecipeIngredient::whereId($id)->delete();
                Session::flash('success', 'Ingredient has been deleted successfully.');
                return redirect()->back();
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function recipeIngredientEdit($id){
        
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (isset($permission[$pmodule . '___Ingredient-edit']) || $permission == 'superadmin') {
            $ingredient = WaRecipeIngredient::whereId($id)->first();
            $data = WaRecipe::where('id', $ingredient->wa_recipe_id)->first();
            list($inventory_items_list, $inventory_items_list_data) = \App\Model\WaInventoryItem::getInventoryItemListData();
            $breadcum = [$title=>route($model.'.index'),'Edit'=>''];
            return view('admin.recipes.recipe_ingredient_edit',compact('title','data','ingredient','model','breadcum','pmodule','permission', 'inventory_items_list', 'inventory_items_list_data'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }
    
    public function recipeIngredientUpdate(Request $request, $id){
        $entity = WaRecipeIngredient::find($id);
        $entity->wa_inventory_item_id = $request->wa_inventory_item_id;
        $entity->weight = $request->weight;
        $entity->material_cost = $request->material_cost;
        $entity->weight_portion = $request->weight_portion;
        $entity->number_of_portion = $request->number_of_portion;
        $entity->cost = $request->cost;
        $entity->save();
        Session::flash('success', 'Ingredient has been updated successfully.');
        $data = WaRecipe::where('id', $entity->wa_recipe_id)->first();
        return redirect()->route('recipes.show', $data->slug);
    }
    
    

}
