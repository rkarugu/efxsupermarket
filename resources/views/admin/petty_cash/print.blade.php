<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash</title>
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
                <?php $settings = getAllSettings(); ?>
                <tr class="top">
                    <th colspan="2">
                        <h2>{{$settings['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">{{$settings['ADDRESS_1']}} {{$settings['ADDRESS_2']}} {{$settings['ADDRESS_3']}}. TEL {{$settings['PHONE_NUMBER']}}</td>
                </tr>
                <tr class="top">
                    <th  colspan="1">
                        VAT NO:
                    </th>
                    <th colspan="1">
                        PIN NO: {{$settings['PIN_NO']}}
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1">PETTY CASH NO: {{$data->petty_cash_no}}</th>
                    <th  colspan="1">DATE: {{date('d-M-Y',strtotime($data->created_at))}}</th>
                </tr>
                <tr class="top">
                    <th colspan="2" style="text-align:left">
                        <span class="form-control">Bank: {{@$data->bank_account->account_number}}</span> <br />
                        <span class="form-control">Payment Date: {{date('Y-m-d',strtotime($data->payment_date))}}</span>  <br />
                        <span class="form-control">Payment Method: {{@$data->payment_method->title}}</span> 
                    </th>
                </tr>
            </tbody>        
        </table>
        <br>
        <table>
            <tbody>
                <tr class="heading">
					<td style="width: 20%"> Account No. </td>
                
                    <td style="width: 20%"> Branch </td>
                    <td style="width: 20%"> Payment For </td>
                    <td style="width: 20%"> Collected By </td>
                    <td style="width: 20%"> Amount </td>
				</tr>
                @php
                    $gross_amount = 0;
                @endphp
                @foreach ($data->items as $item)
                    <tr class="item">
                        <td>{{@$item->chart_of_account->account_name}}</td>
                      
                        <td>{{@$item->branch->name}}</td>
                        <td>{{$item->payment_for}}</td>
                        <td>{{$item->collected_by}}</td>
                        <td>{{manageAmountFormat($item->amount)}}</td>
                    </tr>
                    @php
                    $gross_amount += $item->amount;
                @endphp
                @endforeach            
                
                <tr style="    border-top: 2px dashed #cecece;">
					<td colspan="5"></td>
				</tr>

                <tr >
					<td colspan="2">Amount in Words
                        <br>
                        {{strtoupper(getCurrencyInWords($gross_amount))}}
                    </td>
					<td colspan="1">A/C Balance : 0.00</td>
					<td colspan="2">Total Amount: {{manageAmountFormat($gross_amount)}}</td>
				</tr>
               
                <tr >
					<td colspan="1" style="text-align: right">Customer Sign:  </td>
					<td colspan="4" style="border-bottom:2px dashed rgb(153, 153, 153)"></td>
				</tr>
                <tr >
					<td colspan="1" style="text-align: right">Cashiers Sign:  </td>
					<td colspan="4" style="border-bottom:2px dashed rgb(153, 153, 153)"></td>
				</tr>
                <tr >
					<td colspan="1" style="text-align: right">Managers Sign:  </td>
					<td colspan="4" style="border-bottom:2px dashed rgb(153, 153, 153)"></td>
				</tr>
            </tbody>
        </table>
    </div>   
</body>
</html>