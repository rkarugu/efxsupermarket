@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <h4>
                        <i class="fa fa-filter" aria-hidden="true"></i> Filter
                        <hr>
                    </h4>
                    <form action="{{route('admin.account-inquiry.search')}}" method="GET">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="">Account</label>
                                    <select class="form-control" name="account" id="paymentAccount">
                                        @if ($accounts)
                                            <option value="{{$accounts->id}}" selected>{{$accounts->account_name}} ({{$accounts->account_code}})</option>                                    
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="">Branch</label>
                                <select class="form-control branches" name="branch" id="branches">
                                    @if ($branch)
                                        <option value="{{$branch->id}}" selected>{{$branch->name}}</option>                                    
                                    @endif
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="">From</label>
                                <input type="date" class="form-control" name="start-date" id="payment_date" value="{{Request::get('start-date')}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="">To</label>
                                <input type="date" class="form-control" name="end-date" id="payment_date" value="{{Request::get('end-date')}}">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" value="filter" name="manage">Filter</button>
                                <button type="submit" class="btn btn-primary" value="export" name="manage">Export</button>
                                <button type="submit" class="btn btn-primary" value="export-grouped-transaction" name="manage">Grouped Transaction</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    @php
                        $account_codes =  getChartOfAccountsList();
                    @endphp
                    <table class="table table-bordered table-sm table-hover w-100">
                        <tr>
                            <th class="text-right" colspan="10">Opening Balance</th>
                            <th id="openingBalance"
                                style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ $openingBalance }}</th>
                        </tr>
                        <tr>
                            <th>Branch</th>
                            <th>Account</th>
                            <th>Account Code</th>
                            <th>Transaction No</th>
                            <th>Transcaction Date</th>
                            <th>Posting Date</th>
                            <th>Narrative</th>
                            <th>Tag</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th style="text-align: right;">Running Balance</th>
                        </tr>
                        @php
                            $runningBalance=$openingBalance;
                        @endphp
                        @foreach ($record as $row)
                            <tr>
                                <td>{!! (isset($row->restaurant->name)) ? $row->restaurant->name : '----' !!}</td>
                                <td>{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!}</td>
                                <td>{!! $row->account !!}</td>
                                <td>
                                    @if ($row->transaction_no)
                                        <a target="blank" href="{{route('admin.account-inquiry.details',$row->transaction_no)}}">{!! $row->transaction_no !!}</a>
                                    @else
                                     ---
                                    @endif
                                </td>
                                <td>{!! getDateFormatted($row->trans_date) !!}</td>
                                <td>{!! getDateFormatted($row->created_at) !!}</td>
                                
                                @if($row->transaction_type=="Sales Invoice" && $row->amount > 0)
                                    @php
                                    $accountno = explode(':',$row->narrative);
                                    @endphp
                                    <td>{!! (count($accountno)> 1 ) ? $accountno[0] : '---' !!}</td>
                                @elseif ($row->transaction_type == "Receipt")
                                    <td>{{ $row->narrative }}</td>
                                @else
                                    @php
                                    $accountno = explode('/',$row->narrative);
                                    @endphp
                                    <td>{!! (count($accountno)> 1 ) ? $accountno[1] : '---' !!}</td>
                                @endif
                                <td>{!! (isset($row->restaurant->branch_code)) ? $row->restaurant->branch_code : '----' !!}</td>
                                <td class="text-right">{!! $row->amount>='0'?manageAmountFormat($row->amount):'' !!}</td>
                                <td class="text-right">{!! $row->amount<='0'?manageAmountFormat($row->amount):'' !!}</td>
                                <td class="text-right">
                                    @php
                                        $runningBalance += $row->amount;
                                    @endphp
                                    {{ manageAmountFormat($runningBalance) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <th colspan="8" class="text-right">Total</th>
                            <th>{{manageAmountFormat($positiveAMount)}}</th>
                            <th>{{manageAmountFormat($negativeAMount)}}</th>
                            <th>{{manageAmountFormat($runningBalance)}}</th>
                        </tr>
                    </table>
                    
                </div>
            </div>
        </div>
    </section>
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
        $('body').addClass('sidebar-collapse');
    });
    var paymentAccount = function(){
            $("#paymentAccount").select2(
            {
                placeholder:'Select Account',
                ajax: {
                    url: '{{route("expense.category_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        }
        paymentAccount();
        var branches = function(){
            $(".branches").select2(
            {
                placeholder:'Select Branch',
                ajax: {
                    url: '{{route("expense.branches")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        branches();
</script>
@endsection
