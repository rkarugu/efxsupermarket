
@extends('layouts.admin.admin')

@section('content')

<style>
.buttons-html5{
 background-color: #f39c12 !important;
border-color: #e08e0b !important;
border-radius: 3px !important;
-webkit-box-shadow: none !important;
box-shadow: none !important;
border: 1px solid transparent !important;
color: #fff !important;
display: inline-block !important;
padding: 7px 10px !important;
margin-bottom: 0 !important;
font-size: 14px !important;
font-weight: 400 !important;
line-height: 1.42857143 !important;
text-align: center !important;
white-space: nowrap !important;
vertical-align: middle !important;
}
</style>
<?php 
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;

?>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <div align = "right"> 
                            @if(isset($permission['sales-invoices___posted-receipt']) || $permission == 'superadmin')
                                <a href = "{!! route('maintain-customers.debtors-inquiry',['slug'=>'showroom-stock','posted'=>'receipt'])!!}" class = "btn btn-success">Posted Receipt</a>  
                                @endif
                                
                                @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                                <a href = "{!! route('maintain-customers.enter-customer-payment',['slug'=>'showroom-stock'])!!}" class = "btn btn-success">Receipt</a>  
                                <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a>
                                @endif
                            </div>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable2">
                                    <thead>
                                    <tr>
                                        <th width="5%">S.No.</th>
                                       
                                        <th width="5%"  >Order No</th>
                                         <th width="10%"  >Order date</th>
                                         <th width="10%"  >Customer</th>

                                         
                                             <th width="10%"  >Total Amount</th>
                                               <th width="10%"  >Status</th>
                                         
                                        
                                          <th  width="10%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
											@php
											$dueamount = ($list->getRelatedItem->sum('total_cost_with_vat') - $list->getRelatedCustomerAllocatedAmnt->sum('allocated_amount'));
											@endphp
											@if($dueamount!=0)
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->sales_invoice_number !!}</td>
                                                 <td>{!! $list->order_date !!}</td>
                                                  <td>{!! ucfirst(@$list->getRelatedCustomer->customer_name) !!}</td>

                                                  

                                          <td>{{ manageAmountFormat($list->getRelatedItem->sum('total_cost_with_vat'))}}</td>
                                           <td>{!! ucfirst($list->status) !!}</td>
                                                 

                                                 
                                                
                                                <td class = "action_crud">

                                                @if($list->order_creating_status == 'pending')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                   

                                                  

                                                  

                                                    <span>
                                                    <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button  style="float:left"><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                    </form>
                                                    </span>

                                                    @else
                                                       <span>
                                                    <a title="View" href="{{ route($model.'.show', $list->slug) }}" ><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    </span>
                                                     @endif

                                                        @if($list->order_creating_status == 'completed')
                                                       <span>
                                                    <a title="Print" href="javascript:void(0)" onclick="printBill('{!! $list->slug!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                   <span>
                                                    <a title="Export To Pdf" href="{{ route($model.'.exportToPdf', $list->slug)}}"><i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                  
                                                        @endif

													@if($logged_user_info->role_id == 1 || isset($my_permissions['sales-invoices___sales-item-reserve-transaction']))
                                                   <span>
                                                    <a title="Reserve" href="{{ route('sales-invoices.reserve-transaction', $list->slug) }}" onclick="return confirm('Do you want to reverse the sales transactions ?')"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                                  </span>
                                                    @endif
                                                  

                                                      





                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
                                            @endif
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

	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
	<script type="text/javascript" class="init">
$(document).ready(function() {
	$('#create_datatable2').DataTable( {
        pageLength: "100",
		dom: 'Bfrtip',
		buttons: [
			{
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf" aria-hidden="true">',
            exportOptions: {
                columns: [0,1,2,3,4,5],
            },
			customize : function(doc) {
			doc.content[1].table.widths = ["*", "*", "*", "*", "*", "*"];
            }
           }
		]
	} );
} );

                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
            </script>
@endsection