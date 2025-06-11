<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Excel;
use App\Model\WaSoldButUnbookedItem;
use App\Model\OrderedItem;


class SalesController extends Controller {
    
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 30000);
        $this->model = 'sales';
        $this->title = 'Sales';
        $this->pmodule = 'recipes';
    }
    
    
    
    public function salesDeductions(Request $request)
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Sales Deductions';
        $model = $this->model.'-sales_deduction';
       //  echo $pmodule . '-view<br>';;
        // echo "<pre>"; print_r($permission); die;
        if (isset($permission[$pmodule . '___ingredient-view']) || $permission == 'superadmin') 
        {
            //$data = OrderedItem::with('getrelatedOrderForItem', 'getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociateFooditem.getAssociateRecipe.getAssociateIngredient.getAssociateItemDetail')->get();


            if ($request->has('start-date') || $request->has('end-date'))
            {
                $data = OrderedItem::with('getrelatedOrderForItem', 'getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociateFooditem.getAssociateRecipe.getAssociateIngredient.getAssociateItemDetail');
                if ($request->has('start-date'))
               {
                    $data = $data->where('created_at','>=',$request->input('start-date'));
               }
                if($request->has('end-date'))
                {
                    $data = $data->where('created_at','<=',$request->input('end-date'));
                }
                $data = $data->get();
                 if ($request->has('manage-request') && $request->input('manage-request') == 'xls')
            {
                $this->exportDataToExcel('SALESDEDUCTION',$data,$request,'Sales Deductions'); 
            }
            }

           


            $breadcum = [$title => ''];
            return view('admin.sales.sales_deductions', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
        } 
        else 
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function salesWithLessQuantity(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Sales with less quantity';
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
           // $data = WaSoldButUnbookedItem::with('getAssociatedOrder', 'getAssociatedOrderedItem.getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociatedInventoryItem')->get();

             if ($request->has('start-date') || $request->has('end-date'))
        {
            $data = WaSoldButUnbookedItem::with('getAssociatedOrder', 'getAssociatedOrderedItem.getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociatedInventoryItem');
            if ($request->has('start-date'))
           {
                $data = $data->where('created_at','>=',$request->input('start-date'));
           }
            if($request->has('end-date'))
            {
                $data = $data->where('created_at','<=',$request->input('end-date'));
            }
            $data = $data->get();


            if ($request->has('manage-request') && $request->input('manage-request') == 'xls')
            {
                $this->exportDataToExcel('SALESWITHLESSQUANTITY',$data,$request,'Sales With Less Quantity'); 
            }
        }





            $breadcum = [$title => ''];
            return view('admin.sales.sales_with_less_quantity', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
        }
        else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    
    public function salesWithNoRecipeLink(Request $request){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = 'Sales Deductions';
        $model = $this->model.'-sales_with_no_recipe_link';
      //  if (isset($permission[$pmodule . '___sales_with_no_recipe_link_view']) || $permission == 'superadmin') {
           

           /* $data = OrderedItem::with('getrelatedOrderForItem', 'getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociateFooditem.getAssociateRecipe.getAssociateIngredient.getAssociateItemDetail')
                    ->leftJoin('food_items', 'ordered_items.food_item_id', '=', 'food_items.id')
                    ->where(function($query){
                        $query->orWhereNull('food_items.wa_recipe_id', null)
                        ->orWhere('food_items.wa_recipe_id',  '=', '');
                    })
                    ->get();*/

        if ($request->has('start-date') || $request->has('end-date'))
        {
             $data = OrderedItem::with('getrelatedOrderForItem', 'getAssociateFooditem.getAssociateRecipe.getAssociateLocation.getBranchDetail', 'getAssociateFooditem.getAssociateRecipe.getAssociateIngredient.getAssociateItemDetail')
                    ->leftJoin('food_items', 'ordered_items.food_item_id', '=', 'food_items.id')
                    ->where(function($query){
                        $query->orWhereNull('food_items.wa_recipe_id', null)
                        ->orWhere('food_items.wa_recipe_id',  '=', '');
                    });
            if ($request->has('start-date'))
           {
                $data = $data->where('ordered_items.created_at','>=',$request->input('start-date'));
           }
            if($request->has('end-date'))
            {
                $data = $data->where('ordered_items.created_at','<=',$request->input('end-date'));
            }
            $data = $data->get();
             if ($request->has('manage-request') && $request->input('manage-request') == 'xls')
            {
                $this->exportDataToExcel('SALESWITHNORECIPELINK',$data,$request,'Sales With No Recipe Link'); 
            }
        }       
            
            
            $breadcum = [$title => ''];
            return view('admin.sales.sales_with_no_recipe_link', compact('data','title','model', 'breadcum', 'pmodule', 'permission'));
// `        } else {
//             Session::flash('warning', 'Invalid Request');
//             return redirect()->back();
//         }`
    }

    public function exportDataToExcel($case,$dataArr,$request,$report_name)
    {
        $export_array = [];
        $export_array[] = array($report_name);//heading;
        $date_arr = array('','','','Printed On:'.date('d/m/Y h:i A'));
        if ($request->has('start-date'))
        {
            $date_arr[0] = 'Period From : '.date('d/m/Y h:i A',strtotime($request->input('start-date')));  

        }
        if ($request->has('end-date'))
        {
            $date_arr[0] = $date_arr[0] != ''?$date_arr[0].'  - To : '.date('d/m/Y h:i A',strtotime($request->input('end-date'))):' To :'.date('d/m/Y h:i A',strtotime($request->input('end-date')));  
        }
        $export_array[] = $date_arr;
        if($case == 'SALESDEDUCTION')
        {          
            $export_array[] = array('S NO','Order NO','Menu Item','Date','Recipe No','Recipe Name','Recipe Cost','Family Group','Ingredient Details','Qty Deducted','Weight','Store Location','Branch');
            $b =1;
            if(isset($dataArr) && count($dataArr)>0)
            {
                foreach($dataArr as $row)
                {
                    if(isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient))
                    {
                        foreach($row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient  as $recipe_ingredient_key => $recipe_ingredient_row)
                        {
                            $item_row = isset($recipe_ingredient_row->getAssociateItemDetail) ? $recipe_ingredient_row->getAssociateItemDetail : []; 
                            $qty_deducted = getinventoryItemDeductedQuantity($row->id, $item_row->id);
                            $item_detail_str = !empty($inventory_item_detail_arr) ? '' : '';
                            $item_detail_str .= "$item_row->title ($item_row->stock_id_code)";
                                 $export_array[] = array($b,
                                    manageOrderidWithPad($row->getrelatedOrderForItem->id),
                                    isset($row->getAssociateFooditem->name) ? $row->getAssociateFooditem->name : '',
                                    getDateTimeFormatted($row->getrelatedOrderForItem->created_at),
                                    isset($row->getAssociateFooditem->getAssociateRecipe->recipe_number) ? $row->getAssociateFooditem->getAssociateRecipe->recipe_number : '',
                                    $row->getAssociateFooditem->getAssociateRecipe->title,
                                    $recipe_ingredient_row->cost,
                                    $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name,
                                    $item_detail_str,
                                    $qty_deducted,
                                    ($recipe_ingredient_row->weight * $row->item_quantity),
                                    isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name) ? $row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name : '',
                                    isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name) ? $row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name : ''

                                    );
                                $b++;
                         }
                    }
                }
            }

        }
        else if($case=='SALESWITHLESSQUANTITY')
        {
            
            $export_array[] = array('S NO','Order NO','Date','Recipe No','Ingredient No','Ingredient Name','QTBD','QOH(that time)','Store Location','Branch');
            $b =1;
            if(isset($dataArr) && count($dataArr)>0)
            {
                foreach($dataArr as $row)
                {
                    $export_array[] = array(
                        $b,
                        manageOrderidWithPad($row->getAssociatedOrder->id),
                        getDateTimeFormatted($row->getAssociatedOrder->created_at),
                        isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->recipe_number) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->recipe_number : '',$row->getAssociatedInventoryItem->stock_id_code,$row->getAssociatedInventoryItem->title,$row->qoh + $row->deficient_quantity,$row->qoh,isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name : '',isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name : ''
                        );
                    $b++;
                }
            }
        }

        else if($case == 'SALESWITHNORECIPELINK')
        {
            $export_array[] = array('S NO','Order NO','Date','Item Name','Quantity');
            $b =1;
              if(isset($dataArr) && count($dataArr)>0)
              {
                foreach($dataArr as $row)
                {
                    $export_array[] = array($b,
                        manageOrderidWithPad($row->getrelatedOrderForItem->id),
                        getDateTimeFormatted($row->getrelatedOrderForItem->created_at),
                        $row->item_title,
                        $row->item_quantity

                        );
                    $b++;

                }
              }
                       
        }
        else
        {

        }




        return Excel::create($case, function($excel) use ($export_array) {
            $excel->sheet('mySheet', function($sheet) use ($export_array)
            {
                $sheet->fromArray($export_array);
            });
        })->download('xls');
    }
    
}