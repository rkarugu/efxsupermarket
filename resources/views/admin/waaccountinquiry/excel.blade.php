<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Sheet</title>
</head>
<body>
    @php
                        $account_codes =  getChartOfAccountsList();
                    @endphp
                    <table class="table table-bordered table-sm table-hover w-100">
                        <tr>
                            <th>Branch</th>
                            <th>Account</th>
                            <th>Account Code</th>
                            <th>Transaction No</th>
                            <th>Date</th>
                            <th>Narrative</th>
                            <th>Tag</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>

                        @foreach ($record as $row)
                            <tr>
                                <td>{!! (isset($row->restaurant->name)) ? $row->restaurant->name : '----' !!}</td>
                                <td>{!! isset($account_codes[$row->account]) ? $account_codes[$row->account] : '' !!}</td>
                                <td>{!! $row->account !!}</td>
                                <td>
                                    {!! $row->transaction_no !!}
                                </td>
                                <td>{!! getDateFormatted($row->trans_date) !!}</td>
                                
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
                                <td>{!! (isset($row->restaurant->branch_code)) ? $row->restaurant->branch_code : '----' !!}</td>
                                <td>{!! $row->amount>='0'?$row->amount:'' !!}</td>
                                <td>{!! $row->amount<='0'?$row->amount:'' !!}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <th colspan="7" class="text-right">Total</th>
                            <th>{{$positiveAMount}}</th>
                            <th>{{$negativeAMount}}</th>
                        </tr>
                    </table>
</body>
</html>