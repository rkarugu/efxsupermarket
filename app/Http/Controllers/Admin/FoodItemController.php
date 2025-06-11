<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\FoodItem;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Model\CondimentGroup;
use App\Model\ItemCategoryRelation;
use App\Model\ItemCondimentGroupRelation;
use App\Model\FoodItemsPrintClassRelation;
use App\Model\CategoryAndFoodItemTaxManager;
use App\Http\Requests\Admin\FoodItemAddRequest;
use App\Http\Requests\Admin\FoodItemUpdateRequest;
use DB;
use Session;
use Excel;

class FoodItemController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    protected $pmodule_2;
    public function __construct()
    {
        $this->model = 'menu-items';
        $this->title = 'General Item';
        $this->pmodule = 'general-item';
        $this->pmodule_2 = 'offer-themes-nights';
    } 

    public function index()
    {
       // $this->foodItemNotRelatedtoplu();
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = FoodItem::with('getClassName','getAssociateRecipe')->where('is_general_item','1')->orderBy('id', 'DESC')->get();
           // dd($lists);// die;
            $breadcum = [$title=>route($model.'.index'),'Listing'=>''];
            return view('admin.menuitems.index',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function themeIndex()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule_2;
        $title = 'Offer/Theme Nights';
        $model = $this->model;
        if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
        {
            $lists = FoodItem::where('is_general_item','0')->orderBy('id', 'DESC')->get();
            $breadcum = [$title=>route($model.'.themeindex'),'Listing'=>''];
            return view('admin.menuitems.themeindex',compact('title','lists','model','breadcum','pmodule','permission'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule.'___add']) || $permission == 'superadmin')
        {
            $familyGroups = $this->getParentList('getUnalcoholicgroup');
            $getCondimentGroupsList = $this->getCondimentGroupsList();
            $subFimilyGroups = $this->getParentList(4);
            $familyGroups = $familyGroups->toArray()+$subFimilyGroups->toArray();
            $printclasses = $this->getAllPrintClassesName();
            $all_taxes = $this->getAllTaxFromTaxManagers();
          //  $pluNumberList = $this->getPluList();
            $title = 'Add '.$this->title;
            $model = $this->model;
            $breadcum = [$this->title =>route($model.'.index'),'Add'=>''];
            $recipe_list = \App\Model\WaRecipe::getRecipeList();
            return view('admin.menuitems.create',compact('title','model','breadcum','familyGroups','printclasses','getCondimentGroupsList','all_taxes','pluNumberList', 'recipe_list'));
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function themeCreate()
    {
        $permission =  $this->mypermissionsforAModule();
        if(isset($permission[$this->pmodule_2.'___add']) || $permission == 'superadmin')
        {
            $familyGroups = $this->getParentList(9);
            $getCondimentGroupsList = $this->getCondimentGroupsList();
            $printclasses = $this->getAllPrintClassesName();
            $title = 'Add Offer/Theme Nights';
            $model = $this->model;
            $pluNumberList = [];//$this->getPluList();
            $breadcum = ['Offer/Theme Nights' =>route($model.'.themeindex'),'Add'=>''];
            return view('admin.menuitems.themecreate',compact('title','model','breadcum','familyGroups','printclasses','getCondimentGroupsList','pluNumberList')); 
        }
        else
        {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }


    public function store(FoodItemAddRequest $request)
    {
        try
        {
            $row = new FoodItem();
            $row->name= $request->name;
            $row->description= $request->description;
            $row->price= $request->price;
              $row->recipe_cost= $request->recipe_cost;
            $row->is_available_in_stock = '0';
            if($request->is_available_in_stock)
            {
                $row->is_available_in_stock = '1';
            }


            $row->show_to_customer = '0';
            if($request->show_to_customer)
            {
                $row->show_to_customer = '1';
            }

             $row->show_to_waiter = '0';
            if($request->show_to_waiter)
            {
                $row->show_to_waiter = '1';
            }
            
            $row->check_stock_before_sale = ($request->check_stock_before_sale) ? '1' : '0';
            $row->recipe_mandatory = ($request->recipe_mandatory) ? '1' : '0';
            
            //$row->plu_number = $request->plu_number?$request->plu_number:null;
            $row->wa_recipe_id = isset($request->wa_recipe_id) ? $request->wa_recipe_id : null;
            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'menu_items','100');
                $row->image= $image;
            }
            $row->save();

            ItemCategoryRelation::updateOrCreate(
                    ['item_id' => $row->id],  ['category_id' => $request->category_id]
                    ); 
            //ItemCondimentGroupRelation::where('food_item_id',$row->id)->delete();
            if($request->condiment_group_ids && count($request->condiment_group_ids)>0)
            {

                foreach($request->condiment_group_ids as $condiment_group_id)
                {
                    ItemCondimentGroupRelation::updateOrCreate(
                        ['condiment_group_id' => $condiment_group_id,'food_item_id' => $row->id]
                    );     
                } 

            }


             if($request->print_class_ids && count($request->print_class_ids)>0)
            {

                foreach($request->print_class_ids as $print_class_id)
                {
                    FoodItemsPrintClassRelation::updateOrCreate(
                        ['print_class_id' => $print_class_id,'food_item_id' => $row->id]
                    );     
                } 

            }


            if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    CategoryAndFoodItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'food_item_id' => $row->id]
                    );     
                } 

            }



             

            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.index'); 
        }
        catch(\Exception $e)
        {
            dd($e);
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function themeStore(FoodItemAddRequest $request)
    {
        try
        {
            $row = new FoodItem();
            $row->name= $request->name;
            $row->description= $request->description;
            $row->is_general_item='0';
            //$row->print_class_id= $request->print_class_id;
            $row->plu_number = $request->plu_number?$request->plu_number:null;

            if($request->file('image'))
            {
                $file = $request->file('image');
                $image = uploadwithresize($file,'menu_items','100');
                $row->image= $image;
            }
            $row->save();

            ItemCategoryRelation::updateOrCreate(
                    ['item_id' => $row->id],  ['category_id' => $request->category_id]
                    );   

            if($request->condiment_group_ids && count($request->condiment_group_ids)>0)
            {

                foreach($request->condiment_group_ids as $condiment_group_id)
                {
                    ItemCondimentGroupRelation::updateOrCreate(
                        ['condiment_group_id' => $condiment_group_id,'food_item_id' => $row->id]
                    );     
                } 

            }

            if($request->print_class_ids && count($request->print_class_ids)>0)
            {

                foreach($request->print_class_ids as $print_class_id)
                {
                    FoodItemsPrintClassRelation::updateOrCreate(
                        ['print_class_id' => $print_class_id,'food_item_id' => $row->id]
                    );     
                } 

            }

            Session::flash('success', 'Record added successfully.');
            return redirect()->route($this->model.'.themeindex'); 
        }
        catch(\Exception $e)
        {

           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {
        
    }


    public function edit($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule.'___edit']) || $permission == 'superadmin')
            {

                $row =  FoodItem::whereSlug($slug)->first();
                if($row)
                {
                    $printclasses = $this->getAllPrintClassesName();
                    $familyGroups = $this->getParentList('getUnalcoholicgroup');
                    $subFimilyGroups = $this->getParentList(4);
                    $getCondimentGroupsList = $this->getCondimentGroupsList();
                    $all_taxes = $this->getAllTaxFromTaxManagers();
                    $familyGroups = $familyGroups->toArray()+$subFimilyGroups->toArray();
                    $title = 'Edit '.$this->title;
                    $breadcum = [$this->title =>route($this->model.'.index'),'Edit'=>''];
                    $model = $this->model;
                    //$pluNumberList = $this->getPluList();
                    $recipe_list = \App\Model\WaRecipe::getRecipeList();
                    return view('admin.menuitems.edit',compact('title','model','breadcum','row','familyGroups','printclasses','getCondimentGroupsList','all_taxes','pluNumberList','recipe_list')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
           
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }

    public function themeEdit($slug)
    {
        try
        {
            $permission =  $this->mypermissionsforAModule();
            if(isset($permission[$this->pmodule_2.'___edit']) || $permission == 'superadmin')
            {
                $row =  FoodItem::whereSlug($slug)->first();
                if($row)
                {
                    $printclasses = $this->getAllPrintClassesName();
                    $familyGroups = $this->getParentList(9);
                    $getCondimentGroupsList = $this->getCondimentGroupsList();
                    $title = 'Edit Offer/Theme Nights';
                    $breadcum = ['Offer/Theme Nights' =>route($this->model.'.themeindex'),'Edit'=>''];
                    $model = $this->model;
                    $pluNumberList = [];//$this->getPluList();
                    return view('admin.menuitems.themeedit',compact('title','model','breadcum','row','familyGroups','printclasses','getCondimentGroupsList','pluNumberList')); 
                }
                else
                {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                } 
            }
            else
            {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            } 

            
           
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back();
        }
    }


    public function update(FoodItemUpdateRequest $request, $slug)
    {
        try
        {
            $row =  FoodItem::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            $row->description= $request->description;
            $row->price= $request->price;
             $row->recipe_cost= $request->recipe_cost;

            //$row->plu_number = $request->plu_number?$request->plu_number:$row->plu_number;
            $row->wa_recipe_id = isset($request->wa_recipe_id) ? $request->wa_recipe_id : null;
            $row->is_available_in_stock = '0';
            if($request->is_available_in_stock)
            {
                $row->is_available_in_stock = '1';
            }

            $row->show_to_customer = '0';
            if($request->show_to_customer)
            {
                $row->show_to_customer = '1';
            }

             $row->show_to_waiter = '0';
            if($request->show_to_waiter)
            {
                $row->show_to_waiter = '1';
            }
            $row->check_stock_before_sale = ($request->check_stock_before_sale) ? '1' : '0';
            $row->recipe_mandatory = ($request->recipe_mandatory) ? '1' : '0';
            

            //$row->print_class_id= $request->print_class_id;
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'menu_items','100');
                if($previous_row->image)
                {
                    unlinkfile('menu_items',$previous_row->image);
                }
                $row->image= $image;
            }
            $row->save();
             ItemCategoryRelation::updateOrCreate(
                    ['item_id' => $row->id],  ['category_id' => $request->category_id]
                    );  

             ItemCondimentGroupRelation::where('food_item_id',$row->id)->delete();
            if($request->condiment_group_ids && count($request->condiment_group_ids)>0)
            {

                foreach($request->condiment_group_ids as $condiment_group_id)
                {
                    ItemCondimentGroupRelation::updateOrCreate(
                        ['condiment_group_id' => $condiment_group_id,'food_item_id' => $row->id]
                    );     
                } 

            }


            FoodItemsPrintClassRelation::where('food_item_id',$row->id)->delete();
            if($request->print_class_ids && count($request->print_class_ids)>0)
            {

                foreach($request->print_class_ids as $print_class_id)
                {
                    FoodItemsPrintClassRelation::updateOrCreate(
                        ['print_class_id' => $print_class_id,'food_item_id' => $row->id]
                    );     
                } 

            }

             CategoryAndFoodItemTaxManager::where('food_item_id',$row->id)->delete();
             if($request->tax_manager_ids && count($request->tax_manager_ids)>0)
            {
                foreach($request->tax_manager_ids as $tax_manager_id)
                {
                    CategoryAndFoodItemTaxManager::updateOrCreate(
                        ['tax_manager_id' => $tax_manager_id,'food_item_id' => $row->id]
                    );     
                } 

            }



            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.index');
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }

    public function themeUpdate(FoodItemUpdateRequest $request, $slug)
    {
        try
        {
            $row =  FoodItem::whereSlug($slug)->first();
            $previous_row = $row;
            $row->name= $request->name;
            $row->description= $request->description;
            $row->plu_number = $request->plu_number?$request->plu_number:$row->plu_number;
            
            //$row->print_class_id= $request->print_class_id;
            
            if($request->file('image_update'))
            {
                $file = $request->file('image_update');
                $image = uploadwithresize($file,'menu_items','100');
                if($previous_row->image)
                {
                    unlinkfile('menu_items',$previous_row->image);
                }
                $row->image= $image;
            }
            $row->save();
            ItemCategoryRelation::updateOrCreate(
                    ['item_id' => $row->id],  ['category_id' => $request->category_id]
                    );  

            ItemCondimentGroupRelation::where('food_item_id',$row->id)->delete();
            if($request->condiment_group_ids && count($request->condiment_group_ids)>0)
            {

                foreach($request->condiment_group_ids as $condiment_group_id)
                {
                    ItemCondimentGroupRelation::updateOrCreate(
                        ['condiment_group_id' => $condiment_group_id,'food_item_id' => $row->id]
                    );     
                } 

            }


            FoodItemsPrintClassRelation::where('food_item_id',$row->id)->delete();
            if($request->print_class_ids && count($request->print_class_ids)>0)
            {

                foreach($request->print_class_ids as $print_class_id)
                {
                    FoodItemsPrintClassRelation::updateOrCreate(
                        ['print_class_id' => $print_class_id,'food_item_id' => $row->id]
                    );     
                } 

            }
            Session::flash('success', 'Record updated successfully.');
            return redirect()->route($this->model.'.themeindex');
        }
        catch(\Exception $e)
        {
           $msg = $e->getMessage();
           Session::flash('warning', $msg);
           return redirect()->back()->withInput();
        }
    }


    public function destroy($slug)
    {
        try
        {
            $row = FoodItem::whereSlug($slug)->first();
            FoodItem::whereSlug($slug)->delete();
            if($row->image)
            {
                unlinkfile('menu_items',$row->image); 
            }
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        }
        catch(\Exception $e)
        {

            Session::flash('warning','Invalid Request');
            return redirect()->back();
        }
    }

    public function getfamilygroupormenuitemgroup(Request $request)
    {
        $major_group_id = $request->major_group_id;
        if($major_group_id=='1')
        {
            //this is food and beverage then return family group
            $array = $this->getrecursiveentryforfamilygroup();

        }
    }

    public function getrecursiveentryforfamilygroup()
    {
        
    }


    public function foodItemNotRelatedtoplu()
    {
         $lists = FoodItem::select('name','id')->where('is_general_item','1')
         ->where('plu_number',null)
         ->where('recipe_cost', '0.00')
         ->orderBy('id', 'DESC')
         ->get()->toArray();
         sort($lists);

         $all_item[0]=['Sn','Name'];
         $i = 1;

         foreach($lists as $item)
         {
            $all_item[$i]=[$i,$item['name']];
            $i++;
         }
         $file_name = "menuitemwithoutplu";



         return Excel::create($file_name, function($excel) use ($all_item) {
            $excel->sheet('mySheet', function($sheet) use ($all_item)
            {
                $sheet->fromArray($all_item);
            });
        })->download('xls');

        
    }

    public function priceListExport()
    {
            $lists = FoodItem::with('getClassName','getAssociateRecipe','getItemCategoryRelation')->where('is_general_item','1')->orderBy('id', 'DESC')->get()->toArray();
       //  sort($lists);

         $all_item[0]=['Sn','Name','Price','Recipe Cost','Print Class','Family/Sub Family Group','Recipe'];
         $i = 1;
        //     echo "<pre>"; print_r($lists); die;        
          foreach($lists as $item)
         {
        $classname = [];
            foreach($item['get_class_name'] as $key=> $ClassName){
                $classname[$key] = $ClassName['get_associate_print_class']['name'];
            }

            $all_item[$i]=[$i,$item['name'],$item['price'],$item['recipe_cost'],implode(',',$classname),strtoupper(getCategoryNameById($item['get_item_category_relation']['category_id'])),((isset($item['get_associate_recipe']['title']) && $item['get_associate_recipe']['title']!="") ? $item['get_associate_recipe']['title'] : '-')];
            $i++;
         }
         $file_name = "pricelist";



         return Excel::create($file_name, function($excel) use ($all_item) {
            $excel->sheet('mySheet', function($sheet) use ($all_item)
            {
                $sheet->fromArray($all_item);
            });
        })->download('xls');

        
    }



    
}
