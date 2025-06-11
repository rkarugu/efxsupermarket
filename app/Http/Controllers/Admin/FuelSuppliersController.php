<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaSupplier;
use App\Models\FuelSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FuelSuppliersController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'fuel-suppliers';
        $this->title = 'fuel-suppliers';
        $this->pmodule = 'fuel-suppliers';
        $this->basePath = 'admin.fuel_suppliers';
    }
    public function index()
    {
   
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $user = Auth::user();
        // $branches = WaLocationAndStore::all();
        $fuelSuppliers = FuelSupplier::with(['supplierDetails', 'supplierDetails.suppTrans'])->get();
       
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
           
            $breadcum = [$title => route('maintain-items.index'), 'Listing' => ''];
            return view('admin.fuel_suppliers.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'fuelSuppliers', 'user'));
        } else {
            Session::flash('warning', 'You dont have permission to view this page');
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
        $breadcum = [$title => route('fuel-suppliers.index'), 'Create' => ''];
        $existingSupplierIds = FuelSupplier::pluck('wa_suppliers_id')->toArray();
        $suppliers = WaSupplier::whereNotIn('id', $existingSupplierIds)->where('service_type', 'services')->get();
        
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            return view($basePath . '.create', compact('title', 'model', 'breadcum', 'suppliers'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try{
            $existingSupplier = FuelSupplier::where('wa_suppliers_id', $request->supplier)->first();
            if($existingSupplier)
            {
                return redirect()->back()->withErrors(['errors' => 'fuel Supplier already exists']);
            }
            $fuelSupplier = new FuelSupplier();
            $fuelSupplier->wa_suppliers_id = $request->supplier;
            $fuelSupplier->save();

        return redirect()->route('fuel-suppliers.index')->with('success', 'Fuel Supplier Added Successfully' );
        }catch(\Throwable $e){
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
    }
    public function destroy($id){
        try {
            FuelSupplier::find($id)->delete();
            return redirect()->route('fuel-suppliers.index')->with('success', 'Fuel Supplier Deleted Successfully' );
            
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }
  
    
}
