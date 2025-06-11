<html>
<title>Print</title>

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
                        {{ $list->storeLocation?->location_name }}
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
                    <th style="text-align: right;">DATE: {!! date('d-m-Y', strtotime($list->getRelatedGrn->delivery_date)) !!}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">
                        {!! ucfirst($list->getSupplier->name) !!} | {!! $list->getSupplier->supplier_code !!}
                    </th>
                    <th style="text-align: right;">Supplier Invoice No: {{ @$r_p->supplier_invoice_no }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">Address: {!! ucfirst($list->getSupplier->address) !!}</th>
                    <th style="text-align: right;">CU Invoice No: {{ @$r_p->cu_invoice_number }}</th>
                </tr>
                @if ($grn->first()->invoice)
                    <tr>
                        <th style="text-align: left;" colspan="2"></th>
                        <th style="text-align: right;">Invoice Date:
                            {{ date('d-m-Y', strtotime($grn->first()->invoice->supplier_invoice_date)) }}</th>
                    </tr>
                @endif
                <tr>
                    <th style="text-align: left;" colspan="2"></th>
                    <th style="text-align: right;">LPO No: {{ $list->purchase_no }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2"></th>
                    <th style="text-align:right">GRN No: {{ @$grn->first()->grn_number }}</th>
                </tr>
                <tr>
                    <th style="text-align: left;" colspan="2">Vehicle REG No: {{ @$list->vehicle_reg_no }}</th>
                    <th style="text-align: right;">Delivery Note: {{ @$list->receive_note_doc_no }}</th>
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
                    <td style="width: 10%;">QOH Before</td>
                    <td style="width: 10%;">GRN Qty</td>
                    <td style="width: 15%;">New QOH</td>
                    <td style="width: 12%;">Weight</td>
                    <td style="width: 12%;">Incl Cost</td>
                    <td style="width: 10%;">Vat</td>
                    <td style="width: 15%;">Total Cost</td>
                </tr>
                @php
                    $counter = 1;
                    $gross = 0;
                    $disc = 0;
                    $tvat = 0;
                    $sellvalue = 0;
                    $TONNAGE = 0;

                @endphp
                @foreach ($grn as $key => $items)
                    @if ((float) $items->qty_received > 0)
                        <?php
                        $invoice_info = json_decode($items->invoice_info);
                        $nett = $invoice_info->order_price * $invoice_info->qty;
                        $net_price = $nett;
                        if ($invoice_info->discount_percent > '0') {
                            $discount_amount = ($invoice_info->discount_percent * $nett) / 100;
                            $disc += $discount_amount;
                            $nett = $nett - $discount_amount;
                        }
                        
                        $vat_amount = 0;
                        $inclusivePrice = $invoice_info->order_price;
                        if ($invoice_info->vat_rate > '0') {
                            $vat_amount = round($nett - ($nett * 100) / ($invoice_info->vat_rate + 100), 2);
                            $tvat += $vat_amount;
                            $inclusivePrice = round((100 * $invoice_info->order_price) / ($invoice_info->vat_rate + 100), 2);
                        }
                        ?>
                        <tr class="item" style="border-bottom: 2px solid black !important;">
                            <td>{{ $counter++ }}</td>
                            <td>{{ $items->item_code }}</td>
                            <td>{{ $items->item_description }}</td>
                            <td>{{ $items->getTotalQoh() }}</td>
                            <td>{{ $items->qty_received }}</td>
                            <td>{{ $items->getTotalQoh() + $items->qty_received }}</td>
                            <td>{{ round(($items->getRelatedInventoryItem?->getInventoryItemDetail?->net_weight ?? 0) * $items->qty_received ?? 0, 2) }}
                            </td>
                            <td>{{ manageAmountFormat($invoice_info->order_price) }}</td>
                            <td>{{ manageAmountFormat($vat_amount) }}</td>
                            <td>{{ manageAmountFormat($nett) }}</td>

                            @php
                                $gross += round($nett, 2);
                                $sellvalue += round(
                                    $items->qty_received * @$items->getRelatedInventoryItem->selling_price,
                                    2,
                                );
                            @endphp
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
                <tr style="    border-top: 2px dashed #cecece;">
                    <td colspan="10"></td>
                </tr>
                <tr style="    border-top: 2px dashed #cecece;">
                    <td colspan="10"></td>
                </tr>
                @php
                    $roundOff = fmod($gross, 1); //0.25
                    if ($roundOff != 0) {
                        if ($roundOff > '0.50') {
                            $roundOff = '+' . round(1 - $roundOff, 2);
                        } else {
                            $roundOff = '-' . round($roundOff, 2);
                        }
                        $gross += $roundOff;
                    }

                    $roundVat = fmod($tvat, 1); //0.25
                    if ($roundVat != 0) {
                        if ($roundVat > '0.50') {
                            $roundVat = '+' . round(1 - $roundVat, 2);
                        } else {
                            $roundVat = '-' . round($roundVat, 2);
                        }
                        $tvat += $roundVat;
                    }
                @endphp
                <tr>
                    <td colspan="2" style="text-align:left">Total Items:</td>
                    <td colspan="2" style="text-align:left">{{ number_format($grn->sum('qty_received')) }}</td>
                    <td colspan="3"></td>
                    <td style="text-align: right;" colspan="2">Gross Amt:</td>
                    <td colspan="1">{{ manageAmountFormat($gross) }}</td>

                </tr>

                <tr>
                    <td colspan="2" style="text-align:left">Total Tonnage:</td>
                    <td colspan="2" style="text-align:left">{{ number_format($TONNAGE) }}</td>
                    <td colspan="3"></td>
                    <td style="text-align: right;" colspan="2">Disc:</td>
                    <td style="text-align: right;" colspan="1">{{ manageAmountFormat($disc) }}</td>
                </tr>
                <tr>
                    <td colspan="7"></td>
                    <td style="text-align: right;" colspan="2">Goods:</td>
                    <td style="text-align: right;" colspan="1">{{ manageAmountFormat($gross) }}</td>

                </tr>
                <tr>
                    <td colspan="7"></td>
                    <td style="text-align: right;" colspan="2">Vat:</td>
                    <td style="text-align: right;" colspan="1">{{ manageAmountFormat($tvat) }}</td>
                </tr>

                <tr>
                   
                    <td colspan="7"></td>
                    <td style="text-align: right;" colspan="2">Total Value:</td>
                    <td style="text-align: right;" colspan="1">{{ manageAmountFormat($gross) }}</td>
                </tr>
                
                <tr>
                    <td colspan="5"></td>
                    <td colspan="3"></td>

                  
                </tr>
                <tr>
                    <td>

                    </td>
                    <td></td>
                    <td></td>
                   
                    <td colspan="3"></td>

                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="3"></td>

                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                   
                    <td style="text-align: right;" colspan="4"></td>
                </tr>
               
            </tbody>
        </table>
        <table>
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
                

            </tbody>
        </table>

    </div>
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
