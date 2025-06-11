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
	<tr><td colspan="9"><div align="center"><b>Sales Commission Report</b></div><div style="text-align: right;"> {{date('d F,Y')}}</div></td></tr>

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
             <th width="20%"  >Salesman</th>
             <th width="10%"  >Sales Amount</th>
             <th width="15%"  >Commission Amount</th>
	    </tr>
	    </thead>
	    <tbody>
                                   <?php $sales_amount = []; ?>
                                   <?php $cost_amount = []; ?>
                                   <?php $total_amount = []; ?>
	                                   @foreach($detail as $datas)
	                                    <tr>     
	                                      <td>{{ ucfirst($datas['salesman_name'])}}</td>                                      
	                                      <td>{{ manageAmountFormat(array_sum($datas['sales_amount'])) }}</td>                                      	                                      
	                                      <td>{{ manageAmountFormat($datas['commission_amount']) }}</td>                                      	                                      
	                                    </tr>
	                                    <?php $sales_amount[] = array_sum($datas['sales_amount']); ?>
	                                    <?php $cost_amount[] = $datas['commission_amount']; ?>
	                                    @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td>{{ manageAmountFormat(array_sum($sales_amount)) }}</td>
                                      <td>{{ manageAmountFormat(array_sum($cost_amount)) }}</td>

                                    </tfoot>

 		
	</table>
	
 
</div>




</body>
</html>
