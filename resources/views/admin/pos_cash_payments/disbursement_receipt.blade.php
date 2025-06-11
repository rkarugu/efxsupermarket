@php
    $settings = getAllSettings();
@endphp

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body {
            /*padding: 10px;*/
        }
        .invoice-box {
            margin: auto;
            font-size: 11px;
            line-height: 10px;
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
            padding-bottom: 10px;
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
            /*border-bottom: 1px solid #eee;*/
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

    <style>
        #receipt-main {
            padding: 0;
            margin: 0;
            width: 100%;
        }

        #receipt-header {
            position: relative;
            width: 100%;
            text-align: center;
        }

        #receipt-header span {
            display: block;
            font-size: 30px;
        }

        .normal {
            display: block;
            font-size: 30px;
        }

        div.dashed {
            border-top: 5px dashed #000 !important;
            border-bottom: 5px dashed #000 !important;
            padding: 4px 0;
        }

        .order-item {
            font-size: 30px;
            width: 50%;
            float: left;
        }

        .customer-details .normal {
            font-size: 30px;
        }
    </style>
</head>

<body>
<div id="receipt-main">
    <div id="receipt-header">
        <h3 style="margin: 10px; padding: 0; font-size: 35px;"> {{ $settings['COMPANY_NAME'] }} </h3>
        <span> {{ $settings['ADDRESS_2']}} {{ $settings['ADDRESS_3']}} </span>
        <span> Tel: {{ $settings['PHONE_NUMBER']}} </span>
        <span> Email: {{ $settings['EMAILS']}} </span>
        <span> Website: {{ $settings['WEBSITE']}} </span>
        <span> PIN No: {{ $settings['PIN_NO']}} </span>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <h3 style="margin: 0; padding: 0; font-size: 45px;"> Cash Payment Receipt </h3>
    </div>
    <br>
    <div class="normal" style="position: relative; width: 100%;">
        <div style="">
            <div style="position: relative; width: 50%; float: left;"> Reference</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;">{{ $cashPayment->doocument_no }} </div>
            <div style="clear: both;"></div>
        </div>
        <br>
        <div style="">
            <div style="position: relative; width: 50%; float: left;"> Time</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{ $cashPayment->disbursed_at }} </div>
            <div style="clear: both;"></div>
        </div>

        <hr>

        <div style="margin-top: 5px;">
            <div style="position: relative; width: 50%; float: left;"> Amount</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{ number_format($cashPayment ->amount,2 ) }} </div>
            <div style="clear: both;"></div>
        </div>

        <div style="margin-top: 5px;">
            <div style="position: relative; width: 50%; float: left;"> Reason </div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{$cashPayment->payment_reason ?? ''}} </div>
            <div style="clear: both;"></div>
        </div>

        <div>
            <div style="position: relative; width: 50%; float: left;"> Disbursed By</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{ $cashPayment->initiator->name}} </div>
            <div> Sign: ___________________ </div>
            <div style="clear: both;"></div>
        </div>
        <br>
        <hr>

        <div style="margin-top: 5px; font-weight: bold;">
            <div style="position: relative; width: 50%; float: left;"> Payee</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{ $cashPayment->recipient->name }} </div>
            <div> Sign: ___________________ </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>
</body>

</html>
