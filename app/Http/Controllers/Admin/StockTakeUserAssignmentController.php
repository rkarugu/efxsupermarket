<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UserItemAllocation;
use App\Imports\BulkUserItemAllocationImport;
use App\Model\Restaurant;
use App\Model\User;
use App\Model\WaInventoryCategory;
use App\Model\WaInventoryLocationUom;
use App\Model\WaInventoryItem;
use App\Model\WaLocationAndStore;
use App\Models\DisplayBinUserItemAllocation;
use App\Models\StockTakeUserAssignment;
use App\Models\StockTakeUserAssignmentAssignee;
use App\Services\ExcelDownloadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class StockTakeUserAssignmentController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'stock-take-user-assignment';
        $this->title = 'stock-take-user-assignment';
        $this->pmodule = 'stock-take-user-assignment';
        $this->basePath = 'admin.stock_take_users';
    }
    public function index(Request $request)
    {
        $from = $request->start_date ? Carbon::parse($request->start_date)->toDateString() : Carbon::now()->toDateString();
        $to = $request->end_date ? Carbon::parse($request->end_date)->toDateString() : Carbon::now()->toDateString();
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $user = Auth::user();
        $branches = WaLocationAndStore::all();
        if($user->role_id == 152){
            $stockTakeAssignments = StockTakeUserAssignment::latest()->where('uom_id', $user->wa_unit_of_measures_id);
        }else{
            $stockTakeAssignments = StockTakeUserAssignment::latest();
        }
        if($request->branch){
            $stockTakeAssignments = $stockTakeAssignments->where('wa_location_and_store_id', $request->branch);
        }
        $stockTakeAssignments = $stockTakeAssignments->whereBetween('stock_take_date', [$from, $to])->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
           
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.stock_take_users.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'stockTakeAssignments','branches', 'user'));
        } else {
            Session::flash('warning', 'You do not have permission to view this page');
            return redirect()->back();
        }
    }

    public function create()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $breadcum = [$title => route('admin.stock-counts-users-assingment'), 'Create' => ''];
        $user = Auth::user();
        $users = User::whereIn('role_id', [152, 168, 176,169,170])->where('restaurant_id',$user->restaurant_id)->get();
        $categoriesInBin = WaInventoryLocationUom::where('uom_id', $user->wa_unit_of_measures_id)
            ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
            ->pluck('wa_inventory_items.wa_inventory_category_id')
            ->toArray();
        if($user->role_id == 152){
            $categories = WaInventoryCategory::whereIn('id', $categoriesInBin)->get();
        }else{
            $categories = WaInventoryCategory::all();
        }
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            return view($basePath . '.create', compact('title', 'model', 'breadcum','users', 'categories'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            
            
            $user = Auth::user();
            $assignment  = new StockTakeUserAssignment();
            $assignment->stock_take_date = Carbon::parse($request->stock_take_date)->toDateString();
            $assignment->created_by = $user->id;
            // $assignment->user_id = $request->user;
            $assignment->uom_id = $user->wa_unit_of_measures_id ?? 15;
            $assignment->wa_location_and_store_id = $user->wa_location_and_store_id;
            $assignment->category_ids = implode(",",$request->category);
            $assignment->save();
            
            foreach ($request->user as $value) {
                StockTakeUserAssignmentAssignee::create([
                    'user_id' => $value,
                    'stock_take_user_assignment_id' => $assignment->id,
                ]);
            }
       
            DB::commit();
        return redirect()->route('admin.stock-counts-users-assingment')->with('success', 'Stock Take Assignment Created successfully' );
        }catch(\Throwable $e){
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
    }
    public function edit($id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $breadcum = [$title => route('admin.stock-counts-users-assingment'), 'Create' => ''];
        $users = User::whereIn('role_id', [152, 168, 176,169,170])->get();
        $user = Auth::user();
        $categoriesInBin = WaInventoryLocationUom::where('uom_id', $user->wa_unit_of_measures_id)
        ->leftJoin('wa_inventory_items', 'wa_inventory_items.id', '=', 'wa_inventory_location_uom.inventory_id')
        ->pluck('wa_inventory_items.wa_inventory_category_id')
        ->toArray();
        if($user->role_id == 152){
            $categories = WaInventoryCategory::whereIn('id', $categoriesInBin)->get();
        }else{
            $categories = WaInventoryCategory::all();
        }
        $assignment = StockTakeUserAssignment::find($id);

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            
            return view($basePath . '.edit', compact('title', 'model', 'breadcum','users', 'categories', 'assignment'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $user = Auth::user();
            $assignment  = StockTakeUserAssignment::find($id);
            $assignment->stock_take_date = Carbon::parse($request->stock_take_date)->toDateString();
            // $assignment->user_id = $request->user;
            $assignment->category_ids = implode(",",$request->category);
            $assignment->save();

            DB::table('stock_take_user_assignment_assignees')->where('stock_take_user_assignment_id', $id)->delete();
            foreach ($request->user as $value) {
                StockTakeUserAssignmentAssignee::create([
                    'user_id' => $value,
                    'stock_take_user_assignment_id' => $assignment->id,
                ]);
            }
            
            DB::commit();
            return redirect()->route('admin.stock-counts-users-assingment')->with('success', 'Stock Take Assignment updated successfully' );

        }catch(\Throwable $e){
            DB::rollBack();
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
    }
    public function uploadItemsIndex(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = WaLocationAndStore::all();
        $users = User::whereIn('role_id', [169, 170, 181])->get();
        // $users =  User::all();
        if (isset($permission[$pmodule . '___upload']) || $permission == 'superadmin') {   
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.stock_take_users.user_items_uploads', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'users'));
        } else {
            Session::flash('warning', 'You do not have permission to view this page');
            return redirect()->back();
        }
    }

    public function uploadUserItemAssignments(Request $request)
    {
        $user = User::find($request->user);
        if(!$user->wa_unit_of_measures_id){
            return redirect()->back()->with('warning', 'Selected User does not  have a bin location.');
        }
        if(!$user->wa_location_and_store_id){
            return redirect()->back()->with('warning', 'Selected User does not  have a  store location.');
        }
        $data = [];
        if($request->intent  && $request->intent == 'Template'){
            $payload = [
                'stock id code' => 'ABCYZ',
            ];
            $data[] = $payload;
            return ExcelDownloadService::download($user->name.'_items_template', collect($data), ['ITEM CODE']);
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $import = new UserItemAllocation($user->id, $user->wa_unit_of_measures_id, $user->wa_location_and_store_id);
        Excel::import($import, $request->file('file'));

       $duplicates = $import->getDuplicates();

        if ($duplicates->isNotEmpty()) {
            return redirect()->back()->with('warning', 'Item(s) '.$duplicates->pluck('item_code')->implode(', ').' is invalid');
        }

        return redirect()->route('admin.stock-count.user-item-assignments.all')->with('success', 'Upload successfull');

    }
    public function batchUploadItemsIndex(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = WaLocationAndStore::all();
        $users = User::whereIn('role_id', [169, 170, 181])->get();
        // $users =  User::all();
        if (isset($permission[$pmodule . '___upload']) || $permission == 'superadmin') {   
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.stock_take_users.batch_upload', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'users'));
        } else {
            Session::flash('warning', 'You do not have permission to view this page');
            return redirect()->back();
        }
    }
    public function batchUploadUserItemAssignments(Request $request)
    {
       
        $data = [];
        if($request->intent  && $request->intent == 'Template'){
            $payload = [
                'stock id code' => 'ABCYZ',
                'user id' => 7
            ];
            $data[] = $payload;
            return ExcelDownloadService::download('bulk_items__upload_template', collect($data), ['ITEM CODE', 'USER ID']);
        }
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        $import = new BulkUserItemAllocationImport();
        Excel::import($import, $request->file('file'));

       $duplicates = $import->getDuplicates();

        if ($duplicates->isNotEmpty()) {
            return redirect()->back()->with('warning', 'Item(s) '.$duplicates->pluck('item_code')->implode(', ').' is invalid'.' or Users(s) '.$duplicates->pluck('user_id')->implode(', ').' is invalid');
        }

        return redirect()->route('admin.stock-count.user-item-assignments.all')->with('success', 'Upload successfull');

    }
    public function displayBinStockAssignments(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branches = WaLocationAndStore::all();
        $users = User::whereIn('role_id', [169, 170, 181])->get();
        $user = Auth::user();
        $allocations = DisplayBinUserItemAllocation::with(['getRelatedUser', 'getRelatedStore', 'getRelatedBin', 'getRelatedItem']);
        if($request->branch){
            $allocations = $allocations->where('wa_location_and_store_id', $request->branch);
        }
        if($request->user){
            $allocations = $allocations->where('user_id', $request->user);
        }
        $allocations = $allocations->get();
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {   
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            
            if($request->intent && $request->intent == 'excel'){
                $data = [];
                foreach ($allocations as $allocation) {
                    $payload = [
                        'bin' => $allocation->getRelatedBin?->title,
                        'user' => $allocation->getRelatedUser?->name,
                        'code' => $allocation->getRelatedItem?->stock_id_code,
                        'Item' => $allocation->getRelatedItem?->title,

                    ];
                    $data[] = $payload;
                }
                return ExcelDownloadService::download('display_bins_user_item_allocations', collect($data), ['BIN LOCATION', 'USER', 'CODE', 'ITEM']);

            }
            return view('admin.stock_take_users.display_bin_assignments', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'branches', 'users','user','allocations'));
        } else {
            Session::flash('warning', 'You do not have permission to view this page');
            return redirect()->back();
        }

    }
    public function addAllocation(Request $request)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $branch_id = Auth::user()->restaurant_id;
        $users = User::whereIn('role_id', [169, 170, 181])->get();
        $items = WaInventoryItem::where('status', 1)->get();
        
        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {   
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.stock_take_users.add_allocation', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'users', 'items'));
        } else {
            Session::flash('warning', 'You do not have permission to view this page');
            return redirect()->back();
        }
    }
    public function storeAllocation(Request $request)
    {
        try {
            $user = User::find($request->user);
            if(!$user->wa_unit_of_measures_id){
                return redirect()->back()->with('warning', 'Selected User does not  have a bin location.');
            }
            if(!$user->wa_location_and_store_id){
                return redirect()->back()->with('warning', 'Selected User does not  have a  store location.');
            }
            $existingAllocation = DisplayBinUserItemAllocation::where('user_id', $user->id)->where('wa_inventory_item_id')->get();
            if($existingAllocation->count() > 0){
                return redirect()->back()->with('warning', 'User already has a stock count assignment for this location.');
            }
            $allocation = new DisplayBinUserItemAllocation();
            $allocation->wa_inventory_item_id = $request->item;
            $allocation->user_id = $user->id;
            $allocation->wa_location_and_store_id = $user->wa_location_and_store_id;
            $allocation->bin_id = $user->wa_unit_of_measures_id;
            $allocation->save();
            return redirect()->route('admin.stock-count.user-item-assignments.all')->with('success', 'Allocation created successfully');
        } catch (\Throwable $th) {
            Session::flash('warning', $th);
            return redirect()->back();
            
        }
       
        
      
    }
    public function destroyAllocation($id)
    {
        DisplayBinUserItemAllocation::destroy($id);
        return redirect()->route('admin.stock-count.user-item-assignments.all')->with('success', 'Allocation deleted successfully');
    }
    public function transferAllocation(Request $request){
        try{
            $items =  DisplayBinUserItemAllocation::where('user_id', $request->from_user)->get();
            foreach($items as$allocation){
                $allocation->user_id = $request->to_user;
                $allocation->save();
            }
            return $this->jsonify(['message' => 'Items Transfered Successfully']);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
