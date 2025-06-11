<!DOCTYPE html>
<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    font-size: 12px;
}

td, th {
    padding: 8px;
}
.data_detail td, .data_detail th {
    border: 1px solid #dddddd;
}


</style>
</head>
<body>

<table >
  <tr>
    <th colspan="4" style="text-align: center;">
      <h3>{!! $COMPANY_NAME !!}</h3>
      <h3>{!! $heading !!}</h3>
      <h3>{!! $period_from!=''?$period_from:'' !!} {!! $period_to!=''?$period_to:'' !!}</h3>
    </th>
  </tr>
 
  
  <tr>
   <td colspan="3">
   </td>
     <td style="text-align: right;">{!! $printed_time !!}</td>
  </tr>  
</table>



<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>Account Code</th>
		<th width="70%">Account Name</th>
		<th style="text-align:right;">Period Debits</th>
		<th style="text-align:right;">Period Credits</th>
	</tr>
		<?php  $counter = 1;
		  $openingBalanceAmount = [];
                                      $periodDebit = [];
                                        $periodCredit = [];
                                          $periodBalance = [];
                                            $closingBalance = [];


		?>
		@foreach($mixed_array as $account_name=>$itemArray)
		<tr><td colspan="7" style="font-weight: bold;">{{ $account_name }}</td></tr>

				<?php 

						$subopeningBalanceAmount = [];
						$subperiodDebit = [];
						$subperiodCredit = [];
						$subperiodBalance = [];
						$subclosingBalance = [];

				?>
				@foreach($itemArray as $itemData)

		<tr>
		 <td>{{ $itemData['gl_account'] }}</td>
                                       <td>{{ $itemData['gl_account_name'] }}</td>
                                      
                                       <td style="text-align:right;">{{ manageAmountFormat($itemData['periodDebit'])}}</td>
                                       <td style="text-align:right;">{{ manageAmountFormat($itemData['periodCredit'])}}</td>
		</tr>
		<?php 
		  $openingBalanceAmount[]= $itemData['openingBalanceAmount'];
                                      $periodDebit[] = $itemData['periodDebit'];
                                        $periodCredit[] =$itemData['periodCredit'];
                                          $periodBalance[] = $itemData['periodBalance'];
                                            $closingBalance[]= $itemData['closingBalance'];


             $subopeningBalanceAmount[]= $itemData['openingBalanceAmount'];
                                      $subperiodDebit[] = $itemData['periodDebit'];
                                        $subperiodCredit[] =$itemData['periodCredit'];
                                          $subperiodBalance[] = $itemData['periodBalance'];
                                            $subclosingBalance[]= $itemData['closingBalance'];
		$counter++; ?>
		@endforeach
		 <tr style ="font-weight: bold;">
                                       <td></td>
                                     
                                       <td>Sub Total</td>
                                        <td style="text-align:right;">{{ manageAmountFormat(array_sum($subperiodDebit))}}</td>
                                       <td style="text-align:right;">{{ manageAmountFormat(array_sum($subperiodCredit))}}</td>
                                    </tr>
		@endforeach

		  <tr style ="font-weight: bold;">
                                       <td></td>
                                       
                                       <td>Total</td>
                                        <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodDebit))}}</td>
                                       <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodCredit))}}</td>
                                    </tr>

</table>






</body>
</html>
