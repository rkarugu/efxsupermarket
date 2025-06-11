
<html>
<title>Print</title>

<head>
	<style type="text/css">
	.underline{
		 text-decoration: underline;

	}

	.item_table td{
		border-right: 1px solid;
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




<b ><span class="underline">LOCAL PURCHASE ORDER NO: </span>    &nbsp;&nbsp;&nbsp;&nbsp; {{ $row->purchase_no}}</b>
<br><br>

<table style="border: 1px solid !important;" width="100%">
	<tr>
		<td>
			Supplier name: {{ ucfirst($row->getSupplier->name)}}<br>
			{{ $row->getSupplier->address}}<br>
		</td>
		<td style="text-align: right;" colspan="6">
		Order Date:{{ date('d.M.Y',strtotime($row->purchase_date))}}<br>
		Delivery Date:{{ date('d.M.Y',strtotime($row->updated_at))}}<br>
		P.R No: {{ $row->purchase_no}}
		</td>
	</tr>
	<tr>
		<td colspan="7" style="border-top: 1px solid;">
			Dept/Store {{ $row->getDepartment->department_name}} - {{ $row->getStoreLocation->location_name}}
		</td>
		
	</tr>
	

</table>

<table width="100%" style="border-bottom: 1px solid !important;" >

	
	<tr style="font-weight: bold;" >
	<td width= "5%" class="align_float_center">
			Sn.
		</td>
		<td width= "20%" class="align_float_center"> 
			Article
		</td>
		<td  width= "10%" class="align_float_center">
			Qty
		</td>
		<td  width= "15%" class="align_float_center">
			Unit
		</td>
		<td  width= "15%" class="align_float_center">
			Unit Price
		</td>

		<td width= "10%" class="align_float_center">
			Discount %
		</td>
		<td width= "10%" class="align_float_center">
			VAt %
		</td>
		<td width= "15%" class="align_float_center">
			Total Price Inc VAT 
		</td>
		
	</tr>

</table>

<br>
<span class="underline">Kindly supply the items listed below:</span>
<table width="100%" style="border: 1px solid;" class="item_table">
 @if($row->getRelatedItem)
 <?php $i = 1;
 $tax_amount = [];
 $sub_total = [];
 ?>
 @foreach($row->getRelatedItem as $getRelatedItem)
<tr>
	<td width= "5%" class="align_float_center">
			{{ $i }}
		</td>
		@if($getRelatedItem->item_type == 'Stock')
		<td  width= "20%" class="align_float_center">
			{{ $getRelatedItem->getInventoryItemDetail->title }}
		</td>
		@else
		<td  width= "20%" class="align_float_center">
			{{ $getRelatedItem->getNonStockItemDetail->gl_code->account_name }}
		</td>
		@endif
		<td  width= "10%" class="align_float_center">
			{{ manageAmountFormat($getRelatedItem->supplier_quantity) }}
		</td>
		<td  width= "15%" class="align_float_center">
			{{ $getRelatedItem->getSupplierUomDetail->title }}
		</td>
		<td  width= "15%" class="align_float_right">
			{{ $getRelatedItem->order_price }}
		</td>

		<td width= "10%" class="align_float_right">
			0.00%
		</td>
		<td width= "10%" class="align_float_right">
			{{ $getRelatedItem->vat_rate }}
		</td>
		<td width= "15%" class="align_float_right">
			{{ manageAmountFormat($getRelatedItem->vat_amount+($getRelatedItem->order_price*$getRelatedItem->supplier_quantity)) }}
		</td>
		
	</tr>
	<?php  $i++ ; 
	$tax_amount[] = $getRelatedItem->vat_amount;
	$sub_total[] = $getRelatedItem->order_price*$getRelatedItem->supplier_quantity ;

	?>
	@endforeach

	@endif

</table>
<hr>

<table style="border: 1px solid;" width="100%">
	<tr>
		<td>
		<b>NB:</b><br>
		Deliveries Accepted Subject to COUNT,Weight and QUALITY.	
		<br>
		Delivery of Goods is between 8AM to 4PM. Any Delivery after 4PM will not be received.


		</td>

		<td style="text-align: right">
			<span >

				Net Amount &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($sub_total))}}<br>
				Vat 16% &nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(array_sum($tax_amount))}}<br>
				<?php
					$totalamnt = array_sum($sub_total)+array_sum($tax_amount);
					$roundOff = fmod($totalamnt, 1); //0.25
					if($roundOff!=0){
						if($roundOff > '0.50'){
							$roundOff = '+'.round((1-$roundOff),2);
						}else{
							$roundOff = '-'.round($roundOff,2);
						}
						?>
				Round Off &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$roundOff}}<br>
						<?php
					}
				?>
				<b>Total Amount </b>&nbsp; &nbsp; &nbsp; &nbsp; {{ manageAmountFormat(round(array_sum($sub_total)+array_sum($tax_amount)))}}<br>
			</span>
		</td>
	</tr>

	<tr>
		
		

		<td colspan="2">
			<b>APPROVALS:</b>
		</td>
	</tr>
	
		
		<tr>

		<td colspan="2">
			Prepared By<br>

			 {{ $row->getrelatedEmployee->userRole->title }} &nbsp; &nbsp; &nbsp; &nbsp; {{ $row->getrelatedEmployee->name }}
		</td>
	</tr>

	<tr>

		<td colspan="2">
		<table width="100%" style="font-weight: bold;">
			
			<?php 
                                  
                                    $rendered = [];
                                      ?>
			  @foreach($row->getRelatedAuthorizationPermissions as $permissionResponse)
			   @if(!in_array($permissionResponse->getExternalAuthorizerProfile->id,$rendered))
			   <?php 
                                    $rendered[] = $permissionResponse->getExternalAuthorizerProfile->id;

                                    ?>
				<tr>
			<td width = "25%"> Level {{ $permissionResponse->approve_level}}</td>
			<td width = "25%"> Ok</td>
			<td width = "25%"> {{ ucfirst($permissionResponse->getExternalAuthorizerProfile->name)}}</td>
			<td width = "25%"> {{ date('m/d/Y H:i A',strtotime($permissionResponse->updated_at))}}</td>

			</tr>
			@endif

			@endforeach

		</table>
			
		</td>
	</tr>

	<tr>

		<td colspan="2">
			Printed By: &nbsp; &nbsp; &nbsp; &nbsp; {{ getLoggeduserProfile()->name }}
		</td>
	</tr>


</table>

</body>
</html>
