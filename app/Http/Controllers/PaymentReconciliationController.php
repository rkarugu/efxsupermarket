<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use App\Models\PaymentVerificationBank;
use App\Models\SuspendedTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaymentReconciliationController extends Controller
{
    static string $permissionModule = 'reconciliation';
    static string $baseRoute = 'payment-reconciliation';

    public function showOverviewPage(Request $request): View|RedirectResponse
    {
        if (!can('see-overview', self::$permissionModule)) {
            return returnAccessDeniedPage();
        }

        $branches = Restaurant::select('id', 'name')->get();

        $title = 'Payment reconciliation - Overview';
        $model = self::$permissionModule;
        $base_route = self::$baseRoute;

        return view('reconciliation.payments.overview', compact('title', 'model', 'branches', 'base_route'));
    }

    public function getDebtorsBalance(Request $request): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [],
            'message' => '',
        ];

        try {
            $data = WaDebtorTran::query()
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

//            if ($request->start_date) {
//                $data = $data->whereDate('wa_debtor_trans.trans_date', '>=', Carbon::parse($request->start_date)->startOfDay());
//            }
//
//            if ($request->end_date) {
//                $data = $data->whereDate('wa_debtor_trans.trans_date', '<=', Carbon::parse($request->end_date)->endOfDay());
//            }

            $response['data'] = manageAmountFormat($data->sum('amount'));
            return $this->jsonify($response, 200);
        } catch (Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $this->jsonify($response, 500);
        }
    }

    public function getSummary(Request $request): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [],
            'message' => '',
        ];

        try {
            $data = WaDebtorTran::query()
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

            $salesDate = $request->start_date;
            if (!$salesDate) {
                $salesDate = Carbon::today()->toDateString();
            }

//            if ($request->end_date) {
//                $data = $data->whereDate('wa_debtor_trans.trans_date', '<=', Carbon::parse($request->end_date)->addDay()->endOfDay());
//            }

            $data = $data->whereDate('wa_debtor_trans.trans_date', '=', Carbon::parse($salesDate)->addDay()->toDateString());

            $data = $data->where('document_no', 'like', 'RCT%');

            $response['data']['all'] = manageAmountFormat(abs($data->clone()->whereNot('verification_status', 'manual upload')->sum('amount')));
            $response['data']['approved'] = manageAmountFormat(abs($data->clone()->where('verification_status', 'approved')->sum('amount')));
            $response['data']['verified'] = manageAmountFormat(abs($data->clone()->where('verification_status', 'verified')->sum('amount')));
            $response['data']['pending'] = manageAmountFormat(abs($data->clone()->where('verification_status', 'pending')->sum('amount')));

            return $this->jsonify($response, 200);
        } catch (Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $this->jsonify($response, 500);
        }
    }

    public function getSalesVsReceipts(Request $request): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [],
            'message' => '',
        ];

        try {
            $salesDate = $request->start_date;
            if (!$salesDate) {
                $salesDate = Carbon::today()->toDateString();
            }

            $sales = DB::table('wa_internal_requisition_items')
                ->join('wa_internal_requisitions', 'wa_internal_requisition_items.wa_internal_requisition_id', '=', 'wa_internal_requisitions.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_internal_requisitions.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

//            if ($request->start_date) {
//                $sales = $sales->whereDate('wa_internal_requisition_items.created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
//            }
//
//            if ($request->end_date) {
//                $sales = $sales->whereDate('wa_internal_requisition_items.created_at', '>=', Carbon::parse($request->end_date)->endOfDay());
//            }

            $sales = $sales->whereDate('wa_internal_requisition_items.created_at', '=', Carbon::parse($salesDate)->toDateString());

            $receipts = WaDebtorTran::query()
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

//            if ($request->start_date) {
//                $receipts = $receipts->whereDate('wa_debtor_trans.trans_date', '>=', Carbon::parse($request->start_date)->startOfDay());
//            }
//
//            if ($request->end_date) {
//                $receipts = $receipts->whereDate('wa_debtor_trans.trans_date', '<=', Carbon::parse($request->end_date)->endOfDay());
//            }

            $receipts = $receipts->whereDate('wa_debtor_trans.trans_date', '=', Carbon::parse($salesDate)->addDay()->toDateString());
            $receipts = $receipts->where('document_no', 'like', 'RCT%');

            $response['data']['sales'] = manageAmountFormat(abs($sales->clone()->sum('total_cost_with_vat')));
            $response['data']['receipts'] = manageAmountFormat(abs($receipts->clone()->whereNot('verification_status', 'manual upload')->sum('amount')));
            $response['data']['variance'] = manageAmountFormat(abs($sales->clone()->sum('total_cost_with_vat')) - abs($receipts->clone()->whereNot('verification_status', 'manual upload')->sum('amount')));
            return $this->jsonify($response, 200);
        } catch (Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $this->jsonify($response, 500);
        }
    }

    public function getReconIssues(Request $request): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [],
            'message' => '',
        ];

        try {
            $salesDate = $request->start_date;
            if (!$salesDate) {
                $salesDate = Carbon::today()->toDateString();
            }

            $data = WaDebtorTran::query()
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

//            if ($request->start_date) {
//                $data = $data->whereDate('wa_debtor_trans.trans_date', '>=', Carbon::parse($request->start_date)->startOfDay());
//            }
//
//            if ($request->end_date) {
//                $data = $data->whereDate('wa_debtor_trans.trans_date', '<=', Carbon::parse($request->end_date)->endOfDay());
//            }

            $data = $data->whereDate('wa_debtor_trans.trans_date', '=', Carbon::parse($salesDate)->addDay()->toDateString());
            $data = $data->where('document_no', 'like', 'RCT%');

            //missing
            $response['data']['missing'] = manageAmountFormat(abs($data->clone()->where('verification_status', 'pending')->sum('amount')));
            $response['data']['missing_count'] = abs($data->clone()->where('verification_status', 'pending')->count());

            //duplicate
            $data = $data->groupBy('reference', 'amount')
                ->havingRaw('COUNT(*) > 1');

            $duplicateRows = $data->get([
                'reference',
                'amount',
                DB::raw('COUNT(*) as duplicate_count'),
                DB::raw('SUM(amount) as total_amount')
            ]);
            $totalDuplicateAmount = $duplicateRows->sum('total_amount');
            $totalDuplicateCount = $duplicateRows->sum('duplicate_count');

            $response['data']['duplicate'] = manageAmountFormat(abs($totalDuplicateAmount));
            $response['data']['duplicate_count'] = $totalDuplicateCount;

            //unknown
            $unknownBankings = PaymentVerificationBank::query()
                ->where('status', 'Pending');
            if ($request->start_date) {
                $unknownBankings = $unknownBankings->whereDate('payment_verification_banks.bank_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }
            if ($request->end_date) {
                $unknownBankings = $unknownBankings->whereDate('payment_verification_banks.bank_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }
            $response['data']['unknown'] = manageAmountFormat(abs($unknownBankings->clone()->sum('amount')));
            $response['data']['unknown_count'] = abs($unknownBankings->clone()->count());
            return $this->jsonify($response, 200);
        } catch (Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $this->jsonify($response, 500);
        }
    }

    public function getReconResolutions(Request $request): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => [],
            'message' => '',
        ];

        try {
            $data = SuspendedTransaction::query()
                ->join('wa_customers', 'suspended_transactions.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

            if ($request->start_date) {
                $data = $data->whereDate('suspended_transactions.trans_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }

            if ($request->end_date) {
                $data = $data->whereDate('suspended_transactions.trans_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }

            //missing
            $response['data']['suspended'] = manageAmountFormat(abs($data->clone()->where('status', 'suspended')->sum('amount')));
            $response['data']['suspended_count'] = abs($data->clone()->count());
            $response['data']['expunged'] = manageAmountFormat(abs($data->clone()->where('status', 'expunged')->sum('amount')));
            $response['data']['expunged_count'] = abs($data->clone()->count());
            $manual = WaDebtorTran::query()
                ->join('wa_customers', 'wa_debtor_trans.wa_customer_id', 'wa_customers.id')
                ->join('routes', function ($join) use ($request) {
                    $query = $join->on('wa_customers.route_id', 'routes.id');
                    if ($request->branch_id) {
                        $query = $query->where('routes.restaurant_id', $request->branch_id);
                    }
                });

            if ($request->start_date) {
                $manual = $manual->whereDate('wa_debtor_trans.trans_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }

            if ($request->end_date) {
                $manual = $manual->whereDate('wa_debtor_trans.trans_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }

            $manual = $manual->where('document_no', 'like', 'RCT%')
                ->where('manual_upload_status', '1');

            $response['data']['manual'] = manageAmountFormat(abs($manual->clone()->sum('amount')));
            $response['data']['manual_count'] = abs($manual->clone()->count());

            return $this->jsonify($response, 200);
        } catch (Throwable $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            return $this->jsonify($response, 500);
        }
    }
}
