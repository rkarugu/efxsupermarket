<html>
<title>Print Received Note</title>

<head>
    <style type="text/css">
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        .invoice-box * {
            font-size: 12px;
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
            font-size: 12px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
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
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            font-size: 11px
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
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

        .text-center {
            text-align: center
        }

        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }
    </style>

</head>

<body>

    <?php $all_settings = getAllSettings(); ?>
    <div class="invoice-box">
        <table>
            <tbody>
                <tr class="top">
                    <td style="width: 10%"></td>
                    <th style="text-align: center">
                        <h2 style="margin: 0">{{ $all_settings['COMPANY_NAME'] }}</h2>
                    </th>
                    <td style="width: 10%"></td>
                </tr>
                <tr class="top">
                    <td style="width: 10%"></td>
                    <td style="text-align: center;">
                        {{ $all_settings['ADDRESS_1'] }} {{ $all_settings['ADDRESS_2'] }}
                        {{ $all_settings['ADDRESS_3'] }} TEL {{ $all_settings['PHONE_NUMBER'] }}
                    </td>
                    <td style="width: 10%"></td>
                </tr>
                <tr class="top">
                    <td style="width: 10%"></td>
                    <td style="text-align: center;">
                        {{ $order->storeLocation?->location_name }}
                    </td>
                    <td style="width: 10%"></td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <th style="text-align: left;" colspan="2">
                        <h2>GOODS RECEIVED NOTE</h2>
                    </th>
                    <td rowspan="7" style="vertical-align: middle;">
                        @if (isset($pdf_d))
                            <img src="data:image/png;base64,{{ base64_encode($qr_code) }}" alt="QR Code">
                        @else
                            {!! $qr_code !!}
                        @endif
                    </td>
                    <th style="text-align: right;">DATE: {!! date('d-m-Y', strtotime($order->getRelatedGrn->delivery_date)) !!}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">
                        {!! ucfirst($order->getSupplier->name) !!} | {!! $order->getSupplier->supplier_code !!}
                    </th>
                    <th style="text-align: right;">Supplier Invoice No: {{ @$r_p->supplier_invoice_no }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">Address: {!! ucfirst($order->getSupplier->address) !!}</th>
                    <th style="text-align: right;">CU Invoice No: {{ @$r_p->cu_invoice_number }}</th>
                </tr>
                @if ($grns->first()->invoice)
                    <tr>
                        <th style="text-align: left;" colspan="2"></th>
                        <th style="text-align: right;">Invoice Date:
                            {{ date('d-m-Y', strtotime($grns->first()->invoice->supplier_invoice_date)) }}</th>
                    </tr>
                @endif
                <tr>
                    <th style="text-align: left;" colspan="2"></th>
                    <th style="text-align: right;">LPO No: {{ $order->purchase_no }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2"></th>
                    <th style="text-align:right">GRN No: {{ @$grns->first()->grn_number }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">Vehicle REG No: {{ @$order->vehicle_reg_no }}</th>
                    <th style="text-align: right;">Delivery Note: {{ @$order->receive_note_doc_no }}</th>
                </tr>
            </tbody>
        </table>

        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <td style="width: 5%;">Sr.</td>
                    <td style="width: 12%;">Code</td>
                    <td style="width: 30%;">Description</td>
                    <td style="width: 10%;">GRN Qty</td>
                    <td style="width: 12%;">Weight</td>
                </tr>
                @php
                    $counter = 1;
                    $TONNAGE = 0;
                @endphp
                @foreach ($grns as $key => $items)
                    @if ((float) $items->qty_received > 0)
                        <tr class="item" style="border-bottom: 2px solid black !important;">
                            <td>{{ $counter++ }}</td>
                            <td>{{ $items->item_code }}</td>
                            <td>{{ $items->item_description }}</td>
                            <td>{{ $items->qty_received }}</td>
                            <td>{{ round(($items->getRelatedInventoryItem?->getInventoryItemDetail?->net_weight ?? 0) * $items->qty_received ?? 0, 2) }}
                            </td>
                            @php
                                $TONNAGE += round(
                                    ($items->getRelatedInventoryItem->getInventoryItemDetail->net_weight ?? 1) *
                                        $items->qty_received,
                                    2,
                                );
                            @endphp
                        </tr>
                    @endif
                @endforeach
                <tr style="border-top: 2px dashed #cecece;">
                    <td colspan="5"></td>
                </tr>
                <tr style="border-top: 2px dashed #cecece;">
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:left">Total Items:</td>
                    <td style="text-align:left">{{ number_format($grns->sum('qty_received')) }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:left">Total Tonnage:</td>
                    <td style="text-align:left">{{ number_format($TONNAGE) }}</td>
                    <td colspan="2"></td>
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
        <table style="margin-top:35px">
            <thead>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                <tr>
                    <th style="width: 14%; font-size: 11px; text-align:left">ENTERED BY: </th>
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
                    <th style="width: 14%; font-size: 11px; text-align:left">DRIVER:</th>
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
                    <td colspan="7"></td>
                </tr>
                <tr>
                    <td colspan="2">Dated: {{ date('d/m/Y') }}</td>
                    <td colspan="2">Timed: {{ date('H:i:s') }}</td>
                    <td style="text-align: right;" colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
