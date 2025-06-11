<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receipt #{{ $data->sales_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            width: 300px;
            margin: 0 auto;
            padding: 10px 0;
        }
        .center {
            text-align: center;
        }
        .business-info {
            margin-bottom: 5px;
        }
        .business-info h2 {
            margin: 0 0 2px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .business-info p {
            margin: 0;
            font-size: 11px;
        }
        .divider {
            border-top: 1px dashed #222;
            margin: 6px 0;
        }
        .details, .totals, .payments {
            width: 100%;
            margin-bottom: 4px;
        }
        .details td {
            padding: 1px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .items-table th, .items-table td {
            border-bottom: 1px dotted #aaa;
            padding: 2px 0;
            font-size: 11px;
            text-align: left;
        }
        .items-table th {
            font-weight: bold;
            border-bottom: 1px solid #222;
        }
        .totals td {
            padding: 1px 0;
        }
        .totals .label {
            text-align: right;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="business-info center">
            <h2>{{ config('app.name', 'Business Name') }}</h2>
            <p>{{ $settings['ADDRESS_2'] ?? '' }} {{ $settings['ADDRESS_3'] ?? '' }}</p>
            <p>Tel: {{ $settings['PHONE_NUMBER'] ?? '' }}</p>
            <p>PIN: {{ $settings['PIN_NO'] ?? '' }}</p>
        </div>
        <div class="center">
            <strong>POS RECEIPT</strong><br>
            <span>Invoice #: {{ $data->sales_no }}</span><br>
            <span>Date: {{ $data->date }} {{ $data->time }}</span><br>
            <span>Cashier: {{ $data->user->name }}</span>
        </div>
        <div class="divider"></div>
        <table class="details">
            <tr><td>Customer:</td><td>{{ $data->customer }}</td></tr>
            @if($data->customer_phone_number)
            <tr><td>Phone:</td><td>{{ $data->customer_phone_number }}</td></tr>
            @endif
            @if($data->customer_pin)
            <tr><td>PIN:</td><td>{{ $data->customer_pin }}</td></tr>
            @endif
        </table>
        <div class="divider"></div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data->items as $item)
                <tr>
                    <td>{{ $item->item->title }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->selling_price, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table class="totals">
            <tr><td class="label">Subtotal:</td><td>{{ number_format($data->items->sum('total'), 2) }}</td></tr>
            <tr><td class="label">VAT:</td><td>{{ number_format($data->items->sum('vat_amount'), 2) }}</td></tr>
            <tr><td class="label"><strong>Total:</strong></td><td><strong>{{ number_format($data->items->sum('total') + $data->items->sum('vat_amount'), 2) }}</strong></td></tr>
        </table>
        <div class="divider"></div>
        <table class="payments">
            <tr><td colspan="2"><strong>Payment</strong></td></tr>
            @foreach($payments as $payment)
            <tr><td>{{ $payment['title'] }}</td><td>{{ number_format($payment['amount'], 2) }}</td></tr>
            @endforeach
        </table>
        <div class="footer">
            Thank you for shopping with us!<br>
            Powered by {{ config('app.name', 'POS System') }}
        </div>
    </div>
</body>
</html>
