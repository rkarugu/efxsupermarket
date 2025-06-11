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
            margin: 0 !important;
            padding: 0 !important;
        }
        #receipt-main {
            padding: 0;
            margin: 0 !important;
            width: 100%;
        }

        #receipt-header {
            position: relative;
            width: 100%;
            text-align: center;
        }

        #receipt-header span {
            display: block;
            font-size: 40px;
        }

        .normal {
            display: block;
            font-size: 35px;
        }

        div.dashed {
            border-top: 5px dashed #000 !important;
            border-bottom: 5px dashed #000 !important;
            padding: 4px 0;
        }

        .order-item {
            font-size: 35px;
            width: 50%;
            float: left;
        }

        .customer-details .normal {
            font-size: 40px;
        }
        .table {
            width: 100%;
            font-size: 30px;
            border-collapse: collapse;
        }

        .table tr.heading td {
            border-bottom: 2px dotted #000;
            border-top: 2px dotted #000;
            font-weight: bold;
            padding: 10px 0;
        }

        .table tr.item td {
            padding: 8px 0;
            border-bottom: 1px dotted #000;
        }

        .table tr hr {
            border: none;
            border-bottom: 1px dotted #000;
            margin: 0;
        }
    </style>
</head>

<body>
<div id="receipt-main">
    <div id="receipt-header" >
        <h3 style="margin: 10px; padding: 0; font-size: 40px;"> {{ $settings['COMPANY_NAME'] }} </h3>
        <span> {{ $settings['ADDRESS_2']}} {{ $settings['ADDRESS_3']}} </span>
        <span> Tel: {{ $settings['PHONE_NUMBER']}} </span>
        <span> Email: {{ $settings['EMAILS']}} </span>
        <span> Website: {{ $settings['WEBSITE']}} </span>
        <span> PIN No: {{ $settings['PIN_NO']}} </span>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <h3 style="margin: 0; padding: 0; font-size: 45px;"> SMALL PACK DISPATCH </h3>
        <span class="normal" style="font-size: 40px; font-weight: 300">---{{ $dispatchInfo['document_no'] }} ---</span>
    </div>


    <div style="margin-top: 20px; text-align: center" class="customer-details">
        <span class="normal"> Time: {{ \Carbon\Carbon::now()->format('d/m/Y H:m:s') }} </span>
        @if ($dispatchInfo['route'])
            <span class="normal"> Route: {{$dispatchInfo['route']}} </span>
        @endif
        <span class="normal"> Center: {{$dispatchInfo['center']}} </span>
        @if ($dispatchInfo['print_count'] > 0)
            <span style="font-size:30px !important">REPRINT {{$dispatchInfo['print_count']+1}}</span>
        @endif
        <br>
    </div>
    <br>

    @foreach ($data as $item)
    <table style="margin-top:20px;!important;">
        <tr class="heading" style="font-size: 35px">
            <td>Bin: {{$item['title']}}</td>
        </tr>
    </table>
    
    <table class="table">
        <tbody>
        <tr class="heading" style="font-size: 35px">
            <td>#</td>
            <td >Item</td>
            <td >Qty</td>
        </tr>
        @foreach ($item['items'] as $key => $item)
            <tr class="" style="width: 100%">
                <td style="font-size: 29px">{{ $loop -> iteration }}.</td>
                <td style="font-size: 29px">
                    {{@$item['title']}}
                </td>
                <td style="font-size: 29px">{{@$item['totalQty']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endforeach
</div>
</body>

</html>
