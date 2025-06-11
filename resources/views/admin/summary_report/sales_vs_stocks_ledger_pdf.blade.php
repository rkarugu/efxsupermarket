<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        .horizontal_dotted_line {
            display: flex;
        }

        .horizontal_dotted_line:after {
            border-bottom: 2px dashed #b2b2b2;
            ;
            content: '';
            flex: 1;
        }

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
            font-size: 12px;
            line-height: 25px;
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
            /* background: #eee; */
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        /* .invoice-box table tr.item td:last-child {
            border-bottom: 1px solid #eee;
        } */

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
    <?php $all_settings = getAllSettings(); ?>
    <div class="invoice-box">
        <table style="width: 100%; margin-bottom: 20px">
            <tbody>
                <tr>
                    <th colspan="2">
                        <h2 style="text-align: left; margin:0">{!! strtoupper($all_settings['COMPANY_NAME']) !!}</h2>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4 style="text-align: left; margin:0">SALES VS STOCKS LEDGER</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">{{ is_null($branch) ? '' : "BRANCH: $branch->name" }}
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4 style="text-align: left; margin:0">Input By: {{ $user->name }}</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">DATE FROM:
                            {{ date('d-M-Y', strtotime(request()->date)) }} |
                            DATE TO {{ date('d-M-Y', strtotime(request()->todate)) }} | TIME: {{ date('H:i A') }}</h4>
                    </th>
                </tr>
            </tbody>
        </table>
        <table>
            <thead>
                <tr class="heading">
                    <td style="text-align:right">SALES LEDGER</td>
                    <td style="text-align:right">STOCKS LEDGER </td>
                    <td style="text-align:right">VARIANCE</td>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $salesInvoices = 0;
                    $salesReturns = 0;   
                    ?>
                    @foreach ($data as $item)
                        @php
                            $salesInvoices += $item->vcs;
                            $salesReturns += $item->returns  
                            
                        @endphp
                        
                    @endforeach
                <tr class="item" style="border-bottom: 2px solid black; border-top: 2px solid black;"> 
                    <th style="text-align:right">{{ manageAmountFormat($salesInvoices - $salesReturns) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($salesLedgerInvoices - $salesLedgerReturns) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat(($salesInvoices - $salesReturns) - ($salesLedgerInvoices - $salesLedgerReturns) )   }}</th>
                </tr>
        
            </tbody>
        </table>
        <br>
      
          
   
    </div>
</body>

</html>
