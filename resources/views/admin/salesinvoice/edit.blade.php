@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
      <form method="POST" action="" accept-charset="UTF-8" class="" enctype="multipart/form-data" novalidate="novalidate">
            {{ csrf_field() }}
             <?php 
	             	//echo "<pre>"; print_r($row); die;
                    $sales_invoice_number = $row->sales_invoice_number;
                    $order_date = $row->order_date;


                    ?>

            <div class = "row">

              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Sales Order No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('sales_invoice_number',  $sales_invoice_number , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Customer Name</label>
                    <div class="col-sm-7 twodiv">
                      

                          {!!Form::select('wa_customer_id', getCustomersTwoList(),$row->wa_customer_id, ['class' => 'form-control mlselec6t twocustomers','required'=>true,'placeholder' => 'Please select','id'=>'wa_customer_id' ])!!} 
                    </div>
                    <div class="col-sm-7 alldiv" style="display: none;">
                      

                          {!!Form::select('wa_customer_id', getCustomersList(),$row->wa_customer_id, ['class' => 'form-control mlselec6t allcustomers','required'=>true,'placeholder' => 'Please select','id'=>'wa_customer_id' ])!!} 
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

                     <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Load All Customer </label>
                    <div class="col-sm-6">
	                    <input type="checkbox" id="loadallcustomer" />
                    </div>
                </div>
            </div>
                     </div>
                   

<?php 
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;

?>

		@if($logged_user_info->role_id == 1 || isset($my_permissions['sales-invoices___draw-stock-from']))
                   
                        <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Draw Stock From</label>
                    <div class="col-sm-6">
                         {!!Form::select('from_strore_location_id',getStoreLocationDropdownByBranch(getLoggeduserProfile()->restaurant_id), $row->wa_location_and_store_id, ['class' => 'form-control','id'=>'from_strore_location_id' ,'placeholder'=>'Please select','readonly'=>true,'disabled'=>true ])!!} 
                          <span id = "error_msg_from_strore_location_id"></span>
                    </div>
                </div>
            </div>
                     </div>

		@endif



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
                                          <td><span id = "quantity_{{ $item->id }}">{{ $item->quantity }}</span></td>
                                          <td>{{ $item->getUnitOfMeasure->title}}</td>
                                          <td><span id = "unit_price_{{ $item->id }}">{{ $item->unit_price }}</span>
                                          <span style="display:none;" id = "actual_unit_price_{{ $item->id }}">{{ $item->actual_unit_price }}</span>

                                          </td>
                                          <td><span id = "line_amount_{{ $item->id }}">{{ $item->unit_price*$item->quantity }}</span></td>
                                          <td>
                                            <input type="number" name= "discount_percent_{{ $item->id }}" id="discount_percent_{{ $item->id }}" class="discount_percent_class form-control" min="0" value="{{ $item->discount_percent }}" data-attr="{{ $item->id }}">
                                            </td>
                                          <td>
                                              <input type="number" name= "discount_amount_{{ $item->id }}" id="discount_amount_{{ $item->id }}" class="discount_amount_class form-control" readonly="readonly" value="{{ $item->discount_amount }}">
                                          </td>



                                          <td><span id = "vat_rate_{{ $item->id }}">{{ $item->vat_rate }}</span></td>
                                          <td><span id = "vat_amount_{{ $item->id }}">{{ $item->vat_amount }}</span></td>

                                          <td><span id = "service_charge_rate_{{ $item->id }}">{{ $item->service_charge_rate }}</span></td>
                                          <td><span id = "service_charge_amount_{{ $item->id }}">{{ $item->service_charge_amount }}</span></td>

                                          <td><span id = "catering_levy_rate_{{ $item->id }}">{{ $item->catering_levy_rate }}</span></td>
                                          <td><span id = "catering_levy_amount_{{ $item->id }}">{{ $item->catering_levy_amount }}</span></td>





                                          <td><span id = "total_cost_with_vat_{{ $item->id }}">{{ $item->total_cost_with_vat }}</span></td>
                                           <td>{{ $item->note }}</td>
                                     
                                      
                                    </tr>
                                    @endforeach
                        
                                   


                                    </tbody>
                                </table>
                                <div align="right"><button type= "submit" class="btn btn-success">Process</button></div><br>
                                </form>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                              <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel" id="addItemForm" >Add Item To Order</button>
                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addManualItemModel" onclick="getmanualitemlist()">Manual Entry</button>                              
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>


    <!-- Modal -->

<div id="addRequisitionItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Item To Order</h4>
      </div>
      <div class="modal-body">


      



        <form class="validate"  role="form" method="POST" action="{{ route($model.'.addMore',$row->slug) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}

            
              {!! Form::hidden('sales_invoice_number', $sales_invoice_number, []) !!}  
              {!! Form::hidden('selected_order_date', $order_date, ['id'=>'selected_order_date']) !!} 
              {!! Form::hidden('selected_customer_id', null, ['id'=>'selected_customer_id']) !!} 
               {!! Form::hidden('selected_status', null, ['id'=>'selected_status']) !!} 

 {!! Form::hidden('selected_request_or_delivery', null, ['id'=>'selected_request_or_delivery']) !!} 
 {!! Form::hidden('selected_from_strore_location_id', null, ['id'=>'selected_from_strore_location_id']) !!} 
                

               

                    
               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Type</label>
                    <div class="col-sm-9">
                        {!! Form::select('item_type', ['item'=>'ITEM','gl-code'=>'GL CODE'],null, ['maxlength'=>'255','placeholder' => 'Please select item Type', 'required'=>true, 'class'=>'form-control mlselec6t_modal','id'=>'item_type']) !!}  
                    </div>
                </div>
            </div>
             {!! Form::hidden('selected_item_type', null, ['id'=>'selected_item_type']) !!} 
           

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_item_id', [],null, ['maxlength'=>'255','placeholder' => 'Please select item', 'required'=>true, 'class'=>'form-control mlselec6t_modal','id'=>'item']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item No</label>
                    <div class="col-sm-9">
                       
                        {!! Form::text('item_no', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'item_no','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>




             <div class="box-body">
                <div class="form-group">

                     <label for="inputEmail3" class="col-sm-3 control-label">Unit Price</label>
                    <div class="col-sm-3">
                       
                         {!! Form::number('unit_price', 0, ['class'=>'form-control','id'=>'unit_price','min'=>'0','required'=>true]) !!}   
                    </div>

                    <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost</label>
                    <div class="col-sm-3">
                       
                        {!! Form::text('standard_cost', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'standard_cost','readonly'=>true]) !!}  
                    </div>

                    
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                 <label for="inputEmail3" class="col-sm-3 control-label">Quantity</label>
                    <div class="col-sm-3">
                      


                       {!! Form::number('quantity', 1, ['required'=>true, 'class'=>'form-control','id'=>'quantity','min'=>'0']) !!}  
                    </div>
                    <label for="inputEmail3" class="col-sm-3 control-label">UOM</label>
                    <div class="col-sm-3">
                      


                         {!! Form::select('unit_of_measure', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => '',  'class'=>'form-control ','id'=>'unit_of_measure','required'=>true]) !!}  
                    </div>

                   
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Vat Rate</label>
                    <div class="col-sm-9">
                        {!! Form::select('vat_rate', ['0'=>'VAT 0%','16'=>'VAT 16%'],null, [ 'class'=>'form-control','id'=>'vat_rate']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Service Charge</label>
                    <div class="col-sm-9">
                        {!! Form::select('service_charge_rate', ['0'=>'Service Charge 0%','10'=>'Service Charge 10%'],null, [ 'class'=>'form-control','id'=>'service_charge_rate']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Catering Levy</label>
                    <div class="col-sm-9">
                        {!! Form::select('catering_levy_rate', ['0'=>'Catering Levy 0%','2'=>'Catering Levy 2%'],null, [ 'class'=>'form-control','id'=>'catering_levy_rate']) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Total Amount Inc VAT</label>
                    <div class="col-sm-9">
                        

                         {!! Form::number('total_amount_inc_vat', null, ['required'=>true, 'class'=>'form-control','id'=>'total_amount_inc_vat','readonly'=>true]) !!} 
                    </div>
                </div>
            </div>







           

          
            
            

           

            

            

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                    <div class="col-sm-9">
                       {!! Form::textarea('note', null, ['maxlength'=>'1000', 'class'=>'form-control','id'=>'note','required'=>true]) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                       <div class="box-footer">
                <button type="submit" class="btn btn-primary" id ="add_submitform">Add</button>
            </div>
                    </div>
                </div>
            </div>

            

             
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="addManualItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 60%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Multiple Item To Transfer</h4>
      </div>
      <div class="modal-body">



        {!! Form::model($row, ['method' => 'POST','route' => [$model.'.addMoreManual', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}



             {!! Form::hidden('selected_item_type', "item", ['id'=>'selected_item_type']) !!} 

                 {!! Form::hidden('type', 'manual_item', []) !!} 

             <div class="box-body">
                <div class="form-group">
                     <div class="col-sm-12">
						<div id="manualitemsList">
						</div>
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                       <div class="box-footer">
                <button type="button" class="btn btn-warning" onclick="addmoremanualitemlist()">Add More Items</button>
                <button type="submit" class="btn btn-primary" class="addItemByCreate">Save</button>
                
            </div>
                    </div>
                </div>
            </div>

            

             
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
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
                url: '{{route('sales-invoices.get.customer-detail')}}',
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

     $("#loadallcustomer").change(function(){
	    var ischecked= $(this).is(':checked');
	    if(ischecked){
		    $('.twocustomers').attr("disabled","disabled");
		    $('.allcustomers').removeAttr("disabled","");
		    $('.twodiv').css({"display":"none"});
		    $('.alldiv').css({"display":"block"});
//		    alert('checkd ' + $(this).val());		    
	    }else{
		    $('.allcustomers').attr("disabled","disabled");
		    $('.twocustomers').removeAttr("disabled","");
		    $('.twodiv').css({"display":"block"});
		    $('.alldiv').css({"display":"none"});
//		    alert('uncheckd ' + $(this).val());		    		    
	    }
    });
  

   

  $(document).ready(function(){ 
     manageTotalPrice();
  
  });




    $(function () {
        $(".mlselec6t").select2();
    });
    $(function () {
        $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});
    });



    $("#item_type").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
       $("#standard_cost").val('');
        $("#item_type").attr('disabled',true);
        $("#selected_item_type").val($("#item_type").val());
      
       
       
        var selected_item_type = $("#item_type").val();
        manageitem(selected_item_type);

    });

      $("#item").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        $("#standard_cost").val('');

         $("#unit_price").val('');

        
           $("#prev_standard_cost").val('');
        var selected_item_id = $("#item").val();
       
        getItemDetails(selected_item_id);

    });






    function getItemDetails(selected_item_id)
   {
    
     
    
        if(selected_item_id != "")
        {
          var item_type =  $("#item_type").val();
            if(item_type == 'item')
           {
               jQuery.ajax({
                  url: '{{route('sales-invoices.items.detail')}}',
                  type: 'POST',
                  data:{selected_item_id:selected_item_id,item_type:item_type},
                  headers: {
                  'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function (response) 
                  {
                    var obj = jQuery.parseJSON(response);
                    $("#item_no").val(obj.stock_id_code);
                    $("#unit_of_measure").val(obj.unit_of_measure).change();
                    $("#standard_cost").val(obj.standard_cost);
                     $("#unit_price").val(obj.standard_cost);
                      manageTotalPrice();  
                  }
              });
           }
           else
           {
             jQuery.ajax({
                  url: '{{route('sales-invoices.items.detail')}}',
                  type: 'POST',
                  data:{selected_item_id:selected_item_id,item_type:item_type},
                  headers: {
                  'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function (response) 
                  {
                    var obj = jQuery.parseJSON(response);
                    $("#item_no").val(obj.stock_id_code);
                    $("#unit_of_measure").attr('disabled',false);
                    $("#standard_cost").val(obj.standard_cost);
                     $("#unit_price").val(obj.standard_cost);

                      manageTotalPrice();  
                  }
              });
           }
  
        }
       
   }

    function manageitem(selected_item_type)
   {
   
        if(selected_item_type != "")
        {
         

          
             jQuery.ajax({
                url: '{{route('sales-invoices.items')}}',
                type: 'POST',
                data:{selected_item_type:selected_item_type},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#item").val('');
                    $("#item").html(response);
                   
                   

                }
            });

         
           
        }
        else
        {
           $("#item").val('');
           $("#item").html('<option selected="selected" value="">Please select item</option>');
        }
   }



  $("#unit_price,#quantity,#vat_rate").on("keyup", function (e) {
      manageTotalPrice();
    });


  $(".discount_percent_class").on("keyup", function (e) {

      manageDiscountAmount($(this).attr('data-attr'));
    });

   $(".discount_percent_class").change(function (e) {
      manageDiscountAmount($(this).attr('data-attr'));
    });

   function manageDiscountAmount(row_id)
   {
    var discount_rate = $("#discount_percent_"+row_id).val();
    var unit_price = $("#actual_unit_price_"+row_id).html();
      var quantity =  $("#quantity_"+row_id).html();
    var discount_amount =  (discount_rate*unit_price)/100;
    $("#discount_amount_"+row_id).val(parseFloat(discount_amount*quantity).toFixed(2));

    var new_price = unit_price-discount_amount
    new_price = parseFloat(new_price).toFixed(2);
     $("#unit_price_"+row_id).html(new_price);

    
      var newLineAmount = new_price*quantity;
        $("#line_amount_"+row_id).html(parseFloat(newLineAmount).toFixed(2));
         var vat_rate =  $("#vat_rate_"+row_id).html();

        // var new_vat_amount = (newLineAmount*vat_rate)/100;
         //  $("#vat_amount_"+row_id).html(parseFloat(new_vat_amount).toFixed(2));

        //  var total_amount_with_vat =  newLineAmount+new_vat_amount;
        var total_amount_with_vat =  newLineAmount;
           $("#total_cost_with_vat_"+row_id).html(parseFloat(total_amount_with_vat).toFixed(2));
          
   
   }

   

   


  

   $("#unit_price,#quantity,#vat_rate").change(function (e) {
      manageTotalPrice();
    });

   function manageTotalPrice()
   {  
     var unit_price = $("#unit_price").val();
     var quantity = $("#quantity").val();
     var vat_rate = $("#vat_rate").val();
     var total_price_witout_vat = unit_price*quantity;

     var vat_amount = (vat_rate*total_price_witout_vat)/100;

    // var total_price_with_vat = total_price_witout_vat+vat_amount;
    var total_price_with_vat = total_price_witout_vat;
     $('#total_amount_inc_vat').val(parseFloat(total_price_with_vat));



   }



   function getmanualitemlist(){
 
            jQuery.ajax({
                url: '{{route('transfers.getManualItemsList')}}?type=sales-invoice',
                type: 'POST',
                 headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#manualitemsList").val('');
                    $("#manualitemsList").html(response);
                }
            });
       
   }


   function addmoremanualitemlist(){
 
            jQuery.ajax({
                url: '{{route('transfers.getManualItemsList')}}',
                type: 'POST',
                 headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                     $("#manualitemsList").append(response);
                }
            });
       
   }

  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
    
   
@endsection


