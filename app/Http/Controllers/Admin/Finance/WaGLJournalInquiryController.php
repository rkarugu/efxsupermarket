<?php

namespace App\Http\Controllers\Admin\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WaGlTran;
use App\Model\WaNumerSeriesCode;
use App\Exports\Finance\GLJournalInquiryExport;
use Excel;
class WaGLJournalInquiryController extends Controller
{
    protected $model = 'journal-inquiry';
    protected $title = 'Journal Inquiry';
    protected $pmodule = 'journal-inquiry';

    public function index(Request $request)
    {
        if(!$this->modulePermissions('view')){
            return redirect()->back();
        }
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['number_series'] = WaNumerSeriesCode::get();
        // $data['data'] = WaGlTran::with(['relatedItems'])->where('wa_journal_entrie_id','!=',NULL)->groupBy('wa_journal_entrie_id')->get();
        return view('admin.Finance.wa_gl_journal_inquiry.index')->with($data);
    }
    public function modulePermissions($type)
    {
        $permission =  $this->mypermissionsforAModule();
        if(!isset($permission[$this->pmodule.'___'.$type]) && $permission != 'superadmin')
        {
            \Session::flash('warning', 'Invalid Request');
            return false; 
        }
        return true;
    }
    public function search(Request $request)
    {
        if(!$this->modulePermissions('view')){
            return redirect()->back();
        }
        $data['title'] = $this->title;
        $data['model'] = $this->model;
        $data['pmodule'] = $this->pmodule;
        $data['number_series'] = WaNumerSeriesCode::get();
        $gl = WaGlTran::with(['restaurant','relatedItems'])->where('transaction_no','!=',NULL)->groupBy('transaction_no');
        if($request->account)
        {
            $gl = $gl->where('grn_type_number',$request->account);
        }
        if($request->get('start-date') && $request->get('end-date'))
        {
            $date1 = $request->get('start-date').' 00:00:00';
            $date2 = $request->get('end-date').' 23:59:59';       
            $gl = $gl->whereDate('trans_date', '>=', $date1)->whereDate('trans_date', '<=', $date2);
        }
        $data['data'] = $gl->get();

        if($request->manage && $request->manage == 'export')
        {
            $account_codes =  getChartOfAccountsList();
            $positiveAMount = 0;
            $negativeAMount = 0;
            $arrays = [];
            foreach ($data['data'] as $row) {
                if($row->transaction_type=="Sales Invoice" && $row->amount > 0){
                    $accountno = explode(':',$row->narrative);
                    $narrative = (count($accountno)> 1 ) ? $accountno[0] : '---';
                } else {
                    $accountno = explode('/',$row->narrative);
                    $narrative = (count($accountno)> 1 ) ? $accountno[1] : '---';
                }
                $payload = [
                    'date' => getDateFormatted($row->trans_date),
                    'transaction_type' => $row->transaction_type,
                    'transaction_no' => $row->transaction_no,
                    'account' => $row->account,
                    'account_description' => isset($account_codes[$row->account]) ? $account_codes[$row->account] : '',
                    'narrative' => $narrative,
                    'reference' => $row->reference,
                    'tag' => (isset($row->restaurant->branch_code)) ? $row->restaurant->branch_code : '----',
                    'debit' => $row->amount>='0'?manageAmountFormat($row->amount):'',
                    'credit' => $row->amount<='0'?manageAmountFormat($row->amount):'',
                ];
                if($row->amount>='0'){
                    $positiveAMount = $positiveAMount + $row->amount;
                }else {
                    $negativeAMount = $negativeAMount + $row->amount;
                }
                $arrays[] = $payload;
                foreach ($row->relatedItems->where('id','!=',$row->id) as $item) {
                    if($item->transaction_type=="Sales Invoice" && $row->amount > 0){
                        $accountno = explode(':',$item->narrative);
                        $narrative2 = (count($accountno)> 1 ) ? $accountno[0] : '---';
                    } else {
                        $accountno = explode('/',$item->narrative);
                         $narrative2 = (count($accountno)> 1 ) ? $accountno[1] : '---';
                    }
                    $payload = [
                        'date' => '',
                        'transaction_type' => '',
                        'transaction_no' => '',
                        'account' => $item->account,
                        'account_description' => isset($account_codes[$item->account]) ? $account_codes[$item->account] : '',
                        'narrative' => $narrative2,
                        'reference' => $row->reference,
                        'tag' => (isset($item->restaurant->branch_code)) ? $item->restaurant->branch_code : '----',
                        'debit' => $item->amount>='0'?manageAmountFormat($item->amount):'',
                        'credit' => $item->amount<='0'?manageAmountFormat($item->amount):'',
                    ];
                    if($item->amount>='0'){
                        $positiveAMount = $positiveAMount + $item->amount;
                    }else {
                        $negativeAMount = $negativeAMount + $item->amount;
                    }
                    $arrays[] = $payload;
                }
            }
            $arrays[] = [
                'date'=>'',
                'transaction_type'=>'',
                'transaction_no'=>'',
                'account'=>'',
                'account_description'=>'',
                'narrative'=>'',
                'reference'=>'',
                'tag'=>'Total',
                'debit'=>manageAmountFormat($positiveAMount),
                'credit'=>manageAmountFormat($negativeAMount),
            ];
            
            $export = new GLJournalInquiryExport(collect($arrays));
            return Excel::download($export, 'gl-journal-inquiry-'.date('Y-m-d-H-i-s').'.xlsx');     
        }
        $view = view('admin.Finance.wa_gl_journal_inquiry.excel')->with($data)->render();
        return response()->json($view);
    }
}
