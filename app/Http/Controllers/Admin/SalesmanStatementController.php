<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Model\WaCustomer;
use App\Model\WaDebtorTran;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SalesmanStatementController extends Controller
{
    public function generateStatement(Request $request): JsonResponse
    {
        if (!$request->route_id) {
            return $this->jsonify(['message' => 'Select a route to run statement'], 422);
        }

        try {
            $route = Route::select('id', 'route_name')->find($request->route_id);
            if (!$route) {
                return $this->jsonify(['message' => 'The provided route ID is invalid'], 422);
            }

            if ($request->start_date) {
                $startDate = Carbon::parse($request->start_date);
            } else {
                $startDate = Carbon::now();
            }
            $startDate = $startDate->startOfDay();

            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date);
            } else {
                $endDate = Carbon::now();
            }
            $endDate = $endDate->endOfDay();

            $waCustomer = WaCustomer::select('id')->where('route_id', $route->id)->first();
            $transactions = WaDebtorTran::select('id', 'created_at', 'amount', 'wa_customer_id', 'amount', 'reference', 'document_no')
                ->where('wa_customer_id', $waCustomer->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $openingBalance = WaDebtorTran::where('wa_customer_id', $waCustomer->id)->whereDate('created_at', '<', $startDate->toDate())->sum('amount') ?? 0;
            $previousBalance = $openingBalance;
            $data = [];
            foreach ($transactions as $trans) {
                $balance = $previousBalance + (float)$trans->amount;
                $data[] = [
                    'narrative' => $trans->reference,
                    'document_no' => $trans->document_no,
                    'date' => Carbon::parse($trans->created_at)->format('d/m/Y H:i A'),
                    'balance' => format_amount_with_currency($balance),
                    'amount' => format_amount_with_currency($trans->amount),
                ];
                $previousBalance = $balance;
            }

            $closingBalance = collect($data)->last()['balance'] ?? 0;

            return $this->jsonify([
                'opening_balance' => format_amount_with_currency($openingBalance),
                'closing_balance' => $closingBalance,
                'transactions' => $data
            ], 200);


//            $pdf = \PDF::loadView('admin.salesman_reports.statement', compact('transactions', 'openingBalance', 'route', 'user', 'startDate', 'endDate', 'closingBalance'));
//            return $pdf->stream();
        } catch (\Throwable $e) {
            return $this->jsonify(['message' => $e->getMessage()], 500);
        }
    }
}


//namespace App\Http\Controllers\Admin;
//
//use App\Http\Controllers\Controller;
//use App\Model\Route;
//use App\Model\WaCustomer;
//use App\Model\WaDebtorTran;
//use Carbon\Carbon;
//use Illuminate\Http\Request;
//use Tymon\JWTAuth\Facades\JWTAuth;
//
//class SalesmanStatementController extends Controller
//{
//    public function generateStatement(Request $request)
//    {
//        if (!$request->route_id) {
//            return $this->jsonify(['message' => 'Select a route to run statement'], 422);
//        }
//
//        try {
//            $route = Route::select('id', 'route_name')->find($request->route_id);
//            if (!$route) {
//                return $this->jsonify(['message' => 'The provided route ID is invalid'], 422);
//            }
//
//            if ($request->start_date) {
//                $startDate = Carbon::parse($request->start_date);
//            } else {
//                $startDate = Carbon::now();
//            }
//            $startDate = $startDate->startOfDay();
//
//            if ($request->end_date) {
//                $endDate = Carbon::parse($request->end_date);
//            } else {
//                $endDate = Carbon::now();
//            }
//            $endDate = $endDate->endOfDay();
//
//            $waCustomer = WaCustomer::select('id')->where('route_id', $route->id)->first();
//            $transactions = WaDebtorTran::select('id', 'created_at', 'amount', 'wa_customer_id', 'amount', 'reference', 'document_no')
//                ->where('wa_customer_id', $waCustomer->id)
//                ->whereBetween('created_at', [$startDate, $endDate])
//                ->get();
//
//            $openingBalance = WaDebtorTran::where('wa_customer_id', $waCustomer->id)->whereDate('created_at', '<', $startDate->toDateString())->sum('amount') ?? 0;
//            $previousBalance = $openingBalance;
//            foreach ($transactions as $trans) {
//                $trans->balance = $previousBalance + (float)$trans->amount;
//                $trans->date = Carbon::parse($trans->created_at)->format('d/m/Y H:i A');
//                $previousBalance = $trans->balance;
//            }
//
//            $user = JWTAuth::toUser($request->token);
//            $startDate = $startDate->toDateString();
//            $endDate = $endDate->toDateString();
//            $closingBalance = $transactions->last()->balance;
//
//            $pdf = \PDF::loadView('admin.salesman_reports.statement', compact('transactions', 'openingBalance', 'route', 'user', 'startDate', 'endDate', 'closingBalance'));
//            return $pdf->stream();
//        } catch (\Throwable $e) {
//            return $this->jsonify(['message' => $e->getMessage()], 500);
//        }
//    }
//}

