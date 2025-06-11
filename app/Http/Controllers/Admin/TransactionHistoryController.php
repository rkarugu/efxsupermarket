<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaDebtorTran;
use App\Models\SuspendedTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TransactionHistoryController extends Controller
{
    protected $model = 'transaction-history';

    protected $title = 'Transaction History';

    public function index()
    {
        if (!can($this->model, 'reconciliation')) {
            return returnAccessDeniedPage();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Reconciliation' => '', $title => ''];

        $processingUpload = false;
        
        return view('admin.transaction_history.index', compact('title', 'model', 'breadcum', 'processingUpload'));
    }
    
    public function fetch(Request $request)
    {
        if (!can($this->model, 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }

        $title = $this->title;
        $model = $this->model;
        $breadcum = ['Reconciliation' => '', $title => ''];
        $debtorTrans = WaDebtorTran::where('document_no',$request->transaction)->get();
        
        try {
            
            $trans = [];
            foreach ($debtorTrans as $key => $tran) {
                $trans[]=[
                    'Cat'=>'Debtors',
                    "customer" => $tran->customerDetail->customer_name .'('.$tran->customerDetail->customer_code.')',
                    "trans_date" => date('d-m-Y',strtotime($tran->trans_date)),
                    "input_date" => $tran->input_date,
                    "reference" => $tran->reference,
                    "amount" => $tran->amount,
                    "document_no" => $tran->document_no,
                    "created_at" => date('Y-m-d H:i', strtotime($tran->created_at)),
                    "reconciled" => $tran->reconciled,
                    "channel" => $tran->channel,
                    "branch_id" => $tran->branchMain->name,
                    "verification_status" => $tran->verification_status,
                    "manual_upload_status" => $tran->manual_upload_status
                ];

                $suspends=SuspendedTransaction::where('document_no',$request->transaction)->get();
                foreach ($suspends as $key => $suspend) {
                    $trans[]=[
                        'Cat'=>'Suspend',
                        "suspended_by" => $suspend->suspendedBy->name,
                        "resolved_by" => $suspend->resolvedBy->name,
                        "document_no" => $suspend->document_no,
                        "reference" => $suspend->reference,
                        "edited_reference" => $suspend->edited_reference,
                        "amount" => $suspend->amount,                        
                        "edited_amount" => $suspend->edited_amount,
                        'customer' => $suspend->customerDetail->customer_name .'('.$suspend->customerDetail->customer_code.')',
                        'edited_customer' => $suspend->editedCustomerDetail ? $suspend->editedCustomerDetail->customer_name .'('.$suspend->editedCustomerDetail->customer_code.')' : '',
                        "reason" => $suspend->reason,
                        "status" => $suspend->status,
                        "created_at" => date('Y-m-d H:i', strtotime($suspend->created_at)),
                    ];
                }
            }
   
            $processingUpload = true;
            usort($trans, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            return view('admin.transaction_history.index', compact('title', 'model', 'breadcum', 'processingUpload','trans'));

        } catch (\Throwable $e) {
            Session::flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
}
