<!DOCTYPE html>
<html>
<head>



</head>
<body>

<div style="width: 90%;margin:auto;font-size: 12px; ">
<table width="100%">
	<tr><td><div align="center"><b>Daily RVC Sales Detail</b></div></td></tr>
	<tr><td ><div style="text-align: center;"> @if(isset($restro_name)) {{ $restro_name}} @endif</div> <div style="text-align: right;"> {{getLoggeduserProfile()['name']}}</div></td></tr>


</table>

<table width="100%" style="border-bottom: 2px solid black;">
	<tr>
		<td style="text-align: left;">Period From: @if(isset($start_date)) {{ date('d/m/Y',strtotime($start_date))}} @endif To: @if(isset($end_date)) {{ date('d/m/Y',strtotime($end_date))}} @endif </td>
		<td style="text-align: right;">Printed on {{ date('d/m/Y h:i A')}} </td>
	</tr>
	
</table>

<div width="100%" style="margin-top: 5px;background-color: black;color: white;text-align: center;">@if(isset($restro_name)) {{ $restro_name}} @endif</div>

<table width="100%" style="border:1px solid;">
	
	<tr>


		<td width="50%" style="border-right: 1px solid;">
			<table width="100%">
				<tr>
					<td style="text-align: left;">&nbsp;Net Sales</td>
					<td style="text-align: right;"><?php
						$total_net_sales = [];
						foreach($mjGroupDetail as $netSaleArr)
						{
							$total_net_sales[] = $netSaleArr['net_sale'];
						}
						echo manageAmountFormat(array_sum($total_net_sales));
						?></td>

				</tr>

				<tr>
					<td style="text-align: left;">+Service Charges</td>
					<td style="text-align: right;">00</td>
					
				</tr>
				<tr>
					<td style="text-align: left;">+Tax Collected</td>
					<td style="text-align: right;">
						<?php
						$total_charge = [];
						foreach($mjGroupDetail as $chargeArr)
						{
							$total_charge[] = $chargeArr['total_charges'];
						}
						echo manageAmountFormat(array_sum($total_charge));
						?>


					</td>
					
				</tr>

				<tr>
					<td style="text-align: left;font-weight: bold;">=Total Revenue</td>
					<td style="text-align: right;font-weight: bold;">{{ manageAmountFormat(array_sum($total_net_sales)+array_sum($total_charge))}}</td>
					
				</tr>

				<tr >
					<td style="text-align: left;">&nbsp;&nbsp;</td>
					<td style="text-align: right;"></td>
					
				</tr>

				<tr>
					<td style="text-align: left;">Item Discount</td>
					<td style="text-align: right;">
						
						<?php 

				
				$total_discount_amount_arr = [];
				foreach($discount_record_arr as $discount_data)
				{
					$total_discount_amount_arr[] = $discount_data['total_amount'];
				}
				echo '-'.manageAmountFormat(array_sum($total_discount_amount_arr));
				?>

					</td>
					
				</tr>
				<tr>
					<td style="text-align: left;">+Subtotal Discount</td>
					<td style="text-align: right;">00</td>
					
				</tr>

				<tr>
					<td style="text-align: left;font-weight: bold;">=Total Discounts</td>
					<td style="text-align: right;font-weight: bold;">{{  '-'.manageAmountFormat(array_sum($total_discount_amount_arr)) }}</td>
					
				</tr>
			</table>

		</td>



		<td width="50%">
			<table width="100%">
				<tr>
					<td style="text-align: left;">Voids</td>
					<td style="text-align: center;">{{$cancledOrdertSummary['count']}}</td>
					<td style="text-align: right;">{{manageAmountFormat($cancledOrdertSummary['amount'])}}</td>

				</tr>

				<tr>
					<td colspan="3">&nbsp;</td>
					

				</tr>

				<tr>
					<td style="text-align: left;">Rounding Total</td>
					<td style="text-align: center;"></td>
					<td style="text-align: right;">0.00</td>

				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
					

				</tr>
				<tr>
					<td style="text-align: left;">Complementary</td>
					<td style="text-align: center;">{{$discount_record_arr['Complementary']['no_of_transactions']}}</td>
					<td style="text-align: right;">{{manageAmountFormat($discount_record_arr['Complementary']['total_amount'])}}</td>

				</tr>

				
			</table>

		</td>
	</tr>
	
</table>

<fieldset>
	<legend>Tracking</legend>

	<table width="100%">
		<tr>
			<td width="33%" style="border-right: 1px solid;">

				<table width="100%" style="font-size: 10px;">
				<?php 
				$totalPaymentMethodAmount = [];
				$totalPaymentMethodQuantity = [];
				?>
				@foreach($paymentDataSummary as $pMethodSummary)
					<tr>
						<td width="33%" style="text-align: left;">{{ $pMethodSummary['payment_mode']}}</td>
						<td width="33%" style="text-align: right;">{{ $pMethodSummary['number_of_transaction']}}</td>
						<td width="34%"  style="text-align: right;">{{ manageAmountFormat($pMethodSummary['amount'])}}</td>
					</tr>
					<?php 
				$totalPaymentMethodAmount[] = $pMethodSummary['amount'];
				$totalPaymentMethodQuantity[] = $pMethodSummary['number_of_transaction'];
				?>
				@endforeach



					<tr>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">Subtotal</td>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ array_sum($totalPaymentMethodQuantity) }}</td>
						<td width="34%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ manageAmountFormat(array_sum($totalPaymentMethodAmount))}}</td>
					</tr>






				</table>



			</td>
			<td width="33%">

			<table width="100%" style="border-right: 1px solid;">

				<?php 

				$total_discount_qty = [];
				$total_discount_amount = [];
				?>

					@foreach($discount_record_arr as $discount_name =>$discount_data)
					<tr>
						<td width="33%" style="text-align: left;">{{ str_replace("_",' ',$discount_name) }}</td>
						<td width="33%" style="text-align: right;">{{ $discount_data['no_of_transactions']}}</td>
						<td width="34%"  style="text-align: right;">{{ '-'.manageAmountFormat($discount_data['total_amount'])}}</td>
					</tr>
					<?php 

				$total_discount_qty[] = $discount_data['no_of_transactions'];
				$total_discount_amount[] = $discount_data['total_amount'];
				?>
					@endforeach



					<tr>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">Subtotal</td>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ array_sum($total_discount_qty)}}</td>
						<td width="34%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ '-'.manageAmountFormat(array_sum($total_discount_amount))}}</td>
					</tr>






				</table>


			</td>



			<td width="34%">

				<table width="100%">
				<?php 
				$totalMjQty = [];
				$totalMjAmount = [];
				?>

				@foreach($mjGroupDetail as $mjGroupData)
					<tr>
						<td width="33%" style="text-align: left;">{{ $mjGroupData['title']}}</td>
						<td width="33%" style="text-align: right;">{{ $mjGroupData['item_total_quantity']}}</td>
						<td width="34%"  style="text-align: right;">{{ manageAmountFormat($mjGroupData['gross_sale'])}}</td>
					</tr>
						<?php 
				$totalMjQty[] = $mjGroupData['item_total_quantity'];
				$totalMjAmount[] = $mjGroupData['gross_sale'];
				?>
				@endforeach



					<tr>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">Subtotal</td>
						<td width="33%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ array_sum($totalMjQty)}}</td>
						<td width="34%"  style="border-top: 2px solid;text-align: right;font-weight: bold;">{{ manageAmountFormat(array_sum($totalMjAmount))}}</td>
					</tr>






				</table>



			</td>
		</tr>



	</table>

</fieldset>

</div>




</body>
</html>
