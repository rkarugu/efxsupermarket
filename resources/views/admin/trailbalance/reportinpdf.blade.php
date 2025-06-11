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
    <th colspan="4" style="text-align: left;">
      <h3 style="margin-bottom: 0px;padding-bottom:0px;">{!! $COMPANY_NAME !!}</h3>
      <h3 style="margin-bottom: 0px;padding-bottom:0px;">{!! $heading !!}</h3>
      <h3 style="margin-bottom: 0px;padding-bottom:0px;">{!! $period_from!=''?$period_from:'' !!} {!! $period_to!=''?$period_to:'' !!}</h3>
      <h3 style="margin-bottom: 0px;padding-bottom:0px;">Branch : {{ $branch }}</h3>
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
		<th width="50%">Account Name</th>
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
		@foreach($data as $itemArray)
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






</body>
</html>
