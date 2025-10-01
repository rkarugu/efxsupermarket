<html>
<title>Print</title>

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

<?php $all_settings = getAllSettings();
$getLoggeduserProfile = getLoggeduserProfile();
?>
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
                <b>INVOICE</b>
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
        
        @if ($list->print_count > 1)
        <tr>
            <td colspan="4" class="center">
                <b>REPRINT INVOICE COUNT: {{$list->print_count - 1}}</b>
            </td>
        </tr>
        @endif
        
        <!-- Horizontal Line -->
        <tr>
            <td colspan="4"><hr></td>
        </tr>
        
        <!-- Customer Details Section -->
        <tr>
            <td colspan="4" style="text-align: left; padding: 10px 0; line-height: 1.4;">
                @php
                    $customer = $list->get_customer;
                    $shift_id = \App\SalesmanShift::find($list->shift_id);
                @endphp
                <b>Invoice No.: {{$list->transfer_no}}</b><br>
                <b>Company PIN: {{$all_settings['PIN_NO'] ?? 'Https://testing.com'}}</b><br>
                <b>Customer PIN: {{$list->customer_pin ?? $customer->kra_pin ?? ''}}</b><br>
                <b>Customer Name: {{$customer->customer_name ?? $list->name}}</b><br>
                <b>Date: {{date('d/m/Y H:i', strtotime($list->transfer_date))}}</b><br>
                <b>Served By: {{$list->user->name ?? getLoggeduserProfile()->name}}</b><br>
                <b>Customer Account: {{$customer->account_type ?? 'TEST BUSINESS'}}</b><br>
                <b>Mobile: {{$list->customer_phone_number ?? $customer->telephone ?? '0700'}}</b><br>
                <b>B/F: KSh {{number_format($customer->balance ?? 100, 2)}}</b>
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
            $TONNAGE = 0;
            $gross_amount = 0;
            $totalDiscount = 0;
            $netAmount = 0;
            $totalVat = 0;
            $totalItems = count($list->getRelatedItem);
        @endphp
        
        @foreach($list->getRelatedItem as $index => $item)
            <!-- Item Row -->
            <tr>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>{{$index + 1}} {{strtoupper($item->getInventoryItemDetail->title)}}</b><br>
                    <b>Pc(s)</b>
                </td>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>{{number_format($item->quantity, 2)}}</b>
                </td>
                <td style="font-weight: bold; text-align: left; padding: 8px; vertical-align: top;">
                    <b>x {{number_format($item->selling_price, 2)}}</b>
                </td>
                <td style="font-weight: bold; text-align: right; padding: 8px; vertical-align: top;">
                    <b>{{number_format($item->quantity * $item->selling_price, 2)}}</b>
                </td>
            </tr>
            
            @if($index < $totalItems - 1)
            <!-- Horizontal Line between items -->
            <tr>
                <td colspan="4" style="padding: 0;"><hr style="border: 1px solid #000; margin: 5px 0;"></td>
            </tr>
            @endif

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
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>TOTAL INVOICE AMNT:</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($gross_amount, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>CURRENT DUE AMOUNT</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format($gross_amount, 2)}}</b></td>
        </tr>
        <tr> 
            <td style="text-align: left; font-weight: bold; padding: 8px;"><b>ACCOUNT BALANCE</b></td>
            <td style="text-align: right; font-weight: bold; padding: 8px;"><b>KSh {{number_format(100, 2)}}</b></td>
        </tr>
    </table>

    <hr>

    <!-- CU INFORMATION Section -->
    <div style="width: 100%; text-align: center; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; font-size: 16px; padding: 10px;">
                    <b>CU INFORMATION</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>Date: {{date('d/m/Y', strtotime($list->transfer_date))}} Time: {{date('H:i:s A', strtotime($list->transfer_date))}}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>SCU ID:</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>DIGITAX ID: sale_01K15DQMBB8RT9JTK9FP1K6J2H</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>SCU INVOICE NO: {{$list->transfer_no ?? '210'}}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>Internal Data:</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 5px;">
                    <b>Receipt Signature:</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding: 20px;">
                    <!-- Static QR Code -->
                    @php
                        $qrCodeUrl = "https://itax.kra.go.ke/KRA-Portal/invoiceChk.htm?actionCode=loadPage&invoiceNo=" . $list->transfer_no;
                    @endphp
                    @if(isset($is_print))
                        {{ QrCode::size(100)->generate($qrCodeUrl) }}
                    @else
                        <img src="data:image/png;base64, {!! base64_encode(QrCode::size(100)->generate($qrCodeUrl)) !!} ">
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; font-weight: bold; padding: 10px;">
                    <b>MPESA TILL NO: 166538 NO CASH PAYMENT ON DELIVERY!</b>
                </td>
            </tr>
        </table>
    </div>


</div>

</body>
</html>
