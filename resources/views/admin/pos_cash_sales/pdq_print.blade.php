@php
    $settings = getAllSettings();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $data->sales_no ?? 'N/A' }}</title>
    <style>
        @page {
            margin: 5mm;
        }
        body {
            font-family: helvetica, sans-serif;
            margin: 0;
            padding: 0;
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
            font-size: 10pt;
            line-height: 1.2;
            margin: 2px 0;
        }
        .normal {
            display: block;
            font-size: 9pt;
            line-height: 1.2;
            margin: 2px 0;
        }
        .bolder {
            display: block;
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.2;
            margin: 2px 0;
        }
        .table {
            width: 100%;
            font-size: 9pt;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table tr.heading td {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            font-weight: bold;
            padding: 4px 2px;
        }
        .table tr.item td {
            padding: 3px 2px;
            border-bottom: 1px dotted #000;
        }
        .table tr hr {
            border: none;
            border-bottom: 1px dotted #000;
            margin: 2px 0;
        }
        .section {
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<div id="receipt-main">
    <div id="receipt-header">
        <h3 style="margin: 4px 0; padding: 0; font-size: 12pt;">{{ $settings['COMPANY_NAME'] ?? 'Company Name' }}</h3>
        <span>{{ $settings['ADDRESS_2'] ?? '' }} {{ $settings['ADDRESS_3'] ?? '' }}</span>
        <span>Tel: {{ $settings['PHONE_NUMBER'] ?? '' }}</span>
        <span>Email: {{ $settings['EMAILS'] ?? '' }}</span>
        <span>Website: {{ $settings['WEBSITE'] ?? '' }}</span>
        <span>PIN No: {{ $settings['PIN_NO'] ?? '' }}</span>
    </div>

    <div class="section text-center">
        <h3 style="margin: 4px 0; padding: 0; font-size: 12pt;">CASH SALE RECEIPT</h3>
        <span class="normal" style="font-size: 10pt;">--- {{ $data->sales_no ?? 'N/A' }} ---</span>
        @if (isset($data->print_count) && $data->print_count > 1)
            <span style="font-size: 9pt; font-weight: bold">REPRINT {{$data->print_count-1}}</span>
        @endif
    </div>

    <div class="section">
        <span class="normal">Time: {{ isset($data->paid_at) ? $data->paid_at->format('d/m/y H:i A') : 'Not Paid' }}</span>
        <span class="normal">Customer Name: {{ $data->customer ?? 'N/A' }}</span>
        <span class="normal">Customer Number: {{ isset($data->customer_phone_number) ? (substr($data->customer_phone_number, 0, 2) . ' *****' . substr($data->customer_phone_number, -2)) : 'N/A' }}</span>
        @if(isset($data->buyer) && isset($data->buyer->kra_pin))
            <span class="normal">Customer KRA Pin: {{ $data->buyer->kra_pin }}</span>
        @endif
        <span class="normal" style="font-style: italic">Prices are inclusive of tax where applicable.</span>
    </div>

    <table class="table">
        <tr class="heading">
            <td style="width: 40%">Item</td>
            <td style="width: 15%">Qty</td>
            <td style="width: 20%">Price</td>
            <td style="width: 25%; text-align:right">Amount</td>
        </tr>
        @php
            $total = 0;
            $vat_amount = 0;
        @endphp

        @foreach($data->items ?? [] as $item)
            <tr class="item">
                <td>{{optional($item->item)->description ?? 'N/A'}}</td>
                <td>{{$item->qty ?? 0}}</td>
                <td>{{number_format($item->selling_price ?? 0, 2)}}</td>
                <td class="text-right">{{number_format(($item->qty ?? 0) * ($item->selling_price ?? 0), 2)}}</td>
            </tr>
            @php
                $total += ($item->qty ?? 0) * ($item->selling_price ?? 0);
                $vat_amount += $item->vat_amount ?? 0;
            @endphp
        @endforeach

        <tr><td colspan="4"><hr></td></tr>
        <tr>
            <td colspan="3" class="text-right">Total:</td>
            <td class="text-right">{{ number_format($total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">VAT:</td>
            <td class="text-right">{{ number_format($vat_amount, 2) }}</td>
        </tr>

        @if($payments && $payments->count() > 0)
            <tr><td colspan="4"><hr></td></tr>
            @php
                $totalPaid = 0;
            @endphp
            @foreach($payments as $payment)
                <tr>
                    <td colspan="3">{{ucfirst(strtolower($payment->title ?? 'N/A'))}}</td>
                    <td class="text-right">
                        @if($payment->is_cash)
                            {{ number_format($data->cash ?? 0, 2) }}
                            @php $totalPaid += $data->cash ?? 0; @endphp
                        @else
                            {{ number_format($payment->amount ?? 0, 2) }}
                            @php $totalPaid += $payment->amount ?? 0; @endphp
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3">Total Paid</td>
                <td class="text-right">{{ number_format($totalPaid, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3">Change</td>
                <td class="text-right">{{ number_format($data->change ?? 0, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="section text-center">
        <span class="normal">Thank you for shopping with us.</span>
        <br>
        <span class="normal">&copy; {{ date('Y') }}. RetailPay Bizwiz ERP.</span>
    </div>
</div>
</body>
</html>
