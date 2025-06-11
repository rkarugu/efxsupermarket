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
            margin-bottom: 20px;
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
            padding-bottom: 5px;
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

    <div class="invoice-box">
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2>KANINI HARAKA ENTERPRISES LTD</h2>
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1" style="font-size: 15px;">
                       <h3>Geomapping Summary - {{$branch->name}}</h3> 
                    </th>
                    <th colspan="1" style="font-size: 11px; text-align:right;">
                        <h5>printed on : {{ \Carbon\Carbon::now()->toDateTimeString() }}</h5> 
                     </th>
                </tr>
            </tbody>
        </table>
        <br>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th  style="width:30%;text-align:left">Route</th>
                    <th>Centres</th>
                    <th>Existing Customers</th>
                    <th>New Customers</th>
                    <th>Total Customers</th>
                    <th>Geomapped Customers</th>
                    <th> % Completion</th>
                </tr>
                <tr style="border-top: 2px dashed #cecece;">
                    <td colspan="9"></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $route)
                    <tr>
                        <th>{{ $loop->index + 1 }}</th>
                        <td style="text-align: left;">{{ $route->route_name }}</td>
                        <td style="text-align: center;">{{ $route->centre_count }}</td>
                        <td style="text-align: center;">{{ $route->customer_count - $route->new_customers }}</td>
                        <td style="text-align: center;">{{ $route->new_customers }}</td>
                        <td style="text-align: center;">{{ $route->customer_count }}</td>
                        <td style="text-align: center;">{{ $route->geomapped_customer_count }}</td>
                        <th style="text-align: center;">
                            {{($route->customer_count > 0) ? number_format(($route->geomapped_customer_count / $route->customer_count) * 100, 2) : 'N/A'}}
                        </th>


                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <th colspan="2" style="text-align: center;">Totals</th>
                <td style="text-align: center;">{{ $data->sum('centre_count') }}</td>
                <td style="text-align: center;">{{ $data->sum('customer_count') - $data->sum('new_customers') }}</td>
                <td style="text-align: center;">{{ $data->sum('new_customers') }}</td>
                <td style="text-align: center;">{{ $data->sum('customer_count') }}</td>
                <td style="text-align: center;">{{ $data->sum('geomapped_customer_count') }}</td>
                <th style="text-align: center;">
                    {{ number_format(($data->sum('geomapped_customer_count') / $data->sum('customer_count')) * 100, 2) }}
                </th>
                <th></th>


            </tfoot>

        </table>
        <br>
    </div>
</body>

</html>
