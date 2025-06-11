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
            <td colspan="2" style="text-align: center;">
                {{ $all_settings['PHONE_NUMBER'] }} | {{ $all_settings['EMAILS'] }} | {{ $all_settings['WEBSITE'] }}
            </td>
        </tr>
        <tr class="top">
            <td colspan="2" style="text-align: center;">
                PIN NO: {{ $all_settings['PIN_NO'] }}
            </td>
        </tr>

            <tr class="top">
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr class="top">
                <th colspan="2" style="text-align: center">
                    <span style="font-size:18px !important">SHIFT RETURNS</span>
                </th>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
        <tr class="top">
            @php
                // $customer = $list->get_customer;
                // $shift_id = \App\SalesmanShift::find($list->shift_id);
            @endphp
            <th colspan="1" style="text-align: left;">Return No: {!! $transfer_no!!}</th>
        </tr>
        <tr class="top">
            {{-- <th colspan="1" style="text-align: left;">Route: {!! @$shift_id->shift_id !!}</th> --}}
        </tr>
        <tr class="top">
            {{-- <th colspan="1" style="text-align: left;">Salesman: {{$list->customer_pin}}</th> --}}
        </tr>
        <tr class="top">
            {{-- <th colspan="2" style="text-align: left;">Shift  Date: {{$list->customer_phone_number}}</th> --}}
        </tr>
        </tbody>
    </table>

    <br>
    @php
    $totalreceived =0;
    $qtyreceived =0;
    @endphp
    <table>
        <tbody>
        <tr class="heading">
            <td >#</td>
            <td>Initiated On</td>
            <td>Received On</td>
            <td >Title</td>
            <td>Initiated By</td>
            <td >Return Qty</td>
            <td >Received Qty</td>
            <td >Price </td>
            <td >Total</td>
            {{-- <td >Amount</td> --}}
           
        </tr>
      
        @foreach($list as $item)
            @php
            $totalreceived += ($item->selling_price) * ($item->received_quantity ?? '0');
            $qtyreceived += (int)$item->received_quantity;
            @endphp
            <tr class="item">
                <td>{{$loop->index+1}}</td>
                <td>{{$item->return_date}}</td>
                <td>{{$item->processing_date}}</td>
                <td>{{$item->item_name}}</td>
                <td>{{$item->initiator}}</td>
                <td style="text-align: center;">{{ (int)$item->return_quantity }}</td>
                <td style="text-align: center;">{{(int)$item->received_quantity}}</td>
                <td>{{number_format($item->selling_price,2 )}}</td>
                <td>{{number_format(($item->selling_price) * ($item->received_quantity ?? '0'), 2) }}</td>
                
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr > 
                <td colspan="8" style="float:right"><strong> Total </strong></td>
               
                <td colspan="1"><strong > {{ number_format($totalreceived,2 )}} </strong></td>
            </tr>
        </tfoot>
    </table>

</div>

</body>
</html>
