<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\WalletMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WalletMatrixController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;
    protected  $basePath;

    public function __construct(Request $request)
    {
        $this->model = 'wallet-matrix';
        $this->title = 'Wallet Matrix';
        $this->pmodule = 'wallet-matrix';
        $this->basePath = 'admin.wallet_matrix';
    }

    public function index()
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;

        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $walletParameters = WalletMatrix::all();
            $breadcum = [$title => route($model.'.index'), 'Listing' => ''];
            return view($basePath . '.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission', 'walletParameters'));
        } else {
            Session::flash('warning', 'Invalid Request');
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
        $breadcum = [$title => route($model.'.create'), 'Create' => ''];

        if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') {
            
            return view($basePath . '.create', compact('title', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

  
    public function store(Request $request)
    {
 
        try{
            $validator = Validator::make($request->all(), [
                'parameter' => 'required|max:255',               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $parameter = new WalletMatrix();
                $parameter->parameter = $request->parameter;
                $parameter->salesman = $request->salesman_rate ?? 0;
                $parameter->delivery_driver = $request->delivery_driver_rate ?? 0;
                $parameter->turn_boy = $request->turn_boy_rate?? 0;
                $parameter->driver_grn = $request->driver_grn_rate?? 0;     
                $parameter->save();
                return redirect()->route("wallet-matrix.index")->with('success', 'Wallet Parameter Created successfully' );

            }
        }catch(\Throwable $e){
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
    }

  
    public function show(string $id)
    {
        //
    }

   
    public function edit(string $id)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        $basePath = $this->basePath;
        $parameter = WalletMatrix::find($id);
        $breadcum = [$title => route('wallet-matrix.index'), 'listing' => ''];

        if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
            
            return view($basePath . '.edit', compact('title', 'model', 'breadcum', 'parameter'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

   
    public function update(Request $request, string $id)
    {
        try{
            $validator = Validator::make($request->all(), [
                'parameter' => 'required|max:255',               
                ]);
            if ($validator->fails()) 
            {
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $parameter = WalletMatrix::find($id);
                $parameter->parameter = $request->parameter;
                $parameter->salesman = $request->salesman_rate ?? 0;
                $parameter->delivery_driver = $request->delivery_driver_rate ?? 0;
                $parameter->turn_boy = $request->turn_boy_rate?? 0;
                $parameter->driver_grn = $request->driver_grn_rate?? 0;     
                $parameter->save();
                return redirect()->route("wallet-matrix.index")->with('success', 'Wallet Parameter Updated successfully' );

            }
        }catch(\Throwable $e){
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);

        }
    }

   
    public function destroy(string $id)
    {
        //
    }
}
