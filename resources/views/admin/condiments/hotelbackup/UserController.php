<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Model\User;
use App\Model\LoyaltyPoint;
use App\Model\UserDevice;
use App\Http\Requests\Admin\UserAddRequest;
use JWTAuthException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Model\Restaurant;
use App\Model\PrintClassUser;
use App\Model\PrintClass;
use App\Model\Order;
use App\Model\EmployeeTableAssignment;
use App\Model\OrderedItem;
use App\Model\WaiterTip;
use App\Model\OrderBookedTable;
use App\Model\HistoryPrintDocket;
use DB;
use App\Model\Feedback;
use App\Model\DjRequest;
use App\Model\Reservation;
use App\Model\ReservationEmail;
use App\Model\UserCardDetail;
use App\Model\Wallet;
use App\Model\WalletTransaction;
use App\Model\WaStockMove;
use App\Model\WaNumerSeriesCode;
use App\Model\WaSoldButUnbookedItem;





    class UserController extends Controller
    {   
        private $user;
        private $uploadsfolder;
        public function __construct(User $user){
            $this->user = $user;
            $this->uploadsfolder = asset('uploads/');
            
        }
       
        public function registerForApiKey(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'name' => 'required|max:255',
                            'password' => 'required|max:30|min:6',
                            'phone_number' => 'required|numeric|unique:users',
                            'email' => 'required|email|max:255|unique:users'
                ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                $user = (object)[]; 
                return response()->json(['status'=>false,'message'=>$error,'userDetail'=>$user]);
            }
            else
            {
                $row = new User();
                $row->name= $request->name;
                $row->role_id=10;
                $row->email=$request->email;
                $row->phone_number = $request->phone_number;
                $row->password=bcrypt($request->password);
                $row->status='1';
                $row->save();
                $user = $row;
                return response()->json(['status'=>true,'message'=>'User created successfully','userDetail'=>$user]);
            }
           
        }

        public function getWalletBalance(Request $request)
        {
            

            $validator = Validator::make($request->all(), [    
                        'phone_number' => 'required',        
                ]);

            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {  

                $data['balance'] = $this->getWalletBalanceByPhoneNumber($request->phone_number);
                return response()->json(['status'=>true,'message'=>'Wallet amount','data'=>$data]);
            }
        } 

         public function addWalletBalanceByLoyalityPoint(Request $request)
        {
            

            $validator = Validator::make($request->all(), [    
                        'user_id' => 'required',  
                       
                        'loyality_points' => 'required',           
                ]);

            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {  

               
              
                    $user =  User::where('id',$request->user_id)->first();
                    if($user)
                    {
                        $haveLoyaltYPoint = $this->getLoyaltyPointsByUserId($request->user_id);

                        //dd($haveLoyaltYPoint);
                        if($request->loyality_points <= $haveLoyaltYPoint)
                        {
                            $row = new WalletTransaction();
                            $row->phone_number = $user->phone_number;
                            $row->entry_type = 'Loyalty Top Up';
                            $row->amount = $request->loyality_points;
                            $row->user_id = $user->id;
                            $row->save();
                            $this->updateWalletAmount($user->phone_number);
                            $spentLoyaltyPoint =  new LoyaltyPoint();
                            $spentLoyaltyPoint->wallet_transaction_id = $row->id;
                            $spentLoyaltyPoint->points = $request->loyality_points;
                            $spentLoyaltyPoint->points_source = 'TOPUP';
                             $spentLoyaltyPoint->user_id = $request->user_id;
                            $spentLoyaltyPoint->status = 'SPENT';
                            $spentLoyaltyPoint->save();




                             $haveLoyaltYPoint = $this->getLoyaltyPointsByUserId($request->user_id);
                             return response()->json(['status'=>true,'message'=>'Points redeemed successfully.','loyality_points'=>$haveLoyaltYPoint]);
                           
                        }
                        else
                        {
                            return response()->json(['status'=>false,'message'=>'Do not have enough points to add in wallet.']);
                        }
                       
                        

                    }
                    else
                    {
                        return response()->json(['status'=>false,'message'=>'Invalid User']);
                    }

              

            }
        }



        public function addCardDetailsForCustomer(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'card_name' => 'required|max:255',
                            'phone_number' => 'required',
                            'email' => 'required|email|max:255',
                            
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {  

                $row = UserCardDetail::where('user_id',$request->user_id)->first();
                if(!$row)
                {
                   $row =  new UserCardDetail();
                }
                $row->user_id = $request->user_id;
                $row->card_name = $request->card_name;
                $row->phone_number = $request->phone_number;
                $row->email = $request->email;
                $row->save();
                return response()->json(['status'=>true,'message'=>'Card added successfully']);
            }
        }

        public function deleteCardDetailsForCustomer(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                           
                            
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {  

                $row = UserCardDetail::where('user_id',$request->user_id)->delete();
                return response()->json(['status'=>true,'message'=>'Card deleted successfully']);
            }
        }




        public function getCardDetailsForCustomer(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                                    
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {  

                $row = UserCardDetail::where('user_id',$request->user_id)->first();
                if($row)
                {
                    $data = [
                        "card_name"=>$row->card_name,
                        "phone_number"=>$row->phone_number,
                        "email"=>$row->email
                    ];
                    return response()->json(['status'=>true,'message'=>'Card details','data'=>$data]);
                }
                else
                {
                     return response()->json(['status'=>false,'message'=>'Do not have any card detail.']);
                }  
            }
        }
        
        public function getApiKey(Request $request)
        {
            $credentials = $request->only('email', 'password');
            $token = null;
            try {
               if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['invalid_email_or_password'], 422);
               }
            } catch (JWTAuthException $e) {
                return response()->json(['failed_to_create_token'], 500);
            }
            return response()->json(compact('token'));
        }




        public function signupCheck(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'name' => 'required|max:255',
                            'email' => 'required|email|max:255|unique:users',
                            //'dob'=>'required',
                            'phone_number' => 'required|numeric|unique:users',
                            
                ]);

            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                $user = (object)[]; 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $otp = $this->randomOtp(4);
                //$otp  = '1234';
                //send otp code start from here
                $otp_is_sent = $this->sendOtpForUsers($otp,$request->phone_number,$request->name);
                //send otp code end from here
                return response()->json(['status'=>true,'message'=>'Please verify the OTP','otp'=>$otp,'otp_is_sent'=>$otp_is_sent]);
            }
        }

        public function signup(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'name' => 'required|max:255',
                            'email' => 'required|email|max:255|unique:users',
                            //'dob'=>'required',
                            'phone_number' => 'required|numeric|unique:users',
                            //'gender'=>'required',
                            'nationality'=>'required',
                            
                            
                            'password' => 'required|max:30|min:6',
                            'device_type'=>'required',
                            'device_id'=>'required',
                            'profile_pic' =>  'mimes:jpeg,jpg,png',
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {

                $row = new User();
                $row->name= $request->name;
                $row->role_id=11;
                $row->email=$request->email;
               
                $row->nationality = $request->nationality;
                $row->phone_number = $request->phone_number;
                $row->password = bcrypt($request->password);
                $row->status='1';
                if(isset($request->dob) && $request->dob != '')
                {
                    $row->dob = convertDMYtoYMD($request->dob);
                }

                 if(isset($request->gender) && $request->gender != '')
                {
                    $row->gender = $request->gender;
                }
                
                if($request->file('profile_pic'))
                {
                    $file = $request->file('profile_pic');
                    $image = uploadwithresize($file,'users','186');
                    $row->image= $image;
                }
                
                $row->save();

                $this->giveLoyalityzpointForSignup($row->id);
                $this->manageDeviceIdAndToken($row->id,$request->device_id,$request->device_type,'add');
                $user = $this->getuserdetailfromObjectArray($row);
                return response()->json(['status'=>true,'message'=>'Registration successfully','userdetails'=>$user]);
            }
        }

        public function getuserdetailfromObjectArray($row)
        {
            $user = (object)array(
                    'userid'=>$row->id,
                    'profile_pic'=>$row->image?$this->uploadsfolder.'/users/'.$row->image:$this->uploadsfolder.'/nouser.jpg',
                    'profile_pic_thumb'=>$row->image?$this->uploadsfolder.'/users/thumb/'.$row->image:$this->uploadsfolder.'/nouser.jpg',
                    'name'=>$row->name,
                    'email'=>$row->email,
                    'dob'=>isset($row->dob)?date('d/m/Y',strtotime($row->dob)):'',
                    'phone_number'=>$row->phone_number,
                    'gender'=>isset($row->gender)?$row->gender:'',
                    'nationality'=>$row->nationality,
                    'pending_order_count'=>$this->getPendingorderCount($row->id),
                    'loyalty_points'=>$this->getLoyaltyPointsByUserId($row->id)
                   
                    //'thumb_profile_pic'=>$row->image?$this->uploadsfolder.'/users/thumb/'.$row->image:$this->uploadsfolder.'/nouser.jpg',
                    
                   
                    );
            return $user;
        }

        public function getwaiterdetailfromObjectArray($row)
        {

            $restro = Restaurant::whereId($row->restaurant_id)->first();
            $user = (object)array(
                    'userid'=>$row->id,
                    'profile_pic'=>$row->image?$this->uploadsfolder.'/users/'.$row->image:$this->uploadsfolder.'/nouser.jpg',
                    'profile_pic_thumb'=>$row->image?$this->uploadsfolder.'/users/thumb/'.$row->image:$this->uploadsfolder.'/nouser.jpg',
                    'name'=>$row->name,
                    'email'=>$row->email,
                    
                    'phone_number'=>$row->phone_number,
                    'id_number'=>$row->id_number,
                    'badge_number'=>$row->badge_number,
                    'date_employeed'=>$row->date_employeed,
                    'restaurant_id'=>$row->restaurant_id,
                    'latitude'=>$restro->latitude,
                    'longitude'=>$restro->longitude
                    );
            return $user;
        }


        public function login(Request $request)
        {
            
            $validator = Validator::make($request->all(), [
                            'Email_PhoneNo' => 'required',
                            'password' => 'required',
                            'device_type'=>'required',
                            'device_id'=>'required',
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $user_name = $request->Email_PhoneNo;
                $password = bcrypt($request->password);
                $row = User::where('role_id',11)
                        ->where('email', $user_name)
                        ->orWhere('phone_number',$user_name)
                        ->first();
                if ($row && Hash::check($request->password, $row->password)) 
                {
                    $user =  $this->getuserdetailfromObjectArray($row);

                    $this->manageDeviceIdAndToken($row->id,$request->device_id,$request->device_type,'add');

                   
                    return response()->json(['status'=>true,'message'=>'login successfully','userdetails'=>$user,'unseen_notification_count'=>$this->getUnseenNotificationCount($row->id)]);

                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid username or password']);
                }
                
            }
        }


        public function getWaiterLogin(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'Email_PhoneNo' => 'required',
                            'password' => 'required',
                            'device_type'=>'required',
                            'device_id'=>'required',
                           // 'longitude'=>'required',
                           // 'latitude'=>'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $user_name = $request->Email_PhoneNo;
                $password = bcrypt($request->password);
                $row = User::where('role_id',4)
                        ->where('email', $user_name)
                        ->orWhere('phone_number',$user_name)
                        ->first();
                if ($row && Hash::check($request->password, $row->password)) 
                {
                    $allow_location_check = false;//make its true after location updation
                    if($allow_location_check == true)
                    {
                        if(isset($request->longitude) && isset($request->latitude) && $request->latitude != "" && $request->longitude != "")
                        {
                            $distance =  $this->distance(
                                          $request->latitude,
                                          $request->longitude, 
                                          $row->userRestaurent->latitude,
                                         $row->userRestaurent->longitude,
                                          'K');
                            if($distance <=1)
                           {
                                $user =  $this->getwaiterdetailfromObjectArray($row);


                                $this->deleteDeviceTokenForAllUser($request->device_id);

                                $this->manageDeviceIdAndToken($row->id,$request->device_id,$request->device_type,'add');
                                return response()->json(['status'=>true,'message'=>'login successfully','userdetails'=>$user]);
                           }
                           else
                           {
                                return response()->json(['status'=>false,'message'=>'To view the Menu, you have to be within a radius of 1 Km from Branch']);
                           }
                        }
                        else
                        {
                             return response()->json(['status'=>false,'message'=>'Location is required']);
                        }
                    }
                    else
                    {
                        $user =  $this->getwaiterdetailfromObjectArray($row);
                        $this->manageDeviceIdAndToken($row->id,$request->device_id,$request->device_type,'add');
                        return response()->json(['status'=>true,'message'=>'login successfully','userdetails'=>$user]);
                    }
                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid username or password']);
                }
                
            }
        }

        public function forgotPassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'email' => 'required|email',
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $row = User::where('role_id',11)
                        ->where('email', $request->email)
                        ->first();
                if($row)
                {
                    $new_password = $this->randomOtp(10);
                    $row->password = bcrypt($new_password);
                    $row->save();
                    $data=  array(
                        "email"=>$row->email,
                        "password"=>$new_password,
                        );
                    $data['html'] = 'Yor updated password is: '.$new_password;
                    mailSend($data);
                    return response()->json(['status'=>true,'message'=>'Your Password will be sent to your email address']);
                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid email']);
                }

            }
        }

        public function getProfile($user_id=null)
        {
            $row = User::where('role_id',11)
                        ->where('id', $user_id)
                        ->first();
            if($row)
            {
                $user = $this->getuserdetailfromObjectArray($row);
                return response()->json(['status'=>true,'message'=>'Profile fetch successfully','userdetails'=>$user]);
            }
            else
            {
                return response()->json(['status'=>false,'message'=>'Invalid user']);
            }
        }


        public function updateProfile(Request $request)
        {
            $validator = Validator::make($request->all(), [

                            'user_id' => 'required',
                            'profile_pic' =>  'mimes:jpeg,jpg,png',
                            'name' => 'required|max:255',
                            'email' => 'required|email|unique:users,email,' . $request->user_id,
                            //'dob'=>'required',
                            'phone_number' => 'required|numeric|unique:users,phone_number,' . $request->user_id,
                           // 'gender'=>'required',
                            'nationality'=>'required',
                           
                            
                            
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                
                $row = User::whereId($request->user_id)->first();
                $previous_row = $row;
                $row->name= $request->name;
                $row->email=$request->email;
                $row->gender = $request->gender;
                $row->nationality = $request->nationality;
                $row->phone_number = $request->phone_number;
                if(isset($request->dob) && $request->dob != '')
                {
                    $row->dob = convertDMYtoYMD($request->dob);
                }

                if(isset($request->gender) && $request->gender != '')
                {
                    $row->gender = $request->gender;
                }
                
              
                if($request->file('profile_pic'))
                {
                    $file = $request->file('profile_pic');
                    $image = uploadwithresize($file,'users','186');
                    if($previous_row->image)
                    {
                        unlinkfile('users',$previous_row->image);
                    }
                    $row->image= $image;
                }
                
                $row->save();
               

                $user = $user = $this->getuserdetailfromObjectArray($row);
                return response()->json(['status'=>true,'message'=>'profile updated successfully','userdetails'=>$user]);
            }
        }


        public function logout(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'device_type'=>'required',
                            'device_id'=>'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                 $this->manageDeviceIdAndToken($request->user_id,$request->device_id,$request->device_type,'delete');
                return response()->json(['status'=>true,'message'=>'Logout successfully']);

            }
        }


        public function changePassword(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'currentPassword'=>'required',
                            'newPassword'=>'required'
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $row = User::whereId($request->user_id)->first();
                if (Hash::check($request->currentPassword, $row->password)) 
                {
                     $row->password = bcrypt($request->newPassword);
                     $row->save();
                     
                    return response()->json(['status'=>true,'message'=>'Password changed successfully']);

                }
                else
                {
                     return response()->json(['status'=>false,'message'=>'Old password is not correct']);
                }
            }
        }



        public function manageDeviceIdAndToken($user_id,$device_id,$device_type,$methodName)
        {
            if($methodName =='add')
            {
                 UserDevice::updateOrCreate(
                    ['user_id' => $user_id,'device_id'=>$device_id,'device_type'=>$device_type]
                    ); 
            }
            if($methodName=='delete')
            {
                UserDevice::where('user_id',$user_id)
                ->where('device_id',$device_id)
                ->where('device_type',$device_type)
                ->delete();
            }
        }

        public function deleteDeviceTokenForAllUser($device_id)
        {
             UserDevice:where('device_id',$device_id)
                ->delete();
        }

        public function loginPrintClassUser(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'username' => 'required',
                            'password' => 'required',
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $user_name = $request->username;
                $password = bcrypt($request->password);
                $row = PrintClassUser::where('username',$user_name)->first();
                if ($row && Hash::check($request->password, $row->password)) 
                {
                    //$user =  $this->getuserdetailfromObjectArray($row);
                     $print_class_data = PrintClass::where('id',$row->print_class_id)->first();
                    $print_class_name =$print_class_data->name;
                    $user = (object)[
                    'print_class_user_id' => $row->id,
                    'name' => $row->name,
                    'username' => $row->username,
                    'restaurant_id' => $row->restaurant_id,
                    'print_class_name'=>$print_class_name,
                   
                    'print_class_id' => $row->print_class_id
                    ];
                   $user->name = $row->name;
                   $user->username = $row->username;
                   $user->restaurant_id = $row->restaurant_id;
                   $user->print_class_id = $row->print_class_id;
                    return response()->json(['status'=>true,'message'=>'login successfully','userdetails'=>$user]);

                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid username or password']);
                }
                
            }
        }




        public function getcolorBystatus($status)
        {
            $status_color = ['NEW'=>'red','PREPARATION'=>'orange','READY TO PICK'=>'blue','DELIVERED'=>'green'];
            return $status_color[$status];
        }


        public function getOrderedItemHistoryByRestroAndPrintClass($restaurant_id,$print_class_id,$print_class_user_id)
        {
            $orderdetail=[];
            $order_arr =  OrderedItem::where('restaurant_id',$restaurant_id)
            ->where('print_class_id',$print_class_id)
            ->whereNotIn('item_delivery_status',['DELIVERED','PENDING','CANCLED','COMPLETED'])
            ->pluck('order_id')->toArray();

            $order_arr =array_unique($order_arr);
            $print_class_user = PrintClassUser::where('id',$print_class_user_id)->first();
            foreach($order_arr as $order)
            {
                $order_data =  Order::where('id',$order)->first();

                $can_cancle_item = 'no';
                $can_print_bill = 'no';
                $can_print_docket = 'yes';
                $current_status =  OrderedItem::where('restaurant_id',$restaurant_id)
                ->where('print_class_id',$print_class_id)
                ->where('order_id',$order)
                ->first();

                $ordered_item_arr =  OrderedItem::where('restaurant_id',$restaurant_id)
                ->where('print_class_id',$print_class_id)
                ->whereNotIn('item_delivery_status',['DELIVERED','PENDING','CANCLED','COMPLETED'])
                ->where('order_id',$order)
                ->get();

                if($print_class_user->can_cancle_item == '1' && $current_status->item_delivery_status=='NEW' && $order_data->order_type=='POSTPAID')
                {
                    $can_cancle_item = 'yes';
                }

                if($print_class_user->can_print_bill == '1' )
                {
                    $can_print_bill = 'yes';
                }


                $print_class_id =  $print_class_user->print_class_id;
                $status_setting = $this->getPrintClassSetting();
                $total_count = HistoryPrintDocket::where('print_class_id',$print_class_id)->where('order_id',$order_data->id)->count();
                if($total_count=='1')
                {
                    if($status_setting[$print_class_id]['can_print'] =='no')
                    {
                        $can_print_docket= 'no';
                    }
                }
                   
                $inner_array = [
                'date_and_time'=>date('Y-m-d h:i A',strtotime($order_data->created_at)),
                'order_number'=>$order_data->id,
                'table_number'=>getAssociateTableWithOrder($order_data),
                'number_of_guest'=>$order_data->total_guests,
                'waiter'=>getAssociateWaiteWithOrder($order_data),
                'status'=>$current_status->item_delivery_status,
                'status_color'=>$this->getcolorBystatus($current_status->item_delivery_status),
                'can_cancle_item'=>$can_cancle_item,
                'can_print_bill'=>$can_print_bill,
                'can_print_docket'=>$can_print_docket

                ];






                $condiments = [];
                $item_desc_array = [];
                foreach($ordered_item_arr as $ordered_item)
                {
                    $condiment_arr =  json_decode($ordered_item->condiments_json);
                    $item_desc = 'Item: '.$ordered_item->item_title;
                    if($ordered_item->item_comment && $ordered_item->item_comment !="")
                    {
                        $item_desc .= '('.$ordered_item->item_comment.')';
                    }
                    $item_desc .= '<br>Qty: '.$ordered_item->item_quantity;
                    $item_desc_array[] = $item_desc;


                    if($condiment_arr && count($condiment_arr)>0)
                    {

                        foreach($condiment_arr as $condiment_data)
                        {
                            if($condiment_data->sub_items && count($condiment_data->sub_items)>0)
                            {
                                foreach($condiment_data->sub_items as $sub_items)
                                {
                                    if($sub_items->title)
                                    {
                                        $condiments[] = ucfirst($sub_items->title);
                                    }
                                }


                            }
                        }
                    }

                }

                $inner_array['item_desc_array'] = implode(' ,<br>',$item_desc_array);
                $inner_array['condiments'] = implode(' ,',$condiments);

                $orderdetail[] = $inner_array;
            }
                
            return $orderdetail;
        }

        public function getOrderedItemForPrintClassUsers(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'restaurant_id' => 'required',
                            'print_class_id' => 'required',
                            'print_class_user_id'=>'required'
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
               $orderdetail=$this->getOrderedItemHistoryByRestroAndPrintClass($request->restaurant_id,$request->print_class_id,$request->print_class_user_id);
                return response()->json(['status'=>true,'message'=>'Order Items','orderdetail'=>$orderdetail]);
            }
        }


         public function cancleItemsFromOrder(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'ordered_id' => 'required',
                            'print_class_id' => 'required',
                            
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
               $order_id = $request->ordered_id;
               $print_class_id = $request->print_class_id;
               OrderedItem::where('order_id',$order_id)->where('print_class_id',$print_class_id)->update(['item_delivery_status'=>'CANCLED']);
               $is_have_another_data = OrderedItem::where('order_id',$order_id)->where('item_delivery_status','!=','CANCLED')->first();
               if(!$is_have_another_data)
               {
                    Order::where('id',$order_id)->update(['status'=>'CANCLED']);
               }
                return response()->json(['status'=>true,'message'=>'Item cancled successfully']);
            }
        }


        public function updateOrderedItemStatus(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'ordered_id' => 'required',
                            'current_status' => 'required',
                            'print_class_id' => 'required',
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $order_id = $request->ordered_id;
                $print_class_id = $request->print_class_id;
                $current_status = $request->current_status;
                if($current_status == 'NEW')
                {
                    $update_status = 'PREPARATION';
                    $this->createNotification('READYTOPREPAIR',['order_id'=>$order_id,'updated_at'=>date('Y-m-d H:i:s')]);
                }

                if($current_status == 'PREPARATION')
                {
                    $update_status = 'READY TO PICK';
                    $this->createNotification('READYTOPICK',['order_id'=>$order_id,'updated_at'=>date('Y-m-d H:i:s')]);
                }
                if($current_status == 'READY TO PICK')
                {
                    $update_status = 'DELIVERED';
                    $this->updateManageStockMoves($order_id);
                }
                DB::table('ordered_items')->where('print_class_id', $print_class_id)
                 ->where('order_id',$order_id)
                 ->update(['item_delivery_status' => $update_status]);

                if(isset($update_status)) {
                    $current_data = [
                        'status'=>$update_status,
                        'status_color'=>$this->getcolorBystatus($update_status)
                    ];
                    return response()->json(['status'=>true,'message'=>'Status updated','new_status'=>$current_data]); 
                }
                else
                {
                     return response()->json(['status'=>false,'message'=>'You have marked delivered']);
                }
            }
        }
        
        protected function updateManageStockMoves($order_id){
            $order = Order::with('getAssociateItemWithOrder.getAssociateFooditem')
                ->where('id', $order_id)
                ->first();
            
            $order_items = $order->getAssociateItemWithOrder;
            foreach($order_items as $order_item_key => $order_item_row){

                if($order_item_row->getAssociateFooditem->getAssociateRecipe)
                {
                $recipe_row = $order_item_row->getAssociateFooditem->getAssociateRecipe;
                $wa_location_and_store_id = $recipe_row->wa_location_and_store_id;
                $recipe_ingredients = $order_item_row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient;
                foreach($recipe_ingredients as $key => $recipe_ingredient_row) {
                    $series_module = WaNumerSeriesCode::where('module','GRN')->first();
                    $dateTime = date('Y-m-d H:i:s');
                    
                    $inventory_item_row = $recipe_ingredient_row->getAssociateItemDetail;
                    $inventory_qoh = getItemAvailableQuantity($inventory_item_row->stock_id_code, $wa_location_and_store_id);
                    $weight = $recipe_ingredient_row->weight;
                    if($weight > $inventory_qoh){
                        $deficient_quantity = $weight - $inventory_qoh;
                        $sold_but_unbooked_entity = new WaSoldButUnbookedItem();
                        $sold_but_unbooked_entity->order_id = $order->id;
                        $sold_but_unbooked_entity->wa_recipe_ingredient_id = $recipe_ingredient_row->id;
                        $sold_but_unbooked_entity->ordered_item_id = $order_item_row->id;
                        $sold_but_unbooked_entity->wa_inventory_item_id = $inventory_item_row->id;
                        $sold_but_unbooked_entity->qoh = $inventory_qoh;
                        $sold_but_unbooked_entity->deficient_quantity = $deficient_quantity;
                        $sold_but_unbooked_entity->save();
                        $weight = $inventory_qoh;
                    }
                    if($weight > 0){
                        $stockMove = new WaStockMove();
                        $stockMove->user_id = $order->user_id;
                        $stockMove->ordered_item_id = $order_item_row->id;
                        $stockMove->restaurant_id = $order_item_row->restaurant_id;
                        $stockMove->wa_location_and_store_id = $wa_location_and_store_id;
                        $stockMove->wa_inventory_item_id = $inventory_item_row->id;
                        $stockMove->standard_cost = $inventory_item_row->standard_cost;
                        $stockMove->qauntity = -($weight);
                        $stockMove->stock_id_code = $inventory_item_row->stock_id_code;
                        $stockMove->grn_type_number = $series_module->type_number;
                        $stockMove->grn_last_nuber_used = $series_module->last_number_used;
                        $stockMove->price = $inventory_item_row->standard_cost;
                        $stockMove->refrence = $order->slug;
                        $stockMove->save();
                    }
                    
                }
            }
            }
            return;
        }

        public function getOrderItemCondimentDetail($order)
        {
            $condiment = [];
           $condiment_arr =  json_decode($order->condiments_json);
          if($condiment_arr && count($condiment_arr)>0)
          {

            foreach($condiment_arr as $condiment_data)
            {
                if($condiment_data->sub_items && count($condiment_data->sub_items)>0)
                {
                    foreach($condiment_data->sub_items as $sub_items)
                    {
                        if($sub_items->title)
                        {
                            $condiment[] = ucfirst($sub_items->title);
                        }
                    }
                    
                    
                }
            }
          }
            return implode(' ,',$condiment);
        }

        public function getValidateComplimentaryAmountWithCode(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'complimentry_code' => 'required',
                            'order_final_price' => 'required' 
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $complimentry_code = $request->complimentry_code;
                $order_final_price = $request->order_final_price;

               $is_exist = User::where('complementary_number',$complimentry_code)->first();
               if($is_exist)
               {
                    $currentMonth = date('m');
                    $currentMonthSpentAmountWithComplementaryCode = Order::where('complimentry_code',$complimentry_code)
                        ->whereRaw('MONTH(created_at) = ?',[$currentMonth])
                        ->whereNotIn('status',['CANCLED','PENDING'])
                        ->sum('order_final_price');
                    $canOrderWithLeftPriceAmount = $is_exist->complementary_amount - $currentMonthSpentAmountWithComplementaryCode;
                    if($order_final_price<=$canOrderWithLeftPriceAmount)
                    {
                        //can make order with complementary amount
                        return response()->json(['status'=>true,'message'=>'Can purchase']);
                    }
                    else
                    {
                        //limit exceed
                        return response()->json(['status'=>false,'message'=>'Complementary number limit exceed for this month']); 
                    }
               }
               else
               {
                return response()->json(['status'=>false,'message'=>'Invalid complementary number']); 
               }
            }
        }

        public function getFeedbackStatus($id,$rating_for)
        {
            $status = 'PENDING';
            if($rating_for == 'O')
            {
                $row = Feedback::where('order_id',$id)->first();
                if($row)
                {
                    $status =  $row->status == 'N'?'SKIPED':'COMPLETED';
                }
            }
            if($rating_for == 'R')
            {
                $row = Feedback::where('restaurant_id',$id)->first();
                if($row)
                {
                    $status =  $row->status == 'N'?'SKIPED':'COMPLETED';
                }
            }
            return $status;
        }

        public function getMyOrders(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $all_orders = Order::where('user_id',$request->user_id)
                   // ->where('restaurant_id',$request->restaurant_id)
                    ->whereNotIn('status',['CANCLED','PENDING'])
                    ->orderBy('id','desc')
                    ->get();
                $all_orders_listing = [];
                foreach($all_orders as $order)
                {
                   $waiters =  getAssociateWaiterIdsWithOrder($order);
                   $waiter_id = '';
                   if(count($waiters)>0)
                   {
                        $waiter_id = $waiters[0];
                   }
                  $inner_array = [
                  'order_id'=>$order->id,
                  'waiter_id'=>$waiter_id,
                  'order_total_price'=>$order->order_final_price,
                  'feedback_status'=>$this->getFeedbackStatus($order->id,'O'),
                  'is_tip_given'=>$this->isTipGiven($order->id),
                  'order_is_completed'=>$order->status=='COMPLETED'?true:false,
                    'order_created_time'=>date('h:i A',strtotime($order->created_at)),
                      'order_created_date'=>date('Y-m-d',strtotime($order->created_at)),
                      'order_created_timestr'=>strtotime($order->created_at),
                     'items'=>[],
                    'offer_data'=>[]
                  ];
                  $ordered_item = $order->getAssociateItemWithOrder;
                  $counter = 0;
                  foreach($ordered_item as $item)
                  {
                    if($item->item_delivery_status !='CANCLED' && !$item->order_offer_id)
                    {

                      $item_detail = $item->getAssociateFooditem;
                      $inner_array['items'][$counter]['title'] = $item_detail->name;
                      $inner_array['items'][$counter]['item_id'] = $item_detail->id;
                      $inner_array['items'][$counter]['quantity'] = $item->item_quantity;
                      $inner_array['items'][$counter]['price'] = $item->price;
                      $inner_array['items'][$counter]['comment'] = isset($item->item_comment)?$item->item_comment:'';
                      $inner_array['items'][$counter]['image'] = $item_detail->image?$this->uploadsfolder.'/menu_items/thumb/'.$item_detail->image:$this->uploadsfolder.'/item_none.png';
                      $inner_array['items'][$counter]['condiments'] = [];
                      $condiments_json_arr = json_decode($item->condiments_json);
                      if(count($condiments_json_arr)>0)
                      {
                        foreach($condiments_json_arr as $cn)
                        {
                          if(count($cn->sub_items)>0)
                          {
                            foreach($cn->sub_items as $condiment_item)
                            {
                             
                              $inner_array['items'][$counter]['condiments'][] =ucfirst($condiment_item->title);
                            }
                          } 
                        }
                      }
                      $counter++;
                    }
                  }
                 

                   
                 $ordered_offers = $order->getAssociateOffersWithOrder;
                  foreach($ordered_offers as $offers)
                  {
                    $is_exist = OrderedItem::where('order_offer_id',$offers->id)
                    ->whereNotIn('item_delivery_status',['CANCLED','PENDING'])
                    ->first();
                    if($is_exist)
                    {
                        $single_offer = [
                        'offer_title'=>$offers->offer_title,
                        'offer_quantity'=>$offers->quantity,
                        'offer_price'=>$offers->price,
                        'offer_image'=>$offers->getAssociateOffersDetail->image?$this->uploadsfolder.'/menu_item_groups/thumb/'.$offers->getAssociateOffersDetail->image:$this->uploadsfolder.'/item_none.png',
                    
                    
                            ];
                         $inner_array['offer_data'][] = $single_offer;
                    }
                   
                  }
                   $all_orders_listing[] = $inner_array;
                }
                return response()->json(['status'=>true,'message'=>'My orders listing','listings'=>$all_orders_listing,
                    'pending_order_count'=>$this->getPendingorderCount($request->user_id),
                    'loyalty_points'=>$this->getLoyaltyPointsByUserId($request->user_id)

                    ]);
            }
        }

        public function getAllWaiterRelatedToOrder(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'order_id' => 'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $order = Order::where('id',$request->order_id)->first();
                if($order)
                {
                    $waiter_arr = [];
                    $all_tables_arr = $order->getAssociateTableWithOrder()->pluck('table_id')->toArray();
                    if(count($all_tables_arr))
                    {
                       $waiter_ids_arr =  EmployeeTableAssignment::whereIn('table_manager_id',$all_tables_arr)->pluck('user_id')->toArray();
                       if(count($waiter_ids_arr)>0)
                       {
                            $waiters = User::whereIn('id',$waiter_ids_arr)->get();
                            $counter = 0;
                            foreach($waiters as $waiter)
                            {
                                $waiter_arr[$counter]['waiter_id'] = $waiter->id;
                                $waiter_arr[$counter]['name'] = ucfirst($waiter->name);
                                $waiter_arr[$counter]['badge_number'] = $waiter->badge_number;
                                $waiter_arr[$counter]['id_number'] = $waiter->id_number;
                                $waiter_arr[$counter]['image'] = $waiter->image?$this->uploadsfolder.'/users/thumb/'.$waiter->image:$this->uploadsfolder.'/item_none.png';
                                $counter++;
                            }
                       }
                       
                    }
                    return response()->json(['status'=>true,'message'=>'Waiter Listings','listings'=>$waiter_arr]);
                   
                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid order']);
                }
               
            }
        }

        public function addWaiterTip(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'order_id' => 'required',
                            'tip_amount' => 'required',
                            'payment_mode' => 'required',
                            'waiter_id' => 'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $tip =  new WaiterTip();
                $tip->waiter_id = $request->waiter_id;
                $tip->tip_amount = $request->tip_amount;
                $tip->payment_mode = $request->payment_mode;
                $tip->order_id = $request->order_id;

                if(isset($request->mpesa_request_id))
                {
                    $tip->mpesa_request_id = $request->mpesa_request_id;
                }
                $tip->save();
                return response()->json(['status'=>true,'message'=>'Tip added successfully']);
            }
            
        }

        public function getLoyaltyPoint($user_id=null)
        {
            $row = LoyaltyPoint::with('order')
                    ->select('order_id','points','points_source','status',DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as Date'))
                        ->where('user_id', $user_id)
                       
                        ->orderBy('id','DESC')
                        ->get();
           





           // $total = array_sum($row->pluck('points')->toArray());

            $total = $this->getLoyaltyPointsByUserId($user_id);
            if($row)
            {
                return response()->json(['status'=>true,'message'=>'User Loyalty Points fetch successfully','LoyaltyPoints'=>$row,'totalpoints'=>$total]);
            }
            else
            {
                return response()->json(['status'=>false,'message'=>'Invalid user']);
            }
        }

        public function printbillForOrder(Request $request)
        {

            $validator = Validator::make($request->all(), [
                            'ordered_id' => 'required',
                            'print_class_user_id' => 'required',
                            'print_type' => 'required'
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {

                $print_class_user_info = PrintClassUser::where('id',$request->print_class_user_id)->first();
                $print_class_id = $print_class_user_info->print_class_id;
                $historyPrintDocket =  new HistoryPrintDocket();
                $historyPrintDocket->order_id = $request->ordered_id;
                $historyPrintDocket->print_class_id = $print_class_id;
                $historyPrintDocket->save();
                $receipt = printBill($request->print_class_user_id,$request->ordered_id,$request->print_type,'P');

                return $receipt;
            }
            

        }

        public function getPrintClassSetting()
        {
            $all_print_class=  PrintClass::get();
            $status_setting = [];
            foreach($all_print_class as $print_class_data)
            {
                $status_setting[$print_class_data->id]['status'] = $print_class_data->jump_to_status;
                $status_setting[$print_class_data->id]['can_print'] = 'yes';
                if($print_class_data->can_multiple_print == '0')
                {
                    $status_setting[$print_class_data->id]['can_print'] = 'no';
                }

            }
            return $status_setting;
          
        }


       

        public function managedocketstatus(Request $request)
        {

            $validator = Validator::make($request->all(), [
                            'ordered_id' => 'required',
                            'print_class_user_id' => 'required'
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                 $print_class_user_info = PrintClassUser::where('id',$request->print_class_user_id)->first();
                $print_class_id = $print_class_user_info->print_class_id;
                $data = ['status_changed'=>false];
                $total_count = HistoryPrintDocket::where('print_class_id',$print_class_id)->where('order_id',$request->ordered_id)->count();
                if($total_count=='1')
                {
                   $order_item =  OrderedItem::where('order_id',$request->ordered_id)->where('print_class_id',$print_class_id)->first();


                    if($order_item->item_delivery_status == 'NEW' || $order_item->item_delivery_status == 'PREPARATION')
                    {

                        $status_setting = $this->getPrintClassSetting();

                       

                        $changed_status = $status_setting[$print_class_id]['status'];
                        $can_print = $status_setting[$print_class_id]['can_print'];
                        $status_color=$this->getcolorBystatus($changed_status);

                         $order_item =  OrderedItem::where('order_id',$request->ordered_id)->where('print_class_id',$print_class_id)->update(['item_delivery_status'=>$changed_status]);
                        if($changed_status == 'PREPARATION')
                        {
                             $this->createNotification('READYTOPREPAIR',['order_id'=>$request->ordered_id,'updated_at'=>date('Y-m-d H:i:s')]);
                        }

                         if($changed_status == 'READY TO PICK')
                        {
                             $this->createNotification('READYTOPICK',['order_id'=>$request->ordered_id,'updated_at'=>date('Y-m-d H:i:s')]);
                        }



                       
                       
                        $data = ['status_changed'=>true,'changed_status'=>$changed_status,'can_print'=>$can_print,'status_color'=>$status_color];
                    }
                    
                }
                return response()->json(['status'=>true,'message'=>'settings','data'=>$data]);
            }
            

        }


        

        public function distance($lat1, $lon1, $lat2, $lon2, $unit) 
        {
            /***********************************************************************************************
                function for get the distance between two coordinates
                $unit = K(kilometer),M(Miles),N(Natural miles)
            ***********************************************************************************************/
                //echo $lat1.'=='.$lon1.'=='.$lat2.'=='.$lon2.'=='.$unit; die;
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);

          if ($unit == "K") 
          {
              return round(($miles * 1.609344),2).' Km away';
          } 
          else if ($unit == "N") 
          {
              return ($miles * 0.8684);
          } 
          else 
          {
              return $miles;
          }
        }





        public function loginDjUser(Request $request)
        {
            $validator = Validator::make($request->all(), [
                            'username' => 'required',
                            'password' => 'required',
                            
                        ]);
            if ($validator->fails()) 
            {
                $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $user_name = $request->username;
                $password = bcrypt($request->password);


                $row = User::where('role_id',100)
                        ->where('status', '1')
                         ->where(function($query) use ($user_name){
                        $query  ->where('email', $user_name);
                        $query->orWhere('phone_number', $user_name);
                        })
                       
                        ->first();
                if ($row && Hash::check($request->password, $row->password) && $row->status == '1') 
                {
                     $user = (object)[
                    'user_id' => $row->id,
                    'name' => $row->name,
                   
                    'restaurant_id' => $row->restaurant_id
                  
                    ];
                    return response()->json(['status'=>true,'message'=>'login successfully','userdetails'=>$user]);

                }
                else
                {
                    return response()->json(['status'=>false,'message'=>'Invalid username or password']);
                }

                
                
            }
        }


        

          public function updateDjRequestStatus(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'request_id' => 'required',
                            'current_status' => 'required',
                            
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $row = DjRequest::where('id',$request->request_id)->first();

                 if($row)
                 {
                    $row->status = 'COMPLETED';
                    $row->save();
                 }

                 $data = ['status'=>'COMPLETED','status_color'=>'red'];
                return response()->json(['status'=>true,'message'=>'Status updated successfully','data'=>$data]);
            }
        }

        public function getNewResquestFroDj(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'restaurant_id' => 'required',
                            
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {
                $data = [];
                $i = 0;
                $restaurant_id = $request->restaurant_id;
                $all_request = DjRequest::where('restaurant_id',$restaurant_id)->whereIn('status',['NEW'])->get();
                foreach($all_request as $my_request)
                {
                   $data[$i]['request_id'] = $my_request->id;
                   $data[$i]['date_and_time'] = date('Y-m-d h:i A',strtotime($my_request->created_at));
                    $data[$i]['comment'] = $my_request->comment;
                    $data[$i]['user_name'] = $my_request->getAssociateUser->name;
                    $data[$i]['status'] = $my_request->status;
                    $data[$i]['status_color'] = $my_request->status=='NEW'?'green':'red';
                   $i++; 
                }
                return response()->json(['status'=>true,'message'=>'listing','data'=>$data]);
            }
        }

        public function makeDjRequest(Request $request)
        {
             $validator = Validator::make($request->all(), [
                            'user_id' => 'required',
                            'restaurant_id' => 'required',
                            'comment' => 'required',
                            
                ]);
            if ($validator->fails()) 
            {
               $error = $this->validationHandle($validator->messages()); 
                return response()->json(['status'=>false,'message'=>$error]);
            }
            else
            {   
                $new_request = new DjRequest();
                $new_request->comment = $request->comment;
                $new_request->user_id = $request->user_id;
                $new_request->restaurant_id = $request->restaurant_id;
                $new_request->save();
                return response()->json(['status'=>true,'message'=>'Your request added successfully','request_id'=>$new_request->id]);
            }
        }


    public function getRestaurantFloorPlans()
    {
        $rows = Restaurant::get();
        $all_floor_plans = [];
        $i =0;
        foreach($rows as $row)
        {
            $all_floor_plans[$i]['restaurant_id'] = $row->id;
            $all_floor_plans[$i]['restaurant_floor_image'] =$this->uploadsfolder.'/restaurants/'.$row->floor_image;
            $all_floor_plans[$i]['restaurant_floor_image_thumb'] =$this->uploadsfolder.'/restaurants/thumb/'.$row->floor_image;

             $all_floor_plans[$i]['restaurant_name'] = $row->name;
               $all_floor_plans[$i]['restaurant_location'] = $row->location;
               $i++;
          

        }
        return response()->json(['status'=>true,'message'=>'Restaurant list with floor plan','data'=>$all_floor_plans]);
    }

    public function addReservationrequest(Request $request)
    {
        ignore_user_abort(true);
        set_time_limit(0);
        ob_start();  
        $validator = Validator::make($request->all(), [
                        'user_id' => 'required',
                        'restaurant_id' => 'required',
                       
                        'reservation_time' => 'required',
                        'comment' => 'max:250',
                        'phone_number' => 'required',
                        'email' => 'required|email',
                      
                        'event_type' => 'required|max:250', 
                    ]);
        if ($validator->fails()) 
        {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else
        {
         
            $row =  new Reservation();
            $row->user_id = $request->user_id;
            $row->restaurant_id = $request->restaurant_id;
          
            $row->reservation_time = date('Y-m-d H:i:s',strtotime($request->reservation_time));
            $row->comment = $request->comment?$request->comment:null;
            $row->phone_number = $request->phone_number;
            $row->email = $request->email;
           
            $row->event_type = $request->event_type;
            $row->save();


            $reservation_data = Reservation::where('id',$row->id)->first();
            

            // This code use for notification message send response return speedly.
            

            echo json_encode(["status"=>true,"message"=>'Reservation Booking successfull']);
            header("Content-Encoding: none");
            header('Connection: close');
            header('Content-Length: '.ob_get_length());
            ob_end_flush();
            ob_flush();
            flush();                   
            session_write_close();  


            $all_reservation_emails = ReservationEmail::select('email','phone_number')->get();


           //$admin_email_arr = ["events@thebigfivebreweries.com","sales@thebigfivebreweries.com","info@thebigfivebreweries.com"];

           //$admin_email_arr = array();
          // foreach($all_reservation_emails as $getemail)
          // {
//
           //}

          
            foreach($all_reservation_emails as $arr_email)
            {




                $this->sendReservationEmail($arr_email->email,'admin',$reservation_data);
                if($arr_email->phone_number && $arr_email->phone_number != '')
               {
                $this->sendMessageOnMobileNumber($arr_email->phone_number,'You have got a new reservation request');
               }
            }



            $this->sendReservationEmail($row->email,'user',$reservation_data);   
        }
            
    }

    public function sendReservationEmail($mail_address,$user_type,$row)
    {
        $data['send_to'] = $mail_address;
        $data['restro_name'] = $row->getAssociateRestro->name;
        $data['name'] = $row->getAssociateUser->name;
        $data['no_of_person'] = $row->no_of_person;
        $data['event_type'] = $row->event_type;
        $data['time'] = date('d-m-Y h:i A',strtotime($row->reservation_time));


          $data['subject'] = 'Reservation request';

          
        if($user_type == 'user')
        {
              $data['subject'] = 'Your booking at Restaurant is pending.';
            reservationEmailSend('emails.user_reservation',$data);
        }
        else if($user_type == 'admin')
        {  
            reservationEmailSend('emails.admin_reservation',$data);
        }
        else
        {

        }
    }
    
    public function addAmountToWallet(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|digits_between:1,10|exists:users,id',
            'amount' => 'required',
            'refrence_description'=> 'required'
        ]);
        if ($validator->fails()) {
            $error = $this->validationHandle($validator->messages()); 
            return response()->json(['status'=>false,'message'=>$error]);
        }
        else {
            $user_id = $request->user_id;
            $user =  User::where('id', $user_id)->first();
            $row = new WalletTransaction();
            $row->phone_number = $user->phone_number;
            $row->entry_type = 'Mpesa Top Up';
            $row->amount = $request->amount;
            $row->user_id = $user_id;
            $row->refrence_description = ($request->refrence_description) ? $request->refrence_description : null;
            $row->save();
            $this->updateWalletAmount($user->phone_number);
            return response()->json([
                'status'=>true,
                'message'=>'Wallet has been recharged successfully',
                'data'=>[]
            ]);
        }
    }// end function.



        

    
    }  