<html>
<title>PDF</title>

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

<div style="width: 100%; height: auto; text-align:center" >
	<span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
	{{ $all_settings['ADDRESS_1']}}<br>
	{{ $all_settings['ADDRESS_2']}}<br>
	{{ $all_settings['ADDRESS_3']}}<br>
	Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
	
	</div>
<h3 style="text-align: center;">Supplier Delta</h3>

<div style="width: 50%; float: left;  height: auto;" >
	<table  width="100%" style="float: right;" class="makebold">
 	
	 	<tr>
			<td width="20%">Supplier </td>
			<td  > {!! ':  '.$supplier->name !!}</td>
		</tr>
		<tr>
			<td width="20%">Delta No</td>
			<td > {{  ':  '.$demandCode}}</td>
		</tr>
		<tr>
			<td width="20%">Date :</td>
			<td > {!! ':  '.$delta->created_at !!}</td>
		</tr>
	</table>
</div>
	<div style="clear: both;">
	</div>

 {{-- <table width="100%">
<tr>
	<td width="45%" style="border: 1px solid;font-weight: bold;">{!! @$list->fromStoreDetail->location_name!!}</td>
	<td width="10%" style="font-weight: bold; text-align: center;">To</td>
	<td width="45%" style="border: 1px solid;font-weight: bold;">{!! @$list->toStoreDetail->location_name!!}</td>
</tr>

</table> --}}
<table border="1" width="100%" cellspacing="0" class="table">
	<tr>

<th width="5%">#</th>	
<th >Item No.</th>
<th >QoH</th>
<th >Current Cost</th>
<th >New Cost</th>
<th >Delta</th>
	</tr>
 <?php  $total_amount = [];
		$qty = [];
?>
@foreach($data as $delta)
<?php
//	echo "<pre>"; print_r($item->getInventoryItemDetail->selling_price); die;
?>
	<tr>
<th>{{$loop->index + 1 }}</th>
	
<td >{!! $delta['item'] !!}</td>
<td >{!! $delta['Qoh'] !!}</td>
<td style="text-align: right;">{!! $delta['current_cost'] !!}</td>
<td style="text-align: right;">{!! $delta['new_cost'] !!}</td>
<td style="text-align: right;">{!! manageAmountFormat($delta['demand']) !!}</td>
	</tr>
	<?php $total_amount[] = $delta['demand'] ?>
	<?php $qty[] = $delta['Qoh']; ?>
	@endforeach
</table>

<hr>

<table  width="100%" class="makebold" >

	<tr>
	
<td width="17%">Grand Total.</td>
<td width="17%"></td>
<td width="15%"></td>
<td width="17%"></td>

<td width="17%" style="border-bottom: 1px solid; text-align:right;">{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
	</tr>

 	
</table>
<br>
{{-- 
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

	

	
</table> --}}

<br>
{{-- <table  width="100%" class="makebold" >

	
	

	<tr>
	
<td width="33%" style="font-size: 12px;">Report Printed By: {!! getLoggeduserProfile()->name!!}</td>

 <td width="34%" style="text-align: center;" ></td>
	</tr>


	
</table> --}}
<hr>

</body>
</html>