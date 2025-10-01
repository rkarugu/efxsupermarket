<html>
<title>Print Order</title>

<head>
    <style type="text/css">
        body {
            font-family: arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            text-align: left;
            padding: 4px 8px;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        hr {
            border: 1px solid #000;
            margin: 5px 0;
        }

        .customer-details {
            line-height: 1.4;
            padding: 10px 0;
        }

        .invoice-box {
            margin: auto;
            font-size: 12px;
            line-height: 20px;
            font-family: arial, sans-serif;
            color: #000;
        }

    </style>

</head>
<body>

<div class="invoice-box">
    <table style="width: 100%;">
        <!-- Company Name & Address (centered) -->
        <tr>
            <td colspan="4" class="center">
                <b style="font-size: 18px;">{{ strtoupper($all_settings['COMPANY_NAME']) }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="center">
                <b>SALES ORDER</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="center">
                <b>{{ $all_settings['ADDRESS_1'] }}, {{ $all_settings['ADDRESS_2'] }}</b>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="center">
                <b>Mobile: {{ $all_settings['PHONE_NUMBER'] ?? '0740804489' }}</b>
            </td>
        </tr>
        
        
        <!-- Horizontal Line -->
        <tr>
            <td colspan="4"><hr></td>
        </tr>
        
        <!-- Customer Details Section -->
        <tr>
            <td colspan="4" style="text-align: left; padding: 10px 0; line-height: 1.4;">
                @php
                    $customer = $list->getRouteCustomer;
                    $shift = $list->shift;
                @endphp
                <b>Order No.: {{$list->requisition_no}}</b><br>
                <b>Company PIN: {{$all_settings['PIN_NO'] ?? 'Https://testing.com'}}</b><br>
                <b>Customer PIN: {{$customer->kra_pin ?? ''}}</b><br>
                <b>Customer Name: {{$customer->bussiness_name ?? $customer->name ?? 'N/A'}}</b><br>
                <b>Date: {{date('d/m/Y H:i', strtotime($list->created_at))}}</b><br>
                <b>Served By: {{$list->getrelatedEmployee->name ?? 'N/A'}}</b><br>
                <b>Customer Account: {{$customer->account_type ?? 'BUSINESS'}}</b><br>
                <b>Mobile: {{$customer->phone ?? '0700'}}</b><br>
                <b>B/F: KSh {{number_format($customer->balance ?? 0, 2)}}</b>
            </td>
        </tr>
        
        <!-- Horizontal Line -->
        <tr>
            <td colspan="4"><hr></td>
        </tr>
    </table>

    <!-- Items Table -->
    <table style="width:100%; border-collapse: collapse;">
        <!-- Table Headers -->
        <tr style="border-bottom: 2px solid #000;">
            <td style="font-weight: bold; text-align: left; padding: 8px;"><b>Item</b></td>
            <td style="font-weight: bold; text-align: left; padding: 8px;"><b>Qty</b></td>
            <td style="font-weight: bold; text-align: left; padding: 8px;"><b>Price</b></td>
            <td style="font-weight: bold; text-align: right; padding: 8px;"><b>Amount</b></td>
        </tr>
        @php
            $gross_amount = 0;
            $totalDiscount = 0;
            $totalVat = 0;
            $totalItems = count($list->getRelatedItem);
        @endphp
        
        @foreach($list->getRelatedItem as $index => $item)
            <!-- Item Row -->
            <tr>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>{{$index + 1}} {{strtoupper($item->getInventoryItemDetail->title)}}</b><br>
                    <b>{{$item->getInventoryItemDetail->pack_size->title ?? 'Pc(s)'}}</b>
                </td>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>{{number_format($item->quantity, 2)}}</b>
                </td>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>x {{number_format($item->unit_cost, 2)}}</b>
                </td>
                <td style="font-weight: bold; text-align: right; padding: 8px; vertical-align: top;">
                    <b>{{number_format($item->total_cost_with_vat, 2)}}</b>
                </td>
            </tr>
            
            @if($index < $totalItems - 1)
            <!-- Horizontal Line between items -->
            <tr>
                <td colspan="4" style="padding: 0;"><hr style="border: 1px solid #000; margin: 5px 0;"></td>
            </tr>
            @endif

            @php
                $gross_amount += $item->total_cost_with_vat;
                $totalDiscount += $item->discount ?? 0;

                // Calculate VAT
                $vat = 0;
                if ($item->getInventoryItemDetail->taxManager && $item->getInventoryItemDetail->taxManager->tax_value > 0) {
                    $taxRate = $item->getInventoryItemDetail->taxManager->tax_value;
                    if ($item->getInventoryItemDetail->taxManager->tax_format === 'PERCENTAGE') {
                        $itemSubtotal = $item->quantity * $item->unit_cost - ($item->discount ?? 0);
                        $vat = ($itemSubtotal * $taxRate) / 100;
                    }
                }
                $totalVat += $vat;
            @endphp
        @endforeach
    </table>

    <hr>

    <!-- Summary Section -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px; width: 70%;"><b>No of Items</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px; width: 30%;"><b>{{count($list->getRelatedItem)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>Subtotal:</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($gross_amount - $totalVat, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>VAT</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($totalVat, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>TOTAL ORDER AMOUNT:</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($gross_amount, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>CURRENT DUE AMOUNT</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($gross_amount, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>ACCOUNT BALANCE</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($customer->balance ?? 0, 2)}}</b></td>
        </tr>
    </table>

    <hr>

    <!-- CU INFORMATION Section -->
    <div style="width: 100%; text-align: center; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; font-size: 16px; padding: 10px;">
                    <b>ORDER INFORMATION</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>Date: {{date('d/m/Y', strtotime($list->created_at))}} Time: {{date('H:i:s A', strtotime($list->created_at))}}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>ORDER ID: {{$list->requisition_no ?? $list->id}}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>SALESMAN: {{$list->getrelatedEmployee->name ?? 'N/A'}}</b>
                </td>
            </tr>
            @if($shift)
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>SHIFT: {{$shift->shift_name ?? 'N/A'}}</b>
                </td>
            </tr>
            @endif
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>STATUS: {{strtoupper($list->status)}}</b>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 30px; font-size: 10px;">
        <p><b>Thank you for your business!</b></p>
        <p>This is a computer generated document.</p>
    </div>

</div>

<script type="text/javascript">
    window.print();
</script>

</body>
</html>
