<html>
<title>Print</title>

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
                <img src="{{ asset('uploads/restaurants/' . @$row->getBranch->image) }}" class="img-circle"
                    alt="Branch Logo" style="float:right;width: 115px; margin-bottom: 23px;">
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
                @if (isset($pdf_d))
                    <img src="data:image/png;base64,{{ base64_encode($qr_code) }}" alt="QR Code">
                @else
                    {!! $qr_code !!}
                @endif
            </th>
        </tr>
    </table>
    <h3 style="text-align:center;border-bottom:2px solid #aaa; padding-bottom:5px">LOCAL PURCHASE ORDER</h3>
    <table class="table no-border">
        <tr>
            <td><strong>LPO No:</strong> {{ $row->purchase_no }}</td>
            <td class="text-center"><strong>Order Date:</strong> {{ date('d.M.Y', strtotime($row->purchase_date)) }}
            </td>
            <td class="text-right"><strong>Delivery Date:</strong> {{ date('d.M.Y', strtotime($row->updated_at)) }}
            </td>
        </tr>
    </table>
    <table class="table table-bordered">
        <tr>
            <th class="text-left">TO</th>
            <th class="text-left">SHIP TO</th>
        </tr>
        <tr>
            <td>
                Supplier name: {{ ucfirst($row->getSupplier->name) }}<br>
                {{ $row->getSupplier->address }}<br>
            </td>
            <td colspan="1">
                {{ $settings['COMPANY_NAME'] }}<br>
                Location: {{ @$row->getStoreLocation->location_name }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="">
                Note: {{ $row->note }}
            </td>
        </tr>
    </table>
    <table class="table table-bordered items">
        <thead>
            <tr>
                <th style="width:5%">
                    Sr
                </th>
                <th style="width:10%">
                    Code
                </th>
                <th style="width:40%">
                    Description
                </th>
                <th style="text-align:right !important;width:6%">
                    Qty
                </th>
                <th style="text-align:right !important;width:8%">
                    Free Stock
                </th>
                <th style="text-align:right !important; width:12%">
                    Price List Cost
                </th>
                <th style="text-align:right !important; width:8%">
                    Disc.
                </th>
                <th style="text-align:right !important; width:10%">
                    Discount
                </th>
                <th style="text-align:right !important; width:14%">
                    Total Cost Inc VAT
                </th>
            </tr>
        </thead>
        <tbody>
            @if ($row->getRelatedItem)
                <?php $i = 1;
                $tax_amount = [];
                $sub_total = [];
                $total_weight = [];
                $invoice_discount = 0;
                $distribution_discount = 0;
                $transport_rebate = 0;
                ?>
                @foreach ($row->getRelatedItem as $getRelatedItem)
                    <tr>
                        <td>
                            {{ $i }}
                        </td>
                        <td>
                            {{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}
                        </td>
                        <td>
                            {{ $getRelatedItem->getInventoryItemDetail->title }}
                        </td>
                        <td style="text-align:right !important;">
                            {{ manageAmountFormat($getRelatedItem->supplier_quantity) }}
                            {{-- {{@$getRelatedItem->getInventoryItemDetail->pack_size->title}} --}}
                        </td>
                        <td style="text-align:right !important;">
                            {{ manageAmountFormat($getRelatedItem->free_qualified_stock) }}
                        </td>
                        <td style="text-align:right !important;">
                            {{ manageAmountFormat($getRelatedItem->order_price) }}
                        </td>
                        <td style="text-align:right !important;">
                            {{-- {{ $getRelatedItem->getInventoryItemDetail->net_weight * $getRelatedItem->supplier_quantity }} --}}
                            {{ manageAmountFormat($getRelatedItem->discount_percentage) }}
                        </td>


                        <td style="text-align:right !important;">
                            {{ manageAmountFormat($getRelatedItem->discount_amount) }}
                        </td>
                        <td style="text-align:right !important;">
                            {{ manageAmountFormat(round($getRelatedItem->order_price * $getRelatedItem->supplier_quantity, 2) - $getRelatedItem->discount_amount) }}
                        </td>
                    </tr>
                    <?php $i++;
                    $total_weight[] = @$getRelatedItem->getInventoryItemDetail->net_weight * $getRelatedItem->supplier_quantity;
                    $tax_amount[] = $getRelatedItem->vat_amount;
                    // $tax_amount[] = ($getRelatedItem->vat_rate / (100 + $getRelatedItem->vat_rate)) * ($getRelatedItem->order_price * $getRelatedItem->supplier_quantity);
                    $sub_total[] = $getRelatedItem->order_price * $getRelatedItem->supplier_quantity - $getRelatedItem->vat_amount - $getRelatedItem->discount_amount;
                    $t = $getRelatedItem->order_price * $getRelatedItem->supplier_quantity - $getRelatedItem->discount_amount;
                    $settings = json_decode($getRelatedItem->discount_settings);
                    if ($settings) {
                        $inv_per = (float) (isset($settings->invoice_percentage) ? $settings->invoice_percentage : 0);
                        $invoice_discount += ($t * $inv_per) / 100;
                        $transport_rebate_per_unit = (float) isset($settings->transport_rebate_per_unit) ? $settings->transport_rebate_per_unit : 0;
                        $transport_rebate_percentage = (float) isset($settings->transport_rebate_percentage) ? $settings->transport_rebate_percentage : 0;
                        $transport_rebate_per_tonnage = (float) isset($settings->transport_rebate_per_tonnage) ? $settings->transport_rebate_per_tonnage : 0;
                        $distribution_discount += (float) isset($settings->distribution_discount) ? $settings->distribution_discount * $getRelatedItem->quantity : 0;
                        if ($transport_rebate_per_unit > 0) {
                            $transport_rebate += $transport_rebate_per_unit * $getRelatedItem->quantity;
                        } elseif ($transport_rebate_percentage > 0) {
                            $transport_rebate += ($t * $transport_rebate_percentage) / 100;
                        } elseif ($transport_rebate_per_tonnage > 0) {
                            $transport_rebate += $transport_rebate_per_tonnage * $getRelatedItem->measure;
                        }
                    }
                    ?>
                @endforeach
                <tr>
                    <td colspan="2"><strong>Tonnage:</strong> </td>
                    <td colspan="4">{{ manageAmountFormat(array_sum($total_weight)) }} kgs</td>
                    <th colspan="2" class="text-right"></th>
                    <td class="text-right"></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Total Items:</strong> </td>
                    <td colspan="4">{{ $i - 1 }}</td>
                    <td colspan="2"></td>
                    <td></td>
                </tr>
            @endif
            <tr>
                <td colspan="6" rowspan="8" class="text-left">
                    <table class="table no-border m-0">
                        <tr>
                            <td width="60%">
                                <h4>Note:</h4>
                                <ol style="margin:0; padding-left:16px;margin-top: 10px;">
                                    <li>Please quote <strong>LPO No.</strong> On the invoice</li>
                                    <li>Please supply as per the LPO</li>
                                    <li>No deliveries will be offloaded after <strong>5:00pm</strong></li>
                                    <li>LPO is valid for <strong>7 days</strong></li>
                                    <li>Quantity on LPO should be the quantity on invoice.</li>
                                    <li>LPO price is the INVOICE price.</li>
                                    <li>THE INVOICE MUST BE A TAX INVOICE.</li>
                                </ol>
                            </td>
                            @if ($row->supplier_own == "OwnCollection")
                                <td class="text-left">
                                    <h4>Driver Details:</h4>
                                    Motor Vehicle: {{ @$row->vehicle->name }} /
                                    {{ @$row->vehicle->license_plate_number }} <br>
                                    Driver Name: {{ @$row->employee->name }} <br>
                                    ID Number: {{ @$row->employee->id_number }} <br>
                                    Phone Number: {{ @$row->employee->phone_number }}
                                </td>
                            @endif
                        </tr>
                    </table>
                </td>
                <th colspan="2">Gross Amount</th>
                <td class="text-right">{{ manageAmountFormat(array_sum($sub_total)) }}</td>
            </tr>
            <tr>
                <th colspan="2">Vat 16%</th>
                <td class="text-right">{{ manageAmountFormat(array_sum($tax_amount)) }}</td>
            </tr>
            @php
                $totalamnt = array_sum($sub_total) + array_sum($tax_amount);
                $roundOff = fmod($totalamnt, 1); //0.25
                if ($roundOff != 0) {
                    if ($roundOff > '0.50') {
                        $roundOff = '+' . round(1 - $roundOff, 2);
                    } else {
                        $roundOff = '-' . round($roundOff, 2);
                    }
                }
            @endphp
            <tr>
                <th colspan="2">Round Off </th>
                <td class="text-right">{{ $roundOff }}</td>
            </tr>
            <tr>
                <th colspan="2">Base Disc.</th>
                <td class="text-right">{{ manageAmountFormat($row->getRelatedItem->sum('discount_amount') ?? 0) }}</td>
            </tr>
            <tr>
                <th colspan="2">Transport Rebate</th>
                <td class="text-right">{{ manageAmountFormat($transport_rebate) }} </td>
            </tr>
            <tr>
                <th colspan="2">Invoice Disc.</th>
                <td class="text-right">{{ manageAmountFormat($invoice_discount) }}</td>
            </tr>
            <tr>
                <th colspan="2">Distribution Disc.</th>
                <td class="text-right">{{ manageAmountFormat($distribution_discount) }}</td>
            </tr>
            <tr>
                <th colspan="2">Total Amount</th>
                <td class="text-right">
                    {{ manageAmountFormat(round(array_sum($sub_total) + array_sum($tax_amount) - $invoice_discount - $distribution_discount - $transport_rebate)) }}
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table no-border">
        <tr>
            <td colspan="3">
                Prepared By: {{ @$row->getrelatedEmployee->name }}
            </td>
            <td colspan="2" style="text-align:right">
                @if (empty(@$row->getrelatedEmployee->e_sign_image))
                    Signature:
                @else
                    <img src="{{ asset('uploads/users/' . @$row->getrelatedEmployee->e_sign_image) }}" alt="E-Sign"
                        style="width: 95px;height:auto">
                @endif
            </td>
        </tr>
        @php
            $rendered = [];
        @endphp
        @foreach ($row->getRelatedAuthorizationPermissions as $permissionResponse)
            @if ($loop->first)
                <tr>
                    <td colspan="5">
                        <b>APPROVALS:</b>
                    </td>
                </tr>
            @endif
            @if (!in_array(@$permissionResponse->getExternalAuthorizerProfile->id, $rendered))
                @php
                    $rendered[] = @$permissionResponse->getExternalAuthorizerProfile->id;
                @endphp
                <tr>
                    <td> Level {{ $permissionResponse->approve_level }}</td>
                    <td> Ok </td>
                    <td> {{ ucfirst(@$permissionResponse->getExternalAuthorizerProfile->name) }}</td>
                    <td> {{ date('m/d/Y H:i A', strtotime($permissionResponse->updated_at)) }}</td>
                    <td style="text-align:right">
                        @if (empty(@$permissionResponse->getExternalAuthorizerProfile->e_sign_image))
                            Signature:
                        @else
                            <img src="{{ asset('uploads/users/' . @$permissionResponse->getExternalAuthorizerProfile->e_sign_image) }}"
                                alt="E-Sign" style="width: 95px;height:auto">
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
    <script type="text/php">
        if ( isset($pdf) ) { 
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 9;
            $pdf->text(270, 780, "Page ".$PAGE_NUM." of ".$PAGE_COUNT, $font, $size);
        }
    </script>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
