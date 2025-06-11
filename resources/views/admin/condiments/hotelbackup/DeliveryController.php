<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Model\BeerKegCategory;
use App\Model\BeerAndKegCategoryRelation;
use App\Model\BeerItemsAndCategoryRelation;
use App\Model\BeerDeliveryItem;

use App\Model\DeliveryOrder;

use App\Model\DeliveryOrderItem;






use DB;
class DeliveryController extends Controller
{   
   
    private $uploadsfolder;
    public function __construct()
    {
        
        $this->uploadsfolder = asset('uploads/');
        ini_set('memory_limit', '4096M');
        set_time_limit(30000000); // Extends to 5 minutes.
    }


    public function getDeliverySubMajorGroup()
    {
        $lists = BeerKegCategory::whereLevel(1)->orderBy('display_order', 'asc')->get();
        $menulist = [];
        foreach($lists as $list)
        {
            $inner_array = [];
            $inner_array['submajorgroup_id'] = $list->id;
            $inner_array['pic'] = $this->uploadsfolder.'/beerandkeg/'.$list->image;
             $inner_array['pic_thumb'] = $this->uploadsfolder.'/beerandkeg/'.$list->image;
            $inner_array['title'] = strtoupper($list->name);
            $menulist[] = $inner_array;
        }
        $response_array = ['status'=>true,'message'=>'Sub major groups list','data'=>$menulist];
        return response()->json($response_array);
    }

    public function getDeliveryFamilyGroups(Request $request)
    {

         $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'submajorgroup_id' => 'required',             
            ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {
            $lists = BeerKegCategory::whereId($request->submajorgroup_id)->first();
            $menulist = [];
            if($lists && count($lists->getManyRelativeChilds)>0)
            {
                foreach($lists->getManyRelativeChilds as $list)
                {
                    $inner_array = [];
                    $inner_array['family_group_id'] = $list->getRelativeCategorysData->id;
                    $inner_array['pic'] = $this->uploadsfolder.'/beerandkeg/'.$list->getRelativeCategorysData->image;
                     $inner_array['pic_thumb'] = $this->uploadsfolder.'/beerandkeg/'.$list->getRelativeCategorysData->image;
                    $inner_array['title'] = strtoupper($list->getRelativeCategorysData->name);
                    $inner_array['is_have_sub_family'] = (int)$list->getRelativeCategorysData->is_have_another_layout;
                    $menulist[] = $inner_array;
                }
            }
            $response_array = ['status'=>true,'message'=>'Family groups list','data'=>$menulist];
            return response()->json($response_array);
        }
    }

    public function getDeliverySubFamilyGroups(Request $request)
    {

        $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'family_group_id' => 'required',                      
            ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {
            $lists = BeerKegCategory::whereId($request->family_group_id)->first();
            $menulist = [];
            if($lists && count($lists->getManyRelativeChilds)>0)
            {
                foreach($lists->getManyRelativeChilds as $list)
                {
                    $inner_array = [];
                    $inner_array['sub_family_group_id'] = $list->getRelativeCategorysData->id;
                    $inner_array['pic'] = $this->uploadsfolder.'/beerandkeg/'.$list->getRelativeCategorysData->image;
                    $inner_array['pic_thumb'] = $this->uploadsfolder.'/beerandkeg/'.$list->getRelativeCategorysData->image;
                    $inner_array['title'] = strtoupper($list->getRelativeCategorysData->name);
                    $menulist[] = $inner_array;
                } 
            }
            $response_array = ['status'=>true,'message'=>'Sub Family groups list','data'=>$menulist];
            return response()->json($response_array);
        }
    }

    public function getDeliveryAppetizer(Request $request)
    {
        $validator = Validator::make($request->all(), [
                        'family_or_sub_family_group_id' => 'required', 
                       // 'restaurant_id' => 'required',   
                ]);

        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {

            $beer_keg_category_id = $request->family_or_sub_family_group_id;
            $all_items = BeerItemsAndCategoryRelation::where('beer_keg_category_id',$beer_keg_category_id)->get();
           $response_array = ['status'=>true,'message'=>'list response'];
            $sMCounter= 0;
            $items=[];

            foreach($all_items as $itemList)
            {
                if($itemList->getRelativeitemDetail->is_available_in_stock=='1')
                {

                    $item_pic_url = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/beerandkeg/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';
                    $item_pic_url_thumb = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/beerandkeg/thumb/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';


                   $inner_array = [
                        'title'=>ucfirst($itemList->getRelativeitemDetail->name),
                        'appetizer_id'=>$itemList->getRelativeitemDetail->id,
                        'pic_url'=> $item_pic_url,
                        'pic_thumb_url'=>$item_pic_url_thumb,
                        
                        'description'=>$itemList->getRelativeitemDetail->description,
                        'price'=> (string) $itemList->getRelativeitemDetail->price,

                    ];
                    $items[]=$inner_array;
                  
                    $sMCounter++;
                }
            }
            sort($items);
            $response_array['menu_list'] = $items;
            return response()->json($response_array);
        }
    }

        public function getDeliveryAppetizerdetail(Request $request)
        {
             $validator = Validator::make($request->all(), [
                           // 'restaurant_id' => 'required',
                            'appetizer_id' => 'required',
                            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $item = BeerDeliveryItem::whereId($request->appetizer_id)->first();
                $taxation = $this->getTaxationForItem($item);
                $item_pic_url = $item->image?$this->uploadsfolder.'/beerandkeg/'.$item->image:$this->uploadsfolder.'/item_none.png';
                $item_pic_url_thumb = $item->image?$this->uploadsfolder.'/beerandkeg/thumb/'.$item->image:$this->uploadsfolder.'/item_none.png';
                $item_arr = [
                            'appetizer_id'=>$item->id,
                            'pic_url'=> $item_pic_url,
                            'pic_thumb_url'=>$item_pic_url_thumb,
                            'title'=>ucfirst($item->name),
                            'description'=>$item->description,
                            'price'=> (string) $item->price,
                            'item_charges'=>$taxation
                        ];



              
                return response()->json(['status'=>true,
                    'message'=>'Appetizerdetail response','detail'=>$item_arr]);
            }
        }


        public function getCheckoutForBeerDelivery(Request $request)
        {
              $validator = Validator::make($request->all(), [ 
                    'user_id' => 'required',
                    'total_price' => 'required',
                    'address'=>'required',
                   
                    'checkout_json'=>'required'
                            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $json = json_decode($request->checkout_json);
                
                $new_order = new DeliveryOrder();
                $new_order->user_id = $request->user_id;
                $new_order->final_comment = isset($request->final_comment)?$request->final_comment:'';
                $new_order->order_final_price = $request->total_price;
                 $new_order->address = $request->address;
                $new_order->slug = rand(99,999).strtotime(date('Y-m-d h:i:s'));
                 if(isset($json->order_charges))
               {
                    $new_order->order_charges = json_encode($json->order_charges);
                  
               }


                $new_order->save();

                $order_id = $new_order->id;

                 if(isset($json->Appetizerdata) && count($json->Appetizerdata)>0)
               {
               
                    $this->storeDeliveryItemForOrder($json->Appetizerdata,$order_id);
               }
                return response()->json(['status'=>true,'message'=>'Your order added successfully.','delivery_order_id'=>$new_order->id]);

            }
        }


    public function storeDeliveryItemForOrder($items_array,$order_id)
    {
        //OrderedItem

        $items_array_for_insert = [];
        foreach($items_array as $item)
        {
            $inner_array=[
              'beer_delivery_item_id'=>$item->appetizer_id,
              'price'=>$item->price,
             
              'item_title'=>$item->title,
              'item_comment'=>isset($item->comment)?$item->comment:'',
              'item_quantity'=>$item->quantity,
              'delivery_order_id'=>$order_id,
             
              'created_at'=>date('Y-m-d h:i:s'),
              'updated_at'=>date('Y-m-d h:i:s'),
              'item_charges'=>isset($item->item_charges)?json_encode($item->item_charges):null,
             
            ];

           
            $items_array_for_insert[] = $inner_array;
        }
       DeliveryOrderItem::insert($items_array_for_insert);
    }

}  