<!DOCTYPE html>
<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
     font-size: 10px;
}

td, th {
    padding: 6px;
}
.data_detail td, .data_detail th {
    border: 1px solid #dddddd;
}


</style>
</head>
<body>

<table >
  <tr>
    <th colspan="4" style="text-align: center; padding: 2px;">{!! $heading !!}</th>
  </tr>
  @if($restro_name != '')
  <tr>
   <td colspan="4" style="text-align: center;" >{!! $restro_name !!}</td>
   </tr>
  @endif

   @if($waiter_name != '')
  <tr>
   <td >{!! $waiter_name !!}</td>
    <td colspan="3" ></td>
  </tr>
  @endif
 <?php 
$logged_user_info = getLoggeduserProfile();
 ?>

  <tr>
   <td colspan="3">
    </td>
     <td style="text-align: right;">{!! ucfirst($logged_user_info->name) !!}</td>
  </tr>  
  
  <tr>
   <td colspan="3">
   {!! $period_from!=''?$period_from:'' !!} {!! $period_to!=''?$period_to:'' !!}
   </td>
     <td style="text-align: right;">{!! $printed_time !!}</td>
  </tr>  
</table>





@if($case == 'PERCENTAGEPROFITREPORT')
<table style = "border:1px solid gray" class="data_detail">
	<tr>
			
			<th   >Item</th>
			<th  >Family Group</th>
			<th  >Sales Price</th>
			<th  >Cost</th>
			<th   >Sales QTY</th>
			<th   >Total Sales</th>
			<th   >Total Cost</th>
			<th   >Profit</th>
			<th   >Margin(%) </th>
	</tr>
	<?php 
	$total_qty = [];
    $total_amount = [];
     $counter = 1;
     foreach($mixed_array as $row){ ?>

      <tr>

                                    <?php 
                                    $total_sale = $row['item_total_quantity']*$row['item_price'];
                                    $total_cost = $row['item_total_quantity']*$row['cost'];
                                    $profit = $total_sale-$total_cost;
                                    $profit_percentage = 0;
                                    if($total_cost>0)
                                    {
                                         $profit_percentage = ($profit/$total_cost)*100;
                                    }






                                    $final_sale[] = $total_sale;
                                      $final_cost[] = $total_cost;
                                        $final_profit[] = $profit;


                                   


                                    ?>

                                       
                                        <td>{!! $row['title'] !!}</td>
                                         <td>{!! $row['family_group_name'] !!}</td>


                                          <td>{!! manageAmountFormat($row['item_price']) !!}</td>
                                          <td>{!! manageAmountFormat($row['cost']) !!}</td>
                                           <td>{!! manageAmountFormat($row['item_total_quantity']) !!}</td>
                                           <td>{!! manageAmountFormat($total_sale) !!}</td>
                                          <td>{!! manageAmountFormat($total_cost) !!}</td>
                                          <td>{!! manageAmountFormat($profit) !!}</td>
                                          <td>{!! manageAmountFormat($profit_percentage) !!}</td>






                                    </tr>
                                    <?php 
                                   
                                    $counter++; ?>

     <?php } ?>

      <?php 
                                    $fianl_margin = 0;
                                     if(array_sum($final_cost)>0)
                                    {
                                         $fianl_margin = (array_sum($final_profit)/array_sum($final_cost))*100;
                                    }
                                    ?>
                                    <tr style=" font-weight: bold;">
                                        
                                       
                                        <td></td>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ manageAmountFormat(array_sum($final_sale))}}</td>
                                        <td>{{ manageAmountFormat(array_sum($final_cost))}}</td>
                                        <td>{{ manageAmountFormat(array_sum($final_profit))}}</td>
                                        <td>{{ manageAmountFormat($fianl_margin)}}</td>
                                        </tr>
	




</table>
@endif


@if($case == 'DISCOUNTREPORTS')
<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>S.No.</th>
		<th  >Discount Name</th>
		<th  >No Of Transactions</th>
		<th   >Discount Amount</th>
	</tr>
		<?php  $counter = 1;


		?>
		@foreach($mixed_array as $discount_name=>$row)
		<tr>
		<td>{!! $counter !!}</td>
		<td>{!! str_replace('_',' ',$discount_name) !!}</td>
		<td>{!! $row['no_of_transactions']!!}</td>
		<td>{!! manageAmountFormat($row['total_amount']) !!}
		</tr>
		<?php 
		 $total_qty[] = $row['no_of_transactions'];
                                    $total_amount[] = $row['total_amount'];
		$counter++; ?>
		@endforeach

		 <tr>
		<td></td>
		<td>Grand Total</td>
		<td>{!! array_sum($total_qty) !!}</td>
		<td>{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
	</tr>

</table>
@endif

@if($case == 'CASHIERREPORT')
<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th >S.No.</th>
		<th >Payment Name</th>
		<th >Amount</th>
	</tr>
		<?php  $counter = 1;
		 $total_amount = [];

		?>
		@foreach($mixed_array as $data)
		<tr>
			<td>{!! $counter !!}</td>
			<td>{!! $data['payment_mode'] !!}</td>
			<td>{!! manageAmountFormat($data['amount']) !!}</td>
		</tr>
		<?php 
		 $total_amount[] = $data['amount'];
		$counter++; ?>
		@endforeach
		 <tr>
		<td></td>
		<td>Grand Total</td>
		<td>{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
	</tr>

</table>
@endif

@if($case == 'PAYMENTSALESUMMARY')
<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>SN</th>
		<th>TITLE</th>
		<th>No of Tranx</th>
		<th>Total Payments</th>
	</tr>
	<?php 
	$total_qty = [];
    $total_amount = [];
     $counter = 1;
     foreach($mixed_array as $array){
	?>
	<tr>
		<td>{!! $counter !!}</td>
		<td>{!! $array->payment_mode !!}</td>
		<td>{!! $array->number_of_transaction !!}</td>
		<td>{!! manageAmountFormat($array->amount) !!}</td>
	</tr>
	<?php  
		$total_qty[] = $array->number_of_transaction;
        $total_amount[] = $array->amount;
        $counter++; 
    } ?>

   <tr>
		<td></td>
		<td>Grand Total</td>
		<td>{!! array_sum($total_qty) !!}</td>
		<td>{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
	</tr>

</table>
@endif

@if($case == 'CONDIMENTSALESREPORTWITHPLU')
<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>SN</th>
		<th>Item</th>
		<th>Sales QTY</th>
		<th> Plu no</th>
		<th> Plu Name</th>
	</tr>
	<?php 
	$total_qty = [];
   
     $counter = 1;
     foreach($mixed_array as $array){
	?>
	<tr>

		
		<td>{!! $counter !!}</td>
		<td>{!! $array['title']!!}</td>
		<td>{!! $array['item_total_quantity'] !!}</td>
		<td>{!! $array['plu_number'] !!}</td>
		<td>{!! $array['plu_name'] !!}</td>
	</tr>
	<?php  
		$total_qty[] = $array['item_total_quantity'];
        
        $counter++; 
    } ?>

   <tr>
		<td></td>
		<td>Grand Total</td>
		<td>{!! array_sum($total_qty) !!}</td>
		<td></td>
		<td></td>
	</tr>

</table>
@endif


@if($case == 'FAMILYGROUPMENUITEMGENERALSALES')
<table style = "border:1px solid gray;" class="data_detail">
        <thead>
        <tr>
            <th>Item</th>
            <th align="right">Price</th>
             <th align="center">Sales QTY</th>
             <th align="right">% for SalesQty</th>
            <th align="right">Gross Sales</th>
            <th align="right">% for Gross Sales</th>
           
        </tr>
        </thead>
		<?php
			$final_totla_charge= [];		
		?>

        @foreach($mixed_array as $key=> $rows)
        <?php  $counter = 1;
        $total_qty = [];
        $total_amount = [];
        $total_qty_per = [];
        $total_gross_per = [];
        $total_disc = [];
 
        ?>

        @foreach($rows as $row)
         @if($row['title']!="")
        <tr>
            <td>{!! ucfirst(strtolower($row['title'])) !!}</td>
            <td align="right">{!! $row['price'] !!}</td>
             <td align="center">{!! $row['item_total_quantity'] !!}</td>
             @if($row['item_total_quantity'] > 0)
             <td align="right">{!! round(($row['item_total_quantity']/$rows['total_qty_total'])*100,2) !!}%</td>
            <?php 
            $total_qty_per[] = (($row['item_total_quantity']/$rows['total_qty_total'])*100);
            ?>
             @else
             <td align="right">0%</td>
             @endif
            <td align="right">{!! manageAmountFormat($row['gross_sale']) !!}</td>
             @if($row['gross_sale'] > 0)
             <td align="right">{!! round(($row['gross_sale']/$rows['gross_sales_total'])*100,2) !!}%</td>
            <?php 
            $total_gross_per[] = (($row['gross_sale']/$rows['gross_sales_total'])*100);
            ?>
             @else
             <td align="right">0%</td>
             @endif
        </tr>
        <?php 
        $total_qty[] = $row['item_total_quantity'];
        $total_amount[] = $row['gross_sale'];
        $total_disc[] = $row['total_charges'];
        $final_totla_charge[] = $row['gross_sale'];
        $counter++; ?>
        @endif
        @endforeach
        <thead>
        <tr>
            <th>{!! ucfirst(strtolower($key)) !!}</th>
            <th></th>
            <th align="center">{!! array_sum($total_qty) !!}</th>
            <th align="right">{!! array_sum($total_qty_per) !!}%</th>
            <th align="right">{!! manageAmountFormat(array_sum($total_amount)) !!}</th>
            <th align="right">{!! array_sum($total_gross_per) !!}%</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th align="left">Gross Sale Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th align="right">{!! manageAmountFormat(array_sum($final_totla_charge)) !!}</th>
            <th></th>
        </tr>
        </tfoot>
        @endforeach

</table>
@endif



@if($case != 'CONDIMENTSALESREPORTWITHPLU' && $case != 'PAYMENTSALESUMMARY' && $case != 'DISCOUNTREPORTS' && $case != 'CASHIERREPORT' && $case !='FAMILYGROUPMENUITEMGENERALSALES'  && $case != 'PERCENTAGEPROFITREPORT')


	<table style = "border:1px solid gray" class="data_detail">
	<tr>
		<th>SN</th>
		<th>Item</th>
		@if($case == 'MENUITEMGENERALSALES')
		<th>Family Group</th>
		@endif
		<th>Sales QTY</th>
		<th>Gross Sales</th>

		@if($taxes_name)
		 @foreach($taxes_name as $charges_name_detail)
            <th   >{!! str_replace('_',' ',strtoupper($charges_name_detail))!!}</th>                              
          @endforeach

		@endif


		<th>Taxes</th>
		<th>Net Sales % Of Ttl</th>
		@if($case == 'MENUITEMGENERALSALESWITHPLU')
		<th>Plu No</th>
		<th>Plu Name</th>

		@endif

		@if($case == 'FAMILYGROUPSALESWITHGL')
		<th>GL Code</th>
		<th>GL Name</th>

		@endif


		
	</tr>
	<?php 
	$total_qty = [];
	$total_amount = [];
	$total_disc = [];
     $counter = 1;
     $final_totla_charge= [];
     foreach($mixed_array as $array){
	?>
	<tr>
		<td>{!! $counter !!}</td>
		<td>{!! $array['title'] !!}</td>
		@if($case == 'MENUITEMGENERALSALES')
		<td>{!! $array['family_group_name'] !!}</td>
		@endif
		<td>{!! $array['item_total_quantity'] !!}</td>
		<td>{!! manageAmountFormat($array['gross_sale']) !!}</td>
		@if($taxes_name)
	    @foreach($taxes_name as $charges_name_detail)
	     <td>
	     <?php 
	        if(isset($array[$charges_name_detail]))
	        {
	            echo  manageAmountFormat($array[$charges_name_detail]);
	            $final_totla_charge[$charges_name_detail][] = $array[$charges_name_detail]; 
	        }
	        else
	            {
	                echo '0.00';
	            }
	     ?>
	         

	     </td>
	      @endforeach
	      @endif


		<td>{!! manageAmountFormat($array['total_charges']) !!}</td>
		<td>{!! manageAmountFormat($array['gross_sale']-$array['total_charges']) !!}</td>
		@if($case == 'MENUITEMGENERALSALESWITHPLU')
		<td>{!! $array['plu_number'] !!}</td>
		<td>{!! $array['plu_name'] !!}</td>

		@endif

		@if($case == 'FAMILYGROUPSALESWITHGL')
		<td>{!! $array['gl_code'] !!}</td>
		<td>{!! $array['gl_name'] !!}</td>

		@endif


		

	</tr>
	<?php  
		$total_qty[] = $array['item_total_quantity'];
        $total_amount[] = $array['gross_sale'];
        $total_disc[] = $array['total_charges']; 

        $counter++;
        
    } ?>

   <tr>
		<td  @if($case == 'MENUITEMGENERALSALES') colspan = "2" @endif></td>
		<td>Grand Total</td>
		<td>{!! array_sum($total_qty) !!}</td>
		<td>{!! manageAmountFormat(array_sum($total_amount)) !!}</td>
		@if($taxes_name)
		@foreach($taxes_name as $charges_name_detail)
			<td>
			<?php 

			if(isset($final_totla_charge[$charges_name_detail]))
			{
			echo manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
			}
			else
			{
			echo '0.00';
			}
			?>
			</td>
		@endforeach
		@endif


		<td>{!! manageAmountFormat(array_sum($total_disc)) !!}</td>
		<td @if($case == 'MENUITEMGENERALSALESWITHPLU' || $case == 'FAMILYGROUPSALESWITHGL' ) colspan = "3" @endif>{!! manageAmountFormat( array_sum($total_amount)-array_sum($total_disc)) !!}</td>
		
		

		
	</tr>

</table>
@endif
</body>
</html>
