@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Entry</title>
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
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 12px;
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

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="1" style="width: 50%;text-align:left">
                        PIN NO: {{getAllSettings()['PIN_NO']}}
                    </th>
                    <th colspan="1"  style="width: 50%;text-align:right">DATED: {{date('d/M/Y')}}</th>

                </tr>
             
                <tr class="top">
                    <th colspan="2"  style="width: 100%;text-align:center;font-size:18px !important">Journal Voucher</th>
                </tr>
                <tr class="top">
                    <th colspan="1"  style="width: 50%;text-align:left">JV:  {{@$grns->first()->transaction_no}}</th>
                    <th colspan="1"  style="width: 50%;text-align:right">Posted By:  {{@$grns->first()->user->name}}</th>
                </tr>
              
            </tbody>        
        </table>
        <table>
            <tbody>
                <tr class="heading">
					<th style="width: 8%">Date</th>
                    <th style="width: 20%">Reference</th>
                    <th style="width: 8%">Account</th>
                    <th style="width: 14%">Name</th>
                    <th style="width: 15%">Debit</th>
                    <th style="width: 15%">Credit</th>
                    <th style="width: 20%">Narration</th>
				</tr>
                @foreach ($grns as $item)
                    <tr>
                        <td>{{$item->date}}</td>
                        <td>{{$item->supplier_account_number}}</td>
                        <td>{{$item->account}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->debit}}</td>
                        <td>{{$item->credit}}</td>
                        <td style="text-align: left">{{$item->narrative}}</td>
                    </tr>
                @endforeach
                </tbody>
        </table>
        <table>
            <tbody>
                
                <tr >
					<td colspan="7"></td>
				</tr>

                <tr >
					<th colspan="7">{{count($grns)}} Lines</th>
				
				</tr>
                <tr >
					<td colspan="7"></td>
				</tr>

                <tr >
					<td colspan="7"></td>
				</tr>

                <tr >
					<th colspan="7" >Signed By..................................................................................................................................................................</th>
				</tr>

            </tbody>
        </table>
    </div>   
</body>
</html>