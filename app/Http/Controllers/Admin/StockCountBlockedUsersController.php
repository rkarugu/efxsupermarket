<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use App\Models\BlockUsersExemptionSchedule;
use App\Models\BlockUsersExemptionScheduleUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

class StockCountBlockedUsersController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'stock-count-blocked-users';
        $this->title = 'Stock Count Blocked Users';
        $this->pmodule = 'stock-count-blocked-users';
        $this->basePath = 'admin.stock_count_blocked_users';
    }
    public function index(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $yesterday = Carbon::now()->subDays(1)->toDateString();
        $restaurants = Restaurant::all();
        $authuser = Auth::user();
        // $users = DB::table('users')
        //         ->select(
        //             'users.id as id',
        //             'users.name as name',
        //             'wa_unit_of_measures.title as bin',
        //             'wa_unit_of_measures.id as uom_id',
        //             DB::raw("(SELECT COUNT(DISTINCT category_id) FROM
        //                 wa_stock_count_variation WHERE DATE(wa_stock_count_variation.created_at) = '$yesterday'
        //                 AND wa_stock_count_variation.uom_id = users.wa_unit_of_measures_id AND wa_stock_count_variation.reference != 'system generated'
        //             )  as counted_categories"),
        //             DB::raw("(SELECT COUNT(DISTINCT wa_inventory_item_id) FROM
        //                 wa_stock_count_variation WHERE DATE(wa_stock_count_variation.created_at) = '$yesterday'
        //                 AND wa_stock_count_variation.uom_id = users.wa_unit_of_measures_id  AND wa_stock_count_variation.reference != 'system generated'
        //             )  as counted_items"),
        //             DB::raw("(SELECT COUNT(DISTINCT inventory_id) FROM
        //                 wa_inventory_location_uom 
        //                 LEFT JOIN  wa_inventory_items  ON wa_inventory_items.id = wa_inventory_location_uom.inventory_id
        //                 WHERE wa_inventory_location_uom.uom_id = users.wa_unit_of_measures_id
        //                 AND wa_inventory_items.status = 1
        //             )  as total_items"),
        //             DB::raw("(SELECT COUNT(DISTINCT wa_inventory_items.wa_inventory_category_id) 
        //                 FROM wa_inventory_location_uom 
        //                 LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_uom.inventory_id
        //                 WHERE wa_inventory_location_uom.uom_id = users.wa_unit_of_measures_id
        //                 AND wa_inventory_items.status = 1
        //             )  as total_categories"),
        //             DB::raw("(SELECT COUNT(DISTINCT inventory_id) FROM
        //                 wa_inventory_location_uom 
        //                 LEFT JOIN wa_inventory_items ON  wa_inventory_items.id  = wa_inventory_location_uom.inventory_id
        //                 WHERE wa_inventory_location_uom.uom_id = users.wa_unit_of_measures_id
        //                 AND wa_inventory_items.wa_inventory_category_id IN (SELECT category_id FROM wa_stock_count_variation
        //                 WHERE DATE(wa_stock_count_variation.created_at) = '$yesterday' AND wa_stock_count_variation.uom_id = users.wa_unit_of_measures_id)  
        //             )  as total_items_in_counted_categories"),
                    
        //         )
        //         ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', '=', 'users.wa_unit_of_measures_id')
        //         ->where('is_blocked', 1);
        //     if($request->branch){
        //         $users = $users->where('users.restaurant_id', $request->branch);
        //     }else{
        //         $users = $users->where('users.restaurant_id', $authuser->restaurant_id);
        //     }
        //         $users = $users->get();
        $yesterdayStart = Carbon::yesterday()->startOfDay()->toDateTimeString();
        $yesterdayEnd = Carbon::yesterday()->endOfDay()->toDateTimeString();

        $users = DB::table('users')
            ->select(
                'users.id as id',
                'users.name as name',
                'wa_unit_of_measures.title as bin',
                'wa_unit_of_measures.id as uom_id',
                DB::raw('COUNT(DISTINCT wscv.category_id) as counted_categories'),
                DB::raw('COUNT(DISTINCT wscv.wa_inventory_item_id) as counted_items'),
                DB::raw('COUNT(DISTINCT wilu.inventory_id) as total_items'),
                DB::raw('COUNT(DISTINCT wa_inventory_items.wa_inventory_category_id) as total_categories'),
                DB::raw('COUNT(DISTINCT wilu_in_counted.inventory_id) as total_items_in_counted_categories')
            )
            ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', '=', 'users.wa_unit_of_measures_id')
            ->leftJoin('wa_stock_count_variation as wscv', function ($join) use ($yesterdayStart, $yesterdayEnd) {
                $join->on('wscv.uom_id', '=', 'users.wa_unit_of_measures_id')
                    ->whereBetween('wscv.created_at', [$yesterdayStart, $yesterdayEnd])
                    ->where('wscv.reference', '!=', 'system generated');
            })
            ->leftJoin('wa_inventory_location_uom as wilu', 'wilu.uom_id', '=', 'users.wa_unit_of_measures_id')
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wilu.inventory_id')
            ->leftJoin('wa_inventory_location_uom as wilu_in_counted', function ($join) use ($yesterdayStart, $yesterdayEnd) {
                $join->on('wilu_in_counted.uom_id', '=', 'users.wa_unit_of_measures_id')
                    ->whereIn('wa_inventory_items.wa_inventory_category_id', function ($query) use ($yesterdayStart, $yesterdayEnd) {
                        $query->select('category_id')
                            ->from('wa_stock_count_variation')
                            ->leftJoin('wa_inventory_items as inner_items', 'inner_items.id', 'wa_stock_count_variation.wa_inventory_item_id')
                            ->where('inner_items.status', 1)
                            ->whereBetween('wa_stock_count_variation.created_at', [$yesterdayStart, $yesterdayEnd]);
                    });
            })
            ->where('users.is_blocked', 1)
            ->where('wa_inventory_items.status', 1);

        if ($request->branch) {
            $users->where('users.restaurant_id', $request->branch);
        } else {
            $users->where('users.restaurant_id', $authuser->restaurant_id);
        }

        $users = $users->groupBy('users.id')
            ->get();

        
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
           
            return view('admin.stock_count_blocked_users.index', compact('title', 'model', 'pmodule', 'permission','users', 'restaurants', 'authuser'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }
    public function unblockAll()
    {
        $users = User::where('is_blocked', 1)->with(['uom'])->get();
        foreach ($users as $user) {
            $user->is_blocked = 0;
            $user->block_reason = '';
            $user->save();
        }
        Session::flash('success', 'All Users Unblocked Successfully');
        return redirect()->back();
    }
    public function unblockSelected(Request $request)
    {
        $selectedUsers = $request->input('selected_users');
        if ($selectedUsers) {
            foreach ($selectedUsers as $user_id) {
                $user = User::find($user_id);
                $user->is_blocked = 0;
                $user->block_reason  =  '';
                $user->save();
            }
        }

        return redirect()->back()->with('success', 'Selected users have been unblocked.');
    }

    public function blockUserExemptionSchedules(Request $request){
        $model = 'stock-count-blocked-users-exemption-schedules';
        $permission = $this->mypermissionsforAModule();
        $pmodule = 'stock-count-blocked-users-exemption-schedules';
        $title = $this->title;
        $basePath = $this->basePath;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $tomorrow = Carbon::tomorrow()->toDateString();
            $exemptedUsers = DB::table('block_users_exemption_schedule_users')
                ->select(
                    'block_users_exemption_schedule_users.*',
                    'users.name as storekeeper',
                    'creator.name as creator',
                    'wa_location_and_stores.location_name as store',
                    'wa_unit_of_measures.title as bin'   
                )
                ->leftJoin('block_users_exemption_schedules', 'block_users_exemption_schedules.id', 'block_users_exemption_schedule_users.schedule_id')
                ->leftJoin('users', 'users.id', 'block_users_exemption_schedule_users.user_id')
                ->leftJoin('wa_location_and_stores', 'wa_location_and_stores.id', 'users.wa_location_and_store_id')
                ->leftJoin('wa_unit_of_measures', 'wa_unit_of_measures.id', 'users.wa_unit_of_measures_id')
                ->leftJoin('users as creator', 'block_users_exemption_schedule_users.added_by', 'creator.id')
                ->where('block_users_exemption_schedules.target_date', $tomorrow)
                ->get();
            $exemptedUsersId = $exemptedUsers->pluck('user_id')->toArray();

            $users  = User::where('role_id', 152)->whereNotIn('id', $exemptedUsersId)->get();

           
            return view('admin.stock_count_blocked_users.exemption_schedules', compact('title', 'model', 'pmodule', 'permission', 'tomorrow', 'users', 'exemptedUsers'));
        } else {
            Session::flash('warning', 'Unauthorized');
            return redirect()->back();
        }

    }
    public function storeExemptionScheduleUsers(Request $request){
        try {
            $user = Auth::user();
            $schedule =  BlockUsersExemptionSchedule::where('target_date', Carbon::tomorrow()->toDateString())->first();
            foreach($request->users as $storeKeeper){
                $record = new BlockUsersExemptionScheduleUser();
                $record->user_id = $storeKeeper;
                $record->schedule_id = $schedule->id;
                $record->added_by = $user->id;
                $record->save();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Users exempted successfully!'
            ], 200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function deleteUser(Request $request)
    {
        try {
            $user = BlockUsersExemptionScheduleUser::find($request->id);
        
            if ($user) {
                $user->delete();
                return response()->json(['message' => 'User removed successfully.'], 200);
            }
    
            return response()->json(['message' => 'record not found.'], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
      
    }

}
