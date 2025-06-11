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

{{-- 

<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>Account Code</th>
		<th width="50%">Account Name</th>
		<th style="text-align:right;"></th>
		<th style="text-align:right;"></th>
	</tr>
		<?php  $counter = 1;
		  $openingBalanceAmount = [];
      $periodDebit = [];
      $periodCredit = [];
      $periodBalance = [];
      $closingBalance = [];

		?>
		@foreach($data as $account_name=>$itemArray)
				<?php 

						$subopeningBalanceAmount = [];
						$subperiodDebit = [];
						$subperiodCredit = [];
						$subperiodBalance = [];
						$subclosingBalance = [];

				?>

        <tr>
          <td>{{ $itemArray['account'] }}</td>
          <td>{{ $itemArray['name'] }}</td>
          <td style="text-align:right;">{{ $itemArray['debit'] }}</td>
          <td style="text-align:right;">{{ $itemArray['credit'] }}</td>
        </tr>
		<?php 
      // $openingBalanceAmount[]= $itemArray['openingBalanceAmount'];
      // $periodDebit[] = $itemArray['periodDebit'];
      // $periodCredit[] =$itemArray['periodCredit'];
      // $periodBalance[] = $itemArray['periodBalance'];
      // $closingBalance[]= $itemArray['closingBalance'];
      // $subopeningBalanceAmount[]= $itemArray['openingBalanceAmount'];
      // $subperiodDebit[] = $itemArray['periodDebit'];
      // $subperiodCredit[] =$itemArray['periodCredit'];
      // $subperiodBalance[] = $itemArray['periodBalance'];
      // $subclosingBalance[]= $itemArray['closingBalance'];
		$counter++; ?>

		@endforeach

<tr style ="font-weight: bold;">
    <td></td>
    <td>Total</td>
    <td style="text-align:right;">{{ $totalDebit }}</td>
    <td style="text-align:right;">{{ $totalCredit }}</td>
</tr>

</table>
 --}}
