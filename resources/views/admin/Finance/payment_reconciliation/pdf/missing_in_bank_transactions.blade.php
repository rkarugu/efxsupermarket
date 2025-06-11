<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missing In Bank Transactions</title>
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
            line-height: 15px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 11px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 1px 2px 1px 2px;
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
            text-align: center;
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
        <table width="100%">
            <tr >
                <td colspan="15" align="left" style="font-weight: 700; font-size: 16px;text-align: left !important">Matching Transactions
             </td>
            </tr>
        </table> 
        <table width="100%" style="margin: 10px 0;">
            <tr style="border-bottom: 1px solid #ddd;">
                <td colspan="15" align="left" style="padding-bottom:5px; font-size: 16px;display: flex;">                
                    <span style="margin-right:15px;"><b>Start Date : </b>{{$payment->start_date}}</span>
          
                    <span style="margin-right:15px;"><b>End Date : </b>{{$payment->start_date}}</span>
           
                    <span style="margin-right:15px;"><b>Branch : </b>{{$payment->branch_name}}</span>
               
                    <span style="margin-right:15px;"><b>Channel : </b>{{$payment->channel}}</span>
                </td>
                <td colspan="4"></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr class="heading">
                    <th style="width: 3%;">#</th>
                    <th>Bank Date</th>
                    <th>Amount</th>
                    <th>Reference</th>
				</tr>
            </thead>
            <tbody>
                @foreach ($allMissingBankData as $item)
                    <tr class="details">
                        <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                        <td>{{$item->bank_date}}</td>
                        <td>{{manageAmountFormat($item->amount)}}</td>
                        <td>{{$item->reference}}</td>
                    </tr>
                @endforeach
        </table>
        
    </div>   
</body>
</html>