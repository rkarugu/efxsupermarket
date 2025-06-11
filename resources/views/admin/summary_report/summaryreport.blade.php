<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Report</title>
    <style>
        .horizontal_dotted_line {
  display: flex;
} 
.horizontal_dotted_line:after {
  border-bottom: 2px dashed #b2b2b2;;
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

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2>{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">
                {!! strtoupper(getAllSettings()['ADDRESS_1'])!!} {!! strtoupper(getAllSettings()['ADDRESS_3'])!!}, {!! strtoupper(getAllSettings()['ADDRESS_3'])!!},{!! getAllSettings()['PHONE_NUMBER'] !!}</td>
                </tr>
                
                <tr class="top">
                    <th colspan="1" style="width:30%">Input By: {{$user->name}}</th>
                    <th  colspan="1" style="width:70%">DATE FROM: {{date('d-M-Y',strtotime(request()->date))}} | DATE TO {{date('d-M-Y',strtotime(request()->todate))}} | TIME: {{date('H:i A')}}</th>
                </tr>
            </tbody>        
        </table>
        <table>
            <thead>
                <tr class="heading">
					<td > User</td>
                    <td > Cash Sales </td>
                    <td >Cash Sales Return</td>
                    <td > Invoices </td>
                    <td > Invoices Return </td>
                    <td > Total </td>
                    <td > Petty Cash </td>
                    <td > C/Rec </td>
                    <td > Net Cash </td>
				</tr>
            </thead>
                <tbody>
                @php
                    $cash_sales = $cash_sales_returns = $invoices = $invoices_return = $petty_cash = $customer_receipt = 0;
                @endphp
                @foreach ($data as $item)
                @if($item->cash_sales != '' || $item->cash_sales_returns != '' || $item->invoices != '' || $item->customer_receipt != '' || $item->invoices_return != '' || $item->petty_cash != '')
                    <tr class="item">
                        <td>{{$item->name}}</td>
                        <td>{{manageAmountFormat($item->cash_sales + $item->pos_cash_sales_returns)}}</td>
                        <td>{{manageAmountFormat($item->cash_sales_returns)}}</td>
                        <td>{{manageAmountFormat($item->invoices)}}</td>
                        <td>{{manageAmountFormat($item->invoices_return)}}</td>
                        @php 
                        $total = 0;
                        $total = (($item->cash_sales ?? 0.00) + ($item->pos_cash_sales_returns ?? 0.00)) + ($item->invoices ?? 0.00) - ($item->cash_sales_returns ?? 0.00) - ($item->invoices_return ?? 0.00);
                        @endphp
                        <td>{{manageAmountFormat($total)}}</td>
                        <td>{{manageAmountFormat($item->petty_cash)}}</td>
                        <td>{{manageAmountFormat(abs($item->customer_receipt))}}</td>
                        @php 
                        $netcash = 0;
                        $netcash = (((($item->cash_sales ?? 0.00) + ($item->pos_cash_sales_returns ?? 0.00)) - ($item->cash_sales_returns ?? 0.00)) - ($item->petty_cash ?? 0.00)) + abs($item->customer_receipt) ;
                        $cash_sales += (($item->cash_sales ?? 0.00) + ($item->pos_cash_sales_returns ?? 0.00));
                        $cash_sales_returns += ($item->cash_sales_returns ?? 0.00);
                        $invoices += ($item->invoices ?? 0.00);
                        $invoices_return += ($item->invoices_return ?? 0.00);
                        $petty_cash += ($item->petty_cash ?? 0.00);
                        $customer_receipt += abs($item->customer_receipt ?? 0.00);
                        @endphp
                        <td style="text-align:right">{{manageAmountFormat($netcash)}}</td>
                    </tr>
                @endif
                @endforeach   
                <tr style="    border-top: 2px dashed #cecece;">
					<td colspan="9"></td>
				</tr>         
                <tr>
                    <th style="text-align:left">Total</th>
                    <th style="text-align:left">{{manageAmountFormat($cash_sales)}}</th>
                    <th style="text-align:left">{{manageAmountFormat($cash_sales_returns)}}</th>
                    <th style="text-align:left">{{manageAmountFormat($invoices)}}</th>
                    <th style="text-align:left">{{manageAmountFormat($invoices_return)}}</th>
                    @php 
                    $total1 = 0;
                    $total1 = ($cash_sales ?? 0.00) + ($invoices ?? 0.00) - ($cash_sales_returns ?? 0.00) - ($invoices_return ?? 0.00);
                    @endphp
                    <th style="text-align:left">{{manageAmountFormat($total1)}}</th>
                    <th style="text-align:left">{{manageAmountFormat($petty_cash)}}</th>
                    <th style="text-align:left">{{manageAmountFormat(abs($customer_receipt))}}</th>
                    @php 
                    $netcash1 = 0;
                    $netcash1 = ((($cash_sales ?? 0.00) - ($cash_sales_returns ?? 0.00)) - ($petty_cash ?? 0.00)) + ($customer_receipt) ;                    
                    @endphp
                    <th style="text-align:right">{{manageAmountFormat($netcash1)}}</th>
                </tr>
                <tr>
					<td colspan="9" style="    border-bottom: 2px dashed #cecece;"></td>
				</tr>
                <tr>
					<td colspan="2" >
                        <div class="horizontal_dotted_line">    
                            Done By:
                        </div>
                    </td>
                    <td colspan="1"></td>
					<td colspan="2"><div class="horizontal_dotted_line">    Checked By</div></td>
                    <td colspan="1"></td>
					<td colspan="2"><div class="horizontal_dotted_line">    Approved:</div></td>
                    <td colspan="1"></td>
				</tr>
            </tbody>
        </table>       
    </div>   
</body>
</html>