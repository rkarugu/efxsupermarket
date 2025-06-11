<html>
<title>PDF</title>

<head>
    <style type="text/css">


        .item_table td{
            /*border-right: 1px solid;*/
        }
        .align_float_center
        {
            text-align:  center;
        }
        .makebold td{
            font-weight: bold;
        }
        .table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
        }

        .table td, th {
            border: 1px solid #dddddd;
            text-align: right;
            margin-top: 5px;
        }
        .table tr {
            padding: 2px;
        }
        /*
        .table tr:nth-child(even) {
          background-color: #dddddd;
        }
        */

    </style>

</head>
<body>

<?php $all_settings = getAllSettings();?>


<div style="width: 100%; height: auto; text-align:center;" >
    <span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
    {{ $all_settings['ADDRESS_1']}}<br>
    {{ $all_settings['ADDRESS_2']}}<br>
    {{ $all_settings['ADDRESS_3']}}<br>
    Tel: {{ $all_settings['PHONE_NUMBER']}}<br>

</div>
<h5 style="text-align: center;"> Chief Cashier Declaration {{ $today. ' '. $branch}}</h5>



<div style="clear: both;">
</div>
@php
    $total = [];
@endphp
<table class="table mt-3">
    <thead>
    <tr class="text-right">
        <th>#</th>
        <th>Name</th>
        <th>Drop Limit</th>
        <th>Total Sales</th>
        <th>Returns</th>
        @foreach($payMethods as $payMethod)
            @if(!$payMethod->is_cash)
                <th class="text-right">{{ \Illuminate\Support\Str::title($payMethod->title )}}</th>
            @endif

        @endforeach
        <th>Total Drops</th>
        <th>Cash At Hand</th>
    </tr>
    </thead>
    <tbody>
    @foreach($groupedData as $index => $data)
        <tr class="parent-row">
            <td>{{ $loop -> iteration }}</td>
            <td>{{ $data['user_name'] }}</td>
            <td>{{ $data['branch'] }}</td>
            <td>{{number_format( $data['user']->drop_limit, 2) ?? 0.00 }}</td>
            <td>{{ number_format($data['total_sales'], 2) }}</td>
            <td>{{ number_format($data['total_returns'], 2) }}</td>
            <td>{{number_format(($data['net_cash']) , 2) }}</td>
            @php
                $amounts = [];
                $cash = 0 - $data['total_change'];
                foreach ($data['payment_methods'] as $payment_method) {
                    $amounts[$payment_method['method_id']] = $payment_method['amount'];
                    if ($payment_method['is_cash']) {
                        $cash += $payment_method['amount'];
                    }
                }
            @endphp

            @foreach($payMethods as $payMethod)
                @if(!$payMethod->is_cash)
                    <th class="text-right">
                        {{ isset($amounts[$payMethod->id]) ? number_format($amounts[$payMethod->id], 2) : '-' }}
                    </th>
                @endif

            @endforeach
            <td>{{ number_format($data['total_drops'], 2)}}</td>
            <td>{{ number_format($data['total_cash'] - $data['total_returns'], 2)}}</td>
        </tr>
    @endforeach

    </tbody>
    <tfoot>
      <tr class="text-bold">
        <td colspan="4">Grand Totals</td>
        @php
            $grouped = collect($groupedData);
        @endphp
        <td  class="text-right">{{number_format( $grouped->sum('total_sales'), 2) }}</td>
        <td  class="text-right">{{number_format( $grouped->sum('total_returns'), 2) }}</td>
        <td  class="text-right">{{number_format( $grouped->sum('net_cash'), 2) }}</td>

        @php
            $methods = [];
             foreach ($grandTotals as $key=>$value) {
                     $methods[$key] = $value;
                 }
        @endphp

        @foreach($payMethods as $payMethod)
            @if(!$payMethod->is_cash)
                <th class="text-right">
                    {{ isset($methods[$payMethod->title]) ? number_format($methods[$payMethod->title], 2) : '-' }}
                </th>
            @endif
        @endforeach
        <td  class="text-right">{{ number_format($grouped->sum('total_drops'), 2) }}</td>
        <td  class="text-right">{{ number_format($grouped->sum('total_cash') - $grouped->sum('total_drops') - $grouped->sum('total_returns') , 2) }}</td>
        <td></td>
    </tr>
    </tfoot>
</table>
<table  style="margin-top: 15px" class="table">
    <tbody>
    <tr>
        <td>Checked By: ___________________</td>
        <td>Approved By: ___________________</td>
    </tr>
    </tbody>
</table>

</body>
</html>