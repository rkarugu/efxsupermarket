<table>
    <tr>
        <td colspan="18"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="18"><strong>DRIVER PERFORMANCE  REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="18"></td>
    </tr>
   
    @if($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="18"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>

            <th><strong>DRIVER</strong></th>
            <th><strong>TOTAL SHIFTS</strong></th>
            <th><strong>SHIFTS STARTED ON TIME</strong></th>
            <th><strong>START SHIFT REWARD</strong></th>
            <th><strong>SHIFTS ENDED ON TIME</strong></th>
            <th><strong>END SHIFT REWARD</strong></th>
            <th><strong>TOTAL DISPATCHES</strong></th>
            <th><strong>STORES LOADED NEXT DAY</strong></th>
            <th><strong>LOADING REWARD</strong></th>
            <th><strong>ACTUAL DELIVERIES</strong></th>
            <th><strong>SYSTEM DELIVERIES</strong></th>
            <th><strong>SYSTEM USAGE REWARD</strong></th>
            <th><strong>TOTAL FUEL ENTRIES</strong></th>
            <th><strong>ENTRIES BELOW EXPECTED</strong></th>
            <th><strong>ENTRIES WITHIN EXPECTED</strong></th>
            <th><strong>FUEL REWARD</strong></th>
            <th><strong>EXPECTED REWARDS</strong></th>
            <th><strong>EARNED REWARD</strong></th>
            <th><strong>TURN BOY</strong></th>
            <th><strong>TURN BOY REWARD</strong></th>


        </tr>
    </thead>
    <tbody>
        @php
            $totalExpectedRewards = $totalEarnedRewards = 0;
        @endphp
        @foreach ($excelData as $data)
            <tr>
                <td>{{$data['driver']}}</td>
                <td>{{$data['total_shifts']}}</td>
                <td>{{$data['shifts_started_on_time']}}</td>
                <td @style(['background-color:#00ff00' => $data['start_shift_reward']> 0])>{{$data['start_shift_reward']}}</td>
                <td>{{$data['end_shifts_on_time']}}</td>
                <td @style(['background-color:#00ff00' => $data['end_shift_reward']> 0])>{{$data['end_shift_reward']}}</td>
                <td>{{$data['total_dispatches']}}</td>
                <td>{{$data['store_dispatches_loaded_next_day']}}</td>
                <td @style(['background-color:#00ff00' => $data['dispatch_reward']> 0])>{{$data['dispatch_reward']}}</td>
                <td>{{$data['expected_deliveries']}}</td>
                <td>{{$data['actual_deliveries']}}</td>
                <td @style(['background-color:#00ff00' => $data['system_usage_reward']> 0])>{{$data['system_usage_reward']}}</td>
                <td>{{$data['total_fuel_entries']}}</td>
                <td>{{$data['fueled_below_expected']}}</td>
                <td>{{$data['fueled_within_expected']}}</td>
                <td @style(['background-color:#00ff00' => $data['fuelling_reward']> 0])>{{$data['fuelling_reward']}}</td>
                <td>{{manageAmountFormat($data['expected_rewards'])}}</td>
                <td>{{manageAmountFormat($data['total_reward'])}}</td>
                <td>{{$data['turnboy']}}</td>
                <td>{{manageAmountFormat($data['total_reward'] / 2 )}}</td>


            </tr>
            @php
                $totalExpectedRewards +=  $data['expected_rewards'];
                $totalEarnedRewards += $data['total_reward'];
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="1" style="text-align: right"><strong>Total</strong></td>
            <td style="text-align: right"><strong>{{ $totals['total_shifts']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['shifts_started_on_time']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['start_shift_reward']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['end_shifts_on_time']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['end_shift_reward']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['total_dispatches']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['store_dispatches_loaded_next_day']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['dispatch_reward']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['expected_deliveries']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['actual_deliveries']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['system_usage_reward']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['total_fuel_entries']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['fueled_below_expected']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['fueled_within_expected']}}</strong></td>
            <td style="text-align: right"><strong>{{ $totals['fuelling_reward']}}</strong></td>
            <td style="text-align: right"><strong>{{ manageAmountFormat($totalExpectedRewards)}}</strong></td>
            <td style="text-align: right"><strong>{{ manageAmountFormat($totalEarnedRewards)}}</strong></td>
            <td></td>
            <td style="text-align: right"><strong>{{ manageAmountFormat($totalEarnedRewards / 2)}}</strong></td>


        </tr>
    </tfoot>
</table>
