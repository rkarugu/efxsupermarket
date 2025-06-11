<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Cash Sales</title>
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
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}.</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">SUPREME STORE - GOODS RECEIPT NOTE</td>
                </tr>
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    {{-- <th colspan="1" style="width: 33%;text-align:left">
                        PIN NO: P051604625B
                    </th> --}}
                    <th colspan="1"  style="width: 33%;text-align:left">Receipt NO: {{$data->receive_code}}</th>
                    <th colspan="1" style="width: 33%;text-align:right">DATE: {{date('d-M-Y',strtotime($data->date))}}</th>

                </tr>
            </tbody>        
        </table>
        <table>
            <tbody>
                <tr class="heading">
					<td style="width: 15%;">Code</td>
					<td style="width: 40%;">Description</td>
					<td style="width: 25%;">Location</td>
					<td style="width: 20%;">Qty</td>
				</tr>
                @php
                    $TONNAGE = $gross_amount = 0;
                @endphp
                @foreach ($data->items as $item)
                    <tr class="item">
                        <td>{{@$item->item->stock_id_code}}</td>
                        <td>{{@$item->item->description}}</td>
                        <td>{{($item->location->location_name)}}</td>
                        <td>{{manageAmountFormat((int)$item->qty)}}</td>
                    </tr>

                    @php
                        $gross_amount += $item->qty;
                    @endphp
                @endforeach
                </tbody>
        </table>
        <table>
            <tbody>
                
                <tr >
					<td colspan="4"></td>
				</tr>

                <tr >
					<td colspan="2">{{count($data->items)}} Lines</td>
					<td style="text-align: right;" colspan="1">Total Quantity:</td>
					<td  colspan="1">{{manageAmountFormat($gross_amount)}}</td>
				</tr>
                
                <tr >
					<td colspan="4" style="text-align: left">Received By........................................................ </td>
				</tr>

                <tr >
					<td colspan="4" style="text-align: left">Confirmed By........................................................ </td>
				</tr>
            </tbody>
        </table>
    </div>   
</body>
</html>