<table>
    <tr>
        <td colspan="26"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="26"><strong>SALESMAN PERFORMANCE  REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="26"></td>
    </tr>
   
    @if($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="26"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th><strong>ROUTE</strong></th>
            <th><strong>SALESMAN</strong></th>
            <th><strong>GROUP</strong></th>
            <th><strong>SALES</strong></th>
            <th><strong>SHIFT TONNAGE TARGET</strong></th>
            <th><strong>EXPECTED SHIFTS</strong></th>
            <th><strong>ACTUAL SHIFTS</strong></th>
            <th><strong>EXPECTED TONNAGE</strong></th>
            <th><strong>ACHIEVED TONNAGE</strong></th>
            <th><strong>TONNAGE REWARD</strong></th>
            <th><strong>CTNS</strong></th>
            <th><strong>CTNS REWARD</strong></th>
            <th><strong>DZNS</strong></th>
            <th><strong>DZNS REWARD</strong></th>
            {{-- <th><strong>BULK TONNAGE</strong></th> --}}
            {{-- <th><strong>CATEGORIZED TONNAGE REWARD</strong></th> --}}
            <th><strong>EXPECTED MET</strong></th>
            <th><strong>ACTUAL MET</strong></th>
            <th><strong>MET %</strong></th>
            <th><strong>MET REWARD</strong></th>
            <th><strong>FULLY ONSITE SHIFT</strong></th>
            <th><strong>FULLY ONSITE REWARD</strong></th>
            <th><strong>SHIFTS OPENED ON TIME</strong></th>
            <th><strong>ONTIME REWARD</strong></th>
            <th><strong>SHIFTS CLOSED ON TIME</strong></th>
            <th><strong>TIME MANAGEMENT REWARD</strong></th>
            <th><strong>RETURNS</strong></th>
            <th><strong>RETURNS REWARD</strong></th>
            <th><strong>EXPECTED REWARD</strong></th>
            <th><strong>EARNED REWARDS</strong></th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalExpectedRewards = $totalEarnedRewards = 0;
        @endphp
        @foreach ($excelData as $data)
            <tr>
                <td>{{$data['route']}}</td>
                <td>{{$data['salesman']}}</td>
                <td>{{$data['group']}}</td>
                <td>{{$data['sales']}}</td>
                <td>{{$data['shift_tonnage_target']}}</td>
                <td>{{$data['total_shifts']}}</td>
                <td>{{$data['actual_shifts']}}</td>
                <td>{{$data['expected_tonnage']}}</td>
                <td>{{$data['achieved_tonnage']}}</td>
                <td @style(['background-color:#00ff00' => $data['tonnage_reward'] > 0])>{{$data['tonnage_reward']}}</td>
                <td>{{$data['ctn_tonnage']}}</td>
                <td @style(['background-color:#00ff00' => $data['ctns_reward'] > 0])>{{$data['ctns_reward']}}</td>
                <td>{{$data['dzn_tonnage']}}</td>
                <td @style(['background-color:#00ff00' => $data['dzns_reward'] > 0])>{{$data['dzns_reward']}}</td>
                {{-- <td>{{$data['bulk_tonnage']}}</td> --}}
                {{-- <td @style(['background-color:#00ff00' => $data['category_tonnage_reward'] > 0])>{{$data['category_tonnage_reward']}}</td> --}}
                <td>{{$data['expected_met']}}</td>
                <td>{{$data['actual_met']}}</td>
                <td>{{$data['met_percentage']}}</td>
                <td @style(['background-color:#00ff00' => $data['met_reward'] > 0])>{{$data['met_reward']}}</td>
                <td>{{$data['fully_onsite_shifts']}}</td>
                <td @style(['background-color:#00ff00' => $data['fully_onsite_reward'] > 0])>{{$data['fully_onsite_reward']}}</td>
                <td>{{$data['shifts_opened_ontime']}}</td>
                <td @style(['background-color:#00ff00' => $data['shifts_opened_ontime_reward'] > 0])>{{$data['shifts_opened_ontime_reward']}}</td>
                <td>{{$data['shifts_closed_past_time']}}</td>
                <td @style(['background-color:#00ff00' => $data['time_management_reward'] > 0])>{{$data['time_management_reward']}}</td>
                <td>{{$data['returns']}}</td>
                <td @style(['background-color:#00ff00' => $data['returns_reward']> 0])>{{$data['returns_reward']}}</td>
                <td>{{manageAmountFormat($data['expected_rewards'])}}</td>
                <td >{{manageAmountFormat($data['total_rewards'])}}</td>
            </tr>
            @php
                $totalExpectedRewards +=  $data['expected_rewards'];
                $totalEarnedRewards += $data['total_rewards'];
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            {{-- <td colspan="24" style="text-align: right"><strong>Total</strong></td> --}}
            <th colspan="3">Totals</th>
            <td class="amount"><strong>{{ manageAmountFormat($totals['sales'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['shift_tonnage_target'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['expected_shifts'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['actual_shifts'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['expected_tonnage'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['achieved_tonnage'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['tonnage_reward'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['ctns_tonnage'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['ctns_reward'])}}</strong></td>

            <td class="amount"><strong>{{ manageAmountFormat($totals['dzns_tonnage'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['dzns_reward'])}}</strong></td>

            {{-- <td class="amount"><strong>{{ manageAmountFormat($totals['bulk_tonnage'])}}</strong></td> --}}
            {{-- <td class="amount"><strong>{{ manageAmountFormat($totals['category_tonnage_reward'])}}</strong></td> --}}
            <td class="amount"><strong>{{ manageAmountFormat($totals['expected_met'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['actual_met'])}}</strong></td>
            <td></td>                                   
            <td class="amount"><strong>{{ manageAmountFormat($totals['met_reward'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['fully_onsite_shifts'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['fully_onsite_reward'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_opened_ontime'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_opened_ontime_reward'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['shifts_closed_past_time'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['time_management_reward'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['returns'])}}</strong></td>
            <td class="amount"><strong>{{ manageAmountFormat($totals['returns_reward'])}}</strong></td>
            <td style="text-align: right"><strong>{{ manageAmountFormat($totalExpectedRewards)}}</strong></td>
            <td style="text-align: right"><strong>{{ manageAmountFormat($totalEarnedRewards)}}</strong></td>

        </tr>
    </tfoot>
</table>
