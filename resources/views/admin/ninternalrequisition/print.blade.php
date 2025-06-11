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
  font-size: 12px;
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


<h1 style="text-align: center;">Internal Requisition - {!! $row->requisition_no!!}</h1>
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
			<td width="50%" style="text-align: right;">{!! date('Y-m-d',strtotime($row->requisition_date))!!}</td>
		</tr>
	 	<tr>
			<td width="50%">Route :</td>
			<td width="50%" style="text-align: right;">{!!$row->route !!}</td>
		</tr>
	 	<tr>
			<td width="50%">Veh. Reg. No. :</td>
			<td width="50%" style="text-align: right;">{!!$row->vehicle_register_no !!}</td>
		</tr>
	 	<tr>
			<td width="50%">Customer :</td>
			<td width="50%" style="text-align: right;">{!!$row->customer !!}</td>
		</tr>
	</table>
</div>
	<div style="clear: both;">
	</div>

 <table width="100%">
<tr>

	<td width="45%" style="border: 1px solid;font-weight: bold;">{!! $row->getRelatedToLocationAndStore->location_name!!}</td>
</tr>

</table>
<table border="1" width="100%" cellspacing="0" class="table">
	<tr>
	
<th width="17%">Item No.</th>
<th width="17%">Description</th>
<th width="15%">QTY</th>
<th width="17%">Unit</th>
<th width="17%">Selling Price</th>
<th width="17%">Total</th>
	</tr>
 <?php  $total_amount = [];
		$qty = [];
?>
@foreach($row->getRelatedItem as $item)
<?php
//	echo "<pre>"; print_r($item->getInventoryItemDetail->selling_price); die;
?>
	<tr>
	
<td width="17%">{!! $item->getInventoryItemDetail->stock_id_code!!}</td>
<td width="17%">{!! ucfirst($item->getInventoryItemDetail->title)!!}</td>
<td width="15%">{!! floor($item->quantity) !!}</td>
<td width="17%">{!! $item->getInventoryItemDetail->getUnitOfMeausureDetail->title!!}</td>
<td width="17%">{!! manageAmountFormat($item->getInventoryItemDetail->selling_price) !!}</td>
<td width="17%">{!! manageAmountFormat($item->getInventoryItemDetail->selling_price*$item->quantity) !!}</td>
	</tr>
	<?php $total_amount[] = $item->quantity*$item->getInventoryItemDetail->selling_price; ?>
	<?php $qty[] = $item->quantity; ?>
	@endforeach
</table>

<hr>

<table  width="100%" class="makebold" >

	<tr>
	
<td width="17%">Grand Total.</td>
<td width="17%"></td>
<td width="15%">{{floor(array_sum($qty))}}</td>
<td width="17%"></td>

<td width="17%"></td>
<td width="17%" style="border-bottom: 1px solid;">{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
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