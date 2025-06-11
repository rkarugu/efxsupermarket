<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Advice</title>
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
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
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
        <table >
            <tbody>
                <tr class="top">
                    <th colspan="3"  style="text-align: center;">
                        <h2>REMITTANCE ADVICE</h2>
                    </th>
                </tr>
              
                <tr class="top">
                    <th  colspan="1">PAYEE : {{$supplier->name}}</th>
                    <th colspan="1"  style="text-align: center;">
                        CHEQ REQ NO. : {{@$request->cheque_number}}
                    </th>
                    <th colspan="1">
                        DOCNO : {{@$request->document_no}}
                    </th>
                </tr>
                <tr class="top">
                    <th  colspan="2">BANK : {{@$bank_account->account_name}}</th>
                    <th  colspan="1">DATE: {{date('d-M-Y',strtotime($request->date_paid))}}</th>
                </tr>

                <tr class="top">
                    <th  colspan="3">Please Find Here With Cheque No. {{@$request->cheque_number}}. FOR Slis: {{@$request->narrative}}</th>
                </tr>
            </tbody>        
        </table>
        <br>
        <br>
        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <th style="width:13%;text-align:left">Date</th>
                    <th style="width:13%;text-align:left">Type</th>
                    <th style="width:20%;text-align:left">Docno</th>
                    <th style="width:20%;text-align:right">Pending Amount</th>
                    <th style="width:20%;text-align:right;padding-right:8px">Amount Paid </th>
                    <th style="width:14%;text-align:left">Comment </th>
				</tr>
                @foreach($lists as $k=> $supptran)
                <tr class="item">
                    <td style="text-align:left">{{$supptran->trans_date}}</td>
                    <td style="text-align:left">{{@$supptran->getNumberSystem->code}}</td>
                    <td style="text-align:left">{{$supptran->document_no}}</td>
                    <td style="text-align: right">{{manageAmountFormat((float)$supptran->total_amount_inc_vat-$supptran->allocated_amount)}}</td>    
                    <td style="text-align: right;padding-right:8px">{{manageAmountFormat((float)(@$amount[$supptran->id] ?? 0))}}</td>
                    <td style="text-align:left">{{$supptran->description}}</td>
                </tr>
                @endforeach
                <tr class="">
                    <th colspan="4" style="text-align: right">Total Amount Paid</th>
                    <th colspan="1" style="text-align:right">{{manageAmountFormat((float)@array_sum($amount))}}</th>
                    <th colspan="1" ></th>
                </tr>
                <tr class="">
                    <th colspan="6" style="text-align: center">Please Acknowledge Receipt</th>
                </tr>
            </tbody>
        </table>
    </div>   
</body>
</html>