@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Slip</title>
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
            /* border-bottom: 1px solid #ddd; */
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

<?php $all_settings = getAllSettings();
//  echo print_r($all_settings); die;

?>

<div class="invoice-box">
    <table  style="text-align: center;">
        <tbody>
        <tr class="top">
            <td colspan="4" style="text-align: center; font-size: 20px;">
                {!! strtoupper($all_settings['COMPANY_NAME'])!!}
                <br>{!! strtoupper($all_settings['ADDRESS_1'])!!}
                <br>TEL: {!! $all_settings['PHONE_NUMBER'] !!}
                <br>{!!$all_settings['PIN_NO']!!}
                <br>
            </td>
        </tr>

        <tr style="margin-top: 60px !important;">
                <td style="font-size:13px !important;text-weight:bold">Customer name::<br> {{$data->customer}} </td>
                <td colspan="3"  style="font-size:13px !important;text-weight:bold"> CASH SALE NO:: <br> {{$data->sales_no}}</td>
                <td  colspan="4"  style="font-size:13px !important;text-weight:bold"> DATE: <br>{{date('d-M-Y',strtotime($data->date))}} {{date('H:i A',strtotime($data->updated_at))}}</td>
        </tr>


        <tr style="width:100%;">
            <td colspan="10"><hr class="new4"></td>
        </tr>
        <tr class="top" style="text-align: left">
            <td style="text-align: left;  font-size: 15px; text-weight:bold " >
                Items:  {{ $data ->items -> count() }}
            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="10"><hr class="new4"></td>
        </tr>
        </tbody>
    </table>
    <div style="text-align: left">
        <p>
            You were served by: {{ $data -> user -> name }} <br>
            E&OE GOODS SOLD ARE NOT RE-ACCEPTED <br>
            Thank you for shopping with Us  <br>
        </p>
    </div>

</div>
</body>
</html>