<?php

namespace App\Http\Controllers\Admin;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Model\WaAssets;
use App\Model\WaUnitOfMeasure;
use App\Model\WaChartsOfAccount;
use App\Model\WaAssetCategory;
use App\Model\WaAssetLocation;
use App\Model\TaxManager;
use App\Model\WaAssetDepreciation;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'assets';
        $this->title = 'Assets Management';
        $this->pmodule = 'assets';
        
    } 

    public function index(Request $request)
    {
        if (!can('view', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();

        if(request()->wantsJson())
        {
            $assets = WaAssets::query();
            return DataTables::of($assets)
                ->toJson();
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaAssets::getDataModel($limit , $start , $search, $this->sortable_columns[$orderby], $order);            
            $totaldepartment       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['amount'] = '-';
                $data[$key]['links'] = view('admin.assets.links',['data'=>$re])->render();
            }
            $response['response'] = $data;
            // dd($response['response']);

            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totaldepartment),
                "recordsTotal"      =>  intval( $totaldepartment),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $model = $this->model;
        $title = $this->title;
        $pmodule = $this->pmodule;
        return view('admin.assets.index',compact('model','title','pmodule'));
    }

    //Location
    public function location_index(Request $request)
    {
        
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission['assets-location___view']) && $permission != 'superadmin') {
            return redirect()->route('admin.dashboard');
        }
        if($request->ajax())
        {
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $dataDelete = WaAssets::where('wa_asset_location_id','!=',NULL)->get()->pluck('wa_asset_location_id')->toArray();

            $response       = WaAssetLocation::getDataModel($limit , $start , $search, null, $order);            
            $totaldepartment       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = view('admin.assets.location.links',['data'=>$re,'dataDelete'=>$dataDelete])->render();
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totaldepartment),
                "recordsTotal"      =>  intval( $totaldepartment),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = 'assets-location';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        return view('admin.assets.location.index')->with($data);
    }

    public function location_add(Request $request)
    {
        if (!can('add-location', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        
        $data['model'] = 'assets-location';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        $data['asset_location'] = WaAssetLocation::orderBy('id','DESC')->get();
        $data['branches'] = DB::table('restaurants')->select('id','name')->get();
        return view('admin.assets.location.add')->with($data);
    }

    public function location_save(Request $request)
    {
        if (!can('add-location', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $request->validate([ 
            'location_id' => 'required|string|min:1|max:255',
            'location_description' => 'required|string|min:1|max:255',
            'location_parent' => 'nullable|exists:wa_asset_locations,id',
            'branch' => 'required|exists:restaurants,id'
        ]);

        DB::beginTransaction();
        try {
            WaAssetLocation::create([
                'location_ID' => $request->location_id,
                'location_description' => $request->location_description,
                'wa_asset_locations_id' => $request->wa_asset_locations_id  , 
                'restaurant_id' => $request->branch      
            ]);
            DB::commit();
            request()->session()->flash('success', 'Asset Location Added Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }
        
        return redirect(route('assets.location.index'));
    }

    public function location_edit($id)
    {
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission['assets-location___edit']) && $permission != 'superadmin') {
            return redirect()->route('admin.dashboard');
        }
        $data['model'] = 'assets-location';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        $data['asset_location'] = WaAssetLocation::orderBy('id','DESC')->get();
        $data['data'] = WaAssetLocation::findOrFail($id);
        $data['branches'] = DB::table('restaurants')->select('id','name')->get();
        return view('admin.assets.location.edit')->with($data);
    }

    public function location_update(Request $request)
    {
        if (!can('edit-location', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $request->validate([ 
            'id' => 'required|exists:wa_asset_locations,id',
            'location_id' => 'required|string|min:1|max:255',
            'location_description' => 'required|string|min:1|max:255',
            'location_parent' => 'nullable|exists:wa_asset_locations,id',
            'branch' => 'required|exists:restaurants,id'
        ]);
        
        DB::beginTransaction();
        try {
            $location = WaAssetLocation::find($request->id);
            $location->location_ID = $request->location_id;
            $location->location_description = $request->location_description;
            $location->wa_asset_locations_id = $request->location_parent;
            $location->restaurant_id = $request->branch;
            $location->save();
            DB::commit();
            request()->session()->flash('success', 'Asset Location Updated Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }

        return redirect(route('assets.location.index'));
    }

    public function location_delete(Request $request)
    {
        if (!can('delete-location', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $dataDelete = WaAssets::where('wa_asset_location_id','!=',NULL)->get()->pluck('wa_asset_location_id')->toArray();
        if(in_array($request->id,$dataDelete))
        {
            request()->session()->flash('danger', 'This location is already in use');
            return redirect()->back();
        }
        $location = WaAssetLocation::find($request->id);
        $location->delete();
        if($location)
        {   
            request()->session()->flash('success', 'Asset Location Deleted Successfully.');
            return redirect()->back();
        }
        request()->session()->flash('danger', 'Something went wrong');
        return redirect()->back();
    }

    //Category
    public function category_index(Request $request)
    {
        if (!can('view-category', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        
        if($request->ajax())
        {
            $this->sortable_columns = [];
            $this->sortable_columns[] = 'id';
            $this->sortable_columns[] = 'category_code';
            $this->sortable_columns[] = 'category_description';
            $dataDelete = \App\Model\WaAssets::where('wa_asset_categorie_id','!=',NULL)->get()->pluck('wa_asset_categorie_id')->toArray();
            $limit          = $request->input('length');
            $start          = $request->input('start');
            $search         = $request['search']['value'];
            $orderby        = $request['order']['0']['column'];
            $order          = $orderby != "" ? $request['order']['0']['dir'] : "";
            $draw           = $request['draw'];          
            $response       = \App\Model\WaAssetCategory::getDataModel($limit , $start , $search, $this->sortable_columns[$orderby], $order);            
            $totaldepartment       = $response['count'];
            $data = $response['response']->toArray();
            foreach($data as $key => $re){
                $data[$key]['links'] = view('admin.assets.category.links',['data'=>$re,'dataDelete'=>$dataDelete])->render();
            }
            $response['response'] = $data;
            $return = [
                "draw"              =>  intval($draw),
                "recordsFiltered"   =>  intval( $totaldepartment),
                "recordsTotal"      =>  intval( $totaldepartment),
                "data"              =>  $response['response']
            ];
            return $return;
        }
        $data['model'] = 'assets-category';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        return view('admin.assets.category.index')->with($data);
    }

    public function category_delete(Request $request)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission['assets-category___view']) && $permission != 'superadmin') {
            return response()->json([
                'result' => -1,
                'message' => 'Restricted You Dont have permission',
            ]);
        }
        $dataDelete = \App\Model\WaAssets::where('wa_asset_categorie_id','!=',NULL)->get()->pluck('wa_asset_categorie_id')->toArray();
        if(in_array($request->id,$dataDelete))
        {
            return response()->json([
                'result' => -1,
                'message' => 'This category is already in use',
            ]);
        }
        $location = \App\Model\WaAssetCategory::find($request->id);
        $location->delete();
        if($location)
        {   
            $response['result'] = 1;
            $response['refresh'] = true;
            $response['message'] = 'category deleteds successfully';
            return response()->json($response);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
    public function category_add(Request $request)
    {
        if (!can('add-category', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        
        $data['model'] = 'assets-category';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        $data['asset_category'] =WaAssetCategory::orderBy('id','DESC')->get();
        $data['profit_loss'] = WaChartsOfAccount::where('pl_or_bs','PROFIT AND LOSS')->orderBy('id','DESC')->get();
        $data['gl'] = WaChartsOfAccount::where('pl_or_bs','BALANCE SHEET')->orderBy('id','DESC')->get();
        return view('admin.assets.category.add')->with($data);
    }

    public function category_save(Request $request)
    {
        if (!can('add-location', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $request->validate([ 
            'category_code' => 'required|string|min:1|max:255',
            'category_description' => 'required|string|min:1|max:255',
            'fixed_asset_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_depreciation_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_disposal_id' => 'required|exists:wa_charts_of_accounts,id',
            'balance_sheet_id' => 'required|exists:wa_charts_of_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            WaAssetCategory::create([
                'category_code' => $request->category_code, 
                'category_description' => $request->category_description, 
                'fixed_asset_id' => $request->fixed_asset_id,
                'profit_loss_depreciation_id' => $request->profit_loss_depreciation_id,
                'profit_loss_disposal_id' => $request->profit_loss_disposal_id,
                'balance_sheet_id' => $request->balance_sheet_id
            ]);
            DB::commit();
            request()->session()->flash('success', 'Asset Category Added Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            request()->session()->flash('danger', $e->getMessage());
        }
        
        return redirect(route('assets.category.index'));
    }

    public function category_edit($id)
    {
        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        $permission = $this->mypermissionsforAModule();
        if (!isset($permission['assets-category___edit']) && $permission != 'superadmin') {
            return redirect()->back();
        }
        $data['model'] = 'assets-category';
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        $data['asset_category'] = \App\Model\WaAssetCategory::orderBy('id','DESC')->get();
        $data['profit_loss'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','PROFIT AND LOSS')->orderBy('id','DESC')->get();
        $data['gl'] = \App\Model\WaChartsOfAccount::where('pl_or_bs','BALANCE SHEET')->orderBy('id','DESC')->get();
        $data['data'] = \App\Model\WaAssetCategory::findOrFail($id);
        return view('admin.assets.category.edit')->with($data);
    }

    public function category_update(Request $request)
    {
        if(!$request->ajax())
        {
            return redirect()->route('admin.dashboard');
        }
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:wa_asset_categories,id',
            'category_code' => 'required|string|min:1|max:255',
            'category_description' => 'required|string|min:1|max:255',
            'fixed_asset_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_depreciation_id' => 'required|exists:wa_charts_of_accounts,id',
            'profit_loss_disposal_id' => 'required|exists:wa_charts_of_accounts,id',
            'balance_sheet_id' => 'required|exists:wa_charts_of_accounts,id',
        ],[],[]);
        if ($validator->fails()) {
            return response()->json([
                'result' => 0,
                'errors' => $validator->errors()
            ]);
        }
        # WaAssetCategory
        $category = \App\Model\WaAssetCategory::find($request->id);
        $category->category_code = $request->category_code;
        $category->category_description = $request->category_description;
        $category->fixed_asset_id = $request->fixed_asset_id;
        $category->profit_loss_depreciation_id = $request->profit_loss_depreciation_id;
        $category->profit_loss_disposal_id = $request->profit_loss_disposal_id;
        $category->balance_sheet_id = $request->balance_sheet_id;
        $category->save();
        if($category)
        {   
            $response['result'] = 1;
            $response['refresh'] = true;
            $response['message'] = 'category updated successfully';
            return response()->json($response);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function add()
    {
        if (!can('add', $this->pmodule)) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model = $this->model;
        $pmodule = $this->pmodule;
        
        $data['model'] = $this->model;
        $data['title'] = $this->title;
        $data['pmodule'] = $this->pmodule;
        $data['units'] = WaUnitOfMeasure::get();
        $data['chart_of_accounts'] = WaChartsOfAccount::orderBy('id','DESC')->get();
        $data['assets'] = WaAssets::orderBy('id','DESC')->get();
        $data['asset_category'] = WaAssetCategory::orderBy('id','DESC')->get();
        $data['asset_location'] = WaAssetLocation::orderBy('id','DESC')->get();
        $data['vats'] = TaxManager::orderBy('id','DESC')->get();
        $data['asset_depreciation'] = WaAssetDepreciation::orderBy('id','DESC')->get();
        $data['profit_loss'] = WaChartsOfAccount::where('pl_or_bs','PROFIT AND LOSS')->orderBy('id','DESC')->get();
        $data['gl'] = WaChartsOfAccount::where('pl_or_bs','BALANCE SHEET')->orderBy('id','DESC')->get();
        // dd($data);
        return view('admin.assets.create')->with($data);
    }
}
