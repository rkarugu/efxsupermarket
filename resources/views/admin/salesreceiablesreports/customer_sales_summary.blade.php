
@extends('layouts.admin.admin')

@section('content')
<style>
.buttons-excel{
    background-color: #f39c12 !important;
    border-color: #e08e0b !important;
    border-radius: 3px !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: 1px solid transparent !important;
    color: #fff !important;
    display: inline-block !important;
    padding: 6px 12px !important;
    margin-bottom: 0 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.42857143 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}
.dt-buttons{
width: 10%  !important;
position: relative !important;
left: 80px !important;
top: -70px;
}
</style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'sales-and-receivables-reports.customer_sales_summary','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">


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

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('salesman_id', getAllsalesmanList(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect'  ])!!}
                            </div>
                            </div>
 
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>	                  
                                 </div>
                                  <div class="col-sm-1"><button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button></div>
                                 
                            </div>
                            </div>

                            </form>
                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                 <b>Salesman Name :@if(isset($salesmanname)) {{ $salesmanname}} @endif</b>
	                            <br>
	                            <br>
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="5%"  >Sales No</th>
                                         <th width="10%"  >Sales date</th>
                                         <th width="10%"  >Business Name</th>
                                         <th width="10%"  >Phone No.</th>
                                        <th width="15%"  >Contact Name </th>
                                         <th width="10%"  >Route</th>

                                         
                                               <th width="10%"  >Area Name</th>
                                         
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

                                    </div>
                        </div>
                    </div>
    </section>


  
@endsection


@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
</script>

<script>
            

      $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
</script>

	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script type="text/javascript" class="init">
	


$(document).ready(function() {
	$('#create_datatable1').DataTable( {
        pageLength: "100",
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
		]
	} );
} );



	</script>

@endsection