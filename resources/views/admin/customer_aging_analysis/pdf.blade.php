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
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="1">
                        <h2>{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="1" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                   
                </tr>
              
                <tr class="top">
                    <th  colspan="1">
                        AGE DEBTORS LIST FOR THE DATE : {{request()->date ? date('d/m/Y',strtotime(request()->date)) : date('d/m/Y')}}
                    </th>
                </tr>
            </tbody>        
        </table>
        <br>
        <br>
        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <th colspan="2" style="text-align: left;">A/C No.</th>
                    <th  style="text-align: right;">Outstanding Balance</th>
				</tr>
                <tr>
					<td colspan="3"  style="    border-bottom: 2px dashed #cecece;"></td>
				</tr>
                <?php $b = 1; $grandtotal=0; ?>
                @if(isset($lists) && !empty($lists))
                @foreach($lists as $list)
                    <tr class="item">
                        <td  style="text-align: left;">{!! $list->customer_code !!}</td>
                        <td style="text-align: left;">{!! $list->customer_name !!}</td>
                        <td style="text-align: right;">{!! number_format($list->total_amount_f,2) !!}</td>                                               
                    </tr>
                   <?php $b++; $grandtotal +=$list->total_amount_f; ?>
                @endforeach
                @endif
                <tr>
                    <td></td>
                    <td style="text-align: right;"> Grand Total </td>
                    <td style="text-align: right;">{!! number_format($grandtotal,2) !!}</td>
                </tr>
            </tbody>
        </table>
    </div>   
</body>
</html>