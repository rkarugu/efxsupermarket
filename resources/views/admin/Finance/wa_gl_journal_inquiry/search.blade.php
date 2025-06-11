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
                    <form action="{{route('admin.journal-inquiry.search')}}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="">Transaction Type</label>
                                    <select class="form-control" name="account" id="paymentAccount">
                                        @foreach ($number_series as $item)
                                        <option value="{{$item->type_number}}" 
                                            @if (request()->input('account') == $item->type_number)
                                                selected
                                            @endif
                                        >{{$item->description}} - {{$item->code}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="">From</label>
                                <input type="date" class="form-control" value="{{request()->input('start-date')}}" name="start-date" id="payment_date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="">To</label>
                                <input type="date" class="form-control" name="end-date" value="{{request()->input('end-date')}}" id="payment_date">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" value="filter" name="manage">Filter</button>
                                <button type="submit" class="btn btn-primary" value="export" name="manage">Export</button>
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
                        $positiveAMount = 0;
                        $negativeAMount = 0;
                    @endphp
                    <table class="table table-bordered table-sm table-hover w-100">
                        <tr>
                            <th>Date</th>
                            <th>Transaction type</th>
                            <th>Transaction No</th>
                            <th>Account</th>
                            <th>Account Description</th>
                            <th>Narrative</th>
                            <th>Reference</th>
                            <th>Tag</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                       
                        @foreach ($data as $key => $row)
                            <tr class="@if($key % 2 == 0) OddTableRows @endif">
                                <td>{!! getDateFormatted($row->trans_date) !!}</td>
                                <td>{!! $row->transaction_type !!}</td>

                                <td>{!! $row->transaction_no !!}</td>
                                <td>{!! $row->account !!}</td>
                                <td>{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!}</td>
                              
                                @if($row->transaction_type=="Sales Invoice" && $row->amount > 0)
                                @php
                                $accountno = explode(':',$row->narrative);
                                @endphp
                                <td>{!! (count($accountno)> 1 ) ? $accountno[0] : '---' !!}</td>
                                @else
                                @php
                                $accountno = explode('/',$row->narrative);
                                @endphp
                                <td>{!! (count($accountno)> 1 ) ? $accountno[1] : '---' !!}</td>
                                @endif
                                <td>{{$row->reference}}</td>
                                <td>{!! (isset($row->restaurant->branch_code)) ? $row->restaurant->branch_code : '----' !!}</td>
                                <td>{!! $row->amount>='0'?$row->amount:'' !!}</td>
                                <td>{!! $row->amount<='0'?$row->amount:'' !!}</td>
                                 @php
                                    if($row->amount>='0'){
                                        $positiveAMount = $positiveAMount + $row->amount;
                                    }else {
                                        $negativeAMount = $negativeAMount + $row->amount;
                                    }
                                    
                                @endphp 
                            </tr>
                          
                            @foreach ($row->relatedItems->where('id','!=',$row->id) as $item)
                            <tr class="@if($key % 2 == 0) OddTableRows @endif">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{!! $item->account !!}</td>
                                <td>{!! isset($account_codes[$item->account]) ? $account_codes[$item->account] : '' !!}</td>
                              
                                @if($item->transaction_type=="Sales Invoice" && $row->amount > 0)
                                @php
                                $accountno = explode(':',$item->narrative);
                                @endphp
                                <td>{!! (count($accountno)> 1 ) ? $accountno[0] : '---' !!}</td>
                                @else
                                @php
                                $accountno = explode('/',$item->narrative);
                                @endphp
                                <td>{!! (count($accountno)> 1 ) ? $accountno[1] : '---' !!}</td>
                                @endif
                                <td>{{$row->reference}}</td>
                                <td>{!! (isset($item->restaurant->branch_code)) ? $item->restaurant->branch_code : '----' !!}</td>
                                <td>{!! $item->amount>='0'?$item->amount:'' !!}</td>
                                <td>{!! $item->amount<='0'?$item->amount:'' !!}</td>
                                @php
                                    if($item->amount>='0'){
                                        $positiveAMount = $positiveAMount + $item->amount;
                                    }else {
                                        $negativeAMount = $negativeAMount + $item->amount;
                                    }
                                    
                                @endphp 
                            </tr>
                            @endforeach
                        @endforeach

                        <tr>
                            <th colspan="8" class="text-right">Total</th>
                            <th>{{$positiveAMount}}</th>
                            <th>{{$negativeAMount}}</th>
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
    .OddTableRows {
        background-color: #CCCCCC;
    }
</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    var paymentAccount = function(){
            $("#paymentAccount").select2();
        }
        paymentAccount();
       
</script>
@endsection
