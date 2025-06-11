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
		.align_float_center
{
  text-align:  center;
}

	</style>
	
</head>
<body>

<?php $all_settings = getAllSettings();?>


<br>
<div style="text-align: center;" class="underline"><b>INTERNAL REQUISITION</b></div>
<div>Delivery Loc: {{ $row->getBranch->name}} - {{ $row->getDepartment->department_name }}</div>
<div>Requisition No: {{ $row->requisition_no}} <div style="text-align: right;">Order Date: {{ $row->requisition_date}}</div></div><br>

<table width="100%" style="border: 1px solid">
<tr>
	<td width = "40%">Article</td>
	<td width = "30%">Unit</td>
	<td width = "30%">Qty</td>
</tr>
</table>

<table width="100%" style="border-bottom: 1px solid;" >
 @if($row->getRelatedItem && count($row->getRelatedItem)>0)
   @foreach($row->getRelatedItem as $getRelatedItem)
<tr>
	<td width = "40%">{{ $getRelatedItem->getInventoryItemDetail->title }} @if($getRelatedItem->note) <br> <span style="padding-left: 30px;font-size: 12px;">({{ $getRelatedItem->note }}) </span>  @endif</td>
	<td width = "30%">{{ $getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
	<td width = "30%">{{ $getRelatedItem->quantity }}</td>
</tr>
@endforeach

@endif
<table style="border: 1px solid;" width="100%">
	

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
			

			  @foreach($row->getRelatedAuthorizationPermissions as $permissionResponse)
				<tr>
			<td width = "25%"> Level {{ $permissionResponse->approve_level}}</td>
			<td width = "25%"> Ok</td>
			<td width = "25%"> {{ ucfirst($permissionResponse->getInternalAuthorizerProfile->name)}}</td>
			<td width = "25%"> {{ date('m/d/Y H:i A',strtotime($permissionResponse->updated_at))}}</td>

			</tr>

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