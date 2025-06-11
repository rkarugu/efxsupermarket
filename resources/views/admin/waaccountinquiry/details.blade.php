@extends('layouts.admin.admin')
@section('content')
   
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Transaction Account ({{ $transaction }})</h3>
                    @if (can('edit-account-transaction', $model)) 
                        <a href="{{ route("admin.account-inquiry.edit",$transaction ) }}" role="button" class="btn btn-primary"> <i class="fa-solid fa-pen-to-square"></i> Update Account </a>
                    @endif
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    @php
                        $account_codes =  getChartOfAccountsList();
                    @endphp
                    <table class="table table-bordered table-sm table-hover w-100">
                        <tr>
                            <th>Branch</th>
                            <th>GL Account</th>
                            <th>Transaction No</th>
                            <th>Date</th>
                           
                            <th>Narrative</th>
                            <th>Period</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>

                        @foreach ($record as $row)
                            <tr>
                                <td>{!! (isset($row->restaurant->name)) ? $row->restaurant->name : '----' !!}</td>
                                <td>{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!} ({!! $row->account !!})</td>
                                <td>{!! $row->transaction_no !!}</td>
                                <td>{!! getDateFormatted($row->trans_date) !!}</td>
                               
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
                                <td>{!! $row->period_number !!}</td>
                                <td>{!! $row->amount>='0'?$row->amount:'' !!}</td>
                                <td>{!! $row->amount<='0'?$row->amount:'' !!}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <th colspan="6" class="text-right">Total</th>
                            <th>{{$positiveAMount}}</th>
                            <th>{{$negativeAMount}}</th>
                        </tr>
                    </table>
                    
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script>
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
        });
    </script>
@endsection
