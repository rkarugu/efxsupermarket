<html>
<title>Print</title>

<head>
    <style type="text/css">
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

        .invoice-box * {
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
            font-size: 40px;
            line-height: 40px;
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
            /* border-bottom: 1px solid #eee; */
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

<?php $all_settings = getAllSettings();
$getLoggeduserProfile = getLoggeduserProfile();
?>
<div class="invoice-box">
    <table style="text-align: center;">
        <tbody>
        <tr class="top">
            <th colspan="2">
                <span style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</span>
            </th>
        </tr>

        <tr class="top">
            <td colspan="2" style="text-align: center;">
                {{ $all_settings['ADDRESS_1'] }}, {{ $all_settings['ADDRESS_2'] }}
            </td>
        </tr>

        <tr class="top">
            <th colspan="2">
                <span >Stock Return (Excess)</span>
            </th>
        </tr>
        </tbody>
    </table>

    <table>
        <tbody>
        <tr class="top">
            
            <th colspan="1" style="text-align: left;"> </th>
            <th colspan="1" style="text-align: right;"></th>
        </tr>
        <tr class="top">
            <th colspan="1" style="text-align: left;">Customer Name: {{$list->debtor->employee->name}} </th>
            <th colspan="1" style="text-align:right">Document NO: {!! $list->document_no!!}</th>
        </tr>
        <tr class="top">
            <th colspan="1" style="text-align: left;">Customer Phone Number: {{$list->debtor->employee->phone_number}} </th>
            <th colspan="1" style="text-align:right">DATE: {!! date('Y-m-d H:i:s',strtotime($list->created_at))!!}</th>
        </tr>
        <tr class="top">
            <th colspan="2" style="text-align: left;"></th>
        </tr>
        </tbody>
    </table>

    <br>

    <table>
        <tbody>
        <tr class="heading">
            <td style="width: 10%;">Code</td>
            <td style="width: 31%;">Description</td>
            <td style="width: 6%;">Qty</td>
            <td style="width: 12%;">Price</td>
            <td style="width: 12%;">Amount</td>
            <td style="width: 8%;">Vat%</td>
            <td style="width: 11%;">Total</td>
        </tr>
        @php
            $gross_amount = 0;
            $netAmount = 0;
            $totalVat = 0;
            
        @endphp
        @foreach($list->items as $item)
        @php
        
            $price= abs($item->price);
        @endphp
            <tr class="item">
                <td>{{$item->inventoryItem->stock_id_code}}</td>
                <td>{{$item->inventoryItem->title}}</td>
                <td>{{floor($item->quantity)}}</td>
                <td>{{manageAmountFormat($price)}}</td>
                <td>{{manageAmountFormat($item->quantity*$price)}}</td>
                <td>{{$item->vat_percentage}}</td>
                <td>{{manageAmountFormat(abs($item->total))}}</td>
            </tr>

            @php
                $gross_amount += (($item->quantity*$price)-0);
                $vatRate = (float)$item->vat_percentage;
                if ($vatRate) {
                    $totalVat += (($vatRate / (100 + $vatRate)) * $price) * $item->quantity;
                }
            @endphp
        @endforeach
        </tbody>
    </table>

    <table>
        <tbody>
        <tr style="border-top: 2px dashed #cecece;">
            <td colspan="5"></td>
        </tr>

        <tr>
            <td style="width: 41%;"></td>
            <td style="width: 30%;"></td>
            <td style="width: 15%;text-align: right;">Gross Amount:</td>
            <td style="width: 11%;">{{manageAmountFormat($gross_amount)}}</td>
        </tr>
        <tr>
            <td style="width: 41%;"></td>
            <td style="width: 40%;"></td>
            <td style="width: 8%;text-align: right;">Net Amount:</td>
            <td style="width: 11%;">{{ manageAmountFormat(($gross_amount - $totalVat) ?? 0.00) }}</td>
        </tr>
        <tr>
            <td style="width: 41%;"></td>
            <td style="width: 40%;"></td>
            <td style="width: 8%;text-align: right;">V.A.T:</td>
            <td style="width: 11%;">{{manageAmountFormat($totalVat ?? 0.00)}}</td>
        </tr>
        <tr>
            <td style="width: 41%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 8%;text-align: right;">Total:</td>
            <td style="width: 15%;">{{manageAmountFormat($gross_amount)}}</td>
        </tr>
        </tbody>
    </table>


    

</div>

</body>
</html>
