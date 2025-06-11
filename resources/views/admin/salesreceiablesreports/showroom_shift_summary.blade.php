
@extends('layouts.admin.admin')

@section('content')
   <?php 
       $account_codes =  getChartOfAccountsList();
	   $totalexp = 0;
	   $totalamounts = [];
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


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'sales-and-receivables-reports.showroom-shift-summary','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
 
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('customer_id', getCustomersTwoList(), null, ['placeholder'=>'Select Customer', 'class' => 'form-control mlselect'  ])!!}
                            </div>
                            </div>
 
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('start-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('end-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                            </div>

                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                                 <div class="col-sm-1"><button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button></div>

                             <div class="col-sm-2">
                        </div>
                                
                            </div>
                            </div>

                            </form>
		   							   @foreach($expenseList as $val)
							   	<?php 
								   	$totalexp += abs($val->amount);
							   	?>
							   	@endforeach

                           <div class="col-sm-12">
	                           <div class="col-sm-8">

	                           </div> 
	                           <div class="col-sm-4">
							   	<b>Amount</b><br>
							   	<div style="border-top:1px solid; border-bottom:1px solid;">
								   	<b>{{manageAmountFormat(array_sum($totalamounts)+array_sum($paymentmethodtotal))}}</b>
 							   	</div>
							   	
	                           </div> 


                           </div> 

                           <div class="col-sm-12">
	                           <div class="col-sm-8">
							   <b>EXPENSES</b><br>
                                <table class="table table-bordered" style="width: 80%;">
                                    <thead>
                                    <tr>
                                        <th width="10%">Date</th>
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
	                           <div class="col-sm-4">
	                           </div> 
                           </div> 
                      
                           <div class="col-sm-12">
	                           <div class="col-sm-8">

	                           </div> 
	                           <div class="col-sm-4">
							   	<b>Net Amount</b><br>
								   	<div style="border-top:1px solid; border-bottom:1px solid;">
									   	<b>{{manageAmountFormat((array_sum($totalamounts)+array_sum($paymentmethodtotal))-$totalexp)}}</b> 							   	
		                           </div> 
	                           </div> 
                           </div> 


                        </div>
              
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
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
                                    {{-- <td>{{ getCustomerNameByDocumentNumber($item->document_no) }}</td> --}}
                                    <td>{{ @$item->debt_or_trans->customerDetail->customer_name }}</td>
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
                    </div>

    </section>


  
@endsection


@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
$(document).ready(function(){
  $(".getshiftdata").change(function(){
	  var salesmanId = $(this).val();
    $.ajax({
	    url: "{{route('sales-and-receivables-reports.getShiftBySalesman')}}",
	    dataType:"JSON", 
	    data:{'_token':"{{csrf_token()}}",salesman_id:salesmanId,'shift_summary':'1'},
	    success: function(result){
		    $('.shiftList').html('');
			$.each(result, function (key, val) {
		    $('.shiftList').append('<option value="'+key+'">'+val+'</option>');
			});
//			$("#div1").html(result);
    	}});
  });
});

    $(function () {
	    		$(".mlselec6t").select2({
			closeOnSelect : false,
		});
//        $(".mlselec6t").select2();
    });

</script>

<script>
            
     $('.datepicker').datetimepicker({
       format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
     });
</script>


@endsection