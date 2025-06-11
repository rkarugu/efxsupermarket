<!DOCTYPE html>
<html>
<head>


<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  font-size: 11px;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>

<div style="width: 100%; margin:auto; ">
<table width="104%">
	<tr><td colspan="9"><div align="center"><b>Customer Sales Summary Report</b></div><div style="text-align: right;"> {{date('d F,Y')}}</div></td></tr>

</table>

<table width="104%" style="border-bottom: 2px solid black;">
	<tr>
		<td colspan="9" style="text-align: left;"><b>Salesman Name :@if(isset($salesmanname)) {{ $salesmanname}} @endif </b>
			<br />
			<br />
			<b>From Date : {{$date1}} | To Date : {{$date2}}</b>
	</tr>
	
</table>

 
	<table width="100%" style="border:1px solid;">
	    <thead>
	    <tr>
			<th width="5%">S.No.</th>
			<th width="5%">Sales No</th>
			<th width="10%">Sales date</th>
			<th width="10%">Business Name</th>
			<th width="10%">Phone No.</th>
			<th width="15%">Contact Name </th>
			<th width="10%">Route</th>
			<th width="10%">Area Name</th>
			<th width="10%" style="text-align: right;">Amount</th>
	    </tr>
	    </thead>
	    <tbody>
        @if(isset($lists) && !empty($lists))
            <?php $b = 1;
                //echo "<pre>"; print_r($lists); die;
                $totalamount = 0;
            ?>
            @foreach($lists as $list)
            <?php
                //echo "<pre>"; print_r($list->getRelatedCustomer); die;
                  $amnt = 0;
				 foreach($list->getRelatedItem as $key=> $val){
					 $amnt += ($val->unit_price*$val->quantity);
				 }


            ?>
             
                <tr>
					<td>{!! $b !!}</td>
						<td>{!! $list->cash_sales_number !!}</td>
					<td>{!! $list->order_date !!}</td>
					<td>{!! ucfirst($list->getRelatedCustomer->customer_name) !!}</td>
					<td>{!! ucfirst($list->getRelatedCustomer->telephone) !!}</td>
					<td>{!! $list->getRelatedCustomer->contact_person !!}</td>
					<td>{!! $list->route !!}</td>
					<td>{!! ucfirst($list->getRelatedCustomer->street) !!}</td>
					<td style="text-align: right;">{!! manageAmountFormat($amnt) !!}</td>                                                  
                 </tr>
               <?php
	               $totalamount += $amnt;
	                $b++; ?>
            @endforeach
        @endif
	    </tbody>
		 <tfoot style="font-weight: bold;">
		  <td></td>
		  <td> </td>
		  <td> </td>
		  <td> </td>
		  <td> </td>
		  <td> </td>
		  <td> </td>
		  <td>Total Amount </td>
		  <td style="text-align: right;">{{ manageAmountFormat($totalamount) }}</td>
		</tfoot>

 		
	</table>
	
 
</div>




</body>
</html>
