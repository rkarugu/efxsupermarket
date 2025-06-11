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
            /* font-style: italic; */
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
<body style="width: 80%">

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2" >
                        <h2  style="    text-align: left;margin-bottom:4px">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="2" ><h3 style=" font-weight:bold;   text-align: left;margin-bottom:4px;margin-top:0px;font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;">Selling Report</h3></th>
                </tr>
                
                <tr class="top">
                    {{-- <th colspan="1" style="width:30%">Input By: {{$user->name}}</th> --}}
                    <th  colspan="2" style="text-align:left">DATE FROM: {{date('d-M-Y',strtotime(request()->date))}} | DATE TO {{date('d-M-Y',strtotime(request()->todate))}} | TIME: {{date('H:i A')}}</th>
                </tr>
            </tbody>        
        </table>
        <table >
            <thead>
                <tr class="heading">
					<td > Stock Item</td>
                    <td style="text-align: right"> QTY Sold </td>
                    <td style="text-align: right"> Total Value</td>
				</tr>
            </thead>
                <tbody>
               @php
                   $total_quantity = $sold_value = 0;
               @endphp
                @foreach ($fdata as $item)
               
                    <tr class="item">
                        <td>{{$item->stock_id_code}}</td>
                        <td style="text-align: right">{{manageAmountFormat(abs($item->total_quantity))}}</td>
                        <td style="text-align: right">{{manageAmountFormat($item->sold_value)}}</td>
                        @php
                   $total_quantity += abs($item->total_quantity);
                   $sold_value += $item->sold_value;
               @endphp
                    </tr>
                @endforeach   
                <tr style="    border-top: 2px dashed #cecece;">
					<td colspan="3"></td>
				</tr>         
                <tr>
                    <th style="text-align:left">Total</th>
                    <th style="text-align:right" colspan="1">{{manageAmountFormat($total_quantity)}}</th>
                    <th style="text-align:right" colspan="1">{{manageAmountFormat($sold_value)}}</th>
                  
                </tr>
                <tr>
					<td colspan="3" style="    border-bottom: 2px dashed #cecece;"></td>
				</tr>
                {{-- <tr>
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

				</tr> --}}
            </tbody>
        </table>
       
    </div>   
</body>
</html>