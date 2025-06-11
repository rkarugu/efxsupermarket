<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Route Profitability Report </title>
    <style>

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
            padding: 0;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 11px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box * {
            font-size: 12px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item:last-child {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table style="text-align: center;">
        <tbody>
        <tr class="row">

            <th style="text-align:left !important; font-size:18px">Route: {{@$location_name}}</th>
            <th style="text-align:center !important; font-size:18px">Trip Profitability Report</th>
            <th style="text-align:right !important; font-size:18px">Date: {{date("d/M/Y")}}</th>

        </tr>
        </tbody>

    </table>
    <br>
    <hr/>
    <br>
    <table>

        <tbody>
        <tr class="heading">

            <th style="text-align:left !important">Particular</th>

            <th style="text-align:left !important">Total Quantity</th>
            <th style="text-align:left !important">Total Selling Price</th>
            <th style="text-align:left !important">Total Cost</th>
            <th style="text-align:right !important">Gross Profit</th>

        </tr>
        @php
            $grandtotal = 0;
            $totalQuantity = 0;
            $grandprice = 0;
            $grandprofit =0;
        @endphp
        @foreach($data as $key => $val)
            <tr class="item">

                <td>{{$val->title}}</td>
                @php
                    $total_cost = $val->standard_cost_sum;
                    $totalQuantity += abs($val->total_quantity);
                    $grandtotal  += $total_cost;
                    $grandprice  += $val->price_sum;
                    $grandprofit += ($val->price_sum - abs($total_cost));
                @endphp
                <td>{{manageAmountFormat(abs($val->total_quantity))}}</td>
                <td>{{manageAmountFormat($val->price_sum)}}</td>
                <td>{{manageAmountFormat(abs($total_cost))}}</td>
                <td>{{manageAmountFormat($val->price_sum - abs($total_cost))}}</td>
            </tr>
        @endforeach


        {{--        <tr class="item">--}}
        {{--            <th>Total Tonnage:</th>--}}

        {{--            <th style="text-align:left">{{manageAmountFormat($totalQuantity)}}</th>--}}
        {{--            <th></th>--}}
        {{--            <th></th>--}}
        {{--            <th></th>--}}
        {{--        </tr>--}}
        <tr class="item">
            <th colspan="5">
                <hr/>
            </th>
        </tr>

        <tr class="item">
            <th colspan="5" style="text-align:right;">Total Gross Profit: {{manageAmountFormat($grandprofit)}}</th>
        </tr>

        <tr class="item">
            <th style="text-align:left;">Vehicle Reg No: {{ $telematicsData['vehicle_registration_number'] }}</th>

            <th colspan="2" style="text-align:right;">Distance Covered: {{ $telematicsData['distance_formatted'] }}</th>
            <th></th>
            <th></th>
        </tr>
        <tr class="item">
            <th style="text-align:left;"></th>

            <th colspan="2" style="text-align:right;">Fuel Consumed in Liters: {{ $telematicsData['fuel_formatted'] }}</th>
            <th></th>
            <th></th>
        </tr>
        <tr class="item">
            <th style="text-align:left;"></th>

            <th colspan="2" style="text-align:right;">Cost per Litre: {{ $telematicsData['fuel_cost_formatted'] }}</th>
            <th colspan="2">Total Fuel Cost: {{ $telematicsData['total_fuel_cost_formatted'] }}</th>
        </tr>

        <tr class="item">
            <th colspan="3" style="text-align:right;"></th>
            @php
                $netProfit = $grandprofit - $telematicsData['total_fuel_cost'];
            @endphp
            <th colspan="2">Total Net Profit Cost: {{ "KES. ".number_format($netProfit, 2) }}</th>
        </tr>

        </tbody>

    </table>
</div>

</body>
</html>