<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Model\Route;
use App\Model\User;
use App\Model\WaDebtorTran;
use App\SalesmanShift;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SalesmanShiftReport extends Controller
{
    protected $model;
    protected $base_route;
    protected $resource_folder;
    protected $base_title;
    protected $permissions_module;
    protected $pmodule;


    public function __construct()
    {
        $this->model = 'salesman-shifts-report';
        $this->base_route = 'route-performance-report';
        $this->resource_folder = 'admin.salesreceiablesreports';
        $this->base_title = 'Summary Customer Statement';
        $this->permissions_module = 'sales-and-receivables-reports';
        $this->pmodule = 'sales-and-receivables-reports';
    }
    public function index(Request $request)
    {

        $title = 'Summary Customer Statement';
        $model = $this->model;
        $permission = $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;

        $start = Carbon::parse($request->from) ?? now()->startOfDay()->format('Y-m-d');
        $end = Carbon::parse($request->to) ?? now();
        $sales_people =  Route::pluck('route_name', 'id');
        $sales_man_id = $request->sales_man;
        $show = true;

        if ($permission == 'superadmin' || isset($permission[$pmodule . '___salesman_shift_report'])) {
            if (!$sales_man_id){
                $show = false;
                return view("admin.salesreceiablesreports.salesman_shift_report", compact('show','title','model','sales_people'));
            }

            $route = Route::find($sales_man_id);
            $shifts = SalesmanShift::where('route_id', $sales_man_id)
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('start_time', [$start, $end])
                        ->orWhereBetween('closed_time', [$start, $end]);
                })
                ->with(['salesman_route', 'salesman_route.WaCustomer', 'shiftCustomers'])
                ->get();

            foreach ($shifts as $shift) {
                $shift->start_time = $shift->start_time ?? Carbon::parse($shift->closed_time)->startOfDay();
                $shift->formatted_start_date = Carbon::parse($shift->start_time)->format('Y-m-d');
            }

            $initialOpeningBalance = 0;

            $customerCodes = $shifts->map(function ($record) {
                return $record->salesman_route->WaCustomer->customer_code;
            })->unique();

            $allDebtorTrans = WaDebtorTran::whereIn('customer_number', $customerCodes)
                ->whereBetween('trans_date', [$start, $end])
                ->orderBy('trans_date')
                ->get();

            $allReceipts  = $allDebtorTrans->filter(function ($item) {
                return str_starts_with($item['document_no'], 'RCT-');
            });

            $returns = $allDebtorTrans->filter(function ($item) {
                return str_starts_with($item['document_no'], 'RTN-');
            });

            $invoices = $allDebtorTrans->filter(function ($item) {
                return str_starts_with($item['document_no'], 'INV-');
            });

            $data = [];
            foreach ($shifts as $index => $shift)
            {

                $totalShiftReturns = 0;
                foreach ($returns as $return)
                {
                    if ($return->shift_id == $shift->id) {
                        $totalShiftReturns += $return->amount;
                    }
                }


                $startDate = Carbon::parse($shift->start_time)->format('Y-m-d');
                $endDate = $shift->closed_time
                    ? Carbon::parse($shift->closed_time)->format('Y-m-d')
                    : Carbon::now()->format('Y-m-d');

                $nextShiftStartTime = isset($shifts[$index + 1]) ? $shifts[$index + 1]->start_time : date('Y-m-d');

                $startAfterShift = Carbon::parse($endDate)->addDay();

                /*work on receipts*/
                $receiptsOutsideShift = [];
                $receiptsDuringShift= 0;

                $allReceipts = $allReceipts->filter(function ($receipt) use ($startDate, $startAfterShift, $nextShiftStartTime, &$receiptsDuringShift, &$receiptsOutsideShift) {
                    if ($receipt->trans_date->format('Y-m-d') === $startDate) {
                        $receiptsDuringShift += $receipt->amount;
                        return false;
                    }
                    if ($receipt->trans_date > $startAfterShift->toDateString() && $receipt->trans_date <= Carbon::parse($nextShiftStartTime)->subDay()) {
                        $receiptsOutsideShift[] = [
                            'trans_date' => $receipt->trans_date,
                            'customer_code' => $receipt->customer_number,
                            'document_no' => $receipt->document_no,
                            'reference' => $receipt->reference,
                            'amount' => $receipt->amount,
                        ];
                        return false;
                    }
                    return true;
                });


                $receiptsOutsideShiftTotal  = collect($receiptsOutsideShift)->sum('amount');

                /*work on Invoices*/
                $totalInvoices = 0;
                $totalDiscounts = 0;

                if (!isset($processedDates[$startDate])) {
                    $processedDates[$startDate] = true;

                    $invoices->reject(function ($invoice) use (&$totalDiscounts, &$totalInvoices, $startDate) {
                        if ($invoice->trans_date->toDateString() === $startDate) {
                            if ($invoice->amount < 0) {
                                $totalDiscounts += $invoice->amount;
                            }
                            $totalInvoices += $invoice->amount;
                            return false;
                        }
                        return true;
                    });
                } else {
                    $totalInvoices = 0;
                    $totalDiscounts = 0;
                }

                $data[] = [
                    'id' => $shift->id,
                    'shift_id' => $shift->shift_id,
                    'start_date' => $shift->start_time,
                    'end_date' => $shift->closed_time,
                    'invoices' => $totalInvoices,
                    'returns' => $totalShiftReturns,
                    'discounts' => $totalDiscounts,
                    'receipts' => $receiptsDuringShift + $receiptsOutsideShiftTotal ,
                    'receipts_outside' => $receiptsOutsideShift,
                    'receipts_outside_total' => $receiptsOutsideShiftTotal,
                    'receipts_during_shift' => $receiptsDuringShift,
                ];
            }


            $grandTotalInvoices = 0;
            $grandTotalReturns =0;
            $grandTotalReceipts = 0;

            $grandTotalDiscounts = 0;
            $grandTotalReceiptsDuring = 0;
            $grandTotalReceiptsAfter = 0;
            $openingBalance = $initialOpeningBalance;
            $mappedInvoices = 0;
            $mappedReceipts = 0;
            $mappedReturns = 0;

            $shiftsData = [];
            foreach ($data as $index => $shift) {
                $shift['opening_balance'] = $openingBalance;

                $totalInvoices = $shift['invoices'];
                $totalReturns = $shift['returns'];
                $totalDiscounts = $shift['discounts'];
                $totalReceipts = $shift['receipts'];
                $TotalReceiptsDuring = $shift['receipts_during_shift'];
                $TotalReceiptsAfter = $shift['receipts_outside_total'];

                $credits = $totalInvoices + $openingBalance;
                $debits = $totalReturns + $totalReceipts;
                $closingBalance =$credits + $debits;
                $shift['closing_balance'] = $closingBalance;

                // Update grand totals

                $grandTotalDiscounts += $totalDiscounts;
                $grandTotalReceiptsDuring += $TotalReceiptsDuring;
                $grandTotalReceiptsAfter += $TotalReceiptsAfter;
                $mappedInvoices += $totalInvoices;
                $mappedReceipts += $totalReceipts;
                $mappedReturns += $totalReturns;

                $grandTotalInvoices  += $totalInvoices;
                $grandTotalReturns += $totalReturns;
                $grandTotalReceipts  += $totalReceipts;


                $openingBalance = $closingBalance;

                $shiftsData[] = $shift;
            }

            if ($request->get('manage-request') == "print") {
                $sales_man = $sales_people->firstWhere('id',$sales_man_id);
                return view('admin.salesreceiablesreports.salesman_shift_report_pdf', compact('route','grandTotalReceiptsAfter','grandTotalReceiptsDuring','sales_man','shiftsData', 'openingBalance','grandTotalInvoices', 'grandTotalReturns', 'grandTotalDiscounts', 'grandTotalReceipts', 'openingBalance',));
            }
            if ($request->get('manage-request') == "pdf") {
                $sales_man = $sales_people->firstWhere('id',$sales_man_id);
                $pdf =PDF::loadView('admin.salesreceiablesreports.salesman_shift_report_pdf', compact('mappedReturns','mappedReceipts','mappedInvoices','route','grandTotalReceiptsAfter','grandTotalReceiptsDuring','sales_man','shiftsData', 'openingBalance','grandTotalInvoices', 'grandTotalReturns', 'grandTotalDiscounts', 'grandTotalReceipts', 'openingBalance',));
                return $pdf->download('customer_sales_summary_report' . date('Y_m_d_h_i_s') . '.pdf');
            }

            return view("admin.salesreceiablesreports.salesman_shift_report", compact('mappedReturns','mappedReceipts','mappedInvoices','grandTotalReceiptsAfter','grandTotalReceiptsDuring','show','shiftsData', 'openingBalance','grandTotalInvoices', 'grandTotalReturns', 'grandTotalDiscounts', 'grandTotalReceipts', 'openingBalance','title','model','sales_people'));

        }

        Session::flash('warning', 'Invalid Request');

        return redirect()->back();
    }

}
