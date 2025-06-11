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
	<tr><td colspan="9"><div align="center"><b>Salesman Summary Report</b></div><div style="text-align: right;"> {{date('d F,Y')}}</div></td></tr>

</table>

<table width="104%" style="border-bottom: 2px solid black;">
	<tr>
		<td colspan="9" style="text-align: left;">
			<br />
			<br />
			<b>From Date : {{$date1}} | To Date : {{$date2}}</b>
		</td>
	</tr>
	
</table>

 
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      
                                        <th width="60%"  >Sales Person Name</th>
                                        <th width="40%"  >Amount</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php $total_amount = []; ?>
                                   @foreach($detail as $data)
                                    <tr>     
                                      <td>{{ ucfirst($data['salesman_name'])}}</td>
                                      <td>{{ manageAmountFormat(array_sum($data['amount'])) }}</td>  
                                    </tr>
                                    <?php $total_amount[] = array_sum($data['amount']); ?>
                                     @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td>{{ manageAmountFormat(array_sum($total_amount)) }}</td>

                                    </tfoot>

                                </table>	
 
</div>




</body>
</html>
