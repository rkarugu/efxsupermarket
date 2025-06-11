<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletSupplierDocumentProcess;
use App\Models\WalletSupplierDocumentProcessFile;
use App\Models\WalletSupplierDocumentProcessLog;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WalletSupplierDocumentProcessController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct() {}

    public function index()
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = 'supplier-bank-deposits-initial-approval';
        $title = 'Initial Approval Bank Deposits';
        $model = 'supplier-bank-deposits-initial-approval';

        if (!can('view', $pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $request_data = null;
        $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
        $wallet_bank_slips_data = $api->postRequest('/api/get-all-wallet-bank-slip-requests', $request_data);

        if (!empty($wallet_bank_slips_data['wallet_bank_slips'])) {
            foreach ($wallet_bank_slips_data['wallet_bank_slips'] as $data) {

                $existing_approval = WalletSupplierDocumentProcess::where('wallet_bank_payment_id', $data['id'])
                    ->where('onboarding_id', $data['onboarding_id'])
                    ->first();

                if (!$existing_approval) {
                    $wallet_slip_approval = WalletSupplierDocumentProcess::create([
                        'wallet_bank_payment_id' => $data['id'],
                        'onboarding_id' => $data['onboarding_id'],
                        'trade_agreement_id' => $data['trade_agreement_id'],
                        'amount' => $data['amount'],
                        'status' => $data['status'],
                        'bank' => $data['bank'],
                        'payment_method' => $data['payment_method'],
                        'uploaded_date' => $data['created_at'],
                    ]);

                    if (!empty($data['walletfiles'])) {
                        foreach ($data['walletfiles'] as $file) {
                            WalletSupplierDocumentProcessFile::create([
                                'wallet_supplier_document_process_id' => $wallet_slip_approval->id,
                                'file_path' => $file['path'],
                            ]);
                        }
                    }
                }
            }
        }

        $wallet_slip_approvals_data = WalletSupplierDocumentProcess::with(
            'tradeagreement',
            'tradeagreement.supplier',
            'walletsupplierdocumentprocessfiles',
            'walletsupplierdocumentprocesslog'
        )
            ->get();

        return view('admin.supplier_utility.wallet_bank_slip_approvals.approve_wallet_bank_slips', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'wallet_slip_approvals_data'
        ));
    }

    public function updateWalletSlipStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'status' => 'required|string|in:Approved,Rejected',
        ]);

        $ids = $validated['ids'];
        $status = $validated['status'];

        $data = WalletSupplierDocumentProcess::whereIn('id', $ids)->get();
        foreach ($data as $dta) {

            $updated_data = WalletSupplierDocumentProcessLog::create([
                'wallet_supplier_document_process_id' => $dta->id,
                'approved_by' => Auth::user()->id,
                'previous_status' => $dta->status,
                'updated_status' => $status,
                'previous_approve_status' => $dta->approve_status,
                'updated_approve_status' => $dta->approve_status,
                'stage' => 'Initial',
            ]);

            $dta->status = $status;
            $dta->save();
        }

        return response()->json([
            'message' => 'Status updated successfully.',
        ]);
    }



    public function index_final()
    {

        $permission = $this->mypermissionsforAModule();
        $pmodule = 'supplier-bank-deposits-final-approval';
        $title = 'Final Approval Bank Deposits';
        $model = 'supplier-bank-deposits-final-approval';

        if (!can('view', $pmodule)) {
            Session::flash('warning', pageRestrictedMessage());
            return redirect()->back();
        }

        $wallet_slip_approvals_data = WalletSupplierDocumentProcess::with(
            'tradeagreement',
            'tradeagreement.supplier',
            'walletsupplierdocumentprocessfiles',
            'walletsupplierdocumentprocesslogs'
        )
        ->get();
        // dd($wallet_slip_approvals_data->walletsupplierdocumentprocesslog->count());

        return view('admin.supplier_utility.wallet_bank_slip_approvals.approve_wallet_bank_slips_final', compact(
            'title',
            'model',
            'pmodule',
            'permission',
            'wallet_slip_approvals_data'
        ));
    }

    public function updateWalletSlipStatusFinal(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'wallet_bank_payment_id' => 'required|array',
            'status' => 'required|string|in:Approved,Rejected',
        ]);

        $ids = $validated['ids'];
        $wallet_bank_payment_ids = $validated['wallet_bank_payment_id'];
        $status = $validated['status'];

        // WalletSupplierDocumentProcess::whereIn('id', $ids)->update([
        //     'status' => $status,
        //     'approve_status' => 'Final'
        // ]);

        $data = WalletSupplierDocumentProcess::whereIn('id', $ids)->get();
        foreach ($data as $dta) {
            // dd($dta,$dta->status, $status);
            $updated_data = WalletSupplierDocumentProcessLog::create([
                'wallet_supplier_document_process_id' => $dta->id,
                'approved_by' => Auth::user()->id,
                'previous_status' => $dta->status,
                'updated_status' => $status,
                'previous_approve_status' => $dta->approve_status,
                'updated_approve_status' => 'Final',
                'stage' => 'Final',
            ]);

            $dta->status = $status;
            $dta->approve_status = 'Final';
            $dta->save();
        }





        $updated_records = WalletSupplierDocumentProcess::whereIn('id', $ids)
            ->where('status', 'Approved')
            ->where('approve_status', 'Final')
            ->get(['id', 'wallet_bank_payment_id']);

        $request_data = [
            'status' => $status,
            'ids' => $updated_records->pluck('id')->toArray(),
            'wallet_bank_payment_ids' => $updated_records->pluck('wallet_bank_payment_id')->toArray()
        ];

        if ($request_data['ids']) {
            $api = new ApiService(env('SUPPLIER_PORTAL_URI'));
            $wallet_bank_slips_data_updated = $api->postRequest('/api/update-all-wallet-bank-slip-requests', $request_data);
        }

        return response()->json([
            'message' => 'Status updated successfully.',
        ]);
    }
}
