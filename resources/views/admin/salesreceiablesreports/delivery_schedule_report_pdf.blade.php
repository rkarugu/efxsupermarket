@php
    $settings = getAllSettings();
@endphp

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> DELIVERY SCHEDULE REPORT </title>
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
            margin-bottom: 0;
            padding-bottom: 0;
            color: #000;
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
            font-size: 11px;
        }

        .invoice-box table, table {
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
            /* padding-bottom: 10px; */
        }

        .invoice-box table tr.top table td.title {
            font-size: 11px;
            /*line-height: 45px;*/
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

        .bordered-div {
            width: 100%;
            border: 1px solid;
            padding: 8px;
            margin-bottom: 20px;
            font-size: 11px;
            font-weight: 400;
            text-align: left;
            color: #555;
        }

        .bordered-div span {
            display: block;
            margin-bottom: 5px;
        }

        #customers-table {
            width: 100%;
        }

        #customers-table tr.heading td {
            font-weight: bold;
            text-align: left;
            color: #555;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <table style="text-align: left;">
        <tbody>
        <tr >
            <th colspan="2" style="font-size:18px !important;font-weight: bold; text-align:left !important;">
                {{ $settings['COMPANY_NAME'] }}
            </th>
        </tr>
        <tr >
            <th colspan="2" style="font-size:16px !important;font-weight: bold; text-align:left !important; margin:3px !important;">
                {{ $branch }}
            </th>
        </tr>

        <tr >
            <th colspan="1" style="font-size:14px !important;font-weight: bold; text-align:left !important;">
                DELIVERY SCHEDULE REPORT
            </th>
            <th colspan="1" style="font-size:14px !important;font-weight: bold; text-align:right !important; margin:3px !important;">
                {{ $deliveryDate }}
            </th>
        </tr>

      
        </tbody>
    </table>
</div>


<table id="customers-table" style="font-size: 10px !important;">
    <thead>
  
    <tr class="heading">
        <th style="width: 3%; text-align: left !important;">#</th>
        <th style="width:20%; text-align: left !important;">ROUTE</th>
        <th style="text-align: left !important;">VAN</th>
        <th style=" width:20%; text-align: left !important;">DRIVER</th>
        <th style="text-align: center !important;">TONNAGE</th>
        <th style="text-align: center !important;">CTNS</th>
        <th style="text-align: center !important;">DZNS</th>
        <th style="text-align: right !important;">AMOUNT</th>
      
    </tr>
    </thead>

    <tbody>
        @php
        $totalTonnage = 0;
        $totalCtns = 0;
        $totalDzns = 0;
        $totalAmount = 0;
    @endphp

    @foreach($deliverySchedules as $index => $schedule)
    <tr>
        <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
        <td style="width: 20%;"> {{ $schedule->route?->route_name ?? '-' }} </td>
        <td>{{ $schedule->vehicle?->license_plate_number ?? '-' }}</td>
        <td style="width: 20%;">{{  $schedule->driver?->name ?? '-'}}</td>
        <td  style="text-align: center;"> {{ $schedule->shift->shift_ctns }} </td>
        <td style="text-align: center;"> {{ $schedule->shift->shift_dzns }} </td>
        <td style="text-align: center;"> {{ number_format($schedule->shift->shift_tonnage, 2) }} </td>
        <td style="text-align: right"> {{ manageAmountFormat($schedule->shift->shift_total) }}  </td>
    
        
    
    </tr>

        @php

        $totalTonnage += $schedule->shift->shift_tonnage;
        $totalCtns += $schedule->shift->shift_ctns;
        $totalDzns += $schedule->shift->shift_dzns;
       $totalAmount += $schedule->shift->shift_total;
     @endphp
    @endforeach
    <tr>

        <th colspan="4" style="text-align:left; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">Total</th>
        <th style="text-align: center; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">{{$totalCtns}}</th>
        <th style="text-align: center; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">{{$totalDzns}}</th>
        <th style="text-align: center; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">{{number_format($totalTonnage,2)}}</th>
        <th style="border-top: 2px solid black !important; border-bottom: 2px solid black !important; text-align: right">{{manageAmountFormat($totalAmount)}}</th>
        
    </tr>
    </tbody>
</table>
</body>
</html>