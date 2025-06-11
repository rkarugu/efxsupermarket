<?php

namespace App\Http\Controllers\Admin\Finance;

use Auth;
use App\Enums\Status\PaymentVerification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Model\WaNumerSeriesCode;
use App\Model\WaDebtorTran;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Exports\Finance\DebtorTransactionsExport;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\PaymentMethod;

class DebtorTransController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!can('debtor-trans', 'reconciliation')) {
            Session::flash(pageRestrictedMessage());
            return redirect()->back();
        }
        $my_permissions = $this->mypermissionsforAModule();
        $title = 'Debtor Transactions';
        $model= 'debtor-trans';
        // $channels = DB::table('wa_debtor_trans')->where('channel','!=',NULL)->select('channel')->distinct()->get()->pluck('channel');
        $channels = PaymentMethod::where([
            ['use_for_receipts',1],
            ['use_as_channel',1]
            ])->get();

        $branches = DB::table('restaurants')->select('id','name')->get();

        $breadcum = [
            'General Ledger' => '',
            $title => ''
        ];
        

        $number_series= WaNumerSeriesCode::get();
        return view('admin.Finance.debtor_trans.list', compact('number_series','title','model','breadcum','channels','branches', 'my_permissions'));
    }

    
    public function datatable()
    {
        $transactionTypes = WaNumerSeriesCode::whereIn('id',[12])->get()->pluck('id');
 
        $debtors = DB::table('wa_debtor_trans')  //WaDebtorTran::query()
                    ->whereIn('wa_debtor_trans.type_number',$transactionTypes)
                    ->where('wa_debtor_trans.channel','!=',NULL)
                    ->where(function($q) {
                        $q->where('wa_debtor_trans.document_no', 'like', 'RCT%')
                        ->orWhere('wa_debtor_trans.document_no', 'like', 'CS%');  
                    })
                    ->select([
                        'wa_debtor_trans.*',
                        'wa_customers.customer_name',
                        'restaurants.name as branch_name'
                    ])
                    ->join('restaurants','restaurants.id','wa_debtor_trans.branch_id')
                    ->join('wa_customers','wa_customers.id','wa_debtor_trans.wa_customer_id');     
                     
                    if (request()->status != 'all') {
                        if (request()->status == 'Duplicate') {
                            $debtors->whereIn(
                                DB::raw('(amount, reference)'),
                                function($query) {
                                    $query->select('amount', 'reference')
                                          ->from('wa_debtor_trans')
                                          ->groupBy('amount', 'reference')
                                          ->havingRaw('COUNT(*) > 1');
                                }
                            );
                            $debtors->orderBy('reference','desc');
                        } else {
                            $debtors->where('verification_status', request()->status);
                        }
                    }

        if(request()->channel != 'all'){
            $debtors->where('wa_debtor_trans.channel', request()->channel);
        }
        if(request()->branch != 'all'){
            $debtors->where('restaurants.id', request()->branch);
        }
        if(request()->route != 'all'){
            $debtors->where('wa_customers.id', request()->route);
        }
        if(request()->start_date && request()->end_date){
            $debtors->whereBetween('trans_date',[request()->start_date.' 00:00:00',request()->end_date.' 23:59:59']);
        }
        if (request()->filled('type')) {
            if (request()->type == 'pdf') {
                $debtors = $debtors->get();
                $pdf = \PDF::loadView('admin.Finance.debtor_trans.pdf', compact('debtors'));
                $report_name = 'debtor-transactions' . date('Y_m_d_H_i_A');
                // return $pdf->stream();
                return $pdf->download($report_name . '.pdf');
            }
            if (request()->type == 'excel') {
                $customerPayments = $debtors->get()->map(function ($debtor) {
                return [
                    'trans_date' => Carbon::parse($debtor->trans_date)->format('Y-m-d'),
                    'document_no' => $debtor->document_no,
                    'channel' => $debtor->channel,
                    'branch' => $debtor->branch_name,
                    'route' => $debtor->customer_name,
                    'reference' => $debtor->reference ?? '-',
                    'verification' => $debtor->verification_status,
                    'amount' => number_format(abs($debtor->amount)),
                ];
            });


        $export = new DebtorTransactionsExport(collect($customerPayments));
        return Excel::download($export, 'System Transations.xlsx');
            }
        }

        return DataTables::of($debtors)
            ->editColumn('amount', function ($amount) {
                return manageAmountFormat(abs($amount->amount));
            })
            ->editColumn('trans_date', function ($date) {
                return date('Y-m-d',strtotime($date->trans_date));
            })
            ->editColumn('verification_status', function ($debtor){
                return ucfirst($debtor->verification_status);
            })
            ->editColumn('branch_name', function ($branch) {
                if ($branch->branch_name) {
                    return $branch->branch_name;
                }
                return '-';
            })
            ->with('total_amount', function () use ($debtors) {
                return manageAmountFormat(abs($debtors->get()->sum('amount')));
            })
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
