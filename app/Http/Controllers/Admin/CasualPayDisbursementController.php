<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Model\WaGlTran;
use App\Model\Restaurant;
use App\Model\WaBanktran;
use App\Model\WaBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Model\WaChartsOfAccount;
use App\Model\WaNumerSeriesCode;
use App\Model\WaAccountingPeriod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CasualsPayDisbursement;
use App\Services\PesaFlowDisbursementService;

class CasualPayDisbursementController extends Controller
{
    protected $title = 'Casuals Pay';
    protected $model = 'casuals-pay';
    
    public function showSuccessfulDisbursementsPage()
    {
        if (can('view', 'casuals-pay-successful-disbursements')) {
            $title = $this->title . ' | Successful Disbursements';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Casuals Pay' => '', 'Successful Disbursements' => ''];

            $branches = Restaurant::all();

            return view('admin.hr.payroll.casual-pay.successful-disbursements', compact('title', 'model', 'breadcum', 'branches'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function showFailedDisbursementsPage()
    {
        if (can('view', 'casuals-pay-failed-disbursements')) {
            $title = $this->title . ' | Failed Disbursements';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Casuals Pay' => '', 'Failed Disbursements' => ''];

            $user = Auth::user();

            $branches = Restaurant::all();

            return view('admin.hr.payroll.casual-pay.failed-disbursements', compact('title', 'model', 'breadcum', 'user', 'branches'));
        } else {
            return returnAccessDeniedPage();
        }
    }
    
    public function showExpungedDisbursementsPage()
    {
        if (can('view', 'casuals-pay-expunged-disbursements')) {
            $title = $this->title . ' | Expunged Disbursements';
            $model = $this->model;
            $breadcum = ['HR and Payroll' => '', 'Payroll' => '', 'Casuals Pay' => '', 'Expunged Disbursements' => ''];

            $branches = Restaurant::all();

            return view('admin.hr.payroll.casual-pay.expunged-disbursements', compact('title', 'model', 'breadcum', 'branches'));
        } else {
            return returnAccessDeniedPage();
        }
    }

    // API
    public function successfulDisbursements(Request $request)
    {
        $branchId = $request->query('branch_id');
        $month = $request->query('month');
        $year = $request->query('year');

        $disbursements =CasualsPayDisbursement::with('casualsPayPeriodDetail.casualsPayPeriod', 'casualsPayPeriodDetail.casual.branch')
            ->when($branchId, function ($query) use ($branchId) {
                $query->whereHas('casualsPayPeriodDetail.casual', function ($casual) use ($branchId) {
                    $casual->where('branch_id', $branchId);
                });
            })
            ->when($month, function ($query) use ($month) {
                $query->whereHas('casualsPayPeriodDetail.casualsPayPeriod', function ($query) use ($month) {
                    $query->whereMonth('start_date', Carbon::parse($month)->month);
                });
            })
            ->when($year, function ($query) use ($year) {
                $query->whereHas('casualsPayPeriodDetail.casualsPayPeriod', function ($query) use ($year) {
                    $query->whereYear('start_date', $year);
                });
            })
            ->where('call_back_status', 'complete')
            ->latest()
            ->get();
        
        return response()->json($disbursements);
    }

    public function failedDisbursements(Request $request)
    {
        $branchId = $request->query('branch_id');

        $disbursements =CasualsPayDisbursement::with('casualsPayPeriodDetail.casualsPayPeriod', 'casualsPayPeriodDetail.casual.branch')
            ->when($branchId, function ($query) use ($branchId) {
                $query->whereHas('casualsPayPeriodDetail.casual', function ($casual) use ($branchId) {
                    $casual->where('branch_id', $branchId);
                });
            })
            ->where(function ($query) {
                $query->whereNot('call_back_status', 'complete')
                    ->orWhereNull('call_back_status');
            })
            ->latest()
            ->get();
        
        return response()->json($disbursements);
    }

    public function expungedDisbursements(Request $request)
    {
        $branchId = $request->query('branch_id');
        $month = $request->query('month');
        $year = $request->query('year');

        $disbursements =CasualsPayDisbursement::withoutGlobalScope('expunged')
            ->with('casualsPayPeriodDetail.casualsPayPeriod', 'casualsPayPeriodDetail.casual.branch')
                ->when($branchId, function ($query) use ($branchId) {
                    $query->whereHas('casualsPayPeriodDetail.casual', function ($casual) use ($branchId) {
                        $casual->where('branch_id', $branchId);
                    });
                })
                ->when($month, function ($query) use ($month) {
                    $query->whereHas('casualsPayPeriodDetail.casualsPayPeriod', function ($query) use ($month) {
                        $query->whereMonth('start_date', Carbon::parse($month)->month);
                    });
                })
                ->when($year, function ($query) use ($year) {
                    $query->whereHas('casualsPayPeriodDetail.casualsPayPeriod', function ($query) use ($year) {
                        $query->whereYear('start_date', $year);
                    });
                })
            ->where('expunged', true)
            ->latest()
            ->get();
        
        return response()->json($disbursements);
    }

    public function failedDisbursementsRecheckAndResend(Request $request)
    {
        try {
            $pesaFlow = new PesaFlowDisbursementService();
            
            $updatedCount = 0;
            $initiatedCount = 0;
            foreach($request->all() as $id) {
                $disbursement = CasualsPayDisbursement::with('casualsPayPeriodDetail.casual')->find($id);

                $response = json_decode($pesaFlow->getTransactionStatus($disbursement->reference), true);

                $status = $response['status'];
                
                $updated = $this->processDisbursement($disbursement, $status);
                
                if ($updated) {
                    $updatedCount++;
                }

                if ($status != 'complete') {
                    $callBackUrl = env('APP_URL') . "/api/hr/payroll/casual-pay-period-details-disbursement/{$disbursement->id}/callback";

                    try {
                        $response = $pesaFlow->initiateWithdrawal($disbursement->casualsPayPeriodDetail->casual->phone_no, $disbursement->amount, $callBackUrl);

                        if ($response->ok()) {
                            $disbursement->update(['reference' => $response->json()['reference']]);
                
                            $initiatedCount++;

                        } else {
                            throw new Exception($response->body());
                        }

                    } catch (Exception $e) {
                        Log::info("Failed: $disbursement->narrative: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['message' => "$updatedCount disbursement updated, $initiatedCount disbursement re-initiated."]);
            
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function failedDisbursementsExpunge(Request $request)
    {
        try {
            CasualsPayDisbursement::whereIn('id', $request->all())->update(['expunged' => true]);

            return response()->json(['message' => 'Disbursements expunged successfully']);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function disbursementCallback(Request $request, $disbursementId)
    {
        Log::info("Callback received from pesaflow b2c");
        Log::info(json_encode($request->all()));

        try {
            $disbursement = CasualsPayDisbursement::find($disbursementId);

            if ($disbursement) {
                $this->processDisbursement($disbursement, $request['status']);

            } else {
                Log::info("Casual pay disbursement not found");
            }
        } catch (\Throwable $e) {
            Log::info("Call back PF failed");
            Log::error($e->getMessage(), $e->getTrace());
        }
    }

    public function processDisbursement($disbursement, $status)
    {
        try {
            $disbursement->update(['call_back_status' => $status]);
    
            if ($status == 'complete') {
                $series_module = WaNumerSeriesCode::where('module', 'CASUAL_PAY_DISBURSEMENT')->first();
                $accountingPeriod = WaAccountingPeriod::where('is_current_period', '1')->first();
    
                $date = now();
                $amount = $disbursement->amount;
                $narrative = $disbursement->narrative;
    
                // CREDIT BANK ACCOUNT
                $bank_account = WaBankAccount::where('account_code', '988329')->first();
                $btran = new WaBanktran();
                $btran->type_number = $series_module->type_number;
                $btran->document_no = $disbursement->document_no;
                $btran->bank_gl_account_code = $bank_account->getGlDetail?->account_code;
                $btran->reference = $disbursement->reference;
                $btran->trans_date = $date;
                $btran->wa_payment_method_id = 11; //PETTY CASH
                $btran->amount = $amount * -1;
                $btran->wa_curreny_id = 0;
                $btran->cashier_id = 1;
                $btran->save();
    
                // DEBIT EXPENSE ACCOUNT
                $dr = new WaGlTran();
                $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $dr->grn_type_number = $series_module->type_number;
                $dr->trans_date = $date;
                $dr->restaurant_id = 10; // MAKONGENI;
                $dr->tb_reporting_branch = 10; // MAKONGENI;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $disbursement->document_no;
                $dr->narrative = $narrative;
                $dr->reference = $disbursement->reference;
                $dr->account = WaChartsOfAccount::where('account_code', '56002-033')->first();
                $dr->amount = $amount;
                $dr->save();
    
    
                // CREDIT BANK ACCOUNT
                $dr = new WaGlTran();
                $dr->period_number = $accountingPeriod ? $accountingPeriod->period_no : null;
                $dr->grn_type_number = $series_module->type_number;
                $dr->trans_date = $date;
                $dr->restaurant_id = 10; // MAKONGENI;
                $dr->tb_reporting_branch = 10; // MAKONGENI;
                $dr->grn_last_used_number = $series_module->last_number_used;
                $dr->transaction_type = $series_module->description;
                $dr->transaction_no = $disbursement->document_no;
                $dr->narrative = $narrative;
                $dr->reference = $disbursement->reference;
                $dr->account = $bank_account->getGlDetail->account_code;
                $dr->amount = $amount * -1;
                $dr->save();
                
                return true;
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
