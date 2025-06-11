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
.align_float_center
{
  text-align:  center;
}
.makebold td{
	font-weight: bold;
}
.table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  font-size: 10px;
}

.table td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 2px;
}

/*
.table tr:nth-child(even) {
  background-color: #dddddd;
}
*/

	</style>
	
</head>
<body>

<?php $all_settings = getAllSettings();?>


<h1 style="text-align: center;">Transfer - {!! $list->transfer_no!!}</h1>
<div style="width: 50%; float: left; height: auto;" >
<span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
{{ $all_settings['ADDRESS_1']}}<br>
{{ $all_settings['ADDRESS_2']}}<br>
{{ $all_settings['ADDRESS_3']}}<br>
Tel: {{ $all_settings['PHONE_NUMBER']}}<br>

</div>
<div style="width: 50%; float: left;  height: auto;" >
	<table  width="100%" style="float: right;" class="makebold">
 	 	<tr>
			<td width="50%">Date :</td>
			{{-- <td width="50%" style="text-align: right;">{!! date('Y-m-d',strtotime($list->transfer_date))!!}</td> --}}
			<td width="50%" style="text-align: right;">{!! $list->created_at!!}</td>

		</tr>
		<tr>
			<td width="50%">Manual Doc. No. :</td>
			<td width="50%" style="text-align: right;">{!!$list->manual_doc_number ?? '-' !!}</td>
		</tr>
	 	{{-- <tr>
			<td width="50%">Route :</td>
			<td width="50%" style="text-align: right;">{!!$list->route !!}</td>
		</tr> --}}
	 	{{-- <tr>
			<td width="50%">Veh. Reg. No. :</td>
			<td width="50%" style="text-align: right;">{!!$list->vehicle_register_no !!}</td>
		</tr> --}}
	 	
	</table>
</div>
	<div style="clear: both;">
	</div>

 <table width="100%">
<tr>
	<td width="45%" style="border: 1px solid;font-weight: bold;">{!! @$list->fromStoreDetail->location_name!!}</td>
	<td width="10%" style="font-weight: bold; text-align: center;">To</td>
	<td width="45%" style="border: 1px solid;font-weight: bold;">{!! @$list->toStoreDetail->location_name!!}</td>
</tr>

</table>
<table border="1" width="100%" cellspacing="0" class="table">
	<tr>
<th>#</th>	
<th >Item No.</th>
<th >Description</th>
{{-- <th width="15%">QTY</th> --}}
{{-- <th width="17%">Unit</th> --}}
<th >QTY</th>

<th style="text-align:right !important;">Cost</th>
<th style="text-align:right !important;">Total</th>
	</tr>
 <?php  $total_amount = [];
		$qty = [];
		$weight = [];
		$totalItems = $itemsdata->count() ?? 0;

?>
@foreach($itemsdata as $item)
<?php
//	echo "<pre>"; print_r($item->getInventoryItemDetail->selling_price); die;
?>
	<tr>
<td>{{ $loop->iteration }}</td>	
<td >{!! @$item->getInventoryItemDetail->stock_id_code!!}</td>
<td >{!! ucfirst(@$item->getInventoryItemDetail->title)!!}</td>
{{-- <td width="15%">{!! floor($item->quantity) !!}</td> --}}
{{-- <td width="17%">{!! @$item->getInventoryItemDetail->getUnitOfMeausureDetail->title!!}</td> --}}
{{-- <td width="17%">{!! @$item->getInventoryItemDetail->pack_size->title!!}</td> --}}
<td >{!! floor($item->quantity) !!}</td>


<td style="text-align:right !important;">{!! manageAmountFormat($item->getInventoryItemDetail->standard_cost ?? 0.00) !!}</td>
<td style="text-align:right !important;">{!! manageAmountFormat(($item->getInventoryItemDetail->standard_cost  ?? 0.00)*$item->quantity) !!}</td>
	</tr>
	<?php $total_amount[] = $item->quantity*($item->getInventoryItemDetail->standard_cost  ?? 0.00); ?>
	<?php 
	$qty[] = $item->quantity;
	$weight[] = ( $item->getInventoryItemDetail->gross_weight ?? 0 )* $item->quantity ;
	?>

	@endforeach
</table>

<hr>

<table  width="100%" class="makebold" >

	<tr>
		<td width="39%" style="font-size: 12px;">
			Total Line Items: {{ $totalItems }} 	
		</td>
		<td width="39%" style="font-size: 12px;">
			Total Weight: {{floor(array_sum($weight))}}
		</td>

		<td width="39%" style="font-size: 12px;">
			Total Qty: {{floor(array_sum($qty))}}
		</td>
	</tr>
	
	<tr>

	
<td width="17%">Grand Total.</td>
<td width="17%"></td>
{{-- <td width="15%">{{floor(array_sum($qty))}}</td> --}}
<td width="17%"></td>
{{-- <td width="15%">{{floor(array_sum($qty))}}</td> --}}
<td width="15%"></td>



<td width="17%"></td>
<td width="17%" style="border-bottom: 1px solid; text-align:right !important;">{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
	</tr>

 	
</table>
<br>

<table  width="100%" class="makebold" >

	
	<tr>
	
<td width="33%">--------------------------</td>

<td width="33%">--------------------------</td>
<td width="34%" style="text-align: right;" >------------------------------------------</td>
	</tr>
	
		<tr>
	
<td width="33%">Issued By</td>

<td width="33%" >Checked By</td>
<td width="34%" style="text-align: center;" >Received By</td>
	</tr>

	

	
</table>

<br>
<table  width="100%" class="makebold" >

	
	

	<tr>
	
<td width="33%" style="font-size: 12px;">Report Printed By: {!! getLoggeduserProfile()->name!!}</td>

 <td width="34%" style="text-align: center;" ></td>
	</tr>


	
</table>
<hr>

</body>
</html>