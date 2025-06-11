@php
    $settings = getAllSettings();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debtor Transactions</title>
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
            font-size: 10px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 10px;
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
  
    <div class="invoice-box" style="margin-bottom: 15px">
        <table class="table no-border" style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th colspan="2" style="font-size:18px !important;font-weight: bold; text-align:left !important">
                        {{ $settings['COMPANY_NAME'] }}
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="2"
                        style="font-size:16px !important;font-weight: bold; text-align:left !important; margin:3px !important;">
                        
                    </th>
                </tr>

                <tr class="top" style="margin-bottom:5px !important;">
                    <th colspan="1" style="font-size:15px !important;font-weight: bold; text-align:left !important">
                        BULK SMS LOG REPORT
                    </th>
                    <th colspan="1"
                        style="font-size:15px !important;font-weight: bold; text-align:right !important; margin:3px !important;">
                        
                    </th>
                </tr>


            </tbody>
        </table>
    </div>
    <div class="invoice-box">
        <table width="100%">
        <tbody width="100%">                           
                <tr class="heading">
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">Date</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">ISSN</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">User's Number</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">Message Text</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">SMS Length</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">Category</th>
                    <th style="font-weight: 500; font-size: 10;text-align: center !important">Status</th>
                </tr>                        
                @foreach($logs as $log)
                    <tr class="item">
                        <td>{{ date('d-m-Y',strtotime($log->created_at)) }}</td>
                        <td>{{ $log->issn }}</td>
                        <td>{{ $log->phone_number }}</td>
                        <td>{{ $log->message}}</td>
                        <td>{{ $log->sms_length}}</td>
                        <td>{{ $log->category}}</td>
                        <td>{{ $log->send_status ? 'Sent': 'Fail'}}</td>
                    </tr>
                @endforeach
                        
                </tbody>
        </table>
     
    </div>   
</body>
</html>



