<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Restaurant;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class PoscashBankingReportController extends Controller
{
    protected string $model = 'cash-banking-report';
    protected string $permissionModule = 'cashier-management';

    public function showReportPage(): View|RedirectResponse
    {
        if (!can('cash-banking-report', $this->permissionModule)) {
            return returnAccessDeniedPage();
        }

        $title = 'Cash Banking Report';
        $model = $this->model;
        $breadcrum = ['Sales & Receivables' => '', 'Cash Banking Report' => ''];

        $branches = Restaurant::select('id', 'name')->get();
        $user = User::find(Auth::id());

        return view('banking_approval.cash_banking_report', compact('title', 'model', 'branches', 'breadcrum', 'user'));
    }

    public function generateReport(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $toDate = Carbon::parse($request->date)->endOfDay();
            // $fromDate = '2024-11-03 00:00:00';
            // $toDate = '2024-11-03 12:00:00';

            $salesQuery = DB::select("
                select users.name as cashier, 
                roles.slug as cashier_type,
                coalesce(sum(items.total - items.discount_amount), 0) as cs
                from wa_pos_cash_sales_items as items
                join wa_pos_cash_sales as sales on items.wa_pos_cash_sales_id = sales.id 
                and sales.branch_id = $request->branch_id 
                and sales.status = 'Completed'
                and (sales.created_at between '$fromDate' and '$toDate')
                join users on sales.attending_cashier = users.id 
                join roles on users.role_id = roles.id
                group by cashier
            ");
           

            $returnsQuery = DB::select("
            select users.name as cashier, 
                coalesce(sum(selling_price * r.return_quantity), 0) as csr
                from wa_pos_cash_sales_items_return as r
                join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                and sales.branch_id = $request->branch_id 
                and sales.status = 'Completed'
                join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                join users on sales.attending_cashier = users.id 
                where r.accepted = 1 and (r.accepted_at between '$fromDate' and '$toDate')
                group by cashier
            ");

            $tabletReturnsQuery = DB::select("
            select users.name as cashier, 
                coalesce(sum(selling_price * r.return_quantity), 0) as csr
                from wa_pos_cash_sales_items_return as r
                join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                and sales.branch_id = $request->branch_id 
                and sales.status = 'Completed'
                and sales.is_tablet_sale = 1
                and sales.user_id = sales.attending_cashier
                join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                join users on sales.attending_cashier = users.id 
                where r.accepted = 1 and (r.created_at between '$fromDate' and '$toDate')
                group by cashier
            ");

            $paymentsQuery = DB::select("
                select users.name as cashier,
                coalesce(sum(IF(is_cash = 1, payments.amount, 0)), 0) as cp,
                coalesce(sum(IF(is_cash = 0, payments.amount, 0)), 0) as db
                from wa_pos_cash_sales as sales
                join users on sales.attending_cashier = users.id
                join wa_pos_cash_sales_payments as payments on sales.id = payments.wa_pos_cash_sales_id
                join payment_methods as methods on payments.payment_method_id = methods.id
                where (sales.created_at between '$fromDate' and '$toDate')
                and sales.branch_id = $request->branch_id and sales.status = 'Completed'
                group by cashier
            ");

            $dropsQuery = DB::select("
                select users.name as cashier,
                coalesce(sum(amount), 0) as drp
                from cash_drop_transactions as drops
                join users on drops.cashier_id = users.id and restaurant_id = 1
                where (drops.created_at between '$fromDate' and '$toDate')
                group by cashier
            ");

            $invoiceQuery = DB::select("
                select users.name as cashier, coalesce(sum(total_cost_with_vat), 0) as inv 
                from wa_internal_requisition_items as items 
                join wa_internal_requisitions as invoices on items.wa_internal_requisition_id = invoices.id 
                and invoices.invoice_type = 'Backend' 
                and invoices.restaurant_id = $request->branch_id
                join users on invoices.user_id = users.id
                where (invoices.created_at between '$fromDate' and '$toDate')
                group by cashier
            ");

            $invoiceReturnsQuery = DB::select("
                select users.name as cashier, coalesce(sum(items.selling_price * r.received_quantity), 0) as crn 
                from wa_inventory_location_transfer_item_returns as r 
                join wa_inventory_location_transfer_items as items on r.wa_inventory_location_transfer_item_id = items.id 
                join wa_inventory_location_transfers as transfers on r.wa_inventory_location_transfer_id = transfers.id 
                join wa_internal_requisitions as invoices on transfers.transfer_no = invoices.requisition_no 
                and invoices.invoice_type = 'Backend' 
                and invoices.restaurant_id = $request->branch_id
                join users on invoices.user_id = users.id
                where (invoices.created_at between '$fromDate' and '$toDate')
                group by cashier
            ");

            $chequesQuery = DB::select("
                select users.name as cashier, coalesce(sum(cheques.amount), 0) as chq from register_cheque as cheques 
                join users on cheques.deposited_by = users.id 
                where (cheques.created_at between '$fromDate' and '$toDate') 
                and cheques.branch_id = $request->branch_id
            ");

            $missingCashiers = [];
            collect($invoiceQuery)->map(function ($record) use ($salesQuery, &$missingCashiers) {
                $cashier = collect($salesQuery)->where('cashier', $record->cashier)->first()?->cashier;
                if (!$cashier) {
                    $missingCashiers[] = $record->cashier;
                }
            });
            collect($returnsQuery)->map(function ($record) use ($salesQuery, &$missingCashiers) {
                $cashier = collect($salesQuery)->where('cashier', $record->cashier)->first()?->cashier;
                if (!$cashier) {
                    $missingCashiers[] = $record->cashier;
                }
            });

            $salesQuery = collect($salesQuery);

            foreach ($missingCashiers as $cashier) {
                $cashierRecord = new stdClass();
                $cashierRecord->cashier = $cashier;
                $cashierRecord->cashier_type = 'pos-cashier';
                $cashierRecord->cs = 0;
                $cashierRecord->disc = 0;
                $cashierRecord->sales = 0;
                $salesQuery->push($cashierRecord);
            }

            $records = collect($salesQuery)->map(function ($record) use ($paymentsQuery, $dropsQuery, $returnsQuery, $tabletReturnsQuery, $invoiceQuery, $invoiceReturnsQuery, $chequesQuery) {
                $record->cashier_type = $record->cashier_type == 'pos-salesman' ? 'Tablet' : 'Counter';
                // $record->cs = $record->cs - $record->disc;
                $record->cs = $record->cs;
                $record->inv = collect($invoiceQuery)->where('cashier', $record->cashier)->first()?->inv ?? 0;
                $record->csr = (collect($returnsQuery)->where('cashier', $record->cashier)->first()?->csr ?? 0);
                $record->tsr = (collect($tabletReturnsQuery)->where('cashier', $record->cashier)->first()?->csr ?? 0);
                $record->crn = collect($invoiceReturnsQuery)->where('cashier', $record->cashier)->first()?->crn ?? 0;
                $record->ns = $record->cs + $record->inv - $record->csr - $record->crn;
                $record->cp = collect($paymentsQuery)->where('cashier', $record->cashier)->first()?->cp ?? 0;
                $record->db = collect($paymentsQuery)->where('cashier', $record->cashier)->first()?->db ?? 0;
                $record->chq = collect($chequesQuery)->where('cashier', $record->cashier)->first()?->chq ?? 0;
                $record->tc = $record->cp + $record->db - $record->chq;

                $record->drp = 0;
                if ($record->cashier_type == 'Counter') {
                    $record->drp = collect($dropsQuery)->where('cashier', $record->cashier)->first()?->drp ?? 0;
                }

                $record->ec = $record->cashier_type == 'Counter' ? $record->cp - $record->csr - $record->drp : 0;
                // $record->ec = $record->cashier_type == 'Counter' ? $record->ns - $record->db - $record->drp : 0;

                // f is formatted. Retaining raw values for summing
                $record->fcs = manageAmountFormat($record->cs);
                $record->finv = manageAmountFormat($record->inv);
                $record->fcsr = manageAmountFormat($record->csr);
                $record->ftsr = manageAmountFormat($record->tsr);
                $record->fcrn = manageAmountFormat($record->crn);
                $record->fns = manageAmountFormat($record->ns);
                $record->fcp = manageAmountFormat($record->cp);
                $record->fdb = manageAmountFormat($record->db);
                $record->fchq = manageAmountFormat($record->chq);
                $record->ftc = manageAmountFormat($record->tc);
                $record->fec = manageAmountFormat($record->ec);
                $record->fdrp = manageAmountFormat($record->drp);

                return $record;
            });

            $records = $records->sortByDesc('cs');

            $records->push(([
                'cashier' => '',
                'cashier_type' => 'Cashier Totals',
                'cs' => manageAmountFormat($records->sum('cs')),
                'inv' => manageAmountFormat($records->sum('inv')),
                'csr' => manageAmountFormat($records->sum('csr')),
                'tsr' => manageAmountFormat($records->sum('tsr')),
                'crn' => manageAmountFormat($records->sum('crn')),
                'ns' => manageAmountFormat($records->sum('ns')),
                'cp' => manageAmountFormat($records->sum('cp')),
                'db' => manageAmountFormat($records->sum('db')),
                'chq' => manageAmountFormat($records->sum('chq')),
                'tc' => manageAmountFormat($records->sum('tc')),
                'ec' => manageAmountFormat($records->sum('ec')),
                'drp' => manageAmountFormat($records->sum('drp')),
            ]));

            return $this->jsonify($records->values());
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }

    public function generateChiefCashierReport(Request $request): JsonResponse
    {
        try {
            $fromDate = Carbon::parse($request->date)->startOfDay();
            $toDate = Carbon::parse($request->date)->endOfDay();

            $bf = 0;
            $bankedCash = 0;
            $cashVariance = $bf - $bankedCash;

            $dropsQuery = DB::select("
                select coalesce(sum(amount), 0) as drp
                from cash_drop_transactions as drops
                join users on drops.cashier_id = users.id and restaurant_id = $request->branch_id
                where (drops.created_at between '$fromDate' and '$toDate')
            ");
            $drops = collect($dropsQuery)->sum('drp');


            $cdmQuery = DB::select("
                select coalesce(sum(banked_amount), 0) as amount
                from cash_drop_transactions as drops
                join users on drops.cashier_id = users.id and restaurant_id = $request->branch_id
                where (drops.created_at between '$fromDate' and '$toDate')
            ");
            $cdms = collect($cdmQuery)->sum('amount');

            $tabletReturnsQuery = DB::select("
                select coalesce(sum(selling_price * r.return_quantity), 0) as tsr
                from wa_pos_cash_sales_items_return as r
                join wa_pos_cash_sales as sales on r.wa_pos_cash_sales_id = sales.id 
                and sales.branch_id = $request->branch_id 
                and sales.status = 'Completed'
                and sales.is_tablet_sale = 1
                and (r.accepted_at between '$fromDate' and '$toDate')
                join wa_pos_cash_sales_items as items on items.id = r.wa_pos_cash_sales_item_id 
                join users on sales.attending_cashier = users.id 
                where r.accepted = 1 and sales.user_id = sales.attending_cashier
            ");
            $tabletReturns = collect($tabletReturnsQuery)->sum('tsr');


            $crd = 0;
            $bankedCrd = 0;
            $crdVariance = $crd - $bankedCrd;

            $variance = $drops - $cdms;
            $ecb = $cashVariance + $variance + $crdVariance - $tabletReturns;

            $data = [
                'bf' => manageAmountFormat($bf),
                'banked_cash' => manageAmountFormat($bankedCash),
                'cash_variance' => manageAmountFormat($cashVariance),
                'drops' => manageAmountFormat($drops),
                'cdms' => manageAmountFormat($cdms),
                'variance' => manageAmountFormat($variance),
                'crd' => manageAmountFormat($crd),
                'bcrd' => manageAmountFormat($bankedCrd),
                'crd_variance' => manageAmountFormat($crdVariance),
                'tsr' => manageAmountFormat($tabletReturns),
                'ecb' => manageAmountFormat($ecb)
            ];

            return $this->jsonify($data);
        } catch (\Throwable $th) {
            return $this->jsonify(['message' => $th->getMessage()], 500);
        }
    }
}
