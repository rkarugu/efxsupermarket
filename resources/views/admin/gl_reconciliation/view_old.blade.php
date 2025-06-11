@extends('layouts.admin.admin')
@php
$beginningBalance = abs($data->beginning_balance);
$unpresentedCheque = $vouchers->sum('amount');
$uncreditedItems = abs($missingInBank->sum('amount'));

@endphp

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> View GL Reconcile </h3>
                    <div class="d-flex">
                        @if (can('close-recon',$model) && $data->status=='pending')
                            <a  class="btn btn-primary"  data-toggle="modal" data-target="#confirmationModal"><i class="fa-regular fa-thumbs-up"></i> Close Recon</a>
                        @endif
                        @if (can('re-verify',$model))
                            <a href="{{ route('gl-reconciliation.re-verify',$data->id) }}" class="btn btn-primary" style="margin-left: 10px;"><i class="fas fa-solid fa-rotate"></i> Re-Verify</a>
                        @endif
                        
                        {{-- @if (can('edit',$model))
                            <a href="{{ route('gl-reconciliation.edit',$data->id) }}" class="btn btn-primary" style="margin-left: 10px;"><i class="fas fa-pen"></i> Edit</a>
                        @endif --}}
                        <a href="{{ route('gl-reconciliation.list') }}" class="btn btn-primary" style="margin-left: 10px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="bank_account" class="control-label"> Bank Account </label>
                        <span class="form-control">{{$data->bankAccount->account_name}} ({{$data->bankAccount->account_code}})</span>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="">Start Date</label>
                          <span class="form-control">{{$data->start_date}}</span>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label for="">End Date</label>
                          <span class="form-control">{{$data->end_date}}</span>
                        </div>
                    </div>
    
                    <div class="form-group col-sm-4 text-right">
                        <h2 style="margin-bottom: 0px;font-weight:700;">Ksh. <span id="difference">
                               
                        </span></h2>
                        <span style="font-weight: 600">Difference</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                          <label for="">Beginning balance</label>              
                          <span class="form-control">{{ manageAmountFormat(abs($data->beginning_balance)) }}</span>                    
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Ending Balance</label>     
                            <span class="form-control">{{ manageAmountFormat($data->ending_balance) }}</span>     
                        </div>
                    </div>
                </div>               
            </div>
        </div>
    </section>
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12 no-padding-h" id="getintervalview">

                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#statements" data-toggle="tab">Statements</a></li>
                            <li><a href="#serviceCharges" data-toggle="tab">Service Charges</a></li>
                            <li><a href="#interests" data-toggle="tab">Interests</a></li>
                            <li><a href="#beginningBal" data-toggle="tab">Beginning Bal.</a></li>
                            <li><a href="#unpresentedCheq" data-toggle="tab">Unpresented Cheq.</a></li>
                            <li><a href="#payments" data-toggle="tab">Missing Transactions</a></li>
                            <li><a href="#unknownBanking" data-toggle="tab">Unknown Banking</a></li>
                            <li><a href="#matched" data-toggle="tab">Matched</a></li>
                        </ul>
        
                        <div class="tab-content">
                            <div class="tab-pane" id="unknownBanking">
                                <div class="box-body">
                                    <table class="table table-bordered datatable" id="">
                                        <thead>
                                            <tr>
                                                <th>Bank Date</th>
                                                <th>Reference</th>
                                                <th class="text-right">Debit</th>
                                                <th class="text-right">Credit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $debitTotal = 0;
                                                $creditTotal = 0;
                                            @endphp
                                            @foreach ($unknownBankings as $bank)
                                                @php
                                                $debit=0;
                                                $credit=0;
                                                    if ($bank->amount < 0) {
                                                        $debit = $bank->amount;
                                                        $debitTotal += $debit;
                                                    } else {
                                                        $credit = $bank->amount;
                                                        $creditTotal += $credit;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $bank->bank_date}}</td>
                                                    <td>{{ $bank->reference}}</td>
                                                    <td>{{ manageAmountFormat($debit) }}</td>
                                                    <td>{{ manageAmountFormat($credit) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">Total</td>
                                                <th>{{ manageAmountFormat($debitTotal) }}</th>
                                                <th> {{ manageAmountFormat($creditTotal) }} </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane active" id="statements">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <table class="table table-bordered" id="pendingDataTable">
                                                <tr>
                                                    <td colspan="2"><b>Cash Book</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Balance Per Cash Book (C1) <small><i>beginning balance</i></small></td>
                                                    <td class="text-right"> {{ manageAmountFormat($beginningBalance) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Unpresented Cheque (C2) <small><i>cheque not banked</i></small></td>
                                                    <td class="text-right"> {{ manageAmountFormat($unpresentedCheque) }} </td>
                                                </tr>
                                                <tr>
                                                    <td>Uncredited Items (C3) <small><i>missing transactions</i></small></td>
                                                    <td class="text-right">{{ manageAmountFormat($uncreditedItems) }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Adjusted Cash Book Balance ((C1 + C2) - C3=C4)</th>
                                                    <th class="text-right"> 
                                                        @php
                                                            $c4 = ($beginningBalance + $unpresentedCheque) - $uncreditedItems;
                                                        @endphp
                                                        {{ manageAmountFormat($c4)}} </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Bank Statement Balance(C5)</th>
                                                    <th class="text-right"> 
                                                        @php
                                                            $c5 =$data->ending_balance;
                                                        @endphp
                                                        {{ manageAmountFormat($c5)}} </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">(C4-C5=C6)</th>
                                                    <th class="text-right"> 
                                                        @php
                                                            $c6 = $c4-$c5;
                                                        @endphp
                                                        {{ manageAmountFormat($c6)}} </th>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><b>Bank Statement</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Undebited in Cash Book (B1) <small><i>unknown banking <!-- (pick total credit from unknown bankings i.e bank statement)--></i></small></td>
                                                    <td class="text-right">{{ manageAmountFormat($creditTotal) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>UnCredited in Cash Book (B2) <small><i>supplier payment not in system </i><!--( pick total debits from unknown bankings)--></small></td>
                                                    <td class="text-right">
                                                        {{ manageAmountFormat(abs($debitTotal)) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Total (B1 - B2)</th>
                                                    <th class="text-right">{{ manageAmountFormat($creditTotal-abs($debitTotal))}}</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Difference (C6 + B1 - B2)</th>
                                                    <th class="text-right" id="difference_amount">{{ manageAmountFormat($c6+$creditTotal-$debitTotal)}}</th>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="serviceCharges">
                                <div class="box-body">
                                    <table class="table table-bordered" id="pendingDataTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Document No.</th>
                                                <th>Account</th>
                                                <th>Reference</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalExpense=0;
                                            @endphp
                                            @foreach ($data->extras as $item)
                                                @if ($item->type=='Expense')
                                                @php
                                                    $totalExpense +=abs($item->amount);
                                                @endphp
                                                    <tr>
                                                        <td>{{$item->date}}</td>
                                                        <td>{{ $item->document_no }}</td>
                                                        <td>{{$item->chartOfAccount->account_name}} ({{$item->chartOfAccount->account_code}})</td>
                                                        <td>{{$item->reference}}</td>
                                                        <td class="text-right">{{ manageAmountFormat(abs($item->amount)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2"></th>
                                                <th style="text-align: right;" colspan="1">Total:</th>
                                                <th id="debtorsTotal"
                                                    style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                                    {{ manageAmountFormat($totalExpense) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                        
                                    </table>
                                </div>
                            </div>
        
                            <div class="tab-pane" id="interests">
                                <div class="box-body">
                                    <table class="table table-bordered" id="debtorsDataTable">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Document No.</th>
                                                <th>Account</th>
                                                <th>Reference</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalInterest=0;
                                            @endphp
                                            @foreach ($data->extras as $item)
                                                @if ($item->type=='Interest')
                                                @php
                                                    $totalInterest += $item->amount;
                                                @endphp
                                                    <tr>
                                                        <td>{{$item->date}}</td>
                                                        <td>{{ $item->document_no }}</td>
                                                        <td>{{$item->chartOfAccount->account_name}} ({{$item->chartOfAccount->account_code}})</td>
                                                        <td>{{$item->reference}}</td>
                                                        <td class="text-right">{{ manageAmountFormat($item->amount) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2"></th>
                                                <th style="text-align: right;" colspan="1">Total:</th>
                                                <th id="debtorsTotal"
                                                    style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                                    {{ manageAmountFormat($totalInterest) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                        
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="beginningBal">
                                <div class="box-body">
                                    <table class="table table-bordered" id="beginningBalDataTable">
                                        <thead>

                                            <tr>
                                                <th class="text-right" colspan="8">Opening Balance</th>
                                                <th id="openingBalance"
                                                    style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;"></th>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <th>Branch</th>
                                                <th>Description</th>
                                                <th>Reference</th>
                                                <th>Transaction Type</th>
                                                <th>Transaction No</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Running Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr style="font-weight: bold;">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td id="debitTotal"></td>
                                                <td id="creditTotal"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th style="text-align: right;" colspan="8">Closing Balance:</th>
                                                <th id="closingBalance" style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                                </th>
                                            </tr>
                                        </tfoot>
                                        {{-- <tfoot>
                                            <tr>
                                                <th colspan="1"></th>
                                                <th style="text-align: right;" colspan="1">Total:</th>
                                                <th id="debtorsTotal"
                                                    style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                                    {{ manageAmountFormat($totalInterest) }}
                                                </th>
                                            </tr>
                                        </tfoot> --}}
                                        
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="unpresentedCheq">
                                <div class="box-body">
                                    <table class="table table-bordered" id="vouchersDataTable">
                                        <thead>
                                            <tr>
                                                <th>Voucher No.</th>
                                                <th>Date Created</th>
                                                <th>Supplier</th>
                                                <th>Account</th>
                                                <th>Payment Mode</th>
                                                <th>Items</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vouchers as $voucher)
                                                <tr>
                                                    <td>{{ $voucher->number }}</td>
                                                    <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                                                    <td>{{ $voucher->supplier->name }}</td>
                                                    <td>{{ $voucher->account->account_name }}</td>
                                                    <td>{{ $voucher->paymentMode->mode }}</td>
                                                    <td>{{ $voucher->voucher_items_count }}</td>
                                                    <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-right">Total</th>
                                                <th class="text-right" id="totalAmount">{{ manageAmountFormat($vouchers->sum('amount')) }}</th>
                                            </tr>
                                        </tfoot>                                        
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="payments">
                                <div class="box-body">
                                    
                                    <table class="table table-bordered" id="paymentDataTable">
                                        <thead>
                                            <tr>
                                                <th>Trans Date</th>
                                                <th>Route</th>
                                                <th>Document No.</th>
                                                <th>Channel</th>
                                                <th>Reference</th>
                                                <th>Status</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td><b id="total_payments"></b></td>
                                            </tr>
                                            </tfoot>
                                    </table>
                                </div>
                            </div>

                            

                            <div class="tab-pane" id="supplierPayments">
                                <div class="box-body">
                                    <table class="table table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>Bank Date</th>
                                                <th>Reference</th>
                                                <th>Channel</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($supplierPayments as $item)
                                                <tr>
                                                    <td>{{ $item->bank_date }}</td>
                                                    <td>{{ $item->reference }}</td>
                                                    <td>{{ $item->channel }}</td>
                                                    <td>{{ $item->amount }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td><b id="total_unknown"></b></td>
                                            </tr>
                                            </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="matched">
                                <div class="box-body">
                                    <table class="table table-bordered" id="matchedDataTable">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Bank Date</th>
                                                <th>Document No.</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
        
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="modal-title" id="confirmationModalLabel">Confirm Action</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <h4>Are you sure you want to Close this Recon ?</h4>
                </div>
                <div class="box-footer">
                    <form action="{{ route('gl-reconciliation.close-recon',$data->id) }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="confirmActionButton">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('body').addClass('sidebar-collapse');
        $('#difference').text($('#difference_amount').text());

        $('.datatable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });

        $("#paymentDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('gl-reconciliation.payment-table',$data->id) !!}',
                data: function(data) {
                    
                }
            },
            columns: [
                {
                    data: 'trans_date',
                    name: 'trans_date',
                },
                {
                    data: 'customer_detail.customer_name',
                    name: 'customerDetail.customer_name',
                },
                {
                    data: 'document_no',
                    name: 'document_no',
                },
                {
                    data: 'channel',
                    name: 'channel',
                },
                {
                    data: 'reference',
                    name: 'reference'
                },
                {
                    data: 'verification_status',
                    name: 'verification_status'
                },
                {
                    data:'amount',
                    name:'amount',
                    className: 'text-right'
                }
                
            ],
            
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();
                $("#total_payments").text(json.total);
            }
        });

        $("#beginningBalDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('gl-reconciliation.begin_balance-table',$data->id) !!}',
                data: function(data) {
                    
                }
            },
            columns: [{
                    data: 'created_at',
                    name: 'wa_gl_trans.created_at',
                },
                    {
                        data: 'branch.name',
                        name: 'branch.name'
                    },
                    {
                        data: 'narrative',
                        name: 'narrative'
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'transaction_no',
                        name: 'transaction_no'
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        className: 'text-right'
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        className: 'text-right'
                    },
                    {
                        data: 'running_balance',
                        name: 'running_balance',
                        className: 'text-right'
                    }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#debitTotal").text(json.debitCreditTotal.debit);
                    $("#creditTotal").text(json.debitCreditTotal.credit);
                    $("#openingBalance").html(json.opening_balance);
                    $("#closingBalance").html(json.closing_balance);
                }
        });

        $("#vouchersDataTabl").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    data: function(data) {
                        
                    }
                },
                columns: [{
                        data: "id",
                        name: "id"
                    },
                    
                    {
                        data: "actions",
                        name: "actions",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalAmount").html(json.total_amount);
                }
        });

        $("#matchedDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('gl-reconciliation.matched-table',$data->id) !!}',
                data: function(data) {
                    
                }
            },
            columns: [
                {
                    data: 'reference',
                    name: 'reference',
                },  
                {
                    data: 'bank_statement.bank_date',
                    name: 'bankStatement.bank_date',
                },    
                {
                    data: 'document_no',
                    name: 'document_no'
                },
                {
                    data: 'bank_statement.amount',
                    name: 'bankStatement.amount',
                    className: 'text-right'
                },              
            ],
        });
                
    });

</script>
@endsection