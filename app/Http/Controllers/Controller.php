<?php

namespace App\Http\Controllers;

use App\DeliveryManShift;
use App\DeliverySchedule;
use App\LoadingSheetDispatch;
use App\Model\Route;
use App\Model\WaInternalRequisition;
use App\Model\WaInternalRequisitionItem;
use App\Model\WaShift;
use App\SalesmanShift;
use App\SalesmanShiftStoreDispatch;
use App\VehicleAssignment;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Model\Restaurant;
use App\Model\Role;
use App\Model\Category;
use App\Model\CategoryRelation;
use App\Model\ItemCategoryRelation;
use App\Model\SubMajorGroup;
use App\Model\MenuItemGroup;
use App\Model\FamilyGroup;
use App\Model\PrintClass;
use App\Model\Condiment;
use App\Model\CondimentGroup;
use App\Model\TaxManager;
use App\Model\Order;
use App\Model\WaiterTip;
use App\Model\LoyaltyPoint;
use App\Model\Setting;
use App\Model\OrderBookedTable;
use App\Model\OrderedItem;
use App\Model\Notification;
use App\Model\UserDevice;
use App\Model\WaCategory;
use File;
use App\Model\OrderReceiptRelation;
use App\Model\OrderReceipt;
use App\Model\TableManager;
use App\Model\FoodItemsPrintClassRelation;
use App\Model\ReceiptSummaryPayment;
use App\Model\OrderOffer;

use App\Model\BeerKegCategory;
use App\Model\BeerAndKegCategoryRelation;
use App\Model\BeerItemsAndCategoryRelation;
use App\Model\Wallet;

use App\Model\WalletTransaction;
use App\Model\DeliveryOrderReceiptRelation;
use App\Model\DeliveryOrder;
use Excel;
use App\Model\DeliveryOrderItem;
use App\Model\DeliveryReceiptSummaryPayment;
use App\Model\User;
use Illuminate\Support\Facades\DB;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getPrintclassidByItemId($item_id, $restaurant_id)
    {
        $all_related_print_class = FoodItemsPrintClassRelation::where('food_item_id', $item_id)->pluck('print_class_id')->toArray();
        $item_related_print_class_id = null;
        if (count($all_related_print_class) > 0) {

            $my_array = [];
            $my_final_array = [];
            $ids = implode(',', $all_related_print_class);

            $results = DB::select(DB::raw("SELECT count(*) total_order_count,
                print_class_id FROM ordered_items where restaurant_id = $restaurant_id and 
                 print_class_id IN  ($ids) group by print_class_id"));
            if (count($results) > 0) {
                foreach ($results as $print_class_item_data) {
                    $my_array[$print_class_item_data->print_class_id] = $print_class_item_data->total_order_count;
                }

                foreach ($all_related_print_class as $all_print_class) {
                    $my_final_array[$all_print_class] = 0;
                    if (isset($my_array[$all_print_class])) {
                        $my_final_array[$all_print_class] = $my_array[$all_print_class];
                    }
                }
                asort($my_final_array, SORT_NUMERIC);
                reset($my_final_array);
                $first_key = key($my_final_array);
                $item_related_print_class_id = $first_key;
            } else {
                $item_related_print_class_id = $all_related_print_class[0];
            }
        }
        return $item_related_print_class_id;
    }

    public function storeOfferItemForOrder($offer_details_arr, $order_id, $restaurant_id)
    {
        $offer_items = [];
        foreach ($offer_details_arr as $offer_data) {
            $new_orderd_offer = new OrderOffer();
            $new_orderd_offer->order_id = $order_id;
            $new_orderd_offer->offer_id = $offer_data->offer_id;
            $new_orderd_offer->offer_title = $offer_data->offername;
            $new_orderd_offer->quantity = $offer_data->quantity;
            $new_orderd_offer->restaurant_id = $restaurant_id;
            $new_orderd_offer->price = $offer_data->price;
            $new_orderd_offer->offer_charges = isset($offer_data->offer_charges) ? json_encode($offer_data->offer_charges) : null;
            $new_orderd_offer->save();
            $order_offer_id = $new_orderd_offer->id;

            foreach ($offer_data->offer_item as $offer_item) {
                $inner_array = [
                    'food_item_id' => $offer_item->appetizer_id,
                    'price' => 0,
                    'print_class_id' => $this->getPrintclassidByItemId($offer_item->appetizer_id, $restaurant_id),
                    'item_title' => $offer_item->title,
                    'item_comment' => isset($offer_item->comment) ? $offer_item->comment : '',
                    'item_quantity' => $offer_data->quantity,
                    'order_offer_id' => $order_offer_id,
                    'order_id' => $order_id,
                    'restaurant_id' => $restaurant_id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'condiments_json' => isset($offer_item->condiment_items) ? json_encode($offer_item->condiment_items) : null

                ];
                $offer_items[] = $inner_array;
            }
        }

        if (count($offer_items) > 0) {
            OrderedItem::insert($offer_items);
        }


    }

    public function storeGeneralItemForOrder($items_array, $order_id, $restaurant_id, $order_default_status)
    {
        //OrderedItem

        $items_array_for_insert = [];
        foreach ($items_array as $item) {
            $inner_array = [
                'food_item_id' => $item->appetizer_id,
                'price' => $item->price,
                'print_class_id' => $this->getPrintclassidByItemId($item->appetizer_id, $restaurant_id),
                'item_title' => $item->title,
                'item_comment' => isset($item->comment) ? $item->comment : '',
                'item_quantity' => $item->quantity,
                'order_id' => $order_id,
                'restaurant_id' => $restaurant_id,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
                'item_charges' => isset($item->item_charges) ? json_encode($item->item_charges) : null,
                'condiments_json' => isset($item->condiment_items) ? json_encode($item->condiment_items) : null
            ];

            if ($order_default_status == 'PENDING') {
                $inner_array['item_delivery_status'] = 'PENDING';
            }
            $items_array_for_insert[] = $inner_array;
        }
        OrderedItem::insert($items_array_for_insert);
    }

    public function bookedTheTableForRelatedOrder($tableIdsString, $total_guests, $order_id, $order_default_status)
    {

        $my_order = Order::where('id', $order_id)->first();
        $table_ids_array = explode(',', $tableIdsString);
        $inserting_array = [];
        foreach ($table_ids_array as $booked_table) {
            $inner_array = [
                'order_id' => $order_id,
                'table_id' => $booked_table
            ];
            $inserting_array[] = $inner_array;
        }

        if ($my_order->getAssociateUserForOrder->role_id == '11') {
            $booking_status = 'BOOKED';
            if ($order_default_status == 'PENDING') {
                $booking_status = 'BLOCKED';
            }
            TableManager::whereIn('id', $table_ids_array)->update(['booking_status' => $booking_status]);
        }


        OrderBookedTable::insert($inserting_array);
    }

    public function getRestaurantList()
    {
        $list = Restaurant::pluck('name', 'id');
        return $list;
    }

    public function getRoleList()
    {
        $list = Role::whereNotIn('slug', ['super-admin', 'developer', 'user'])->pluck('title', 'id');
        return $list;
    }

    public function getCategoryList()
    {
        $list = WaCategory::pluck('title', 'id');
        return $list;
    }

    public function majorCategoryForaddItem()
    {
        $list = Category::where('level', 0)->pluck('name', 'id');
        return $list;
    }

    public function getAllPrintClassesName()
    {
        $list = PrintClass::pluck('name', 'id');
        return $list;
    }

    public function getFamilyGroupList()
    {
        $list = FamilyGroup::pluck('name', 'id');
        return $list;
    }

    public function getParentList($level)
    {
        if ($level == '0') {
            $list = Category::whereLevel(0)->whereIn('slug', ['food-and-beverage', 'tobacco', 'offers-them-nights'])->pluck('name', 'id');
            return $list;
        } else {
            if ($level == '2') {
                //here we adding family group then its allows only food and beverages and tobacco product in it
                $list = Category::whereLevel($level);
                $list = $list->get();
                $main_list = [];
                foreach ($list as $dt) {
                    $relative_toSubGroup = $dt->getRelativeData->toArray();
                    $is_from_food_and_tobbacco = CategoryRelation::where('category_id', $relative_toSubGroup['parent_id'])
                        ->whereIn('parent_id', [1, 5])
                        ->first();
                    if ($is_from_food_and_tobbacco) {
                        $main_list[$dt->id] = $dt->name;
                    }

                }
                return $main_list;


            } else if ($level == 'forFamilyGroup') {
                //here we adding family group then its allows only food and beverages and tobacco product in it
                $list = Category::whereLevel(2)->where('is_have_another_layout', '!=', '1');
                $list = $list->get();
                $main_list = [];
                foreach ($list as $dt) {
                    if ($dt->getRelativeData) {
                        $relative_toSubGroup = $dt->getRelativeData->toArray();
                        $is_from_food_and_tobbacco = CategoryRelation::where('category_id', $relative_toSubGroup['parent_id'])
                            ->whereIn('parent_id', [1, 5])
                            ->first();
                    } else {
                        $relative_toSubGroup = [];
                        $is_from_food_and_tobbacco = [];
                    }
                    if ($is_from_food_and_tobbacco) {
                        $main_list[$dt->id] = $dt->name;
                    }

                }
                return $main_list;


            } else if ($level == 'forAlcoholicFamilyGroup') {
                //here we adding family group then its allows only food and beverages and tobacco product in it
                $list = Category::whereLevel(2)->where('is_have_another_layout', '1');
                $list = $list->get();
                $main_list = [];
                foreach ($list as $dt) {
                    if (isset($dt->getRelativeData)) {
                        $relative_toSubGroup = $dt->getRelativeData->toArray();
                        $is_from_food_and_tobbacco = CategoryRelation::where('category_id', $relative_toSubGroup['parent_id'])
                            ->whereIn('parent_id', [1, 5])
                            ->first();

                    } else {
                        $relative_toSubGroup = [];
                        $is_from_food_and_tobbacco = [];
                    }
                    if ($is_from_food_and_tobbacco) {
                        $main_list[$dt->id] = $dt->name;
                    }

                }
                return $main_list;


            } elseif ($level == '5') {
                $list = Category::whereLevel(2)->get();
                $main_list = [];
                foreach ($list as $dt) {
                    $relative_toSubGroup = $dt->getRelativeData->toArray();
                    $is_from_food_and_tobbacco = CategoryRelation::where('category_id', $relative_toSubGroup['parent_id'])
                        ->whereIn('parent_id', [6])
                        ->first();
                    if ($is_from_food_and_tobbacco) {
                        $main_list[$dt->id] = $dt->name;
                    }
                }
                return $main_list;
                // dd($list);
            } elseif ($level == 'getSubmajorGroupsForMenuItems') {
                $list = Category::whereLevel('1');
                $conditions = function ($query) {

                    $query->where('parent_id', '!=', '6');
                };
                $list->whereHas('getRelativeData', $conditions);;
                $list = $list->pluck('name', 'id');
                return $list;
            } elseif ($level == 'getSubmajorGroupsForOffers') {
                $list = Category::whereLevel('1');
                $conditions = function ($query) {

                    $query->where('parent_id', '6');
                };
                $list->whereHas('getRelativeData', $conditions);;
                $list = $list->pluck('name', 'id');
                return $list;
            } elseif ($level == 'getalcoholicfamilyGroups') {
                $list = Category::whereLevel('3')->where('is_have_another_layout', '1')->pluck('name', 'id');
                return $list;
            } elseif ($level == 'getUnalcoholicgroup') {
                $list = Category::whereLevel('3')->where('is_have_another_layout', '!=', '1')->pluck('name', 'id');
                return $list;
            } else {
                $list = Category::whereLevel($level)->pluck('name', 'id');
                return $list;
            }

        }

    }


    public function getDeliveryParentList($level)
    {
        $list = [];
        if ($level == 'getSubMajorGroup') {
            $list = BeerKegCategory::whereLevel(1)->pluck('name', 'id');
            return $list;
        } else if ($level == 'getFamilyGroup') {
            $list = BeerKegCategory::whereLevel(2)->where('is_have_another_layout', '1')->pluck('name', 'id');
            return $list;
        } else {
            return $list;
        }


    }


    public function canWeDeleteThisDeliveryType($type, $id)
    {
        $status = true;


        if ($type == 'FamilyGroup') {
            $is_have_child = BeerAndKegCategoryRelation::where('parent_id', $id)->first();


            $is_have_item = BeerItemsAndCategoryRelation::where('beer_keg_category_id', $id)->first();

            if ($is_have_child || $is_have_item) {
                $status = false;
            }
        }
        return $status;
    }

    public function canWeDeleteThis($type, $id)
    {
        $status = true;

        if ($type == 'SUBMAJORGROUP' || $type == 'MENUITEMGROUP') {
            $is_have_child = CategoryRelation::where('parent_id', $id)->first();
            if ($is_have_child) {
                $status = false;
            }

            if ($type == 'MENUITEMGROUP' && $status == true) {
                $is_have_child = ItemCategoryRelation::where('category_id', $id)->first();
                if ($is_have_child) {
                    $status = false;
                }
            }
        }
        if ($type == 'FAMILYGROUP' || $type == 'SUBFAMILYGROUP') {
            $is_have_child = ItemCategoryRelation::where('category_id', $id)->first();
            $is_have_child_another = CategoryRelation::where('parent_id', $id)->first();
            if ($is_have_child || $is_have_child_another) {
                $status = false;
            }
        }
        return $status;
    }


    function validationHandle($validation)
    {
        foreach ($validation->getMessages() as $field_name => $messages) {
            if (!isset($firstError)) {
                $firstError = $messages[0];
                //$error[$field_name]=$messages[0];
            }
        }
        return $firstError;
    }

    function randomOtp($length = 30)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    function generateOtp($length = 5): int
    {
//        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//        $code = '';
//
//        for ($i = 0; $i < $length; $i++) {
//            $randomIndex = rand(0, strlen($characters) - 1);
//            $code .= $characters[$randomIndex];
//        }

        return random_int(1000, 9999);
    }

    function formatMoney($amount): string
    {
        return 'KES. ' . number_format((float)$amount, 2);
    }


    public function getCondimentList()
    {
        $list = Condiment::pluck('title', 'id');
        return $list;

    }


    function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        /***********************************************************************************************
         * function for get the distance between two coordinates
         * $unit = K(kilometer),M(Miles),N(Natural miles)
         ***********************************************************************************************/
        //echo $lat1.'=='.$lon1.'=='.$lat2.'=='.$lon2.'=='.$unit; die;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return round(($miles * 1.609344), 2);
        }
        if ($unit == "M") {
            return round(($miles * 1.609344), 2) * 1000;
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function getBasicRouteInformation(Route $route, $user, $currentUserLat = null, $currentUserLng = null, $ignoreCenterIntel = false, $overrideCenterCount = true): Route
    {
        $route->is_order_taking_day = false;
        $timeEstimate = $route->sections->sum('time_estimate');
        $route->duration = $timeEstimate > 0 ? CarbonInterval::minutes(ceil($timeEstimate / 60))->cascade()->forHumans() : "$timeEstimate minutes";
        $route->distance = $route->sections->sum('distance_estimate');
        $route->distance = round($route->distance / 1000, 2) . " km";

        if ($overrideCenterCount) {
            $route->centers_count = $route->centers()->count();
        }

        $route->order_taking_day = $this->getOrderTakingDay($route->order_taking_days);
        $route->items_to_be_received = false;
        $route->should_validate_gate_pass  = false;
        $route->gate_pass_code = "";
        $route->target_amount = $route->sales_target;
        $route->target_balance = 0;
        $route->target_percentage = 0;
        $route->override_check_sales_proximity = false;

        $lat_lngs = [];
        foreach ($route->polylines as $polyline) {
            $latLngSectionPairs = json_decode($polyline->lat_lngs);
            if ($latLngSectionPairs) {
                foreach ($latLngSectionPairs as $latLngSectionPair) {
                    $lat_lngs[] = [
                        'lat' => $latLngSectionPair[0],
                        'lng' => $latLngSectionPair[1],
                    ];
                }
            }
        }

        $route->lat_lngs = $lat_lngs;

        $currentUserRole = $user->userRole->slug;

        // Salesman parameter overrides
        if (($currentUserRole == 'salesman') || ($currentUserRole == 'sales-man')) {
            $orderTakingDays = explode(',', $route->order_taking_days);
            if ($orderTakingDays) {
                $today = Carbon::now()->dayOfWeek;
                if (in_array($today, $orderTakingDays)) {
                    $route->is_order_taking_day = true;
                }
            }

            $routeHasOpenShift = SalesmanShift::where('status', 'open')->where('route_id', $route->id)->first();
            if ($route->is_order_taking_day && $routeHasOpenShift) {
                if ($routeHasOpenShift->shift_type == 'offsite') {
                    $route->override_check_sales_proximity = true;
                }

                $route->target_balance = $route->sales_target;
                $today = Carbon::now()->toDateString();
                $orders = WaInternalRequisition::with('getRelatedItem')
                    ->whereDate('created_at', '=', Carbon::parse($today))
                    ->where('route_id', $route->id)
                    ->get();
                $orderTotal = 0;

                foreach ($orders as $order) {
                    $orderTotal += $order->getOrderTotal();
                }

                if ($orderTotal > 0) {
                    $route->target_balance = $route->sales_target - $orderTotal;
                    if ($route->target_balance < 0) {
                        $route->target_balance = 0;
                    }

                    if ($route->sales_target == 0) {
                        $percentage = 100;
                    } else if ($orderTotal == 0 && $route->sales_target == 0) {
                        $percentage = 0;
                    } else {
                        $percentage = floor(($orderTotal / $route->sales_target) * 100);
                    }
                    $route->target_percentage = $percentage;

                    if ($route->target_percentage > 100) {
                        $route->target_percentage = 100;
                    }
                }
            }
        }

        // Delivery man parameter overrides
        if (($currentUserRole == 'delivery') || ($currentUserRole == 'driver')) {
            $route->items_to_be_received = $this->routeHasUnreceivedItems($user);

            $deliverySchedule = DeliverySchedule::with('route')->where('status', 'loaded')->where('gate_pass_status', 'initiated')
                ->forDriver($user->id)->first();
            if ($deliverySchedule) {
                $route->should_validate_gate_pass  = true;
                $route->gate_pass_code = "$deliverySchedule->delivery_number-{$deliverySchedule->route->route_name}";
            }
        }

        $route->target_amount = "KES. " . number_format($route->target_amount, 2);
        $route->target_balance = "KES. " . number_format($route->target_balance, 2);

        if ($overrideCenterCount) {
            $route->shops_count = $route->waRouteCustomer()->count();
        }

        if (!$ignoreCenterIntel) {
            foreach ($route->centers as $center) {
                $center->distance = '0 km';
                $center->duration = '0 minutes';

                $center->unvisited_shops = 0;
                foreach ($center->waRouteCustomers as $shop) {
                    $shop->distance = '0 km';
                    $shop->duration = '0 minutes';
                    $shop->can_edit = false;

                    if ($shop->created_by == 0) {
                        $shop->can_edit = true;
                    }

                    // Shop Photo
                    if ($shop->image_url) {
                        $appUrl = env('APP_URL');
                        $shop->photo = "$appUrl/uploads/shops/" . $shop->image_url;
                    }

                    // Shop visited or not
                    switch ($currentUserRole) {
                        case ($currentUserRole == 'sales-man') || ($currentUserRole == 'salesman'):
                            $shop->visited_by_salesman = false;
                            $currentShift = SalesmanShift::where('status', 'open')->where('salesman_id', $user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->shiftCustomers()->where('route_customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    $shop->visited_by_salesman = $routeCustomer->visited == 1;
                                    if (!$shop->visited_by_salesman) {
                                        $center->unvisited_shops += 1;
                                    }
                                }
                            }
                            break;
                        case ($currentUserRole == 'delivery') || ($currentUserRole == 'driver'):
                            $shop->visited_by_deliveryman = false;
                            $currentShift = DeliveryManShift::where('status', 'open')->where('deliveryman_id', $user->id)->first();
                            if ($currentShift) {
                                $routeCustomer = $currentShift->shiftCustomers()->where('route_customer_id', $shop->id)->first();
                                if ($routeCustomer) {
                                    $shop->visited_by_deliveryman = $routeCustomer->visited == 1;
                                    if (!$shop->visited_by_deliveryman) {
                                        $center->unvisited_shops += 1;
                                    }
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        unset($route->polylines);
        return $route;
    }

    public function getRawRouteInformation(Route $route, $user): Route
    {
        $route->is_order_taking_day = false;
        $timeEstimate = $route->sections->sum('time_estimate');
        $route->duration = $timeEstimate > 0 ? CarbonInterval::minutes(ceil($timeEstimate / 60))->cascade()->forHumans() : "$timeEstimate minutes";
        $route->distance = $route->sections->sum('distance_estimate');
        $route->distance = round($route->distance / 1000, 2) . " km";
        $route->centers_count = $route->centers()->count();
        $route->centers_count = $route->centers()->count();
        $route->shops_count = $route->waRouteCustomer()->count();
        $route->order_taking_day = $this->getOrderTakingDay($route->order_taking_days);
        $route->items_to_be_received = false;
        $route->target_amount = $route->sales_target;
        $route->target_balance = 0;
        $route->target_percentage = 0;
        $route->override_check_sales_proximity = false;

        // Salesman parameter overrides
        if ($user->role_id == 4) {
            $orderTakingDays = explode(',', $route->order_taking_days);
            if ($orderTakingDays) {
                $today = Carbon::now()->dayOfWeek;
                if (in_array($today, $orderTakingDays)) {
                    $route->is_order_taking_day = true;
                }
            }

            $routeHasOpenShift = SalesmanShift::where('status', 'open')->where('route_id', $route->id)->first();
            if ($route->is_order_taking_day && $routeHasOpenShift) {
                if ($routeHasOpenShift->shift_type == 'offsite') {
                    $route->override_check_sales_proximity = true;
                }

                $route->target_balance = $route->sales_target;
                $today = Carbon::now()->toDateString();
                $orders = WaInternalRequisition::with('getRelatedItem')
                    ->whereDate('created_at', '=', Carbon::parse($today))
                    ->where('route_id', $route->id)
                    ->get();
                $orderTotal = 0;

                foreach ($orders as $order) {
                    $orderTotal += $order->getOrderTotal();
                }

                if ($orderTotal > 0) {
                    $route->target_balance = $route->sales_target - $orderTotal;
                    if ($route->target_balance < 0) {
                        $route->target_balance = 0;
                    }

                    if ($route->sales_target == 0) {
                        $percentage = 100;
                    } else if ($orderTotal == 0 && $route->sales_target == 0) {
                        $percentage = 0;
                    } else {
                        $percentage = floor(($orderTotal / $route->sales_target) * 100);
                    }
                    $route->target_percentage = $percentage;

                    if ($route->target_percentage > 100) {
                        $route->target_percentage = 100;
                    }
                }
            }
        }

        // Delivery man parameter overrides
        if ($user->role_id == 6) {
            $route->items_to_be_received = $this->routeHasUnreceivedItems($user);
        }

        $route->target_amount = "KES. " . number_format($route->target_amount, 2);
        $route->target_balance = "KES. " . number_format($route->target_balance, 2);

        return $route;
    }

    private function routeHasUnreceivedItems($user): bool
    {
        $deliverySchedule = DeliverySchedule::with('items')->whereIn('status', ['consolidating', 'consolidated'])->forDriver($user->id)->first();
        if (!$deliverySchedule) {
            return false;
        }

        $dispatchedLoadingSheets = SalesmanShiftStoreDispatch::where('shift_id', $deliverySchedule->shift_id)->where('dispatched', true)->count();
        if ($dispatchedLoadingSheets == 0) {
            return false;
        }

        $unReceivedLoadingSheets = SalesmanShiftStoreDispatch::where('shift_id', $deliverySchedule->shift_id)->where('dispatched', true)
            ->where('received', false)
            ->count();
        return $unReceivedLoadingSheets > 0;
    }

    public function getOrderTakingDay($dayValuesArray): string
    {
        $orderTakingDayValues = explode(',', $dayValuesArray);
        if (!$orderTakingDayValues || (count($orderTakingDayValues) == 0)) {
            return '';
        }

        $today = Carbon::now()->dayOfWeek;
        if (in_array($today, $orderTakingDayValues)) {
            return 'Today';
        }

        $tomorrow = $today + 1;
        if (in_array($tomorrow, $orderTakingDayValues)) {
            return 'Tomorrow';
        }

        sort($orderTakingDayValues);
        return mapDayOfWeekValueToName($orderTakingDayValues[0]);
    }

    public function getDurationOrDistanceBetweenPoints($originLat, $originLng, $destinationLat, $destinationLng)
    {
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'query' => [
                'origins' => $originLat . ',' . $originLng,
                'destinations' => $destinationLat . ',' . $destinationLng,
                'key' => config('app.google_maps_api_key'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['status'] === 'OK') {
            return $data['rows'][0]['elements'][0];
        }

        return null; // Handle the case when the API request fails
    }

    public function getCondimentGroupsList()
    {
        $list = CondimentGroup::pluck('title', 'id');
        return $list;
    }

    public function getAllTaxFromTaxManagers()
    {
        $list = TaxManager::pluck('title', 'id');
        return $list;
    }

    public function getTaxationForItem($food_item)
    {
        $charges_array = [];
        if ($food_item->getManyRelativeTaxes && count($food_item->getManyRelativeTaxes) > 0) {
            $counter = 0;
            foreach ($food_item->getManyRelativeTaxes as $tax) {
                $charges_array[$counter]['charges_name'] = ucfirst($tax->getRelativeTaxdetail->title);
                $charges_array[$counter]['charges_value'] = $tax->getRelativeTaxdetail->tax_value;
                $charges_array[$counter]['charges_format'] = $tax->getRelativeTaxdetail->tax_format;
                $counter++;
            }
        }
        return $charges_array;
    }

    public function getTaxationForOffer($offer)
    {
        $charges_array = [];
        if ($offer->getManyRelativeTaxes && count($offer->getManyRelativeTaxes) > 0) {
            $counter = 0;
            foreach ($offer->getManyRelativeTaxes as $tax) {
                $charges_array[$counter]['charges_name'] = ucfirst($tax->getRelativeTaxdetail->title);
                $charges_array[$counter]['charges_value'] = $tax->getRelativeTaxdetail->tax_value;
                $charges_array[$counter]['charges_format'] = $tax->getRelativeTaxdetail->tax_format;
                $counter++;
            }
        }
        return $charges_array;
    }

    public function getPendingorderCount($user_id)
    {
        $count = Order::where('user_id', $user_id)->whereNotIn('status', ['PENDING', 'CANCLED', 'COMPLETED'])->count();
        return $count;
    }

    public function isTipGiven($order_id)
    {
        $is_given = WaiterTip::where('order_id', $order_id)->first();
        if ($is_given) {
            return true;
        } else {
            return false;
        }
    }

    public function giveLoyalityzpointForSignup($user_id)
    {
        $settings = Setting::whereIn('name', ['GIVE_LOYALTY_POINT_FOR_SIGNUP', 'LOYALTY_POINT_FOR_SIGNUP'])->pluck('description', 'name')->toArray();
        if ($settings['GIVE_LOYALTY_POINT_FOR_SIGNUP'] == '1') {
            $new_row = new LoyaltyPoint();
            $new_row->order_id = null;
            $new_row->user_id = $user_id;
            $new_row->points = $settings['LOYALTY_POINT_FOR_SIGNUP'];
            $new_row->status = 'GIVEN';
            $new_row->points_source = 'SIGNUP';
            $new_row->save();
        }
    }

    public function manageLoyalityPoint($order, $order_default_status)
    {

        if ($order->getAssociateUserForOrder->role_id == '11') {
            $regular_item_price = [];
            $happyhours_item_price = [];
            $settings = Setting::whereIn('name', ['MINIMUM_AMOUNT_FOR_LOYALTY_POINT', 'GIVEN_LOYALTY_POINT', 'ALLOW_LOYALTY_POINT_EARNING', 'HAPPY_HOURS_TIME'])->pluck('description', 'name')->toArray();
            $happy_hours_time = explode('-', $settings['HAPPY_HOURS_TIME']);
            if ($settings['ALLOW_LOYALTY_POINT_EARNING'] == '1') {
                $order_time = strtotime(date('Y-m-d H:i', strtotime($order->created_at)));
                $happy_hours_enabled = false;
                $happy_hours_start_time = '';
                $happy_hours_end_time = '';
                if (count($happy_hours_time) == 2) {
                    $happy_hours_start_time = strtotime(date('Y-m-d') . ' ' . $happy_hours_time[0]);
                    $happy_hours_end_time = strtotime(date('Y-m-d') . ' ' . $happy_hours_time[1]);
                }
                if ($order_time >= $happy_hours_start_time && $order_time <= $happy_hours_end_time) {
                    $happy_hours_enabled = true;
                }
                foreach ($order->getAssociateItemWithOrder as $ordered_item) {
                    if (!$ordered_item->order_offer_id) {
                        if ($ordered_item->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->allow_happy_hours == '1' && $happy_hours_enabled == true) {
                            $happyhours_item_price[] = $ordered_item->price * $ordered_item->item_quantity;
                        } else {
                            $regular_item_price[] = $ordered_item->price * $ordered_item->item_quantity;
                        }
                    }
                }

                foreach ($order->getAssociateOffersWithOrder as $offers) {
                    $regular_item_price[] = $offers->price * $offers->quantity;
                }

                $regular_item_price = array_sum($regular_item_price);
                $happyhours_item_price = array_sum($happyhours_item_price);


                $MINIMUM_AMOUNT_FOR_LOYALTY_POINT = $settings['MINIMUM_AMOUNT_FOR_LOYALTY_POINT'];
                $GIVEN_LOYALTY_POINT = $settings['GIVEN_LOYALTY_POINT'];


                $point_for_per_minimum_amount = $regular_item_price / $MINIMUM_AMOUNT_FOR_LOYALTY_POINT;
                $point_for_per_minimum_amount = intval($point_for_per_minimum_amount) * $GIVEN_LOYALTY_POINT;
                $earned_loyalty_point_for_regular_item = intval($point_for_per_minimum_amount);

                $point_for_per_minimum_amount = $happyhours_item_price / $MINIMUM_AMOUNT_FOR_LOYALTY_POINT;
                $point_for_per_minimum_amount = intval($point_for_per_minimum_amount) * $GIVEN_LOYALTY_POINT;
                $earned_loyalty_point_for_happy_hours = intval($point_for_per_minimum_amount) * 2;

                $earned_loyalty_point = intval($earned_loyalty_point_for_happy_hours + $earned_loyalty_point_for_regular_item);
                if ($earned_loyalty_point > 0) {
                    $new_row = new LoyaltyPoint();
                    $new_row->order_id = $order->id;
                    $new_row->user_id = $order->user_id;
                    $new_row->points = $earned_loyalty_point;
                    if ($order_default_status == 'PENDING') {
                        $new_row->status = 'PENDING';
                    }
                    $new_row->save();
                }
            }
        }
    }

    public function getLoyaltyPointsByUserId($user_id)
    {
        $total_loyalty_point = LoyaltyPoint::where('user_id', $user_id)
            ->where('status', 'GIVEN')
            ->sum('points');

        $total_spent_loyalty_point = LoyaltyPoint::where('user_id', $user_id)
            ->where('status', 'SPENT')
            ->sum('points');

        //  echo $total_loyalty_point.'==='.$total_spent_loyalty_point;die;
        return $total_loyalty_point - $total_spent_loyalty_point;
    }


    public function spentLoyalityPoints($order, $amount)
    {
        $new_row = new LoyaltyPoint();
        $new_row->order_id = $order->id;
        $new_row->user_id = $order->user_id;
        $new_row->points = $amount;
        $new_row->status = 'SPENT';
        $new_row->points_source = 'ORDER';
        $new_row->save();
    }


    public function mypermissionsforAModule()
    {
        $logged_user_info = getLoggeduserProfile();
        
        if (!$logged_user_info) {
            return [];
        }

        if ($logged_user_info->role_id == 1) {
            return 'superadmin';
        } else {
            return $logged_user_info->permissions ?? [];
        }
    }

    public function myUserPermissionsforAModule()
    {
        $logged_user_info = getLoggeduserProfile();

        if ($logged_user_info->role_id == 1) {
            return 'superadmin';
        } else {
            return $logged_user_info->user_permissions;
        }

    }

    public function sendOtpForUsers($otp, $phone_number, $name)
    {
        //$phone_number = '724756011';

        $phone_number = (int)$phone_number;
        if (substr($phone_number, 0, 3) != '254') {
            $phone_number = '254' . $phone_number;
        }
        //$phone_number = '254'.$phone_number;
        $data_string = array("msisdn" => $phone_number, "message" => 'Your otp for sign-up is :' . $otp, "CustomerName" => $name, 'correlator' => strtotime(rand(11, 99) . date('Y-m-d H:i:s')));
        $ch = curl_init('162.243.32.84:7790/sendSMz');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $is_sent = 'Yes';
        if (!strpos($result, "Success")) {
            //otp not sent
            // echo 'not';die;
            $is_sent = 'No';
        }

        // $path    = 'resources/views/checkoutjson.blade.php';
        //$bytes_written = File::put($path, $is_sent);
        return $is_sent . '==' . $phone_number;

    }


    public function sendMessageOnMobileNumber($message, $phone_number)
    {
        //$phone_number = '724756011';

        $phone_number = (int)$phone_number;
        if (substr($phone_number, 0, 3) != '254') {
            $phone_number = '254' . $phone_number;
        }
        //$phone_number = '254'.$phone_number;
        $data_string = array("msisdn" => $phone_number, "message" => $message, "CustomerName" => 'Brew-Restro', 'correlator' => strtotime(rand(11, 99) . date('Y-m-d H:i:s')));
        $ch = curl_init('162.243.32.84:7790/sendSMz');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $is_sent = 'Yes';
        if (!strpos($result, "Success")) {
            //otp not sent
            // echo 'not';die;
            $is_sent = 'No';
        }

        // $path    = 'resources/views/checkoutjson.blade.php';
        //$bytes_written = File::put($path, $is_sent);
        return $is_sent . '==' . $phone_number;

    }


    public function getPluList()
    {
        $listing = [];
        $json = '{"pos_id": "81600692", "cutomer_number": "VA","cutomer_name": " Roy Karugu", "user_id": "JK10", "user_pin": "1234", "amount": "100", "narration": "Fuel Expenses", "trans_type": "GETPLU", "card_uid": "04935822643680","start_date": "2017-05-12","end_date": "2017-05-23","NEWPIN": "1214","transfer_card_number": "2W4C 6ES6","destination_phone_number": "254712863203"}';
        $data_string = array("transDetails" => $json);
        $ch = curl_init('http://138.197.171.157/plu/Recipes.php');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result);

        if (isset($result->status) && $result->status == 'OK' && isset($result->listing)) {

            $list = $result->listing;
            $counter = 0;
            foreach ($list as $detail) {
                $listing[$counter]['title'] = $detail->recipename;
                $listing[$counter]['recipeno'] = $detail->recipeno;
                $counter++;
            }
        }
        sort($listing);
        $listing = array_column($listing, 'title', 'recipeno');
        return $listing;

    }


    public function getGLDetailOld()
    {
        $GLAccount_No_listing = [];
        $GLAccount_Name_listing = [];
        $json = '{"pos_id": "81600692", "cutomer_number": "VA","cutomer_name": "Roy Karugu ", "user_id": "JK10", "user_pin": "1234", "amount": "100", "narration": "Fuel Expenses", "trans_type": "GETAC", "card_uid": "04935822643680","start_date": "2017-05-12","end_date": "2017-05-23","NEWPIN": "1214","transfer_card_number": "2W4C 6ES6","destination_phone_number": "254710136807"}';
        $data_string = array("transDetails" => $json);
        $ch = curl_init('http://138.197.171.157/plu/GL.php');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $result = json_decode($result);

        if (isset($result->status) && $result->status == 'OK' && isset($result->listing)) {

            $list = $result->listing;
            $counter = 0;
            foreach ($list as $detail) {

                $GLAccount_No_listing[$counter]['accountname'] = $detail->accountcode . ' - ' . $detail->accountname;
                $GLAccount_No_listing[$counter]['accountcode'] = $detail->accountcode;
                $counter++;

            }
        }
        sort($GLAccount_No_listing);
        $GLAccount_No_listing = array_column($GLAccount_No_listing, 'accountname', 'accountcode');
        return $GLAccount_No_listing;

    }

    public function getGLDetail()
    {
        $gl_accounts = \App\Model\WaChartsOfAccount::select(DB::raw("CONCAT(account_name,' - ',account_code) AS name"), 'account_code')
            ->pluck('name', 'account_code')
            ->toArray();
        return $gl_accounts;
    }

    public function getUnseenNotificationCount($user_id)
    {
        return Notification::where('user_id', $user_id)->where('is_seen', '0')->count();
    }

    public function createNotification($case, $dataarray)
    {
        switch ($case) {
            case "READYTOPICK":

                $order_id = $dataarray['order_id'];
                $updated_time = $dataarray['updated_at'];

                $order = Order::where('id', $order_id)->first();
                $related_user = $order->getAssociateUserForOrder;
                $user_ids_arra = [];
                if ($related_user->role_id == 11) {
                    //user
                    $user_ids_arra[] = $related_user->id;
                }
                $all_waiter = getAssociateWaiterIdsWithOrder($order);
                if (count($user_ids_arra) > 0) {
                    $data = [];
                    $d = 0;
                    $notification_message = manageOrderidWithPad($order_id) . ' is ready to pick';
                    foreach ($user_ids_arra as $user_id) {
                        $data[$d]['user_id'] = $user_id;
                        $data[$d]['order_id'] = $order_id;
                        $data[$d]['title'] = 'Ready To Pick';
                        $data[$d]['message'] = $notification_message;
                        $data[$d]['created_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $data[$d]['updated_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $d++;
                    }
                    Notification::insert($data);
                    $this->sendNotification($user_ids_arra, $notification_message);
                }

                if (count($all_waiter) > 0) {
                    //$user_ids_arra  = array_merge($user_ids_arra,$all_waiter);
                    $data = [];
                    $d = 0;
                    $table_data = OrderBookedTable::select('table_id')->where('order_id', $order_id)->first();
                    $table_name = '';
                    if ($table_data) {
                        $table_name = $table_data->getRelativeTableData->name;
                    }
                    $notification_message = manageOrderidWithPad($order_id) . ' is ready to pick for table no: ' . $table_name;
                    foreach ($all_waiter as $user_id) {
                        $data[$d]['user_id'] = $user_id;
                        $data[$d]['order_id'] = $order_id;
                        $data[$d]['title'] = 'Ready To Pick';
                        $data[$d]['message'] = $notification_message;
                        $data[$d]['created_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $data[$d]['updated_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $d++;
                    }
                    Notification::insert($data);
                    $this->sendNotification($all_waiter, $notification_message);
                }
                break;
            case "READYTOPREPAIR":

                $order_id = $dataarray['order_id'];
                $updated_time = $dataarray['updated_at'];

                $order = Order::where('id', $order_id)->first();
                /*$related_user = $order->getAssociateUserForOrder;
                $user_ids_arra = [];
                if($related_user->role_id==11)
                {
                    
                    $user_ids_arra[] = $related_user->id;
                }*/

                $all_waiter = getAssociateWaiterIdsWithOrder($order);
                /*
                if(count($user_ids_arra)>0)
                {
                    $data = [];
                    $d = 0;
                    $notification_message = manageOrderidWithPad($order_id).' is ready to pick';
                    foreach($user_ids_arra as $user_id)
                    {
                        $data[$d]['user_id'] = $user_id;
                        $data[$d]['order_id'] = $order_id;
                        $data[$d]['title'] = 'Ready To Pick';
                        $data[$d]['message'] = $notification_message;
                        $data[$d]['created_at'] = date('Y-m-d H:i:s',strtotime($updated_time));
                         $data[$d]['updated_at'] = date('Y-m-d H:i:s',strtotime($updated_time));
                        $d++;
                    }
                    Notification::insert($data);
                    $this->sendNotification($user_ids_arra,$notification_message);
                }*/

                if (count($all_waiter) > 0) {

                    $data = [];
                    $d = 0;
                    $table_data = OrderBookedTable::select('table_id')->where('order_id', $order_id)->first();
                    $table_name = '';
                    if ($table_data) {
                        $table_name = $table_data->getRelativeTableData->name;
                    }
                    $notification_message = manageOrderidWithPad($order_id) . ' is ready to prepair for table no: ' . $table_name;
                    foreach ($all_waiter as $user_id) {
                        $data[$d]['user_id'] = $user_id;
                        $data[$d]['order_id'] = $order_id;
                        $data[$d]['title'] = 'Ready To Prepair';
                        $data[$d]['message'] = $notification_message;
                        $data[$d]['created_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $data[$d]['updated_at'] = date('Y-m-d H:i:s', strtotime($updated_time));
                        $d++;
                    }
                    Notification::insert($data);
                    $this->sendNotification($all_waiter, $notification_message);
                }
                break;


            default:
                return true;
        }
    }

    /* public function checknotification()
     {
         $url = 'https://fcm.googleapis.com/fcm/send';
           $server_key = 'AIzaSyCzZ4QXkM484MKxp4eoNCchwVd0zIUBSyA';
         $fields = array
             (
                 'priority'             => "high",
                 'notification'         => array( "title"=>"Brew-Restro", "body" =>'test')
             );
             
             $fields['to'] = 'dGuVef20Oms:APA91bHnJ7KS9vcaT3U-fjJcQiEqRVi_M4Gh3_fZOjgkmRYD9l4MDa00qsb5IMsOn8ez5fzIIBoDrlT4VcE-nqpLhL7Z8B6vizlVxE6xIE59RJusWatrPbeDwVG2guKgVkl0VPYqz_5p';
             
             $headers = array(
                             'Content-Type:application/json',
                             'Authorization:key='.$server_key
                         );  
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
             $result = curl_exec($ch);
             dd($result);
             curl_close($ch);
     }*/

    public function sendNotification($user_ids_array, $msg)
    {


        /*only for user*/
        $user_ids_array = User::select('id', 'role_id')->whereIn('id', $user_ids_array)->where('role_id', '11')->pluck('id')->toArray();
        $user_ids_array = count($user_ids_array) > 0 ? $user_ids_array : [0];

        $url = 'https://fcm.googleapis.com/fcm/send';
        $all_devices = UserDevice::select('device_type', 'device_id')->whereIn('user_id', $user_ids_array)->get();
        $iphone_array = [];
        $android_array = [];
        $server_key = 'AIzaSyCzZ4QXkM484MKxp4eoNCchwVd0zIUBSyA';
        if (count($all_devices) > 0) {
            foreach ($all_devices as $device) {
                if ($device->device_type == 'ANDROID') {
                    $android_array[] = $device->device_id;
                }
                if ($device->device_type == 'IPHONE') {
                    $iphone_array[] = $device->device_id;
                }
            }
        }

        if (count($iphone_array) > 0) {

            $fields = array
            (
                'priority' => "high",
                'notification' => array("title" => "Brew-Restro", "body" => $msg, 'sound' => 'default')

            );


            if (count($iphone_array) > 1) {
                $fields['registration_ids'] = $iphone_array;
            } else {
                $fields['to'] = $iphone_array[0];
            }
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $server_key
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);


        }
        if (count($android_array) > 0) {


            $fields = array
            (
                'priority' => "high",
                'notification' => array("title" => "Brew-Restro", "body" => $msg, 'sound' => 'default'),
                'data' => array("title" => 'Brew-Restro', 'type' => 'nofic', 'notify_msg' => $msg, 'msg' => $msg)
            );
            if (count($android_array) > 1) {
                $fields['registration_ids'] = $android_array;
            } else {
                $fields['to'] = $android_array[0];
            }
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $server_key
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        }

    }

    public function updateOrderStatusFromPendingToNew($order_id, $user_id, $transaction_id)
    {

        Order::where('id', $order_id)->update(['status' => 'NEW_ORDER', 'transaction_id' => $transaction_id]);
        OrderedItem::where('order_id', $order_id)->update(['item_delivery_status' => 'NEW']);
        $all_booked_tables_ids = OrderBookedTable::where('order_id', $order_id)->pluck('table_id')->toArray();
        if (count($all_booked_tables_ids) > 0) {
            TableManager::whereIn('id', $all_booked_tables_ids)->update(
                ['booking_status' => 'BOOKED', 'booking_for_user_id' => $user_id]
            );
        }

        LoyaltyPoint::where('order_id', $order_id)->update(['status' => 'GIVEN']);

    }

    public function makeReceipt($order_ids_arr, $user_id)
    {
        $receipt = new OrderReceipt();

        $receipt->user_id = $user_id;

        $receipt->save();
        $receipt_id = $receipt->id;
        foreach ($order_ids_arr as $order_id) {
            OrderReceiptRelation::updateOrCreate(
                ['order_receipt_id' => $receipt_id, 'order_id' => $order_id]
            );
        }
        return $receipt_id;
        //OrderReceiptRelation
    }

    public function managepaymentSummary($receipt_id, $new_order)
    {
        $billreceipt = new ReceiptSummaryPayment();
        $billreceipt->order_receipt_id = $receipt_id;
        $billreceipt->payment_mode = $new_order->payment_mode;
        $billreceipt->narration = '';
        $billreceipt->amount = $new_order->order_final_price;
        $billreceipt->mpesa_request_id = isset($new_order->mpesa_request_id) ? $new_order->mpesa_request_id : null;
        $billreceipt->save();
    }

    public function managetimeForall($receipt_id, $time)
    {
        OrderReceiptRelation::where('order_receipt_id', $receipt_id)->update(['created_at' => date('Y-m-d H:i:s', strtotime($time))]);
        $all_orders = OrderReceiptRelation::select('order_id')->where('order_receipt_id', $receipt_id)->get();
        foreach ($all_orders as $order) {
            Order::where('id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($time))]);
            OrderedItem::where('order_id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($time))]);
            OrderOffer::where('order_id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($time))]);
        }
        ReceiptSummaryPayment::where('order_receipt_id', $receipt_id)->update(['created_at' => date('Y-m-d H:i:s', strtotime($time))]);
    }

    public function managetimeForallDelivery($receipt_id, $time)
    {
        DeliveryOrderReceiptRelation::where('delivery_order_receipt_id', $receipt_id)->update(['created_at' => date('Y-m-d H:i:s', strtotime($time))]);
        $all_orders = DeliveryOrderReceiptRelation::select('delivery_order_id')->where('delivery_order_receipt_id', $receipt_id)->get();
        foreach ($all_orders as $order) {
            DeliveryOrder::where('id', $order->delivery_order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($time))]);
            DeliveryOrderItem::where('delivery_order_id', $order->delivery_order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($time))]);

        }
        DeliveryReceiptSummaryPayment::where('delivery_order_receipt_id', $receipt_id)->update(['created_at' => date('Y-m-d H:i:s', strtotime($time))]);


    }


    public function managetimeForallCron()
    {
        $all_receipts = OrderReceipt::select('id', 'created_at')->get()->toArray();
        foreach ($all_receipts as $receipt) {

            OrderReceiptRelation::where('order_receipt_id', $receipt['id'])->update(['created_at' => date('Y-m-d H:i:s', strtotime($receipt['created_at']))]);

            $all_orders = OrderReceiptRelation::select('order_id')->where('order_receipt_id', $receipt['id'])->get();
            foreach ($all_orders as $order) {
                Order::where('id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($receipt['created_at']))]);
                OrderedItem::where('order_id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($receipt['created_at']))]);
                OrderOffer::where('order_id', $order->order_id)->update(['billing_time' => date('Y-m-d H:i:s', strtotime($receipt['created_at']))]);
            }


            ReceiptSummaryPayment::where('order_receipt_id', $receipt['id'])->update(['created_at' => date('Y-m-d H:i:s', strtotime($receipt['created_at']))]);


        }
        echo 'ff';
        die;
    }

    public function getWalletBalanceByPhoneNumber($phoneNumber)
    {
        $row = Wallet::select('amount')->where('phone_number', $phoneNumber)->first();

        return $row ? (float)$row->amount : 0;
    }

    public function getWalletBalanceByUserId($user_id)
    {
        $row = Wallet::select('amount')->where('user_id', $user_id)->first();

        return $row ? (float)$row->amount : 0;
    }

    public function updateWalletAmount($phoneNumber)
    {
        $cr_amount = WalletTransaction::where('phone_number', $phoneNumber)->where('transaction_type', 'CR')->sum('amount');
        $dr_amount = WalletTransaction::where('phone_number', $phoneNumber)->where('transaction_type', 'DR')->sum('amount');
        $left_balance = round($cr_amount - $dr_amount, 2);
        $wallet = Wallet::where('phone_number', $phoneNumber)->first();
        if (!$wallet) {
            $wallet = new Wallet();
        }
        $wallet->phone_number = $phoneNumber;
        $wallet->amount = $left_balance;
        $wallet->save();
    }

    public function jsonify($payload, $statusCode = 200): JsonResponse
    {
        return response()->json($payload, $statusCode);
    }
}