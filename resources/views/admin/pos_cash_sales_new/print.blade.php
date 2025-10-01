@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
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
                <!-- Company Name & Address (centered) -->
                <tr class="top">
                    <th colspan="3">
                        <h2 style="font-size:18px !important; font-weight: bold;">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="3" style="text-align: center; font-weight: bold;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
                <!-- Company phone number (centered) -->
                <tr class="top">
                    <td colspan="3" style="text-align: center; font-weight: bold;">Mobile: {{getAllSettings()['PHONE'] ?? '0740804489'}}</td>
                </tr>
                <!-- Horizontal Line -->
                <tr class="top">
                    <td colspan="3"><hr style="border: 1px solid #000; margin: 5px 0;"></td>
                </tr>
                <!-- Customer Details Section (left-aligned) -->
                <tr class="top">
                    <td colspan="3" style="text-align: left; font-weight: bold; padding: 10px 0;">
                        <div style="line-height: 1.4;">
                            Invoice No.: {{$data->sales_no}}<br>
                            Company PIN: {{getAllSettings()['PIN_NO']}}<br>
                            Customer PIN: {{$data->customer_pin ?? ''}}<br>
                            Customer Name: {{$data->customer}}<br>
                            Date: {{date('d/m/Y H:i',strtotime($data->date . ' ' . $data->time))}}<br>
                            Served By: {{@$data->user->name}}<br>
                            Telephone No: {{$data->customer_phone_number ?? ''}}<br>
                        </div>
                    </td>
                </tr>
                <!-- Horizontal Line -->
                <tr class="top">
                    <td colspan="3"><hr style="border: 1px solid #000; margin: 5px 0;"></td>
                </tr>
                @if ($data->print_count > 1)
                    <tr class="top">
                        <th  colspan="3" style="width: 100%;text-align:center">
                            <div style="display: flex">
                                <div style="width: 50%;text-align:center">
                                    <h2 style="font-size:18px !important">REPRINT {{$data->print_count-1}}</h2>
                                </div>
                                <div style="width: 50%;text-align:center">
                                    <h2 style="font-size:18px !important">REPRINT {{$data->print_count-1}}</h2>
                                </div>
                            </div>    
                        </th>
                    </tr>
                @endif
            </tbody>        
        </table>
        <table>
            <tbody>
                <tr class="heading">
					<td style="width: 5%; font-weight: bold;">No</td>
					<td style="width: 45%; font-weight: bold;">Description</td>
					<td style="width: 8%; font-weight: bold;">Qty</td>
					<td style="width: 10%; font-weight: bold;">Price</td>
					<td style="width: 12%; font-weight: bold;">Amount</td>
					<td style="width: 8%;text-align:right;padding-right:2px; font-weight: bold;">Disc</td>
					<td style="width: 8%;text-align:right;padding-right:2px; font-weight: bold;">Vat%</td>
					<td style="width: 10%; font-weight: bold;">Total</td>
				</tr>
                @php
                    $TONNAGE = $gross_amount = 0;
                    $itemNumber = 1;
                    $totalItems = count($data->items);
                @endphp
                @foreach ($data->items as $index => $item)
                    <tr class="item">
                        <td style="font-weight: bold;">{{$itemNumber}}</td>
                        <td style="font-weight: bold;">{{@$item->item->description}}</td>
                        <td style="font-weight: bold;">{{((int)$item->qty)}}</td>
                        <td style="font-weight: bold;">{{number_format($item->selling_price, 0)}}</td>
                        <td style="font-weight: bold;">{{number_format($item->qty*$item->selling_price, 0)}}</td>
                        <td style="font-weight: bold;">{{number_format($item->discount_amount, 0)}}</td>
                        <td style="font-weight: bold;">{{number_format($item->vat_percentage, 0)}}</td>
                        <td style="font-weight: bold;">{{number_format($item->qty*$item->selling_price, 0)}}</td>
                    </tr>
                    
                    @if($index < $totalItems - 1)
                        <!-- Horizontal Line between items -->
                        <tr>
                            <td colspan="9"><hr style="border: 1px solid #000; margin: 2px 0;"></td>
                        </tr>
                    @endif

                    @php
                        $TONNAGE += ($item->item->net_weight ?? 1) * $item->qty;
                        $gross_amount += ($item->qty*$item->selling_price) - $item->discount_amount;
                        $itemNumber++;
                    @endphp
                @endforeach
                </tbody>
        </table>
        <hr style="border: 1px solid #000; margin: 5px 0;">
        <table>
            <tbody>
                <tr >
					<td colspan="3" style="text-align: left; font-weight: bold;">No of Items:</td>
					<td colspan="2" style="text-align: right; font-weight: bold;">{{count($data->items)}}</td>
				</tr>
                <tr >
					<td colspan="3" style="text-align: left; font-weight: bold;">Subtotal:</td>
					<td colspan="2" style="text-align: right; font-weight: bold;">{{manageAmountFormat($gross_amount - ($data->items->sum('vat_amount') ?? 0.00))}}</td>
				</tr>
                <tr >
					<td colspan="3" style="text-align: left; font-weight: bold;">Discount:</td>
					<td colspan="2" style="text-align: right; font-weight: bold;">{{manageAmountFormat($data->items->sum('discount_amount') ?? 0.00)}}</td>
				</tr>
                <tr >
					<td colspan="3" style="text-align: left; font-weight: bold;">VAT:</td>
					<td colspan="2" style="text-align: right; font-weight: bold;">{{manageAmountFormat($data->items->sum('vat_amount') ?? 0.00)}}</td>
				</tr>
                <tr >
					<td colspan="3" style="text-align: left; font-weight: bold;">TOTAL:</td>
					<td colspan="2" style="text-align: right; font-weight: bold;">{{manageAmountFormat($gross_amount)}}</td>
				</tr>
                <tr >
					<td colspan="5"><hr style="border: 1px solid #000; margin: 5px 0;"></td>
				</tr>
                <tr >
					<td colspan="2" style="font-weight: bold;">TONNAGE : {{manageAmountFormat($TONNAGE)}}</td>
					<td colspan="1"></td>
					<td colspan="1" style="text-align: right; font-weight: bold;" >Total:  </td>
					<td colspan="1" style="font-weight: bold;">
                        {{manageAmountFormat($gross_amount)}}
                        <hr style="border: 1px dashed #979797;">
                    </td>
				</tr>
                <tr >
					<td colspan="3" style="font-weight: bold;">Amount in Words
                        <br>
                        {{strtoupper(getCurrencyInWords($gross_amount))}}
                    </td>
					<td colspan="1"></td>
					<td colspan="1" style="font-weight: bold;">Change: {{$data->change}}</td>
				</tr>
                <tr >
                    <td colspan="5"></td>
                </tr>
                <tr >
                    <td colspan="5"></td>
                </tr>
                <tr >
                    <td colspan="5"></td>
                </tr>
                {{--
                @if ($getLoggeduserProfile->upload_data == 0)
                    <tr >
                        <td colspan="5" style="text-align:center">{{($data->upload_data)}}</td>					
                    </tr>                    
                @endif
                --}}
            </tbody>
        </table>
    </div>   
</body>
</html>