@php
    $settings = getAllSettings();
@endphp

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ $report_name }} </title>
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
            <th colspan="2" style="font-size:16px !important;font-weight: bold; text-align:left !important; margin:3px !important;">
                {{ $branch }}
            </th>
        </tr>
        <tr class="top">
            <th colspan="1" style="font-size:15px !important;font-weight: bold; text-align:left !important;">
                DELIVERY REPORT
            </th>
            <th colspan="1" style="font-size:15px !important;font-weight: bold; text-align:right !important; margin:3px !important;">
                {{ $shift->date }}
            </th>
        </tr>


        </tbody>
    </table>
</div>

<div class="bordered-div" style="font-size: 13px !important">
    <div style="float: left;">
        <span> Delivery No: {{ $schedule?->delivery_number ?? '' }} </span>
        <span> Route: {{ $shift->relatedRoute->route_name }} </span>
        <span> Salesman: {{ $shift->salesman->name }} </span>

    </div>

    <div style="float: left; margin-left: 30px;">
        <span> Vehicle: {{ $schedule?->vehicle?->license_plate_number }} </span>
        <span> Driver: {{ $schedule?->driver?->name }} </span>
        <span> Loader: - </span>

    </div>

    <div style="float: left; margin-left: 30px;">
        <span> Total Weight: {{ $shift->shift_tonnage }}T </span>
        <span> Total Amount: {{ manageAmountFormat($shift->shift_total) }} </span>
    </div>

    <div style="clear:both;"></div>
</div>

<div class="bordered-div">
    <h3 style="margin: 0 0 10px 0!important; font-weight: 500; color: #555; font-size: 11px !important;"> INVOICES </h3>
    <span style="font-size: 12px !important;"> {{ $shift?->invoices }} </span>
</div>

<table id="customers-table" style="font-size: 12px !important;">
    <thead>
    <tr class="heading">
        <th style="width: 3%; text-align: left !important;">#</th>
        <th style="text-align: left !important;"> Invoice</th>
        <th style="text-align: left !important;"> A/C Name</th>
        <th style="text-align: left !important;"> Location</th>
        <th style="text-align: right !important;"> Weight</th>
        <th style="text-align: right !important;"> Total</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $totalAmount = 0;
    ?>
    @foreach($shift->actual_orders as $index => $order)
        <tr style="margin: 6px !important;">
            <th scope="row" style="width: 3%; border-bottom:1px solid black !important; border-top:1px solid black !important"> {{ $loop->index +1 }}</th>
            <td style="border-bottom:1px solid black !important; border-top:1px solid black !important"> {{ $order['invoice_id'] }}</td>
            <td style="border-bottom:1px solid black !important; border-top:1px solid black !important"> {{ $order['customer_name'] }}</td>
            <td style="border-bottom:1px solid black !important; border-top:1px solid black !important"> {{ $order['location'] }}</td>
            <td style="text-align: right !important; border-bottom:1px solid black !important; border-top:1px solid black !important">{{ number_format($order['tonnage'], 2) }}</td>
            <td style="text-align: right !important; border-bottom:1px solid black !important; border-top:1px solid black !important"> {{ manageAmountFormat($order['total']) }}</td>
        </tr>
            <?php
            $totalAmount += $order['total'];
            ?>

    @endforeach
    <tr style="border-bottom:2px solid black !important; border-top:2px solid black !important">
        <th colspan="4">Total</th>
        <th style="text-align:right;">{{ number_format($shift->shift_tonnage,2) }}</th>

        <th style="text-align:right;">{{ manageAmountFormat($totalAmount) }}</th>

    </tr>
    </tbody>
</table>
</body>
</html>