@php
    $settings = getAllSettings();
@endphp

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> geomapping  schedule details </title>
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
            font-size: 13px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        td{
            font-size: 14px !important;
        }

        .invoice-box * {
            font-size: 13px;
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
            padding-bottom: 2px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 30px;
            line-height: 20px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #ddd;
            /* font-weight: bold; */
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
                text-align: left !important;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: left !important;
            }
        }

        .bordered-div {
            width: 100%;
            border: 1px solid;
            padding: 8px;
            margin-bottom: 20px;
            font-size: 14px;
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

        #customers-table tr.heading {
            font-weight: bold;
            font-size: 12px !important;
            text-align: left;
            color: #555;
        }
        .data{
            font-size: 9px !important;
        }
        td{
            text-align: left;
            margin-left: 2px; 
            color: #555;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table style="text-align: left !important;">
        <tbody>
        <tr class="top">
            <th colspan="2">
                <h2 style="font-size:16px !important; text-align:left !important; font-weight: bold;">{{ $settings['COMPANY_NAME'] }}</h2>
            </th>
        </tr>

        <tr class="top">
            <th colspan="2">
                <h2 style="font-size:14px !important; text-align:left !important; margin-top: 0px !important;">{{ $schedule->branchDetails?->name.' - '.$schedule->route?->route_name }} </h2>
            </th>
        </tr>
        </tbody>
    </table>
</div>


<div class="bordered-div">
    <div style="float: left;">
        <table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
            <thead>
                <th></th>
                <th></th>
                
            </thead>
            <tbody>
                <tr>
                    <td>
                        DATE
                    </td>
                    <td>
                        : {{ \Carbon\Carbon::parse($schedule->date)->toFormattedDayDateString() }} 
                    </td>
                </tr>
                <tr>
                    <td>
                        SALESMAN
                    </td>
                    <td>
                        : {{ $schedule->route->salesman() ? $schedule->route->salesman()->name : 'Not Assigned' }} 
                    </td>
                </tr>
                <tr>
                    <td>
                        SUPERVISOR
                    </td>
                    <td>
                        : {{ $schedule->route_manager }}
                    </td>
                </tr>
                <tr>
                    <td>
                        BIZWIZ REP
                    </td>
                    <td>
                        : {{ $schedule->bizwiz_rep }}
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    <div style="float: left; margin-left: 130px;">
        <table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
            <thead>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        EXISTING CUSTOMERS
                    </td>
                    <td>
                        : {{ $page_stats['total_customers'] - $page_stats['new_customers'] }}
                    </td>
                </tr>
                <tr>
                    <td>
                        NEW CUSTOMERS
                    </td>
                    <td>
                        : {{ $page_stats['new_customers'] }} 
                    </td>
                </tr>
                <tr>
                    <td>
                        GEOMAPPED
                    </td>
                    <td>
                        : {{ $page_stats['verified_count'] }}
                    </td>
                </tr>
                <tr>
                    <td>
                        PERCENT COMPLETE
                    </td>
                    <td>
                        : {{ $page_stats['percentage_verified'] }} %
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div style="clear:both;"></div>
</div>

<h4 style="text-align: left; font-size:14px !important;">GEOMAPPED EXISTING CUSTOMERS<h4>
<table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
    <thead>
    <tr class="heading">
        <th style="width: 3%; text-align: left !important; ">#</th>
        <th style="text-align: left !important;"> Time Attended</th>
        <th style="text-align: left !important;"> Center</th>
        <th style="text-align: left !important;"> Bussiness Name</th>
        <th style="text-align: left !important;"> Customer Name</th>
        <th style="text-align: left !important; "> Phone Number</th>
        <th style="text-align: left !important;"> Comment</th>

    </tr>
    </thead>

    <tbody>
        @foreach ($existingCustomers as $customer)
            <tr class="data">
                <td style="font-size: 11px !important;">{{ $loop->index + 1 }}</td>
                <td style="font-size: 11px !important;">{{ \Carbon\Carbon::parse($customer->updated_at)->toTimeString() }}</td>
                <td style="font-size: 11px !important;">{{ $customer->center?->name }}</td>
                <td style="font-size: 11px !important;">{{ $customer->bussiness_name }}</td>
                <td style="font-size: 11px !important;">{{ $customer->name }}</td>
                <td style="font-size: 11px !important;">{{ $customer->phone }}</td>
                <td style="font-size: 11px !important;">{{ $customer->comment }}</td>

            </tr>
        @endforeach

    </tbody>
</table>
<h4 style="text-align: left; font-size:14px !important;">NEW CUSTOMERS<h4>
    <table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
        <thead>
        <tr class="heading">
            <th style="width: 3%; text-align: left !important;">#</th>
            <th style="text-align: left !important;"> Time Attended</th>
            <th style="text-align: left !important;"> Center</th>
            <th style="text-align: left !important;"> Bussiness Name</th>
            <th style="text-align: left !important;"> Customer Name</th>
            <th style="text-align: left !important; "> Phone Number</th>
            <th style="text-align: left !important;"> Comment</th>
    
        </tr>
        </thead>
    
        <tbody>
            @foreach ($newCustomers as $customer)
                <tr class="data">
                    <td style="font-size: 11px !important;">{{ $loop->index + 1 }}</td>
                    <td style="font-size: 11px !important;">{{ \Carbon\Carbon::parse($customer->updated_at)->toTimeString() }}</td>
                    <td style="font-size: 11px !important;">{{ $customer->center?->name }}</td>
                    <td style="font-size: 11px !important;">{{ $customer->bussiness_name }}</td>
                    <td style="font-size: 11px !important;">{{ $customer->name }}</td>
                    <td style="font-size: 11px !important;">{{ $customer->phone }}</td>
                    <td style="font-size: 11px !important;">{{ $customer->comment }}</td>
                  
    
                </tr>
            @endforeach
    
        </tbody>
    </table>
    <h4 style="text-align: left; font-size:14px !important;">NEW CENTRES<h4>
        <table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
            <thead>
            <tr class="heading">
                <th style="width: 3%; text-align: left !important;">#</th>
                <th style="text-align: left !important;"> Name</th>
                <th style="text-align: left !important;"> Location</th>
                <th style="text-align: left !important;"> Latitude</th>
                <th style="text-align: left !important;"> Longitude</th>
        
            </tr>
            </thead>
        
            <tbody>
                @foreach ($newCenters as $center)
                    <tr class="data">
                        <td style="font-size: 11px !important;">{{ $loop->index + 1 }}</td>
                        <td style="font-size: 11px !important;">{{ $center->name }}</td>
                        <td style="font-size: 11px !important;">{{ $center->center_location_name ?? '-' }}</td>
                        <td style="font-size: 11px !important;">{{ $center->lat }}</td>
                        <td style="font-size: 11px !important;">{{ $center->lng }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>

    
</body>

</html>
