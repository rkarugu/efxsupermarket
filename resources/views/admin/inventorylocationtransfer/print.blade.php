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
        .payment-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 20px;
    }

    .payment-table td {
        width: 50%;
        text-align: center;
        vertical-align: top;
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

        @if ($list->print_count > 1)
            <tr class="top">
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr class="top">
                <th colspan="2" style="text-align: center">
                    <span style="font-size:18px !important">REPRINT {{$list->print_count-1}}</span>
                </th>
            </tr>
        @endif
        </tbody>
    </table>

    <table>
        <tbody>
        <tr class="top">
            @php
                $customer = $list->get_customer;
                $shift_id = \App\SalesmanShift::find($list->shift_id);
            @endphp
            <th colspan="1" style="text-align: left;">ACC NO: {{ $customer->customer_name }} | {{@$customer->telephone}}</th>
            <th colspan="1" style="text-align: right;">Invoice NO: {!! $list->transfer_no!!}</th>
        </tr>
        <tr class="top">
            <th colspan="1" style="text-align: left;">Customer Name: {{$list->name}}</th>
            <th colspan="1" style="text-align: right;">ShiftId: {!! @$shift_id->shift_id !!}</th>
        </tr>
        <tr class="top">
            <th colspan="1" style="text-align: left;">Customer PIN: {{$list->customer_pin ?? $customer->kra_pin}}</th>
            <th colspan="1" style="text-align:right">DATE: {!! date('Y-m-d H:i:s',strtotime($list->transfer_date))!!}</th>
        </tr>
        <tr class="top">
            <th colspan="2" style="text-align: left;">Customer Phone Number: {{$list->customer_phone_number}}</th>
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
            <td style="width: 10%;">Disc</td>
            <td style="width: 8%;">Vat%</td>
            <td style="width: 11%;">Total</td>
        </tr>
        @php
            $TONNAGE = 0;
            $gross_amount = 0;
            $totalDiscount = 0;
            $netAmount = 0;
            $totalVat = 0;
        @endphp
        @foreach($list->getRelatedItem as $item)
            <tr class="item">
                <td>{{$item->getInventoryItemDetail->stock_id_code}}</td>
                <td>{{$item->getInventoryItemDetail->title}}</td>
                <td>{{floor($item->quantity)}}</td>
                <td>{{manageAmountFormat($item->selling_price)}}</td>
                <td>{{manageAmountFormat($item->quantity*$item->selling_price)}}</td>
                <td>{{manageAmountFormat($item->getDiscount())}}</td>
                <td>{{$item->vat_rate}}</td>
                <td>{{manageAmountFormat($item->total_cost_with_vat)}}</td>
            </tr>

            @php
                $gross_amount += (($item->quantity*$item->selling_price)-$item->getDiscount());
                $TONNAGE += (($item->getInventoryItemDetail->net_weight ?? 0) * $item->quantity);
                $totalDiscount += $item->getDiscount();

                $vat = 0;
                if ($item->vat_rate > 0) {
                    $vat = ($item->vat_rate/(100 + $item->vat_rate)) * $item->total_cost_with_vat;
                }
                $totalVat += $vat;
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
            <td colspan="3">{{count($list->getRelatedItem)}} Lines</td>
            <td style="text-align: right;" colspan="1">Gross Amount:</td>
            <td>{{manageAmountFormat($gross_amount)}}</td>
        </tr>
        <tr>
            <td colspan="1">Prepared by: {{@$list->user->name}}</td>
            <td>Time: {{date('H:i A',strtotime($list->created_at))}}</td>
            <td colspan="1">Delivered By: ___________</td>
            <td style="text-align: right;" colspan="1">Discount:</td>
            <td>{{manageAmountFormat($totalDiscount)}}</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="text-align: right;" colspan="1">Net Amount:</td>
            <td>{{ manageAmountFormat(($gross_amount - $totalVat) ?? 0.00) }}</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td style="text-align: right;" colspan="1">V.A.T:</td>
            <td>{{manageAmountFormat($totalVat ?? 0.00)}}</td>
        </tr>
        <tr>
            <td colspan="2">Received By: ______________</td>
            <td colspan="1">Sign: ______________</td>
            <td colspan="1">TONNAGE : {{manageAmountFormat($TONNAGE)}}</td>
            <td colspan="1" style="text-align: center;">
                <hr style="border: 1px dashed #7b7b7b;">
            </td>
        </tr>
        <tr>
            <td colspan="1"></td>
            <td colspan="1">RUBBER STAMP</td>
            <td colspan="1"></td>
            <td colspan="1" style="text-align: right;">Total:
                <hr style="border: 2px dashed #979797;">
            </td>
            <td colspan="1">
                {{manageAmountFormat($gross_amount)}}
                <hr style="border: 2px dashed #979797;">
            </td>
        </tr>
        <tr>
            <td colspan="3">Amount in Words
                <br>
                {{strtoupper(getCurrencyInWords($gross_amount))}}
            </td>
            @php

            @endphp
            @php
                $invoices = \App\Model\WaInventoryLocationTransfer::where('shift_id',@$list->shift_id)->pluck('id')->toArray();
                $invoicesItems = \App\Model\WaInventoryLocationTransferItem::whereIn('wa_inventory_location_transfer_id',$invoices)->sum('total_cost_with_vat');
                $balance = $invoicesItems;
                $customer = $list->get_customer;
                if ($customer->is_invoice_customer)
                    {
                          $balance = \App\Model\WaDebtorTran::where('wa_customer_id', $customer->id)->sum('amount');
                    }

            @endphp

            <td colspan="1">A/C Balance : {{manageAmountFormat($balance)}}</td>
            <td colspan="1">Change: {{$list->change}}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        @if ($getLoggeduserProfile->upload_data == 0)
            <tr>
                <td colspan="5" style="text-align:center">{{($list->upload_data)}}</td>

            </tr>
        @endif
        </tbody>
    </table>


    @if(!empty($esd_details))

        <div style="width:100%; padding: 10px; text-align:left;">
            <div style="width:20%;  float: left;">
                @if($esd_details->verify_url!="")

                    @if(isset($is_print))
                        {{ QrCode::size(120)->generate($esd_details->verify_url) }}
                    @else
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::size(120)->generate($esd_details->verify_url)) !!} ">
                    @endif

                @endif
            </div>
            <div style="width:80%; text-align:left;  float: left;">
                CU Serial No : {{ $esd_details->cu_serial_number }}<br>
                <p> CU Invoice Number : {{ $esd_details->cu_invoice_number }} </p>
            </div>
        </div>
    @else
        <div style="width:100%; padding: 10px; text-align:left;">
            <div style="width:20%;  float: left;">
                @php
                    $qrCodeUrl="https://itax.kra.go.ke/KRA-Portal/invoiceChk.htm?actionCode=loadPage&invoiceNo=0040094030000023930-";
                @endphp

                @if(isset($is_print))
                    {{ QrCode::size(70)->generate($qrCodeUrl) }}
                @else
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::size(70)->generate($qrCodeUrl)) !!} ">
                @endif
            </div>
        </div>
    @endif
    <div style="margin-top: 70px; padding-top: 70px;">
        {{-- <hr style="border: none; border-top: 1px solid #000; text-align: center; margin-top: 20px;"> --}}
        <span style="position: relative; top: -12px; background-color: white; padding: 0 10px;">
            <span style="font-size: 20px; letter-spacing: 5px;">********</span>
            <span style="font-weight: bold;">Payment Details</span>
            <span style="font-size: 20px; letter-spacing: 5px;">********</span>
        </span>
        <h4>(PLEASE DO NOT MAKE CASH PAYMENTS TO OUR STAFF. THE COMPANY WILL NOT BE LIABLE FOR ANY LOSS RESULTING FROM CASH TRANSACTIONS.)</h4>
    </div>

    @if(isset($restaurant))
        <div class="payment-details">
            @php
                $paybills = [$restaurant->kcb_mpesa_paybill];
                // shuffle($paybills);
            @endphp

            <div class="bank-section">
                <p>MPESA Paybill: <span style="font-size: 13px; font-weight: bold;">{{ $paybills[0] }}</span></p>
                <p>Account Number: <span style="font-size: 13px; font-weight: bold;">{{$payment_code}}</span></p>
            </div>
            <div class="bank-section">
                <p>Vooma Paybill : <span style="font-size: 13px; font-weight: bold;">{{$restaurant->kcb_vooma_paybill}}</span></p>
                <p>Account Number: <span style="font-size: 13px; font-weight: bold;">{{$payment_code}}</span></p>
            </div>
            <div class="bank-section">
                <p>Equity Biller Number: <span style="font-size: 13px; font-weight: bold;">{{$restaurant->equity_paybill}}</span></p>
                <p>Account Number: <span style="font-size: 13px; font-weight: bold;">{{$payment_code}}</span></p>
            </div>
            <div class="bank-section">
                <p>{{ $all_settings['INVOICE_NOTE'] }}</p>

            </div>
        </div>

    @endif

    
    <div>
        
    </div>
    

   {{-- <table>--}}
{{--        <tr>--}}
{{--            <td>--}}
{{--                <b>PAYMENT REF:</b>--}}
{{--            </td>--}}
{{--            <td>--}}
{{--                How to pay via MPESA--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td>--}}
{{--                0000--}}
{{--                --}}{{--        {{ $ref }}--}}
{{--            </td>--}}
{{--            <td>--}}
{{--                Paybill <b>522533</b>--}}
{{--            </td>--}}

{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td>--}}

{{--            </td>--}}
{{--            <td>--}}
{{--                <b>7652611#0000</b>--}}
{{--                --}}{{--        <b>7652611#{{ $ref }}</b>--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--    </table> --}}

</div>

</body>
</html>
