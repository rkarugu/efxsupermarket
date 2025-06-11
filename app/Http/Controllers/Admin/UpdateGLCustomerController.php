<?php

namespace App\Http\Controllers\Admin;

use Session;
use App\Http\Controllers\Controller;
use App\Model\WaChartsOfAccount;
use Illuminate\Http\Request;
use App\Model\WaGlTran;
use App\Model\WaInternalRequisition;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ExcelDownloadService;
use Illuminate\Support\Facades\DB;

class UpdateGLCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('view', 'update-customer-to-gl')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = 'Update Customer to GL';
        $model = 'update-customer-to-gl';

        $breadcum = [
            'Update Customer to GL' => '',
            $title => ''
        ];
        $account = WaChartsOfAccount::whereIn('account_code',['55001-001']);

        if (request()->wantsJson()) {
            $data = DB::table('wa_gl_trans')
            ->leftJoin('restaurants','restaurants.id','wa_gl_trans.tb_reporting_branch')
            ->leftJoin('wa_charts_of_accounts','wa_charts_of_accounts.account_code','wa_gl_trans.account')
            ->where('transaction_no','like','INV%')
            ->where('wa_gl_trans.customer_id',NULL);
           
            if(request()->filled('start_date') && request()->filled('end_date')){
                $data->whereBetween('wa_gl_trans.trans_date', [request()->start_date.' 00:00:00', request()->end_date.' 23:59:59']);
            }
            
            $data->select(
                'wa_gl_trans.created_at',
                'restaurants.name as branch_name',
                'wa_gl_trans.narrative',
                'wa_gl_trans.reference',
                'wa_gl_trans.transaction_type',
                'wa_gl_trans.transaction_no',
                'wa_gl_trans.amount',
                'wa_gl_trans.trans_date',
                'wa_charts_of_accounts.account_code',
                'wa_charts_of_accounts.account_name',
            );
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('account',function($transaction){
                    return $transaction->account_code.'('.$transaction->account_name.')';
                })
                ->editColumn('trans_date',function($transaction){
                    return date('d-m-Y', strtotime($transaction->trans_date));
                })
                ->addColumn('debit',function($item){
                    return $item->amount > 0 ? manageAmountFormat($item->amount) : '';
                })
                ->addColumn('credit',function($item){                    
                    return $item->amount < 0 ? manageAmountFormat($item->amount) : '';
                })
                ->with('debitCreditTotal', function() use($data){
                    $debit = 0;
                    $credit = 0;
                    foreach($data->get() as $item){
                        if($item->amount > 0){
                            $debit = $debit + $item->amount;
                        } else{
                            $credit = $credit + $item->amount;
                        }
                    }
                    return ['debit'=>manageAmountFormat($debit),'credit'=>manageAmountFormat($credit)];
                })
                
                ->toJson();
        }

        if (request()->print == 'excel') {
            $lists = WaGlTran::with('getAccountDetail','restaurant')->where('tb_reporting_branch',NULL);
            
            if (request()->filled('start_date') && request()->filled('end_date')) {
                $lists->whereBetween('trans_date', [request()->start_date, request()->end_date]);
            }
            // $lists->groupBy('transaction_no');
            $lists = $lists->orderBy('trans_date')->get();
            
                $data = [];
                foreach ($lists as $trans) {
                    $child = [];
                    $child['Transaction Date'] = date('d-m-Y', strtotime($trans->trans_date));
                    $child['Transaction No'] = $trans->transaction_no;
                    $child['Transaction Type'] = $trans->transaction_type;
                    $child['Narrative'] = $trans->narrative;
                    $child['Branch'] = $trans->restaurant->name;
                    $child['Account'] = $trans->getAccountDetail->account_code.'('.$trans->getAccountDetail->account_name.')';
                    $child['Amount'] = manageAmountFormat($trans->amount);
                    $data[] = $child;
                }

                return ExcelDownloadService::download('Gl-Transaction-No-TB-Branch', collect($data), 
                ['TRANSACTION DATE', 'TRANSACTION NO', 'TRANSACTION TYPE','NARRATIVE','BRANCH', 'ACCOUNT', 'AMOUNT']);
            
        }

        return view('admin.update_customer_to_gl.list', compact('title', 'model', 'breadcum'));
    }

    public function process(Request $request)
    {
        try{
            $glTrans = WaGlTran::whereBetween('trans_date', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59'])
                // ->where('account',$request->account)
                ->select('transaction_no')
                ->groupBy('transaction_no')
                ->get()->pluck('transaction_no');
            foreach ($glTrans as $value) {
                if(str_contains($value, 'INV')){
                    $Invoice = WaInternalRequisition::where('requisition_no',$value)->first();
                    DB::table('wa_gl_trans')->where('transaction_no', $value)->update([
                        'customer_id' => $Invoice->customer_id,
                    ]);
                }
            }
            $request->session()->flash('success', 'Process Complete.');
            
        } catch (\Exception $e) {
            $request->session()->flash('danger', $e->getMessage());
        }
        return redirect(route('update-customer-to-gl.index'));
    }

}
