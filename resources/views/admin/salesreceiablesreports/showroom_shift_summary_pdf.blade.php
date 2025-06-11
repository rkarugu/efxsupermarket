<!DOCTYPE html>
<html>
<head>


<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  font-size: 10px !important;
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
       $account_codes =  getChartOfAccountsList();
	   $totalexp = 0;
	   $totalamount = []; //echo "<pre>"; print_r($depoitelist); die; 	                                   
	   $paymentmethodtotal = []; //echo "<pre>"; print_r($depoitelist); die; 	                                   
   ?>
     @foreach($mydebtorlist as $key=> $row)
    <?php
   		$totalamount = $row->getRelatedItem->sum('total_cost_with_vat');	                                   
   		$totalpaid   = $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount');	                                   
	    $totalamounts[] = ($totalamount-$totalpaid); 
	?>
     @endforeach

     @foreach($depoitelist as $key=> $val)
    <?php $paymentmethodtotal[] = abs($val->amount); ?>
     @endforeach
        <div class="col-md-12 no-padding-h">
						<center><h2>Showroom Shift Summary Report</h2></center>
						<br />
                           <div class="col-sm-12" style="font-size: 12px;">
							   		<b>From Date : {{ $date1 }} - To Date : {{ $date2 }}</b>
					   			<br />
					   			<br />
                           </div> 
 							   @foreach($expenseList as $val)
							   	<?php 
								   	$totalexp += abs($val->amount);
							   	?>
							   	@endforeach

                           <div style="width: 100%; height: 150px;font-size: 12px;">
	                           <div style="width: 60%; float: left; font-size: 12px;">
	                            <h4>EXPENSES</h4>
                                <table class="table table-bordered" style="width: 80%;">
                                    <thead>
                                    <tr>
                                        <th width="25%">Date</th>
                                        <th width="20%">Refrence</th>
                                         <th width="20%">Payment Method</th>
                                        <th width="20%">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $b = 1;

                                          $total_amount = [];
                                         //echo "<pre>"; print_r($row); die;
                                        ?>
                                        @foreach($expenseList as $list)
                                            <tr>
                                                <td>{!! date('Y-m-d',strtotime($list->trans_date)) !!}</td>
                                                <td>{!! $list->reference !!}</td>
                                                <td>{!! @$list->getPaymentMethod->title !!}</td>
	                                            <td>{!! @manageAmountFormat($list->amount) !!}</td>
                                            </tr>
                                            <?php 
	                                            $b++;
                                                $total_amount[] = $list->amount;
                                            ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td style="font-weight: bold;">Total</td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_amount))}}</td>
                                    </tfoot>

                                </table>
	                           </div> 
	                           <div class="col-sm-4" style="width: 40%;font-size: 12px; float: left;">
	                            <h4>Amount</h4>
							   	<div style="border-top:1px solid; border-bottom:1px solid;">
								   	<b>{{manageAmountFormat(array_sum($totalamounts)+array_sum($paymentmethodtotal))}}</b>
 							   	</div>
 							   	
	                           </div> 
                           </div> 
                           
                           <div class="col-sm-12" style="width: 100%; font-size: 12px; height: 70px;">
	                           <div class="col-sm-8" style="width: 60%; float: left;">
							   &nbsp;
	                           </div> 
	                           <div class="col-sm-4" style="width: 40%; float: left;">
	                            <h4>Net Amount</h4>
							   	<div style="border-top:1px solid; border-bottom:1px solid;">
								   	<b>{{manageAmountFormat((array_sum($totalamounts)+array_sum($paymentmethodtotal))-$totalexp)}}</b>
 							   	</div>
 							   	
	                           </div> 
                           </div> 

                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="10%"  >Payment Method</th>
                                        <th width="15%"  >Date</th>
                                        <th width="10%"  >Receipt No</th>
                                        <th width="20%"  >Customer Name</th>
                                        <th width="15%"  >Cashier Name</th>
                                        <th width="10%"  >Reference</th>                                       
                                        <th width="10%"  >Amount</th>
                                        
                                    </tr>
                                     </thead>
                                    <tbody>
                                   <?php $i = 1;
                                   $final_amount = [];

                                   ?>
                                   @foreach($depoitelist as $item)
                                   <tr>
                                   <td>{{ $item->getPaymentMethod->title }}</td>
                                   <td>{{ date('Y-m-d',strtotime($item->trans_date)) }}</td>
                                   <td>{{ $item->document_no }}</td>
                                    <td>{{ getCustomerNameByDocumentNumber($item->document_no) }}</td>
                                   <td>{{ $item->getCashierDetail?$item->getCashierDetail->name:'' }}</td>
                                   <td>{{ $item->reference }}</td>
                                   <td>{{ manageAmountFormat(abs($item->amount)) }}</td>
                                   </tr>
                                   <?php 

                                   $final_amount[] = abs($item->amount);
                                   $i++; ?>
                                    
                                    @endforeach                                   
                                    </tbody>
 
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td> </td>
                                      <td> </td>
                                      <td> </td>
                                      <td> </td>
                                      <td> </td>
                                      <td>{{ manageAmountFormat(array_sum($final_amount)) }}</td>

                                    </tfoot>

                                </table>

                            </div>

                                    </div>


                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
	                        <div  style="height: 150px ! important;"> 
	                         <div>
 
                           <div class="col-sm-12">
	                            <h4>Debtors</h4>

                            </div> 
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="10%">Invoice Number</th>
                                        <th width="20%">Customer Name</th>
                                        <th width="15%">Date</th>
                                        <th width="15%">Due Date</th>
                                        <th width="10%">Due</th>
                                    </tr>
                                     </thead>
                                    <tbody>
                                   <?php 
									$i = 1;
                                   $total_amount = [];
                                   $final_amount = [];
                                   $paidtotal = 0;
                                   $deutotal = 0;
	                                   
	                                ?>
 
                                   @foreach($mydebtorlist as $row)
                                   <?php
								   		$totalamount = $row->getRelatedItem->sum('total_cost_with_vat');	                                   
								   		$totalpaid   = $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount');	                                   
								   		if($totalamount > $totalpaid){
                                   ?>
                                    <tr>
                                       <td>{{ $i }}</td>
                                       <td>{{ $row->sales_invoice_number}}</td>
                                       <td>{{ ucfirst(@$row->getRelatedCustomer->customer_name)}}</td>
                                       <td>{{ $row->order_date}}</td>
                                       <td>{{ $row->order_date}}</td>


                                       <?php 
                                       //echo "<pre>"; print_r($row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount')); die;
                                       $total_amount = $row->getRelatedItem->sum('total_cost_with_vat');
                                       $final_amount[] = $total_amount;
                                       ?>

                                       <td>{{number_format(($total_amount - $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount')),2)}}</td>

                                        @php
                                        $paidtotal += ($row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount')); 
                                        $deutotal += ($total_amount - $row->getRelatedCustomerAllocatedAmnt->sum('allocated_amount')); 
                                        @endphp

                                    </tr>
                                    <?php $i++; } ?>
                                    @endforeach
                                    </tbody>
 
                                    <tfoot style="font-weight: bold;">
                                      <td>Grand Total</td>
                                      <td> </td>
                                      <td> </td>
                                      <td> </td>
                                      <td> </td>
 	                                  <td>{{number_format($deutotal,2)}}</td>

                                    </tfoot>

                                </table>
                            </div>


                         </div>
                    </div>
             
</div>




</body>
</html>
