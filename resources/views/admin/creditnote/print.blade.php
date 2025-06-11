<html>
<title>Print</title>

<head> 
	<style type="text/css">
	.underline{
		 text-decoration: underline;

	}

	.item_table td{
		border-right: 1px solid;"
		}
		.align_float_right
{
  text-align:  right;
}

.align_float_center
{
  text-align:  center;
}

	</style>
	
</head>
<body>

<?php $all_settings = getAllSettings();?>
<span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
{{ $all_settings['ADDRESS_1']}}<br>
{{ $all_settings['ADDRESS_2']}}<br>
{{ $all_settings['ADDRESS_3']}}<br>
Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
{{ $all_settings['EMAILS']}}<br>
{{ $all_settings['WEBSITE']}}<br>
Pin No: {{ $all_settings['PIN_NO']}}<br>
Vat No: {{ $all_settings['VAT_NO']}}<br><br>




<b ><span class="underline">Credit Note No: </span>    &nbsp;&nbsp;&nbsp;&nbsp; {{ $row->credit_note_number}}</b>
<br><br>
<table style="border: 1px solid !important;" width="100%">
	<tr>
		<td>
			Customer name: {{ ucfirst($row->getRelatedCustomer->customer_name)}}<br>
			Address: {{ $row->getRelatedCustomer->address}}<br>
			Phone: {{ $row->getRelatedCustomer->telephone}}<br>
		</td>
		<td style="text-align: right;" colspan="6">
		Order Date:{{ date('d.M.Y',strtotime($row->order_date))}}<br>
		
		S.O No: {{ $row->credit_note_number}}
		</td>
	</tr>
	
	

</table>
<table width="100%" style="border-bottom: 1px solid !important;" >

	
	<tr style="font-weight: bold;font-size: 12px;">
	<td width= "5%" class="align_float_center">
			Sn.
		</td>
		<td width= "31%" class="align_float_center"> 
			Article
		</td>
		<td  width= "8%" class="align_float_center">
			Qty
		</td>
		<td  width= "12%" class="align_float_center">
			Unit
		</td>
		<td  width= "12%" class="align_float_center">
			Unit Price
		</td>

		<td width= "10%" class="align_float_center">
			Discount %
		</td>
		<td width= "10%" class="align_float_center">
			VAt %
		</td>
		
		<td width= "12%" class="align_float_center">
			Gross 
		</td>
		
	</tr>

</table>
<br>
<span class="underline">Kindly supply the items listed below:</span>
<table width="100%" style="border: 1px solid;" class="item_table">
 @if($row->getRelatedItem)
 <?php $i = 1;
 $vat_amount = [];
 $service_charge_amount = [];
 $catering_levy_amount = [];
 $sub_total = [];
 ?>
 @foreach($row->getRelatedItem as $getRelatedItem)
<tr style="font-size: 12px;">
	<td width= "5%" class="align_float_center">
			{{ $i }}
		</td>
		<td  width= "31%" class="align_float_center">
			{{ $getRelatedItem->note}}
		</td>
		<td  width= "8%" class="align_float_center">
			{{ manageAmountFormat($getRelatedItem->quantity) }}
		</td>
		<td  width= "12%" class="align_float_center">
			{{ $getRelatedItem->getUnitOfMeasure->title}}
		</td>
		<td  width= "12%" class="align_float_right">
			{{ manageAmountFormat($getRelatedItem->actual_unit_price) }}
		</td>

		<td width= "10%" class="align_float_right">
			{{ manageAmountFormat($getRelatedItem->discount_percent) }}
		</td>
		<td width= "10%" class="align_float_right">
			
				{{ manageAmountFormat($getRelatedItem->vat_rate) }}
		</td>
		
		<td width= "12%" class="align_float_right">
			{{ manageAmountFormat($getRelatedItem->total_cost_with_vat ) }}
		</td>
		
	</tr>
	<?php  $i++ ; 
	$vat_amount[] = $getRelatedItem->vat_amount;
	$service_charge_amount[] = $getRelatedItem->service_charge_amount;
	$catering_levy_amount[] = $getRelatedItem->catering_levy_amount;
	$sub_total[] = $getRelatedItem->total_cost_with_vat ;

	?>
	@endforeach

	@endif

</table>
<hr>

<table style="border: 1px solid;" width="100%">
	<tr>
		<td>
		

		</td>

		<td style="text-align: right">
			<span >

				Net Amount &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($sub_total)-(array_sum($vat_amount)+array_sum($service_charge_amount)+array_sum($catering_levy_amount)))}}<br>
				VAT (16%) &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($vat_amount))}}<br>
				Service Charge (10%) &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($service_charge_amount))}}<br>
				Catering Levy (2%) &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($catering_levy_amount))}}<br>
				<b>Total Amount </b>&nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($sub_total))}}<br>
			</span>
		</td>
	</tr>

	
	


	<!--tr>

		<td colspan="2">
			Printed By: &nbsp; &nbsp; &nbsp; &nbsp; {{ getLoggeduserProfile()->name }}
		</td>
	</tr-->


</table>
<hr>
{{-- 
<table style="border: 1px solid;" width="100%">
	<tr>
		<td colspan="2">
		<span style="font-family: Times New Roman;">
		<b style="text-decoration: underline;">Conditions:</b><br>

		1.Invoice must be paid full with in 7 days before the date of event<br>
		2.Cheques should be written in favor of DARI Limited<br>
		3.Personal cheques are not acceptable unless prior arrangements have been made.<br>
		4.All rates are inclusive of the statutory taxes and levies-16% VAT, 10% service charge and 2% training levy.<br>
		5.Payments are payable by cash, Mpesa (buy goods and services-till no 202574), Visa or to the account below:<br></span>
		Account Name: DARI LIMITED.<br> 
		Bank: STANDARD CHARTERED<br>
		Branch: KAREN<br>
		Account No: 0102023611901<br>
		<span style="font-family: Times New Roman;">Thank you for choosing Dari Restaurant and we look forward to hosting you again.</span>


		</td>

		
			
	</tr>

	
	


	<!--tr>

		<td colspan="2">
			Printed By: &nbsp; &nbsp; &nbsp; &nbsp; {{ getLoggeduserProfile()->name }}
		</td>
	</tr-->


</table> --}}

</body>
</html>