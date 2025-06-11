<html>
<title>Goods Received Note</title>

<head>
    <style type="text/css">
        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 400;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
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

        h4 {
            font-size: 12px
        }

        h5 {
            font-size: 10px
        }

        a {
            color: #06f;
        }

        /* Base styles */
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 10px;
            background-color: transparent;
            border-collapse: collapse;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            padding: 5px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #aaa;
            text-align: left;
        }

        /* Table header */
        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 2px solid #aaa;
            text-align: left;
        }

        /* Striped tables */
        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        /* Bordered tables */
        .table-bordered {
            border: 1px solid #aaa;
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #aaa;
        }

        /* Condensed tables */
        .table-condensed>thead>tr>th,
        .table-condensed>tbody>tr>th,
        .table-condensed>tfoot>tr>th,
        .table-condensed>thead>tr>td,
        .table-condensed>tbody>tr>td,
        .table-condensed>tfoot>tr>td {
            padding: 5px;
        }

        .table.no-border,
        .table.no-border td,
        .table.no-border th {
            border: 0;
            padding: 5px 0 0
        }

        /* Margin utility classes */
        .m-0 {
            margin: 0 !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mr-0 {
            margin-right: 0 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .ml-0 {
            margin-left: 0 !important;
        }

        .mx-0 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Padding utility classes */
        .p-0 {
            padding: 0 !important;
        }

        .pt-0 {
            padding-top: 0 !important;
        }

        .pr-0 {
            padding-right: 0 !important;
        }

        .pb-0 {
            padding-bottom: 0 !important;
        }

        .pl-0 {
            padding-left: 0 !important;
        }

        .px-0 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .py-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .table.items>thead>tr>th {
            font-size: 9px;
            text-transform: uppercase
        }

        .table.items>tbody>tr>th,
        .table.items>tbody>tr>td {
            padding: 3px;
            font-size: 11px
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .underline {
            text-decoration: underline;

        }
    </style>
</head>

<body>
    <table class="table no-border mb-0 mt-0">
        <tr>
            <td colspan="2">
                <h1>{{ $settings['COMPANY_NAME'] }}</h1>
            </td>
            <th rowspan="2">
                <img src="{{ asset('uploads/restaurants/' . $order->getBranch->image) }}" class="img-circle" alt=""
                    style="float:right;width: 115px; margin-bottom: 23px;">
            </th>
        </tr>
        <tr>
            <td>
                {{ $settings['ADDRESS_1'] }}<br>
                {{ $settings['ADDRESS_2'] }}<br>
                {{ $settings['ADDRESS_3'] }}<br>
                Tel: {{ $settings['PHONE_NUMBER'] }}<br>
                {{ $settings['EMAILS'] }}<br>
                {{ $settings['WEBSITE'] }}<br>
                Pin No: {{ $settings['PIN_NO'] }}<br>
            </td>
            <th class="text-center" style="width: 30%;">
                <img src="data:image/png;base64,{{ base64_encode($qr_code) }}" alt="QR Code">
            </th>
        </tr>
    </table>
    <h3 style="text-align:center;border-bottom:2px solid #aaa; padding-bottom:5px">GOODS RECEIVED NOTE</h3>
    <table class="table no-border">
        <tbody>
            <tr>
                <th>
                    {!! ucfirst($order->getSupplier->name) !!} | {!! $order->getSupplier->supplier_code !!}
                </th>
                <th class="text-right">DATE: {!! date('d-m-Y', strtotime($grnItems->first()->delivery_date)) !!}</th>
            </tr>
            <tr>
                <th>{!! ucfirst($order->getSupplier->address) !!}</th>
                <th class="text-right">Supplier Invoice No: {{ $grnItems->first()->supplier_invoice_no }}</th>
            </tr>
            <tr>
                <th>Vehicle REG No: {{ @$order->vehicle_reg_no }}</th>
                <th class="text-right">CU Invoice No: {{ $grnItems->first()->cu_invoice_number }}</th>
            </tr>
            @if ($grnItems->first()->invoice)
                <tr>
                    <th></th>
                    <th class="text-right">Invoice Date:
                        {{ date('d-m-Y', strtotime($grnItems->first()->invoice->supplier_invoice_date)) }}</th>
                </tr>
            @endif
            <tr>
                <th></th>
                <th class="text-right">LPO No: {{ $order->purchase_no }}</th>
            </tr>
            <tr>
                <th></th>
                <th class="text-right">GRN No: {{ $grnItems->first()->grn_number }}</th>
            </tr>
            <tr>
                <th></th>
                <th class="text-right">Delivery Note: {{ @$order->receive_note_doc_no }}</th>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered items">
        <thead>
            <tr>
                <th style="width: 5%;">Sr.</th>
                <th style="width: 8%;">Code</th>
                <th style="width: 35%;">Description</th>
                <th>QOH Before</th>
                <th>GRN Qty</th>
                <th>Free Qty</th>
                <th>New QOH</th>
                <th>Weight</th>
                <th>Incl Cost</th>
                <th>Vat</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1;
                $TONNAGE = 0;
            @endphp

            @foreach ($grnItems as $grnItem)
                @if ((float) $grnItem->qty_received > 0)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $grnItem->item_code }}</td>
                        <td>{{ $grnItem->item_description }}</td>
                        <td>{{ $grnItem->getTotalQoh() }}</td>
                        <td>{{ $grnItem->item_quantity }}</td>
                        <td>{{ $grnItem?->purchaseOrderItem?->free_qualified_stock ?? 0 }}</td>
                        <td>{{ $grnItem->getTotalQoh() + $grnItem->item_quantity + $grnItem?->purchaseOrderItem?->free_qualified_stock ?? 0 }}
                        </td>
                        <td>{{ round(($grnItem->getRelatedInventoryItem?->getInventoryItemDetail?->net_weight ?? 0) * $grnItem->item_quantity ?? 0, 2) }}
                        </td>
                        <td>{{ manageAmountFormat($grnItem->item_price) }}</td>
                        <td>{{ manageAmountFormat($grnItem->item_vat) }}</td>
                        <td class="text-right">{{ manageAmountFormat($grnItem->item_total) }}</td>
                        @php
                            $TONNAGE += round(
                                ($grnItem->getRelatedInventoryItem->getInventoryItemDetail->net_weight ?? 1) *
                                    $grnItem->item_quantity,
                                2,
                            );
                        @endphp
                    </tr>
                @endif
            @endforeach
            <tr>
                <th colspan="2" style="text-align:left">Total Items:</th>
                <td colspan="6" style="text-align:left">{{ number_format($grnItems->count()) }}</td>
                <th colspan="2" class="text-right"></th>
                <td colspan="1" class="text-right"></td>
            </tr>
            <tr>
                <th colspan="2" style="text-align:left">Total Tonnage:</th>
                <td colspan="6" style="text-align:left">{{ number_format($TONNAGE) }}</td>
                <th colspan="2"></th>
                <td colspan="1" class="text-right"></td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <th colspan="2">Gross Amount</th>
                <td colspan="1" class="text-right">{{ manageAmountFormat($grnItems->sum('item_exclusive')) }}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <th colspan="2">Vat:</th>
                <td colspan="1" class="text-right">{{ manageAmountFormat($grnItems->sum('item_vat')) }}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <th colspan="2">Total Disc:</th>
                <td colspan="1" class="text-right">{{ manageAmountFormat($grnItems->sum('item_discount')) }}</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <th colspan="2">Total Value:</th>
                <td colspan="1" class="text-right">{{ manageAmountFormat($grnItems->sum('item_total')) }}</td>
            </tr>
        </tbody>
    </table>
    @if ($returns->count())
        <h2 class="text-left">RETURNS</h2>
        <table class="table table-bordered" style="width: 70%; margin-bottom: 25px">
            <thead>
                <tr>
                    <th style="text-align: left">Item Code</th>
                    <th style="text-align: left">Quantity</th>
                    <th style="text-align: left">Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returns as $return)
                    <tr>
                        <td>{{ $return->item_code }}</td>
                        <td>{{ $return->returned_quantity }}</td>
                        <td>{{ $return->reason }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if (isset($discount))
        <h4 class="text-left">Distribution Discounts</h4>
        <table class="table table-bordered items" style="width: 70%; margin-bottom: 25px">
            <thead>
                <tr>
                    <th style="text-align: left">Item Code</th>
                    <th style="text-align: left">Quantity</th>
                    <th style="text-align: right">Discount</th>
                    <th style="text-align: right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach ($discount->items as $discountItem)
                    <tr>
                        <td>{{ $discountItem->item_code }}</td>
                        <td>{{ $discountItem->quantity }}</td>
                        <td class="text-right">KES {{ $discountItem->discount_value }}</td>
                        <td class="text-right">
                            {{ manageAmountFormat($total = $discountItem->discount_value * $discountItem->item_quantity) }}
                        </td>
                    </tr>
                    @php $totalAmount += $total; @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right" colspan="3">Total</th>
                    <th class="text-right">{{ manageAmountFormat($totalAmount) }}</th>
                </tr>
            </tfoot>
        </table>
    @endif
    <table class="table no-border">
        <tbody>
            <tr>
                <th style="width: 14%; font-size: 11px;">ENTERED BY: </th>
                <td style="width: 9%; font-size: 11px">NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;">{{ @$r_p->processor->name }}
                </td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:14% "></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:20%;">
                    {{ @$r_p->confirmed_at ? date('d/M/Y H:i A', strtotime(@$r_p->confirmed_at)) : '' }} </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <th style="width: 15%">RECEIVED BY: </th>
                <td style="width: 9%; font-size: 11px">1. NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"> </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <th style="width: 14%; font-size: 11px"> </th>
                <td style="width: 9%; font-size: 11px">2. NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"> </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <th style="width: 14%; font-size: 11px">DRIVER: </th>
                <td style="width: 9%; font-size: 11px">NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"></td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <th style="width: 14%; font-size: 11px">SECURITY BY: </th>
                <td style="width: 9%; font-size: 11px">NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"> </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr style="margin-bottom: 6px !important;">
                <th style="width: 14%; font-size: 11px">CHECKED BY: </th>
                <td style="width: 9%; font-size: 11px">NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"> </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr style="margin-bottom: 6px !important;">
                <th style="width: 9%; font-size: 11px">AUTHORIZED BY: </th>
                <td style="width: 9%; font-size: 11px">NAME: </td>
                <td style="border-bottom:2px dashed #848484  !important; width:25%;"></td>
                <td style="width: 9%; font-size: 11px">SIGN: </td>
                <td style="border-bottom:2px dashed #848484; width:15%"></td>
                <td style="width: 9%; font-size: 11px">DATE: </td>
                <td style="border-bottom:2px dashed #848484; width:19%;"> </td>
            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>
            <tr>
                <td colspan="2">Dated: {{ date('d/m/Y') }}</td>
                <td colspan="2">Timed: {{ date('H:i:s') }}</td>
                <td colspan="3"></td>
            </tr>

        </tbody>
    </table>
    <!-- Add the page number script -->
    <script type="text/php">
        if ( isset($pdf) ) { 
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 9;
            $pdf->text(270, 780, "Page ".$PAGE_NUM." of ".$PAGE_COUNT, $font, $size);
        }
    </script>
</body>

</html>
