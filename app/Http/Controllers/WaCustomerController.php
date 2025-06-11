<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\Route;
use App\Model\WaCompanyPreference;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Model\WaGlTran;
use App\Models\FraudJournal;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaCustomerController extends Controller
{
    protected string $model = 'maintain-customers';
    protected string $permissionModule = 'maintain-customers';

    public function showFraudPostingPage($customer): View|RedirectResponse
    {
        if (!can('settle-from-fraud', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Settle Customer Account';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Maintain Customers' => ''];

        $customer = WaCustomer::select(
            "wa_customers.*",
            DB::raw("(select coalesce(sum(amount), 0) from wa_debtor_trans where wa_customer_id = wa_customers.id) as balance")
        )->where('slug', $customer)->first();

        $user = Auth::user();
        if (!$customer) {
            return returnAccessDeniedPage();
        }

        $customer->balance = manageAmountFormat($customer->balance);

        return view('wa_customers.post_fraud', compact('title', 'model', 'breadcrum', 'customer', 'user'));
    }

    public function settleAccountFromFraud(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $customer = WaCustomer::find($request->customer_id);
            $route = Route::find($customer->route_id);

            $documentNumber = getCodeWithNumberSeries('FRAUD AND INVESTIGATION');
            $journal = FraudJournal::create([
                'journal_number' => $documentNumber,
                'reference_date' => Carbon::now(),
                'document_no' => $documentNumber,
                'document_reference' => "$customer->customer_name / FRAUD SETTLEMENT",
                'customer_account_id' => $customer->id,
                'comments' => $request->comment,
                'narrative' => $request->comment,
                'amount' => abs($request->amount),
                'branch_id' => $route->restaurant_id,
                'posted_by' => $request->blamable
            ]);

            $fraudPaymentMethod = PaymentMethod::where('slug', 'fraud-journals')->first();
            $trans = WaDebtorTran::create([
                'wa_customer_id' => $customer->id,
                'customer_number' => $customer->customer_code,
                'trans_date' => Carbon::now(),
                'input_date' => Carbon::now(),
                'invoice_customer_name' => "$customer->customer_name",
                'reference' => $journal->document_reference,
                'amount' => $journal->amount * -1,
                'document_no' => $documentNumber,
                'branch_id' => $journal->branch_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'channel' => $fraudPaymentMethod?->title,
                'wa_payment_method_id' => $fraudPaymentMethod?->id,
                'user_id' => $request->blamable,
                'register_cheque_id' => $journal->id
            ]);

            $glTrans = [];

            $companyPreference = WaCompanyPreference::find(1);
            $debtorsControl = $companyPreference->debtorsControlGlAccount?->account_code;
            $glTrans[] = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'restaurant_id' => $trans->branch_id,
                'tb_reporting_branch' => $trans->branch_id,
                'transaction_type' => 'FRAUD JOURNAL',
                'transaction_no' => $trans->document_no,
                'trans_date' => $trans->trans_date,
                'account' => $debtorsControl,
                'amount' => abs($trans->amount) * -1,
                'reference' => $trans->reference,
                'narrative' => "$customer->customer_name / $trans->document_no / $trans->reference",
            ];

            $fraudAccount = '55001-007';
            $glTrans[] = [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'restaurant_id' => $trans->branch_id,
                'tb_reporting_branch' => $trans->branch_id,
                'transaction_type' => 'Fraud Journal',
                'transaction_no' => $trans->document_no,
                'trans_date' => $trans->trans_date,
                'account' => $fraudAccount,
                'amount' => abs($trans->amount),
                'reference' => $trans->reference,
                'narrative' => "$customer->customer_name / $trans->document_no / $trans->reference",
            ];

            WaGlTran::insert($glTrans);

            DB::commit();
            return $this->jsonify([]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
