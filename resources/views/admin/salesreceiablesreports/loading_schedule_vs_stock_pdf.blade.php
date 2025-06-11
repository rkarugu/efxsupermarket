@php
    $settings = getAllSettings();
@endphp

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> LOADING SCHEDULE VS STOCKS REPORT </title>
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
            padding-bottom: 20px;
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
        <tr class="top">
            <th colspan="2" style="font-size:18px !important;font-weight: bold; text-align:left !important;">
                {{ $settings['COMPANY_NAME'] }}
            </th>
        </tr>
        <tr class="top">
            <th colspan="2" style="font-size:16px !important; text-align:left !important; margin:3px !important;">
                {{$branch}}
            </th>
        </tr>

        <tr class="top">
            <th colspan="1" style="font-size:15px !important; text-align:left !important;">
                LOADING SCHEDULE VS STOCKS REPORT
            </th>
            <th colspan="1" style="font-size:15px !important; text-align:right !important; margin:3px !important;">
                {{ $shift->date }}
            </th>
        </tr>

      
        </tbody>
    </table>
</div>

<div class="bordered-div">
    <div style="float: left;">
        <span> Delivery No: {{ $schedule?->delivery_number ?? '' }} </span>
        {{-- <span> Date: {{ $shift->date }} </span> --}}
        <span> Route: {{ $shift->relatedRoute->route_name }} </span>
        <span> Salesman: {{ $shift->salesman->name }} </span>

    </div>

    <div style="float: left; margin-left: 30px;">
        <span> Vehicle: {{ $schedule?->vehicle?->license_plate_number }} </span>
        <span> Driver: {{ $schedule?->driver?->name }} </span>
        {{-- <span> Salesman: {{ $shift->salesman->name }} </span> --}}
    </div>

    <div style="float: left; margin-left: 30px;">
        <span> Loader: - </span>
        <span> Total Weight: {{ $shift->shift_tonnage }}T </span>
    </div>

    <div style="clear:both;"></div>
</div>

<div class="bordered-div">
    <h3 style="margin: 0 0 10px 0!important; font-weight: 500; color: #555; font-size: 11px !important;"> INVOICES ON LOADING SHEET </h3>
    <span style="font-size: 11px !important;"> {{ $shift?->invoices }} </span>
</div>

<h3 style="margin: 0 0 10px 0!important; font-weight: 500; color: #555; font-size: 11px !important; text-align: left !important;"> DETAILED STOCK LEDGER </h3>
<table id="customers-table" style="font-size: 10px !important;">
    <thead>
    <tr class="heading">
        <th style="width: 3%; text-align: left !important;">#</th>
        <th style="text-align: left !important;"> Document Number</th>
        <th style="text-align: left !important;"> Route</th>
        <th style="text-align: left !important;"> Date</th>
        <th style="text-align: right !important;"> Total</th>
    </tr>
    </thead>

    <tbody>
    @php
        $grandTotal = 0;
    @endphp

    @foreach($invoices as $index => $invoice)
        <tr>
            <th scope="row" style="width: 3%;"> {{ $loop->index +1 }}</th>
            <td> {{ $invoice['document_number'] }} </td>
            <td> {{ $invoice['route'] }} </td>
            <td> {{ $invoice['date'] }} </td>
            <td style="text-align: right;"> {{ number_format($invoice['total'], 2) }} </td>
        </tr>

        @php
            $grandTotal += $invoice['total'];
        @endphp
    @endforeach
    <tr>
        <th colspan="4" style="text-align: center; border-top: 2px solid black !important; border-bottom: 2px solid black !important;">Grand Total</th>
        <th style="border-top: 2px solid black !important; border-bottom: 2px solid black !important; text-align: right">{{ number_format($grandTotal, 2) }}</th>
    </tr>
    </tbody>
</table>
</body>
</html>