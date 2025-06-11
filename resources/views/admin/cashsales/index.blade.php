
@extends('layouts.admin.admin')

@section('content')
<?php 
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;

?>


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'cash-sales.index','method'=>'get']) !!}

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


                           


                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                 <!--div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div-->
                                 <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('cash-sales.index') !!}"  >Clear </a>
                           
                        </div>
                             <div class="col-sm-2">
                        </div>
                                
                            </div>
                            </div>

                            </form>
                        </div>

                             <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable_50">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="5%"  >Sales No</th>
                                         <th width="10%"  >Sales date</th>
                                         <th width="10%"  >Customer</th>
                                         <th width="10%"  >Phone No.</th>
                                        <th width="15%"  >Veh. Reg. No. </th>
                                         <th width="10%"  >Route</th>
                                         <th width="10%"  >Docunent No.</th>

                                         
                                               <th width="10%"  >Sales Man</th>
                                         
                                         <th width="10%"  >Amount</th>
                                        
                                          <th  width="10%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;
	                                    //   echo "<pre>"; print_r($lists); die;
                                        ?>
                                        @foreach($lists as $list)
                                        <?php


                                        ?>
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->cash_sales_number !!}</td>
                                                 <td>{!! $list->order_date !!}</td>
                                                  <td>{!! ucfirst($list->getRelatedCustomer->customer_name) !!}</td>
                                                  <td>{!! ucfirst($list->getRelatedCustomer->telephone) !!}</td>
                                                <td>{!! $list->vehicle_reg_no !!}</td>
                                                 <td>{!! $list->route !!}</td>
                                                 <td>{!! $list->document_no !!}</td>


                                           <td>{!! ucfirst(@$list->getRelatedSalesman->name) !!}</td>
                                                 <?php
	                                               //  $amnt = 0;
/*
													 foreach($list->getRelatedItem as $val){
														 $amnt += $val->unit_price * $val->quantity;
													 }
*/
                                                 ?>
                                                  
                                                 <td>{!! manageAmountFormat($list->relatedItemTotal) !!}</td>                                                  

                                                 
                                                
                                                <td class = "action_crud">

                                                    <a title="View" href="{{ route($model.'.show', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>

													@if($logged_user_info->role_id == 1 || isset($my_permissions['cash-sales___reserve-transaction']))
                                                    <a title="Reserve" href="{{ route('cash-sales.reserve-transaction', $list->document_no) }}" onclick="return confirm('Do you want to reverse the sales transactions ?')"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                                    @endif
                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>


      <script type="text/javascript">
       function printBill(slug)
       {
          var confirm_text = 'order'; 
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('sales-invoices.print')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{slug:slug},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=600');
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
   
@endsection
@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script>
                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
            </script>

@endsection