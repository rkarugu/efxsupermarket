<style>
table {
    font-family: arial, sans-serif;
    
}

td, th {
    text-align: left;
    padding: 2px 8px;
}
.managepaddingfotboth{
padding-top: 15px;
padding-bottom: 15px;

}
.managepaddingtop{
padding-top: 15px;
}
.managepaddingbottom{
padding-bottom: 15px;
}
</style>

<?php $all_settings = getAllSettings();
//	echo print_r($all_settings); die;

?>
<div style="width: 100%;" class="clearfix" id="div_content">
  <table class="table" style="width: 100%;">



    <tr><td colspan="3" style="text-align: center;">
  <b>{!! strtoupper($all_settings['COMPANY_NAME'])!!} </b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
  <b>{!! strtoupper($all_settings['ADDRESS_1'])!!} {!! strtoupper($all_settings['ADDRESS_3'])!!}, {!! strtoupper($all_settings['ADDRESS_3'])!!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
   <b>{!! $all_settings['PHONE_NUMBER'] !!}</b>
  </td>
</tr>

<tr>
  <td colspan="3" >
      Customer name: {{ ucfirst($row->getRelatedCustomer->customer_name)}}  </td>
</tr>

<tr>
  <td colspan="3" >
      Invoice No. {{ ucfirst($row->sales_invoice_number)}}  </td>
</tr>
<tr>
  <td colspan="3" >
      Cashier Name: {{ ucfirst(getLoggeduserProfile()->name) }}  </td>
</tr>


<tr  colspan="3" style ="border:1px solid black;"></tr>


  </table>
</div>
<hr>
<div style="width: 100%;" class="clearfix">
<table style="width:100%;">
  @if($row->getRelatedItem)
 <?php $i = 1;
	// echo "<pre>"; print_r($row->getRelatedItem); die;
 $vat_amount = [];
 $service_charge_amount = [];
 $catering_levy_amount = [];
 $sub_total = [];
 ?>
 @foreach($row->getRelatedItem as $getRelatedItem)
<!--  -->
  <tr>
    <td style=" text-align: left;">{{ $i }} {{ $getRelatedItem->item_name}}</td>
    <td style=" text-align: right;">
      {{ manageAmountFormat($getRelatedItem->actual_unit_price) }}
         </td>
  </tr>
  <?php  $i++ ; 
  $vat_amount[] = $getRelatedItem->vat_amount;
  $total_cost[] = $getRelatedItem->total_cost;
  $service_charge_amount[] = $getRelatedItem->service_charge_amount;
  $catering_levy_amount[] = $getRelatedItem->catering_levy_amount;
  $sub_total[] = $getRelatedItem->total_cost_with_vat ;

  ?>
  @endforeach

  @endif
</table>
</div>
<hr>

<div style="width: 100%;" >
	<table style="width: 100%;">
	<tr> 
		<td colspan="2">Amount Ex. VAT</td>
		<td style="text-align: right;"><b>{!! manageAmountFormat(array_sum($total_cost)-array_sum($vat_amount))!!}</b></td>
	</tr>
	<tr> 
		<td colspan="2">VAT Amount </td>
		<td style="text-align: right;"><b>{!! manageAmountFormat(array_sum($vat_amount))!!}</b></td>
	</tr>
	<tr> 
		<td colspan="2">Total Amount</td>
		<td style="text-align: right;"><b>{!! manageAmountFormat(array_sum($total_cost))!!}</b></td>
	</tr>
</table>
 
</div>
