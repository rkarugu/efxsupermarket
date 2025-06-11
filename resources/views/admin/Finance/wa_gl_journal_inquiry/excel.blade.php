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
                                <td>{!! $row->amount>='0'?manageAmountFormat($row->amount):'' !!}</td>
                                <td>{!! $row->amount<='0'?manageAmountFormat($row->amount):'' !!}</td>
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
                                <td>{!! $item->amount>='0'?manageAmountFormat($item->amount):'' !!}</td>
                                <td>{!! $item->amount<='0'?manageAmountFormat($item->amount):'' !!}</td>
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
                            <th>{{manageAmountFormat($positiveAMount)}}</th>
                            <th>{{manageAmountFormat($negativeAMount)}}</th>
                        </tr>
                    </table>

                    <style>
                            .OddTableRows {
        background-color: #CCCCCC;
    }
                    </style>