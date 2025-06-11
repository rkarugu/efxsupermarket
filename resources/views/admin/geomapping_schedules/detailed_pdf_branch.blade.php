<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geomapping Summary</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            text-align: left !important;
            font-weight: 300;
            margin-top: 2px !important;
            color: #555;
        }
        body h4 {
            text-align: left !important;
            font-weight: 300;
            margin-top: 2px !important;
            color: #555;
        }
        body h2 {
            text-align: left !important;
            font-weight: 300;
            margin-top: 6px;
            /* margin-bottom: 20px; */
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 12px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
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
            /* padding-bottom: 5px; */
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
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td:last-child {
            border-bottom: 1px solid #eee;
        }
        .bordered{
            border: 1px solid #000 !important;
        }
        .bordered td{
            border: 1px solid #000 !important;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: left;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        .item.bg-grey {}

        .horizontal_dotted_line {
            text-align: left !important;
        }
    </style>
</head>

<body>
    @foreach ($routes as $route)
    <div class="invoice-box">
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h3>KANINI HARAKA ENTERPRISES LTD</h3>
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1" style="font-size: 15px;">
                       <h4>Geomapping Summary - {{$branch->name}} - {{$route->route_name}}</h4> 
                    </th>
                    <th colspan="1" style="font-size: 11px; text-align:right;">
                        <h5>printed on : {{ \Carbon\Carbon::now()->toDateTimeString() }}</h5> 
                     </th>
                </tr>
            </tbody>
        </table>
        <table>
            <tr class="bordered">
                <th style="border:1px solid #000;">Total Customers : {{$route->customer_count}}</th>
                <th  style="border:1px solid #000;">Geomapped Customers : {{$route->geomapped_customer_count}}</th>
                <th  style="border:1px solid #000;">Unmapped: {{$route->customer_count - ($route->geomapped_customer_count ?? 0) }} </th>
            </tr>
        </table>
        <!-- Mapped Customers Table -->
        <h4>Mapped Customers</h4>
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">#</th>
                    <th style="text-align: left;">Center</th>
                    <th style="text-align: left;">Customer Name</th>
                    <th style="text-align: left;">Business Name</th>
                    <th style="text-align: left;">Phone</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $numbering = 0;
                @endphp
                @isset($data[$route->id])
                    @foreach ($data[$route->id]->where('is_mapped', true) as $customer)
                        <tr class="bordered">
                            <td>{{$numbering + 1}}</td>
                            <td>{{ $customer->center }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->bussiness_name }}</td>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        @php
                            $numbering ++;
                        @endphp
                    @endforeach
                @endisset
            </tbody>
        </table>

         <!-- Unmapped Customers Table -->
         <h4>Unmapped Customers</h4>
         <table>
             <thead>
                 <tr>
                     <th style="text-align: left;">#</th>
                     <th style="text-align: left;">Center</th>
                     <th style="text-align: left;">Customer Name</th>
                     <th style="text-align: left;">Business Name</th>
                     <th style="text-align: left;">Phone</th>
                 </tr>
             </thead>
             <tbody>
                @php
                    $numbering = 0;
                @endphp
                 @isset($data[$route->id])
                     @foreach ($data[$route->id]->where('is_mapped', false) as $customer)
                         <tr class="bordered">
                             <td>{{$numbering + 1}}</td>
                             <td>{{ $customer->center }}</td>
                             <td>{{ $customer->name }}</td>
                             <td>{{ $customer->bussiness_name }}</td>
                             <td>{{ $customer->phone }}</td>
                         </tr>
                         @php
                            $numbering++;
                         @endphp
                     @endforeach
                 @endisset
             </tbody>
         </table>

    </div>
    @if (!$loop->last)
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach
</body>
</html>
