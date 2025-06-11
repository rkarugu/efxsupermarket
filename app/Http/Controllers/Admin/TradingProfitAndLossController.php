<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WaAccountGroup;
use App\Model\WaChartsOfAccount;
use App\Model\WaGlTran;
use App\Model\WaLocationAndStore;
use App\Model\WaStockMove;
use App\Services\ExcelDownloadService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TradingProfitAndLossController extends Controller
{
    protected $model = 'trading-profit-and-loss';

    protected $title = 'Trading Profit & Loss';

    public function index()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->endOfMonth()->format('Y-m-d 23:59:59');

        $openingStock = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * items.standard_cost)) as amount')
            ])
            ->join('wa_inventory_items as items', 'items.id', 'wa_inventory_item_id')
            ->where('wa_stock_moves.created_at', '<', $from)
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_location_and_store_id', request()->branch);
            })
            ->first();

        $closingStock = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * items.standard_cost)) as amount'),
                DB::raw("ABS(SUM((100 / (100 + tax_managers.tax_value)* (qauntity * wa_stock_moves.standard_cost)))) as exc_amount"),
            ])
            ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
            ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
            ->where('wa_stock_moves.created_at', '<', $to)
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
            })
            ->first();

        $grns = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * wa_stock_moves.standard_cost)) as amount'),
                DB::raw("ABS(SUM((100 / (100 + tax_managers.tax_value)* (qauntity * wa_stock_moves.standard_cost)))) as exc_amount"),
            ])
            ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
            ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
            ->where('document_no', 'LIKE', 'GRN%')
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
            })
            ->first();

        $transfersIn = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * wa_stock_moves.standard_cost)) as amount'),
                DB::raw("ABS(SUM((100 / (100 + tax_managers.tax_value)* (qauntity * wa_stock_moves.standard_cost)))) as exc_amount"),
            ])
            ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
            ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
            ->where(function ($query) {
                $query->where('document_no', 'LIKE', 'MAR%')
                    ->orWhere('document_no', 'LIKE', 'TRA%');
            })
            ->where('qauntity', '>', 0)
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
            })
            ->first();

        $transfersOut = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * wa_stock_moves.standard_cost)) as amount'),
                DB::raw("ABS(SUM((100 / (100 + tax_managers.tax_value)* (qauntity * wa_stock_moves.standard_cost)))) as exc_amount"),
            ])
            ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
            ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
            ->where(function ($query) {
                $query->where('document_no', 'LIKE', 'MAR%')
                    ->orWhere('document_no', 'LIKE', 'TRA%');
            })
            ->where('qauntity', '<', 0)
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
            })
            ->first();

        $returns = WaStockMove::query()
            ->select([
                DB::raw('ABS(SUM(qauntity * wa_stock_moves.standard_cost)) as amount'),
                DB::raw("ABS(SUM((100 / (100 + tax_managers.tax_value)* (qauntity * wa_stock_moves.standard_cost)))) as exc_amount"),
            ])
            ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
            ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
            ->where('document_no', 'LIKE', 'RFS%')
            ->whereBetween('wa_stock_moves.created_at', [$from, $to])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
            })
            ->first();

        $incomeGroups = WaAccountGroup::whereHas('getAccountSection', function ($query) {
            $query->where('section_name', 'INCOME');
        });

        $sales = WaGlTran::query()
            ->select([
                'account',
                DB::raw('SUM(amount) as amount'),
            ])
            ->whereBetween('trans_date', [$from, $to])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('restaurant_id', WaLocationAndStore::find(request()->branch)->wa_branch_id);
            })
            ->groupBY('account')
            ->whereIn('account', WaChartsOfAccount::whereIn('wa_account_group_id', $incomeGroups->pluck('id')->toArray())->get()->pluck('account_code')->toArray())
            ->first();

        $expenseGroups = WaAccountGroup::whereHas('getAccountSection', function ($query) {
            $query->where('section_name', 'EXPENSES');
        })->get();

        $expenses = WaGlTran::query()
            ->select([
                'account',
                DB::raw('SUM(amount) as amount'),
            ])
            ->when(request()->filled('branch'), function ($query) {
                $query->where('restaurant_id', WaLocationAndStore::find(request()->branch)->wa_branch_id);
            })
            ->whereIn('account', WaChartsOfAccount::whereIn('wa_account_group_id', $expenseGroups->pluck('id')->toArray())->get()->pluck('account_code')->toArray())
            ->whereBetween('trans_date', [$from, $to])
            ->groupBy('account')
            ->get();

        if (request()->download == 'pdf') {
            $pdf = Pdf::loadView('admin.reports.print.trading_profit_and_loss', [
                'sales' => abs($sales->amount ?? 0),
                'openingStock' => $openingStock->amount,
                'grns_total' => $grns->exc_amount,
                'transfers_in_total' => $transfersIn->exc_amount,
                'transfers_out_total' => abs($transfersOut->exc_amount),
                'returns_total' => abs($returns->exc_amount),
                'closingStock' => $closingStock->exc_amount,
                'expenses' => $expenses,
                'from' => Carbon::parse($from)->format('d/m/Y'),
                'to' => Carbon::parse($to)->format('d/m/Y'),
            ]);

            return $pdf->stream('trading_profit_and_loss_' . date('Y-m-d-H-i-s') . '.pdf');
        }

        return view('admin.reports.trading_profit_and_loss', [
            'model' => $this->model,
            'title' => $this->title,
            'breadcum' => [
                $this->title => ''
            ],
            'sales' => abs($sales->amount ?? 0),
            'openingStock' => $openingStock->amount,
            'grns_total' => $grns->exc_amount,
            'transfers_in_total' => $transfersIn->exc_amount,
            'transfers_out_total' => abs($transfersOut->exc_amount),
            'returns_total' => abs($returns->exc_amount),
            'closingStock' => $closingStock->exc_amount,
            'expenses' => $expenses,
            'locations' => WaLocationAndStore::all()
        ]);
    }

    public function download()
    {
        if (!can('view', $this->model)) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $from = request()->filled('from') ? request()->from . ' 00:00:00' : now()->startOfMonth()->format('Y-m-d 00:00:00');
        $to = request()->filled('to') ? request()->to . ' 23:59:59' : now()->endOfMonth()->format('Y-m-d 23:59:59');

        if (request()->transactions == 'grns') {
            $moves = WaStockMove::query()
                ->select([
                    'wa_stock_moves.created_at',
                    'document_no',
                    'items.stock_id_code',
                    'items.title',
                    'qauntity',
                    'tax_managers.tax_value',
                    'wa_stock_moves.standard_cost',
                    DB::raw("((100 / (100 + tax_managers.tax_value)) * wa_stock_moves.standard_cost) as cost_exc"),
                    DB::raw('ABS(qauntity * wa_stock_moves.standard_cost) as amount')
                ])
                ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
                ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
                ->where('document_no', 'LIKE', 'GRN%')
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->when(request()->filled('branch'), function ($query) {
                    $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
                })
                ->get()
                ->map(function ($record) {
                    $record->created_at = Carbon::parse($record->created_at)->format('Y-m-d H:i:s');
                    $record->amount_exc = round(abs($record->cost_exc * $record->qauntity), 2);
                    $record->standard_cost = manageAmountFormat($record->standard_cost);
                    $record->cost_exc = manageAmountFormat($record->cost_exc);
                    $record->amount = manageAmountFormat($record->amount);
                    $record->amount_exc = manageAmountFormat($record->amount_exc);

                    return $record;
                });

            return ExcelDownloadService::download('grns_' . date('YmdHis'), $moves, [
                'date', 'document_no', 'stock_id_code', 'item_name', 'quantity', 'vat_rate', 'cost', 'cost_exc', 'amount', 'amount_exc'
            ]);
        }

        if (request()->transactions == 'transfers-in') {
            $transfersIn = WaStockMove::query()
                ->select([
                    'wa_stock_moves.created_at',
                    'document_no',
                    'items.stock_id_code',
                    'items.title',
                    'qauntity',
                    'tax_managers.tax_value',
                    'wa_stock_moves.standard_cost',
                    DB::raw("((100 / (100 + tax_managers.tax_value)) * wa_stock_moves.standard_cost) as cost_exc"),
                    DB::raw('ABS(qauntity * wa_stock_moves.standard_cost) as amount')
                ])
                ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
                ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
                ->where(function ($query) {
                    $query->where('document_no', 'LIKE', 'MAR%')
                        ->orWhere('document_no', 'LIKE', 'TRA%');
                })
                ->where('qauntity', '>', 0)
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->when(request()->filled('branch'), function ($query) {
                    $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
                })
                ->get()->map(function ($record) {
                    $record->created_at = Carbon::parse($record->created_at)->format('Y-m-d H:i:s');
                    $record->amount_exc = round(abs($record->cost_exc * $record->qauntity), 2);
                    $record->standard_cost = manageAmountFormat($record->standard_cost);
                    $record->cost_exc = manageAmountFormat($record->cost_exc);
                    $record->amount = manageAmountFormat($record->amount);
                    $record->amount_exc = manageAmountFormat($record->amount_exc);

                    return $record;
                });

            return ExcelDownloadService::download('transfers_in_' . date('YmdHis'), $transfersIn, [
                'date', 'document_no', 'stock_id_code', 'item_name', 'quantity', 'vat_rate', 'cost', 'cost_exc', 'amount', 'amount_exc'
            ]);
        }

        if (request()->transactions == 'transfers-out') {
            $transfersOut = WaStockMove::query()
                ->select([
                    'wa_stock_moves.created_at',
                    'document_no',
                    'items.stock_id_code',
                    'items.title',
                    'qauntity',
                    'tax_managers.tax_value',
                    'wa_stock_moves.standard_cost',
                    DB::raw("((100 / (100 + tax_managers.tax_value)) * wa_stock_moves.standard_cost) as cost_exc"),
                    DB::raw('ABS(qauntity * wa_stock_moves.standard_cost) as amount')
                ])
                ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
                ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
                ->where(function ($query) {
                    $query->where('document_no', 'LIKE', 'MAR%')
                        ->orWhere('document_no', 'LIKE', 'TRA%');
                })
                ->where('qauntity', '<', 0)
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->when(request()->filled('branch'), function ($query) {
                    $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
                })
                ->get()->map(function ($record) {
                    $record->created_at = Carbon::parse($record->created_at)->format('Y-m-d H:i:s');
                    $record->amount_exc = round(abs($record->cost_exc * $record->qauntity), 2);
                    $record->standard_cost = manageAmountFormat($record->standard_cost);
                    $record->cost_exc = manageAmountFormat($record->cost_exc);
                    $record->amount = manageAmountFormat($record->amount);
                    $record->amount_exc = manageAmountFormat($record->amount_exc);

                    return $record;
                });

            return ExcelDownloadService::download('transfers_out_' . date('YmdHis'), $transfersOut, [
                'date', 'document_no', 'stock_id_code', 'item_name', 'quantity', 'vat_rate', 'cost', 'cost_exc', 'amount', 'amount_exc'
            ]);
        }

        if (request()->transactions == 'returns') {
            $returns = WaStockMove::query()
                ->select([
                    'wa_stock_moves.created_at',
                    'document_no',
                    'items.stock_id_code',
                    'items.title',
                    'qauntity',
                    'tax_managers.tax_value',
                    'wa_stock_moves.standard_cost',
                    DB::raw("((100 / (100 + tax_managers.tax_value)) * wa_stock_moves.standard_cost) as cost_exc"),
                    DB::raw('ABS(qauntity * wa_stock_moves.standard_cost) as amount')
                ])
                ->join('wa_inventory_items as items', 'items.id', '=', 'wa_inventory_item_id')
                ->join('tax_managers', 'items.tax_manager_id', '=', 'tax_managers.id')
                ->where('document_no', 'LIKE', 'RFS%')
                ->whereBetween('wa_stock_moves.created_at', [$from, $to])
                ->when(request()->filled('branch'), function ($query) {
                    $query->where('wa_stock_moves.wa_location_and_store_id', request()->branch);
                })->get()->map(function ($record) {
                    $record->created_at = Carbon::parse($record->created_at)->format('Y-m-d H:i:s');
                    $record->amount_exc = round(abs($record->cost_exc * $record->qauntity), 2);
                    $record->standard_cost = manageAmountFormat($record->standard_cost);
                    $record->cost_exc = manageAmountFormat($record->cost_exc);
                    $record->amount = manageAmountFormat($record->amount);
                    $record->amount_exc = manageAmountFormat($record->amount_exc);

                    return $record;
                });

            return ExcelDownloadService::download('returns_' . date('YmdHis'), $returns, [
                'date', 'document_no', 'stock_id_code', 'item_name', 'quantity', 'vat_rate', 'cost', 'cost_exc', 'amount', 'amount_exc'
            ]);
        }
    }
}
