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
        <h3 style="margin: 0; padding: 0; font-size: 45px;"> SALESMAN STATEMENT </h3>
    </div>

    <div style="margin-top: 20px;" class="customer-details">
        <span class="normal"> Route: {{ $route->route_name }} </span>
        <span class="normal"> Salesman: {{ $user->name }} </span>
        <span class="normal"> Start Date: {{ $startDate }} </span>
        <span class="normal"> End Date: {{ $startDate }} </span>
    </div>

    <br>
    <br>

    <div class="normal" style="margin-top: 10px; position: relative; width: 100%; border-bottom: 5px solid #000 !important">
        <div style="margin-top: 5px; font-weight: bold;">
            <div style="position: relative; width: 60%; float: left;"> Opening Balance</div>
            <div style="position: relative; width: 40%; float: left; text-align: right;"> {{ number_format($openingBalance, 2) }} </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <br>

    <div style="padding: 5px 0;">
        @foreach($transactions as $index => $trans)
            <div style="position: relative; width: 100%; margin-top: 10px; border-bottom: 5px dashed black;" class="order-item-main">
                <div style="position: relative; width: 100%;" class="normal"> {{ $index + 1 }}. {{ $trans->reference }} </div>
                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 35%;"> {{ $trans->document_no }}</div>
                    <div class="order-item" style="text-align: right; width: 65%;"> {{ $trans->date }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 30%;"> Amount</div>
                    <div class="order-item" style="text-align: right; width: 70%;"> {{ number_format($trans->amount, 2) }} </div>

                    <div style="clear: both;"></div>
                </div>

                <div style="position: relative; width: 100%;">
                    <div class="order-item" style="width: 30%;"> Balance</div>
                    <div class="order-item" style="text-align: right; width: 70%;"> {{ number_format($trans->balance, 2) }} </div>

                    <div style="clear: both;"></div>
                </div>
            </div>
        @endforeach
    </div>

    <br>

    <div class="normal" style="margin-top: 10px; position: relative; width: 100%; border-bottom: 5px solid #000 !important">
        <div style="margin-top: 5px; font-weight: bold;">
            <div style="position: relative; width: 60%; float: left;"> Closing Balance</div>
            <div style="position: relative; width: 40%; float: left; text-align: right;"> {{ number_format($closingBalance, 2) }} </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <br>

    <div style="margin-top: 40px; text-align: center; font-size: 50px;">
        <span> &copy; {{ \Carbon\Carbon:: now()->year }}. RetailPay. </span>
    </div>
</div>
</body>

</html>
