@php
    $settings = getAllSettings();
@endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        body {
            font-family: "Helvetica Neue", sans-serif;
            font-size: 14px;
            color: #000000;

        }
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
            font-size: 14px;
        }

        .normal {
            display: block;
            font-size: 14px;
        }

        .bolder {
            display: block;
            font-size: 15px;
            font-weight: 700;
        }

        .order-item {
            font-size: 30px;
            width: 50%;
            float: left;
        }

        .customer-details .normal {
            font-size: 14px;
        }

        .table {
            width: 100%;
            font-size: 14px;
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
    <div id="receipt-header">
        <div style="width:100%; text-align:center;">
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorHTML();
                $barcode = $generator->getBarcode($data->sales_no, $generator::TYPE_CODE_128);
            @endphp
            <div style="display: inline-block; height: 50px">
                <div style="transform: scale(2);">
                    {!! $barcode !!}
                </div>
            </div>
            <br>
        </div>

        <h3 style="margin: 10px; padding: 0; font-size: 15px;"> {{ $settings['COMPANY_NAME'] }}</h3>
        <span> {{ $settings['ADDRESS_2']}} {{ $settings['ADDRESS_3']}} </span>
        <span> Tel: {{ $settings['PHONE_NUMBER']}} </span>
        <span> Email: {{ $settings['EMAILS']}} </span>
        <span> Website: {{ $settings['WEBSITE']}} </span>
        <span> PIN No: {{ $settings['PIN_NO']}} </span>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <h3 style="margin: 0; padding: 0; font-size: 15px;"> DISPATCH SHEET</h3>
        <span class="bolder" style="font-size: 15px"> Sale No.: {{ $data->sales_no }} </span>
        <br>
    </div>

    <div style="margin-top: 20px; text-align: center" class="customer-details">
        <span class="normal"> Time: {{ \Carbon\Carbon::now()->format('d/m/Y H:m:s') }} </span>
        <span class="normal"> Customer Name: {{ $data->customer }} </span>
        <span class="normal"> Customer Number: {{ $data-> customer_phone_number }} </span>
        <br>
    </div>

    <table class="table">
        <tbody>
        <tr class="heading">
            <td style="width: 10%;"></td>
            <td style="width: 80%;">Item</td>
            <td style="width: 10%;">Qty</td>
        </tr>
        @foreach ($data->items as $key => $datum)

            @foreach($datum as $item)
                @if ($loop->first)
                    <tr class="top">
                        <th  colspan="5" style="width: 100%;text-align:center">
                            <h2 style="font-size:12px !important">{{$item->item->getBin(getLoggeduserProfile()->wa_location_and_store_id)}}</h2>
                        </th>
                    </tr>
                @endif
                <tr class="item">
                    <td>{{ $loop -> iteration }}</td>
                    <td>{{@$item->item->description}}</td>
                    <td>{{@$item->qty}}</td>
                </tr>

            @endforeach

        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>