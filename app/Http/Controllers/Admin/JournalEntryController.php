<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Advertisement;
use DB;
use App\Model\WaJournalEntry;
use App\Model\WaJournalEntrieItem;
use Session;
use App\Model\WaGlTran;
use App\Model\WaBanktran;
use App\Model\WaDebtorTran;
use App\Model\WaSuppTran;
use App\Model\WaCompanyPreference;
use App\Model\WaAccountingPeriod;
use App\Model\WaNumerSeriesCode;



class JournalEntryController extends Controller
{

    protected $model;
    protected $title;
    protected $pmodule;
    public function __construct()
    {
        $this->model = 'journal-entries';
        $this->title = 'Journal Entry';
        $this->pmodule = 'journal-entries';
    }

    public function index()
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (isset($permission[$pmodule . '___view']) || $permission == 'superadmin') {
            $lists = WaJournalEntry::orderBy('id', 'desc')->where('status', 'pending')->get();
            $breadcum = [$title => route($model . '.index'), 'Listing' => ''];
            return view('admin.journalentries.index', compact('title', 'lists', 'model', 'breadcum', 'pmodule', 'permission'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function processed_index(Request $request)
    {
        $permission =  $this->mypermissionsforAModule();
        $pmodule = $this->pmodule;
        $title = $this->title;
        $model = $this->model;
        if (!isset($permission[$this->pmodule . '___processed']) && $permission != 'superadmin') {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
        $breadcum = [$title => route($model . '.processed_index'), 'Listing' => ''];

        $grns = [];
        if ($request->ajax()) {
            // print_r("expression"); die;
            if ($request->filter == 'filter') {
                $grns = WaGlTran::select([
                    'trans_date', 'grn_type_number',
                    'transaction_no',
                    'user_id',
                    'account',
                    'narrative',
                    'reference',
                ])->with(['user', 'getAccountDetail'])->where('grn_type_number', 11)->where(function ($w) use ($request) {
                    if ($request->from) {
                        $w->whereBetween('trans_date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
                    } else {
                        $w->whereBetween('trans_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59']);
                    }
                })->groupBy('transaction_no')->orderBy('trans_date', 'DESC')->get();
                $sumgrns = WaGlTran::where('grn_type_number', 11)->where(function ($w) use ($request) {
                    if ($request->from) {
                        $w->whereBetween('trans_date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
                    } else {
                        $w->whereBetween('trans_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59']);
                    }
                })->orderBy('trans_date', 'DESC')->get();
                foreach ($grns as $key => $value) {
                    $grns[$key]->date = date('d/m/Y', strtotime($value->trans_date));
                    $grns[$key]->name = @$value->getAccountDetail->account_name;
                    $grns[$key]->credit = manageAmountFormat(@$sumgrns->where('transaction_no', $value->transaction_no)->where('amount', '<=', 0)->sum('amount'));
                    $grns[$key]->debit = manageAmountFormat(@$sumgrns->where('transaction_no', $value->transaction_no)->where('amount', '>', 0)->sum('amount'));
                    $grns[$key]->posted_by = @$value->user->name ?? '';
                    $grns[$key]->reference = ($value->reference != NULL ? $value->reference : '');
                    $url = 'printMe("' . route('journal-entries.processed_index', ['transaction_no' => $value->transaction_no, 'filter' => 'print']) . '"); return false;';
                    $grns[$key]->action = "<a onclick='" . $url . "' href='#' class='btn btn-sm btn-biz-pinkish'>Print</a>";
                }
                return response()->json($grns);
            }
            if ($request->filter == 'print') {
                $grns = WaGlTran::with(['user', 'getAccountDetail'])->where('grn_type_number', 11)->where('transaction_no', $request->transaction_no)
                    ->orderBy('trans_date', 'DESC')->get();
                foreach ($grns as $key => $value) {
                    $grns[$key]->date = date('d/m/Y', strtotime($value->trans_date));
                    $grns[$key]->name = @$value->getAccountDetail->account_name;
                    $grns[$key]->credit = ($value->amount <= 0 ? manageAmountFormat($value->amount) : '--');
                    $grns[$key]->debit = ($value->amount > 0 ? manageAmountFormat($value->amount) : '--');
                    $grns[$key]->reference = ($value->reference != NULL ? $value->reference : '');
                }
                return view('admin.journalentries.processed_print', compact('grns'));
            }
        }

        $grns = WaGlTran::select([
            'trans_date', 'grn_type_number',
            'transaction_no',
            'user_id',
            'account',
            'narrative',
            'reference',
        ])->with(['user', 'getAccountDetail'])->where('grn_type_number', 11)->where(function ($w) use ($request) {
            if ($request->from) {
                $w->whereBetween('trans_date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            } else {
                $w->whereBetween('trans_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59']);
            }
        })->groupBy('transaction_no')->orderBy('trans_date', 'DESC')->get();

        //     $grns = WaGlTran::select([
        //     \DB::raw('MAX(trans_date) as trans_date'),
        //     'grn_type_number',
        //     'transaction_no',
        //     'user_id',
        //     'account',
        //     \DB::raw('MAX(narrative) as narrative'),
        //     \DB::raw('MAX(reference) as reference'),
        // ])
        // ->with(['user', 'getAccountDetail'])
        // ->where('grn_type_number', 11)
        // ->when($request->from, function ($query) use ($request) {
        //     $from = $request->from . ' 00:00:00';
        //     $to = $request->to . ' 23:59:59';
        //     return $query->whereBetween('trans_date', [$from, $to]);
        // }, function ($query) {
        //     $today = date('Y-m-d');
        //     return $query->whereBetween('trans_date', [$today . ' 00:00:00', $today . ' 23:59:59']);
        // })
        // ->groupBy('transaction_no', 'grn_type_number', 'user_id', 'account')
        // ->orderByDesc('trans_date')
        // ->get();
        $sumgrns = WaGlTran::where('grn_type_number', 11)->where(function ($w) use ($request) {
            if ($request->from) {
                $w->whereBetween('trans_date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            } else {
                $w->whereBetween('trans_date', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59']);
            }
        })->orderBy('trans_date', 'DESC')->get();
        foreach ($grns as $key => $value) {
            $grns[$key]->date = date('d/m/Y', strtotime($value->trans_date));
            $grns[$key]->name = @$value->getAccountDetail->account_name;
            $grns[$key]->credit = manageAmountFormat(@$sumgrns->where('transaction_no', $value->transaction_no)->where('amount', '<=', 0)->sum('amount'));
            $grns[$key]->debit = manageAmountFormat(@$sumgrns->where('transaction_no', $value->transaction_no)->where('amount', '>', 0)->sum('amount'));
            $grns[$key]->posted_by = @$value->user->name;
            $grns[$key]->reference = ($value->reference != NULL ? $value->reference : '');
            $url = 'printMe("' . route('journal-entries.processed_index', ['transaction_no' => $value->transaction_no, 'filter' => 'print']) . '"); return false;';
            $grns[$key]->action = "<a onclick='" . $url . "' href='#' class='btn btn-sm btn-biz-pinkish'>Print</a>";
        }
        return view('admin.journalentries.processed_index', compact('title', 'grns', 'model', 'breadcum', 'pmodule', 'permission'));
    }

    public function create()
    {
        $permission =  $this->mypermissionsforAModule();
        if (isset($permission[$this->pmodule . '___add']) || $permission == 'superadmin') {
            $title = 'Add ' . $this->title;
            $model = $this->model;
            $restroList = $this->getRestaurantList();

            $breadcum = [$this->title => route($model . '.index'), 'Add' => ''];
            return view('admin.journalentries.create', compact('title', 'restroList', 'model', 'breadcum'));
        } else {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {

        try {

            if ($request->credit > '0' && $request->debit > '0') {
                if ($request->credit < $request->debit) {
                    $request->debit = $request->debit - $request->credit;
                    $request->credit = '0';
                } else {
                    $request->credit = $request->credit - $request->debit;
                    $request->debit = '0';
                }
            }



            if ($request->credit == '0' && $request->debit == '0') {
                Session::flash('warning', "Invalid Amount");
                return redirect()->back()->withInput();
            } else {

                $logged_user_info = getLoggeduserProfile();
                $row = new WaJournalEntry();
                $row->journal_entry_no = $request->journal_entry_no;
                $row->user_id = $logged_user_info->id;
                $row->date_to_process = $request->date_to_process;
                $row->entry_type  = $request->entry_type;
                $row->save();

                $item                      = new WaJournalEntrieItem();
                $item->wa_journal_entry_id = $row->id;
                $item->gl_account_id       = $request->gl_account_id;
                $item->restaurant_id       = $request->restaurant;
                $item->entry_type          = $request->entry_type;
                $item->credit              = $request->credit ? $request->credit : '0';
                $item->debit               = $request->debit ? $request->debit : '0';
                $item->narrative           = $request->narrative;
                $item->reference          = $request->reference;
                $item->save();
                $row->save();
                updateUniqueNumberSeries('JOURNAL_ENTRY', $request->purchase_no);
                Session::flash('success', 'Record added successfully.');
                return redirect()->route($this->model . '.edit', $row->slug);
            }
        } catch (\Exception $e) {

            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }


    public function show($id)
    {
    }
    public function getAccountNo(Request $request)
    {
        $accountno = [];
        if (!empty($request->get('type'))) {
            $type = $request->get('type');
            if ($type == "GL Account") {
                $accountno = getChartOfAccountsDropdown();
            }
            if ($type == "Bank Account") {
                $accountno = getBankAccountDropdownsJonralEntry();
            }
            if ($type == "Customer Account") {
                $accountno = getCustomerDropdowns();
            }
            if ($type == "Supplier Account") {
                $accountno = getSupplierDropdown();
            }
        }
        $data = ['success' => true, 'data' => $accountno];
        return response()->json($data);
    }

    public function process($slug)
    {

        //  echo getCodeWithNumberSeries('CREDITORS_PAYMENT');; die;
        $row =  WaJournalEntry::with(['getRelatedItem.getSuppDetail'])->whereSlug($slug)->where('status', 'pending')->first();
        // dd($row);
        if ($row) {
            if ($row->getRelatedItem) {
                $logged_user_info = getLoggeduserProfile();

                $getRelatedItemArr = $row->getRelatedItem;
                $credit = round($getRelatedItemArr->sum('credit'), 2);
                $debit = round($getRelatedItemArr->sum('debit'), 2);

                if ($credit == $debit) {
                    $accountuingPeriod =  WaAccountingPeriod::where('is_current_period', '1')->first();
                    $series_module = WaNumerSeriesCode::where('module', 'JOURNAL_ENTRY')->first();
                    $companyPreference =  WaCompanyPreference::with(['debtorsControlGlAccount', 'creditorControlGlAccount'])->where('id', '1')->first();

                    $dateTime = $row->date_to_process; //date('Y-m-d H:i:s');
                    foreach ($getRelatedItemArr as $item) {
                        //						echo "<pre>"; print_r($item->getGlDetail->wa_account_group_id==4); die;

                        $san = '';
                        $saname = '';
                        if ($item->entry_type == "Supplier Account") {
                            $san = $item->getSuppDetail->supplier_code;
                            $saname = $item->getSuppDetail->name;
                        }
                        if ($item->credit > '0') {
                            if ($item->entry_type == "Customer Account") {
                                $accountno  = $companyPreference->debtorsControlGlAccount->account_code;
                            } elseif ($item->entry_type == "Supplier Account") {
                                // $companyPreference =  WaCompanyPreference::where('id', '1')->first();
                                $accountno  = $companyPreference->creditorControlGlAccount->account_code;
                            } else {
                                $accountno = $item->getGlDetail->account_code;
                            }
                            $cr = new WaGlTran();
                            $cr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                            $cr->wa_journal_entrie_id = $row->id;
                            $cr->grn_type_number = $series_module->type_number;
                            $cr->trans_date = $dateTime;
                            $cr->restaurant_id = $item->restaurant_id; //getLoggeduserProfile()->restaurant_id;
                            $cr->grn_last_used_number = $series_module->last_number_used;
                            $cr->transaction_type = $series_module->description;
                            $cr->transaction_no = $row->journal_entry_no;
                            $cr->narrative = $item->narrative;
                            $cr->account = $accountno;
                            $cr->supplier_account_number = $saname;
                            $cr->user_id = $logged_user_info->id;
                            $cr->amount = '-' . $item->credit;
                            $cr->reference     = ($item->entry_type == "Supplier Account" ? $san : $item->reference);
                            $cr->save();

                            if (@$item->getGlDetail->wa_account_group_id == 4) {
                                $btran = new WaBanktran();
                                $btran->type_number = $series_module->type_number;
                                $btran->document_no = $row->journal_entry_no;
                                $btran->bank_gl_account_code = $item->getGlDetail->account_code;
                                $btran->reference = $item->narrative;
                                $btran->trans_date = $dateTime;
                                $btran->wa_payment_method_id = 1;
                                $btran->amount = '-' . $item->credit;
                                $btran->wa_curreny_id = 1;
                                $btran->save();
                            }


                            if ($item->entry_type == "Customer Account") {

                                $debtorTran = new WaDebtorTran();
                                $debtorTran->type_number =  $series_module ? $series_module->type_number : '';
                                $debtorTran->wa_customer_id = $item->getCustDetail->id;
                                $debtorTran->customer_number = $item->getCustDetail->customer_code;
                                $debtorTran->trans_date = $dateTime;
                                $debtorTran->input_date = $dateTime;
                                $debtorTran->wa_accounting_period_id = $accountuingPeriod ? $accountuingPeriod->id : null;
                                $debtorTran->amount = '-' . $item->credit;
                                $debtorTran->document_no = $row->journal_entry_no;
                                $debtorTran->reference = $item->narrative;
                                $debtorTran->user_id = $logged_user_info->id;
                                $debtorTran->save();
                            }

                            if ($item->entry_type == "Supplier Account") {

                                $newSupplierTrans = new WaSuppTran();
                                $document_no = $row->journal_entry_no; //getCodeWithNumberSeries('CREDITORS_PAYMENT');
                                $newSupplierTrans->document_no = $document_no;
                                $newSupplierTrans->total_amount_inc_vat  = $item->credit;;
                                $newSupplierTrans->trans_date  = $dateTime;
                                $newSupplierTrans->suppreference  = $item->narrative;
                                $newSupplierTrans->supplier_no  = $item->getSuppDetail->supplier_code;
                                $newSupplierTrans->grn_type_number  = $series_module->type_number;
                                $newSupplierTrans->journel_entry_id  = $item->id;
                                $newSupplierTrans->save();
                            }
                        } else {
                            if ($item->entry_type == "Customer Account") {
                                // $companyPreference =  WaCompanyPreference::where('id', '1')->first();
                                $accountno  = $companyPreference->debtorsControlGlAccount->account_code;
                            } elseif ($item->entry_type == "Supplier Account") {
                                // $companyPreference =  WaCompanyPreference::where('id', '1')->first();
                                $accountno  = $companyPreference->creditorControlGlAccount->account_code;
                            } else {
                                $accountno = $item->getGlDetail->account_code;
                            }
                            //debit
                            $dr = new WaGlTran();
                            $dr->period_number = $accountuingPeriod ? $accountuingPeriod->period_no : null;
                            $dr->wa_journal_entrie_id = $row->id;
                            $dr->grn_type_number = $series_module->type_number;
                            $dr->trans_date = $dateTime;
                            $dr->restaurant_id = $item->restaurant_id; //getLoggeduserProfile()->restaurant_id;
                            $dr->grn_last_used_number = $series_module->last_number_used;
                            $dr->transaction_type = $series_module->description;
                            $dr->transaction_no = $row->journal_entry_no;
                            $dr->narrative = $item->narrative;
                            $dr->account = $accountno;
                            $dr->user_id = $logged_user_info->id;
                            $dr->supplier_account_number = $saname;
                            $dr->amount = $item->debit;
                            $dr->reference     = ($item->entry_type == "Supplier Account" ? $san : $item->reference);
                            $dr->save();

                            if (isset($item->getGlDetail->wa_account_group_id) &&  @$item->getGlDetail->wa_account_group_id == 4) {
                                $btran = new WaBanktran();
                                $btran->type_number = $series_module->type_number;
                                $btran->document_no = $row->journal_entry_no;
                                $btran->bank_gl_account_code = $item->getGlDetail->account_code;
                                $btran->reference = $item->narrative;
                                $btran->trans_date = $dateTime;
                                $btran->wa_payment_method_id = 1;
                                $btran->amount = $item->debit;
                                $btran->wa_curreny_id = 1;
                                $btran->save();
                            }



                            if ($item->entry_type == "Bank Account") {
                                $btran = new WaBanktran();
                                $btran->type_number = $series_module->type_number;
                                $btran->document_no = $row->journal_entry_no;
                                $btran->bank_gl_account_code = $item->getGlDetail->account_code;
                                $btran->reference = $item->narrative;
                                $btran->trans_date = $dateTime;
                                $btran->wa_payment_method_id = 1;
                                $btran->amount = $item->debit;
                                $btran->wa_curreny_id = 1;
                                //echo "<pre>"; print_r($btran); die;

                                $btran->save();
                            }

                            if ($item->entry_type == "Customer Account") {

                                $debtorTran = new WaDebtorTran();
                                $debtorTran->type_number =  $series_module ? $series_module->type_number : '';
                                $debtorTran->wa_customer_id = $item->getCustDetail->id;
                                $debtorTran->customer_number = $item->getCustDetail->customer_code;
                                $debtorTran->trans_date = $dateTime;
                                $debtorTran->input_date = $dateTime;
                                $debtorTran->wa_accounting_period_id = $accountuingPeriod ? $accountuingPeriod->id : null;
                                $debtorTran->amount = $item->debit;
                                $debtorTran->document_no = $row->journal_entry_no;
                                $debtorTran->reference = $item->narrative;
                                $debtorTran->save();
                            }

                            if ($item->entry_type == "Supplier Account") {

                                $newSupplierTrans = new WaSuppTran();
                                $document_no = $row->journal_entry_no; //getCodeWithNumberSeries('CREDITORS_PAYMENT');
                                $newSupplierTrans->document_no = $document_no;
                                $newSupplierTrans->total_amount_inc_vat  = '-' . $item->debit;
                                $newSupplierTrans->trans_date  = $dateTime;
                                $newSupplierTrans->suppreference  = $item->narrative;
                                $newSupplierTrans->supplier_no  = $item->getSuppDetail->supplier_code;
                                $newSupplierTrans->grn_type_number  = $series_module->type_number;
                                $newSupplierTrans->journel_entry_id  = $item->id;
                                $newSupplierTrans->save();
                            }
                        }
                    }
                    $row->status = 'processed';
                    $row->save();
                    Session::flash('success', "Processed successfully.");
                    return redirect()->route($this->model . '.index');
                } else {

                    Session::flash('warning', "Debit and Credit amount should be equal");
                    return redirect()->back();
                }
            } else {
                Session::flash('warning', "Add Journal Entry to Process");
                return redirect()->back();
            }
        } else {
            Session::flash('warning', "Journal Entry Not Found");
            return redirect()->back();
        }
    }


    public function edit($slug)
    {
        try {
            $permission =  $this->mypermissionsforAModule();
            if (isset($permission[$this->pmodule . '___edit']) || $permission == 'superadmin') {
                $row =  WaJournalEntry::whereSlug($slug)->first();
                // dd($row);
                if ($row) {
                    $item = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->first();
                    $lastitem = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->groupBy('entry_type')->orderBy('id', 'DESC')->first();
                    $title = 'Edit ' . $this->title;
                    $breadcum = [$this->title => route($this->model . '.index'), 'Edit' => ''];
                    $model = $this->model;
                    $accountnolist = [];
                    if (!empty($row->entry_type)) {

                        $type = $row->entry_type;
                        if ($type == "GL Account") {
                            $accountnolist = getChartOfAccountsDropdown();
                        }
                        if ($type == "Bank Account") {
                            $accountnolist = getBankAccountDropdownsJonralEntry();
                        }
                        if ($type == "Customer Account") {
                            $accountnolist = getCustomerDropdowns();
                        }
                        if ($type == "Supplier Account") {
                            $accountnolist = getSupplierDropdown();
                        }
                    }
                    $restroList = $this->getRestaurantList();

                    return view('admin.journalentries.edit', compact('title', 'accountnolist', 'restroList', 'model', 'item', 'lastitem', 'breadcum', 'row'));
                } else {
                    Session::flash('warning', 'Invalid Request');
                    return redirect()->back();
                }
            } else {
                Session::flash('warning', 'Invalid Request');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back();
        }
    }



    public function update(Request $request, $slug)
    {
        try {
            if ($request->credit > '0' && $request->debit > '0') {
                if ($request->credit < $request->debit) {
                    $request->debit = $request->debit - $request->credit;
                    $request->credit = '0';
                } else {
                    $request->credit = $request->credit - $request->debit;
                    $request->debit = '0';
                }
            }

            if ($request->credit == '0' && $request->debit == '0') {
                Session::flash('warning', "Invalid Amount");
                return redirect()->back()->withInput();
            } else {

                $row =  WaJournalEntry::whereSlug($slug)->where('status', 'pending')->first();
                if ($row) {
                    $checkitem = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->first();
                    $checkcountitem = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->where('entry_type', $request->entry_type)->count();
                    $creditsum = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->where('entry_type', $request->entry_type)->sum('credit');
                    $debitsum = WaJournalEntrieItem::where('wa_journal_entry_id', $row->id)->where('entry_type', $request->entry_type)->sum('debit');
                    //  dd($checkitem);
                    // echo $request->entry_type.' : '.$checkitem->entry_type.' : '.$request->entry_type; die;
                    if ($checkitem) {
                        if ($request->entry_type == $checkitem->entry_type) {
                            // if ($debitsum > 0 && $request->debit == 0) {
                            //     Session::flash('danger', "Please Debit by " . $request->entry_type . " Only");
                            //     return redirect()->back()->withInput();
                            // } else if ($creditsum > 0 && $request->credit == 0) {
                            //     Session::flash('danger', "Please Credit by " . $request->entry_type . " Only");
                            //     return redirect()->back()->withInput();
                            // }else{
                            $item = new WaJournalEntrieItem();
                            $item->wa_journal_entry_id = $row->id;
                            $item->gl_account_id =  $request->gl_account_id;
                            $item->restaurant_id =  $request->restaurant;
                            $item->entry_type = $request->entry_type;
                            $item->credit = $request->credit ? $request->credit : '0';
                            $item->debit = $request->debit ? $request->debit : '0';
                            $item->narrative = $request->narrative;
                            $item->reference              = $request->reference;
                            $item->save();
                            // }
                        } else if ($request->entry_type != $checkitem->entry_type) {
                            //   echo $creditsum.' : '.$debitsum; die;
                            // if ($debitsum > 0 && $request->debit == 0) {
                            //     Session::flash('danger', "Please Debit by ". $request->entry_type." Only");
                            //     return redirect()->back()->withInput();
                            // } else if ($creditsum > 0 && $request->credit == 0) {
                            //     Session::flash('danger', "Please Credit by ". $request->entry_type." Only");
                            //     return redirect()->back()->withInput();
                            // }else{
                            $item = new WaJournalEntrieItem();
                            $item->wa_journal_entry_id = $row->id;
                            $item->gl_account_id =  $request->gl_account_id;
                            $item->restaurant_id =  $request->restaurant;
                            $item->entry_type = $request->entry_type;
                            $item->credit = $request->credit ? $request->credit : '0';
                            $item->debit = $request->debit ? $request->debit : '0';
                            $item->narrative = $request->narrative;
                            $item->reference              = $request->reference;
                            $item->save();
                            // }
                        } else if ($request->entry_type != "GL Account") {
                            //   echo $creditsum.' : '.$debitsum; die;
                            // if ($debitsum > 0 && $request->debit == 0) {
                            //     Session::flash('danger', "Please Debit by " . $request->entry_type . " Only");
                            //     return redirect()->back()->withInput();
                            // } else if ($creditsum > 0 && $request->credit == 0) {
                            //     Session::flash('danger', "Please Credit by " . $request->entry_type . " Only");
                            //     return redirect()->back()->withInput();
                            // } else {
                            $item = new WaJournalEntrieItem();
                            $item->wa_journal_entry_id = $row->id;
                            $item->gl_account_id =  $request->gl_account_id;
                            $item->restaurant_id =  $request->restaurant;
                            $item->entry_type = $request->entry_type;
                            $item->credit = $request->credit ? $request->credit : '0';
                            $item->debit = $request->debit ? $request->debit : '0';
                            $item->narrative = $request->narrative;
                            $item->reference              = $request->reference;
                            $item->save();
                            // }
                        } else {
                            Session::flash('danger', "Invalid Operation");
                            return redirect()->back()->withInput();
                        }
                    } else {
                        Session::flash('warning', "Invalid Action");
                        return redirect()->back()->withInput();
                    }
                    Session::flash('success', 'Record added successfully.');
                    return redirect()->route($this->model . '.edit', $row->slug);
                } else {
                    Session::flash('warning', "Invalid Access");
                    return redirect()->back()->withInput();
                }
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Session::flash('warning', $msg);
            return redirect()->back()->withInput();
        }
    }



    public function destroy($slug)
    {
        try {
            WaJournalEntry::whereSlug($slug)->delete();
            Session::flash('success', 'Deleted successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('warning', 'Invalid Request');
            return redirect()->back();
        }
    }

    public function deleteItem($slug, $item_id)
    {
        $row =  WaJournalEntry::whereSlug($slug)->where('status', 'pending')->first();
        if ($row) {
            WaJournalEntrieItem::where('id', $item_id)->delete();

            $getUpdated_row = WaJournalEntry::whereSlug($slug)->where('status', 'pending')->first();
            if (count($getUpdated_row->getRelatedItem) > 0) {
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.edit', $row->slug);
            } else {
                WaJournalEntry::whereSlug($slug)->delete();
                Session::flash('success', 'Record updated successfully.');
                return redirect()->route($this->model . '.create');
            }
        } else {
            Session::flash('warning', "Invalid Access");
            return redirect()->back()->withInput();
        }
    }
}
