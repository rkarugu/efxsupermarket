<!DOCTYPE html>
<html>
<head>


<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
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
<?php
$totalexp = 0;
?>

@foreach($expenseList as $val)
<?php 
$totalexp += $val->total_exp;
?>
@endforeach

<div style="width: 90%;margin:auto;font-size: 12px; ">
<table width="100%">
	<tr><td><div align="center"><b>Showroom Sales Summary Report</b></div><div style="text-align: right;"> {{date('d F,Y')}}</div></td></tr>

</table>

<table width="100%" style="border-bottom: 2px solid black;">
	<tr>
		<td style="text-align: left;">Date : {{$startdate}} to {{$enddate}}</td>
	</tr>
	
</table>

 
	<table width="100%" style="border:1px solid;">
	    <thead>
	    <tr>
	      
	         <th>Item Code</th>
	         <th style="text-align: center;">Item Sold</th>
	         <th style="text-align: right;">Average Price</th>
             <th style="text-align: right;">Standard Cost</th>                                        
	         <th style="text-align: right;">Total Sale</th>                                        
             <th style="text-align: right;">Total Cost</th>                                        
             <th style="text-align: right;">Margin</th>                                        
	    </tr>
	    </thead>
	    <tbody>
				<?php $total_amount = []; 
				$total_cost = []; 

	           $total_margin = []; 
	
	           $itemtaken = [];
	           $itemreturn = [];
	           $itemsold = [];		   
	   ?>
	           @if($all_item)
	           @foreach($all_item as $data)
                <tr>     
                  <td>{{ $data->item_no}}</td>                                      
                  <td style="text-align: center;">{{ abs($data->item_sold) }}</td>                                      
                  <td style="text-align: right;">{{ manageAmountFormat($data->avg_price)}}</td>                                      
				<td style="text-align: right;">{{ manageAmountFormat($data->standard_cost)}}</td>                                      
                   <td style="text-align: right;">{{ manageAmountFormat(abs(($data->item_sold * $data->avg_price))) }}</td>  
				<td style="text-align: right;">{{ manageAmountFormat(abs(($data->item_sold * $data->standard_cost))) }}</td>  
				<td style="text-align: right;">{{ manageAmountFormat(abs($data->item_sold * $data->avg_price) - abs($data->item_sold * $data->standard_cost)) }}</td>  

                </tr>
	        <?php $total_amount[] = ($data->item_sold * $data->avg_price); 
                    $total_cost[] = ($data->item_sold * $data->standard_cost); 

                    $total_margin[] = abs($data->item_sold * $data->avg_price) - abs($data->item_sold * $data->standard_cost); 

 	                $itemsold[] = $data->item_sold; 
		        
	        ?>
	        @endforeach
	    </tbody>
	        <tfoot style="font-weight: bold;">
	          <td>Grand Total</td>
	          <td style="text-align: center;">{{  (abs(array_sum($itemsold))) }}</td>
	          <td></td>
	          <td></td>
	          <td style="text-align: right;">{{ manageAmountFormat(abs(array_sum($total_amount))) }}</td>
	          <td style="text-align: right;">{{ manageAmountFormat(abs(array_sum($total_cost))) }}</td>
	          <td style="text-align: right;">{{ manageAmountFormat(abs(array_sum($total_margin))) }}</td>
	
	        </tfoot>
	
	        @endif
		
	</table>
	<div style="width: 100%; text-align: right; padding-top: 30px; font-size: 14px;">
		<b>Gross Profit : {{ manageAmountFormat(abs(array_sum($total_margin))) }}</b><br>
	</div>
 
</div>




</body>
</html>
