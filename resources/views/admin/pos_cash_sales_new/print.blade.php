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
                <div style="transform: scale(1);">
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
        <h3 style="margin: 0; padding: 0; font-size: 15px;"> CASH SALE RECEIPT</h3>
        <span class="bolder"> Sale No.: {{ $data->sales_no }} </span>
        @if ($data->print_count > 1)
            <span style="font-size:15px !important; font-weight: bold">REPRINT {{$data->print_count-1}}</span>
        @endif
        <br>
    </div>

    <div style="margin-top: 20px;" class="customer-details">

        <span class="normal"> Time: {{ $data->paid_at->format('d/m/y  H:i A') }} </span>

        <span class="normal"> Customer Name: {{ $data->customer }} </span>
        <span class="normal"> Customer Number: {{ substr($data-> customer_phone_number, 0, 2) . ' *****' . substr($data-> customer_phone_number, -2) }}</span>
        @if($data->buyer ->kra_pin)
            <span class="normal"> Customer KRA Pin: {{ $data-> buyer->kra_pin }}</span>
        @endif


        <br>

        <span class="normal"> Prices are inclusive of tax where applicable. </span>
    </div>
    <br>

    <table class="table">
        <tbody>
        <tr class="heading">
            <td >Item</td>
            <td>Qty</td>
            <td>Price</td>
            <td style="text-align:right">Amount</td>
        </tr>
        @php
            $TONNAGE =0;
            $gross_amount = 0;
            $count = 0 ;
            $vat_amount = 0 ;
            $total_discount = 0 ;
            $net_amount = 0 ;
        @endphp
        @foreach($data->items as $item)
            <tr style="width:100%;">
            </tr>

        @endforeach
        @foreach($data->items as $item)
            <tr style="width:100%;">
                <td colspan="5" style="text-align:left;">{{ $loop->iteration }}  {{@$item->item->description}}</td>
            </tr>
            <tr class="item">
                <td>{{@$item->item->pack_size->title}}</td>
                <td>{{@$item->qty}}</td>
                <td>{{manageAmountFormat($item->selling_price)}}</td>
                <td style="text-align:right;">{{manageAmountFormat($item->qty*$item->selling_price)}}</td>
            </tr>
            @php
                $TONNAGE += (($item->item->net_weight ?? 1) * $item->qty);
                $gross_amount += $item->qty*$item->selling_price;
                $net_amount += (($item->qty*$item->selling_price) - $item->discount_amount);
                $total_discount += $item -> discount_amount;
                $count ++ ;
                $vat_amount += $item->vat_amount;
            @endphp
        @endforeach

        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>
        <tr style="width:100%;">
            <td colspan="3" style="text-align:left !important">
                Gross Totals <br>
                Discount <br>
                Totals <br>
            </td>

            <td colspan="1" style="text-align:right !important">
                {{ manageAmountFormat($gross_amount) }} <br>
                {{ manageAmountFormat($total_discount) }} <br>
                {{ manageAmountFormat($net_amount) }}
            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>
        <tr >
            <td colspan="5" style="text-align:left !important">
                {{strtoupper(getCurrencyInWords($gross_amount))}}
            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>

        <tr style="width:100%;">
            <td colspan="1" style="text-align:left !important">
                <span style="border-bottom:1px dashed">CODE</span>
            </td>
            <td colspan="2" style="text-align:right !important">
                <span style="border-bottom:1px dashed">VATABLE AMT</span>
            </td>
            <td colspan="2" style="text-align:right !important">
                <span style="border-bottom:1px dashed">VAT AMT</span>
            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="1" style="text-align:left !important">
                S
            </td>
            <td colspan="2" style="text-align:right !important">
                {{manageAmountFormat($gross_amount)}}
            </td>
            <td colspan="2" style="text-align:right !important">
                {{manageAmountFormat($vat_amount)}}
            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>
        <tr style="width:100%;">
            <td colspan="3" style="text-align:left !important">
                @foreach ($payments as $payment)
                    {{ucfirst(strtolower($payment->title))}} <br>

                @endforeach
            </td>
            <td colspan="1" style="text-align:right !important">
                @php
                    $totalAmountPaid = 0;
                @endphp
                @foreach ($payments as $payment)
                    @if($payment->is_cash)
                        {{ $data -> cash }}
                        @php
                            $totalAmountPaid += $data->cash;
                        @endphp
                    @else
                        {{ manageAmountFormat($payment->amount) }}<br>
                        @php
                            $totalAmountPaid += $payment->amount;
                        @endphp
                    @endif


                @endforeach

            </td>

        </tr>
        <tr style="width: 100%;">
            <td colspan="3" style="text-align:left !important">
                Total Paid<br>

            </td>
            <td colspan="1" style="text-align:right !important">
                {{ manageAmountFormat($totalAmountPaid) }}<br>

            </td>
        </tr>
        <tr style="width: 100%;">
            <td colspan="3" style="text-align:left !important">
                Change<br>

            </td>
            <td colspan="1" style="text-align:right !important">
                {{ manageAmountFormat($data->change) }}<br>

            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5" style="text-align:left !important">
                @if($data->attending_cashier != $data->user_id )
                    You were served by: <b>{{@$data->attendingCashier->name}}</b>
                    <br>
                    Sales Rep: <b>{{@$data->user->name}}</b>
                @else
                    You were served by: <b>{{@$data->user->name}}</b>
                @endif

            </td>
        </tr>
        <tr style="width:100%;">
            <td colspan="5"><hr class="new4"></td>
        </tr>
        </tbody>
    </table>

    @if($esd_details)
        <div style="width:100%; text-align:center; margin-top: 20px !important;">
            @if($esd_details->verify_url!="")

                {{ QrCode::size(120)->generate($esd_details->verify_url) }}
{{--                @if(isset($is_print))--}}
{{--                    {{ QrCode::size(200)->generate($esd_details->verify_url) }}--}}
{{--                @else--}}
{{--                    <img src="data:image/png;base64, {!! base64_encode(QrCode::size(200)->generate($esd_details->verify_url)) !!} " alt="">--}}
{{--                @endif--}}
            @endif
            <br>
            <br>
            <span class="normal"> {{ $esd_details->cu_serial_number }}</span>
            <span class="normal"> CU Invoice Number : {{ $esd_details->cu_invoice_number }}</span>
        </div>
    @endif


    <div style="margin-top: 40px; text-align: center; font-size: 14px;">
        <span> Thank you for shopping with us. </span>
        <br>
        <span> &copy; {{ \Carbon\Carbon:: now()->year }}. Effecentrix POS. </span>
    </div>
</div>
</body>

</html>
