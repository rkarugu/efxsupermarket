<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 400;
            margin: 0;
            padding: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0 0 5px 0;
            line-height: 1.6;
        }

        h1 {
            font-size: 18px
        }

        h2 {
            font-size: 16px
        }

        h3 {
            font-size: 14px
        }

        h5 {
            font-size: 12px
        }

        a {
            color: #06f;
        }

        .table * {
            font-size: 12px;
        }

        .table.lists * {
            font-size: 10px;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 3px 5px;
            vertical-align: center;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            border-top: 1px solid #aaaaaa
        }

        .table>thead>tr>th {
            border-bottom: 2px solid #aaaaaa;
            padding: 5px;
            background: #eee;
        }

        .table.signatures>tbody>tr>td {
            padding: 15px 5px
        }

        .table.signatures>tbody>tr>th {
            padding: 15px 5px;
            width: 15%;
        }

        .table-bordered {
            border: 1px solid #aaaaaa
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #aaaaaa
        }

        .table-bordered>thead>tr>th,
        .table-bordered>thead>tr>td {
            border-bottom-width: 2px
        }

        .table.no-border,
        .table.no-border td,
        .table.no-border th {
            border: 0
        }

        table.text-center,
        table td.text-center,
        table th.text-center {
            text-align: center
        }

        table.text-right,
        table td.text-right,
        table th.text-right {
            text-align: right
        }

        table.text-left,
        table td.text-left,
        table th.text-left {
            text-align: left
        }

        .underline {
            border-bottom: 1px solid #aaa !important;
        }

        .text-center {
            text-align: center
        }

        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }

        .deliveries * {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="text-left">
        <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
        <h3>DELIVERY SCHEDULE</h3>
    </div>
    <table class="table no-border" style="margin-top: 15px">
        <tr>
            <th class="text-left" style="padding-left: 0">
                <span style="margin-right: 10px">{{ $advancePayment->supplier->name }}</span>
            </th>
            <th class="text-right">
                {{ $advancePayment->created_at->format('d/m/Y H:i') }}
            </th>
        </tr>
        <tr>
            <th class="text-left" style="padding-left: 0">
                Voucher No: {{ $advancePayment->payment->voucher->number }}
            </th>
        </tr>
        <tr>
            <th class="text-left" style="padding-left: 0">
                LPO No: {{ $advancePayment->lpo->purchase_no }}
            </th>
        </tr>
    </table>
    <table class="table table-bordered" style="margin-top: 30px; margin-right:auto; width:70%">
        <tr>
            <th class="text-left">Reference No</th>
            <th class="text-left">Date</th>
            <th class="text-right">WHT Amount</th>
            <th class="text-right">Paid Amount</th>
        </tr>
        <tr>
            <td class="text-left">{{ $advancePayment->id }}</td>
            <td class="text-left">{{ $advancePayment->created_at->format('d/m/Y') }}</td>
            <td class="text-right">{{ manageAmountFormat($advancePayment->withholding_amount) }}</td>
            <td class="text-right">{{ manageAmountFormat($advancePayment->payment->amount) }}</td>
        </tr>
    </table>
    <table class="table table-bordered" style="margin-top: 15px">
        <tr>
            <th class="text-left">Item</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
        <tbody>
            @foreach ($advancePayment->lpo->purchaseOrderItems as $orderItem)
                <tr>
                    <td class="text-left">{{ $orderItem->inventoryItem->title }}</td>
                    <td class="text-right">{{ number_format($orderItem->quantity) }}</td>
                    <td class="text-right">{{ manageAmountFormat($orderItem->order_price) }}</td>
                    <td class="text-right">{{ manageAmountFormat($orderItem->total_cost_with_vat - $orderItem->other_discounts_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h5 style="margin-top: 15px">Deliveries</h5>
    <table class="table table-bordered deliveries">
        <tr>
            <th class="text-left">Date</th>
            <th class="text-left">Vehicle</th>
            <th class="text-left">Location</th>
            <th class="text-left">LPO</th>
            <th class="text-left">GRN</th>
            <th class="text-left">Delivery</th>
            <th class="text-left">Cu Invoice No.</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
        <tbody>
            @foreach ($grns as $grn)
                <tr>
                    <td>{{ $grn->delivery_date }}</td>
                    <td>{{ $grn->vehicle_reg_no }}</td>
                    <td>{{ $grn->location_name }}</td>
                    <td>{{ $grn->purchaseOrder->purchase_no }}</td>
                    <td>{{ $grn->grn_number }}</td>
                    <td>{{ $grn->supplier_invoice_no }}</td>
                    <td>{{ $grn->cu_invoice_number }}</td>
                    <td class="text-right">{{ number_format($grn->itemQuantity) }}</td>
                    <td class="text-right">{{ manageAmountFormat($grn->itemPrice) }}</td>
                    <td class="text-right">{{ manageAmountFormat($grn->itemTotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table signatures no-border" style="margin-top: 50px">
        <tbody>
            <tr>
                <th class="text-left">Prepared By:</th>
                <td class="underline"><strong>{{ strtoupper($advancePayment->preparedBy->name) }}</strong></td>
                <th class="text-left">Sign:</th>
                <td colspan="3" class="underline"></td>
            </tr>
            <tr>
                <th class="text-left">Approved By:</th>
                <td class="underline"></td>
                <th class="text-left">Authorized By:</th>
                <td colspan="3" class="underline"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
