<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Adjustment</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            font-size: 12px;

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
                <tr class="top">
                    <th colspan="2">
                        <h2>{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
                <tr class="top">
                    <th  colspan="1">
                        VAT NO:{{getAllSettings()['VAT_NO']}}
                    </th>
                    <th colspan="1">
                        PIN NO: {{getAllSettings()['PIN_NO']}}
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1">ADJ NO: {{$data->document_no}}</th>
                    <th  colspan="1">DATE: {{date('d-M-Y',strtotime($data->created_at))}}</th>
                </tr>
            </tbody>        
        </table>
        <table>
            <tbody>
                <tr class="heading">
					<td style="width: 8%"> Item </td>                
					<td style="width: 30%"> Description </td>                
                    <td style="width: 10%"> Location </td>
                    <td style="width: 9%"> QtyBef </td>
                    <td style="width: 8%"> Adj Qty </td>
                    <td style="width: 10%"> New Qty </td>
                    <td style="width: 10%"> Value </td>
                    <td style="width: 15%"> Comment </td>
				</tr>
                @php
                    $gross_amount = 0;
                @endphp
                @foreach ($data->childs as $item)
                @php
                    $stkm = $stockmoves->where('stock_adjustment_id',$item->id)->first();
                @endphp
                    <tr class="item">
                        <td>{{@$item->item->stock_id_code}}</td>
                        <td>{{@$item->item->description}}</td>                      
                        <td>{{@$item->location->location_name}}</td>
                        <td>{{manageAmountFormat(@$stkm->new_qoh - $item->adjustment_quantity)}}</td>
                        <td>{{manageAmountFormat($item->adjustment_quantity)}}</td>
                        <td>{{manageAmountFormat(@$stkm->new_qoh)}}</td>
                        <td>{{manageAmountFormat($item->adjustment_quantity * @$stkm->selling_price)}}</td>
                        <td>{{$item->comments}}</td>
                    </tr>
                    @php
                    $gross_amount += ($item->adjustment_quantity * @$stkm->selling_price);
                @endphp
                @endforeach            
                
                <tr style="    border-top: 2px dashed #cecece;">
					<td colspan="8"></td>
				</tr>

                <tr class="heading">
					<td colspan="7" style="text-align: right">Grand Total</td>
					<td colspan="1"> {{manageAmountFormat($gross_amount)}}</td>
				</tr>
               
               
            </tbody>
        </table>
    </div>   
</body>
</html>