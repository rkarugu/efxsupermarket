<style>
table {
    font-family: arial, sans-serif;
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
</style>

<?php $all_settings = getAllSettings(); ?>

<div style="width: 100%;" class="clearfix" id="div_content">
  <table class="table" style="width: 100%;">
    <!-- Company Name & Address (centered) -->
    <tr>
        <td colspan="4" class="center">
            <b style="font-size: 18px;">{!! strtoupper($all_settings['COMPANY_NAME'])!!}</b>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="center">
            <b>INVOICE</b>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="center">
            <b>{!! strtoupper($all_settings['ADDRESS_1'])!!}, {!! strtoupper($all_settings['ADDRESS_2'])!!}</b>
        </td>
    </tr>
    <tr>
        <td colspan="4" class="center">
            <b>Mobile: {!! $all_settings['PHONE_NUMBER'] ?? '0740804489' !!}</b>
        </td>
    </tr>
    
    @if($row->print_count > 1)
    <tr>
        <td colspan="4" class="center">
            <b>REPRINT INVOICE COUNT: {{$row->print_count - 1}}</b>
        </td>
    </tr>
    @endif
    
    <!-- Horizontal Line -->
    <tr>
        <td colspan="4"><hr></td>
    </tr>
    
    <!-- Customer Details Section -->
    <tr>
        <td colspan="4" style="text-align: left; padding: 10px 0;">
            <div style="line-height: 1.4;">
                <b>Invoice No.: {{$row->sales_invoice_number}}</b><br>
                <b>Company PIN: {{$all_settings['PIN_NO'] ?? 'Https://testing.com'}}</b><br>
                <b>Customer PIN:</b><br>
                <b>Customer Name: {{ucfirst($row->getRelatedCustomer->customer_name ?? 'N/A')}}</b><br>
                <b>Date: {{date('d/m/Y H:i', strtotime($row->created_at))}}</b><br>
                <b>Served By: {{ucfirst(getLoggeduserProfile()->name)}}</b><br>
                <b>Salesman name: {{ucfirst(getLoggeduserProfile()->name)}}</b><br>
                <b>Customer Account: {{$row->getRelatedCustomer->account_type ?? 'TEST BUSINESS'}}</b><br>
                <b>Mobile: {{$row->getRelatedCustomer->phone_number ?? '0700'}}</b><br>
                <b>B/F: KSh {{number_format($row->getRelatedCustomer->balance ?? 100, 2)}}</b>
            </div>
        </td>
    </tr>
    
    <!-- Horizontal Line -->
    <tr>
        <td colspan="4"><hr></td>
    </tr>
  </table>
</div>
<!-- Items Table -->
<div style="width: 100%;" class="clearfix">
<table style="width:100%; border-collapse: collapse;">
    <!-- Table Headers -->
    <tr style="border-bottom: 1px solid #000;">
        <td style="font-weight: bold; text-align: left; padding: 5px;"><b>Uom</b></td>
        <td style="font-weight: bold; text-align: left; padding: 5px;"><b>Qty</b></td>
        <td style="font-weight: bold; text-align: left; padding: 5px;"><b>Price</b></td>
        <td style="font-weight: bold; text-align: right; padding: 5px;"><b>Amount</b></td>
    </tr>
    
    @if($row->getRelatedItem)
    <?php 
    $i = 1;
    $vat_amount = [];
    $total_cost = [];
    $service_charge_amount = [];
    $catering_levy_amount = [];
    $sub_total = [];
    $totalItems = count($row->getRelatedItem);
    ?>
    
    @foreach($row->getRelatedItem as $index => $getRelatedItem)
    <!-- Item Row -->
    <tr>
        <td style="font-weight: bold; text-align: left; padding: 5px;">
            <b>{{strtoupper($getRelatedItem->item_name ?? 'SOLAI MMEAL 12X2KG BALE')}}</b><br>
            <b>Pc(s)</b>
        </td>
        <td style="font-weight: bold; text-align: left; padding: 5px;">
            <b>{{$getRelatedItem->quantity ?? '1.00'}}</b>
        </td>
        <td style="font-weight: bold; text-align: left; padding: 5px;">
            <b>x {{number_format($getRelatedItem->actual_unit_price ?? 2000, 2)}}</b>
        </td>
        <td style="font-weight: bold; text-align: right; padding: 5px;">
            <b>{{number_format(($getRelatedItem->quantity ?? 1) * ($getRelatedItem->actual_unit_price ?? 2000), 2)}}</b>
        </td>
    </tr>
    
    @if($index < $totalItems - 1)
    <!-- Horizontal Line between items -->
    <tr>
        <td colspan="4"><hr style="margin: 2px 0;"></td>
    </tr>
    @endif
    
    <?php  
    $i++; 
    $vat_amount[] = $getRelatedItem->vat_amount ?? 0;
    $total_cost[] = $getRelatedItem->total_cost ?? (($getRelatedItem->quantity ?? 1) * ($getRelatedItem->actual_unit_price ?? 2000));
    $service_charge_amount[] = $getRelatedItem->service_charge_amount ?? 0;
    $catering_levy_amount[] = $getRelatedItem->catering_levy_amount ?? 0;
    $sub_total[] = $getRelatedItem->total_cost_with_vat ?? (($getRelatedItem->quantity ?? 1) * ($getRelatedItem->actual_unit_price ?? 2000));
    ?>
    @endforeach
    @endif
</table>
</div>
<hr>

<!-- Summary Section -->
<div style="width: 100%;">
	<table style="width: 100%; border-collapse: collapse;">
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>No of Items</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>{{count($row->getRelatedItem ?? [])}}</b></td>
	</tr>
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>Subtotal:</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>KSh {{number_format(array_sum($total_cost) - array_sum($vat_amount), 2)}}</b></td>
	</tr>
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>VAT</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>KSh {{number_format(array_sum($vat_amount), 2)}}</b></td>
	</tr>
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>TOTAL INVOICE AMNT:</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>KSh {{number_format(array_sum($total_cost), 2)}}</b></td>
	</tr>
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>CURBET DUE AMOUNT</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>KSh {{number_format(array_sum($total_cost), 2)}}</b></td>
	</tr>
	<tr> 
		<td style="text-align: left; font-weight: bold; padding: 5px;"><b>ACCOUNT BALANCE</b></td>
		<td style="text-align: right; font-weight: bold; padding: 5px;"><b>KSh {{number_format($row->getRelatedCustomer->balance ?? 100, 2)}}</b></td>
	</tr>
</table>
</div>

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
                <b>Date: {{date('d/m/Y')}} Time: {{date('H:i:s A')}}</b>
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
                <b>SCU INVOICE NO: {{$row->sales_invoice_number ?? '210'}}</b>
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
                <!-- QR Code placeholder -->
                <div style="width: 100px; height: 100px; border: 2px solid #000; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <b>QR CODE</b>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; padding: 10px;">
                <b>MPESA TILL NO: 166538 NO CASH PAYMENT ON DELIVERY!</b>
            </td>
        </tr>
    </table>
</div>
