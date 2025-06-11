<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingSupplierDocumentProcess;
use App\Models\BillingSupplierDocumentProcessFile;
use App\Models\BillingSupplierDocumentProcessLog;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SupplierBillingController extends Controller
{

    protected $model;
    protected $pmodel;
    protected $title;
    protected $pmodule;

    public function billing_submitted_index()
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = 'billing-submitted';
        $title = 'Initial Billings Submitted Approvals';
        $model = 'billing-submitted';

        if (!can('view', $pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $data = null;
        $response_data = [];

        $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
        $billing_bank_slips_data = $api->postRequest('/api/supplier/get-supplier-subscriptions-data', $data);

        if (!empty($billing_bank_slips_data['subscription_plans'])) {
            foreach ($billing_bank_slips_data['subscription_plans'] as $data) {

                $existing_approval = BillingSupplierDocumentProcess::where('billing_bank_payment_id', $data['id'])
                    ->where('onboarding_id', $data['tradeagreement']['onboarding_id'])
                    ->first();

                if (!$existing_approval) {

                    $bank = '';
                    $payment_method = '';

                    if ($data['bankpaymentdeposit']['bank'] == 0) {
                        $bank = 'null';
                    } elseif ($data['bankpaymentdeposit']['bank'] == 1) {
                        $bank = 'KCB';
                    } elseif ($data['bankpaymentdeposit']['bank'] == 2) {
                        $bank = 'EQUITY';
                    } else {
                        $bank = 'null';
                    }

                    if ($data['bankpaymentdeposit']['payment_method'] == 0) {
                        $payment_method = 'null';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 1) {
                        $payment_method = 'RTGS';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 2) {
                        $payment_method = 'CHEQUE';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 3) {
                        $payment_method = 'EFT';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 4) {
                        $payment_method = 'PESALINK';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 5) {
                        $payment_method = 'CASH';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 6) {
                        $payment_method = 'DIRECT_ENT';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 7) {
                        $payment_method = 'DIRECT';
                    } elseif ($data['bankpaymentdeposit']['payment_method'] == 8) {
                        $payment_method = 'COUNTER';
                    } else {
                        $payment_method = 'null';
                    }

                    $billing_slip_approval = BillingSupplierDocumentProcess::create([
                        'billing_bank_payment_id' => $data['id'],
                        'onboarding_id' => $data['tradeagreement']['onboarding_id'],
                        'trade_agreement_id' => $data['tradeagreement']['id'],
                        'amount' => $data['cost'],
                        'status' => $data['subscriptionbillinginvoice']['payment_status'],
                        'approve_status' => 'Initial',
                        'bank' => $bank,
                        'payment_method' => $payment_method,
                        'uploaded_date' => $data['bankpaymentdeposit']['created_at'],
                    ]);

                    if (!empty($data['bankpaymentdeposit']['bankpaymentimages'])) {
                        foreach ($data['bankpaymentdeposit']['bankpaymentimages'] as $file) {
                            BillingSupplierDocumentProcessFile::create([
                                'billing_supplier_document_process_id' => $billing_slip_approval->id,
                                'file_path' => $file['path'],
                            ]);
                        }
                    }
                }
            }
        }

        $billing_slip_approvals_data = BillingSupplierDocumentProcess::with(
            'tradeagreement',
            'tradeagreement.supplier',
            'billingsupplierdocumentprocessfiles',
            'billingsupplierdocumentprocesslog'
        )->get();

        return view('admin.supplier_portal.billing.billing_index', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'data',
            'response_data',
            'billing_slip_approvals_data'
        ));
    }

    public function updateBillingSlipStatusInitial(Request $request)
    {

        $validated = $request->validate([
            'ids' => 'required|array',
            'status' => 'required|string|in:Approved,Rejected',
        ]);

        $ids = $validated['ids'];
        $status = $validated['status'];

        $data = BillingSupplierDocumentProcess::whereIn('id', $ids)->get();
        foreach ($data as $dta) {

            $updated_billing_data = BillingSupplierDocumentProcessLog::where('billing_supplier_document_process_id', $dta->id)->first();

            if (!$updated_billing_data) {
                $updated_data = BillingSupplierDocumentProcessLog::create([
                    'billing_supplier_document_process_id' => $dta->id,
                    'approved_by' => Auth::user()->id,
                    'previous_status' => $dta->status,
                    'updated_status' => $status,
                    'previous_approve_status' => $dta->approve_status,
                    'updated_approve_status' => $dta->approve_status,
                    'stage' => 'Initial',
                ]);
            }

            $dta->status = $status;
            $dta->save();
        }

        return response()->json([
            'message' => 'Status updated successfully.',
        ]);
    }


    public function billing_submitted_index_final()
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = 'billing-submitted-final';
        $title = 'Final Billings Submitted Approvals';
        $model = 'billing-submitted-final';

        if (!can('view', $pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $billing_slip_approvals_data = BillingSupplierDocumentProcess::with(
            'tradeagreement',
            'tradeagreement.supplier',
            'billingsupplierdocumentprocessfiles',
            'billingsupplierdocumentprocesslogs'
        )
            ->get();

        return view('admin.supplier_portal.billing.billing_index_final', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'billing_slip_approvals_data'
        ));
    }

    public function updateBillinglipStatusFinal(Request $request)
    {

        $validated = $request->validate([
            'ids' => 'required|array',
            'billing_bank_payment_id' => 'required|array',
            'status' => 'required|string|in:Approved,Rejected',
        ]);

        $ids = $validated['ids'];
        $billing_bank_payment_ids = $validated['billing_bank_payment_id'];
        $status = $validated['status'];

        $data = BillingSupplierDocumentProcess::whereIn('id', $ids)->get();
        foreach ($data as $dta) {

            $updated_billing_data = BillingSupplierDocumentProcessLog::where('billing_supplier_document_process_id', $dta->id)->first();

            if (!$updated_billing_data) {
                $updated_data = BillingSupplierDocumentProcessLog::create([
                    'billing_supplier_document_process_id' => $dta->id,
                    'approved_by' => Auth::user()->id,
                    'previous_status' => $dta->status,
                    'updated_status' => $status,
                    'previous_approve_status' => $dta->approve_status,
                    'updated_approve_status' => 'Final',
                    'stage' => 'Final',
                ]);
            }

            $dta->status = $status;
            $dta->approve_status = 'Final';
            $dta->save();
        }

        $updated_records = BillingSupplierDocumentProcess::whereIn('id', $ids)
            ->where('status', 'Approved')
            ->where('approve_status', 'Final')
            ->get(['id', 'billing_bank_payment_id']);

        $request_data = [
            'status' => $status,
            'ids' => $updated_records->pluck('id')->toArray(),
            'billing_bank_payment_ids' => $updated_records->pluck('billing_bank_payment_id')->toArray()
        ];

        if ($request_data['ids']) {
            $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $billing_bank_slips_data_updated = $api->postRequest('/api/update-all-billing-bank-slip-requests', $request_data);
        }

        return response()->json([
            'message' => 'Status updated successfully.',
        ]);
    }
}
