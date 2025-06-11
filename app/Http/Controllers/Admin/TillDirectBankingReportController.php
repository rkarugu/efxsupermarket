<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TenderEntryExport;
use App\Http\Controllers\Controller;
use App\Model\PaymentMethod;
use App\Model\WaChartsOfAccount;
use App\WaTenderEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TillDirectBankingReportController extends Controller
{
    protected $model;
    protected $title;
    protected $pmodule;

    public function __construct()
    {
        $this->model = 'till-direct-banking-report';
        $this->title = 'Till Direct Banking Report';
        $this->pmodule = 'till-direct-banking-report';
    }

    public function index()
    {
        if (!can($this->model, 'sales-and-receivables-reports')) {
            return redirect()->back()->withErrors(['errors' => pageRestrictedMessage()]);
        }

        $breadcum = [
            'Maintain Suppliers' => route('maintain-suppliers.index'),
            $this->title => route("sales-and-receivables-reports.$this->model")
        ];

        $accounts = WaChartsOfAccount::get();

        $query = WaTenderEntry::query()
            ->with([
                'cashier',
                'customer'
            ]);

        $channel = '';

        $startDate = request()->filled('to') ? request()->from . ' 00:00:00' : now()->format('Y-m-d 00:00:00');
        $endDate = request()->filled('from') ? request()->to . ' 23:59:59' : now()->format('Y-m-d 23:59:59');
        $dates = "$startDate - $endDate";
        $query->whereBetween('trans_date', [$startDate, $endDate]);

        if (request()->filled('channel')) {
            $channel .= request()->channel;
            $query->where('channel', 'LIKE', $channel . '%');
        }

        if (request()->filled('branch')) {
            $query->where('branch_id', request()->branch);
        }

        if (request()->filled('action')  && request()->action == 'excel') {
            $export = new TenderEntryExport($query->get());

            return Excel::download($export, "tender_entry_report" . date('Y_m_d_H_i_s') . ".xlsx");
        }

        if (request()->filled('action')  && request()->action == 'pdf') {
            $data = $query->get()->map(function($data){
                $reference = $data->reference;
                if (str_contains($reference, 'Transfer')) {
                    $explodedRef = explode('~',$data->reference);
                    $reference = $explodedRef[1];
                    if (strlen($reference) < 5 ) {
                        $explodedRef = explode(' ',$data->reference);
                        $reference = end($explodedRef);
                    }
                }
                if (str_contains($reference, 'Cr From')) {
                    $explodedRef = explode('@',$data->reference);
                    $explodedRef2 = explode(' ',$explodedRef[0]);
                    $reference = end($explodedRef2);
                    if (strlen($reference) < 5 ) {
                        $explodedRef = explode(' ',$data->reference);
                        $reference = end($explodedRef);
                    }
                }

                $additionalInfo = $data->reference == $data->additional_info ? $reference : ($data->additional_info !='null'? $data->additional_info : ' ');

                return [
                    'trans_date' => $data->trans_date,
                    'channel' => $data->channel,
                    'customer_name' => $data->customer->customer_name,
                    'reference' => $reference,
                    'additional_info' => $additionalInfo,
                    'amount' => $data->amount
                ];
            });
            $pdf = Pdf::loadview('admin.reports.print.till_direct_banking_report', [
                'transactions' => $data,
                'channel' => $channel,
                'dates' => $dates,
            ])->setOption('isPhpEnabled', true)
            ->setPaper('A4', 'portrait');
            return $pdf->download('tender_entries_report' . date('Y-m-d-H-i-s') . '.pdf');
        }

        if (request()->wantsJson()) {
            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('trans_date', function ($transaction) {
                    return $transaction->trans_date->format('Y-m-d H:i:s');
                })
                ->editColumn('amount', function ($transaction) {
                    return manageAmountFormat($transaction->amount);
                })
                ->with('total', function () use ($query) {
                    return $query->sum('amount');
                })
                ->toJson();
        }

        $channels = PaymentMethod::where([
            ['use_for_receipts',1],
            ['use_as_channel',1]
            ])->get();
        return view('admin.reports.till_direct_banking_report', [
            'title' => $this->title,
            'model' => $this->model,
            'pmodule' => $this->pmodule,
            'breadcum' => $breadcum,
            'accounts' => $accounts,
            'channels' => $channels
        ]);
    }
}
