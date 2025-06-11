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
            font-size: 35px;
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
            font-size: 35px;
        }
        .table {
            width: 100%;
            font-size: 25px;
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
        <h3 style="margin: 10px; padding: 0; font-size: 35px;"> {{ $settings['COMPANY_NAME'] }} </h3>
        <span> {{ $settings['ADDRESS_2']}} {{ $settings['ADDRESS_3']}} </span>
        <span> Tel: {{ $settings['PHONE_NUMBER']}} </span>
        <span> Email: {{ $settings['EMAILS']}} </span>
        <span> Website: {{ $settings['WEBSITE']}} </span>
        <span> PIN No: {{ $settings['PIN_NO']}} </span>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <h3 style="margin: 0; padding: 0; font-size: 45px;"> DISPATCH </h3>
        <span class="normal" style="font-size: 35px; font-weight: 300">--- {{ $data->sales_no }} ---</span>
        @if ($data->dispatch_print_count > 1)
            <span style="font-size:15px !important; font-weight: bold">REPRINT {{$data->dispatch_print_count-1}}</span>
        @endif
    </div>


    <div style="margin-top: 20px; text-align: center" class="customer-details">
        <span class="normal"> Sale No.: {{ $data->sales_no }} </span>
        <span class="normal"> Time: {{$data->paid_at->format('d/m/y  H:i A') }} </span>
        <span class="normal"> Customer Name: {{ $data->customer }} </span>
        <span class="normal"> Customer Number: {{ substr($data-> customer_phone_number, 0, 2) . ' *****' . substr($data-> customer_phone_number, -2) }} </span>
        <br>
    </div>
    <br>

    <table class="table">
        <tbody>
        <tr class="heading" style="font-size: 35px">
            <td>#</td>
            <td >Item</td>
            <td >Qty</td>
        </tr>
        @foreach ($data->items as $key => $datum)
           
            @foreach($datum as $item)
                @if ($loop->first)
                    <tr class="top">
                        <th  colspan="6" style="width: 100%;text-align:center">
                            <h2 style="font-size:30px !important">{{$item->item->getBin(getLoggeduserProfile()->wa_location_and_store_id)}}</h2>
                        </th>
                    </tr>
                @endif
                <tr class="" style="width: 100%">
                    <td style="font-size: 25px">{{ $loop -> iteration }}.</td>
                    <td style="font-size: 25px">{{@$item->item->description}}</td>
                    <td style="text-align: right; font-size: 25px">{{@$item->qty}}</td>
                </tr>

            @endforeach

        @endforeach
        </tbody>
    </table>

    <table style="margin-top: 20px;" class="table">
        <tbody>

        <tr style="width:100%;">
            <td colspan="5" style="text-align:center !important">
                Cashier: <b>{{@$data->attendingCashier->name}}</b>

            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>

</html>
