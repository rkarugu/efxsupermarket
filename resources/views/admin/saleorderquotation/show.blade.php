@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
      <form method="POST" action="" accept-charset="UTF-8" class="" enctype="multipart/form-data" novalidate="novalidate">
            {{ csrf_field() }}
             <?php 
                    $sales_order_number = $row->sales_order_number;
                    $order_date = $row->order_date;


                    ?>

            <div class = "row">

              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Pro-Forma Invoice No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('sales_order_number',  $sales_order_number , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Customer Name</label>
                    <div class="col-sm-7">
                      

                          {!!Form::select('wa_customer_id', getCustomersList(),$row->wa_customer_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select','id'=>'wa_customer_id','disabled'=>true ])!!} 
                    </div>
                </div>
            </div>
          
                   </div>
                  


             <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Address</label>
                    <div class="col-sm-7">
                        {!! Form::text('address',$row->getRelatedCustomer->address, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true,'id'=>'address']) !!}  
                    </div>
                </div>
            </div>
            </div>

             <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Phone Number</label>
                    <div class="col-sm-7">
                        {!! Form::text('phone_number', $row->getRelatedCustomer->telephone, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true,'id'=>'phone_number']) !!}  
                    </div>
                </div>
            </div>
            </div>

            

              <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Order Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('order_date', $order_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control ','readonly'=>true,'id'=>'order_date']) !!}  
                    </div>
                </div>
            </div>
            </div>



              </div>
              <div class = "col-sm-6">
                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Status</label>
                    <div class="col-sm-6">
                         {!!Form::select('status', ['open'=>'Open','close'=>'Close'],$row->status, ['class' => 'form-control ','required'=>true,'id'=>'status','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                   </div>

                     <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Request/Delivery</label>
                    <div class="col-sm-6">
                         {!!Form::select('request_or_delivery',['request'=>'Request','delivery'=>'Delivery'], $row->request_or_delivery, ['class' => 'form-control ','required'=>true,'id'=>'request_or_delivery','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                   


              </div>


            </div>





          

            


           



            


           

             
        </form>
    </div>
</section>

       <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title"> Lines</h3>

                            <span id = "requisitionitemtable">
                           

                              <form class=""  role="form" method="POST" action="{{ route($model.'.process',$row->slug) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>Type</th>
                                      <th>Description</th>
                                      <th>Item No</th>
                                     <th>QTY</th>
                                       <th>UOM</th>
                                      <th>Unit Price</th>
                                       <th>Line Amount</th>
                                        <th>Discount%</th>
                                        <th>Discount Amount</th>
                                          <th>VAT Rate</th>
                                          <th>VAT Amount</th>
                                            <th>Service Charge Rate</th>
                                          <th>Service Charge Amount</th>
                                           <th>Catering Levy Rate</th>
                                          <th>Catering Levy Amount</th>
                                      <th>Total Amount Inc VAT</th>
                                       <th>Notes</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($row->getRelatedItem as $item)
                                       <tr>
                                           <td>{{ strtoupper($item->item_type) }}</td>
                                          <td>{{ $item->item_name}}</td>
                                          <td>{{ $item->item_no }}</td>
                                          <td><span id = "quantity_{{ $item->id }}">{{ manageAmountFormat($item->quantity) }}</span></td>
                                          <td>{{ $item->getUnitOfMeasure->title}}</td>
                                          <td><span id = "unit_price_{{ $item->id }}">{{ manageAmountFormat($item->unit_price) }}</span>
                                          <span style="display:none;" id = "actual_unit_price_{{ $item->id }}">{{ $item->actual_unit_price }}</span>

                                          </td>
                                          <td><span id = "line_amount_{{ $item->id }}">{{ manageAmountFormat($item->unit_price*$item->quantity) }}</span></td>
                                          <td>
                                           {{ manageAmountFormat($item->discount_percent) }}
                                            </td>
                                          <td>
                                            {{ manageAmountFormat($item->discount_amount) }}
                                          </td>
                                          <td><span id = "vat_rate_{{ $item->id }}">{{ $item->vat_rate }}</span></td>
                                          <td><span id = "vat_amount_{{ $item->id }}">{{ manageAmountFormat($item->vat_amount) }}</span></td>
                                          
                                          <td><span id = "service_charge_rate_{{ $item->id }}">{{ $item->service_charge_rate }}</span></td>
                                          <td><span id = "service_charge_amount_{{ $item->id }}">{{ $item->service_charge_amount }}</span></td>

                                          <td><span id = "catering_levy_rate_{{ $item->id }}">{{ $item->catering_levy_rate }}</span></td>
                                          <td><span id = "catering_levy_amount_{{ $item->id }}">{{ $item->catering_levy_amount }}</span></td>
                                          <td><span id = "total_cost_with_vat_{{ $item->id }}">{{ manageAmountFormat($item->total_cost_with_vat) }}</span></td>
                                           <td>{{ $item->note }}</td>
                                     
                                      
                                    </tr>
                                    @endforeach
                        
                                   


                                    </tbody>
                                </table>
                             
                                </form>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                             
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>


    <!-- Modal -->

@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">


  $("#wa_customer_id").change(function(){
      var customerid = $(this).val();
      $("#address").val('');
      $("#phone_number").val('');
       if(customerid != "")
       {
            $("#selected_customer_id").val(customerid);
            jQuery.ajax({
                url: '{{route('proforma-invoice.get.customer-detail')}}',
                type: 'POST',
                data:{customer_id:customerid},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                  var obj = jQuery.parseJSON(response);
                  $("#address").val(obj.address);
                  $("#phone_number").val(obj.telephone);
                  
                }
            });
       }
       else
       {
       
        $("#selected_customer_id").val('');
       }
     });

  

   















   

   


  





  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
    
   
@endsection


