<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Model\Advertisement;
use App\Model\ItemCategoryRelation;
use App\Model\FoodItem;
use App\Model\CondimentGroup;
use App\Model\TableManager;
use App\Model\TaxManager;
use App\Model\RatingType;
use App\Model\Feedback;
use App\Model\FeedbackRating;
use App\Model\Notification;
use App\Model\User;
use App\Model\EmployeeTableAssignment;
use App\Model\OrderedItem;
use App\Model\Condiment;
use App\Model\AwayTake;
use App\Model\AwayTakeHit;
use App\Model\SocialLink;
use DB;
    class PageController extends Controller
    {   
       
        private $uploadsfolder;
        public function __construct(){
            
            $this->uploadsfolder = asset('uploads/');
            ini_set('memory_limit', '4096M');
            set_time_limit(30000000); // Extends to 5 minutes.
            
        }


        public function getCategoryList(Request $request)
        {
           
            $lists = Category::whereLevel(0)->orderBy('display_order', 'asc')->get();
            $menulist = [];
            foreach($lists as $list)
            {
                $inner_array = [];
                $inner_array['category_id'] = $list->id;
                $inner_array['menu_pic'] = $this->uploadsfolder.'/major_groups/'.$list->image;
                $inner_array['menu_title'] = strtoupper($list->name);
                $menulist[] = $inner_array;
            }

             $rows = Advertisement::whereStatus('1')->orderBy('display_order', 'asc')->get();
             $advertise = [];
             foreach($rows as $row)
             {
                $advertise[] = $this->uploadsfolder.'/advertisements/'.$row->image;
             }

             $response_array = ['status'=>true,'message'=>'Category list response','menulist'=>$menulist,'advertise'=>$advertise];

              if(isset($request->user_id))
              {
                $response_array['pending_order_count'] = $this->getPendingorderCount($request->user_id);
                $response_array['loyalty_points'] = $this->getLoyaltyPointsByUserId($request->user_id);

                $response_array['unseen_notification_count'] = $this->getUnseenNotificationCount($request->user_id);


               
              }

            return response()->json($response_array);
            
        }


        public function getSocailLinks(Request $request)
        {
           
            $lists = SocialLink::select('social_link','slug')->get();
            $linkslist = [];
            foreach($lists as $list)
            {
                $inner_array = [];
                $inner_array['slug'] = $list->slug;
                $inner_array['url'] = $list->social_link;
               
                $linkslist[] = $inner_array;
            }


             $response_array = ['status'=>true,'message'=>'Social links','data'=>$linkslist];

             

            return response()->json($response_array);
            
        }



        public function getMenuList(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'category_id' => 'required',
                            'latitude' => 'required',
                            'longitude' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',         
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $distance =  '10000000000000000000'; //default 2 km
                $lat1 = $request->latitude;
                $lon1 = $request->longitude;
                $results = DB::select(DB::raw("select id,name, ( 6371 * acos( cos( radians(".$lat1.") ) * cos( radians(latitude ) ) * cos( radians(longitude) - radians(".$lon1.")) + sin(radians(".$lat1.")) * sin( radians(latitude)))) AS distance from restaurants   order by distance limit 1") );
                if($results && $results[0]->distance <=$distance)
                {
                    $category_id = $request->category_id;
                    $category_row = Category::whereId($category_id)->first();
                    $response_array = ['status'=>true,'message'=>'menu list response','restaurant_id'=>$results[0]->id ,'restaurant_name'=>strtoupper($results[0]->name)];
                    if($category_row)
                    {
                        $menu_list = [];
                        $sMCounter= 0;
                        if($category_id =='1' || $category_id == '5')
                        {
                            foreach($category_row->getManyRelativeChilds as $sub_category)
                            {
                                $menu_list[$sMCounter]['title'] = $sub_category->getRelativeCategorysData->name;
                                $menu_items = CategoryRelation::whereParentId($sub_category->getRelativeCategorysData->id)->get();
                                foreach($menu_items as $data)
                                {
                                    $inner_array = [
                                    'menu_id'=>$data->getRelativeCategorysData->id,
                                    'pic'=> $this->uploadsfolder.'/menu_item_groups/'.$data->getRelativeCategorysData->image,
                                    'pic_thumb'=> $this->uploadsfolder.'/menu_item_groups/thumb/'.$data->getRelativeCategorysData->image,

                                    'title'=>ucfirst($data->getRelativeCategorysData->name)

                                    ];
                                    if($category_id=='1')
                                    {
                                       $inner_array['is_have_another_layout'] =$data->getRelativeCategorysData->is_have_another_layout;
                                        $inner_array['available_time'] = date('h:iA',strtotime($data->getRelativeCategorysData->available_from)).' - '.date('h:iA',strtotime($data->getRelativeCategorysData->available_to));
                                    }
                                    $menu_list[$sMCounter]['list'][]=$inner_array;
                                }
                                $sMCounter++;
                            }
                            $response_array['menu_list_detail'] = $menu_list;  
                        }

                        if($category_id=="6")
                        {
                            foreach($category_row->getManyRelativeChilds as $sub_category)
                            {
                                $menu_list[$sMCounter]['menu_id'] = $sub_category->getRelativeCategorysData->id;
                                $menu_list[$sMCounter]['pic'] = $this->uploadsfolder.'/submajorgroups/'.$sub_category->getRelativeCategorysData->image;
                                $menu_list[$sMCounter]['pic_thumb'] = $this->uploadsfolder.'/submajorgroups/'.$sub_category->getRelativeCategorysData->image;
                                $menu_list[$sMCounter]['title'] = $sub_category->getRelativeCategorysData->name;

                                $menu_list[$sMCounter]['description'] = $sub_category->getRelativeCategorysData->description;
                                $menu_list[$sMCounter]['post_date'] = date('d M Y',strtotime($sub_category->getRelativeCategorysData->created_at));
                                $sMCounter++;
                            } 
                            $response_array['offers_list'] = $menu_list;   
                        }
                        return response()->json($response_array);
                    }
                    else
                    {
                       return response()->json(['status'=>false,'message'=>'Invalid Category']); 
                    }
                }
                else
                {
                    //do not have any restro with in desired distance
                    return response()->json(['status'=>false,'message'=>'To view the Menu, you have to be within a radius of 1 Km from any Branch']);
                } 
            }
        }

        //function for get family group lists data
        public function getSubmenuLIst(Request $request)
        {
            $validator = Validator::make($request->all(), [
                           'user_id' => 'required',
                            'menu_id' => 'required',
                            'restaurant_id' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $menu_id = $request->menu_id;
                $main_parent_id = $this->getMainParentChild($menu_id);
                $response_array = ['status'=>true,'message'=>'list response'];
                $family_group_list = [];
                $sMCounter= 0;
                if($main_parent_id == '1' || $main_parent_id == '5')
                {
                    $familyGroups = CategoryRelation::whereParentId($menu_id)->get();
                    foreach($familyGroups as $groupdetail)
                    { 
                        $data = $groupdetail->getRelativeCategorysData;
                        $inner_array = [
                         'title'=>ucfirst($data->name),
                            'submenu_id'=>$data->id,
                            'pic_url'=> $this->uploadsfolder.'/family_groups/'.$data->image,
                            'pic_thumb_url'=>$this->uploadsfolder.'/family_groups/thumb/'.$data->image,
                           
                        ];
                        $family_group_list[] = $inner_array;
                        $sMCounter++;
                    }
                    sort($family_group_list);
                    $response_array['submenu_list'] = $family_group_list;
                }
                if($main_parent_id == '6')
                {
                   $familyGroups = CategoryRelation::whereParentId($menu_id)->get();
                    foreach($familyGroups as $groupdetail)
                    { 
                        $data = $groupdetail->getRelativeCategorysData;
                        $inner_array = [
                            'title'=>ucfirst($data->name),
                            'submenu_id'=>$data->id,
                            'pic_url'=> $this->uploadsfolder.'/menu_item_groups/'.$data->image,
                            'pic_thumb_url'=>$this->uploadsfolder.'/menu_item_groups/thumb/'.$data->image,
                           
                            'description'=>$data->description,
                            'price'=> (string) $data->price

                        ];
                        $family_group_list[] = $inner_array;
                        $sMCounter++;
                    }
                    sort($family_group_list);
                    $response_array['submenu_list'] = $family_group_list;
                }
                //echo '<pre>';
                //print_r($response_array);
                return response()->json($response_array);
            }
        }

        //function for get sub family group lists data
        public function getSubmenuLIstInAlcohol(Request $request)
        {
            $validator = Validator::make($request->all(), [
                           'user_id' => 'required',
                            'menu_id' => 'required',
                            'restaurant_id' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $menu_id = $request->menu_id;

                $main_parent_id = $this->getMainParentChild($menu_id);
                $is_have_another_layout = Category::whereId($menu_id)->where('is_have_another_layout','1')->first();
                if($is_have_another_layout)
                {
                    $response_array = ['status'=>true,'message'=>'list response'];
                    $family_group_list = [];
                    $sMCounter= 0;
                    if($main_parent_id == '1' || $main_parent_id == '5')
                    {
                        $familyGroups = CategoryRelation::whereParentId($menu_id)->get();
                        foreach($familyGroups as $groupdetail)
                        { 
                            $data = $groupdetail->getRelativeCategorysData;
                            $inner_array = [
                                'title'=>ucfirst($data->name),
                                'submenu_id'=>$data->id,
                                'pic_url'=> $this->uploadsfolder.'/subfamilygroups/'.$data->image,
                                'pic_thumb_url'=>$this->uploadsfolder.'/subfamilygroups/thumb/'.$data->image
                                
                            ];
                            $family_group_list[] = $inner_array;
                            $sMCounter++;
                        }
                        sort($family_group_list);
                        $response_array['submenu_list'] = $family_group_list;
                    }
                   
                    return response()->json($response_array);
                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'There is no more menu list to expand']);
                }
                
            }
        }



        public function getAppetizer(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'restaurant_id' => 'required',
                            'menu_id' => 'required',
                            'submenu_id' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',    
                           
                            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {


                $roleParameter = getUserRoleStockParameter($request->user_id);



                $menu_id = $request->menu_id;
                $main_parent_id = $this->getMainParentChild($menu_id);
                $response_array = ['status'=>true,'message'=>'list response'];
                $sMCounter= 0;
                $items=[];
                if($main_parent_id == '1' || $main_parent_id == '5' )
                {
                    $all_items = ItemCategoryRelation::whereCategoryId($request->submenu_id)->get();

                    foreach($all_items as $itemList)
                    {
                        

                        if($itemList->getRelativeitemDetail->is_available_in_stock=='1' && $itemList->getRelativeitemDetail->$roleParameter == '1')
                        {

                            $item_pic_url = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/menu_items/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';
                            $item_pic_url_thumb = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/menu_items/thumb/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';


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

                }

                 if($main_parent_id == '6')
                 {
                    $cat_detail = Category::whereId($request->submenu_id)->first();
                    $all_items = ItemCategoryRelation::whereCategoryId($request->submenu_id)->get();
                    $response_array['offer_price'] = $cat_detail->price;
                    $response_array['offer_charges'] =  $this->getTaxationForOffer($cat_detail);
                    $response_array['offer_id'] = $cat_detail->id;
                    $response_array['max_selection_limit'] = $cat_detail->max_selection_limit;
                    foreach($all_items as $itemList)
                    {

                        if($itemList->getRelativeitemDetail->is_available_in_stock=='1' && $itemList->getRelativeitemDetail->$roleParameter == '1')
                        {
                            $item_pic_url = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/menu_items/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';
                            $item_pic_url_thumb = $itemList->getRelativeitemDetail->image?$this->uploadsfolder.'/menu_items/thumb/'.$itemList->getRelativeitemDetail->image:$this->uploadsfolder.'/item_none.png';


                           $inner_array = [
                                'title'=>ucfirst($itemList->getRelativeitemDetail->name),
                                'appetizer_id'=>$itemList->getRelativeitemDetail->id,
                                'pic_url'=> $item_pic_url,
                                'pic_thumb_url'=>$item_pic_url_thumb,
                                
                                'description'=>$itemList->getRelativeitemDetail->description,
                            ];
                            $items[]=$inner_array;
                          
                            $sMCounter++;
                        }
                    }
                    sort($items);
                    $response_array['menu_list'] = $items;
                 }
                
                
                return response()->json($response_array);
            }
        }

        public function getAppetizerdetail(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'restaurant_id' => 'required',
                            'menu_id' => 'required',
                            'appetizer_id' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',         
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $item = FoodItem::whereId($request->appetizer_id)->first();
                $taxation = $this->getTaxationForItem($item);
                $item_pic_url = $item->image?$this->uploadsfolder.'/menu_items/'.$item->image:$this->uploadsfolder.'/item_none.png';
                $item_pic_url_thumb = $item->image?$this->uploadsfolder.'/menu_items/thumb/'.$item->image:$this->uploadsfolder.'/item_none.png';


                $item_arr = [
                            'appetizer_id'=>$item->id,
                            'pic_url'=> $item_pic_url,
                            'pic_thumb_url'=>$item_pic_url_thumb,
                            'title'=>ucfirst($item->name),
                            'description'=>$item->description,
                            'price'=> (string) $item->price,
                            'item_charges'=>$taxation
                        ];
                $condiment_arr = [];
                $counter = 0;
                foreach($item->getManyRelativeCondimentsGroup as $related_group)
                {
                    $condiment_group_detail =  CondimentGroup::whereId($related_group->condiment_group_id)->first();
                    $condiment_arr[$counter]['condiment_id'] = $related_group->condiment_group_id;
                    $condiment_arr[$counter]['title'] = ucfirst($condiment_group_detail->title);
                    $condiment_arr[$counter]['description'] = '';
                    $condiment_arr[$counter]['max_selection_limit'] = $condiment_group_detail->max_selection_limit;
                    $condiment_item = 0;
                    foreach($condiment_group_detail->getManyRelativeCondiments as $condiment)
                    {
                        $condiment_arr[$counter]['sub_items'][$condiment_item]['id'] = $condiment->getRelativecondimentdetail->id;
                        $condiment_arr[$counter]['sub_items'][$condiment_item]['title'] = $condiment->getRelativecondimentdetail->title;
                        $condiment_item++;
                    }
                    $counter++;
                }
                $item_arr['condiment_items'] = $condiment_arr;
                return response()->json(['status'=>true,'message'=>'Appetizerdetail response','detail'=>$item_arr]);
            }
        }

        public function getTableList(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'restaurant_id' => 'required',
                            'device_type' => 'required',
                            'device_id' => 'required',         
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $user = User::where('id',$request->user_id)->first();
                $all_assigned_table =  EmployeeTableAssignment::pluck('user_id','table_manager_id')->toArray();
                $table_array = [];
                $already_booked = [];

                if($user->role_id == '11')
                {
                    //my code

                      /* $all_tables = TableManager::where('booking_status','FREE')->where('status','1')->where('restaurant_id',$request->restaurant_id)->get();*/
                    $all_tables = TableManager::where('status','1')->where('restaurant_id',$request->restaurant_id)->get();
                    $counter = 0;
                    foreach($all_tables as $table)
                    {
                        if(isset($all_assigned_table[$table->id]))
                        {
                            $table_array[$counter]['Table_id'] = $table->id;
                            $table_array[$counter]['Table_no'] = $table->name;
                            $counter++;
                        } 
                    }

                   
                    $all_pre_booked_tables = TableManager::where('booking_status','BLOCKED')->where('status','1')->where('restaurant_id',$request->restaurant_id)
                        ->where('booking_for_user_id',$request->user_id)
                        ->get();
                        $acounter = 0;
                    foreach($all_pre_booked_tables as $btable)
                    {
                        if(isset($all_assigned_table[$btable->id]))
                        {
                            $already_booked[$acounter]['Table_id'] = $btable->id;
                            $already_booked[$acounter]['Table_no'] = $btable->name;
                            $acounter++;
                        }
                    }
                    //my code
                }
                if($user->role_id == '4')
                {
                   $counter = 0;
                    $all_tables = TableManager::where('status','1')->where('restaurant_id',$request->restaurant_id)->get();
                    foreach($all_tables as $table)
                    {
                        if(isset($all_assigned_table[$table->id]) && $all_assigned_table[$table->id] == $user->id)
                        {
                            $table_array[$counter]['Table_id'] = $table->id;
                            $table_array[$counter]['Table_no'] = $table->name;
                            $table_array[$counter]['unbilled_amount'] = manageAmountFormat(getUnbilledAmountByTableId($table->id));
                            $counter++;
                        } 
                    } 
                }

              
               
              
               


                return response()->json(['status'=>true,'message'=>'list response',
                    'Table_List'=>$table_array,'Already_booked'=>$already_booked

                    ]);
            }
        }

        public function tableStatus(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'restaurant_id' => 'required',
                        'Table_id' => 'required',
                        'booking_status' => 'required',
                        'device_type' => 'required',
                        'device_id' => 'required',                   
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 

                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $my_table = TableManager::where('id',$request->Table_id)->first();
                if($my_table)
                {
                    $my_table->booking_status = $request->booking_status;
                    $user_id = $request->user_id;
                    $booking_for_user_id = null;
                    if($my_table->booking_status=='BLOCKED')
                    {
                        $booking_for_user_id = $user_id; 
                    }
                    $my_table->booking_for_user_id = $booking_for_user_id;
                    $my_table->save();
                }
                
                return response()->json(['status'=>true,'message'=>'Table status changed successfully']);
            }
        }

        public function getRestaurantChargesDetail(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'restaurant_id' => 'required',
                        'device_type' => 'required',
                        'device_id' => 'required',                   
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $all_taxes = TaxManager::where('status','1')->get();
                $charges_array = [];
                $counter = 0;
                foreach($all_taxes as $taxes)
                {
                    $charges_array[$counter]['charges_name'] = ucfirst($taxes->title);
                    $charges_array[$counter]['charges_value'] = $taxes->tax_value;
                    $charges_array[$counter]['charges_format'] = $taxes->tax_format;
                    $counter++;
                }

                
                return response()->json(['status'=>true,'message'=>'list response','charges_List'=>$charges_array]);
            }
        }


        

       public function getMainParentChild($child_id) 
       {
            $menu_items = CategoryRelation::whereCategoryId($child_id)->first();
            if ($menu_items) 
            {
                $child_data = $this->getMainParentChild($menu_items->parent_id);
                $child_id = $child_data;
            }
            return $child_id;
        }

        public function getRatingTypes(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'rating_for' => 'required',
                                       
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
               
               $all_rating_types= RatingType::where('rating_for',$request->rating_for)->get();
               $rating_arr = [];
               $c = 0;
               foreach($all_rating_types as $data)
               {
                    $rating_arr[$c]['rating_type_id'] = $data->id;
                    $rating_arr[$c]['title'] = ucfirst($data->title);
                    $rating_arr[$c]['image'] = $this->uploadsfolder.'/ratingtypes/'.$data->image;
                    $c++;
               }
                
                $response_array  =['status'=>true,'message'=>'list response','rating_types'=>$rating_arr];
               if(isset($request->latitude) && isset($request->longitude))
               {


                $distance =  '1'; //default 2 km
                $lat1 = $request->latitude;
                $lon1 = $request->longitude;
                $results = DB::select(DB::raw("select id,name, ( 6371 * acos( cos( radians(".$lat1.") ) * cos( radians(latitude ) ) * cos( radians(longitude) - radians(".$lon1.")) + sin(radians(".$lat1.")) * sin( radians(latitude)))) AS distance from restaurants   order by distance limit 1") );
                if($results && $results[0]->distance <=$distance)
                {
                    $response_array['restaurant_id'] = $results[0]->id;
                    $response_array['restaurant_name'] = strtoupper($results[0]->name);
                }
                    

               }
                
                return response()->json($response_array);
            }
        }

        public function setRatingByUser(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'rating_status'=>'required', //Y for yes N for Skipped
                        'user_id' => 'required',
                        'rating_for' => 'required',//O for order and R for Restro
                        'rating_for_id' => 'required',//it can be order id or restaurant id                 
                ]);
            $msg = 'Invalid Credentials';

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
               $msg = 'Thank you for your valuable feedback';
              if($request->rating_status == "Y")
              {
                //rating given
                if(isset($request->ratingJson) && $request->ratingJson != '')
                {

                    $json = json_decode($request->ratingJson);


                    $feedback =  new Feedback();
                    $feedback->feedback = $json->feedback_msg;
                    $feedback->status = 'Y';
                    $feedback->user_id = $request->user_id;

                    if($request->rating_for == 'O')
                    {
                        $feedback->order_id = $request->rating_for_id;
                    }
                    if($request->rating_for == 'R')
                    {
                        $feedback->restaurant_id = $request->rating_for_id;
                    }
                    $feedback->save();

                    $feedback_id =  $feedback->id;
                    //echo "<pre>";print_r(json_decode($json->ratings)); die;
                    foreach($json->ratings as $rating)
                    {
                       // dd($rating);
                       $feedback_rating = new FeedbackRating();
                       $feedback_rating->feedback_id = $feedback_id;
                       $feedback_rating->rating_type_id = $rating->rating_type_id;
                       $feedback_rating->rating = $rating->rating_number;
                       $feedback_rating->save();
                    }  
                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Rating json is required']);
                }
              }
              else
              {
                $msg = 'Skipped';
                //skipped
                if($request->rating_for == 'O')
                {
                    $feedback =  new Feedback();
                    $feedback->order_id = $request->rating_for_id;
                    $feedback->status = 'N';
                    $feedback->user_id = $request->user_id;
                    $feedback->save();
                }
                
              }
                return response()->json(['status'=>true,'message'=>$msg]);
            }
        }

        public function getNotification(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required'              
                ]);
            $msg = 'Invalid Credentials';

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
               $rows =  Notification::where('user_id',$request->user_id)
                            ->orderBy('id','desc')
                            ->get();
               $notification_rows = [];
               $c= 0;
               foreach($rows as $row)
               {
                    $notification_rows[$c]['id'] = $row->id;
                    $notification_rows[$c]['title'] = $row->title;
                    $notification_rows[$c]['message'] = $row->message;
                    $notification_rows[$c]['created_at'] = strtotime($row->created_at);
                    $notification_rows[$c]['is_seen'] = $row->is_seen=='1'?true:false;
                    $c++;
               }
                $response_array  =['status'=>true,'message'=>'list response','notifications'=>$notification_rows];
                 return response()->json($response_array);
            }
        }

        public function setNotificationSeen(Request $request)
        {
            $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'notification_ids'=>'required'              
                ]);
             if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $all_notification_ids = explode(',',$request->notification_ids);
                if(count($all_notification_ids)>0)
                {
                    Notification::whereIn('id',$all_notification_ids)->update(['is_seen'=>'1']);
                }
             $response_array  =['status'=>true,'message'=>'Status updated successfully'];
              return response()->json($response_array);
            }
        }


        public function getItemSellReportWithPlu(Request $request)
        {
           
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','created_at','billing_time','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->where('order_offer_id',null)
                ->whereHas('getrelatedOrderForItem',function ($sql_query) {  
                                                $sql_query->where('order_type', 'PREPAID');
                                                })
            ;

            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('billing_time','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_item = $all_item->where('billing_time','<=',$request->input('end-date'));   
            }
            $data  = $all_item->get();
            $detail = [];
            $charges_names = [];
            $pluNumberList = $this->getPluList();
           foreach($data as $row)
           {
                
                if($row->getAssociateFooditem->plu_number && isset($pluNumberList[$row->getAssociateFooditem->plu_number])){
                $total_charges= [];
                $key = $row->food_item_id;
                $key = $key.'-'.date('Ymd',strtotime($row->billing_time));

                $get_charge = true;
                if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                {
                $row->price = 0;
                $get_charge = false;

                }
                $detail[$key]['Item'] = $row->getAssociateFooditem->name;
                $detail[$key]['Date'] = date('Y-m-d H:i:s',strtotime($row->billing_time));
                $detail[$key]['PLU_Number'] = $row->getAssociateFooditem->plu_number;
                $detail[$key]['PLU_Name'] = $pluNumberList[$row->getAssociateFooditem->plu_number];
                if(isset($detail[$key]['SalesQty']))
                {
                    $detail[$key]['SalesQty'] = $row->item_quantity+$detail[$key]['SalesQty'];
                }
                else
                {
                    $detail[$key]['SalesQty'] = $row->item_quantity;
                }


                if(isset($row->getAssociateFooditem->getManyRelativePrintClasses) && count($row->getAssociateFooditem->getManyRelativePrintClasses)>0)
                {
                    $cc =1;
                    foreach($row->getAssociateFooditem->getManyRelativePrintClasses as $pint_class)
                    {
                        if($cc == 1)
                        {
                           $detail[$key]['Print_class_id'] = $pint_class->getAssociatePrintClass->id;
                            $detail[$key]['Print_class_name'] = $pint_class->getAssociatePrintClass->name; 
                             $cc++;
                        }
                       
                    } 
                }



                            

            if(isset($detail[$key]['gross_sale']))
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
            }
            else
            {
                $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
            }
            $charges_arr = json_decode($row->item_charges);

            if(count($charges_arr)>0 && $get_charge == true)
            {

                foreach($charges_arr as $ch)
                {
                    if(isset($ch->charged_amount))
                    {
                        $total_charges[] = $ch->charged_amount;

                        $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                        if(isset($detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))]))
                        {
                             $detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))] = round($detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount,2);
                        }
                        else
                        {
                            $detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))] = round($ch->charged_amount,2);
                        }
                       
                    }
                }

                if(isset($detail[$key]['total_charges']))
                {
                        $detail[$key]['total_charges'] = round(array_sum($total_charges)+$detail[$key]['total_charges'],2);
                }
                else
                {
                    $detail[$key]['total_charges'] = round(array_sum($total_charges),2);
                }
            }

            if(!isset($detail[$key]['total_charges']))
            {
                $detail[$key]['total_charges'] = '0';
            }
            $detail[$key]['net_sale'] = round($detail[$key]['gross_sale']- $detail[$key]['total_charges'],2);

            //unset($detail[$key]['total_charges']);
            }
            
           }

            $response_array  =['status'=>true,'message'=>'listings','data'=>array_values($detail)];
            return response()->json($response_array);
           
        }

        public function getFamilyGroupSellReportWithGl(Request $request)
        {
            $all_item =  OrderedItem::select(['food_item_id','price','item_quantity','item_charges','created_at','billing_time','order_id'])->whereNotIn('item_delivery_status',['CANCLED','PENDING'])->where('order_offer_id',null)
            ->whereHas('getrelatedOrderForItem',function ($sql_query) {  
                                                $sql_query->where('order_type', 'PREPAID');
                                                })
            ;
            if ($request->has('start-date'))
            {
                $all_item = $all_item->where('billing_time','>=',$request->input('start-date'));
            }
            if ($request->has('end-date'))
            {
                $all_item = $all_item->where('billing_time','<=',$request->input('end-date'));
            }
            $data  = $all_item->get();
            $detail = [];
            $getGLDetail = $this->getGLDetail();
            foreach($data as $row)
           {
               
                $food_item = FoodItem::select('id')->where('id',$row->food_item_id)->first();
                $familyGroup = $food_item->getItemCategoryRelation->getRelativecategoryDetail;

                if($familyGroup->gl_account_no && isset($getGLDetail[$familyGroup->gl_account_no]))
                {
                    $total_charges= [];
                    $total_dicount_to_order = 0;
                    $key  = $familyGroup->id;
                    $key = $key.'-'.date('Ymd',strtotime($row->billing_time));

                    $get_charge = true;
                    if($row->getrelatedOrderForItem->complimentry_code && $row->getrelatedOrderForItem->complimentry_code>0)
                    {
                        $row->price = 0;
                        $get_charge = false;

                    }
                    $detail[$key]['title'] = $familyGroup->name;
                    $detail[$key]['gl_code'] = $familyGroup->gl_account_no;
                    $detail[$key]['gl_name'] =$getGLDetail[$familyGroup->gl_account_no];
                    $detail[$key]['Date'] = date('Y-m-d H:i:s',strtotime($row->created_at));
                    if(isset($detail[$key]['gross_sale']))
                    {
                        $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price)+$detail[$key]['gross_sale'];
                    }
                    else
                    {
                        $detail[$key]['gross_sale'] = ($row->item_quantity*$row->price);
                    }


                    $total_sale_amount = $row->item_quantity*$row->price;

                   



                    if($row->getrelatedOrderForItem->admin_discount_in_percent && $row->getrelatedOrderForItem->admin_discount_in_percent>0)
                    {

                        $total_dicount_to_order = ($row->getrelatedOrderForItem->admin_discount_in_percent*$total_sale_amount)/100;
                    }




                     if(isset($detail[$key]['admin_discount']))
                    {
                        $detail[$key]['admin_discount'] =$total_dicount_to_order+$detail[$key]['admin_discount'];
                    }
                    else
                    {
                        $detail[$key]['admin_discount'] = $total_dicount_to_order;
                    }


                    //admin_discount_in_percent
                    //dd();





                    $charges_arr = json_decode($row->item_charges);

                if(count($charges_arr)>0 && $get_charge == true)
                {

                    foreach($charges_arr as $ch)
                    {
                        if(isset($ch->charged_amount))
                        $total_charges[] = $ch->charged_amount;

                        $charges_names[str_replace(' ','_',strtolower($ch->charges_name))] = str_replace(' ','_',strtolower($ch->charges_name)); 

                        if(isset($detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))]))
                        {
                             $detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))] = round($detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))]+$ch->charged_amount,2);
                        }
                        else
                        {
                            $detail[$key]['charges'][str_replace(' ','_',strtolower($ch->charges_name))] = round($ch->charged_amount,2);
                        }
                    }
                    if(isset($detail[$key]['total_charges']))
                    {
                            $detail[$key]['total_charges'] = round(array_sum($total_charges)+$detail[$key]['total_charges'],2);
                    }
                    else
                    {
                        $detail[$key]['total_charges'] = round(array_sum($total_charges),2);
                    }
                }

                if(!isset($detail[$key]['total_charges']))
                {
                    $detail[$key]['total_charges'] = '0';
                }
                $detail[$key]['net_sale'] = round($detail[$key]['gross_sale']- $detail[$key]['total_charges'],2);

                $detail[$key]['net_sale'] = round($detail[$key]['net_sale']- $detail[$key]['admin_discount'],2);


                }
           }
            $response_array  =['status'=>true,'message'=>'listings','data'=>array_values($detail)];
            return response()->json($response_array);
        }


    public function getCondimentSellReportWithPlu(Request $request)
    {
        $all_item =  OrderedItem::select(['order_id','condiments_json','item_quantity','created_at'])->where('item_delivery_status','COMPLETED')->where('condiments_json','!=',null)->where('condiments_json','!=','[]')
            ->whereHas('getrelatedOrderForItem',function ($sql_query) {  
                                                $sql_query->where('order_type', 'PREPAID');
                                                })
        ;
        if ($request->has('start-date'))
        {
            $all_item = $all_item->where('created_at','>=',$request->input('start-date'));
        }
        if ($request->has('end-date'))
        {
            $all_item = $all_item->where('created_at','<=',$request->input('end-date'));
        }
        $data  = $all_item->orderBy('order_id','desc')->get();
        $detail = [];
        $pluNumberList = $this->getPluList();   
        foreach($data as $row)
        {
            $condiments_json = json_decode($row->condiments_json);
            foreach($condiments_json as $sub_items)
            {
                if(isset($sub_items->sub_items) && count($sub_items->sub_items)>0)
                {
                    foreach($sub_items->sub_items as $condiment)
                    {
                        $condiment_detail = Condiment::where('id',$condiment->id)->where('plu_number','!=',null)->first();
                        if($condiment_detail && isset($pluNumberList[$condiment_detail->plu_number]))
                        {
                            $key = $condiment->id;
                            $key = $key.'-'.date('Ymd',strtotime($row->created_at));
                            $detail[$key]['title'] = $condiment->title;
                            $detail[$key]['plu_number'] =$condiment_detail->plu_number;
                            $detail[$key]['plu_name'] = $pluNumberList[$condiment_detail->plu_number];
                            $detail[$key]['Date'] = date('Y-m-d H:i:s',strtotime($row->created_at));
                            if(isset($detail[$key]['item_total_quantity']))
                            {
                                $detail[$key]['item_total_quantity'] = $row->item_quantity+$detail[$key]['item_total_quantity'];
                            }
                            else
                            {
                                $detail[$key]['item_total_quantity'] = $row->item_quantity;
                            }
                        }
                    }
                }
            }
        }
        sort($detail);
        $response_array  =['status'=>true,'message'=>'listings','data'=>array_values($detail)];
        return response()->json($response_array);
    }

    public function searchItem(Request $request)
    {
         $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'search_text'=>'required'              
                ]);
             if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
              



              $user_id =   $request->user_id;

               $roleParameter = getUserRoleStockParameter($request->user_id);

             $user_detail =  User::whereId($user_id)->first();
			$search_text = $request->search_text;
			$all_item = FoodItem::Where('name', 'like', '%' . $search_text . '%')->where('is_general_item','1')->where('status','1')
                ->where('is_available_in_stock','1')
                 ->where($roleParameter,'1')

                ->where('is_deleted','0')->get();
			$items=[];
			foreach($all_item as $itemList)
			{
				$item_pic_url = $itemList->image?$this->uploadsfolder.'/menu_items/'.$itemList->image:$this->uploadsfolder.'/item_none.png';
				$item_pic_url_thumb = $itemList->image?$this->uploadsfolder.'/menu_items/thumb/'.$itemList->image:$this->uploadsfolder.'/item_none.png';
				$inner_array = [
				'menu_id'=>1,
				'title'=>ucfirst($itemList->name),
				'appetizer_id'=>$itemList->id,
				'pic_url'=> $item_pic_url,
				'pic_thumb_url'=>$item_pic_url_thumb,
				'description'=>$itemList->description,
				'price'=> (string) $itemList->price,
				];
				$items[]=$inner_array;
			}
			$response_array  =['status'=>true,'message'=>'Item listing'];
			sort($items);
			$response_array['menu_list'] = $items;

             if($user_detail && $user_detail->role_id == '4')
             {	

				return response()->json($response_array);
             }
             else
             {
             	//user case
             	if(isset($request->latitude) && $request->latitude != "" && isset($request->longitude) && $request->longitude != "")
             	{

             		$distance = 1;
					$lat1 = $request->latitude;
					$lon1 = $request->longitude;
					$results = DB::select(DB::raw("select id,name, ( 6371 * acos( cos( radians(".$lat1.") ) * cos( radians(latitude ) ) * cos( radians(longitude) - radians(".$lon1.")) + sin(radians(".$lat1.")) * sin( radians(latitude)))) AS distance from restaurants   order by distance limit 1") );
					if($results && $results[0]->distance <=$distance)
					{
						$response_array['restaurant_id'] = $results[0]->id;
						$response_array['restaurant_name'] = $results[0]->name;
						return response()->json($response_array);
					}
					else
					{
						//do not have any restro with in desired distance
                    	return response()->json(['status'=>false,'message'=>'To view the Menu, you have to be within a radius of 1 Km from any Branch']);
					}

             	}
             	else
             	{
             		return response()->json(['status'=>false,'message'=>'Location is required']);
             	}

             		 
             }

              
            }
    }

    public function getTakeAwayList(Request $request)
    {
        $rows = AwayTake::get();
        $data=[];
        foreach($rows as $row)
        {
            $inner_array = [
            'title'=>$row->title,'url'=>$row->url,'take_away_id'=>$row->id
            ];
            $data[$row->restaurant_id]['restaurant_id']= $row->restaurant_id;
             $data[$row->restaurant_id]['restaurant_location']= $row->getAssociateRestro->location;
            $data[$row->restaurant_id]['restaurant_name']= $row->getAssociateRestro->name;
             $data[$row->restaurant_id]['restaurant_image']=$this->uploadsfolder.'/restaurants/'.$row->getAssociateRestro->image;
             $data[$row->restaurant_id]['restaurant_image_thumb']=$this->uploadsfolder.'/restaurants/thumb/'.$row->getAssociateRestro->image;
            $data[$row->restaurant_id]['lisitng'][]= $inner_array;
        }
        $response_array  =['status'=>true,'message'=>'Listing','data'=>array_values($data)];
        return response()->json($response_array);
      
    }


    public function addTakeAwayHit(Request $request)
    {
        $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'take_away_id'=>'required'              
                ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {

            $row   =  new AwayTakeHit();
            $row->user_id= $request->user_id;
             $row->away_take_id= $request->take_away_id;
             $row->save();
             $response_array  =['status'=>true,'message'=>'Added successfully'];
            return response()->json($response_array);
        }
    }



    



    }  