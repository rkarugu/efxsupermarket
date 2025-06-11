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
            font-size: 50px;
        }

        .normal {
            display: block;
            font-size: 50px;
        }

        div.dashed {
            border-top: 5px dashed #000 !important;
            border-bottom: 5px dashed #000 !important;
            padding: 4px 0;
        }

        .order-item {
            font-size: 50px;
            width: 50%;
            float: left;
        }

        .customer-details .normal {
            font-size: 45px;
        }
    </style>
</head>

<body>
<div id="receipt-main">
    <div id="receipt-header">
        <h3 style="margin: 10px; padding: 0; font-size: 40px;"> {{ $settings['COMPANY_NAME'] }} </h3>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <h3 style="margin: 0; padding: 0; font-size: 45px;"> SHIFT RETURNS </h3>
    </div>

    <div style="margin-top: 20px;" class="customer-details">
    <span class="normal"> Return No: {{ $returnNumber }} </span>
        <span class="normal"> Route: {{ $shift->route }} </span>
        <span class="normal"> Salesman: {{ $shift->salesman->name }} </span>
        <span class="normal"> Shift Date: {{ $shift->shift_date }} </span>
    </div>

    <br>
    <br>

    <div style="padding: 5px 0;">
        @foreach($returnRecords as $index => $item)
            <div style="position: relative; width: 100%; margin-top: 10px; border-bottom: 5px dashed black;" class="order-item-main">
                <div style="position: relative; width: 100%;" class="normal"> {{ $index + 1 }}. {{ ucwords(strtolower($item->item_name)) }} </div>
                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 30%;"> Date </div>
                    <div class="order-item" style="text-align: right; width: 70%;"> {{ \Carbon\Carbon::parse($item->return_date)->format('Y-m-d H:i:s') }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 30%;">  By </div>
                    <div class="order-item" style="text-align: right; width: 70%;"> {{ ucwords(strtolower($item->initiator)) }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item"> Returned Qty </div>
                    <div class="order-item" style="text-align: right;"> {{ $item->return_quantity }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 80%;"> Received By Store </div>
                    <div class="order-item" style="text-align: right; width: 20%;"> {{ $item->received_quantity }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item"> Return Total </div>
                    <div class="order-item" style="text-align: right;"> {{ number_format($item->total, 2) }} </div>

                    <div style="clear: both;"></div>
                </div>
            </div>
        @endforeach
    </div>

    <br>

    <div class="normal" style="margin-top: 10px; position: relative; width: 100%; border-bottom: 5px solid #000 !important">
        <div style="margin-top: 5px; font-weight: bold;">
            <div style="position: relative; width: 50%; float: left;"> Total</div>
            <div style="position: relative; width: 50%; float: left; text-align: right;"> {{ number_format($returnTotal, 2) }} </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <br>

    @if($esdData)
        <div style="width:100%; text-align:center;">
            <img src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($esdData->verify_url)) !!} " alt="">
            <br>
            <span class="normal"> {{ $esdData->cu_serial_number }}</span>
            <span class="normal"> CU Invoice Number : {{ $esdData->cu_invoice_number }}</span>
        </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 50px;">
        <span> &copy; {{ \Carbon\Carbon:: now()->year }}. RetailPay. </span>
    </div>
</div>
</body>

</html>
