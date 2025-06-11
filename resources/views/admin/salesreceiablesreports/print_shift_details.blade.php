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
            text-align: left;
            color: #555;
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
    <table style="text-align: center;">
        <tbody>
        <tr class="top">
            <th colspan="2">
                <h2 style="font-size:18px !important;font-weight: bold;">{{ $settings['COMPANY_NAME'] }}</h2>
            </th>
        </tr>

        <tr class="top">
            <th colspan="2">
                <h2 style="font-size:16px !important;"> {{$shift->shiftId}} SHIFT  REPORT</h2>
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
                        : {{ \Carbon\Carbon::parse($shift->created_at)->toFormattedDayDateString()}} 
                    </td>
                </tr>
                <tr>
                    <td>
                        SALESMAN
                    </td>
                    <td>
                        : {{ $shift->salesman->name }} 
                    </td>
                </tr>
                <tr>
                    <td>
                        SALES TARGET
                    </td>
                    <td>
                        : {!! format_amount_with_currency($shift->relatedRoute?->sales_target) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        SHIFT  TOTAL
                    </td>
                    <td>
                        : {!! format_amount_with_currency($shift->shift_total) !!}
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
                        MET CUSTOMERS
                    </td>
                    <td>
                        : {{$visitedCustomers}}/{{$routeCustomers}} 
                    </td>
                </tr>
                <tr>
                    <td>
                        TONNAGE
                    </td>
                    <td>
                        : {{ number_format($shift->shift_tonnage, 2)}}/{{$route->tonnage_target}} 
                    </td>
                </tr>
                <tr>
                    <td>
                        CTNS
                    </td>
                    <td>
                        : {{$shift->shift_ctns}}/{{$route->ctn_target}}
                    </td>
                </tr>
                <tr>
                    <td>
                        DOZENS
                    </td>
                    <td>
                        : {{$shift->shift_dzns}}/{{$route->dzn_target}}
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- <span> MET  CUSTOMERS: {{$visitedCustomers}}/{{$routeCustomers}} </span>
        <span> TONNAGE: {{ number_format($shift->shift_tonnage, 2)}}/{{$route->tonnage_target}} </span>
        <span> CTNS: {{$shift->shift_ctns}}/{{$route->ctn_target}} </span>
        <span> DOZENS: {{$shift->shift_dzns}}/{{$route->dzn_target}} </span>
        <span> SHIFT TOTAL: {!! format_amount_with_currency($shift->shift_total) !!}  </span> --}}


    </div>

    {{-- <div style="float: left; margin-left: 30px;">
        <span> : - </span>
        <span> Total Weight: {{ $shift->shift_tonnage }}T </span>
    </div> --}}

    <div style="clear:both;"></div>
</div>
{{-- 
<div class="bordered-div">
    <h3 style="margin: 0 0 10px 0!important; font-weight: 500; color: #555;"> INVOICES </h3>
    <span> {{ $shift?->invoices }} </span>
</div> --}}
<h4 style="text-align: left;">MET CUSTOMERS<h4>
<table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
    <thead>
    <tr class="heading">
        <th style="width: 3%; text-align: left !important;">#</th>
        <th style="text-align: left !important;"> NAME</th>
        <th style="text-align: left !important;"> PHONE</th>
        <th style="text-align: left !important;"> CENTER</th>
        <th style="text-align: left !important;"> ORDER NO</th>
        <th style="text-align: left !important; width:17% !important;"> ORDER  TOTAL</th>
        <th style="text-align: left !important;"> TONNAGE</th>


    </tr>
    </thead>

    <tbody>
        <?php $rowIndex = 0; ?>

    
        @foreach ($data as $shiftCustomer)
        @if ($shiftCustomer['is_met'] == 1)
        <tr>
        <td>{{++$rowIndex}}</td>
        <td>{{ $shiftCustomer['customer_name'] }}</td>
        <td>{{ $shiftCustomer['customer_phone_no'] }}</td>
        <td>{{ $shiftCustomer['customer_town'] }}</td>
        {{-- <td>@if ($shiftCustomer['is_met'] == 1)
            YES
            
        @else
        NO
            
        @endif</td> --}}
        <td>
            @if ($shiftCustomer['order_no'])
            {{$shiftCustomer['order_no']}}
                
            @else
            N/A
                
            @endif
        </td>
        
        <td style="width: 17% !important;">
            @if ($shiftCustomer['order_total'])
            {!! format_amount_with_currency($shiftCustomer['order_total']) !!}
                
            @else
            N/A
                
            @endif


        </td>
        <td>{{  number_format($shiftCustomer['customer_tonnage'], 2)  }}</td>
        <td>  <div class="action-button-div">
            @if ($shiftCustomer['order_slug'])
            <a href="{{ route('get-shop-order-details', $shiftCustomer['order_slug']) }}"
            class="text-primary" title="View Order Details"><i
                class='fa fa-eye text-primary fa-lg'></i></a>
                
            @endif
       
        </div></td>
    </tr>
            
        @endif
        
            
        @endforeach
    </tbody>
</table>

<h4 style="text-align: left;">UNMET CUSTOMERS<h4>
    <table id="customers-table" style="padding-top: 1px !important; margin-top:0 !important;"  >
        <thead>
        <tr class="heading">
            <th style="width: 3%; text-align: left !important;">#</th>
            <th style="text-align: left !important;"> NAME</th>
            <th style="text-align: left !important;"> PHONE</th>
            <th style="text-align: left !important;"> CENTER</th>
            <th style="text-align: left !important;"> REASON</th>
        </tr>
        </thead>
        <tbody>    
            <?php $rowNumber = 0; ?>
                               
            @foreach ($data as $shiftCustomer)
            @if ($shiftCustomer['is_met'] != 1)
            <tr>
            
                <td>{{++$rowNumber}}</td>
                <td>{{ $shiftCustomer['customer_name'] }}</td>
                <td>{{ $shiftCustomer['customer_phone_no'] }}</td>
                <td>{{ $shiftCustomer['customer_town'] }}</td>
                {{-- <td>
                    @if ($shiftCustomer['order_no'])
                    {{$shiftCustomer['order_no']}}
                        
                    @else
                    N/A
                        
                    @endif
                </td> --}}
                
                {{-- <td>
                    @if ($shiftCustomer['order_total'])
                    {!! format_amount_with_currency($shiftCustomer['order_total']) !!}
                        
                    @else
                    N/A
                        
                    @endif


                </td> --}}
                {{-- <td>{{  number_format($shiftCustomer['customer_tonnage'], 2)  }}</td> --}}
                <td>{{$shiftCustomer['reported_issue'] ?? '-' }}</td>
                {{-- <td>  <div class="action-button-div">
                    @if ($shiftCustomer['order_slug'])
                    <a href="{{ route('get-shop-order-details', $shiftCustomer['order_slug']) }}"
                    class="text-primary" title="View Order Details"><i
                        class='fa fa-eye text-primary fa-lg'></i></a>
                        
                    @endif
                
                </div></td> --}}
            </tr>
                
            @endif
            
                
            @endforeach
            </tbody>
    
        
    </table>
    
</body>

</html>
