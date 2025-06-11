<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Excel;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryItem;


class ImportsController extends Controller {

    
    
    public function importInventoryCategory(Request $request){
        $file_path = 'public/uploads/excel_imports/item_cat.xlsx';
        $data = Excel::load($file_path)->toArray();
        $not_saved_row = [];
       
        foreach($data as $key => $row){


            $wa_stock_type_category_row = \App\Model\WaStockTypeCategory::where('title', $row['stocktype'])->first();
            
            $wa_stock_family_group_row = \App\Model\WaStockFamilyGroup::where('title', $row['familygroup'])->first();
            
            $stock_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['stockact'])->first();
            
            $wip_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['wipact'])->first();
            
            $stock_adjustments_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['adjglact'])->first();
            
            $internal_stock_issues_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['issueglact'])->first();
            
            $price_variance_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['purchpricevaract'])->first();
            
            $usage_variance_gl_code_row = \App\Model\WaChartsOfAccount::where('account_code', $row['materialuseagevarac'])->first();
            
            if(empty($wa_stock_family_group_row)
                || empty($stock_gl_code_row) 
                || empty($wip_gl_code_row) 
                || empty($stock_adjustments_gl_code_row) 
                || empty($internal_stock_issues_gl_code_row)
                || empty($price_variance_gl_code_row)
                || empty($usage_variance_gl_code_row)
            ) {
                //dd($row);
                $not_saved_row[] = $row;
                continue;
            }


            
            
            $entity = WaInventoryCategory::firstOrNew(
                [
                    'category_code'=>$row['categoryid'],
                ]
            );
            $entity->category_description = $row['categorydescription'];
            $entity->wa_stock_type_category_id = !empty($wa_stock_type_category_row->id) ? $wa_stock_type_category_row->id : 3;
            $entity->wa_stock_family_group_id = $wa_stock_family_group_row->id;
            $entity->stock_gl_code_id= $stock_gl_code_row->id;
            $entity->wip_gl_code_id = $wip_gl_code_row->id;
            $entity->stock_adjustments_gl_code_id = $stock_adjustments_gl_code_row->id;
            $entity->internal_stock_issues_gl_code_id = $internal_stock_issues_gl_code_row->id;
            $entity->price_variance_gl_code_id =  $price_variance_gl_code_row->id;
            $entity->usage_variance_gl_code_id = $usage_variance_gl_code_row->id;
            $entity->save();
        }
        echo "Not saved row <pre>";
        
        dd($not_saved_row); die;
        die('completed');
    }
    
    
    public function importInventoryItems(Request $request){
        $file_path = 'public/uploads/excel_imports/new_items.xlsx';
        $data = Excel::load($file_path)->toArray();
        $not_saved_row = [];
        foreach($data as $key => $row){
            $wa_inventory_category_row = \App\Model\WaInventoryCategory::where('category_code', $row['categoryid'])->first();
            
            $wa_unit_of_measure_row = \App\Model\WaUnitOfMeasure::where('title', $row['units'])->first();
            if(empty($wa_inventory_category_row) || empty($wa_unit_of_measure_row)){
                $not_saved_row[] = $key;
                continue;
            }


            
            
            
          

              $entity = WaInventoryItem::where('stock_id_code',$row['stockid'])->first();
            if(!$entity)
            {
               $entity =  new WaInventoryItem();
               $entity->stock_id_code = $row['stockid'];
            }
            $entity->title = $row['description'];
            $entity->wa_inventory_category_id = $wa_inventory_category_row->id;
            $entity->standard_cost = $row['materialcost'];
            $entity->wa_unit_of_measure_id = $wa_unit_of_measure_row->id;
            //dd($entity);
            
            $entity->save();
        }
        echo "Not saved row <pre>";
        
        print_r($not_saved_row); die;
        die('completed');
    }
    
    
    
    public function importRecipe(Request $request){
        $file_path = 'public/uploads/excel_imports/Recipes-Header.xlsx';
        $data = Excel::load($file_path)->toArray();
        $not_saved_row = [];
        foreach($data as $key => $row){
            $unit_of_mesaurement_row = \App\Model\WaUnitOfMeasure::where('title', $row['base_unit'])->first();
            if(empty($unit_of_mesaurement_row)){
                $not_saved_row[] = $key;
                continue;
            }
            
            
            
            $entity = \App\Model\WaRecipe::firstOrNew(
                [
                    'recipe_number'=>$row['recipeno'],
                ]
            );
            $entity->title = $row['recipename'];
            $entity->user_id = getLoggeduserProfile()->id;
            $entity->major_group_id = $row['groupid'];
            $entity->unit_of_mesaurement_id = $unit_of_mesaurement_row->id;
            $entity->save();
        }
        echo "Not saved row <pre>";
        
        print_r($not_saved_row); die;
        die('completed');
    }
    
    public function importRecipeIngredient(Request $request){
        $file_path = 'public/uploads/excel_imports/Recipe-Lines.xlsx';
        $data = Excel::load($file_path)->toArray();
        $not_saved_row = [];
        foreach($data as $key => $row){
            
            $recipe_row = \App\Model\WaRecipe::where('recipe_number', $row['recipeno'])->first();
            
            $inventory_item_row = \App\Model\WaInventoryItem::where('title', $row['ingredient_name'])->first();
            
            if(empty($recipe_row) || empty($inventory_item_row)){
                $not_saved_row[] = $key;
                continue;
            }
            
            $inventory_item_row->standard_cost = empty($inventory_item_row->standard_cost) ? 0 : $inventory_item_row->standard_cost;
            $row['portion'] = empty($row['portion']) ? 0 : $row['portion'];
            $weight_portion = $inventory_item_row->standard_cost / $row['portion'];
            
            $entity = \App\Model\WaRecipeIngredient::firstOrNew(
                [
                    'wa_inventory_item_id'=>$inventory_item_row->id,
                ]
            );
            $entity->wa_recipe_id = $recipe_row->id;
            $entity->weight = $row['weight'];
            $entity->material_cost = $row['material_cost'];
            $entity->weight_portion = $weight_portion;
            $entity->number_of_portion = $row['portion'];
            $entity->cost = $row['cos'];
            $entity->save();
        }
        echo "Not saved row <pre>";
        
        print_r($not_saved_row); die;
        die('completed');
    }
    
    public function importSuppliers(){
        $file_path = 'public/uploads/excel_imports/Suppliers.csv';
        $data = Excel::load($file_path)->toArray();
        foreach($data as $key => $row){
            $entity = \App\Model\WaSupplier::firstOrNew(
                [
                    'supplier_code'=>$row['supplierid']
                ]
            );
            $entity->name = $row['suppname'];
            $entity->address = $row['address5'];
            $entity->country = $row['address6'];
            $entity->telephone = $row['lng'];
            $entity->facsimile = $row['facsimile'];
            $entity->email = $row['email_address'];
            $entity->url = $row['url'];
            $entity->supplier_type = $row['suppliertype'];
            $entity->supplier_since = !empty($row['suppliersince']) ? date('Y-m-d',strtotime($row['suppliersince'])): null;
            $entity->bank_reference = $row['bankrefrence'];
            $entity->remittance_advice = $row['remittanceadvice'];
            $entity->remittance_advice = $row['remittanceadvice'];
            $entity->tax_group = $row['taxgroupid'];
            $entity->save();
            
        }
        die('completed');
    }

}
