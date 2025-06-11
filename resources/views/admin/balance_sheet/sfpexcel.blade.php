<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Statement</title>
</head>
<body>
    
<table>

        <tr>
            <th></th>
            <th>MTD</th>
            <th>YTD-1</th>
            <th>YTD-2</th>
            <th>YTD-3</th>
        </tr>
        
       
        <tr>
            <th colspan="5">
                Capital Employed
            </th>
        </tr>
        @php
            $e_this_month = $e_this_year = $e_previous_year = $e_two_year_back = 0;
        @endphp
        @foreach($EQUITY as $equity)
            @foreach($equity->getWaAccountGroup as $group)
                @foreach($group->getChartAccount as $account)
                    <tr>
                        <td>{{$account->account_name}}</td>
                        <td style="text-align:right">{{abs($account->this_month_amount)}}</td>
                        <td style="text-align:right">{{abs($account->this_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->previous_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->two_year_back_amount)}}</td>
                    </tr>
                    @php
                        $e_this_month += abs($account->this_month_amount);
                        $e_this_year += abs($account->this_year_amount);
                        $e_previous_year += abs($account->previous_year_amount);
                        $e_two_year_back += abs($account->two_year_back_amount);
                    @endphp
                @endforeach
            @endforeach
        @endforeach
        <tr>
            <th class="borders-op">Total</th>
            <th class="borders-op" style="text-align:right">{{$e_this_month}}</th>
            <th class="borders-op" style="text-align:right">{{$e_this_year}}</th>
            <th class="borders-op" style="text-align:right">{{$e_previous_year}}</th>
            <th class="borders-op" style="text-align:right">{{$e_two_year_back}}</th>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>

        <tr>
            <th colspan="5">
                Non-Current Liabilities
            </th>
        </tr>
        @php
            $nl_this_month = $nl_this_year = $nl_previous_year = $nl_two_year_back = 0;
        @endphp
        @foreach($NONCURRENTLIABILITIES as $nonliabi)
            @foreach($nonliabi->getWaAccountGroup as $group)
                @foreach($group->getChartAccount as $account)
                    <tr>
                        <td>{{$account->account_name}}</td>
                        <td style="text-align:right">{{abs($account->this_month_amount)}}</td>
                        <td style="text-align:right">{{abs($account->this_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->previous_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->two_year_back_amount)}}</td>
                    </tr>
                    @php
                    $nl_this_month += abs($account->this_month_amount);
                    $nl_this_year += abs($account->this_year_amount);
                    $nl_previous_year += abs($account->previous_year_amount);
                    $nl_two_year_back += abs($account->two_year_back_amount);
                @endphp
                @endforeach
            @endforeach
        @endforeach
        <tr>
            <th class="borders-op">Total</th>
            <th class="borders-op" style="text-align:right">{{$nl_this_month}}</th>
            <th class="borders-op" style="text-align:right">{{$nl_this_year}}</th>
            <th class="borders-op" style="text-align:right">{{$nl_previous_year}}</th>
            <th class="borders-op" style="text-align:right">{{$nl_two_year_back}}</th>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <th colspan="5">
                Non-Current Assets
            </th>
        </tr>
        @php
            $na_this_month = $na_this_year = $na_previous_year = $na_two_year_back = 0;
        @endphp
        @foreach($NONCURRENTASSESTS as $nonassets)
            @foreach($nonassets->getWaAccountGroup as $group)
                @foreach($group->getChartAccount as $account)
                    <tr>
                        <td>{{$account->account_name}}</td>
                        <td style="text-align:right">{{abs($account->this_month_amount)}}</td>
                        <td style="text-align:right">{{abs($account->this_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->previous_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->two_year_back_amount)}}</td>
                    </tr>
                    @php
                    $na_this_month += abs($account->this_month_amount);
                    $na_this_year += abs($account->this_year_amount);
                    $na_previous_year += abs($account->previous_year_amount);
                    $na_two_year_back += abs($account->two_year_back_amount);
                @endphp
                @endforeach
            @endforeach
        @endforeach
        <tr>
            <th class="borders-op">Total</th>
            <th class="borders-op" style="text-align:right">{{$na_this_month}}</th>
            <th class="borders-op" style="text-align:right">{{$na_this_year}}</th>
            <th class="borders-op" style="text-align:right">{{$na_previous_year}}</th>
            <th class="borders-op" style="text-align:right">{{$na_two_year_back}}</th>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>


        <tr>
            <th colspan="5">
                Current Assets
            </th>
        </tr>
        @php
            $ca_this_month = $ca_this_year = $ca_previous_year = $ca_two_year_back = 0;
        @endphp
        @foreach($ASSETS as $asset)
            @foreach($asset->getWaAccountGroup as $group)
                @foreach($group->getChartAccount as $account)
                    <tr>
                        <td>{{$account->account_name}}</td>
                        <td style="text-align:right">{{abs($account->this_month_amount)}}</td>
                        <td style="text-align:right">{{abs($account->this_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->previous_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->two_year_back_amount)}}</td>
                    </tr>
                    @php
                        $ca_this_month += abs($account->this_month_amount);
                        $ca_this_year += abs($account->this_year_amount);
                        $ca_previous_year += abs($account->previous_year_amount);
                        $ca_two_year_back += abs($account->two_year_back_amount);
                    @endphp
                @endforeach
            @endforeach
        @endforeach
        <tr>
            <th class="borders-op">Total</th>
            <th class="borders-op" style="text-align:right">{{$ca_this_month}}</th>
            <th class="borders-op" style="text-align:right">{{$ca_this_year}}</th>
            <th class="borders-op" style="text-align:right">{{$ca_previous_year}}</th>
            <th class="borders-op" style="text-align:right">{{$ca_two_year_back}}</th>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <th colspan="5">
                Liabilities
            </th>
        </tr>
        @php
            $li_this_month = $li_this_year = $li_previous_year = $li_two_year_back = 0;
        @endphp
        @foreach($LIABILITIES as $liabi)
            @foreach($liabi->getWaAccountGroup as $group)
                @foreach($group->getChartAccount as $account)
                    <tr>
                        <td>{{$account->account_name}}</td>
                        <td style="text-align:right">{{abs($account->this_month_amount)}}</td>
                        <td style="text-align:right">{{abs($account->this_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->previous_year_amount)}}</td>
                        <td style="text-align:right">{{abs($account->two_year_back_amount)}}</td>
                    </tr>
                    @php
                        $li_this_month += abs($account->this_month_amount);
                        $li_this_year += abs($account->this_year_amount);
                        $li_previous_year += abs($account->previous_year_amount);
                        $li_two_year_back += abs($account->two_year_back_amount);
                    @endphp
                @endforeach
            @endforeach
        @endforeach
        <tr>
            <th class="borders-op">Total</th>
            <th class="borders-op" style="text-align:right">{{$li_this_month}}</th>
            <th class="borders-op" style="text-align:right">{{$li_this_year}}</th>
            <th class="borders-op" style="text-align:right">{{$li_previous_year}}</th>
            <th class="borders-op" style="text-align:right">{{$li_two_year_back}}</th>
        </tr>

        
        
       


    </table>       

                            
                        

</body>
</html>