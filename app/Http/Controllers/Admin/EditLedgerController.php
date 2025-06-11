<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\GlTags;
use App\Model\WaGlReversedTrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Model\WaGlTran;
use App\Model\WaBanktran;
use App\Model\WaDebtorTran;
use App\Model\WaDepartment;
use App\Model\Projects;
use App\Model\WaNumerSeriesCode;
use App\Model\WaChartsOfAccount;

use App\Model\WaSuppTran;
use Illuminate\Support\Facades\Session;

class EditLedgerController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {
        $this->model = 'edit-ledger';
        $this->title = 'Edit Ledger';
        $this->pmodule = 'edit-ledger';
    }

    public function index(Request $request) {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            if($request->manage == 'edit' && $request->transaction){
                return $this->editList($request,$title,$model,$pmodule,$permission);
            }
            if($request->manage == 'reversal' && $request->transaction){
                return $this->reversal($request,$title,$model,$pmodule,$permission);
            }
            if($request->manage == 'view' && $request->transaction){
                return $this->viewList($request,$title,$model,$pmodule,$permission);
            }
            return view('admin.edit_ledger.index', compact('title', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function viewList($request,$title,$model,$pmodule,$permission)
    {
        if (!isset($permission[$pmodule . '___view']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $validations = Validator::make($request->all(),[
            'transaction'=>'required'
        ]);
        if($validations->fails()){
            Session::flash('warning','Transaction Number is required');
            return redirect()->route('edit-ledger.index');
        }
        $data['glaccounts'] = \App\Model\WaChartsOfAccount::get();
        $data['resturants'] = \App\Model\Restaurant::get();
        $data['title'] = $title;
        $data['model'] = $model;
        $data['pmodule'] = $pmodule;
        $data['permission'] = $permission;

        $data['lists'] = WaGlTran::with([
            'user','getAccountDetail'
        ])->where('transaction_no',$request->transaction)->get();

        $data['bank_trans'] = WaBanktran::where('document_no',$request->transaction)->get();
        $data['debtor_trans'] = WaDebtorTran::where('document_no',$request->transaction)->get();
        $data['supp_trans'] = WaSuppTran::where('document_no',$request->transaction)->get();


        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        return view('admin.edit_ledger.viewList')->with($data);

    }


    public function editList($request,$title,$model,$pmodule,$permission)
    {
        if (!isset($permission[$pmodule . '___edit_transaction']) && $permission == 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $validations = Validator::make($request->all(),[
            'transaction'=>'required'
        ]);
        if($validations->fails()){
            Session::flash('warning','Transaction Number is required');
            return redirect()->route('edit-ledger.index');
        }
        $data['glaccounts'] = \App\Model\WaChartsOfAccount::get();
        $data['resturants'] = \App\Model\Restaurant::get();
        $data['title'] = $title;
        $data['model'] = $model;
        $data['pmodule'] = $pmodule;
        $data['permission'] = $permission;

        $data['lists'] = WaGlTran::with([
            'user','getAccountDetail'
        ])->where('transaction_no',$request->transaction)->get();

        $data['bank_trans'] = WaBanktran::where('document_no',$request->transaction)->get();
        $data['debtor_trans'] = WaDebtorTran::where('document_no',$request->transaction)->get();
        $data['supp_trans'] = WaSuppTran::where('document_no',$request->transaction)->get();


        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        return view('admin.edit_ledger.editList')->with($data);

    }

    public function edit(Request $request,$transactionId)
    {
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $glaccounts = \App\Model\WaChartsOfAccount::get();
            $resturants = \App\Model\Restaurant::get();
            $departments = WaDepartment::get();
            $Projects = Projects::get();
            $gl_tags = GlTags::get();
            $lists = WaGlTran::with([
                'user','getAccountDetail'
            ])->where('id',$transactionId)->first();
            return view('admin.edit_ledger.edit', compact('gl_tags','Projects','resturants','title', 'model','lists', 'breadcum', 'pmodule', 'permission','departments','glaccounts'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function bankTransedit(Request $request,$transactionId){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $glaccounts = \App\Model\WaChartsOfAccount::get();
            $lists = WaBanktran::findOrFail($transactionId);


            return view('admin.edit_ledger.bank_trans_edit', compact('gl_tags','Projects','resturants','title', 'model','lists', 'breadcum', 'pmodule', 'permission','departments','glaccounts'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function debtorTransedit(Request $request,$transactionId){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $glaccounts = \App\Model\WaChartsOfAccount::get();
            $lists = WaDebtorTran::findOrFail($transactionId);


            return view('admin.edit_ledger.debtor_trans_edit', compact('gl_tags','Projects','resturants','title', 'model','lists', 'breadcum', 'pmodule', 'permission','departments','glaccounts'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function supplierTransedit(Request $request,$transactionId){
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            $glaccounts = \App\Model\WaChartsOfAccount::get();
            $lists = WaSuppTran::findOrFail($transactionId);


            return view('admin.edit_ledger.supplier_trans_edit', compact('gl_tags','Projects','resturants','title', 'model','lists', 'breadcum', 'pmodule', 'permission','departments','glaccounts'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function reversal($request,$title,$model,$pmodule,$permission)
    {
        if (!isset($permission[$pmodule . '___reverse_transaction']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $validations = Validator::make($request->all(),[
            'transaction'=>'required'
        ]);
        if($validations->fails()){
            Session::flash('warning','Transaction Number is required');
            return redirect()->route('edit-ledger.index');
        }
        $data['glaccounts'] = \App\Model\WaChartsOfAccount::get();
        $data['resturants'] = \App\Model\Restaurant::get();
        $data['title'] = $title;
        $data['model'] = $model;
        $data['pmodule'] = $pmodule;
        $data['permission'] = $permission;

        $data['lists'] = WaGlTran::with([
            'user','getAccountDetail'
        ])->where('transaction_no',$request->transaction)->get();
        $data['bank_trans'] = WaBanktran::where('document_no',$request->transaction)->get();
        $data['debtor_trans'] = WaDebtorTran::where('document_no',$request->transaction)->get();
        $data['supp_trans'] = WaSuppTran::where('document_no',$request->transaction)->get();


        $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
        return view('admin.edit_ledger.reversal')->with($data);

    }

    public function destroy(Request $request,$transactionId)
    {
        $validations = Validator::make($request->all(),[
            'transaction' => 'required',
        ]);
        if($validations->fails()){
            return response()->json([
                'result' => -1,
                'message' => 'Transaction Number is required',
            ]);
        }
        $check = DB::transaction(function($e) use ($request){
            $data = [];
            $glTrans = WaGlTran::where('transaction_no',$request->transaction)->get()->toArray();
            $rev_num = getCodeWithNumberSeries('REV_TRANS');
            $mainData = [];
            $user = getLoggeduserProfile();
            $series_module = WaNumerSeriesCode::where('module','REV_TRANS')->first();
            $rev_gl = [];
            foreach ($glTrans as $key => $value) {
                unset($value['id']);
                $mainData[] = $value;
                $child = [];
                $child['transaction_no'] = $rev_num;
                $child['transaction_type'] = 'Reverse Transaction';
                $child['grn_type_number'] = $series_module->type_number;
                $child['grn_last_used_number'] = $series_module->last_number_used;
                $child['trans_date'] = $value['trans_date'];
                $child['account'] = $value['account'];
                $child['narrative'] = $value['narrative']. ' - '.$value['transaction_no'] ;
                $child['reference'] = $value['reference']. ' - '.$value['transaction_no'] ;
                $child['amount'] = -$value['amount'];
                $child['user_id'] = $user->id;
                $rev_gl[] = $child;

            }
            if(count($mainData)>0){
                WaGlReversedTrans::insert($mainData);
                $WaBanktran = WaBanktran::where('document_no',$request->transaction)->get()->toArray();
                $bnk_trans = [];
                foreach($WaBanktran as $bankTrans){
                    $bnk_child = [];
                    $bnk_child['type_number'] = $series_module->type_number;
                    $bnk_child['document_no'] = $rev_num;
                    $bnk_child['trans_date'] = $bankTrans['trans_date'];
                    $bnk_child['amount'] = -$bankTrans['amount'];
                    $bnk_child['account'] = $bankTrans['account'];
                    $bnk_child['sub_account'] = $bankTrans['sub_account'];
                    $bnk_child['reference'] = 'Reverse Transaction - '. $bankTrans['reference']. ' - '.$bankTrans['document_no'] ;

                    $bnk_child['supplier_account'] = $bankTrans['supplier_account'];
                    $bnk_trans[] = $bnk_child;
                }

                $WaDebtorTran = WaDebtorTran::where('document_no',$request->transaction)->get()->toArray();
                $dbt_trans = [];
                foreach($WaDebtorTran as $dbtTrans){
                    $dbt_child = [];
                    $dbt_child['type_number'] = $series_module->type_number;
                    $dbt_child['wa_customer_id'] = $dbtTrans['wa_customer_id'];
                    $dbt_child['customer_number'] = $dbtTrans['customer_number'];
                    $dbt_child['trans_date'] = $dbtTrans['trans_date'];
                    $dbt_child['wa_accounting_period_id'] = $dbtTrans['wa_accounting_period_id'];
                    $dbt_child['amount'] = -$dbtTrans['amount'];
                    $dbt_child['document_no'] = $rev_num;
                    $dbt_child['user_id'] = $user->id;
                    $dbt_child['invoice_customer_name'] = $dbtTrans['invoice_customer_name'];
                    $dbt_child['salesman_id'] = $dbtTrans['salesman_id'];
                    $dbt_child['salesman_user_id'] = $dbtTrans['salesman_user_id'];
                    $dbt_child['shift_id'] = $dbtTrans['shift_id'];
                    $dbt_child['allocated_amount'] = -$dbtTrans['allocated_amount'];
                    $dbt_child['route_id'] = $dbtTrans['route_id'];
                    $dbt_child['reference'] = 'Reverse Transaction - ' . $dbtTrans['reference']. ' - '.$dbtTrans['document_no'] ;
                    $dbt_child['wa_route_customer_id'] = $dbtTrans['wa_route_customer_id'];
                    $dbt_trans[] = $dbt_child;
                }
                $WaSuppTran = WaSuppTran::where('document_no',$request->transaction)->get()->toArray();
                $sup_trans = [];
                foreach($WaSuppTran as $supTrans){
                    $sup_child = [];
                    $sup_child['grn_type_number'] = $supTrans['grn_type_number'];
                    $sup_child['supplier_no'] = $supTrans['supplier_no'];
                    $sup_child['suppreference'] = 'Reverse Transaction';
                    $sup_child['trans_date'] = $supTrans['trans_date'];
                    $sup_child['total_amount_inc_vat'] = -$supTrans['total_amount_inc_vat'];
                    $sup_child['vat_amount'] = -$supTrans['vat_amount'];
                    $sup_child['document_no'] = $rev_num;
                    $sup_child['description'] = $sup_child['suppreference'].' - '. $supTrans['document_no'];
                    $sup_child['account'] = $supTrans['account'];
                    $sup_child['round_off'] = -$supTrans['round_off'];
                    $sup_child['allocated_amount'] = -$supTrans['allocated_amount'];
                    $sup_trans[] = $sup_child;
                }
                WaGlTran::insert($rev_gl);
                if(count($bnk_trans)>0){
                    WaBanktran::insert($bnk_child);
                }
                if(count($dbt_trans)>0){
                    WaDebtorTran::insert($dbt_trans);
                }
                if(count($sup_trans)>0){
                    WaSuppTran::insert($sup_trans);
                }
            }
            updateUniqueNumberSeries('REV_TRANS',$rev_num);

            return true;
        });
        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Transaction Reversal Processed successfully',
                'location'=>route($this->model . '.index')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function update(Request $request, $id)
    {
        //dd($request->id);
        $validations = Validator::make($request->all(),[
            'id' => 'required|exists:wa_gl_trans,id',
            'balancing_gl_account' => 'required|exists:wa_charts_of_accounts,id',
            'account' => 'required|exists:wa_charts_of_accounts,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'department_id' => 'nullable|exists:wa_departments,id',
            'project_id' => 'nullable|exists:projects,id',
            'trans_date' => 'required|date|date_format:Y-m-d',
            'trans_time' => 'nullable',
            'narrative'=>'nullable|string|max:250',
            'reference'=>'nullable|string|max:250',
            'gl_tag'=>'nullable|exists:gl_tags,id'
        ]);
        if($validations->fails()){
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $check = DB::transaction(function($e) use ($request){
            $account = WaChartsOfAccount::where('id',$request->account)->first();
            $balancing_gl_account = WaChartsOfAccount::where('id',$request->balancing_gl_account)->first();
            $gl_trans = WaGlTran::where('id',$request->id)->first();

            $bankTrans = WaBanktran::where('document_no',$gl_trans->transaction_no)->where('account',$gl_trans->account)->first();
            //dd($account->account_code);

            if($bankTrans){
                $bankTrans->reference = $request->reference;
                $bankTrans->trans_date = $request->trans_date.' '.$request->trans_time;
                $bankTrans->account = $account->account_code;
                $bankTrans->save();
            }
            $gl_trans->balancing_gl_account = $balancing_gl_account->account_code;
            $gl_trans->account = $account->account_code;
            $gl_trans->restaurant_id = $request->restaurant_id;
            $gl_trans->department_id = $request->department_id;
            $gl_trans->project_id = $request->project_id;
            $gl_trans->trans_date = $request->trans_date.' '.$request->trans_time;
            $gl_trans->narrative = $request->narrative;
            $gl_trans->reference = $request->reference;
            $gl_trans->gl_tag = $request->gl_tag;
            $gl_trans->save();
            return true;
        });

        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Transaction Updated successfully',
                'location'=>route($this->model . '.index')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }


    public function bankTransUpdate(Request $request, $id)
    {
        $validations = Validator::make($request->all(),[
            'id' => 'required|exists:wa_gl_trans,id',
            'balancing_gl_account' => 'required|exists:wa_charts_of_accounts,id',
            'account' => 'required|exists:wa_charts_of_accounts,id',
            'reference'=>'nullable|string|max:250',
        ]);
        if($validations->fails()){
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $check = DB::transaction(function($e) use ($request){
            $account = WaChartsOfAccount::where('id',$request->account)->first();
            $balancing_gl_account = WaChartsOfAccount::where('id',$request->balancing_gl_account)->first();

            $bank_trans_find = WaBanktran::where('id',$request->id)->first();

            $bankTrans = WaBanktran::where('document_no',$bank_trans_find->document_no)->where('account',$bank_trans_find->account)->first();
            //dd($account->account_code);

            if($bankTrans){
                $bankTrans->reference = $request->reference;
                //$bankTrans->trans_date = $request->trans_date.' '.$request->trans_time;
                $bankTrans->account = $account->account_code;
                $bankTrans->save();
            }
            $bank_trans_find->bank_gl_account_code = $balancing_gl_account->account_code;
            $bank_trans_find->account = $account->account_code;
            //$bank_trans_find->restaurant_id = $request->restaurant_id;
            $bank_trans_find->reference = $request->reference;
            $bank_trans_find->save();
            return true;
        });

        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Transaction Updated successfully',
                'location'=>route($this->model . '.index')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function debtorTransUpdate(Request $request, $id)
    {

        $validations = Validator::make($request->all(),[
            'id' => 'required|exists:wa_gl_trans,id',
            'customer_number' => 'required',
            'reference'=>'nullable|string|max:250',
        ]);
        if($validations->fails()){
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $check = DB::transaction(function($e) use ($request){
            $account = WaChartsOfAccount::where('id',$request->account)->first();
            $balancing_gl_account = WaChartsOfAccount::where('id',$request->balancing_gl_account)->first();

            $debtor_trans_find = WaDebtorTran::where('id',$request->id)->first();

            $bankTrans = WaBanktran::where('document_no',$debtor_trans_find->document_no)->where('account',$debtor_trans_find->account)->first();
            //dd($account->account_code);

            if($bankTrans){
                $bankTrans->reference = $request->reference;
                $bankTrans->save();
            }
            $debtor_trans_find->customer_number = $request->customer_number;
            $debtor_trans_find->reference = $request->reference;
            $debtor_trans_find->save();
            return true;
        });

        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Transaction Updated successfully',
                'location'=>route($this->model . '.index')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }

    public function supplierTransUpdate(Request $request, $id)
    {

        $validations = Validator::make($request->all(),[
            'id' => 'required|exists:wa_gl_trans,id',
            'balancing_gl_account' => 'required|exists:wa_charts_of_accounts,id',
            'account' => 'required|exists:wa_charts_of_accounts,id',
            'reference'=>'nullable|string|max:250',
        ]);
        if($validations->fails()){
            return response()->json([
                'result' => 0,
                'errors' => $validations->errors(),
            ]);
        }
        $check = DB::transaction(function($e) use ($request){
            $account = WaChartsOfAccount::where('id',$request->account)->first();
            $balancing_gl_account = WaChartsOfAccount::where('id',$request->balancing_gl_account)->first();

            $supplier_trans_find = WaSuppTran::where('id',$request->id)->first();

            $bankTrans = WaBanktran::where('document_no',$supplier_trans_find->transaction_no)->where('account',$supplier_trans_find->account)->first();

            if($bankTrans){
                $bankTrans->reference = $request->suppreference;
                //$bankTrans->trans_date = $request->trans_date.' '.$request->trans_time;
                $bankTrans->account = $account->account_code;
                $bankTrans->save();
            }
            $supplier_trans_find->balancing_gl_account = $balancing_gl_account->account_code;
            $supplier_trans_find->account = $account->account_code;
            //$supplier_trans_find->restaurant_id = $request->restaurant_id;
            $supplier_trans_find->suppreference = $request->suppreference;
            $supplier_trans_find->save();
            return true;
        });

        if($check)
        {
            return response()->json([
                'result' => 1,
                'message' => 'Transaction Updated successfully',
                'location'=>route($this->model . '.index')
            ]);
        }
        return response()->json([
            'result' => -1,
            'message' => 'Something went wrong',
        ]);
    }
}
