
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                                 <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Generate Bills
                            </div><br>
                            {!! Form::open(['route' => 'admin.generate-bills','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                            <div class="col-sm-6">
                            <div class="form-group">
                            {!!Form::select('waiter-id', $waiter_info, null, ['placeholder'=>'Select Waiter', 'class' => 'form-control','id'=>'selector_selects'  ])!!}
                            </div>
                            </div>

                             <div class="col-sm-1">
                            <div class="form-group">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Submit</button>
                            </div>
                            </div>

                             <div class="col-sm-1">
                            <div class="form-group">
                             <a class="btn btn-info" href="{!! route('admin.generate-bills') !!}"  >Clear</a>
                            </div>
                            </div>




                            

                            </div>

                            </div>

                            </form>
                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                              @if($waiter_id != '')
                             
                              {!! Form::model(null, ['method' => 'post','route' => ['admin.post.generate-bills', $waiter_id],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                              {{ csrf_field() }}


                               <?php 
                            $myTotalAmount = [];
                             if(isset($lists) && !empty($lists))
                             {
	                          ?>
	                          <div class="col-sm-12">
		                          <button type="submit" class="btn btn-primary">Combine Bill</button>
	                          </div>
	                          <?php
                              foreach($lists as $listing)
                              {
                                 foreach($listing->getAssociateOrdersWithBill as $single_order_new)
                                 {
                                  $myTotalAmount[] = $single_order_new->getAssociateOrderForBill->order_final_price;
                                 }
                              }
                             }
                            ?>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        
                                        <th><input type="checkbox" id="checkall" style="display: block;"></th>
                                        <th>Bill ID</th>
                                        <th>Date</th>
                                        <th>Waiter Name</th>
                                        <th>Number of Orders</th>
                                        <th class="noneedtoshort" >Total Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1; $totalorderamnt = 0; ?>
                                        @foreach($lists as $list)

                                        <?php 
                                        $showTheRow = false;
                                        if($permission == 'superadmin')
                                        {
                                            $showTheRow = true;
                                        }
                                        else
                                        {
                                          if(count($list->getAssociateOrdersWithBill)>0 && $list->getAssociateOrdersWithBill[0]->getAssociateOrderForBill->restaurant_id == $logged_user_info->restaurant_id)
                                          {
                                            
                                             $showTheRow = true;
                                          }
                                        }

                                        if($showTheRow == true){
                                        ?>


                                       
                                         
                                            <tr>
                                                <td>
                                                {{ Form::checkbox('order___'.$list->id , $list->id ) }}



                                                </td>
                                               
                                              <td>{!! $list->id !!}</td>
                                              <td>
                                                {!! date('Y-m-d',strtotime($list->created_at)) !!}
                                              </td>
                                              <td>{!! ucfirst($list->getAssociateUserForBill->name) !!}</td>
                                              <td>{!! count($list->getAssociateOrdersWithBill) !!}</td>

                                               {{-- <td>{!! implode(', ',$list->getAssociateOrdersWithBill->pluck('order_id')->toArray()) !!}</td> --}}
                                              
                                              <td>
                                              <?php 
                                              $total_bill = []; ?>
                                              @foreach($list->getAssociateOrdersWithBill as $single_order)
                                              <?php $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
                                              $totalorderamnt += $single_order->getAssociateOrderForBill->order_final_price;
                                               ?>

                                              @endforeach 
                                              {!! round(array_sum($total_bill)); !!}
                                              </td>

                                            </tr>
                                           <?php $b++; ?>

                                           <?php } ?>
                                        @endforeach
 
                                    @endif


                                    </tbody>
                                     <tfoot>
                                        <td ></td>
                                        <td></td>
                                        <td ></td>
                                        <td></td>
                                        <td ><b>Total</b></td>
                                        <td ><b>{{ manageAmountFormat(round($totalorderamnt)) }}</b></td>

                                    </tfoot>
                                </table>
                            </div>

                                </form>
                                @endif
                            </div>
                        </div>
                    </div>


    </section>
   <style type="text/css">
       .table td {
  font-size: 13px;
}
.select2-container .select2-selection--single .select2-selection__rendered {
  /* padding-left: 0; */
  /* padding-right: 0; */
  /* height: auto; */
  margin-top: -6px;
  height: 100px !important;
}

   </style>

   

    <script type="text/javascript">


      function printBill(order_id,user_id,print_type)
       {
          var confirm_text = 'bill';
          if(print_type == 'D')
          {
            confirm_text = 'docket';
          }
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.receipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{order_id:order_id,user_id:user_id,print_type:print_type},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Bill</title>');
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

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
    $(function () {
      $("#selector_selects").select2();
     
});

$("#checkall").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});

</script>

@endsection
